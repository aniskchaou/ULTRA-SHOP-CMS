<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
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
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$wr_nitro_options = WR_Nitro::get_options();
if ( ! $wr_nitro_options['wc_single_product_meta'] ) return;

global $post, $product;

// Get single style
$single_style = get_post_meta( get_the_ID(), 'single_style', true );
if ( $single_style == 0 ) {
	$single_style = $wr_nitro_options['wc_single_style'];
} else {
	$single_style = get_post_meta( get_the_ID(), 'single_style', true );
}

$cats = get_the_terms( $post->ID, 'product_cat' );
$cat_count = is_array( $cats ) ? count( $cats ) : 0;
$tags = get_the_terms( $post->ID, 'product_tag' );
$tag_count = is_array( $tags ) ? count( $tags ) : 0;

?>
<div class="product_meta p-meta mgb20 mgt20 clear">
	<?php do_action( 'woocommerce_product_meta_start' ); ?>

    <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in db mgb10">' . '<span class="fwb dib">'._n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'wr-nitro' ) . '</span><span class="posted_in_cat"> ', '</span></span>' ); ?>

    <?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . '<span class="fwb dib db">'._n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'wr-nitro' ) . '</span> ', '</span>' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><span class="fwb dib"><?php esc_html_e( 'SKU:', 'wr-nitro' ); ?></span><span class="sku"><?php echo esc_html( ($sku = $product->get_sku()) ? $sku : esc_html__( 'N/A', 'wr-nitro' ) ); ?></span></span>

	<?php endif; ?>

	<?php if ( ( $single_style == 2 || $single_style == 3 || $single_style == 4 ) && ! $product->is_type( 'variable' ) ) : ?>
		<span class="availability mgb10">
			<?php $availability = $product->get_availability(); ?>
			<span class="fwb dib"><?php esc_html_e( 'Availability', 'wr-nitro' ); ?></span>:
			<span class="stock <?php echo esc_attr( $product->is_in_stock() ? 'in-stock' : 'out-stock' ); ?>">
				<?php
					// Check product stock
					if ( $product->get_manage_stock() && ! empty( $availability['availability'] ) ) :
						echo esc_html( $availability['availability'] );
					elseif ( ! $product->get_manage_stock() && $product->is_in_stock() ) :
						esc_html_e( 'In Stock', 'wr-nitro' );
					else :
						esc_html_e( 'Out Of Stock', 'wr-nitro' );
					endif;
				?>
			</span>
		</span>
	<?php endif; ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>
</div>
