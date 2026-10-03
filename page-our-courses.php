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

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Fetch all categories for the filter bar ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
$categories = get_terms( array(
    'taxonomy'   => 'course-category',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ PHP Dictionary for Globe Mapping ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
$globe_languages_map = array(
    'english'  => array('isos' => array('GBR', 'USA', 'CAN', 'AUS', 'NZL', 'IRL', 'ZAF'), 'lat' => 51.5072, 'lng' => -0.1276, 'flag' => 'https://flagcdn.com/w320/gb.png', 'flagSmall' => 'https://flagcdn.com/w40/gb.png', 'name' => 'English', 'nativeName' => 'English', 'speakers' => '1.5B+', 'countriesText' => 'United Kingdom Ã‚Â· United States Ã‚Â· Canada Ã‚Â· Australia Ã‚Â· New Zealand Ã‚Â· Ireland Ã‚Â· South Africa'),
    'engleza'  => array('isos' => array('GBR', 'USA', 'CAN', 'AUS', 'NZL', 'IRL', 'ZAF'), 'lat' => 51.5072, 'lng' => -0.1276, 'flag' => 'https://flagcdn.com/w320/gb.png', 'flagSmall' => 'https://flagcdn.com/w40/gb.png', 'name' => 'English', 'nativeName' => 'English', 'speakers' => '1.5B+', 'countriesText' => 'United Kingdom Ã‚Â· United States Ã‚Â· Canada Ã‚Â· Australia Ã‚Â· New Zealand Ã‚Â· Ireland Ã‚Â· South Africa'),

    'romanian' => array('isos' => array('ROU', 'MDA'), 'lat' => 45.9432, 'lng' => 24.9668, 'flag' => 'https://flagcdn.com/w320/ro.png', 'flagSmall' => 'https://flagcdn.com/w40/ro.png', 'name' => 'Romanian', 'nativeName' => 'RomÃƒÂ¢nÃ„Æ’', 'speakers' => '26M+', 'countriesText' => 'Romania Ã‚Â· Republic of Moldova'),
    'romana'   => array('isos' => array('ROU', 'MDA'), 'lat' => 45.9432, 'lng' => 24.9668, 'flag' => 'https://flagcdn.com/w320/ro.png', 'flagSmall' => 'https://flagcdn.com/w40/ro.png', 'name' => 'Romanian', 'nativeName' => 'RomÃƒÂ¢nÃ„Æ’', 'speakers' => '26M+', 'countriesText' => 'Romania Ã‚Â· Republic of Moldova'),

    'japanese' => array('isos' => array('JPN'), 'lat' => 36.2048, 'lng' => 138.2529, 'flag' => 'https://flagcdn.com/w320/jp.png', 'flagSmall' => 'https://flagcdn.com/w40/jp.png', 'name' => 'Japanese', 'nativeName' => 'ÃƒÂ¦Ã¢â‚¬â€Ã‚Â¥ÃƒÂ¦Ã…â€œÃ‚Â¬ÃƒÂ¨Ã‚ÂªÃ…Â¾', 'speakers' => '125M+', 'countriesText' => 'Japan'),
    'japoneza' => array('isos' => array('JPN'), 'lat' => 36.2048, 'lng' => 138.2529, 'flag' => 'https://flagcdn.com/w320/jp.png', 'flagSmall' => 'https://flagcdn.com/w40/jp.png', 'name' => 'Japanese', 'nativeName' => 'ÃƒÂ¦Ã¢â‚¬â€Ã‚Â¥ÃƒÂ¦Ã…â€œÃ‚Â¬ÃƒÂ¨Ã‚ÂªÃ…Â¾', 'speakers' => '125M+', 'countriesText' => 'Japan'),

    'spanish'  => array('isos' => array('ESP', 'MEX', 'ARG', 'COL', 'PER', 'VEN', 'CHL', 'CUB', 'DOM'), 'lat' => 40.4637, 'lng' => -3.7492, 'flag' => 'https://flagcdn.com/w320/es.png', 'flagSmall' => 'https://flagcdn.com/w40/es.png', 'name' => 'Spanish', 'nativeName' => 'EspaÃƒÆ’Ã‚Â±ol', 'speakers' => '580M+', 'countriesText' => 'Spain Ã‚Â· Mexico Ã‚Â· Argentina Ã‚Â· Colombia Ã‚Â· Peru Ã‚Â· Chile Ã‚Â· and 14 more'),
    'spaniola' => array('isos' => array('ESP', 'MEX', 'ARG', 'COL', 'PER', 'VEN', 'CHL', 'CUB', 'DOM'), 'lat' => 40.4637, 'lng' => -3.7492, 'flag' => 'https://flagcdn.com/w320/es.png', 'flagSmall' => 'https://flagcdn.com/w40/es.png', 'name' => 'Spanish', 'nativeName' => 'EspaÃƒÆ’Ã‚Â±ol', 'speakers' => '580M+', 'countriesText' => 'Spain Ã‚Â· Mexico Ã‚Â· Argentina Ã‚Â· Colombia Ã‚Â· Peru Ã‚Â· Chile Ã‚Â· and 14 more'),

    'french'   => array('isos' => array('FRA', 'CAN', 'BEL', 'CHE', 'SEN', 'CIV', 'CMR', 'MLI'), 'lat' => 46.2276, 'lng' => 2.2137, 'flag' => 'https://flagcdn.com/w320/fr.png', 'flagSmall' => 'https://flagcdn.com/w40/fr.png', 'name' => 'French', 'nativeName' => 'FranÃƒÆ’Ã‚Â§ais', 'speakers' => '321M+', 'countriesText' => 'France Ã‚Â· Belgium Ã‚Â· Switzerland Ã‚Â· Canada Ã‚Â· Senegal Ã‚Â· and 24 more'),
    'franceza' => array('isos' => array('FRA', 'CAN', 'BEL', 'CHE', 'SEN', 'CIV', 'CMR', 'MLI'), 'lat' => 46.2276, 'lng' => 2.2137, 'flag' => 'https://flagcdn.com/w320/fr.png', 'flagSmall' => 'https://flagcdn.com/w40/fr.png', 'name' => 'French', 'nativeName' => 'FranÃƒÆ’Ã‚Â§ais', 'speakers' => '321M+', 'countriesText' => 'France Ã‚Â· Belgium Ã‚Â· Switzerland Ã‚Â· Canada Ã‚Â· Senegal Ã‚Â· and 24 more'),

    'german'   => array('isos' => array('DEU', 'AUT', 'CHE'), 'lat' => 51.1657, 'lng' => 10.4515, 'flag' => 'https://flagcdn.com/w320/de.png', 'flagSmall' => 'https://flagcdn.com/w40/de.png', 'name' => 'German', 'nativeName' => 'Deutsch', 'speakers' => '130M+', 'countriesText' => 'Germany Ã‚Â· Austria Ã‚Â· Switzerland'),
    'germana'  => array('isos' => array('DEU', 'AUT', 'CHE'), 'lat' => 51.1657, 'lng' => 10.4515, 'flag' => 'https://flagcdn.com/w320/de.png', 'flagSmall' => 'https://flagcdn.com/w40/de.png', 'name' => 'German', 'nativeName' => 'Deutsch', 'speakers' => '130M+', 'countriesText' => 'Germany Ã‚Â· Austria Ã‚Â· Switzerland'),

    'italian'  => array('isos' => array('ITA', 'CHE', 'SMR'), 'lat' => 41.8719, 'lng' => 12.5674, 'flag' => 'https://flagcdn.com/w320/it.png', 'flagSmall' => 'https://flagcdn.com/w40/it.png', 'name' => 'Italian', 'nativeName' => 'Italiano', 'speakers' => '85M+', 'countriesText' => 'Italy Ã‚Â· Switzerland Ã‚Â· San Marino'),
    'italiana' => array('isos' => array('ITA', 'CHE', 'SMR'), 'lat' => 41.8719, 'lng' => 12.5674, 'flag' => 'https://flagcdn.com/w320/it.png', 'flagSmall' => 'https://flagcdn.com/w40/it.png', 'name' => 'Italian', 'nativeName' => 'Italiano', 'speakers' => '85M+', 'countriesText' => 'Italy Ã‚Â· Switzerland Ã‚Â· San Marino'),

    'chinese'  => array('isos' => array('CHN', 'TWN', 'SGP'), 'lat' => 35.8617, 'lng' => 104.1954, 'flag' => 'https://flagcdn.com/w320/cn.png', 'flagSmall' => 'https://flagcdn.com/w40/cn.png', 'name' => 'Chinese', 'nativeName' => 'ÃƒÂ¤Ã‚Â¸Ã‚Â­ÃƒÂ¦Ã¢â‚¬â€œÃ¢â‚¬Â¡', 'speakers' => '1.1B+', 'countriesText' => 'China Ã‚Â· Taiwan Ã‚Â· Singapore'),
    'chineza'  => array('isos' => array('CHN', 'TWN', 'SGP'), 'lat' => 35.8617, 'lng' => 104.1954, 'flag' => 'https://flagcdn.com/w320/cn.png', 'flagSmall' => 'https://flagcdn.com/w40/cn.png', 'name' => 'Chinese', 'nativeName' => 'ÃƒÂ¤Ã‚Â¸Ã‚Â­ÃƒÂ¦Ã¢â‚¬â€œÃ¢â‚¬Â¡', 'speakers' => '1.1B+', 'countriesText' => 'China Ã‚Â· Taiwan Ã‚Â· Singapore'),
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

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Initial course query (9 per page) ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ REAL STATS ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬

/**
 * Real student count & rating (Cached with Transients for extreme performance)
 */
$stats_cache = get_transient( 'rima_real_course_stats' );
if ( false === $stats_cache ) {
    // Student Count
    global $wpdb;
    $real_student_count = (int) $wpdb->get_var("SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} WHERE meta_key = 'eltdf_user_courses'");
    
    if ( $real_student_count === 0 && function_exists( 'wc_get_orders' ) ) {
        $real_student_count = (int) $wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = 'shop_order' AND post_status IN ('wc-completed', 'wc-processing')");
    }
    $display_students = $real_student_count > 0 ? ( floor( $real_student_count / 10 ) * 10 ) . '+' : '0+';

    // Average Rating
    $rating_sum = 0;
    $rating_count = 0;
    $course_products = $wpdb->get_col("SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'eltdf_course_woo_product_meta' AND meta_value != ''");
    
    if (!empty($course_products)) {
        $product_ids = implode(',', array_map('intval', $course_products));
        $ratings = $wpdb->get_col("SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_wc_average_rating' AND post_id IN ($product_ids)");
        foreach ($ratings as $r) {
            if ((float) $r > 0) {
                $rating_sum += (float) $r;
                $rating_count++;
            }
        }
    }
    
    $display_rating = $rating_count > 0 ? number_format( $rating_sum / $rating_count, 1 ) : '5.0';

    $stats_cache = array(
        'display_students' => $display_students,
        'display_rating'   => $display_rating
    );
    set_transient( 'rima_real_course_stats', $stats_cache, 12 * HOUR_IN_SECONDS );
}

$display_students = $stats_cache['display_students'];
$display_rating   = $stats_cache['display_rating'];
?>


<!-- ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â
     HERO SECTION
     ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â -->
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
                    <span class="roc-stat-number"><?php echo esc_html( $display_rating ); ?>ÃƒÂ¢Ã‹Å“Ã¢â‚¬Â¦</span>
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

<!-- ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â
     INTERACTIVE LANGUAGE SHOWCASE REEL (replaces courses.mp4)
     Dynamic ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â reads from $active_globe_langs (auto-updates with new categories)
     ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â -->
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
                        <span class="roc-reel-stat-num"><?php echo esc_html( $rlang['speakers'] ?? 'Ã¢â‚¬â€' ); ?></span>
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

<!-- 
     PREMIUM PACKAGES CONFIGURATOR (VUE 3) - BENTO GRID & STICKY CART
-->
<?php 
// ─── Packages & UI settings ─────────────────────────────────────────
if ( function_exists('rima_get_packages') ) {
    $pricing_data = rima_get_packages();
    $ui_settings  = rima_get_packages_ui();
} else {
    $pricing_data = get_option('rima_pricing_packages', []);
    $ui_settings  = array_merge([
        'title'    => 'Build Your Package',
        'subtitle' => 'Select your language, level, and addons.',
        'cta'      => 'PROCEED TO CHECKOUT',
        'features' => [
            'Full access to Rima LMS',
            'Interactive exercises',
            'Self-paced progress tracking',
            'Official Certificate included',
        ],
    ], $pricing_data['ui'] ?? []);
}
$encoded_pricing = json_encode($pricing_data);
$encoded_ui      = json_encode($ui_settings);
?>

<section class="roc-packages-configurator tw-py-24 tw-bg-[#030408] tw-font-sans tw-relative tw-overflow-hidden" id="rima-packages-app" v-cloak>
    
    <!-- Ambient Glows -->
    <div class="tw-absolute tw-top-[-10%] tw-left-[-10%] tw-w-[40%] tw-h-[50%] tw-bg-[#E11D48] tw-rounded-full tw-mix-blend-screen tw-filter tw-blur-[150px] tw-opacity-20 tw-pointer-events-none"></div>
    <div class="tw-absolute tw-bottom-[-10%] tw-right-[-10%] tw-w-[40%] tw-h-[50%] tw-bg-[#12308E] tw-rounded-full tw-mix-blend-screen tw-filter tw-blur-[150px] tw-opacity-20 tw-pointer-events-none"></div>

    <div class="tw-max-w-7xl tw-mx-auto tw-px-4 sm:tw-px-6 tw-relative tw-z-10">
        
        <div class="tw-mb-12 tw-text-center lg:tw-text-left">
            <h2 class="tw-text-5xl tw-font-display tw-font-extrabold tw-text-white tw-tracking-tight tw-mb-4"><?php echo esc_html($ui_settings['title']); ?></h2>
            <p class="tw-text-lg tw-text-gray-400 tw-max-w-2xl"><?php echo esc_html($ui_settings['subtitle']); ?></p>
        </div>

        <div class="tw-flex tw-flex-col-reverse lg:tw-flex-row tw-gap-10 tw-items-start">
            
            <!-- Left: Options Grid (Bento) -->
            <div class="tw-flex-1 tw-space-y-10 tw-w-full">
                
                <!-- 1. Language -->
                <div>
                    <h3 class="tw-text-2xl tw-font-bold tw-text-white tw-mb-6 tw-flex tw-items-center tw-gap-3">
                        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-text-sm">1</div>
                        Language
                    </h3>
                    <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 tw-gap-4">
                        <button v-for="lang in languages" :key="lang.id" 
                                @click="selection.language = lang.id"
                                :class="['tw-relative tw-p-6 tw-rounded-[1.5rem] tw-border tw-transition-all tw-duration-300 tw-ease-out tw-text-left tw-flex tw-flex-col tw-gap-4 group tw-overflow-hidden', 
                                         selection.language === lang.id ? 'tw-border-[#E11D48] tw-bg-gradient-to-br tw-from-[#E11D48]/20 tw-to-[#12308E]/20 tw-text-white' : 'tw-border-white/5 tw-bg-white/[0.03] hover:tw-bg-white/[0.06] hover:tw-border-white/20 tw-text-gray-300']">
                            
                            <img :src="lang.flag" class="tw-w-16 tw-h-12 tw-rounded-lg tw-shadow-md tw-object-cover group-hover:tw-scale-105 tw-transition-transform tw-duration-300" />
                            <div>
                                <div :class="['tw-font-bold tw-text-xl tw-tracking-wide tw-mb-1', selection.language === lang.id ? 'tw-text-white' : 'tw-text-white']">{{ lang.name }}</div>
                                <div :class="['tw-text-sm', selection.language === lang.id ? 'tw-text-gray-300' : 'tw-text-gray-500']">{{ lang.native }}</div>
                            </div>
                            
                            <!-- Check -->
                            <div v-if="selection.language === lang.id" class="tw-absolute tw-top-4 tw-right-4 tw-w-6 tw-h-6 tw-bg-[#E11D48] tw-text-white tw-rounded-full tw-flex tw-items-center tw-justify-center tw-shadow-lg">
                                <i data-lucide="check" class="tw-w-3.5 tw-h-3.5 tw-stroke-[3]"></i>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- 2. Level -->
                <div>
                    <h3 class="tw-text-2xl tw-font-bold tw-text-white tw-mb-6 tw-flex tw-items-center tw-gap-3">
                        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-text-sm">2</div>
                        CEFR Level
                    </h3>
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4">
                        <button v-for="level in levels" :key="level.id"
                                @click="selection.level = level.id"
                                :class="['tw-relative tw-p-6 tw-rounded-[1.5rem] tw-border tw-transition-all tw-duration-300 tw-ease-out tw-text-left tw-flex tw-flex-col group', 
                                         selection.level === level.id ? 'tw-border-[#12308E] tw-bg-gradient-to-br tw-from-[#12308E]/20 tw-to-transparent tw-shadow-[0_0_20px_rgba(18,48,142,0.2)]' : 'tw-border-white/5 tw-bg-white/[0.03] hover:tw-bg-white/[0.06] hover:tw-border-white/20']">
                            
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                                <span :class="['tw-font-bold tw-text-2xl', selection.level === level.id ? 'tw-text-white' : 'tw-text-white']">{{ level.name }}</span>
                                <div :class="['tw-w-3 tw-h-3 tw-rounded-full', selection.level === level.id ? 'tw-bg-[#12308E] tw-shadow-[0_0_8px_#12308E]' : 'tw-bg-white/20']"></div>
                            </div>
                            <div :class="['tw-font-semibold tw-mb-2', selection.level === level.id ? 'tw-text-[#60a5fa]' : 'tw-text-gray-300']">{{ level.title }}</div>
                            <div class="tw-text-sm tw-text-gray-400 tw-leading-relaxed">{{ level.desc }}</div>
                        </button>
                    </div>
                </div>

                <!-- 3. Format -->
                <div>
                    <h3 class="tw-text-2xl tw-font-bold tw-text-white tw-mb-6 tw-flex tw-items-center tw-gap-3">
                        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-text-sm">3</div>
                        Learning Format
                    </h3>
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                        <button v-for="format in formats" :key="format.id"
                                @click="selection.format = format.id; selection.duration = format.defaultDuration"
                                :class="['tw-relative tw-p-6 tw-rounded-[1.5rem] tw-border tw-transition-all tw-duration-300 tw-ease-out tw-text-left tw-flex tw-items-start tw-gap-5 group', 
                                         selection.format === format.id ? 'tw-border-white/30 tw-bg-white/10' : 'tw-border-white/5 tw-bg-white/[0.03] hover:tw-bg-white/[0.06] hover:tw-border-white/20']">
                            
                            <div :class="['tw-w-14 tw-h-14 tw-rounded-2xl tw-flex tw-items-center tw-justify-center tw-shrink-0 tw-transition-colors', selection.format === format.id ? 'tw-bg-white tw-text-black' : 'tw-bg-white/10 tw-text-gray-400 group-hover:tw-text-white']">
                                <i :data-lucide="format.icon" class="tw-w-6 tw-h-6"></i>
                            </div>
                            <div>
                                <div :class="['tw-font-bold tw-text-xl tw-mb-1', selection.format === format.id ? 'tw-text-white' : 'tw-text-gray-200']">{{ format.name }}</div>
                                <div class="tw-text-sm tw-text-gray-400">{{ format.desc }}</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- 4. Duration (Only for non-platform) -->
                <transition name="fade-up">
                    <div v-if="selection.format !== 'platform'">
                        <h3 class="tw-text-2xl tw-font-bold tw-text-white tw-mb-6 tw-flex tw-items-center tw-gap-3">
                            <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-text-sm">4</div>
                            Duration
                        </h3>
                        <div class="tw-grid tw-grid-cols-3 tw-gap-4">
                            <button v-for="dur in durations" :key="dur.id"
                                    @click="selection.duration = dur.id"
                                    :class="['tw-p-5 tw-rounded-[1.5rem] tw-border tw-transition-all tw-duration-300 tw-ease-out tw-text-center tw-font-bold', 
                                            selection.duration === dur.id ? 'tw-border-white/30 tw-bg-white/10 tw-text-white' : 'tw-border-white/5 tw-bg-white/[0.03] tw-text-gray-400 hover:tw-bg-white/[0.06] hover:tw-text-white hover:tw-border-white/20']">
                                {{ dur.name }}
                            </button>
                        </div>
                    </div>
                </transition>

            </div>

            <!-- Right: The "Apple Store" Sticky Cart -->
            <div class="tw-w-full lg:tw-w-[420px] tw-sticky tw-top-24 tw-shrink-0">
                <div class="tw-bg-[#0f111a] tw-border tw-border-white/10 tw-rounded-[2rem] tw-overflow-hidden tw-shadow-2xl">
                    
                    <div class="tw-p-8">
                        <h3 class="tw-text-sm tw-font-bold tw-text-gray-400 tw-uppercase tw-tracking-widest tw-mb-6">Your Package</h3>
                        
                        <!-- Dynamic Selections (Receipt style) -->
                        <div class="tw-space-y-4 tw-mb-8">
                            <!-- Course Selection -->
                            <div class="tw-flex tw-items-start tw-gap-4 tw-p-4 tw-bg-white/5 tw-rounded-2xl tw-border tw-border-white/5">
                                <img :src="currentLanguageFlag" class="tw-w-10 tw-h-8 tw-rounded tw-object-cover tw-shrink-0" />
                                <div class="tw-flex-1">
                                    <div class="tw-font-bold tw-text-white">{{ currentLanguageName }} {{ currentLevelName }}</div>
                                    <div class="tw-text-xs tw-text-gray-400">{{ currentFormatName }} <span v-if="selection.format !== 'platform'">• {{ currentDurationName }}</span></div>
                                </div>
                                <div class="tw-font-medium tw-text-white">
                                    {{ calculatedBasePrice }} RON
                                </div>
                            </div>
                        </div>

                        <!-- Add-ons -->
                        <div class="tw-mb-8">
                            <h4 class="tw-text-sm tw-font-bold tw-text-gray-400 tw-mb-4">Add-Ons</h4>
                            <div class="tw-space-y-3">
                                
                                <label class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-rounded-2xl tw-border tw-border-white/5 tw-bg-white/[0.02] tw-cursor-pointer hover:tw-bg-white/[0.04] tw-transition-colors">
                                    <div class="tw-flex-1">
                                        <div class="tw-font-bold tw-text-white tw-text-sm">1-on-1 Mentoring</div>
                                        <div class="tw-text-xs tw-text-gray-500">2h/week dedicated support</div>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-gap-4">
                                        <span class="tw-text-sm tw-font-medium tw-text-gray-300">+990 RON</span>
                                        <div :class="['tw-w-11 tw-h-6 tw-rounded-full tw-p-1 tw-transition-colors tw-duration-300 tw-ease-in-out', addons.mentoring ? 'tw-bg-[#E11D48]' : 'tw-bg-gray-600']">
                                            <input type="checkbox" v-model="addons.mentoring" class="tw-sr-only">
                                            <div :class="['tw-w-4 tw-h-4 tw-bg-white tw-rounded-full tw-shadow-sm tw-transition-transform tw-duration-300 tw-ease-in-out', addons.mentoring ? 'tw-translate-x-5' : 'tw-translate-x-0']"></div>
                                        </div>
                                    </div>
                                </label>

                                <label class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-rounded-2xl tw-border tw-border-white/5 tw-bg-white/[0.02] tw-cursor-pointer hover:tw-bg-white/[0.04] tw-transition-colors">
                                    <div class="tw-flex-1">
                                        <div class="tw-font-bold tw-text-white tw-text-sm">Premium Certificate</div>
                                        <div class="tw-text-xs tw-text-gray-500">Physical copy sent globally</div>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-gap-4">
                                        <span class="tw-text-sm tw-font-medium tw-text-gray-300">+190 RON</span>
                                        <div :class="['tw-w-11 tw-h-6 tw-rounded-full tw-p-1 tw-transition-colors tw-duration-300 tw-ease-in-out', addons.certificate ? 'tw-bg-[#E11D48]' : 'tw-bg-gray-600']">
                                            <input type="checkbox" v-model="addons.certificate" class="tw-sr-only">
                                            <div :class="['tw-w-4 tw-h-4 tw-bg-white tw-rounded-full tw-shadow-sm tw-transition-transform tw-duration-300 tw-ease-in-out', addons.certificate ? 'tw-translate-x-5' : 'tw-translate-x-0']"></div>
                                        </div>
                                    </div>
                                </label>

                            </div>
                        </div>

                        <!-- Total -->
                        <div class="tw-border-t tw-border-white/10 tw-pt-6 tw-mb-6">
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                                <span class="tw-text-gray-400">Total</span>
                                <div class="tw-flex tw-items-baseline tw-gap-1">
                                    <span class="tw-text-4xl tw-font-display tw-font-bold tw-text-white">{{ totalPrice }}</span>
                                    <span class="tw-text-lg tw-text-gray-400">RON</span>
                                </div>
                            </div>
                            <div class="tw-text-right tw-text-xs tw-text-[#10B981] tw-font-medium" v-if="addons.mentoring || addons.certificate">
                                Includes add-ons
                            </div>
                        </div>

                        <button class="tw-w-full tw-py-4 tw-bg-white hover:tw-bg-gray-200 tw-text-black tw-rounded-xl tw-font-bold tw-text-[15px] tw-tracking-wide tw-transition-colors tw-flex tw-items-center tw-justify-center tw-gap-2">
                            <?php echo esc_html($ui_settings['cta']); ?>
                        </button>
                    </div>

                    <!-- Features -->
                    <div class="tw-bg-black/30 tw-p-6">
                        <ul class="tw-space-y-3 tw-text-sm tw-text-gray-400">
                            <?php foreach ($ui_settings['features'] as $feature) : ?>
                            <li class="tw-flex tw-items-center tw-gap-3">
                                <i data-lucide="check-circle-2" class="tw-w-4 tw-h-4 tw-text-[#E11D48]"></i>
                                <?php echo esc_html($feature); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* Animations */
