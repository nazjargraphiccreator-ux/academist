<?php
/* ============================================================
   12. DIRECT BANK TRANSFER (BACS) PAYMENT PROOF UPLOAD
   ============================================================ */

// Display upload box on view order page
add_action( 'woocommerce_order_details_after_order_table', 'rima_add_payment_proof_upload_section', 10, 1 );
function rima_add_payment_proof_upload_section( $order ) {
    if ( ! is_a( $order, 'WC_Order' ) ) return;
    if ( $order->get_payment_method() !== 'bacs' ) return;
    
    $allowed_statuses = array( 'on-hold', 'pending', 'failed' );
    $order_status = $order->get_status();
    $proof_id = get_post_meta( $order->get_id(), '_rima_payment_proof', true );
    $proof_url = $proof_id ? wp_get_attachment_url( $proof_id ) : '';

    if ( ! in_array( $order_status, $allowed_statuses ) ) {
        if ( $proof_id ) {
            ?>
            <div class="rima-proof-container success-uploaded mt-4 p-4 rounded-4 border bg-white shadow-sm">
                <h4 class="h5 fw-bold mb-2 text-dark">
                    <span class="rima-en">Payment Proof</span>
                    <span class="rima-ro">Dovadă Plată</span>
                </h4>
                <p class="text-muted small">
                    <span class="rima-en">The payment proof has been uploaded and verified.</span>
                    <span class="rima-ro">Dovada de plată a fost trimisă și verificată.</span>
                </p>
                <a href="<?php echo esc_url($proof_url); ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-3">
                    <i class="fa fa-file-text-o me-2"></i><?php _e('Vizualizează dovada', 'rima-academy'); ?>
                </a>
            </div>
            <?php
        }
        return;
    }

    ?>
    <div class="rima-proof-container mt-4 p-4 rounded-4 border bg-white shadow-sm">
        <h4 class="h5 fw-bold mb-2 text-dark">
            <span class="rima-en">Upload Payment Proof</span>
            <span class="rima-ro">Încarcă Dovada Plății</span>
        </h4>
        <p class="text-muted small mb-3">
            <span class="rima-en">For direct bank transfer orders, please upload a copy of the payment receipt (PDF, PNG, JPG - max 5MB) to speed up course activation.</span>
            <span class="rima-ro">Pentru plățile prin transfer bancar, te rugăm să încarci o copie a dovezii de plată (PDF, PNG, JPG - max 5MB) pentru a grăbi activarea contului.</span>
        </p>

        <div class="rima-upload-box p-4 border border-2 border-dashed rounded-3 text-center position-relative <?php echo $proof_id ? 'has-file' : ''; ?>" id="rima-proof-dropzone" style="cursor: pointer; border-color: #cbd5e1; background-color: #f8fafc; transition: all 0.3s ease;">
            <input type="file" id="rima-proof-file-input" accept="application/pdf,image/png,image/jpeg,image/jpg" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" style="z-index: 2; cursor: pointer;">
            
            <div class="rima-upload-box-idle" style="<?php echo $proof_id ? 'display:none;' : ''; ?>">
                <i class="fa fa-cloud-upload text-muted mb-2" style="font-size: 32px;"></i>
                <p class="mb-1 fw-semibold text-dark">
                    <span class="rima-en">Click to upload or drag & drop</span>
                    <span class="rima-ro">Apasă pentru a alege fișierul sau trage-l aici</span>
                </p>
                <span class="text-muted small">PDF, PNG, JPG (max 5MB)</span>
            </div>

            <div class="rima-upload-box-success" style="<?php echo $proof_id ? '' : 'display:none;'; ?>">
                <i class="fa fa-check-circle text-success mb-2" style="font-size: 32px;"></i>
                <p class="mb-1 fw-semibold text-success">
                    <span class="rima-en">Proof Uploaded Successfully</span>
                    <span class="rima-ro">Dovada a fost încărcată cu succes</span>
                </p>
                <div class="d-flex justify-content-center gap-2 mt-2 position-relative" style="z-index: 3;">
                    <a href="<?php echo esc_url($proof_url); ?>" id="rima-proof-preview-link" target="_blank" class="btn btn-sm btn-outline-secondary rounded-2" style="<?php echo $proof_url ? '' : 'display:none;'; ?>">
                        <i class="fa fa-eye"></i> <span class="rima-en">View File</span><span class="rima-ro">Vezi Fișier</span>
                    </a>
                    <button type="button" id="rima-proof-change-btn" class="btn btn-sm btn-outline-danger rounded-2">
                        <i class="fa fa-refresh"></i> <span class="rima-en">Change File</span><span class="rima-ro">Schimbă Fișier</span>
                    </button>
                </div>
            </div>
            
            <div class="rima-upload-box-progress" style="display: none;">
                <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0 fw-semibold text-primary">Se încarcă fișierul...</p>
            </div>
        </div>
        <div id="rima-proof-error-msg" class="text-danger small mt-2" style="display: none;"></div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        const dropzone = $('#rima-proof-dropzone');
        const fileInput = $('#rima-proof-file-input');
        const idleState = $('.rima-upload-box-idle');
        const successState = $('.rima-upload-box-success');
        const progressState = $('.rima-upload-box-progress');
        const errorMsg = $('#rima-proof-error-msg');
        const previewLink = $('#rima-proof-preview-link');
        const orderId = <?php echo $order->get_id(); ?>;

        fileInput.on('change', function() {
            const file = this.files[0];
            if (!file) return;

            // Validate file type
            const allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                errorMsg.text('Format invalid. Sunt permise doar fișiere PDF, PNG și JPG.').show();
                return;
            }

            // Validate size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                errorMsg.text('Fișierul este prea mare. Lăsați dimensiunea maximă de 5MB.').show();
                return;
            }

            errorMsg.hide();
            idleState.hide();
            successState.hide();
            progressState.show();

            const formData = new FormData();
            formData.append('proof_file', file);
            formData.append('order_id', orderId);
            formData.append('action', 'rima_upload_payment_proof');
            formData.append('nonce', '<?php echo wp_create_nonce("rima_proof_nonce_" . $order->get_id()); ?>');

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    progressState.hide();
                    if (response.success) {
                        previewLink.attr('href', response.data.url);
                        previewLink.show();
                        successState.show();
                        dropzone.addClass('has-file');
                    } else {
                        idleState.show();
                        errorMsg.text(response.data || 'A apărut o eroare la încărcare.').show();
                    }
                },
                error: function() {
                    progressState.hide();
                    idleState.show();
                    errorMsg.text('Eroare de rețea. Te rugăm să încerci din nou.').show();
                }
            });
        });

        $('#rima-proof-change-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.val('');
            dropzone.removeClass('has-file');
            successState.hide();
            idleState.show();
        });
    });
    </script>
    <style>
    .rima-upload-box:hover {
        border-color: #102d56 !important;
        background-color: #f1f5f9 !important;
    }
    .rima-upload-box.has-file {
        border-style: solid !important;
        border-color: #10b981 !important;
        background-color: #f0fdf4 !important;
    }
    .cursor-pointer { cursor: pointer; }
    </style>
    <?php
}

