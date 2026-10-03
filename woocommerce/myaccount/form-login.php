<?php
/**
 * RIMA Academy - Ultra-Modern Login / Register / Lost Password
 * Dark Mode with 3D Globe & Glassmorphism
 * 
 * Matches homepage design language exactly.
 * academist-child/woocommerce/myaccount/form-login.php
 *
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( is_user_logged_in() ) { return; }

// Get logo URL (white version for dark background)
$logo_url = 'https://rima-academy.com/wp-content/uploads/2026/06/light-logo.png';

// Globe image
$globe_img = get_stylesheet_directory_uri() . '/assets/img/hero-parallax-globe.png';

do_action( 'woocommerce_before_customer_login_form' );
?>

<style>
/* ==========================================================================
   RIMA LOGIN PAGE - Dark Mode + Globe + Glassmorphism
   Matches homepage design tokens exactly
   ========================================================================== */

/* Design Tokens (from rima-home.css) */
.rima-login-page {
    --rhm-bg: #040814;
    --rhm-bg-alt: #0a1024;
    --rhm-crimson: #8a1f2c;
    --rhm-crimson-light: #b91c1c;
    --rhm-navy: #1c355e;
    --rhm-text: #A0ABC0;
    --rhm-white: #FFFFFF;
    --rhm-radius: 24px;
    --rhm-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    --glass-bg: rgba(255, 255, 255, 0.03);
    --glass-border: rgba(255, 255, 255, 0.06);
    --glass-blur: 20px;
    --input-bg: rgba(255, 255, 255, 0.06);
    --input-border: rgba(255, 255, 255, 0.08);
    --input-focus-border: rgba(138, 31, 44, 0.6);
    --input-focus-glow: rgba(138, 31, 44, 0.15);
}

/* Force theme header/parent wrappers to dark to prevent white bleed */
body.woocommerce-account,
body.woocommerce-account .eltdf-wrapper,
body.woocommerce-account .eltdf-wrapper-inner,
body.woocommerce-account .eltdf-content,
body.woocommerce-account .eltdf-content .eltdf-content-inner,
body.woocommerce-account .eltdf-full-width,
body.woocommerce-account .eltdf-full-width .eltdf-full-width-inner,
body.woocommerce-account .woocommerce,
body.woocommerce-account .eltdf-page-content-holder,
body.woocommerce-account .eltdf-row-grid-section {
    background: #040814 !important;
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* Ensure no gap below header */
body.woocommerce-account .eltdf-content {
    margin-top: 0 !important;
}

/* Remove default padding from content holder */
body.woocommerce-account .eltdf-page-content-holder {
    padding-bottom: 0 !important;
    padding-top: 0 !important;
}

/* ==========================================================================
   HIDE FOOTER ALWAYS + HIDE THEME HEADER ON MOBILE
   ========================================================================== */
body.woocommerce-account footer,
body.woocommerce-account .eltdf-page-footer,
body.woocommerce-account .lqd-sticky-footer,
body.woocommerce-account [class*="footer"] {
    display: none !important;
}

/* Hide theme header ONLY on mobile (restore it on desktop) */
@media (max-width: 640px) {
    body.woocommerce-account .eltdf-page-header,
    body.woocommerce-account .eltdf-mobile-header,
    body.woocommerce-account header,
    body.woocommerce-account #header,
    body.woocommerce-account .site-header,
    body.woocommerce-account [id*="header"],
    body.woocommerce-account [class*="mobile-nav"],
    body.woocommerce-account .mobile-bottom-nav,
    body.woocommerce-account .footer-nav-area {
        display: none !important;
    }
}

/* Page Full Background */
.rima-login-page {
    position: relative;
    min-height: 100vh;
    background: var(--rhm-bg);
    display: flex;
    font-family: var(--rhm-font);
    /* overflow removed - was preventing position:fixed from working */
    padding: 0 !important;
    margin: 0 !important;
}

/* Immersive Layout Container */
.rima-immersive-layout {
    align-items: center;
    justify-content: center;
    width: 100%;
}

/* Vignette Overlay */
.rima-login-page::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: radial-gradient(ellipse at 50% 50%, transparent 0%, var(--rhm-bg) 90%);
    pointer-events: none;
    z-index: 1;
}

/* ==========================================================================
   FULLSCREEN GLOBE BACKGROUND
   ========================================================================== */

.rima-fullscreen-globe {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100vw;
    height: 100vh;
    opacity: 0.35;
    pointer-events: auto;
    z-index: 0;
    overflow: hidden; /* Contain the canvas here instead */
}

/* ==========================================================================
   BACK BADGE BUTTON
   ========================================================================== */
.rima-back-badge {
    display: inline-flex;
    align-items: center;
    align-self: flex-start;  /* stick to left edge of centered-wrapper */
    gap: 6px;
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    padding: 5px 13px 5px 10px;
    border-radius: 20px;
    text-decoration: none;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    transition: all 0.25s ease;
    margin-bottom: 0.75rem;
}

.rima-back-badge:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.28);
    transform: translateX(-3px);
    text-decoration: none;
}

.rima-back-badge i {
    font-size: 0.7rem;
    opacity: 0.75;
}

/* ==========================================================================
   CENTERED FORM WRAPPER
   ========================================================================== */

.rima-centered-wrapper {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 500px;
    padding: 1.5rem 1rem;
    display: flex;
    flex-direction: column;
    align-items: stretch;
}

