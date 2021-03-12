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
 * Nitro Masonry Builder shortcode.
 */
class Nitro_Toolkit_Shortcode_Masonry_Element extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'masonry_element';

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
					'size'    => 'small-square',
					'setting' => '',
				),
				$atts
			)
		);

		$classes = array();

		if ( $setting ) {
			$setting_class = vc_shortcode_custom_css_class( $setting, '' );
			$classes[]     = 'class="' . $setting_class . '"';
		}

		// Generate HTML code.
		$html .= '<div class="item ' . esc_attr( $size ) . '">';
			$html .= '<div ' . implode( ' ', $classes ) . '>';
				$html .= '<div class="item-inner">';
					$html .= do_shortcode( $content );
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_masonry_element', force_balance_tags( $html ) );
	}
}
