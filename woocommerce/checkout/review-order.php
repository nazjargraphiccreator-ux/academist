<?php
/**
 * Review order table (Custom Card Style)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="shop_table woocommerce-checkout-review-order-table rima-review-order-container" style="display: block !important; visibility: visible !important; width: 100%;">
    <div class="rima-order-summary-body">
        <!-- Products -->
        <div class="rima-order-products">
            <?php
            do_action( 'woocommerce_review_order_before_cart_contents' );

            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                    $product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                    $quantity = $cart_item['quantity'];
                    $product_id = $cart_item['product_id'];
                    $subtotal = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $quantity ), $cart_item, $cart_item_key );
                    ?>
                    <div class="rima-order-product card-style <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
                        <!-- Top Row: Image, Name, Remove -->
                        <div class="rima-product-top-row">
                            <div class="rima-product-image-wrapper">
                                <?php
                                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key );
                                echo $thumbnail; // CSS styles this directly
                                ?>
                            </div>
                            <div class="rima-product-info-wrapper">
                                <div class="rima-product-name-row">
                                    <div class="rima-order-product-name"><?php echo wp_kses_post($product_name); ?></div>
                                    <?php 
                                        echo sprintf(
                                            '<a href="%s" class="rima-remove-item" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fa fa-trash-alt"></i></a>',
                                            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                            esc_html__( 'Remove this item', 'woocommerce' ),
                                            esc_attr( $product_id ),
                                            esc_attr( $_product->get_sku() )
                                        );
                                    ?>
                                </div>
                                <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                            </div>
                        </div>

                        <!-- Middle Row: Price & Quantity -->
                        <div class="rima-product-middle-row">
                            <div class="rima-unit-price-wrapper">
                                <span class="rima-label-small"><?php esc_html_e('PREȚ UNITAR', 'rima-academy'); ?></span>
                                <span class="rima-price-value"><?php echo WC()->cart->get_product_price($_product); ?></span>
                            </div>
                            <div class="rima-quantity-wrapper-row">
                                <span class="rima-label-small"><?php esc_html_e('Cantitate:', 'rima-academy'); ?></span>
                                <div class="rima-order-product-qty-wrapper">
                                    <button type="button" 
                                            class="rima-checkout-qty-btn rima-qty-minus" 
                                            onclick="anaChangeQty(this, 'minus')"
                                            data-action="minus">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                    <input type="number" 
                                           name="cart[<?php echo esc_attr($cart_item_key); ?>][qty]"
                                           class="rima-order-qty-input qty" 
                                           value="<?php echo esc_attr($quantity); ?>" 
                                           min="1" 
                                           max="<?php echo esc_attr($_product->get_max_purchase_quantity()); ?>"
                                           data-cart-key="<?php echo esc_attr($cart_item_key); ?>"
                                           aria-label="<?php esc_attr_e('Cantitate produs', 'rima-academy'); ?>">
                                    <button type="button" 
                                            class="rima-checkout-qty-btn rima-qty-plus" 
                                            onclick="anaChangeQty(this, 'plus')"
                                            data-action="plus">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Row: Subtotal -->
                        <div class="rima-product-subtotal-row">
                            <?php 
                                printf(
                                    esc_html__('Subtotal produs: %s', 'rima-academy'), 
                                    '<span class="rima-subtotal-value">' . $subtotal . '</span>'
                                ); 
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }
            do_action( 'woocommerce_review_order_after_cart_contents' );
            ?>
        </div>

        <!-- Order Totals -->
        <div class="rima-order-totals">
            <div class="rima-order-summary-row">
                <span class="rima-order-summary-label"><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
                <span class="rima-order-summary-value"><?php wc_cart_totals_subtotal_html(); ?></span>
            </div>

            <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                <div class="rima-order-summary-row cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                    <span class="rima-order-summary-label"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
                    <span class="rima-order-summary-value"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
                </div>
            <?php endforeach; ?>

            <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
                <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
                <div class="rima-order-summary-row rima-shipping-row">
                     <span class="rima-order-summary-label" style="color: #1e293b !important; font-weight: 600;"><?php esc_html_e('Cost Livrare:', 'rima-academy'); ?></span>
                     <span class="rima-order-summary-value">
                        <?php wc_cart_totals_shipping_html(); ?>
                     </span>
                </div>
                <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
            <?php endif; ?>

            <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                <div class="rima-order-summary-row fee">
                    <span class="rima-order-summary-label"><?php echo esc_html( $fee->name ); ?></span>
                    <span class="rima-order-summary-value"><?php wc_cart_totals_fee_html( $fee ); ?></span>
                </div>
            <?php endforeach; ?>

            <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
                <?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
                    <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                        <div class="rima-order-summary-row tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                            <span class="rima-order-summary-label"><?php echo esc_html( $tax->label ); ?></span>
                            <span class="rima-order-summary-value"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="rima-order-summary-row tax-total">
                        <span class="rima-order-summary-label"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
                        <span class="rima-order-summary-value"><?php wc_cart_totals_taxes_total_html(); ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="rima-order-summary-row is-total">
                <span class="rima-order-summary-label"><?php esc_html_e( 'Total de plată:', 'woocommerce' ); ?></span>
                <span class="rima-order-summary-value"><?php wc_cart_totals_order_total_html(); ?></span>
            </div>
        </div>
    </div>
</div>
