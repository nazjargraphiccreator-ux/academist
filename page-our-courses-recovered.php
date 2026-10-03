<?php
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
<div class="roc-page-container">
<div class="roc-page-wrap">
    <div class="roc-grid-wrap roc-view-grid-mode" id="roc-grid" role="main" aria-label="Course listings">

        <?php if ( $courses_query->have_posts() ) : ?>
            <?php while ( $courses_query->have_posts() ) : $courses_query->the_post(); ?>
                <?php
                $c_id          = get_the_ID();
                $c_cats        = get_the_terms( $c_id, 'course-category' );
                $c_cat_name    = ( ! is_wp_error( $c_cats ) && ! empty( $c_cats ) ) ? $c_cats[0]->name : '';
                $c_cat_slug    = ( ! is_wp_error( $c_cats ) && ! empty( $c_cats ) ) ? $c_cats[0]->slug : '';
                $instructor_id = get_post_meta( $c_id, 'eltdf_course_instructor_meta', true );
                $instr_name    = $instructor_id ? get_the_title( $instructor_id ) : '';
                $price         = function_exists( 'academist_lms_calculate_course_price' ) ? academist_lms_calculate_course_price( $c_id ) : 0;
                $thumb_url     = get_the_post_thumbnail_url( $c_id, 'large' );
                $lesson_count  = get_post_meta( $c_id, 'eltdf_course_lessons_count', true );
                $duration      = get_post_meta( $c_id, 'eltdf_course_duration', true );
                ?>
                <article class="roc-card" data-category="<?php echo esc_attr( $c_cat_slug ); ?>" itemscope itemtype="https://schema.org/Course">
                    <a href="<?php the_permalink(); ?>" class="roc-card-image-link" tabindex="-1" aria-hidden="true">
                        <div class="roc-card-image">
                            <?php if ( $thumb_url ) : ?>
                                <img src="<?php echo esc_url( $thumb_url ); ?>"
                                     alt="<?php echo esc_attr( get_the_title() ); ?>"
                                     loading="lazy"
                                     itemprop="image" />
                            <?php else : ?>
                                <div class="roc-card-image-placeholder">
                                    <svg width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                </div>
                            <?php endif; ?>

                            <?php if ( $c_cat_name ) : ?>
                                <span class="roc-card-badge-cat"><?php echo esc_html( $c_cat_name ); ?></span>
                            <?php endif; ?>

                            <?php if ( $price > 0 ) : ?>
                                <span class="roc-card-badge-price">
                                    <?php
                                    if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
                                        $pos = get_option( 'woocommerce_currency_pos', 'right' );
                                        $sym = get_woocommerce_currency_symbol();
                                        echo $pos === 'left' ? esc_html( $sym . $price ) : esc_html( $price . ' ' . $sym );
                                    } else {
                                        echo esc_html( $price );
                                    }
                                    ?>
                                </span>
                            <?php else : ?>
                                <span class="roc-card-badge-price roc-badge-free">
                                    <span class="rima-en">Free</span><span class="rima-ro">Gratuit</span>
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>

                    <div class="roc-card-body">
                        <h2 class="roc-card-title" itemprop="name">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>

                        <?php if ( $instr_name ) : ?>
                            <div class="roc-card-instructor">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <span><?php echo esc_html( $instr_name ); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="roc-card-excerpt">
                            <?php
                            if ( has_excerpt() ) {
                                echo wp_trim_words( get_the_excerpt(), 18 );
                            } else {
                                echo wp_trim_words( get_the_content(), 18 );
                            }
                            ?>
                        </div>

                        <!-- List-view meta row (hidden in grid mode) -->
                        <div class="roc-card-meta-row">
                            <?php if ( $lesson_count ) : ?>
                                <span class="roc-meta-item">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/></svg>
                                    <?php echo esc_html( $lesson_count ); ?>
                                    <span class="rima-en"> lessons</span><span class="rima-ro"> lecÈ›ii</span>
                                </span>
                            <?php endif; ?>
                            <?php if ( $duration ) : ?>
                                <span class="roc-meta-item">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <?php echo esc_html( $duration ); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <a href="<?php the_permalink(); ?>"
                           class="roc-card-cta eltdf-btn eltdf-btn-solid rima-btn-blue"
                           itemprop="url">
                            <span class="rima-en">View Course</span>
                            <span class="rima-ro">Vezi Cursul</span>
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>

        <!-- Skeleton cards (shown during AJAX load) -->
        <?php for ( $i = 0; $i < 3; $i++ ) : ?>
            <div class="roc-card roc-skeleton" aria-hidden="true">
                <div class="roc-skeleton-image"></div>
                <div class="roc-card-body">
                    <div class="roc-skeleton-line roc-skeleton-title"></div>
                    <div class="roc-skeleton-line roc-skeleton-sub"></div>
                    <div class="roc-skeleton-line roc-skeleton-text"></div>
                    <div class="roc-skeleton-line roc-skeleton-text roc-skeleton-short"></div>
                    <div class="roc-skeleton-line roc-skeleton-btn"></div>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <!-- â”€â”€ Empty State â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <div class="roc-empty-state" id="roc-empty-state" aria-live="polite" style="display:none;">
            <button class="eltdf-btn eltdf-btn-solid rima-btn-blue" id="roc-reset-filters">
            Clear Filters
        </button>
    </div>

    <!-- â”€â”€ Load More â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <?php if ( $courses_query->max_num_pages > 1 ) : ?>
        <div class="roc-load-more-wrap" id="roc-load-more-wrap">
            <button class="roc-load-more-btn" id="roc-load-more"
                    data-page="1"
                    data-max-pages="<?php echo esc_attr( $courses_query->max_num_pages ); ?>"
                    aria-label="Load more courses">
                <span class="roc-load-more-text">Load More Courses</span>
                <span class="roc-load-more-spinner" aria-hidden="true"></span>
            </button>
        </div>
    <?php endif; ?>
</div>
</div> <!-- /.roc-page-container -->

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

<?php get_footer(); ?>

