<?php
/**
 * AnaCleaning Cart Template (Bilingual EN/RO)
 * 
 * Premium cart with modern design
 * 
 * @package AnaCleaning
 * @version 1.0.0
 * 
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.5.3
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');
?>



<div class="rima-cart-wrapper">

    <!-- Cart Products -->
    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <?php do_action('woocommerce_before_cart_table'); ?>
        
        <div class="rima-cart-products">
            
            <!-- Header -->
            <div class="rima-cart-header">
                <div class="rima-cart-header-cell">
                    <span class="rima-en">Product</span>
                    <span class="rima-ro">Produs</span>
                </div>
                <div class="rima-cart-header-cell">
                    <span class="rima-en">Price</span>
                    <span class="rima-ro">Preț</span>
                </div>
                <div class="rima-cart-header-cell">
                    <span class="rima-en">Quantity</span>
                    <span class="rima-ro">Cantitate</span>
                </div>
                <div class="rima-cart-header-cell">
                    <span class="rima-en">Subtotal</span>
                    <span class="rima-ro">Subtotal</span>
                </div>
                <div class="rima-cart-header-cell"></div>
            </div>
            
            <!-- Cart Items -->
            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                
                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) :
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
            ?>
                <div class="rima-order-product rima-premium-card <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                    
                    <!-- Top Row: Image, Name, Remove -->
                    <div class="rima-product-top-row">
                        <div class="rima-product-image-wrapper">
                            <?php
                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
                            $image_url = wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail');
                            if (!$image_url) $image_url = wc_placeholder_img_src('thumbnail');
                            ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product_name); ?>">
                        </div>
                        <div class="rima-product-info-wrapper">
                            <div class="rima-product-name-row">
                                <?php if (!$product_permalink) : ?>
                                    <div class="rima-order-product-name"><?php echo wp_kses_post($product_name); ?></div>
                                <?php else : ?>
                                    <a href="<?php echo esc_url($product_permalink); ?>" class="rima-order-product-name"><?php echo wp_kses_post($product_name); ?></a>
                                <?php endif; ?>

                                <?php
                                echo apply_filters(
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" class="rima-remove-item" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fa fa-trash-alt"></i></a>',
                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                        esc_html__( 'Remove this item', 'woocommerce' ),
                                        esc_attr($product_id),
                                        esc_attr($_product->get_sku())
                                    ),
                                    $cart_item_key
                                );
                                ?>
                            </div>
                            <?php
                            // Meta data
                            echo wc_get_formatted_cart_item_data($cart_item);

                            // Backorder notification
                            if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification"><span class="rima-en">Available on backorder</span><span class="rima-ro">Disponibil pe comandă</span></p>', $product_id));
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Middle Row: Price & Quantity -->
                    <div class="rima-product-middle-row">
                        <div class="rima-unit-price-wrapper">
                            <span class="rima-label-small">
                                <span class="rima-en">UNIT PRICE</span>
                                <span class="rima-ro">PREȚ UNITAR</span>
                            </span>
                            <span class="rima-price-value"><?php echo WC()->cart->get_product_price($_product); ?></span>
                        </div>
                        <div class="rima-quantity-wrapper-row">
                            <span class="rima-label-small">
                                <span class="rima-en">Quantity:</span>
                                <span class="rima-ro">Cantitate:</span>
                            </span>
                            <div class="rima-order-product-qty-wrapper">
                                <button type="button" class="rima-checkout-qty-btn rima-cart-qty-btn" data-action="minus">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <input type="number" 
                                       class="rima-order-qty-input rima-cart-qty-input" 
                                       name="cart[<?php echo esc_attr($cart_item_key); ?>][qty]"
                                       value="<?php echo esc_attr($cart_item['quantity']); ?>" 
                                       min="0" 
                                       max="<?php echo esc_attr($_product->get_max_purchase_quantity()); ?>"
                                       step="1"
                                       data-cart-key="<?php echo esc_attr($cart_item_key); ?>"
                                       inputmode="numeric">
                                <button type="button" class="rima-checkout-qty-btn rima-cart-qty-btn" data-action="plus">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Row: Subtotal -->
                    <div class="rima-product-subtotal-row">
                        <span class="rima-en">Product subtotal: </span>
                        <span class="rima-ro">Subtotal produs: </span>
                        <span class="rima-subtotal-value"><?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?></span>
                    </div>

                </div>
            <?php endif; endforeach; ?>
            
            <!-- Cart Actions -->
            <div class="rima-cart-actions">
                <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" class="rima-continue-shopping">
                    ← 
                    <span class="rima-en">Continue shopping</span>
                    <span class="rima-ro">Continuă cumpărăturile</span>
                </a>
                
                <button type="submit" class="rima-btn rima-btn-outline rima-btn-sm" name="update_cart" value="Update">
                    🔄 
                    <span class="rima-en">Update cart</span>
                    <span class="rima-ro">Actualizează coș</span>
                </button>
                
                <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
            </div>
            
        </div>
        
        <?php do_action('woocommerce_after_cart_table'); ?>
    </form>
    
    <!-- Cart Summary Sidebar -->
    <div class="rima-cart-summary">
        <h3 class="rima-cart-summary-title">
            <span class="rima-en">Cart Summary</span>
            <span class="rima-ro">Sumar Coș</span>
        </h3>
        
        <!-- Subtotal -->
        <div class="rima-cart-summary-row">
            <span class="rima-cart-summary-label">
                <span class="rima-en">Subtotal</span>
                <span class="rima-ro">Subtotal</span>
            </span>
            <span class="rima-cart-summary-value"><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>
        
        <!-- Coupon -->
        <?php if (wc_coupons_enabled()) : ?>
            <div class="rima-coupon-form">
                <input type="text" name="coupon_code" class="rima-coupon-input" id="rima_coupon_code" placeholder="Coupon code / Cod cupon" />
                <button type="button" class="rima-coupon-btn" id="rima_apply_coupon">
                    <span class="rima-en">Apply</span>
                    <span class="rima-ro">Aplică</span>
                </button>
            </div>
        <?php endif; ?>
        
        <!-- Applied Coupons -->
        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <div class="rima-cart-summary-row">
                <span class="rima-cart-summary-label">
                    <span class="rima-en">Coupon: </span>
                    <span class="rima-ro">Cupon: </span>
                    <?php echo esc_html($code); ?>
                </span>
                <span class="rima-cart-summary-value">-<?php wc_cart_totals_coupon_html($coupon); ?></span>
            </div>
        <?php endforeach; ?>

        <!-- Total -->
        <div class="rima-cart-summary-total">
            <span class="rima-cart-summary-total-label">
                <span class="rima-en">Total:</span>
                <span class="rima-ro">Total de plată:</span>
            </span>
            <span class="rima-cart-summary-total-value"><?php wc_cart_totals_order_total_html(); ?></span>
        </div>
        
        <!-- Desktop Actions -->
        <div class="rima-desktop-cart-actions">
            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="rima-btn rima-btn-primary rima-btn-lg rima-checkout-btn">
                <span class="rima-en">Proceed to Checkout</span>
                <span class="rima-ro">Finalizează Comanda</span>
            </a>
            
            <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" class="rima-btn rima-btn-outline rima-continue-shopping-desktop">
                ← 
                <span class="rima-en">Continue shopping</span>
                <span class="rima-ro">Continuă cumpărăturile</span>
            </a>
        </div>
        
    </div>
    
    <!-- Mobile Sticky Footer -->
    <div class="rima-mobile-sticky-footer">
        <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" class="rima-btn rima-btn-outline rima-btn-sm rima-sticky-btn">
            ← 
            <span class="rima-en">Continue</span>
            <span class="rima-ro">Continuă</span>
        </a>
        <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="rima-btn rima-btn-primary rima-btn-lg rima-checkout-btn rima-sticky-btn">
            <span class="rima-en">Checkout</span>
            <span class="rima-ro">Finalizează</span>
        </a>
    </div>
    
    <style>
    /* ── Mobile Sticky Footer Buttons ─────────────────────────────── */
    .rima-mobile-sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        gap: 10px;
        padding: 12px 16px;
        background: #fff;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.12);
        z-index: 9999;
    }

    .rima-mobile-sticky-footer .rima-btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 14px 12px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 12px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .rima-mobile-sticky-footer .rima-btn-outline {
        background: #fff;
        color: #00BFA6;
        border: 2px solid #00BFA6;
    }

    .rima-mobile-sticky-footer .rima-btn-primary {
        background: linear-gradient(135deg, #00BFA6 0%, #102d56 100%);
        color: #fff !important;
    }

    .rima-mobile-sticky-footer .rima-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(0,191,166,0.4);
        color: #fff;
    }

    /* Add padding so content isn't hidden under sticky footer on mobile */
    @media (max-width: 768px) {
        .rima-cart-wrapper {
            padding-bottom: 80px;
        }
    }

    /* Hide mobile sticky footer on desktop */
    @media (min-width: 769px) {
        .rima-mobile-sticky-footer {
            display: none !important;
        }
        
        /* Hide the bottom cart actions (Actualizează + Continuă) on desktop */
        .rima-cart-actions {
            display: none !important;
        }
        
        /* Make sidebar continue button match finalize button size */
        .rima-cart-summary .rima-btn-outline {
            padding: 15px 30px !important;
            font-size: 16px !important;
            width: 100% !important;
        }
    }
    </style>

