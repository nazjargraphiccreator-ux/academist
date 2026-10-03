<?php
/**
 * Academist Child Theme â€” functions.php
 * RIMA Academy Professional Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── RIMA Email Notification System ────────────────────────────
require_once get_stylesheet_directory() . '/inc/rima-email-notifications.php';

// ── RIMA Secure PDF Tracker ────────────────────────────
require_once get_stylesheet_directory() . '/inc/pdf-server.php';
require_once get_stylesheet_directory() . '/inc/product-meta.php';


/* ============================================================
   0. FORCE CLASSIC CART & CHECKOUT (disable Gutenberg Blocks)
   ============================================================ */
add_filter( 'woocommerce_cart_page_allow_shortcode',     '__return_true' );
add_filter( 'woocommerce_checkout_page_allow_shortcode', '__return_true' );

// Disable WooCommerce Block Cart and Block Checkout
add_filter( 'woocommerce_feature_wc_blocks_enabled', '__return_false' );
add_filter( 'has_block', function( $has_block, $block_name, $post ) {
    if ( in_array( $block_name, array( 'woocommerce/cart', 'woocommerce/checkout' ), true ) ) {
        return false;
    }
    return $has_block;
}, 10, 3 );

// Add body class so our CSS can target the pages
add_filter( 'body_class', function( $classes ) {
    if ( is_cart() )     $classes[] = 'rima-cart-page';
    if ( is_checkout() ) $classes[] = 'rima-checkout-page';
    
    // Global server-side language state
    if ( is_user_logged_in() ) {
        $lang_pref = get_user_meta( get_current_user_id(), '_rima_lang_pref', true );
        if ( $lang_pref === 'ro' ) {
            $classes[] = 'rima-lang-ro';
        }
    }
    
    return $classes;
} );

add_action( 'init', 'rima_force_enable_vc_for_lms' );
function rima_force_enable_vc_for_lms() {
    // 1. Force via DB option for existing installations
    $current = get_option('wpb_js_content_types');
    if ( ! is_array($current) ) {
        $current = array('page', 'post');
    }
    
    $lms_types = array('course', 'lesson', 'quiz', 'question');
    $updated = false;
    foreach ( $lms_types as $pt ) {
        if ( ! in_array( $pt, $current ) ) {
            $current[] = $pt;
            $updated = true;
        }
    }
    if ( $updated ) {
        update_option( 'wpb_js_content_types', $current );
    }
}

// 2. Set defaults via WPBakery API
add_action( 'vc_before_init', function() {
    if ( function_exists( 'vc_set_default_editor_post_types' ) ) {
        vc_set_default_editor_post_types( array( 'page', 'post', 'course', 'lesson', 'quiz', 'question' ) );
    }
});


/* ============================================================
   0.2 BILINGUAL CHECKOUT & ADDRESS FIELDS (EN / RO)
   ============================================================ */
add_filter( 'woocommerce_default_address_fields', 'rima_bilingual_address_fields', 999 );
function rima_bilingual_address_fields( $fields ) {
    $translations = array(
        'first_name' => 'Prenume',
        'last_name'  => 'Nume de familie',
        'company'    => 'Companie (opțional)',
        'country'    => 'Țară / Regiune',
        'address_1'  => 'Adresă stradală',
        'address_2'  => 'Apartament, suită, unitate etc. (opțional)',
        'city'       => 'Localitate / Oraș',
        'state'      => 'Județ / Sector',
        'postcode'   => 'Cod poștal',
    );
    foreach ( $fields as $key => $field ) {
        if ( isset( $translations[ $key ] ) ) {
            $en_label = isset($fields[ $key ]['label']) ? $fields[ $key ]['label'] : '';
            $ro_label = $translations[ $key ];
            $fields[ $key ]['label'] = '<span class="rima-en">' . $en_label . '</span><span class="rima-ro">' . $ro_label . '</span>';
        }
    }
    return $fields;
}

// Enable SVG Support
function rima_add_svg_support($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'rima_add_svg_support');

// Remove generic lorem ipsum from academist-lms reviews
add_filter('gettext', 'rima_remove_lorem_ipsum_reviews', 10, 3);
function rima_remove_lorem_ipsum_reviews($translated_text, $text, $domain) {
    if ($domain === 'academist-lms') {
        if (strpos($text, 'Lorem Ipsn gravida nibh vel') !== false) {
            return '';
        }
    }
    return $translated_text;
}

// Force comments open for courses so the review form or warning message is always displayed
add_filter( 'comments_open', 'rima_force_course_comments_open', 10, 2 );
function rima_force_course_comments_open( $open, $post_id ) {
    if ( get_post_type( $post_id ) === 'course' ) {
        return true;
    }
    return $open;
}

// Backend protection: block review submission if user is not enrolled
add_filter( 'preprocess_comment', 'rima_restrict_course_comments_backend' );
function rima_restrict_course_comments_backend( $commentdata ) {
    $post_id = $commentdata['comment_post_ID'];
    if ( get_post_type( $post_id ) === 'course' ) {
        if ( ! is_user_logged_in() || ! function_exists( 'academist_lms_user_has_course' ) || ! academist_lms_user_has_course( $post_id ) ) {
            wp_die( __( 'You must be enrolled in this course to leave a review.', 'academist-child' ) );
        }
    }
    return $commentdata;
}

add_filter( 'woocommerce_billing_fields', 'rima_bilingual_billing_fields', 999 );
function rima_bilingual_billing_fields( $fields ) {
    $translations = array(
        'billing_phone' => 'Telefon',
        'billing_email' => 'Adresă de email',
    );
    foreach ( $fields as $key => $field ) {
        if ( isset( $translations[ $key ] ) ) {
            $en_label = isset($fields[ $key ]['label']) ? $fields[ $key ]['label'] : '';
            $ro_label = $translations[ $key ];
            $fields[ $key ]['label'] = '<span class="rima-en">' . $en_label . '</span><span class="rima-ro">' . $ro_label . '</span>';
        }
    }
    return $fields;
}

/* ============================================================
   1. ENQUEUE STYLES & SCRIPTS
   ============================================================ */
if ( ! function_exists( 'academist_elated_child_theme_enqueue_scripts' ) ) {

	function academist_elated_child_theme_enqueue_scripts() {
		$parent_style = 'academist-elated-default-style';
		wp_enqueue_style( 'academist-elated-child-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ) );

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
		if ( is_front_page() || is_home() || is_page_template( 'page-home.php' ) ) {
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

/**
 * RIMA LOGIN PAGE — MOBILE APP LAYOUT
 * Injected via wp_head with priority 999 (after all theme CSS) so it wins on mobile.
 * Only runs on the My Account page when user is not logged in.
 */
add_action( 'wp_head', function() {
    if ( ! function_exists('is_account_page') || ! is_account_page() ) return;
    if ( is_user_logged_in() ) return;
    ?>
    <style id="rima-login-mobile-css">
    /* ============================================================
       RIMA LOGIN — MOBILE APP LAYOUT  (≤ 640px)
       Loaded in <head> via wp_head priority 999 — beats all theme CSS
       ============================================================ */
    @media screen and (max-width: 640px) {

        /* 1. Hide theme header + mobile nav bar on login page */
        body.woocommerce-account:not(.logged-in) header,
        body.woocommerce-account:not(.logged-in) .eltdf-page-header,
        body.woocommerce-account:not(.logged-in) .eltdf-mobile-header,
        body.woocommerce-account:not(.logged-in) #header,
        body.woocommerce-account:not(.logged-in) .site-header,
        body.woocommerce-account:not(.logged-in) nav.eltdf-mobile-header,
        body.woocommerce-account:not(.logged-in) .eltdf-sticky-header,
        body.woocommerce-account:not(.logged-in) .eltdf-top-bar,
        body.woocommerce-account:not(.logged-in) .footer-widgets-area,
        body.woocommerce-account:not(.logged-in) footer,
        body.woocommerce-account:not(.logged-in) .eltdf-page-footer,
        body.woocommerce-account:not(.logged-in) [class*="mobile-nav"],
        body.woocommerce-account:not(.logged-in) [class*="bottom-bar"] {
            display: none !important;
        }

        /* 2. Body removes default padding WordPress/theme adds for admin bar etc */
        body.woocommerce-account:not(.logged-in) {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        /* 3. Full-screen page layout */
        body.woocommerce-account:not(.logged-in) .rima-login-page,
        body.woocommerce-account:not(.logged-in) .rima-login-page.rima-immersive-layout {
            display: block !important;
            min-height: 100vh !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100vw !important;
            align-items: unset !important;
            justify-content: unset !important;
        }

        /* 4. Globe more subtle */
        body.woocommerce-account:not(.logged-in) #rhm-globe-viz,
        body.woocommerce-account:not(.logged-in) .rima-fullscreen-globe {
            opacity: 0.15 !important;
        }

        /* 5. Wrapper — full width, stretch */
        body.woocommerce-account:not(.logged-in) .rima-centered-wrapper {
            max-width: 100vw !important;
            width: 100vw !important;
            padding: 0 !important;
            min-height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            box-sizing: border-box !important;
        }

        /* 6. Card fills full screen */
        body.woocommerce-account:not(.logged-in) .rima-lp-card {
            flex: 1 1 auto !important;
            width: 100% !important;
            max-width: 100% !important;
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
            border-bottom: none !important;
            border-top: 1px solid rgba(255,255,255,0.1) !important;
            box-shadow: none !important;
            background: rgba(6, 8, 18, 0.96) !important;
            backdrop-filter: blur(30px) !important;
            -webkit-backdrop-filter: blur(30px) !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }

        /* 7. App navbar header */
        body.woocommerce-account:not(.logged-in) .rima-card-header {
            position: sticky !important;
            top: 0 !important;
            z-index: 9999 !important;
            padding: 12px 20px !important;
            background: rgba(4, 6, 14, 0.98) !important;
            backdrop-filter: blur(30px) !important;
            -webkit-backdrop-filter: blur(30px) !important;
            border-bottom: 1px solid rgba(255,255,255,0.09) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            box-sizing: border-box !important;
        }

        body.woocommerce-account:not(.logged-in) .rima-card-logo {
            display: none !important;
        }

        /* 8. Back badge compact - left aligned since logo is hidden */
        body.woocommerce-account:not(.logged-in) .rima-back-badge {
            font-size: 0.72rem !important;
            padding: 4px 10px 4px 8px !important;
            gap: 4px !important;
        }

        /* 9. Card body */
        body.woocommerce-account:not(.logged-in) .rima-card-body {
            padding: 20px 20px 40px !important;
        }

        /* 10. Inputs larger for touch */
        body.woocommerce-account:not(.logged-in) .rima-lp-input {
            padding: 14px 16px !important;
            font-size: 16px !important; /* prevents iOS zoom */
            border-radius: 10px !important;
        }

        /* 11. Submit full width */
        body.woocommerce-account:not(.logged-in) .rima-lp-submit {
            width: 100% !important;
            padding: 15px !important;
            font-size: 1rem !important;
            border-radius: 12px !important;
            margin-top: 8px !important;
        }

        /* 12. Tabs full width */
        body.woocommerce-account:not(.logged-in) .rima-lp-tabs {
            margin-bottom: 20px !important;
        }
        body.woocommerce-account:not(.logged-in) .rima-lp-tab {
            font-size: 0.78rem !important;
            padding: 10px 8px !important;
        }

        /* 13. Hide card top accent */
        body.woocommerce-account:not(.logged-in) .rima-lp-card::before {
            display: none !important;
        }
    }
    </style>
    <?php
}, 999 );

/**
 * 1d_bis. RIMA IFRAME SPA MODE
 * Hides headers, footers, and sidebars when loading a lesson in the dashboard SPA iframe
 */
add_action('wp_head', function() {
    if ( isset($_GET['rima_iframe']) && $_GET['rima_iframe'] === '1' ) {
        echo '<style>
            /* Hide all standard theme wrappers and navigation */
            header, footer, .eltdf-page-header, .eltdf-mobile-header, .eltdf-page-footer, .eltdf-title-holder, .eltdf-sidebar, .eltdf-top-bar, .eltdf-bottom-bar, #wpadminbar { display: none !important; }
            
            /* Reset body padding/margins */
            body, html { padding: 0 !important; margin: 0 !important; height: 100vh; overflow-y: auto; background: #f8f9fa !important; }
            .eltdf-wrapper, .eltdf-wrapper-inner, .eltdf-content, .eltdf-content-inner { padding: 0 !important; margin: 0 !important; }
            
            /* Lesson specific fixes */
            .eltdf-lesson-single-holder { margin-top: 0 !important; padding: 20px !important; }
            .eltdf-grid-row { display: block !important; margin: 0 !important; }
            .eltdf-page-content-holder { width: 100% !important; padding: 0 !important; }
        </style>';
    }
}, 999);

/* ============================================================
   1e. WPBAKERY — Forțează assets + shortcodes global
   ============================================================
   Permite shortcode-urile WPBakery (inclusiv static blocks) să
   funcționeze pe orice template PHP, nu doar pe paginile editate
   cu WPBakery. Necesar pentru footer/header din Static Blocks.
   ============================================================ */

// 1) Încarcă CSS-ul WPBakery pe toate paginile frontend
add_action( 'wp_enqueue_scripts', function() {
    if ( class_exists( 'Vc_Manager' ) ) {
        wp_enqueue_style( 'js_composer_front' );
        wp_enqueue_style( 'js_composer_custom_css' );
    }
}, 30 );

// 2) Încarcă JS-ul WPBakery pe toate paginile frontend
add_action( 'wp_enqueue_scripts', function() {
    if ( class_exists( 'Vc_Manager' ) ) {
        wp_enqueue_script( 'wpb_composer_front_js' );
    }
}, 30 );

// 3) Înregistrează Post Type 'static_block' pentru ca userul să aibă meniul "Static Blocks" înapoi
add_action( 'init', 'rima_register_static_blocks_cpt' );
function rima_register_static_blocks_cpt() {
    register_post_type( 'static_block', array(
        'labels' => array(
            'name'          => 'Static Blocks',
            'singular_name' => 'Static Block',
            'menu_name'     => 'Static Blocks',
            'all_items'     => 'All Static Blocks',
            'add_new'       => 'Add New',
            'add_new_item'  => 'Add New Static Block',
            'edit_item'     => 'Edit Static Block',
            'new_item'      => 'New Static Block',
            'view_item'     => 'View Static Block',
        ),
        'public'              => true,
        'exclude_from_search' => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-layout',
        'supports'            => array( 'title', 'editor', 'revisions' ),
    ) );
}

// 4) Forțează WPBakery să se activeze pe post type-ul 'static_block' implicit
// 4) Force WPBakery in DB options so the editor appears for the user
add_action( 'admin_init', function() {
    $pt = get_option( 'wpb_js_content_types' );
    if ( ! is_array( $pt ) ) {
        $pt = array( 'page', 'post' );
    }
    if ( ! in_array( 'static_block', $pt ) ) {
        $pt[] = 'static_block';
        update_option( 'wpb_js_content_types', $pt );
    }
} );

// 5) Helper: randează un Static Block WPBakery după slug
//    Folosire în template: rima_static_block( 'footer' );
if ( ! function_exists( 'rima_static_block' ) ) {
    function rima_static_block( $slug, $echo = true ) {
        // Caută post-ul de tip 'static_block' cu slug-ul dat
        $post = get_page_by_path( $slug, OBJECT, 'static_block' );

        if ( ! $post ) {
            // Fallback
            $args  = array(
                'post_type'      => 'static_block',
                'post_status'    => 'publish',
                'name'           => $slug,
                'posts_per_page' => 1,
            );
            $query = new WP_Query( $args );
            $post  = $query->have_posts() ? $query->posts[0] : null;
            wp_reset_postdata();
        }

        if ( ! $post ) {
            return $echo ? '' : '';
        }

        // Force WPBakery to map all shortcodes before processing
        if ( class_exists( 'WPBMap' ) && method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
            WPBMap::addAllMappedShortcodes();
        }

        // Procesează shortcode-urile WPBakery și returnează HTML-ul
        $content = apply_filters( 'the_content', $post->post_content );
        $content = do_shortcode( $content );

        if ( $echo ) {
            echo $content;
        } else {
            return $content;
        }
    }
}

add_shortcode( 'rima_static_block', 'rima_static_block_shortcode_handler' );
add_shortcode( 'static_block_slug', 'rima_static_block_shortcode_handler' );
add_shortcode( 'static_block', 'rima_static_block_shortcode_handler' );
add_shortcode( 'html_block', 'rima_static_block_shortcode_handler' );

function rima_static_block_shortcode_handler( $atts ) {
    $atts = shortcode_atts( array(
        'slug' => '',
        'id'   => '',
    ), $atts );

    if ( ! empty( $atts['slug'] ) ) {
        return rima_static_block( $atts['slug'], false, 'slug' );
    } elseif ( ! empty( $atts['id'] ) ) {
        return rima_static_block( $atts['id'], false, 'id' );
    }

    return '';
}

// Ensure Custom HTML widgets process shortcodes
add_filter( 'widget_text', 'do_shortcode', 11 );
add_filter( 'widget_custom_html_content', 'do_shortcode', 11 );

// Test debug output
add_action( 'wp_footer', function() {
    echo "<!-- DEBUG SHORTCODE: " . do_shortcode('[static_block_slug slug="footer"]') . " -->";
} );

/* ============================================================
   1a. NAVIGATION ICONS FILTER (Inline SVG per endpoint)
   ============================================================ */
add_filter('rima_nav_icon', function($icon, $endpoint) {
    $icons = [
        'dashboard'    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
        'my-courses'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
        'orders'       => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
        'edit-address' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'edit-account' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
    ];
    return isset($icons[$endpoint]) ? $icons[$endpoint] : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="4"/></svg>';
}, 10, 2);

/* ============================================================
   1b. LANGUAGE SYSTEM – Apply class instantly on <head> to avoid flash
   ============================================================ */
add_action( 'wp_head', function() {
    // Apply saved language preference BEFORE paint (avoid flash)
    ?>
    <style id="rima-lang-style-inline">
    /* Hide manual RO spans since Google will auto-translate EN spans */
    .rima-ro { display: none !important; }

    /* Hide Google Translate UI elements */
    .goog-te-banner-frame.skiptranslate { display: none !important; }
    body { top: 0px !important; }
    #goog-gt-tt { display: none !important; }
    .goog-tooltip { display: none !important; }
    .goog-tooltip:hover { display: none !important; }
    .goog-text-highlight { background-color: transparent !important; border: none !important; box-shadow: none !important; }
    </style>
    <?php
}, 1 );

add_action( 'wp_footer', function() {
    $lang_pref = get_user_meta( get_current_user_id(), '_rima_lang_pref', true ) ?: 'en';
    ?>
    <div id="google_translate_element" style="display:none;"></div>
    <script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'en', includedLanguages: 'ro,en', autoDisplay: false}, 'google_translate_element');
    }
    </script>
    

    <script>
    (function($) {
        try {
            /*  Robust Toggle click delegation
 */
            $(document).on('click', '.rima-lang-toggle-card, #rima-lang-switch', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var isRo = document.body.classList.contains('rima-lang-ro');
                var newLang = isRo ? 'en' : 'ro';
                
                /*  Toggle visual body class
 */
                if (newLang === 'ro') {
                    document.body.classList.add('rima-lang-ro');
                } else {
                    document.body.classList.remove('rima-lang-ro');
                }

                /*  1. Save to localStorage for legacy components
 */
                localStorage.setItem('rima_lang', newLang);

                /*  2. Set Google Translate Cookie explicitly for the root domain
 */
                var domain = window.location.hostname;
                document.cookie = "googtrans=/en/" + newLang + "; path=/; domain=" + domain;
                document.cookie = "googtrans=/en/" + newLang + "; path=/"; /*  Also without domain just in case
 */
                
                /*  3. Trigger Google Translate select change (instant visual feedback if possible)
 */
                var select = document.querySelector('select.goog-te-combo');
                if (select) {
                    select.value = newLang;
                    select.dispatchEvent(new Event('change'));
                }
                
                console.log('Rima Language switched to: ' + newLang);

                /*  4. Save to server and reload page to apply changes everywhere (CSS spans + GT)
 */
                $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                    action: 'rima_save_lang_pref',
                    lang:   newLang,
                    nonce:  '<?php echo wp_create_nonce('rima_lang_nonce'); ?>'
                }).always(function() {
                    window.location.reload();
                });
            });

            /*  Sync visual state on load
 */
            setTimeout(function() {
                var savedLang = '<?php echo esc_js($lang_pref); ?>';
                var match = document.cookie.match(new RegExp('(^| )googtrans=([^;]+)'));
                var googLang = match ? match[2].split('/')[2] : 'en';
                
                if (googLang === 'ro' || (savedLang === 'ro' && googLang !== 'en')) {
                    document.body.classList.add('rima-lang-ro');
                    
                    if (googLang !== 'ro') {
                        var select = document.querySelector('select.goog-te-combo');
                        if (select) {
                            select.value = 'ro';
                            select.dispatchEvent(new Event('change'));
                        }
                    }
                }
            }, 1000);
        } catch (err) { console.error('Rima Lang Switch error:', err); }
    })(jQuery);
    </script>
    <?php
}, 5 );

