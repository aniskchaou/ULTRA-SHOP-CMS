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

$wr_output = $wr_data = $wr_class = '';

// Get blog settings
$wr_style = $wr_nitro_options['blog_style'];

// Render carousel
$wr_data  = 'data-owl-options=\'{"autoplay": "false", "items": "1", "dots": "true", "nav": "true"' . ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ) . '}\'';
$wr_class = ' wr-nitro-carousel';


// Get gallery image
$wr_image_full    = WR_Nitro_Helper::gallery();
$wr_image_zigzag  = WR_Nitro_Helper::gallery( '405x300' );
$wr_image_classic = WR_Nitro_Helper::gallery( '450x450' );

if ( ! empty( $wr_image_full ) && isset( $wr_image_full[0] ) && $wr_image_full[0] ) {
	$wr_output .= '<div class="entry-thumb oh' . esc_attr( $wr_class ) . '" ' . $wr_data . '>';
		// Render image
		if ( ! empty( $wr_image_full ) ) {
			foreach ( $wr_image_full as $key => $photo ) {
				// Generate exact size of image follow blog style
				if ( 'zigzag' == $wr_style ) {
					$wr_image      = $wr_image_zigzag[$key];
					$wr_img_width  = 405;
					$wr_img_height = 300;
				} elseif ( 'simple' == $wr_style ) {
					$wr_image      = $wr_image_classic[$key];
					$wr_img_width  = 450;
					$wr_img_height = 450;
				} elseif ( 'masonry' == $wr_style || ( 'classic' == $wr_style ) ) {
					$wr_image      = $photo;
					$wr_img_width  = '';
					$wr_img_height = '';
				}

				// Output image thumbnail
				$wr_output .= '<a data-lightbox="nivo" data-lightbox-gallery="' . get_the_ID() . '" href="' . esc_url( $photo ) . '"><img class="ts-03" src="' . esc_url( $wr_image ) . '" alt="' . esc_attr( get_the_title() ) . '"  width="' . esc_attr( $wr_img_width ) . '" height="' . esc_attr(  $wr_img_height ) . '" ></a>';
			}
		}
	$wr_output .= '</div>';
	echo '' . $wr_output;
} else {
	WR_Nitro_Render::get_template( 'blog/format/standard' );
}
