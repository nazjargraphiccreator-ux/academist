<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ============================================================
   1. ENQUEUE STYLES & SCRIPTS
   ============================================================ */
if ( ! function_exists( 'academist_elated_child_theme_enqueue_scripts' ) ) {

	function academist_elated_child_theme_enqueue_scripts() {
		$parent_style = 'academist-elated-default-style';
		wp_enqueue_style( 'academist-elated-child-style', get_stylesheet_directory_uri() . '/style.css', array(), '2026.18' );
// Register and enqueue RIMA Design System globally to support the side cart on all pages
		$design_system_deps = array();
		if ( class_exists( 'WooCommerce' ) && ( is_account_page() || is_cart() || is_checkout() ) ) {
			wp_enqueue_style(
				'bootstrap-5',
				'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
				array(),
				'5.3.3'
			);
			$design_system_deps[] = 'bootstrap-5';
		}

		wp_enqueue_style(
			'rima-design-system',
			get_stylesheet_directory_uri() . '/assets/css/rima-design-system.css',
			$design_system_deps,
			'2.0.0'
		);

		// 2026 Premium Redesign Styles
		wp_enqueue_style(
			'rima-2026-premium',
			get_stylesheet_directory_uri() . '/assets/css/rima-2026-premium.css',
			array('rima-design-system'),
			'1.0.0'
		);

		// Home Page 2026 CSS
		if ( is_front_page() || is_home() || is_page_template( 'page-home-modern.php' ) ) {
			wp_enqueue_style(
				'rima-home-style',
				get_stylesheet_directory_uri() . '/assets/css/rima-home-2026.css',
				array( 'rima-design-system' ),
				'1.0.0'
			);
		}

		// Dashboard CSS & other conditional styles
		if ( class_exists( 'WooCommerce' ) && ( is_account_page() || is_cart() || is_checkout() ) ) {
            if ( is_account_page() ) {
                wp_enqueue_style(
                    'rima-dashboard-style',
                    get_stylesheet_directory_uri() . '/assets/css/dashboard-style.css',
                    array('bootstrap-5'),
                    '2.0.0'
                );

                // Localize script for AJAX avatar upload
                wp_localize_script( 'jquery', 'rima_ajax_obj', array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce'    => wp_create_nonce( 'rima_nonce' )
                ) );
            }
            
            if ( is_cart() ) {
                wp_enqueue_style(
                    'rima-cart-style',
                    get_stylesheet_directory_uri() . '/assets/css/rima-cart.css',
                    array('rima-design-system'),
                    '2.0.0'
                );
            }
            
            if ( is_checkout() ) {
                wp_enqueue_style(
                    'rima-checkout-style',
                    get_stylesheet_directory_uri() . '/assets/css/rima-checkout.css',
                    array('rima-design-system'),
                    '2.0.0'
                );
            }
            
            // Romanian Cities Dropdown for Checkout and Edit Address
            if ( is_checkout() || is_account_page() ) {
                wp_enqueue_script(
                    'rima-romanian-cities',
                    get_stylesheet_directory_uri() . '/assets/js/romanian-cities.js',
                    array('jquery'),
                    '1.0.0',
                    true
                );
                wp_localize_script( 'rima-romanian-cities', 'romanianCitiesObj', array(
                    'jsonUrl' => get_stylesheet_directory_uri() . '/inc/romanian-cities.json'
                ) );
            }
		}

        // Contact Page CSS — load only on the page that uses the "Rima Academy – Contact Page" template
        if ( is_page_template( 'page-contact.php' ) ) {
            wp_enqueue_style(
                'rima-contact-style',
                get_stylesheet_directory_uri() . '/assets/css/rima-contact.css',
                array( 'rima-design-system' ),
                time()
            );
        }

        // Single Course CSS & JS
        if ( is_singular( 'course' ) ) {
            wp_enqueue_style(
                'rima-single-course-style',
                get_stylesheet_directory_uri() . '/assets/css/rima-single-course.css',
                array( 'rima-design-system' ),
                '1.0.0'
            );
            wp_enqueue_script(
                'rima-single-course-script',
                get_stylesheet_directory_uri() . '/assets/js/rima-single-course.js',
                array( 'jquery' ),
                '1.0.0',
                true
            );
        }

        // Our Courses CSS & JS
        if ( is_post_type_archive( 'course' ) || is_page_template( 'page-our-courses.php' ) || is_page( 'our-courses' ) || is_page( 'cursuri' ) ) {
            wp_enqueue_style(
                'rima-courses-style',
                get_stylesheet_directory_uri() . '/assets/css/rima-courses.css',
                array( 'rima-design-system' ),
                time()
            );
            wp_enqueue_script(
                'rima-courses-script',
                get_stylesheet_directory_uri() . '/assets/js/rima-courses.js',
                array( 'jquery' ),
                '1.0.0',
                true
            );
        }
	}
	add_action( 'wp_enqueue_scripts', 'academist_elated_child_theme_enqueue_scripts', 20 );
}
