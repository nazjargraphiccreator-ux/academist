<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * RIMA Academy - 2026 Premium Customizer Settings
 */
function rima_customize_register( $wp_customize ) {
    // Panel
    $wp_customize->add_panel( 'rima_2026_design', array(
        'title'       => __( 'RIMA 2026 Design (Ultramodern)', 'rima' ),
        'priority'    => 10,
    ));

    // Section - Colors
    $wp_customize->add_section( 'rima_2026_colors', array(
        'title'       => __( 'Culori (Colors)', 'rima' ),
        'panel'       => 'rima_2026_design',
    ));

    // Setting: Primary Color (Deep Blue)
    $wp_customize->add_setting( 'rima_primary_color', array(
        'default'           => '#0F172A', // Oxford Blue
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rima_primary_color', array(
        'label'    => __( 'Primary (Oxford Blue)', 'rima' ),
        'section'  => 'rima_2026_colors',
    )));

    // Setting: Primary Light (Lighter Dark Blue)
    $wp_customize->add_setting( 'rima_primary_light_color', array(
        'default'           => '#1E293B',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rima_primary_light_color', array(
        'label'    => __( 'Primary Light (Slate Blue)', 'rima' ),
        'section'  => 'rima_2026_colors',
    )));

    // Setting: Secondary Color (Cyber Cyan)
    $wp_customize->add_setting( 'rima_secondary_color', array(
        'default'           => '#B41527', // Crimson Red
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rima_secondary_color', array(
        'label'    => __( 'Secondary (Crimson Red)', 'rima' ),
        'section'  => 'rima_2026_colors',
    )));

    // Setting: Accent Color (Electric Violet)
    $wp_customize->add_setting( 'rima_accent_color', array(
        'default'           => '#D4AF37', // Gold
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rima_accent_color', array(
        'label'    => __( 'Accent (Academic Gold)', 'rima' ),
        'section'  => 'rima_2026_colors',
    )));

    // Section - Glassmorphism & UI
    $wp_customize->add_section( 'rima_2026_ui', array(
        'title'       => __( 'Glassmorphism & UI', 'rima' ),
        'panel'       => 'rima_2026_design',
    ));

    // Glassmorphism blur
    $wp_customize->add_setting( 'rima_glass_blur', array(
        'default'           => '20',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control( 'rima_glass_blur', array(
        'type'        => 'number',
        'label'       => __( 'Glassmorphism Blur (px)', 'rima' ),
        'section'     => 'rima_2026_ui',
        'description' => 'Default is 20.',
    ));
    
    // Glassmorphism opacity
    $wp_customize->add_setting( 'rima_glass_opacity', array(
        'default'           => '0.7',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control( 'rima_glass_opacity', array(
        'type'        => 'text',
        'label'       => __( 'Glassmorphism Opacity (0.0 to 1.0)', 'rima' ),
        'section'     => 'rima_2026_ui',
        'description' => 'Default is 0.7.',
    ));
    
    // Border Radius Global
    $wp_customize->add_setting( 'rima_border_radius', array(
        'default'           => '16',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control( 'rima_border_radius', array(
        'type'        => 'number',
        'label'       => __( 'Global Base Border Radius (px)', 'rima' ),
        'section'     => 'rima_2026_ui',
        'description' => 'This is the base MD radius. LG and XL will scale automatically. Default is 16.',
    ));
}
add_action( 'customize_register', 'rima_customize_register' );

/**
 * Output Customizer CSS Variables
 */
function rima_customizer_css() {
    $primary       = get_theme_mod('rima_primary_color', '#0F172A');
    $primary_light = get_theme_mod('rima_primary_light_color', '#1E293B');
    $secondary     = get_theme_mod('rima_secondary_color', '#B41527');
    $accent        = get_theme_mod('rima_accent_color', '#D4AF37');
    
    $blur    = get_theme_mod('rima_glass_blur', '20');
    $opacity = get_theme_mod('rima_glass_opacity', '0.7');
    $radius  = get_theme_mod('rima_border_radius', '16');
    
    // Calculate radius scale
    $rad_sm = max(4, $radius - 8);
    $rad_lg = $radius + 8;
    $rad_xl = $radius + 16;
    
    $css = "
    :root {
        --rima-primary: {$primary} !important;
        --rima-primary-light: {$primary_light} !important;
        --rima-secondary: {$secondary} !important;
        --rima-accent: {$accent} !important;
        
        --rima-surface-glass: rgba(255, 255, 255, {$opacity}) !important;
        --rima-glass-blur: blur({$blur}px) !important;
        
        --rima-radius-sm: {$rad_sm}px !important;
        --rima-radius-md: {$radius}px !important;
        --rima-radius-lg: {$rad_lg}px !important;
        --rima-radius-xl: {$rad_xl}px !important;
        
        --rima-gradient-primary: linear-gradient(135deg, {$secondary} 0%, {$accent} 100%) !important;
        --rima-gradient-dark: linear-gradient(135deg, {$primary} 0%, {$primary_light} 100%) !important;
    }
    
    /* Apply dynamic blur variable to glass elements */
    .rima-site-header, .rima-checkout-step, .rima-order-summary, .rima-modern-course-card, .rima-premium-card, .rima-lp-card {
        backdrop-filter: var(--rima-glass-blur) !important;
        -webkit-backdrop-filter: var(--rima-glass-blur) !important;
        background: var(--rima-surface-glass) !important;
    }
    ";
    
    wp_add_inline_style( 'rima-global-tokens', $css );
}
add_action( 'wp_enqueue_scripts', 'rima_customizer_css', 20 );