/* ============================================================
   1c. AJAX: Save language preference to user_meta
   ============================================================ */
add_action( 'wp_ajax_rima_save_lang_pref', function() {
    check_ajax_referer( 'rima_lang_nonce', 'nonce' );
    if ( ! is_user_logged_in() ) wp_send_json_error();
    $lang = sanitize_key( $_POST['lang'] ?? 'en' );
    if ( ! in_array( $lang, array('en','ro'), true ) ) wp_send_json_error();
    update_user_meta( get_current_user_id(), '_rima_lang_pref', $lang );
    wp_send_json_success();
} );

/* ============================================================
   1d. AJAX: Contact Page Form Submission (guests + logged-in)
   ============================================================ */
add_action( 'wp_ajax_rima_contact_submit',        'rima_handle_contact_form' );
add_action( 'wp_ajax_nopriv_rima_contact_submit', 'rima_handle_contact_form' );

function rima_handle_contact_form() {
    // Nonce check
    if ( ! check_ajax_referer( 'rima_contact_nonce', 'rima_contact_nonce_field', false ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed.' ) );
        return;
    }

    // Sanitize inputs
    $first_name = sanitize_text_field( $_POST['rc_first_name'] ?? '' );
    $last_name  = sanitize_text_field( $_POST['rc_last_name']  ?? '' );
    $email      = sanitize_email( $_POST['rc_email']           ?? '' );
    $subject    = sanitize_text_field( $_POST['rc_subject']    ?? '' );
    $topic      = sanitize_key( $_POST['rc_topic']             ?? 'other' );
    $message    = sanitize_textarea_field( $_POST['rc_message'] ?? '' );
    $gdpr       = ! empty( $_POST['rc_gdpr'] );

    // Validation
    if ( ! $first_name || ! $last_name ) {
        wp_send_json_error( array( 'message' => 'Name is required.' ) );
        return;
    }
    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Invalid email address.' ) );
        return;
    }
    if ( ! $message ) {
        wp_send_json_error( array( 'message' => 'Message is required.' ) );
        return;
    }
    if ( ! $gdpr ) {
        wp_send_json_error( array( 'message' => 'GDPR consent is required.' ) );
        return;
    }

    $full_name    = trim( $first_name . ' ' . $last_name );
    $admin_email  = get_option( 'admin_email' );
    $site_name    = get_bloginfo( 'name' );

    // ── Email to admin ───────────────────────────────────────
    $mail_subject = sprintf( '[%s] Contact: %s — %s', $site_name, $full_name, $subject ?: $topic );
    $mail_body    = "New contact message from {$full_name} <{$email}>\n\n"
                  . "Topic:   {$topic}\n"
                  . "Subject: {$subject}\n\n"
                  . "Message:\n{$message}\n\n"
                  . '---\nSent from ' . get_site_url();

    $mail_headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        "Reply-To: {$full_name} <{$email}>",
    );

    wp_mail( $admin_email, $mail_subject, $mail_body, $mail_headers );

    // ── Auto-reply to sender ─────────────────────────────────
    $reply_subject = sprintf( 'Am primit mesajul tău — %s', $site_name );
    $reply_body    = "Bună {$first_name},\n\n"
                   . "Mulțumim că ne-ai contactat! Am primit mesajul tău și îți vom răspunde în cel mult 24 de ore.\n\n"
                   . "Echipa Rima Academy\n"
                   . get_site_url();

    wp_mail( $email, $reply_subject, $reply_body, array( 'Content-Type: text/plain; charset=UTF-8' ) );

    // ── Save to DB as custom post ─────────────────────────────
    $post_id = wp_insert_post( array(
        'post_type'   => 'rima_contact_msg',
        'post_title'  => $mail_subject,
        'post_status' => 'private',
        'meta_input'  => array(
            '_rc_from_name'  => $full_name,
            '_rc_from_email' => $email,
            '_rc_topic'      => $topic,
            '_rc_subject'    => $subject,
            '_rc_message'    => $message,
            '_rc_gdpr'       => $gdpr ? '1' : '0',
            '_rc_ip'         => $_SERVER['REMOTE_ADDR'] ?? '',
            '_rc_date'       => current_time( 'mysql' ),
        ),
    ), false );

    wp_send_json_success( array( 'post_id' => $post_id ) );
}

// Register the private custom post type for storing contact messages
add_action( 'init', function() {
    register_post_type( 'rima_contact_msg', array(
        'label'         => 'Contact Messages',
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-email-alt',
        'supports'      => array( 'title', 'custom-fields' ),
        'capability_type' => 'post',
        'map_meta_cap'  => true,
    ) );
} );

/* ============================================================
   2. AVATAR UPLOAD â€” AJAX HANDLER
   ============================================================ */
function academist_child_upload_avatar() {
	check_ajax_referer( 'rima_nonce', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( __( 'Not authenticated.', 'rima-academy' ) );
	}
	if ( empty( $_FILES['avatar'] ) ) {
		wp_send_json_error( __( 'No file received.', 'rima-academy' ) );
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$allowed  = array( 'jpg', 'jpeg', 'png', 'gif', 'webp' );
	$file_ext = strtolower( pathinfo( $_FILES['avatar']['name'], PATHINFO_EXTENSION ) );

	if ( ! in_array( $file_ext, $allowed ) ) {
		wp_send_json_error( __( 'Invalid file type. Accepted: JPG, PNG, GIF, WebP.', 'rima-academy' ) );
	}
	if ( $_FILES['avatar']['size'] > 2 * 1024 * 1024 ) {
		wp_send_json_error( __( 'File is too large. Max 2MB.', 'rima-academy' ) );
	}

	$attachment_id = media_handle_upload( 'avatar', 0 );
	if ( is_wp_error( $attachment_id ) ) {
		wp_send_json_error( $attachment_id->get_error_message() );
	}

	$url = wp_get_attachment_url( $attachment_id );
	update_user_meta( get_current_user_id(), 'social_profile_image', $url );

	wp_send_json_success( array( 'url' => $url ) );
}
add_action( 'wp_ajax_academist_child_upload_avatar', 'academist_child_upload_avatar' );

function academist_child_remove_avatar() {
	check_ajax_referer( 'rima_nonce', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( __( 'Not authenticated.', 'rima-academy' ) );
	}
	delete_user_meta( get_current_user_id(), 'social_profile_image' );
	wp_send_json_success();
}
add_action( 'wp_ajax_academist_child_remove_avatar', 'academist_child_remove_avatar' );

/* ============================================================
   3. HEADER DROPDOWN â€” Override membership nav items
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

/* ============================================================
   4. REMOVE OLD PARENT THEME WOOCOMMERCE MENU OVERRIDES
   ============================================================ */
function academist_child_remove_parent_woo_menu() {
	remove_filter( 'woocommerce_account_menu_items', 'academist_membership_extend_woo_navigation' );
	remove_filter( 'academist_membership_dashboard_navigation_pages', 'academist_lms_add_profile_navigation_item', 10 );
}
add_action( 'init', 'academist_child_remove_parent_woo_menu', 25 );

/* ============================================================
   5. PLUGIN TEMPLATE OVERRIDE PATH
   ============================================================ */
// Allow the membership plugin's template to be overridden in child theme
add_filter( 'academist_membership_filter_templates_dir', function( $dir ) {
	$child_dir = get_stylesheet_directory() . '/academist-membership/';
	return file_exists( $child_dir ) ? $child_dir : $dir;
});add_action('wp_head', function() {
    if ( class_exists('WooCommerce') && is_user_logged_in() ) {
        echo '<script type="text/javascript">
        var rima_ajax_obj = {
            "ajax_url": "' . esc_url(admin_url('admin-ajax.php')) . '",
            "nonce": "' . wp_create_nonce('rima_nonce') . '"
        };
        </script>';
    }
});
add_action('wp_footer', function() {
    if ( class_exists('WooCommerce') && is_user_logged_in() ) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            /*  Bind to both avatar upload inputs (sidebar and edit account form)
 */
            $('#rima-avatar-upload, #avatar-upload-input').on('change', function() {
                var file = this.files[0];
                if (!file) return;

                var formData = new FormData();
                formData.append('avatar', file);
                formData.append('action', 'academist_child_upload_avatar');
                formData.append('nonce', rima_ajax_obj.nonce);

                var statusDiv = $('#avatar-upload-status');
                if(statusDiv.length) {
                    statusDiv.show().text('Uploading...').removeClass('text-danger text-success').addClass('text-info');
                }

                $.ajax({
                    url: rima_ajax_obj.ajax_url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response.success) {
                            $('#edit-avatar-preview').css('background-image', 'url(' + response.data.url + ')');
                            $('#user-avatar-display').css('background-image', 'url(' + response.data.url + ')');
                            if(statusDiv.length) {
                                statusDiv.text('Avatar updated!').removeClass('text-info text-danger').addClass('text-success');
                                setTimeout(() => statusDiv.fadeOut(), 3000);
                            }
                        } else {
                            if(statusDiv.length) {
                                statusDiv.text('Error: ' + response.data).removeClass('text-info').addClass('text-danger');
                            } else {
                                alert('Error uploading avatar: ' + response.data);
                            }
                        }
                    },
                    error: function() {
                        if(statusDiv.length) {
                            statusDiv.text('Network error.').removeClass('text-info').addClass('text-danger');
                        } else {
                            alert('Network error while uploading avatar.');
                        }
                    }
                });
            });
        });
        </script>
        <?php
    }
}, 100);

/* ============================================================
   6. OVERRIDE MY COURSES ENDPOINT TEMPLATE
   ============================================================ */
add_action('init', function() {
    remove_action( 'woocommerce_account_my-courses_endpoint', 'rima_my_courses_content' );
    add_action( 'woocommerce_account_my-courses_endpoint', 'academist_child_my_courses_content' );
}, 20);

function academist_child_my_courses_content() {
    wc_get_template( 'myaccount/my-courses.php', array(), '', get_stylesheet_directory() . '/woocommerce/' );
}

/* ============================================================
   7. FORCE ENABLE REGISTRATION (Bypass WP Option Check)
   ============================================================ */
add_filter( 'pre_option_users_can_register', '__return_true' );
add_filter( 'option_users_can_register', '__return_true' );
add_filter( 'pre_option_woocommerce_enable_myaccount_registration', function() { return 'yes'; } );
add_filter( 'option_woocommerce_enable_myaccount_registration', function() { return 'yes'; } );

/* ============================================================
   7.1 FORCE CHILD THEME TEMPLATE FOR WOOCOMMERCE LOGIN (ULTIMATE)
   ============================================================ */
add_action('init', function() {
    remove_shortcode('woocommerce_my_account');
    add_shortcode('woocommerce_my_account', 'rima_force_my_account_shortcode');
});

function rima_force_my_account_shortcode($atts) {
    ob_start();
    
    global $wp;
    
    if ( is_user_logged_in() ) {
        // Let WooCommerce handle the logged-in view
        if ( function_exists('wc_get_template') ) {
            wc_get_template( 'myaccount/my-account.php', array(
                'current_user' => get_user_by( 'id', get_current_user_id() ),
            ) );
        }
    } elseif ( isset( $wp->query_vars['lost-password'] ) ) {
        // We are on the Lost Password page! Load the lost password template.
        if ( function_exists('wc_get_template') ) {
            wc_get_template( 'myaccount/form-lost-password.php', array(
                'form' => 'lost_password',
            ) );
        }
    } else {
        // FORCE our custom login template!
        $custom_template = get_stylesheet_directory() . '/woocommerce/myaccount/form-login.php';
        if ( file_exists( $custom_template ) ) {
            include $custom_template;
        } else {
            echo '<div class="woocommerce-error">ERROR: Custom RIMA template file is missing at <code>' . esc_html($custom_template) . '</code>. Please make sure the <strong>woocommerce</strong> folder was uploaded inside the academist-child theme.</div>';
            // Fallback to default
            if ( function_exists('wc_get_template') ) {
                wc_get_template( 'myaccount/form-login.php' );
            }
        }
    }
    
    return ob_get_clean();
}

