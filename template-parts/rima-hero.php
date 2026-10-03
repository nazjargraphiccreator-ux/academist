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
                    <a href="/our-courses/" class="rima-btn rima-btn-primary" data-cursor="-hidden">
                        <span>Start Learning</span>
                    </a>
                    <a href="#how-it-works" class="rima-btn rima-btn-secondary">
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

    
