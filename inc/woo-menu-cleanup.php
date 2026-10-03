<?php
/* ============================================================
   4. REMOVE OLD PARENT THEME WOOCOMMERCE MENU OVERRIDES
   ============================================================ */
function academist_child_remove_parent_woo_menu() {
	remove_filter( 'woocommerce_account_menu_items', 'academist_membership_extend_woo_navigation' );
	remove_filter( 'academist_membership_dashboard_navigation_pages', 'academist_lms_add_profile_navigation_item', 10 );
}
add_action( 'init', 'academist_child_remove_parent_woo_menu', 25 );

