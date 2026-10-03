<?php
/**
 * Lost Password Form - Premium Bilingual Design for RIMA Academy
 * academist-child/woocommerce/myaccount/form-lost-password.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get logo URL
$logo_id  = get_theme_mod('custom_logo');
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'medium') : '';
if ( ! $logo_url && function_exists('academist_elated_options') ) {
	$logo_url = academist_elated_options()->getOptionValue('logo_image');
}

do_action( 'woocommerce_before_lost_password_form' );
?>

<div class="container-fluid p-0 academist-professional-login-wrapper" style="min-height: 100vh; background-color: #f8f9fa;">
	<div class="row g-0 min-vh-100 login-split-container">

		<!-- LEFT BRANDING PANEL (Consistent with Login) -->
		<div class="col-lg-5 d-none d-lg-flex flex-column justify-content-center align-items-center text-center p-5 position-relative login-image-side" 
             style="background: linear-gradient(135deg, #0B1D3A 0%, #17325c 100%); overflow: hidden;">
            
            <!-- Decorative Elements -->
            <div class="position-absolute rounded-circle" style="width: 500px; height: 500px; background: rgba(255,25,73,0.15); filter: blur(80px); top: -150px; left: -150px;"></div>
            <div class="position-absolute rounded-circle" style="width: 400px; height: 400px; background: rgba(16, 45, 86,0.15); filter: blur(60px); bottom: -100px; right: -100px;"></div>
            
			<div class="position-relative z-1" style="max-width: 420px;">
				<?php if ( $logo_url ) : ?>
					<img src="<?php echo esc_url($logo_url); ?>" alt="RIMA Academy" class="img-fluid mb-5" style="max-height: 90px; filter: brightness(0) invert(1);">
				<?php else : ?>
					<div class="mb-5">
						<h1 class="display-4 fw-black text-white m-0 lh-1" style="letter-spacing:-1px">RIMA</h1>
						<p class="text-danger fw-bold m-0" style="letter-spacing:3px">ACADEMY</p>
					</div>
				<?php endif; ?>

				<h2 class="display-5 fw-bold text-white mb-4 lh-sm">
                    <span class="rima-en">Master a New Language</span>
                    <span class="rima-ro">Stăpânește o Limbă Nouă</span>
                </h2>
				<p class="fs-5 text-white-50 mb-5">
					<span class="rima-en">Access premium courses, interactive study materials, and live Zoom sessions with expert instructors.</span>
					<span class="rima-ro">Accesează cursuri premium, materiale interactive și sesiuni live pe Zoom cu instructori experți.</span>
				</p>
			</div>
		</div>

		<!-- RIGHT FORM PANEL -->
		<div class="col-lg-7 d-flex justify-content-center align-items-center p-4 p-md-5 login-form-side position-relative">
            


			<div class="w-100 rima-auth-card bg-white p-5 rounded-4 shadow-lg border-0" style="max-width: 480px;">

                <div class="text-center mb-5">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded-circle mb-4" style="width: 70px; height: 70px;">
                        <i class="fa fa-unlock-alt fs-3"></i>
                    </div>
                    <h3 class="h3 fw-bold text-dark mb-3">
                        <span class="rima-en">Lost Password</span>
                        <span class="rima-ro">Recuperare Parolă</span>
                    </h3>
                    <p class="text-muted">
                        <span class="rima-en"><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?></span>
                        <span class="rima-ro">Ai uitat parola? Te rugăm să introduci numele de utilizator sau adresa de email. Vei primi pe email un link pentru crearea unei noi parole.</span>
                    </p>
                </div>

				<form method="post" class="woocommerce-ResetPassword lost_reset_password">

					<div class="mb-4 text-start">
                        <label for="user_login" class="form-label text-muted fw-bold small ms-1 mb-2">
                            <span class="rima-en">Username or email</span>
                            <span class="rima-ro">Nume utilizator sau email</span>
                        </label>
                        <input class="form-control bg-light border-0 px-4 py-3 rounded-3 shadow-none" type="text" name="user_login" id="user_login" autocomplete="username" placeholder="Username or email" required style="height: 58px;" />
					</div>

					<div class="clear"></div>

					<?php do_action( 'woocommerce_lostpassword_form' ); ?>

					<div class="mt-4 mb-4">
						<input type="hidden" name="wc_reset_password" value="true" />
                        <button type="submit" class="btn w-100 py-3 fw-bold rounded-pill shadow-sm text-uppercase" style="background-color: #991b1b !important; color: white !important; letter-spacing: 1px; border: none;" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>">
                            <i class="fa fa-key me-2"></i>
                            <span class="rima-en">Reset Password</span>
                            <span class="rima-ro">Resetează Parola</span>
                        </button>
					</div>

					<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

                    <div class="text-center mt-4 pt-4 border-top">
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="text-decoration-none text-muted fw-semibold transition-all rima-hover-underline d-inline-flex align-items-center gap-2">
                            <i class="fa fa-arrow-left"></i>
                            <span class="rima-en">Back to Login</span>
                            <span class="rima-ro">Înapoi la Autentificare</span>
                        </a>
                    </div>
				</form>
			</div>
		</div>
		<!-- /RIGHT FORM PANEL -->

	</div>
</div>

<style>
/* Same resets as form-login.php */
.rima-auth-card .form-control {
    background-color: #f3f4f6 !important;
    color: #1e293b !important;
    height: 58px;
    border-radius: 12px !important;
}
.rima-auth-card .form-control:focus {
    box-shadow: 0 0 0 4px rgba(11, 29, 58, 0.1) !important;
}
.rima-hover-underline:hover {
    text-decoration: underline !important;
}
body.rima-lang-ro #rima-lang-switch-login .rima-lang-switch-thumb {
    transform: translateX(20px);
}
body.rima-lang-ro .rima-lang-label-en { opacity: 0.5; }
body.rima-lang-ro .rima-lang-label-ro { opacity: 1; color: var(--rima-navy) !important; }
body:not(.rima-lang-ro) .rima-lang-label-en { opacity: 1; color: var(--rima-navy) !important; }
body:not(.rima-lang-ro) .rima-lang-label-ro { opacity: 0.5; }
</style>

<script>
jQuery(document).ready(function($) {
    $('#rima-lang-switch-login').on('click', function() {
        var isRo = document.body.classList.contains('rima-lang-ro');
        var newLang = isRo ? 'en' : 'ro';
        
        if (newLang === 'ro') {
            document.body.classList.add('rima-lang-ro');
        } else {
            document.body.classList.remove('rima-lang-ro');
        }
        localStorage.setItem('rima_lang', newLang);

        var domain = window.location.hostname;
        document.cookie = "googtrans=/en/" + newLang + "; path=/; domain=" + domain;
        document.cookie = "googtrans=/en/" + newLang + "; path=/";

        var select = document.querySelector('select.goog-te-combo');
        if (select) {
            select.value = newLang;
            select.dispatchEvent(new Event('change'));
        }

        $.post(rima_ajax_obj.ajax_url, {
            action: 'rima_save_lang_pref',
            lang:   newLang,
            nonce:  rima_ajax_obj.nonce
        }).always(function() {
            window.location.reload();
        });
    });
});
</script>

<?php do_action( 'woocommerce_after_lost_password_form' ); ?>
