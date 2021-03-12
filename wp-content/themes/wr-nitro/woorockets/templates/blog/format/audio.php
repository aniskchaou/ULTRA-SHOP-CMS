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

// Get theme options
$wr_nitro_options = WR_Nitro::get_options();

$wr_output = '';

// Get blog settings
$wr_style = $wr_nitro_options['blog_style'];

if ( 'masonry' == $wr_style || 'classic' == $wr_style ) {
	// Get content audio
	echo WR_Nitro_Helper::audio_embed();
} else {
	if ( has_post_thumbnail() ) {
		if ( 'zigzag' == $wr_style ) {
			// Get post thumbnail source
			$wr_image = wp_get_attachment_image_src( get_post_thumbnail_id(), '405x300' );
		} elseif ( 'simple' == $wr_style ) {
			// Get post thumbnail source
			$wr_image = wp_get_attachment_image_src( get_post_thumbnail_id(), '450x450' );
		}

		// Output image thumbnail
		$wr_output .= '<div class="entry-thumb">';
			$wr_output .= '<i class="bts-40 pa tc fa ts-03 body_bg fa fa-music"></i>';
			$wr_output .= '<a href="' . esc_url( get_permalink() ) . '"><img class="cxs-12" src="' . esc_url( $wr_image[0] ) . '" alt="' . esc_attr( get_the_title() ) . '" width="' . esc_attr( $wr_image[1] ) . '" height="' . esc_attr( $wr_image[2] ) . '" /></a>';

		if ( 'simple' == $wr_style ) {
			$wr_output .= '<div class="pa">' . WR_Nitro_Helper::audio_embed() . '</div>';
		}
		$wr_output .= '</div>';

		echo '' . $wr_output;
	}
}
