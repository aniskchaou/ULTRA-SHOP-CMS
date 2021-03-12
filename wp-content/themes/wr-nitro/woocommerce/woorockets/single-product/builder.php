<?php
/**
 * Single Product Builder
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooRockets
 * @package 	Nitro
 * @version     1.1.9
 */

$wr_nitro_options = WR_Nitro::get_options();

// Get single style
$single_style = get_post_meta( get_the_ID(), 'single_style', true );
if ( $single_style == 0 ) {
	$single_style = $wr_nitro_options['wc_single_style'];
} else {
	$single_style = get_post_meta( get_the_ID(), 'single_style', true );
}

$builder = get_post_meta( get_the_ID(), 'enable_builder', true );

if ( $builder ) {
	if ( $single_style == 1 || $single_style == 4 ) {
		echo '<div class="row">';
			echo '<div class="cm-12">';
				echo '<div class="p-single-builder mgb50">';
					the_content();
				echo '</div>';
			echo '</div>';
		echo '</div>';
	} elseif ( $single_style == 2 || $single_style == 5 ) {
		echo '<div class="row mgt50">';
			echo '<div class="p-single-builder">';
				the_content();
			echo '</div>';
		echo '</div>';
	} elseif ( $single_style == 3 ) {
		echo '<div class="row">';
			echo '<div class="cm-12 w800-12">';
				echo '<div class="p-single-builder mgb50 mgt50">';
					the_content();
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}