// Handle AJAX Upload request
add_action( 'wp_ajax_rima_upload_payment_proof', 'rima_ajax_handle_payment_proof_upload' );
function rima_ajax_handle_payment_proof_upload() {
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    
    if ( ! $order_id ) {
        wp_send_json_error( 'Comandă invalidă.' );
    }

    // Verify nonce
    check_ajax_referer( 'rima_proof_nonce_' . $order_id, 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'Nu ești autentificat.' );
    }

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        wp_send_json_error( 'Comanda nu a fost găsită.' );
    }

    // Verify ownership
    if ( $order->get_customer_id() !== get_current_user_id() ) {
        wp_send_json_error( 'Nu ai permisiunea de a modifica această comandă.' );
    }

    if ( empty($_FILES['proof_file']) ) {
        wp_send_json_error( 'Niciun fișier selectat.' );
    }

    $file = $_FILES['proof_file'];

    // Types
    $allowed_types = array( 'application/pdf', 'image/png', 'image/jpeg', 'image/jpg' );
    if ( ! in_array( $file['type'], $allowed_types ) ) {
        wp_send_json_error( 'Format de fișier nepermis. Doar PDF, PNG sau JPG.' );
    }

    // Size
    if ( $file['size'] > 5 * 1024 * 1024 ) {
        wp_send_json_error( 'Fișierul depășește limita de 5MB.' );
    }

    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );

    $attachment_id = media_handle_upload( 'proof_file', $order_id );

    if ( is_wp_error( $attachment_id ) ) {
        wp_send_json_error( 'Eroare la salvare: ' . $attachment_id->get_error_message() );
    }

    update_post_meta( $order_id, '_rima_payment_proof', $attachment_id );

    wp_send_json_success( array(
        'url' => wp_get_attachment_url( $attachment_id )
    ) );
}

