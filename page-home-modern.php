<?php
/**
 * Template Name: RIMA Home Premium 2026
 * Description: Ultra-modern premium homepage with GSAP, Bento Grid, and 3D Globe.
 */

get_header(); ?>

<!-- RIMA PREMIUM HOME CSS -->
<style>
/* ==========================================================================
   RIMA PREMIUM DESIGN AESTHETICS (High-End Agency Style)
   ========================================================================== */
.rima-premium-home {
    background-color: #030408;
    color: #ffffff;
    font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
    overflow-x: hidden;
    position: relative;
}

.eltdf-content, .eltdf-content-inner { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
/* Typography & Macro Spacing */
.rima-premium-home h1, 
.rima-premium-home h2, 
.rima-premium-home h3 {
    font-family: 'Outfit', sans-serif;
    font-weight: 700;
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.rima-eyebrow {
    display: inline-block;
    padding: 6px 16px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 9999px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    font-weight: 500;
    margin-bottom: 24px;
    color: #a0aec0;
}

/* Fluid Hero Section */
.rima-hero-section {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding: 120px 5% 80px;
    overflow: hidden;
}

.rima-hero-content {
    position: relative;
    z-index: 10;
    max-width: 800px;
}

.rima-hero-title {
    font-size: clamp(3rem, 7vw, 6.5rem);
    background: linear-gradient(135deg, #ffffff 0%, #a0aec0 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 32px;
}

.rima-hero-desc {
    font-size: clamp(1.1rem, 2vw, 1.35rem);
    color: rgba(255, 255, 255, 0.6);
    line-height: 1.6;
    max-width: 600px;
    margin-bottom: 48px;
}

/* 3D Globe Container */
#rima-globe-container {
    background: radial-gradient(circle at center, rgba(138, 31, 44, 0.4) 0%, transparent 70%);
    position: absolute;
    top: 50%;
    right: -10%;
    transform: translateY(-50%);
    width: 60vw;
    height: 60vw;
    max-width: 800px;
    max-height: 800px;
    z-index: 1;
    opacity: 0.8;
    pointer-events: none; /* Let clicks pass through if needed */
}

/* Premium Buttons */
.rima-btn {
    display: inline-flex;
    align-items: center;
    background: #ffffff;
    color: #030408;
    padding: 16px 24px 16px 32px;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 1.1rem;
    text-decoration: none;
    transition: all 0.6s cubic-bezier(0.32, 0.72, 0, 1);
}
.rima-btn:hover {
    transform: scale(0.98);
}
.rima-btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(3, 4, 8, 0.05);
    border-radius: 50%;
    margin-left: 16px;
    transition: transform 0.6s cubic-bezier(0.32, 0.72, 0, 1);
}
.rima-btn:hover .rima-btn-icon {
    transform: translateX(4px) scale(1.05);
}

/* Double-Bezel Architecture (Bento Grid) */
.rima-bento-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 24px;
    padding: 120px 5%;
}

.rima-card-shell {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 32px;
    padding: 8px;
    transition: transform 0.6s cubic-bezier(0.32, 0.72, 0, 1);
}

.rima-card-core {
    background: rgba(255, 255, 255, 0.03);
    box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.1);
    border-radius: 24px;
    padding: 40px;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.rima-card-shell:hover {
    transform: translateY(-8px);
}

.col-span-8 { grid-column: span 8; }
.col-span-4 { grid-column: span 4; }
.col-span-6 { grid-column: span 6; }

@media (max-width: 768px) {
    .rima-bento-grid {
        grid-template-columns: 1fr;
        padding: 60px 5%;
    }
    .col-span-8, .col-span-4, .col-span-6 {
        grid-column: span 1;
    }
    #rima-globe-container {
    background: radial-gradient(circle at center, rgba(138, 31, 44, 0.4) 0%, transparent 70%);
        top: 20%;
        right: -50%;
        width: 150vw;
        height: 150vw;
        opacity: 0.4;
    }
}
</style>

