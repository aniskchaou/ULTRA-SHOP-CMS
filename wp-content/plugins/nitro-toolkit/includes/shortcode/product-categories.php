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
class Nitro_Toolkit_Shortcode_Product_Categories extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'product_categories';

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
				wp_enqueue_style( 'wr-nitro-woocommerce' );
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

		$html = $attr = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'title'       => '',
					'type'        => '',
					'exclude'     => '',
					'thumb'       => '',
					'extra_class' => '',
				),
				$atts
			)
		);

		// Get product category
		$terms = get_terms( 'product_cat', array( 'hide_empty' => 0, 'exclude' => explode( ',', $exclude ) ) );

		if ( ! empty( $title ) && '2' == $type ) {
			$attr = 'data-expand="true"';
		}

		$html .= '<div class="sc-cat-list pr ' . esc_attr( $extra_class ) . '" ' . $attr . '>';
			$html .= '<a class="db bg-primary title" href="javascript:void(0);" class="title"><i class="fa fa-bars mgr30"></i> ' . esc_html( $title ) . '</a>';
			$html .= self::get_cat_list( $terms );
		$html .= '</div>';

		// Restore global product data in case this is shown inside a product post
		wc_setup_product_data( $post );

		return apply_filters( 'nitro_toolkit_shortcode_product_categories', force_balance_tags( $html ) );
	}

	/**
	 * Define helper function to get category thumbnail.
	 *
	 * @param   int  $cat_id  Category ID.
	 *
	 * @return  string
	 */
	public static function get_cat_thumbnail( $cat_id ) {
		$html = '';

		// Get category thumbnail ID
		$thumbnail_id = get_woocommerce_term_meta( $cat_id, 'thumbnail_id', true );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, '60x60' );
			$image = $image[0];
		} else {
			$image = wc_placeholder_img_src();
		}

		if ( $image ) {
			$html = '<img class="ts-03 mgr20" src="' . esc_url( $image ) . '" alt="" width="36" height="36">';
		}

		return $html;
	}

	/**
	 * Get product category list.
	 *
	 * @return  string
	 */
	public static function get_cat_list( $terms, $parent = 0, $level = 1 ) {
		$html = '';

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$link = get_term_link( $term->slug, 'product_cat' );

				if ( $term->parent == $parent ) {
					$children = get_term_children( $term->term_id, 'product_cat' );

					$html .= '<li>';
						$html .= '<a class="color-heading hover-primary" href="' . esc_url( $link ) . '">';
							$html .= self::get_cat_thumbnail( $term->term_id );
							$html .= '<span>' . esc_html( $term->name ) . '</span>';
						$html .= '</a>';

						if ( $level == 1 && wp_is_mobile() && $children ) {
							$html .= '<i class="fa fa-angle-down pa sc-cat-mobile"></i>';
						}

						$html .= self::get_cat_list( $terms, $term->term_id, $level + 1 );
					$html .= '</li>';
				}
			}

			if ( ! empty( $html ) ){
				$html = '<ul>' . $html . '</ul>';
			}
		}

		return $html;
	}
}
