<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Ensure WPBakery is active
add_action( 'vc_before_init', 'rima_register_vc_elements' );

function rima_register_vc_elements() {

    // 1. RIMA Hero Element
    vc_map( array(
        "name" => "Rima Hero (Globe)",
        "base" => "rima_hero",
        "category" => "Rima Academy",
        "icon" => "icon-wpb-layer-shape-text",
        "params" => array(
            array(
                "type" => "textfield",
                "holder" => "h3",
                "class" => "",
                "heading" => "Title",
                "param_name" => "title",
                "value" => "Master",
            )
        )
    ) );

    // 2. RIMA Courses Flip Grid
    vc_map( array(
        "name" => "Rima Courses Grid (3D Flip)",
        "base" => "rima_courses_grid",
        "category" => "Rima Academy",
        "icon" => "icon-wpb-application-icon-large"
    ) );

    // 3. RIMA Pathway
    vc_map( array(
        "name" => "Rima Pathway (How it works)",
        "base" => "rima_pathway",
        "category" => "Rima Academy",
        "icon" => "icon-wpb-ui-tab-content"
    ) );
}

add_shortcode('rima_hero', 'rima_sc_hero');
function rima_sc_hero($atts) {
    ob_start();
    require get_stylesheet_directory() . '/template-parts/rima-hero.php';
    return ob_get_clean();
}

add_shortcode('rima_courses_grid', 'rima_sc_courses_grid');
function rima_sc_courses_grid($atts) {
    ob_start();
    require get_stylesheet_directory() . '/template-parts/rima-courses-grid.php';
    return ob_get_clean();
}

add_shortcode('rima_pathway', 'rima_sc_pathway');
function rima_sc_pathway($atts) {
    ob_start();
    require get_stylesheet_directory() . '/template-parts/rima-pathway.php';
    return ob_get_clean();
}
