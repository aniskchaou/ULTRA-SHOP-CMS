<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post;

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

$wr_is_custom_attribute_activated = class_exists( 'WR_Custom_Attributes' ) ? true : false;

$wr_product_class = $wr_hover_class = '';

// Extra post classes
$wr_classes = array();

array_push( $wr_classes, $wr_product_class );

// Hover style
$wr_hover_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['hover_style'] : $wr_nitro_options['wc_archive_item_hover_style'];
if ( 'default' == $wr_hover_style ) {
	$wr_hover_class = '';
} elseif ( $wr_hover_style == 'flip-back' ) {
	// Flip back effect
	$wr_flip_effect = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['transition_effects'] : $wr_nitro_options['wc_archive_item_transition'];

	// Hover class
	$wr_hover_class .= $wr_hover_style . ' ' . $wr_flip_effect;
} else {
	$wr_hover_class = $wr_hover_style;
}

// Show Compare
$wr_show_compare = $wr_nitro_options['wc_general_compare'];

// Show Wishlist
$wr_show_wishlist = $wr_nitro_options['wc_general_wishlist'];

// Catalog mode
$wr_catalog_mode = $wr_nitro_options['wc_archive_catalog_mode'];

// Show price
$wr_show_price = $wr_nitro_options['wc_archive_catalog_mode_price'];

// Icon Set
$wr_icons = $wr_nitro_options['wc_icon_set'];
?>

<div class="product__image fl oh pr <?php echo esc_attr( $wr_hover_class ); ?>">
	<?php

		if ( $wr_hover_style == 'mask' )
			echo '<span class="mask-inner"></span>';

		if ( $wr_hover_style == 'flip-back' ) {
			wc_get_template( 'woorockets/content-product/product-image-double.php' );
		} else {
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			echo '<a class="db" href="' . esc_url( get_permalink() ) . '">';
				do_action( 'woocommerce_before_shop_loop_item_title' ) ;
			echo '</a>';
		}
	?>
	<?php
		$stock_status = get_post_meta( $post->ID, '_stock_status', true );
		if ( $stock_status == 'outofstock' ) {
			echo '<div class="product__status pa tu color-white fwb">' . esc_html__( 'Out Of Stock', 'wr-nitro' ) . '</div>';
		}
	?>
</div><!-- .product__image -->

<div class="product__list fl pdl30">
	<?php 
		if ( $wr_nitro_options['wc_display_custom_attr_position'] == 'before-title' && $wr_is_custom_attribute_activated ) {
			do_action( 'wc_display_custom_attr' ); 
		}
	?>
	<div class="product__title">
		<h3 class="mg0"><a class="hover-primary" href="<?php esc_url( the_permalink() ); ?>" title="<?php esc_attr( the_title() ); ?>"><?php the_title(); ?></a></h3>
	</div><!-- .product__title -->

	<div class="product__price mgt10 fc aic jcsb">
		<?php
			if ( ! $wr_catalog_mode || $wr_show_price ) {
				wc_get_template( 'loop/price.php' );
			}

			wc_get_template( 'loop/rating.php' );
		?>
	</div><!-- .product__price -->
	<?php 
		if ( $wr_nitro_options['wc_display_custom_attr_position'] == 'after-title' && $wr_is_custom_attribute_activated ) {
			do_action( 'wc_display_custom_attr' ); 
		}
	?>
	<div class="product__description mgt20">
		<?php the_excerpt(); ?>
	</div><!-- .product__description -->

	<?php if ( ! $wr_catalog_mode ) : ?>
		<div class="product__action fl mgt20">
			<?php
				// Add to cart button
				if ( ( $wr_nitro_options['wc_buynow_btn'] && ! $wr_nitro_options['wc_disable_btn_atc'] ) || ! $wr_nitro_options['wc_buynow_btn'] || ( $wr_nitro_options['wc_buynow_btn'] && $wr_nitro_options['wc_disable_btn_atc'] && ! $product->is_type( 'simple' ) ) ) {
					wc_get_template( 'loop/add-to-cart.php' );
				}

				// Quick buy button
				if ( $wr_nitro_options['wc_buynow_btn'] && $product->is_purchasable() && $product->is_type( 'simple' ) && ! WR_Nitro_Helper::check_gravityforms( $post->ID ) && ! WR_Nitro_Helper::yith_wc_product_add_ons( $post->ID ) && ! WR_Nitro_Helper::wc_measurement_price_calculator( $post->ID ) ) {
					echo '<a class="product__btn btr-50 button btn-buynow" href="#" data-product-id="' . get_the_ID() . '"><i class="mgr10 nitro-icon-' . esc_attr( $wr_icons ) . '-quickbuy"></i>' . esc_html__( 'Buy Now', 'wr-nitro' ) . '</a>';
				}

				// Add to wishlist button
				if ( class_exists( 'YITH_WCWL' ) && $wr_show_wishlist== '1' ) :
					echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
				endif;

				// Add compare button
				if ( class_exists( 'YITH_WOOCOMPARE' ) && $wr_show_compare == '1' ) {
					echo '
						<div class="product__compare icon_color">
							<a class="product__btn bts-50 btb nitro-line dib pr" href="#"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-compare"></i><span class="tooltip ab">' . esc_html__( 'Compare', 'wr-nitro' ) . '</span></a>
							<div class="hidden">' . do_shortcode( '[yith_compare_button container="no"]' ) . '</div>
						</div>
					';
				}
			?>
		</div><!-- .p-action -->
	<?php endif; ?>
</div><!-- .p-info -->
