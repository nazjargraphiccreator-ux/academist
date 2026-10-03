<?php
/**
 * My Account Dashboard - RIMA Academy Premium (2026 Design)
 * academist-child/woocommerce/myaccount/dashboard.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$current_user = wp_get_current_user();
$first_name   = get_user_meta( $current_user->ID, 'first_name', true ) ?: $current_user->display_name;

// Quick stats
$orders  = class_exists('WooCommerce') ? wc_get_orders( array('customer' => $current_user->ID, 'limit' => -1, 'status' => array('completed','processing')) ) : array();
$n_courses = 0;
$seen = array();
foreach ( $orders as $o ) {
    foreach ( $o->get_items() as $item ) {
        if (!in_array($item->get_product_id(), $seen)) {
            $seen[] = $item->get_product_id();
            $n_courses++;
        }
    }
}

$is_admin = current_user_can('manage_options');
$company  = get_user_meta( $current_user->ID, 'billing_company', true );
$is_pj    = ! empty( $company );
$demo_url = add_query_arg( 'action', 'rima_demo_excel', admin_url('admin-ajax.php') );
?>

<div class="rima-dashboard-content" style="font-family: 'Inter', sans-serif;">
	<!-- Welcome Banner with Integrated Stats -->
	<div class="rima-welcome-banner shadow-lg rounded-4 mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #102d56 0%, #1d4ed8 100%);">
		<!-- Background Glows -->
		<div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="pointer-events: none;">
			<div class="position-absolute rounded-circle" style="width: 300px; height: 300px; background: rgba(59,130,246,0.2); filter: blur(60px); top: -100px; right: -50px;"></div>
			<div class="position-absolute rounded-circle" style="width: 250px; height: 250px; background: rgba(139,92,246,0.15); filter: blur(50px); bottom: -100px; left: -50px;"></div>
		</div>
		
		<div class="card-body p-4 p-md-5 position-relative z-1 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
			<div class="banner-content" style="max-width: 70%;">
				<h2 class="display-6 fw-bold text-white mb-3" style="letter-spacing: -0.5px;">
					<span class="rima-en">Welcome back, <?php echo esc_html( $first_name ); ?>! 👋</span>
					<span class="rima-ro">Bine ai revenit, <?php echo esc_html( $first_name ); ?>! 👋</span>
				</h2>
				<p class="fs-6 text-white-50 m-0" style="line-height: 1.6;">
					<span class="rima-en">Access your courses, study materials, and Zoom sessions below. Continue your learning journey with RIMA Academy.</span>
					<span class="rima-ro">Accesează cursurile tale, materialele de studiu și sesiunile Zoom. Continuă-ți călătoria de învățare cu RIMA Academy.</span>
				</p>
			</div>
			
			<div class="banner-stat text-center border rounded-4 p-4 flex-shrink-0" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); border-color: rgba(255,255,255,0.15) !important;">
				<i class="fa fa-graduation-cap fs-2 mb-2 d-block text-info"></i>
				<h4 class="display-5 fw-bold text-white mb-1 lh-1"><?php echo esc_html($n_courses); ?></h4>
				<span class="small fw-bold text-uppercase text-white-50" style="letter-spacing:1px;">
					<span class="rima-en">Active Courses</span>
					<span class="rima-ro">Cursuri Active</span>
				</span>
			</div>
		</div>
	</div>

	<!-- Admin / PJ Action Bar -->
	<?php if ( $is_admin || $is_pj ) : ?>
	<div class="rima-action-bar d-flex flex-wrap gap-3 mb-5 p-4 rounded-4 shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0;">
		<?php if ( $is_admin ) : ?>
		<div class="flex-fill">
			<h5 class="fw-bold text-dark mb-1"><i class="fa fa-shield text-primary me-2"></i>Admin Access</h5>
			<p class="text-muted small mb-3">Acces la panoul de administrare RIMA Academy (Aplicația Web).</p>
			<a href="<?php echo esc_url( wc_get_account_endpoint_url('rima-admin-panel') ); ?>" class="btn btn-primary fw-bold px-4 py-2 rounded-pill shadow-sm" style="background: #0f172a; border: none;">
				<i class="fa fa-external-link me-2"></i> Deschide RIMA Admin
			</a>
		</div>
		<?php endif; ?>
		
		<?php if ( $is_pj ) : ?>
		<div class="flex-fill <?php echo $is_admin ? 'border-start ps-md-4' : ''; ?>">
			<h5 class="fw-bold text-dark mb-1"><i class="fa fa-building text-success me-2"></i>Management Studenți (PJ)</h5>
			<p class="text-muted small mb-3">Descarcă modelul Excel pentru înrolarea studenților/angajaților.</p>
			<a href="<?php echo esc_url($demo_url); ?>" class="btn fw-bold px-4 py-2 rounded-pill shadow-sm text-white" style="background: #10b981; border: none;">
				<i class="fa fa-download me-2"></i> Descarcă Demo Excel (.xlsx)
			</a>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<!-- Quick Navigation Cards -->
	<div class="d-flex align-items-center gap-2 mb-4 mt-2">
		<div class="rounded-pill" style="width: 5px; height: 24px; background: #3b82f6;"></div>
		<h3 class="h5 fw-bold m-0 text-dark">
			<span class="rima-en">Quick Access</span>
			<span class="rima-ro">Acces Rapid</span>
		</h3>
	</div>

	<div class="rima-quick-grid">
		<a href="<?php echo esc_url( wc_get_account_endpoint_url('my-courses') ); ?>" class="quick-link-card">
			<div class="quick-link-icon-wrapper" style="background: rgba(59,130,246,0.1); color: #3b82f6;">
				<i class="fa fa-graduation-cap"></i>
			</div>
			<h4>
				<span class="rima-en">My Courses</span>
				<span class="rima-ro">Cursurile Mele</span>
			</h4>
			<p>
				<span class="rima-en">Access study materials and Zoom sessions</span>
				<span class="rima-ro">Accesează materialele și sesiunile Zoom</span>
			</p>
		</a>
		
		<a href="<?php echo esc_url( wc_get_account_endpoint_url('edit-account') ); ?>" class="quick-link-card">
			<div class="quick-link-icon-wrapper" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
				<i class="fa fa-user"></i>
			</div>
			<h4>
				<span class="rima-en">Account Details</span>
				<span class="rima-ro">Detalii Cont</span>
			</h4>
			<p>
				<span class="rima-en">Edit profile, password and security</span>
				<span class="rima-ro">Editează profilul, parola și securitatea</span>
			</p>
		</a>
		
		<a href="<?php echo esc_url(site_url('/our-courses/')); ?>" class="quick-link-card">
			<div class="quick-link-icon-wrapper" style="background: rgba(16,185,129,0.1); color: #10b981;">
				<i class="fa fa-search"></i>
			</div>
			<h4>
				<span class="rima-en">New Courses</span>
				<span class="rima-ro">Cursuri Noi</span>
			</h4>
			<p>
				<span class="rima-en">Discover all available courses</span>
				<span class="rima-ro">Descoperă toate cursurile disponibile</span>
			</p>
		</a>
	</div>
</div>

<style>
/* 2026 Modern Dashboard CSS */
.rima-quick-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
	gap: 20px;
}
.quick-link-card {
	background: #fff;
	border: 1px solid #e2e8f0;
	border-radius: 16px;
	padding: 24px;
	text-decoration: none !important;
	transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.quick-link-card:hover {
	transform: translateY(-4px);
	box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.1);
	border-color: #cbd5e1;
}
.quick-link-icon-wrapper {
	width: 54px;
	height: 54px;
	border-radius: 14px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 24px;
	margin-bottom: 20px;
}
.quick-link-card h4 {
	color: #0f172a;
	font-size: 18px;
	font-weight: 700;
	margin: 0 0 8px 0;
}
.quick-link-card p {
	color: #64748b;
	font-size: 14px;
	margin: 0;
	line-height: 1.5;
}
</style>
