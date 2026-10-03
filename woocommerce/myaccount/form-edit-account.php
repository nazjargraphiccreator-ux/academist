<?php
/**
 * Edit account form - RIMA Academy Premium (Bilingual EN/RO)
 * Styled exactly like xstore-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user = wp_get_current_user();
$profile_image = get_user_meta( $user->ID, 'social_profile_image', true );
$is_custom_avatar = ! empty( $profile_image );
if ( empty( $profile_image ) ) {
	$profile_image = get_avatar_url( $user->ID, array( 'size' => 160 ) );
}
$user_initial = strtoupper( substr( $user->display_name, 0, 1 ) );

do_action( 'woocommerce_before_edit_account_form' ); ?>

<div class="rima-edit-account-page">
    
    <!-- Avatar Section - Instant Upload -->
    <div class="rima-avatar-section shadow-sm border rounded-4 mb-4 p-4 d-flex align-items-center gap-4 bg-light">
        <div class="rima-avatar-container position-relative" style="width: 140px; height: 140px; flex-shrink: 0;">
            <div class="rima-avatar-preview rounded-circle border border-3 border-danger shadow-sm h-100 w-100 position-relative overflow-hidden cursor-pointer" 
                 id="edit-avatar-preview"
                 style="background-image:url('<?php echo esc_url( $profile_image ); ?>'); background-size: cover; background-position: center;">
                
                <div class="rima-avatar-overlay position-absolute inset-0 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center opacity-0 text-white transition-all h-100 w-100">
                    <i class="fa fa-camera fs-3 text-white"></i>
                </div>
            </div>
            <input type="file" id="rima-avatar-upload" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp">
        </div>
        
        <div class="rima-avatar-info">
            <h4 class="fw-bold mb-1 text-dark">
                <span class="rima-en">Profile Photo</span>
                <span class="rima-ro">Fotografie Profil</span>
            </h4>
            <p class="small text-muted mb-3">
                <span class="rima-en">Click on the avatar or upload button to change. Max 2MB.</span>
                <span class="rima-ro">Apasă pe avatar sau pe butonul de încărcare pentru a schimba. Max 2MB.</span>
            </p>
            <div class="rima-avatar-actions d-flex gap-2 align-items-center">
                <button type="button" class="btn btn-sm btn-danger fw-semibold px-3 py-2 text-white" id="rima-btn-upload" style="background:#FF1949; border-color:#FF1949;">
                    <i class="fa fa-upload me-1 text-white"></i> <span class="rima-en">Upload</span><span class="rima-ro">Încarcă</span>
                </button>
                <?php if ( $is_custom_avatar ) : ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary fw-semibold px-3 py-2" id="rima-btn-remove">
                        <i class="fa fa-trash me-1"></i> <span class="rima-en">Delete</span><span class="rima-ro">Șterge</span>
                    </button>
                <?php endif; ?>
            </div>
            <div id="avatar-upload-status" class="small mt-2 fw-bold text-danger" style="display: none;"></div>
        </div>
    </div>

    <!-- Account Details Form -->
    <form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

        <?php do_action( 'woocommerce_edit_account_form_start' ); ?>

        <!-- Personal Info Section -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                    <i class="fa fa-user text-danger fs-5"></i>
                    <h3 class="h5 fw-bold mb-0 text-dark">
                        <span class="rima-en">Personal Details</span>
                        <span class="rima-ro">Informații Personale</span>
                    </h3>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_first_name" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">First name</span><span class="rima-ro">Prenume</span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control bg-light border-0 py-3 px-3 shadow-none rounded-3" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_last_name" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">Last name</span><span class="rima-ro">Nume</span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control bg-light border-0 py-3 px-3 shadow-none rounded-3" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" required />
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="account_display_name" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">Display name</span><span class="rima-ro">Nume afișat</span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control bg-light border-0 py-3 px-3 shadow-none rounded-3" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" required />
                            <span class="small text-muted mt-1 d-block fst-italic">
                                <span class="rima-en">This will be how your name will be displayed in the account section and in reviews</span>
                                <span class="rima-ro">Acesta va fi numele afișat în secțiunea contului și în recenzii</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="account_email" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">Email address</span><span class="rima-ro">Adresă email</span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control bg-light border-0 py-3 px-3 shadow-none rounded-3" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" required />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Change Section -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                    <i class="fa fa-lock text-danger fs-5"></i>
                    <h3 class="h5 fw-bold mb-0 text-dark">
                        <span class="rima-en">Change Password</span>
                        <span class="rima-ro">Schimbă Parola</span>
                    </h3>
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        <div class="form-group position-relative rima-password-group">
                            <label for="password_current" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">Current password (leave blank to leave unchanged)</span>
                                <span class="rima-ro">Parola curentă (lasă gol pentru a nu schimba)</span>
                            </label>
                            <input type="password" class="form-control bg-light border-0 py-3 px-3 shadow-none rounded-3" name="password_current" id="password_current" autocomplete="off" />
                            <button type="button" class="btn position-absolute bottom-0 end-0 mb-1 me-1 px-3 shadow-none text-muted rima-eye-btn" style="border:none; background:transparent;"><i class="fa fa-eye text-muted"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group position-relative rima-password-group">
                            <label for="password_1" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">New password (leave blank to leave unchanged)</span>
                                <span class="rima-ro">Parolă nouă (lasă gol pentru a nu schimba)</span>
                            </label>
                            <input type="password" class="form-control bg-light border-0 py-3 px-3 shadow-none rounded-3" name="password_1" id="password_1" autocomplete="off" />
                            <button type="button" class="btn position-absolute bottom-0 end-0 mb-1 me-1 px-3 shadow-none text-muted rima-eye-btn" style="border:none; background:transparent;"><i class="fa fa-eye text-muted"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group position-relative rima-password-group">
                            <label for="password_2" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">Confirm new password</span>
                                <span class="rima-ro">Confirmă parola nouă</span>
                            </label>
                            <input type="password" class="form-control bg-light border-0 py-3 px-3 shadow-none rounded-3" name="password_2" id="password_2" autocomplete="off" />
                            <button type="button" class="btn position-absolute bottom-0 end-0 mb-1 me-1 px-3 shadow-none text-muted rima-eye-btn" style="border:none; background:transparent;"><i class="fa fa-eye text-muted"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wordfence 2FA Section -->
        <?php if ( shortcode_exists('wordfence_2fa_management') ) : ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                        <i class="fa fa-shield text-danger fs-5"></i>
                        <h3 class="h5 fw-bold mb-0 text-dark">
                            <span class="rima-en">Two-Factor Authentication (2FA)</span>
                            <span class="rima-ro">Securitate Cont (2FA)</span>
                        </h3>
                    </div>
                    <div class="rima-2fa-content pt-2">
                        <?php echo do_shortcode('[wordfence_2fa_management]'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php do_action( 'woocommerce_edit_account_form' ); ?>

        <!-- Form Actions -->
        <div class="mt-4 pt-2">
            <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
            <button type="submit" class="btn btn-danger px-5 py-3 fw-bold rounded-3 shadow-sm text-uppercase text-white" style="letter-spacing:1px; background:#991b1b !important; border-color:#991b1b !important;" name="save_account_details" value="Save changes">
                <i class="fa fa-save me-2"></i>
                <span class="rima-en">Save changes</span>
                <span class="rima-ro">Salvează modificările</span>
            </button>
            <input type="hidden" name="action" value="save_account_details" />
        </div>

        <?php do_action( 'woocommerce_edit_account_form_end' ); ?>
    </form>
</div>

<style>
/* Custom styled styling to match xstore-child design theme */
.rima-avatar-section {
    transition: all 0.3s ease;
}
.rima-avatar-preview {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.rima-avatar-preview:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 24px rgba(255, 25, 73, 0.25) !important;
}
.rima-avatar-preview:hover .rima-avatar-overlay {
    opacity: 1 !important;
}
.rima-avatar-overlay {
    transition: opacity 0.2s ease;
}
.woocommerce-MyAccount-content form.edit-account .form-control {
    padding: 16px 20px !important;
    font-size: 15px !important;
}
.rima-eye-btn {
    z-index: 10;
}
.rima-eye-btn:hover, .rima-eye-btn:focus {
    color: #FF1949 !important;
}
.show-password-input { display: none !important; }
</style>

