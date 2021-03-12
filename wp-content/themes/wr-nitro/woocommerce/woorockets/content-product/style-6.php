<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post;

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;
$wr_is_custom_attribute_activated = class_exists( 'WR_Custom_Attributes' ) ? true : false;

// Get product list style
$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];

// Hover style
$wr_hover_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['hover_style'] : $wr_nitro_options['wc_archive_item_hover_style'];
if ( 'default' == $wr_hover_style ) {
	$wr_hover_class = '';
} elseif ( $wr_hover_style == 'flip-back' ) {
	// Flip back effect
	$flip_effect = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['transition_effects'] : $wr_nitro_options['wc_archive_item_transition'];

	// Hover class
	$wr_hover_class = $wr_hover_style . ' ' . $flip_effect;
} else {
	$wr_hover_class = $wr_hover_style;
}

// Show compare
$wr_show_compare = $wr_nitro_options['wc_general_compare'];

// Show wishlist
$wr_show_wishlist = $wr_nitro_options['wc_general_wishlist'];

// Catalog mode
$wr_catalog_mode = $wr_nitro_options['wc_archive_catalog_mode'];

// Show price
$wr_show_price = $wr_nitro_options['wc_archive_catalog_mode_price'];

// Icon Set
$wr_icons = $wr_nitro_options['wc_icon_set'];

// Countdown for sale product
$start = get_post_meta( get_the_ID(), '_sale_price_dates_from', true );
$end   = get_post_meta( get_the_ID(), '_sale_price_dates_to', true );
$now   = date( 'd-m-y' );

// Get setting of booster plugin
$booster_pop = get_post_meta( get_the_ID(), '_wcj_product_open_price_enabled', true );
?>
<div class="product__wrap product-btn-center btn-inside-thumbnail nitro-line">
	<div class="product__image oh pr <?php echo esc_attr( $wr_hover_class ); ?>">
		<?php
			wc_get_template( 'woorockets/content-product/product-image.php' );
			wc_get_template( 'loop/rating.php' );
		?>

			<div class="product__action pa fc jcc">
				<?php
					// Product quickview
					if ( $wr_nitro_options['wc_general_quickview'] ) {
						echo '<a class="product__btn bts-40 btn-quickview heading-bg body_bg_text db hover-main" href="#0" data-prod="' . esc_attr( $post->ID ) . '"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-quickview"></i><span class="tooltip ab">' . esc_html__( 'Quick View', 'wr-nitro' ) . '</span></a>';
					}

					// Quick buy button
					if ( $wr_nitro_options['wc_buynow_btn'] && $product->is_purchasable() && $product->is_type( 'simple' ) && ! $wr_catalog_mode && $product->is_in_stock() && ! WR_Nitro_Helper::check_gravityforms( $post->ID ) && ! WR_Nitro_Helper::yith_wc_product_add_ons( $post->ID ) && ! WR_Nitro_Helper::wc_measurement_price_calculator( $post->ID ) && $booster_pop != 'yes' ) {
						echo '<a class="product__btn bts-40 heading-bg body_bg_text db btn-buynow hover-main" href="#" data-product-id="' . get_the_ID() . '"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-quickbuy"></i><span class="tooltip ab">' . esc_html__( 'Buy Now', 'wr-nitro' ) . '</span></a>';
					}

					// Add to cart button
					if ( ( $wr_nitro_options['wc_buynow_btn'] && ! $wr_nitro_options['wc_disable_btn_atc'] ) || ! $wr_nitro_options['wc_buynow_btn'] || ( $wr_nitro_options['wc_buynow_btn'] && $wr_nitro_options['wc_disable_btn_atc'] && ! $product->is_type( 'simple' ) ) ) {
						wc_get_template( 'loop/add-to-cart.php' );
					}

					// Add compare button
					if ( class_exists( 'YITH_WOOCOMPARE' ) && $wr_show_compare && ! $wr_catalog_mode ) {
						echo '
							<div class="product__compare">
								<a class="product__btn bts-40 heading-bg body_bg_text mg0 db hover-main" href="#"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-compare"></i><span class="tooltip ab">' . esc_html__( 'Compare', 'wr-nitro' ) . '</span></a>
								<div class="hidden">' . do_shortcode( '[yith_compare_button container="no"]' ) . '</div>
							</div>
						';
					}
				?>
			</div><!-- .product__action -->

		<?php if ( ! empty( $wr_nitro_shortcode_attrs['countdown'] ) && ( $end && date( 'd-m-y', $start ) <= $now ) ) : ?>
			<div class="product__countdown pa bgw">
				<div class="wr-nitro-countdown fc jcsb tc aic" data-time='{"day": "<?php echo date( 'd', $end ); ?>", "month": "<?php echo date( 'm', $end ); ?>", "year": "<?php echo date( 'Y', $end ); ?>"}'></div>
			</div>
		<?php endif; ?>

		<?php
			$stock_status = get_post_meta( $post->ID, '_stock_status', true );
			if ( $stock_status == 'outofstock' ) {
				echo '<div class="product__status pa tu color-white fwb">' . esc_html__( 'Out Of Stock', 'wr-nitro' ) . '</div>';
			}
		?>
	</div><!-- .product__image -->
	<div class="product__info nitro-line">
		<?php 
			if ( $wr_nitro_options['wc_display_custom_attr_position'] == 'before-title' && $wr_is_custom_attribute_activated ) {
				do_action( 'wc_display_custom_attr' ); 
			}
		?>
		<div class="product__title">
			<h3 class="mg0"><a class="hover-main" href="<?php esc_url( the_permalink() ); ?>" title="<?php echo wp_kses( get_the_title(), '' ); ?>"><?php the_title(); ?></a></h3>
		</div><!-- .product__title -->

		<?php if ( ! $wr_catalog_mode || $wr_show_price ) : ?>
			<div class="product__price">
				<?php wc_get_template( 'loop/price.php' ); ?>
			</div><!-- .product__price -->
		<?php endif;

		if ( $wr_nitro_options['wc_display_custom_attr_position'] == 'after-title' && $wr_is_custom_attribute_activated ) {
			do_action( 'wc_display_custom_attr' ); 
		}

		if ( class_exists( 'YITH_WCWL' ) && $wr_show_wishlist && ! $wr_catalog_mode ) :
			echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		endif; ?>
	</div>
</div><!-- .product-btn-center -->
