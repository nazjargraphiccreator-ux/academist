<?php
/**
 * Academist Child Theme �€” functions.php
 * RIMA Academy Professional Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── RIMA Email Notification System ────────────────────────────
require_once get_stylesheet_directory() . '/inc/rima-email-notifications.php';

// ── RIMA Secure PDF Tracker ────────────────────────────
require_once get_stylesheet_directory() . '/inc/pdf-server.php';
require_once get_stylesheet_directory() . '/inc/product-meta.php';
require_once get_stylesheet_directory() . '/inc/rima-pricing-ajax.php';

// ── RIMA B2B Checkout Logic ────────────────────────────
require_once get_stylesheet_directory() . '/inc/rima-b2b-checkout.php';
// Custom Shortcodes & Globe Helpers
require_once get_stylesheet_directory() . '/inc/custom-shortcodes.php';


// Unhook Academist defaults that conflict - use template_redirect for correct timing
add_action('wp', 'rima_remove_academist_annoyances', 1);
function rima_remove_academist_annoyances() {
    // Remove parent theme's mobile header (always - we have our own)
    remove_action('academist_elated_action_after_body_tag', 'academist_elated_get_mobile_header', 10);
    remove_action('academist_elated_action_after_header_area', 'academist_elated_get_mobile_header', 10);
    // Remove parent theme's title on our courses page
    remove_action('academist_elated_action_after_header_area', 'academist_elated_get_title', 20);
}

// Also hook earlier (wp_loaded) as a belt-and-suspenders approach
add_action('wp_loaded', 'rima_remove_academist_annoyances_early', 1);
function rima_remove_academist_annoyances_early() {
    remove_action('academist_elated_action_after_body_tag', 'academist_elated_get_mobile_header', 10);
    remove_action('academist_elated_action_after_header_area', 'academist_elated_get_mobile_header', 10);
}

// -- FIX: Prevent Fatal Error from academist-core plugin --
// academist_core_set_open_graph_meta() calls academist_elated_option_get_value()
// which can be undefined. Remove dangerous hook; replace with a safe version.
add_action( 'after_setup_theme', 'rima_fix_open_graph_hook', 99 );
function rima_fix_open_graph_hook() {
    remove_action( 'academist_elated_action_header_meta', 'academist_core_set_open_graph_meta', 10 );
    if ( ! function_exists( 'academist_elated_option_get_value' ) ) {
        add_action( 'academist_elated_action_header_meta', 'rima_safe_open_graph_meta', 10 );
    }
}
function rima_safe_open_graph_meta() {
    if ( ! is_singular() ) return;
    global $post;
    $title       = get_the_title( $post );
    $description = has_excerpt( $post )
        ? get_the_excerpt( $post )
        : wp_trim_words( strip_shortcodes( $post->post_content ), 30 );
    $url   = get_permalink( $post );
    $image = '';
    if ( has_post_thumbnail( $post ) ) {
        $img   = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'large' );
        $image = $img ? $img[0] : '';
    }
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
    echo '<meta property="og:type" content="website" />' . "\n";
    if ( $image ) {
        echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
    }
}


// ── RIMA Header & Footer Builder ────────────────────────────


// Force WPBakery to enable on our new Template Post Type
add_action( 'init', 'rima_enable_vc_for_templates' );
function rima_enable_vc_for_templates() {
    if ( function_exists('vc_set_default_editor_post_types') ) {
        $list = get_option( 'wpb_js_content_types' );
        if ( ! is_array( $list ) ) {
            $list = array( 'page', 'post' );
        }
        if ( ! in_array( 'rima_template', $list ) ) {
            $list[] = 'rima_template';
            update_option( 'wpb_js_content_types', $list );
        }
    }
}

// ── RIMA Modern Cart & Checkout CSS ────────────────────────────
add_action( 'wp_enqueue_scripts', 'rima_enqueue_modern_checkout_css', 999 );
function rima_enqueue_modern_checkout_css() {
    if ( class_exists( 'WooCommerce' ) ) {
        if ( is_cart() || is_checkout() || is_account_page() ) {
            wp_enqueue_style(
                'rima-cart-checkout-modern',
                get_stylesheet_directory_uri() . '/assets/css/rima-cart-checkout-modern.css',
                array(),
                time()
            );
        }
    }
}

// ── RIMA 2026 Design System Enqueue ────────────────────────────
add_action( 'wp_enqueue_scripts', 'rima_enqueue_design_system', 999 );
function rima_enqueue_design_system() {
    // Core Tokens & Typography
    wp_enqueue_style(
        'rima-global-tokens',
        get_stylesheet_directory_uri() . '/assets/css/rima-global.css',
        array(),
        time() );
    
    // UI Components Library
    wp_enqueue_style(
        'rima-components',
        get_stylesheet_directory_uri() . '/assets/css/components.css',
        array('rima-global-tokens'),
        time() );

    // Header Styles
    wp_enqueue_style(
        'rima-header',
        get_stylesheet_directory_uri() . '/assets/css/header.css',
        array('rima-global-tokens', 'rima-components'),
        time() );

    // Footer Styles
    wp_enqueue_style(
        'rima-footer',
        get_stylesheet_directory_uri() . '/assets/css/footer.css',
        array('rima-global-tokens'),
        time() );
    // Page Specific Styles
    if (is_page_template('page-our-courses.php')) {
        wp_enqueue_style('rima-courses', get_stylesheet_directory_uri() . '/assets/css/rima-courses.css', array('rima-global-tokens', 'rima-components'), time() );
    }
    if (is_page_template('page-contact.php')) {
        wp_enqueue_style('rima-contact', get_stylesheet_directory_uri() . '/assets/css/rima-contact.css', array('rima-global-tokens', 'rima-components'), time() );
    }
}

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
        'company'    => 'Companie (op?ional)',
        'country'    => '?ara / Regiune',
        'address_1'  => 'Adresa stradala',
        'address_2'  => 'Apartament, suita, unitate etc. (op?ional)',
        'city'       => 'Localitate / Ora?',
        'state'      => 'Jude? / Sector',
        'postcode'   => 'Cod po?tal',
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
        'billing_email' => 'Adresa de email',
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

require_once get_stylesheet_directory() . '/inc/enqueue-assets.php';
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
   1e. WPBAKERY — For?eaza assets + shortcodes global
   ============================================================
   Permite shortcode-urile WPBakery (inclusiv static blocks) sa
   func?ioneze pe orice template PHP, nu doar pe paginile editate
   cu WPBakery. Necesar pentru footer/header din Static Blocks.
   ============================================================ */