/* ============================================================
   8. BULLETPROOF REGISTRATION & LOGIN SHORTCODE OVERRIDES
   ============================================================ */
add_action('wp_loaded', function() {
    remove_shortcode('eltdf_user_register');
    add_shortcode('eltdf_user_register', 'rima_custom_user_register_html');
});

function rima_custom_user_register_html() {
    ob_start();
    ?>
    <div class="eltdf-social-register-holder">
        <form method="post" class="eltdf-register-form">
            <fieldset>
                <div>
                    <label>Username*</label>
                    <input type="text" name="user_register_name" id="user_register_name" value="" required pattern=".{3,}" title="Three or more characters"/>
                </div>
                <div>
                    <label>Email*</label>
                    <input type="email" name="user_register_email" id="user_register_email" value="" required />
                </div>
                <div>
                    <label>Password*</label>
                    <input type="password" name="user_register_password" id="user_register_password" value="" required />
                </div>
                <div>
                    <label>Repeat Password*</label>
                    <input type="password" name="user_register_confirm_password" id="user_register_confirm_password"  value="" required />
                </div>
                
                <div class="eltdf-register-button-holder" style="margin-top: 20px;">
                    <button type="submit" class="eltdf-btn eltdf-btn-solid eltdf-btn-small" style="background-color: #991b1b !important; border-color: #991b1b !important;"><i class="fa fa-user-plus me-2" style="margin-right: 5px;"></i> Register</button>
                    <?php wp_nonce_field( 'eltdf-ajax-register-nonce', 'eltdf-register-security' ); ?>
                </div>
            </fieldset>
        </form>
        <div class="eltdf-membership-response-holder clearfix"></div>
    </div>
    <?php
    return ob_get_clean();
}

/* ============================================================
   8.5 AJAX UPDATE CART QUANTITY
   ============================================================ */
add_action('wp_ajax_rima_update_cart_quantity', 'rima_update_cart_quantity');
add_action('wp_ajax_nopriv_rima_update_cart_quantity', 'rima_update_cart_quantity');
function rima_update_cart_quantity() {
    if ( ! isset( $_POST['cart_item_key'] ) || ! isset( $_POST['qty'] ) ) {
        wp_send_json_error();
    }
    $cart_item_key = sanitize_text_field( $_POST['cart_item_key'] );
    $qty = intval( $_POST['qty'] );
    
    if ( $qty <= 0 ) {
        WC()->cart->remove_cart_item( $cart_item_key );
    } else {
        WC()->cart->set_quantity( $cart_item_key, $qty );
    }
    
    WC_AJAX::get_refreshed_fragments();
    wp_die();
}

/* ============================================================
   9. LMS AUTHENTICATION REDIRECTS & FLOATING SIDE-CART
   ============================================================ */
add_action('wp_footer', function() {
    $my_account_url = class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : '/my-account/';
    
    // Build user data for the menu
    if ( is_user_logged_in() ) {
        $current_user  = wp_get_current_user();
        $display_name  = $current_user->display_name ?: $current_user->user_login;
        $first_name    = $current_user->first_name ?: '';
        $last_name     = $current_user->last_name ?: '';
        // Initials: first letter of first and last name, fallback to first two of display_name
        $initials = '';
        if ($first_name && $last_name) {
            $initials = strtoupper(mb_substr($first_name,0,1) . mb_substr($last_name,0,1));
        } elseif ($display_name) {
            $parts = explode(' ', trim($display_name));
            $initials = strtoupper(mb_substr($parts[0],0,1) . (isset($parts[1]) ? mb_substr($parts[1],0,1) : ''));
        }
        if (empty($initials)) $initials = 'U';
        
        // Avatar URL
        $avatar_url = get_avatar_url($current_user->ID, ['size' => 80]);
        $has_custom_avatar = !strpos($avatar_url, 'gravatar.com/avatar/') || strpos($avatar_url, 'd=mm') === false;
        
        // Dashboard / My Account links
        $dashboard_url  = $my_account_url;
        $courses_url    = $my_account_url . 'my-courses/';
        $orders_url     = $my_account_url . 'orders/';
        $profile_url    = $my_account_url . 'edit-account/';
        $logout_url     = wp_logout_url( home_url() );
        
        $is_logged_in_html = 'true';
    } else {
        $display_name = '';
        $initials = '';
        $avatar_url = '';
        $has_custom_avatar = false;
        $dashboard_url = $courses_url = $orders_url = $profile_url = $logout_url = '';
        $is_logged_in_html = 'false';
    }
    ?>
    
    <!-- RIMA Premium User Menu CSS -->
    <style>
    /* Hide original theme login widget */
    .eltdf-login-register-widget { display: none !important; }

    /* Disable Top Bar completely */
    .eltdf-top-bar, 
    .eltdf-top-bar-background, 
    .eltdf-top-bar-wrapper,
    #eltdf-top-bar,
    .top-bar,
    .eltdf-top-bar-area { 
        display: none !important; 
        height: 0 !important; 
        overflow: hidden !important; 
        visibility: hidden !important; 
    }

    /* Hide the red square side menu opener */
    .eltdf-side-menu-button-opener {
        display: none !important;
    }

    /* Disable default Academist hover cart dropdown */
    .eltdf-shopping-cart-dropdown,
    .eltdf-shopping-cart-dropdown-inner {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
    
    /* Premium User Menu Container - inline with header cart */
    #rima-user-menu-container {
        display: inline-flex;
        vertical-align: middle;
        align-items: center;
        gap: 10px;
        margin-right: 12px;
    }


    /* Not logged in: Login/Register buttons */
    .rima-topbar-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border-radius: 30px;
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: .2px;
        text-decoration: none !important;
        transition: all 0.2s ease;
        cursor: pointer;
        line-height: 1;
        white-space: nowrap;
    }
    .rima-topbar-btn-login {
        background: transparent;
        color: #374151 !important;
        border: 1.5px solid rgba(55,65,81,0.25) !important;
    }
    .rima-topbar-btn-login:hover {
        background: rgba(11,29,58,0.05);
        border-color: #374151 !important;
        color: #0B1D3A !important;
    }
    .rima-topbar-btn-register {
        background: linear-gradient(135deg, #FF1949 0%, #c8002f 100%);
        color: #fff !important;
        border: none !important;
        box-shadow: 0 3px 10px rgba(255,25,73,0.25);
    }
    .rima-topbar-btn-register:hover {
        background: linear-gradient(135deg, #e6003d 0%, #a8002a 100%);
        transform: translateY(-1px);
        box-shadow: 0 5px 16px rgba(255,25,73,0.35);
        color: #fff !important;
    }
    
    /* Logged in: Avatar + dropdown */
    .rima-user-avatar-wrap {
        position: relative;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        height: 100%;
    }
    .rima-user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 2px solid #e5e7eb;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0B1D3A, #17325c);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        font-family: 'Inter', sans-serif;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        transition: all 0.2s ease;
        user-select: none;
    }
    .rima-user-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    .rima-user-avatar-wrap:hover .rima-user-avatar {
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        transform: scale(1.05);
    }
    
    /* Dropdown */
    .rima-user-dropdown {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        min-width: 240px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 12px 48px rgba(0,0,0,0.14);
        padding: 8px 0;
        z-index: 999999;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px) scale(0.97);
        transition: all 0.22s cubic-bezier(0.34,1.56,0.64,1);
        pointer-events: none;
    }
    .rima-user-dropdown.rima-open {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: all;
    }
    
    /* Dropdown header with user info */
    .rima-dropdown-header {
        padding: 14px 18px 10px;
        border-bottom: 1px solid #f1f1f1;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .rima-dropdown-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0B1D3A, #17325c);
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }
    .rima-dropdown-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    .rima-dropdown-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .rima-dropdown-name {
        font-size: 13px;
        font-weight: 700;
        color: #0B1D3A;
        line-height: 1.2;
    }
    .rima-dropdown-role {
        font-size: 10px;
        color: #008b9e;
        background: rgba(0, 229, 255, 0.1);
        border: 1px solid rgba(0, 229, 255, 0.2);
        padding: 3px 8px;
        border-radius: 12px;
        margin-top: 5px;
        display: inline-block;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    
    /* Dropdown box-sizing override to prevent layout overflow */
    .rima-user-dropdown,
    .rima-user-dropdown * {
        box-sizing: border-box !important;
    }

    /* Dropdown items */
    .rima-dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 18px;
        font-size: 13px;
        color: #374151 !important;
        text-decoration: none !important;
        transition: all 0.15s ease;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }
    .rima-dropdown-item:hover {
        background: rgba(255, 71, 87, 0.08) !important;
        color: #ff4757 !important;
        padding-left: 22px;
    }
    .rima-dropdown-item .rima-di-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
        transition: all 0.15s;
    }
    .rima-dropdown-item:hover .rima-di-icon {
        background: rgba(255, 71, 87, 0.15) !important;
        color: #ff4757 !important;
    }
    .rima-dropdown-divider {
        height: 1px;
        background: #f1f1f1;
        margin: 6px 0;
    }
    .rima-dropdown-item.rima-logout {
        color: #dc2626 !important;
    }
    .rima-dropdown-item.rima-logout:hover {
        background: #fff5f5;
    }
    .rima-dropdown-item.rima-logout .rima-di-icon {
        background: #fee2e2;
    }
    
    /* Arrow indicator on avatar */
    .rima-user-caret {
        font-size: 10px;
        color: #9ca3af;
        transition: transform 0.2s;
        margin-left: 3px;
    }
    .rima-user-avatar-wrap.rima-open .rima-user-caret {
        transform: rotate(180deg);
        color: #0B1D3A;
    }
    
    /* Mobile Menu Overlay */
    .rima-mobile-menu-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.7); z-index: 100000; opacity: 0; visibility: hidden; transition: 0.3s;
        pointer-events: none;
    }
    .rima-mobile-menu-overlay.rima-menu-open {
        opacity: 1; visibility: visible;
        pointer-events: auto;
    }
    .rima-mobile-menu {
        position: fixed; top: 0; left: -320px; width: 300px; max-width: 80%; height: 100%;
        background: #0f172a; z-index: 100001; transition: 0.3s;
        display: flex; flex-direction: column; overflow-y: auto;
        box-shadow: 5px 0 25px rgba(0,0,0,0.5);
    }
    .rima-mobile-menu.rima-menu-open {
        left: 0;
    }
    .rima-mobile-menu-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .rima-mobile-menu-header h4 {
        color: white; margin: 0; font-size: 20px;
    }
    .rima-mobile-menu-close {
        color: white; font-size: 28px; cursor: pointer;
    }
    .rima-mobile-menu-content {
        padding: 20px;
    }
    .rima-mobile-menu-content ul {
        list-style: none; padding: 0; margin: 0;
    }
    .rima-mobile-menu-content li {
        margin-bottom: 15px;
    }
    .rima-mobile-menu-content a {
        color: white; text-decoration: none; font-size: 18px; font-weight: 500;
    }
    .rima-mobile-menu-content .sub-menu {
        padding-left: 15px; margin-top: 10px; border-left: 2px solid rgba(255,255,255,0.1);
    }
    .rima-mobile-menu-content .sub-menu a {
        font-size: 15px; color: #cbd5e1;
    }

    /* RIMA Mobile Bottom Nav */
    #rima-mobile-bottom-nav {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: #ffffff;
        box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
        z-index: 99999;
        justify-content: space-around;
        padding: 12px 0 8px 0;
        border-top: 1px solid #f0f0f0;
    }
    .rima-mob-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #555;
        text-decoration: none;
        font-size: 11px;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        transition: color 0.3s ease;
    }
    .rima-mob-nav-item:hover, .rima-mob-nav-item:active {
        color: #ff0050; /* Brand pink */
    }
    .rima-mob-icon {
        margin-bottom: 4px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .rima-mob-icon svg {
        width: 22px;
        height: 22px;
        stroke: currentColor;
    }
    .rima-mob-cart-badge {
        position: absolute;
        top: -6px;
        right: -10px;
        background: #ff0050;
        color: white;
        font-size: 10px;
        font-weight: bold;
        min-width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    @media only screen and (max-width: 768px) {
        #rima-mobile-bottom-nav {
            display: flex;
        }
        body {
            padding-bottom: 100px !important; 
        }
        /* Optionally hide desktop cart on mobile to avoid duplication */
        .eltdf-shopping-cart-holder, .eltdf-header-cart {
            display: none !important;
        }
    }
    </style>
    
    <!-- RIMA Premium User Menu HTML -->
    <div id="rima-user-menu-container">
        <?php if ( is_user_logged_in() ) : 
            // Fetch custom avatar or fallback
            $profile_image = get_user_meta( $current_user->ID, 'social_profile_image', true );
            if ( empty( $profile_image ) ) {
                $profile_image = get_avatar_url( $current_user->ID, array( 'size' => 80 ) );
            }
        ?>
        <!-- LOGGED IN: Avatar + Dropdown -->
        <div class="rima-user-avatar-wrap" id="rimaUserAvatarWrap">
            <div class="rima-user-avatar" id="rimaUserAvatar">
                <img src="<?php echo esc_url($profile_image); ?>" alt="Avatar">
            </div>
            <span class="rima-user-caret">▾</span>
            
            <!-- Dropdown -->
            <div class="rima-user-dropdown" id="rimaUserDropdown">
                <!-- Header -->
                <div class="rima-dropdown-header">
                    <div class="rima-dropdown-avatar">
                        <img src="<?php echo esc_url($profile_image); ?>" alt="Avatar">
                    </div>
                    <div>
                        <div class="rima-dropdown-name"><?php echo esc_html($display_name); ?></div>
                        <?php 
                            $user_roles = (array) $current_user->roles;
                            $role_slug  = !empty($user_roles) ? $user_roles[0] : '';
                            global $wp_roles;
                            $role_name  = isset($wp_roles->roles[$role_slug]['name']) ? translate_user_role($wp_roles->roles[$role_slug]['name']) : 'Student';
                        ?>
                        <div class="rima-dropdown-role"><?php echo esc_html($role_name); ?></div>
                    </div>
                </div>
                
                <!-- Menu Items -->
                <a href="<?php echo esc_url($dashboard_url); ?>" class="rima-dropdown-item">
                    <span class="rima-di-icon">📊</span>
                    <span class="rima-en">Dashboard</span>
                    <span class="rima-ro">Panou de Control</span>
                </a>
                <a href="<?php echo esc_url($courses_url); ?>" class="rima-dropdown-item">
                    <span class="rima-di-icon">📚</span>
                    <span class="rima-en">My Courses</span>
                    <span class="rima-ro">Cursurile Mele</span>
                </a>
                <a href="<?php echo esc_url($orders_url); ?>" class="rima-dropdown-item">
                    <span class="rima-di-icon">🛍️</span>
                    <span class="rima-en">Orders</span>
                    <span class="rima-ro">Comenzi</span>
                </a>
                <a href="<?php echo esc_url($profile_url); ?>" class="rima-dropdown-item">
                    <span class="rima-di-icon">⚙️</span>
                    <span class="rima-en">Account Details</span>
                    <span class="rima-ro">Detalii Cont</span>
                </a>
                
                <div class="rima-dropdown-divider"></div>
                
                <a href="<?php echo esc_url($logout_url); ?>" class="rima-dropdown-item rima-logout">
                    <span class="rima-di-icon">🚪</span>
                    <span class="rima-en">Logout</span>
                    <span class="rima-ro">Deconectare</span>
                </a>
            </div>
        </div>
        
        <?php else : ?>
        <!-- NOT LOGGED IN: Login + Register buttons -->
        <a href="<?php echo esc_url($my_account_url); ?>" class="rima-topbar-btn rima-topbar-btn-login">
            👤 <span class="rima-en">Login</span><span class="rima-ro">Conectare</span>
        </a>
        <a href="<?php echo esc_url($my_account_url . '#rima-register-tab'); ?>" class="rima-topbar-btn rima-topbar-btn-register">
            ✨ <span class="rima-en">Register</span><span class="rima-ro">Înregistrare</span>
        </a>
        <?php endif; ?>
    </div>


    <script>
    jQuery(document).ready(function($) {

        /*  ─── 1. Relocate user menu next to Cart in header ───
 */
        function relocateHeaderWidgets() {
            var cartOuter = document.querySelector('.eltdf-shopping-cart-holder');
            var ourMenu   = document.getElementById('rima-user-menu-container');

            if (cartOuter && ourMenu && !cartOuter.parentNode.contains(ourMenu)) {
                cartOuter.parentNode.insertBefore(ourMenu, cartOuter);
            }
        }
        relocateHeaderWidgets();
        /*  Re-run on scroll in case sticky header clones the bar
 */
        $(window).on('scroll.rimaHeader', function() { relocateHeaderWidgets(); });

        /*  ─── 2. Avatar dropdown toggle ───
 */
        $(document).on('click', '#rimaUserAvatarWrap', function(e) {
            e.stopPropagation();
            $(this).toggleClass('rima-open');
            $('#rimaUserDropdown').toggleClass('rima-open');
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#rimaUserAvatarWrap').length) {
                $('#rimaUserAvatarWrap').removeClass('rima-open');
                $('#rimaUserDropdown').removeClass('rima-open');
            }
        });

        /*  ─── 3. Redirect leftover theme auth openers ───
 */
        var authLinks = document.querySelectorAll('.eltdf-login-opener, .eltdf-register-opener');
        authLinks.forEach(function(link) {
            link.classList.remove('eltdf-modal-opener');
            link.setAttribute('data-modal', '');
            link.href = '<?php echo esc_url($my_account_url); ?>';
        });

        /*  ─── 4. Slide-out Side Cart (robust binding) ───
 */
        /*  Use true capture phase listener to completely bypass any theme scripts calling stopPropagation
 */
        document.addEventListener('click', function(e) {
            var cartTarget = e.target.closest('.eltdf-shopping-cart-holder, .eltdf-header-cart, .eltdf-cart-icon');
            if (cartTarget) {
                e.preventDefault();
                e.stopPropagation();
                var sideCart = document.getElementById('rima-side-cart');
                var overlay = document.getElementById('rima-side-cart-overlay');
                if (sideCart) sideCart.classList.add('rima-cart-open');
                if (overlay) overlay.classList.add('rima-cart-open');
            }
        }, true);
        
        /*  Keep checking and overriding the href just in case
 */
        setInterval(function() {
            $('.eltdf-shopping-cart-holder a, .eltdf-header-cart > a').attr('href', 'javascript:void(0);');
        }, 1500);

        $(document).on('click', '#rima-side-cart-close, #rima-side-cart-overlay', function() {
            $('#rima-side-cart').removeClass('rima-cart-open');
            $('#rima-side-cart-overlay').removeClass('rima-cart-open');
        });

        /*  ─── 5. Header Language Toggle logic ───
 */
        /*  Initialize from local storage
 */
        var currentLang = localStorage.getItem('rima_lang') || 'en';
        $('body').addClass('rima-lang-' + currentLang);
        
        /*  ─── 7. Translate WooCommerce Cart Strings ───
 */
        window.translateWooCommerceStrings = function() {
            var isRo = $('body').hasClass('rima-lang-ro');
            
            var emptyMessage = $('.woocommerce-mini-cart__empty-message');
            if(emptyMessage.length) {
                emptyMessage.text(isRo ? 'Niciun produs în coș.' : 'No products in the cart.');
            }
            
            $('.woocommerce-mini-cart__buttons .button.wc-forward:not(.checkout)').text(isRo ? 'Vezi Coșul' : 'View Cart');
            $('.woocommerce-mini-cart__buttons .button.checkout').text(isRo ? 'Finalizare Comandă' : 'Checkout');
        };
        translateWooCommerceStrings(); /*  run on load
 */

        /*  ─── 8. Replace Cart Icon with Professional SVG ───
 */
        var cartIconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>';
        
        function rimaInjectCartSvg() {
            $('.eltdf-cart-icon').html(cartIconSvg);
        }
        rimaInjectCartSvg();
        
        /*  WooCommerce often replaces the header cart via AJAX on page load (fragments refresh), restoring the old icon.
 */
        /*  We re-inject the SVG when it finishes.
 */
        $(document.body).on('wc_fragments_refreshed wc_fragments_loaded updated_wc_div added_to_cart', function() {
            rimaInjectCartSvg();
        });

        $(document).on('click', '#rima-header-lang-toggle', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var isRo = $('body').hasClass('rima-lang-ro');
            if (isRo) {
                $('body').removeClass('rima-lang-ro').addClass('rima-lang-en');
                localStorage.setItem('rima_lang', 'en');
            } else {
                $('body').removeClass('rima-lang-en').addClass('rima-lang-ro');
                localStorage.setItem('rima_lang', 'ro');
            }
            window.translateWooCommerceStrings();
        });

    }); /*  end ready
 */

    /*  Listen to WooCommerce added_to_cart event via jQuery
 */
    jQuery(document).ready(function($) {
        $(document).on('added_to_cart', function(event, fragments, cart_hash, $button) {
            /*  Get product name dynamically (from button data, or fallback to single page title)
 */
            var productName = 'Course';
            if ($button && ($button.attr('data-product_name') || $button.data('product_name'))) {
                productName = $button.attr('data-product_name') || $button.data('product_name');
            } else if ($('.rsc-hero-title').length) {
                productName = $('.rsc-hero-title').text().trim();
            } else if ($('h1.product_title').length) {
                productName = $('h1.product_title').text().trim();
            }

            var isRo = $('body').hasClass('rima-lang-ro') || (localStorage.getItem('rima_lang') === 'ro');
            var msg = isRo ? '"' + productName + '" a fost adăugat în coș!' : '"' + productName + '" has been added to cart!';
            
            /*  Show success toast with dynamic message
 */
            var $toast = $('#rima-cart-toast');
            if($toast.length) {
                $toast.text(msg).addClass('show-toast');
                setTimeout(function() {
                    $toast.removeClass('show-toast');
                }, 4000);
            }
    
            /*  Update Side Cart HTML with fragments
 */
            if(fragments && fragments['div.widget_shopping_cart_content']) {
                $('#rima-side-cart-content').html(fragments['div.widget_shopping_cart_content']);
                if (typeof window.translateWooCommerceStrings === 'function') {
                    window.translateWooCommerceStrings();
                }
            }
        });

        /*  AJAX update cart quantity on +/- button clicks
 */
        $(document).on('click', '.rima-cart-qty-minus, .rima-cart-qty-plus', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $btn = $(this);
            var key = $btn.attr('data-cart_item_key');
            var $input = $btn.siblings('.rima-cart-qty-input');
            var currentQty = parseInt($input.val()) || 1;
            var newQty = $btn.hasClass('rima-cart-qty-plus') ? currentQty + 1 : currentQty - 1;
            
            if (newQty < 0) newQty = 0;
            
            $('#rima-side-cart').addClass('rima-cart-loading');
            
            $.ajax({
                type: 'POST',
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action: 'rima_update_cart_quantity',
                    cart_item_key: key,
                    qty: newQty
                },
                success: function(response) {
                    if (response && response.fragments) {
                        var fragments = response.fragments;
                        $.each(fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                        $(document.body).trigger('wc_fragments_refreshed');
                    }
                    $('#rima-side-cart').removeClass('rima-cart-loading');
                },
                error: function() {
                    $('#rima-side-cart').removeClass('rima-cart-loading');
                }
            });
        });

        /*  Auto-open side-cart on page load if WooCommerce notices (added to cart messages) are present
 */
        if ($('.woocommerce-message').length > 0) {
            var sideCart = document.getElementById('rima-side-cart');
            var cartOverlay = document.getElementById('rima-side-cart-overlay');
            if(sideCart) {
                sideCart.classList.add('rima-cart-open');
                cartOverlay.classList.add('rima-cart-open');
            }
        }
    });
    </script>

    <!-- Toast Notification -->
    <div id="rima-cart-toast" class="rima-cart-toast"></div>

    <!-- Side Cart HTML -->
    <div id="rima-side-cart-overlay" class="rima-side-cart-overlay"></div>
    <div id="rima-side-cart" class="rima-side-cart">
        <div class="rima-side-cart-header">
            <h4><span class="rima-en">Your Cart</span><span class="rima-ro">Coșul Tău</span></h4>
            <span id="rima-side-cart-close" class="rima-side-cart-close">&times;</span>
        </div>
        <div id="rima-side-cart-content" class="rima-side-cart-content widget_shopping_cart_content">
            <?php 
            if(class_exists('WooCommerce')) {
                woocommerce_mini_cart(); 
            }
            ?>
        </div>
    </div>

    
    <!-- Mobile Menu Overlay HTML -->
    <div id="rima-mobile-menu-overlay" class="rima-mobile-menu-overlay"></div>
    <div id="rima-mobile-menu" class="rima-mobile-menu">
        <div class="rima-mobile-menu-header">
            <h4><span class="rima-en">Menu</span><span class="rima-ro">Meniu</span></h4>
            <span id="rima-mobile-menu-close" class="rima-mobile-menu-close">&times;</span>
        </div>
        <div class="rima-mobile-menu-content">
            <?php
            wp_nav_menu( array(
                'menu_id'        => 'mobile-primary-menu',
                'menu_class'     => 'rima-mobile-nav',
                'fallback_cb'    => 'wp_page_menu',
            ) );
            ?>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div id="rima-mobile-bottom-nav">
        <a href="https://rima-academy.com/" class="rima-mob-nav-item">
            <span class="rima-mob-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </span>
            <span class="rima-en">Home</span><span class="rima-ro">Acasă</span>
        </a>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="rima-mob-nav-item">
            <span class="rima-mob-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </span>
            <span class="rima-en">Account</span><span class="rima-ro">Cont</span>
        </a>
        <a href="#" class="rima-mob-nav-item" id="rima-mobile-menu-toggle">
            <span class="rima-mob-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </span>
            <span class="rima-en">Menu</span><span class="rima-ro">Meniu</span>
        </a>
        <a href="#" class="rima-mob-nav-item" id="rima-mob-nav-cart">
            <span class="rima-mob-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                <span class="rima-mob-cart-badge">0</span>
            </span>
            <span class="rima-en">Cart</span><span class="rima-ro">Coș</span>
        </a>
    </div>

    <script>
    jQuery(document).ready(function($) {
        
        /*  Mobile Menu Logic
 */
        $(document).on('click', '#rima-mobile-menu-toggle, .eltdf-mobile-menu-opener', function(e) {
            e.preventDefault();
            $('#rima-mobile-menu').addClass('rima-menu-open');
            $('#rima-mobile-menu-overlay').addClass('rima-menu-open');
        });

        $(document).on('click', '#rima-mobile-menu-close, #rima-mobile-menu-overlay', function(e) {
            $('#rima-mobile-menu').removeClass('rima-menu-open');
            $('#rima-mobile-menu-overlay').removeClass('rima-menu-open');
        });

        /*  Mobile bottom nav cart trigger
 */
        $(document).on('click', '#rima-mob-nav-cart', function(e) {
            e.preventDefault();
            $('#rima-side-cart').addClass('rima-cart-open');
            $('#rima-side-cart-overlay').addClass('rima-cart-open');
        });

        /*  Sync mobile cart badge with desktop cart badge every second
 */
        setInterval(function() {
            var count = $('.eltdf-cart-number').first().text();
            if (count !== undefined && count !== '') {
                $('.rima-mob-cart-badge').text(count);
            }
        }, 1000);
    });
    </script>

    <?php
}, 999);