/* ==========================================================================
   GLASS CARD
   ========================================================================== */

.rima-lp-card {
    width: 100%;
    background: var(--glass-bg);
    backdrop-filter: blur(var(--glass-blur));
    -webkit-backdrop-filter: blur(var(--glass-blur));
    border: 1px solid var(--glass-border);
    border-radius: var(--rhm-radius);
    overflow: hidden;
    box-shadow:
        0 30px 60px rgba(0, 0, 0, 0.45),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

/* Card Header: Logo + Back Badge */
.rima-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.75rem 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    background: rgba(255, 255, 255, 0.02);
}

.rima-card-logo {
    max-height: 38px;
    width: auto;
    filter: drop-shadow(0 0 10px rgba(255,255,255,0.08));
    flex-shrink: 0;
}

/* Card body padding */
.rima-card-body {
    padding: 2rem 2rem 2.25rem;
}

/* Back Badge - inline in header */
.rima-back-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    padding: 5px 12px 5px 9px;
    border-radius: 20px;
    text-decoration: none;
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    transition: all 0.22s ease;
    white-space: nowrap;
    flex-shrink: 0;
}

.rima-back-badge:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.11);
    border-color: rgba(255, 255, 255, 0.25);
    transform: translateX(-2px);
    text-decoration: none;
}

.rima-back-badge i {
    font-size: 0.68rem;
    opacity: 0.7;
}


/* ==========================================================================
   MOBILE APP-LIKE LAYOUT  (≤ 640px) - AGGRESSIVE OVERRIDES
   ========================================================================== */
@media (max-width: 640px) {

    /* Force full-screen block layout */
    body.woocommerce-account .rima-login-page,
    body.woocommerce-account .rima-login-page.rima-immersive-layout {
        display: block !important;
        min-height: 100vh !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }

    /* Globe: more subtle on mobile */
    body.woocommerce-account .rima-fullscreen-globe {
        opacity: 0.18 !important;
    }

    /* Wrapper: full screen, no max-width */
    body.woocommerce-account .rima-centered-wrapper {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        min-height: 100vh !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        position: relative !important;
        z-index: 2 !important;
    }

    /* Card: full-width, no side borders - native app look */
    body.woocommerce-account .rima-lp-card {
        flex: 1 !important;
        width: 100% !important;
        max-width: 100% !important;
        border-radius: 0 !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom: none !important;
        border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: none !important;
        background: rgba(8, 10, 20, 0.95) !important;
        backdrop-filter: blur(24px) !important;
        -webkit-backdrop-filter: blur(24px) !important;
        margin: 0 !important;
    }

    /* App navbar: sticky top bar */
    body.woocommerce-account .rima-card-header {
        position: sticky !important;
        top: 0 !important;
        z-index: 999 !important;
        padding: 0.8rem 1.25rem !important;
        background: rgba(5, 7, 15, 0.98) !important;
        backdrop-filter: blur(24px) !important;
        -webkit-backdrop-filter: blur(24px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        justify-content: space-between !important;
        display: flex !important;
        align-items: center !important;
    }

    body.woocommerce-account .rima-card-logo {
        max-height: 26px !important;
    }

    /* Compact back badge */
    body.woocommerce-account .rima-back-badge {
        font-size: 0.73rem !important;
        padding: 4px 10px 4px 8px !important;
        gap: 4px !important;
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    /* Card body: generous touch padding */
    body.woocommerce-account .rima-card-body {
        padding: 1.5rem 1.25rem 2rem !important;
    }

    /* Larger inputs for touch */
    body.woocommerce-account .rima-lp-input {
        padding: 0.9rem 1rem !important;
        font-size: 1rem !important;
        border-radius: 10px !important;
    }

    /* Tabs full width */
    body.woocommerce-account .rima-lp-tabs {
        gap: 0 !important;
    }

    body.woocommerce-account .rima-lp-tab {
        font-size: 0.78rem !important;
        padding: 0.65rem 0.5rem !important;
    }

    /* Submit full width */
    body.woocommerce-account .rima-lp-submit {
        width: 100% !important;
        padding: 1rem !important;
        font-size: 1rem !important;
        border-radius: 12px !important;
        margin-top: 0.5rem !important;
    }

    /* No top accent line on mobile */
    body.woocommerce-account .rima-lp-card::before {
        display: none !important;
    }
}


/* Top Accent Line */
.rima-lp-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 2rem;
    right: 2rem;
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--rhm-crimson), transparent);
    border-radius: 0 0 4px 4px;
}

/* ==========================================================================
   TAB PILLS
   ========================================================================== */

.rima-lp-tabs {
    display: flex;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 16px;
    padding: 5px;
    margin-bottom: 2rem;
    list-style: none;
}

.rima-lp-tabs li {
    flex: 1;
}

.rima-lp-tabs .rima-lp-tab {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    padding: 12px 8px;
    border: none;
    background: transparent;
    color: var(--rhm-text);
    font-family: var(--rhm-font);
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.rima-lp-tabs .rima-lp-tab:hover {
    color: var(--rhm-white);
    background: rgba(255, 255, 255, 0.04);
}

.rima-lp-tabs .rima-lp-tab.active {
    background: var(--rhm-crimson);
    color: var(--rhm-white);
    box-shadow: 0 4px 20px rgba(138, 31, 44, 0.35);
}

.rima-lp-tabs .rima-lp-tab i {
    font-size: 0.75rem;
}

/* Hidden Lost Password Tab */
.rima-lp-tab-lost {
    display: none;
}
.rima-lp-tab-lost.visible {
    display: block;
}

/* ==========================================================================
   FORM HEADER
   ========================================================================== */

.rima-lp-form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.rima-lp-form-header h3 {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--rhm-white);
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
}

