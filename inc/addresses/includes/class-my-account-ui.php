<?php
/**
 * ANA Addresses - My Account UI
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ANA_Addresses_My_Account_UI {
    
    public function __construct() {
        // Override the default WooCommerce Addresses endpoint
        add_action( 'init', [ $this, 'override_addresses_endpoint' ] );
    }

    public function override_addresses_endpoint() {
        if ( class_exists('WooCommerce') ) {
            remove_action( 'woocommerce_account_addresses_endpoint', 'woocommerce_account_addresses' );
            add_action( 'woocommerce_account_addresses_endpoint', [ $this, 'render_multiple_addresses' ] );
        }
    }

    public function render_multiple_addresses() {
        $user_id = get_current_user_id();
        $billing_addrs = ANA_Addresses_Plugin::get_addresses( $user_id, 'billing' );
        $shipping_addrs = ANA_Addresses_Plugin::get_addresses( $user_id, 'shipping' );
        
        // Output the HTML
        ?>
        <div class="ana-my-account-addresses">
            
            <p>Aici poți gestiona multiple adrese de facturare și livrare pentru contul tău.</p>

            <div class="ana-addresses-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                
                <!-- BILLING -->
                <div class="ana-address-column">
                    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom:15px;">
                        <h3 style="margin:0;">Adrese Facturare</h3>
                        <button class="button ana-btn-add-new" data-type="billing">+ Adaugă Nouă</button>
                    </div>

                    <?php if ( empty($billing_addrs) ) : ?>
                        <div class="woocommerce-message woocommerce-message--info">Nu ai nicio adresă salvată.</div>
                    <?php else : ?>
                        <?php foreach ($billing_addrs as $addr) : 
                            $is_pj = isset($addr['entity_type']) && $addr['entity_type'] === 'pj';
                        ?>
                            <div class="ana-address-card <?php echo !empty($addr['is_default']) ? 'is-default' : ''; ?>" style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; margin-bottom:15px; background: #fff; position: relative;">
                                <?php if (!empty($addr['is_default'])) : ?>
                                    <span class="badge bg-primary" style="position:absolute; top: 15px; right: 15px; font-size:10px; padding: 4px 8px; border-radius:4px; background:#102d56; color:#fff;">PREDEFINITĂ</span>
                                <?php endif; ?>
                                
                                <div class="ana-card-header" style="font-weight: 600; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                                    <?php if ($is_pj) : ?>
                                        🏢 <?php echo esc_html($addr['company'] ?? 'Firmă'); ?>
                                    <?php else : ?>
                                        👤 <?php echo esc_html(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="ana-card-body" style="font-size: 14px; color: #475569; line-height: 1.6;">
                                    <?php if ($is_pj) : ?>
                                        <div><strong>CUI:</strong> <?php echo esc_html($addr['vat_number'] ?? ''); ?></div>
                                        <div><strong>Reg. Com:</strong> <?php echo esc_html($addr['reg_com'] ?? ''); ?></div>
                                    <?php endif; ?>
                                    <div><?php echo esc_html($addr['address_1'] ?? ''); ?> <?php echo esc_html($addr['address_2'] ?? ''); ?></div>
                                    <div><?php echo esc_html($addr['city'] ?? ''); ?>, <?php echo esc_html($addr['state'] ?? ''); ?> <?php echo esc_html($addr['postcode'] ?? ''); ?></div>
                                    <div><?php echo esc_html($addr['country'] ?? 'RO'); ?></div>
                                </div>
                                
                                <div class="ana-card-actions" style="margin-top: 15px; display:flex; gap: 10px;">
                                    <button class="button ana-btn-edit" data-json="<?php echo esc_attr(wp_json_encode($addr)); ?>" style="padding: 5px 10px; font-size: 12px;">Editează</button>
                                    <button class="button ana-btn-delete" data-id="<?php echo esc_attr($addr['id'] ?? ''); ?>" style="padding: 5px 10px; font-size: 12px; background: transparent; color: #dc2626; border: 1px solid #dc2626;">Șterge</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- SHIPPING -->
                <div class="ana-address-column">
                    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom:15px;">
                        <h3 style="margin:0;">Adrese Livrare</h3>
                        <button class="button ana-btn-add-new" data-type="shipping">+ Adaugă Nouă</button>
                    </div>

                    <?php if ( empty($shipping_addrs) ) : ?>
                        <div class="woocommerce-message woocommerce-message--info">Nu ai nicio adresă salvată.</div>
                    <?php else : ?>
                        <?php foreach ($shipping_addrs as $addr) : ?>
                            <div class="ana-address-card <?php echo !empty($addr['is_default']) ? 'is-default' : ''; ?>" style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; margin-bottom:15px; background: #fff; position: relative;">
                                <?php if (!empty($addr['is_default'])) : ?>
                                    <span class="badge bg-primary" style="position:absolute; top: 15px; right: 15px; font-size:10px; padding: 4px 8px; border-radius:4px; background:#102d56; color:#fff;">PREDEFINITĂ</span>
                                <?php endif; ?>
                                
                                <div class="ana-card-header" style="font-weight: 600; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                                    🚚 <?php echo esc_html(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')); ?>
                                </div>
                                
                                <div class="ana-card-body" style="font-size: 14px; color: #475569; line-height: 1.6;">
                                    <div><?php echo esc_html($addr['address_1'] ?? ''); ?> <?php echo esc_html($addr['address_2'] ?? ''); ?></div>
                                    <div><?php echo esc_html($addr['city'] ?? ''); ?>, <?php echo esc_html($addr['state'] ?? ''); ?> <?php echo esc_html($addr['postcode'] ?? ''); ?></div>
                                    <div><?php echo esc_html($addr['country'] ?? 'RO'); ?></div>
                                </div>
                                
                                <div class="ana-card-actions" style="margin-top: 15px; display:flex; gap: 10px;">
                                    <button class="button ana-btn-edit" data-json="<?php echo esc_attr(wp_json_encode($addr)); ?>" style="padding: 5px 10px; font-size: 12px;">Editează</button>
                                    <button class="button ana-btn-delete" data-id="<?php echo esc_attr($addr['id'] ?? ''); ?>" style="padding: 5px 10px; font-size: 12px; background: transparent; color: #dc2626; border: 1px solid #dc2626;">Șterge</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <!-- MODAL FORM FOR ADDING / EDITING -->
        <div id="ana-address-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:99999; align-items:center; justify-content:center;">
            <div id="ana-address-modal" style="background:#fff; width:90%; max-width:600px; max-height:90vh; overflow-y:auto; border-radius:12px; padding:25px; position:relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                <button type="button" id="ana-modal-close" style="position:absolute; top:15px; right:15px; background:none; border:none; font-size:24px; cursor:pointer; color: #64748b;">&times;</button>
                <h3 id="ana-modal-title" style="margin-top:0; font-size:20px; color:#1e293b; margin-bottom:20px;">Adaugă Adresă</h3>
                
                <form id="ana-address-form">
                    <input type="hidden" name="address_id" id="ana_f_id" value="">
                    <input type="hidden" name="address_type" id="ana_f_type" value="billing">
                    <input type="hidden" name="action" value="ana_frontend_save_address">
                    <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('ana_frontend_nonce')); ?>">
                    
                    <div id="ana_f_entity_toggle" style="display:flex; gap:10px; margin-bottom:15px;">
                        <label style="flex:1; text-align:center; padding:10px; background:#f1f5f9; border-radius:8px; cursor:pointer;">
                            <input type="radio" name="entity_type" value="pf" checked> Persoană Fizică
                        </label>
                        <label style="flex:1; text-align:center; padding:10px; background:#f1f5f9; border-radius:8px; cursor:pointer;">
                            <input type="radio" name="entity_type" value="pj"> Persoană Juridică
                        </label>
                    </div>

                    <div id="ana_f_pj_fields" style="display:none; background:#f8fafc; padding:15px; border-radius:8px; margin-bottom:15px;">
                        <div style="display:flex; gap:10px; margin-bottom:10px;">
                            <input type="text" id="ana_f_anaf_cui" placeholder="Caută CUI (ex: 42467528)" style="flex:1; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                            <button type="button" id="ana_f_anaf_btn" class="button" style="background:#102d56; color:white; border-radius:4px; padding: 0 15px;">Caută ANAF</button>
                        </div>
                        <div id="ana_f_anaf_status" style="font-size:12px; margin-bottom:10px;"></div>

                        <label style="display:block; margin-bottom:5px; font-size:13px;">Companie *</label>
                        <input type="text" name="company" id="ana_f_company" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        
                        <div style="display:flex; gap:10px;">
                            <div style="flex:1;">
                                <label style="display:block; margin-bottom:5px; font-size:13px;">CUI *</label>
                                <input type="text" name="vat_number" id="ana_f_vat" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                            </div>
                            <div style="flex:1;">
                                <label style="display:block; margin-bottom:5px; font-size:13px;">Reg. Com</label>
                                <input type="text" name="reg_number" id="ana_f_reg" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:5px; font-size:13px;">Nume *</label>
                            <input type="text" name="first_name" id="ana_f_fn" required style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        </div>
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:5px; font-size:13px;">Prenume *</label>
                            <input type="text" name="last_name" id="ana_f_ln" required style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        </div>
                    </div>

                    <label style="display:block; margin-bottom:5px; font-size:13px;">Țară / Regiune *</label>
                    <input type="text" name="country" id="ana_f_country" value="RO" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">

                    <label style="display:block; margin-bottom:5px; font-size:13px;">Adresa *</label>
                    <input type="text" name="address_1" id="ana_f_addr1" required placeholder="Strada, număr..." style="width:100%; margin-bottom:10px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    <input type="text" name="address_2" id="ana_f_addr2" placeholder="Apartament, bloc... (opțional)" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">

                    <div style="display:flex; gap:10px;">
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:5px; font-size:13px;">Oraș *</label>
                            <input type="text" name="city" id="ana_f_city" required style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        </div>
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:5px; font-size:13px;">Județ *</label>
                            <input type="text" name="state" id="ana_f_state" required style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        </div>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:5px; font-size:13px;">Cod Poștal</label>
                            <input type="text" name="postcode" id="ana_f_postcode" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        </div>
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:5px; font-size:13px;">Telefon</label>
                            <input type="text" name="phone" id="ana_f_phone" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        </div>
                    </div>

                    <label style="display:flex; align-items:center; gap:10px; margin-top:10px; cursor:pointer; font-size: 14px;">
                        <input type="checkbox" name="is_default" id="ana_f_def" value="1">
                        Setează ca adresă predefinită
                    </label>

                    <div style="margin-top:25px; text-align:right;">
                        <button type="button" class="button" id="ana-modal-cancel" style="background:transparent; color:#64748b; margin-right: 10px; border: 1px solid #cbd5e1; padding: 8px 15px; border-radius: 4px;">Anulează</button>
                        <button type="submit" class="button" id="ana-modal-save" style="background:#102d56; color:#fff; padding: 8px 20px; border-radius: 4px; border: none; cursor: pointer;">Salvează Adresa</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            
            // Open Modal for NEW
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

                $('#ana-address-modal-overlay').css('display', 'flex');
            });

            // Open Modal for EDIT
            $('.ana-btn-edit').on('click', function(e) {
                e.preventDefault();
                var data = $(this).data('json');
                $('#ana_f_id').val(data.id || '');
                $('#ana_f_type').val(data.address_type || 'billing');
                $('#ana-modal-title').text('Editează Adresa');

                if(data.address_type === 'shipping') {
                    $('#ana_f_entity_toggle').hide();
                } else {
                    $('#ana_f_entity_toggle').show();
                }

                $('input[name="entity_type"][value="' + (data.entity_type || 'pf') + '"]').prop('checked', true).trigger('change');
                
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

                $('#ana-address-modal-overlay').css('display', 'flex');
            });

            // Toggle PF/PJ
            $('input[name="entity_type"]').on('change', function() {
                if ($(this).val() === 'pj') {
                    $('#ana_f_pj_fields').slideDown();
                    $('#ana_f_company, #ana_f_vat').prop('required', true);
                } else {
                    $('#ana_f_pj_fields').slideUp();
                    $('#ana_f_company, #ana_f_vat').prop('required', false);
                }
            });

            // Close Modal
            $('#ana-modal-close, #ana-modal-cancel').on('click', function() {
                $('#ana-address-modal-overlay').hide();
            });

            // Delete
            $('.ana-btn-delete').on('click', function(e) {
                e.preventDefault();
                if(confirm('Sigur vrei să ștergi această adresă?')) {
                    var id = $(this).data('id');
                    var btn = $(this);
                    btn.text('...');
                    $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                        action: 'ana_frontend_delete_address',
                        address_id: id,
                        nonce: '<?php echo esc_attr(wp_create_nonce('ana_frontend_nonce')); ?>'
                    }, function(res) {
                        if(res.success) {
                            location.reload();
                        } else {
                            alert(res.data.message || 'Eroare la ștergere');
                            btn.text('Șterge');
                        }
                    });
                }
            });

            // Form Submit
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

            // ANAF Lookup
            $('#ana_f_anaf_btn').on('click', function() {
                var cui = $('#ana_f_anaf_cui').val().trim();
                if(!cui) return;
                var btn = $(this);
                btn.prop('disabled', true).text('⏳');
                $('#ana_f_anaf_status').html('<span style="color:#d97706">Se comunică cu ANAF...</span>');
                
                $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                    action: 'rima_anaf_lookup',
                    cui: cui,
                    nonce: '<?php echo esc_attr(wp_create_nonce('rima_anaf_lookup')); ?>'
                }, function(res) {
                    btn.prop('disabled', false).text('Caută ANAF');
                    if(res.success && res.data) {
                        $('#ana_f_anaf_status').html('<span style="color:#059669">✅ Găsit!</span>');
                        $('#ana_f_company').val(res.data.name || '');
                        $('#ana_f_vat').val(cui);
                        $('#ana_f_reg').val(res.data.reg_com || '');
                        $('#ana_f_addr1').val(res.data.address || '');
                        $('#ana_f_city').val(res.data.city || '');
                        $('#ana_f_state').val(res.data.county || '');
                        if(res.data.phone) $('#ana_f_phone').val(res.data.phone);
                    } else {
                        $('#ana_f_anaf_status').html('<span style="color:#dc2626">❌ Nu a fost găsit.</span>');
                    }
                });
            });
            
            // Adjust grid layout on mobile
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
        <?php
    }
}
new ANA_Addresses_My_Account_UI();
