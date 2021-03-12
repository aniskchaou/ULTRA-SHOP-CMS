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
 * Nitro Slider shortcode.
 */
class Nitro_Toolkit_Shortcode_Carousel extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'carousel';

	/**
	 * Enqueue custom scripts / stylesheets.
	 *
	 * @return  void
	 */
	public function enqueue_scripts() {
		if ( is_singular() ) {
			global $post;

			if ( has_shortcode( $post->post_content, "nitro_{$this->shortcode}" ) ) {
				// Enqueue required assets.
				wp_enqueue_style(  'owl-carousel' );
				wp_enqueue_script( 'owl-carousel' );
			}
		}

		// Let parent class load default scripts.
		parent::enqueue_scripts();
	}

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_carousel_custom_css';

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
					'items'              => 4,
					'gutter_width'       => 30,
					'pagination'         => 'false',
					'navigation'         => 'false',
					'extra_class'        => '',
					'nitro_carousel_custom_id' => '',
				),
				$atts
			)
		);

		// Generate custom ID.
		$id = $nitro_carousel_custom_id;

		if ( ! empty( $gutter_width ) ) {
			$css .= '
				#' . esc_attr( $id ) . ' .owl-item {
					padding: 0 ' . ( is_numeric( trim( $gutter_width / 2 ) ) ? trim( $gutter_width / 2 ) . 'px' : trim( $gutter_width / 2 ) ) . ';
				}
			';
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
		$html = $data = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'items'              => 4,
					'gutter_width'       => 30,
					'auto_play'          => '',
					'pagination'         => '',
					'navigation'         => '',
					'extra_class'        => '',
					'sc_992'             => 4,
					'sc_768'             => 3,
					'sc_600'             => 2,
					'sc_375'             => 1,
					'nitro_carousel_custom_id' => '',
				),
				$atts
			)
		);

		// Generate custom ID.
		$id = $nitro_carousel_custom_id;

		$wr_nitro_options = WR_Nitro::get_options();

		$attr = array();

		if ( ! empty( $items ) ) {
			$attr[] = '"items": "' . ( int ) $items . '"';
		}
		if ( ! empty( $auto_play ) ) {
			$attr[] = '"autoplay": "true"';
		}
		if ( ! empty( $navigation ) ) {
			$attr[] = '"nav": "true"';
		}
		if ( ! empty( $pagination ) ) {
			$attr[] = '"dots": "true"';
		}
		if ( ! empty( $sc_992 ) ) {
			$attr[] = '"desktop": "' . ( int ) $sc_992 . '"';
		}
		if ( ! empty( $sc_768 ) ) {
			$attr[] = '"tablet": "' . ( int ) $sc_768 . '"';
		}
		if ( ! empty( $sc_600 ) ) {
			$attr[] = '"mobile": "' . ( int ) $sc_600 . '"';
		}
		if ( ! empty( $sc_375 ) ) {
			$attr[] = '"sm_mobile": "' . ( int ) $sc_375 . '"';
		}

        if ( ! empty( $sc_992 ) || ! empty( $sc_768 ) || ! empty( $sc_600 ) || ! empty( $sc_375 ) ) {
            $attr[] = '"custom_responsive": "true"';
        }

		if ( ! empty( $attr ) ) {
			$data = 'data-owl-options=\'{' . esc_attr( implode( ', ', $attr ) ) . ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ) . '}\'';
		}

		$html .= '<div class="nitro-carousel-wrap oh">';
			$html .= '<div id="' . esc_attr( $id ) . '" class="nitro-carousel pr wr-nitro-carousel ' . esc_attr( $extra_class ) . '" ' . $data . '>';
				$html .= do_shortcode( $content );
			$html .= '</div>';
		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_carousel', force_balance_tags( $html ) );
	}
}
