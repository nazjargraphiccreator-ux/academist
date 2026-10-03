<?php
/* ============================================================
   7. FORCE ENABLE REGISTRATION (Bypass WP Option Check)
   ============================================================ */
add_filter( 'pre_option_users_can_register', '__return_true' );
add_filter( 'option_users_can_register', '__return_true' );
add_filter( 'pre_option_woocommerce_enable_myaccount_registration', function() { return 'yes'; } );
add_filter( 'option_woocommerce_enable_myaccount_registration', function() { return 'yes'; } );

require_once get_stylesheet_directory() . '/inc/woo-login-shortcode.php';
