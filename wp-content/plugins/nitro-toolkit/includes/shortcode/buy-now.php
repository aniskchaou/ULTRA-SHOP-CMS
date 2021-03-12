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
 * Nitro Buy Now shortcode.
 */
class Nitro_Toolkit_Shortcode_Buy_Now extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'buy_now';

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
				wp_enqueue_style( 'wr-nitro-woocommerce'     );
				wp_enqueue_script( 'magnific-popup' );
			}
		}

		// Let parent class load default scripts.
		parent::enqueue_scripts();
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
		$html = $id = '';

		$atts = shortcode_atts( array(
			'id'           => '',
			'checkout'     => 1,
			'payment_info' => 1,
			'style'        => 'text-icon',
			'button_text'  => 'Buy Now',
			'button_style' => 'wr-btn-solid',
			'extra_class'  => '',
		), $atts );

		// Get product ID
		$id = $atts['id'];

		if ( ! empty( $atts['button_text'] ) ) {
			$button_text = $atts['button_text'];
		} else {
			$button_text = 'Buy Now';
		}

		// Get extra class
		$classes = array( 'wr-buy-now' );

		if ( ! empty( $atts['style'] ) ) {
			$classes[] = $atts['style'];
		}

		if ( ! empty( $atts['extra_class'] ) ) {
			$classes[] = $atts['extra_class'];
		}

		$html .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		$html .= '<a class="product__btn btr-40 color-dark bgw dib btn-buynow pr wr-btn ' . esc_attr( $atts['button_style'] ) . '" href="#" data-product-id="' . esc_attr( $id ) . '" data-checkout="' . absint( $atts['checkout'] ) . '" data-payment-info="' . absint( $atts['payment_info'] ) . '" >';
		if ( 'text' != $atts['style'] ) {
			$html .= '<i class="fa fa-cart-arrow-down ' . ( ( 'icon' != $atts['style'] ) ? 'mgr10' : '' ) . '"></i>';
		}
		if ( 'icon' != $atts['style'] ) {
			$html .= esc_attr( $button_text );
		}
		$html .=  '<span class="tooltip ab ts-03">' . esc_attr( $button_text ) . '</span></a>';
		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_buy_now', force_balance_tags( $html ) );

	}
}
