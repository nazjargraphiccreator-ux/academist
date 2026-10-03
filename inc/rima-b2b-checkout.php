<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * RIMA B2B Checkout Logic
 * Adds PF/PJ toggle and AJAX Excel upload for students
 */

// 1. Add fields before billing form
add_action( 'woocommerce_checkout_billing', 'rima_add_checkout_client_type', 5 );
function rima_add_checkout_client_type( $checkout ) {
    ?>
    <div class="rima-client-type-wrapper" style="margin-bottom: 20px; padding: 15px; background: var(--rima-surface-alt); border-radius: 8px; border: 1px solid var(--rima-border);">
        <h3 style="font-size: 16px; margin-bottom: 10px; color: var(--rima-text);">Tip Client / Facturare</h3>
        <div style="display: flex; gap: 20px;">
            <label style="cursor: pointer; display: flex; align-items: center; gap: 5px; color: var(--rima-text-muted);">
                <input type="radio" name="rima_client_type" value="pf" checked> Persoană Fizică (Individual)
            </label>
            <label style="cursor: pointer; display: flex; align-items: center; gap: 5px; color: var(--rima-text-muted);">
                <input type="radio" name="rima_client_type" value="pj"> Persoană Juridică (Companie)
            </label>
        </div>
    </div>

    <!-- ANAF Lookup Container (shown only for B2B) -->
    <div id="rima-b2b-anaf-container" style="display: none; margin-bottom: 20px; padding: 15px; background: var(--rima-surface-alt); border-radius: 8px; border: 1px solid var(--rima-border);">
        <h3 style="font-size: 15px; margin-bottom: 10px; color: var(--rima-text);">Date Companie (Căutare ANAF)</h3>
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" id="rima_b2b_cui" name="billing_cui" placeholder="Introdu CUI (ex: RO123456)" style="flex: 1; padding: 10px; border-radius: 6px; border: 1px solid var(--rima-border); background: var(--rima-surface); color: var(--rima-text); font-size: 14px;">
            <button type="button" id="rima_b2b_anaf_lookup" style="background: var(--rima-primary); color: #fff; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; transition: 0.2s;">Caută</button>
        </div>
        <input type="hidden" name="billing_reg_com" id="rima_b2b_reg_com" value="">
        <div id="rima_b2b_anaf_status" style="font-size: 13px; margin-top: 8px;"></div>
    </div>

    <!-- Container for Excel Upload -->
    <div id="rima-b2b-upload-container" style="display: none; margin-bottom: 20px; padding: 20px; background: rgba(99, 102, 241, 0.05); border-radius: 8px; border: 1px dashed rgba(99, 102, 241, 0.3);">
        <h3 style="font-size: 15px; margin-bottom: 5px; color: #818cf8; display:flex; align-items:center; gap:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
            Înrolare Studenți (Opțional)
        </h3>
        <p style="font-size: 13px; color: #9ca3af; margin-bottom: 15px;">Dacă achiziționați cursuri pentru angajați, puteți încărca un tabel Excel (.xlsx) cu numele și adresele lor de email pentru a le crea automat conturi după plată.</p>
        
        <input type="file" id="rima_b2b_excel_file" accept=".xlsx,.xls" style="display: none;">
        <input type="hidden" name="rima_b2b_excel_url" id="rima_b2b_excel_url" value="">
        
        <div style="display: flex; gap: 10px; align-items: center;">
            <button type="button" id="rima_b2b_trigger_upload" style="background: #4f46e5; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">
                Alege Fișier Excel
            </button>
            <span id="rima_b2b_upload_status" style="font-size: 13px; color: #ccc;">Niciun fișier selectat</span>
        </div>
    </div>
    <?php
}

