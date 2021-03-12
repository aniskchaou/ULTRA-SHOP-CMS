<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$wc_measurement_price_calculator_activated = call_user_func( 'is_' . 'plugin' . '_active', 'woocommerce-measurement-price-calculator/woocommerce-measurement-price-calculator.php' );

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>
	<div class="cart_list-outer">
		<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">

			<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

						$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
						$thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( apply_filters( 'single_product_small_thumbnail_size', '60x60' ) ), $cart_item, $cart_item_key );
						$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>

						<li data-key="<?php echo esc_attr( $cart_item_key ); ?>" class="<?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">

							<div class="remove-item">
								<?php
									echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
										'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										__( 'Remove this item', 'woocommerce' ),
										esc_attr( $product_id ),
										esc_attr( $cart_item_key ),
										esc_attr( $_product->get_sku() )
									), $cart_item_key );
								?>
							</div>

							<div class="img-item-outer">
								<div class="img-item">
									<?php if ( ! $_product->is_visible() ) : ?>
										<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
									<?php else : ?>
										<a href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>">
											<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
										</a>
									<?php endif; ?>
								</div>
							</div>

							<div class="info-item">
								<h5 class="title-item">
									<?php echo '<a href="' . get_permalink( $cart_item['product_id'] ) . '">' . $product_name . '</a>'; ?>
								</h5>
								<div class="price-item">
									<?php
										if ( $_product->is_sold_individually() ) {
											$input_number = 1;
										} else {
											if ( $wc_measurement_price_calculator_activated && WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $_product ) && isset( $cart_item['pricing_item_meta_data']['_quantity'] ) && $cart_item['pricing_item_meta_data']['_quantity'] ) {
												$cart_item['quantity'] = $cart_item['pricing_item_meta_data']['_quantity'];
											}

											$input_number = '<input min="0" step="1" ' .  ( ( $_product->backorders_allowed() || intval( $_product->get_stock_quantity() ) == 0 ) ? '' : ' max="' . intval( $_product->get_stock_quantity() ) . '" data-max="' . intval( $_product->get_stock_quantity() ) . '"' ) . ' type="number" value="' . intval( $cart_item['quantity'] ) . '" data-value-old="' . intval( $cart_item['quantity'] ) . '" class="edit-number extenal-bdcl" />';
										}

										echo '<span class="quantity-minicart">' . sprintf( '%s <span class="multiplication">&times;</span> %s', '<span class="count-item">' . $input_number . '</span>' , $product_price ) . '</span>';
								 	?>
								</div>

								<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
							</div>
						</li>
						<?php
					}
				}
			?>
		</ul><!-- end product list -->
	</div>

	<div class="price-checkout">
		<p class="woocommerce-mini-cart__total total"><strong><?php esc_html_e( 'Subtotal', 'wr-nitro' ); ?>:</strong> <span class="mini-price"><?php echo WC()->cart->get_cart_subtotal(); ?></span></p>

		<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

		<p class="woocommerce-mini-cart__buttons buttons">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="wr-btn wr-btn-outline wc-forward"><?php esc_html_e( 'View Cart', 'wr-nitro' ); ?></a>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="button checkout"><?php esc_html_e( 'Checkout', 'wr-nitro' ); ?></a>
		</p>
	</div>

<?php else : ?>

	<p class="woocommerce-mini-cart__empty-message"><?php esc_html_e( 'No products in the cart.', 'wr-nitro' ); ?></p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
