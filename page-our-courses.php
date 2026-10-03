<?php
// 1. Fetch Languages and their Courses for the 3D Flip Cards
$language_data = array();
$course_cats = get_terms( array(
    'taxonomy'   => 'course-category',
    'hide_empty' => true,
) );

if ( ! empty( $course_cats ) && ! is_wp_error( $course_cats ) ) {
    
    foreach ( $course_cats as $cat ) {
        $slug = strtolower($cat->slug);
        
        // Fetch image set in Academist LMS (course-category taxonomy)
        $cat_img = get_term_meta( $cat->term_id, 'course_category_image', true );
        
        // Elated themes sometimes store the attachment ID instead of URL
        if ( is_numeric( $cat_img ) ) {
            $cat_img = wp_get_attachment_url( $cat_img );
        }

        // Fetch up to 4 courses for this category
        $courses_args = array(
            'post_type'      => 'course',
            'posts_per_page' => 4,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'course-category',
                    'field'    => 'slug',
                    'terms'    => $cat->slug,
                ),
            ),
        );
        $courses_query = new WP_Query($courses_args);
        $cat_courses = array();

        if ($courses_query->have_posts()) {
            while ($courses_query->have_posts()) {
                $courses_query->the_post();
                $c_id = get_the_ID();
                $price = function_exists( 'academist_lms_calculate_course_price' ) ? academist_lms_calculate_course_price( $c_id ) : 0;
                
                $price_html = '';
                if ( $price > 0 ) {
                    if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
                        $pos = get_option( 'woocommerce_currency_pos', 'right' );
                        $sym = get_woocommerce_currency_symbol();
                        $price_html = $pos === 'left' ? esc_html( $sym . $price ) : esc_html( $price . ' ' . $sym );
                    } else {
                        $price_html = esc_html( $price );
                    }
                } else {
                    $price_html = 'Free';
                }

                // Extract level (e.g. A1-A2) from title
                $title = get_the_title();
                $level = '';
                if (preg_match('/([A-C][1-2]\s*-\s*[A-C][1-2])|([A-C][1-2])/', $title, $matches)) {
                    $level = $matches[0];
                    $title = trim(str_replace($level, '', $title));
                    $title = trim(trim($title, '-:'));
                }
                
                $excerpt = wp_trim_words(get_the_excerpt(), 8, '...');

                $cat_courses[] = array(
                    'title'      => $title,
                    'level'      => $level ? $level : 'All Levels',
                    'link'       => get_permalink(),
                    'excerpt'    => $excerpt,
                    'price_html' => $price_html
                );
                
                // If the category has no custom image, use the featured image of the first course
                if ( empty( $cat_img ) && has_post_thumbnail() ) {
                    $cat_img = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                }
            }
            wp_reset_postdata();
        }
        
        // Final fallback if absolutely no image exists
        if ( empty( $cat_img ) ) {
            $cat_img = 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=800&auto=format&fit=crop';
        }

        // Only add language if it has courses
        if (!empty($cat_courses)) {
            $language_data[] = array(
                'name'    => $cat->name,
                'slug'    => $cat->slug,
                'img'     => $cat_img,
                'courses' => $cat_courses
            );
        }
    }
}

// Reorder languages: Romanian, English, Japanese
$ordered_data = array();
$desired_order = array('romanian', 'english', 'japanese');
foreach ($desired_order as $slug) {
    foreach ($language_data as $lang) {
        if (strtolower($lang['slug']) === $slug) {
            $ordered_data[] = $lang;
            break;
        }
    }
}
// Add any others that might exist
foreach ($language_data as $lang) {
    if (!in_array(strtolower($lang['slug']), $desired_order)) {
        $ordered_data[] = $lang;
    }
}
$language_data = $ordered_data;

// 2. Fetch Real Testimonials (Fixed)
$testimonial_args = array(
    'post_type'      => 'testimonials',
    'posts_per_page' => 10,
    'post_status'    => 'publish'
);
$testimonial_query = new WP_Query($testimonial_args);
$testimonial_items = array();

if ($testimonial_query->have_posts()) {
    while ($testimonial_query->have_posts()) {
        $testimonial_query->the_post();
        
        // In Academist, the author is often a meta field 'eltdf_testimonial_author' or the post_title. 
        // The text is usually the post_title or post_content. Let's grab both.
        $text = wp_strip_all_tags(get_the_title());
        $author = get_post_meta(get_the_ID(), 'eltdf_testimonial_author', true);
        if (empty($author)) {
            $author = "Student";
        }
        
        // Sometimes the text is in the content
        $content = wp_strip_all_tags(get_the_content());
        if (!empty($content) && strlen($content) > 10) {
            $text = $content;
        }

        if (!empty($text)) {
            $testimonial_items[] = array(
                'text' => $text,
                'author' => $author
            );
        }
    }
    wp_reset_postdata();
}

