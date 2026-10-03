<?php
/**
 * My Addresses - Multiple Addresses Override
 * academist-child/woocommerce/myaccount/my-address.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();
if (!class_exists('ANA_Addresses_Plugin')) {
    // Fallback if plugin is off
    wc_get_template('myaccount/my-address-fallback.php');
    return;
}

$billing_addrs = ANA_Addresses_Plugin::get_addresses( $user_id, 'billing' );
$shipping_addrs = ANA_Addresses_Plugin::get_addresses( $user_id, 'shipping' );

// Helper function to render a card
if ( ! function_exists('ana_render_bank_card') ) {
    function ana_render_bank_card($addr, $type = 'billing') {
        $is_pj = isset($addr['entity_type']) && $addr['entity_type'] === 'pj';
        
        // Decide background color based on type (Billing = dark blue, Shipping = dark red/purple)
        $bg_gradient = $type === 'billing' 
            ? 'linear-gradient(135deg, #1e293b 0%, #0f172a 100%)' 
            : 'linear-gradient(135deg, #4c0519 0%, #881337 100%)';
        ?>
        <div class="ana-address-card <?php echo !empty($addr['is_default']) ? 'is-default' : ''; ?>" style="
            background: <?php echo $bg_gradient; ?>;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            position: relative;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            font-family: 'Courier New', Courier, monospace;
            overflow: hidden;
            min-height: 190px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        ">
            <!-- Glossy Overlay -->
            <div style="position:absolute; top:-50%; left:-50%; width:200%; height:200%; background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%); pointer-events: none;"></div>
            
            <!-- Header: Chip & Default Badge -->
            <div style="display:flex; justify-content: space-between; align-items: flex-start; z-index: 1;">
                <svg width="45" height="32" viewBox="0 0 40 30" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.9;">
                    <rect width="40" height="30" rx="4" fill="#EAB308"/>
                    <path d="M0 10H10V20H0V10Z" fill="#CA8A04"/>
                    <path d="M30 10H40V20H30V10Z" fill="#CA8A04"/>
                    <path d="M15 0H25V30H15V0Z" fill="#CA8A04"/>
                    <path d="M5 5L15 15M35 5L25 15M5 25L15 15M35 25L25 15" stroke="#A16207" stroke-width="1.5"/>
                </svg>

                <?php if (!empty($addr['is_default'])) : ?>
                    <span style="font-size:10px; font-weight:bold; letter-spacing:1px; padding: 5px 10px; border-radius:6px; background:rgba(255,255,255,0.15); backdrop-filter:blur(8px); color:#fff; font-family: sans-serif; border: 1px solid rgba(255,255,255,0.1);">
                        PREDEFINITĂ
                    </span>
                <?php endif; ?>
            </div>

            <!-- Middle: Name (Cardholder) -->
            <div style="z-index: 1; margin-top: 25px;">
                <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 6px; font-family: sans-serif;">
                    <?php echo $is_pj ? 'Companie / Entitate Juridică' : 'Nume Titular'; ?>
                </div>
                <div style="font-size: 18px; font-weight: 600; text-transform: uppercase; letter-spacing: 3px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                    <?php if ($is_pj) : ?>
                        <?php echo esc_html($addr['company'] ?? 'Firma'); ?>
                    <?php else : ?>
                        <?php echo esc_html(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')); ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bottom: Address Details -->
            <div style="z-index: 1; margin-top: 20px; display: flex; justify-content: space-between; align-items: flex-end; gap: 15px;">
                <div style="font-size: 12px; color: #cbd5e1; line-height: 1.5; text-transform: uppercase; letter-spacing: 1px; flex: 1;">
                    <?php if ($is_pj) : ?>
                        <div style="color:#fff; margin-bottom:4px;">CUI: <?php echo esc_html($addr['vat_number'] ?? ''); ?> <span style="opacity:0.5;">|</span> REG: <?php echo esc_html($addr['reg_com'] ?? ''); ?></div>
                    <?php endif; ?>
                    <div><?php echo esc_html($addr['address_1'] ?? ''); ?> <?php echo esc_html($addr['address_2'] ?? ''); ?></div>
                    <div><?php echo esc_html($addr['city'] ?? ''); ?>, <?php echo esc_html($addr['state'] ?? ''); ?></div>
                    <div><?php echo esc_html($addr['country'] ?? 'RO'); ?> <?php echo esc_html($addr['postcode'] ?? ''); ?></div>
                </div>
                
                <!-- Actions -->
                <div style="display:flex; flex-direction:column; gap: 8px; font-family: sans-serif;">
                    <button class="ana-btn-edit" data-json="<?php echo esc_attr(wp_json_encode($addr)); ?>" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight:bold; cursor: pointer; transition: 0.2s;">
                        EDITEAZĂ
                    </button>
                    <button class="ana-btn-delete" data-id="<?php echo esc_attr($addr['id'] ?? ''); ?>" style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight:bold; cursor: pointer; transition: 0.2s;">
                        ȘTERGE
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
<div class="ana-my-account-addresses" style="font-family: sans-serif;">
    <p style="color: #64748b; margin-bottom: 25px; font-size:15px;">Aici poți gestiona multiple adrese de facturare și livrare pentru contul tău.</p>
    
    <div class="ana-addresses-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- BILLING -->
        <div class="ana-address-column">
            <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom:20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                <h3 style="margin:0; font-size: 20px; color:#0f172a; display:flex; align-items:center; gap:8px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Adrese Facturare
                </h3>
                <button class="button ana-btn-add-new" data-type="billing" style="background:#f43f5e; color:#fff; border-radius:30px; font-weight:bold; padding: 8px 16px; border:none; font-size:12px; cursor:pointer;">+ Adaugă Nouă</button>
            </div>
            <?php if ( empty($billing_addrs) ) : ?>
                <div class="woocommerce-message woocommerce-message--info" style="border-radius:8px;">Nu ai nicio adresă salvată.</div>
            <?php else : ?>
                <?php foreach ($billing_addrs as $addr) : ?>
                    <?php ana_render_bank_card($addr, 'billing'); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- SHIPPING -->
        <div class="ana-address-column">
            <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom:20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                <h3 style="margin:0; font-size: 20px; color:#0f172a; display:flex; align-items:center; gap:8px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    Adrese Livrare
                </h3>
                <button class="button ana-btn-add-new" data-type="shipping" style="background:#f43f5e; color:#fff; border-radius:30px; font-weight:bold; padding: 8px 16px; border:none; font-size:12px; cursor:pointer;">+ Adaugă Nouă</button>
            </div>
            <?php if ( empty($shipping_addrs) ) : ?>
                <div class="woocommerce-message woocommerce-message--info" style="border-radius:8px;">Nu ai nicio adresă salvată.</div>
            <?php else : ?>
                <?php foreach ($shipping_addrs as $addr) : ?>
                    <?php ana_render_bank_card($addr, 'shipping'); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL FORM -->
<div id="ana-address-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.8); backdrop-filter: blur(4px); z-index:99999; align-items:center; justify-content:center; font-family: sans-serif;">
    <div id="ana-address-modal" style="background:#fff; width:90%; max-width:600px; max-height:90vh; overflow-y:auto; border-radius:16px; padding:30px; position:relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
        <button type="button" id="ana-modal-close" style="position:absolute; top:20px; right:20px; background:#f1f5f9; border:none; width:36px; height:36px; border-radius:50%; font-size:20px; display:flex; align-items:center; justify-content:center; cursor:pointer; color: #64748b; transition: 0.2s;">&times;</button>
        
        <h3 id="ana-modal-title" style="margin-top:0; font-size:22px; color:#0f172a; margin-bottom:25px; font-weight:bold;">Adaugă Adresă</h3>
        
        <form id="ana-address-form">
            <input type="hidden" name="address_id" id="ana_f_id" value="">
            <input type="hidden" name="address_type" id="ana_f_type" value="billing">
            <input type="hidden" name="action" value="ana_frontend_save_address">
            <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('ana_frontend_nonce')); ?>">
            
            <div id="ana_f_entity_toggle" style="display:flex; gap:10px; margin-bottom:20px;">
                <label style="flex:1; text-align:center; padding:12px; background:#f1f5f9; border-radius:10px; cursor:pointer; font-weight:600; color:#334155; transition: 0.2s;">
                    <input type="radio" name="entity_type" value="pf" checked style="margin-right:8px;"> Persoană Fizică
                </label>
                <label style="flex:1; text-align:center; padding:12px; background:#f1f5f9; border-radius:10px; cursor:pointer; font-weight:600; color:#334155; transition: 0.2s;">
                    <input type="radio" name="entity_type" value="pj" style="margin-right:8px;"> Persoană Juridică
                </label>
            </div>

            <div id="ana_f_pj_fields" style="display:none; background:#f8fafc; border: 1px solid #e2e8f0; padding:20px; border-radius:12px; margin-bottom:20px;">
                <div style="display:flex; gap:10px; margin-bottom:10px;">
                    <input type="text" id="ana_f_anaf_cui" placeholder="Caută CUI (ex: 42467528)" style="flex:1; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    <button type="button" id="ana_f_anaf_btn" class="button" style="background:#1e293b; color:white; border-radius:8px; padding: 0 20px; border:none; cursor:pointer;">Caută ANAF</button>
                </div>
                <div id="ana_f_anaf_status" style="font-size:13px; margin-bottom:15px; font-weight:600;"></div>

                <label style="display:block; margin-bottom:6px; font-size:13px; color:#475569; font-weight:600;">Companie *</label>
                <input type="text" name="company" id="ana_f_company" style="width:100%; margin-bottom:15px; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; background:#fff;">
                
                <div style="display:flex; gap:15px;">
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:6px; font-size:13px; color:#475569; font-weight:600;">CUI *</label>
                        <input type="text" name="vat_number" id="ana_f_vat" style="width:100%; margin-bottom:0; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; background:#fff;">
                    </div>
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:6px; font-size:13px; color:#475569; font-weight:600;">Reg. Com</label>
                        <input type="text" name="reg_number" id="ana_f_reg" style="width:100%; margin-bottom:0; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; background:#fff;">
                    </div>
                </div>
            </div>

            <div style="display:flex; gap:15px; margin-bottom:15px;">
                <div style="flex:1;">
                    <label style="display:block; margin-bottom:6px; font-size:13px; color:#475569; font-weight:600;">Nume *</label>
                    <input type="text" name="first_name" id="ana_f_fn" required style="width:100%; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </div>
                <div style="flex:1;">
                    <label style="display:block; margin-bottom:6px; font-size:13px; color:#475569; font-weight:600;">Prenume *</label>
                    <input type="text" name="last_name" id="ana_f_ln" required style="width:100%; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </div>
            </div>

            <label style="display:block; margin-bottom:6px; font-size:13px; color:#475569; font-weight:600;">Țara / Regiune *</label>
            <input type="text" name="country" id="ana_f_country" value="RO" style="width:100%; margin-bottom:15px; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">

            <label style="display:block; margin-bottom:6px; font-size:13px; color:#475569; font-weight:600;">Adresă *</label>
            <input type="text" name="address_1" id="ana_f_addr1" required placeholder="Strada, număr..." style="width:100%; margin-bottom:10px; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">
            <input type="text" name="address_2" id="ana_f_addr2" placeholder="Apartament, bloc... (opțional)" style="width:100%; margin-bottom:15px; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">

            <div style="display:flex; gap:15px; margin-bottom:15px;">
                <div style="flex:1;">
                    <label style="display:block; margin-bottom:6px; font-size:13px; color:#475569; font-weight:600;">Oraș *</label>
                    <input type="text" name="city" id="ana_f_city" required style="width:100%; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </div>
                <div style="flex:1;">
                    <label style="display:block; margin-bottom:6px; font-size:13px; color:#475569; font-weight:600;">Județ *</label>
                    <input type="text" name="state" id="ana_f_state" required style="width:100%; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </div>
            </div>

            <div style="display:flex; gap:15px; margin-bottom:20px;">
                <div style="flex:1;">
                    <label style="display:block; margin-bottom:6px; font-size:13px; color:#475569; font-weight:600;">Cod Poștal</label>
                    <input type="text" name="postcode" id="ana_f_postcode" style="width:100%; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </div>
                <div style="flex:1;">
                    <label style="display:block; margin-bottom:6px; font-size:13px; color:#475569; font-weight:600;">Telefon</label>
                    <input type="text" name="phone" id="ana_f_phone" style="width:100%; padding:10px 15px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </div>
            </div>

            <label style="display:flex; align-items:center; gap:10px; margin-top:10px; cursor:pointer; font-size: 15px; color:#1e293b; font-weight:500; background:#f1f5f9; padding:12px 15px; border-radius:8px;">
                <input type="checkbox" name="is_default" id="ana_f_def" value="1" style="width:20px; height:20px; accent-color:#0f172a;">
                Setează ca adresă predefinită
            </label>

            <div style="margin-top:30px; display:flex; justify-content:flex-end; gap:15px;">
                <button type="button" class="button" id="ana-modal-cancel" style="background:#fff; color:#64748b; border: 1px solid #cbd5e1; padding: 12px 24px; border-radius: 8px; cursor:pointer; font-weight:600;">Anulează</button>
                <button type="submit" class="button" id="ana-modal-save" style="background:#0f172a; color:#fff; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight:600; box-shadow:0 4px 6px -1px rgba(15, 23, 42, 0.2);">Salvează Adresa</button>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Hover effects for entity toggle
    $('input[name="entity_type"]').closest('label').on('click', function() {
        $('input[name="entity_type"]').closest('label').css({'background':'#f1f5f9', 'border':'1px solid transparent'});
        $(this).css({'background':'#e2e8f0', 'border':'1px solid #cbd5e1'});
    });

    $('.ana-btn-add-new').on('click', function(e) {
        e.preventDefault();
        $('#ana-address-form')[0].reset();
        $('#ana_f_id').val('');
        $('#ana_f_type').val($(this).data('type'));
        $('#ana-modal-title').text($(this).data('type') === 'shipping' ? 'Adaugă Adresă Livrare' : 'Adaugă Adresă Facturare');
        
        if($(this).data('type') === 'shipping') {
            $('#ana_f_entity_toggle').hide();
            $('input[name="entity_type"][value="pf"]').prop('checked', true).trigger('change');
        } else {
            $('#ana_f_entity_toggle').show();
            $('input[name="entity_type"][value="pf"]').prop('checked', true).trigger('change');
        }
        $('#ana-address-modal-overlay').css('display', 'flex').hide().fadeIn(200);
    });

    $('.ana-btn-edit').on('click', function(e) {
        e.preventDefault();
        var data = $(this).data('json');
        $('#ana_f_id').val(data.id || '');
        $('#ana_f_type').val(data.address_type || 'billing');
        $('#ana-modal-title').text('Editează Adresă');

        if(data.address_type === 'shipping') {
            $('#ana_f_entity_toggle').hide();
        } else {
            $('#ana_f_entity_toggle').show();
        }

        $('input[name="entity_type"][value="' + (data.entity_type || 'pf') + '"]').prop('checked', true).trigger('change');
        $('input[name="entity_type"]:checked').closest('label').trigger('click');

        $('#ana_f_company').val(data.company || '');
        $('#ana_f_vat').val(data.vat_number || '');
        $('#ana_f_reg').val(data.reg_com || '');
        $('#ana_f_fn').val(data.first_name || '');
        $('#ana_f_ln').val(data.last_name || '');
        $('#ana_f_country').val(data.country || 'RO');
        $('#ana_f_addr1').val(data.address_1 || '');
        $('#ana_f_addr2').val(data.address_2 || '');
        $('#ana_f_city').val(data.city || '');
        $('#ana_f_state').val(data.state || '');
        $('#ana_f_postcode').val(data.postcode || '');
        $('#ana_f_phone').val(data.phone || '');
        $('#ana_f_def').prop('checked', data.is_default == 1);
        $('#ana-address-modal-overlay').css('display', 'flex').hide().fadeIn(200);
    });

    $('input[name="entity_type"]').on('change', function() {
        if ($(this).val() === 'pj') {
            $('#ana_f_pj_fields').slideDown(200);
            $('#ana_f_company, #ana_f_vat').prop('required', true);
        } else {
            $('#ana_f_pj_fields').slideUp(200);
            $('#ana_f_company, #ana_f_vat').prop('required', false);
        }
    });

    $('#ana-modal-close, #ana-modal-cancel').on('click', function() {
        $('#ana-address-modal-overlay').fadeOut(200);
    });

    $('.ana-btn-delete').on('click', function(e) {
        e.preventDefault();
        if(confirm('Sigur vrei să ștergi această adresă?')) {
            var id = $(this).data('id');
            var btn = $(this);
            var originalText = btn.text();
            btn.text('...').prop('disabled', true);
            $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                action: 'ana_frontend_delete_address',
                address_id: id,
                nonce: '<?php echo esc_attr(wp_create_nonce('ana_frontend_nonce')); ?>'
            }, function(res) {
                if(res.success) {
                    location.reload();
                } else {
                    alert(res.data.message || 'Eroare la ștergere');
                    btn.text(originalText).prop('disabled', false);
                }
            });
        }
    });

    $('#ana-address-form').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#ana-modal-save');
        btn.prop('disabled', true).text('Se salvează...');
        $.post('<?php echo admin_url('admin-ajax.php'); ?>', $(this).serialize(), function(res) {
            if(res.success) {
                location.reload();
            } else {
                alert(res.data.message || 'Eroare la salvare');
                btn.prop('disabled', false).text('Salvează Adresa');
            }
        }).fail(function() {
            alert('Eroare de conexiune.');
            btn.prop('disabled', false).text('Salvează Adresa');
        });
    });

    $('#ana_f_anaf_btn').on('click', function() {
        var cui = $('#ana_f_anaf_cui').val().trim();
        if(!cui) return;
        var btn = $(this);
        btn.prop('disabled', true).text('⌛');
        $('#ana_f_anaf_status').html('<span style="color:#d97706">Se comunică cu ANAF...</span>');
        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
            action: 'rima_anaf_lookup',
            cui: cui,
            nonce: '<?php echo esc_attr(wp_create_nonce('rima_anaf_lookup')); ?>'
        }, function(res) {
            btn.prop('disabled', false).text('Caută ANAF');
            if(res.success && res.data) {
                $('#ana_f_anaf_status').html('<span style="color:#059669">✓ Găsit!</span>');
                $('#ana_f_company').val(res.data.name || '');
                $('#ana_f_vat').val(cui);
                $('#ana_f_reg').val(res.data.reg_com || '');
                $('#ana_f_addr1').val(res.data.address || '');
                $('#ana_f_city').val(res.data.city || '');
                $('#ana_f_state').val(res.data.county || '');
                if(res.data.phone) $('#ana_f_phone').val(res.data.phone);
            } else {
                $('#ana_f_anaf_status').html('<span style="color:#dc2626">✕ Nu a fost găsit.</span>');
            }
        });
    });
    
    function adjustGrid() {
        if(window.innerWidth < 768) {
            $('.ana-addresses-grid').css({'grid-template-columns': '1fr'});
        } else {
            $('.ana-addresses-grid').css({'grid-template-columns': '1fr 1fr'});
        }
    }
    $(window).resize(adjustGrid);
    adjustGrid();
});
</script>
