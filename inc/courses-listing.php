<?php
/* ============================================================
   2026 â€” OUR COURSES LISTING: Enqueue & AJAX handler
   ============================================================ */

/**
 * Enqueue assets for Our Courses listing page & single course page
 */
add_action( 'wp_enqueue_scripts', 'rima_enqueue_2026_course_assets', 25 );
function rima_enqueue_2026_course_assets() {
    $theme_uri = get_stylesheet_directory_uri();
    $ver       = wp_get_theme()->get( 'Version' );

    // â”€â”€ Our Courses listing page â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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

    // â”€â”€ Single Course â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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
 * AJAX handler â€” filter / search / sort courses
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

require_once get_stylesheet_directory() . '/inc/custom-shortcodes.php';

