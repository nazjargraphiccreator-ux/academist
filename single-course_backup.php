<?php
/**
 * Single Course — 2026 MasterClass Concept
 * RIMA Academy / Academist LMS
 *
 * @package Academist Child
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
do_action( 'academist_elated_action_before_main_content' );

if ( ! have_posts() ) { get_footer(); return; }
the_post();

if ( isset( $_REQUEST['add-to-cart'] ) ) {
    error_log( 'ADD TO CART IN SINGLE COURSE. Request method: ' . $_SERVER['REQUEST_METHOD'] . ' data: ' . print_r($_REQUEST, true) );
}


/* -- Core meta -------------------------------------------------- */
$course_id     = get_the_ID();
$instructor_id = get_post_meta( $course_id, 'eltdf_course_instructor_meta', true );
if ( $instructor_id && get_post_status( $instructor_id ) !== 'publish' ) {
    $instructor_id = false;
}

// Categories
$cats     = get_the_terms( $course_id, 'course-category' );
$cat_name = ( ! is_wp_error( $cats ) && ! empty( $cats ) ) ? $cats[0]->name : '';
$cat_slug = ( ! is_wp_error( $cats ) && ! empty( $cats ) ) ? $cats[0]->slug : '';

// Thumbnail
$thumb_id  = get_post_thumbnail_id( $course_id );
$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';

// Price
$price       = function_exists( 'academist_lms_calculate_course_price' ) ? academist_lms_calculate_course_price( $course_id ) : 0;
$price_label = '';
if ( $price == 0 ) {
    $price_label = 'Free';
} elseif ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
    $pos         = get_option( 'woocommerce_currency_pos', 'right' );
    $sym         = get_woocommerce_currency_symbol();
    $price_label = $pos === 'left' ? esc_html( $sym . $price ) : esc_html( $price . ' ' . $sym );
} else {
    $price_label = esc_html( $price );
}

// Instructor meta
$instr_name    = $instructor_id ? get_the_title( $instructor_id )                                 : '';
$instr_img_id  = $instructor_id ? get_post_thumbnail_id( $instructor_id )                         : 0;
$instr_img_url = $instr_img_id  ? wp_get_attachment_image_url( $instr_img_id, 'thumbnail' )       : '';
$instr_title   = $instructor_id ? get_post_meta( $instructor_id, 'eltdf_instructor_title', true ) : '';

$instr_bio_raw = $instructor_id ? get_post_field( 'post_content', $instructor_id ) : '';
$instr_bio     = $instr_bio_raw ? apply_filters( 'the_content', $instr_bio_raw ) : '';

// Course meta
$duration      = get_post_meta( $course_id, 'eltdf_course_duration', true );
$lessons_count = get_post_meta( $course_id, 'eltdf_course_lessons_count', true );
$certificate   = get_post_meta( $course_id, 'eltdf_course_certificate', true );
$outcomes_raw  = get_post_meta( $course_id, 'eltdf_course_outcomes', true );
$outcomes      = $outcomes_raw ? array_filter( array_map( 'trim', explode( "\n", $outcomes_raw ) ) ) : array();
$video_preview = get_post_meta( $course_id, 'eltdf_course_preview_video', true );

if ( has_excerpt() ) {
    $course_desc_html = wp_kses_post( get_the_excerpt() );
} else {
    $full_content     = apply_filters( 'the_content', get_the_content() );
    $course_desc_html = wp_trim_words( wp_strip_all_tags( $full_content ), 35 );
}