// 1) �ncarca CSS-ul WPBakery pe toate paginile frontend
add_action( 'wp_enqueue_scripts', function() {
    if ( class_exists( 'Vc_Manager' ) ) {
        wp_enqueue_style( 'js_composer_front' );
        wp_enqueue_style( 'js_composer_custom_css' );
    }
}, 30 );

// 2) �ncarca JS-ul WPBakery pe toate paginile frontend
add_action( 'wp_enqueue_scripts', function() {
    if ( class_exists( 'Vc_Manager' ) ) {
        wp_enqueue_script( 'wpb_composer_front_js' );
    }
}, 30 );

// 3) �nregistreaza Post Type 'static_block' pentru ca userul sa aiba meniul "Static Blocks" �napoi
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

// 4) For?eaza WPBakery sa se activeze pe post type-ul 'static_block' implicit
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

// 5) Helper: randeaza un Static Block WPBakery dupa slug
//    Folosire �n template: rima_static_block( 'footer' );
if ( ! function_exists( 'rima_static_block' ) ) {
    function rima_static_block( $slug, $echo = true ) {
        // Cauta post-ul de tip 'static_block' cu slug-ul dat
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

        // Proceseaza shortcode-urile WPBakery ?i returneaza HTML-ul
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
   1b. LANGUAGE SYSTEM – Inline CSS + initial body class (no flash)
   ============================================================ */
add_action( 'wp_head', function() {
    // Determine active language: server pref (logged-in) > cookie (guest) > default EN
    $lang = 'en';
    if ( is_user_logged_in() ) {
        $lang = get_user_meta( get_current_user_id(), '_rima_lang_pref', true ) ?: 'en';
    } else {
        // Read googtrans cookie server-side if possible
        if ( ! empty( $_COOKIE['googtrans'] ) ) {
            $parts = explode( '/', trim( $_COOKIE['googtrans'], '/' ) );
            $cookie_lang = end( $parts );
            if ( in_array( $cookie_lang, [ 'en', 'ro' ], true ) ) {
                $lang = $cookie_lang;
            }
        }
    }
    // Store for JS below
    ?>
    <script>window.rimaInitLang = '<?php echo esc_js($lang); ?>';</script>
    <style id="rima-lang-style-inline">
    /* ── Manual bilingual spans ── */
    .rima-ro { display: none !important; }
    /* When body has rima-lang-ro: show RO spans, hide EN spans */
    body.rima-lang-ro .rima-ro { display: inline !important; }
    body.rima-lang-ro .rima-en { display: none !important; }

    /* ── Hide Google Translate toolbar ── */
    .goog-te-banner-frame.skiptranslate,
    .goog-te-gadget-icon,
    #\:1\.container { display: none !important; }
    body { top: 0px !important; }
    #goog-gt-tt,
    .goog-tooltip,
    .goog-tooltip:hover { display: none !important; }
    .goog-text-highlight {
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }
    </style>
    <?php
    // Apply class via inline script BEFORE body renders (avoids flash)
    if ( $lang === 'ro' ) {
        ?><script>document.documentElement.classList.add('rima-lang-ro');</script><?php
    }
}, 1 );

/* ============================================================
   1c. LANGUAGE SYSTEM – Footer: Google Translate + unified toggle
   ============================================================ */
add_action( 'wp_footer', function() {
    $lang_pref     = 'en';
    $is_logged_in  = is_user_logged_in();
    if ( $is_logged_in ) {
        $lang_pref = get_user_meta( get_current_user_id(), '_rima_lang_pref', true ) ?: 'en';
    }
    ?>
    <!-- Google Translate Widget (hidden) -->
    <div id="google_translate_element" style="display:none;"></div>
    <script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'en',
            includedLanguages: 'ro,en',
            autoDisplay: false
        }, 'google_translate_element');
    }
    </script>
    <script async src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <script>
    /* ======================================================
       RIMA Unified Language Controller v2
       ====================================================== */
    (function() {
        'use strict';

        // ── State ──
        var RIMA_LANG = {
            current: window.rimaInitLang || 'en',
            isLoggedIn: <?php echo $is_logged_in ? 'true' : 'false'; ?>,
            ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('rima_lang_nonce'); ?>',

            // ── Helpers ──
            setBodyClass: function(lang) {
                if (lang === 'ro') {
                    document.body.classList.add('rima-lang-ro');
                    document.body.classList.remove('rima-lang-en');
                } else {
                    document.body.classList.remove('rima-lang-ro');
                    document.body.classList.add('rima-lang-en');
                }
            },

            setCookie: function(lang) {
                var val = lang === 'ro' ? '/en/ro' : '/en/en';
                var domain = window.location.hostname;
                document.cookie = 'googtrans=' + val + '; path=/; domain=' + domain;
                document.cookie = 'googtrans=' + val + '; path=/';
            },

            triggerGoogleTranslate: function(lang) {
                var sel = document.querySelector('select.goog-te-combo');
                if (sel) {
                    sel.value = lang;
                    sel.dispatchEvent(new Event('change'));
                    return true;
                }
                return false;
            },

            saveToServer: function(lang, callback) {
                if (!this.isLoggedIn) { if(callback) callback(); return; }
                var xhr = new XMLHttpRequest();
                xhr.open('POST', this.ajaxUrl, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && callback) callback();
                };
                xhr.send('action=rima_save_lang_pref&lang=' + lang + '&nonce=' + this.nonce);
            },

            // ── Main toggle ──
            toggle: function() {
                var newLang = (this.current === 'ro') ? 'en' : 'ro';
                this.apply(newLang, true);
            },

            apply: function(lang, saveServer) {
                this.current = lang;

                // 1. Body class (instant visual feedback for manual .rima-en/.rima-ro spans)
                this.setBodyClass(lang);

                // 2. localStorage (for JS components that read it)
                try { localStorage.setItem('rima_lang', lang); } catch(e) {}

                // 3. Cookie for Google Translate persistence
                this.setCookie(lang);

                // 4. Trigger Google Translate (no reload needed)
                var gtDone = this.triggerGoogleTranslate(lang);

                // 5. Update all toggle button labels on the page
                this.updateToggleUI(lang);

                // 6. Translate WooCommerce dynamic strings
                this.translateWcStrings(lang);

                // 7. Save to server for logged-in users
                if (saveServer) {
                    this.saveToServer(lang);
                }

                // 8. If GT not ready yet, retry after it loads
                if (!gtDone && lang === 'ro') {
                    var self = this;
                    var tries = 0;
                    var retry = setInterval(function() {
                        if (self.triggerGoogleTranslate(lang) || ++tries > 20) {
                            clearInterval(retry);
                        }
                    }, 300);
                }
            },

            updateToggleUI: function(lang) {
                // Update any toggle buttons with data-lang-en / data-lang-ro attributes
                var btns = document.querySelectorAll('[data-rima-lang-toggle]');
                btns.forEach(function(btn) {
                    var labelEl = btn.querySelector('.rima-lang-label');
                    if (labelEl) {
                        labelEl.textContent = lang === 'ro' ? 'EN' : 'RO';
                    }
                    btn.setAttribute('aria-label', lang === 'ro' ? 'Switch to English' : 'Comuta �n Rom�na');
                    btn.title = lang === 'ro' ? 'Switch to English' : 'Comuta �n Rom�na';
                });
            },

            translateWcStrings: function(lang) {
                var isRo = (lang === 'ro');
                // Mini-cart
                var emptyMsg = document.querySelector('.woocommerce-mini-cart__empty-message');
                if (emptyMsg) emptyMsg.textContent = isRo ? 'Niciun produs �n co?.' : 'No products in the cart.';
                var viewCart = document.querySelector('.woocommerce-mini-cart__buttons .button.wc-forward:not(.checkout)');
                if (viewCart) viewCart.textContent = isRo ? 'Vezi Co?ul' : 'View Cart';
                var checkoutBtn = document.querySelector('.woocommerce-mini-cart__buttons .button.checkout');
                if (checkoutBtn) checkoutBtn.textContent = isRo ? 'Finalizare Comanda' : 'Checkout';
            },

            // ── Init ──
            init: function() {
                var self = this;

                // Apply stored language on load
                this.setBodyClass(this.current);
                this.translateWcStrings(this.current);
                this.updateToggleUI(this.current);

                // Delegate click on ALL toggle selectors (unified)
                document.addEventListener('click', function(e) {
                    var t = e.target.closest(
                        '[data-rima-lang-toggle], #rima-lang-switch, #rima-header-lang-toggle, .rima-lang-toggle-card'
                    );
                    if (!t) return;
                    e.preventDefault();
                    e.stopPropagation();
                    self.toggle();
                });

                // Sync GT after it finishes loading (may take 1-2s)
                var syncTimer = setInterval(function() {
                    var sel = document.querySelector('select.goog-te-combo');
                    if (!sel) return;
                    clearInterval(syncTimer);
                    if (self.current === 'ro' && sel.value !== 'ro') {
                        sel.value = 'ro';
                        sel.dispatchEvent(new Event('change'));
                    } else if (self.current === 'en' && sel.value !== 'en') {
                        sel.value = 'en';
                        sel.dispatchEvent(new Event('change'));
                    }
                }, 400);

                // WooCommerce cart update → re-translate
                if (typeof jQuery !== 'undefined') {
                    jQuery(document.body).on(
                        'wc_fragments_refreshed wc_fragments_loaded updated_wc_div added_to_cart',
                        function() { self.translateWcStrings(self.current); }
                    );
                }

                // Expose globally
                window.RIMA_LANG = self;
                // Legacy compat: expose translateWooCommerceStrings
                window.translateWooCommerceStrings = function() { self.translateWcStrings(self.current); };
            }
        };

        // Auto-init when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() { RIMA_LANG.init(); });
        } else {
            RIMA_LANG.init();
        }
    })();
    </script>
    <?php
}, 5 );

