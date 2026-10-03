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
    
    <!-- Avatar Section - Modern SaaS -->
    <div class="rima-avatar-section shadow-sm rounded-4 mb-5 p-4 p-md-5 d-flex flex-column flex-md-row align-items-center gap-4 bg-white position-relative overflow-hidden" style="border: 1px solid #e2e8f0;">
        <div class="rima-avatar-container position-relative" style="width: 130px; height: 130px; flex-shrink: 0;">
            <div class="rima-avatar-preview rounded-circle shadow h-100 w-100 position-relative overflow-hidden cursor-pointer" 
                 id="edit-avatar-preview"
                 style="background-image:url('<?php echo esc_url( $profile_image ); ?>'); background-size: cover; background-position: center; border: 4px solid #fff;">
                
                <div class="rima-avatar-overlay position-absolute inset-0 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center opacity-0 text-white transition-all h-100 w-100">
                    <i class="fa fa-camera fs-4 text-white"></i>
                </div>
            </div>
            <input type="file" id="rima-avatar-upload" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp">
        </div>
        
        <div class="rima-avatar-info text-center text-md-start">
            <h4 class="fw-bold mb-2 text-dark fs-5">
                <span class="rima-en">Profile Photo</span>
                <span class="rima-ro">Fotografie Profil</span>
            </h4>
            <p class="small text-muted mb-4" style="font-size: 14px; max-width: 400px;">
                <span class="rima-en">Update your avatar. Recommended size 500x500px, max 2MB.</span>
                <span class="rima-ro">Actualizează avatarul. Dimensiune recomandată 500x500px, max 2MB.</span>
            </p>
            <div class="rima-avatar-actions d-flex gap-3 align-items-center justify-content-center justify-content-md-start">
                <button type="button" class="btn btn-dark rounded-pill fw-semibold px-4 py-2 text-white shadow-sm transition-all rima-hover-lift border-0" id="rima-btn-upload" style="background: #0f172a;">
                    <i class="fa fa-cloud-upload me-2 text-white"></i> <span class="rima-en">Upload Photo</span><span class="rima-ro">Încarcă Poză</span>
                </button>
                <?php if ( $is_custom_avatar ) : ?>
                    <button type="button" class="btn btn-light rounded-pill fw-semibold px-4 py-2 border rima-hover-lift bg-white" id="rima-btn-remove">
                        <span class="text-danger"><i class="fa fa-trash me-1"></i> <span class="rima-en">Remove</span><span class="rima-ro">Șterge</span></span>
                    </button>
                <?php endif; ?>
            </div>
            <div id="avatar-upload-status" class="small mt-3 fw-bold text-danger" style="display: none;"></div>
        </div>
    </div>

    <!-- Account Details Form -->
    <form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

        <?php do_action( 'woocommerce_edit_account_form_start' ); ?>

        <!-- Personal Info Section -->
        <div class="card shadow-sm rounded-4 mb-5 bg-white rima-form-card" style="border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.03) !important;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-5">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa fa-user text-primary fs-5"></i>
                    </div>
                    <div>
                        <h3 class="h5 fw-bold mb-0 text-dark">
                            <span class="rima-en">Personal Details</span>
                            <span class="rima-ro">Informații Personale</span>
                        </h3>
                        <p class="small text-muted mb-0 mt-1">
                            <span class="rima-en">Update your personal information.</span>
                            <span class="rima-ro">Actualizează informațiile personale.</span>
                        </p>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_first_name" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">First name</span><span class="rima-ro">Prenume</span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control py-3 px-3 shadow-none rima-modern-input" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_last_name" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">Last name</span><span class="rima-ro">Nume</span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control py-3 px-3 shadow-none rima-modern-input" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" required />
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="account_display_name" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">Display name</span><span class="rima-ro">Nume afișat</span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control py-3 px-3 shadow-none rima-modern-input" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" required />
                            <span class="small text-muted mt-2 d-block fst-italic">
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
                            <input type="email" class="form-control py-3 px-3 shadow-none rima-modern-input" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" required />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Change Section -->
        <div class="card shadow-sm rounded-4 mb-5 bg-white rima-form-card" style="border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.03) !important;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-5">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa fa-lock text-danger fs-5"></i>
                    </div>
                    <div>
                        <h3 class="h5 fw-bold mb-0 text-dark">
                            <span class="rima-en">Security</span>
                            <span class="rima-ro">Securitate (Parolă)</span>
                        </h3>
                        <p class="small text-muted mb-0 mt-1">
                            <span class="rima-en">Update your password here.</span>
                            <span class="rima-ro">Actualizează parola contului.</span>
                        </p>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        <div class="form-group position-relative rima-password-group">
                            <label for="password_current" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">Current password (leave blank to leave unchanged)</span>
                                <span class="rima-ro">Parola curentă (lasă gol pentru a nu schimba)</span>
                            </label>
                            <input type="password" class="form-control py-3 px-3 shadow-none rima-modern-input" name="password_current" id="password_current" autocomplete="off" />
                            <button type="button" class="position-absolute d-flex align-items-center justify-content-center text-muted rima-eye-btn" style="border:none; background:transparent; right: 10px; bottom: 8px; width: 35px; height: 35px; cursor: pointer; border-radius: 50%;"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group position-relative rima-password-group">
                            <label for="password_1" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">New password</span>
                                <span class="rima-ro">Parolă nouă</span>
                            </label>
                            <input type="password" class="form-control py-3 px-3 shadow-none rima-modern-input" name="password_1" id="password_1" autocomplete="off" />
                            <button type="button" class="position-absolute d-flex align-items-center justify-content-center text-muted rima-eye-btn" style="border:none; background:transparent; right: 10px; bottom: 8px; width: 35px; height: 35px; cursor: pointer; border-radius: 50%;"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group position-relative rima-password-group">
                            <label for="password_2" class="fw-bold text-dark small mb-2 d-block">
                                <span class="rima-en">Confirm new password</span>
                                <span class="rima-ro">Confirmă parola nouă</span>
                            </label>
                            <input type="password" class="form-control py-3 px-3 shadow-none rima-modern-input" name="password_2" id="password_2" autocomplete="off" />
                            <button type="button" class="position-absolute d-flex align-items-center justify-content-center text-muted rima-eye-btn" style="border:none; background:transparent; right: 10px; bottom: 8px; width: 35px; height: 35px; cursor: pointer; border-radius: 50%;"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .rima-modern-input {
                background-color: #f8fafc !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 12px !important;
                color: #0f172a !important;
                font-size: 15px !important;
                transition: all 0.2s ease-in-out !important;
            }
            .rima-modern-input:focus {
                background-color: #ffffff !important;
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important;
                outline: none !important;
            }
            .rima-modern-input:hover {
                background-color: #f1f5f9 !important;
            }
            .rima-eye-btn {
                transition: all 0.2s;
                background-color: transparent !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                color: #64748b !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            .rima-eye-btn i {
                color: inherit !important;
                font-size: 16px !important;
            }
            .rima-eye-btn:hover {
                background: #e2e8f0 !important;
                color: #0f172a !important;
            }
            .rima-hover-lift {
                transition: all 0.2s ease;
            }
            .rima-hover-lift:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            }
            .rima-form-card {
                transition: all 0.3s ease;
            }
        </style>

        <!-- Wordfence 2FA Section -->
        <?php if ( shortcode_exists('wordfence_2fa_management') ) : ?>
            <div class="card shadow-sm rounded-4 mb-5 bg-white rima-form-card" style="border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.03) !important;">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-dark bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-shield text-dark fs-5"></i>
                        </div>
                        <div>
                            <h3 class="h5 fw-bold mb-0 text-dark">
                                <span class="rima-en">Two-Factor Authentication (2FA)</span>
                                <span class="rima-ro">Securitate Cont (2FA)</span>
                            </h3>
                            <p class="small text-muted mb-0 mt-1">
                                <span class="rima-en">Add an extra layer of security to your account.</span>
                                <span class="rima-ro">Adaugă un nivel suplimentar de securitate contului.</span>
                            </p>
                        </div>
                    </div>
                    <div class="rima-2fa-content pt-2">
                        <?php echo do_shortcode('[wordfence_2fa_management]'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php do_action( 'woocommerce_edit_account_form' ); ?>

        <!-- Form Actions -->
        <div class="mt-5 mb-5 d-flex justify-content-end">
            <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-pill shadow rima-hover-lift text-uppercase text-white" style="letter-spacing:1px; background: linear-gradient(135deg, #102d56, #1d4ed8) !important; border:none;" name="save_account_details" value="Save changes">
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
<div class="rima-addresses-wrapper">
    <?php wc_get_template( 'myaccount/my-address.php' ); ?>
</div>