<script>
jQuery(document).ready(function($) {
    // Click on avatar trigger file input
    $('#edit-avatar-preview, #rima-btn-upload').on('click', function(e) {
        if ($(e.target).attr('id') === 'rima-btn-remove' || $(e.target).closest('#rima-btn-remove').length) return;
        $('#rima-avatar-upload').trigger('click');
    });

    // Handle delete avatar action
    $('#rima-btn-remove').on('click', function(e) {
        e.preventDefault();
        if ( ! confirm('Sigur vrei să ștergi poza de profil?') ) return;

        var statusDiv = $('#avatar-upload-status');
        statusDiv.show().text('Removing...').removeClass('text-danger text-success').addClass('text-info');

        $.ajax({
            url: rima_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'academist_child_remove_avatar',
                nonce: rima_ajax_obj.nonce
            },
            success: function(response) {
                if (response.success) {
                    statusDiv.text('Avatar removed!').removeClass('text-info text-danger').addClass('text-success');
                    
                    // Fallback to default Gravatar image
                    var gravatarUrl = '<?php echo esc_url( get_avatar_url( $user->ID, array( 'size' => 160 ) ) ); ?>';
                    $('#edit-avatar-preview').css('background-image', 'url(' + gravatarUrl + ')');
                    $('#user-avatar-display').css('background-image', 'url(' + gravatarUrl + ')');
                    
                    $('#rima-btn-remove').fadeOut(200, function() { $(this).remove(); });
                    setTimeout(() => statusDiv.fadeOut(), 3000);
                } else {
                    statusDiv.text('Error: ' + response.data).removeClass('text-info').addClass('text-danger');
                }
            },
            error: function() {
                statusDiv.text('Network error.').removeClass('text-info').addClass('text-danger');
            }
        });
    });

    // Custom Password Toggle
    $('.rima-eye-btn').on('click', function(e) {
        e.preventDefault();
        var input = $(this).siblings('input');
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>

<!-- Inject Addresses Here -->
<div class="mt-5 pt-4">
    <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
        <i class="fa fa-map-marker text-danger fs-5"></i>
        <h3 class="h5 fw-bold mb-0 text-dark">
            <span class="rima-en">Addresses</span>
            <span class="rima-ro">Adrese</span>
        </h3>
    </div>
    <div class="rima-addresses-wrapper">
        <?php wc_get_template( 'myaccount/my-address.php' ); ?>
    </div>
</div>
