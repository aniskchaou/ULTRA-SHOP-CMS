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
 * Nitro Counter Up shortcode.
 */
class Nitro_Toolkit_Shortcode_Countdown extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'countdown';

	/**
	 * Enqueue scripts.
	 *
	 * @return  void
	 */
	public function enqueue_scripts() {
		parent::enqueue_scripts();

		if ( is_singular() ) {
			global $post;

			if ( has_shortcode( $post->post_content, "nitro_{$this->shortcode}" ) ) {
				wp_enqueue_script( 'jquery-countdown' );
			}
		}
	}

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_countdown_custom_css';

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
					'style'               => 'vertical',
					'year'                => '',
					'month'               => '',
					'day'                 => '',
					'bg_color'            => '',
					'number_color'        => '',
					'time_color'          => '',
					'line'                => '',
					'space'               => 60,
					'extra_class'         => '',
					'countdown_custom_id' => '',
				),
				$atts
			)
		);

		// Generate custom ID.
		$id = $countdown_custom_id;

		// Background
		$css .= $bg_color ? '#' . esc_attr( $id ) . '{ background: ' . esc_attr( $bg_color ) . '; }' : '';

		// Number color
		$css .= $number_color ? '#' . esc_attr( $id ) . ' .color-primary { color: ' . esc_attr( $number_color ) . '; }' : '';

		// Timer color
		$css .= $time_color ? '#' . esc_attr( $id ) . ' .color-dark { color: ' . esc_attr( $time_color ) . '; }' : '';

		if ( ! empty( $space ) ) {
			if ( $style == 'vertical' ) {
				$css .= '
					.sc-countdown.vertical .pr:not(:last-child) {
						margin-bottom: ' . (int) $space / 2 . 'px;
						padding-bottom: ' . (int) $space / 2 . 'px;
					}
				';
			} else {
				$css .= '
					.sc-countdown.horizontal .pr:not(:last-child) {
						margin-right: ' . (int) $space / 2 . 'px;
						padding-right: ' . (int) $space / 2 . 'px;
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
		$html = $flex = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'style'               => 'vertical',
					'year'                => '',
					'month'               => '',
					'day'                 => '',
					'bg_color'            => '',
					'number_color'        => '',
					'time_color'          => '',
					'line'                => '',
					'space'               => 60,
					'extra_class'         => '',
					'countdown_custom_id' => '',
				),
				$atts
			)
		);

		$id = $countdown_custom_id;

		$classes = array( 'sc-countdown tc' );
		if ( $style ) {
			$classes[] = $style;
		}

		if ( $style == 'horizontal' ) {
			$flex = ' fc jcc';
		}

		if ( $line ) {
			$classes[] = 'line';
		}

		if ( ! empty( $extra_class ) ) {
			$classes[] = $extra_class;
		}

		// Generate HTML code.
		$html .= '<div id="' . esc_attr( $id ) . '" class="' . implode( ' ', $classes ) . '">';
			$html .= '<div class="wr-nitro-countdown' . $flex . '" data-time=\'{"day": "' . (int) $day . '", "month": "' . (int) $month . '", "year": "' . (int) $year . '"}\'></div>';
		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_countdown', force_balance_tags( $html ) );
	}
}
