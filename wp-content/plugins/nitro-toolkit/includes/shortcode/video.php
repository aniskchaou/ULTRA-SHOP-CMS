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
class Nitro_Toolkit_Shortcode_Video extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'video';

	/**
	 * Enqueue scripts.
	 *
	 * @return  void
	 */
	public function enqueue_scripts() {
		if ( is_singular() ) {
			global $post;

			if ( has_shortcode( $post->post_content, "nitro_{$this->shortcode}" ) ) {
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
		$html = $id = $frame_style = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'source'          => 'youtube',
					'url'             => '',
					'style'           => 'screen',
					'icon_color'      => '#2d2d2d',
					'width'           => 500,
					'graphic'         => '',
					'align'           => '',
					'shadow'          => '',
					'popup'           => '',
					'control'         => '',
					'autoplay'        => '',
					'extra_class'     => '',
					'video_custom_id' => ''
				),
				$atts
			)
		);

		$classes = array( 'sc-video' );

		if ( $extra_class ) {
			$classes[] = $extra_class;
		}

		if ( $align ) {
			$classes[] = $align;
		}

		if ( ! empty( $shadow ) ) {
			$classes[] = 'shadow-' . $shadow;
		}

		if ( $style == 'screen' ) {
			$frame_style = 'style="max-width:' . esc_attr( $width ) . 'px";';
		}

		$html .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" ' . $frame_style . '>';

			if ( $source == 'youtube' ) {
				$url = explode( 'v=', $url );
				if ( isset( $url[1] ) ) {
					$url = explode( '&', $url[1] );

					$id = $url[0];

					if ( ! $id ) {
						$url = explode( '/', $url );
						$id = $url[ count( $url ) - 1 ];
					}
				}

				if ( $id ) {
					if ( $style == 'screen' ) {
						$html .= '<div class="fluid-iframe pr">';
							$html .= '<iframe class="pa" type="text/html" width="' . esc_attr( $width ) . '" height="' . intval( $width / 1.777 ) . '" src="https://www.youtube.com/embed/' . $id . '?rel=0' . ( $control ? '&controls=1' : '&controls=0' ) . ( $autoplay ? '&autoplay=1' : '' ) . '" frameborder="0"/>';
						$html .= '</div>';
					} elseif ( $style == 'icon' ) {
						$html .= '<a data-popup=\'{"control":"' . ( $control ? 'true' : 'false' ) . '"}\' class="sc-video-popup" href="https://www.youtube.com/watch?v=' . $id . '">';
							$html .= '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="64px" height="64px" viewBox="0 0 16 16">';
								$html .= '<path fill="' . esc_attr( $icon_color ) . '" d="M8 1c3.9 0 7 3.1 7 7s-3.1 7-7 7-7-3.1-7-7 3.1-7 7-7zM8 0c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8v0z"/>';
								$html .= '<path fill="' . esc_attr( $icon_color ) . '" d="M6 4v8l6-4z"/>';
							$html .= '</svg>';
						$html .= '</a>';
					} else {
						if ( ! empty( $graphic ) ) {
							$image_id = preg_replace( '/[^\d]/', '', $graphic );
							$image    = wp_get_attachment_image_src( $image_id, 'full' );

							$html .= '<div class="sc-video-thumb dib pr">';
								if ( $popup ) {
									$html .= '<a data-popup=\'{"control":"' . ( $control ? 'true' : 'false' ) . '"}\' class="sc-video-popup" href="https://www.youtube.com/watch?v=' . $id . '">';
										$html .= '<img class="ts-03" src="' . esc_url( $image[0] ) . '" width="' . esc_attr( $width ) . '" height="' . esc_attr( $image[2] ) . '" alt="Video Thumbnail" />';
										$html .= '<svg class="pa" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="64px" height="64px" viewBox="0 0 16 16">';
											$html .= '<path fill="' . esc_attr( $icon_color ) . '" d="M8 1c3.9 0 7 3.1 7 7s-3.1 7-7 7-7-3.1-7-7 3.1-7 7-7zM8 0c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8v0z"/>';
											$html .= '<path fill="' . esc_attr( $icon_color ) . '" d="M6 4v8l6-4z"/>';
										$html .= '</svg>';
									$html .= '</a>';
								} else {
									$html .= '<a class="sc-yt-trigger" href="#">';
										$html .= '<img src="' . esc_url( $image[0] ) . '" width="' . esc_attr( $width ) . '" height="' . esc_attr( $image[2] ) . '" alt="Video Thumbnail" />';
										$html .= '<svg class="pa" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="64px" height="64px" viewBox="0 0 16 16">';
											$html .= '<path fill="' . esc_attr( $icon_color ) . '" d="M8 1c3.9 0 7 3.1 7 7s-3.1 7-7 7-7-3.1-7-7 3.1-7 7-7zM8 0c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8v0z"/>';
											$html .= '<path fill="' . esc_attr( $icon_color ) . '" d="M6 4v8l6-4z"/>';
										$html .= '</svg>';
									$html .= '</a>';
									$html .= '<iframe class="pa" type="text/html" src="https://www.youtube.com/embed/' . $id . '?showinfo=0&enablejsapi=1' . ( $control ? '&controls=1' : '&controls=0' ) . '" frameborder="0"/>';
								}
							$html .= '</div>';
						}
					}
				}
			} elseif ( $source == 'vimeo' ) {
				if ( $url ) {
					$id = (int) substr( parse_url( $url, PHP_URL_PATH ), 1 );
				}
				if ( $id ) {
					if ( $style == 'screen' ) {
						$html .= '<div class="fluid-iframe pr">';
							$html .= '<iframe class="pa" src="//player.vimeo.com/video/' . $id . '?rel=0' . ( $autoplay ? '&autoplay=1' : '' ) . '" width="' . esc_attr( $width ) . '" height="' . intval( $width / 1.777 ) . '" frameborder="0"></iframe>';
						$html .= '</div>';
					} elseif ( $style == 'icon' ) {
						$html .= '<a data-popup=\'{"control":"' . ( $control ? 'true' : 'false' ) . '"}\' class="sc-video-popup" href="https://www.vimeo.com/' . $id . '">';
							$html .= '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="64px" height="64px" viewBox="0 0 16 16">';
								$html .= '<path fill="' . esc_attr( $icon_color ) . '" d="M8 1c3.9 0 7 3.1 7 7s-3.1 7-7 7-7-3.1-7-7 3.1-7 7-7zM8 0c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8v0z"/>';
								$html .= '<path fill="' . esc_attr( $icon_color ) . '" d="M6 4v8l6-4z"/>';
							$html .= '</svg>';
						$html .= '</a>';
					} else {
						if ( ! empty( $graphic ) ) {
							$image_id = preg_replace( '/[^\d]/', '', $graphic );
							$image    = wp_get_attachment_image_src( $image_id, 'full' );
							$html .= '<div class="sc-video-thumb dib pr">';
								if ( $popup ) {
									$html .= '<a data-popup=\'{"control":"' . ( $control ? 'true' : 'false' ) . '"}\' class="sc-video-popup" href="https://www.vimeo.com/' . $id . '">';
										$html .= '<img class="ts-03" src="' . esc_url( $image[0] ) . '" width="' . esc_attr( $width ) . '" height="' . esc_attr( $image[2] ) . '" alt="Video Thumbnail" />';
										$html .= '<svg class="pa" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="64px" height="64px" viewBox="0 0 16 16">';
											$html .= '<path fill="' . esc_attr( $icon_color ) . '" d="M8 1c3.9 0 7 3.1 7 7s-3.1 7-7 7-7-3.1-7-7 3.1-7 7-7zM8 0c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8v0z"/>';
											$html .= '<path fill="' . esc_attr( $icon_color ) . '" d="M6 4v8l6-4z"/>';
										$html .= '</svg>';
									$html .= '</a>';
								} else {
									$html .= '<a class="sc-vm-trigger" href="#">';
										$html .= '<img src="' . esc_url( $image[0] ) . '" width="' . esc_attr( $width ) . '" height="' . esc_attr( $image[2] ) . '" alt="Video Thumbnail" />';
										$html .= '<svg class="pa" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="64px" height="64px" viewBox="0 0 16 16">';
											$html .= '<path fill="' . esc_attr( $icon_color ) . '" d="M8 1c3.9 0 7 3.1 7 7s-3.1 7-7 7-7-3.1-7-7 3.1-7 7-7zM8 0c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8v0z"/>';
											$html .= '<path fill="' . esc_attr( $icon_color ) . '" d="M6 4v8l6-4z"/>';
										$html .= '</svg>';
									$html .= '</a>';
									$html .= '<iframe class="pa" type="text/html" src="//player.vimeo.com/video/' . $id . '"?api=1 frameborder="0" />';
									wp_enqueue_script( 'froogaloop' );
								}
							$html .= '</div>';
						}
					}
				}
			}

		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_video', force_balance_tags( $html ) );
	}
}
