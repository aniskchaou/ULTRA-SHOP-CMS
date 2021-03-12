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
 * Nitro Banner shortcode.
 */
class Nitro_Toolkit_Shortcode_Banner extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'banner';

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_banner_custom_css';

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
					'image'                 => '',
					'image_alignment'       => 'tl',
					'image_content'         => 'content_image',
					'image_inner'           => '',
					'text_heading'          => '',
					'text_heading_size'     => '',
					'text_description'      => '',
					'text_description_size' => '',
					'text_color'            => '#fff',
					'mask_color'            => 'rgba(0,0,0,.5)',
					'hover_effects'         => 'style-1',
					'link'                  => '',
					'extra_class'           => '',
					'banner_custom_id'      => ''
				),
				$atts
			)
		);

		if ( ! empty( $image ) ) {
			$img_id   = preg_replace( '/[^\d]/', '', $image );
			$img_data = wp_get_attachment_image_src( $img_id, 'full' );
		}
		if ( ! empty( $image_inner ) ) {
			$img_inner_id    = preg_replace( '/[^\d]/', '', $image_inner );
			$img_inner_data  = wp_get_attachment_image_src( $img_inner_id, 'full' );
		}

		// Generate custom ID.
		$id = $banner_custom_id;

		if ( 'style-12' == $hover_effects && 'content_image' == $image_content && ! ( empty( $image ) || empty( $image_inner ) ) ) {
			$css .= '
				#' . esc_attr( $id ) . '.image-banner.image.style-12 {
					background: rgba(66, 66, 66, 0.5) url(' . esc_url( $img_data[0] ) . ') repeat 50% 100%;
					height: ' . esc_attr( $img_data[1] ). 'px;
					max-width: ' . esc_attr( $img_data[2] ). 'px;
					width: 100%;
					z-index: 5;
				}
				#' . esc_attr( $id ) . '.image-banner.image.style-12 .banner-inner {
					background: rgba(66, 66, 66, 0.5) url(' . esc_url( $img_inner_data[0] ) . ') repeat fixed 50% 100%;
					display: inline-block;
					height: 100%;
					width: ' . esc_attr( floor( $img_inner_data[1] / 20 ) ) . 'px;
					z-index: 4;
					padding: 2px;
					-webkit-transition: all 1.3s;
					-moz-transition: all 1.3s;
					transition: all 1.3s;
				}
				#' . esc_attr( $id ) . '.image-banner.image.style-12 .banner-inner:hover {
					opacity: 0;
					-webkit-transition: all 0s linear;
					-moz-transition: all 0s linear;
					transition: all 0s linear;
				}
			';
		}

		if ( 'style-13' == $hover_effects && ! empty( $mask_color ) ) {
			$css .= '
				#' . esc_attr( $id ) . '.image-banner.style-13 .content .inner-content {
					background: ' . esc_attr( $mask_color ) . ';
				}
			';
		}

		if ( ! empty( $text_color ) && 'content_text' == $image_content ) {
			$css .= '
				#' . esc_attr( $id ) . '.image-banner.text .inner-content > * {
					color: ' . esc_attr( $text_color ) . ';
				}
			';
			if ( 'style-1' == $hover_effects ) {
				$css .= '
					#' . esc_attr( $id ) . '.image-banner.text.style-1 .content h2:after {
						background: ' . esc_attr( $text_color ) . ';
					}
				';
			}
			if ( 'style-4' == $hover_effects ) {
				$css .= '
					#' . esc_attr( $id ) . '.image-banner.text.style-4 .content p {
						border-color: ' . esc_attr( $text_color ) . ';
					}
				';
			}
			if ( 'style-5' == $hover_effects ) {
				$css .= '
					#' . esc_attr( $id ) . '.image-banner.text.style-5 .content:before {
						border-bottom-color: ' . esc_attr( $text_color ) . ';
						border-top-color: ' . esc_attr( $text_color ) . ';
					}
					#' . esc_attr( $id ) . '.image-banner.text.style-5 .content:after {
						border-left-color: ' . esc_attr( $text_color ) . ';
						border-right-color: ' . esc_attr( $text_color ) . ';
					}
				';
			}
			if ( 'style-6' == $hover_effects ) {
				$css .= '
					#' . esc_attr( $id ) . '.image-banner.text.style-6 .content:before,
					#' . esc_attr( $id ) . '.image-banner.text.style-6 .content:after {
						background: ' . esc_attr( $text_color ) . ';
					}
				';
			}
			if ( 'style-7' == $hover_effects ) {
				$css .= '
					#' . esc_attr( $id ) . '.image-banner.text.style-7 .content h2:after {
						background: ' . esc_attr( $text_color ) . ';
					}
				';
			}
			if ( 'style-11' == $hover_effects ) {
				$css .= '
					#' . esc_attr( $id ) . '.image-banner.text.style-11 .content h2:before,
					#' . esc_attr( $id ) . '.image-banner.text.style-11 .content h2:after {
						background: ' . esc_attr( $text_color ) . ';
					}
				';
			}
			if ( 'style-12' == $hover_effects ) {
				$css .= '
					#' . esc_attr( $id ) . '.image-banner.text.style-12 .content h2:before,
					#' . esc_attr( $id ) . '.image-banner.text.style-12 .content h2:after {
						color: ' . esc_attr( $text_color ) . ';
					}
					#' . esc_attr( $id ) . '.image-banner.text.style-12 .content:before {
						background: ' . esc_attr( $text_color ) . ';
					}
				';
			}

			if ( ! empty( $text_heading_size ) && 'content_text' == $image_content ) {
				$css .= '
					#' . esc_attr( $id ) . '.image-banner.text .content h2 {
						font-size: ' . esc_attr( $text_heading_size ) . 'px;
					}
				';
			}

			if ( ! empty( $text_description_size ) && 'content_text' == $image_content ) {
				$css .= '
					#' . esc_attr( $id ) . '.image-banner.text .content p {
						font-size: ' . esc_attr( $text_description_size ) . 'px;
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
		$html = $img = $img_inner = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'image'                 => '',
					'image_alignment'       => 'tl',
					'image_content'         => 'content_image',
					'image_inner'           => '',
					'text_heading'          => '',
					'text_heading_size'     => '',
					'text_description'      => '',
					'text_description_size' => '',
					'text_color'            => '#fff',
					'hover_effects'         => 'style-1',
					'link'                  => '',
					'extra_class'           => '',
					'banner_custom_id'      => ''
				),
				$atts
			)
		);

		// Get image link and image data
		if ( ! empty( $image ) ) {
			$img_id   = preg_replace( '/[^\d]/', '', $image );
			$img_data = wp_get_attachment_image_src( $img_id, 'full' );

			$img = '<img class="front ts-04 cxs-12 cs-12" src="' . esc_url( $img_data[0] ) . '" width="' . esc_attr( $img_data[1] ) . '"  height="' . esc_attr( $img_data[2] ) . '" alt="' . esc_attr( 'Front Image', 'nitro' ) . '" />';
		}
		if ( ! empty( $image_inner ) ) {
			$img_inner_id    = preg_replace( '/[^\d]/', '', $image_inner );
			$img_inner_data  = wp_get_attachment_image_src( $img_inner_id, 'full' );

			$img_inner = '<img class="back ts-04" src="' . esc_url( $img_inner_data[0] ) . '" width="' . esc_attr( $img_inner_data[1] ) . '"  height="' . esc_attr( $img_inner_data[2] ) . '" alt="' . esc_attr( 'Back Image', 'nitro' ) . '" />';
		}

		// Generate custom ID.
		$id = $banner_custom_id;

		$classes = array( 'image-banner' );

		if ( ! empty( $extra_class ) ) {
			$classes[] = $extra_class;
		}

		// Image container alignment
		if ( ! empty( $image_alignment ) ) {
			$classes[] = $image_alignment;
		}

		// Image container alignment
		if ( 'content_image' == $image_content ) {
			$classes[] = 'image';
		} else {
			$classes[] = 'text';
		}

		// Hover effects
		if ( ! empty( $hover_effects ) ) {
			$classes[] = $hover_effects;
		}

		// Generate HTML code.
		if ( 'content_image' == $image_content ) {
			if ( 'style-12' == $hover_effects ) {
				$html .= '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '">';
					for ( $i = 1; $i <= floor( $img_data[1] / 20 ); $i++ ) {
						$html .= '<div class="banner-inner"></div>';
					}
				$html .= '</div>';
			} else {
				$html .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
					$html .= '<div class="pr oh cs-12 cxs-12">';
						$html .= '<a href="' . esc_url( $link ) . '" class="dt oh pr cxs-12 cs-12">';
							$html .= $img;
							$html .= $img_inner;
						$html .= '</a>';
					$html .= '</div>';
				$html .= '</div>';
			}
		} else {
			$html .= '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '">';
				$html .= '<div class="pr oh cs-12 cxs-12">';
					$html .= '<a href="' . esc_url( $link ) . '" class="dt oh pr cxs-12 cs-12">';
						$html .= $img;
					$html .= '</a>';
					$html .= '<div class="content">';
						$html .= '<div class="inner-content">';
							if ( 'style-10' == $hover_effects || 'style-12' == $hover_effects ) {
								$html .= '<h2 data-content="' . esc_html( $text_heading ) . '">' . esc_html( $text_heading ) . '</h2>';
							} else {
								$html .= '<h2>' . wp_kses_post( $text_heading ) . '</h2>';
							}
							if ( 'style-10' != $hover_effects || 'style-11' != $hover_effects || 'style-12' != $hover_effects  ) {
								$html .= '<p>' . wp_kses_post( $text_description ) . '</p>';
							}
						$html .= '</div>';
					$html .= '</div>';
				$html .= '</div>';
			$html .= '</div>';
		}

		return apply_filters( 'nitro_toolkit_shortcode_banner', force_balance_tags( $html ) );
	}
}
