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
 * Nitro Subscribe Form shortcode.
 */
class Nitro_Toolkit_Shortcode_Subscribe_Form extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'subscribe_form';

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_subscribe_form_custom_css';

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
					'form_width'    => '300px',
					'form_height'   => '50px',
					'submit_button' => 'button-submit',
					'icon_size'     => '16',
					'icon_position' => 'inside',
					'icon_color'    => '#333',
					'text_size'     => '14',
					'bg_color'      => '#333',
					'text_color'    => '#fff',
					'border_width'  => '1px',
					'border_radius' => '',
					'border_color'  => '#ebebeb',
					'extra_class'   => '',
					'subscribe_form_custom_id'   => '',
				), $atts
			)
		);

		// Generate custom ID
		$id = $subscribe_form_custom_id;

	if ( ! empty( $form_width ) ) {
			$css .= '
		#' . esc_attr( $id ) . '.sc-subscribe-form {
			width: ' . esc_attr( $form_width ) . ';
		}';
	}
		$css .= '
	#' . esc_attr( $id ) . '.sc-subscribe-form input,
	#' . esc_attr( $id ) . '.sc-subscribe-form button {
		height: ' . ( ! empty( $form_height ) ? esc_attr( $form_height ) : '45px' ) . ';
		line-height: ' . ( ! empty( $form_height ) ? esc_attr( $form_height ) : '45px' ) . ';
		border-width: ' . ( ! empty( $border_width ) ? esc_attr( $border_width ) : '1px' ) . ';
		border-color: ' . ( ! empty( $border_color ) ? esc_attr( $border_color ) : '#ebebeb' ) . ';
		border-radius: ' . ( ! empty( $border_radius ) ? esc_attr( $border_radius ) . 'px' : '0px' ) . ';
	}';

	if ( 'button-submit' == $submit_button && $border_radius ) {
			$css .= '
	#' . esc_attr( $id ) . '.sc-subscribe-form input {
			margin-right: 10px;
		}';
	}

	if ( 'button-submit' == $submit_button ) {
			$css .= '
		#' . esc_attr( $id ) . '.sc-subscribe-form input[type="submit"] {
			background: ' . esc_attr( $bg_color ) . ';
			color: ' . esc_attr( $text_color ) . ';
			font-size: ' . ( ! empty( $text_size ) ? esc_attr( $text_size ) . 'px' : '14px' ) . ';
		}';
	} else {
		$css .= '
		#' . esc_attr( $id ) . '.sc-subscribe-form button i {
			color: ' . esc_attr( $icon_color ) . ';
			font-size: ' . ( ! empty( $icon_size ) ? esc_attr( $icon_size ) . 'px' : '16px' ) . ';
		}';
		$css .= '
		#' . esc_attr( $id ) . '.sc-subscribe-form button {
			width: ' . esc_attr( $form_height ) . ';
		}';
	}
	if ( 'icon-submit' == $submit_button && 'outside' == $icon_position ) {
			$css .= '
		#' . esc_attr( $id ) . '.sc-subscribe-form input[type="email"] {
			width: calc(100% - ' . esc_attr( $form_height ) . ');
		}';
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
		extract(
			shortcode_atts(
				array(
					'link'          => '',
					'placeholder'   => 'Enter your email',
					'submit_button' => 'button-submit',
					'icon_position' => 'inside',
					'button_text'   => 'Subscribe',
					'extra_class'   => '',
					'subscribe_form_custom_id'   => '',
				), $atts
			)
		);

		$classes = array();

		if ( ! empty( $extra_class ) ) {
			$classes[] = $extra_class;
		}
		if ( 'icon-submit' == $submit_button ) {
			$classes[] = $icon_position;
		}

		if ( ! empty( $submit_button ) ) {
			$classes[] = $submit_button;
		}

		// Generate custom ID
		$id = $subscribe_form_custom_id;

		// Generate HTML code.
		$html .= '<div id="' . esc_attr( $id ) . '" class="sc-subscribe-form pr ' . esc_attr( implode( ' ', $classes ) ) . '">';

			$html .= '<form target="_blank" rel="noopener noreferrer" class="validate" name="mc-embedded-subscribe-form" method="post" action="' . esc_url( $link ) . '">';
			$html .= '<div class="mc-field-group ' . ( ( 'button-submit' == $submit_button ) ? 'fc' : '' ) . '">';

			$html .= '<input type="email" required="" placeholder="' . esc_attr( $placeholder ) . '" class="newsletter-email extenal-bdcl" name="EMAIL" value="">';
			$html .= '<input type="hidden" id="group_8" name="group[19829][8]" value="1" class="av-checkbox">';

			if ( 'button-submit' ==  $submit_button ) {
				$html .= '<input type="submit" value="' . esc_attr( $button_text ) . '" class="newsletter-submit">';
			} else {
				$html .= '<button type="submit" class="newsletter-submit pa"><i class="fa fa-envelope-o"></i></button>';
			}

			$html .= '</div>';
			$html .= '</form>';

		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_subscribe_form', force_balance_tags( $html ) );
	}
}