// 2. Add JavaScript to handle toggle and AJAX upload
add_action( 'wp_footer', 'rima_b2b_checkout_scripts' );
function rima_b2b_checkout_scripts() {
    if ( ! is_checkout() ) return;
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Toggle Company Fields
        function toggleB2BFields() {
            var val = $('input[name="rima_client_type"]:checked').val();
            if (val === 'pj') {
                $('#billing_company_field').show();
                $('#rima-b2b-anaf-container').slideDown();
                $('#rima-b2b-upload-container').slideDown();
            } else {
                $('#billing_company_field').hide();
                $('#rima-b2b-anaf-container').slideUp();
                $('#rima-b2b-upload-container').slideUp();
            }
        }

        $('input[name="rima_client_type"]').on('change', toggleB2BFields);
        toggleB2BFields(); // Init on load

        // Handle ANAF Lookup
        $('#rima_b2b_anaf_lookup').on('click', function(e) {
            e.preventDefault();
            var rawCui = $.trim($('#rima_b2b_cui').val());
            if (!rawCui) {
                $('#rima_b2b_anaf_status').html('<span style="color:#ef4444;">Introduți CUI-ul firmei.</span>');
                return;
            }
            // Strip RO/ro prefix (e.g. "RO123456" → "123456")
            var cui = rawCui.replace(/^[Rr][Oo]\s*/,'').replace(/[^0-9]/g,'');
            if (!cui) {
                $('#rima_b2b_anaf_status').html('<span style="color:#ef4444;">CUI invalid. Introducți doar cifrele sau formatul RO123456.</span>');
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).text('Se caută...').css('opacity', '0.7');
            $('#rima_b2b_anaf_status').html('<span style="color:#fbbf24;">&#9679; Se comunică cu serverele ANAF...</span>');
            // Remove any old company card
            $('#rima_b2b_company_card').remove();

            $.post('<?php echo admin_url("admin-ajax.php"); ?>', {
                action: 'rima_anaf_lookup',
                cui: cui,
                nonce: '<?php echo wp_create_nonce("rima_anaf_lookup"); ?>'
            }, function(res) {
                $btn.prop('disabled', false).text('Caută').css('opacity', '1');
                if (res.success) {
                    var d = res.data;
                    $('#rima_b2b_anaf_status').html('<span style="color:#34d399;">&#10003; Firmă găsită la ANAF!</span>');

                    // Show readonly company card
                    var cardHtml = '<div id="rima_b2b_company_card" style="margin-top:12px; padding:14px; background:rgba(52,211,153,.07); border:1px solid rgba(52,211,153,.35); border-radius:10px; font-size:13px; line-height:1.8;">' +
                        '<strong style="display:block;font-size:14px;color:#1e293b;margin-bottom:6px;">' + (d.name || '') + '</strong>' +
                        (d.reg_com  ? '<span style="color:#475569;">Reg. Com.: <strong>' + d.reg_com + '</strong></span><br>' : '') +
                        (d.address  ? '<span style="color:#475569;">Adresă: '        + d.address  + (d.city ? ', ' + d.city : '') + (d.county ? ', Jud. ' + d.county : '') + '</span><br>' : '') +
                        (d.zip      ? '<span style="color:#475569;">Cod Po\u015ftal: ' + d.zip      + '</span><br>' : '') +
                        (d.phone    ? '<span style="color:#475569;">Telefon: '        + d.phone    + '</span><br>' : '') +
                        (d.status   ? '<span style="color:#6b7280;font-size:11px;">Stare: ' + d.status + '</span>' : '') +
                        '</div>';
                    $('#rima_b2b_anaf_status').after(cardHtml);

                    // Autofill standard WC fields
                    if ($('#billing_company').length && d.name)    $('#billing_company').val(d.name).trigger('change');
                    if ($('#billing_address_1').length && d.address) $('#billing_address_1').val(d.address).trigger('change');
                    if ($('#billing_city').length && d.city)       $('#billing_city').val(d.city).trigger('change');
                    if ($('#billing_postcode').length && d.zip)    $('#billing_postcode').val(d.zip).trigger('change');
                    if ($('#billing_state').length && d.county)    $('#billing_state').val(d.county).trigger('change');
                    if ($('#billing_phone').length && d.phone)     $('#billing_phone').val(d.phone).trigger('change');

                    // Save custom fields
                    $('#rima_b2b_reg_com').val(d.reg_com || '');
                    $('#rima_b2b_cui').val(d.cui || cui);

                    // Tell WooCommerce to update totals
                    $(document.body).trigger('update_checkout');
                } else {
                    $('#rima_b2b_anaf_status').html('<span style="color:#ef4444;">&#10005; ' + (res.data || 'Eroare necunoscută') + '</span>');
                }
            }).fail(function() {
                $btn.prop('disabled', false).text('Caută').css('opacity', '1');
                $('#rima_b2b_anaf_status').html('<span style="color:#ef4444;">Eroare de conexiune la server. Încercați din nou.</span>');
            });
        });

        // Allow Enter key in CUI field
        $('#rima_b2b_cui').on('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); $('#rima_b2b_anaf_lookup').trigger('click'); }
        });

        // Handle AJAX Excel Upload
        $('#rima_b2b_trigger_upload').on('click', function(e) {
            e.preventDefault();
            $('#rima_b2b_excel_file').click();
        });

        $('#rima_b2b_excel_file').on('change', function() {
            var file_data = $(this).prop('files')[0];
            if (!file_data) return;

            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('action', 'rima_upload_b2b_excel');
            form_data.append('nonce', '<?php echo wp_create_nonce("rima_b2b_upload"); ?>');

            $('#rima_b2b_upload_status').html('<span style="color:#fbbf24;">Se încarcă...</span>');

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        $('#rima_b2b_excel_url').val(response.data.url);
                        $('#rima_b2b_upload_status').html('<span style="color:#34d399;">✓ Fișier încărcat cu succes!</span>');
                    } else {
                        $('#rima_b2b_upload_status').html('<span style="color:#ef4444;">Eroare: ' + (response.data || 'Necunoscută') + '</span>');
                    }
                },
                error: function() {
                    $('#rima_b2b_upload_status').html('<span style="color:#ef4444;">Eroare de conexiune la server.</span>');
                }
            });
        });
    });
    </script>
    <?php
}