// Enrollment check
$is_enrolled   = false;
$dashboard_url = '';
$button_text   = 'Go to Dashboard';
if ( is_user_logged_in() ) {
    $user_id      = get_current_user_id();
    $user_courses = get_user_meta( $user_id, 'eltdf_user_courses', true );
    $is_enrolled  = is_array( $user_courses ) && in_array( $course_id, $user_courses, true );
    if ( $is_enrolled ) {
        // Dacă există un PDF RIMA pentru acest curs, direcționează studentul spre Sala de clasă
        $rima_pdf = get_post_meta( $course_id, 'rima_pdf_url', true );
        if ( ! empty( $rima_pdf ) ) {
            $dashboard_url = add_query_arg( array( 'rima_viewer' => $course_id ), wc_get_account_endpoint_url( 'rima-cursuri' ) );
            $button_text   = 'Deschide Sala de Clasă';
        } else {
            $dashboard_url = wc_get_endpoint_url( 'course-dashboard', $course_id, wc_get_page_permalink( 'myaccount' ) );
        }
    }
}

// Related courses
$related_query = new WP_Query( array(
    'post_type'      => 'course',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'post__not_in'   => array( $course_id ),
    'tax_query'      => $cat_slug ? array( array(
        'taxonomy' => 'course-category',
        'field'    => 'slug',
        'terms'    => $cat_slug,
    ) ) : array(),
) );

$params = array( 'sidebar_layout' => 'no-sidebar', 'instructor' => $instructor_id );
?>

<?php if ( function_exists( 'wc_print_notices' ) ) { wc_print_notices(); } ?>

