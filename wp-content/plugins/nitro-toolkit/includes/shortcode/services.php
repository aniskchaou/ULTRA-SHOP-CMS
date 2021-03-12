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
 * Nitro Services shortcode.
 */
class Nitro_Toolkit_Shortcode_Services extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'services';

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
						'character_font_family' => 'character_font_weight',
						'font_family'           => 'font_weight',
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
	protected $metakey = '_wr_shortcode_services_custom_css';

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
					'style'                   => 'character',
					'align'                   => 'left',
					'content_box'             => '',
					'text_color'              => '',
					'read_more'               => '',
					'read_more_link'          => '',
					'extra_class'             => '',
					'character_text'          => 1,
					'character_color'         => '#e2e2e2',
					'character_font_family'   => 'AbeeZee',
					'character_font_weight'   => 100,
					'character_font_size'     => 90,
					'character_margin_bottom' => '',
					'character_width'         => '',
					'icon_color'              => '#d6aa74',
					'icon_hover_color'        => '',
					'icon_mgb'                => '',
					'icon_box'                => '',
					'icon_box_position'       => 'top',
					'icon_box_width'          => 'large',
					'icon_box_custom'         => 80,
					'icon_box_style'          => 'circle',
					'icon_border_color'       => '',
					'icon_bg_color'           => '',
					'icon_hover_border_color' => '',
					'icon_hover_bg_color'     => '',
					'text_hover_color'        => '',
					'icon_size'               => 24,
					'graphic'                 => '',
					'image_radius'            => 0,
					'image_mgb'               => 20,
					'image_opacity'           => 1,
					'setting'                 => '',
					'border_hover_color'      => '',
					'bg_hover_color'          => '',
					'title_text'              => '',
					'font_family'             => '',
					'font_weight'             => '',
					'text_transform'          => 'none',
					'title_color'             => '',
					'title_spacing'           => 0,
					'font_size'               => 24,
					'heading_mgb'             => 15,
					'sep'                     => '',
					'sep_color'               => '#d6aa74',
					'services_custom_id'      => '',
				),
				$atts
			)
		);

		// Generate custom css
		$id = $services_custom_id;

		$css .= '#' . esc_attr( $id ) . ' h4 {
			font-size: ' . ( is_numeric( trim( $font_size ) ) ? trim( $font_size ) . 'px' : trim( $font_size ) ) . ';
			line-height: ' . ( is_numeric( trim( $font_size ) ) ? trim( $font_size * 1.5  ) . 'px' : trim( $font_size * 1.5 ) . 'px' ) . ';
			margin-bottom: ' . ( is_numeric( trim( $heading_mgb ) ) ? trim( $heading_mgb ) . 'px' : trim( $heading_mgb ) ) . ';
			text-transform: ' . esc_attr( $text_transform ) . ';
			letter-spacing: ' . esc_attr( $title_spacing ) . 'px;';

			if ( ! empty( $font_family ) ) {
				$css .= '
					color: ' . esc_attr( $title_color ) . ';
					font-family: "' . esc_attr( $font_family ) . '";
					font-weight: ' . esc_attr( $font_weight ) . ';
				';
			}
		$css .= '}';

		if ( ! empty( $text_color ) ) {
			$css .= '#' . esc_attr( $id ) . ' p { color: ' . esc_attr( $text_color ) . '; } ';
		}

		if ( ! empty( $text_hover_color ) ) {
			$css .= '
				#' . esc_attr( $id ) . ':hover * {
					color: ' . esc_attr( $text_hover_color ) . ' !important;
				}
			';
		}

		$css .= '#' . esc_attr( $id ) . ':hover {';
			if ( ! empty( $border_hover_color ) ) {
				$css .= 'border-color: ' . esc_attr( $border_hover_color ) . ' !important;';
			}

			if ( ! empty( $bg_hover_color ) ) {
				$css .= 'background: ' . esc_attr( $bg_hover_color ) . ' !important;';
			}
		$css .= '}';

		if ( 'character' == $style ) {
			$css .= '
				#' . esc_attr( $id ) . ' svg { ';
						if ( ! empty( $character_margin_bottom ) ) {
							$css .= 'margin-bottom: ' . ( is_numeric( trim( $character_margin_bottom ) ) ? trim( $character_margin_bottom ) . 'px' : trim( $character_margin_bottom ) ) . ';';
						} else {
							$css .= 'margin-bottom: -' . ( is_numeric( trim( $character_font_size / 2.5 ) ) ? trim( $character_font_size / 2.5 ) . 'px' : trim( $character_font_size / 2.5 ) ) . ';';
						}
						$css .= '
					height: ' . ( is_numeric( trim( $character_font_size ) ) ? trim( $character_font_size ) . 'px' : trim( $character_font_size ) ) . ';
					width: ' . ( is_numeric( trim( $character_width ) ) ? trim( $character_width ) . 'px' : trim( $character_width ) ) . ';
				}
				#' . esc_attr( $id ) . ' .character-text {
					font-size: ' . ( is_numeric( trim( $character_font_size ) ) ? trim( $character_font_size ) . 'px' : trim( $character_font_size ) ) . ';
					font-family: "' . esc_attr( $character_font_family ) . '";
					font-weight: ' . esc_attr( $character_font_weight ) . ';
				}
				#' . esc_attr( $id ) . '.character .top {
					stop-color: ' . esc_attr( $character_color ) . ';
					stop-opacity: 1;
				}
				#' . esc_attr( $id ) . '.character .bot {
					stop-color: ' . esc_attr( $character_color ) . ';
					stop-opacity: 0;
				}
				#' . esc_attr( $id ) . '.character.sep h4:before,
				#' . esc_attr( $id ) . '.character.sep.tc h4:after {
					background: ' . esc_attr( $sep_color ) . ';
				}
			';
		} elseif ( 'icon' == $style ) {
			$css .= '
				#' . esc_attr( $id ) . '.icon span {
					color: ' . esc_attr( $icon_color ) . ';
				}
				#' . esc_attr( $id ) . '.icon > span {
					font-size: ' . ( is_numeric( trim( $icon_size ) ) ? trim( $icon_size ) . 'px' : trim( $icon_size ) ) . ';
					margin-bottom: ' . ( is_numeric( trim( $icon_mgb ) ) ? trim( $icon_mgb ) . 'px' : trim( $icon_mgb ) ) . ';
					display: block;
				}
			';
			$css .= $icon_hover_color ? '#' . esc_attr( $id ) . '.icon:hover span { color: ' . esc_attr( $icon_hover_color ) . ';}' : '';

			if ( 'true' == $icon_box ) {
				$css .= '#' . esc_attr( $id ) . '.icon > div.icon-wrap {';
					$css .= $icon_border_color ? 'border: 1px solid ' . esc_attr( $icon_border_color ) . ';' : '';
					$css .= $icon_bg_color ? 'background: ' . esc_attr( $icon_bg_color ) . ';' : '';
					$css .= is_numeric( trim( $icon_size ) ) ? 'font-size: ' . trim( $icon_size ) . 'px;' : '';
				$css .= '}';

				$css .= '#' . esc_attr( $id ) . '.icon:hover > div.icon-wrap {';
					$css .= $icon_hover_border_color ? 'border-color: ' . esc_attr( $icon_hover_border_color ) . ';' : '';
					$css .= $icon_hover_bg_color ? 'background: ' . esc_attr( $icon_hover_bg_color ) . ';' : '';
				$css .= '}';

				if ( 'custom' == $icon_box_width ) {
					$css .= '
						#' . esc_attr( $id ) . '.icon > .icon-wrap.custom {
							height: ' . ( is_numeric( trim( $icon_box_custom ) ) ? trim( $icon_box_custom ) . 'px' : trim( $icon_box_custom ) ) . ';
							width: ' . ( is_numeric( trim( $icon_box_custom ) ) ? trim( $icon_box_custom ) . 'px' : trim( $icon_box_custom ) ) . ';
							line-height: ' . ( is_numeric( trim( $icon_box_custom ) ) ? trim( $icon_box_custom ) . 'px' : trim( $icon_box_custom ) ) . ';
						}
					';
				}
			}

			if ( 'true' == $sep ) {
				$css .= '
					#' . esc_attr( $id ) . '.icon .sep-icon span:before,
					#' . esc_attr( $id ) . '.icon .sep-icon:before,
					#' . esc_attr( $id ) . '.icon.tc .sep-icon:after {
						background: ' . esc_attr( $sep_color ) . ';
					}
				';
			}
		} else {
			$css .= '
				#' . esc_attr( $id ) . '.image img {
					margin-bottom: ' . ( is_numeric( trim( $image_mgb ) ) ? trim( $image_mgb ) . 'px' : trim( $image_mgb ) ) . ';
					border-radius: ' . ( is_numeric( trim( $image_radius ) ) ? trim( $image_radius ) . 'px' : trim( $image_radius ) ) . ';
					opacity: ' . esc_attr( $image_opacity ) . ';
				}
			';

			if ( 'true' == $sep ) {
				$css .= '
					#' . esc_attr( $id ) . '.image .sep-icon span:before,
					#' . esc_attr( $id ) . '.image .sep-icon:before,
					#' . esc_attr( $id ) . '.image.tc .sep-icon:after {
						background: ' . esc_attr( $sep_color ) . ';
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
		$html = $icon_html_start = $icon_html_end = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'style'                   => 'character',
					'align'                   => 'left',
					'content_box'             => '',
					'text_color'              => '#919191',
					'read_more'               => '',
					'read_more_link'          => '',
					'extra_class'             => '',
					'character_text'          => 1,
					'character_color'         => '#e2e2e2',
					'character_font_family'   => 'AbeeZee',
					'character_font_weight'   => 100,
					'character_font_size'     => 90,
					'character_margin_bottom' => '',
					'character_width'         => '',
					'icon_type'               => 'fontawesome',
					'icon_fontawesome'        => 'fa fa-adjust',
					'icon_openiconic'         => 'vc-oi vc-oi-dial',
					'icon_typicons'           => 'typcn typcn-adjust-brightness',
					'icon_entypo'             => 'entypo-icon entypo-icon-note',
					'icon_linecons'           => 'vc_li vc_li-heart',
					'icon_color'              => '#d6aa74',
					'icon_hover_color'        => '',
					'icon_box'                => '',
					'icon_box_position'       => 'top',
					'icon_box_width'          => 'large',
					'icon_box_custom'         => 80,
					'icon_box_style'          => 'circle',
					'icon_box_link'           => '',
					'icon_border_color'       => '',
					'icon_bg_color'           => '',
					'icon_hover_bg_color'     => '',
					'icon_size'               => 24,
					'graphic'                 => '',
					'image_radius'            => 0,
					'image_mgb'               => 20,
					'image_opacity'           => 1,
					'setting'                 => '',
					'border_hover_color'      => '',
					'bg_hover_color'          => '',
					'title_text'              => '',
					'font_family'             => 'Lato',
					'font_weight'             => '',
					'text_transform'          => '',
					'title_color'             => '#2d2d2d',
					'title_spacing'           => 0,
					'font_size'               => 24,
					'sep'                     => '',
					'sep_color'               => '#d6aa74',
					'services_custom_id'      => '',
				),
				$atts
			)
		);

		$classes = array( 'nitro-services' );

		// Get extra class
		if ( ! empty( $extra_class ) ) {
			$classes[] = $extra_class;
		}

		// Get separator alignment
		if ( 'left' == $align ) {
			$classes[] = 'tl';
		} elseif ( 'right' == $align ) {
			$classes[] = 'tr';
		} else {
			$classes[] = 'tc';
		}

		if ( $setting ) {
			$setting_class = vc_shortcode_custom_css_class( $setting, '' );
			$classes[]     = $setting_class;
		}

		// Get separator style
		if ( $style ) {
			$classes[] = $style;
		}

		if ( 'true' == $sep ) {
			$classes[] = 'sep';
		}

		// Generate HTML code.
		$id = $services_custom_id;

		$html .= '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . ' ts-03 pr">';

		if ( 'character' == $style ) {
			$html .= '<svg>';
				$html .= '<defs>';
					$html .= '<linearGradient y2="90%" x2="0%" y1="0%" x1="0%" id="grad-character">';
						$html .= '<stop class="top" offset="0%"/>';
						$html .= '<stop class="bot" offset="100%"/>';
					$html .= '</linearGradient>';
				$html .= '</defs>';
				$html .= '<text class="character-text" fill="url(#grad-character)" y=".8em">' . esc_html( trim( $character_text ) ) . '</text>';
			$html .= '</svg>';
			$html .= '<div class="content">';
				$html .= '<h4>' . wp_kses_post( $title_text ) . '</h4>';
				$html .= '<p class="ts-03">' . wp_kses_post( $content_box ) . '</p>';

				if ( 'true' == $read_more ) {
					$html .= '<a class="more" href="' . esc_url( $read_more_link ) . '">' . __( 'Read more.', 'nitro-toolkit' ) . '</a>';
				}

			$html .= '</div>';
		}

		elseif ( 'icon' == $style ) {
			// Enqueue needed icon font.
			vc_icon_element_fonts_enqueue( $icon_type );

			$icon_box_class = array();

			if ( 'true' == $icon_box ) {
				$icon_box_class[] = $icon_box_position;
				$icon_box_class[] = $icon_box_width;
				$icon_box_class[] = $icon_box_style;
				$icon_html_start  = '<div class="icon-wrap ' . esc_attr( implode( ' ', $icon_box_class ) ) . ' pr tc ts-03 mgb20">';
				$icon_html_end    = '</div>';
			}

			// Render class of icon
			$icon = isset( ${'icon_' . $icon_type} ) ? esc_attr( ${'icon_' . $icon_type} ) : 'fa fa-adjust';

			$html .= $icon_html_start;
				if ( ! empty( $icon_box_link ) ) {
					$html .= '<a href="' . esc_attr( $icon_box_link ) . '">';
				}
					$html .= '<span class="' . esc_attr( $icon ) . ' ts-03"></span>';
				if ( ! empty( $icon_box_link ) ) {
					$html .= '</a>';
				}
			$html .= $icon_html_end;

			$html .= '<div class="content ts-03">';
			$html .= '<h4>' . wp_kses_post( $title_text ) . '</h4>';

			if ( 'true' == $sep ) {
				$html .= '<span class="sep-icon pr"><span></span></span>';
			}

			$html .= '<p>' . wp_kses_post( $content_box ) . '</p>';

			if ( 'true' == $read_more ) {
				$html .= '<a class="more" href="' . esc_url( $read_more_link ) . '">' . __( 'Read more', 'nitro-toolkit' ) . '</a>';
			}

			$html .= '</div>';
		} else {
			if ( ! empty( $graphic ) ) {
				// Get image link and image data
				$image = wpb_getImageBySize( array( 'attach_id' => preg_replace( '/[^\d]/', '', $graphic ) ) );

				$html .= '<div class="image-icon"><img src="' . esc_url( $image['p_img_large'][0] ) . '" alt="' . esc_attr__( 'Separator', 'nitro-toolkit' ) . '" width="' . esc_attr( $image['p_img_large'][1] ) . '" height="' . esc_attr( $image['p_img_large'][2] ) . '" /></div>';
			}
			$html .= '<div class="content">';
			$html .= '<h4>' . wp_kses_post( $title_text ) . '</h4>';

			if ( 'true' == $sep ) {
				$html .= '<span class="sep-icon pr"><span></span></span>';
			}

			$html .= '<p class="ts-03">' . wp_kses_post( $content_box ) . '</p>';

			if ( 'true' == $read_more ) {
				$html .= '<a class="more" href="' . esc_url( $read_more_link ) . '">' . __( 'Read more', 'nitro-toolkit' ) . '</a>';
			}

			$html .= '</div>';
		}

		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_services', force_balance_tags( $html ) );
	}
}