/* ============================================================
   10. REGISTRATION VALIDATION (MATH CAPTCHA & CONFIRM PASSWORD)
   ============================================================ */
add_action( 'woocommerce_register_post', 'rima_validate_custom_register_fields', 10, 3 );
function rima_validate_custom_register_fields( $username, $email, $validation_errors ) {
    
    // 1. Validate Confirm Password
    if ( isset( $_POST['password'] ) && isset( $_POST['rima_confirm_password'] ) ) {
        if ( $_POST['password'] !== $_POST['rima_confirm_password'] ) {
            $validation_errors->add( 'password_mismatch', __( 'The passwords do not match.', 'woocommerce' ) );
        }
    }
    
    // 2. Validate Math Captcha
    if ( isset( $_POST['rima_math_answer'] ) && isset( $_POST['rima_math_hash'] ) ) {
        $answer = sanitize_text_field( $_POST['rima_math_answer'] );
        $hash   = sanitize_text_field( $_POST['rima_math_hash'] );
        
        $expected_hash = md5('rima_math_' . $answer);
        
        if ( $hash !== $expected_hash ) {
            $validation_errors->add( 'math_captcha_error', __( 'The anti-spam math answer is incorrect.', 'woocommerce' ) );
        }
    } else {
        $validation_errors->add( 'math_captcha_missing', __( 'Please solve the anti-spam math question.', 'woocommerce' ) );
    }
}

add_filter( 'woocommerce_process_login_errors', 'rima_validate_custom_login_fields', 10, 3 );
function rima_validate_custom_login_fields( $validation_errors, $login, $password ) {
    
    // Validate Math Captcha for Login
    if ( isset( $_POST['rima_math_answer_login'] ) && isset( $_POST['rima_math_hash_login'] ) ) {
        $answer = sanitize_text_field( $_POST['rima_math_answer_login'] );
        $hash   = sanitize_text_field( $_POST['rima_math_hash_login'] );
        
        $expected_hash = md5('rima_math_' . $answer);
        
        if ( $hash !== $expected_hash ) {
            $validation_errors->add( 'math_captcha_error', __( 'The anti-spam math answer is incorrect.', 'woocommerce' ) );
        }
    } else {
        // Only trigger this if we are actually submitting the login form, since WooCommerce might process logins from other places.
        // We check if the custom nonce is present or just rely on the form submission via our form
        if ( isset($_POST['login']) ) {
            $validation_errors->add( 'math_captcha_missing', __( 'Please solve the anti-spam math question.', 'woocommerce' ) );
        }
    }
    return $validation_errors;
}

/* ============================================================
   11. EMAIL VERIFICATION ON REGISTRATION
   ============================================================ */
// 1. Prevent auto-login and set user as unverified
add_filter( 'woocommerce_registration_auth_new_customer', '__return_false' );

add_action( 'woocommerce_created_customer', 'rima_require_email_verification_on_register', 10, 3 );
function rima_require_email_verification_on_register( $customer_id, $new_customer_data, $password_generated ) {
    $activation_hash = wp_generate_password( 20, false );
    update_user_meta( $customer_id, 'rima_is_activated', '0' );
    update_user_meta( $customer_id, 'rima_activation_hash', $activation_hash );
    
    // Send Email
    $user = get_user_by( 'id', $customer_id );
    $my_account_url = wc_get_page_permalink( 'myaccount' );
    $activation_link = add_query_arg( array(
        'rima_activate' => $customer_id,
        'hash'          => $activation_hash
    ), $my_account_url );
    
    $subject = "Confirm Your Account - RIMA Academy";
    
    // HTML Email Template
    ob_start(); ?>
    <div style="background:linear-gradient(135deg,#C8102E 0%,#8B0A1E 100%);padding:32px 40px;text-align:center;">
        <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128231;</div>
        <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;font-family:Arial,sans-serif;">Activate Your Account</h1>
        <p style="color:rgba(255,255,255,.85);font-size:14px;margin-top:10px;line-height:1.6;font-family:Arial,sans-serif;">Just one more step to start your language journey.</p>
    </div>
    <div style="background:#1A2E45;padding:36px 40px;font-family:Arial,sans-serif;">
        <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo esc_html( $user->display_name ?: $user->user_login ); ?>,</p>
        <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Thank you for joining RIMA Academy! Please verify your email address to activate your account and access all our courses and features.</p>
        
        <div style="text-align:center;margin:35px 0;">
            <a href="<?php echo esc_url_raw($activation_link); ?>" style="display:inline-block;background:linear-gradient(135deg,#C8102E 0%,#8B0A1E 100%);color:#fff;font-size:16px;font-weight:700;padding:16px 45px;border-radius:50px;letter-spacing:0.5px;text-decoration:none;box-shadow:0 8px 24px rgba(200,16,46,0.4);">Verify Email Address</a>
        </div>
        
        <p style="color:rgba(255,255,255,.5);font-size:13px;line-height:1.5;margin-bottom:0;">If the button doesn't work, copy and paste this link into your browser:<br><br><a href="<?php echo esc_url_raw($activation_link); ?>" style="color:#C8102E;word-break:break-all;"><?php echo esc_url_raw($activation_link); ?></a></p>
    </div>
    <?php
    $message_html = ob_get_clean();
    
    if ( function_exists('rima_send_email') ) {
        rima_send_email( $user->user_email, $subject, $message_html, 'Please verify your email address to activate your account.' );
    } else {
        // Fallback if the function is missing for some reason
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $user->user_email, $subject, $message_html, $headers );
    }
}

