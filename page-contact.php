<?php
/**
 * Template Name: Rima Academy - Contact Page
 *
 * Custom e-learning contact page for Rima Academy.
 * Place this file in the academist-child theme root.
 * In WordPress admin â†’ Pages â†’ Add New â†’ set Page Attributes â†’ Template = "Rima Academy - Contact Page"
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


<div class="rima-contact-page tw-relative tw-w-full tw-min-h-screen tw-bg-transparent tw-font-sans" id="rima-contact-page">

    <!-- GLOBE FIXED BACKGROUND -->
    <div class="rc-globe-wrapper tw-fixed tw-inset-0 tw-w-full tw-h-full tw-z-0 tw-pointer-events-none">
        <div class="tw-absolute tw-inset-0 tw-bg-[radial-gradient(circle_at_center,#0a0b10_0%,#030408_100%)] tw-z-[-2]"></div>
        <div class="tw-absolute tw-inset-0 tw-bg-[radial-gradient(circle_at_center,rgba(225,29,72,0.15)_0%,transparent_60%)] tw-z-[-1]"></div>
        <div id="rc-globe-viz" class="tw-w-full tw-h-full tw-opacity-100 tw-filter tw-brightness-110 tw-contrast-110"></div>
    </div>

    <!-- Background Ambient Glows for 2026 -->
    <div class="tw-fixed tw-top-[-10%] tw-left-[-10%] tw-w-[50%] tw-h-[50%] tw-bg-[#E11D48] tw-rounded-full tw-mix-blend-screen tw-filter tw-blur-[150px] tw-opacity-10 tw-pointer-events-none tw-z-0"></div>
    <div class="tw-fixed tw-bottom-[-10%] tw-right-[-10%] tw-w-[50%] tw-h-[50%] tw-bg-[#12308E] tw-rounded-full tw-mix-blend-screen tw-filter tw-blur-[150px] tw-opacity-10 tw-pointer-events-none tw-z-0"></div>

    <div class="tw-relative tw-z-10 tw-max-w-7xl tw-mx-auto tw-px-4 sm:tw-px-6 tw-pt-32 tw-pb-24">
        
        <!-- HERO SECTION -->
        <section class="tw-text-center tw-mb-24" aria-label="<?php esc_attr_e( 'Contact Hero', 'academist' ); ?>">
            <div class="tw-mb-10 tw-flex tw-justify-center">
                <img src="https://rima-academy.com/wp-content/uploads/2026/06/light-logo.png" alt="Rima Academy" class="tw-h-20 tw-drop-shadow-[0_0_15px_rgba(255,255,255,0.15)]">
            </div>
            <div class="tw-inline-flex tw-items-center tw-gap-2 tw-bg-white/5 tw-px-4 tw-py-1.5 tw-rounded-full tw-border tw-border-white/10 tw-mb-6 tw-text-sm tw-font-medium tw-text-gray-300">
                <i data-lucide="headphones" class="tw-w-4 tw-h-4 tw-text-[#E11D48]"></i>
                <span class="rima-en">We're here to help</span>
                <span class="rima-ro">Suntem aici sa te ajutam</span>
            </div>
            <h1 class="tw-text-5xl md:tw-text-7xl tw-font-display tw-font-extrabold tw-text-white tw-tracking-tight tw-mb-6">
                <span class="rima-en">Get in <span class="tw-text-transparent tw-bg-clip-text tw-bg-gradient-to-r tw-from-[#E11D48] tw-to-[#12308E]">Touch</span></span>
                <span class="rima-ro">Contacteaza <span class="tw-text-transparent tw-bg-clip-text tw-bg-gradient-to-r tw-from-[#E11D48] tw-to-[#12308E]">Echipa</span></span>
            </h1>
            <p class="tw-text-xl tw-text-gray-400 tw-max-w-2xl tw-mx-auto">
                <span class="rima-en">Questions about our courses, memberships, or your learning journey? Our team responds within 24 hours.</span>
                <span class="rima-ro">Întrebari despre cursuri, abonamente sau parcursul tau de înva?are? Echipa noastra raspunde în 24 de ore.</span>
            </p>
        </section>

        <!-- QUICK INFO CARDS (Glassmorphism Grid) -->
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-6 tw-mb-24">
            
            <!-- Email -->
            <a href="mailto:office@rima-academy.com" class="tw-group tw-bg-white/[0.02] tw-border tw-border-white/10 tw-rounded-[2rem] tw-p-8 tw-backdrop-blur-md tw-shadow-2xl hover:tw-bg-white/5 hover:tw-border-white/20 tw-transition-all tw-duration-300">
                <div class="tw-w-14 tw-h-14 tw-rounded-2xl tw-bg-gradient-to-br tw-from-[#12308E]/20 tw-to-[#E11D48]/20 tw-border tw-border-[#12308E]/30 tw-flex tw-items-center tw-justify-center tw-mb-6 group-hover:tw-scale-110 tw-transition-transform tw-duration-300">
                    <i data-lucide="mail" class="tw-w-6 tw-h-6 tw-text-white"></i>
                </div>
                <h4 class="tw-text-lg tw-font-bold tw-text-white tw-mb-2"><span class="rima-en">Email</span><span class="rima-ro">Email</span></h4>
                <p class="tw-text-sm tw-text-gray-400 group-hover:tw-text-[#E11D48] tw-transition-colors">office@rima-academy.com</p>
            </a>

            <!-- WhatsApp -->
            <a href="https://wa.me/40736852666" target="_blank" rel="noopener noreferrer" class="tw-group tw-bg-white/[0.02] tw-border tw-border-white/10 tw-rounded-[2rem] tw-p-8 tw-backdrop-blur-md tw-shadow-2xl hover:tw-bg-white/5 hover:tw-border-[#10B981]/30 tw-transition-all tw-duration-300">
                <div class="tw-w-14 tw-h-14 tw-rounded-2xl tw-bg-[#10B981]/10 tw-border tw-border-[#10B981]/30 tw-flex tw-items-center tw-justify-center tw-mb-6 group-hover:tw-scale-110 tw-transition-transform tw-duration-300">
                    <i data-lucide="message-circle" class="tw-w-6 tw-h-6 tw-text-[#10B981]"></i>
                </div>
                <h4 class="tw-text-lg tw-font-bold tw-text-white tw-mb-2">WhatsApp</h4>
                <p class="tw-text-sm tw-text-gray-400 group-hover:tw-text-[#10B981] tw-transition-colors">+40 736 852 666</p>
            </a>

            <!-- Response Time -->
            <div class="tw-bg-white/[0.02] tw-border tw-border-white/10 tw-rounded-[2rem] tw-p-8 tw-backdrop-blur-md tw-shadow-2xl">
                <div class="tw-w-14 tw-h-14 tw-rounded-2xl tw-bg-[#8B5CF6]/10 tw-border tw-border-[#8B5CF6]/30 tw-flex tw-items-center tw-justify-center tw-mb-6">
                    <i data-lucide="clock" class="tw-w-6 tw-h-6 tw-text-[#8B5CF6]"></i>
                </div>
                <h4 class="tw-text-lg tw-font-bold tw-text-white tw-mb-2"><span class="rima-en">Response Time</span><span class="rima-ro">Timp de raspuns</span></h4>
                <p class="tw-text-sm tw-text-gray-400"><span class="rima-en">Within 24 hours</span><span class="rima-ro">În maxim 24 de ore</span></p>
            </div>

            <!-- Platform -->
            <div class="tw-bg-white/[0.02] tw-border tw-border-white/10 tw-rounded-[2rem] tw-p-8 tw-backdrop-blur-md tw-shadow-2xl">
                <div class="tw-w-14 tw-h-14 tw-rounded-2xl tw-bg-[#E11D48]/10 tw-border tw-border-[#E11D48]/30 tw-flex tw-items-center tw-justify-center tw-mb-6">
                    <i data-lucide="globe" class="tw-w-6 tw-h-6 tw-text-[#E11D48]"></i>
                </div>
                <h4 class="tw-text-lg tw-font-bold tw-text-white tw-mb-2"><span class="rima-en">Platform</span><span class="rima-ro">Platforma</span></h4>
                <p class="tw-text-sm tw-text-gray-400"><span class="rima-en">100% Online</span><span class="rima-ro">100% Online</span></p>
            </div>

        </div>

        <!-- MAIN LAYOUT: Form + Sidebar -->
        <div class="tw-flex tw-flex-col lg:tw-flex-row tw-gap-10 tw-items-start">
            
            <!-- LEFT: Contact Form -->
            <div class="tw-flex-1 tw-w-full">
                <div class="tw-p-1 tw-bg-gradient-to-b tw-from-white/10 tw-to-transparent tw-rounded-[2.5rem]">
                    <div class="tw-bg-[#0B0D14] tw-rounded-[calc(2.5rem-4px)] tw-overflow-hidden tw-border tw-border-white/10 tw-shadow-2xl tw-shadow-black/80">
                        
                        <div class="tw-p-10 tw-border-b tw-border-white/10 tw-bg-white/[0.02]">
                            <h2 class="tw-text-3xl tw-font-bold tw-text-white tw-tracking-tight tw-mb-2">
                                <span class="rima-en">Send us a Message</span>
                                <span class="rima-ro">Trimite-ne un Mesaj</span>
                            </h2>
                            <p class="tw-text-gray-400">
                                <span class="rima-en">Fill in the form below and we'll get back to you as soon as possible.</span>
                                <span class="rima-ro">Completeaza formularul de mai jos ?i î?i vom raspunde în cel mai scurt timp.</span>
                            </p>
                        </div>
                        
                        <div class="tw-p-10">
                            <!-- Topic Selection Pills -->
                            <div class="tw-flex tw-flex-wrap tw-gap-3 tw-mb-10 rc-topic-pills">
                                <button class="rc-topic-pill active tw-px-5 tw-py-2.5 tw-rounded-xl tw-border tw-border-white/10 tw-bg-white/5 tw-text-white tw-text-sm tw-font-medium hover:tw-bg-white/10 tw-transition-colors data-[active=true]:tw-border-[#E11D48] data-[active=true]:tw-bg-[#E11D48]/20" data-en="?? Courses" data-ro="?? Cursuri"><span class="rima-en">?? Courses</span><span class="rima-ro">?? Cursuri</span></button>
                                <button class="rc-topic-pill tw-px-5 tw-py-2.5 tw-rounded-xl tw-border tw-border-white/10 tw-bg-white/5 tw-text-gray-400 tw-text-sm tw-font-medium hover:tw-bg-white/10 hover:tw-text-white tw-transition-colors" data-en="?? Membership" data-ro="?? Abonament"><span class="rima-en">?? Membership</span><span class="rima-ro">?? Abonament</span></button>
                                <button class="rc-topic-pill tw-px-5 tw-py-2.5 tw-rounded-xl tw-border tw-border-white/10 tw-bg-white/5 tw-text-gray-400 tw-text-sm tw-font-medium hover:tw-bg-white/10 hover:tw-text-white tw-transition-colors" data-en="?? Technical" data-ro="?? Tehnic"><span class="rima-en">?? Technical</span><span class="rima-ro">?? Tehnic</span></button>
                                <button class="rc-topic-pill tw-px-5 tw-py-2.5 tw-rounded-xl tw-border tw-border-white/10 tw-bg-white/5 tw-text-gray-400 tw-text-sm tw-font-medium hover:tw-bg-white/10 hover:tw-text-white tw-transition-colors" data-en="?? Partnership" data-ro="?? Parteneriat"><span class="rima-en">?? Partnership</span><span class="rima-ro">?? Parteneriat</span></button>
                            </div>

                            <div class="rc-quform-wrapper 2026-quform-theme" id="rc-quform-area">
                            <?php
                            if ( function_exists( 'quform_render' ) || has_shortcode( get_post_field( 'post_content', get_the_ID() ), 'quform' ) || shortcode_exists( 'quform' ) ) {
                                echo do_shortcode( '[quform id="1" name="Contact"]' );
                            } else {
                                echo '<div class="tw-p-6 tw-bg-rose-500/10 tw-border tw-border-rose-500/20 tw-rounded-2xl tw-text-center tw-text-gray-300">';
                                echo '<span class="rima-en">The contact form is temporarily unavailable. Please email us at <a href="mailto:office@rima-academy.com" class="tw-text-white hover:tw-text-rose-400 tw-underline">office@rima-academy.com</a></span>';
                                echo '<span class="rima-ro">Formularul este temporar indisponibil. Ne po?i scrie la <a href="mailto:office@rima-academy.com" class="tw-text-white hover:tw-text-rose-400 tw-underline">office@rima-academy.com</a></span>';
                                echo '</div>';
                            }
                            ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Sidebar (Sticky) -->
            <div class="tw-w-full lg:tw-w-[420px] tw-space-y-8 tw-sticky tw-top-24">
                
                <!-- FAQ Card -->
                <div class="tw-bg-white/[0.02] tw-border tw-border-white/10 tw-rounded-[2rem] tw-p-8 tw-backdrop-blur-md tw-shadow-2xl">
                    <div class="tw-flex tw-items-center tw-gap-4 tw-mb-8">
                        <div class="tw-w-12 tw-h-12 tw-rounded-xl tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-text-white tw-border tw-border-white/20">
                            <i data-lucide="help-circle" class="tw-w-6 tw-h-6"></i>
                        </div>
                        <div>
                            <h3 class="tw-text-xl tw-font-bold tw-text-white">
                                <span class="rima-en">FAQ</span>
                                <span class="rima-ro">Întrebari Frecvente</span>
                            </h3>
                            <p class="tw-text-xs tw-text-gray-400">
                                <span class="rima-en">Quick answers</span>
                                <span class="rima-ro">Raspunsuri rapide</span>
                            </p>
                        </div>
                    </div>

                    <div class="tw-space-y-4">
                        <?php
                         = array(
                            array(
                                'q_en' => 'How do I access my purchased courses?',
                                'a_en' => 'After purchase, log into your account and navigate to My Account ? My Courses. All your enrolled courses appear there instantly.',
                                'q_ro' => 'Cum accesez cursurile cumparate?',
                                'a_ro' => 'Dupa achizi?ie, conecteaza-te în contul tau ?i acceseaza Contul Meu ? Cursurile Mele.',
                            ),
                            array(
                                'q_en' => 'Can I get a certificate?',
                                'a_en' => 'Absolutely. Upon completing all lessons and passing the final quiz, you receive a downloadable certificate.',
                                'q_ro' => 'Pot ob?ine un certificat?',
                                'a_ro' => 'Absolut. Dupa parcurgerea tuturor lec?iilor ?i promovarea testului final, prime?ti un certificat de finalizare.',
                            ),
                            array(
                                'q_en' => 'What payment methods do you accept?',
                                'a_en' => 'We accept all major credit/debit cards, PayPal, and bank transfers.',
                                'q_ro' => 'Ce metode de plata accepta?i?',
                                'a_ro' => 'Acceptam carduri de credit/debit, PayPal ?i transfer bancar.',
                            ),
                        );
                        foreach (  as  =>  ) :
                        ?>
                        <div class="tw-border tw-border-white/10 tw-rounded-2xl tw-overflow-hidden tw-bg-white/[0.02]">
                            <button class="rc-faq-question tw-w-full tw-text-left tw-px-5 tw-py-4 tw-flex tw-items-center tw-justify-between tw-text-sm tw-font-semibold tw-text-gray-200 hover:tw-text-white hover:tw-bg-white/5 tw-transition-colors">
                                <span>
                                    <span class="rima-en"><?php echo esc_html( ['q_en'] ); ?></span>
                                    <span class="rima-ro"><?php echo esc_html( ['q_ro'] ); ?></span>
                                </span>
                                <i data-lucide="chevron-down" class="tw-w-4 tw-h-4 tw-text-gray-500 tw-transition-transform tw-duration-300"></i>
                            </button>
                            <div class="rc-faq-answer tw-px-5 tw-pb-4 tw-text-xs tw-text-gray-400 tw-leading-relaxed tw-hidden">
                                <div class="tw-pt-2 tw-border-t tw-border-white/10">
                                    <span class="rima-en"><?php echo esc_html( ['a_en'] ); ?></span>
                                    <span class="rima-ro"><?php echo esc_html( ['a_ro'] ); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Social Card -->
                <div class="tw-bg-gradient-to-br tw-from-[#12308E] tw-to-[#E11D48] tw-rounded-[2rem] tw-p-8 tw-shadow-2xl tw-shadow-[#E11D48]/20 tw-text-white tw-relative tw-overflow-hidden">
                    <div class="tw-absolute tw-inset-0 tw-bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] tw-opacity-10 tw-mix-blend-overlay"></div>
                    
                    <h3 class="tw-text-2xl tw-font-bold tw-mb-6 tw-flex tw-items-center tw-gap-3 tw-relative tw-z-10">
                        <i data-lucide="hash" class="tw-w-6 tw-h-6 tw-text-white/70"></i>
                        <span class="rima-en">Follow Us</span>
                        <span class="rima-ro">Urmare?te-ne</span>
                    </h3>
                    
                    <div class="tw-space-y-4 tw-relative tw-z-10">
                        <a href="https://facebook.com/rimaacademy" target="_blank" rel="noopener noreferrer" class="tw-flex tw-items-center tw-gap-4 tw-p-4 tw-rounded-2xl tw-bg-white/10 hover:tw-bg-white/20 tw-transition-colors tw-backdrop-blur-sm">
                            <i data-lucide="facebook" class="tw-w-6 tw-h-6"></i>
                            <div>
                                <div class="tw-font-bold">Facebook</div>
                                <div class="tw-text-xs tw-text-white/70"><span class="rima-en">Updates & News</span><span class="rima-ro">Nouta?i & ?tiri</span></div>
                            </div>
                        </a>
                        <a href="https://instagram.com/rimaacademy" target="_blank" rel="noopener noreferrer" class="tw-flex tw-items-center tw-gap-4 tw-p-4 tw-rounded-2xl tw-bg-white/10 hover:tw-bg-white/20 tw-transition-colors tw-backdrop-blur-sm">
                            <i data-lucide="instagram" class="tw-w-6 tw-h-6"></i>
                            <div>
                                <div class="tw-font-bold">Instagram</div>
                                <div class="tw-text-xs tw-text-white/70"><span class="rima-en">Behind the scenes</span><span class="rima-ro">Din culise</span></div>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- GLOBAL CTA -->
    <section class="tw-relative tw-py-32 tw-text-center tw-overflow-hidden">
        <div class="tw-absolute tw-inset-0 tw-bg-[#0B0D14]/80 tw-backdrop-blur-xl"></div>
        <div class="tw-absolute tw-top-1/2 tw-left-1/2 -tw-translate-x-1/2 -tw-translate-y-1/2 tw-w-[800px] tw-h-[800px] tw-bg-[#E11D48] tw-rounded-full tw-mix-blend-screen tw-filter tw-blur-[200px] tw-opacity-10 tw-pointer-events-none"></div>
        
        <div class="tw-relative tw-z-10 tw-max-w-3xl tw-mx-auto tw-px-6">
            <h2 class="tw-text-5xl md:tw-text-6xl tw-font-display tw-font-bold tw-text-white tw-tracking-tight tw-mb-6">
                Your Future Starts Now.
            </h2>
            <p class="tw-text-xl tw-text-gray-400 tw-mb-10">Join a global community of language learners.</p>
            <a href="/my-account/" class="tw-inline-flex tw-items-center tw-gap-3 tw-bg-white tw-text-[#030408] tw-px-10 tw-py-5 tw-rounded-full tw-font-bold tw-text-lg hover:tw-scale-105 hover:tw-shadow-[0_0_40px_rgba(255,255,255,0.3)] tw-transition-all tw-duration-300">
                Create Free Account
                <i data-lucide="arrow-right" class="tw-w-5 tw-h-5"></i>
            </a>
        </div>
    </section>

</div><!-- /.rima-contact-page -->
<script>
(function($) {
    'use strict';

    /* â”€â”€ Topic pills to Quform integration & Translation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
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
                input.setAttribute('placeholder', isRo ? 'IntroduceÈ›i numele complet' : 'Please enter your full name here.');
            } else if (name.includes('email')) {
                input.setAttribute('placeholder', isRo ? 'IntroduceÈ›i adresa de email' : 'Please enter your e-mail address:');
            } else if (name.includes('phone') || name.includes('tel')) {
                input.setAttribute('placeholder', isRo ? 'IntroduceÈ›i numÄƒrul de telefon' : 'Please enter your phone number:');
            } else if (input.tagName.toLowerCase() === 'textarea') {
                input.setAttribute('placeholder', isRo ? 'ScrieÈ›i mesajul aici...' : 'Write your message here...');
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
                label.innerText = isRo ? 'Mesajul tÄƒu *' : 'Your Message *';
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

    /* â”€â”€ FAQ Accordion â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
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

    /* â”€â”€ Quform is self-handling â€“ no custom AJAX needed â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */

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
            .atmosphereColor('#e62243')
            .atmosphereAltitude(0.25) // slightly thicker atmosphere
            .width(window.innerWidth)
            .height(window.innerHeight)
            .polygonCapColor(feat => {
                const iso = feat.properties.ISO_A3;
                return markerData.find(m => m.isos.includes(iso)) ? 'rgba(230, 34, 67, 0.4)' : 'rgba(10, 20, 40, 0.8)';
            })
            .polygonSideColor(() => 'rgba(0,0,0,0)')
            .polygonStrokeColor(feat => {
                const iso = feat.properties.ISO_A3;
                return markerData.find(m => m.isos.includes(iso)) ? 'rgba(230, 34, 67, 1)' : 'rgba(230, 34, 67, 0.15)';
            })
            .polygonAltitude(0.01)
            .ringsData(markerData)
            .ringColor(() => '#e62243')
            .ringMaxRadius(7)
            .ringPropagationSpeed(3)
            .ringRepeatPeriod(1000)
            .htmlElementsData(markerData)
            .htmlElement(d => {
                const el = document.createElement('div');
                el.innerHTML = `
                    <div style="display:flex; align-items:center; background: rgba(10, 15, 30, 0.8); backdrop-filter: blur(4px); padding: 4px 8px; border-radius: 20px; border: 1px solid rgba(230, 34, 67, 0.5); pointer-events: none;">
                        <img src="${d.flag}" alt="flag" style="width: 20px; height: 14px; border-radius: 2px; margin-right: 6px;" />
                        <span style="color: #fff; font-size: 12px; font-weight: 500; font-family: Inter, sans-serif;">${d.label}</span>
                    </div>
                `;
                return el;
            })
            .arcsData(arcsData)
            .arcColor(() => 'rgba(230, 34, 67, 0.8)')
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
get_footer();
?>



