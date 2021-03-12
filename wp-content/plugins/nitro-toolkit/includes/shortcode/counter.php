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
class Nitro_Toolkit_Shortcode_Counter extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'counter_up';

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
				wp_enqueue_script( 'waypoints', NITRO_TOOLKIT_URL . 'assets/js/vendors/counterup/waypoints.min.js', array(), false, true );
				wp_enqueue_script( 'nitro-toolkit-counterup', NITRO_TOOLKIT_URL . 'assets/js/vendors/counterup/counterup.min.js', array( 'waypoints' ), false, true );
			}
		}
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
		$html = $title_style = $icon_style = $number_style = $desc_style = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'title'                => 'Sample Title',
					'title_fontsize'       => 26,
					'title_color'          => '#646464',
					'number'               => 999,
					'number_fontsize'      => 46,
					'number_color'         => '#646464',
					'description'          => 'Sample Description',
					'description_fontsize' => 10,
					'description_color'    => '#cacaca',
					'icon'                 => '',
					'horizontal'           => '',
					'icon_fontawesome'     => 'fa fa-adjust',
					'icon_fontsize'        => '',
					'icon_color'           => '#d6aa74',
				),
				$atts
			)
		);

		if ( $icon ) {
			if ( ! empty( $icon_fontsize ) ) {
				$icon_style .= ' font-size: ' . ( is_numeric( trim( $icon_fontsize ) ) ? trim( $icon_fontsize ) . 'px;' : trim( $icon_fontsize ) );
				$icon_style .= ' line-height: ' . ( is_numeric( trim( $icon_fontsize ) ) ? trim( $icon_fontsize ) . 'px;' : trim( $icon_fontsize ) );
			}
			if ( ! empty( $icon_color ) ) {
				$icon_style .= ' color: ' . esc_attr( $icon_color ) . ';';
			}
		}

		if ( ! empty( $number_fontsize ) ) {
			$number_style .= ' font-size: ' . ( is_numeric( trim( $number_fontsize ) ) ? trim( $number_fontsize ) . 'px;' : trim( $number_fontsize ) );
			$number_style .= ' line-height: ' . ( is_numeric( trim( $number_fontsize ) ) ? trim( $number_fontsize ) . 'px;' : trim( $number_fontsize ) );
		}

		if ( ! empty( $number_color ) ) {
			$number_style .= ' color: ' . esc_attr( $number_color ) . ';';
		}

		if ( ! empty( $title_fontsize ) ) {
			$title_style .= ' font-size: ' . ( is_numeric( trim( $title_fontsize ) ) ? trim( $title_fontsize ) . 'px;' : trim( $title_fontsize ) );
			$title_style .= ' line-height: ' . ( is_numeric( trim( $title_fontsize ) ) ? trim( $title_fontsize ) . 'px;' : trim( $title_fontsize ) );
		}

		if ( ! empty( $title_color ) ) {
			$title_style .= ' color: ' . esc_attr( $title_color ) . ';';
		}

		if ( ! empty( $description ) ) {
			if ( ! empty( $description_fontsize ) ) {
				$desc_style .= ' font-size: ' . ( is_numeric( trim( $description_fontsize ) ) ? trim( $description_fontsize ) . 'px;' : trim( $description_fontsize ) );
				$desc_style .= ' line-height: ' . ( is_numeric( trim( $description_fontsize ) ) ? trim( $description_fontsize ) . 'px;' : trim( $description_fontsize ) );
			}
			if ( ! empty( $description_color ) ) {
				$desc_style .= ' color: ' . esc_attr( $description_color ) . ';';
			}
		}

		// Generate HTML code.
		$html .= '<div class="counter-wrap tc ' . ( ( $horizontal == 'true' ) ? 'horizontal fc aic jcsb fcw' : '' ) . '">';

		if ( $icon ) {
			$html .= '<div class="icon" style="' . esc_attr( $icon_style ) . '"><i class="' . esc_attr( $icon_fontawesome ) . '"></i></div>';
		}

		if ( ! empty( $number ) ) {
			$html .= '<div class="number" style="' . esc_attr( $number_style ) . '">' . esc_html( $number ) . '</div>';
		}

		if ( ! empty( $title ) ) {
			$html .= '<h3 class="title" style="' . esc_attr( $title_style ) . '">' . esc_html( $title ) . '</h3>';
		}

		if ( ! empty( $description ) ) {
			$html .= '<div class="desc tu fwb" style="' . esc_attr( $desc_style ) . '">' . esc_html( $description ) . '</div>';
		}

		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_counter', force_balance_tags( $html ) );
	}
}
