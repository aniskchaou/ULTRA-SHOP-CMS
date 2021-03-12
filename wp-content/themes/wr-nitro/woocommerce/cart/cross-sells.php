<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( WC()->version < '3.0.0' ) {
	global $product;

	$crosssells = WC()->cart->get_cross_sells();

	if ( sizeof( $crosssells ) == 0 ) return;

	$meta_query = WC()->query->get_meta_query();

	$args = array(
		'post_type'           => 'product',
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => 1,
		'posts_per_page'      => apply_filters( 'woocommerce_cross_sells_total', $posts_per_page ),
		'orderby'             => $orderby,
		'post__in'            => $crosssells,
		'meta_query'          => $meta_query,
		'suppress_filters'    => true,
	);

	$products = new WP_Query( $args );

	if ( $products->have_posts() ) : ?>

		<div class="cross-sells">

			<h4><?php esc_html_e( 'You may be interested in&hellip;', 'wr-nitro' ) ?></h4>

			<table class="shop_table">
				<tbody>
					<?php while ( $products->have_posts() ) : $products->the_post(); ?>
						<tr>
							<td class="product-thumbnail">
								<?php echo woocommerce_get_product_thumbnail( 'thumbnail'); ?>
							</td>
							<td class="product-name heading-color">
								<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
								<?php wc_get_template( 'loop/rating.php' ); ?>
							</td>
							<td class="product-price">
								<?php wc_get_template( 'loop/price.php' ); ?>
							</td>
							<td class="product-subtotal">
								<?php wc_get_template( 'loop/add-to-cart.php' ); ?>
							</td>
						</tr>
					<?php endwhile; // end of the loop. ?>
				</tbody>
			</table>
		</div>

	<?php endif;
} else {
	if ( $cross_sells ) : ?>

		<div class="cross-sells">

			<h4><?php esc_html_e( 'You may be interested in&hellip;', 'wr-nitro' ) ?></h4>

			<table class="shop_table">
				<tbody>
					<?php foreach ( $cross_sells as $cross_sell ) : ?>
						<?php
							$post_object = get_post( $cross_sell->get_id() );

							setup_postdata( $GLOBALS['post'] =& $post_object );
						?>
						<tr>
							<td class="product-thumbnail">
								<?php echo woocommerce_get_product_thumbnail( 'thumbnail'); ?>
							</td>
							<td class="product-name heading-color">
								<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
								<?php wc_get_template( 'loop/rating.php' ); ?>
							</td>
							<td class="product-price">
								<?php wc_get_template( 'loop/price.php' ); ?>
							</td>
							<td class="product-subtotal">
								<?php wc_get_template( 'loop/add-to-cart.php' ); ?>
							</td>
						</tr>
					<?php endforeach; // end of the loop. ?>
				</tbody>
			</table>

		</div>

	<?php endif;
}

wp_reset_postdata();
