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
 * Nitro Social Icons shortcode.
 */
class Nitro_Toolkit_Shortcode_Social extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'social';

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_social_custom_css';

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
				'style'              => 'default',
				'size'               => 'small',
				'icon_color'         => '#d6aa74',
				'bg_color'           => '#d6aa74',
				'border_color'       => '#d6aa74',
				'icon_hover_color'   => '#646464',
				'bg_hover_color'     => '#d6aa74',
				'border_hover_color' => '#363636',
				'border_width'       => '1',
				'multicolor'         => '',
				'extra_class'        => '',
				'social_custom_id'   => '',
			), $atts )
		);

		// Generate custom ID
		$id = $social_custom_id;

		$css .= '
	#' . esc_attr( $id ) . '.social-bar a {';

		if ( $multicolor != 'yes' ) {
			$css .= '
		color: ' . esc_attr( $icon_color ) . ';';
		}

		if ( ( $style == 'solid_square' || $style == 'solid_circle' || $style == 'solid_rounded' ) && $multicolor != 'yes' ) {
			$css .= '
		background-color: ' . esc_attr( $bg_color ) . ';';
		}

		if ( $style == 'outline_square' || $style == 'outline_circle' || $style == 'outline_rounded' ) {
			$css .= '
		border-width: ' . esc_attr( $border_width ) . 'px;
		border-color: ' . esc_attr( $border_color ) . ';';
		}

		if ( $border_width > 1 && $size == "large" ) {
			$css .= '
		line-height: ' . ( 64 - esc_attr( $border_width ) ) . 'px;';
		}

		if ( $border_width > 1 && $size == "normal" ) {
			$css .= '
		line-height: ' . ( 44 - esc_attr( $border_width ) ) . 'px;';
		}

		if ( $border_width > 1 && $size == "small" ) {
			$css .= '
		line-height: ' . ( 32 - esc_attr( $border_width ) ) . 'px;';
		}

		$css .= '
	}';

		$css .= '
	#' . esc_attr( $id ) . '.social-bar a:hover {
		color: ' . esc_attr( $icon_hover_color ) . ';';

		if ( $style == 'solid_square' || $style == 'solid_circle' || $style == 'solid_rounded' ) {
			$css .= '
		background-color: ' . esc_attr( $bg_hover_color ) . '!important;';
		}

		if ( $style == 'outline_square' || $style == 'outline_circle' || $style == 'outline_rounded' ) {
			$css .= '
		border-color: ' . esc_attr( $border_hover_color ) . ';';
		}

		$css .= '
	}';

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
				'style'            => 'default',
				'size'             => 'small',
				'facebook'         => '',
				'twitter'          => '',
				'linkedin'         => '',
				'behance'          => '',
				'instagram'        => '',
				'gplus'            => '',
				'skype'            => '',
				'pinterest'        => '',
				'github'           => '',
				'foursquare'       => '',
				'dribbble'         => '',
				'youtube'          => '',
				'rss'              => '',
				'vk'               => '',
				'tumblr'           => '',
				'multicolor'       => '',
				'extra_class'      => '',
				'social_custom_id' => '',
			), $atts )
		);

		$classes = array();

		// Get extra class
		if ( ! empty( $extra_class ) ) {
			$classes[] = $extra_class;
		}
		// Get icon style
		if ( $style ) {
			$classes[] = $style;
		}
		// Get Multi colors
		if ( $multicolor ==  'yes' ) {
			$classes[] = 'multicolor';
		}
		// Get Icon size
		if ( $size ) {
			$classes[] = $size;
		}
		// Generate custom ID
		$id = $social_custom_id;

		$channels = array(
			'facebook'    => $facebook,
			'twitter'     => $twitter,
			'instagram'   => $instagram,
			'google-plus' => $gplus,
			'skype'       => $skype,
			'linkedin'    => $linkedin,
			'dribbble'    => $dribbble,
			'behance'     => $behance,
			'github'      => $github,
			'foursquare'  => $foursquare,
			'youtube'     => $youtube,
			'tumblr'      => $tumblr,
			'pinterest'   => $pinterest,
			'rss'         => $rss,
			'vk'          => $vk
		);

		$html .= '<div id="' . esc_attr( $id ) . '" class="social-bar ' . esc_attr( implode( ' ', $classes ) ) . '">';
			foreach ( $channels as $key => $value ) {
				if ( ! empty( $value ) ) {
					if ( 'skype' != $key ) {
						$html .='<a class="' . esc_attr( $key ) . ' dib pr tc mgr10 mgb10" href="' . $value . '"><i class="fa fa-' . esc_attr( $key ) . '"></i><span class="tooltip ab ts-03">' . str_replace( '-', ' ', esc_html( $key ) ) . '</span></a>';
					} else {
						$html .='<a class="' . esc_attr( $key ) . ' dib pr tc mgr10 mgb10" href="skype:' . $value . '?chat"><i class="fa fa-' . esc_attr( $key ) . '"></i><span class="tooltip ab ts-03">' . str_replace( '-', ' ', esc_html( $key ) ) . '</span></a>';
					}
				}
			}
		$html .= '</div>';

		return apply_filters( 'Nitro_Toolkit_Shortcode_Social', force_balance_tags( $html ) );
	}
}
