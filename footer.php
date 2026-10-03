                </div> <!-- close div.content_inner -->
            </div>  <!-- close div.content -->
            
            <!-- RIMA GLOBAL FOOTER BUILDER -->
            <footer id="rima-global-footer" class="rima-site-footer" >
                <div class="rima-footer-top-accent"></div>
                <div class="rima-container">
                    
                    <div class="rima-footer-grid">
                        <!-- BRAND COL -->
                        <div class="rima-footer-col rima-footer-brand">
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <span style="font-size: 24px; font-weight: 800; color: white;">RIMA Academy</span>
                            </a>
                            <p>Professional language learning with structured CEFR courses, expert instructors and a modern digital learning platform.</p>
                            
                            <div class="rima-social-icons" style="margin-top: 1.5rem;">
                                <a href="https://facebook.com/rima.academy" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></a>
                                <a href="https://instagram.com/rima.academy" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg></a>
                                <a href="https://linkedin.com/company/rima-academy" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg></a>
                            </div>
                        </div>

                        <!-- COL 2 -->
                        <div class="rima-footer-col">
                            <h4>Courses</h4>
                            <ul>
                                <li><a href="<?php echo esc_url(site_url('/course-category/english/')); ?>">General English</a></li>
                                <li><a href="<?php echo esc_url(site_url('/course-category/business-english/')); ?>">Business English</a></li>
                                <li><a href="<?php echo esc_url(site_url('/course-category/romanian/')); ?>">Romanian for Expats</a></li>
                                <li><a href="<?php echo esc_url(site_url('/course-category/japanese/')); ?>">Japanese (N5-N1)</a></li>
                            </ul>
                        </div>

                        <!-- COL 3 -->
                        <div class="rima-footer-col">
                            <h4>Resources</h4>
                            <ul>
                                <li><a href="#">CEFR Levels Explained</a></li>
                                <li><a href="#">Placement Test</a></li>
                                <li><a href="#">Student Dashboard</a></li>
                                <li><a href="#">Certificate Validation</a></li>
                            </ul>
                        </div>

                        <!-- COL 4 -->
                        <div class="rima-footer-col">
                            <h4>For Business</h4>
                            <ul>
                                <li><a href="#">Corporate Training</a></li>
                                <li><a href="#">Employee Assessment</a></li>
                                <li><a href="#">B2B Invoicing</a></li>
                                <li><a href="#">Contact Sales</a></li>
                            </ul>
                        </div>

                        <!-- COL 5 -->
                        <div class="rima-footer-col">
                            <h4>Support</h4>
                            <ul>
                                <li><a href="<?php echo esc_url(site_url('/contact/')); ?>">Contact Us</a></li>
                                <li><a href="#">FAQ</a></li>
                                <li><a href="#">Technical Support</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- BOTTOM BAR -->
                    <div class="rima-footer-bottom">
                        <div class="rima-footer-copyright">
                            <span style="color: var(--rima-text-muted); font-size: 0.875rem;">&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>. Toate drepturile rezervate.</span>
                        </div>
                        <div class="rima-footer-bottom-links">
                            <?php 
                            // Try to get Privacy Policy
                            $privacy_url = function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : '';
                            if ($privacy_url) {
                                echo '<a href="' . esc_url($privacy_url) . '">Privacy Policy</a>';
                            }

                            // Function to find a page by partial title/slug
                            function rima_get_legal_page($slugs) {
                                foreach ($slugs as $slug) {
                                    $page = get_page_by_path($slug);
                                    if ($page) return get_permalink($page->ID);
                                }
                                return '#';
                            }
                            
                            $terms_url = rima_get_legal_page(['termeni-si-conditii', 'terms', 'termeni']);
                            $cookie_url = rima_get_legal_page(['politica-de-cookies', 'cookies', 'cookie-policy']);
                            ?>
                            <a href="<?php echo esc_url($terms_url); ?>">Termeni și Condiții</a>
                            <a href="<?php echo esc_url($cookie_url); ?>">Politica de Cookies</a>
                        </div>
                    </div>

                </div>
            </footer>
            
        </div> <!-- close div.eltdf-wrapper-inner  -->
    </div> <!-- close div.eltdf-wrapper -->
    
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
        <a href="<?php echo esc_url(home_url('/')); ?>" class="rima-mob-nav-item">
            <span class="rima-mob-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </span>
            <span class="rima-en">Home</span><span class="rima-ro">Acasă</span>
        </a>
        <a href="<?php echo function_exists('wc_get_page_permalink') ? esc_url( wc_get_page_permalink( 'myaccount' ) ) : '#'; ?>" class="rima-mob-nav-item">
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
            <span class="rima-en">Cart</span><span class="rima-ro">Coș</span>
        </a>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var mobCart = document.getElementById('rima-mob-nav-cart');
        var sc = document.getElementById('rima-side-cart');
        var overlay = document.getElementById('rima-side-cart-overlay');
        var closeBtn = document.getElementById('rima-side-cart-close');
        
        if (mobCart) {
            mobCart.addEventListener('click', function(e) {
                e.preventDefault();
                if(sc) sc.classList.add('rima-cart-open');
                if(overlay) overlay.classList.add('rima-cart-open');
            });
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                if(sc) sc.classList.remove('rima-cart-open');
                if(overlay) overlay.classList.remove('rima-cart-open');
            });
        }
        
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                if(sc) sc.classList.remove('rima-cart-open');
                if(overlay) overlay.classList.remove('rima-cart-open');
            });
        }
        
        if (typeof jQuery !== 'undefined') {
            jQuery(document.body).on('added_to_cart', function() {
                if(sc) sc.classList.add('rima-cart-open');
                if(overlay) overlay.classList.add('rima-cart-open');
            });
        }
    });
    </script>
    <?php wp_footer(); ?>
    <!-- SPA INITIALIZATION -->
    <script>
    window.rimaVueAppsQueue = [];
    window.initRimaVueApps = function() {
        if (window.rimaVueAppsQueue && window.rimaVueAppsQueue.length > 0) {
            window.rimaVueAppsQueue.forEach(fn => {
                if (typeof fn === 'function') fn();
            });
            // Clear queue after running so they don't run multiple times on next nav
            window.rimaVueAppsQueue = [];
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        // Run once on initial load
        window.initRimaVueApps();

        if (typeof Swup !== 'undefined') {
            const swup = new Swup({
                containers: ['#swup'],
                animationSelector: '[class*="swup-transition-"]',
                cache: true,
                linkSelector: 'a[href^="' + window.location.origin + '"]:not([data-no-swup]):not([target="_blank"]):not([href*="wp-admin"]):not([href*="wp-login"]):not([href*="cart"]):not([href*="checkout"]):not([href*="my-account"])'
            });

            // Initialize Vue apps after page transition
            swup.hooks.on('page:view', () => {
                window.initRimaVueApps();
                
                // Re-init Elementor/WPBakery if present
                if (typeof jQuery !== 'undefined') {
                    jQuery(document).trigger('ready');
                }
            });
        }
    });
    </script>
</body>
</html>


