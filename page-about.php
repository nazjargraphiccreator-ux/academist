<?php
/**
 * Template Name: Rima Academy – About Page
 *
 * Custom e-learning about page for Rima Academy.
 * Place this file in the academist-child theme root.
 * In WordPress admin → Pages → Add New (or Edit "About Us") → set Page Attributes → Template = "Rima Academy – About Page"
 *
 * @package AcademistChild
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

// Optional bilingual support if you want to translate it later
$is_ro = isset( $_COOKIE['rima_lang'] ) ? $_COOKIE['rima_lang'] === 'ro' : false;
if ( is_user_logged_in() ) {
    $meta_lang = get_user_meta( get_current_user_id(), '_rima_lang_pref', true );
    if ( $meta_lang ) $is_ro = ( $meta_lang === 'ro' );
}

// NOTE: Replace this URL with the actual URL of the uploaded image from Media Library
$rima_image_url = 'https://rima-academy.com/wp-content/uploads/2026/07/rima-about-image.jpg'; 
?>

<div class="rima-about-page" id="rima-about-page">
    
    <!-- Floating Languages Background -->
    <div class="rima-about-floating-bg">
        <div class="rima-bg-gradient-blob"></div>
        <div class="rima-lang-float" data-speed="0.8" style="top: 15%; left: 5%;">日本語</div>
        <div class="rima-lang-float" data-speed="-0.6" style="top: 65%; left: -2%;">Română</div>
        <div class="rima-lang-float" data-speed="1.1" style="top: 25%; right: 2%;">English</div>
        <div class="rima-lang-float" data-speed="-1.2" style="bottom: 10%; right: 5%;">こんにちは</div>
        <div class="rima-lang-float" data-speed="0.5" style="top: 80%; left: 45%;">Salut</div>
        <div class="rima-lang-float" data-speed="0.9" style="bottom: 5%; left: 20%;">Hello</div>
    </div>

    <div class="rima-about-container">
        
        <div class="rima-about-grid">
            
            <!-- Left Side: Image -->
            <div class="rima-about-image-col">
                <div class="rima-sticky-wrapper">
                    <div class="rima-about-image-wrapper">
                        <!-- Put the URL here if you want to hardcode it, or leave the variable -->
                        <img src="<?php echo esc_url($rima_image_url); ?>" alt="Rima" class="rima-about-img">
                        <div class="rima-about-image-decoration"></div>
                    </div>
                    
                    <div class="rima-profile-summary">
                        <h4>Rima</h4>
                        <div class="rima-profile-title">Founder & Language Instructor</div>
                        <div class="rima-profile-credentials">
                            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg> BA in English & Japanese</span>
                            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg> MA in Intercultural Comm. & Translation</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Content -->
            <div class="rima-about-content-col">
                
                <h1 class="rima-about-label" style="font-size:32px; color:#102d56; text-transform:none; letter-spacing:normal; margin-bottom:25px;">About Me</h1>
                
                <div class="rima-about-text">
                    
                    <div class="rima-about-section">
                        <div class="rima-section-header">
                            <span class="rima-section-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg></span>
                            <h3>Hi, I'm Rima!</h3>
                        </div>
                        <p class="rima-intro-text" style="margin-bottom:0 !important; padding-bottom:0; border-bottom:none;">Languages have always fascinated me: the sounds, the stories, and the beautiful ways people connect with each other across the world. My love for Japanese language and culture inspired me to create a space where learning feels warm, clear, and truly enjoyable.</p>
                    </div>
                    
                    <div class="rima-about-section">
                        <div class="rima-section-header">
                            <span class="rima-section-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg></span>
                            <h3>My background</h3>
                        </div>
                        <p>I studied at <strong>Dimitrie Cantemir Christian University</strong>, where I earned a Bachelor's degree in English and Japanese. Those years gave me a solid foundation in both languages, from grammar and vocabulary to literature and culture, and deepened my admiration for Japan and its unique way of seeing the world.</p>
                        <p>I then completed a Master's degree in <strong>Intercultural Communication and Translation</strong>. This programme taught me that translation is far more than replacing words from one language with another. Every expression carries culture, history, and a way of thinking. Today I bring this perspective into every lesson, so my students learn not only what to say, but also <em>why</em> people say it that way.</p>
                    </div>
                    
                    <div class="rima-about-section">
                        <div class="rima-section-header">
                            <span class="rima-section-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></span>
                            <h3>What you can expect in my lessons</h3>
                        </div>
                        <p>I teach Romanian, English, and Japanese, and every course is built on the same ideas:</p>
                        <ul class="rima-premium-list">
                            <li>
                                <div class="rima-list-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                                <div><strong>Real-life language:</strong> whichever language you choose, we practise the phrases you will actually use, such as introducing yourself, ordering food, asking for directions, or making new friends.</div>
                            </li>
                            <li>
                                <div class="rima-list-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                                <div><strong>Culture in every lesson:</strong> you'll discover the customs, traditions, festivals, and everyday etiquette behind each language, from Romanian Mărțișor and English-speaking holidays to Japanese politeness levels, so you understand the people as well as the words.</div>
                            </li>
                            <li>
                                <div class="rima-list-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                                <div><strong>Lessons shaped around you:</strong> your goals, your pace, and your interests guide what we study together, whether you are learning for travel, work, study, or pleasure.</div>
                            </li>
                            <li>
                                <div class="rima-list-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                                <div><strong>Learning through what you love:</strong> music, films, series, books, and anime in Romanian, English, or Japanese can all become part of your practice.</div>
                            </li>
                            <li>
                                <div class="rima-list-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                                <div><strong>A safe space to make mistakes:</strong> mistakes are simply part of learning, in every language, and each one helps you grow.</div>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="rima-about-section">
                        <div class="rima-section-header">
                            <span class="rima-section-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg></span>
                            <h3>My teaching philosophy</h3>
                        </div>
                        <p>For me, learning a new language is never just about words, grammar, or pronunciation. It is about opening doors to new ideas, new friendships, and new ways of understanding the world, and yourself.</p>
                        <p>Through these courses, I want every learner to feel encouraged, motivated, and confident. My approach is patient, practical, and supportive, so that each student can enjoy the process and see real progress, one step at a time.</p>
                    </div>
                    
                    <div class="rima-about-section">
                        <div class="rima-section-header">
                            <span class="rima-section-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg></span>
                            <h3>Let's begin together</h3>
                        </div>
                        <p>Whether you are drawn to Romanian, English, or Japanese by culture, anime, travel, work, study, or simple curiosity, I would be happy to guide you on this journey. Your first step can start today.</p>
                    </div>

                    <div class="rima-premium-motto">
                        <div class="rima-motto-icon-large">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        </div>
                        <div class="rima-motto-content">
                            <div class="rima-motto-jp">継続は力なり</div>
                            <div class="rima-motto-en">Keizoku wa chikara nari</div>
                            <div class="rima-motto-ro">“Continuing is strength. Every small step brings you closer to your goal.”</div>
                        </div>
                    </div>

                </div>



            </div>

        </div>

    </div>
</div>

<style>
/* CSS specific to the About Page */
.rima-about-page {
    position: relative;
    padding: 100px 20px;
    background-color: #f8f9fa;
    overflow: hidden;
    min-height: 80vh;
    display: flex;
    align-items: center;
    font-family: 'Inter', 'Roboto', sans-serif;
}

