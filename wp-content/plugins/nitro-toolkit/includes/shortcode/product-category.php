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
 * Nitro Product Categories shortcode.
 */
class Nitro_Toolkit_Shortcode_Product_Category extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'product_category';

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
				wp_enqueue_style( 'wr-nitro-woocommerce'     );
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
		global $post;

		$html = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'cat_id'      => '',
					'show_count'  => '',
					'extra_class' => '',
				),
				$atts
			)
		);

		if ( ! empty( $cat_id ) ) {
			// Get single terms
			$term = get_term( $cat_id, 'product_cat' );

			if( ! $term ) {
				return;
			}

			// Get single term link
			$link = get_term_link( $term->slug, 'product_cat' );

			$html .= '<div class="sc-product-cat pr tc ' . esc_attr( $extra_class ) . '">';
				$html .= '<a class="db" href="' . esc_url( $link ) . '">';
					$html .= self::get_cat_thumbnail( $term->term_id );
				$html .= '</a>';
				$html .= '<div class="pa tc body_bg">';
					$html .= '<a href="' . esc_url( $link ) . '" class="db tu fwb color-dark">' . $term->name . '</a>';

					if ( ! empty( $show_count ) ) {
						$html .= apply_filters( 'woocommerce_subcategory_count_html', ' <span class="count tu color-gray">' . esc_html( $term->count ) . ' ' . esc_html__( 'Items', 'nitro' ) . '</span>', $term );
					}

				$html .= '</div>';
			$html .= '</div>';
		}

		// Restore global product data in case this is shown inside a product post
		wc_setup_product_data( $post );

		return apply_filters( 'nitro_toolkit_shortcode_product_category', force_balance_tags( $html ) );
	}

	/**
	 * Define helper function to get category thumbnail.
	 *
	 * @param   int  $cat_id  Category ID.
	 *
	 * @return  string
	 */
	public static function get_cat_thumbnail( $cat_id ) {
		// Get category thumbnail ID
		if ( function_exists( 'get_term_meta' ) ) {
			$thumbnail_id = get_term_meta( $cat_id, 'thumbnail_id', true );
		} else {
			$thumbnail_id = get_woocommerce_term_meta( $cat_id, 'thumbnail_id', true );
		}

		// Generate HTML to display category thumbnail
		$image_data = '';

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image( $thumbnail_id, 'full' );
		} else {
			$cat = get_cat_name( $cat_id );

			$image = '<img src="' . wc_placeholder_img_src() . '" alt="' . esc_attr( $cat ) . '" />';
			$image_data[0] = $image_data[1] = '450';
		}

		if ( $image ) {
			$html = $image;
		}

		return $html;
	}
}