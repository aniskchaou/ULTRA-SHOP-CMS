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
 * Nitro Lists shortcode.
 */
class Nitro_Toolkit_Shortcode_Lists extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'lists';

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_lists_custom_css';

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
				'style'          => 'none',
				'icon_color'     => '#363636',
				'divider'        => '',
				'border'         => '',
				'divider_color'  => '#c5c5c5',
				'border_color'   => '#363636',
				'bg_color'       => '#fff',
				'line_height'    => '20',
				'divider_width'  => '1',
				'border_width'   => '1',
				'divider_style'  => 'solid',
				'border_style'   => 'solid',
				'border_radius'  => '0',
				'lists_custom_id' => '',
			), $atts )
		);

		// Generate custom ID
		$id = $lists_custom_id;

		$css .= '
	#' . esc_attr( $id ) . '.nitro-list ul li {
		padding: ' . ( is_numeric( trim( $line_height / 2 ) ) ? trim( $line_height / 2 ) . 'px' : trim( $line_height / 2 ) ) . ' 0;';

		if ( $divider ) {
			$css .= '
		border-bottom-width: ' . ( ( $divider_width ) ? $divider_width . 'px' : '1px' ) . ';
		border-bottom-style: ' . ( ( $divider_style ) ? $divider_style : 'solid' ) . ';
		border-bottom-color: ' . ( ( $divider_color ) ? $divider_color : '#363636' ) . ' !important;';
		}

		$css .= '
	}';

	if ( $border ) {
		$css .= '
	#' . esc_attr( $id ) . '.nitro-list ul li span {
		width: 25px;
		height: 25px;
		line-height: 27px;
		font-size: 16px;
		background-color: ' . ( ( $bg_color ) ? $bg_color : '#fff' ) . ';
		border-width: ' . ( ( $border_width ) ? $border_width . 'px' : '1px' ) . ';
		border-style: ' . ( ( $border_style ) ? $border_style : 'solid' ) . ';
		border-color: ' . ( ( $border_color ) ? $border_color : '#363636' ) . ';
		border-radius: ' . ( ( $border_radius ) ? $border_radius . 'px' : '0' ) . ';
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
		extract( shortcode_atts(
			array(
				'style'             => 'none',
				'icon_fontawesome'  => '',
				'list_item'         => '',
				'link'              => '',
				'icon_color'        => '#363636',
				'list_custom_class' => '',
				'lists_custom_id'    => '',
				'list_content'      => '',
			), $atts )
		);

		$classes = array();

		// Get extra class
		if ( ! empty( $list_custom_class ) ) {
			$classes[] = $list_custom_class;
		}
		// Get icon style
		if ( $style ) {
			$classes[] = $style;
		}

		// Generate custom ID
		$id = $lists_custom_id;

		$values = ( array ) vc_param_group_parse_atts( $list_content );

		$html .= '<div id="' . esc_attr( $id ) . '" class="nitro-list ' . esc_attr( implode( ' ', $classes ) ) . '">';
		$html .= '<ul class="db">';

		foreach ( $values as $value ) {
			// Parse link
			$link = isset( $value['link'] ) ? ( ( '||' === $value['link'] ) ? '' : $value['link'] ) : '';

			$link = vc_build_link( $link );

			$use_link = false;
			if ( strlen( $link['url'] ) > 0 ) {
				$use_link = true;
				$a_href = $link['url'];
				$a_title = $link['title'];
				$a_target = strlen( $link['target'] ) > 0 ? $link['target'] : '_self';
			}
			$attributes = array();
			if ( $use_link ) {
				$attributes[] = 'href="' . esc_url( trim( $a_href ) ) . '"';
				$attributes[] = 'title="' . esc_attr( trim( $a_title ) ) . '"';
				$attributes[] = 'target="' . esc_attr( trim( $a_target ) ) . '"';
				$attributes = implode( ' ', $attributes );
			}

			if ( ! empty( $value[ 'list_item' ] ) ) {
				$html .= '<li class="pr">';
				if ( 'icon-list' == $style ) {
					$html .= '<span class="dib tc mgr10"><i class="tc ' . esc_attr( $value[ 'icon_fontawesome' ] ) . '" style="color: ' . esc_attr( $value[ 'icon_color' ] ) . ';"></i></span>';
				} elseif ( 'number-list' == $style ) {
					$html .= '<span class="dib tc color-white" style="background-color: ' . esc_attr( $value[ 'icon_color' ] ) . ';"></span>';
				}
				if ( $use_link ) {
					$html .= '<a ' . $attributes . '> ' . esc_html( $value[ 'list_item' ] ) . '</a>';
				} else {
					$html .= $value[ 'list_item' ];
				}
				$html .= '</li>';
			}
		}

		$html .= '</ul>';
		$html .= '</div>';

		return apply_filters( 'Nitro_Toolkit_Shortcode_Lists', force_balance_tags( $html ) );
	}
}