// 2. Add notice after registration redirect
add_filter( 'woocommerce_registration_redirect', 'rima_registration_redirect_notice' );
function rima_registration_redirect_notice( $redirect ) {
    wc_add_notice( __('Registration successful! Please check your email inbox to activate your account.', 'woocommerce'), 'success' );
    return wc_get_page_permalink( 'myaccount' );
}

// 3. Block login if unverified
add_filter( 'woocommerce_process_login_errors', 'rima_prevent_unverified_login', 10, 3 );
function rima_prevent_unverified_login( $validation_error, $login, $password ) {
    $user = get_user_by( 'login', $login );
    if ( ! $user ) {
        $user = get_user_by( 'email', $login );
    }
    
    if ( $user ) {
        $is_activated = get_user_meta( $user->ID, 'rima_is_activated', true );
        if ( $is_activated === '0' ) {
            $validation_error->add( 'unverified_account', __('You must verify your email address before logging in. Please check your inbox.', 'woocommerce') );
        }
    }
    return $validation_error;
}

// 4. Handle activation link click
add_action( 'template_redirect', 'rima_process_email_verification' );
function rima_process_email_verification() {
    if ( isset( $_GET['rima_activate'] ) && isset( $_GET['hash'] ) ) {
        $user_id = intval( $_GET['rima_activate'] );
        $hash    = sanitize_text_field( $_GET['hash'] );
        
        $stored_hash = get_user_meta( $user_id, 'rima_activation_hash', true );
        if ( $stored_hash === $hash && !empty($hash) ) {
            update_user_meta( $user_id, 'rima_is_activated', '1' );
            delete_user_meta( $user_id, 'rima_activation_hash' );
            wc_add_notice( __('Your account has been successfully verified! You can now log in.', 'woocommerce'), 'success' );
            
            wp_redirect( wc_get_page_permalink( 'myaccount' ) );
            exit;
        } else {
            wc_add_notice( __('Invalid or expired activation link.', 'woocommerce'), 'error' );
            wp_redirect( wc_get_page_permalink( 'myaccount' ) );
            exit;
        }
    }
}

/* ============================================================
   12. DIRECT BANK TRANSFER (BACS) PAYMENT PROOF UPLOAD
   ============================================================ */

// Display upload box on view order page
add_action( 'woocommerce_order_details_after_order_table', 'rima_add_payment_proof_upload_section', 10, 1 );
function rima_add_payment_proof_upload_section( $order ) {
    if ( ! is_a( $order, 'WC_Order' ) ) return;
    if ( $order->get_payment_method() !== 'bacs' ) return;
    
    $allowed_statuses = array( 'on-hold', 'pending', 'failed' );
    $order_status = $order->get_status();
    $proof_id = get_post_meta( $order->get_id(), '_rima_payment_proof', true );
    $proof_url = $proof_id ? wp_get_attachment_url( $proof_id ) : '';

    if ( ! in_array( $order_status, $allowed_statuses ) ) {
        if ( $proof_id ) {
            ?>
            <div class="rima-proof-container success-uploaded mt-4 p-4 rounded-4 border bg-white shadow-sm">
                <h4 class="h5 fw-bold mb-2 text-dark">
                    <span class="rima-en">Payment Proof</span>
                    <span class="rima-ro">Dovadă Plată</span>
                </h4>
                <p class="text-muted small">
                    <span class="rima-en">The payment proof has been uploaded and verified.</span>
                    <span class="rima-ro">Dovada de plată a fost trimisă și verificată.</span>
                </p>
                <a href="<?php echo esc_url($proof_url); ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-3">
                    <i class="fa fa-file-text-o me-2"></i><?php _e('Vizualizează dovada', 'rima-academy'); ?>
                </a>
            </div>
            <?php
        }
        return;
    }

    ?>
    <div class="rima-proof-container mt-4 p-4 rounded-4 border bg-white shadow-sm">
        <h4 class="h5 fw-bold mb-2 text-dark">
            <span class="rima-en">Upload Payment Proof</span>
            <span class="rima-ro">Încarcă Dovada Plății</span>
        </h4>
        <p class="text-muted small mb-3">
            <span class="rima-en">For direct bank transfer orders, please upload a copy of the payment receipt (PDF, PNG, JPG - max 5MB) to speed up course activation.</span>
            <span class="rima-ro">Pentru plățile prin transfer bancar, te rugăm să încarci o copie a dovezii de plată (PDF, PNG, JPG - max 5MB) pentru a grăbi activarea contului.</span>
        </p>

        <div class="rima-upload-box p-4 border border-2 border-dashed rounded-3 text-center position-relative <?php echo $proof_id ? 'has-file' : ''; ?>" id="rima-proof-dropzone" style="cursor: pointer; border-color: #cbd5e1; background-color: #f8fafc; transition: all 0.3s ease;">
            <input type="file" id="rima-proof-file-input" accept="application/pdf,image/png,image/jpeg,image/jpg" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" style="z-index: 2; cursor: pointer;">
            
            <div class="rima-upload-box-idle" style="<?php echo $proof_id ? 'display:none;' : ''; ?>">
                <i class="fa fa-cloud-upload text-muted mb-2" style="font-size: 32px;"></i>
                <p class="mb-1 fw-semibold text-dark">
                    <span class="rima-en">Click to upload or drag & drop</span>
                    <span class="rima-ro">Apasă pentru a alege fișierul sau trage-l aici</span>
                </p>
                <span class="text-muted small">PDF, PNG, JPG (max 5MB)</span>
            </div>

            <div class="rima-upload-box-success" style="<?php echo $proof_id ? '' : 'display:none;'; ?>">
                <i class="fa fa-check-circle text-success mb-2" style="font-size: 32px;"></i>
                <p class="mb-1 fw-semibold text-success">
                    <span class="rima-en">Proof Uploaded Successfully</span>
                    <span class="rima-ro">Dovada a fost încărcată cu succes</span>
                </p>
                <div class="d-flex justify-content-center gap-2 mt-2 position-relative" style="z-index: 3;">
                    <a href="<?php echo esc_url($proof_url); ?>" id="rima-proof-preview-link" target="_blank" class="btn btn-sm btn-outline-secondary rounded-2" style="<?php echo $proof_url ? '' : 'display:none;'; ?>">
                        <i class="fa fa-eye"></i> <span class="rima-en">View File</span><span class="rima-ro">Vezi Fișier</span>
                    </a>
                    <button type="button" id="rima-proof-change-btn" class="btn btn-sm btn-outline-danger rounded-2">
                        <i class="fa fa-refresh"></i> <span class="rima-en">Change File</span><span class="rima-ro">Schimbă Fișier</span>
                    </button>
                </div>
            </div>
            
            <div class="rima-upload-box-progress" style="display: none;">
                <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0 fw-semibold text-primary">Se încarcă fișierul...</p>
            </div>
        </div>
        <div id="rima-proof-error-msg" class="text-danger small mt-2" style="display: none;"></div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        const dropzone = $('#rima-proof-dropzone');
        const fileInput = $('#rima-proof-file-input');
        const idleState = $('.rima-upload-box-idle');
        const successState = $('.rima-upload-box-success');
        const progressState = $('.rima-upload-box-progress');
        const errorMsg = $('#rima-proof-error-msg');
        const previewLink = $('#rima-proof-preview-link');
        const orderId = <?php echo $order->get_id(); ?>;

        fileInput.on('change', function() {
            const file = this.files[0];
            if (!file) return;

            /*  Validate file type
 */
            const allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                errorMsg.text('Format invalid. Sunt permise doar fișiere PDF, PNG și JPG.').show();
                return;
            }

            /*  Validate size (5MB)
 */
            if (file.size > 5 * 1024 * 1024) {
                errorMsg.text('Fișierul este prea mare. Lăsați dimensiunea maximă de 5MB.').show();
                return;
            }

            errorMsg.hide();
            idleState.hide();
            successState.hide();
            progressState.show();

            const formData = new FormData();
            formData.append('proof_file', file);
            formData.append('order_id', orderId);
            formData.append('action', 'rima_upload_payment_proof');
            formData.append('nonce', '<?php echo wp_create_nonce("rima_proof_nonce_" . $order->get_id()); ?>');

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    progressState.hide();
                    if (response.success) {
                        previewLink.attr('href', response.data.url);
                        previewLink.show();
                        successState.show();
                        dropzone.addClass('has-file');
                    } else {
                        idleState.show();
                        errorMsg.text(response.data || 'A apărut o eroare la încărcare.').show();
                    }
                },
                error: function() {
                    progressState.hide();
                    idleState.show();
                    errorMsg.text('Eroare de rețea. Te rugăm să încerci din nou.').show();
                }
            });
        });

        $('#rima-proof-change-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.val('');
            dropzone.removeClass('has-file');
            successState.hide();
            idleState.show();
        });
    });
    </script>
    <style>
    .rima-upload-box:hover {
        border-color: #102d56 !important;
        background-color: #f1f5f9 !important;
    }
    .rima-upload-box.has-file {
        border-style: solid !important;
        border-color: #10b981 !important;
        background-color: #f0fdf4 !important;
    }
    .cursor-pointer { cursor: pointer; }
    </style>
    <?php
}

// Handle AJAX Upload request
add_action( 'wp_ajax_rima_upload_payment_proof', 'rima_ajax_handle_payment_proof_upload' );
function rima_ajax_handle_payment_proof_upload() {
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    
    if ( ! $order_id ) {
        wp_send_json_error( 'Comandă invalidă.' );
    }

    // Verify nonce
    check_ajax_referer( 'rima_proof_nonce_' . $order_id, 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'Nu ești autentificat.' );
    }

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        wp_send_json_error( 'Comanda nu a fost găsită.' );
    }

    // Verify ownership
    if ( $order->get_customer_id() !== get_current_user_id() ) {
        wp_send_json_error( 'Nu ai permisiunea de a modifica această comandă.' );
    }

    if ( empty($_FILES['proof_file']) ) {
        wp_send_json_error( 'Niciun fișier selectat.' );
    }

    $file = $_FILES['proof_file'];

    // Types
    $allowed_types = array( 'application/pdf', 'image/png', 'image/jpeg', 'image/jpg' );
    if ( ! in_array( $file['type'], $allowed_types ) ) {
        wp_send_json_error( 'Format de fișier nepermis. Doar PDF, PNG sau JPG.' );
    }

    // Size
    if ( $file['size'] > 5 * 1024 * 1024 ) {
        wp_send_json_error( 'Fișierul depășește limita de 5MB.' );
    }

    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );

    $attachment_id = media_handle_upload( 'proof_file', $order_id );

    if ( is_wp_error( $attachment_id ) ) {
        wp_send_json_error( 'Eroare la salvare: ' . $attachment_id->get_error_message() );
    }

    update_post_meta( $order_id, '_rima_payment_proof', $attachment_id );

    wp_send_json_success( array(
        'url' => wp_get_attachment_url( $attachment_id )
    ) );
}

// Add Admin Metabox to show the payment proof in WooCommerce order details page
add_action( 'add_meta_boxes', 'rima_add_order_payment_proof_metabox' );
function rima_add_order_payment_proof_metabox() {
    // Screen compatibility for HPOS & standard tables
    $screen = class_exists( 'Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrderStatusTableController' ) && 
              method_exists('Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrderStatusTableController', 'is_active') &&
              Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrderStatusTableController::is_active() 
              ? wc_get_page_screen_id( 'shop-order' ) 
              : 'shop_order';

    add_meta_box(
        'rima_payment_proof_box',
        '📄 Dovadă Plată / Payment Proof',
        'rima_render_payment_proof_metabox',
        $screen,
        'side',
        'default'
    );
}

function rima_render_payment_proof_metabox( $post_or_order_object ) {
    // Resolve order depending on HPOS or normal meta boxes
    $order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
    if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
        echo '<p>Comandă invalidă.</p>';
        return;
    }

    if ( $order->get_payment_method() !== 'bacs' ) {
        echo '<p class="description">Această comandă nu folosește ca metodă de plată Transferul Bancar.</p>';
        return;
    }

    $proof_id = get_post_meta( $order->get_id(), '_rima_payment_proof', true );

    if ( ! $proof_id ) {
        echo '<div class="notice notice-warning inline" style="margin: 0 0 10px 0; padding: 8px;"><p style="margin: 0;">⚠️ Utilizatorul nu a încărcat încă dovada de plată.</p></div>';
        return;
    }

    $proof_url = wp_get_attachment_url( $proof_id );
    $file_path = get_attached_file( $proof_id );
    $file_ext  = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

    echo '<div style="margin-bottom: 12px;">';
    echo '<p style="margin-top: 0;"><strong>Dovadă încărcată:</strong></p>';
    
    if ( in_array( $file_ext, array('png', 'jpg', 'jpeg') ) ) {
        echo '<a href="' . esc_url($proof_url) . '" target="_blank">';
        echo '<img src="' . esc_url($proof_url) . '" style="max-width: 100%; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 8px; display: block;" />';
        echo '</a>';
    } else {
        echo '<div style="background: #f1f1f1; padding: 12px; border-radius: 4px; text-align: center; margin-bottom: 8px;">';
        echo '<span class="dashicons dashicons-pdf" style="font-size: 40px; width: 40px; height: 40px; color: #d63636; line-height:1;"></span>';
        echo '<p style="margin: 5px 0 0 0; font-size: 12px;">Fișier PDF</p>';
        echo '</div>';
    }

    echo '<a href="' . esc_url($proof_url) . '" target="_blank" class="button button-primary button-large" style="width: 100%; text-align: center;">';
    echo '<span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> Deschide / Descarcă Dovada';
    echo '</a>';
    echo '</div>';
}

// Force "Apply Now" button to directly Add to Cart on Course Pages
add_action('wp_footer', function() {
    if (is_singular('course')) {
        global $post;
        // Academist LMS usually links a WooCommerce product ID in post meta
        $product_id = get_post_meta($post->ID, 'eltdf_course_woo_product_meta', true);
        
        // Output JS to override the Apply Now button regardless
        ?>
        <script>
        jQuery(document).ready(function($) {
            var productId = '<?php echo esc_js($product_id); ?>';
            if (!productId) return;

            function doAjaxAddToCart($btn, productId) {
                var isRo = $('body').hasClass('rima-lang-ro') || (localStorage.getItem('rima_lang') === 'ro');
                var originalText = $btn.text();
                $btn.text(isRo ? 'Se adaugă...' : 'Adding...');
                $btn.css({'opacity': '0.7', 'pointer-events': 'none'});
                
                /*  Use our custom silent endpoint to prevent WC notices from saving in the session
 */
                        /*  Trigger fragment refresh to update cart counters
 */
                        $(document.body).trigger('wc_fragment_refresh');
                        
                        var productName = $('.rsc-hero-title').text().trim() || 'Course';
                        var msg = isRo ? '"' + productName + '" a fost adăugat în coș!' : '"' + productName + '" has been added to cart!';
                        
                        var $toast = $('#rima-cart-toast');
                        if($toast.length) {
                            $toast.text(msg).addClass('show-toast');
                            setTimeout(function() {
                                $toast.removeClass('show-toast');
                            }, 4000);
                        }
                        
                        /*  Bounce the header cart icon
 */
                        var $cartHolder = $('.eltdf-shopping-cart-holder');
                        if($cartHolder.length) {
                            $cartHolder.addClass('rima-cart-bounce');
                            setTimeout(function() {
                                $cartHolder.removeClass('rima-cart-bounce');
                            }, 400);
                        }
                    } else {
                        var errMsg = 'Error adding to cart.';
                        if (response && response.data && response.data.message) {
                            errMsg = response.data.message;
                        }
                        alert(errMsg);
                        $btn.text(originalText);
                        $btn.css({'opacity': '1', 'pointer-events': 'auto'});
                    }
                }).fail(function() {
                    $btn.text(originalText);
                    $btn.css({'opacity': '1', 'pointer-events': 'auto'});
                });
            }

            /*  1. Intercept standard WooCommerce cart form (Logged In Users)
 */
            $('form.cart').on('submit', function(e) {
                e.preventDefault();
                var $btn = $(this).find('button[type="submit"]');
                doAjaxAddToCart($btn, productId);
            });

            /*  2. Intercept Academist login/apply buttons (Logged Out Users)
 */
            var $applyBtns = $('.eltdf-lms-buy-button, .eltdf-btn[href*="user-dashboard"]');
            if ($applyBtns.length) {
                $applyBtns.each(function() {
                    var $originalBtn = $(this);
                    var $clone = $originalBtn.clone(false); /*  Clone without events
 */
                    $clone.removeClass('eltdf-lms-buy-button eltdf-lms-buy-button-opened');
                    $clone.addClass('rima-custom-add-to-cart');
                    $clone.attr('href', 'javascript:void(0);');
                    $originalBtn.replaceWith($clone);
                });
                
                /*  Use event delegation on the cloned buttons
 */
                $(document).off('click', '.rima-custom-add-to-cart').on('click', '.rima-custom-add-to-cart', function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    doAjaxAddToCart($(this), productId);
                });
            }

            /*  Force "View Cart" to say "Add to Cart" for consistency on reload
 */
            $('.eltdf-btn').each(function() {
                var txt = $(this).text().trim().toLowerCase();
                if (txt === 'view cart' || txt === 'apply now') {
                    var isRo = $('body').hasClass('rima-lang-ro');
                    $(this).text(isRo ? 'Adaugă în Coș' : 'Add to Cart');
                }
            });
            
            /*  Hide standard WooCommerce notices on this page if they appear on load
 */
            $('.woocommerce-notices-wrapper, .woocommerce-message, .woocommerce-error, .woocommerce-info').hide();
        });
        </script>
        <?php
    }
}, 999);