// 3. AJAX Handler for the file upload
add_action( 'wp_ajax_rima_upload_b2b_excel', 'rima_ajax_upload_b2b_excel' );
add_action( 'wp_ajax_nopriv_rima_upload_b2b_excel', 'rima_ajax_upload_b2b_excel' );
function rima_ajax_upload_b2b_excel() {
    check_ajax_referer('rima_b2b_upload', 'nonce');

    if ( empty($_FILES['file']) ) {
        wp_send_json_error('Niciun fișier selectat.');
    }

    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ( !in_array($ext, ['xls', 'xlsx']) ) {
        wp_send_json_error('Format nepermis. Doar .xlsx sau .xls.');
    }

    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    $upload_overrides = array( 'test_form' => false, 'mimes' => array('xls' => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') );
    $movefile = wp_handle_upload( $file, $upload_overrides );

    if ( $movefile && ! isset( $movefile['error'] ) ) {
        wp_send_json_success( array( 'url' => $movefile['url'], 'file' => $movefile['file'] ) );
    } else {
        wp_send_json_error( $movefile['error'] );
    }
}

// 4. AJAX Handler for ANAF Lookup
add_action( 'wp_ajax_rima_anaf_lookup', 'rima_ajax_anaf_lookup_handler' );
add_action( 'wp_ajax_nopriv_rima_anaf_lookup', 'rima_ajax_anaf_lookup_handler' );
function rima_ajax_anaf_lookup_handler() {
    check_ajax_referer('rima_anaf_lookup', 'nonce');
    
    $cui = preg_replace( '/[^0-9]/', '', $_POST['cui'] ?? '' );
    if ( empty($cui) ) wp_send_json_error( 'Cod Unic de Înregistrare invalid.' );

    $body = [ [ 'cui' => (int) $cui, 'data' => date('Y-m-d') ] ];

    $response = wp_remote_post(
        'https://webservicesp.anaf.ro/api/PlatitorTvaRest/v9/tva',
        [
            'body'    => wp_json_encode( $body ),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            'timeout'   => 15,
            'sslverify' => true,
        ]
    );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'Eroare conexiune ANAF: ' . $response->get_error_message() );
    }

    $http_code = wp_remote_retrieve_response_code( $response );
    if ( $http_code !== 200 ) {
        wp_send_json_error( 'Serverul ANAF a returnat eroare (HTTP ' . $http_code . ').' );
    }

    $raw  = wp_remote_retrieve_body( $response );
    $data = json_decode( $raw, true );

    // ANAF v9 returns { "cod": 200, "message": "SUCCESS", "found": [...], "notFound": [...] }
    if ( empty( $data['found'] ) || ! is_array( $data['found'] ) ) {
        wp_send_json_error( 'Firma nu a fost găsită la ANAF. Verificați CUI-ul.' );
    }

    $company = $data['found'][0];
    $gen     = $company['date_generale'] ?? [];

    // In ANAF v9, address is a flat string in date_generale.adresa
    // and there may also be inregistrare_scop_Tva sub-object
    $address_str = $gen['adresa'] ?? '';
    $city        = '';
    $county      = '';
    $zip         = $gen['codPostal'] ?? '';

    // Try to extract city from inregistrare_scop_Tva if available
    if ( ! empty( $company['inregistrare_scop_Tva']['perioade_TVA'] ) ) {
        // Not structured enough for city extraction here
    }

    // Try adresa_sediu_social if present (some versions)
    if ( ! empty( $company['adresa_sediu_social'] ) ) {
        $addr    = $company['adresa_sediu_social'];
        $parts   = [];
        if ( ! empty( $addr['sdenumire_Strada'] ) )  $parts[] = 'Str. ' . $addr['sdenumire_Strada'];
        if ( ! empty( $addr['snumar_Strada'] ) )     $parts[] = 'Nr. '  . $addr['snumar_Strada'];
        if ( ! empty( $addr['sdenumire_Bloc'] ) )    $parts[] = 'Bl. '  . $addr['sdenumire_Bloc'];
        if ( ! empty( $addr['sdenumire_Scara'] ) )   $parts[] = 'Sc. '  . $addr['sdenumire_Scara'];
        if ( ! empty( $addr['sdenumire_Etaj'] ) )    $parts[] = 'Et. '  . $addr['sdenumire_Etaj'];
        if ( ! empty( $addr['sdenumire_Ap'] ) )      $parts[] = 'Ap. '  . $addr['sdenumire_Ap'];
        $address_str = implode( ', ', $parts );
        $city        = $addr['sdenumire_Localitate'] ?? '';
        $county      = $addr['sdenumire_Judet']      ?? '';
        $zip         = $addr['scod_Postal']          ?? $zip;
    }

    wp_send_json_success( [
        'name'    => $gen['denumire']  ?? '',
        'reg_com' => $gen['nrRegCom']  ?? '',
        'cui'     => $gen['cui']       ?? $cui,
        'address' => $address_str,
        'city'    => $city,
        'county'  => $county,
        'zip'     => $zip,
        'phone'   => $gen['telefon']   ?? '',
        'status'  => $gen['stare_inregistrare'] ?? '',
    ] );
}

