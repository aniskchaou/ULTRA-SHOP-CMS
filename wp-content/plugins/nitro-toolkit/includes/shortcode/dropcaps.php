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
 * Nitro Dropcaps shortcode.
 */
class Nitro_Toolkit_Shortcode_Dropcaps extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'dropcaps';

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_dropcaps_custom_css';

	/**
	 * Generate custom CSS.
	 *
	 * @param   array  $atts  Shortcode parameters.
	 *
	 * @return  string
	 */
	public function generate_css( $atts ) {
		$css = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'color'              => '#323232',
					'style'              => 'no-bg',
					'bg_color'           => '#d4a769',
					'border_color'       => '#d4a769',
					'extra_class'        => '',
					'dropcaps_custom_id' => ''
				),
				$atts
			)
		);

		// Generate custom ID.
		$id = $dropcaps_custom_id;

		$css .= '
	#' . esc_attr( $id ) . '.nitro-dropcaps span {';
		$css .= 'color: ' . esc_attr( $color ) . ';';
		if ( 'square-solid' == $style || 'circle-solid' == $style ) {
			$css .= 'background: ' . esc_attr( $bg_color ) . ';';
		}
		if ( 'square-outline' == $style || 'circle-outline' == $style ) {
			$css .= 'border-color: ' . esc_attr( $border_color ) . ';';
		}
	$css .= '}';

		return $css;
	}

	/**
	 * Generate HTML code based on shortcode parameters.
	 *
	 * @param   array   $atts     Shortcode parameters.
	 * @param   string  $content  Current content.
	 *
	 * @return  string
	 */
	public function generate_html( $atts, $content = null ) {
		$html = $img = $img_inner = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'text'               => '',
					'dropcaps'           => '',
					'color'              => '#323232',
					'style'              => 'no-bg',
					'bg_color'           => '#d4a769',
					'border_color'       => '#d4a769',
					'extra_class'        => '',
					'dropcaps_custom_id' => ''
				),
				$atts
			)
		);

		// Generate custom ID.
		$id = $dropcaps_custom_id;

		if ( ! empty( $text ) ) {
			$html .= '<div id="' . esc_attr( $id ) . '" class="nitro-dropcaps ' . esc_attr( $extra_class ) . '">';

			if ( ! empty( $dropcaps ) ) {
				$html .= '<span class="big tc ' . esc_attr( $style ) . ' fl fwb bs-50">' . esc_html( $dropcaps ) . '</span>';
			}
				$html .= do_shortcode( $text );
			$html .= '</div>';
		}

		return apply_filters( 'nitro_toolkit_shortcode_dropcaps', force_balance_tags( $html ) );
	}
}
