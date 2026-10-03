<?php
/**
 * Template Name: RIMA Contact Premium
 */
get_header(); ?>

<style>
/* ==========================================================================
   RIMA PREMIUM CONTACT AESTHETICS
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
    padding: 120px 5% 60px;
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
    padding: 40px 5% 120px;
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

/* Contact Form Overrides */
.rima-contact-form input,
.rima-contact-form textarea {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    padding: 16px 20px;
    border-radius: 12px;
    font-family: 'Outfit', sans-serif;
    font-size: 1rem;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}
.rima-contact-form input:focus,
.rima-contact-form textarea:focus {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.3);
    outline: none;
    box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.05);
}
.rima-contact-form label {
    display: block;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 8px;
    font-weight: 500;
}
.rima-contact-btn {
    background: #ffffff;
    color: #030408;
    border: none;
    padding: 16px 32px;
    border-radius: 9999px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.3s ease;
    width: auto;
    display: inline-block;
}
.rima-contact-btn:hover {
    transform: scale(0.97);
}

@media (max-width: 768px) {
    .rima-bento-grid { grid-template-columns: 1fr; }
    .col-span-12, .col-span-8, .col-span-6, .col-span-4 { grid-column: span 1; }
}
</style>

<div class="rima-premium-page">
    <section class="rima-page-hero">
        <span class="rima-eyebrow gsap-fade-up">Get in Touch</span>
        <h1 class="gsap-fade-up" style="transition-delay: 100ms;">Start Your Journey.</h1>
        <p class="gsap-fade-up" style="transition-delay: 200ms;">Have a question about our courses, corporate training, or certification processes? Our advisory team is here to help.</p>
    </section>

    <section class="rima-bento-grid">
        
        <!-- Contact Form (Left) -->
        <div class="rima-card-shell col-span-8 gsap-fade-up">
            <div class="rima-card-core" style="background: radial-gradient(circle at top right, rgba(138, 31, 44, 0.1), transparent 70%);">
                <h2 style="font-size: 2rem; margin-bottom: 32px;">Send us a Message</h2>
                
                <div class="rima-contact-form">
                    <?php 
                        // If Contact Form 7 is installed, use its shortcode, otherwise fallback to HTML
                        if (shortcode_exists('contact-form-7')) {
                            echo do_shortcode('[contact-form-7 title="RIMA Premium Contact"]');
                        } else {
                    ?>
                        <form action="#" method="POST">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div>
                                    <label>First Name</label>
                                    <input type="text" placeholder="John" required>
                                </div>
                                <div>
                                    <label>Last Name</label>
                                    <input type="text" placeholder="Doe" required>
                                </div>
                            </div>
                            <label>Email Address</label>
                            <input type="email" placeholder="john@company.com" required>
                            
                            <label>How can we help?</label>
                            <textarea rows="5" placeholder="Tell us about your learning goals..." required></textarea>
                            
                            <button type="submit" class="rima-contact-btn">Send Message</button>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Contact Info (Right) -->
        <div class="rima-card-shell col-span-4 gsap-fade-up" style="transition-delay: 100ms;">
            <div class="rima-card-core" style="background: linear-gradient(180deg, rgba(255,255,255,0.02) 0%, rgba(255,255,255,0) 100%);">
                <h3 style="font-size: 1.5rem; margin-bottom: 24px;">Direct Contact</h3>
                
                <div style="margin-bottom: 32px;">
                    <span style="font-size: 2rem; margin-bottom: 12px; display: block;">📍</span>
                    <h4 style="font-size: 1.1rem; color: #fff; margin-bottom: 4px;">Headquarters</h4>
                    <p style="color: rgba(255,255,255,0.5); font-size: 0.95rem;">Bucharest, Romania<br>Available Worldwide via Virtual Campus</p>
                </div>

                <div style="margin-bottom: 32px;">
                    <span style="font-size: 2rem; margin-bottom: 12px; display: block;">📧</span>
                    <h4 style="font-size: 1.1rem; color: #fff; margin-bottom: 4px;">Email Support</h4>
                    <p style="color: rgba(255,255,255,0.5); font-size: 0.95rem;">office@rima-academy.com</p>
                </div>

                <div>
                    <span style="font-size: 2rem; margin-bottom: 12px; display: block;">🕒</span>
                    <h4 style="font-size: 1.1rem; color: #fff; margin-bottom: 4px;">Business Hours</h4>
                    <p style="color: rgba(255,255,255,0.5); font-size: 0.95rem;">Mon-Fri: 09:00 - 18:00 (EEST)</p>
                </div>
            </div>
        </div>
        
    </section>
</div>

<script>
window.rimaVueAppsQueue = window.rimaVueAppsQueue || [];
window.rimaVueAppsQueue.push(function() {
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
});
</script>

<?php get_footer(); ?>