/**
 * ==============================================================
 * RIMA ACADEMY: Auto-sync LMS Courses with WooCommerce Products
 * ==============================================================
 */
add_action('save_post_course', 'rima_auto_sync_course_with_woo', 10, 3);
function rima_auto_sync_course_with_woo($post_id, $post, $update) {
    // Don't run on autosave or revisions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;
    
    // Check if it's a valid course
    if ($post->post_status !== 'publish' && $post->post_status !== 'draft') return;

    // Get the course price from Academist meta 
    $price = get_post_meta($post_id, 'eltdf_course_price_meta', true);
    if (empty($price)) $price = '0'; 

    // Check if a product is already linked
    $product_id = get_post_meta($post_id, 'eltdf_course_woo_product_meta', true);
    $product = $product_id ? wc_get_product($product_id) : false;
    $thumbnail_id = get_post_thumbnail_id($post_id);

    if ($product_id && $product) {
        // Update existing product price, title, image
        $product->set_regular_price($price);
        $product->set_name($post->post_title);
        if ($thumbnail_id) {
            $product->set_image_id($thumbnail_id);
        }
        $product->save();
        update_post_meta($product_id, '_rima_linked_course_id', $post_id);
    } else {
        // Create new hidden virtual product
        $product = new WC_Product_Simple();
        $product->set_name($post->post_title);
        $product->set_status('publish');
        $product->set_catalog_visibility('hidden');
        $product->set_virtual(true);
        $product->set_sold_individually(true); // Can only buy one of each course
        $product->set_regular_price($price);
        if ($price > 0) {
            $product->set_price($price);
        }
        if ($thumbnail_id) {
            $product->set_image_id($thumbnail_id);
        }
        
        $new_product_id = $product->save();

        if ($new_product_id) {
            // Link it to the course natively for Academist
            update_post_meta($post_id, 'eltdf_course_woo_product_meta', $new_product_id);
            // Optionally link product back to course
            update_post_meta($new_product_id, '_rima_linked_course_id', $post_id);
        }
    }
}

// Admin Trigger to sync all existing courses (Run once)
add_action('admin_init', function() {
    if (isset($_GET['rima_sync_courses']) && current_user_can('manage_options')) {
        $courses = get_posts(array(
            'post_type' => 'course',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ));
        foreach ($courses as $course) {
            rima_auto_sync_course_with_woo($course->ID, $course, true);
        }
        wp_die('Toate cursurile (' . count($courses) . ') au fost sincronizate cu WooCommerce și li s-au creat produse ascunse! Poti inchide aceasta pagina și verifica pe site.');
    }
});

// Bypass stubborn page caches (Cloudflare/WP Rocket) by injecting the JS fix via WooCommerce AJAX fragments!
// Since WooCommerce AJAX is never cached, this guarantees the script runs for all users, even on cached HTML pages.
add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    $inject_js = '<script>
        document.addEventListener("click", function(e) {
            var cartTarget = e.target.closest(".eltdf-shopping-cart-holder, .eltdf-header-cart, .eltdf-cart-icon");
            if (cartTarget) {
                e.preventDefault();
                e.stopPropagation();
                var sideCart = document.getElementById("rima-side-cart");
                var overlay = document.getElementById("rima-side-cart-overlay");
                if (sideCart) sideCart.classList.add("rima-cart-open");
                if (overlay) overlay.classList.add("rima-cart-open");
            }
        }, true);
        
        var sv = \'<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>\';
        var iconNode = document.querySelector(".eltdf-cart-icon");
        if(iconNode) iconNode.innerHTML = sv;
    </script>';

    if (is_array($fragments)) {
        foreach ($fragments as $key => $html) {
            if (strpos($html, 'eltdf-cart-icon') !== false) {
                $fragments[$key] .= $inject_js;
                break; // only inject once
            }
        }
    }
    return $fragments;
}, 999);

add_action('template_redirect', function() {
    if (function_exists('is_shop') && is_shop()) {
        wp_safe_redirect(home_url('/our-courses/'), 301);
        exit;
    }
    
    // Only redirect product pages on GET requests to allow POST form submissions (like Add to Cart) to process
    if (is_singular('product') && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        $product_id = get_the_ID();
        $courses = get_posts(array(
            'post_type' => 'course',
            'meta_key' => 'eltdf_course_woo_product_meta',
            'meta_value' => $product_id,
            'posts_per_page' => 1
        ));
        if (!empty($courses)) {
            wp_safe_redirect(get_permalink($courses[0]->ID), 301);
            exit;
        }
    }
});

/**
 * ==============================================================
 * RIMA ACADEMY: Make single course Add to Cart form submit to current course URL
 * ==============================================================
 */
add_filter('woocommerce_add_to_cart_form_action', function($action) {
    if (is_singular('course')) {
        return ''; // submitting to empty action posts to the current page URL
    }
    return $action;
}, 99);

/**
 * ==============================================================
 * RIMA ACADEMY: Enqueue WooCommerce AJAX Add to Cart script on course listing/archive and single course pages
 * ==============================================================
 */
add_action('wp_enqueue_scripts', function() {
    if (is_singular('course') || is_post_type_archive('course') || is_page('our-courses')) {
        if (function_exists('is_woocommerce')) {
            wp_enqueue_script('wc-add-to-cart');
        }
    }
}, 30);

/**
 * ==============================================================
 * RIMA ACADEMY: Force Child Theme's single-course.php template
 * ==============================================================
 */
add_filter('single_template', function($template) {
    global $post;
    if (!empty($post) && $post->post_type === 'course') {
        $child_template = get_stylesheet_directory() . '/single-course.php';
        if (file_exists($child_template)) {
            return $child_template;
        }
    }
    return $template;
}, 999);

/**
 * ==============================================================
 * RIMA ACADEMY: Remove Forum Tab from Single Course Page
 * ==============================================================
 */
add_filter('academist_elated_filter_single_course_tabs', function($tabs) {
    if (isset($tabs['forum'])) {
        unset($tabs['forum']);
    }
    return $tabs;
}, 99);


/* ============================================================
   2026 — OUR COURSES LISTING: Enqueue & AJAX handler
   ============================================================ */

/**
 * Enqueue assets for Our Courses listing page & single course page
 */
add_action( 'wp_enqueue_scripts', 'rima_enqueue_2026_course_assets', 25 );
function rima_enqueue_2026_course_assets() {
    $theme_uri = get_stylesheet_directory_uri();
    $ver       = wp_get_theme()->get( 'Version' );

    // ── Our Courses listing page ──────────────────────────
    if ( is_page( 'our-courses' ) || is_page_template( 'page-our-courses.php' ) ) {
        wp_enqueue_style(
            'rima-courses',
            $theme_uri . '/assets/css/rima-courses.css',
            array(),
            '1.0.3'
        );
        wp_enqueue_script(
            'rima-courses',
            $theme_uri . '/assets/js/rima-courses.js',
            array(),
            $ver,
            true
        );
        wp_localize_script( 'rima-courses', 'rimaCourses', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'rima_courses_filter' ),
        ) );
    }

    // ── Single Course ─────────────────────────────────────
    if ( is_singular( 'course' ) ) {
        wp_enqueue_style(
            'rima-courses',
            $theme_uri . '/assets/css/rima-courses.css',
            array(),
            $ver
        );
        wp_enqueue_style(
            'rima-single-course',
            $theme_uri . '/assets/css/rima-single-course.css',
            array( 'rima-courses' ),
            $ver
        );
        wp_enqueue_script(
            'rima-single-course',
            $theme_uri . '/assets/js/rima-single-course.js',
            array(),
            $ver,
            true
        );
    }
}

/**
 * AJAX handler — filter / search / sort courses
 */
add_action( 'wp_ajax_rima_filter_courses',        'rima_ajax_filter_courses' );
add_action( 'wp_ajax_nopriv_rima_filter_courses', 'rima_ajax_filter_courses' );

function rima_ajax_filter_courses() {
    check_ajax_referer( 'rima_courses_filter', 'nonce' );

    $category = sanitize_text_field( $_POST['category'] ?? '' );
    $search   = sanitize_text_field( $_POST['search']   ?? '' );
    $sort     = sanitize_text_field( $_POST['sort']     ?? 'title_asc' );
    $page     = max( 1, intval( $_POST['page']     ?? 1 ) );
    $per_page = max( 1, intval( $_POST['per_page'] ?? 9 ) );

    // Build orderby
    $orderby = 'date';
    $order   = 'DESC';
    switch ( $sort ) {
        case 'oldest':
            $order = 'ASC';
            break;
        case 'title_asc':
            $orderby = 'title';
            $order   = 'ASC';
            break;
        case 'title_desc':
            $orderby = 'title';
            $order   = 'DESC';
            break;
    }

    $args = array(
        'post_type'      => 'course',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'orderby'        => $orderby,
        'order'          => $order,
        's'              => $search,
    );

    if ( $category ) {
        $args['tax_query'] = array( array(
            'taxonomy' => 'course-category',
            'field'    => 'slug',
            'terms'    => $category,
        ) );
    }

    $query = new WP_Query( $args );

    if ( ! $query->have_posts() ) {
        wp_send_json_success( array(
            'html'  => '',
            'found' => 0,
            'total' => wp_count_posts( 'course' )->publish,
            'pages' => 0,
        ) );
        return;
    }

    ob_start();

    while ( $query->have_posts() ) {
        $query->the_post();

        $cid         = get_the_ID();
        $cats        = get_the_terms( $cid, 'course-category' );
        $cat_name    = ( ! is_wp_error( $cats ) && ! empty( $cats ) ) ? $cats[0]->name : '';
        $price       = function_exists( 'academist_lms_calculate_course_price' ) ? academist_lms_calculate_course_price( $cid ) : 0;
        $thumb       = get_the_post_thumbnail_url( $cid, 'medium_large' );
        $instr_id    = get_post_meta( $cid, 'eltdf_course_instructor_meta', true );
        $instr_name  = $instr_id ? get_the_title( $instr_id ) : '';
        $duration    = get_post_meta( $cid, 'eltdf_course_duration', true );
        $lessons     = get_post_meta( $cid, 'eltdf_course_lessons_count', true );
        $certificate = get_post_meta( $cid, 'eltdf_course_certificate', true );

        // Price label
        if ( $price == 0 ) {
            $price_badge = '<span class="roc-card-badge-price roc-badge-free"><span class="rima-en">Free</span><span class="rima-ro">Gratuit</span></span>';
        } elseif ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
            $pos = get_option( 'woocommerce_currency_pos', 'right' );
            $sym = get_woocommerce_currency_symbol();
            $pl  = $pos === 'left' ? $sym . $price : $price . ' ' . $sym;
            $price_badge = '<span class="roc-card-badge-price">' . esc_html( $pl ) . '</span>';
        } else {
            $price_badge = '<span class="roc-card-badge-price">' . esc_html( $price ) . '</span>';
        }

        ?>
        <article class="roc-card" data-cat="<?php echo esc_attr( $cats && ! is_wp_error( $cats ) ? $cats[0]->slug : '' ); ?>">
            <a href="<?php the_permalink(); ?>" class="roc-card-image-link" tabindex="-1">
                <div class="roc-card-image">
                    <?php if ( $thumb ) : ?>
                        <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" />
                    <?php else : ?>
                        <div class="roc-card-image-placeholder">
                            <svg width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.18)" stroke-width="1"><path d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                        </div>
                    <?php endif; ?>
                    <?php if ( $cat_name ) : ?>
                        <span class="roc-card-badge-cat"><?php echo esc_html( $cat_name ); ?></span>
                    <?php endif; ?>
                    <?php echo $price_badge; ?>
                </div>
            </a>
            <div class="roc-card-body">
                <h3 class="roc-card-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
                <?php if ( $instr_name ) : ?>
                    <div class="roc-card-instructor">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <?php echo esc_html( $instr_name ); ?>
                    </div>
                <?php endif; ?>
                <p class="roc-card-excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt() ?: get_the_content(), 18 ) ); ?></p>
                <!-- List-mode meta -->
                <div class="roc-card-meta-row">
                    <?php if ( $duration ) : ?>
                        <span class="roc-meta-item">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <?php echo esc_html( $duration ); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ( $lessons ) : ?>
                        <span class="roc-meta-item">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                            <?php echo esc_html( $lessons ); ?> <span class="rima-en">lessons</span><span class="rima-ro">lecții</span>
                        </span>
                    <?php endif; ?>
                    <?php if ( $certificate ) : ?>
                        <span class="roc-meta-item">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                            <span class="rima-en">Certificate</span><span class="rima-ro">Certificat</span>
                        </span>
                    <?php endif; ?>
                </div>
                <a href="<?php the_permalink(); ?>" class="roc-card-cta eltdf-btn eltdf-btn-solid rima-btn-blue">
                    <span class="rima-en">View Course</span>
                    <span class="rima-ro">Vezi Cursul</span>
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        </article>
        <?php
    }

    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success( array(
        'html'  => $html,
        'found' => $query->post_count,
        'total' => $query->found_posts,
        'pages' => $query->max_num_pages,
    ) );
}

/**
 * SPA Iframe Mode for Dashboard Lesson Viewer
 * Hides all theme headers, footers, and padding when ?rima_iframe=1 is present.
 */
