<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
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

// Get theme options.
$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;
$wr_sizer = '';
$wr_attr = $wr_classes = array();
$is_shop = ( ( function_exists( 'is_shop' ) && is_shop() ) || is_post_type_archive( 'product' ) || ( function_exists( 'is_product_category' ) && is_product_category() ) || ( function_exists( 'is_product_tag' ) && is_product_tag() ) || ( function_exists( 'is_woocommerce' ) && is_woocommerce() && is_tax() ) );

// Style of list product

if ( wp_is_mobile() ) {
	$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_mobile_style'];

	if ( $is_shop ) {
		$wr_classes[]  = 'mobile-layout';
		// Column layout
		if ( 2 == $wr_nitro_options['wc_archive_mobile_layout_column'] ) {
			$wr_classes[]  = 'column-2';
		}
	}
} else {
	$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];
}

if ( $wr_nitro_shortcode_attrs || ( $is_shop && ! wp_is_mobile() ) ) {
	// Enable border
	if ( $wr_nitro_options['wc_archive_border_wrap'] ) {
		$wr_classes[] = 'boxed';
	} else {
		$wr_classes[] = 'un-boxed';
	}

	// Column layout
	if ( 'list' != $wr_style ) {
		$wr_classes[] = 'columns-' . ( $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['columns'] : $wr_nitro_options['wc_archive_layout_column'] );
	}

	$wr_item_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['style'] : $wr_nitro_options['wc_archive_item_layout'];

	// Item style
	$wr_classes[] = 'item-style-' . absint( $wr_item_style );
}

// List style
if ( 'masonry' == $wr_style ) {
	$wr_attr[]    = 'data-masonry=\'{"selector":".product", "columnWidth":".grid-sizer"}\'';
	$wr_classes[] = 'wr-nitro-masonry masonry-layout';
	$wr_sizer  = 'cs-6 cm-' . (int) ( 12 / ( $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['columns'] : $wr_nitro_options['wc_archive_layout_column'] ) );
} else {
	$wr_classes[] = $wr_style . ' ' . esc_attr( $wr_style ) . '-layout';
}

// Pagination style
if ( $wr_nitro_options['wc_archive_pagination_type'] == 'number' ) {
	$wr_classes[] = 'pag-number';
}

// Slider setting for shortcode products
if ( ! empty( $wr_nitro_shortcode_attrs ) && $wr_nitro_shortcode_attrs['slider'] && 'sc-products' == $wr_nitro_shortcode_attrs['shortcode'] ) {
	if ( ! empty( $wr_nitro_shortcode_attrs['columns'] ) ) {
		$wr_attr_slider[] = '"items": "' . ( int ) $wr_nitro_shortcode_attrs['columns'] . '"';
	}
    if ( ! empty( $wr_nitro_shortcode_attrs['auto_play'] ) ) {
        $wr_attr_slider[] = '"autoplay": "true"';
    }
    if ( ! empty( $wr_nitro_shortcode_attrs['timeout'] ) ) {
        $wr_attr_slider[] = '"autoplayTimeout": "'.$wr_nitro_shortcode_attrs['timeout'].'"';
    }
	if ( ! empty( $wr_nitro_shortcode_attrs['navigation'] ) ) {
		$wr_attr_slider[] = '"nav": "true"';
	}
	if ( ! empty( $wr_nitro_shortcode_attrs['pagination'] ) ) {
		$wr_attr_slider[] = '"dots": "true"';
	}
	if ( ! empty( $wr_nitro_shortcode_attrs['992'] ) ) {
		$wr_attr_slider[] = '"desktop": "' . ( int ) $wr_nitro_shortcode_attrs['992'] . '"';
	}
	if ( ! empty( $wr_nitro_shortcode_attrs['768'] ) ) {
		$wr_attr_slider[] = '"tablet": "' . ( int ) $wr_nitro_shortcode_attrs['768'] . '"';
	}
	if ( ! empty( $wr_nitro_shortcode_attrs['600'] ) ) {
		$wr_attr_slider[] = '"mobile": "' . ( int ) $wr_nitro_shortcode_attrs['600'] . '"';
	}
	if ( ! empty( $wr_nitro_shortcode_attrs['375'] ) ) {
		$wr_attr_slider[] = '"sm_mobile": "' . ( int ) $wr_nitro_shortcode_attrs['375'] . '"';
	}
	$wr_attr_slider[] = '"custom_responsive": "true"';

	if ( ! empty( $wr_attr_slider ) ) {
		$wr_attr[] = 'data-owl-options=\'{' . esc_attr( implode( ', ', $wr_attr_slider ) ) . ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ) . '}\'';
	}
	$wr_classes[] = 'wr-nitro-carousel';
}
?>

<div <?php echo implode( ' ', $wr_attr ); ?> class="products <?php echo esc_attr( $wr_nitro_shortcode_attrs['shortcode'] ) . ' ' . implode( ' ', $wr_classes ) . ( ( is_customize_preview() && ! $wr_nitro_shortcode_attrs ) ? ' customizable customize-section-product_list' : '' );  ?>">

<?php if ( $wr_style == 'masonry' ) {
	echo '<div class="grid-sizer ' . esc_attr( $wr_sizer ) . '"></div>';
} ?>