.rima-lp-form-header p {
    font-size: 0.9rem;
    color: var(--rhm-text);
    margin: 0;
}

/* ==========================================================================
   FORM FIELDS
   ========================================================================== */

.rima-lp-field {
    margin-bottom: 1.5rem;
    position: relative;
}

.rima-lp-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--rhm-text);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.rima-lp-label .required {
    color: var(--rhm-crimson-light);
}

.rima-login-page .woocommerce-form .rima-lp-input,
.rima-login-page .woocommerce-form input[type="text"].rima-lp-input,
.rima-login-page .woocommerce-form input[type="email"].rima-lp-input,
.rima-login-page .woocommerce-form input[type="password"].rima-lp-input,
.rima-login-page .woocommerce-form input[type="number"].rima-lp-input {
    width: 100% !important;
    height: 52px !important;
    padding: 0 1rem !important;
    padding-right: 3rem !important;
    background: var(--input-bg) !important;
    border: 1px solid var(--input-border) !important;
    border-radius: 14px !important;
    color: var(--rhm-white) !important;
    font-family: var(--rhm-font) !important;
    font-size: 0.95rem !important;
    transition: all 0.3s ease !important;
    box-shadow: none !important;
    line-height: normal !important;
    -webkit-appearance: none !important;
    -moz-appearance: textfield !important;
    box-sizing: border-box !important;
}

.rima-login-page .woocommerce-form .rima-lp-input::placeholder {
    color: rgba(160, 171, 192, 0.4) !important;
}

.rima-login-page .woocommerce-form .rima-lp-input:focus {
    background: rgba(255, 255, 255, 0.08) !important;
    border-color: var(--input-focus-border) !important;
    box-shadow: 0 0 0 4px var(--input-focus-glow) !important;
}

/* Field Icon */
.rima-lp-field-icon {
    position: absolute;
    bottom: 18px;
    right: 1.25rem;
    color: rgba(160, 171, 192, 0.4);
    font-size: 0.9rem;
    pointer-events: none;
    transition: color 0.3s ease;
    z-index: 10 !important;
}

.rima-lp-field:focus-within .rima-lp-field-icon {
    color: var(--rhm-crimson);
}

/* Eye Toggle */
.rima-login-page .rima-lp-eye-btn {
    position: absolute !important;
    bottom: 12px !important;
    right: 1rem !important;
    background: transparent !important;
    border: none !important;
    color: rgba(160, 171, 192, 0.5) !important;
    font-size: 1rem !important;
    cursor: pointer !important;
    padding: 4px !important;
    transition: color 0.2s ease !important;
    z-index: 10 !important;
    pointer-events: auto !important;
    width: auto !important;
    height: auto !important;
    min-width: 0 !important;
    min-height: 0 !important;
    box-shadow: none !important;
}

.rima-login-page .rima-lp-eye-btn:hover {
    color: var(--rhm-white) !important;
}

/* ==========================================================================
   BUTTONS
   ========================================================================== */

.rima-lp-btn-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    height: 54px;
    background: linear-gradient(135deg, var(--rhm-crimson) 0%, #a52530 100%);
    color: var(--rhm-white) !important;
    border: none;
    border-radius: 14px;
    font-family: var(--rhm-font);
    font-size: 0.9rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.rima-lp-btn-primary::before {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
    transition: left 0.6s ease;
}

.rima-lp-btn-primary:hover::before {
    left: 100%;
}

.rima-lp-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(138, 31, 44, 0.4);
}

.rima-lp-btn-secondary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    height: 54px;
    background: rgba(28, 53, 94, 0.5);
    color: var(--rhm-white) !important;
    border: 1px solid rgba(28, 53, 94, 0.6);
    border-radius: 14px;
    font-family: var(--rhm-font);
    font-size: 0.9rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.3s ease;
}

.rima-lp-btn-secondary:hover {
    background: rgba(28, 53, 94, 0.7);
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(28, 53, 94, 0.3);
}

/* ==========================================================================
   FORM EXTRAS
   ========================================================================== */

.rima-lp-extras {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.rima-lp-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
}

.rima-lp-checkbox input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--rhm-crimson);
    cursor: pointer;
}

.rima-lp-checkbox label {
    color: var(--rhm-text);
    font-size: 0.85rem;
    cursor: pointer;
    user-select: none;
}

.rima-lp-link {
    color: var(--rhm-crimson-light);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: color 0.2s ease;
}

.rima-lp-link:hover {
    color: #ff6b7a;
    text-decoration: underline;
}

/* ==========================================================================
   TAB CONTENT
   ========================================================================== */

.rima-lp-tab-content {
    display: none;
}

.rima-lp-tab-content.active {
    display: block;
    animation: rima-fade-in 0.3s ease;
}

@keyframes rima-fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ==========================================================================
   PASSWORD STRENGTH
   ========================================================================== */