/* Floating Languages Background */
.rima-about-floating-bg {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    overflow: hidden;
    pointer-events: none;
    z-index: 0;
}
.rima-bg-gradient-blob {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 70vw;
    height: 70vw;
    max-width: 800px;
    max-height: 800px;
    background: radial-gradient(circle, rgba(16, 45, 86, 0.04) 0%, rgba(153, 27, 27, 0.02) 40%, rgba(255, 255, 255, 0) 70%);
    transform: translate(-50%, -50%);
    filter: blur(60px);
}
.rima-lang-float {
    position: absolute;
    font-size: 6vw; /* Responsive */
    font-weight: 900;
    color: rgba(16, 45, 86, 0.04); /* Very subtle watermark */
    white-space: nowrap;
    user-select: none;
    transition: transform 1.2s cubic-bezier(0.2, 0.8, 0.2, 1);
    letter-spacing: -2px;
}
@media (min-width: 1400px) {
    .rima-lang-float { font-size: 90px; }
}
@media (max-width: 768px) {
    .rima-lang-float { font-size: 40px; }
}

.rima-about-container {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.rima-about-grid {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 70px;
    align-items: start;
}

/* Image column */
.rima-about-image-col {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    position: sticky;
    top: 100px; /* Makes the image stay on screen while scrolling the text */
}

.rima-sticky-wrapper {
    position: sticky;
    top: 100px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    width: 100%;
    max-width: 380px;
}

.rima-about-image-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 4 / 5;
    border-radius: 20px;
    z-index: 2;
}

.rima-profile-summary {
    background: #ffffff;
    border-radius: 16px;
    padding: 25px 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    border: 1px solid rgba(16, 45, 86, 0.04);
    text-align: center;
}

.rima-profile-summary h4 {
    font-size: 24px;
    font-weight: 800;
    color: #102d56;
    margin: 0 0 5px 0;
    letter-spacing: -0.5px;
}

.rima-profile-title {
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #dc2626;
    font-weight: 700;
    margin-bottom: 20px;
}

.rima-profile-credentials {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.rima-profile-credentials span {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 14px;
    color: #4a5568;
    font-weight: 500;
}

.rima-profile-credentials svg {
    width: 16px;
    height: 16px;
    color: #102d56;
}

.rima-about-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(16, 45, 86, 0.15);
    position: relative;
    z-index: 2;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.rima-about-img:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(16, 45, 86, 0.2);
}

.rima-about-image-decoration {
    position: absolute;
    top: 20px;
    left: -20px;
    width: 100%;
    height: 100%;
    border: 2px solid #102d56;
    border-radius: 20px;
    z-index: 1;
    opacity: 0.5;
}

