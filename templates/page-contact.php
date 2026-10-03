<?php
/**
 * Template Name: Rima Academy - Contact Page
 *
 * Custom e-learning contact page for Rima Academy.
 * Place this file in the academist-child theme root.
 * In WordPress admin → Pages → Add New → set Page Attributes → Template = "Rima Academy - Contact Page"
 *
 * @package AcademistChild
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Enqueue home css for parallax and CTA sections on this page
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('rima-home-css', get_stylesheet_directory_uri() . '/assets/css/rima-home.css', array(), time());
});

get_header();

// Bilingual helpers
$is_ro = isset( $_COOKIE['rima_lang'] ) ? $_COOKIE['rima_lang'] === 'ro' : false;
if ( is_user_logged_in() ) {
    $meta_lang = get_user_meta( get_current_user_id(), '_rima_lang_pref', true );
    if ( $meta_lang ) $is_ro = ( $meta_lang === 'ro' );
}
?>

<div class="rima-contact-page" id="rima-contact-page">

    <!-- GLOBE FIXED BACKGROUND -->
    <div class="rc-globe-wrapper" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 0; pointer-events: none;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); z-index: -2;"></div>
        <!-- Brighter light overlay -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, rgba(180,21,39,0.15) 0%, transparent 60%); z-index: -1;"></div>
        <div id="rc-globe-viz" style="width: 100%; height: 100%; opacity: 1; filter: brightness(1.2) contrast(1.1);"></div>
    </div>

    <!-- ================================================================
         HERO SECTION
         ================================================================ -->
    <section class="rima-contact-hero" aria-label="<?php esc_attr_e( 'Contact Hero', 'academist' ); ?>">
        <div class="rc-hero-logo" style="margin-bottom: 30px;">
            <img src="https://rima-academy.com/wp-content/uploads/2026/06/light-logo.png" alt="Rima Academy" style="max-height: 90px; filter: drop-shadow(0 0 15px rgba(255,255,255,0.15));">
        </div>
        <div class="rima-contact-hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <span class="rima-en">We're here to help</span>
            <span class="rima-ro">Suntem aici să te ajutăm</span>
        </div>
        <h1>
            <span class="rima-en">Get in <span>Touch</span><br>with Rima Academy</span>
            <span class="rima-ro">Contactează<br><span>Rima Academy</span></span>
        </h1>
        <p>
            <span class="rima-en">Questions about our courses, memberships, or your learning journey?<br>Our team responds within 24 hours.</span>
            <span class="rima-ro">Întrebări despre cursuri, abonamente sau parcursul tău de învățare?<br>Echipa noastră răspunde în 24 de ore.</span>
        </p>
    </section>

    <!-- Wave separator -->
    <div class="rima-contact-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,60 C360,0 1080,80 1440,20 L1440,60 Z" fill="transparent"/>
        </svg>
    </div>

    <!-- QUICK INFO CARDS -->
    <div class="rima-contact-quick-info" role="list">
        <!-- Email -->
        <a href="mailto:office@rima-academy.com" class="rc-info-card" role="listitem" id="rc-contact-email">
            <div class="rc-info-card-icon blue" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
            </div>
            <div class="rc-info-card-body">
                <h4><span class="rima-en">Email</span><span class="rima-ro">Email</span></h4>
                <span class="rc-info-link">office@rima-academy.com</span>
            </div>
        </a>
        <!-- WhatsApp -->
        <a href="https://wa.me/40736852666" target="_blank" rel="noopener noreferrer" class="rc-info-card" role="listitem" id="rc-contact-phone">
            <div class="rc-info-card-icon green" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
            </div>
            <div class="rc-info-card-body">
                <h4><span class="rima-en">WhatsApp</span><span class="rima-ro">WhatsApp</span></h4>
                <span class="rc-info-link">+40 736 852 666</span>
            </div>
        </a>
        <!-- Response -->
        <div class="rc-info-card" role="listitem" id="rc-contact-response">
            <div class="rc-info-card-icon purple" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="rc-info-card-body">
                <h4><span class="rima-en">Response Time</span><span class="rima-ro">Timp de răspuns</span></h4>
                <span class="rc-info-text"><span class="rima-en">Within 24 hours</span><span class="rima-ro">În maxim 24 de ore</span></span>
            </div>
        </div>
        <!-- Platform -->
        <div class="rc-info-card" role="listitem" id="rc-contact-location">
            <div class="rc-info-card-icon red" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </div>
            <div class="rc-info-card-body">
                <h4><span class="rima-en">Platform</span><span class="rima-ro">Platformă</span></h4>
                <span class="rc-info-text"><span class="rima-en">100% Online</span><span class="rima-ro">100% Online</span></span>
            </div>
        </div>
    </div>

    <!-- MAIN GRID: Form + Sidebar -->
    <div class="rima-contact-main">
        <!-- Left: Contact Form -->
        <div class="rima-contact-form-card" id="rc-form-section">
            <div class="rima-contact-form-header">
                <h2>
                    <span class="rima-en">Send us a Message</span>
                    <span class="rima-ro">Trimite-ne un Mesaj</span>
                </h2>
                <p>
                    <span class="rima-en">Fill in the form below and we'll get back to you as soon as possible.</span>
                    <span class="rima-ro">Completează formularul de mai jos și îți vom răspunde în cel mai scurt timp.</span>
                </p>
            </div>
            <div class="rima-contact-form-body">
                <div class="rc-topic-pills" role="list" aria-label="<?php esc_attr_e( 'Message topics', 'academist' ); ?>">
                    <span class="rc-topic-pill active" role="listitem" data-en="📚 Courses" data-ro="📚 Cursuri"><span class="rima-en">📚 Courses</span><span class="rima-ro">📚 Cursuri</span></span>
                    <span class="rc-topic-pill" role="listitem" data-en="👑 Membership" data-ro="👑 Abonament"><span class="rima-en">👑 Membership</span><span class="rima-ro">👑 Abonament</span></span>
                    <span class="rc-topic-pill" role="listitem" data-en="🔧 Technical" data-ro="🔧 Tehnic"><span class="rima-en">🔧 Technical</span><span class="rima-ro">🔧 Tehnic</span></span>
                    <span class="rc-topic-pill" role="listitem" data-en="🤝 Partnership" data-ro="🤝 Parteneriat"><span class="rima-en">🤝 Partnership</span><span class="rima-ro">🤝 Parteneriat</span></span>
                    <span class="rc-topic-pill" role="listitem" data-en="💬 Other" data-ro="💬 Altele"><span class="rima-en">💬 Other</span><span class="rima-ro">💬 Altele</span></span>
                </div>
                <div class="rc-quform-wrapper" id="rc-quform-area">
                <?php
                if ( function_exists( 'quform_render' ) || has_shortcode( get_post_field( 'post_content', get_the_ID() ), 'quform' ) || shortcode_exists( 'quform' ) ) {
                    echo do_shortcode( '[quform id="1" name="Contact"]' );
                } else {
                    echo '<p class="rc-quform-fallback"><em>';
                    echo '<span class="rima-en">The contact form is temporarily unavailable. Please email us at <a href="mailto:office@rima-academy.com">office@rima-academy.com</a></span>';
                    echo '<span class="rima-ro">Formularul este temporar indisponibil. Ne poți scrie la <a href="mailto:office@rima-academy.com">office@rima-academy.com</a></span>';
                    echo '</em></p>';
                }
                ?>
                </div>
            </div>
        </div>

        <!-- Right: Sidebar -->
        <aside class="rima-contact-sidebar" aria-label="<?php esc_attr_e( 'Additional contact information', 'academist' ); ?>">
            <!-- FAQ Accordion -->
            <div class="rc-faq-card">
                <div class="rc-faq-card-header">
                    <h3>
                        <span class="rima-en">Frequently Asked</span>
                        <span class="rima-ro">Întrebări Frecvente</span>
                    </h3>
                    <p>
                        <span class="rima-en">Quick answers to common questions</span>
                        <span class="rima-ro">Răspunsuri rapide la întrebări comune</span>
                    </p>
                </div>
                <?php
                $faqs = array(
                    array(
                        'q_en' => 'How do I access my purchased courses?',
                        'a_en' => 'After purchase, log into your account and navigate to My Account → My Courses. All your enrolled courses appear there instantly.',
                        'q_ro' => 'Cum accesez cursurile cumpărate?',
                        'a_ro' => 'După achiziție, conectează-te în contul tău și accesează Contul Meu → Cursurile Mele. Toate cursurile înscrise apar imediat acolo.',
                    ),
                    array(
                        'q_en' => 'Can I get a certificate after completing a course?',
                        'a_en' => 'Absolutely. Upon completing all lessons and passing the final quiz, you receive a downloadable certificate of completion.',
                        'q_ro' => 'Pot obține un certificat după finalizarea cursului?',
                        'a_ro' => 'Absolut. După parcurgerea tuturor lecțiilor și promovarea testului final, primești un certificat de finalizare descărcabil.',
                    ),
                    array(
                        'q_en' => 'What payment methods do you accept?',
                        'a_en' => 'We accept all major credit/debit cards, PayPal, and bank transfers for Romanian customers.',
                        'q_ro' => 'Ce metode de plată acceptați?',
                        'a_ro' => 'Acceptăm carduri de credit/debit, PayPal și transfer bancar pentru clienții din România.',
                    ),
                );
                foreach ( $faqs as $index => $faq ) :
                    $item_id = 'rc-faq-' . $index;
                    $panel_id = 'rc-faq-panel-' . $index;
                ?>
                <div class="rc-faq-item<?php echo $index === 0 ? ' open' : ''; ?>" id="<?php echo esc_attr( $item_id ); ?>">
                    <button class="rc-faq-question" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $panel_id ); ?>" type="button" id="<?php echo esc_attr( $item_id . '-btn' ); ?>">
                        <span>
                            <span class="rima-en"><?php echo esc_html( $faq['q_en'] ); ?></span>
                            <span class="rima-ro"><?php echo esc_html( $faq['q_ro'] ); ?></span>
                        </span>
                        <span class="rc-faq-chevron" aria-hidden="true">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </span>
                    </button>
                    <div class="rc-faq-answer" id="<?php echo esc_attr( $panel_id ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $item_id . '-btn' ); ?>" <?php echo $index !== 0 ? 'hidden' : ''; ?>>
                        <span class="rima-en"><?php echo esc_html( $faq['a_en'] ); ?></span>
                        <span class="rima-ro"><?php echo esc_html( $faq['a_ro'] ); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Social Links (Previously Hours Card, let's just make it Social Card) -->
            <div class="rc-social-card">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                    </svg>
                    <span class="rima-en">Follow Us</span>
                    <span class="rima-ro">Urmărește-ne</span>
                </h3>
                <div class="rc-social-links-wrap">
                    <a href="https://facebook.com/rimaacademy" target="_blank" rel="noopener noreferrer" class="rc-social-link">
                        <span class="rc-social-link-icon fb" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/></svg>
                        </span>
                        <div class="rc-social-link-text">
                            <strong>Facebook</strong>
                            <span><span class="rima-en">Updates & News</span><span class="rima-ro">Noutăți & Știri</span></span>
                        </div>
                    </a>
                    <a href="https://instagram.com/rimaacademy" target="_blank" rel="noopener noreferrer" class="rc-social-link">
                        <span class="rc-social-link-icon ig" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </span>
                        <div class="rc-social-link-text">
                            <strong>Instagram</strong>
                            <span><span class="rima-en">Behind the scenes</span><span class="rima-ro">Din culise</span></span>
                        </div>
                    </a>
                    <a href="https://www.tiktok.com/@rimaacademy" target="_blank" rel="noopener noreferrer" class="rc-social-link">
                        <span class="rc-social-link-icon tt" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.76a4.85 4.85 0 0 1-1.01-.07z"/></svg>
                        </span>
                        <div class="rc-social-link-text">
                            <strong>TikTok</strong>
                            <span><span class="rima-en">Short tutorials</span><span class="rima-ro">Tutoriale scurte</span></span>
                        </div>
                    </a>
                </div>
            </div>
        </aside>
    </div>
    <!-- PARALLAX MEDIA BREAK (From Home) -->
    <section class="rhm-parallax-break">
        <?php 
        $parallax_img = get_the_post_thumbnail_url(get_the_ID(), 'full');
        if (!$parallax_img) {
            $parallax_img = 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2000&auto=format&fit=crop';
        }
        $bg_style = "background-image: url('" . esc_url($parallax_img) . "');";
        ?>
        <div class="rhm-parallax-bg" style="<?php echo $bg_style; ?>"></div>
        <div class="rhm-parallax-overlay">
            <h2 class="rhm-massive-text rc-massive-text" data-speed="0.5">WE ARE HERE<br>FOR YOU</h2>
        </div>
    </section>

    <!-- FINAL CTA (From Home) -->
    <section class="rhm-cta">
        <div class="rhm-cta-glow"></div>
        <div class="rhm-cta-content">
            <h2 class="rhm-h2">Your Future Starts Now.</h2>
            <p class="rhm-p">Join a global community of language learners.</p>
            <a href="/my-account/" class="rhm-btn rhm-btn-primary rhm-btn-large magnetic-btn">Create Free Account</a>
        </div>
    </section>

</div><!-- /.rima-contact-page -->

<script>
(function($) {
    'use strict';

    /* ── Topic pills to Quform integration & Translation ─────────── */
    const translateQuform = () => {
        const isRo = document.body.classList.contains('rima-lang-ro') || (localStorage.getItem('rima_lang') === 'ro');
        
        // Translate Button
        const submitBtns = document.querySelectorAll('.rc-quform-wrapper .quform-submit .quform-button-text');
        submitBtns.forEach(btn => {
            btn.innerText = isRo ? 'Trimite Mesajul' : 'Send Message';
        });

        // Placeholder translation
        const inputs = document.querySelectorAll('.rc-quform-wrapper input[type="text"], .rc-quform-wrapper input[type="email"], .rc-quform-wrapper input[type="tel"], .rc-quform-wrapper textarea');
        inputs.forEach(input => {
            const name = input.getAttribute('name') || '';
            if (name.includes('name')) {
                input.setAttribute('placeholder', isRo ? 'Introduceți numele complet' : 'Please enter your full name here.');
            } else if (name.includes('email')) {
                input.setAttribute('placeholder', isRo ? 'Introduceți adresa de email' : 'Please enter your e-mail address:');
            } else if (name.includes('phone') || name.includes('tel')) {
                input.setAttribute('placeholder', isRo ? 'Introduceți numărul de telefon' : 'Please enter your phone number:');
            } else if (input.tagName.toLowerCase() === 'textarea') {
                input.setAttribute('placeholder', isRo ? 'Scrieți mesajul aici...' : 'Write your message here...');
            }
        });
        
        // Label translations (if Quform didn't do it)
        const labels = document.querySelectorAll('.rc-quform-wrapper .quform-label > label');
        labels.forEach(label => {
            const txt = label.innerText.toLowerCase();
            if (txt.includes('nume') || txt.includes('name')) {
                label.innerText = isRo ? 'Numele complet *' : 'First and Last name: *';
            } else if (txt.includes('telefon') || txt.includes('phone')) {
                label.innerText = isRo ? 'Telefon *' : 'Phone number: *';
            } else if (txt.includes('email') || txt.includes('e-mail')) {
                label.innerText = isRo ? 'Adresa de email *' : 'Email Address *';
            } else if (txt.includes('mesaj') || txt.includes('message')) {
                label.innerText = isRo ? 'Mesajul tău *' : 'Your Message *';
            } else if (txt.includes('subiect') || txt.includes('subject')) {
                label.innerText = isRo ? 'Subiect' : 'Subject';
            }
        });
    };
    
    // Run initially and whenever body class changes (MutationObserver)
    translateQuform();
    setTimeout(translateQuform, 1000);
    setTimeout(translateQuform, 2500);

    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === "class") {
                translateQuform();
            }
        });
    });
    observer.observe(document.body, { attributes: true });

    const pills = document.querySelectorAll('.rc-topic-pill');
    pills.forEach(pill => {
        pill.addEventListener('click', function() {
            pills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            
            const textarea = document.querySelector('.rc-quform-wrapper .quform-input-textarea textarea');
            if (textarea) {
                const isRo = document.body.classList.contains('rima-lang-ro') || (localStorage.getItem('rima_lang') === 'ro');
                const topicText = isRo ? this.getAttribute('data-ro') : this.getAttribute('data-en');
                let msg = textarea.value;
                msg = msg.replace(/^\[Topic:.*?\]\n\n/, '');
                textarea.value = `[Topic: ${topicText}]\n\n${msg}`;
                
                textarea.style.transition = 'box-shadow 0.3s ease, border-color 0.3s ease';
                textarea.style.borderColor = '#8a1f2c';
                textarea.style.boxShadow = '0 0 0 4px rgba(138, 31, 44, 0.15)';
                setTimeout(() => { 
                    textarea.style.boxShadow = 'none'; 
                    textarea.style.borderColor = '#cbd5e0';
                }, 600);
            }
        });
    });

    // Inject SVG Icons for inputs
    const injectIcons = () => {
        const wrappers = document.querySelectorAll('.quform-input');
        wrappers.forEach(wrapper => {
            if (wrapper.querySelector('.rc-input-icon')) return; // Already injected
            
            const input = wrapper.querySelector('input, textarea');
            if (!input) return;
            
            const name = input.getAttribute('name') || '';
            let iconSvg = '';
            
            if (name.includes('name') || name.includes('nume')) {
                iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>`;
            } else if (name.includes('phone') || name.includes('tel')) {
                iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>`;
            } else if (name.includes('email')) {
                iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>`;
            } else if (input.tagName.toLowerCase() === 'textarea') {
                iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>`;
            }
            
            if (iconSvg) {
                wrapper.style.position = 'relative';
                input.style.paddingLeft = '42px';
                
                const iconSpan = document.createElement('span');
                iconSpan.className = 'rc-input-icon';
                iconSpan.innerHTML = iconSvg;
                iconSpan.style.position = 'absolute';
                iconSpan.style.left = '16px';
                iconSpan.style.top = input.tagName.toLowerCase() === 'textarea' ? '18px' : '16px';
                iconSpan.style.color = '#718096'; // Dark grey for white background
                iconSpan.style.pointerEvents = 'none';
                iconSpan.style.display = 'flex';
                iconSpan.style.zIndex = '5';
                
                wrapper.appendChild(iconSpan);
            }
        });
    };

    /* ── FAQ Accordion ────────────────────────────────────────────── */
    $('.rc-faq-question').on('click', function() {
        var $item = $(this).closest('.rc-faq-item');
        var isOpen = $item.hasClass('open');

        // Close all
        $('.rc-faq-item').removeClass('open')
            .find('.rc-faq-question').attr('aria-expanded', 'false')
            .end()
            .find('.rc-faq-answer').hide().attr('hidden', '');

        // Open clicked
        if ( ! isOpen ) {
            $item.addClass('open');
            $item.find('.rc-faq-question').attr('aria-expanded', 'true');
            $item.find('.rc-faq-answer').removeAttr('hidden').show();
        }
    });

    /* ── Quform is self-handling – no custom AJAX needed ─────────── */

})(jQuery);
</script>

