<?php
/* ============================================================
   8. BULLETPROOF REGISTRATION & LOGIN SHORTCODE OVERRIDES
   ============================================================ */
add_action('wp_loaded', function() {
    remove_shortcode('eltdf_user_register');
    add_shortcode('eltdf_user_register', 'rima_custom_user_register_html');
});

function rima_custom_user_register_html() {
    ob_start();
    ?>
    <div class="eltdf-social-register-holder">
        <form method="post" class="eltdf-register-form">
            <fieldset>
                <div>
                    <label>Username*</label>
                    <input type="text" name="user_register_name" id="user_register_name" value="" required pattern=".{3,}" title="Three or more characters"/>
                </div>
                <div>
                    <label>Email*</label>
                    <input type="email" name="user_register_email" id="user_register_email" value="" required />
                </div>
                <div>
                    <label>Password*</label>
                    <input type="password" name="user_register_password" id="user_register_password" value="" required />
                </div>
                <div>
                    <label>Repeat Password*</label>
                    <input type="password" name="user_register_confirm_password" id="user_register_confirm_password"  value="" required />
                </div>
                
                <div class="eltdf-register-button-holder" style="margin-top: 20px;">
                    <button type="submit" class="eltdf-btn eltdf-btn-solid eltdf-btn-small" style="background-color: #991b1b !important; border-color: #991b1b !important;"><i class="fa fa-user-plus me-2" style="margin-right: 5px;"></i> Register</button>
                    <?php wp_nonce_field( 'eltdf-ajax-register-nonce', 'eltdf-register-security' ); ?>
                </div>
            </fieldset>
        </form>
        <div class="eltdf-membership-response-holder clearfix"></div>
    </div>
    <?php
    return ob_get_clean();
}

require_once get_stylesheet_directory() . '/inc/ajax-update-cart.php';
