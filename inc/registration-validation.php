<?php
/* ============================================================
   10. REGISTRATION VALIDATION (MATH CAPTCHA & CONFIRM PASSWORD)
   ============================================================ */
add_action( 'woocommerce_register_post', 'rima_validate_custom_register_fields', 10, 3 );
function rima_validate_custom_register_fields( $username, $email, $validation_errors ) {
    
    // 1. Validate Confirm Password
    if ( isset( $_POST['password'] ) && isset( $_POST['rima_confirm_password'] ) ) {
        if ( $_POST['password'] !== $_POST['rima_confirm_password'] ) {
            $validation_errors->add( 'password_mismatch', __( 'The passwords do not match.', 'woocommerce' ) );
        }
    }
    
    // 2. Validate Math Captcha
    if ( isset( $_POST['rima_math_answer'] ) && isset( $_POST['rima_math_hash'] ) ) {
        $answer = sanitize_text_field( $_POST['rima_math_answer'] );
        $hash   = sanitize_text_field( $_POST['rima_math_hash'] );
        
        $expected_hash = md5('rima_math_' . $answer);
        
        if ( $hash !== $expected_hash ) {
            $validation_errors->add( 'math_captcha_error', __( 'The anti-spam math answer is incorrect.', 'woocommerce' ) );
        }
    } else {
        $validation_errors->add( 'math_captcha_missing', __( 'Please solve the anti-spam math question.', 'woocommerce' ) );
    }
}

add_filter( 'woocommerce_process_login_errors', 'rima_validate_custom_login_fields', 10, 3 );
function rima_validate_custom_login_fields( $validation_errors, $login, $password ) {
    
    // Validate Math Captcha for Login
    if ( isset( $_POST['rima_math_answer_login'] ) && isset( $_POST['rima_math_hash_login'] ) ) {
        $answer = sanitize_text_field( $_POST['rima_math_answer_login'] );
        $hash   = sanitize_text_field( $_POST['rima_math_hash_login'] );
        
        $expected_hash = md5('rima_math_' . $answer);
        
        if ( $hash !== $expected_hash ) {
            $validation_errors->add( 'math_captcha_error', __( 'The anti-spam math answer is incorrect.', 'woocommerce' ) );
        }
    } else {
        // Only trigger this if we are actually submitting the login form, since WooCommerce might process logins from other places.
        // We check if the custom nonce is present or just rely on the form submission via our form
        if ( isset($_POST['login']) ) {
            $validation_errors->add( 'math_captcha_missing', __( 'Please solve the anti-spam math question.', 'woocommerce' ) );
        }
    }
    return $validation_errors;
}

require_once get_stylesheet_directory() . '/inc/email-verification.php';