<script src="https://unpkg.com/globe.gl"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Globe Setup for Contact Page
    const globeContainer = document.getElementById('rc-globe-viz');
    if (globeContainer && typeof Globe !== 'undefined') {
        
        // Dynamic Languages based on course categories
        const markerData = <?php echo rima_get_globe_markers_json(); ?>;

        const arcsData = [];
        const origin = markerData[0]; // English/London as origin
        for (let i = 1; i < markerData.length; i++) {
            arcsData.push({
                startLat: origin.lat, startLng: origin.lng,
                endLat: markerData[i].lat, endLng: markerData[i].lng
            });
        }

        const globe = Globe()(globeContainer)
            .globeImageUrl('https://unpkg.com/three-globe/example/img/earth-dark.jpg')
            .bumpImageUrl('https://unpkg.com/three-globe/example/img/earth-topology.png')
            .backgroundColor('rgba(0,0,0,0)')
            .showAtmosphere(true)
            .atmosphereColor('#B41527')
            .atmosphereAltitude(0.25) // slightly thicker atmosphere
            .width(window.innerWidth)
            .height(window.innerHeight)
            .polygonCapColor(feat => {
                const iso = feat.properties.ISO_A3;
                return markerData.find(m => m.isos.includes(iso)) ? 'rgba(180, 21, 39, 0.4)' : 'rgba(10, 20, 40, 0.8)';
            })
            .polygonSideColor(() => 'rgba(0,0,0,0)')
            .polygonStrokeColor(feat => {
                const iso = feat.properties.ISO_A3;
                return markerData.find(m => m.isos.includes(iso)) ? 'rgba(180, 21, 39, 1)' : 'rgba(180, 21, 39, 0.15)';
            })
            .polygonAltitude(0.01)
            .ringsData(markerData)
            .ringColor(() => '#D4AF37')
            .ringMaxRadius(7)
            .ringPropagationSpeed(3)
            .ringRepeatPeriod(1000)
            .htmlElementsData(markerData)
            .htmlElement(d => {
                const el = document.createElement('div');
                el.innerHTML = `
                    <div style="display:flex; align-items:center; background: rgba(10, 15, 30, 0.8); backdrop-filter: blur(4px); padding: 4px 8px; border-radius: 20px; border: 1px solid rgba(212, 175, 55, 0.5); pointer-events: none;">
                        <img src="${d.flag}" alt="flag" style="width: 20px; height: 14px; border-radius: 2px; margin-right: 6px;" />
                        <span style="color: #fff; font-size: 12px; font-weight: 500; font-family: Inter, sans-serif;">${d.label}</span>
                    </div>
                `;
                return el;
            })
            .arcsData(arcsData)
            .arcColor(() => 'rgba(212, 175, 55, 0.8)')
            .arcDashLength(0.4)
            .arcDashGap(0.2)
            .arcDashAnimateTime(1500);

        fetch('https://raw.githubusercontent.com/vasturiano/globe.gl/master/example/datasets/ne_110m_admin_0_countries.geojson')
            .then(res => res.json())
            .then(countries => {
                globe.polygonsData(countries.features);
            });

        globe.controls().autoRotate = true;
        globe.controls().autoRotateSpeed = 0.8;
        globe.controls().enableZoom = false; // Disabled so page can scroll normally

        globe.pointOfView({ lat: 20, lng: 0, altitude: 2.5 }, 1000);

        window.addEventListener('resize', () => {
            if (window.innerWidth > 0) {
                globe.width(window.innerWidth);
                globe.height(window.innerHeight);
            }
        });
    }
});
</script>


<?php
/**
 * Footer — WPBakery Static Block
 *
 * Încearcă să randeze static block-ul cu slug-ul "footer" (sau "footer-block").
 * Dacă WPBakery nu e activ sau block-ul nu există, cade pe footer-ul standard al temei.
 *
 * Creare footer în WP Admin:
 *   WPBakery → Static Blocks → Add New → Slug: "footer"
 *   SAU Pages → Add New (tip: vc_element) cu slug "footer"
 */
$footer_slugs = array( 'footer', 'footer-block', 'rima-footer', 'site-footer' );
$footer_rendered = false;

if ( function_exists( 'rima_static_block' ) ) {
    foreach ( $footer_slugs as $slug ) {
        $block_post = get_page_by_path( $slug, OBJECT, array( 'vc_element', 'page', 'post' ) );
        if ( $block_post && ! empty( $block_post->post_content ) ) {
            echo '<footer class="rima-wpb-footer" role="contentinfo">';
            rima_static_block( $slug );
            echo '</footer>';
            $footer_rendered = true;
            break;
        }
    }
}

// Apelăm întotdeauna get_footer() pentru wp_footer() hooks, scripts și </body></html>
get_footer();
?>