add_action( 'wp_head', 'rima_spa_iframe_mode' );
function rima_spa_iframe_mode() {
    if ( isset( $_GET['rima_iframe'] ) && $_GET['rima_iframe'] === '1' ) {
        echo '<style>
            header.eltdf-page-header, 
            footer, 
            .eltdf-mobile-header,
            .eltdf-title-holder,
            .eltdf-top-bar { display: none !important; }
            .eltdf-wrapper-inner { padding: 0 !important; margin: 0 !important; }
            .eltdf-content { margin-top: 0 !important; }
            body { background: #fff !important; overflow-x: hidden; padding-top: 0 !important; }
            .eltdf-container-inner { width: 100% !important; max-width: 100% !important; padding: 20px !important; }
            
            /* Academist LMS specific overrides for full width lesson */
            .eltdf-lms-single-lesson .eltdf-lms-lesson-content { margin: 0 auto; max-width: 900px; padding-bottom: 50px; }
            .eltdf-lms-single-quiz .eltdf-lms-quiz-content { margin: 0 auto; max-width: 900px; padding-bottom: 50px; }
            
            /* Hide the Academist native back-to-course button and breadcrumbs if present */
            .eltdf-lms-single-lesson .eltdf-lms-lesson-back-btn { display: none !important; }
        </style>';
    }
}

/* ============================================================
   RIMA DYNAMIC GLOBE MARKERS
   Generates JSON marker data based on active 'course-category' terms
   ============================================================ */
function rima_get_globe_markers_json() {
    $globe_dict = array(
        'english'    => array('aliases' => array('english', 'engleza'), 'lat' => 51.5072,  'lng' => -0.1276,  'flag' => 'https://flagcdn.com/w40/gb.png', 'isos' => array('GBR', 'USA', 'CAN', 'AUS'), 'label' => 'English'),
        'romanian'   => array('aliases' => array('romanian', 'romana'), 'lat' => 45.9432,  'lng' => 24.9668,  'flag' => 'https://flagcdn.com/w40/ro.png', 'isos' => array('ROU', 'MDA'), 'label' => 'Romanian'),
        'japanese'   => array('aliases' => array('japanese', 'japoneza'), 'lat' => 36.2048,  'lng' => 138.2529, 'flag' => 'https://flagcdn.com/w40/jp.png', 'isos' => array('JPN'), 'label' => 'Japanese'),
        'spanish'    => array('aliases' => array('spanish', 'spaniola'), 'lat' => 40.4637,  'lng' => -3.7492,  'flag' => 'https://flagcdn.com/w40/es.png', 'isos' => array('ESP', 'MEX', 'ARG', 'COL', 'PER', 'CHL'), 'label' => 'Spanish'),
        'french'     => array('aliases' => array('french', 'franceza'), 'lat' => 46.2276,  'lng' => 2.2137,   'flag' => 'https://flagcdn.com/w40/fr.png', 'isos' => array('FRA', 'CAN', 'BEL', 'CHE', 'CMR'), 'label' => 'French'),
        'german'     => array('aliases' => array('german', 'germana'), 'lat' => 51.1657,  'lng' => 10.4515,  'flag' => 'https://flagcdn.com/w40/de.png', 'isos' => array('DEU', 'AUT', 'CHE'), 'label' => 'German'),
        'italian'    => array('aliases' => array('italian', 'italiana'), 'lat' => 41.8719,  'lng' => 12.5674,  'flag' => 'https://flagcdn.com/w40/it.png', 'isos' => array('ITA', 'CHE'), 'label' => 'Italian'),
        'chinese'    => array('aliases' => array('chinese', 'chineza'), 'lat' => 35.8617,  'lng' => 104.1954, 'flag' => 'https://flagcdn.com/w40/cn.png', 'isos' => array('CHN', 'TWN', 'SGP'), 'label' => 'Chinese'),
        'arabic'     => array('aliases' => array('arabic', 'araba'), 'lat' => 23.8859,  'lng' => 45.0792,  'flag' => 'https://flagcdn.com/w40/sa.png', 'isos' => array('SAU', 'EGY', 'ARE', 'MAR', 'DZA'), 'label' => 'Arabic'),
        'portuguese' => array('aliases' => array('portuguese', 'portugheza'), 'lat' => -14.235,  'lng' => -51.9253, 'flag' => 'https://flagcdn.com/w40/pt.png', 'isos' => array('PRT', 'BRA', 'AGO'), 'label' => 'Portuguese'),
        'russian'    => array('aliases' => array('russian', 'rusa'), 'lat' => 61.524,   'lng' => 105.3188, 'flag' => 'https://flagcdn.com/w40/ru.png', 'isos' => array('RUS', 'BLR', 'KAZ'), 'label' => 'Russian'),
        'korean'     => array('aliases' => array('korean', 'coreeana'), 'lat' => 35.9078,  'lng' => 127.7669, 'flag' => 'https://flagcdn.com/w40/kr.png', 'isos' => array('KOR'), 'label' => 'Korean'),
        'dutch'      => array('aliases' => array('dutch', 'olandeza'), 'lat' => 52.1326,  'lng' => 5.2913,   'flag' => 'https://flagcdn.com/w40/nl.png', 'isos' => array('NLD', 'BEL', 'SUR'), 'label' => 'Dutch'),
        'turkish'    => array('aliases' => array('turkish', 'turca'), 'lat' => 38.9637,  'lng' => 35.2433,  'flag' => 'https://flagcdn.com/w40/tr.png', 'isos' => array('TUR'), 'label' => 'Turkish'),
        'hindi'      => array('aliases' => array('hindi'), 'lat' => 20.5937,  'lng' => 78.9629,  'flag' => 'https://flagcdn.com/w40/in.png', 'isos' => array('IND'), 'label' => 'Hindi')
    );

    $found_langs = array();
    
    if ( taxonomy_exists('course-category') ) {
        $course_categories = get_terms(array(
            'taxonomy' => 'course-category',
            'hide_empty' => false
        ));

        if ( ! is_wp_error( $course_categories ) && ! empty( $course_categories ) ) {
            foreach( $course_categories as $term ) {
                $slug = strtolower($term->slug);
                $name = strtolower($term->name);
                
                foreach( $globe_dict as $key => $data ) {
                    $matched = false;
                    foreach ( $data['aliases'] as $alias ) {
                        if( strpos($slug, $alias) !== false || strpos($name, $alias) !== false ) {
                            $matched = true;
                            break;
                        }
                    }
                    if ( $matched ) {
                        if ( ! isset($found_langs[$key]) ) {
                            $found_langs[$key] = array(
                                'lat'   => $data['lat'],
                                'lng'   => $data['lng'],
                                'flag'  => $data['flag'],
                                'label' => $data['label'],
                                'isos'  => $data['isos']
                            );
                        }
                    }
                }
            }
        }
    }

    if ( empty($found_langs) ) {
        foreach( array('english', 'romanian', 'japanese', 'spanish', 'french', 'german') as $def_key ) {
            $data = $globe_dict[$def_key];
            $found_langs[$def_key] = array(
                'lat'   => $data['lat'],
                'lng'   => $data['lng'],
                'flag'  => $data['flag'],
                'label' => $data['label'],
                'isos'  => $data['isos']
            );
        }
    }

    return json_encode(array_values($found_langs));
}


// Remove Addresses from Woo Menu
add_filter( 'woocommerce_account_menu_items', 'rima_remove_addresses_tab', 999 );
function rima_remove_addresses_tab( $items ) {
    if ( isset( $items['edit-address'] ) ) {
        unset( $items['edit-address'] );
    }
    return $items;
}





/* ============================================================
   RIMA: PREMIUM MOBILE MENU OVERRIDE
   ============================================================ */
add_action('wp_footer', function() {
    // Only apply on frontend
    if ( is_admin() ) return;
    ?>
    <div id="rima-custom-mobile-menu">
        <div class="rima-mobile-menu-overlay"></div>
        <div class="rima-mobile-menu-drawer">
            <div class="rima-mobile-menu-header">
                <img src="https://rima-academy.com/wp-content/uploads/2026/06/light-logo.png" alt="Rima Academy" class="rima-mobile-logo">
                <button class="rima-mobile-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="rima-mobile-menu-links">
                <a href="https://rima-academy.com/">
                    <i class="fas fa-home"></i>
                    Home
                </a>
                <a href="https://rima-academy.com/about-us/">
                    <i class="fas fa-info-circle"></i>
                    About Us
                </a>
                <a href="https://rima-academy.com/our-courses/">
                    <i class="fas fa-graduation-cap"></i>
                    Our Courses
                </a>
                <a href="https://rima-academy.com/contact-us/">
                    <i class="fas fa-envelope"></i>
                    Contact Us
                </a>
                <a href="https://rima-academy.com/my-account/" class="rima-mobile-btn">
                    <i class="fas fa-user-circle"></i>
                    My Account
                </a>
            </div>
            
            <div class="rima-mobile-footer-info">
                <div class="rima-mobile-socials">
                    <a href="https://facebook.com/rimaacademy" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://instagram.com/rimaacademy" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://wa.me/+40736852666" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
                <a href="tel:+443003030266" class="rima-mobile-phone">
                    <i class="fas fa-phone"></i>
                    +44 300 303 0266
                </a>
            </div>
        </div>
    </div>

    <style>
    /* KILL THE NATIVE DOWNWARD MENU */
    .eltdf-mobile-nav, nav.eltdf-mobile-nav {
        display: none !important;
        opacity: 0 !important;
        height: 0 !important;
        visibility: hidden !important;
    }

    /* PREMIUM MOBILE MENU CSS */
    #rima-custom-mobile-menu {
        position: fixed !important;
        top: 0 !important; left: 0 !important; width: 100% !important; height: 100vh !important;
        z-index: 2147483647 !important; 
        display: flex;
        pointer-events: none;
    }
    .rima-mobile-menu-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    .rima-mobile-menu-drawer {
        position: absolute;
        top: 0; right: -100%; 
        width: 85%;
        max-width: 340px;
        height: 100%;
        background: #0d1117; 
        box-shadow: -10px 0 40px rgba(0,0,0,0.6);
        display: flex;
        flex-direction: column;
        transition: right 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        border-left: 1px solid rgba(255,255,255,0.08);
        overflow-y: auto;
    }
    #rima-custom-mobile-menu.rima-menu-open {
        pointer-events: auto;
    }
    #rima-custom-mobile-menu.rima-menu-open .rima-mobile-menu-overlay {
        opacity: 1;
    }
    #rima-custom-mobile-menu.rima-menu-open .rima-mobile-menu-drawer {
        right: 0; 
    }
    .rima-mobile-menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 25px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .rima-mobile-logo {
        height: 35px;
    }
    .rima-mobile-close {
        background: rgba(255,255,255,0.05); 
        border: 1px solid rgba(255,255,255,0.1); 
        color: #fff;
        width: 40px; height: 40px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 12px;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
    }
    .rima-mobile-close i {
        font-size: 1.2rem;
    }
    .rima-mobile-close:active {
        transform: scale(0.95);
    }
    .rima-mobile-menu-links {
        padding: 25px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex-grow: 1;
    }
    .rima-mobile-menu-links a {
        display: flex;
        align-items: center;
        gap: 15px;
        color: #c9d1d9; 
        text-decoration: none;
        font-size: 1.1rem;
        font-weight: 500;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
        padding: 16px 18px;
        border-radius: 12px;
        transition: all 0.3s;
        border: 1px solid transparent;
    }
    .rima-mobile-menu-links a i {
        color: #8b949e;
        transition: color 0.3s;
        width: 22px;
        text-align: center;
        font-size: 1.15rem;
    }
    .rima-mobile-menu-links a:hover, .rima-mobile-menu-links a:active {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        color: #fff;
    }
    .rima-mobile-menu-links a:hover i, .rima-mobile-menu-links a:active i {
        color: #fff;
    }
    .rima-mobile-menu-links a.rima-mobile-btn {
        background: linear-gradient(135deg, #ff1949, #d1002a); 
        border: 1px solid rgba(240,246,252,0.1);
        color: #fff !important;
        margin-top: 20px;
        justify-content: center;
        font-weight: 600;
    }
    .rima-mobile-menu-links a.rima-mobile-btn i {
        color: #fff;
        margin-right: 5px;
    }
    .rima-mobile-footer-info {
        padding: 25px 20px;
        border-top: 1px solid rgba(255,255,255,0.08);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }
    .rima-mobile-socials {
        display: flex;
        gap: 15px;
    }
    .rima-mobile-socials a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        color: #fff;
        transition: background 0.3s;
    }
    .rima-mobile-socials a i {
        font-size: 1.3rem;
    }
    .rima-mobile-socials a:hover {
        background: rgba(255,255,255,0.15);
    }
    .rima-mobile-phone {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.1rem;
        background: rgba(255,255,255,0.05);
        padding: 12px 20px;
        border-radius: 30px;
        border: 1px solid rgba(255,255,255,0.1);
        width: 100%;
        justify-content: center;
    }
    .rima-mobile-phone i {
        color: #fff;
    }
    </style>

    <script>
    jQuery(document).ready(function($) {
        $('body').append($('#rima-custom-mobile-menu'));

        document.addEventListener('click', function(e) {
            var target = $(e.target).closest('.eltdf-mobile-header-opener, .eltdf-mobile-menu-opener');
            if (target.length) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                $('#rima-custom-mobile-menu').addClass('rima-menu-open');
                $('.eltdf-mobile-nav').hide(); 
            }
        }, true);

        $(document).on('click', '.rima-mobile-close, .rima-mobile-menu-overlay', function(e) {
            e.preventDefault();
            $('#rima-custom-mobile-menu').removeClass('rima-menu-open');
        });
    });
    </script>
    <?php
}, 9999);

/* ============================================================
   RIMA: MY ACCOUNT MULTIPLE ADDRESSES UI
   ============================================================ */
class ANA_Addresses_My_Account_UI_Child {
    public function __construct() {
        add_action( 'init', [ $this, 'override_addresses_endpoint' ] );
    }

    public function override_addresses_endpoint() {
        if ( class_exists('WooCommerce') && class_exists('ANA_Addresses_Plugin') ) {
            remove_action( 'woocommerce_account_addresses_endpoint', 'woocommerce_account_addresses' );
            add_action( 'woocommerce_account_addresses_endpoint', [ $this, 'render_multiple_addresses' ] );
        }
    }

