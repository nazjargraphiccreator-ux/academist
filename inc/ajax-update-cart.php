<?php
/* ============================================================
   8.5 AJAX UPDATE CART QUANTITY
   ============================================================ */
add_action('wp_ajax_rima_update_cart_quantity', 'rima_update_cart_quantity');
add_action('wp_ajax_nopriv_rima_update_cart_quantity', 'rima_update_cart_quantity');
function rima_update_cart_quantity() {
    if ( ! isset( $_POST['cart_item_key'] ) || ! isset( $_POST['qty'] ) ) {
        wp_send_json_error();
    }
    $cart_item_key = sanitize_text_field( $_POST['cart_item_key'] );
    $qty = intval( $_POST['qty'] );
    
    if ( $qty <= 0 ) {
        WC()->cart->remove_cart_item( $cart_item_key );
    } else {
        WC()->cart->set_quantity( $cart_item_key, $qty );
    }
    
    WC_AJAX::get_refreshed_fragments();
    wp_die();
}

require_once get_stylesheet_directory() . '/inc/lms-auth-redirects.php';