// Add Admin Metabox to show the payment proof in WooCommerce order details page
add_action( 'add_meta_boxes', 'rima_add_order_payment_proof_metabox' );
function rima_add_order_payment_proof_metabox() {
    // Screen compatibility for HPOS & standard tables
    $screen = class_exists( 'Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrderStatusTableController' ) && 
              method_exists('Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrderStatusTableController', 'is_active') &&
              Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrderStatusTableController::is_active() 
              ? wc_get_page_screen_id( 'shop-order' ) 
              : 'shop_order';

    add_meta_box(
        'rima_payment_proof_box',
        'ðŸ“„ Dovadă Plată / Payment Proof',
        'rima_render_payment_proof_metabox',
        $screen,
        'side',
        'default'
    );
}

function rima_render_payment_proof_metabox( $post_or_order_object ) {
    // Resolve order depending on HPOS or normal meta boxes
    $order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
    if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
        echo '<p>Comandă invalidă.</p>';
        return;
    }

    if ( $order->get_payment_method() !== 'bacs' ) {
        echo '<p class="description">Această comandă nu folosește ca metodă de plată Transferul Bancar.</p>';
        return;
    }

    $proof_id = get_post_meta( $order->get_id(), '_rima_payment_proof', true );

    if ( ! $proof_id ) {
        echo '<div class="notice notice-warning inline" style="margin: 0 0 10px 0; padding: 8px;"><p style="margin: 0;">âš ï¸ Utilizatorul nu a încărcat încă dovada de plată.</p></div>';
        return;
    }

    $proof_url = wp_get_attachment_url( $proof_id );
    $file_path = get_attached_file( $proof_id );
    $file_ext  = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

    echo '<div style="margin-bottom: 12px;">';
    echo '<p style="margin-top: 0;"><strong>Dovadă încărcată:</strong></p>';
    
    if ( in_array( $file_ext, array('png', 'jpg', 'jpeg') ) ) {
        echo '<a href="' . esc_url($proof_url) . '" target="_blank">';
        echo '<img src="' . esc_url($proof_url) . '" style="max-width: 100%; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 8px; display: block;" />';
        echo '</a>';
    } else {
        echo '<div style="background: #f1f1f1; padding: 12px; border-radius: 4px; text-align: center; margin-bottom: 8px;">';
        echo '<span class="dashicons dashicons-pdf" style="font-size: 40px; width: 40px; height: 40px; color: #d63636; line-height:1;"></span>';
        echo '<p style="margin: 5px 0 0 0; font-size: 12px;">Fișier PDF</p>';
        echo '</div>';
    }

    echo '<a href="' . esc_url($proof_url) . '" target="_blank" class="button button-primary button-large" style="width: 100%; text-align: center;">';
    echo '<span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> Deschide / Descarcă Dovada';
    echo '</a>';
    echo '</div>';
}

