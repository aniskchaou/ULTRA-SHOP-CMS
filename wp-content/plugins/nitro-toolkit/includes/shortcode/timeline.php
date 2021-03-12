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
 * Nitro timelines shortcode.
 */
class Nitro_Toolkit_Shortcode_timeline extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'timeline';

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_timeline_custom_css';

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
		extract( shortcode_atts(
			array(
				'style'             	 => '',
				'pin_color'         	 => '#000',
				'pin_border'        	 => '#bfbfbf',
				'timeline_custom_id'	 => '',
			), $atts )
		);

		// Generate custom ID
		$id = $timeline_custom_id;

		if ( $pin_border && $pin_color ) {
			$css .= '
				#' . esc_attr( $id ) . '.nitro-timeline ul .timeline-content:before {
					border-color: ' . esc_attr( $pin_border ) . ';
					background: ' . esc_attr( $pin_color ) . ';
				}
			';
		}

		if ( ! empty( $pin_color ) ) {
			if ( 'style-1' == $style ) {
				$css .= '
					#' . esc_attr( $id ) . '.nitro-timeline ul .timeline-content:after {
						background: ' . esc_attr( $pin_color ) . ';
					}
				';
			} else {
				$css .= '
					#' . esc_attr( $id ) . '.nitro-timeline.style-2 li:after {
						background: ' . esc_attr( $pin_color ) . ';
					}
				';
			}
		}

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
		$html = '';

		// Extract shortcode parameters.
		extract( shortcode_atts(
			array(
				'style'                  => 'style-1',
				'pin_color'              => '#000',
				'pin_border'             => '#bfbfbf',
				'timeline_content'       => '',
				'heading'                => '',
				'datetime'               => '',
				'content'                => '',
				'timeline_custom_class'  => '',
				'timeline_custom_id'     => '',
			), $atts )
		);

		$classes = array();

		// Get extra class
		if ( ! empty( $timeline_custom_class ) ) {
			$classes[] = $timeline_custom_class;
		}
		// Get style
		if ( $style ) {
			$classes[] = $style;
		}

		// Generate custom ID
		$id = $timeline_custom_id;

		$values = ( array ) vc_param_group_parse_atts( $timeline_content );

		$html .= '<div id="' . esc_attr( $id ) . '" class="nitro-timeline ' . esc_attr( implode( ' ', $classes ) ) . '">';
		$html .= '<ul>';

		foreach ( $values as $value ) {

			if ( ! empty( $values ) ) {
				$html .= '<li class="pr">';
					$html .= '<div class="timeline-item fc">';
						$html .= '<time class="datetime">' . esc_html( $value['datetime'] ) . '</time>';
						$html .= '<div class="timeline-content">';
							$html .= '<div class="timeline-content-inner">';
								$html .= '<h3>' . esc_html( $value['heading'] ) . '</h3>';
								$html .= '<p>' . wp_kses_post( $value['content'] ) . '</p>';
							$html .= '</div>';
						$html .= '</div>';
					$html .= '</div>';
				$html .= '</li>';
			}
		}

		$html .= '</ul>';
		$html .= '</div>';

		return apply_filters( 'Nitro_Toolkit_Shortcode_timeline', force_balance_tags( $html ) );
	}
}