<?php
/* ============================================================
   7.1 FORCE CHILD THEME TEMPLATE FOR WOOCOMMERCE LOGIN (ULTIMATE)
   ============================================================ */
add_action('init', function() {
    remove_shortcode('woocommerce_my_account');
    add_shortcode('woocommerce_my_account', 'rima_force_my_account_shortcode');
});

function rima_force_my_account_shortcode($atts) {
    ob_start();
    
    global $wp;
    
    if ( is_user_logged_in() ) {
        // Let WooCommerce handle the logged-in view
        if ( function_exists('wc_get_template') ) {
            wc_get_template( 'myaccount/my-account.php', array(
                'current_user' => get_user_by( 'id', get_current_user_id() ),
            ) );
        }
    } elseif ( isset( $wp->query_vars['lost-password'] ) ) {
        // We are on the Lost Password page! Load the lost password template.
        if ( function_exists('wc_get_template') ) {
            wc_get_template( 'myaccount/form-lost-password.php', array(
                'form' => 'lost_password',
            ) );
        }
    } else {
        // FORCE our custom login template!
        $custom_template = get_stylesheet_directory() . '/woocommerce/myaccount/form-login.php';
        if ( file_exists( $custom_template ) ) {
            include $custom_template;
        } else {
            echo '<div class="woocommerce-error">ERROR: Custom RIMA template file is missing at <code>' . esc_html($custom_template) . '</code>. Please make sure the <strong>woocommerce</strong> folder was uploaded inside the academist-child theme.</div>';
            // Fallback to default
            if ( function_exists('wc_get_template') ) {
                wc_get_template( 'myaccount/form-login.php' );
            }
        }
    }
    
    return ob_get_clean();
}

require_once get_stylesheet_directory() . '/inc/auth-shortcodes.php';