/* ============================================================
   1d. AJAX: Save language preference (logged-in users only)
   ============================================================ */
add_action( 'wp_ajax_rima_save_lang_pref', function() {
    check_ajax_referer( 'rima_lang_nonce', 'nonce' );
    if ( ! is_user_logged_in() ) wp_send_json_error( 'not_logged_in' );
    $lang = sanitize_key( $_POST['lang'] ?? 'en' );
    if ( ! in_array( $lang, [ 'en', 'ro' ], true ) ) wp_send_json_error( 'invalid_lang' );
    update_user_meta( get_current_user_id(), '_rima_lang_pref', $lang );
    wp_send_json_success( [ 'lang' => $lang ] );
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
    $reply_subject = sprintf( 'Am primit mesajul tau — %s', $site_name );
    $reply_body    = "Buna {$first_name},\n\n"
                   . "Mul?umim ca ne-ai contactat! Am primit mesajul tau ?i �?i vom raspunde �n cel mult 24 de ore.\n\n"
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
   2. AVATAR UPLOAD �€” AJAX HANDLER
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

require_once get_stylesheet_directory() . '/inc/header-dropdown-nav.php';
require_once get_stylesheet_directory() . '/inc/woo-menu-cleanup.php';
require_once get_stylesheet_directory() . '/inc/plugin-template-overrides.php';
require_once get_stylesheet_directory() . '/inc/force-registration.php';

// ==========================================
// RIMA ACADEMY: Redirect to Control Panel on Theme Activation
// ==========================================
add_action( 'after_switch_theme', 'rima_redirect_to_control_panel' );
function rima_redirect_to_control_panel() {
    if ( ! get_option( 'rima_theme_activated_redirect' ) ) {
        update_option( 'rima_theme_activated_redirect', true );
        wp_safe_redirect( admin_url( 'admin.php?page=rima-academy-dashboard' ) );
        exit;
    }
}

// ==========================================
// RIMA ACADEMY: WooCommerce Premium Styling
// ==========================================
add_action( 'wp_enqueue_scripts', 'rima_enqueue_woo_overrides', 999 );
function rima_enqueue_woo_overrides() {
    if ( class_exists('WooCommerce') && ( is_woocommerce() || is_checkout() || is_cart() || is_account_page() ) ) {
        wp_enqueue_style( 'rima-woo-overrides', get_stylesheet_directory_uri() . '/assets/css/rima-woo-overrides.css', array(), '1.0' );
    }
}


require_once get_stylesheet_directory() . '/inc/rima-vc-elements.php';