.rima-lp-strength {
    display: none;
    margin-top: -0.5rem;
    margin-bottom: 1.5rem;
    padding: 0 2px;
}

.rima-lp-strength.show {
    display: block;
}

.rima-lp-strength-bars {
    display: flex;
    gap: 4px;
    height: 4px;
    margin-bottom: 6px;
}

.rima-lp-strength-bar {
    flex: 1;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.08);
    transition: background 0.3s ease;
}

.rima-lp-strength-bar.weak { background: #e74c3c; }
.rima-lp-strength-bar.medium { background: #f39c12; }
.rima-lp-strength-bar.strong { background: #00BFA6; }

.rima-lp-strength-text {
    font-size: 0.7rem;
    font-weight: 600;
    text-align: right;
    color: var(--rhm-text);
}

/* ==========================================================================
   BACK LINK
   ========================================================================== */

.rima-lp-back-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 1.5rem;
}

.rima-lp-back-link a {
    color: var(--rhm-text);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: color 0.2s ease;
}

.rima-lp-back-link a:hover {
    color: var(--rhm-white);
}

/* ==========================================================================
   PRIVACY TEXT
   ========================================================================== */

.rima-lp-privacy {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 0.8rem;
    color: rgba(160, 171, 192, 0.5);
    line-height: 1.6;
}

.rima-lp-privacy a {
    color: var(--rhm-crimson-light);
    text-decoration: none;
    font-weight: 600;
}

.rima-lp-privacy a:hover {
    text-decoration: underline;
}

/* ==========================================================================
   WOOCOMMERCE OVERRIDES
   ========================================================================== */

.rima-login-page .woocommerce-error,
.rima-login-page .woocommerce-info,
.rima-login-page .woocommerce-message {
    background: rgba(138, 31, 44, 0.15);
    border: 1px solid rgba(138, 31, 44, 0.3);
    border-radius: 14px;
    color: var(--rhm-white);
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    font-family: var(--rhm-font);
    font-size: 0.9rem;
}

.rima-login-page .show-password-input,
.rima-login-page .woocommerce-password-toggle,
.rima-login-page button.show-password-input,
.rima-login-page span.show-password-input,
.rima-login-page .show-password,
.rima-login-page .hide-password {
    display: none !important;
    opacity: 0 !important;
    visibility: hidden !important;
}
.rima-login-page span.password-input {
    display: block;
    width: 100%;
}

/* ==========================================================================
   RESPONSIVE
   ========================================================================== */

/* Tablet and below */
@media (max-width: 992px) {
    .rima-login-page {
        align-items: flex-start;
        padding-top: 2rem !important;
    }

    .rima-centered-wrapper {
        padding: 0 1rem;
        padding-bottom: 90px; /* Avoid overlapping with bottom mobile nav */
    }

    /* Hide the immersive logo on mobile because the theme's white mobile header already displays it */
    .rima-immersive-logo {
        display: none !important;
    }

    .rima-lp-card {
        max-width: 100%;
        padding: 2rem 1.25rem; /* Reduced padding to make form more compact */
        border-radius: 16px;
    }
    /* Back Button on Mobile */
    .rima-back-home-btn {
        top: 1rem;
        left: 1rem;
    }
}

/* Small phones */
@media (max-width: 400px) {
    .rima-lp-card {
        padding: 1.5rem 1.25rem;
    }

    .rima-lp-tabs .rima-lp-tab {
        font-size: 0.7rem;
        padding: 10px 6px;
    }

    .rima-lp-tabs .rima-lp-tab i {
        display: none;
    }
}

/* Hide WooCommerce default form-row  */
.rima-login-page .woocommerce-form-login,
.rima-login-page .woocommerce-form-register {
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Force button full-width */
.rima-login-page button[type="submit"],
.rima-login-page button[type="submit"].woocommerce-Button,
.rima-login-page .rima-lp-btn-primary,
.rima-login-page .rima-lp-btn-secondary {
    display: flex !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
}

/* Override any theme heading colors */
.rima-login-page h3,
.rima-login-page h2 {
    color: var(--rhm-white) !important;
}

/* Override footer within page */
.woocommerce-account .eltdf-page-footer,
.woocommerce-account .eltdf-page-footer .eltdf-footer-top-holder {
    display: none !important;
}

/* Language Toggle Styles */
body.rima-lang-ro .rima-en { display: none !important; }
body:not(.rima-lang-ro) .rima-ro { display: none !important; }
</style>

<div class="rima-login-page rima-immersive-layout">

    <!-- FULLSCREEN GLOBE BACKGROUND -->
    <div id="rhm-globe-viz" class="rima-fullscreen-globe"></div>

    <!-- CENTERED IMMERSIVE CONTENT -->
    <div class="rima-centered-wrapper">

        <div class="rima-lp-card">

            <!-- Card Header: Logo + Back Badge -->
            <?php $back_url = wp_get_referer() ? wp_get_referer() : home_url('/'); ?>
            <div class="rima-card-header">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="RIMA Academy" class="rima-card-logo">
                </a>
                <a href="<?php echo esc_url($back_url); ?>" class="rima-back-badge">
                    <i class="fa fa-chevron-left"></i>
                    <span class="rima-en">Back</span>
                    <span class="rima-ro">Înapoi</span>
                </a>
            </div>

            <!-- Card Body -->
            <div class="rima-card-body">

                <!-- Tab Pills -->
                <ul class="rima-lp-tabs" role="tablist">
                    <li>
                        <button class="rima-lp-tab active" data-target="rima-tab-login" type="button" role="tab">
                            <i class="fa fa-sign-in"></i>
                            <span class="rima-en">Login</span>
                            <span class="rima-ro">Login</span>
                        </button>
                    </li>
                    <li>
                        <button class="rima-lp-tab" data-target="rima-tab-register" type="button" role="tab">
                            <i class="fa fa-user-plus"></i>
                            <span class="rima-en">Register</span>
                            <span class="rima-ro">Cont Nou</span>
                        </button>
                    </li>
                    <li class="rima-lp-tab-lost">
                        <button class="rima-lp-tab" data-target="rima-tab-reset" type="button" role="tab">
                            <i class="fa fa-unlock-alt"></i>
                            <span class="rima-en">Reset</span>
                            <span class="rima-ro">Resetare</span>
                        </button>
                    </li>
                </ul>

                <!-- ============ LOGIN TAB ============ -->
                <div class="rima-lp-tab-content active" id="rima-tab-login">
                    <div class="rima-lp-form-header">
                        <h3>
                            <span class="rima-en">Welcome back</span>
                            <span class="rima-ro">Bine ai revenit</span>
                        </h3>
                        <p>
                            <span class="rima-en">Log in to your account to continue</span>
                            <span class="rima-ro">Conecteaza-te la contul tau</span>
                        </p>
                    </div>

                    <form class="woocommerce-form woocommerce-form-login login" method="post">
                        <?php do_action( 'woocommerce_login_form_start' ); ?>

                        <div class="rima-lp-field">
                            <label class="rima-lp-label" for="username">
                                <span class="rima-en">Username or email</span>
                                <span class="rima-ro">Utilizator sau email</span>
                            </label>
                            <input type="text" class="rima-lp-input" name="username" id="username"
                                   autocomplete="username" placeholder="name@example.com"
                                   value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required />
                            <i class="fa fa-user rima-lp-field-icon"></i>
                        </div>

                        <div class="rima-lp-field">
                            <label class="rima-lp-label" for="password">
                                <span class="rima-en">Password</span>
                                <span class="rima-ro">Parola</span>
                            </label>
                            <input class="rima-lp-input" type="password" name="password" id="password"
                                   autocomplete="current-password" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" required />
                            <button type="button" class="rima-lp-eye-btn" aria-label="Toggle password"><i class="fa fa-eye"></i></button>
                        </div>

                        <?php 
                        $num1_login = rand(1, 9);
                        $num2_login = rand(1, 9);
                        $sum_login = $num1_login + $num2_login;
                        $hash_login = md5('rima_math_' . $sum_login);
                        ?>
                        <div class="rima-lp-field">
                            <label class="rima-lp-label" for="rima_math_answer_login">
                                <span class="rima-en">Anti-spam: What is <?php echo $num1_login; ?> + <?php echo $num2_login; ?>?</span>
                                <span class="rima-ro">Anti-spam: Cat fac <?php echo $num1_login; ?> + <?php echo $num2_login; ?>?</span>
                                <span class="required">*</span>
                            </label>
                            <input type="number" class="rima-lp-input" name="rima_math_answer_login"
                                   id="rima_math_answer_login" placeholder="?" required />
                            <i class="fa fa-shield rima-lp-field-icon"></i>
                            <input type="hidden" name="rima_math_hash_login" value="<?php echo esc_attr($hash_login); ?>">
                        </div>

                        <?php do_action( 'woocommerce_login_form' ); ?>

                        <div class="rima-lp-extras">
                            <div class="rima-lp-checkbox">
                                <input name="rememberme" type="checkbox" id="rememberme" value="forever" />
                                <label for="rememberme">
                                    <span class="rima-en">Remember me</span>
                                    <span class="rima-ro">Tine-ma minte</span>
                                </label>
                            </div>
                            <a href="#" class="rima-lp-link rima-lp-lost-trigger">
                                <span class="rima-en">Lost password?</span>
                                <span class="rima-ro">Parola pierduta?</span>
                            </a>
                        </div>

                        <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                        <input type="hidden" name="redirect" value="<?php echo esc_url( wc_get_page_permalink('myaccount') ); ?>" />

                        <button type="submit" class="woocommerce-button button woocommerce-form-login__submit rima-lp-btn-primary" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>">
                            <span class="rima-en">Login</span>
                            <span class="rima-ro">Autentificare</span>
                            <i class="fa fa-arrow-right"></i>
                        </button>

                        <?php do_action( 'woocommerce_login_form_end' ); ?>
                    </form>
                </div>

                <!-- ============ REGISTER TAB ============ -->
                <div class="rima-lp-tab-content" id="rima-tab-register">
                    <div class="rima-lp-form-header">
                        <h3>
                            <span class="rima-en">Join RIMA Academy</span>
                            <span class="rima-ro">Alatura-te RIMA Academy</span>
                        </h3>
                        <p>
                            <span class="rima-en">Create your account in seconds</span>
                            <span class="rima-ro">Creeaza-ti contul in cateva secunde</span>
                        </p>
                    </div>

                    <form method="post" class="woocommerce-form woocommerce-form-register register">
                        <?php do_action( 'woocommerce_register_form_start' ); ?>

                        <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
                        <div class="rima-lp-field">
                            <label class="rima-lp-label" for="reg_username">
                                <span class="rima-en">Username</span>
                                <span class="rima-ro">Utilizator</span>
                                <span class="required">*</span>
                            </label>
                            <input type="text" class="rima-lp-input" name="username" id="reg_username"
                                   autocomplete="username" placeholder="Username"
                                   value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required />
                            <i class="fa fa-user rima-lp-field-icon"></i>
                        </div>
                        <?php endif; ?>

                        <div class="rima-lp-field">
                            <label class="rima-lp-label" for="reg_email">
                                <span class="rima-en">Email address</span>
                                <span class="rima-ro">Adresa de email</span>
                                <span class="required">*</span>
                            </label>
                            <input type="email" class="rima-lp-input" name="email" id="reg_email"
                                   autocomplete="email" placeholder="name@example.com"
                                   value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required />
                            <i class="fa fa-envelope rima-lp-field-icon"></i>
                        </div>

                        <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
                        <div class="rima-lp-field">
                            <label class="rima-lp-label" for="reg_password">
                                <span class="rima-en">Password</span>
                                <span class="rima-ro">Parola</span>
                                <span class="required">*</span>
                            </label>
                            <input type="password" class="rima-lp-input" name="password" id="reg_password"
                                   autocomplete="new-password" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" required />
                            <button type="button" class="rima-lp-eye-btn" aria-label="Toggle password"><i class="fa fa-eye"></i></button>
                        </div>

                        <!-- Password Strength -->
                        <div class="rima-lp-strength" id="rima-pwd-strength">
                            <div class="rima-lp-strength-bars">
                                <div class="rima-lp-strength-bar" id="str-1"></div>
                                <div class="rima-lp-strength-bar" id="str-2"></div>
                                <div class="rima-lp-strength-bar" id="str-3"></div>
                                <div class="rima-lp-strength-bar" id="str-4"></div>
                            </div>
                            <div class="rima-lp-strength-text" id="str-text"></div>
                        </div>

                        <div class="rima-lp-field">
                            <label class="rima-lp-label" for="rima_confirm_password">
                                <span class="rima-en">Confirm Password</span>
                                <span class="rima-ro">Confirma Parola</span>
                                <span class="required">*</span>
                            </label>
                            <input type="password" class="rima-lp-input" name="rima_confirm_password"
                                   id="rima_confirm_password" autocomplete="new-password"
                                   placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" required />
                            <button type="button" class="rima-lp-eye-btn" aria-label="Toggle password"><i class="fa fa-eye"></i></button>
                        </div>
                        <?php endif; ?>

                        <?php
                        $num1 = rand(1, 9);
                        $num2 = rand(1, 9);
                        $sum = $num1 + $num2;
                        $hash = md5('rima_math_' . $sum);
                        ?>
                        <div class="rima-lp-field">
                            <label class="rima-lp-label" for="rima_math_answer">
                                <span class="rima-en">Anti-spam: What is <?php echo $num1; ?> + <?php echo $num2; ?>?</span>
                                <span class="rima-ro">Anti-spam: Cat fac <?php echo $num1; ?> + <?php echo $num2; ?>?</span>
                                <span class="required">*</span>
                            </label>
                            <input type="number" class="rima-lp-input" name="rima_math_answer"
                                   id="rima_math_answer" placeholder="?" required />
                            <i class="fa fa-shield rima-lp-field-icon"></i>
                            <input type="hidden" name="rima_math_hash" value="<?php echo esc_attr($hash); ?>">
                        </div>

                        <?php do_action( 'woocommerce_register_form' ); ?>
                        <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>

                        <button type="submit" class="rima-lp-btn-primary" name="register" value="Register">
                            <span class="rima-en">Create Account</span>
                            <span class="rima-ro">Creeaza Contul</span>
                            <i class="fa fa-check"></i>
                        </button>

                        <?php do_action( 'woocommerce_register_form_end' ); ?>
                    </form>

                    <div class="rima-lp-privacy">
                        <span class="rima-en">By creating an account, you agree to our</span>
                        <span class="rima-ro">Creand un cont, esti de acord cu</span>
                        <br>
                        <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">
                            <span class="rima-en">Privacy Policy</span>
                            <span class="rima-ro">Politica de Confidentialitate</span>
                        </a>.
                    </div>
                </div>

                <!-- ============ LOST PASSWORD TAB ============ -->
                <div class="rima-lp-tab-content" id="rima-tab-reset">
                    <div class="rima-lp-form-header">
                        <h3>
                            <span class="rima-en">Reset Password</span>
                            <span class="rima-ro">Reseteaza Parola</span>
                        </h3>
                        <p>
                            <span class="rima-en">Enter your email to receive a reset link</span>
                            <span class="rima-ro">Introdu emailul pentru link-ul de resetare</span>
                        </p>
                    </div>

                    <form method="post" class="woocommerce-ResetPassword lost_reset_password"
                          action="<?php echo esc_url(wp_lostpassword_url()); ?>">

                        <div class="rima-lp-field">
                            <label class="rima-lp-label" for="user_login_reset">
                                <span class="rima-en">Username or email</span>
                                <span class="rima-ro">Utilizator sau email</span>
                            </label>
                            <input type="text" class="rima-lp-input" name="user_login" id="user_login_reset"
                                   autocomplete="username" placeholder="name@example.com" required />
                            <i class="fa fa-envelope rima-lp-field-icon"></i>
                        </div>

                        <?php do_action('lostpassword_form'); ?>

                        <button type="submit" class="rima-lp-btn-secondary"
                                value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>">
                            <span class="rima-en">Send Reset Link</span>
                            <span class="rima-ro">Trimite Link Resetare</span>
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </form>

                    <div class="rima-lp-back-link">
                        <a href="#" id="rima-back-to-login">
                            <i class="fa fa-arrow-left"></i>
                            <span class="rima-en">Back to Login</span>
                            <span class="rima-ro">Inapoi la Login</span>
                        </a>
                    </div>
                </div>

            </div><!-- /.rima-card-body -->

        </div><!-- /.rima-lp-card -->
    </div><!-- /.rima-centered-wrapper -->

</div><!-- /.rima-login-page -->

<script>
jQuery(document).ready(function($) {

    // ── Tab Switching ──
    $('.rima-lp-tab').on('click', function(e) {
        e.preventDefault();
        var targetId = $(this).data('target');

        // Deactivate all
        $('.rima-lp-tab').removeClass('active');
        $('.rima-lp-tab-content').removeClass('active');

        // Activate clicked
        $(this).addClass('active');
        $('#' + targetId).addClass('active');
    });

    // ── Lost Password Trigger ──
    $('.rima-lp-lost-trigger').on('click', function(e) {
        e.preventDefault();
        $('.rima-lp-tab-lost').addClass('visible');
        // Click the reset tab
        $('.rima-lp-tab[data-target="rima-tab-reset"]').trigger('click');
    });

    // ── Back to Login ──
    $('#rima-back-to-login').on('click', function(e) {
        e.preventDefault();
        $('.rima-lp-tab-lost').removeClass('visible');
        $('.rima-lp-tab[data-target="rima-tab-login"]').trigger('click');
    });

    // ── Hash-based tab switch ──
    if (window.location.hash === '#rima-register-tab' || window.location.hash === '#register') {
        setTimeout(function() {
            $('.rima-lp-tab[data-target="rima-tab-register"]').trigger('click');
        }, 50);
    }

    // ── Password Toggle ──
    $('.rima-lp-eye-btn').on('click', function(e) {
        e.preventDefault();
        var input = $(this).siblings('.rima-lp-input');
        var icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // ── Password Strength ──
    $('#reg_password').on('input', function() {
        var val = $(this).val();
        var container = $('#rima-pwd-strength');
        var bars = $('.rima-lp-strength-bar');
        var textEl = $('#str-text');
        var isRo = document.body.classList.contains('rima-lang-ro');

        if (val.length === 0) {
            container.removeClass('show');
            return;
        }

        container.addClass('show');

        var score = 0;
        if (val.length > 5) score++;
        if (val.length > 8) score++;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
        if (/[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val)) score++;

        // Reset bars
        bars.removeClass('weak medium strong');

        if (score <= 2) {
            for (var i = 0; i < score; i++) bars.eq(i).addClass('weak');
            textEl.text(isRo ? 'Slaba' : 'Weak').css('color', '#e74c3c');
        } else if (score === 3) {
            for (var j = 0; j < 3; j++) bars.eq(j).addClass('medium');
            textEl.text(isRo ? 'Medie' : 'Medium').css('color', '#f39c12');
        } else if (score === 4) {
            bars.addClass('strong');
            textEl.text(isRo ? 'Puternica' : 'Strong').css('color', '#00BFA6');
        }
    });

});
</script>

</div> <!-- END .rima-login-page -->

<script src="https://unpkg.com/globe.gl"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Globe Setup
    const globeContainer = document.getElementById('rhm-globe-viz');
    if (globeContainer && typeof Globe !== 'undefined') {
        
        const markerData = <?php echo function_exists('rima_get_globe_markers_json') ? rima_get_globe_markers_json() : '[]'; ?>;

        const arcsData = [];
        if(markerData.length > 0) {
            const origin = markerData[0]; // English/London as origin
            for (let i = 1; i < markerData.length; i++) {
                arcsData.push({
                    startLat: origin.lat, startLng: origin.lng,
                    endLat: markerData[i].lat, endLng: markerData[i].lng
                });
            }
        }

        const globe = Globe()(globeContainer)
            .globeImageUrl('https://unpkg.com/three-globe/example/img/earth-dark.jpg')
            .bumpImageUrl('https://unpkg.com/three-globe/example/img/earth-topology.png')
            .backgroundColor('rgba(0,0,0,0)')
            .showAtmosphere(true)
            .atmosphereColor('#00E5FF')
            .atmosphereAltitude(0.2)
            .width(globeContainer.clientWidth)
            .height(globeContainer.clientHeight || window.innerHeight)
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

        globe.controls().autoRotate = true;
        globe.controls().autoRotateSpeed = 0.8;
        globe.controls().enableZoom = false; // Disabled so page can scroll normally

        globe.pointOfView({ lat: 20, lng: 0, altitude: 2.5 }, 1000);

        window.addEventListener('resize', () => {
            if (globeContainer.clientWidth > 0) {
                globe.width(globeContainer.clientWidth);
                globe.height(globeContainer.clientHeight || window.innerHeight);
            }
        });
    }

    // Move back button to body to escape any transform contexts from the theme
    const backBtn = document.querySelector('.rima-back-home-btn');
    if (backBtn) {
        document.body.appendChild(backBtn);
    }

    /* ===== MOBILE APP LAYOUT — JS ENFORCER =====
       Applies inline styles that override ALL CSS, including cached theme styles.
       Runs immediately on DOMContentLoaded for instant effect.
    ================================================ */
    function rimaApplyMobileLayout() {
        if (window.innerWidth > 640) return; // desktop — skip

        // 1. Hide theme header elements
        const headerSelectors = [
            'header',
            '.eltdf-page-header',
            '.eltdf-mobile-header',
            '#header',
            '.site-header',
            '.eltdf-sticky-header',
            '.eltdf-top-bar',
            '[class*="bottom-bar"]',
            '[class*="footer-nav"]'
        ];
        headerSelectors.forEach(sel => {
            document.querySelectorAll(sel).forEach(el => {
                // Only hide if NOT inside our login card
                if (!el.closest('.rima-login-page')) {
                    el.style.setProperty('display', 'none', 'important');
                }
            });
        });

        // 2. Hide footer
        document.querySelectorAll('footer, .eltdf-page-footer, [class*="footer"]').forEach(el => {
            if (!el.closest('.rima-login-page')) {
                el.style.setProperty('display', 'none', 'important');
            }
        });

        // 3. Remove body top padding (admin bar / header pushes)
        document.body.style.setProperty('padding-top', '0', 'important');
        document.body.style.setProperty('margin-top', '0', 'important');

        // 4. Login page: block display
        const loginPage = document.querySelector('.rima-login-page');
        if (loginPage) {
            loginPage.style.setProperty('display', 'block', 'important');
            loginPage.style.setProperty('min-height', '100vh', 'important');
            loginPage.style.setProperty('padding', '0', 'important');
            loginPage.style.setProperty('margin', '0', 'important');
            loginPage.style.setProperty('width', '100vw', 'important');
        }

        // 5. Wrapper: full width
        const wrapper = document.querySelector('.rima-centered-wrapper');
        if (wrapper) {
            wrapper.style.setProperty('max-width', '100vw', 'important');
            wrapper.style.setProperty('width', '100vw', 'important');
            wrapper.style.setProperty('padding', '0', 'important');
            wrapper.style.setProperty('min-height', '100vh', 'important');
            wrapper.style.setProperty('display', 'flex', 'important');
            wrapper.style.setProperty('flex-direction', 'column', 'important');
            wrapper.style.setProperty('align-items', 'stretch', 'important');
        }

        // 6. Card: full screen, no radius
        const card = document.querySelector('.rima-lp-card');
        if (card) {
            card.style.setProperty('flex', '1 1 auto', 'important');
            card.style.setProperty('width', '100%', 'important');
            card.style.setProperty('max-width', '100%', 'important');
            card.style.setProperty('border-radius', '0', 'important');
            card.style.setProperty('border-left', 'none', 'important');
            card.style.setProperty('border-right', 'none', 'important');
            card.style.setProperty('border-bottom', 'none', 'important');
            card.style.setProperty('box-shadow', 'none', 'important');
            card.style.setProperty('margin', '0', 'important');
        }

        // 7. Card header: app navbar
        const cardHeader = document.querySelector('.rima-card-header');
        if (cardHeader) {
            cardHeader.style.setProperty('position', 'sticky', 'important');
            cardHeader.style.setProperty('top', '0', 'important');
            cardHeader.style.setProperty('z-index', '9999', 'important');
            cardHeader.style.setProperty('padding', '12px 20px', 'important');
            cardHeader.style.setProperty('background', 'rgba(4,6,14,0.98)', 'important');
            cardHeader.style.setProperty('display', 'flex', 'important');
            cardHeader.style.setProperty('align-items', 'center', 'important');
            cardHeader.style.setProperty('justify-content', 'flex-start', 'important');
        }

        // 8. Hide logo in card header on mobile
        const cardLogo = document.querySelector('.rima-card-header .rima-card-logo');
        if (cardLogo) {
            cardLogo.style.setProperty('display', 'none', 'important');
        }

        // 9. Card body padding
        const cardBody = document.querySelector('.rima-card-body');
        if (cardBody) {
            cardBody.style.setProperty('padding', '20px 20px 40px', 'important');
        }

        // 10. Inputs: touch-friendly, prevent iOS zoom
        document.querySelectorAll('.rima-lp-input').forEach(inp => {
            inp.style.setProperty('padding', '14px 16px', 'important');
            inp.style.setProperty('font-size', '16px', 'important');
        });

        // 11. Submit: full width
        document.querySelectorAll('.rima-lp-submit').forEach(btn => {
            btn.style.setProperty('width', '100%', 'important');
            btn.style.setProperty('padding', '15px', 'important');
            btn.style.setProperty('font-size', '1rem', 'important');
        });

        // 12. Globe: subtler on mobile
        const globe = document.querySelector('#rhm-globe-viz, .rima-fullscreen-globe');
        if (globe) globe.style.setProperty('opacity', '0.12', 'important');

        console.log('[RIMA] Mobile app layout applied ✓');
    }

    // Run immediately + on resize
    rimaApplyMobileLayout();
    window.addEventListener('resize', rimaApplyMobileLayout);

});
</script>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>