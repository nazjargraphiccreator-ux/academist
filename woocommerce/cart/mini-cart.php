<?php
/**
 * Mini-cart override to match Photo 2 layout with premium AJAX +/- quantity controllers.
 *
 * @package WooCommerce/Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				
				// Find course URL to prevent redirect lag
				$course_url = '';
				$courses = get_posts(array(
					'post_type' => 'course',
					'meta_key' => 'eltdf_course_woo_product_meta',
					'meta_value' => $product_id,
					'posts_per_page' => 1
				));
				if (!empty($courses)) {
					$course_url = get_permalink($courses[0]->ID);
					$course_thumb = get_the_post_thumbnail($courses[0]->ID, 'thumbnail');
				} else {
					$course_url = $_product->get_permalink( $cart_item );
					$course_thumb = '';
				}

				if (!empty($course_thumb)) {
					$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $course_thumb, $cart_item, $cart_item_key );
				} else {
					$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
				}
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $course_url, $cart_item, $cart_item_key );
				?>
				<li class="woocommerce-mini-cart-item mini_cart_item rima-side-cart-item" data-key="<?php echo esc_attr( $cart_item_key ); ?>">
					<!-- Thumbnail -->
					<div class="rima-cart-item-image">
						<?php if ( empty( $product_permalink ) ) : ?>
							<?php echo $thumbnail; ?>
						<?php else : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>">
								<?php echo $thumbnail; ?>
							</a>
						<?php endif; ?>
					</div>

					<!-- Details -->
					<div class="rima-cart-item-details">
						<h5 class="rima-cart-item-title">
							<?php if ( empty( $product_permalink ) ) : ?>
								<?php echo esc_html( $product_name ); ?>
							<?php else : ?>
								<a href="<?php echo esc_url( $product_permalink ); ?>">
									<?php echo esc_html( $product_name ); ?>
								</a>
							<?php endif; ?>
						</h5>

						<!-- Price & Quantity Selector -->
						<div class="rima-cart-item-pricing-row">
							<div class="rima-cart-qty-wrapper">
								<button type="button" class="rima-cart-qty-btn rima-cart-qty-minus" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">&minus;</button>
								<input type="text" readonly class="rima-cart-qty-input" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" />
								<button type="button" class="rima-cart-qty-btn rima-cart-qty-plus" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">+</button>
							</div>
							
							<div class="rima-cart-item-price-times">
								<span class="rima-times-symbol">&times;</span>
								<span class="rima-item-price"><?php echo $product_price; ?></span>
							</div>
						</div>
					</div>

					<!-- Trash Button -->
					<div class="rima-cart-item-remove-wrapper">
						<?php
						echo apply_filters(
							'woocommerce_cart_item_remove_link',
							sprintf(
								'<a href="%s" class="remove remove_from_cart_button rima-cart-remove-btn" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>',
								esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
								/* translators: %s: Item name. */
								esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), $product_name ) ),
								esc_attr( $product_id ),
								esc_attr( $cart_item_key ),
								esc_attr( $_product->get_sku() )
							),
							$cart_item_key
						);
						?>
					</div>
				</li>
				<?php
			}
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<p class="woocommerce-mini-cart__total total rima-side-cart-total-box">
		<?php
		/**
		 * Hook: woocommerce_widget_shopping_cart_total.
		 *
		 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
		 */
		do_action( 'woocommerce_widget_shopping_cart_total' );
		?>
	</p>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<p class="woocommerce-mini-cart__buttons buttons rima-side-cart-buttons-box">
		<?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?>
	</p>

	<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

<?php else : ?>

	<p class="woocommerce-mini-cart__empty-message empty-msg-styled"><?php esc_html_e( 'No products in the cart.', 'woocommerce' ); ?></p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
