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
 * Nitro Counter Up shortcode.
 */
class Nitro_Toolkit_Shortcode_Product_Menu extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'product_menu';

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
		$html = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'title'       => '',
					'sub_title'   => '',
					'cat_id'      => '',
					'limit'       => -1,
					'style'       => '',
					'orderby'     => 'date',
					'order'       => 'DESC',
					'extra_class' => ''
				),
				$atts
			)
		);

		$classes = array( 'sc-product-menu ' . $extra_class );

		$term      = get_term_by( 'id', $cat_id, 'product_cat' );
		$term_link = get_term_link( $term, 'product_cat' );

		$args = array(
			'post_type' 	 => 'product',
			'posts_per_page' => $limit,
			'orderby' 		 => $orderby,
			'order'			 => $order,
			'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $cat_id,
				),
			),
		);
		$query = new WP_Query( $args );

		// Style 2
		if ( 'has-image' == $style ) {
			$classes[] = 'has-image';
		}

		if ( $term ) {

			$html .= '<div class="' . esc_attr( implode(' ', $classes ) ) . '">';

				$html .= '<div class="menu-title tc mgb30">';
					$html .= '<h3 class="mg0">' . esc_html( $title ) . '</h3>';
					$html .= '<h4 class="tu mg0">' . esc_html( $sub_title ) . '</h3>';
				$html .= '</div>';
				$html .= '<ul class="product-menu">';

				while ( $query->have_posts() ) : $query->the_post();
					global $post;

					$product = new WC_Product( get_the_ID() );

						if ( $style == 'has-image' ) {
							$excerpt = wp_trim_words( apply_filters( 'woocommerce_short_description', $post->post_excerpt ), '15', '' );
						} else {
							$excerpt = wp_trim_words( apply_filters( 'woocommerce_short_description', $post->post_excerpt ), '8', '' );
						}

						$html .= '<li class="clear mgb30 pr">';
							if ( 'has-image' == $style ) {
								$html .= '<div class="product-image fl">';
									$html .= '<a href="' . esc_url( get_permalink() ) . '">' . get_the_post_thumbnail( $post->ID, array( 75, 75 ) ) . '</a>';
								$html .= '</div>';
							}

							$html .= '<div class="product-info">';
								$html .= '<div class="product-meta oh">';
									$html .= '<h5 class="product-title mg0 fl"><a href="' . esc_url( get_permalink() ) . '" class="hover-primary">' . get_the_title() . '</a></h5>';
									if ( $price_html = $product->get_price_html() ) :
										$html .= '<p class="price mg0 pdl10 pr color-primary fr">' . $price_html . '</p>';
									endif;
								$html .= '</div>';
								$html .= '<div class="product-desc">' . esc_html( $excerpt ) . '</div>';
							$html .= '</div>';
						$html .= '</li>';
					endwhile;
					wp_reset_postdata();

				$html .= '</ul>';
			$html .= '</div>';

		}

		return apply_filters( 'nitro_toolkit_shortcode_product_menu', force_balance_tags( $html ) );
	}
}
