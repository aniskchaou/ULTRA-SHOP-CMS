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
 * Nitro Separator shortcode.
 */
class Nitro_Toolkit_Shortcode_Separator extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'separator';

	/**
	 * Add google font to link base in Nitro theme.
	 *
	 * @return  void
	 */
	public function add_google_fonts() {
		if ( is_singular() ) {
			global $post;

			if ( has_shortcode( $post->post_content, "nitro_{$this->shortcode}" ) ) {
				$atts = parent::get_attr( $post->post_content );

				if ( $atts ) {
					$list_attr_font = array(
						'font_family' => 'font_weight',
					);
					parent::recursive_font( $atts, $list_attr_font );
				}
			}
		}
		parent::add_google_fonts();
	}

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_separator_custom_css';

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
					'align'               => 'left',
					'style'               => 'solid',
					'height'              => 1,
					'width'               => '100%',
					'color'               => '#c5c5c5',
					'symbol'              => 'icon',
					'icon_type'           => 'fontawesome',
					'icon_fontawesome'    => 'fa fa-adjust',
					'icon_openiconic'     => '',
					'icon_typicons'       => '',
					'icon_entypo'         => '',
					'icon_linecons'       => '',
					'icon_color'          => '#646464',
					'icon_size'           => '14',
					'graphic'             => '',
					'image_radius'        => '100',
					'text_editor'         => '',
					'text_transform'      => 'none',
					'spacing'             => '0',
					'font_family'         => 'Abril Fatface',
					'font_weight'         => 400,
					'font_size'           => 16,
					'text_color'          => '#4f4f4f',
					'margin_top'          => '',
					'margin_bottom'       => '',
					'el_class'            => '',
					'separator_custom_id' => '',
				),
				$atts
			)
		);

		// Generate custom css
		$id = $separator_custom_id;

		// Get image link and image data
		if ( 'image' == $symbol && ! empty( $graphic ) ) {
			// Get image link and image data
			$image = wpb_getImageBySize( array( 'attach_id' => preg_replace( '/[^\d]/', '', $graphic ) ) );
		}

		$css .= '
	#' . esc_attr( $id ) . ' {
		width: ' . ( is_numeric( trim( $width ) ) ? trim( $width ) . 'px' : trim( $width ) ) . ';';
		if ( $margin_top ) {
			$css .= 'margin-bottom: ' . ( is_numeric( trim( $margin_top ) ) ? trim( $margin_top ) . 'px' : trim( $margin_top ) ) . ';';
		}
		if ( $margin_bottom ) {
			$css .= 'margin-bottom: ' . ( is_numeric( trim( $margin_bottom ) ) ? trim( $margin_bottom ) . 'px' : trim( $margin_bottom ) ) . ';';
		}
	$css .= '}';
		if ( 'center' == $align ) {
			$css .= '
	#' . esc_attr( $id ) . '.dashed div:before,
	#' . esc_attr( $id ) . '.dashed div:after {
		background: none;
		border-bottom: 1px dashed ' . esc_attr( $color ) . ';
	}
	#' . esc_attr( $id ) . '.pattern div:before,
	#' . esc_attr( $id ) . '.pattern div:after {
		height: 3px;
	}';
		}
		if ( 'solid' == $style ) {
			$css .= '
	#' . esc_attr( $id ) . ' div {
		background: ' . esc_attr( $color ) . ';
		height: ' . ( is_numeric( trim( $height ) ) ? trim( $height ) . 'px' : trim( $height ) ) . ';
	}';
		}
		if ( 'double' == $style ) {
			$css .= '
	#' . esc_attr( $id ) . '.double div {
		border-top: 1px solid ' . esc_attr( $color ) . ';
		border-bottom: 1px solid ' . esc_attr( $color ) . ';
	}';
		}
		if ( 'dashed' == $style && 'center' != $align ) {
			$css .= '
	#' . esc_attr( $id ) . '.dashed div {
		border-bottom: 1px dashed ' . esc_attr( $color ) . ';
	}';
		}
		if ( 'dashed' == $style && 'center' != $align ) {
			$css .= '
	#' . esc_attr( $id ) . '.dashed div {
		border-bottom: 1px dashed ' . esc_attr( $color ) . ';
	}';
		}
		if ( 'icon' == $symbol && 'center' == $align ) {
			$css .= '
	#' . esc_attr( $id ) . ' span {
		margin-top: -' . ( is_numeric( trim( $icon_size / 2 + 2 ) ) ? trim( $icon_size / 2 + 2 ) . 'px' : trim( $icon_size / 2 + 2) ) . ';
	}';
		}
		if ( 'icon' == $symbol ) {
			$css .= '
	#' . esc_attr( $id ) . ' span {
		color: ' . $icon_color . ';
		font-size: ' . ( is_numeric( trim( $icon_size ) ) ? trim( $icon_size ) . 'px' : trim( $icon_size ) ) . ';
	}
	#' . esc_attr( $id ) . '.tl div,
	#' . esc_attr( $id ) . '.tr div {
		transform: translateY(' . ( is_numeric( trim( $icon_size / 2 + 2 ) ) ? trim( $icon_size / 2 + 2 ) . 'px' : trim( $icon_size / 2 + 2 ) ) . ');
		-o-transform: translateY(' . ( is_numeric( trim( $icon_size / 2 + 2 ) ) ? trim( $icon_size / 2 + 2 ) . 'px' : trim( $icon_size / 2 + 2 ) ) . ');
		-webkit-transform: translateY(' . ( is_numeric( trim( $icon_size / 2 + 2 ) ) ? trim( $icon_size / 2 + 2 ) . 'px' : trim( $icon_size / 2 + 2 ) ) . ');
	}';
		} elseif ( 'image' == $symbol ) {
			$css .= '
	#' . esc_attr( $id ) . '.tc span {
		width: ' . ( is_numeric( trim( $image['p_img_large'][1] + 10 ) ) ? trim( $image['p_img_large'][1] + 10 ) . 'px' : trim( $image['p_img_large'][1] + 10 ) ) . ';
	}
	#' . esc_attr( $id ) . ':not(.tc) div.sep-image {
		transform: translateY(' . ( is_numeric( trim( $image['p_img_large'][1] / 2 ) ) ? trim( $image['p_img_large'][1] / 2 ) . 'px' : trim( $image['p_img_large'][1] / 2 ) ) . ');
		-o-transform: translateY(' . ( is_numeric( trim( $image['p_img_large'][1] / 2 ) ) ? trim( $image['p_img_large'][1] / 2 ) . 'px' : trim( $image['p_img_large'][1] / 2 ) ) . ');
		-webkit-transform: translateY(' . ( is_numeric( trim( $image['p_img_large'][1] / 2 ) ) ? trim( $image['p_img_large'][1] / 2 ) . 'px' : trim( $image['p_img_large'][1] / 2 ) ) . ');
	}
	#' . esc_attr( $id ) . ' img {
		border-radius: ' . ( is_numeric( trim( $image_radius ) ) ? trim( $image_radius ) . 'px' : trim( $image_radius ) ) . ';
	}';
		} else {
			$css .= '
	#' . esc_attr( $id ) . ' span {
		font-size: ' . ( is_numeric( trim( $font_size ) ) ? trim( $font_size ) . 'px' : trim( $font_size ) ) . ';
		line-height: ' . ( is_numeric( trim( $font_size ) ) ? trim( $font_size ) . 'px' : trim( $font_size ) ) . ';
		font-family: "' . esc_attr( $font_family ) . '";
		text-transform: ' . esc_attr( $text_transform ) . ';
		letter-spacing: ' . esc_attr( $spacing ) . 'px;
		color: ' . esc_attr( $text_color ) . ';
	}';
		}
		if ( 'center' == $align && 'text' == $symbol ) {
			$css .= '
	#' . esc_attr( $id ) . ' div {
		bottom: ' . ( is_numeric( trim( $font_size / 2 ) ) ? trim( $font_size / 2 ) . 'px' : trim( $font_size / 2 ) ) . ';
	}';
		} elseif ( 'center' != $align && 'text' == $symbol ) {
			$css .= '
	#' . esc_attr( $id ) . ' div {
		margin-bottom: -' . ( is_numeric( trim( $font_size / 2 + 8 ) ) ? trim( $font_size / 2 + 8 ) . 'px' : trim( $font_size / 2 + 8 ) ) . ';
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
					'align'               => 'left',
					'style'               => 'solid',
					'height'              => 1,
					'width'               => '100%',
					'color'               => '#c5c5c5',
					'symbol'              => 'icon',
					'icon_type'           => 'fontawesome',
					'icon_fontawesome'    => 'fa fa-adjust',
					'icon_openiconic'     => '',
					'icon_typicons'       => '',
					'icon_entypo'         => '',
					'icon_linecons'       => '',
					'icon_color'          => '#646464',
					'icon_size'           => '14',
					'graphic'             => '',
					'image_radius'        => '100',
					'text_editor'         => '',
					'text_transform'      => 'none',
					'spacing'             => '0',
					'font_family'         => 'Abril Fatface',
					'font_weight'         => 400,
					'font_size'           => 16,
					'text_color'          => '#4f4f4f',
					'margin_top'          => '',
					'margin_bottom'       => '',
					'el_class'            => '',
					'separator_custom_id' => '',
				),
				$atts
			)
		);

		$classes = array( 'nitro-separator ' . $el_class );

		// Get separator alignment
		if ( 'left' == $align ) {
			$classes[] = 'tl';
		} elseif ( 'right' == $align ) {
			$classes[] = 'tr';
		} else {
			$classes[] = 'tc';
		}

		// Get separator style
		if ( $style ) {
			$classes[] = $style;
		}

		// Get image link and image data
		if ( 'image' == $symbol && ! empty( $graphic ) ) {
			// Get image link and image data
			$image = wpb_getImageBySize( array( 'attach_id' => preg_replace( '/[^\d]/', '', $graphic ) ) );
		}

		// Generate HTML code.
		$id = $separator_custom_id;

		$html .= '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . ' pr" data-align="' . esc_attr( $align ) . '">';

		if ( 'center' == $align ) {
			$html .= '<div class="sep-left pa sep-' . esc_attr( $symbol ) . '"></div>';
			$html .= '<div class="sep-right pa sep-' . esc_attr( $symbol ) . '"></div>';
		} else {
			$html .= '<div class="sep sep-' . esc_attr( $symbol ) . '"></div>';
		}

			if ( 'icon' == $symbol ) {
				// Enqueue needed icon font.
				vc_icon_element_fonts_enqueue( $icon_type );

				// Render class of icon
				$iconClass = isset( ${'icon_' . $icon_type} ) ? esc_attr( ${'icon_' . $icon_type} ) : 'fa fa-adjust';

				$html .= '<span class="' . $iconClass . '"></span>';
			} elseif ( 'image' == $symbol && ! empty( $graphic ) ) {
				$html .= '<span class="dib"><img src="' . esc_url( $image['p_img_large'][0] ) . '" alt="' . esc_attr__( 'Separator.', 'nitro-toolkit' ) . '" width="' . esc_attr( $image['p_img_large'][1] ) . '" height="' . esc_attr( $image['p_img_large'][2] ) . '" /></span>';
			} else {
				$html .= '<span class="dib">' . esc_html( $text_editor ) . '</span>';
			}

		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_separator', force_balance_tags( $html ) );

	}

}