// Fallback if no testimonials found
if (empty($testimonial_items)) {
    $testimonial_items = array(
        array('text' => 'The best language platform I have ever used.', 'author' => 'Sarah T.'),
        array('text' => 'Native tutors helped me pass JLPT N3 easily.', 'author' => 'John D.'),
        array('text' => 'I love the live Zoom integration!', 'author' => 'Maria M.'),
    );
}

// Enqueue specific assets for this page
add_action('wp_enqueue_scripts', function() {
    // Enqueue GSAP
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js', array(), null, true);
    wp_enqueue_script('gsap-scroll', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js', array('gsap'), null, true);
    
    // Enqueue Custom CSS & JS
    wp_enqueue_style('rima-home-css', get_stylesheet_directory_uri() . '/assets/css/rima-home.css', array(), time());
    wp_enqueue_script('rima-home-js', get_stylesheet_directory_uri() . '/assets/js/rima-home.js', array('gsap', 'gsap-scroll', 'jquery'), time(), true);
});


?>

﻿<?php
/**
 * Template Name: Our Courses
 *
 * Premium 2026 course listing page for RIMA Academy.
 * Replaces the shortcode-based approach with a fully custom
 * WP_Query + AJAX filter system.
 *
 * Language: English by default (.rima-en always shown),
 *           Romanian only for users with rima-lang-ro body class.
 *
 * @package Academist Child
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
do_action( 'academist_elated_action_before_main_content' );

// â”€â”€ Fetch all categories for the filter bar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$categories = get_terms( array(
    'taxonomy'   => 'course-category',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );

// â”€â”€ PHP Dictionary for Globe Mapping â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$globe_languages_map = array(
    'english'  => array('isos' => array('GBR', 'USA', 'CAN', 'AUS', 'NZL', 'IRL', 'ZAF'), 'lat' => 51.5072, 'lng' => -0.1276, 'flag' => 'https://flagcdn.com/w320/gb.png', 'flagSmall' => 'https://flagcdn.com/w40/gb.png', 'name' => 'English', 'nativeName' => 'English', 'speakers' => '1.5B+', 'countriesText' => 'United Kingdom Â· United States Â· Canada Â· Australia Â· New Zealand Â· Ireland Â· South Africa'),
    'engleza'  => array('isos' => array('GBR', 'USA', 'CAN', 'AUS', 'NZL', 'IRL', 'ZAF'), 'lat' => 51.5072, 'lng' => -0.1276, 'flag' => 'https://flagcdn.com/w320/gb.png', 'flagSmall' => 'https://flagcdn.com/w40/gb.png', 'name' => 'English', 'nativeName' => 'English', 'speakers' => '1.5B+', 'countriesText' => 'United Kingdom Â· United States Â· Canada Â· Australia Â· New Zealand Â· Ireland Â· South Africa'),

    'romanian' => array('isos' => array('ROU', 'MDA'), 'lat' => 45.9432, 'lng' => 24.9668, 'flag' => 'https://flagcdn.com/w320/ro.png', 'flagSmall' => 'https://flagcdn.com/w40/ro.png', 'name' => 'Romanian', 'nativeName' => 'RomÃ¢nÄƒ', 'speakers' => '26M+', 'countriesText' => 'Romania Â· Republic of Moldova'),
    'romana'   => array('isos' => array('ROU', 'MDA'), 'lat' => 45.9432, 'lng' => 24.9668, 'flag' => 'https://flagcdn.com/w320/ro.png', 'flagSmall' => 'https://flagcdn.com/w40/ro.png', 'name' => 'Romanian', 'nativeName' => 'RomÃ¢nÄƒ', 'speakers' => '26M+', 'countriesText' => 'Romania Â· Republic of Moldova'),

    'japanese' => array('isos' => array('JPN'), 'lat' => 36.2048, 'lng' => 138.2529, 'flag' => 'https://flagcdn.com/w320/jp.png', 'flagSmall' => 'https://flagcdn.com/w40/jp.png', 'name' => 'Japanese', 'nativeName' => 'æ—¥æœ¬èªž', 'speakers' => '125M+', 'countriesText' => 'Japan'),
    'japoneza' => array('isos' => array('JPN'), 'lat' => 36.2048, 'lng' => 138.2529, 'flag' => 'https://flagcdn.com/w320/jp.png', 'flagSmall' => 'https://flagcdn.com/w40/jp.png', 'name' => 'Japanese', 'nativeName' => 'æ—¥æœ¬èªž', 'speakers' => '125M+', 'countriesText' => 'Japan'),

    'spanish'  => array('isos' => array('ESP', 'MEX', 'ARG', 'COL', 'PER', 'VEN', 'CHL', 'CUB', 'DOM'), 'lat' => 40.4637, 'lng' => -3.7492, 'flag' => 'https://flagcdn.com/w320/es.png', 'flagSmall' => 'https://flagcdn.com/w40/es.png', 'name' => 'Spanish', 'nativeName' => 'EspaÃ±ol', 'speakers' => '580M+', 'countriesText' => 'Spain Â· Mexico Â· Argentina Â· Colombia Â· Peru Â· Chile Â· and 14 more'),
    'spaniola' => array('isos' => array('ESP', 'MEX', 'ARG', 'COL', 'PER', 'VEN', 'CHL', 'CUB', 'DOM'), 'lat' => 40.4637, 'lng' => -3.7492, 'flag' => 'https://flagcdn.com/w320/es.png', 'flagSmall' => 'https://flagcdn.com/w40/es.png', 'name' => 'Spanish', 'nativeName' => 'EspaÃ±ol', 'speakers' => '580M+', 'countriesText' => 'Spain Â· Mexico Â· Argentina Â· Colombia Â· Peru Â· Chile Â· and 14 more'),

    'french'   => array('isos' => array('FRA', 'CAN', 'BEL', 'CHE', 'SEN', 'CIV', 'CMR', 'MLI'), 'lat' => 46.2276, 'lng' => 2.2137, 'flag' => 'https://flagcdn.com/w320/fr.png', 'flagSmall' => 'https://flagcdn.com/w40/fr.png', 'name' => 'French', 'nativeName' => 'FranÃ§ais', 'speakers' => '321M+', 'countriesText' => 'France Â· Belgium Â· Switzerland Â· Canada Â· Senegal Â· and 24 more'),
    'franceza' => array('isos' => array('FRA', 'CAN', 'BEL', 'CHE', 'SEN', 'CIV', 'CMR', 'MLI'), 'lat' => 46.2276, 'lng' => 2.2137, 'flag' => 'https://flagcdn.com/w320/fr.png', 'flagSmall' => 'https://flagcdn.com/w40/fr.png', 'name' => 'French', 'nativeName' => 'FranÃ§ais', 'speakers' => '321M+', 'countriesText' => 'France Â· Belgium Â· Switzerland Â· Canada Â· Senegal Â· and 24 more'),

    'german'   => array('isos' => array('DEU', 'AUT', 'CHE'), 'lat' => 51.1657, 'lng' => 10.4515, 'flag' => 'https://flagcdn.com/w320/de.png', 'flagSmall' => 'https://flagcdn.com/w40/de.png', 'name' => 'German', 'nativeName' => 'Deutsch', 'speakers' => '130M+', 'countriesText' => 'Germany Â· Austria Â· Switzerland'),
    'germana'  => array('isos' => array('DEU', 'AUT', 'CHE'), 'lat' => 51.1657, 'lng' => 10.4515, 'flag' => 'https://flagcdn.com/w320/de.png', 'flagSmall' => 'https://flagcdn.com/w40/de.png', 'name' => 'German', 'nativeName' => 'Deutsch', 'speakers' => '130M+', 'countriesText' => 'Germany Â· Austria Â· Switzerland'),

    'italian'  => array('isos' => array('ITA', 'CHE', 'SMR'), 'lat' => 41.8719, 'lng' => 12.5674, 'flag' => 'https://flagcdn.com/w320/it.png', 'flagSmall' => 'https://flagcdn.com/w40/it.png', 'name' => 'Italian', 'nativeName' => 'Italiano', 'speakers' => '85M+', 'countriesText' => 'Italy Â· Switzerland Â· San Marino'),
    'italiana' => array('isos' => array('ITA', 'CHE', 'SMR'), 'lat' => 41.8719, 'lng' => 12.5674, 'flag' => 'https://flagcdn.com/w320/it.png', 'flagSmall' => 'https://flagcdn.com/w40/it.png', 'name' => 'Italian', 'nativeName' => 'Italiano', 'speakers' => '85M+', 'countriesText' => 'Italy Â· Switzerland Â· San Marino'),

    'chinese'  => array('isos' => array('CHN', 'TWN', 'SGP'), 'lat' => 35.8617, 'lng' => 104.1954, 'flag' => 'https://flagcdn.com/w320/cn.png', 'flagSmall' => 'https://flagcdn.com/w40/cn.png', 'name' => 'Chinese', 'nativeName' => 'ä¸­æ–‡', 'speakers' => '1.1B+', 'countriesText' => 'China Â· Taiwan Â· Singapore'),
    'chineza'  => array('isos' => array('CHN', 'TWN', 'SGP'), 'lat' => 35.8617, 'lng' => 104.1954, 'flag' => 'https://flagcdn.com/w320/cn.png', 'flagSmall' => 'https://flagcdn.com/w40/cn.png', 'name' => 'Chinese', 'nativeName' => 'ä¸­æ–‡', 'speakers' => '1.1B+', 'countriesText' => 'China Â· Taiwan Â· Singapore'),
);

$active_globe_langs = array();
if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
    foreach ( $categories as $cat ) {
        if ( isset( $globe_languages_map[ $cat->slug ] ) ) {
            $lang_data = $globe_languages_map[ $cat->slug ];
            $lang_data['slug'] = $cat->slug;
            $active_globe_langs[] = $lang_data;
        }
    }
}

// â”€â”€ Initial course query (9 per page) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$paged       = max( 1, get_query_var( 'paged' ) );
$per_page    = 9;
$query_args  = array(
    'post_type'      => 'course',
    'post_status'    => 'publish',
    'posts_per_page' => $per_page,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
);
$courses_query = new WP_Query( $query_args );
$total_courses = (int) wp_count_posts( 'course' )->publish;

// â”€â”€ REAL STATS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

/**
 * Real student count:
 * Count unique users who have enrolled (eltdf_user_courses user meta).
 * Falls back to counting all completed WC orders that contain LMS products.
 */
$real_student_count = 0;

// Method 1: count users who have at least one enrolled course
$enrolled_users = get_users( array(
    'meta_key'     => 'eltdf_user_courses',
    'meta_compare' => 'EXISTS',
    'fields'       => 'ID',
    'number'       => -1,
) );
$real_student_count = count( $enrolled_users );

// Fallback: if no LMS meta found, count WC customers with completed orders
if ( $real_student_count === 0 && function_exists( 'wc_get_orders' ) ) {
    $orders = wc_get_orders( array(
        'status' => array( 'wc-completed', 'wc-processing' ),
        'limit'  => -1,
        'return' => 'ids',
    ) );
    $real_student_count = count( $orders );
}

// Friendly display: round down to nearest 10, add "+"
$display_students = $real_student_count > 0
    ? ( floor( $real_student_count / 10 ) * 10 ) . '+'
    : '0+';

/**
 * Real average rating:
 * Pull the WooCommerce product IDs linked to each course via
 * eltdf_course_woo_product_meta and average their star ratings.
 */
$all_course_ids = get_posts( array(
    'post_type'      => 'course',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'fields'         => 'ids',
) );

$rating_sum   = 0;
$rating_count = 0;

foreach ( $all_course_ids as $cid ) {
    $product_id = get_post_meta( $cid, 'eltdf_course_woo_product_meta', true );
    if ( $product_id ) {
        $rating = get_post_meta( $product_id, '_wc_average_rating', true );
        if ( $rating && (float) $rating > 0 ) {
            $rating_sum   += (float) $rating;
            $rating_count++;
        }
    }
}

// If no WC product ratings exist yet, show a placeholder of 5.0
$display_rating = $rating_count > 0
    ? number_format( $rating_sum / $rating_count, 1 )
    : '5.0';

?>


<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     HERO SECTION
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<section class="roc-hero" id="roc-hero-section" aria-label="Our Courses">
    <div class="roc-hero-bg-wrapper" id="roc-parallax-wrapper">
        <div id="roc-globe-viz"></div>
    </div>
    <div class="roc-hero-glass-panel">
        <div class="roc-hero-inner">
            <div class="roc-hero-badge">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <span class="rima-en">RIMA Academy</span><span class="rima-ro">RIMA Academy</span>
            </div>
            <h1 class="roc-hero-title">Transform Your Skills</h1>
            <p class="roc-hero-subtitle">
                Discover <?php echo esc_html( $total_courses ); ?> expert-crafted courses designed to accelerate your professional growth.
            </p>
            <div class="roc-hero-stats">
                <div class="roc-stat">
                    <span class="roc-stat-number"><?php echo esc_html( $total_courses ); ?>+</span>
                    <span class="roc-stat-label">Courses</span>
                </div>
                <div class="roc-stat-divider"></div>
                <div class="roc-stat">
                    <span class="roc-stat-number"><?php echo esc_html( $display_rating ); ?>â˜…</span>
                    <span class="roc-stat-label">Rating</span>
                </div>
            </div>

            <!-- Dynamic Flags UI -->
            <div class="roc-globe-languages">
                <?php foreach ( $active_globe_langs as $lang ) : ?>
                    <button class="roc-lang-flag-btn" data-lang-slug="<?php echo esc_attr( $lang['slug'] ); ?>" data-lat="<?php echo esc_attr( $lang['lat'] ); ?>" data-lng="<?php echo esc_attr( $lang['lng'] ); ?>" title="<?php echo esc_attr( $lang['name'] ); ?>">
                        <img src="<?php echo esc_url( $lang['flag'] ); ?>" alt="<?php echo esc_attr( $lang['name'] ); ?>" />
                        <span class="roc-lang-name"><?php echo esc_html( $lang['name'] ); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     INTERACTIVE LANGUAGE SHOWCASE REEL (replaces courses.mp4)
     Dynamic â€” reads from $active_globe_langs (auto-updates with new categories)
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<?php if ( ! empty( $active_globe_langs ) ) : ?>
<section class="roc-lang-reel" id="roc-lang-reel" aria-label="Language Showcase">
    <div class="roc-reel-canvas">
        <!-- Particle canvas background -->
        <canvas id="roc-reel-particles"></canvas>

        <!-- Slides container -->
        <div class="roc-reel-slides" id="roc-reel-slides">
            <?php foreach ( $active_globe_langs as $idx => $rlang ) : ?>
            <div class="roc-reel-slide <?php echo $idx === 0 ? 'roc-reel-slide-active' : ''; ?>"
                 data-slug="<?php echo esc_attr( $rlang['slug'] ); ?>"
                 data-lat="<?php echo esc_attr( $rlang['lat'] ); ?>"
                 data-lng="<?php echo esc_attr( $rlang['lng'] ); ?>">
                <!-- Flag Visual -->
                <div class="roc-reel-flag-wrap">
                    <div class="roc-reel-glow"></div>
                    <img class="roc-reel-flag" src="<?php echo esc_url( $rlang['flag'] ); ?>" alt="<?php echo esc_attr( $rlang['name'] ); ?>" loading="lazy" />
                </div>
                <!-- Info Panel -->
                <div class="roc-reel-info">
                    <span class="roc-reel-label">Language Course</span>
                    <h2 class="roc-reel-native"><?php echo esc_html( $rlang['nativeName'] ?? $rlang['name'] ); ?></h2>
                    <h3 class="roc-reel-name"><?php echo esc_html( $rlang['name'] ); ?></h3>
                    <p class="roc-reel-countries">Spoken as a first language in<br><strong><?php echo esc_html( $rlang['countriesText'] ?? '' ); ?></strong></p>
                    <div class="roc-reel-stat">
                        <span class="roc-reel-stat-num"><?php echo esc_html( $rlang['speakers'] ?? 'â€”' ); ?></span>
                        <span class="roc-reel-stat-lbl">Speakers Worldwide</span>
                    </div>
                    <button class="roc-reel-cta" data-slug="<?php echo esc_attr( $rlang['slug'] ); ?>">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        View Courses
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Controls overlay -->
        <div class="roc-reel-controls">
            <button class="roc-reel-btn roc-reel-prev" id="roc-reel-prev" aria-label="Previous language">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <div class="roc-reel-dots" id="roc-reel-dots">
                <?php foreach ( $active_globe_langs as $di => $dl ) : ?>
                <button class="roc-reel-dot <?php echo $di === 0 ? 'roc-reel-dot-active' : ''; ?>"
                        data-index="<?php echo $di; ?>"
                        aria-label="<?php echo esc_attr( $dl['name'] ); ?>">
                    <img src="<?php echo esc_url( $dl['flagSmall'] ?? $dl['flag'] ); ?>" alt="" />
                </button>
                <?php endforeach; ?>
            </div>
            <button class="roc-reel-btn roc-reel-next" id="roc-reel-next" aria-label="Next language">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
            </button>
        </div>

        <!-- Progress bar -->
        <div class="roc-reel-progress">
            <div class="roc-reel-progress-fill" id="roc-reel-progress-fill"></div>
        </div>

        <!-- Play/Pause -->
        <button class="roc-reel-playpause" id="roc-reel-playpause" aria-label="Pause autoplay">
            <svg class="roc-reel-icon-pause" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/></svg>
            <svg class="roc-reel-icon-play" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="display:none"><polygon points="5,3 19,12 5,21"/></svg>
        </button>
    </div>
</section>
<?php endif; ?>

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     FILTER BAR
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<div class="roc-filter-bar" id="roc-filter-bar" role="search" aria-label="Course filters">
    <div class="roc-filter-inner">

        <!-- Category pills -->
        <div class="roc-filter-pills" role="tablist" aria-label="Filter by category">
            <button class="roc-pill roc-pill-active"
                    role="tab"
                    aria-selected="true"
                    data-cat=""
                    id="roc-pill-all">
                All Courses
            </button>
            <?php if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) : ?>
                <?php foreach ( $categories as $cat ) : ?>
                    <button class="roc-pill"
                            role="tab"
                            aria-selected="false"
                            data-cat="<?php echo esc_attr( $cat->slug ); ?>"
                            id="roc-pill-<?php echo esc_attr( $cat->slug ); ?>">
                        <?php echo esc_html( $cat->name ); ?>
                        <span class="roc-pill-count"><?php echo esc_html( $cat->count ); ?></span>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Search + Sort + View Toggle -->
        <div class="roc-filter-controls">
            <div class="roc-search-wrap">
                <svg class="roc-search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="search"
                       id="roc-search"
                       class="roc-search-input"
                       placeholder="<?php esc_attr_e( 'Search courses...', 'rima-academy' ); ?>"
                       aria-label="Search courses"
                       autocomplete="off" />
            </div>

            <div class="roc-sort-wrap">
                <select id="roc-sort" class="roc-sort-select" aria-label="Sort courses">
                    <option value="newest"><span class="rima-en">Newest</span></option>
                    <option value="price_asc"><span class="rima-en">Price: Low â†’ High</span></option>
                    <option value="price_desc"><span class="rima-en">Price: High â†’ Low</span></option>
                    <option value="popular"><span class="rima-en">Most Popular</span></option>
                </select>
            </div>

            <div class="roc-view-toggle" role="group" aria-label="View mode">
                <button class="roc-view-btn roc-view-grid active" id="roc-view-grid" aria-pressed="true" title="Grid view">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                </button>
                <button class="roc-view-btn roc-view-list" id="roc-view-list" aria-pressed="false" title="List view">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="4" width="18" height="2" rx="1"/><rect x="3" y="11" width="18" height="2" rx="1"/><rect x="3" y="18" width="18" height="2" rx="1"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Results count -->
    <div class="roc-results-bar">
        <span class="roc-results-count" id="roc-results-count" aria-live="polite">
            <?php
            printf(
                'Showing <strong>%1$d</strong> of <strong>%2$d</strong> courses',
                min( $per_page, $courses_query->found_posts ),
                $courses_query->found_posts
            );
            ?>
        </span>
    </div>