// 5. Save fields to Order Meta
add_action( 'woocommerce_checkout_update_order_meta', 'rima_save_b2b_checkout_fields' );
function rima_save_b2b_checkout_fields( $order_id ) {
    if ( ! empty( $_POST['rima_client_type'] ) ) {
        update_post_meta( $order_id, '_rima_client_type', sanitize_text_field( $_POST['rima_client_type'] ) );
    }
    if ( ! empty( $_POST['billing_cui'] ) ) {
        update_post_meta( $order_id, '_billing_cui', sanitize_text_field( $_POST['billing_cui'] ) );
    }
    if ( ! empty( $_POST['billing_reg_com'] ) ) {
        update_post_meta( $order_id, '_billing_reg_com', sanitize_text_field( $_POST['billing_reg_com'] ) );
    }
    if ( ! empty( $_POST['rima_b2b_excel_url'] ) ) {
        update_post_meta( $order_id, '_rima_b2b_excel_url', esc_url_raw( $_POST['rima_b2b_excel_url'] ) );
    }
}

// 6. Display fields in WP Admin Order Edit (WooCommerce standard)
add_action( 'woocommerce_admin_order_data_after_billing_address', 'rima_display_b2b_fields_in_admin', 10, 1 );
function rima_display_b2b_fields_in_admin( $order ) {
    $client_type = $order->get_meta( '_rima_client_type' );
    $excel_url   = $order->get_meta( '_rima_b2b_excel_url' );
    $cui         = $order->get_meta( '_billing_cui' );
    $reg_com     = $order->get_meta( '_billing_reg_com' );

    echo '<div style="margin-top: 15px; border-top: 1px solid #ccc; padding-top: 10px;">';
    echo '<p><strong>Tip Client:</strong> ' . ($client_type === 'pj' ? 'Persoană Juridică' : 'Persoană Fizică') . '</p>';
    if ( $client_type === 'pj' ) {
        if ( !empty($cui) ) echo '<p><strong>CUI:</strong> ' . esc_html($cui) . '</p>';
        if ( !empty($reg_com) ) echo '<p><strong>Reg. Com:</strong> ' . esc_html($reg_com) . '</p>';
        if ( !empty($excel_url) ) {
            echo '<p><strong>Excel Studenți:</strong> <a href="' . esc_url($excel_url) . '" target="_blank" class="button button-small" style="background:#4f46e5; color:#fff; border:none;">Descarcă Excel</a></p>';
        }
    }
    echo '</div>';
}
