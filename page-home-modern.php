<?php
/**
 * Template Name: RIMA Home Modern (2026)
 * Description: Ultra-modern homepage with Parallax, GSAP, and Glassmorphism.
 */

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

get_header(); ?>

<div id="rima-home-wrapper">

    <!-- 1. HERO SECTION (With 3D Globe & Elearning Theme) -->
    <section class="rhm-hero">
        <div class="rhm-hero-bg">
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1c355e 0%, #040814 100%); z-index: -2;"></div>
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2000&auto=format&fit=crop') center/cover no-repeat; opacity: 0.1; z-index: -1;"></div>
            <style>
                .rhm-globe-wrapper { width: 90vw !important; height: 90vw !important; max-width: 1000px !important; max-height: 1000px !important; top: -49vh !important; left: 0 !important; right: 0 !important; margin: 0 auto !important; transform: none !important; }
                @media (max-width: 1024px) { .rhm-globe-wrapper { width: 110vw !important; height: 110vw !important; top: -42vh !important; } }
                @media (max-width: 768px) { .rhm-globe-wrapper { width: 130vw !important; height: 130vw !important; top: -27vh !important; } }
            </style>
            <div class="rhm-globe-wrapper" style="z-index: 2; opacity: 1;">
                <div id="rhm-globe-viz" style="width: 100%; height: 100%;"></div>
            </div>
            <div class="rhm-hero-vignette" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none; background: radial-gradient(circle at center, transparent 0%, #040814 90%);"></div>
        </div>
        
        <div class="rhm-container rhm-hero-container" style="position: relative; z-index: 10; pointer-events: none;">
            <div class="rhm-hero-content" style="pointer-events: auto;">
                <?php 
                $logo_url = 'https://rima-academy.com/wp-content/uploads/2026/06/light-logo.png';
                ?>
                <div class="rhm-hero-logo-wrapper">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="rhm-hero-logo">
                </div>
                
                <h1 class="rhm-hero-title">
                    Master <br>
                    <span class="rhm-gradient-text rhm-typewriter"><span id="rhm-typed-text"></span><span class="rhm-cursor">|</span></span>
                </h1>
                
                <p class="rhm-hero-p">Unlock global opportunities with native tutors and live interactive sessions.</p>
                
                <div class="rhm-hero-actions">
                    <a href="/our-courses/" class="rhm-glass-btn rhm-btn-primary" data-cursor="-hidden">
                        <span>Start Learning</span>
                    </a>
                    <a href="#how-it-works" class="rhm-glass-btn rhm-btn-secondary">
                        <span>How it works</span>
                    </a>
                </div>
            </div>
            <div class="rhm-hero-visual">
                <!-- Globe is now fully in the background -->
            </div>
        </div>

        <!-- Globe script is dynamically loaded below -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Dynamic Typing Effect
                <?php
                $course_cats = get_terms( array(
                    'taxonomy'   => 'course-category',
                    'hide_empty' => true,
                ) );
                $active_languages = array();
                if ( ! empty( $course_cats ) && ! is_wp_error( $course_cats ) ) {
                    foreach ( $course_cats as $cat ) {
                        $active_languages[] = esc_js( $cat->name );
                    }
                }
                if ( empty( $active_languages ) ) {
                    $active_languages = array("English", "Japanese", "Romanian");
                }
                $js_languages_array = '["' . implode('", "', $active_languages) . '"]';
                ?>
                const words = <?php echo $js_languages_array; ?>;
                let i = 0;
                let timer;
                const typedTextSpan = document.getElementById("rhm-typed-text");
                
                function typingEffect() {
                    let word = words[i].split("");
                    var loopTyping = function() {
                        if (word.length > 0) {
                            typedTextSpan.innerHTML += word.shift();
                        } else {
                            setTimeout(deletingEffect, 2000);
                            return false;
                        }
                        timer = setTimeout(loopTyping, 100);
                    };
                    loopTyping();
                }

                function deletingEffect() {
                    let word = words[i].split("");
                    var loopDeleting = function() {
                        if (word.length > 0) {
                            word.pop();
                            typedTextSpan.innerHTML = word.join("");
                        } else {
                            i = (words.length > (i + 1)) ? ++i : 0;
                            setTimeout(typingEffect, 500);
                            return false;
                        }
                        timer = setTimeout(loopDeleting, 50);
                    };
                    loopDeleting();
                }
                
                typingEffect();

                // Globe Setup
                const globeContainer = document.getElementById('rhm-globe-viz');
                if (globeContainer) {
                    

                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/globe.gl';
                    script.onload = () => {
                        if (typeof Globe === 'undefined') return;
                        

                        const markerData = [
                            { lat: 51.5074, lng: -0.1278, slug: 'english', label: 'English', flag: 'https://flagcdn.com/w40/gb.png', isos: ['GBR'] },
                            { lat: 45.9432, lng: 24.9668, slug: 'romanian', label: 'Romanian', flag: 'https://flagcdn.com/w40/ro.png', isos: ['ROU'] },
                            { lat: 36.2048, lng: 138.2529, slug: 'japanese', label: 'Japanese', flag: 'https://flagcdn.com/w40/jp.png', isos: ['JPN'] }
                        ];

                        const arcsData = markerData.map(d => ({
                            startLat: 51.5074, startLng: -0.1278, // Origin: London
                            endLat: d.lat, endLng: d.lng
                        }));

                        const wrapperEl = document.querySelector('.rhm-globe-wrapper');
                        const wWidth = wrapperEl ? wrapperEl.clientWidth : window.innerWidth / 2;
                        const wHeight = wrapperEl ? wrapperEl.clientHeight : window.innerHeight;

                        const globe = Globe()(globeContainer)
                            .globeImageUrl('https://unpkg.com/three-globe/example/img/earth-dark.jpg')
                            .bumpImageUrl('https://unpkg.com/three-globe/example/img/earth-topology.png')
                            .backgroundColor('rgba(0,0,0,0)')
                            .showAtmosphere(true)
                            .atmosphereColor('#00E5FF')
                            .atmosphereAltitude(0.2)
                            .width(wWidth)
                            .height(wHeight)
                            .polygonCapColor(feat => {
                                const iso = feat.properties.ISO_A3;
                                return markerData.find(m => m.isos.includes(iso)) ? 'rgba(0, 229, 255, 0.4)' : 'rgba(10, 20, 40, 0.8)';
                            })
                            .polygonSideColor(() => 'rgba(0,0,0,0)')
                            .polygonStrokeColor(feat => {
                                const iso = feat.properties.ISO_A3;
                                return markerData.find(m => m.isos.includes(iso)) ? 'rgba(0, 229, 255, 1)' : 'rgba(0, 229, 255, 0.15)';
                            })
                            .polygonAltitude(0.01)
                            .ringsData(markerData)
                            .ringColor(() => '#00E5FF')
                            .ringMaxRadius(7)
                            .ringPropagationSpeed(3)
                            .ringRepeatPeriod(1000)
                            .htmlElementsData(markerData)
                            .htmlElement(d => {
                                const el = document.createElement('div');
                                el.innerHTML = `
                                    <div style="display:flex; align-items:center; background: rgba(10, 15, 30, 0.8); backdrop-filter: blur(4px); padding: 4px 8px; border-radius: 20px; border: 1px solid rgba(0, 229, 255, 0.5); pointer-events: none;">
                                        <img src="${d.flag}" alt="flag" style="width: 20px; height: 14px; border-radius: 2px; margin-right: 6px;" />
                                        <span style="color: #fff; font-size: 12px; font-weight: 500; font-family: Inter, sans-serif;">${d.label}</span>
                                    </div>
                                `;
                                return el;
                            })
                            .arcsData(arcsData)
                            .arcColor(() => 'rgba(0, 229, 255, 0.8)')
                            .arcDashLength(0.4)
                            .arcDashGap(0.2)
                            .arcDashAnimateTime(1500);

                        fetch('https://raw.githubusercontent.com/vasturiano/globe.gl/master/example/datasets/ne_110m_admin_0_countries.geojson')
                            .then(res => res.json())
                            .then(countries => {
                                globe.polygonsData(countries.features);
                            });

                        globe.controls().autoRotate = (window.innerWidth > 768);
                        globe.controls().autoRotateSpeed = 0.8;
                        globe.controls().enableZoom = false; // Disable user interaction for hero

                        globe.pointOfView({ lat: 45, lng: 60, altitude: 2.8 }, 1000);

                        window.addEventListener('resize', () => {
                            if (window.innerWidth > 0) {
                                const newWidth = wrapperEl ? wrapperEl.clientWidth : window.innerWidth / 2;
                                const newHeight = wrapperEl ? wrapperEl.clientHeight : window.innerHeight;
                                globe.width(newWidth);
                                globe.height(newHeight);
                            }
                        });
                    };
                    document.head.appendChild(script);
                }
            });
        </script>    
        <div class="rhm-scroll-indicator">
            <div class="rhm-mouse"></div>
        </div>
    </section>

    <!-- 1.5 TRUSTED BY -->
    <div class="rhm-trusted-by">
        <div class="rhm-container">
            <p>Trusted by learners worldwide to pass official certifications</p>
            <div class="rhm-trusted-logos">
                <span>IELTS</span>
                <span>JLPT</span>
                <span>Cambridge</span>
                <span>TOEFL</span>
                <span>CEFR</span>
            </div>
        </div>
    </div>

    <!-- 2. COURSE CATALOG (3D FLIP CARDS) -->
    <section class="rhm-courses" id="courses">
        <div class="rhm-container">
            <div class="rhm-section-header">
                <h2 class="rhm-h2">Choose Your <span class="rhm-cyan">Language</span>.</h2>
                <p class="rhm-p">Click on a language card to reveal the available course levels.</p>
            </div>
            
            
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
    </section>

    <!-- 3. HOW IT WORKS (The RIMA Pathway) -->
    <section class="rhm-how-it-works" id="how-it-works">
        <div class="rhm-container">
            <div class="rhm-section-header">
                <h2 class="rhm-h2">The <span class="rhm-cyan">RIMA</span> Pathway</h2>
                <p class="rhm-p">A proven step-by-step method to achieve fluency and global certification.</p>
            </div>
            
            <div class="rhm-interactive-pathway">
                <!-- Navigation Tabs -->
                <div class="rhm-pathway-nav">
                    <button class="rhm-pathway-tab active" data-target="pane-1">
                        <span class="step-num">01</span>
                        <span class="step-title">Placement & Assessment</span>
                    </button>
                    <button class="rhm-pathway-tab" data-target="pane-2">
                        <span class="step-num">02</span>
                        <span class="step-title">Choose Your Course</span>
                    </button>
                    <button class="rhm-pathway-tab" data-target="pane-3">
                        <span class="step-num">03</span>
                        <span class="step-title">Live Native Tutors</span>
                    </button>
                    <button class="rhm-pathway-tab" data-target="pane-4">
                        <span class="step-num">04</span>
                        <span class="step-title">Practice & Assignments</span>
                    </button>
                    <button class="rhm-pathway-tab" data-target="pane-5">
                        <span class="step-num">05</span>
                        <span class="step-title">Get Your Certificate</span>
                    </button>
                </div>
                
                <!-- Content Panes -->
                <div class="rhm-pathway-content">
                    
                    <div class="rhm-pathway-pane active" id="pane-1">
                        <h3>Placement & Assessment</h3>
                        <p>Start your journey with a quick assessment. We evaluate your current fluency level to ensure you are placed in the perfect group for maximum growth.</p>
                        <ul>
                            <li>Free initial assessment</li>
                            <li>Personalized learning roadmap</li>
                            <li>Accurate CEFR level placement</li>
                        </ul>
                    </div>

                    <div class="rhm-pathway-pane" id="pane-2">
                        <h3>Choose Your Course</h3>
                        <p>Browse our catalog of expert-curated courses ranging from absolute beginner (A1) to mastery (C2) based on your personal or professional goals.</p>
                        <ul>
                            <li>General Language Courses</li>
                            <li>Business & Corporate Training</li>
                            <li>Exam Preparation (IELTS, JLPT, etc.)</li>
                        </ul>
                    </div>

                    <div class="rhm-pathway-pane" id="pane-3">
                        <h3>Live Native Tutors</h3>
                        <p>Join interactive live sessions directly from our platform. Our certified native tutors guide you through immersive conversations and real-world scenarios.</p>
                        <ul>
                            <li>Interactive Zoom integration</li>
                            <li>Small group or 1-on-1 sessions</li>
                            <li>Learn authentic pronunciation</li>
                        </ul>
                    </div>

                    <div class="rhm-pathway-pane" id="pane-4">
                        <h3>Practice & Assignments</h3>
                        <p>Solidify your knowledge outside of class. Complete interactive quizzes, submit homework, and track your progress in real-time through your student dashboard.</p>
                        <ul>
                            <li>Automated progress tracking</li>
                            <li>Interactive quizzes & exams</li>
                            <li>Detailed feedback from tutors</li>
                        </ul>
                    </div>

                    <div class="rhm-pathway-pane" id="pane-5">
                        <h3>Get Your Certificate</h3>
                        <p>Successfully complete your course modules and pass the final assessment to receive your official certificate of completion from Rima Academy.</p>
                        <ul>
                            <li>Verified Certificate of Completion</li>
                            <li>Add directly to your CV/LinkedIn</li>
                            <li>Proof of your language proficiency</li>
                        </ul>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const tabs = document.querySelectorAll('.rhm-pathway-tab');
                    const panes = document.querySelectorAll('.rhm-pathway-pane');

                    tabs.forEach(tab => {
                        tab.addEventListener('click', () => {
                            // Remove active class from all
                            tabs.forEach(t => t.classList.remove('active'));
                            panes.forEach(p => p.classList.remove('active'));

                            // Add active to clicked
                            tab.classList.add('active');
                            const targetId = tab.getAttribute('data-target');
                            document.getElementById(targetId).classList.add('active');
                        });
                    });
                });
            </script>
        </div>
    </section>

    <!-- 4. PARALLAX MEDIA BREAK -->
    <section class="rhm-parallax-break">
        <?php 
        $parallax_img = get_the_post_thumbnail_url(get_the_ID(), 'full');
        if (!$parallax_img) {
            // Fallback reliable image if no featured image is set (students collaborating/learning)
            $parallax_img = 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2000&auto=format&fit=crop';
        }
        $bg_style = "background-image: url('" . esc_url($parallax_img) . "');";
        ?>
        <div class="rhm-parallax-bg" style="<?php echo $bg_style; ?>"></div>
        <div class="rhm-parallax-overlay">
            <h2 class="rhm-massive-text" data-speed="0.5">FLUENCY IS POWER</h2>
        </div>
    </section>

    <!-- 5. MARQUEE TESTIMONIALS -->
    <section class="rhm-marquee-section">
        <div class="rhm-marquee rhm-marquee-left">
            <div class="rhm-marquee-track">
                <?php foreach($testimonial_items as $item): ?>
                <div class="rhm-testimonial-card">
                    <p class="rhm-testi-text">"<?php echo esc_html($item['text']); ?>"</p>
                    <div class="rhm-testi-author">- <?php echo esc_html($item['author']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="rhm-marquee rhm-marquee-right">
            <div class="rhm-marquee-track">
                <?php 
                $testimonial_reversed = array_reverse($testimonial_items);
                foreach($testimonial_reversed as $item): ?>
                <div class="rhm-testimonial-card">
                    <p class="rhm-testi-text">"<?php echo esc_html($item['text']); ?>"</p>
                    <div class="rhm-testi-author">- <?php echo esc_html($item['author']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 7. FINAL CTA -->
    <section class="rhm-cta">
        <div class="rhm-cta-glow"></div>
        <div class="rhm-cta-content">
            <h2 class="rhm-h2">Your Future Starts Now.</h2>
            <p class="rhm-p">Join a global community of language learners.</p>
            <a href="/my-account/" class="rhm-btn rhm-btn-primary rhm-btn-large magnetic-btn">Create Free Account</a>
        </div>
    </section>

</div>

<?php get_footer(); ?>
<!-- trigger sync -->


