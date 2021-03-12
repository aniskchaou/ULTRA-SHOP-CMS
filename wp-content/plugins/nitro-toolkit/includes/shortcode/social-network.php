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
class Nitro_Toolkit_Shortcode_Social_Network extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'social_network';

	/**
	 * Enqueue custom scripts / stylesheets.
	 *
	 * @return  void
	 */
	public function enqueue_scripts() {
		if ( is_singular() ) {
			global $post;

			if ( has_shortcode( $post->post_content, "nitro_{$this->shortcode}" ) ) {
				// Enqueue required assets.
				wp_enqueue_style(  'owl-carousel' );
				wp_enqueue_script( 'owl-carousel' );
				wp_enqueue_script( 'isotope'      );
			}
		}

		// Let parent class load default scripts.
		parent::enqueue_scripts();
	}

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_social_network_custom_css';

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
					'network'           => 'dribbble',
					'user_id'           => '',
					'layout'            => 'grid',
					'item_large'        => '',
					'columns'           => 4,
					'limit'             => 10,
					'gutter'            => '',
					'slider'            => '',
					'item'              => 4,
					'navigation'        => '',
					'pagination'        => '',
					'autoplay'          => '',
					'enable_info'       => '',
					'info_style'        => 'inside',
					'mask_bg'           => 'rgba(0, 0, 0, .85)',
					'text_color'        => '',
					'social_network_custom_id' => '',
				),
				$atts
			)
		);

		// Generate custom ID.
		$id = $social_network_custom_id;

		if ( ! empty( $gutter ) ) {
			$css .= '#' . esc_attr( $id ) . ' {';
				$css .= 'margin: 0 -' . esc_attr( $gutter / 2 ) . 'px;';
			$css .= '}';

			$css .= '#' . esc_attr( $id ) . ' .item {';
				$css .= 'padding: ' . esc_attr( $gutter / 2 ) . 'px;';
			$css .= '}';

			$css .= '#' . esc_attr( $id ) . '.wr-nitro-carousel {';
				$css .= 'width: calc(100% + ' . esc_attr( $gutter ) . 'px);';
			$css .= '}';

			$css .= '#' . esc_attr( $id ) . ' .owl-item {';
				$css .= 'padding-right: ' . esc_attr( $gutter ) . 'px;';
			$css .= '}';

			$css .= '#' . esc_attr( $id ) . ':not(.wr-nitro-carousel) .info {';
				$css .= 'top: ' . esc_attr( $gutter / 2 ) . 'px;';
				$css .= 'right: ' . esc_attr( $gutter / 2 ) . 'px;';
				$css .= 'bottom: ' . esc_attr( $gutter / 2 ) . 'px;';
				$css .= 'left: ' . esc_attr( $gutter / 2 ) . 'px;';
			$css .= '}';
		}

	if ( $enable_info && 'inside' == $info_style ) {
		if ( ! empty( $mask_bg ) ) {
			$css .= '
			#' . esc_attr( $id ) . ' .info {
				background: ' . esc_attr( $mask_bg ) . ';
			}';
		}

		if ( ! empty( $text_color ) ) {
			$css .= '
			#' . esc_attr( $id ) . ' a {
				color: ' . esc_attr( $text_color ) . ';
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
		$html = $data = $data_layout = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'network'           => 'dribbble',
					'user_id'           => '',
					'access_token'      => '',
					'layout'            => 'grid',
					'item_large'        => '',
					'columns'           => 4,
					'limit'             => 10,
					'gutter'            => '',
					'slider'            => '',
					'item'              => 4,
					'navigation'        => '',
					'pagination'        => '',
					'autoplay'          => '',
					'enable_info'       => '',
					'info_style'        => 'inside',
					'mask_bg'           => 'rgba(0, 0, 0, .85)',
					'text_color'        => '',
					'social_network_custom_id' => '',
				),
				$atts
			)
		);

		// Custom css class
		$classes = array( 'sc-social-network clear' );

		// Generate custom ID.
		$id = $social_network_custom_id;

		// Get columns
		if ( ! empty( $columns ) && '0' == $slider ) {
			$classes[] = 'columns-' . $columns;
		}

		if ( ! empty( $network ) ) {
			$classes[] = $network;
		}

		if ( '1' == $slider ) {
			$wr_nitro_options = WR_Nitro::get_options();

			if ( ! empty( $item ) ) {
				$attr[] = '"items": "' . ( int ) $item . '"';
			}
			if ( ! empty( $autoplay ) ) {
				$attr[] = '"autoplay": "true"';
			}

			if ( ! empty( $attr ) ) {
				$data = 'data-owl-options=\'{' . esc_attr( implode( ', ', $attr ) ) . ',"tablet":"2","mobile":"1"' . ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ) . '}\'';
			}

			$classes[] = 'wr-nitro-carousel';
		}

		if ( 'masonry' == $layout ) {
			$data_layout = 'data-masonry=\'{"selector":".item", "columnWidth":".grid-sizer"}\'';
			$classes[]   = 'wr-nitro-masonry';
		}

		if ( '1' == $slider ) {
			$html .= '<div class="slider-outer oh">';
		}
		$html .= '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '" ' . $data . $data_layout . '>';

			if ( ! empty( $user_id ) ) {
				if ( '0' == $slider && 'masonry' == $layout ) {
					$html .= '<div class="grid-sizer"></div>';
				}

				if ( 'dribbble' == $network ) {
					// Get Dribbbler
					$user = 'https://api.dribbble.com/v1/users/' . esc_attr( $user_id ) . '?access_token=1792c5add5b9abe0baf1bfc492cfd5236cb18ea7c8d8cc31fb9fcdde7bbe0276';

					$getusers    = wp_remote_get( $user );
					$json_decode = json_decode( $getusers['body'], true );

					// Get Dribbble shot
					if ( isset( $json_decode ) ) {
						$shot    = $json_decode['shots_url'] . '?per_page=' . esc_attr( $limit ) . '&access_token=1792c5add5b9abe0baf1bfc492cfd5236cb18ea7c8d8cc31fb9fcdde7bbe0276';
						$getshot =  wp_remote_get( $shot );

						if ( ! is_wp_error( $getshot ) && isset( $getshot['body'] ) && $getshot['body'] )
							$shots = json_decode( $getshot['body'], true );
					}

					// Get large item
					$large = array_map( 'trim', explode( ',', $item_large ) );
					$i = 0;

					if ( isset( $shots ) && $shots ) {
						foreach ( $shots as $key => $value ) {
							$i++;

							$html .= '<div class="item fl pr ' . ( in_array( $i , $large ) ? ' large' : NULL ) . '">';
								if ( isset( $value['images']['hidpi'] ) ) {
									$html .= '<img src="' . esc_url( $value['images']['hidpi'] ) . '" width="800" height="600" alt="' . esc_attr( $value['title'] ) . '">';
								} else {
									$html .= '<img src="' . esc_url( $value['images']['normal'] ) . '" width="400" height="300" alt="' . esc_attr( $value['title'] ) . '">';
								}
								if ( $enable_info ) {
									$html .= '<div class="info pa tc fc aic jcc fcc ts-03 ' . esc_attr( $info_style ) . '">';
										$html .= '<h4><a target="_blank" rel="noopener noreferrer" href="' . esc_url( $value['html_url'] ) . '">' . $value['title'] . '</a></h4>';
										$html .= '<div class="count">';
											$html .= '<span class="like mgr10"><i class="fa fa-heart-o"></i>' . $value['likes_count']  . '</span>';
											$html .= '<span class="comment"><i class="fa fa-comments-o"></i>' . $value['comments_count']  . '</span>';
										$html .= '</div>';
									$html .= '</div>';
								}
							$html .= '</div>';
						}
					}
				} elseif ( 'flickr' == $network ) {
					$api = 'https://api.flickr.com/services/rest/?&method=flickr.people.getPublicPhotos&api_key=ecca58f2187c0fead2016f67970999b2&user_id=' . esc_attr( $user_id ) . '&per_page=' . esc_attr( $limit ) . '&format=php_serial&jsoncallback=?';
					$getphoto = wp_remote_get( $api );
					$photos   = unserialize( $getphoto['body'] );

					// Get large item
					$large = array_map( 'trim', explode( ',', $item_large ) );
					$i = 0;

					if ( isset( $photos['photos']['photo'] ) && $photos['photos']['photo'] ) {
						foreach ( $photos['photos']['photo'] as $key => $photo ) {
							$i++;

							$link = 'http://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg';

							$html .= '<div class="item fl' . ( in_array( $i , $large ) ? ' large' : NULL ) . '">';
								$html .= '<a href="https://www.flickr.com/photos/' . $photo['owner'] . '/' . $photo['id'] . '/" target="_blank" rel="noopener noreferrer"><img src="' . esc_url( $link ) . '" alt="' . $photo['title'] . '" /></a>';
							 $html .= '</div>';
						}
					}
				} elseif ( 'instagram' == $network ) {
					$api = 'https://api.instagram.com/v1/users/' . esc_attr( $user_id ) . '/media/recent/?access_token=' . esc_attr( $access_token ) . '&count=' . esc_attr( $limit );

					$getphoto = wp_remote_get( esc_url_raw( $api ) );
					$photos = json_decode( $getphoto['body'] );

					if ( $photos->meta->code !== 200 ) {
						return '<p>User ID and access token do not match. Please check again.</p>';
					}

					// Get large item
					$large = array_map( 'trim', explode( ',', $item_large ) );
					$i = 0;

					$items_as_objects = $photos->data;
					$items = array();
					foreach ( $items_as_objects as $item_object ) {
						$items[] = array(
							'link'     => $item_object->link,
							'src'      => $item_object->images->standard_resolution->url,
							'comments' => $item_object->comments->count,
							'like'     => $item_object->likes->count
						 );
					}

					foreach ( $items as $item ) {
						$i++;
						$link     = $item['link'];
						$image    = $item['src'];
						$comments = $item['comments'];
						$like     = $item['like'];

						$html .= '<div class="item fl pr' . ( in_array( $i , $large ) ? ' large' : NULL ) . '">';
							$html .= '<a target="_blank" rel="noopener noreferrer" href="' . esc_url( $link ) .'"><img width="640" height="640" src="' . esc_url( $image ) . '" alt="Instagram" /></a>';
							if ( $enable_info ) {
								$html .= '<div class="info instagram pa tc fc aic jcc fcc ts-03 ' . esc_attr( $info_style ) . '">';
									$html .= '<div class="count">';
										$html .= '<span class="like mgr10"><i class="fa fa-heart-o"></i>' . $like  . '</span>';
										$html .= '<span class="comment"><i class="fa fa-comments-o"></i>' . $comments  . '</span>';
									$html .= '</div>';
								$html .= '</div>';
							}
						$html .= '</div>';
					}
				}
			}
		$html .= '</div>';
		if ( '1' == $slider ) {
			$html .= '</div>';
		}

		return apply_filters( 'nitro_toolkit_shortcode_social_network', force_balance_tags( $html ) );
	}
}
