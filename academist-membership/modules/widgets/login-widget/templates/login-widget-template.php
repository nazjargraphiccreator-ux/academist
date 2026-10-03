<?php
/**
 * Header Login Widget - Logged In State
 * Overridden in academist-child to show new unified WooCommerce My Account navigation.
 */
$current_user    = wp_get_current_user();
$name            = $current_user->display_name;
$current_user_id = $current_user->ID;
$account_url     = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_page_permalink( 'myaccount' ) : '#';

// Avatar
$profile_image = get_user_meta( $current_user_id, 'social_profile_image', true );
if ( $profile_image == '' ) {
    $avatar_img = get_avatar( $current_user_id, 28 );
} else {
    $avatar_img = '<img src="' . esc_url( $profile_image ) . '" />';
}
?>

<div class="eltdf-logged-in-user academist-child-header-user">
    <div class="eltdf-logged-in-user-inner">
        <span>
            <?php if ( function_exists( 'academist_membership_kses_img' ) ) {
                echo academist_membership_kses_img( $avatar_img );
            } else {
                echo wp_kses_post( $avatar_img );
            } ?>
            <span class="eltdf-logged-in-user-name"><?php echo esc_html( $name ); ?></span>
        </span>
    </div>
</div>

<ul class="eltdf-login-dropdown">

    <?php if ( class_exists( 'WooCommerce' ) ) :
        // My Account Dashboard
        $my_account_url  = wc_get_page_permalink( 'myaccount' );
        $my_courses_url  = wc_get_account_endpoint_url( 'my-courses' );
        $orders_url      = wc_get_account_endpoint_url( 'orders' );
        $account_det_url = wc_get_account_endpoint_url( 'edit-account' );
        $logout_url      = wc_get_account_endpoint_url( 'customer-logout' );
    ?>
        <li>
            <a href="<?php echo esc_url( $my_account_url ); ?>">
                <span class="eltdf-login-dropdown-item-inner">
                    <i class="fa fa-tachometer" aria-hidden="true"></i>
                    <?php esc_html_e( 'Dashboard', 'academist-child' ); ?>
                </span>
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url( $my_courses_url ); ?>">
                <span class="eltdf-login-dropdown-item-inner">
                    <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                    <?php esc_html_e( 'My Courses', 'academist-child' ); ?>
                </span>
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url( $orders_url ); ?>">
                <span class="eltdf-login-dropdown-item-inner">
                    <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                    <?php esc_html_e( 'Orders', 'academist-child' ); ?>
                </span>
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url( $account_det_url ); ?>">
                <span class="eltdf-login-dropdown-item-inner">
                    <i class="fa fa-user" aria-hidden="true"></i>
                    <?php esc_html_e( 'Account Details', 'academist-child' ); ?>
                </span>
            </a>
        </li>
        <li class="eltdf-login-dropdown-divider"></li>
        <li>
            <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">
                <span class="eltdf-login-dropdown-item-inner">
                    <i class="fa fa-sign-out" aria-hidden="true"></i>
                    <?php esc_html_e( 'Log Out', 'academist-child' ); ?>
                </span>
            </a>
        </li>

    <?php else : ?>
        <?php
        // Fallback if WooCommerce not active
        $nav_items = function_exists( 'academist_membership_get_dashboard_navigation_items' ) ? academist_membership_get_dashboard_navigation_items() : array();
        foreach ( $nav_items as $nav_item ) { ?>
            <li>
                <a href="<?php echo esc_url( $nav_item['url'] ); ?>">
                    <span class="eltdf-login-dropdown-item-inner"><?php echo esc_html( $nav_item['text'] ); ?></span>
                </a>
            </li>
        <?php } ?>
        <li>
            <a href="<?php echo wp_logout_url( home_url( '/' ) ); ?>">
                <span class="eltdf-login-dropdown-item-inner"><?php esc_html_e( 'Log Out', 'academist-child' ); ?></span>
            </a>
        </li>
    <?php endif; ?>

</ul>
