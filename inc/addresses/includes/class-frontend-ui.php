<?php
/**
 * ANA Addresses - Frontend UI Integration
 * Injects multiple addresses logic into Checkout and My Account
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ANA_Addresses_Frontend_UI {
    
    public function __construct() {
        add_action( 'woocommerce_before_checkout_billing_form', [ $this, 'render_checkout_selector' ] );
        add_action( 'woocommerce_checkout_process', [ $this, 'validate_excel_upload' ] );
        add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'save_excel_upload' ], 10, 2 );
        add_action( 'woocommerce_before_edit_account_address_form', [ $this, 'render_my_account_pf_pj_selector' ] );
        add_action( 'wp_head', [ $this, 'output_demo_excel_endpoint' ] );
    }

    /**
     * Output demo Excel download endpoint
     */
    public function output_demo_excel_endpoint() {
        // Handled via admin-ajax.php (see ajax-handlers.php alias rima_download_demo_excel)
    }

    /**
     * CHECKOUT: Single clean PF/PJ UI with ANAF + Excel upload for PJ
     */
    public function render_checkout_selector( $checkout ) {
        if ( ! is_user_logged_in() ) return;

        $user_id   = get_current_user_id();
        $addresses = ANA_Addresses_Plugin::get_addresses( $user_id, 'billing' );
        $ajax_url  = admin_url( 'admin-ajax.php' );
        $nonce     = wp_create_nonce( 'rima_anaf_lookup' );
        $demo_url  = add_query_arg( [ 'action' => 'rima_demo_excel' ], $ajax_url );
        ?>

        <!-- ============================================================
             RIMA CHECKOUT: PF / PJ SELECTOR (single block, no duplicates)
             ============================================================ -->
        <div id="rimaCheckoutBlock" style="margin-bottom: 28px;">

            <!-- PF / PJ Toggle Tabs -->
            <div class="rima-co-tabs">
                <button type="button" class="rima-co-tab is-active" data-tab="pf" id="rimaCoPfTab">
                    👤 <span>PERSOANĂ FIZICĂ</span>
                </button>
                <button type="button" class="rima-co-tab" data-tab="pj" id="rimaCoPjTab">
                    🏢 <span>PERSOANĂ JURIDICĂ</span>
                </button>
            </div>

            <!-- Hidden field that tells server what type -->
            <input type="hidden" name="rima_checkout_entity_type" id="rimaEntityTypeField" value="pf">
            <input type="hidden" name="selected_billing_address_id" id="rimaSelectedAddrId" value="">

            <!-- ======= PF PANEL ======= -->
            <div id="rimaTabPf" class="rima-co-panel">
                <?php if ( ! empty( $addresses ) ) : ?>
                    <div class="rima-co-section">
                        <label class="rima-co-label">Selectează adresă salvată (opțional):</label>
                        <select id="rimaSavedAddrSelect" class="rima-co-select">
                            <option value="">— Completează manual câmpurile de mai jos —</option>
                            <?php foreach ( $addresses as $addr ) :
                                $is_pj = isset($addr['entity_type']) && $addr['entity_type'] === 'pj';
                                if ( $is_pj ) continue; // show only PF in PF tab
                                $title = trim( ($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '') );
                            ?>
                                <option value="<?php echo esc_attr($addr['id']); ?>"
                                        data-json="<?php echo esc_attr(wp_json_encode($addr)); ?>">
                                    👤 <?php echo esc_html($title . ' — ' . ($addr['city'] ?? '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ======= PJ PANEL ======= -->
            <div id="rimaTabPj" class="rima-co-panel" style="display:none;">

                <!-- Saved PJ addresses -->
                <?php
                $pj_addrs = array_filter($addresses, function($a) {
                    return isset($a['entity_type']) && $a['entity_type'] === 'pj';
                });
                if ( ! empty( $pj_addrs ) ) : ?>
                    <div class="rima-co-section">
                        <label class="rima-co-label">Firmă salvată (opțional):</label>
                        <select id="rimaSavedPjSelect" class="rima-co-select">
                            <option value="">— Completează datele manual sau caută prin ANAF —</option>
                            <?php foreach ( $pj_addrs as $addr ) : ?>
                                <option value="<?php echo esc_attr($addr['id']); ?>"
                                        data-json="<?php echo esc_attr(wp_json_encode($addr)); ?>">
                                    🏢 <?php echo esc_html(($addr['company'] ?? 'Firmă') . ' — ' . ($addr['vat_number'] ?? '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- ANAF Lookup -->
                <div class="rima-co-section">
                    <div class="rima-co-section-header">
                        <span class="rima-co-section-icon">🔍</span>
                        <strong>Caută Firmă automat (ANAF)</strong>
                    </div>
                    <div class="rima-co-anaf-row">
                        <input type="text" id="rimaCheckoutCui" class="rima-co-input" placeholder="Introdu CUI (ex: 42467528)">
                        <button type="button" id="rimaCheckoutAnafBtn" class="rima-co-btn-primary">
                            <span class="rima-anaf-text">Caută</span>
                            <span class="rima-anaf-spin" style="display:none;">⏳</span>
                        </button>
                    </div>
                    <div id="rimaCheckoutAnafStatus" class="rima-co-status"></div>
                </div>

                <!-- PJ Fields populated by ANAF or manual -->
                <div class="rima-co-section rima-co-pj-fields">
                    <div class="rima-co-section-header">
                        <span class="rima-co-section-icon">🏢</span>
                        <strong>Date Firmă</strong>
                    </div>
                    <div class="rima-co-grid-2">
                        <div class="rima-co-field">
                            <label class="rima-co-label required">CUI / CIF:</label>
                            <input type="text" name="billing_vat_number" id="rimaBillingVat" class="rima-co-input" placeholder="RO42467528">
                        </div>
                        <div class="rima-co-field">
                            <label class="rima-co-label">Nr. Reg. Comerțului:</label>
                            <input type="text" name="billing_reg_com" id="rimaBillingRegCom" class="rima-co-input" placeholder="J40/1234/2020">
                        </div>
                    </div>
                    <div class="rima-co-grid-2">
                        <div class="rima-co-field">
                            <label class="rima-co-label required">Nume Firmă:</label>
                            <input type="text" name="billing_company_name" id="rimaBillingCompany" class="rima-co-input" placeholder="S.C. Exemplu S.R.L.">
                        </div>
                        <div class="rima-co-field">
                            <label class="rima-co-label">IBAN:</label>
                            <input type="text" name="billing_iban" id="rimaBillingIban" class="rima-co-input" placeholder="RO49AAAA1B31007593840000">
                        </div>
                    </div>
                    <div class="rima-co-grid-2">
                        <div class="rima-co-field">
                            <label class="rima-co-label">Telefon Firmă:</label>
                            <input type="text" name="billing_company_phone" id="rimaBillingCoPhone" class="rima-co-input" placeholder="021 123 456">
                        </div>
                        <div class="rima-co-field">
                            <label class="rima-co-label">Email Firmă:</label>
                            <input type="email" name="billing_company_email" id="rimaBillingCoEmail" class="rima-co-input" placeholder="office@firma.ro">
                        </div>
                    </div>
                </div>

                <!-- Excel Upload -->
                <div class="rima-co-section rima-co-excel-section">
                    <div class="rima-co-section-header">
                        <span class="rima-co-section-icon">📊</span>
                        <strong>Înrolare Studenți (Excel)</strong>
                    </div>
                    <p class="rima-co-hint">
                        Dacă achiziționați cursuri pentru angajați/membri, puteți încărca un fișier Excel (.xlsx) cu datele lor. 
                        Conturile vor fi create automat după finalizarea plății.
                    </p>

                    <div class="rima-co-excel-upload-area" id="rimaExcelDropZone">
                        <input type="file" name="ana_students_excel" id="anaStudentsExcel" accept=".xls,.xlsx" style="display:none;">
                        <div class="rima-co-excel-icon">📋</div>
                        <div class="rima-co-excel-text">Trage fișierul Excel aici sau</div>
                        <button type="button" class="rima-co-btn-excel" id="rimaBtnChooseExcel">
                            📂 Alege Fișier Excel
                        </button>
                        <div id="rimaExcelFileName" class="rima-co-excel-filename"></div>
                    </div>

                    <a href="<?php echo esc_url($demo_url); ?>" class="rima-co-demo-link" download>
                        ⬇️ Descarcă model Excel completat
                    </a>
                </div>

            </div><!-- /#rimaTabPj -->

        </div><!-- /#rimaCheckoutBlock -->

        <style>
        /* ============================================================
           RIMA CHECKOUT PF/PJ BLOCK
           ============================================================ */
        #rimaCheckoutBlock {
            font-family: inherit;
        }

        /* Tabs */
        .rima-co-tabs {
            display: flex;
            background: #f1f5f9;
            border-radius: 14px;
            padding: 5px;
            gap: 0;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }
        .rima-co-tab {
            flex: 1;
            padding: 12px 16px;
            border: none;
            background: transparent;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            transition: all 0.25s ease;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .rima-co-tab.is-active {
            background: linear-gradient(135deg, #102d56, #1d4ed8);
            color: #fff;
            box-shadow: 0 3px 12px rgba(29, 78, 216, 0.3);
        }

        /* Panel */
        .rima-co-panel {
            animation: rimaFadeIn 0.2s ease;
        }
        @keyframes rimaFadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: none; } }

        /* Sections */
        .rima-co-section {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 14px;
        }
        .rima-co-section-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 14px;
        }
        .rima-co-section-icon { font-size: 16px; }

        /* Fields */
        .rima-co-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }
        .rima-co-field { display: flex; flex-direction: column; gap: 4px; }
        .rima-co-label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }
        .rima-co-label.required::after { content: ' *'; color: #ef4444; }
        .rima-co-input, .rima-co-select {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
        }
        .rima-co-input:focus, .rima-co-select:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }

        /* ANAF Row */
        .rima-co-anaf-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .rima-co-anaf-row .rima-co-input { flex: 1; }
        .rima-co-btn-primary {
            padding: 10px 20px;
            background: linear-gradient(135deg, #102d56, #1d4ed8);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            white-space: nowrap;
            transition: opacity 0.2s;
        }
        .rima-co-btn-primary:hover { opacity: 0.9; }

        .rima-co-status {
            font-size: 13px;
            font-weight: 600;
            min-height: 18px;
        }
        .rima-status-ok { color: #059669; }
        .rima-status-err { color: #dc2626; }
        .rima-status-loading { color: #d97706; }
        .rima-co-hint {
            font-size: 13px;
            color: #64748b;
            margin: 0 0 14px;
            line-height: 1.5;
        }

        /* Excel Upload */
        .rima-co-excel-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            padding: 24px 16px;
            text-align: center;
            transition: all 0.2s;
            background: #fafbfc;
            cursor: pointer;
            margin-bottom: 12px;
        }
        .rima-co-excel-upload-area.drag-over {
            border-color: #1d4ed8;
            background: rgba(29, 78, 216, 0.04);
        }
        .rima-co-excel-icon { font-size: 32px; margin-bottom: 8px; }
        .rima-co-excel-text { font-size: 13px; color: #64748b; margin-bottom: 12px; }
        .rima-co-btn-excel {
            padding: 9px 20px;
            background: #fff;
            border: 1.5px solid #1d4ed8;
            color: #1d4ed8;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .rima-co-btn-excel:hover { background: #1d4ed8; color: #fff; }
        .rima-co-excel-filename {
            margin-top: 10px;
            font-size: 13px;
            color: #059669;
            font-weight: 600;
        }
        .rima-co-demo-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 600;
            padding: 6px 0;
        }
        .rima-co-demo-link:hover { text-decoration: underline; }

        @media (max-width: 576px) {
            .rima-co-grid-2 { grid-template-columns: 1fr; }
            .rima-co-tab span { display: none; }
        }
        </style>

        <script>
        jQuery(document).ready(function($) {
            'use strict';

            /* ========================
               TAB SWITCHING
               ======================== */
            function switchTab(tab) {
                $('.rima-co-tab').removeClass('is-active');
                $('.rima-co-tab[data-tab="' + tab + '"]').addClass('is-active');
                $('#rimaEntityTypeField').val(tab);

                if (tab === 'pj') {
                    $('#rimaTabPf').hide();
                    $('#rimaTabPj').show();
                    // Set WC billing_company field if we have it
                    var co = $('#rimaBillingCompany').val();
                    if (co) $('#billing_company').val(co);
                } else {
                    $('#rimaTabPj').hide();
                    $('#rimaTabPf').show();
                    // Clear company from WC fields so it doesn't look like PJ
                    $('#billing_company').val('');
                }
            }

            $('.rima-co-tab').on('click', function() {
                switchTab($(this).data('tab'));
            });

            /* ========================
               LOAD SAVED PF ADDRESS
               ======================== */
            $('#rimaSavedAddrSelect').on('change', function() {
                var data = $(this).find('option:selected').data('json');
                if (!data) return;
                $('#rimaSelectedAddrId').val(data.id || '');
                $('#billing_first_name').val(data.first_name || '');
                $('#billing_last_name').val(data.last_name || '');
                $('#billing_address_1').val(data.address_1 || '');
                $('#billing_address_2').val(data.address_2 || '');
                $('#billing_city').val(data.city || '');
                $('#billing_postcode').val(data.postcode || '');
                $('#billing_phone').val(data.phone || '');
                $('#billing_email').val(data.email || '');
                if (data.state)   { $('#billing_state').val(data.state).trigger('change'); }
                if (data.country) { $('#billing_country').val(data.country).trigger('change'); }
                $('body').trigger('update_checkout');
            });

            /* ========================
               LOAD SAVED PJ ADDRESS
               ======================== */
            $('#rimaSavedPjSelect').on('change', function() {
                var data = $(this).find('option:selected').data('json');
                if (!data) return;
                $('#rimaSelectedAddrId').val(data.id || '');
                $('#rimaBillingVat').val(data.vat_number || data.cui || '');
                $('#rimaBillingRegCom').val(data.reg_com || '');
                $('#rimaBillingCompany').val(data.company || '');
                $('#rimaBillingIban').val(data.iban || '');
                // Mirror to WC fields
                $('#billing_company').val(data.company || '');
                $('#billing_address_1').val(data.address_1 || '');
                $('#billing_city').val(data.city || '');
                $('#billing_postcode').val(data.postcode || '');
                $('#billing_phone').val(data.phone || '');
                if (data.state)   { $('#billing_state').val(data.state).trigger('change'); }
                if (data.country) { $('#billing_country').val(data.country).trigger('change'); }
                $('body').trigger('update_checkout');
            });

            /* ========================
               ANAF LOOKUP (CHECKOUT)
               ======================== */
            $('#rimaCheckoutAnafBtn').on('click', function() {
                var cui = $('#rimaCheckoutCui').val().trim();
                if (!cui) return;

                var $btn  = $(this);
                var $text = $btn.find('.rima-anaf-text');
                var $spin = $btn.find('.rima-anaf-spin');
                $text.hide(); $spin.show();
                $btn.prop('disabled', true);
                $('#rimaCheckoutAnafStatus').html('<span class="rima-status-loading">⏳ Se comunică cu ANAF...</span>');

                $.post('<?php echo esc_js($ajax_url); ?>', {
                    action: 'rima_anaf_lookup',
                    cui: cui,
                    nonce: '<?php echo esc_js($nonce); ?>'
                }, function(res) {
                    if (res.success && res.data) {
                        var d = res.data;
                        // Fill PJ custom fields
                        if (d.name)    { $('#rimaBillingCompany').val(d.name); }
                        if (cui)       { $('#rimaBillingVat').val(cui); }
                        if (d.reg_com) { $('#rimaBillingRegCom').val(d.reg_com); }
                        if (d.phone)   { $('#rimaBillingCoPhone').val(d.phone); }

                        // Mirror to WC standard fields (for order processing)
                        if (d.name)    { $('#billing_company').val(d.name); }
                        if (d.address) { $('#billing_address_1').val(d.address); }
                        if (d.city)    { $('#billing_city').val(d.city); }
                        if (d.phone)   { $('#billing_phone').val(d.phone); }

                        // Match county to WC state select
                        if (d.county) {
                            $('#billing_state option').each(function() {
                                if ($(this).text().toLowerCase().indexOf(d.county.toLowerCase()) !== -1) {
                                    $('#billing_state').val($(this).val()).trigger('change');
                                    return false;
                                }
                            });
                        }

                        $('#billing_company, #billing_city, #billing_address_1').trigger('change');
                        $('body').trigger('update_checkout');

                        $('#rimaCheckoutAnafStatus').html('<span class="rima-status-ok">✅ Companie găsită și completată cu succes!</span>');
                    } else {
                        $('#rimaCheckoutAnafStatus').html('<span class="rima-status-err">❌ ' + (res.data || 'CUI-ul nu a fost găsit în ANAF.') + '</span>');
                    }
                }).fail(function() {
                    $('#rimaCheckoutAnafStatus').html('<span class="rima-status-err">❌ Eroare de conexiune ANAF.</span>');
                }).always(function() {
                    $text.show(); $spin.hide();
                    $btn.prop('disabled', false);
                });
            });

            /* ========================
               SYNC PJ COMPANY TO WC
               ======================== */
            $('#rimaBillingCompany').on('input', function() {
                $('#billing_company').val($(this).val());
            });

            /* ========================
               EXCEL DRAG & DROP
               ======================== */
            var $zone  = $('#rimaExcelDropZone');
            var $input = $('#anaStudentsExcel');

            $('#rimaBtnChooseExcel').on('click', function() { $input.trigger('click'); });
            $zone.on('click', function(e) {
                if (!$(e.target).is('button')) $input.trigger('click');
            });

            $input.on('change', function() {
                if (this.files && this.files[0]) {
                    $('#rimaExcelFileName').text('📎 ' + this.files[0].name);
                }
            });

            $zone.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('drag-over');
            }).on('dragleave', function() {
                $(this).removeClass('drag-over');
            }).on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('drag-over');
                var files = e.originalEvent.dataTransfer.files;
                if (files.length) {
                    $input[0].files = files;
                    $('#rimaExcelFileName').text('📎 ' + files[0].name);
                }
            });

            // Init tab
            switchTab('pf');
        });
        </script>
        <?php
    }

    public function validate_excel_upload() {
        if ( isset($_FILES['ana_students_excel']) && ! empty($_FILES['ana_students_excel']['name']) ) {
            $ext = strtolower( pathinfo( $_FILES['ana_students_excel']['name'], PATHINFO_EXTENSION ) );
            if ( ! in_array( $ext, ['xls', 'xlsx'] ) ) {
                wc_add_notice( 'Fișierul trebuie să fie format Excel (.xls sau .xlsx)', 'error' );
            }
        }
    }

    public function save_excel_upload( $order_id, $data ) {
        // Save entity type to order meta
        if ( isset($_POST['rima_checkout_entity_type']) ) {
            update_post_meta( $order_id, '_rima_entity_type', sanitize_text_field($_POST['rima_checkout_entity_type']) );
        }
        if ( isset($_POST['billing_vat_number']) ) {
            update_post_meta( $order_id, '_billing_vat_number', sanitize_text_field($_POST['billing_vat_number']) );
        }
        if ( isset($_POST['billing_reg_com']) ) {
            update_post_meta( $order_id, '_billing_reg_com', sanitize_text_field($_POST['billing_reg_com']) );
        }
        if ( isset($_POST['billing_iban']) ) {
            update_post_meta( $order_id, '_billing_iban', sanitize_text_field($_POST['billing_iban']) );
        }

        // Save Excel
        if ( isset($_FILES['ana_students_excel']) && ! empty($_FILES['ana_students_excel']['name']) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
            $attachment_id = media_handle_upload( 'ana_students_excel', $order_id );
            if ( ! is_wp_error( $attachment_id ) ) {
                update_post_meta( $order_id, '_ana_students_excel_id', $attachment_id );
            }
        }
    }

    /**
     * MY ACCOUNT: PF/PJ pill toggle on edit address page
     */
    public function render_my_account_pf_pj_selector( $load_address ) {
        if ( $load_address !== 'billing' ) return;

        $user_id = get_current_user_id();
        $company = get_user_meta( $user_id, 'billing_company', true );
        $is_pj   = ! empty( $company );
        $ajax_url = admin_url('admin-ajax.php');
        $nonce    = wp_create_nonce('rima_anaf_lookup');
        ?>
        <div class="rima-address-type-selector mb-4">
            <div class="d-flex w-100 bg-light rounded-pill p-1 shadow-sm" style="border: 1px solid #e2e8f0; max-width: 600px; margin: 0 auto;">
                <label class="flex-fill m-0 rima-type-label text-center rounded-pill py-2 pf-label <?php echo ! $is_pj ? 'active-type bg-primary text-white' : 'text-muted'; ?>" style="cursor:pointer; transition: all 0.3s;">
                    <input type="radio" name="ana_account_address_type" value="pf" class="d-none rima-type-radio" <?php checked( $is_pj, false ); ?>>
                    <i class="fa fa-user me-2"></i><strong>PERSOANĂ FIZICĂ</strong>
                </label>
                <label class="flex-fill m-0 rima-type-label text-center rounded-pill py-2 pj-label <?php echo $is_pj ? 'active-type bg-primary text-white' : 'text-muted'; ?>" style="cursor:pointer; transition: all 0.3s;">
                    <input type="radio" name="ana_account_address_type" value="pj" class="d-none rima-type-radio" <?php checked( $is_pj, true ); ?>>
                    <i class="fa fa-building me-2"></i><strong>PERSOANĂ JURIDICĂ</strong>
                </label>
            </div>
        </div>

        <div id="ana_pj_extra_fields" style="display:none;" class="mb-4 p-4 bg-light rounded-4 border shadow-sm">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="fa fa-search text-primary"></i> Căutare Automată Firmă (ANAF)
            </h5>
            <div class="d-flex gap-2 mb-3">
                <input type="text" id="ana_anaf_cui" class="form-control form-control-lg rounded-3" placeholder="Introdu CUI (ex: 42467528)">
                <button type="button" id="ana_anaf_lookup_btn" class="btn btn-primary fw-bold px-4 rounded-3">Caută</button>
            </div>
            <div id="ana_anaf_status" class="small fw-medium mb-3"></div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            function handleTypeChange() {
                var val = $('input[name="ana_account_address_type"]:checked').val();
                $('.rima-type-label').removeClass('active-type bg-primary text-white').addClass('text-muted');
                if (val === 'pj') {
                    $('.pj-label').addClass('active-type bg-primary text-white').removeClass('text-muted');
                    $('#ana_pj_extra_fields').slideDown();
                    $('p#billing_company_field, p#billing_cui_field, p#billing_reg_com_field, p#billing_vat_field').slideDown();
                } else {
                    $('.pf-label').addClass('active-type bg-primary text-white').removeClass('text-muted');
                    $('#ana_pj_extra_fields').slideUp();
                    $('p#billing_company_field, p#billing_cui_field, p#billing_reg_com_field, p#billing_vat_field').slideUp();
                    $('#billing_company').val('');
                }
            }
            $('input[name="ana_account_address_type"]').on('change', handleTypeChange);
            setTimeout(handleTypeChange, 100);

            $('#ana_anaf_lookup_btn').on('click', function(e) {
                e.preventDefault();
                var cui = $('#ana_anaf_cui').val().trim();
                if (!cui) return;
                var $btn = $(this);
                $btn.text('Se caută...').prop('disabled', true);
                $('#ana_anaf_status').html('<span class="text-warning">⏳ Se comunică cu ANAF...</span>');

                $.post('<?php echo esc_js($ajax_url); ?>', {
                    action: 'rima_anaf_lookup',
                    cui: cui,
                    nonce: '<?php echo esc_js($nonce); ?>'
                }, function(res) {
                    $btn.text('Caută').prop('disabled', false);
                    if (res.success && res.data) {
                        var d = res.data;
                        $('#ana_anaf_status').html('<span class="text-success">✅ Companie găsită și completată!</span>');
                        if (d.name)    { $('#billing_company').val(d.name); }
                        if (d.address) { $('#billing_address_1').val(d.address); }
                        if (d.city)    { $('#billing_city').val(d.city); }
                        if (d.reg_com) { /* custom field */ }
                        $('#billing_company, #billing_city, #billing_address_1').trigger('change');
                    } else {
                        $('#ana_anaf_status').html('<span class="text-danger">❌ ' + (res.data || 'CUI-ul nu a fost găsit.') + '</span>');
                    }
                }).fail(function() {
                    $btn.text('Caută').prop('disabled', false);
                    $('#ana_anaf_status').html('<span class="text-danger">❌ Eroare conexiune ANAF.</span>');
                });
            });
        });
        </script>
        <?php
    }
}

new ANA_Addresses_Frontend_UI();
