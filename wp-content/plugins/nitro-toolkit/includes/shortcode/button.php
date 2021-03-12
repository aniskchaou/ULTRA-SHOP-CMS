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
 * Nitro Button shortcode.
 */
class Nitro_Toolkit_Shortcode_Button extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'button';

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
	protected $metakey = '_wr_shortcode_button_custom_css';

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
					'text'                => 'Button Text',
					'font_family'         => '',
					'link'                => '',
					'font_style'          => 'normal',
					'font_weight'         => '',
					'text_transform'      => 'none',
					'font_size'           => 14,
					'color'               => '#fff',
					'bg_color'            => '#3d3d3d',
					'border_width'        => '',
					'border_style'        => 'solid',
					'border_color'        => '#3d3d3d',
					'line_height'         => '40px',
					'padding'             => 15,
					'margin_top'          => '',
					'margin_right'        => '',
					'margin_bottom'       => '',
					'margin_left'         => '',
					'spacing'             => '',
					'border_radius'       => '',
					'alignment'           => '',
					'icon'                => '',
					'icon_type'           => '',
					'icon_fontawesome'    => '',
					'icon_openiconic'     => '',
					'icon_typicons'       => '',
					'icon_entypo'         => '',
					'icon_linecons'       => '',
					'icon_color'          => '#fff',
					'icon_position'       => 'left',
					'icon_size'           => '',
					'hover_line_height'   => '',
					'hover_padding'       => '',
					'hover_border_radius' => '',
					'hover_border_width'  => '',
					'hover_border_style'  => '',
					'hover_border_color'  => '',
					'hover_icon_color'    => '#fff',
					'hover_color'         => '#fff',
					'hover_bg_color'      => '#000',
					'extra_class'         => '',
					'button_custom_id'    => '',
				),
				$atts
			)
		);

		// Generate custom ID.
		$id = $button_custom_id;

		if ( ! empty( $text ) ) {
			// Normal state
			$css .= '#' . esc_attr( $id ) . ' a {
				display: inline-block;
				font-style: ' . esc_attr( $font_style ) . ';
				text-transform: ' . esc_attr( $text_transform ) . ';
				font-size: ' . ( is_numeric( trim( $font_size ) ) ? trim( $font_size ) . 'px' : trim( $font_size ) ) . ';
				color: ' . esc_attr( $color ) . ';';
				if ( ! empty( $font_family ) ) {
					$css .= '
						font-family: "' . esc_attr( $font_family ) . '";
						font-weight: ' . esc_attr( $font_weight ) . ';
					';
				}
				if ( ! empty( $bg_color ) ) {
					$css .= '
						background: ' . esc_attr( $bg_color ) . ';
					';
				}
				if ( ! empty( $border_width ) ) {
					$css .= '
						border: ' . ( is_numeric( trim( $border_width ) ) ? trim( $border_width ) . 'px ' : trim( $border_width ) ) . $border_style . ' ' . $border_color . ';
					';
				}
				if ( ! empty( $line_height ) ) {
					$css .= '
						line-height: ' . esc_attr( $line_height ) . ';
					';
				}
				if ( ! empty( $padding ) ) {
					$css .= '
						padding: 0 ' . ( is_numeric( trim( $padding ) ) ? trim( $padding ) . 'px' : trim( $padding ) ) . ';
					';
				}
				if ( ! empty( $spacing ) ) {
					$css .= '
						letter-spacing: ' . ( is_numeric( trim( $spacing ) ) ? trim( $spacing ) . 'px' : trim( $spacing ) ) . ';
					';
				}
				if ( ! empty( $border_radius ) ) {
					$css .= '
						border-radius: ' . ( is_numeric( trim( $border_radius ) ) ? trim( $border_radius ) . 'px' : trim( $border_radius ) ) . ';
					';
				}

				if ( ! empty( $margin_top ) ) {
					$css .= '
						margin-top: ' . ( is_numeric( trim( $margin_top ) ) ? trim( $margin_top ) . 'px' : trim( $margin_top ) ) . ';
					';
				}
				if ( ! empty( $margin_right ) ) {
					$css .= '
						margin-right: ' . ( is_numeric( trim( $margin_right ) ) ? trim( $margin_right ) . 'px' : trim( $margin_right ) ) . ';
					';
				}
				if ( ! empty( $margin_bottom ) ) {
					$css .= '
						margin-bottom: ' . ( is_numeric( trim( $margin_bottom ) ) ? trim( $margin_bottom ) . 'px' : trim( $margin_bottom ) ) . ';
					';
				}
				if ( ! empty( $margin_left ) ) {
					$css .= '
						margin-left: ' . ( is_numeric( trim( $margin_left ) ) ? trim( $margin_left ) . 'px' : trim( $margin_left ) ) . ';
					';
				}
			$css .= '}';

			// Hover state
			$css .= '#' . esc_attr( $id ) . ' a:hover {
				color: ' . esc_attr( $hover_color ) . ';
				background: ' . esc_attr( $hover_bg_color ) . ';';

				if ( ! empty( $hover_color ) ) {
					$css .= '
						color: ' . esc_attr( $hover_color ) . ';
					';
				}
				if ( ! empty( $hover_border_color ) ) {
					$css .= '
						border-color: ' . esc_attr( $hover_border_color ) . ';
					';
				}
				if ( ! empty( $hover_bg_color ) ) {
					$css .= '
						background: ' . esc_attr( $hover_bg_color ) . ';
					';
				}
			$css .= '}';

			// Hover icon color
			$css .= '#' . esc_attr( $id ) . ' a:hover i {';
				if ( ! empty( $hover_icon_color ) ) {
					$css .= '
						color: ' . esc_attr( $hover_icon_color ) . ';
					';
				}
			$css .= '}';

			// Icon
			if ( ! empty( $icon ) ) {
				$css .= '#' . esc_attr( $id ) . ' a i {
					color: ' . esc_attr( $icon_color ) . ';
					font-size: ' . ( is_numeric( trim( $icon_size ) ) ? trim( $icon_size ) . 'px' : trim( $icon_size ) ) . ';
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
		$html = $iconhtml = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'text'                => 'Button Text',
					'font_family'         => '',
					'link'                => '',
					'font_style'          => 'normal',
					'font_weight'         => '',
					'text_transform'      => 'none',
					'font_size'           => 14,
					'color'               => '#fff',
					'bg_color'            => '#3d3d3d',
					'border_width'        => '',
					'border_style'        => 'solid',
					'border_color'        => '#3d3d3d',
					'line_height'         => '40px',
					'padding'             => 15,
					'spacing'             => '',
					'border_radius'       => '',
					'alignment'           => '',
					'icon'                => '',
					'icon_type'           => 'fontawesome',
					'icon_fontawesome'    => 'fa fa-adjust',
					'icon_openiconic'     => 'vc-oi vc-oi-dial',
					'icon_typicons'       => 'typcn typcn-adjust-brightness',
					'icon_entypo'         => 'entypo-icon entypo-icon-note',
					'icon_linecons'       => 'vc_li vc_li-heart',
					'icon_color'          => '#fff',
					'icon_position'       => 'left',
					'icon_size'           => '',
					'hover_line_height'   => '',
					'hover_padding'       => '',
					'hover_border_radius' => '',
					'hover_border_width'  => '',
					'hover_border_style'  => '',
					'hover_border_color'  => '',
					'hover_color'         => '#fff',
					'hover_bg_color'      => '#000',
					'extra_class'         => '',
					'button_custom_id'    => '',
				),
				$atts
			)
		);

		$font_families = $attributes = array();

		// Generate custom ID.
		$id = $button_custom_id;

		// Custom css class
		$classes = array ( 'sc-button ' . $extra_class );

		// Button alignment
		if ( ! empty( $alignment ) ) {
			$classes[] = $alignment;
		}

		if ( ! empty( $icon ) ) {
			// Enqueue needed icon font.
			vc_icon_element_fonts_enqueue( $icon_type );

			// Render class of icon
			$iconClass = isset( ${'icon_' . $icon_type} ) ? esc_attr( ${'icon_' . $icon_type} ) : 'fa fa-adjust';
			$iconhtml .= '<i class="' . esc_attr( $iconClass ) . '"></i>';
			$classes[] = 'icon-' . $icon_position;
		}
		//parse link
		$link = ( '||' === $link ) ? '' : $link;
		$link = vc_build_link( $link );
		$use_link = false;
		if ( strlen( $link['url'] ) > 0 ) {
			$use_link = true;
			$a_href = $link['url'];
			$a_title = $link['title'];
			$a_target = strlen( $link['target'] ) > 0 ? $link['target'] : '_self';
		}

		if ( $use_link ) {
			$attributes[] = 'href="' . esc_url( trim( $a_href ) ) . '"';
			$attributes[] = 'title="' . esc_attr( trim( $a_title ) ) . '"';
			$attributes[] = 'target="' . esc_attr( trim( $a_target ) ) . '"';
			$attributes = implode( ' ', $attributes );
		}

		$html .= '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '">';
			$html .= '<a ' . $attributes . '>';
				if ( ! empty( $icon ) && 'left' == $icon_position ) {
					$html .= $iconhtml;
				}
				$html .= '<span>' . wp_kses_post( $text ) . '</span>';
				if ( ! empty( $icon ) && 'right' == $icon_position ) {
					$html .= $iconhtml;
				}
			$html .= '</a>';
		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_button', force_balance_tags( $html ) );
	}
}
