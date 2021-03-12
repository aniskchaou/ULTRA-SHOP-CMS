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
 * Nitro Pricing Table Item shortcode.
 */
class Nitro_Toolkit_Shortcode_Pricing_single extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'pricing_single';

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
					'style'                    => 'style-1',
					'pricing_title'            => '',
					'pricing_title_desc'       => '',
					'pricing_price'            => '',
					'pricing_price_units'      => '',
					'pricing_units'            => '',
					'pricing_content'          => '',
					'button_text'              => '',
					'icon_fontawesome'         => '',
					'show_option_icon'	       => '',
					'button_link'              => '',
					'pricing_featured'         => '',
				),
				$atts
			)
		);

		$classes = array ();

		if ( $pricing_featured ) {
			$classes[] = 'featured';
		}

		$attr_pricing = Nitro_Toolkit_Shortcode::get_attrs();
		$style = $attr_pricing['style'];

		$values = ( array ) vc_param_group_parse_atts( $pricing_content );

		// Generate HTML code.
		$html .= '<div class="pricing-item ' . esc_attr( implode( ' ', $classes ) ) . '">';
			$html .= '<div class="inner ts-03 pr">';

			if ( $pricing_price ) {
				$html .= '<div class="pricing-header pr">';
				$html .= '<h3 class="pricing-title mg0">' . esc_html( $pricing_title ) . '</h3>';

				if ( 'style-1' != $style && ! empty( $pricing_title_desc ) ) {
					$html .= '<span class="pricing-desc db">' . esc_html( $pricing_title_desc ) . '</span>';
				}

				if ( 'style-2' != $style ) {
					$html .= '<div class="price-value pr ' . ( ( ( 'style-3' || 'style-4' ) == $style ) ? 'fc aic jcc' : '' ) . '">';
					$html .= '<span class="price ' . ( ( 'style-4' == $style ) ? 'fl' : '' ) . '">' . esc_html( $pricing_price ) . '</span>';

					if ( 'style-4' == $style ) {
						$html .= '<div class="fl fwb">';
					}

					$html .= '<span class="price-units">' . esc_html( $pricing_price_units ) . '</span>';
					$html .= '<span class="units">' . esc_html( $pricing_units ) . '</span>';

					if ( 'style-4' == $style ) {
						$html .= '</div>';
					}

					$html .= '</div>';
				}
				$html .= '</div>';
			}

			$html .= '<div class="pricing-content">';
				$html .= '<ul>';
					foreach ( $values as $key => $value ) {
						$html .= '<li>' . ( ( $show_option_icon == 'yes' ) ? '<i class="mgr10 ' . esc_attr( $value[ 'item_icon_fontawesome' ] ) . '"></i>' : '' ) . ( isset( $value[ 'pricing_item' ] ) ? esc_html( $value[ 'pricing_item' ] ) : NULL  ) . '</li>';
					}
				$html .= '</ul>';
			$html .= '</div>';
			$html .= '<div class="pricing-footer clear">';

			if ( 'style-2' != $style ) {

				if ( 'style-3' == $style ) {
					$btn_class = 'wr-btn wr-btn-solid';
				} else {
					$btn_class = 'wr-btn wr-btn-outline';
				}

				if ( $button_text ) {
					$html .= '<a class="pricing-button btr-40 dib ' . esc_attr( $btn_class ) . '" href="' . esc_url( $button_link ) . '">' . esc_html( $button_text ) . '</a>';
				} else {
					$html .= '<a class="pricing-button btr-40 dib ' . esc_attr( $btn_class ) . '" href="' . esc_url( $button_link ) . '">' . esc_html__( 'Purchase', 'nitro-toolkit' ) . '</a>';
				}

			} else {
				$html .= '<a class="pricing-button dib fl" href="' . esc_url( $button_link ) . '"><i class="fa fa-long-arrow-right ts-03"></i></a>';
				$html .= '<div class="price-value fr pr">';
				$html .= '<span class="price fl pdr10">' . esc_html( $pricing_price ) . '</span>';
				$html .= '<div class="fl fwb">';
				$html .= '<span class="price-units db">' . esc_html( $pricing_price_units ) . '</span>';
				$html .= '<span class="units db">' . esc_html( $pricing_units ) . '</span>';
				$html .= '</div>';
				$html .= '</div>';
			}

				$html .= '</div>';

			$html .= '</div>';
		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_pricing_single', force_balance_tags( $html ) );
	}
}
