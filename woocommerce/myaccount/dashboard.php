<?php
/**
 * My Account Dashboard - RIMA Academy (Bootstrap 5)
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
?>

<div class="rima-dashboard-content">
	<!-- Welcome Banner with Integrated Stats -->
	<div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden dashboard-welcome-banner text-white">
		<div class="card-body p-4 p-md-5 position-relative z-1 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
			<div class="banner-content max-w-75">
				<h2 class="display-6 fw-bold text-white mb-3">
					<span class="rima-en">Welcome, <?php echo esc_html( $first_name ); ?>! 👋</span>
					<span class="rima-ro">Bun venit, <?php echo esc_html( $first_name ); ?>! 👋</span>
				</h2>
				<p class="fs-6 text-white-50 m-0">
					<span class="rima-en">Welcome to RIMA Academy. Access your courses, study materials and Zoom sessions from the sidebar.</span>
					<span class="rima-ro">Bun venit în platforma RIMA Academy. Accesează cursurile tale și materialele de studiu din meniul lateral.</span>
				</p>
			</div>
			<div class="banner-stat text-center bg-white bg-opacity-10 border border-white border-opacity-25 rounded-3 p-4 flex-shrink-0" style="backdrop-filter: blur(10px);">
				<i class="fa fa-graduation-cap fs-2 mb-2 d-block opacity-75"></i>
				<h4 class="display-5 fw-bold text-white mb-1 lh-1"><?php echo esc_html($n_courses); ?></h4>
				<span class="small fw-bold text-uppercase text-white-50" style="letter-spacing:1px;">
					<span class="rima-en">Courses</span>
					<span class="rima-ro">Cursuri</span>
				</span>
			</div>
		</div>
	</div>

	<!-- Quick Navigation Cards -->
	<div class="d-flex align-items-center gap-2 mb-4">
		<div class="bg-danger rounded" style="width: 5px; height: 24px;"></div>
		<h3 class="h5 fw-bold m-0 text-dark">
			<span class="rima-en">Quick Access</span>
			<span class="rima-ro">Acces Rapid</span>
		</h3>
	</div>

	<div class="rima-quick-grid">
		
		<a href="<?php echo esc_url( wc_get_account_endpoint_url('my-courses') ); ?>" class="quick-link-card">
			<div class="quick-link-icon-wrapper">
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
			<div class="quick-link-icon-wrapper">
				<i class="fa fa-user"></i>
			</div>
			<h4>
				<span class="rima-en">Account Details</span>
				<span class="rima-ro">Detalii Cont</span>
			</h4>
			<p>
				<span class="rima-en">Edit profile and password</span>
				<span class="rima-ro">Editează profilul și parola</span>
			</p>
		</a>
		
		<a href="<?php echo esc_url(site_url('/our-courses/')); ?>" class="quick-link-card">
			<div class="quick-link-icon-wrapper">
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
