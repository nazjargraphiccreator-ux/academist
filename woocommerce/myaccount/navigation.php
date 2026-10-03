<?php
/**
 * My Account navigation - RIMA Academy Professional Sidebar (Bootstrap 5)
 * academist-child/woocommerce/myaccount/navigation.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

do_action( 'woocommerce_before_account_navigation' );

$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
$first        = get_user_meta( $user_id, 'first_name', true );
$last         = get_user_meta( $user_id, 'last_name', true );
$display_name = trim( $first . ' ' . $last ) ?: $current_user->display_name;
$user_email   = $current_user->user_email;
$member_since = date_i18n( get_option('date_format'), strtotime( $current_user->user_registered ) );

// Avatar
$profile_image = get_user_meta( $user_id, 'social_profile_image', true );
if ( empty( $profile_image ) ) {
	$profile_image = get_avatar_url( $user_id, array( 'size' => 160 ) );
}
?>

<nav class="woocommerce-MyAccount-navigation" id="academist-sidebar-nav">
	<div class="card bg-rima-navy text-white border-0 shadow-lg rounded-4 overflow-hidden h-100 rima-sidebar-card">
		<!-- Profile Widget -->
		<div class="card-body p-4 text-center border-bottom border-secondary border-opacity-25 dashboard-user-profile-widget">
			
			<!-- Language Toggle HIGHER UP -->
			<div class="rima-lang-toggle-wrapper mb-4">
				<div class="rima-lang-toggle-card notranslate d-flex align-items-center justify-content-between p-2 rounded-3 mx-auto rima-lang-toggle-dark"
				     style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12); max-width: 160px;">
					<span class="small fw-semibold text-white-50 rima-lang-label-en" style="letter-spacing:.5px;">EN</span>
					<div class="rima-lang-switch" id="rima-lang-switch" title="Switch language / Schimbă limba">
						<div class="rima-lang-switch-thumb" id="rima-lang-thumb"></div>
					</div>
					<span class="small fw-semibold text-white-50 rima-lang-label-ro" style="letter-spacing:.5px;">RO</span>
				</div>
			</div>

			<div class="avatar-wrapper position-relative mx-auto mb-3" id="avatar-wrapper" style="width: 100px; height: 100px;">
				<div class="user-avatar rounded-circle border border-3 border-danger shadow-sm h-100 w-100" id="user-avatar-display"
					style="background-image:url('<?php echo esc_url( $profile_image ); ?>'); background-size: cover; background-position: center;">
					<label class="avatar-upload-overlay d-flex align-items-center justify-content-center" for="avatar-upload-input" title="<?php esc_attr_e('Change photo', 'rima-academy'); ?>">
						<i class="fa fa-camera"></i>
					</label>
				</div>
				<input type="file" id="avatar-upload-input" accept="image/jpeg,image/png,image/gif,image/webp" class="d-none">
				<span id="avatar-upload-status" class="avatar-upload-status"></span>
			</div>

			<h3 class="h5 fw-bold mb-1"><?php echo esc_html( $display_name ); ?></h3>
			<p class="text-white-50 small mb-2"><?php echo esc_html( $user_email ); ?></p>
			
			<!-- Dynamic Role Badge -->
			<?php 
				$user_roles = (array) $current_user->roles;
				$role_slug  = !empty($user_roles) ? $user_roles[0] : '';
				global $wp_roles;
				$role_name  = isset($wp_roles->roles[$role_slug]['name']) ? translate_user_role($wp_roles->roles[$role_slug]['name']) : 'Student';
			?>
			<div class="mb-3">
				<span class="badge" style="background: rgba(0, 229, 255, 0.15); color: var(--rhm-cyan); border: 1px solid rgba(0, 229, 255, 0.3); font-weight: 600; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; letter-spacing: 0.5px;">
					<?php echo esc_html($role_name); ?>
				</span>
			</div>

			<div class="user-member-since d-inline-flex align-items-center gap-2 mx-auto" style="font-size: 0.85rem;">
				<i class="fa fa-calendar"></i>
				<span class="fw-semibold">
					<span class="rima-en">Member since <?php echo esc_html( $member_since ); ?></span>
					<span class="rima-ro">Membru din <?php echo esc_html( $member_since ); ?></span>
				</span>
			</div>
		</div>

		<!-- Navigation -->
		<div class="p-3">
			<ul class="nav nav-pills flex-column gap-2 dashboard-nav-list m-0 p-0">
				<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : 
					
					// Setup Bilingual Labels
					$en_label = $label;
					$ro_label = $label;
					switch( $endpoint ) {
						case 'dashboard': 
							$en_label = 'Dashboard'; 
							$ro_label = 'Panou de Control'; 
							break;
						case 'my-courses': 
							$en_label = 'My Courses'; 
							$ro_label = 'Cursurile Mele'; 
							break;
						case 'orders': 
							$en_label = 'Orders'; 
							$ro_label = 'Comenzi'; 
							break;
						case 'edit-address': 
							$en_label = 'Addresses'; 
							$ro_label = 'Adrese'; 
							break;
						case 'edit-account': 
							$en_label = 'Account Details'; 
							$ro_label = 'Detalii Cont'; 
							break;
						case 'customer-logout': 
							$en_label = 'Logout'; 
							$ro_label = 'Deconectare'; 
							break;
					}

					// Inject Admin Link before Logout
					if ( $endpoint === 'customer-logout' && current_user_can('manage_options') ) :
				?>
					<li class="nav-item">
						<a href="<?php echo esc_url( wc_get_account_endpoint_url('rima-admin-panel') ); ?>" class="nav-link d-flex align-items-center gap-3 fw-semibold rounded-3 p-2 transition-all" style="background: rgba(0, 229, 255, 0.05); border: 1px solid rgba(0, 229, 255, 0.1);">
							<span class="nav-icon d-inline-flex align-items-center justify-content-center rounded bg-white bg-opacity-10 transition-all" style="width: 36px; height: 36px;">
								<svg style="width:18px;height:18px;display:inline-block;stroke:var(--rhm-cyan, #00e5ff);stroke-width:2px;fill:none;vertical-align:middle;position:relative;z-index:99;" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
							</span>
							<span class="nav-label" style="color:var(--rhm-cyan, #00e5ff);">
								<span class="rima-en">RIMA Admin Panel</span>
								<span class="rima-ro">RIMA Admin Panel</span>
							</span>
						</a>
					</li>
				<?php endif; ?>

					<li class="nav-item <?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
						<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="nav-link d-flex align-items-center gap-3 text-white-50 fw-semibold rounded-3 p-2 transition-all">
							<span class="nav-icon d-inline-flex align-items-center justify-content-center rounded bg-white bg-opacity-10 transition-all" style="width: 36px; height: 36px;">
								<?php 
								if ($endpoint === 'customer-logout') {
									echo '<svg style="width:20px;height:20px;display:inline-block;stroke:currentColor;stroke-width:2.5px;fill:none;vertical-align:middle;position:relative;z-index:99;" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>';
								} else {
									echo apply_filters('rima_nav_icon', '', $endpoint); 
								}
								?>
							</span>
							<span class="nav-label">
								<span class="rima-en"><?php echo esc_html( $en_label ); ?></span>
								<span class="rima-ro"><?php echo esc_html( $ro_label ); ?></span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div><!-- /.p-3 nav list -->

	</div><!-- /.card -->
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
