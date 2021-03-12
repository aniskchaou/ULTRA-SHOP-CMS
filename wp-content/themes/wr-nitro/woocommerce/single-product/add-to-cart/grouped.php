<?php
/**
 * Grouped product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/grouped.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $post;

// Get theme option
$wr_nitro_options = WR_Nitro::get_options();

// Catalog mode
$wr_catalog_mode = $wr_nitro_options['wc_archive_catalog_mode'];
if ( $wr_catalog_mode ) return;

// Get single style
$single_style = get_post_meta( get_the_ID(), 'single_style', true );
if ( $single_style == 0 ) {
	$single_style = $wr_nitro_options['wc_single_style'];
} else {
	$single_style = get_post_meta( get_the_ID(), 'single_style', true );
}

// Icon Set
$icons = $wr_nitro_options['wc_icon_set'];

$check_gravityforms = WR_Nitro_Helper::check_gravityforms( $product->get_id() );

$add_to_cart_ajax = true;
if ( $check_gravityforms || ( get_option('woocommerce_enable_ajax_add_to_cart_single') == 'no' && ! (int) $wr_nitro_options['wc_buynow_btn'] ) ) {
	$add_to_cart_ajax = false;
}

do_action( 'woocommerce_before_add_to_cart_form' );
?>

<?php
	if ( $single_style != 1 ) {
		echo '<div class="p-single-action nitro-line btn-inline pdb20 fc aic aife">';
	}
?>
	<form class="cart" method="post" enctype='multipart/form-data'>
		<table cellspacing="0" class="woocommerce-grouped-product-list group_table">
			<tbody>
				<?php
					$quantites_required = false;
					$previous_post      = $post;

					foreach ( $grouped_products as $grouped_product ) :
						$post_object        = get_post( $grouped_product->get_id() );
						$quantites_required = $quantites_required || ( $grouped_product->is_purchasable() && ! $grouped_product->has_options() );

						setup_postdata( $post =& $post_object );
						?>
						<tr>
							<td class="group-thumbnail">
								<?php
									if ( has_post_thumbnail() ) {
										echo apply_filters( 'woocommerce_group_product_image_thumbnail', sprintf( '<a href="%s">%s</a>', get_permalink(), get_the_post_thumbnail( $post->ID, 'shop_thumbnail' ) ) );
									}
								?>
							</td>

							<td class="group-title">
								<label class="db" for="product-<?php echo esc_attr( $grouped_product->get_id() ); ?>">
									<?php echo '' . ( $grouped_product->is_visible() ? '<a href="' . esc_url( apply_filters( 'woocommerce_grouped_product_list_link', get_permalink( $grouped_product->get_id() ) ) ) . '">' . get_the_title( $grouped_product->get_id() ) . '</a>' : get_the_title( $grouped_product->get_id() ) ); ?>
								</label>

								<?php do_action( 'woocommerce_grouped_product_list_before_price', $grouped_product ); ?>

								<?php
									echo '' . $grouped_product->get_price_html();
									echo wc_get_stock_html( $grouped_product );
								?>
							</td>

							<td class="group-quantity">
								<?php if ( ! $grouped_product->is_purchasable() || $grouped_product->has_options() ) : ?>
									<?php woocommerce_template_loop_add_to_cart(); ?>

								<?php elseif ( $grouped_product->is_sold_individually() ) : ?>
									<input type="checkbox" name="<?php echo esc_attr( 'quantity[' . $grouped_product->get_id() . ']' ); ?>" value="1" class="wc-grouped-product-add-to-cart-checkbox" />

								<?php else : ?>
									<?php
										/**
										 * @since 3.0.0.
										 */
										do_action( 'woocommerce_before_add_to_cart_quantity' );

										woocommerce_quantity_input( array(
											'input_name'  => 'quantity[' . $grouped_product->get_id() . ']',
											'input_value' => isset( $_POST['quantity'][ $grouped_product->get_id() ] ) ? wc_stock_amount( $_POST['quantity'][ $grouped_product->get_id() ] ) : 0,
											'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 0, $grouped_product ),
											'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $grouped_product->get_max_purchase_quantity(), $grouped_product ),
										) );

										/**
										 * @since 3.0.0.
										 */
										do_action( 'woocommerce_after_add_to_cart_quantity' );
									?>
								<?php endif; ?>
							</td>
						</tr>
						<?php
					endforeach;

					// Reset to parent grouped product
					$post = $previous_post;

					// Return data to original post.
					setup_postdata( $post =& $previous_post );
				?>
			</tbody>
		</table>

		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />

		<?php if ( $quantites_required ) : ?>

			<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

			<button type="submit" class="single_add_to_cart_button button alt <?php echo esc_attr( $add_to_cart_ajax ? 'wr_single_add_to_cart_ajax' : NULL ); ?>"><i class="nitro-icon-<?php echo esc_attr( $icons ); ?>-cart mgr10 mgt10"></i><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

		<?php endif; ?>
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
<?php
	if ( $single_style != 1 ) {
		echo '</div>';
	}
?>