<div class="rmc-layout">

    <!-- ============================================================
         1. SPLIT HERO
         ============================================================ -->
    <section class="rmc-hero">
        <div class="rmc-hero-inner">
            
            <!-- Left: Content & CTA -->
            <div class="rmc-hero-content">
                <nav class="rmc-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?php echo esc_url( home_url( '/our-courses/' ) ); ?>">Our Courses</a>
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    <span><?php echo esc_html( get_the_title() ); ?></span>
                </nav>

                <?php if ( $cat_name ) : ?>
                    <span class="rmc-badge"><?php echo esc_html( $cat_name ); ?></span>
                <?php endif; ?>

                <h1 class="rmc-title"><?php the_title(); ?></h1>
                <p class="rmc-desc"><?php echo wp_kses_post( $course_desc_html ); ?></p>

                <div class="rmc-meta-row">
                    <div class="rmc-meta-item">
                        <span class="rmc-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        <span class="rmc-meta-val">5.0</span>
                    </div>
                    <?php if ( $instr_name ) : ?>
                        <div class="rmc-meta-item">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span><?php echo esc_html( $instr_name ); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ( $duration ) : ?>
                        <div class="rmc-meta-item">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span><?php echo esc_html( $duration ); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- CTA Box directly in Hero -->
                <div class="rmc-hero-cta-box">
                    <?php 
                    $packages = get_post_meta($course_id, 'rima_course_packages', true);
                    $has_packages = is_array($packages) && !empty(array_filter($packages));
                    if ( ! $has_packages ) {
                    ?>
                        <div class="rmc-hero-price"><?php echo $price_label; ?></div>
                        <div class="rmc-hero-action">
                            <?php if ( $is_enrolled ) : ?>
                                <a href="<?php echo esc_url( $dashboard_url ); ?>" class="rmc-btn-primary"><?php echo esc_html( $button_text ); ?></a>
                            <?php else : ?>
                                <?php academist_lms_get_buy_form(); ?>
                            <?php endif; ?>
                        </div>
                    <?php } else { ?>
                        <div class="rmc-hero-price">Prețuri / Pachete multiple</div>
                        <div class="rmc-hero-action">
                            <?php if ( $is_enrolled ) : ?>
                                <a href="<?php echo esc_url( $dashboard_url ); ?>" class="rmc-btn-primary"><?php echo esc_html( $button_text ); ?></a>
                            <?php else : ?>
                                <a href="#rima-packages-section" class="rmc-btn-primary" style="background:#8B5CF6;">Alege Pachetul</a>
                            <?php endif; ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="rmc-guarantees">
                    <span><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Lifetime access</span>
                    <span><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg> Secure Payment</span>
                </div>
            </div>

            <!-- Right: Media/Preview -->
            <div class="rmc-hero-media">
                <div class="rmc-media-wrapper">
                    <?php if ( $thumb_url ) : ?>
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
                        <?php if ( $video_preview ) : ?>
                            <div class="rmc-play-overlay" data-video="<?php echo esc_attr( $video_preview ); ?>">
                                <div class="rmc-play-icon">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="var(--rima-primary)" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <div class="rmc-media-placeholder"></div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>

    <!-- ============================================================
         2. MAIN CONTENT AREA (Centered & Clean)
         ============================================================ -->
    <main class="rmc-main-content">
        <div class="rmc-content-inner">
            
            <?php if ( ! empty( $outcomes ) ) : ?>
            <section class="rmc-section">
                <h2 class="rmc-section-title">What You'll Learn</h2>
                <div class="rmc-outcomes-grid">
                    <?php foreach ( $outcomes as $outcome ) : ?>
                        <div class="rmc-outcome-item">
                            <span class="rmc-outcome-icon"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                            <span><?php echo esc_html( $outcome ); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <section class="rmc-section rmc-tabs-section">
                <h2 class="rmc-section-title">Course Information</h2>
                <div class="rmc-tabs-wrapper">
                    <?php academist_lms_get_cpt_single_module_template_part( 'single/parts/tabs', 'course', '', $params ); ?>
                </div>
            </section>

            <?php if ( $instructor_id ) : ?>
            <section class="rmc-section">
                <h2 class="rmc-section-title">Your Instructor</h2>
                <div class="rmc-instructor-box">
                    <?php if ( $instr_img_url ) : ?>
                        <img src="<?php echo esc_url( $instr_img_url ); ?>" alt="<?php echo esc_attr( $instr_name ); ?>" class="rmc-instructor-avatar" />
                    <?php else : ?>
                        <div class="rmc-instructor-avatar-placeholder"><?php echo esc_html( mb_substr( $instr_name, 0, 1 ) ); ?></div>
                    <?php endif; ?>
                    
                    <div class="rmc-instructor-info">
                        <h3><?php echo esc_html( $instr_name ); ?></h3>
                        <?php if ( $instr_title ) : ?><p class="rmc-instructor-title"><?php echo esc_html( $instr_title ); ?></p><?php endif; ?>
                        <?php if ( $instr_bio ) : ?>
                            <div class="rmc-instructor-bio"><?php echo $instr_bio; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- RIMA COURSE PACKAGES -->
            <?php if ( ! $is_enrolled && ! empty($has_packages) && class_exists('WooCommerce') ) : ?>
            <section id="rima-packages-section" class="rmc-section" style="margin-top: 40px; padding-top: 40px; border-top: 1px solid #e5e7eb;">
                <h2 class="rmc-section-title" style="text-align:center; font-size: 2rem; margin-bottom: 2.5rem; color: #111827;">Alege pachetul potrivit pentru tine</h2>
                <div class="rmc-packages-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                    <?php foreach ( array_filter($packages) as $pkg_id ) : 
                        $pkg_product = wc_get_product( $pkg_id );
                        if ( ! $pkg_product || $pkg_product->get_status() !== 'publish' ) continue;
                    ?>
                    <div class="rmc-package-card" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 32px 24px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.04); display: flex; flex-direction: column; transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 30px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.04)';">
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 16px; color: #1f2937;"><?php echo esc_html($pkg_product->get_name()); ?></h3>
                        <div style="font-size: 2.5rem; font-weight: 800; color: #8B5CF6; margin-bottom: 8px;">
                            <?php echo $pkg_product->get_price_html(); ?>
                        </div>
                        <?php if ( $pkg_product->get_short_description() ) : ?>
                        <div style="font-size: 0.95rem; color: #4b5563; margin-bottom: 32px; flex-grow: 1; line-height: 1.5;">
                            <?php echo wp_kses_post($pkg_product->get_short_description()); ?>
                        </div>
                        <?php else : ?>
                        <div style="flex-grow: 1;"></div>
                        <?php endif; ?>
                        
                        <form class="cart" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" method="post" enctype='multipart/form-data'>
                            <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $pkg_product->get_id() ); ?>" class="single_add_to_cart_button button alt" style="width: 100%; background: linear-gradient(135deg, #8B5CF6, #3B82F6); color: #fff; border: none; padding: 14px; border-radius: 10px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: opacity 0.2s ease;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                                <?php echo esc_html( $pkg_product->single_add_to_cart_text() ?: 'Adaugă în coș' ); ?>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

        </div>
    </main>

    <!-- ============================================================
         3. RELATED COURSES
         ============================================================ -->
    <?php if ( $related_query->have_posts() ) : ?>
    <div class="rmc-related-wrap">
        <div class="rmc-related-inner">
            <h2 class="rmc-related-title">You Might Also Like</h2>
            <div class="rmc-related-grid">
                <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
                    <?php
                    $r_id       = get_the_ID();
                    $r_price    = function_exists( 'academist_lms_calculate_course_price' ) ? academist_lms_calculate_course_price( $r_id ) : 0;
                    $r_thumb    = get_the_post_thumbnail_url( $r_id, 'medium' );
                    ?>
                    <article class="rmc-card">
                        <a href="<?php the_permalink(); ?>" class="rmc-card-link">
                            <div class="rmc-card-img">
                                <?php if ( $r_thumb ) : ?>
                                    <img src="<?php echo esc_url( $r_thumb ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
                                <?php endif; ?>
                            </div>
                            <div class="rmc-card-body">
                                <h3><?php the_title(); ?></h3>
                                <div class="rmc-card-price">
                                    <?php 
                                    if ( $r_price > 0 && function_exists( 'get_woocommerce_currency_symbol' ) ) {
                                        echo esc_html( $r_price . ' ' . get_woocommerce_currency_symbol() );
                                    } else { echo 'Free'; }
                                    ?>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ============================================================
     4. SMART STICKY BAR
     ============================================================ -->
<div class="rmc-smart-bar" id="rmc-smart-bar">
    <div class="rmc-smart-bar-inner">
        <div class="rmc-smart-info">
            <h4 class="rmc-smart-title"><?php the_title(); ?></h4>
        </div>
        <div class="rmc-smart-action">
            <?php if ( ! empty($has_packages) ) : ?>
                <div class="rmc-smart-price">Vezi Pachetele</div>
                <?php if ( $is_enrolled ) : ?>
                    <a href="<?php echo esc_url( $dashboard_url ); ?>" class="rmc-btn-primary"><?php echo esc_html( $button_text ); ?></a>
                <?php else : ?>
                    <a href="#rima-packages-section" class="rmc-btn-primary" style="background:#8B5CF6;">Alege Pachetul</a>
                <?php endif; ?>
            <?php else: ?>
                <div class="rmc-smart-price"><?php echo $price_label; ?></div>
                <?php if ( $is_enrolled ) : ?>
                    <a href="<?php echo esc_url( $dashboard_url ); ?>" class="rmc-btn-primary"><?php echo esc_html( $button_text ); ?></a>
                <?php else : ?>
                    <?php academist_lms_get_buy_form(); ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var smartBar = document.getElementById('rmc-smart-bar');
    var hero = document.querySelector('.rmc-hero');
    if (!smartBar || !hero) return;

    window.addEventListener('scroll', function() {
        var heroBottom = hero.getBoundingClientRect().bottom;
        // When hero bottom is out of view (less than 0), show sticky bar
        if (heroBottom < 0) {
            smartBar.classList.add('rmc-visible');
        } else {
            smartBar.classList.remove('rmc-visible');
        }
    }, { passive: true });
});
</script>

<?php get_footer(); ?>