.scale-in-enter-active, .scale-in-leave-active { transition: all 0.3s cubic-bezier(0.32, 0.72, 0, 1); }
.scale-in-enter-from, .scale-in-leave-to { opacity: 0; transform: scale(0.5); }
.fade-up-enter-active, .fade-up-leave-active { transition: all 0.4s cubic-bezier(0.32, 0.72, 0, 1); }
.fade-up-enter-from, .fade-up-leave-to { opacity: 0; transform: translateY(10px); }
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

<!-- Include Vue 3 & Lucide -->
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const { createApp, ref, computed, watch, nextTick } = Vue;
    
    // Inject pricing data from PHP
    const pricingData = <?php echo $encoded_pricing; ?>;
    const uiSettings = <?php echo $encoded_ui; ?>;
    
    createApp({
        setup() {
            const languages = [
                { id: 'en', name: 'English', native: 'English', flag: 'https://flagcdn.com/w80/gb.png' },
                { id: 'ro', name: 'Romanian', native: 'Română', flag: 'https://flagcdn.com/w80/ro.png' },
                { id: 'jp', name: 'Japanese', native: '日本語', flag: 'https://flagcdn.com/w80/jp.png' }
            ];

            const levels = [
                { id: 'a1_a2', name: 'A1-A2', title: 'Beginner / Elem.', desc: 'Basic communication and everyday situations.', color: 'tw-bg-[#10B981]' },
                { id: 'b1_b2', name: 'B1-B2', title: 'Intermediate', desc: 'Express yourself with confidence.', color: 'tw-bg-[#3B82F6]' },
                { id: 'c1_c2', name: 'C1-C2', title: 'Advanced', desc: 'Mastery and near-native fluency.', color: 'tw-bg-[#8B5CF6]' }
            ];

            const formats = [
                { id: 'platform', name: 'Platform Only', desc: 'Self-paced learning on our advanced LMS.', icon: 'monitor', defaultDuration: null },
                { id: 'individual', name: '1-on-1 Sessions', desc: 'Zoom sessions with a dedicated tutor.', icon: 'user', defaultDuration: '1_month' },
                { id: 'group', name: 'Group Cohort', desc: 'Small groups (3-8 students).', icon: 'users', defaultDuration: '1_month' },
                { id: 'corporate', name: 'Corporate', desc: 'B2B custom plans for teams and employees.', icon: 'building-2', defaultDuration: '1_month' }
            ];

            const durations = [
                { id: '1_month', name: '1 Month' },
                { id: '3_months', name: '3 Months' },
                { id: '6_months', name: '6 Months' }
            ];

            const selection = ref({
                language: 'en',
                level: 'b1_b2',
                format: 'platform',
                duration: null
            });

            const addons = ref({
                mentoring: false,
                certificate: false
            });

            const currentLanguageName = computed(() => languages.find(l => l.id === selection.value.language)?.name || '');
            const currentLanguageFlag = computed(() => languages.find(l => l.id === selection.value.language)?.flag || '');
            const currentLevelName = computed(() => levels.find(l => l.id === selection.value.level)?.name || '');
            const currentFormatName = computed(() => formats.find(f => f.id === selection.value.format)?.name || '');
            const currentDurationName = computed(() => {
                if(selection.value.format === 'platform') return '-';
                return durations.find(d => d.id === selection.value.duration)?.name || '';
            });

            const calculatedBasePrice = computed(() => {
                try {
                    let price = 0;
                    const format = selection.value.format;
                    const level = selection.value.level;
                    const lang = selection.value.language;
                    
                    if (format === 'platform') {
                        price = pricingData.platform[level][lang];
                    } else if (format === 'individual' || format === 'corporate' || format === 'group') {
                        const duration = selection.value.duration || '1_month';
                        if (pricingData[format] && pricingData[format][level] && pricingData[format][level][duration]) {
                            price = pricingData[format][level][duration][lang];
                        } else {
                            // Fallback
                            price = (pricingData['individual'][level][duration][lang] * 0.7) || 0;
                        }
                    }
                    return Math.round(price) || 0;
                } catch (e) {
                    return 0;
                }
            });

            const totalPrice = computed(() => {
                let total = calculatedBasePrice.value;
                if (addons.value.mentoring) total += 990;
                if (addons.value.certificate) total += 190;
                return total;
            });

            watch(selection, () => {
                nextTick(() => {
                    if (window.lucide) lucide.createIcons();
                });
            }, { deep: true });

            watch(addons, () => {
            }, { deep: true });

            // Init icons on mount
            nextTick(() => { if (window.lucide) lucide.createIcons(); });

            return {
                languages, levels, formats, durations, selection, addons,
                currentLanguageName, currentLanguageFlag, currentLevelName, currentFormatName, currentDurationName,
                calculatedBasePrice, totalPrice
            }
        }
    }).mount('#rima-packages-app');
});
</script>

