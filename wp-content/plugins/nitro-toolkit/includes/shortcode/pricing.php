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
 * Nitro Pricing Table shortcode.
 */
class Nitro_Toolkit_Shortcode_Pricing extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'pricing';

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_pricing_custom_css';

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
		$atts = shortcode_atts( array(
			'style'             => 'style-1',
			'pricing_custom_id' => '',
		), $atts );

		// Make shortcode attributes accessible from outside.
		Nitro_Toolkit_Shortcode::set_attrs( $atts );

		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();

		$color                = $wr_nitro_options['custom_color'];
		$btn_primary_bg       = $wr_nitro_options['btn_primary_bg_color'];
		$btn_primary          = $wr_nitro_options['btn_primary_color'];
		$btn_primary_border   = $wr_nitro_options['btn_primary_border_color'];
		$btn_secondary_bg     = $wr_nitro_options['btn_secondary_bg_color'];
		$btn_secondary        = $wr_nitro_options['btn_secondary_color'];
		$btn_secondary_border = $wr_nitro_options['btn_secondary_border_color'];

		if ( empty( $color ) ) {
			$color = '#d6aa74';
		}

		// Generate custom ID.
		$id = $atts['pricing_custom_id'];

		$css .= '
	#' . esc_attr( $id ) . '.style-1 .price-value,
	#' . esc_attr( $id ) . '.style-4 .price-value,
	#' . esc_attr( $id ) . '.style-3 .pricing-title {
		color: ' . esc_attr( $color ) . '!important;
	}
	#' . esc_attr( $id ) . '.style-2 .fa,
	#' . esc_attr( $id ) . '.style-2 .pricing-title,
	#' . esc_attr( $id ) . '.style-2 .pricing-item:hover .price-value {
		color: ' . esc_attr( $color ) . ';
	}
	#' . esc_attr( $id ) . '.style-1 .pricing-item > .inner:before,
	#' . esc_attr( $id ) . '.style-1 .pricing-item > .inner:after,
	#' . esc_attr( $id ) . '.style-2 .pricing-item > .inner:before,
	#' . esc_attr( $id ) . '.style-2 .pricing-item > .inner:after {
		border-color: ' . esc_attr( $color ) . ';
	}
	#' . esc_attr( $id ) . '.style-2 .featured .inner,
	#' . esc_attr( $id ) . '.style-4 .featured .inner,
	#' . esc_attr( $id ) . '.style-3 .featured .price-value {
		background-color: ' . esc_attr( $color ) . ' !important;
		color: #fff !important;
	}
	#' . esc_attr( $id ) . '.style-1 .featured  {
		box-shadow: 0px 6px 0px 0px ' . esc_attr( $color ) . ';
		z-index: 1;
	}';

		$css .= '
	.wr-pricing-table:not(.style-2) .pricing-item .pricing-button.wr-btn-outline {
		border-color: ' . esc_attr( $btn_secondary_border['normal'] ) . ';
	}
	.wr-pricing-table:not(.style-2) .pricing-item .pricing-button.wr-btn-outline:hover {
		border-color: ' . esc_attr( $btn_secondary_border['hover'] ) . ';
	}
	.wr-pricing-table.style-3 .pricing-item .pricing-button.wr-btn-solid {
		border-color: ' . esc_attr( $btn_primary_border['normal'] ) . ' !important;
	}
	.wr-pricing-table.style-3 .pricing-item .pricing-button.wr-btn-solid:hover {
		border-color: ' . esc_attr( $btn_primary_border['hover'] ) . ' !important;
	}';

		// Reset globally accessible shortcode attributes.
		Nitro_Toolkit_Shortcode::set_attrs( null );

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
		$atts = shortcode_atts( array(
			'style'             => 'style-1',
			'el_class'          => '',
			'pricing_custom_id' => '',
		), $atts );

		// Make shortcode attributes accessible from outside.
		Nitro_Toolkit_Shortcode::set_attrs( $atts );

		$classes = array ();

		if ( ! empty( $atts['style'] ) ) {
			$classes[] = $atts['style'];
		}

		if ( ! empty( $atts['el_class'] ) ) {
			$classes[] = $atts['el_class'];
		}

		if ( 'style-2' == $atts['style']) {
			$classes[] = 'tl';
		} else {
			$classes[] = 'tc';
		}

		// Generate custom ID.
		$id = $atts['pricing_custom_id'];

		// Generate HTML code.
		$html .= '<div id="' . esc_attr( $id ) . '" class="wr-pricing-table fc ' . esc_attr( implode( ' ', $classes ) ) . '">';
		$html .= do_shortcode( $content );
		$html .= '</div>';

		// Reset globally accessible shortcode attributes.
		Nitro_Toolkit_Shortcode::set_attrs( null );

		return apply_filters( 'nitro_toolkit_shortcode_pricing', force_balance_tags( $html ) );
	}
}
