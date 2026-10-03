<?php
/**
 * Template Name: RIMA About Us Premium
 */
get_header(); ?>

<style>
/* ==========================================================================
   RIMA PREMIUM ABOUT US AESTHETICS
   ========================================================================== */
.rima-premium-page {
    background-color: #030408;
    color: #ffffff;
    font-family: 'Outfit', sans-serif;
    overflow-x: hidden;
    position: relative;
    padding-top: 80px;
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
.rima-page-hero {
    position: relative;
    padding: 120px 5% 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}
.rima-page-hero h1 {
    font-size: clamp(3rem, 6vw, 5.5rem);
    background: linear-gradient(135deg, #ffffff 0%, #a0aec0 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 24px;
    font-weight: 800;
}
.rima-page-hero p {
    font-size: clamp(1.1rem, 2vw, 1.35rem);
    color: rgba(255, 255, 255, 0.6);
    max-width: 600px;
    line-height: 1.6;
}
.rima-bento-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 24px;
    padding: 60px 5% 120px;
}
.rima-card-shell {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 32px;
    padding: 8px;
    position: relative;
    overflow: hidden;
}
.rima-card-core {
    background: rgba(255, 255, 255, 0.03);
    box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.1);
    border-radius: 24px;
    padding: 40px;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    z-index: 2;
}
.col-span-12 { grid-column: span 12; }
.col-span-8 { grid-column: span 8; }
.col-span-6 { grid-column: span 6; }
.col-span-4 { grid-column: span 4; }

/* Parallax Image Wrap */
.rima-parallax-img {
    position: absolute;
    top: -20%;
    left: -20%;
    width: 140%;
    height: 140%;
    background-size: cover;
    background-position: center;
    opacity: 0.15;
    z-index: 1;
    transition: transform 0.1s linear;
}
@media (max-width: 768px) {
    .rima-bento-grid { grid-template-columns: 1fr; }
    .col-span-12, .col-span-8, .col-span-6, .col-span-4 { grid-column: span 1; }
}
</style>

<div class="rima-premium-page">
    <section class="rima-page-hero">
        <span class="rima-eyebrow gsap-fade-up">Our Story</span>
        <h1 class="gsap-fade-up" style="transition-delay: 100ms;">Redefining Language<br>Education.</h1>
        <p class="gsap-fade-up" style="transition-delay: 200ms;">RIMA Academy was founded with a singular vision: to bridge global cultures through premium, immersive language learning.</p>
    </section>

    <section class="rima-bento-grid">
        
        <!-- Large Mission Card with Parallax Background -->
        <div class="rima-card-shell col-span-12 gsap-fade-up" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
            <div class="rima-parallax-img" style="background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');"></div>
            <div class="rima-card-core" style="background: rgba(3, 4, 8, 0.7); backdrop-filter: blur(10px); max-width: 800px; margin: 0 auto; text-align: center;">
                <h2 style="font-size: 2.5rem; margin-bottom: 24px;">Our Mission</h2>
                <p style="color: rgba(255,255,255,0.8); font-size: 1.25rem; line-height: 1.6;">We believe that language is the ultimate operating system of humanity. By combining native instructors, cognitive science, and cutting-edge technology, we empower professionals to unlock their global potential.</p>
            </div>
        </div>

        <div class="rima-card-shell col-span-6 gsap-fade-up">
            <div class="rima-card-core" style="background: radial-gradient(circle at top right, rgba(0, 229, 255, 0.1), transparent 70%);">
                <span class="rima-eyebrow">Methodology</span>
                <h3 style="font-size: 2rem; margin-bottom: 16px;">The RIMA Standard</h3>
                <p style="color: rgba(255,255,255,0.6); line-height: 1.6;">We strictly adhere to the CEFR framework, enhanced with our proprietary immersive communication modules. No rote memorization—only practical, real-world fluency.</p>
            </div>
        </div>

        <div class="rima-card-shell col-span-6 gsap-fade-up" style="transition-delay: 100ms;">
            <div class="rima-card-core" style="background: radial-gradient(circle at top right, rgba(230, 34, 67, 0.1), transparent 70%);">
                <span class="rima-eyebrow">Our Instructors</span>
                <h3 style="font-size: 2rem; margin-bottom: 16px;">Elite Native Speakers</h3>
                <p style="color: rgba(255,255,255,0.6); line-height: 1.6;">Less than 2% of applicants pass our rigorous instructor vetting. Every RIMA tutor is certified, experienced, and deeply passionate about cognitive linguistics.</p>
            </div>
        </div>

        <!-- Team Section -->
        <div class="rima-card-shell col-span-4 gsap-fade-up">
            <div class="rima-card-core" style="text-align: center; padding: 60px 40px;">
                <div style="font-size: 3rem; margin-bottom: 24px;">🎯</div>
                <h3 style="font-size: 1.5rem;">Goal-Oriented</h3>
                <p style="color: rgba(255,255,255,0.5); font-size: 0.95rem; margin-top: 12px;">We focus strictly on achieving measurable results for your career and life.</p>
            </div>
        </div>
        <div class="rima-card-shell col-span-4 gsap-fade-up" style="transition-delay: 100ms;">
            <div class="rima-card-core" style="text-align: center; padding: 60px 40px;">
                <div style="font-size: 3rem; margin-bottom: 24px;">🌐</div>
                <h3 style="font-size: 1.5rem;">Fully Remote</h3>
                <p style="color: rgba(255,255,255,0.5); font-size: 0.95rem; margin-top: 12px;">Access the world's best linguistic talent from the comfort of your home.</p>
            </div>
        </div>
        <div class="rima-card-shell col-span-4 gsap-fade-up" style="transition-delay: 200ms;">
            <div class="rima-card-core" style="text-align: center; padding: 60px 40px;">
                <div style="font-size: 3rem; margin-bottom: 24px;">🏆</div>
                <h3 style="font-size: 1.5rem;">Verified Success</h3>
                <p style="color: rgba(255,255,255,0.5); font-size: 0.95rem; margin-top: 12px;">Thousands of students have successfully achieved their targeted CEFR levels.</p>
            </div>
        </div>

    </section>
</div>

<script>
window.rimaVueAppsQueue = window.rimaVueAppsQueue || [];
window.rimaVueAppsQueue.push(function() {
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
        
        // Parallax Effect for Images
        gsap.utils.toArray('.rima-parallax-img').forEach(function(img) {
            gsap.to(img, {
                yPercent: 30,
                ease: "none",
                scrollTrigger: {
                    trigger: img.parentElement,
                    scrub: true
                }
            });
        });

        // Standard Fade Up
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
});
</script>

<?php get_footer(); ?>
