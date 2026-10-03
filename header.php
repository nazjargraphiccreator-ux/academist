<?php
/**
 * RIMA Academy - Global Header
 * Features: Mobile Friendly, Language Slider, User Avatar Dropdown
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// We include standard wp_head
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <?php do_action( 'academist_elated_action_header_meta' ); wp_head(); ?>
    <!-- VUE 3 & SPA ROUTER (SWUP) INJECTION -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://unpkg.com/swup@4"></script>
    <style>
        /* SPA Transitions */
        html.is-animating .swup-transition-fade {
            opacity: 0;
            transform: translateY(10px);
        }
        .swup-transition-fade {
            transition: opacity 0.3s ease, transform 0.3s ease;
            opacity: 1;
            transform: translateY(0);
        }
        /* Loading Bar */
        .swup-progress-bar {
            height: 3px;
            background-color: #00E5FF;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 99999;
            transition: opacity 0.3s, width 0.3s;
        }
    </style>
    <style>
        /* LANGUAGE SLIDER (Toggle) */
        .rima-lang-slider {
            position: relative;
            width: 72px;
            height: 34px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 99px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 4px;
            margin-right: 16px;
            transition: all 0.3s ease;
        }
        .rima-lang-slider .flag {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            z-index: 2;
            pointer-events: none;
            background-size: cover;
            background-position: center;
        }
        .rima-lang-slider .flag-en { background-image: url('https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg'); }
        .rima-lang-slider .flag-ro { background-image: url('https://upload.wikimedia.org/wikipedia/commons/7/73/Flag_of_Romania.svg'); }
        
        .rima-lang-pill {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 32px;
            height: 28px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 99px;
            z-index: 1;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        body.rima-is-en .rima-lang-pill { transform: translateX(34px); }

        /* USER AVATAR DROPDOWN */
        .rima-user-menu {
            position: relative;
            margin-left: 8px;
        }
        .rima-user-trigger {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 99px;
            padding: 4px 12px 4px 4px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .rima-user-trigger:hover { background: rgba(255, 255, 255, 0.1); }
        .rima-user-trigger img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .rima-user-trigger span {
            color: #fff;
            font-weight: 500;
            font-size: 14px;
        }
        
        .rima-user-dropdown {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: 220px;
            background: rgba(10, 15, 25, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 8px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 999;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }
        .rima-user-menu:hover .rima-user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .rima-user-dropdown a {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            color: rgba(255, 255, 255, 0.8) !important;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s;
            font-size: 14px;
        }
        .rima-user-dropdown a:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff !important;
        }
        .rima-user-dropdown a svg {
            margin-right: 12px;
            opacity: 0.7;
        }
    /* ==========================================================================
   RIMA ACADEMY - HEADER STYLES (PREMIUM)
   ========================================================================== */

/* Header Wrapper */
.rima-site-header {
    background-color: var(--rima-surface-dark, #050810);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    position: sticky;
    top: 0;
    z-index: 9999;
    width: 100%;
    transition: all 0.3s ease;
}

.rima-site-header.is-scrolled {
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.rima-header-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 80px;
}

/* Logo */
.rima-header-logo img {
    height: 40px;
    width: auto;
    object-fit: contain;
}

/* Desktop Navigation */
.rima-header-nav {
    flex-grow: 1;
    display: flex;
    justify-content: center;
}

.rima-header-nav ul {
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
    display: flex;
    gap: 30px;
    align-items: center;
}

.rima-header-nav ul li {
    position: relative;
    list-style: none !important;
    margin: 0 !important;
}

.rima-header-nav ul li a {
    color: var(--rima-text-main, #0f172a);
    font-weight: 600;
    font-size: 15px;
    text-decoration: none;
    transition: color 0.3s ease;
    display: block;
    padding: 10px 0;
}

.rima-header-nav ul li a:hover,
.rima-header-nav ul li.current-menu-item > a {
    color: var(--rima-primary, #00E5FF);
}

/* Tools & CTA (Right Side) */
.rima-header-tools {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* Cart Icon */
.rima-header-cart {
    position: relative;
    color: var(--rima-text-main, #0f172a);
    display: flex;
    align-items: center;
    transition: color 0.3s ease;
}

.rima-header-cart:hover {
    color: var(--rima-primary, #00E5FF);
}

.rima-mob-cart-badge {
    position: absolute;
    top: -8px;
    right: -10px;
    background: var(--rima-accent, #e11d48);
    color: white;
    font-size: 11px;
    font-weight: bold;
    height: 18px;
    width: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

/* Mobile Toggle Button */
.rima-mobile-toggle {
    display: none;
    background: none;
    border: none;
    color: var(--rima-text-main, #0f172a);
    cursor: pointer;
    padding: 0;
}

/* ==========================================================================
   MOBILE DRAWER (OFF-CANVAS)
   ========================================================================== */
.rima-drawer-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.7);
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    backdrop-filter: blur(4px);
}
.rima-drawer-overlay.is-open {
    opacity: 1;
    visibility: visible;
}

.rima-mobile-drawer {
    position: fixed;
    top: 0; right: -100%;
    width: 300px;
    max-width: 85vw;
    height: 100vh;
    background: var(--rima-secondary-dark, #090e17);
    z-index: 10001;
    transition: right 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
    box-shadow: -5px 0 25px rgba(0,0,0,0.1);
}
.rima-mobile-drawer.is-open {
    right: 0;
}

.rima-drawer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.rima-drawer-close {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 5px;
}

.rima-drawer-content {
    flex-grow: 1;
    overflow-y: auto;
    padding: 20px;
}

.rima-drawer-nav ul {
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

.rima-drawer-nav ul li {
    border-bottom: 1px solid rgba(255,255,255,0.05);
    margin: 0 !important;
}

.rima-drawer-nav ul li a {
    color: white;
    font-size: 18px;
    font-weight: 500;
    text-decoration: none;
    display: block;
    padding: 15px 0;
}

.rima-drawer-footer {
    padding: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* RESPONSIVE BREAKPOINTS */
@media (max-width: 1024px) {
    .rima-header-nav,
    .rima-header-tools .rima-btn {
        display: none !important;
    }
    .rima-mobile-toggle {
        display: block;
    }
}

/* ==========================================================================
   HOMEPAGE TRANSPARENT HEADER OVERRIDE
   ========================================================================== */
body.home .rima-site-header {
    position: absolute;
    background-color: transparent;
    border-bottom: none;
}
body.home .rima-site-header.is-scrolled {
    position: fixed;
    background-color: var(--rima-surface-dark, #050810);
    box-shadow: 0 4px 20px rgba(0,0,0,0.4);
}

/* Force white text when at the very top of homepage */
body.home .rima-site-header:not(.is-scrolled) .rima-header-nav ul li a,
body.home .rima-site-header:not(.is-scrolled) .rima-header-cart,
body.home .rima-site-header:not(.is-scrolled) .rima-mobile-toggle,
body.home .rima-site-header:not(.is-scrolled) .rima-header-logo span {
    color: #ffffff !important;
}

</style>
</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
    <div class="eltdf-wrapper">
        <div class="eltdf-wrapper-inner">
            <!-- RIMA GLOBAL HEADER -->
            <header id="rima-global-header" class="rima-site-header">
                <div class="rima-container">
                    <div class="rima-header-inner">
                        <!-- LOGO -->
                        <div class="rima-header-logo">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <?php
                                $logo_url = '';
                                if ( function_exists('academist_elated_options') && academist_elated_options()->getOptionValue('logo_image') ) {
                                    $logo_url = academist_elated_options()->getOptionValue('logo_image');
                                } else {
                                    $custom_logo_id = get_theme_mod( 'custom_logo' );
                                    if($custom_logo_id) {
                                        $logo_img = wp_get_attachment_image_src( $custom_logo_id , 'full' );
                                        if ( $logo_img ) $logo_url = $logo_img[0];
                                    }
                                }

                                if ( $logo_url ) {
                                    echo '<img src="' . esc_url( $logo_url ) . '" alt="Rima Academy Logo">';
                                } else {
                                    echo '<span class="rima-text-logo" style="color:#fff; font-size:26px; font-weight:900; letter-spacing:-1px;">RIMA Academy</span>';
                                }
                                ?>
                            </a>
                        </div>
                        
                        <!-- DESKTOP NAV -->
                        <nav class="rima-header-nav">
                            <?php
                            wp_nav_menu( array(
                                'theme_location' => 'main-navigation',
                                'menu_id'        => 'primary-menu',
                                'fallback_cb'    => false,
                            ) );
                            ?>
                        </nav>

                        <!-- TOOLS & CTA -->
                        <div class="rima-header-tools">
                            <!-- LANGUAGE SLIDER -->
                            <div class="rima-lang-slider" id="rima-lang-toggle" onclick="if(window.RIMA_LANG) window.RIMA_LANG.toggle();">
                                <div class="rima-lang-pill"></div>
                                <div class="flag flag-ro" title="Română"></div>
                                <div class="flag flag-en" title="English"></div>
                            </div>

                            <?php
                            $my_account_link = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : site_url();
                            $login_link = $my_account_link;

                            if ( is_user_logged_in() ) {
                                $current_user = wp_get_current_user();
                                $avatar_url = get_user_meta( $current_user->ID, 'social_profile_image', true ) ?: get_avatar_url( $current_user->ID, array( 'size' => 80 ) );
                                $first_name = $current_user->first_name ?: $current_user->user_login;
                                ?>
                                <!-- AVATAR DROPDOWN -->
                                <div class="rima-user-menu">
                                    <div class="rima-user-trigger">
                                        <img src="<?php echo esc_url($avatar_url); ?>" alt="User Avatar">
                                        <span><?php echo esc_html($first_name); ?></span>
                                    </div>
                                    <div class="rima-user-dropdown">
                                        <a href="<?php echo esc_url($my_account_link); ?>">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                            Dashboard
                                        </a>
                                        <a href="<?php echo esc_url(wc_get_endpoint_url('orders', '', $my_account_link)); ?>">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                                            My Orders
                                        </a>
                                        <a href="<?php echo esc_url(wc_get_endpoint_url('edit-account', '', $my_account_link)); ?>">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                            Settings (2FA)
                                        </a>
                                        <hr style="border-top:1px solid rgba(255,255,255,0.05); margin: 8px 0;">
                                        <a href="<?php echo wp_logout_url( site_url() ); ?>" style="color: #ef4444 !important;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                            Logout
                                        </a>
                                    </div>
                                </div>
                                <?php
                            } else {
                                ?>
                                <a href="<?php echo esc_url($login_link); ?>" class="rima-btn rima-btn-secondary" style="color: white !important; border-color: rgba(255,255,255,0.2);">Login</a>
                                <a href="<?php echo esc_url(site_url('/our-courses/')); ?>" class="rima-btn rima-btn-primary">Get Started</a>
                                <?php
                            }
                            
                            // Cart Icon
                            if ( class_exists( 'WooCommerce' ) ) {
                                $cart_count = WC()->cart->get_cart_contents_count();
                                ?>
                                <a href="#" class="rima-header-cart" id="rima-header-cart-toggle" style="margin-left:16px;">
                                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                    <span class="rima-mob-cart-badge"><?php echo $cart_count; ?></span>
                                </a>
                                <?php
                            }
                            ?>
                            
                            <!-- MOBILE TOGGLE -->
                            <button class="rima-mobile-toggle" id="rima-mobile-menu-btn" aria-label="Menu" style="margin-left: 16px;">
                                <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MOBILE DRAWER -->
            <div class="rima-drawer-overlay" id="rima-mobile-overlay"></div>
            <div class="rima-mobile-drawer" id="rima-mobile-drawer">
                <div class="rima-drawer-header">
                    <?php if ( $logo_url ) { ?>
                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="Rima Logo" style="height:35px;">
                    <?php } else { ?>
                        <span style="color:#fff; font-weight:700;">RIMA</span>
                    <?php } ?>
                    <button class="rima-drawer-close" id="rima-mobile-close">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="rima-drawer-content">
                    <nav class="rima-drawer-nav">
                        <?php
                        wp_nav_menu( array(
                            'theme_location' => 'main-navigation',
                            'fallback_cb'    => false,
                        ) );
                        ?>
                    </nav>
                </div>
                <div class="rima-drawer-footer">
                    <?php if ( !is_user_logged_in() ) : ?>
                        <a href="<?php echo esc_url($login_link); ?>" class="rima-btn rima-btn-secondary" style="color:white!important; border-color:white; width:100%; margin-bottom: 12px;">Login</a>
                        <a href="<?php echo esc_url(site_url('/our-courses/')); ?>" class="rima-btn rima-btn-primary" style="width:100%;">Get Started</a>
                    <?php else: ?>
                        <div style="display:flex; align-items:center; margin-bottom:20px; background:rgba(255,255,255,0.05); padding:12px; border-radius:12px;">
                            <img src="<?php echo esc_url($avatar_url); ?>" alt="Avatar" style="width:40px; border-radius:50%; margin-right:12px;">
                            <div>
                                <h4 style="color:#fff; margin:0; font-size:16px;"><?php echo esc_html($first_name); ?></h4>
                                <a href="<?php echo esc_url($my_account_link); ?>" style="color:rgba(255,255,255,0.6); font-size:12px; text-decoration:none;">View Dashboard</a>
                            </div>
                        </div>
                        <a href="<?php echo wp_logout_url( site_url() ); ?>" class="rima-btn rima-btn-secondary" style="color:#ef4444!important; border-color:rgba(239, 68, 68, 0.3); width:100%;">Logout</a>
                    <?php endif; ?>
                </div>
            </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.innerWidth <= 768) {
        const checkAndStyleBottomBar = () => {
            const elements = document.querySelectorAll('div, nav, ul');
            elements.forEach(el => {
                const style = window.getComputedStyle(el);
                if ((style.position === 'fixed' || style.position === 'sticky') && (style.bottom === '0px' || style.bottom === '0')) {
                    const text = el.innerText.toLowerCase();
                    if (text.includes('home') && text.includes('cart')) {
                        el.style.setProperty('background', '#ffffff', 'important');
                        el.style.setProperty('background-color', '#ffffff', 'important');
                        el.style.setProperty('color', '#000000', 'important');
                        el.style.setProperty('border-top', '1px solid #e2e8f0', 'important');
                        
                        el.querySelectorAll('*').forEach(child => {
                            const cStyle = window.getComputedStyle(child);
                            // If it's a notification bubble (number)
                            if (child.innerText.trim().length <= 2 && !isNaN(child.innerText.trim()) && child.innerText.trim() !== '') {
                                child.style.setProperty('background-color', '#e62243', 'important');
                                child.style.setProperty('color', '#ffffff', 'important');
                            } else {
                                // Text and icons should be black
                                child.style.setProperty('color', '#000000', 'important');
                                
                                // Fix SVG fills/strokes if any
                                if (child.tagName.toLowerCase() === 'svg' || child.tagName.toLowerCase() === 'path' || child.tagName.toLowerCase() === 'circle') {
                                    if (cStyle.fill !== 'none' && cStyle.fill !== 'rgba(0, 0, 0, 0)') {
                                        child.style.setProperty('fill', '#000000', 'important');
                                    }
                                    if (cStyle.stroke !== 'none') {
                                        child.style.setProperty('stroke', '#000000', 'important');
                                    }
                                }
                            }
                        });
                    }
                }
            });
        };
        checkAndStyleBottomBar();
        setTimeout(checkAndStyleBottomBar, 500);
        setTimeout(checkAndStyleBottomBar, 1500);
        setTimeout(checkAndStyleBottomBar, 3000);
    }
});
</script>

            <div class="eltdf-content" <?php if(function_exists('academist_elated_content_elem_style_attr')) academist_elated_content_elem_style_attr(); ?>>
                <div id="swup" class="eltdf-content-inner swup-transition-fade">