/* Content column */
.rima-about-content-col {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.rima-about-label {
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 3px;
    color: #dc2626;
    margin: 0 0 -15px 0;
    font-weight: 700;
}

.rima-about-title {
    font-size: 48px;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0;
    line-height: 1.2;
    letter-spacing: -1px;
}

.rima-about-title span {
    color: #102d56;
}

.rima-intro-text {
    font-size: 19px !important;
    line-height: 1.8;
    color: #1a202c;
    font-weight: 500;
    margin-bottom: 40px !important;
    padding-bottom: 25px;
    border-bottom: 1px solid rgba(16, 45, 86, 0.1);
}

.rima-about-section {
    margin-bottom: 35px;
    background: #ffffff;
    border-radius: 16px;
    padding: 25px 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    border: 1px solid rgba(16, 45, 86, 0.04);
    transition: box-shadow 0.3s ease;
}

.rima-about-section:hover {
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.rima-section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
}

.rima-section-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: #fff0f0;
    color: #dc2626;
    border-radius: 8px;
}

.rima-section-icon svg {
    width: 18px;
    height: 18px;
}

.rima-about-text h3 {
    font-size: 21px;
    font-weight: 800;
    color: #102d56;
    margin: 0;
    letter-spacing: -0.5px;
}

.rima-about-text p {
    font-size: 16px;
    line-height: 1.7;
    color: #4a5568;
    margin: 0 0 16px 0;
}

.rima-about-text p:last-child {
    margin-bottom: 0;
}

/* Premium List */
.rima-premium-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.rima-premium-list li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 15.5px;
    line-height: 1.6;
    color: #4a5568;
}

.rima-list-icon {
    flex-shrink: 0;
    color: #102d56;
    margin-top: 3px;
}

.rima-list-icon svg {
    width: 18px;
    height: 18px;
}

.rima-premium-list li strong {
    color: #1a202c;
    font-weight: 700;
}

/* Premium Motto */
.rima-premium-motto {
    margin-top: 40px;
    background: linear-gradient(135deg, #102d56 0%, #1a365d 100%);
    border-radius: 20px;
    padding: 35px 40px;
    display: flex;
    align-items: center;
    gap: 25px;
    box-shadow: 0 20px 40px rgba(16, 45, 86, 0.2);
    color: #ffffff;
    position: relative;
    overflow: hidden;
}

.rima-premium-motto::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(220, 38, 38, 0.15) 0%, transparent 70%);
    border-radius: 50%;
}

.rima-motto-icon-large {
    flex-shrink: 0;
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(5px);
}

.rima-motto-icon-large svg {
    width: 28px;
    height: 28px;
    color: #fca5a5;
}

.rima-motto-content {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.rima-motto-jp {
    font-size: 26px;
    font-weight: 900;
    letter-spacing: 2px;
    color: #ffffff;
    font-family: 'Noto Sans JP', sans-serif;
}

.rima-motto-en {
    font-size: 14px;
    font-style: italic;
    color: #cbd5e1;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.rima-motto-ro {
    font-size: 16px;
    font-weight: 500;
    color: #f8fafc;
    line-height: 1.5;
}

/* Responsive */
@media (max-width: 991px) {
    .rima-about-grid {
        grid-template-columns: 1fr;
        padding: 40px 30px;
        gap: 40px;
    }
    
    .rima-about-image-col {
        position: relative; /* Disable sticky on mobile */
        top: 0;
    }
    
    .rima-about-title {
        font-size: 38px;
    }
    
    .rima-about-image-wrapper {
        max-width: 350px;
    }
}

@media (max-width: 575px) {
    .rima-about-page {
        padding: 60px 15px;
    }
    .rima-about-grid {
        padding: 30px 20px;
        border-radius: 20px;
    }
    .rima-about-title {
        font-size: 32px;
    }
    .rima-about-text p {
        font-size: 16px;
    }
    .rima-about-motto {
        font-size: 16px;
    }
    .rima-about-motto-wrapper {
        flex-direction: column;
        padding: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const aboutPage = document.querySelector('.rima-about-page');
    const floaters = document.querySelectorAll('.rima-lang-float');
    
    if (aboutPage && floaters.length > 0) {
        aboutPage.addEventListener('mousemove', function(e) {
            const rect = aboutPage.getBoundingClientRect();
            // normalized mouse position: -0.5 to 0.5
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            
            floaters.forEach(el => {
                const speed = parseFloat(el.getAttribute('data-speed'));
                // Subtle translation based on speed and mouse position
                const xMove = x * 150 * speed;
                const yMove = y * 150 * speed;
                // Add a very slight rotation for a 3D feel
                const rot = speed * x * 10; 
                
                el.style.transform = `translate(${xMove}px, ${yMove}px) rotate(${rot}deg)`;
            });
        });
        
        aboutPage.addEventListener('mouseleave', function() {
            floaters.forEach(el => {
                el.style.transform = `translate(0px, 0px) rotate(0deg)`;
            });
        });
    }
});
</script>

<?php
get_footer();
