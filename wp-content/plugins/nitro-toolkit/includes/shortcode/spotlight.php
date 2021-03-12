<?php
/**
 * @version    1.0
 * @package    Nitro_Toolkit
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

/**
 * Nitro Spotlight shortcode.
 */
class Nitro_Toolkit_Shortcode_Spotlight extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'spotlight';

	/**
	 * Generate HTML code based on shortcode parameters.
	 *
	 * @param   array   $atts     Shortcode parameters.
	 * @param   string  $content  Current content.
	 *
	 * @return  string
	 */
	public function generate_html( $atts, $content = null ) {
		$html = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'front_image'      => '',
					'back_image'       => '',
					'title'            => '',
					'link'             => '',
					'extra_class'      => '',
				),
				$atts
			)
		);

		// Get image link and image data
		if ( ! empty( $front_image ) ) {
			$front_image_data = wp_get_attachment_image_src( $front_image, 'full' );
			$front_image_alt  = get_post_meta( $front_image, '_wp_attachment_image_alt', true );
		}
		if ( ! empty( $back_image ) ) {
			$back_image_data = wp_get_attachment_image_src( $back_image, 'full' );
			$back_image_alt  = get_post_meta( $back_image, '_wp_attachment_image_alt', true );
		} else {
			$back_image_data = wp_get_attachment_image_src( $front_image, 'full' );
			$back_image_alt  = get_post_meta( $front_image, '_wp_attachment_image_alt', true );
		}

		$classes = array( 'spotlight-image dib' );

		if ( ! empty( $extra_class ) ) {
			$classes[] = $extra_class;
		}

		// Generate HTML code.
		$html .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		if ( ! empty( $front_image ) ) {
			$html .= '<div class="pr">';

				if ( ! empty( $link ) ) {
					$html .= '<a href="' . esc_url( $link ) . '" target="_blank" rel="noopener noreferrer">';
				}
					$html .= '<img class="wr-lazyload front" src="' . esc_url( $front_image_data[0] ) . '" data-src-lazyload="' . esc_url( $front_image_data[0] ) . '" alt="' . esc_attr__( $front_image_alt ) . '" width="' . esc_attr( $front_image_data[1] ) . '" height="' . esc_attr( $front_image_data[2] ) . '">';
					$html .= '<img class="wr-lazyload back" src="' . esc_url( $back_image_data[0] ) . '" data-src-lazyload="' . esc_url( $back_image_data[0] ) . '" alt="' . esc_attr__( $back_image_alt ) . '" width="' . esc_attr( $back_image_data[1] ) . '" height="' . esc_attr( $back_image_data[2] ) . '">';
				if ( ! empty( $link ) ) {
					$html .= '</a>';
				}

			$html .= '</div>';
		}

		if ( ! empty( $title ) ) {
			$html .= '<h3 class="mgt20">' . esc_html( $title ) . '</h3>';
		}
		$html .= '</div>';


		return apply_filters( 'nitro_toolkit_shortcode_spotlight', force_balance_tags( $html ) );
	}
}
