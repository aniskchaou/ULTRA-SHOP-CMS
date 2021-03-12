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
 * Nitro Heading shortcode.
 */
class Nitro_Toolkit_Shortcode_Heading extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'heading';

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
						'font_family'      => 'font_weight',
						'sub_font_family'  => 'sub_font_weight',
						'desc_font_family' => 'desc_font_weight',
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
	protected $metakey = '_wr_shortcode_heading_custom_css';

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
					'tag'                 => 'h1',
					'extra_class'         => '',
					'font_weight'         => '400',
					'text_transform'      => 'none',
					'color'               => '#646464',
					'font_size'           => 44,
					'line_height'         => '44px',
					'text'                => __( 'This is custom heading element', 'wr-nitro' ),
					'sub_text'            => __( 'This is custom heading element', 'wr-nitro' ),
					'desc_text'           => __( 'This is custom heading element', 'wr-nitro' ),
					'link_to'             => '',
					'font_family'         => '',
					'sub_font_family'     => '',
					'desc_font_family'    => '',
					'spacing'             => 0,
					'margin_top'          => 10,
					'margin_bottom'       => 20,
					'sub_font_weight'     => '400',
					'sub_text_transform'  => 'none',
					'sub_color'           => '#4f4f4f',
					'sub_font_size'       => 20,
					'sub_line_height'     => '20px',
					'sub_spacing'         => 0,
					'desc_font_weight'    => '400',
					'desc_text_transform' => 'none',
					'desc_color'          => '',
					'desc_font_size'      => 10,
					'desc_line_height'    => '15px',
					'desc_spacing'        => 3,
					'separator'           => 'none',
					'separator_mgt'       => '',
					'separator_mgb'       => '',
					'separator_width'     => 48,
					'separator_height'    => 1,
					'separator_position'  => 'top',
					'separator_color'     => '#8e8e8e',
					'icon_color'          => '',
					'icon_position'       => 'top',
					'icon_size'           => '',
					'icon_line'           => '',
					'graphic'             => '',
					'image_position'      => 'top',
					'image_radius'        => '',
					'image_line'          => '',
					'heading_custom_id'   => '',
				),
				$atts
			)
		);

		// Generate custom ID.
		$id = $heading_custom_id;

		// Get image link and image data
		if ( 'image' == $separator && ! empty( $graphic ) ) {
			// Get image link and image data
			$image = wpb_getImageBySize( array( 'attach_id' => preg_replace( '/[^\d]/', '', $graphic ) ) );
		}

		if ( ! empty( $sub_text ) ) {
			$css .= '
	#' . esc_attr( $id ) . ' .sub {';
		if ( $sub_font_family ) {
			$css .= '
			font-family: "' . esc_attr( $sub_font_family ) . '";
			font-weight: ' . intval( esc_attr( $sub_font_weight ) ) . ';
			color: ' . $sub_color . ';';
			if ( strpos( $sub_font_weight, 'i' ) !== false ) {
				$css .= '
				font-style: italic;';
			}
		}
	$css .= '
		text-transform: ' . esc_attr( $sub_text_transform ) . ';
		font-size: ' . ( is_numeric( trim( $sub_font_size ) ) ? trim( $sub_font_size ) . 'px' : trim( $sub_font_size ) ) . ';
		line-height: ' . ( is_numeric( trim( $sub_line_height ) ) ? trim( $sub_line_height ) . 'px' : trim( $sub_line_height ) ) . ';
		letter-spacing: ' . ( is_numeric( trim( $sub_spacing ) ) ? trim( $sub_spacing ) . 'px' : trim( $sub_spacing ) ) . ';
	}';
		}

		if ( ! empty( $text ) ) {
			$css .= '
				#' . esc_attr( $id ) . ' .heading > * {';
					if ( $font_family ) {
						$css .= '
						font-family: "' . esc_attr( $font_family ) . '";
						font-weight: ' . intval( esc_attr( $font_weight ) ) . ';';
						if ( strpos( $font_weight, 'i' ) !== false ) {
							$css .= '
							font-style: italic;';
						}
					}
					if ( ! $link_to ) {
						$css .= 'color: ' . esc_attr( $color ) . ';';
					}
				$css .= '
					margin-top: ' . ( is_numeric( trim( $margin_top ) ) ? trim( $margin_top ) . 'px' : trim( $margin_top ) ) . ';
					margin-bottom: ' . ( is_numeric( trim( $margin_bottom ) ) ? trim( $margin_bottom ) . 'px' : trim( $margin_bottom ) ) . ';
					text-transform: ' . esc_attr( $text_transform ) . ';
					font-size: ' . ( is_numeric( trim( $font_size ) ) ? trim( $font_size ) . 'px' : trim( $font_size ) ) . ';
					line-height: ' . ( is_numeric( trim( $line_height ) ) ? trim( $line_height ) . 'px' : trim( $line_height ) ) . ';
					letter-spacing: ' . ( is_numeric( trim( $spacing ) ) ? trim( $spacing ) . 'px' : trim( $spacing ) ) . ';
				}
			';
			if ( ! empty( $link_to ) ) {
				$css .= '#' . esc_attr( $id ) . ' .heading a {';
					$css .= 'color: ' . esc_attr( $color ) . ';';
				$css .= '}';
			}
		}

		if ( ! empty( $desc_text ) ) {
			$css .= '
	#' . esc_attr( $id ) . ' .desc {';
		if ( $desc_font_family ) {
			$css .= '
			font-family: "' . esc_attr( $desc_font_family ) . '";
			font-weight: ' . intval( esc_attr( $desc_font_weight ) ) . ';
			color: ' . esc_attr( $desc_color ) . ';';
			if ( strpos($desc_font_weight, 'i') !== false ) {
				$css .= '
				font-style: italic;';
			}
		}
	$css .= '
		text-transform: ' . esc_attr( $desc_text_transform ) . ';
		font-size: ' . ( is_numeric( trim( $desc_font_size ) ) ? trim( $desc_font_size ) . 'px' : trim( $desc_font_size ) ) . ';
		line-height: ' . ( is_numeric( trim( $desc_line_height ) ) ? trim( $desc_line_height ) . 'px' : trim( $desc_line_height ) ) . ';
		letter-spacing: ' . ( is_numeric( trim( $desc_spacing ) ) ? trim( $desc_spacing ) . 'px' : trim( $desc_spacing ) ) . ';
	}';
		}

		if ( 'line' == $separator ) {
			if ( 'top' == $separator_position ) {
	$css .= '
	#' . esc_attr( $id ) . '.tl {
		padding-left: ' . ( is_numeric( trim( $separator_width + 8 ) ) ? trim( $separator_width + 8 ) . 'px' : trim( $separator_width + 8 ) ) . ';
	}
	#' . esc_attr( $id ) . '.tr {
		padding-right: ' . ( is_numeric( trim( $separator_width + 8 ) ) ? trim( $separator_width + 8 ) . 'px' : trim( $separator_width + 8 ) ) . ';
	}';
			}
	$css .= '
	#' . esc_attr( $id ) . ' .sep:before,
	#' . esc_attr( $id ) . ' .sep:after {
		content: "";
		position: absolute;
	}
	#' . esc_attr( $id ) . ' .sep.line:before,
	#' . esc_attr( $id ) . '.tc .sep.line.top:after {
		width: ' . ( is_numeric( trim( $separator_width ) ) ? trim( $separator_width ) . 'px' : trim( $separator_width ) ) . ';
		height: ' . ( is_numeric( trim( $separator_height ) ) ? trim( $separator_height ) . 'px' : trim( $separator_height ) ) . ';
		background: ' . $separator_color . ';
		margin-top: -' . ( is_numeric( trim( $separator_height / 2 ) ) ? trim( $separator_height / 2 ) . 'px' : trim( $separator_height / 2 ) ) . ';
	}
	#' . esc_attr( $id ) . '.tc .sep.line:before {
		left: -' . ( is_numeric( trim( $separator_width + 25 ) ) ? trim( $separator_width + 25 ) . 'px' : trim( $separator_width + 25 ) ) . ';
	}
	#' . esc_attr( $id ) . '.tc .sep.line.top:after {
		right: -' . ( is_numeric( trim( $separator_width + 25 ) ) ? trim( $separator_width + 25 ) . 'px' : trim( $separator_width + 25 ) ) . ';
	}
	#' . esc_attr( $id ) . '.tc .sep.line.bottom:before {
		margin-left: -' . ( is_numeric( trim( $separator_width / 2 ) ) ? trim( $separator_width / 2 ) . 'px' : trim( $separator_width / 2 ) ) . ';
		left: 50%;
		bottom: -25px;
	}
	#' . esc_attr( $id ) . '.tl .sep.line.top:before,
	#' . esc_attr( $id ) . '.tr .sep.line.top:before {
		top: -25px;
	}
	#' . esc_attr( $id ) . '.tl .sep.line.top:before {
		left: 0;
	}
	#' . esc_attr( $id ) . '.tl .sep.line.bottom:before,
	#' . esc_attr( $id ) . '.tr .sep.line.bottom:before {
		bottom: -25px;
	}
	#' . esc_attr( $id ) . '.tr .sep.line.bottom:before {
		right: 0;
	}
	#' . esc_attr( $id ) . '.tc .sep.line.top:before,
	#' . esc_attr( $id ) . '.tc .sep.line.top:after {
		top: 50%;
	}
	#' . esc_attr( $id ) . '.tr .sep.line.top:before,
	#' . esc_attr( $id ) . '.tr .sep.line.bottom:before {
		right: 0;
	}';

			if ( ! empty( $separator_mgt ) || ! empty( $separator_mgb ) ) {
				$css .= '
	#' . esc_attr( $id ) . ' .sep.line:before,
	#' . esc_attr( $id ) . ' .sep.line:after {';

				if ( ! empty( $separator_mgt ) ) {
					$css .= '
		margin-top: ' . ( is_numeric( trim( $separator_mgt ) ) ? trim( $separator_mgt ) . 'px' : trim( $separator_mgt ) ) . ' !important;';
				}

				if ( ! empty( $separator_mgb ) ) {
					$css .= '
		margin-bottom: ' . ( is_numeric( trim( $separator_mgb ) ) ? trim( $separator_mgb ) . 'px' : trim( $separator_mgb ) ) . ';';
				}

				$css .= '
	}';
			}
		} elseif ( 'icon' == $separator ) {
			$css .= '
	#' . esc_attr( $id ) . '.tr .icon.has-line:after {
		right: ' . ( is_numeric( trim( $icon_size + 5 ) ) ? trim( $icon_size + 5 ) . 'px' : trim( $icon_size + 5 ) ) . ';
	}
	#' . esc_attr( $id ) . ' .sep span {
		color: ' . esc_attr( $icon_color ) . ';
		font-size: ' . ( is_numeric( trim( $icon_size ) ) ? trim( $icon_size ) . 'px' : trim( $icon_size ) ) . ';
	}';

			if ( ! empty( $separator_mgt ) || ! empty( $separator_mgb ) ) {
				$css .= '
	#' . esc_attr( $id ) . ' .sep.icon {';

				if ( ! empty( $separator_mgt ) ) {
					$css .= '
		margin-top: ' . ( is_numeric( trim( $separator_mgt ) ) ? trim( $separator_mgt ) . 'px' : trim( $separator_mgt ) ) . ';';
				}

				if ( ! empty( $separator_mgb ) ) {
					$css .= '
		margin-bottom: ' . ( is_numeric( trim( $separator_mgb ) ) ? trim( $separator_mgb ) . 'px' : trim( $separator_mgb ) ) . ';';
				}

				$css .= '
	}';
			}
		} elseif ( 'image' == $separator ) {
			$css .= '
	#' . esc_attr( $id ) . ' .sep.image img {
		border-radius: ' . ( is_numeric( trim( $image_radius ) ) ? trim( $image_radius ) . 'px' : trim( $image_radius ) ) . ';
	}
	#' . esc_attr( $id ) . '.tr .image.has-line:after {
		right: ' . ( isset( $image['p_img_large'][1] ) && is_numeric( trim( $image['p_img_large'][1] + 7 ) ) ? trim( isset( $image['p_img_large'][1] ) && $image['p_img_large'][1] + 7 ) . 'px' : trim( isset( $image['p_img_large'][1] ) && $image['p_img_large'][1] + 7 ) ) . ';
	}';

			if ( ! empty( $separator_mgt ) || ! empty( $separator_mgb ) ) {
				$css .= '
	#' . esc_attr( $id ) . ' .sep.image {';

				if ( ! empty( $separator_mgt ) ) {
					$css .= '
		margin-top: ' . ( is_numeric( trim( $separator_mgt ) ) ? trim( $separator_mgt ) . 'px' : trim( $separator_mgt ) ) . ';';
				}

				if ( ! empty( $separator_mgb ) ) {
					$css .= '
		margin-bottom: ' . ( is_numeric( trim( $separator_mgb ) ) ? trim( $separator_mgb ) . 'px' : trim( $separator_mgb ) ) . ';';
				}

				$css .= '
	}';
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
		extract(
			shortcode_atts(
				array(
					'align'               => 'left',
					'tag'                 => 'h1',
					'extra_class'         => '',
					'text'                => __( 'This is custom heading element', 'nitro' ),
					'font_family'         => '',
					'font_weight'         => '',
					'text_transform'      => 'none',
					'color'               => '',
					'font_size'           => 44,
					'line_height'         => '44px',
					'spacing'             => 0,
					'margin_top'          => 10,
					'margin_bottom'       => 20,
					'link_to'             => '',
					'sub_text'            => __( 'This is custom heading element', 'nitro' ),
					'sub_font_family'     => '',
					'sub_font_weight'     => '',
					'sub_text_transform'  => 'none',
					'sub_color'           => '',
					'sub_font_size'       => 20,
					'sub_line_height'     => '20px',
					'sub_spacing'         => 0,
					'desc_text'           => __( 'This is custom heading element', 'nitro' ),
					'desc_font_family'    => '',
					'desc_font_weight'    => '',
					'desc_text_transform' => 'none',
					'desc_color'          => '',
					'desc_font_size'      => 10,
					'desc_line_height'    => '15px',
					'desc_spacing'        => 3,
					'separator'           => 'none',
					'separator_mgt'       => '',
					'separator_mgb'       => '',
					'separator_width'     => 48,
					'separator_height'    => 1,
					'separator_position'  => 'top',
					'separator_color'     => '#8e8e8e',
					'icon_type'           => 'fontawesome',
					'icon_fontawesome'    => 'fa fa-adjust',
					'icon_openiconic'     => 'vc-oi vc-oi-dial',
					'icon_typicons'       => 'typcn typcn-adjust-brightness',
					'icon_entypo'         => 'entypo-icon entypo-icon-note',
					'icon_linecons'       => 'vc_li vc_li-heart',
					'icon_color'          => '',
					'icon_position'       => 'top',
					'icon_size'           => '',
					'icon_line'           => '',
					'graphic'             => '',
					'image_position'      => 'top',
					'image_radius'        => '',
					'image_line'          => '',
					'heading_custom_id'   => '',
				),
				$atts
			)
		);

		$classes = array( 'nitro-heading' );

		// Get extra class
		if ( ! empty( $extra_class ) ) {
			$classes[] = $extra_class;
		}

		// Get separator class
		$sep = array( 'sep', $separator );

		// Get heading alignment
		if ( 'left' == $align ) {
			$classes[] = 'tl';
		} elseif ( 'right' == $align ) {
			$classes[] = 'tr';
		} else {
			$classes[] = 'tc';
		}

		// Get image link and image data
		if ( 'image' == $separator && ! empty( $graphic ) ) {
			// Get image link and image data
			$image = wpb_getImageBySize( array( 'attach_id' => preg_replace( '/[^\d]/', '', $graphic ) ) );
		}

		// Generate HTML code.
		$id = $heading_custom_id;

		$html .= '<div id="' . esc_attr( $id ) . '" class="pr ' . esc_attr( implode( ' ', $classes ) ) . '">';

		if ( 'icon' == $separator && 'top' == $icon_position ) {
			// Get separator icon position
			$sep[] = $icon_position;

			// Enable line
			if ( 'true' == $icon_line ) {
				$sep[] = 'has-line';
			}

			// Enqueue needed icon font.
			vc_icon_element_fonts_enqueue( $icon_type );

			// Render class of icon
			$iconClass = isset( ${'icon_' . $icon_type} ) ? esc_attr( ${'icon_' . $icon_type} ) : 'fa fa-adjust';

			$html .= '<div class="' . esc_attr( implode( ' ', $sep ) ) . '">';
			$html .= '<span class="' . $iconClass . '"></span>';
			$html .= '</div>';

		}

		elseif ( 'image' == $separator && ! empty( $graphic ) && 'top' == $image_position ) {
			// Get separator icon position
			$sep[] = $image_position;

			// Enable line
			if ( 'true' == $image_line ) {
				$sep[] = 'has-line';
			}

			$html .= '<div class="' . esc_attr( implode( ' ', $sep ) ) . '">';
				$html .= '<img src="' . esc_url( $image['p_img_large'][0] ) . '" alt="Separator" width="' . esc_attr( $image['p_img_large'][1] ) . '" height="' . esc_attr( $image['p_img_large'][2] ) . '" />';
			$html .= '</div>';

		}

		if ( ! empty( $sub_text ) ) {
			$html .= '<div class="sub">';
			$html .= '<span>' . wp_kses_post( $sub_text ) . '</span>';
			$html .= '</div>';
		}

		if ( ! empty( $text ) ) {
			$html .= '<div class="heading">';
			$html .= '<' . esc_attr( $tag ) . '>';
				if ( ! empty( $link_to ) ) {
					$html .= '<a href="' . esc_url( $link_to ) . '">';
				}
					$html .= wp_kses_post( $text );
				if ( ! empty( $link_to ) ) {
					$html .= '</a>';
				}
			$html .= '</' . esc_attr( $tag ) . '>';
			$html .= '</div>';
		}

		if ( ! empty( $desc_text ) ) {
			$html .= '<div class="desc">';
			$html .= '<span>' . wp_kses_post( $desc_text ) . '</span>';
			$html .= '</div>';
		}

		if ( 'line' == $separator ) {
			// Get separator line position
			$sep[] = $separator_position;

			$html .= '<div class="' . esc_attr( implode( ' ', $sep ) ) . '">';
			$html .= '</div>';

		}

		elseif ( 'icon' == $separator && 'bottom' == $icon_position ) {
			// Get separator icon position
			$sep[] = $icon_position;

			// Enable line
			if ( 'true' == $icon_line ) {
				$sep[] = 'has-line';
			}

			// Enqueue needed icon font.
			vc_icon_element_fonts_enqueue( $icon_type );

			// Render class of icon
			$iconClass = isset( ${'icon_' . $icon_type} ) ? esc_attr( ${'icon_' . $icon_type} ) : 'fa fa-adjust';

			$html .= '<div class="' . esc_attr( implode( ' ', $sep ) ) . '">';
			$html .= '<span class="' . $iconClass . '"></span>';
			$html .= '</div>';

		}

		elseif ( 'image' == $separator && ! empty( $graphic ) && 'bottom' == $image_position ) {
			// Get separator icon position
			$sep[] = $image_position;

			// Enable line
			if ( 'true' == $image_line ) {
				$sep[] = 'has-line';
			}

			$html .= '<div class="' . esc_attr( implode( ' ', $sep ) ) . '">';
				$html .= '<img src="' . esc_url( $image['p_img_large'][0] ) . '" alt="Separator" width="' . esc_attr( $image['p_img_large'][1] ) . '" height="' . esc_attr( $image['p_img_large'][2] ) . '" />';
			$html .= '</div>';

		}

		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_heading', force_balance_tags( $html ) );
	}
}
