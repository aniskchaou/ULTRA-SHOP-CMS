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
 * Nitro Testimonial shortcode.
 */
class Nitro_Toolkit_Shortcode_Testimonial extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'testimonial';

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

				wp_enqueue_script( 'isotope' );
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
		$html = $data = $data_slider = $data_masonry = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'style'                 => 'style-1',
					'columns'               => '3',
					'slider'                => '',
					'masonry'               => '',
					'pagination'            => '',
					'navigation'            => '',
					'autoplay'              => '',
					'autotime'              => '5000',
					'pause'                 => '',
					'align'                 => '',
					's1_position'           => 'top',
					's2_position'           => 'top',
					'align'                 => 'tl',
					'avatar'                => '',
					'avt_shape'             => 'circle',
					'testimonial'           => '',
					'name'                  => '',
					'job'                   => '',
					'extra_class'           => '',
					'testimonials_content'  => '',
				),
				$atts
			)
		);

		$classes = $attr = array();

		// Get extra class.
		if ( ! empty( $extra_class ) ) {
			$classes[] = $extra_class;
		}

		// Get avatar position.
		if ( 'style-1' == $style ) {
			$classes[] = $s1_position;
		} elseif ( 'style-2' == $style ) {
			$classes[] = $s2_position;
		}

		// Get style.
		if ( $style ) {
			$classes[] = $style;
		}

		// Get align content.
		if ( 'style-1' == $style ) {
			$classes[] = $align;
		}

		// Get columns.
		if ( ! empty( $columns ) && ! $slider ) {
			$classes[] = 'columns-' . $columns;
		}

		if ( ! empty( $slider ) ) {
			$wr_nitro_options = WR_Nitro::get_options();

			if ( ! empty( $columns ) ) {
				$attr[] = '"items": "' . ( int ) $columns . '"';
			}
			if ( $autoplay ) {
				$attr[] = '"autoplay": "true"';
			}
			if ( ! empty( $autotime ) && $autoplay ) {
				$attr[] = '"autoplayTimeout": "' . ( int ) $autotime . '"';
			}
			if ( ! empty( $pause ) && $autoplay ) {
				$attr[] = '"autoplayHoverPause": "true"';
			}
			if ( $navigation ) {
				$attr[] = '"nav": "true"';
			}
			if ( $pagination ) {
				$attr[] = '"dots": "true"';
			}

			if ( ! empty( $attr ) ) {
				$data_slider = 'data-owl-options=\'{' . esc_attr( implode( ', ', $attr ) ) . ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ) . '}\'';
			}

			$classes[] = 'wr-nitro-carousel';
		}

		if ( ! empty( $masonry ) && ! $slider ) {
			$data_masonry = 'data-masonry=\'{"selector":".testimonial-item"}\'';
			$classes[] = 'wr-nitro-masonry';
		}

		$html .= '<div class="sc-testimonials oh ' . esc_attr( implode( ' ', $classes ) ) . '" ' . $data_slider . ' ' . $data_masonry . '>';

		$values = ( array ) vc_param_group_parse_atts( $testimonials_content );

		foreach ( $values as $key => $values ) {

			// Get image link and image data
			if ( ! empty( $values[ 'avatar' ] ) ) {
				$img_id       = preg_replace( '/[^\d]/', '', $values[ 'avatar' ] );
				$image        = wpb_getImageBySize( array( 'attach_id' => $img_id ) );
				$image_link   = $image[ 'p_img_large' ][ 0 ];
			}

			if ( 'style-1' == $style ) {
				$html .= '<div class="testimonial-item cs-12 cxs-12 cm-' . (int) (12 / $columns ) . '">';

					if ( ! empty( $masonry ) && ! $slider ) {
						$html .= '<div class="testimonial-item-masonry body_bg">';
					}

					if ( ! empty( $values[ 'avatar' ] ) && 'bottom' != $s1_position ) {
						$html .= '<div class="avatar mgb30 dib">';
							$html .= '<img src="' . esc_url( $image_link ) . '" alt="' .  ( ( ! empty( $values[ 'name' ] ) ) ?  esc_attr( $values[ 'name' ] ) : esc_attr__( 'Avatar', 'nitro-toolkit' ) ) . '" ' . ( ( 'circle' == $avt_shape ) ? 'class="br-50"' : '' ) . ' width="70" height="70" />';
						$html .= '</div>';
					}

					$html .= '<div class="content">';
						$html .= '<p>' . $values[ 'testimonial' ] . '</p>';
					$html .= '</div>';

					if ( ! empty( $values[ 'avatar' ] ) && 'bottom' == $s1_position ) {
						$html .= '<div class="avatar mgt20 dib">';
							$html .= '<img src="' . esc_url( $image_link ) . '" alt="' .  ( ( ! empty( $values[ 'name' ] ) ) ?  esc_attr( $values[ 'name' ] ) : esc_attr__( 'Avatar', 'nitro-toolkit' ) ) . '" ' . ( ( 'circle' == $avt_shape ) ? 'class="br-50"' : '' ) . ' width="' . esc_attr( $image[ 'p_img_large' ][1] ) . '" height="' . esc_attr( $image[ 'p_img_large' ][2] ) . '" />';
						$html .= '</div>';
					}

					$html .= '<div class="author mgt20">';

						if ( ! empty( $values[ 'name' ] ) ) {
							$html .= '<h5 class="name dib">' . esc_html( $values[ 'name' ] ) . '</h5>';
						}

						if ( ! empty( $values[ 'job' ] ) ) {
							$html .= '<p class="job meta-color">' . esc_html( $values[ 'job' ] ) . '</p>';
						}

					$html .= '</div>';

					if ( ! empty( $masonry ) && ! $slider ) {
						$html .= '</div>';
					}
				$html .= '</div>';
			} else {
				$html .= '<div class="testimonial-item cs-12 cxs-12 cm-' . (int) (12 / $columns ) . '">';

				if ( 'bottom' == $s2_position ) {
					$html.= '<div class="fc fccr">';
				}

				$html .= '<div class="author oh ' . ( ( 'bottom' == $s2_position ) ? 'mgt30' : 'mgb30' ) . '">';

				if ( ! empty( $values[ 'avatar' ] ) ) {
					$html .= '<div class="avatar fl dib mgr20">';
						$html .= '<img src="' . esc_url( $image_link ) . '" alt="' .  ( ( ! empty( $values[ 'name' ] ) ) ?  esc_attr( $values[ 'name' ] ) : esc_attr__( 'Avatar', 'nitro-toolkit' ) ) . '" ' . ( ( 'circle' == $avt_shape ) ? 'class="br-50"' : '' ) . ' width="70" height="70" />';
					$html .= '</div>';
				}

				if ( ! empty( $values[ 'name' ] ) ) {
					$html .= '<h5 class="name dib">' . esc_html( $values[ 'name' ] ) . '</h5>';
				}

				if ( ! empty( $values[ 'job' ] ) ) {
					$html .= '<p class="job meta-color">' . esc_html( $values[ 'job' ] ) . '</p>';
				}

				$html .= '</div>';

				$html .= '<div class="content pd30 pdt10 pr btb br-2 body_bg">';
				$html .= '<span class="arrow pa db"><span class="pa"></span></span>';
				$html .= '<svg class="bts-40">';
				$html .= '<g>';
				$html .= '<text y="65" x="0" fill="#e2e2e2">“</text>';
				$html .= '</g>';
				$html .= '</svg>';
				$html .= '<p class="mg0">' . $values[ 'testimonial' ] . '</p>';
				$html .= '</div>';

				if ( 'bottom' == $s2_position ) {
					$html.= '</div>';
				}

				$html .= '</div>';
			}
		}

		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_testimonial', force_balance_tags( $html ) );
	}
}
