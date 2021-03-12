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
 * Nitro Blockquotes shortcode.
 */
class Nitro_Toolkit_Shortcode_Quote extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'quote';

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_quote_custom_css';

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
				'style'            => 'border',
				'icon_position'    => 'left-top',
				'icon_color'       => '#363636',
				'icon_margin_top'  => '',
				'icon_margin_left' => '',
				'border_color'     => '#363636',
				'bg_color'         => '#646464',
				'text_color'       => '#222',
				'font_size'        => '',
				'line_height'      => '',
				'spacing'          => '',
				'outline_style'    => 'solid',
				'border_width'     => '3',
				'quote_content'    => '',
				'author'           => '',
				'quote_custom_id'  => '',
			), $atts )
		);

		// Generate custom ID
		$id = $quote_custom_id;
		$css .= '#' . esc_attr( $id ) . '.nitro-quote blockquote .quote-content {';
			$css .= $text_color ? 'color: ' . esc_attr( $text_color ) . ';' : '';
			$css .= $font_size ? 'font-size: ' . esc_attr( $font_size ) . 'px;' : '';
			$css .= $line_height ? 'line-height: ' . esc_attr( $line_height ) . 'px;' : '';
			$css .= $spacing ? 'letter-spacing: ' . esc_attr( $spacing ) . 'px;' : '';
		$css .= '}';

		$css .= '#' . esc_attr( $id ) . '.nitro-quote blockquote {';
			if ( $style == 'border' || $style == 'outline' ) {
				$css .= '
					border-color: ' . esc_attr( $border_color ) . ' !important;
					border-width: ' . esc_attr( $border_width ) . 'px;
				';
			}

			if ( $style == 'outline' ) {
				$css .= 'border-style: ' . esc_attr( $outline_style ) . ';';
			}

			if ( $style == 'solid-bg' ) {
				$css .= 'background-color: ' . esc_attr( $bg_color ) . ';';
			}
		$css .= '}';

		if ( $style == 'quote-icon' && ! ( empty( $icon_margin_top ) || empty( $icon_margin_left ) ) ) {
			$css .= '#' . esc_attr( $id ) . '.nitro-quote.quote-icon svg {';
				$css .= $icon_margin_top ? 'margin-top: ' . esc_attr( $icon_margin_top ) . 'px;' : '';
				$css .= $icon_margin_left ? 'margin-left: ' . esc_attr( $icon_margin_left ) . 'px;' : '';
			$css .= '}';
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
				'style'           => 'border',
				'border_position' => 'tl',
				'icon_color'      => '#363636',
				'quote_content'   => '',
				'author'          => '',
				'quote_custom_id' => '',
				'extra_class'     => '',
			), $atts )
		);

		$classes = array();

		// Get quote style
		if ( $style ) {
			$classes[] = $style;
		}

		// Get border position
		if ( $border_position && $style == 'border' ) {
			$classes[] = $border_position;
		}

		// Custom class
		if ( ! empty( $extra_class ) ) {
			$classes[] = $extra_class;
		}

		// Generate custom ID
		$id = $quote_custom_id;

		$html .= '<div id="' . esc_attr( $id ) . '" class="nitro-quote ' . esc_attr( implode( ' ', $classes ) ) . '">';
			$html .= '<blockquote class="pr">';
				$html .= '<div class="quote-content pr">';
					if ( $style == 'quote-icon' ) {
						$html .= '<svg class="bts-40">';
							$html .= '<g>';
								$html .= '<text y="82" x="-2" fill="' . esc_attr( $icon_color ) . '">“</text>';
							$html .= '</g>';
						$html .= '</svg>';
					}
					if ( ! empty( $quote_content ) ) {
						$html .= '<span>' . wp_kses_post( $quote_content ) . '</span>';
					}
				$html .= '</div>';

				if ( ! empty( $author ) ) {
					$html .= '<span class="quote-author db mgt20">' . esc_html( $author ) . '</span>';
				}
			$html .= '</blockquote>';
		$html .= '</div>';

		return apply_filters( 'Nitro_Toolkit_Shortcode_Quote', force_balance_tags( $html ) );
	}
}