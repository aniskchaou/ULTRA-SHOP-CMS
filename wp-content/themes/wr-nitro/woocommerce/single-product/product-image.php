<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $woocommerce, $product;

$wr_nitro_options = WR_Nitro::get_options();

// Get single style
$single_style = get_post_meta( get_the_ID(), 'single_style', true );

if ( $single_style == 0 ) {
	$single_style = $wr_nitro_options['wc_single_style'];
}

if ( wp_is_mobile() ) {
	wc_get_template( 'woorockets/single-product/product-image/style-mobile.php' );
} elseif ( isset( $_REQUEST['wr_view_image'] ) && $_REQUEST['wr_view_image'] == 'wr_quickview' ) {
	wc_get_template( 'woorockets/single-product/product-image/style-quickview.php' );
} else {
	wc_get_template( 'woorockets/single-product/product-image/style-' . $single_style . '.php' );
}
?>