</div>

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     COURSE GRID
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->

<div class="rhm-courses" id="courses" style="padding-top: 50px;">
    <div class="rhm-container">
        
            <div class="rhm-flip-grid">
                <?php if(!empty($language_data)): foreach($language_data as $lang): ?>
                
                <div class="rhm-flip-container">
                    <div class="rhm-flip-inner">
                        
                        <!-- FRONT OF CARD (Language) -->
                        <div class="rhm-flip-front" style="background-image: url('<?php echo esc_url($lang['img']); ?>');">
                            <div class="rhm-flip-front-overlay"></div>
                            
                            <?php 
                                $slug = strtolower($lang['slug']);
                                $stampClass = 'rhm-stamp';
                                $stampText = '';
                                if (in_array($slug, array('romanian', 'romana'))) {
                                    $stampText = '⭐ For Foreigners';
                                    $stampClass .= ' rhm-stamp-romanian';
                                } elseif (in_array($slug, array('english'))) {
                                    $stampText = 'Most Popular';
                                    $stampClass .= ' rhm-stamp-blue';
                                } elseif (in_array($slug, array('japanese'))) {
                                    $stampText = 'Trending';
                                    $stampClass .= ' rhm-stamp-purple';
                                }
                            ?>
                            <?php if ($stampText): ?>
                            <div class="<?php echo esc_attr($stampClass); ?>"><?php echo esc_html($stampText); ?></div>
                            <?php endif; ?>

                            <div class="rhm-flip-front-content">
                                <h3><?php echo esc_html($lang['name']); ?></h3>
                                <div class="rhm-flip-hint-wrapper">
                                    <button class="rhm-flip-open" aria-label="View Courses">
                                        <span>View Courses</span>
                                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- BACK OF CARD (Courses) -->
                        <div class="rhm-flip-back" style="background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(2, 6, 23, 0.95) 100%), url('<?php echo esc_url($lang['img']); ?>'); background-size: cover; background-position: center;">
                            <div class="rhm-flip-back-header">
                                <h3>
                                    <?php echo esc_html($lang['name']); ?> Courses
                                    <?php if ($stampText): ?>
                                        <div class="<?php echo esc_attr($stampClass); ?> rhm-stamp-back"><?php echo esc_html($stampText); ?></div>
                                    <?php endif; ?>
                                </h3>
                                <button class="rhm-flip-close" aria-label="Close" title="Back to Languages">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            
                            <div class="rhm-flip-course-list">
                                <?php foreach($lang['courses'] as $course): ?>
                                <a href="<?php echo esc_url($course['link']); ?>" class="rhm-course-list-card" onclick="window.location.href='<?php echo esc_url($course['link']); ?>'; return false;">
                                    <div class="rhm-clc-left">
                                        <div class="rhm-clc-level"><?php echo esc_html($course['level']); ?></div>
                                        <div class="rhm-clc-details">
                                            <p class="rhm-clc-excerpt"><?php echo esc_html($course['excerpt']); ?></p>
                                        </div>
                                    </div>
                                    <div class="rhm-clc-right">
                                        <div class="rhm-clc-price"><?php echo wp_kses_post($course['price_html']); ?></div>
                                        <div class="rhm-btn-view">View</div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="rhm-flip-back-footer">
                                <a href="/courses/" class="rhm-flip-view-all">View all <?php echo esc_html($lang['name']); ?> courses &rarr;</a>
                            </div>
                        </div>

                    </div>
                </div>
                
                <?php endforeach; endif; ?>
            </div>

        </div>
    </div>