    public function render_multiple_addresses() {
        $user_id = get_current_user_id();
        $billing_addrs = ANA_Addresses_Plugin::get_addresses( $user_id, 'billing' );
        $shipping_addrs = ANA_Addresses_Plugin::get_addresses( $user_id, 'shipping' );
        ?>
        <div class="ana-my-account-addresses">
            <p>Aici poți gestiona multiple adrese de facturare și livrare pentru contul tău.</p>
            <div class="ana-addresses-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- BILLING -->
                <div class="ana-address-column">
                    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom:15px;">
                        <h3 style="margin:0;">Adrese Facturare</h3>
                        <button class="button ana-btn-add-new" data-type="billing">+ Adaugă Nouă</button>
                    </div>
                    <?php if ( empty($billing_addrs) ) : ?>
                        <div class="woocommerce-message woocommerce-message--info">Nu ai nicio adresă salvată.</div>
                    <?php else : ?>
                        <?php foreach ($billing_addrs as $addr) : 
                            $is_pj = isset($addr['entity_type']) && $addr['entity_type'] === 'pj';
                        ?>
                            <div class="ana-address-card <?php echo !empty($addr['is_default']) ? 'is-default' : ''; ?>" style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; margin-bottom:15px; background: #fff; position: relative;">
                                <?php if (!empty($addr['is_default'])) : ?>
                                    <span class="badge bg-primary" style="position:absolute; top: 15px; right: 15px; font-size:10px; padding: 4px 8px; border-radius:4px; background:#102d56; color:#fff;">PREDEFINITĂ</span>
                                <?php endif; ?>
                                <div class="ana-card-header" style="font-weight: 600; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                                    <?php if ($is_pj) : ?>
                                        🏢 <?php echo esc_html($addr['company'] ?? 'Firmă'); ?>
                                    <?php else : ?>
                                        👤 <?php echo esc_html(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="ana-card-body" style="font-size: 14px; color: #475569; line-height: 1.6;">
                                    <?php if ($is_pj) : ?>
                                        <div><strong>CUI:</strong> <?php echo esc_html($addr['vat_number'] ?? ''); ?></div>
                                        <div><strong>Reg. Com:</strong> <?php echo esc_html($addr['reg_com'] ?? ''); ?></div>
                                    <?php endif; ?>
                                    <div><?php echo esc_html($addr['address_1'] ?? ''); ?> <?php echo esc_html($addr['address_2'] ?? ''); ?></div>
                                    <div><?php echo esc_html($addr['city'] ?? ''); ?>, <?php echo esc_html($addr['state'] ?? ''); ?> <?php echo esc_html($addr['postcode'] ?? ''); ?></div>
                                    <div><?php echo esc_html($addr['country'] ?? 'RO'); ?></div>
                                </div>
                                <div class="ana-card-actions" style="margin-top: 15px; display:flex; gap: 10px;">
                                    <button class="button ana-btn-edit" data-json="<?php echo esc_attr(wp_json_encode($addr)); ?>" style="padding: 5px 10px; font-size: 12px;">Editează</button>
                                    <button class="button ana-btn-delete" data-id="<?php echo esc_attr($addr['id'] ?? ''); ?>" style="padding: 5px 10px; font-size: 12px; background: transparent; color: #dc2626; border: 1px solid #dc2626;">Șterge</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- SHIPPING -->
                <div class="ana-address-column">
                    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom:15px;">
                        <h3 style="margin:0;">Adrese Livrare</h3>
                        <button class="button ana-btn-add-new" data-type="shipping">+ Adaugă Nouă</button>
                    </div>
                    <?php if ( empty($shipping_addrs) ) : ?>
                        <div class="woocommerce-message woocommerce-message--info">Nu ai nicio adresă salvată.</div>
                    <?php else : ?>
                        <?php foreach ($shipping_addrs as $addr) : ?>
                            <div class="ana-address-card <?php echo !empty($addr['is_default']) ? 'is-default' : ''; ?>" style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; margin-bottom:15px; background: #fff; position: relative;">
                                <?php if (!empty($addr['is_default'])) : ?>
                                    <span class="badge bg-primary" style="position:absolute; top: 15px; right: 15px; font-size:10px; padding: 4px 8px; border-radius:4px; background:#102d56; color:#fff;">PREDEFINITĂ</span>
                                <?php endif; ?>
                                <div class="ana-card-header" style="font-weight: 600; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                                    🚚 <?php echo esc_html(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')); ?>
                                </div>
                                <div class="ana-card-body" style="font-size: 14px; color: #475569; line-height: 1.6;">
                                    <div><?php echo esc_html($addr['address_1'] ?? ''); ?> <?php echo esc_html($addr['address_2'] ?? ''); ?></div>
                                    <div><?php echo esc_html($addr['city'] ?? ''); ?>, <?php echo esc_html($addr['state'] ?? ''); ?> <?php echo esc_html($addr['postcode'] ?? ''); ?></div>
                                    <div><?php echo esc_html($addr['country'] ?? 'RO'); ?></div>
                                </div>
                                <div class="ana-card-actions" style="margin-top: 15px; display:flex; gap: 10px;">
                                    <button class="button ana-btn-edit" data-json="<?php echo esc_attr(wp_json_encode($addr)); ?>" style="padding: 5px 10px; font-size: 12px;">Editează</button>
                                    <button class="button ana-btn-delete" data-id="<?php echo esc_attr($addr['id'] ?? ''); ?>" style="padding: 5px 10px; font-size: 12px; background: transparent; color: #dc2626; border: 1px solid #dc2626;">Șterge</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- MODAL FORM -->
        <div id="ana-address-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:99999; align-items:center; justify-content:center;">
            <div id="ana-address-modal" style="background:#fff; width:90%; max-width:600px; max-height:90vh; overflow-y:auto; border-radius:12px; padding:25px; position:relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                <button type="button" id="ana-modal-close" style="position:absolute; top:15px; right:15px; background:none; border:none; font-size:24px; cursor:pointer; color: #64748b;">&times;</button>
                <h3 id="ana-modal-title" style="margin-top:0; font-size:20px; color:#1e293b; margin-bottom:20px;">Adaugă Adresă</h3>
                
                <form id="ana-address-form">
                    <input type="hidden" name="address_id" id="ana_f_id" value="">
                    <input type="hidden" name="address_type" id="ana_f_type" value="billing">
                    <input type="hidden" name="action" value="ana_frontend_save_address">
                    <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('ana_frontend_nonce')); ?>">
                    
                    <div id="ana_f_entity_toggle" style="display:flex; gap:10px; margin-bottom:15px;">
                        <label style="flex:1; text-align:center; padding:10px; background:#f1f5f9; border-radius:8px; cursor:pointer;">
                            <input type="radio" name="entity_type" value="pf" checked> Persoană Fizică
                        </label>
                        <label style="flex:1; text-align:center; padding:10px; background:#f1f5f9; border-radius:8px; cursor:pointer;">
                            <input type="radio" name="entity_type" value="pj"> Persoană Juridică
                        </label>
                    </div>

                    <div id="ana_f_pj_fields" style="display:none; background:#f8fafc; padding:15px; border-radius:8px; margin-bottom:15px;">
                        <div style="display:flex; gap:10px; margin-bottom:10px;">
                            <input type="text" id="ana_f_anaf_cui" placeholder="Caută CUI (ex: 42467528)" style="flex:1; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                            <button type="button" id="ana_f_anaf_btn" class="button" style="background:#102d56; color:white; border-radius:4px; padding: 0 15px;">Caută ANAF</button>
                        </div>
                        <div id="ana_f_anaf_status" style="font-size:12px; margin-bottom:10px;"></div>

                        <label style="display:block; margin-bottom:5px; font-size:13px;">Companie *</label>
                        <input type="text" name="company" id="ana_f_company" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        
                        <div style="display:flex; gap:10px;">
                            <div style="flex:1;">
                                <label style="display:block; margin-bottom:5px; font-size:13px;">CUI *</label>
                                <input type="text" name="vat_number" id="ana_f_vat" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                            </div>
                            <div style="flex:1;">
                                <label style="display:block; margin-bottom:5px; font-size:13px;">Reg. Com</label>
                                <input type="text" name="reg_number" id="ana_f_reg" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:5px; font-size:13px;">Nume *</label>
                            <input type="text" name="first_name" id="ana_f_fn" required style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        </div>
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:5px; font-size:13px;">Prenume *</label>
                            <input type="text" name="last_name" id="ana_f_ln" required style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        </div>
                    </div>

                    <label style="display:block; margin-bottom:5px; font-size:13px;">Țară / Regiune *</label>
                    <input type="text" name="country" id="ana_f_country" value="RO" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">

                    <label style="display:block; margin-bottom:5px; font-size:13px;">Adresa *</label>
                    <input type="text" name="address_1" id="ana_f_addr1" required placeholder="Strada, număr..." style="width:100%; margin-bottom:10px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    <input type="text" name="address_2" id="ana_f_addr2" placeholder="Apartament, bloc... (opțional)" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">

                    <div style="display:flex; gap:10px;">
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:5px; font-size:13px;">Oraș *</label>
                            <input type="text" name="city" id="ana_f_city" required style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        </div>
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:5px; font-size:13px;">Județ *</label>
                            <input type="text" name="state" id="ana_f_state" required style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        </div>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:5px; font-size:13px;">Cod Poștal</label>
                            <input type="text" name="postcode" id="ana_f_postcode" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        </div>
                        <div style="flex:1;">
                            <label style="display:block; margin-bottom:5px; font-size:13px;">Telefon</label>
                            <input type="text" name="phone" id="ana_f_phone" style="width:100%; margin-bottom:15px; padding:8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        </div>
                    </div>

                    <label style="display:flex; align-items:center; gap:10px; margin-top:10px; cursor:pointer; font-size: 14px;">
                        <input type="checkbox" name="is_default" id="ana_f_def" value="1">
                        Setează ca adresă predefinită
                    </label>

                    <div style="margin-top:25px; text-align:right;">
                        <button type="button" class="button" id="ana-modal-cancel" style="background:transparent; color:#64748b; margin-right: 10px; border: 1px solid #cbd5e1; padding: 8px 15px; border-radius: 4px;">Anulează</button>
                        <button type="submit" class="button" id="ana-modal-save" style="background:#102d56; color:#fff; padding: 8px 20px; border-radius: 4px; border: none; cursor: pointer;">Salvează Adresa</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('.ana-btn-add-new').on('click', function(e) {
                e.preventDefault();
                $('#ana-address-form')[0].reset();
                $('#ana_f_id').val('');
                $('#ana_f_type').val($(this).data('type'));
                $('#ana-modal-title').text($(this).data('type') === 'shipping' ? 'Adaugă Adresă Livrare' : 'Adaugă Adresă Facturare');
                
                if($(this).data('type') === 'shipping') {
                    $('#ana_f_entity_toggle').hide();
                    $('input[name="entity_type"][value="pf"]').prop('checked', true).trigger('change');
                } else {
                    $('#ana_f_entity_toggle').show();
                    $('input[name="entity_type"][value="pf"]').prop('checked', true).trigger('change');
                }
                $('#ana-address-modal-overlay').css('display', 'flex');
            });

            $('.ana-btn-edit').on('click', function(e) {
                e.preventDefault();
                var data = $(this).data('json');
                $('#ana_f_id').val(data.id || '');
                $('#ana_f_type').val(data.address_type || 'billing');
                $('#ana-modal-title').text('Editează Adresa');

                if(data.address_type === 'shipping') {
                    $('#ana_f_entity_toggle').hide();
                } else {
                    $('#ana_f_entity_toggle').show();
                }

                $('input[name="entity_type"][value="' + (data.entity_type || 'pf') + '"]').prop('checked', true).trigger('change');
                $('#ana_f_company').val(data.company || '');
                $('#ana_f_vat').val(data.vat_number || '');
                $('#ana_f_reg').val(data.reg_com || '');
                $('#ana_f_fn').val(data.first_name || '');
                $('#ana_f_ln').val(data.last_name || '');
                $('#ana_f_country').val(data.country || 'RO');
                $('#ana_f_addr1').val(data.address_1 || '');
                $('#ana_f_addr2').val(data.address_2 || '');
                $('#ana_f_city').val(data.city || '');
                $('#ana_f_state').val(data.state || '');
                $('#ana_f_postcode').val(data.postcode || '');
                $('#ana_f_phone').val(data.phone || '');
                $('#ana_f_def').prop('checked', data.is_default == 1);
                $('#ana-address-modal-overlay').css('display', 'flex');
            });

            $('input[name="entity_type"]').on('change', function() {
                if ($(this).val() === 'pj') {
                    $('#ana_f_pj_fields').slideDown();
                    $('#ana_f_company, #ana_f_vat').prop('required', true);
                } else {
                    $('#ana_f_pj_fields').slideUp();
                    $('#ana_f_company, #ana_f_vat').prop('required', false);
                }
            });

            $('#ana-modal-close, #ana-modal-cancel').on('click', function() {
                $('#ana-address-modal-overlay').hide();
            });

            $('.ana-btn-delete').on('click', function(e) {
                e.preventDefault();
                if(confirm('Sigur vrei să ștergi această adresă?')) {
                    var id = $(this).data('id');
                    var btn = $(this);
                    btn.text('...');
                    $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                        action: 'ana_frontend_delete_address',
                        address_id: id,
                        nonce: '<?php echo esc_attr(wp_create_nonce('ana_frontend_nonce')); ?>'
                    }, function(res) {
                        if(res.success) {
                            location.reload();
                        } else {
                            alert(res.data.message || 'Eroare la ștergere');
                            btn.text('Șterge');
                        }
                    });
                }
            });

            $('#ana-address-form').on('submit', function(e) {
                e.preventDefault();
                var btn = $('#ana-modal-save');
                btn.prop('disabled', true).text('Se salvează...');
                $.post('<?php echo admin_url('admin-ajax.php'); ?>', $(this).serialize(), function(res) {
                    if(res.success) {
                        location.reload();
                    } else {
                        alert(res.data.message || 'Eroare la salvare');
                        btn.prop('disabled', false).text('Salvează Adresa');
                    }
                }).fail(function() {
                    alert('Eroare de conexiune.');
                    btn.prop('disabled', false).text('Salvează Adresa');
                });
            });

            $('#ana_f_anaf_btn').on('click', function() {
                var cui = $('#ana_f_anaf_cui').val().trim();
                if(!cui) return;
                var btn = $(this);
                btn.prop('disabled', true).text('⏳');
                $('#ana_f_anaf_status').html('<span style="color:#d97706">Se comunică cu ANAF...</span>');
                $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                    action: 'rima_anaf_lookup',
                    cui: cui,
                    nonce: '<?php echo esc_attr(wp_create_nonce('rima_anaf_lookup')); ?>'
                }, function(res) {
                    btn.prop('disabled', false).text('Caută ANAF');
                    if(res.success && res.data) {
                        $('#ana_f_anaf_status').html('<span style="color:#059669">✅ Găsit!</span>');
                        $('#ana_f_company').val(res.data.name || '');
                        $('#ana_f_vat').val(cui);
                        $('#ana_f_reg').val(res.data.reg_com || '');
                        $('#ana_f_addr1').val(res.data.address || '');
                        $('#ana_f_city').val(res.data.city || '');
                        $('#ana_f_state').val(res.data.county || '');
                        if(res.data.phone) $('#ana_f_phone').val(res.data.phone);
                    } else {
                        $('#ana_f_anaf_status').html('<span style="color:#dc2626">❌ Nu a fost găsit.</span>');
                    }
                });
            });
            
            function adjustGrid() {
                if(window.innerWidth < 768) {
                    $('.ana-addresses-grid').css({'grid-template-columns': '1fr'});
                } else {
                    $('.ana-addresses-grid').css({'grid-template-columns': '1fr 1fr'});
                }
            }
            $(window).resize(adjustGrid);
            adjustGrid();
        });
        </script>
        <?php
    }
}
new ANA_Addresses_My_Account_UI_Child();


/* ============================================================
   RIMA: MULTIPLE ADDRESSES SYSTEM (Moved from Plugin)
   ============================================================ */



/* ============================================================
   RIMA: CUSTOM MY ACCOUNT ADDRESSES AJAX HANDLERS
   ============================================================ */
add_action( 'wp_ajax_ana_frontend_save_address', 'ana_frontend_save_address_handler' );
function ana_frontend_save_address_handler() {
    check_ajax_referer( 'ana_frontend_nonce', 'nonce' );
    if ( ! class_exists('ANA_Addresses_Plugin') ) {
        wp_send_json_error( [ 'message' => 'Pluginul de adrese nu este activ.' ] );
    }
    
    $user_id = get_current_user_id();
    if ( ! $user_id ) wp_send_json_error( [ 'message' => 'Nu e?ti autentificat.' ] );

    $data = [
        'user_id'      => $user_id,
        'address_type' => sanitize_text_field( $_POST['address_type'] ?? 'billing' ),
        'entity_type'  => sanitize_text_field( $_POST['entity_type'] ?? 'pf' ),
        'company'      => sanitize_text_field( $_POST['company'] ?? '' ),
        'vat_number'   => sanitize_text_field( $_POST['vat_number'] ?? '' ),
        'reg_com'      => sanitize_text_field( $_POST['reg_number'] ?? '' ),
        'first_name'   => sanitize_text_field( $_POST['first_name'] ?? '' ),
        'last_name'    => sanitize_text_field( $_POST['last_name'] ?? '' ),
        'address_1'    => sanitize_text_field( $_POST['address_1'] ?? '' ),
        'address_2'    => sanitize_text_field( $_POST['address_2'] ?? '' ),
        'city'         => sanitize_text_field( $_POST['city'] ?? '' ),
        'state'        => sanitize_text_field( $_POST['state'] ?? '' ),
        'postcode'     => sanitize_text_field( $_POST['postcode'] ?? '' ),
        'country'      => sanitize_text_field( $_POST['country'] ?? 'RO' ),
        'phone'        => sanitize_text_field( $_POST['phone'] ?? '' ),
        'is_default'   => !empty($_POST['is_default']) ? 1 : 0
    ];

    $address_id = !empty($_POST['address_id']) ? intval($_POST['address_id']) : null;
    $result = ANA_Addresses_Plugin::save_address( $address_id, $data );

    if ( $result ) {
        wp_send_json_success();
    } else {
        wp_send_json_error( [ 'message' => 'Nu s-a putut salva adresa �n baza de date.' ] );
    }
}

add_action( 'wp_ajax_ana_frontend_delete_address', 'ana_frontend_delete_address_handler' );
function ana_frontend_delete_address_handler() {
    check_ajax_referer( 'ana_frontend_nonce', 'nonce' );
    if ( ! class_exists('ANA_Addresses_Plugin') ) wp_send_json_error();
    
    $user_id = get_current_user_id();
    $address_id = intval($_POST['address_id']);
    
    global $wpdb;
    $table = $wpdb->prefix . 'ana_customer_addresses';
    $addr = $wpdb->get_row($wpdb->prepare("SELECT user_id FROM $table WHERE id = %d", $address_id));
    
    if ( $addr && $addr->user_id == $user_id ) {
        ANA_Addresses_Plugin::delete_address($address_id);
        wp_send_json_success();
    }
    wp_send_json_error( [ 'message' => 'Permisiune refuzata.' ] );
}

add_action('wp_ajax_rima_anaf_lookup', 'rima_anaf_lookup_handler');
add_action('wp_ajax_nopriv_rima_anaf_lookup', 'rima_anaf_lookup_handler');
function rima_anaf_lookup_handler() {
    check_ajax_referer('rima_anaf_lookup', 'nonce');
    $cui = sanitize_text_field($_POST['cui'] ?? '');
    if (empty($cui)) wp_send_json_error();

    $response = wp_remote_post('https://webservicesp.anaf.ro/PlatitorTvaRest/api/v8/ws/tva', [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => wp_json_encode([['cui' => $cui, 'data' => date('Y-m-d')]]),
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Eroare conectare ANAF']);
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (!empty($body['found']) && !empty($body['found'][0])) {
        $data = $body['found'][0];
        $address = $data['adresa'] ?? '';
        $city = ''; $county = '';
        if (preg_match('/Jud\. ([^,]+)/', $address, $m)) $county = trim($m[1]);
        if (preg_match('/Mun\. ([^,]+)/', $address, $m)) $city = trim($m[1]);
        elseif (preg_match('/Ors\. ([^,]+)/', $address, $m)) $city = trim($m[1]);

        wp_send_json_success([
            'name'    => $data['denumire'] ?? '',
            'address' => $address,
            'reg_com' => $data['nrRegCom'] ?? '',
            'phone'   => $data['telefon'] ?? '',
            'city'    => $city,
            'county'  => $county
        ]);
    }

    wp_send_json_error(['message' => 'Nu a fost gasit.']);
}
