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
 * Nitro Member shortcode.
 */
class Nitro_Toolkit_Shortcode_Member extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'member';

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_member_custom_css';

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
					'avatar'           => '',
					'member_custom_id' => '',
				),
				$atts
			)
		);

		// Get image link and image data
		if ( ! empty( $avatar ) ) {
			// Generate custom ID.
		$id = $member_custom_id;

		$css .= '
	#' . $id . ' {
		width: ' . $image_width . 'px;
	}';

		return $css;
	}
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
					'style'            => 'style-1',
					'avatar'           => '',
					'avatar_large'     => '',
					'name'             => '',
					'position'         => '',
					'bio'              => '',
					'facebook'         => '',
					'twitter'          => '',
					'dribbble'         => '',
					'behance'          => '',
					'linkedin'         => '',
					'tumblr'           => '',
					'pinterest'        => '',
					'googleplus'       => '',
					'instagram'        => '',
					'skype'            => '',
					'columns'          => 4,
					's3_member'        => '',
					'extra_class'      => '',
					'member_custom_id' => '',
				),
				$atts
			)
		);

		// Prepare class.
		$classes = array( 'nitro-member ' . $extra_class );

		// Get member style
		if ( $style ) {
			$classes[] = $style;
		}

		// Generate HTML code.
		$id = $member_custom_id;

		$channels = array(
			'facebook'    => $facebook,
			'twitter'     => $twitter,
			'linkedin'    => $linkedin,
			'dribbble'    => $dribbble,
			'behance'     => $behance,
			'pinterest'   => $pinterest,
			'tumblr'      => $tumblr,
			'google-plus' => $googleplus,
			'instagram'   => $instagram,
			'skype'       => $skype,
		);

		if ( 'style-1' == $style ) {
			$html .= '<div id="' . esc_attr( $id ) . '" class="tc oh ' . esc_attr( implode( ' ', $classes ) ) . '">';
				// Get image link and image data
				if ( ! empty( $avatar ) ) {
					$img_id = preg_replace( '/[^\d]/', '', $avatar );
					$image  = wpb_getImageBySize( array( 'attach_id' => $img_id ) );

					$html .= '<div class="avatar pr">';
						$html .= '<img src="' . esc_url( $image['p_img_large'][0] ) . '" alt="' . esc_attr( isset( $name ) ? $name : '' ) . '" width="' . esc_attr( $image['p_img_large'][1] ) . '" height="' . esc_attr( $image['p_img_large'][2] ) . '" />';
					$html .= '</div>';
				}

				$html .= '<div class="info pr">';
					$html .= '<div class="ts-03">';
						$html .= '<h4 class="ts-03 mg0 color-primary">' . esc_html( $name ) . '</h4>';
						$html .= '<span class="ts-03 db">' . esc_html( $position ) . '</span>';
					$html .= '</div>';

				$html .= '</div>';
				$html .= '<div class="social db">';

				foreach ( $channels as $key => $value ) {
					if ( ! empty( $value ) ) {
						if ( $key == 'skype' ) {
							$html .= '<a class="' . esc_attr( $key ) . ' dib bts-40 nitro-line btb pr" href="skype:' . esc_attr( $value ) . '?chat"><i class="fa fa-' . esc_attr( $key ) . '"></a></a>';
						} else {
							$html .= '<a class="' . esc_attr( $key ) . ' dib bts-40 nitro-line btb pr" href="' . esc_url( $value ) . '" target="_blank" rel="noopener noreferrer"><i class="fa fa-' . esc_attr( $key ) . '"></a></a>';
						}
					}
				}

				$html .= '</div>';
			$html .= '</div>';
		} elseif ( 'style-2' == $style) {
			$html .= '<div id="' . esc_attr( $id ) . '" class="oh pr ' . esc_attr( implode( ' ', $classes ) ) . '">';
				// Get image link and image data
				if ( ! empty( $avatar ) ) {
					$img_id = preg_replace( '/[^\d]/', '', $avatar );
					$image  = wpb_getImageBySize( array( 'attach_id' => $img_id ) );

					$html .= '<div class="avatar">';
						$html .= '<img src="' . esc_url( $image['p_img_large'][0] ) . '" alt="' . esc_attr( isset( $name ) ? $name : '' ) . '" width="' . esc_attr( $image['p_img_large'][1] ) . '" height="' . esc_attr( $image['p_img_large'][2] ) . '" />';
					$html .= '</div>';
				}

				$html .= '<div class="info pa">';
					$html .= '<div>';
						$html .= '<h4>' . esc_html( $name ) . '</h4>';
						$html .= '<span>' . esc_html( $position ) . '</span>';
					$html .= '</div>';
					$html .= '<p>' . $bio . '</p>';
				$html .= '</div>';
				$html .= '<div class="social">';

				foreach ( $channels as $key => $value ) {
					if ( ! empty( $value ) ) {
						if ( $key == 'skype' ) {
							$html .= '<a class="' . esc_attr( $key ) . ' db pa bts-40 color-white bgd" href="skype:' . esc_attr( $value ) . '?chat"><i class="fa fa-' . esc_attr( $key ) . '"></a></a>';
						} else {
							$html .= '<a class="' . esc_attr( $key ) . ' db pa bts-40 color-white bgd" href="' . esc_url( $value ) . '" target="_blank" rel="noopener noreferrer"><i class="fa fa-' . esc_attr( $key ) . '"></a></a>';
						}
					}
				}

				$html .= '</div>';
			$html .= '</div>';
		} else {
			$values = ( array ) vc_param_group_parse_atts( $s3_member );

			// Get column
			if ( isset( $columns ) && (int) $columns > 0 ) {
				$data_columns = 'data-columns="' . (int) $columns . '"';
				$classes[] = 'columns-' . (int) $columns;
			}

			// Render HTML
			$html .= '<div ' . $data_columns . ' class="' . esc_attr( implode( ' ', $classes ) ) . '">';
				$two_image = $image_small = $image_small_link = '';

				foreach ( $values as $key => $value ) {
					// Get image link and image data
					if ( ! empty( $value['avatar'] ) ) {
						$image_small_id   = preg_replace( '/[^\d]/', '', $value['avatar'] );
						$image_small      = wp_get_attachment_image_src( $image_small_id, '370x480' );
						$image_small_link = wp_get_attachment_image_src( $image_small_id, 'full' );
					}

					// Get image link and image data
					if ( ! empty( $value['avatar_large'] ) ) {
						$image_large_id   = preg_replace( '/[^\d]/', '', $value['avatar_large'] );
						$image_large      = wp_get_attachment_image_src( $image_large_id, '370x480' );
						$image_large_link = wp_get_attachment_image_src( $image_small_id, 'full' );
					}

					if ( isset( $value['avatar_large'] ) && $value['avatar_large'] ) {
						$two_image = 'two-image';
					}

					$html .= '<div class="member mgb30 ts-03 csx-12 cs-6 cm-' . (int) ( 12 / $columns ) . '">';
						$html .= '<a class="member-avatar pr db ' . esc_attr( $two_image ) . '" href="javascript:void(0);">';
							$html .= '<img class="front ts-03" src="' . esc_url( $image_small[0] ) . '" alt="' . esc_attr( isset( $value['name'] ) ? $value['name'] : '' ) . '" width="370" height="480" />';
							if ( ! empty( $value['avatar_large'] ) ) {
								$html .= '<img class="back ts-03" src="' . esc_url( $image_large[0] ) . '" alt="' . esc_attr( isset( $value['name'] ) ? $value['name'] : '' ) . '" width="370" height="480" />';
							}
							$html .= '<span class="name pa color-primary br-2">' . esc_html( isset( $value['name'] ) ? $value['name'] : '' ) . '</span>';
						$html .= '</a>';

						$html .= '<div class="row">';
							$html .= '<div class="cm-6">';
							if ( ! empty( $value['avatar_large'] ) ) {
								$html .= '<img src="' . esc_url( $image_large_link[0] ) . '" alt="' . esc_attr( isset( $value['name'] ) ? $value['name'] : '' ) . '" />';
							} else {
								$html .= '<img src="' . esc_url( $image_small_link[0] ) . '" alt="' . esc_attr( isset( $value['name'] ) ? $value['name'] : '' ) . '" />';
							}
							$html .= '</div>';
							$html .= '<div class="cm-6">';
								$html .= '<h4>' . esc_html( isset( $value['name'] ) ? $value['name'] : '' ) . '</h4>';
								$html .= '<spanmeta-color">' . esc_html( isset( $value['position'] ) ? $value['position'] : '' ) . '</span>';
								$html .= '<p class="mgt30">' . wp_kses_post( isset( $value['bio'] ) ? $value['bio'] : '' ) . '</p>';
								$html .= '<div class="social">';
									$channels = array( 'facebook','twitter', 'linkedin', 'dribbble', 'behance', 'pinterest', 'tumblr', 'googleplus', 'instagram', 'skype' );

									foreach ( $channels as $channel ) {
										if ( isset( $value[$channel] ) && $value[$channel] ) {
											if ( 'googleplus' == $channel ) {
												$value[ 'google-plus' ] = $value[ $channel ];
												$channel = 'google-plus';
											}
											if ( $channel == 'skype' ) {
												$html .= '<a class="' . esc_attr( $channel ) . ' dib bts-40 nitro-line btb" href="skype:' . esc_attr( $value[$channel] ) . '?chat"><i class="fa fa-' . esc_attr( $channel ) . '"></a></a>';
											} else {
												$html .= '<a class="' . esc_attr( $channel ) . ' dib bts-40 nitro-line btb" href="' . esc_url( $value[$channel] ) . '" target="_blank" rel="noopener noreferrer"><i class="fa fa-' . esc_attr( $channel ) . '"></a></a>';
											}
										}
									}

								$html .= '</div>';
							$html .= '</div>';
						$html .= '</div>';
					$html .= '</div>';
				}
			$html .= '</div>';
		}

		return apply_filters( 'nitro_toolkit_shortcode_member', force_balance_tags( $html ) );
	}
}