</div>
    </div>
</div>
<script src="https://unpkg.com/globe.gl"></script>
<?php
// Pass dynamic languages to JS
$json_globe_langs = json_encode( $active_globe_langs );
?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const globeContainer = document.getElementById('roc-globe-viz');
    if (globeContainer && typeof Globe !== 'undefined') {
        
        const activeLangs = <?php echo $json_globe_langs; ?>;
        
        // Prepare marker data
        const markerData = activeLangs.map(lang => ({
            lat: lang.lat,
            lng: lang.lng,
            flag: lang.flagSmall || lang.flag,
            label: lang.name,
            slug: lang.slug,
            isos: lang.isos || []
        }));

        // We can draw an arc from the first lang (e.g. English) to all others for visual flair
        const arcsData = [];
        if (markerData.length > 1) {
            const origin = markerData[0];
            for (let i = 1; i < markerData.length; i++) {
                arcsData.push({
                    startLat: origin.lat, startLng: origin.lng,
                    endLat: markerData[i].lat, endLng: markerData[i].lng
                });
            }
        }

        const globe = Globe()(globeContainer)
            .globeImageUrl('https://unpkg.com/three-globe/example/img/earth-night.jpg')
            .backgroundColor('rgba(0,0,0,0)')
            .width(globeContainer.clientWidth)
            .height(globeContainer.clientHeight)
            // Polygons for highlighting countries
            .polygonCapColor(feat => {
                const iso = feat.properties.ISO_A3;
                return markerData.find(m => m.isos.includes(iso)) ? 'rgba(0, 229, 255, 0.4)' : 'rgba(0,0,0,0)';
            })
            .polygonSideColor(() => 'rgba(0,0,0,0)')
            .polygonStrokeColor(feat => {
                const iso = feat.properties.ISO_A3;
                return markerData.find(m => m.isos.includes(iso)) ? 'rgba(0, 229, 255, 1)' : 'rgba(255,255,255,0.05)';
            })
            .polygonAltitude(0.01)
            // HTML Markers
            .htmlElementsData(markerData)
            .htmlElement(d => {
                const el = document.createElement('div');
                el.innerHTML = `
                    <div class="roc-glass-marker" data-slug="${d.slug}" data-lat="${d.lat}" data-lng="${d.lng}">
                        <img src="${d.flag}" alt="flag" />
                        <span class="lang-text">${d.label}</span>
                    </div>
                `;
                el.style.pointerEvents = 'auto';
                
                // Click on marker to filter and zoom
                el.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (window.triggerFilterAndZoom) window.triggerFilterAndZoom(d.slug, d.lat, d.lng);
                });
                return el;
            })
            .arcsData(arcsData)
            .arcColor(() => 'rgba(0, 229, 255, 0.8)')
            .arcDashLength(0.4)
            .arcDashGap(0.2)
            .arcDashAnimateTime(1500);

        // Global trigger function to connect the Globe and Flags with the Filter UI
        window.triggerFilterAndZoom = function(slug, lat, lng) {
            // 1. Zoom the globe (using a higher altitude so multiple countries are visible)
            if (globe) {
                globe.pointOfView({ lat: lat, lng: lng, altitude: 2.0 }, 1500);
            }
            // 2. Trigger the AJAX filter by simulating a click on the category pill
            const targetPill = document.querySelector(`.roc-pill[data-cat="${slug}"]`);
            if (targetPill) {
                targetPill.click();
            }
            // 3. Scroll down smoothly so the user sees the courses
            const gridSection = document.getElementById('roc-filter-bar');
            if (gridSection) {
                gridSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        };

        // Attach click listeners to the UI flags under the hero text
        document.querySelectorAll('.roc-lang-flag-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const slug = btn.getAttribute('data-lang-slug');
                const lat = parseFloat(btn.getAttribute('data-lat'));
                const lng = parseFloat(btn.getAttribute('data-lng'));
                window.triggerFilterAndZoom(slug, lat, lng);
            });
        });

        // Fetch GeoJSON data for the world atlas to draw country polygons
        fetch('https://raw.githubusercontent.com/vasturiano/globe.gl/master/example/datasets/ne_110m_admin_0_countries.geojson')
            .then(res => res.json())
            .then(countries => {
                globe.polygonsData(countries.features);
            });

        // Setup auto-rotation
        globe.controls().autoRotate = true;
        globe.controls().autoRotateSpeed = 0.8;
        globe.controls().enableZoom = true; // Enable user zoom so they can see countries up close

        // Focus point to see Europe and Asia
        globe.pointOfView({ lat: 45, lng: 60, altitude: 2.2 }, 1000);

        // Handle resize
        window.addEventListener('resize', () => {
            if (globeContainer.clientWidth > 0) {
                globe.width(globeContainer.clientWidth);
                globe.height(globeContainer.clientHeight);
            }
        });
    }
});
</script>

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     INTERACTIVE REEL JS
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const reelSection = document.getElementById('roc-lang-reel');
    if (!reelSection) return;

    // â”€â”€ Particle Background â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    const canvas = document.getElementById('roc-reel-particles');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        function resizeCanvas() {
            canvas.width = canvas.parentElement.clientWidth;
            canvas.height = canvas.parentElement.clientHeight;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        const particles = [];
        for (let i = 0; i < 80; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * 0.3,
                vy: (Math.random() - 0.5) * 0.3,
                r: Math.random() * 1.5 + 0.5,
                alpha: Math.random() * 0.25 + 0.05
            });
        }

        function drawParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 120) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = `rgba(0,229,255,${0.06 * (1 - dist / 120)})`;
                        ctx.lineWidth = 0.5;
                        ctx.stroke();
                    }
                }
            }
            particles.forEach(p => {
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(0,229,255,${p.alpha})`;
                ctx.fill();
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
                if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
            });
            requestAnimationFrame(drawParticles);
        }
        drawParticles();
    }

    // â”€â”€ Slide Controller â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    const slides = reelSection.querySelectorAll('.roc-reel-slide');
    const dots = reelSection.querySelectorAll('.roc-reel-dot');
    const progressFill = document.getElementById('roc-reel-progress-fill');
    const prevBtn = document.getElementById('roc-reel-prev');
    const nextBtn = document.getElementById('roc-reel-next');
    const playPauseBtn = document.getElementById('roc-reel-playpause');
    const pauseIcon = reelSection.querySelector('.roc-reel-icon-pause');
    const playIcon = reelSection.querySelector('.roc-reel-icon-play');

    let currentSlide = 0;
    let isPlaying = true;
    const SLIDE_DURATION = 5000; // 5 seconds per slide
    let progressStart = Date.now();
    let autoTimer = null;

    function goToSlide(index) {
        slides.forEach(s => s.classList.remove('roc-reel-slide-active'));
        dots.forEach(d => d.classList.remove('roc-reel-dot-active'));
        currentSlide = index;
        slides[currentSlide].classList.add('roc-reel-slide-active');
        dots[currentSlide].classList.add('roc-reel-dot-active');
        // Re-trigger animation
        slides[currentSlide].style.animation = 'none';
        slides[currentSlide].offsetHeight;
        slides[currentSlide].style.animation = '';
        progressStart = Date.now();
    }

    function nextSlide() {
        goToSlide((currentSlide + 1) % slides.length);
    }

    function prevSlide() {
        goToSlide((currentSlide - 1 + slides.length) % slides.length);
    }

    function startAutoplay() {
        stopAutoplay();
        isPlaying = true;
        if (pauseIcon) pauseIcon.style.display = '';
        if (playIcon) playIcon.style.display = 'none';
        progressStart = Date.now();
        autoTimer = setInterval(() => {
            nextSlide();
        }, SLIDE_DURATION);
    }

    function stopAutoplay() {
        isPlaying = false;
        if (pauseIcon) pauseIcon.style.display = 'none';
        if (playIcon) playIcon.style.display = '';
        if (autoTimer) clearInterval(autoTimer);
        autoTimer = null;
    }

    // Progress bar animation
    function updateProgress() {
        if (isPlaying && progressFill) {
            const elapsed = Date.now() - progressStart;
            const pct = Math.min((elapsed / SLIDE_DURATION) * 100, 100);
            progressFill.style.width = pct + '%';
        }
        requestAnimationFrame(updateProgress);
    }
    updateProgress();

    // Event listeners
    if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); if (isPlaying) startAutoplay(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); if (isPlaying) startAutoplay(); });
    if (playPauseBtn) playPauseBtn.addEventListener('click', () => { isPlaying ? stopAutoplay() : startAutoplay(); });

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            const idx = parseInt(dot.dataset.index);
            goToSlide(idx);
            if (isPlaying) startAutoplay();
        });
    });

    // CTA button â€” filter courses and zoom globe
    reelSection.querySelectorAll('.roc-reel-cta').forEach(btn => {
        btn.addEventListener('click', () => {
            const slug = btn.dataset.slug;
            const slide = btn.closest('.roc-reel-slide');
            const lat = parseFloat(slide.dataset.lat);
            const lng = parseFloat(slide.dataset.lng);
            if (window.triggerFilterAndZoom) {
                window.triggerFilterAndZoom(slug, lat, lng);
            }
        });
    });

    // Start!
    startAutoplay();

    // Touch swipe support
    let touchStartX = 0;
    reelSection.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
    reelSection.addEventListener('touchend', e => {
        const diff = e.changedTouches[0].screenX - touchStartX;
        if (Math.abs(diff) > 50) {
            diff > 0 ? prevSlide() : nextSlide();
            if (isPlaying) startAutoplay();
        }
    }, { passive: true });
});
</script>


<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.rhm-flip-open').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const container = btn.closest('.rhm-flip-container');
            document.querySelectorAll('.rhm-flip-container.is-flipped').forEach(c => {
                if (c !== container) c.classList.remove('is-flipped');
            });
            container.classList.add('is-flipped');
        });
    });
    document.querySelectorAll('.rhm-flip-close').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const container = btn.closest('.rhm-flip-container');
            container.classList.remove('is-flipped');
        });
    });
});
</script>

<?php get_footer(); ?>
<!-- trigger sync -->