<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
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
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

if ( $wr_nitro_options['wc_archive_catalog_mode'] ) return;

// Get product item style
$wr_item_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['style'] : $wr_nitro_options['wc_archive_item_layout'];

// Style of list product
$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];

// Icon Set
$wr_icons = $wr_nitro_options['wc_icon_set'];

// Check product has gravity form
$check_gravityforms = WR_Nitro_Helper::check_gravityforms( $product->get_id() );

// Check product has YITH WooCommerce Product Add_Ons
$yith_wc_product_add_ons = WR_Nitro_Helper::yith_wc_product_add_ons( $product->get_id() );

// Check product has WooCommerce Measurement Price Calculator
$wc_measurement_price_calculator = WR_Nitro_Helper::wc_measurement_price_calculator( $product->get_id() );

// Check product has WC Fields Factory
$wc_fields_factory = WR_Nitro_Helper::wc_fields_factory( $product->get_id() );

$ajax_add_to_cart = true;

if( $yith_wc_product_add_ons || $check_gravityforms || $wc_measurement_price_calculator || $wc_fields_factory ) {
	$ajax_add_to_cart = false;
}

if( ! $product->is_type( 'simple' ) ) {
	$ajax_add_to_cart = false;
}

$is_shop = ( ( function_exists( 'is_shop' ) && is_shop() ) || is_post_type_archive( 'product' ) || ( function_exists( 'is_product_category' ) && is_product_category() ) || ( function_exists( 'is_product_tag' ) && is_product_tag() ) || ( function_exists( 'is_woocommerce' ) && is_woocommerce() && is_tax() ) );

if ( wp_is_mobile() && $is_shop ) {
	$class_icon = 'bts-50 %s product_type_%s icon_color"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart"></i><span class="hidden">%s</span></a>';
} else {
	if ( 'list' == $wr_style && ! is_singular( 'product' ) || is_cart() ) {
		$class_icon = 'btr-50 button %s product_type_%s"><i class="mgr10 nitro-icon-' . esc_attr( $wr_icons ) . '-cart"></i><span>%s</span></a>';
	} else {
		if ( '1' == $wr_item_style ) {
			$class_icon = 'db bts-40 color-dark bgw %s product_type_%s hover-primary"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart"></i><span class="tooltip ar">%s</span></a>';
		} elseif ( '2' == $wr_item_style ) {
			$class_icon = 'bgw btb btr-40 color-dark %s product_type_%s"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart mgr10"></i><span>%s</span></a>';
		} elseif ( '3' == $wr_item_style || '4' == $wr_item_style ) {
			$class_icon = 'pr color-dark hover-primary %s product_type_%s"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart"></i><span class="tooltip ab">%s</span></a>';
		} elseif ( '5' == $wr_item_style ) {
			$class_icon = 'button db textup aligncenter pr %s product_type_%s"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart mgr10"></i>%s</a>';
		} elseif ( '6' == $wr_item_style ) {
			$class_icon = 'db bts-40 heading-bg body_bg_text %s product_type_%s hover-main"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart"></i><span class="tooltip ab">%s</span></a>';
		}
	}
}

if( $wc_measurement_price_calculator ) {
	echo sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="' . ( ( $ajax_add_to_cart ) ? 'ajax_add_to_cart' : '' ) . ' product__btn_cart product__btn ' . $class_icon,
		esc_url( get_permalink( $product->get_id() ) ),
		esc_attr( $product->get_id() ),
		esc_attr( $product->get_sku() ),
		esc_attr( isset( $quantity ) ? $quantity : 1 ),
		$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
		esc_attr( $product->get_type() ),
		esc_html__( 'Select options', 'wr-nitro' )
	);
} else {
	echo apply_filters( 'woocommerce_loop_add_to_cart_link',
		sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="' . ( ( $ajax_add_to_cart ) ? 'ajax_add_to_cart' : '' ) . ' product__btn_cart product__btn ' . $class_icon,
			( $ajax_add_to_cart ? esc_url( $product->add_to_cart_url() ) : get_permalink( $product->get_id() ) ),
			esc_attr( $product->get_id() ),
			esc_attr( $product->get_sku() ),
			esc_attr( isset( $quantity ) ? $quantity : 1 ),
			$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
			esc_attr( $product->get_type() ),
			( $ajax_add_to_cart ? esc_html( $product->add_to_cart_text() ) : esc_html__( 'Select options', 'wr-nitro' ) )
		),
	$product );
}