// Force "Apply Now" button to directly Add to Cart on Course Pages
add_action('wp_footer', function() {
    if (is_singular('course')) {
        global $post;
        // Academist LMS usually links a WooCommerce product ID in post meta
        $product_id = get_post_meta($post->ID, 'eltdf_course_woo_product_meta', true);
        
        // Output JS to override the Apply Now button regardless
        ?>
        <script>
        jQuery(document).ready(function($) {
            var productId = '<?php echo esc_js($product_id); ?>';
            if (!productId) return;

            function doAjaxAddToCart($btn, productId) {
                var isRo = $('body').hasClass('rima-lang-ro') || (localStorage.getItem('rima_lang') === 'ro');
                var originalText = $btn.text();
                $btn.text(isRo ? 'Se adaugă...' : 'Adding...');
                $btn.css({'opacity': '0.7', 'pointer-events': 'none'});
                
                // Use our custom silent endpoint to prevent WC notices from saving in the session
                        // Trigger fragment refresh to update cart counters
                        $(document.body).trigger('wc_fragment_refresh');
                        
                        var productName = $('.rsc-hero-title').text().trim() || 'Course';
                        var msg = isRo ? '"' + productName + '" a fost adăugat în coș!' : '"' + productName + '" has been added to cart!';
                        
                        var $toast = $('#rima-cart-toast');
                        if($toast.length) {
                            $toast.text(msg).addClass('show-toast');
                            setTimeout(function() {
                                $toast.removeClass('show-toast');
                            }, 4000);
                        }
                        
                        // Bounce the header cart icon
                        var $cartHolder = $('.eltdf-shopping-cart-holder');
                        if($cartHolder.length) {
                            $cartHolder.addClass('rima-cart-bounce');
                            setTimeout(function() {
                                $cartHolder.removeClass('rima-cart-bounce');
                            }, 400);
                        }
                    } else {
                        var errMsg = 'Error adding to cart.';
                        if (response && response.data && response.data.message) {
                            errMsg = response.data.message;
                        }
                        alert(errMsg);
                        $btn.text(originalText);
                        $btn.css({'opacity': '1', 'pointer-events': 'auto'});
                    }
                }).fail(function() {
                    $btn.text(originalText);
                    $btn.css({'opacity': '1', 'pointer-events': 'auto'});
                });
            }

            // 1. Intercept standard WooCommerce cart form (Logged In Users)
            $('form.cart').on('submit', function(e) {
                e.preventDefault();
                var $btn = $(this).find('button[type="submit"]');
                doAjaxAddToCart($btn, productId);
            });

            // 2. Intercept Academist login/apply buttons (Logged Out Users)
            var $applyBtns = $('.eltdf-lms-buy-button, .eltdf-btn[href*="user-dashboard"]');
            if ($applyBtns.length) {
                $applyBtns.each(function() {
                    var $originalBtn = $(this);
                    var $clone = $originalBtn.clone(false); // Clone without events
                    $clone.removeClass('eltdf-lms-buy-button eltdf-lms-buy-button-opened');
                    $clone.addClass('rima-custom-add-to-cart');
                    $clone.attr('href', 'javascript:void(0);');
                    $originalBtn.replaceWith($clone);
                });
                
                // Use event delegation on the cloned buttons
                $(document).off('click', '.rima-custom-add-to-cart').on('click', '.rima-custom-add-to-cart', function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    doAjaxAddToCart($(this), productId);
                });
            }

            // Force "View Cart" to say "Add to Cart" for consistency on reload
            $('.eltdf-btn').each(function() {
                var txt = $(this).text().trim().toLowerCase();
                if (txt === 'view cart' || txt === 'apply now') {
                    var isRo = $('body').hasClass('rima-lang-ro');
                    $(this).text(isRo ? 'Adaugă în Coș' : 'Add to Cart');
                }
            });
            
            // Hide standard WooCommerce notices on this page if they appear on load
            $('.woocommerce-notices-wrapper, .woocommerce-message, .woocommerce-error, .woocommerce-info').hide();
        });
        </script>
        <?php
    }
}, 999);

/**
 * ==============================================================
 * RIMA ACADEMY: Auto-sync LMS Courses with WooCommerce Products
 * ==============================================================
 */
add_action('save_post_course', 'rima_auto_sync_course_with_woo', 10, 3);
function rima_auto_sync_course_with_woo($post_id, $post, $update) {
    // Don't run on autosave or revisions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;
    
    // Check if it's a valid course
    if ($post->post_status !== 'publish' && $post->post_status !== 'draft') return;

    // Get the course price from Academist meta 
    $price = get_post_meta($post_id, 'eltdf_course_price_meta', true);
    if (empty($price)) $price = '0'; 

    // Check if a product is already linked
    $product_id = get_post_meta($post_id, 'eltdf_course_woo_product_meta', true);
    $product = $product_id ? wc_get_product($product_id) : false;
    $thumbnail_id = get_post_thumbnail_id($post_id);

    if ($product_id && $product) {
        // Update existing product price, title, image
        $product->set_regular_price($price);
        $product->set_name($post->post_title);
        if ($thumbnail_id) {
            $product->set_image_id($thumbnail_id);
        }
        $product->save();
        update_post_meta($product_id, '_rima_linked_course_id', $post_id);
    } else {
        // Create new hidden virtual product
        $product = new WC_Product_Simple();
        $product->set_name($post->post_title);
        $product->set_status('publish');
        $product->set_catalog_visibility('hidden');
        $product->set_virtual(true);
        $product->set_sold_individually(true); // Can only buy one of each course
        $product->set_regular_price($price);
        if ($price > 0) {
            $product->set_price($price);
        }
        if ($thumbnail_id) {
            $product->set_image_id($thumbnail_id);
        }
        
        $new_product_id = $product->save();

        if ($new_product_id) {
            // Link it to the course natively for Academist
            update_post_meta($post_id, 'eltdf_course_woo_product_meta', $new_product_id);
            // Optionally link product back to course
            update_post_meta($new_product_id, '_rima_linked_course_id', $post_id);
        }
    }
}

