<?php
/**
 * Recent viewed Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce, $related_product;

$related_product = true;

$wr_nitro_options = WR_Nitro::get_options();

$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
$viewed_products = array_filter( array_map( 'absint', $viewed_products ) );
if ( empty( $viewed_products ) || ! $wr_nitro_options['wc_single_product_recent_viewed'] ) {
	return;
}

// Get number of product want to show
$limit = $wr_nitro_options['wc_single_product_show'];

// Get product item layout
$layout =  $wr_nitro_options['wc_archive_item_layout'];

$args = apply_filters( 'woocommerce_recent_viewd_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => 12,
	'orderby'              => 'rand',
	'post__in'             => $viewed_products,
	'post__not_in'         => array( $product->get_id() ),
	'suppress_filters'     => true,
) );

$products = new WP_Query( $args );

// Get number of post in loop
$count = $products->post_count;

// Set product class
if ( '5' == $limit ) {
	$class = 'columns-5 fl cs-6 cxs-12';
} else {
	$class = 'cm-' . 12 / $limit . ' cs-6 cxs-12';
}
if ( $products->have_posts() && $count > 1 ) : ?>
	<div class="p-recent-viewed mgt60 mgb30">
		<h3 class="wc-heading tc tu"><?php esc_html_e( 'Recent Viewed Products', 'wr-nitro' ); ?></h3>
		<div class="products grid <?php if ( $count > $limit || wp_is_mobile() && $count > 1 ) echo 'wr-nitro-carousel '; if ( wp_is_mobile() ) echo 'mobile-layout mobile-grid mobile-grid-layout'; ?>" data-owl-options='<?php echo '{"items": "' . $limit . '", "dots": "true", "tablet":"3","mobile":"2"' . ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ) . '}' ?>'>
			<?php while ( $products->have_posts() ) : $products->the_post(); ?>
				<?php if ( $product->get_id() != get_the_ID() ) : ?>
					<?php if ( wp_is_mobile() ) : ?>
						<?php wc_get_template( 'woorockets/content-product/style-mobile.php' ); ?>
					<?php else: ?>
						<div <?php post_class( $class ); ?>>
							<?php wc_get_template( 'woorockets/content-product/style-' . esc_attr( $layout ) . '.php' ); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endwhile; ?>
		</div>
	</div>
<?php endif; ?>

<?php wp_reset_postdata(); ?>
