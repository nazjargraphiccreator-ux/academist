<?php
/* ============================================================
   3. HEADER DROPDOWN ââ‚¬â€ Override membership nav items
   ============================================================ */
function academist_child_override_header_nav_items( $items, $dashboard_url = '' ) {
	if ( ! class_exists( 'WooCommerce' ) ) return $items;
	return array(
		array( 'url' => wc_get_page_permalink('myaccount'),             'text' => __('Dashboard',        'rima-academy') ),
		array( 'url' => wc_get_account_endpoint_url('my-courses'),      'text' => __('My Courses',       'rima-academy') ),
		array( 'url' => wc_get_account_endpoint_url('orders'),          'text' => __('My Orders',        'rima-academy') ),
		array( 'url' => wc_get_account_endpoint_url('edit-account'),    'text' => __('Account Details',  'rima-academy') ),
	);
}
add_filter( 'academist_membership_dashboard_navigation_pages', 'academist_child_override_header_nav_items', 99, 2 );


