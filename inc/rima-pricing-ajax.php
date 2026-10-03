<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * RIMA Pricing AJAX Handlers
 * - Dynamic Product Add to Cart
 * - Custom Price Override in Cart
 * - ANAF Company Lookup (v9 API)
 * - XLSX Employee File Upload Handling
 * - Floating Cart
 */

// ─── 1. ADD DYNAMIC PRODUCT TO CART ────────────────────────────────────────
add_action('wp_ajax_rima_add_dynamic_product',        'rima_ajax_add_dynamic');
add_action('wp_ajax_nopriv_rima_add_dynamic_product', 'rima_ajax_add_dynamic');

function rima_ajax_add_dynamic() {
    $lang  = sanitize_text_field($_POST['lang']    ?? '');
    $level = sanitize_text_field($_POST['level']   ?? '');
    $tier  = sanitize_text_field($_POST['tier']    ?? '');
    $price = floatval($_POST['price']              ?? 0);
    $bill  = sanitize_text_field($_POST['billing'] ?? '');

    if ($price <= 0) {
        wp_send_json_error(['message' => 'Invalid price']);
    }

    // Find or create a hidden base product to use for dynamic cart items
    $base_product_id = get_option('rima_base_dynamic_product_id');
    
    if ( ! $base_product_id || get_post_type( $base_product_id ) !== 'product' || get_post_status( $base_product_id ) !== 'publish' ) {
        // Search by title first
        $existing = get_posts([
            'post_type'   => 'product',
            'post_status' => 'publish',
            'title'       => 'Rima Dynamic Course Base',
            'fields'      => 'ids',
            'numberposts' => 1
        ]);

        if ( ! empty( $existing ) ) {
            $base_product_id = $existing[0];
        } else {
            // Create the hidden base product
            $new_product = new WC_Product_Simple();
            $new_product->set_name( 'Rima Dynamic Course Base' );
            $new_product->set_status( 'publish' );
            $new_product->set_catalog_visibility( 'hidden' ); // Hide from shop
            $new_product->set_virtual( true ); // No shipping
            $new_product->set_sold_individually( false );
            $new_product->set_regular_price( 100 ); // Dummy price
            $new_product->set_price( 100 );
            $base_product_id = $new_product->save();
        }
        
        update_option( 'rima_base_dynamic_product_id', $base_product_id );
    }

    WC()->cart->empty_cart();

    $cart_item_data = [
        'rima_custom_price' => $price,
        'rima_plan_name'    => "LMS Package: $lang - $level ($tier)"
    ];

    $added = WC()->cart->add_to_cart($base_product_id, 1, 0, [], $cart_item_data);

    if ($added) {
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();

        $fragments = apply_filters('woocommerce_add_to_cart_fragments', [
            'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
        ]);

        wp_send_json_success([
            'cart_url'  => wc_get_checkout_url(),
            'fragments' => $fragments,
            'cart_hash' => WC()->cart->get_cart_hash()
        ]);
    } else {
        wp_send_json_error(['message' => 'Failed to add to cart']);
    }
}

// ─── 2. OVERRIDE PRICE IN CART ──────────────────────────────────────────────
add_action('woocommerce_before_calculate_totals', 'rima_custom_cart_price', 9999, 1);
function rima_custom_cart_price($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;
    if (did_action('woocommerce_before_calculate_totals') >= 2) return;

    foreach ($cart->get_cart() as $cart_item) {
        if (isset($cart_item['rima_custom_price'])) {
            $cart_item['data']->set_price($cart_item['rima_custom_price']);
        }
    }
}

// ─── 3. DISPLAY CUSTOM PLAN NAME IN CART/CHECKOUT ──────────────────────────
add_filter('woocommerce_cart_item_name', 'rima_custom_cart_item_name', 10, 3);
function rima_custom_cart_item_name($product_name, $cart_item, $cart_item_key) {
    if (isset($cart_item['rima_plan_name'])) {
        return esc_html($cart_item['rima_plan_name']);
    }
    return $product_name;
}

// ─── 4. SAVE METADATA TO ORDER ──────────────────────────────────────────────
add_action('woocommerce_checkout_create_order_line_item', 'rima_save_custom_order_item_meta', 10, 4);
function rima_save_custom_order_item_meta($item, $cart_item_key, $values, $order) {
    if (isset($values['rima_plan_name'])) {
        $item->add_meta_data('Pachet Achiziționat', $values['rima_plan_name'], true);
    }
}

// ─── 5. ANAF COMPANY LOOKUP (v9 API) ────────────────────────────────────────
add_action('wp_ajax_rima_anaf_proxy',        'rima_anaf_proxy_handler');
add_action('wp_ajax_nopriv_rima_anaf_proxy', 'rima_anaf_proxy_handler');

