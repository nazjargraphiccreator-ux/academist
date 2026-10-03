import sys

file_path = 'functions.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add CSS for mobile menu
css_addition = """
    /* Mobile Menu Overlay */
    .rima-mobile-menu-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.7); z-index: 100000; opacity: 0; visibility: hidden; transition: 0.3s;
    }
    .rima-mobile-menu-overlay.rima-menu-open {
        opacity: 1; visibility: visible;
    }
    .rima-mobile-menu {
        position: fixed; top: 0; left: -320px; width: 300px; max-width: 80%; height: 100%;
        background: #0f172a; z-index: 100001; transition: 0.3s;
        display: flex; flex-direction: column; overflow-y: auto;
        box-shadow: 5px 0 25px rgba(0,0,0,0.5);
    }
    .rima-mobile-menu.rima-menu-open {
        left: 0;
    }
    .rima-mobile-menu-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .rima-mobile-menu-header h4 {
        color: white; margin: 0; font-size: 20px;
    }
    .rima-mobile-menu-close {
        color: white; font-size: 28px; cursor: pointer;
    }
    .rima-mobile-menu-content {
        padding: 20px;
    }
    .rima-mobile-nav ul {
        list-style: none; padding: 0; margin: 0;
    }
    .rima-mobile-nav li {
        margin-bottom: 15px;
    }
    .rima-mobile-nav a {
        color: white; text-decoration: none; font-size: 18px; font-weight: 500;
    }
    .rima-mobile-nav .sub-menu {
        padding-left: 15px; margin-top: 10px; border-left: 2px solid rgba(255,255,255,0.1);
    }
    .rima-mobile-nav .sub-menu a {
        font-size: 15px; color: #cbd5e1;
    }
"""

if "rima-mobile-menu-overlay" not in content:
    content = content.replace('/* RIMA Mobile Bottom Nav */', css_addition + '\n    /* RIMA Mobile Bottom Nav */')

# Add HTML for mobile menu
html_addition = """
    <!-- Mobile Menu Overlay HTML -->
    <div id="rima-mobile-menu-overlay" class="rima-mobile-menu-overlay"></div>
    <div id="rima-mobile-menu" class="rima-mobile-menu">
        <div class="rima-mobile-menu-header">
            <h4><span class="rima-en">Menu</span><span class="rima-ro">Meniu</span></h4>
            <span id="rima-mobile-menu-close" class="rima-mobile-menu-close">&times;</span>
        </div>
        <div class="rima-mobile-menu-content">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_id'        => 'mobile-primary-menu',
                'menu_class'     => 'rima-mobile-nav',
                'fallback_cb'    => false,
            ) );
            ?>
        </div>
    </div>
"""
if "<!-- Mobile Menu Overlay HTML -->" not in content:
    content = content.replace('<!-- Mobile Bottom Navigation -->', html_addition + '\n    <!-- Mobile Bottom Navigation -->')

# Replace Contact link with Menu link
contact_html = """<a href="<?php echo esc_url( site_url( '/contact/' ) ); ?>" class="rima-mob-nav-item">
            <span class="rima-mob-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            </span>
            <span class="rima-en">Contact</span><span class="rima-ro">Contact</span>
        </a>"""
        
menu_html = """<a href="#" class="rima-mob-nav-item" id="rima-mobile-menu-toggle">
            <span class="rima-mob-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </span>
            <span class="rima-en">Menu</span><span class="rima-ro">Meniu</span>
        </a>"""

content = content.replace(contact_html, menu_html)

# Add Javascript logic
js_addition = """
        // Mobile Menu Logic
        $(document).on('click', '#rima-mobile-menu-toggle', function(e) {
            e.preventDefault();
            $('#rima-mobile-menu').addClass('rima-menu-open');
            $('#rima-mobile-menu-overlay').addClass('rima-menu-open');
        });

        $(document).on('click', '#rima-mobile-menu-close, #rima-mobile-menu-overlay', function(e) {
            $('#rima-mobile-menu').removeClass('rima-menu-open');
            $('#rima-mobile-menu-overlay').removeClass('rima-menu-open');
        });
"""

if "// Mobile Menu Logic" not in content:
    content = content.replace('// Mobile bottom nav cart trigger', js_addition + '\n        // Mobile bottom nav cart trigger')


with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("done")