// Admin Trigger to sync all existing courses (Run once)
add_action('admin_init', function() {
    if (isset($_GET['rima_sync_courses']) && current_user_can('manage_options')) {
        $courses = get_posts(array(
            'post_type' => 'course',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ));
        foreach ($courses as $course) {
            rima_auto_sync_course_with_woo($course->ID, $course, true);
        }
        wp_die('Toate cursurile (' . count($courses) . ') au fost sincronizate cu WooCommerce și li s-au creat produse ascunse! Poti inchide aceasta pagina și verifica pe site.');
    }
});

// Bypass stubborn page caches (Cloudflare/WP Rocket) by injecting the JS fix via WooCommerce AJAX fragments!
// Since WooCommerce AJAX is never cached, this guarantees the script runs for all users, even on cached HTML pages.
add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    $inject_js = '<script>
        document.addEventListener("click", function(e) {
            var cartTarget = e.target.closest(".eltdf-shopping-cart-holder, .eltdf-header-cart, .eltdf-cart-icon");
            if (cartTarget) {
                e.preventDefault();
                e.stopPropagation();
                var sideCart = document.getElementById("rima-side-cart");
                var overlay = document.getElementById("rima-side-cart-overlay");
                if (sideCart) sideCart.classList.add("rima-cart-open");
                if (overlay) overlay.classList.add("rima-cart-open");
            }
        }, true);
        
        var sv = \'<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>\';
        var iconNode = document.querySelector(".eltdf-cart-icon");
        if(iconNode) iconNode.innerHTML = sv;
    </script>';

    if (is_array($fragments)) {
        foreach ($fragments as $key => $html) {
            if (strpos($html, 'eltdf-cart-icon') !== false) {
                $fragments[$key] .= $inject_js;
                break; // only inject once
            }
        }
    }
    return $fragments;
}, 999);

add_action('template_redirect', function() {
    if (function_exists('is_shop') && is_shop()) {
        wp_safe_redirect(home_url('/our-courses/'), 301);
        exit;
    }
    
    // Only redirect product pages on GET requests to allow POST form submissions (like Add to Cart) to process
    if (is_singular('product') && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        $product_id = get_the_ID();
        $courses = get_posts(array(
            'post_type' => 'course',
            'meta_key' => 'eltdf_course_woo_product_meta',
            'meta_value' => $product_id,
            'posts_per_page' => 1
        ));
        if (!empty($courses)) {
            wp_safe_redirect(get_permalink($courses[0]->ID), 301);
            exit;
        }
    }
});

/**
 * ==============================================================
 * RIMA ACADEMY: Make single course Add to Cart form submit to current course URL
 * ==============================================================
 */
add_filter('woocommerce_add_to_cart_form_action', function($action) {
    if (is_singular('course')) {
        return ''; // submitting to empty action posts to the current page URL
    }
    return $action;
}, 99);

/**
 * ==============================================================
 * RIMA ACADEMY: Enqueue WooCommerce AJAX Add to Cart script on course listing/archive and single course pages
 * ==============================================================
 */
add_action('wp_enqueue_scripts', function() {
    if (is_singular('course') || is_post_type_archive('course') || is_page('our-courses')) {
        if (function_exists('is_woocommerce')) {
            wp_enqueue_script('wc-add-to-cart');
        }
    }
}, 30);

/**
 * ==============================================================
 * RIMA ACADEMY: Force Child Theme's single-course.php template
 * ==============================================================
 */
add_filter('single_template', function($template) {
    global $post;
    if (!empty($post) && $post->post_type === 'course') {
        $child_template = get_stylesheet_directory() . '/single-course.php';
        if (file_exists($child_template)) {
            return $child_template;
        }
    }
    return $template;
}, 999);

/**
 * ==============================================================
 * RIMA ACADEMY: Remove Forum Tab from Single Course Page
 * ==============================================================
 */
add_filter('academist_elated_filter_single_course_tabs', function($tabs) {
    if (isset($tabs['forum'])) {
        unset($tabs['forum']);
    }
    return $tabs;
}, 99);


require_once get_stylesheet_directory() . '/inc/courses-listing.php';

