<?php
/**
 * Floating button
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooRockets
 * @package 	Nitro
 * @version     1.1.9
 */

global $product;

$wr_nitro_options = WR_Nitro::get_options();

// Floating button
$floating_cart  = $wr_nitro_options['wc_single_floating_button'];

if ( $floating_cart && ( isset( $product ) && $product->is_in_stock() ) ) {
	echo '<div class="actions-fixed pf floating-add-to-cart">';
		if ( $wr_nitro_options['wc_buynow_btn'] ) echo '<div class="fc">';
			// Add to cart button
			if ( ( $wr_nitro_options['wc_buynow_btn'] && ! $wr_nitro_options['wc_disable_btn_atc'] ) || ! $wr_nitro_options['wc_buynow_btn'] ) {
				echo WR_Nitro_Pluggable_WooCommerce::floating_add_to_cart( $product->get_id() );
			}

			// Quick buy button
			if ( $wr_nitro_options['wc_buynow_btn'] ) {
				echo '<button type="submit" class="single_buy_now wr_add_to_cart_button button alt btr-50 db pdl20 pdr20 fl mgl10 br-3"><i class="fa fa-cart-arrow-down mgr10"></i>' . esc_html__( 'Buy now', 'wr-nitro' ) . '</button>';
			}
		if ( $wr_nitro_options['wc_buynow_btn'] ) echo '</div>';
	echo '</div>';
}