<!-- THE ACADEMY EXPERIENCE (BENTO GRID) -->
<section class="tw-bg-[#030408] tw-py-20 tw-font-sans tw-relative">
    <div class="tw-max-w-7xl tw-mx-auto tw-px-4 sm:tw-px-6 tw-relative tw-z-10">
        
        <div class="tw-mb-12 tw-text-center">
            <h2 class="tw-text-4xl tw-font-display tw-font-extrabold tw-text-white tw-tracking-tight tw-mb-4">The Academy Experience</h2>
            <p class="tw-text-gray-400 tw-text-lg">Premium learning features included in every plan.</p>
        </div>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6 tw-auto-rows-[300px]">
            
            <!-- Large Card (Span 2) -->
            <div class="md:tw-col-span-2 tw-relative tw-rounded-[2rem] tw-overflow-hidden tw-group">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1000&auto=format&fit=crop" class="tw-absolute tw-inset-0 tw-w-full tw-h-full tw-object-cover tw-transition-transform tw-duration-700 group-hover:tw-scale-105" />
                <div class="tw-absolute tw-inset-0 tw-bg-gradient-to-t tw-from-[#030408] tw-via-[#030408]/60 tw-to-transparent"></div>
                <div class="tw-absolute tw-bottom-0 tw-left-0 tw-p-8">
                    <div class="tw-w-12 tw-h-12 tw-bg-[#E11D48] tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mb-4">
                        <i data-lucide="video" class="tw-w-6 tw-h-6 tw-text-white"></i>
                    </div>
                    <h3 class="tw-text-2xl tw-font-bold tw-text-white tw-mb-2">Immersive Live Sessions</h3>
                    <p class="tw-text-gray-300 tw-max-w-md">Connect directly with native speakers and expert tutors in high-quality interactive environments.</p>
                </div>
            </div>

            <!-- Small Card -->
            <div class="tw-relative tw-rounded-[2rem] tw-overflow-hidden tw-bg-[#0f111a] tw-border tw-border-white/5 hover:tw-border-white/20 tw-transition-colors tw-group">
                <div class="tw-p-8 tw-flex tw-flex-col tw-h-full tw-justify-end">
                    <div class="tw-w-12 tw-h-12 tw-bg-[#12308E] tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mb-auto tw-shrink-0">
                        <i data-lucide="activity" class="tw-w-6 tw-h-6 tw-text-white"></i>
                    </div>
                    <div>
                        <h3 class="tw-text-xl tw-font-bold tw-text-white tw-mb-2">Advanced Analytics</h3>
                        <p class="tw-text-sm tw-text-gray-400">Track your fluency progress in real-time with our proprietary AI dashboard.</p>
                    </div>
                </div>
            </div>

            <!-- Small Card -->
            <div class="tw-relative tw-rounded-[2rem] tw-overflow-hidden tw-bg-[#0f111a] tw-border tw-border-white/5 hover:tw-border-white/20 tw-transition-colors tw-group">
                <div class="tw-p-8 tw-flex tw-flex-col tw-h-full tw-justify-end">
                    <div class="tw-w-12 tw-h-12 tw-bg-[#10B981] tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mb-auto tw-shrink-0">
                        <i data-lucide="file-check-2" class="tw-w-6 tw-h-6 tw-text-white"></i>
                    </div>
                    <div>
                        <h3 class="tw-text-xl tw-font-bold tw-text-white tw-mb-2">Detailed Feedback</h3>
                        <p class="tw-text-sm tw-text-gray-400">Receive precise corrections on pronunciation, grammar, and vocabulary.</p>
                    </div>
                </div>
            </div>

            <!-- Large Card (Span 2) -->
            <div class="md:tw-col-span-2 tw-relative tw-rounded-[2rem] tw-overflow-hidden tw-group">
                <img src="https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?q=80&w=1000&auto=format&fit=crop" class="tw-absolute tw-inset-0 tw-w-full tw-h-full tw-object-cover tw-transition-transform tw-duration-700 group-hover:tw-scale-105" />
                <div class="tw-absolute tw-inset-0 tw-bg-gradient-to-t tw-from-[#030408] tw-via-[#030408]/60 tw-to-transparent"></div>
                <div class="tw-absolute tw-bottom-0 tw-left-0 tw-p-8">
                    <div class="tw-w-12 tw-h-12 tw-bg-white/10 tw-backdrop-blur-md tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mb-4 tw-border tw-border-white/20">
                        <i data-lucide="globe-2" class="tw-w-6 tw-h-6 tw-text-white"></i>
                    </div>
                    <h3 class="tw-text-2xl tw-font-bold tw-text-white tw-mb-2">Global Community</h3>
                    <p class="tw-text-gray-300 tw-max-w-md">Join thousands of professionals mastering new languages and expanding their horizons daily.</p>
                </div>
            </div>

        </div>
    </div>
</section>


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
                    <option value="price_asc"><span class="rima-en">Price: Low ÃƒÂ¢Ã¢â‚¬Â Ã¢â‚¬â„¢ High</span></option>
                    <option value="price_desc"><span class="rima-en">Price: High ÃƒÂ¢Ã¢â‚¬Â Ã¢â‚¬â„¢ Low</span></option>
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

<!-- ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â
     COURSE GRID
     ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â -->
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
                                    <span class="rima-en"> lessons</span><span class="rima-ro"> lecÃˆâ€ºii</span>
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

    <!-- ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Empty State ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ -->
    <div class="roc-empty-state" id="roc-empty-state" aria-live="polite" style="display:none;">
            <button class="eltdf-btn eltdf-btn-solid rima-btn-blue" id="roc-reset-filters">
            Clear Filters
        </button>
    </div>

    <!-- ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Load More ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ -->
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

<!-- ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â
     INTERACTIVE REEL JS
     ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const reelSection = document.getElementById('roc-lang-reel');
    if (!reelSection) return;

    // ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Particle Background ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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

    // ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Slide Controller ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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

    // CTA button ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â filter courses and zoom globe
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







