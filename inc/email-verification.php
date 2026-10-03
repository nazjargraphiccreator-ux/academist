<?php
/* ============================================================
   11. EMAIL VERIFICATION ON REGISTRATION
   ============================================================ */
// 1. Prevent auto-login and set user as unverified
add_filter( 'woocommerce_registration_auth_new_customer', '__return_false' );

add_action( 'woocommerce_created_customer', 'rima_require_email_verification_on_register', 10, 3 );
function rima_require_email_verification_on_register( $customer_id, $new_customer_data, $password_generated ) {
    $activation_hash = wp_generate_password( 20, false );
    update_user_meta( $customer_id, 'rima_is_activated', '0' );
    update_user_meta( $customer_id, 'rima_activation_hash', $activation_hash );
    
    // Send Email
    $user = get_user_by( 'id', $customer_id );
    $my_account_url = wc_get_page_permalink( 'myaccount' );
    $activation_link = add_query_arg( array(
        'rima_activate' => $customer_id,
        'hash'          => $activation_hash
    ), $my_account_url );
    
    $subject = "Confirm Your Account - RIMA Academy";
    
    // HTML Email Template
    ob_start(); ?>
    <div style="background:linear-gradient(135deg,#C8102E 0%,#8B0A1E 100%);padding:32px 40px;text-align:center;">
        <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128231;</div>
        <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;font-family:Arial,sans-serif;">Activate Your Account</h1>
        <p style="color:rgba(255,255,255,.85);font-size:14px;margin-top:10px;line-height:1.6;font-family:Arial,sans-serif;">Just one more step to start your language journey.</p>
    </div>
    <div style="background:#1A2E45;padding:36px 40px;font-family:Arial,sans-serif;">
        <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo esc_html( $user->display_name ?: $user->user_login ); ?>,</p>
        <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Thank you for joining RIMA Academy! Please verify your email address to activate your account and access all our courses and features.</p>
        
        <div style="text-align:center;margin:35px 0;">
            <a href="<?php echo esc_url_raw($activation_link); ?>" style="display:inline-block;background:linear-gradient(135deg,#C8102E 0%,#8B0A1E 100%);color:#fff;font-size:16px;font-weight:700;padding:16px 45px;border-radius:50px;letter-spacing:0.5px;text-decoration:none;box-shadow:0 8px 24px rgba(200,16,46,0.4);">Verify Email Address</a>
        </div>
        
        <p style="color:rgba(255,255,255,.5);font-size:13px;line-height:1.5;margin-bottom:0;">If the button doesn't work, copy and paste this link into your browser:<br><br><a href="<?php echo esc_url_raw($activation_link); ?>" style="color:#C8102E;word-break:break-all;"><?php echo esc_url_raw($activation_link); ?></a></p>
    </div>
    <?php
    $message_html = ob_get_clean();
    
    if ( function_exists('rima_send_email') ) {
        rima_send_email( $user->user_email, $subject, $message_html, 'Please verify your email address to activate your account.' );
    } else {
        // Fallback if the function is missing for some reason
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $user->user_email, $subject, $message_html, $headers );
    }
}

// 2. Add notice after registration redirect
add_filter( 'woocommerce_registration_redirect', 'rima_registration_redirect_notice' );
function rima_registration_redirect_notice( $redirect ) {
    wc_add_notice( __('Registration successful! Please check your email inbox to activate your account.', 'woocommerce'), 'success' );
    return wc_get_page_permalink( 'myaccount' );
}

// 3. Block login if unverified
add_filter( 'woocommerce_process_login_errors', 'rima_prevent_unverified_login', 10, 3 );
function rima_prevent_unverified_login( $validation_error, $login, $password ) {
    $user = get_user_by( 'login', $login );
    if ( ! $user ) {
        $user = get_user_by( 'email', $login );
    }
    
    if ( $user ) {
        $is_activated = get_user_meta( $user->ID, 'rima_is_activated', true );
        if ( $is_activated === '0' ) {
            $validation_error->add( 'unverified_account', __('You must verify your email address before logging in. Please check your inbox.', 'woocommerce') );
        }
    }
    return $validation_error;
}

// 4. Handle activation link click
add_action( 'template_redirect', 'rima_process_email_verification' );
function rima_process_email_verification() {
    if ( isset( $_GET['rima_activate'] ) && isset( $_GET['hash'] ) ) {
        $user_id = intval( $_GET['rima_activate'] );
        $hash    = sanitize_text_field( $_GET['hash'] );
        
        $stored_hash = get_user_meta( $user_id, 'rima_activation_hash', true );
        if ( $stored_hash === $hash && !empty($hash) ) {
            update_user_meta( $user_id, 'rima_is_activated', '1' );
            delete_user_meta( $user_id, 'rima_activation_hash' );
            wc_add_notice( __('Your account has been successfully verified! You can now log in.', 'woocommerce'), 'success' );
            
            wp_redirect( wc_get_page_permalink( 'myaccount' ) );
            exit;
        } else {
            wc_add_notice( __('Invalid or expired activation link.', 'woocommerce'), 'error' );
            wp_redirect( wc_get_page_permalink( 'myaccount' ) );
            exit;
        }
    }
}

require_once get_stylesheet_directory() . '/inc/bacs-payment-proof.php';