function rima_anaf_proxy_handler() {
    $cui = preg_replace('/[^0-9]/', '', sanitize_text_field($_POST['cui'] ?? ''));
    if (empty($cui)) {
        wp_send_json_error(['message' => 'CUI invalid.']);
    }

    $payload = wp_json_encode([[
        'cui'  => (int)$cui,
        'data' => date('Y-m-d')
    ]]);

    $response = wp_remote_post('https://webservicesp.anaf.ro/api/PlatitorTvaRest/v9/tva', [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => $payload,
        'timeout' => 15
    ]);

    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (!empty($body['found'][0]['date_generale']['denumire'])) {
            $gen = $body['found'][0]['date_generale'];
            $tva = $body['found'][0]['inregistrare_scop_Tva']['scpTVA'] ?? false;
            wp_send_json_success([
                'denumire' => $gen['denumire'] ?? '',
                'adresa'   => $gen['adresa']   ?? '',
                'tva'      => $tva
            ]);
        }
    }

    wp_send_json_error(['message' => 'Compania nu a fost găsită automat.']);
}

// ─── 6. XLSX / CSV EMPLOYEE FILE UPLOAD ────────────────────────────────────
add_action('wp_ajax_rima_upload_emp_xlsx',        'rima_upload_emp_xlsx_handler');
add_action('wp_ajax_nopriv_rima_upload_emp_xlsx', 'rima_upload_emp_xlsx_handler');

function rima_upload_emp_xlsx_handler() {
    if (empty($_FILES['emp_file'])) {
        wp_send_json_error(['message' => 'No file received.']);
    }

    $file = $_FILES['emp_file'];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
        wp_send_json_error(['message' => 'Format invalid. Acceptat: xlsx, xls, csv.']);
    }

    // Move to uploads folder with a unique name
    $upload_dir  = wp_upload_dir();
    $dest_folder = $upload_dir['basedir'] . '/rima-corp-uploads/';
    wp_mkdir_p($dest_folder);

    $new_filename = 'emp_list_' . time() . '_' . sanitize_file_name($file['name']);
    $dest_path    = $dest_folder . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $dest_path)) {
        // Read CSV rows to extract employee count
        $row_count = 0;
        if ($ext === 'csv') {
            if (($fh = fopen($dest_path, 'r')) !== false) {
                while (fgetcsv($fh) !== false) $row_count++;
                fclose($fh);
                $row_count = max(0, $row_count - 1); // subtract header row
            }
        }

        wp_send_json_success([
            'file'      => $new_filename,
            'emp_count' => $row_count > 0 ? $row_count : null,
            'message'   => 'Fișier încărcat cu succes.'
        ]);
    } else {
        wp_send_json_error(['message' => 'Eroare la salvarea fișierului.']);
    }
}

// ─── 7. FLOATING CART ───────────────────────────────────────────────────────
add_action('wp_footer', 'rima_floating_cart_html');
function rima_floating_cart_html() {
    if (is_checkout() || is_cart()) return;
    $cart_count = WC()->cart->get_cart_contents_count();
    $display    = $cart_count > 0 ? '' : 'display:none;';
    ?>
    <style>
        .rima-floating-cart {
            position: fixed; bottom: 30px; right: 30px;
            background: #091747; color: white; border-radius: 50px;
            padding: 15px 25px; display: flex; align-items: center; gap: 15px;
            box-shadow: 0 10px 25px rgba(9,23,71,0.4); cursor: pointer;
            z-index: 9999; transition: all 0.3s ease; text-decoration: none !important;
        }
        .rima-floating-cart:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(9,23,71,0.5); color: white; }
        .rima-fc-icon { position: relative; }
        .rima-fc-count {
            position: absolute; top: -10px; right: -10px;
            background: #ef4444; color: white; font-size: 12px; font-weight: bold;
            width: 20px; height: 20px; border-radius: 50%;
            display: flex; justify-content: center; align-items: center;
        }
        .rima-fc-details { display: flex; flex-direction: column; line-height: 1.2; }
        .rima-fc-title   { font-size: 12px; text-transform: uppercase; font-weight: bold; opacity: 0.8; }
        .rima-fc-total   { font-size: 16px; font-weight: 900; }
    </style>
    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="rima-floating-cart" id="rima-floating-cart" style="<?php echo $display; ?>">
        <div class="rima-fc-icon">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <span class="rima-fc-count rima-count-val"><?php echo $cart_count; ?></span>
        </div>
        <div class="rima-fc-details">
            <span class="rima-fc-title">Finalizează Plata</span>
            <span class="rima-fc-total">Checkout</span>
        </div>
    </a>
    <script>
    jQuery(document.body).on('added_to_cart removed_from_cart', function(event, fragments, cart_hash, $button) {
        if (fragments && fragments['span.rima-count-val']) {
            var cart = document.getElementById('rima-floating-cart');
            if (cart) {
                cart.style.display = 'flex';
                cart.style.transform = 'scale(1.1)';
                setTimeout(function() { cart.style.transform = 'scale(1)'; }, 200);
            }
        }
    });
    </script>
    <?php
}

add_filter('woocommerce_add_to_cart_fragments', 'rima_floating_cart_fragments');
function rima_floating_cart_fragments($fragments) {
    $cart_count = WC()->cart->get_cart_contents_count();
    $fragments['span.rima-count-val'] = '<span class="rima-fc-count rima-count-val">' . $cart_count . '</span>';
    return $fragments;
}
