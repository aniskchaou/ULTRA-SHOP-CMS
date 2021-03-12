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

global $product, $related_product;

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

// Get product list style
$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];

// Get masonry settings
$wr_masonry_image_size = get_post_meta( get_the_ID(), 'wc_masonry_product_size', true );

// Hover style
$wr_hover_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['hover_style'] : $wr_nitro_options['wc_archive_item_hover_style'];

if ( $wr_hover_style == 'mask' )
	echo '<span class="mask-inner" ' . ( $wr_nitro_shortcode_attrs ? ( 'style="background:' . $wr_nitro_shortcode_attrs['mask_overlay_color'] . '"' ) : NULL ) . '></span>';

// Get post thumbnail
if ( $wr_hover_style == 'flip-back' ) {
	wc_get_template( 'woorockets/content-product/product-image-double.php' );
} elseif ( $wr_style == 'masonry' && ! $related_product ) {
	if ( has_post_thumbnail() ) {
		echo '<a class="db" href="' . esc_url( get_permalink() ) . '">';
			if ( $wr_masonry_image_size == 'wc-large-rectangle' || $wr_masonry_image_size == 'wc-small-rectangle' ) {
				the_post_thumbnail( '450x900' );
			} else {
				the_post_thumbnail( '450x450' );
			}
		echo '</a>';
	}
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
