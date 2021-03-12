<?php
/**
 * Product loop sale flash
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

$wr_nitro_options = WR_Nitro::get_options();

$sale = '';

if ( ! ( $wr_nitro_options['wc_archive_catalog_mode'] && ! $wr_nitro_options['wc_archive_catalog_mode_price'] ) ) :

	if ( ( $product->is_type( 'simple' ) || $product->is_type( 'external' ) || $product->is_type( 'bundle' ) ) && $product->is_on_sale() ) {

		$product = wc_get_product( $product->get_id() );
		$regular_price 	= $product->get_regular_price();
		$sale_price 	= $product->get_sale_price();

		if( $regular_price != '' ) {
            $sale = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100, 2 );
		}

	} elseif ( $product->is_type( 'variable' ) && $product->is_on_sale() ) {

		$available_variations = $product->get_available_variations();

		$sale = array();

		foreach ( $available_variations as $val ) {
			if ( $val['display_price'] > 0 ) {
				$regular_price = $val['display_regular_price'];
				$sale_price = $val['display_price'];

                $sale[] = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100, 2 );
			} else {
				$sale[] = '100';
			}
		}

		$sale = max( $sale );
	}

	if ( $product->is_on_sale() ) {
		echo apply_filters( 'woocommerce_sale_flash', '<span class="product__badge sale">-' . $sale . '%</span>', $post, $product );
	}

endif;