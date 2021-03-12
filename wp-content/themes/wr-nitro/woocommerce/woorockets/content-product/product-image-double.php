<?php
/**
 *  Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $woocommerce, $product, $related_product;

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

// Get masonry settings
$wr_masonry_image_size = get_post_meta( get_the_ID(), 'wc_masonry_product_size', true );

$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];

if ( $wr_style == 'masonry' && ! $related_product ) {
	if ( $wr_masonry_image_size == '' || $wr_masonry_image_size == 'wc-large-square' || $wr_masonry_image_size == 'wc-small-square' ) {
		$wr_shop_thumbnail = '450x450';
	} else {
		$wr_shop_thumbnail = '450x900';
	}
} else {
	$wr_shop_thumbnail = 'shop_catalog';
}

// Sale badge
echo woocommerce_show_product_loop_sale_flash();

// Slider setting for shortcode nitro_product
if ( $wr_nitro_shortcode_attrs['slider'] && 'sc-product' == $wr_nitro_shortcode_attrs['shortcode'] ) {
	echo '<div class="wr-nitro-carousel" data-owl-options=\'{"items": "1", "dots": "true"' . ( $wr_nitro_shortcode_attrs['autoplay'] ? ',"autoplay": "true"' : '' ) . ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ) . '}\'>';
}

$wr_attachment_ids = $product->get_gallery_image_ids();

// Get a secondary image
if ( isset( $wr_attachment_ids[0] ) ) {

	$wr_attachment_id = $wr_attachment_ids[0];

	$wr_title = get_the_title();
	$wr_link  = get_the_permalink();
	$wr_image = wp_get_attachment_image( $wr_attachment_id, $wr_shop_thumbnail );

	echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="image__back db pa" title="%s">%s</a>', $wr_link, $wr_title, $wr_image ), $wr_attachment_id, $post->ID );
}

// Product thumbnail
if ( has_post_thumbnail() ) {
	$wr_image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
	$wr_title       = get_the_title();
	$wr_link        = get_the_permalink();
	$wr_image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', $wr_shop_thumbnail ), array(
		'title'	=> $wr_image_title,
		'alt'	=> $wr_image_title
	) );

	echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" class="image__front db" title="%s">%s</a>', $wr_link, $wr_title, $wr_image ), $post->ID );

} else {
	echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), esc_attr__( 'Placeholder', 'wr-nitro' ) ), $post->ID );
}

if ( $wr_nitro_shortcode_attrs['slider'] && 'sc-product' == $wr_nitro_shortcode_attrs['shortcode'] ) {
	echo '</div>';
}