</div>

<?php do_action('woocommerce_before_cart_collaterals'); ?>

<div class="cart-collaterals" style="display: none;">
    <?php
    /**
     * Hidden cart collaterals for WooCommerce compatibility
     */
    do_action('woocommerce_cart_collaterals');
    ?>
</div>

<?php do_action('woocommerce_after_cart'); ?>

<script>
(function($) {
    $(document).ready(function() {

        // Language switch logic for cart
        $('#rima-lang-switch-cart').on('click', function() {
            var isRo = document.body.classList.contains('rima-lang-ro');
            var newLang = isRo ? 'en' : 'ro';
            
            if (newLang === 'ro') {
                document.body.classList.add('rima-lang-ro');
            } else {
                document.body.classList.remove('rima-lang-ro');
            }
            
            localStorage.setItem('rima_lang', newLang);

            var domain = window.location.hostname;
            document.cookie = "googtrans=/en/" + newLang + "; path=/; domain=" + domain;
            document.cookie = "googtrans=/en/" + newLang + "; path=/";

            var select = document.querySelector('select.goog-te-combo');
            if (select) {
                select.value = newLang;
                select.dispatchEvent(new Event('change'));
            }
            
            // Save to server
            if (typeof rima_ajax_obj !== 'undefined') {
                $.post(rima_ajax_obj.ajax_url, {
                    action: 'rima_save_lang_pref',
                    lang:   newLang,
                    nonce:  rima_ajax_obj.nonce
                });
            }
        });

        // ── QUANTITY +/- BUTTONS ──────────────────────────────────────────
        var qtyUpdateTimer = null;

        // Update quantity input when +/- clicked
        $(document).on('click', '.rima-cart-qty-btn', function() {
            var $btn    = $(this);
            var action  = $btn.data('action');
            var $input  = $btn.closest('.rima-order-product-qty-wrapper').find('.rima-cart-qty-input');
            var current = parseInt($input.val(), 10) || 1;
            var min     = parseInt($input.attr('min'), 10) || 0;
            var max     = parseInt($input.attr('max'), 10) || 9999;
            var newVal  = (action === 'plus') ? current + 1 : current - 1;

            // Clamp between min and max
            newVal = Math.max(min, Math.min(max, newVal));
            $input.val(newVal);

            // Trigger debounced cart update
            clearTimeout(qtyUpdateTimer);
            qtyUpdateTimer = setTimeout(function() {
                triggerCartUpdate();
            }, 800);
        });

        // Also trigger update on manual input change
        $(document).on('change', '.rima-cart-qty-input', function() {
            clearTimeout(qtyUpdateTimer);
            qtyUpdateTimer = setTimeout(function() {
                triggerCartUpdate();
            }, 800);
        });

        function triggerCartUpdate() {
            $('button[name="update_cart"]').prop('disabled', false).trigger('click');
        }

        // ── APPLY COUPON ──────────────────────────────────────────────────
        $('#rima_apply_coupon').on('click', function() {
            var couponCode = $('#rima_coupon_code').val();
            if (!couponCode) return;
            
            $.ajax({
                url: wc_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
                type: 'POST',
                data: {
                    security: wc_cart_params.apply_coupon_nonce,
                    coupon_code: couponCode
                },
                success: function() {
                    location.reload();
                }
            });
        });
        
        // Enter key for coupon
        $('#rima_coupon_code').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#rima_apply_coupon').trigger('click');
            }
        });

    });
})(jQuery);
</script>
