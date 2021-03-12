<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @author     WooThemes
 * @package    WooCommerce/Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get theme option
$wr_nitro_options = WR_Nitro::get_options();

// Get single style
$single_style = get_post_meta( get_the_ID(), 'single_style', true );
if ( $single_style == 0 ) {
	$single_style = $wr_nitro_options['wc_single_style'];
} else {
	$single_style = get_post_meta( get_the_ID(), 'single_style', true );
}

$page_title   = $wr_nitro_options['wc_single_title'];

if ( $single_style == 2 && ! $page_title ) {
	the_title( '<h1 itemprop="name" class="product-title mg0 mgb20">', '</h1>' );
} elseif ( $single_style != 2 || wp_is_mobile() ) {
	the_title( '<h1 itemprop="name" class="product-title mg0 mgb20">', '</h1>' );
}