<div class="rima-premium-home">

    <!-- HERO SECTION -->
    <section class="rima-hero-section">
        <div id="rima-globe-container"></div>
        <div class="rima-hero-content gsap-fade-up">
            <span class="rima-eyebrow">The Future of Learning</span>
            <h1 class="rima-hero-title">
                Master 
                <span class="rima-text-gradient-rotate" style="display:inline-block; font-weight:800; min-width: 320px; text-align: left;">
                    <span id="rima-hero-typed">English</span>
                </span><br>
                Unlock the World.
            </h1>
            <p class="rima-hero-desc">Experience an ultra-modern educational journey with AI-driven assessments, native tutors, and an immersive curriculum designed for global success.</p>
            
            <a href="/our-courses/" class="rima-btn group">
                Explore Courses
                <span class="rima-btn-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </span>
            </a>
            
            <div style="margin-top: 24px;">
                <span style="display: inline-flex; align-items: center; background: rgba(230, 34, 67, 0.1); border: 1px solid rgba(230, 34, 67, 0.3); color: #ff4d6d; padding: 6px 14px; border-radius: 9999px; font-size: 0.85rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">
                    <span style="display: inline-block; width: 8px; height: 8px; background: #ff4d6d; border-radius: 50%; margin-right: 8px; box-shadow: 0 0 8px #ff4d6d;"></span>
                    Romanian For Foreigners
                </span>
            </div>
        </div>
    </section>

    <!-- BENTO GRID (APPLE STYLE CARDS) -->
    <section class="rima-bento-grid">
        <!-- Card 1: Live Native Tutors (Large) -->
        <div class="rima-card-shell col-span-8 gsap-fade-up">
            <div class="rima-card-core" style="background: radial-gradient(circle at top right, rgba(138, 31, 44, 0.15), transparent 60%), rgba(255, 255, 255, 0.02);">
                <h3 style="font-size: 2.5rem; margin-bottom: 16px;">Live Native Tutors</h3>
                <p style="color: rgba(255,255,255,0.6); max-width: 400px; margin-bottom: auto; line-height: 1.6;">Join highly interactive live sessions directly through our platform. Native pronunciation, real-time feedback, and immersive conversations to accelerate your fluency.</p>
                
                <div style="margin-top: 40px; display: flex; gap: 16px;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">🌍</div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">🎙️</div>
                </div>
            </div>
        </div>

        <!-- Card 2: Certifications (Medium) -->
        <div class="rima-card-shell col-span-4 gsap-fade-up" style="transition-delay: 100ms;">
            <div class="rima-card-core" style="background: linear-gradient(180deg, rgba(255,255,255,0.02) 0%, rgba(255,255,255,0) 100%);">
                <div style="font-size: 2.5rem; margin-bottom: 24px;">🎓</div>
                <h3 style="font-size: 1.75rem; margin-bottom: 16px;">Verified Certifications</h3>
                <p style="color: rgba(255,255,255,0.6); line-height: 1.6;">Earn verified certificates recognized by institutions worldwide upon completing your courses.</p>
            </div>
        </div>

        <!-- Card 3: For Business (Medium) -->
        <div class="rima-card-shell col-span-4 gsap-fade-up" style="transition-delay: 150ms;">
            <div class="rima-card-core" style="background: rgba(255,255,255,0.02); text-align: center; justify-content: center; align-items: center;">
                <div style="font-size: 3rem; margin-bottom: 24px;">🏢</div>
                <h3 style="font-size: 1.5rem; margin-bottom: 12px;">Corporate Solutions</h3>
                <p style="color: rgba(255,255,255,0.5); font-size: 0.95rem;">Train your entire team with tailored B2B dashboards and automated invoicing.</p>
            </div>
        </div>

        <!-- Card 4: Interactive Dashboard (Large) -->
        <div class="rima-card-shell col-span-8 gsap-fade-up" style="transition-delay: 200ms;">
            <div class="rima-card-core" style="background: radial-gradient(circle at bottom left, rgba(18, 48, 142, 0.15), transparent 60%), rgba(255, 255, 255, 0.02);">
                <span class="rima-eyebrow" style="margin-bottom: 12px;">Platform</span>
                <h3 style="font-size: 2.5rem; margin-bottom: 16px;">AI-Powered Dashboard</h3>
                <p style="color: rgba(255,255,255,0.6); max-width: 450px; line-height: 1.6;">Track your progress, manage assignments, and receive AI-driven recommendations to improve your weak spots automatically.</p>
            </div>
        </div>

        <!-- Featured Courses Dynamic Query -->
        <div class="rima-card-shell col-span-12 gsap-fade-up" style="margin-top: 40px;">
            <div class="rima-card-core" style="padding: 60px 40px; background: rgba(255,255,255,0.01);">
                <span class="rima-eyebrow">Curriculum</span>
                <h2 style="font-size: 3rem; margin-bottom: 40px;">Popular Pathways</h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                    <?php
                    // Fetch top 3 course categories
                    $course_cats = get_terms( array('taxonomy' => 'course-category', 'hide_empty' => true, 'number' => 3) );
                    if ( ! empty( $course_cats ) && ! is_wp_error( $course_cats ) ) {
                        foreach ( $course_cats as $cat ) {
                            echo '<a href="' . esc_url(get_term_link($cat)) . '" style="text-decoration: none; color: inherit; display: block;">';
                            echo '<div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; padding: 32px; transition: transform 0.3s ease, background 0.3s ease;" onmouseover="this.style.background=\'rgba(255,255,255,0.08)\'; this.style.transform=\'translateY(-5px)\';" onmouseout="this.style.background=\'rgba(255,255,255,0.03)\'; this.style.transform=\'translateY(0)\';">';
                            echo '<h4 style="font-size: 1.5rem; margin-bottom: 12px; font-weight: 600;">' . esc_html($cat->name) . '</h4>';
                            echo '<p style="color: rgba(255,255,255,0.5); font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">';
                            echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>';
                            echo esc_html($cat->count) . ' Courses</p>';
                            echo '</div>';
                            echo '</a>';
                        }
                    } else {
                        // Fallback if no categories exist yet
                        echo '<div style="background: rgba(255,255,255,0.03); border-radius: 20px; padding: 32px;"><h4 style="font-size: 1.5rem; margin-bottom: 12px;">Romanian for Foreigners</h4><p style="color: rgba(255,255,255,0.5);">A1 - C2 Levels</p></div>';
                        echo '<div style="background: rgba(255,255,255,0.03); border-radius: 20px; padding: 32px;"><h4 style="font-size: 1.5rem; margin-bottom: 12px;">English for Business</h4><p style="color: rgba(255,255,255,0.5);">Corporate communication</p></div>';
                        echo '<div style="background: rgba(255,255,255,0.03); border-radius: 20px; padding: 32px;"><h4 style="font-size: 1.5rem; margin-bottom: 12px;">Japanese N5-N1</h4><p style="color: rgba(255,255,255,0.5);">JLPT Preparation</p></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- REQUIRED SCRIPTS FOR GSAP & THREE.JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<script>
window.rimaVueAppsQueue = window.rimaVueAppsQueue || [];
window.rimaVueAppsQueue.push(function() {
    // 1. GSAP Scroll Animations
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
        
        gsap.utils.toArray('.gsap-fade-up').forEach(function(elem) {
            gsap.fromTo(elem, 
                { y: 60, opacity: 0, filter: "blur(10px)" },
                { 
                    y: 0, 
                    opacity: 1, 
                    filter: "blur(0px)",
                    duration: 1, 
                    ease: "power3.out",
                    scrollTrigger: {
                        trigger: elem,
                        start: "top 85%",
                    }
                }
            );
        });
    }

    // 2. Realistic SaaS Globe with Connections
    const container = document.getElementById('rima-globe-container');
    if(container && !window.rimaGlobeInitialized) {
        window.rimaGlobeInitialized = true;
        // Dynamically load globe.gl
        if (typeof Globe === 'undefined') {
            const script = document.createElement('script');
            script.src = "https://unpkg.com/globe.gl";
            script.onload = initGlobe;
            document.head.appendChild(script);
        } else {
            initGlobe();
        }
        
        function initGlobe() {
            const arcsData = [
                { startLat: 44.4268, startLng: 26.1025, endLat: 51.5074, endLng: -0.1278, color: '#12308e', name: 'Romanian to English' },
                { startLat: 44.4268, startLng: 26.1025, endLat: 35.6762, endLng: 139.6503, color: '#e62243', name: 'Romanian to Japanese' }
            ];
            
            const labelsData = [
                { lat: 44.4268, lng: 26.1025, text: 'Romanian', size: 1.5, color: 'white' },
                { lat: 51.5074, lng: -0.1278, text: 'English', size: 1.5, color: '#12308e' },
                { lat: 35.6762, lng: 139.6503, text: 'Japanese', size: 1.5, color: '#e62243' }
            ];
            
            const myGlobe = Globe()(container)
                .globeImageUrl('https://unpkg.com/three-globe/example/img/earth-dark.jpg')
                .bumpImageUrl('https://unpkg.com/three-globe/example/img/earth-topology.png')
                .backgroundColor('rgba(0,0,0,0)')
                .arcsData(arcsData)
                .arcColor('color')
                .arcDashLength(0.4)
                .arcDashGap(0.2)
                .arcDashAnimateTime(2000)
                .arcStroke(1.5)
                .labelsData(labelsData)
                .labelLat('lat')
                .labelLng('lng')
                .labelText('text')
                .labelSize('size')
                .labelDotRadius(0.5)
                .labelColor('color')
                .labelResolution(2);
                
            // Setup initial rotation and view
            myGlobe.controls().autoRotate = true;
            myGlobe.controls().autoRotateSpeed = 1.2;
            myGlobe.controls().enableZoom = false;
            myGlobe.pointOfView({ lat: 35, lng: 70, altitude: 2 });
            
            // Match container size
            myGlobe.width(container.clientWidth).height(container.clientHeight);
            
            window.addEventListener('resize', () => {
                myGlobe.width(container.clientWidth).height(container.clientHeight);
            });
        }
    }

    // 3. Typing Effect
    const typedEl = document.getElementById('rima-hero-typed');
    if(typedEl && !window.rimaTypingStarted) {
        window.rimaTypingStarted = true;
        const words = ["English", "Japanese", "Romanian"];
        let i = 0;
        let timer;

        function typingEffect() {
            if (!document.getElementById('rima-hero-typed')) { window.rimaTypingStarted = false; return; }
            let word = words[i].split("");
            var loopTyping = function() {
                if (!document.getElementById('rima-hero-typed')) { window.rimaTypingStarted = false; return; }
                if (word.length > 0) {
                    document.getElementById('rima-hero-typed').innerHTML += word.shift();
                } else {
                    setTimeout(deletingEffect, 2000);
                    return false;
                };
                timer = setTimeout(loopTyping, 150);
            };
            loopTyping();
        };

        function deletingEffect() {
            if (!document.getElementById('rima-hero-typed')) { window.rimaTypingStarted = false; return; }
            let word = words[i].split("");
            var loopDeleting = function() {
                if (!document.getElementById('rima-hero-typed')) { window.rimaTypingStarted = false; return; }
                if (word.length > 0) {
                    word.pop();
                    document.getElementById('rima-hero-typed').innerHTML = word.join("");
                } else {
                    if (words.length > (i + 1)) {
                        i++;
                    } else {
                        i = 0;
                    };
                    typingEffect();
                    return false;
                };
                timer = setTimeout(loopDeleting, 100);
            };
            loopDeleting();
        };
        
        typedEl.innerHTML = "";
        typingEffect();
    }
});
</script>

<?php get_footer(); ?>


