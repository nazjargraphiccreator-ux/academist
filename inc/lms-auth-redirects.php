<?php
/* ============================================================
   9. LMS AUTHENTICATION REDIRECTS & FLOATING SIDE-CART
   ============================================================ */
add_action('wp_footer', function() {
    $my_account_url = class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : '/my-account/';
    
    // Build user data for the menu
    if ( is_user_logged_in() ) {
        $current_user  = wp_get_current_user();
        $display_name  = $current_user->display_name ?: $current_user->user_login;
        $first_name    = $current_user->first_name ?: '';
        $last_name     = $current_user->last_name ?: '';
        // Initials: first letter of first and last name, fallback to first two of display_name
        $initials = '';
        if ($first_name && $last_name) {
            $initials = strtoupper(mb_substr($first_name,0,1) . mb_substr($last_name,0,1));
        } elseif ($display_name) {
            $parts = explode(' ', trim($display_name));
            $initials = strtoupper(mb_substr($parts[0],0,1) . (isset($parts[1]) ? mb_substr($parts[1],0,1) : ''));
        }
        if (empty($initials)) $initials = 'U';
        
        // Avatar URL
        $avatar_url = get_avatar_url($current_user->ID, ['size' => 80]);
        $has_custom_avatar = !strpos($avatar_url, 'gravatar.com/avatar/') || strpos($avatar_url, 'd=mm') === false;
        
        // Dashboard / My Account links
        $dashboard_url  = $my_account_url;
        $courses_url    = $my_account_url . 'my-courses/';
        $orders_url     = $my_account_url . 'orders/';
        $profile_url    = $my_account_url . 'edit-account/';
        $logout_url     = wp_logout_url( home_url() );
        
        $is_logged_in_html = 'true';
    } else {
        $display_name = '';
        $initials = '';
        $avatar_url = '';
        $has_custom_avatar = false;
        $dashboard_url = $courses_url = $orders_url = $profile_url = $logout_url = '';
        $is_logged_in_html = 'false';
    }
    ?>
    
    <!-- RIMA Premium User Menu CSS -->
    <style>
    /* Hide original theme login widget */
    .eltdf-login-register-widget { display: none !important; }

    /* Disable Top Bar completely */
    .eltdf-top-bar, 
    .eltdf-top-bar-background, 
    .eltdf-top-bar-wrapper,
    #eltdf-top-bar,
    .top-bar,
    .eltdf-top-bar-area { 
        display: none !important; 
        height: 0 !important; 
        overflow: hidden !important; 
        visibility: hidden !important; 
    }

    /* Hide the red square side menu opener */
    .eltdf-side-menu-button-opener {
        display: none !important;
    }

    /* Disable default Academist hover cart dropdown */
    .eltdf-shopping-cart-dropdown,
    .eltdf-shopping-cart-dropdown-inner {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
    
    /* Premium User Menu Container - inline with header cart */
    #rima-user-menu-container {
        display: inline-flex;
        vertical-align: middle;
        align-items: center;
        gap: 10px;
        margin-right: 12px;
    }


    /* Not logged in: Login/Register buttons */
    .rima-topbar-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border-radius: 30px;
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: .2px;
        text-decoration: none !important;
        transition: all 0.2s ease;
        cursor: pointer;
        line-height: 1;
        white-space: nowrap;
    }
    .rima-topbar-btn-login {
        background: transparent;
        color: #374151 !important;
        border: 1.5px solid rgba(55,65,81,0.25) !important;
    }
    .rima-topbar-btn-login:hover {
        background: rgba(11,29,58,0.05);
        border-color: #374151 !important;
        color: #0B1D3A !important;
    }
    .rima-topbar-btn-register {
        background: linear-gradient(135deg, #FF1949 0%, #c8002f 100%);
        color: #fff !important;
        border: none !important;
        box-shadow: 0 3px 10px rgba(255,25,73,0.25);
    }
    .rima-topbar-btn-register:hover {
        background: linear-gradient(135deg, #e6003d 0%, #a8002a 100%);
        transform: translateY(-1px);
        box-shadow: 0 5px 16px rgba(255,25,73,0.35);
        color: #fff !important;
    }
    
    /* Logged in: Avatar + dropdown */
    .rima-user-avatar-wrap {
        position: relative;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        height: 100%;
    }
    .rima-user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 2px solid #e5e7eb;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0B1D3A, #17325c);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        font-family: 'Inter', sans-serif;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        transition: all 0.2s ease;
        user-select: none;
    }
    .rima-user-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    .rima-user-avatar-wrap:hover .rima-user-avatar {
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        transform: scale(1.05);
    }
    
    /* Dropdown */
    .rima-user-dropdown {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        min-width: 240px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 12px 48px rgba(0,0,0,0.14);
        padding: 8px 0;
        z-index: 999999;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px) scale(0.97);
        transition: all 0.22s cubic-bezier(0.34,1.56,0.64,1);
        pointer-events: none;
    }
    .rima-user-dropdown.rima-open {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: all;
    }
    
    /* Dropdown header with user info */
    .rima-dropdown-header {
        padding: 14px 18px 10px;
        border-bottom: 1px solid #f1f1f1;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .rima-dropdown-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0B1D3A, #17325c);
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }
    .rima-dropdown-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    .rima-dropdown-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .rima-dropdown-name {
        font-size: 13px;
        font-weight: 700;
        color: #0B1D3A;
        line-height: 1.2;
    }
    .rima-dropdown-role {
        font-size: 10px;
        color: #008b9e;
        background: rgba(0, 229, 255, 0.1);
        border: 1px solid rgba(0, 229, 255, 0.2);
        padding: 3px 8px;
        border-radius: 12px;
        margin-top: 5px;
        display: inline-block;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    
    /* Dropdown box-sizing override to prevent layout overflow */
    .rima-user-dropdown,
    .rima-user-dropdown * {
        box-sizing: border-box !important;
    }

    /* Dropdown items */
    .rima-dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 18px;
        font-size: 13px;
        color: #374151 !important;
        text-decoration: none !important;
        transition: all 0.15s ease;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }
    .rima-dropdown-item:hover {
        background: rgba(255, 71, 87, 0.08) !important;
        color: #ff4757 !important;
        padding-left: 22px;
    }
    .rima-dropdown-item .rima-di-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
        transition: all 0.15s;
    }
    .rima-dropdown-item:hover .rima-di-icon {
        background: rgba(255, 71, 87, 0.15) !important;
        color: #ff4757 !important;
    }
    .rima-dropdown-divider {
        height: 1px;
        background: #f1f1f1;
        margin: 6px 0;
    }
    .rima-dropdown-item.rima-logout {
        color: #dc2626 !important;
    }
    .rima-dropdown-item.rima-logout:hover {
        background: #fff5f5;
    }
    .rima-dropdown-item.rima-logout .rima-di-icon {
        background: #fee2e2;
    }
    
    /* Arrow indicator on avatar */
    .rima-user-caret {
        font-size: 10px;
        color: #9ca3af;
        transition: transform 0.2s;
        margin-left: 3px;
    }
    .rima-user-avatar-wrap.rima-open .rima-user-caret {
        transform: rotate(180deg);
        color: #0B1D3A;
    }
    /* RIMA Mobile Bottom Nav */
    #rima-mobile-bottom-nav {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #ffffff !important;
        box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
        z-index: 99999;
        justify-content: space-around;
        padding: 12px 0 8px 0;
        border-top: 1px solid #f0f0f0;
    }
    .rima-mob-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #000000 !important;
        text-decoration: none;
        font-size: 11px;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        transition: color 0.3s ease;
    }
    .rima-mob-nav-item:hover, .rima-mob-nav-item:active {
        color: #ff0050; /* Brand pink */
    }
    .rima-mob-icon {
        margin-bottom: 4px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .rima-mob-icon svg {
        width: 22px;
        height: 22px;
        stroke: currentColor;
    }
    .rima-mob-cart-badge {
        position: absolute;
        top: -6px;
        right: -10px;
        background: #ff0050;
        color: white;
        font-size: 10px;
        font-weight: bold;
        min-width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    @media only screen and (max-width: 1024px) {
        #rima-mobile-bottom-nav {
            display: flex !important;
        }
        body {
            padding-bottom: 100px !important; 
        }
        
        /* Ensure top cart and user menu are visible when injected in mobile header */
        .eltdf-mobile-header .eltdf-shopping-cart-holder,
        .eltdf-mobile-header .eltdf-header-cart {
            display: inline-block !important;
            margin-right: 15px;
            vertical-align: middle;
        }
        .eltdf-mobile-header #rima-user-menu-container {
            display: inline-block !important;
            margin-right: 15px;
            vertical-align: middle;
        }
        .eltdf-mobile-header .eltdf-position-right {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            padding-right: 20px;
        }
    }
    </style>
    
    <!-- RIMA Premium User Menu HTML -->
    <div id="rima-user-menu-container">
        <?php if ( is_user_logged_in() ) : 
            $current_user = wp_get_current_user();
            // Fetch custom avatar or fallback
            $profile_image = get_user_meta( $current_user->ID, 'social_profile_image', true );
            if ( empty( $profile_image ) ) {
                $profile_image = get_avatar_url( $current_user->ID, array( 'size' => 80 ) );
            }
        ?>
        <!-- LOGGED IN: Avatar + Dropdown -->
        <div class="rima-user-avatar-wrap" id="rimaUserAvatarWrap">
            <div class="rima-user-avatar" id="rimaUserAvatar">
                <img src="<?php echo esc_url($profile_image); ?>" alt="Avatar">
            </div>
            <span class="rima-user-caret">â–¾</span>
            
            <!-- Dropdown -->
            <div class="rima-user-dropdown" id="rimaUserDropdown">
                <!-- Header -->
                <div class="rima-dropdown-header">
                    <div class="rima-dropdown-avatar">
                        <img src="<?php echo esc_url($profile_image); ?>" alt="Avatar">
                    </div>
                    <div>
                        <div class="rima-dropdown-name"><?php echo esc_html($display_name); ?></div>
                        <?php 
                            $user_roles = (array) $current_user->roles;
                            $role_slug  = !empty($user_roles) ? $user_roles[0] : '';
                            global $wp_roles;
                            $role_name  = isset($wp_roles->roles[$role_slug]['name']) ? translate_user_role($wp_roles->roles[$role_slug]['name']) : 'Student';
                        ?>
                        <div class="rima-dropdown-role"><?php echo esc_html($role_name); ?></div>
                    </div>
                </div>
                
                <!-- Menu Items -->
                <a href="<?php echo esc_url($dashboard_url); ?>" class="rima-dropdown-item">
                    <span class="rima-di-icon">ðŸ“Š</span>
                    <span class="rima-en">Dashboard</span>
                    <span class="rima-ro">Panou de Control</span>
                </a>
                <a href="<?php echo esc_url($courses_url); ?>" class="rima-dropdown-item">
                    <span class="rima-di-icon">ðŸ“š</span>
                    <span class="rima-en">My Courses</span>
                    <span class="rima-ro">Cursurile Mele</span>
                </a>
                <a href="<?php echo esc_url($orders_url); ?>" class="rima-dropdown-item">
                    <span class="rima-di-icon">ðŸ›ï¸</span>
                    <span class="rima-en">Orders</span>
                    <span class="rima-ro">Comenzi</span>
                </a>
                <a href="<?php echo esc_url($profile_url); ?>" class="rima-dropdown-item">
                    <span class="rima-di-icon">âš™ï¸</span>
                    <span class="rima-en">Account Details</span>
                    <span class="rima-ro">Detalii Cont</span>
                </a>
                
                <div class="rima-dropdown-divider"></div>
                
                <a href="<?php echo esc_url($logout_url); ?>" class="rima-dropdown-item rima-logout">
                    <span class="rima-di-icon">ðŸšª</span>
                    <span class="rima-en">Logout</span>
                    <span class="rima-ro">Deconectare</span>
                </a>
            </div>
        </div>
        
        <?php else : ?>
        <!-- NOT LOGGED IN: Login + Register buttons -->
        <a href="<?php echo esc_url($my_account_url); ?>" class="rima-topbar-btn rima-topbar-btn-login">
            ðŸ‘¤ <span class="rima-en">Login</span><span class="rima-ro">Conectare</span>
        </a>
        <a href="<?php echo esc_url($my_account_url . '#rima-register-tab'); ?>" class="rima-topbar-btn rima-topbar-btn-register">
            âœ¨ <span class="rima-en">Register</span><span class="rima-ro">Înregistrare</span>
        </a>
        <?php endif; ?>
    </div>


    <script>
    jQuery(document).ready(function($) {

        // â”€â”€â”€ 1. Relocate user menu next to Cart in header â”€â”€â”€
        function relocateHeaderWidgets() {
            var cartOuter = document.querySelector('.eltdf-shopping-cart-holder');
            var ourMenu   = document.getElementById('rima-user-menu-container');

            if (window.innerWidth <= 1024) {
                // Move to mobile header if mobile header exists
                var mobileHeaderRight = document.querySelector('.eltdf-mobile-header .eltdf-position-right-inner') || document.querySelector('.eltdf-mobile-header .eltdf-mobile-menu-opener');
                
                if (mobileHeaderRight && ourMenu) {
                    if (mobileHeaderRight.parentNode && !mobileHeaderRight.parentNode.contains(ourMenu)) {
                        mobileHeaderRight.parentNode.insertBefore(ourMenu, mobileHeaderRight);
                    }
                    if (cartOuter && mobileHeaderRight.parentNode && !mobileHeaderRight.parentNode.contains(cartOuter)) {
                        mobileHeaderRight.parentNode.insertBefore(cartOuter, mobileHeaderRight);
                    }
                }
            } else {
                if (cartOuter && ourMenu && !cartOuter.parentNode.contains(ourMenu)) {
                    cartOuter.parentNode.insertBefore(ourMenu, cartOuter);
                }
            }
        }
        relocateHeaderWidgets();
        // Re-run on scroll and resize
        $(window).on('scroll.rimaHeader resize.rimaHeader', function() { relocateHeaderWidgets(); });

        // â”€â”€â”€ 2. Avatar dropdown toggle â”€â”€â”€
        $(document).on('click', '#rimaUserAvatarWrap', function(e) {
            e.stopPropagation();
            $(this).toggleClass('rima-open');
            $('#rimaUserDropdown').toggleClass('rima-open');
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#rimaUserAvatarWrap').length) {
                $('#rimaUserAvatarWrap').removeClass('rima-open');
                $('#rimaUserDropdown').removeClass('rima-open');
            }
        });

        // â”€â”€â”€ 3. Redirect leftover theme auth openers â”€â”€â”€
        var authLinks = document.querySelectorAll('.eltdf-login-opener, .eltdf-register-opener');
        authLinks.forEach(function(link) {
            link.classList.remove('eltdf-modal-opener');
            link.setAttribute('data-modal', '');
            link.href = '<?php echo esc_url($my_account_url); ?>';
        });

        // â”€â”€â”€ 4. Slide-out Side Cart (robust binding) â”€â”€â”€
        // Use true capture phase listener to completely bypass any theme scripts calling stopPropagation
        document.addEventListener('click', function(e) {
            var cartTarget = e.target.closest('.eltdf-shopping-cart-holder, .eltdf-header-cart, .eltdf-cart-icon');
            if (cartTarget) {
                e.preventDefault();
                e.stopPropagation();
                var sideCart = document.getElementById('rima-side-cart');
                var overlay = document.getElementById('rima-side-cart-overlay');
                if (sideCart) sideCart.classList.add('rima-cart-open');
                if (overlay) overlay.classList.add('rima-cart-open');
            }
        }, true);
        
        // Keep checking and overriding the href just in case
        setInterval(function() {
            $('.eltdf-shopping-cart-holder a, .eltdf-header-cart > a').attr('href', 'javascript:void(0);');
        }, 1500);

        $(document).on('click', '#rima-side-cart-close, #rima-side-cart-overlay', function() {
            $('#rima-side-cart').removeClass('rima-cart-open');
            $('#rima-side-cart-overlay').removeClass('rima-cart-open');
        });

        // â”€â”€â”€ Mobile Bottom Nav Actions â”€â”€â”€
        $(document).on('click', '#rima-mob-nav-cart', function(e) {
            e.preventDefault();
            $('#rima-side-cart').addClass('rima-cart-open');
            $('#rima-side-cart-overlay').addClass('rima-cart-open');
        });

        $(document).on('click', '#rima-mobile-menu-toggle', function(e) {
            e.preventDefault();
            // Trigger theme's mobile menu opener
            $('.eltdf-mobile-menu-opener a').trigger('click');
        });

        // â”€â”€â”€ 5 & 7. Language + WC strings handled by RIMA_LANG controller (see wp_footer) â”€â”€â”€
        // window.RIMA_LANG.current === 'ro' | 'en'
        // window.translateWooCommerceStrings() is exposed by RIMA_LANG for legacy calls

    }); // end ready

    // Listen to WooCommerce added_to_cart event via jQuery
    jQuery(document).ready(function($) {
        $(document).on('added_to_cart', function(event, fragments, cart_hash, $button) {
            // Get product name dynamically (from button data, or fallback to single page title)
            var productName = 'Course';
            if ($button && ($button.attr('data-product_name') || $button.data('product_name'))) {
                productName = $button.attr('data-product_name') || $button.data('product_name');
            } else if ($('.rsc-hero-title').length) {
                productName = $('.rsc-hero-title').text().trim();
            } else if ($('h1.product_title').length) {
                productName = $('h1.product_title').text().trim();
            }

            var isRo = (window.RIMA_LANG ? window.RIMA_LANG.current === 'ro' : false) || (localStorage.getItem('rima_lang') === 'ro');
            var msg = isRo ? '"' + productName + '" a fost adăugat în coș!' : '"' + productName + '" has been added to cart!';
            
            // Show success toast with dynamic message
            var $toast = $('#rima-cart-toast');
            if($toast.length) {
                $toast.text(msg).addClass('show-toast');
                setTimeout(function() {
                    $toast.removeClass('show-toast');
                }, 4000);
            }
    
            // Update Side Cart HTML with fragments
            if(fragments && fragments['div.widget_shopping_cart_content']) {
                $('#rima-side-cart-content').html(fragments['div.widget_shopping_cart_content']);
                if (typeof window.translateWooCommerceStrings === 'function') {
                    window.translateWooCommerceStrings();
                }
            }
        });

        // AJAX update cart quantity on +/- button clicks
        $(document).on('click', '.rima-cart-qty-minus, .rima-cart-qty-plus', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $btn = $(this);
            var key = $btn.attr('data-cart_item_key');
            var $input = $btn.siblings('.rima-cart-qty-input');
            var currentQty = parseInt($input.val()) || 1;
            var newQty = $btn.hasClass('rima-cart-qty-plus') ? currentQty + 1 : currentQty - 1;
            
            if (newQty < 0) newQty = 0;
            
            $('#rima-side-cart').addClass('rima-cart-loading');
            
            $.ajax({
                type: 'POST',
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action: 'rima_update_cart_quantity',
                    cart_item_key: key,
                    qty: newQty
                },
                success: function(response) {
                    if (response && response.fragments) {
                        var fragments = response.fragments;
                        $.each(fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                        $(document.body).trigger('wc_fragments_refreshed');
                    }
                    $('#rima-side-cart').removeClass('rima-cart-loading');
                },
                error: function() {
                    $('#rima-side-cart').removeClass('rima-cart-loading');
                }
            });
        });

        // Auto-open side-cart on page load if WooCommerce notices (added to cart messages) are present
        if ($('.woocommerce-message').length > 0) {
            var sideCart = document.getElementById('rima-side-cart');
            var cartOverlay = document.getElementById('rima-side-cart-overlay');
            if(sideCart) {
                sideCart.classList.add('rima-cart-open');
                cartOverlay.classList.add('rima-cart-open');
            }
        }
    });
    </script>

    <!-- Toast Notification -->
    <div id="rima-cart-toast" class="rima-cart-toast"></div>

    <!-- Side Cart HTML -->
    <div id="rima-side-cart-overlay" class="rima-side-cart-overlay"></div>
    <div id="rima-side-cart" class="rima-side-cart">
        <div class="rima-side-cart-header">
            <h4><span class="rima-en">Your Cart</span><span class="rima-ro">Coșul Tău</span></h4>
            <span id="rima-side-cart-close" class="rima-side-cart-close">&times;</span>
        </div>
        <div id="rima-side-cart-content" class="rima-side-cart-content widget_shopping_cart_content">
            <?php 
            if(class_exists('WooCommerce')) {
                woocommerce_mini_cart(); 
            }
            ?>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div id="rima-mobile-bottom-nav">
        <a href="https://rima-academy.com/" class="rima-mob-nav-item">
            <span class="rima-mob-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </span>
            <span class="rima-en">Home</span><span class="rima-ro">Acasă</span>
        </a>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="rima-mob-nav-item">
            <span class="rima-mob-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </span>
            <span class="rima-en">Account</span><span class="rima-ro">Contul meu</span>
        </a>
        <a href="#" class="rima-mob-nav-item" id="rima-mobile-menu-toggle">
            <span class="rima-mob-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </span>
            <span class="rima-en">Menu</span><span class="rima-ro">Meniu</span>
        </a>
        <a href="#" class="rima-mob-nav-item" id="rima-mob-nav-cart">
            <span class="rima-mob-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                <span class="rima-mob-cart-badge">0</span>
            </span>
            <span class="rima-en">Cart</span><span class="rima-ro">Coșul meu</span>
        </a>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Mobile bottom nav cart trigger
        $(document).on('click', '#rima-mob-nav-cart', function(e) {
            e.preventDefault();
            $('#rima-side-cart').addClass('rima-cart-open');
            $('#rima-side-cart-overlay').addClass('rima-cart-open');
        });

        // Sync mobile cart badge with desktop cart badge every second
        setInterval(function() {
            var count = $('.eltdf-cart-number').first().text();
            if (count !== undefined && count !== '') {
                $('.rima-mob-cart-badge').text(count);
            }
        }, 1000);
    });
    </script>

    <?php
}, 999);

require_once get_stylesheet_directory() . '/inc/registration-validation.php';

