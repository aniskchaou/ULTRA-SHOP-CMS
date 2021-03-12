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
 * Nitro Product Button shortcode.
 */
class Nitro_Toolkit_Shortcode_Product_Button extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'product_button';

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

		$html = $product = '';

		// Extract shortcode parameters.
		$atts = shortcode_atts(
			array(
				'button_style' => 'light',
				'alignment'    => '',
				'list_style'   => 'grid',
				'style'        => 1,
				'id'           => '',
				'show_price'   => '',
				'radius'       => '',
				'wishlist'     => '',
				'extra_class'  => ''
			),
			$atts
		);

		extract( $atts );

		$classes = array( $extra_class );

		// Button Style
		if ( $button_style ) {
			$classes[] = $button_style;
		}

		if ( $button_style == 'light' ) {
			$classes[] = 'nitro-line';
		}

		// Button Alignment
		if ( $alignment ) {
			$classes[] = $alignment;
		}

		// Button Radius
		if ( $radius ) {
			$classes[] = 'btn-radius';
		}

		if ( ! empty( $id ) ) {
			$product_data = get_post( $id );

			$product = wc_setup_product_data( $product_data );
		}

		if ( ! $product ) {
			return '';
		}

		// Filter product post type
		$args = array(
			'post_type'   => 'product',
			'post_status' => 'publish',
			'p'           => $id,
		);

		$query = new WP_Query( $args );


		// Make shortcode attributes accessible from outside.
		Nitro_Toolkit_Shortcode::set_attrs( $atts );

		$html .= '<div class="sc-product-button fc aic ' . esc_attr( implode( ' ', $classes ) ) . '">';
			while ( $query->have_posts() ) :
				$query->the_post();

				ob_start();

				wc_get_template( 'loop/add-to-cart.php' );

				$html .= ob_get_contents();

				ob_end_clean();

				if ( $wishlist && class_exists( 'YITH_WCWL' ) ) {
					$html .= do_shortcode( '[yith_wcwl_add_to_wishlist]' );
				}

			endwhile;
		$html .= '</div>';

		// Restore global post data in case this is shown inside a product post
		wp_reset_postdata();

		// Reset globally accessible shortcode attributes.
		Nitro_Toolkit_Shortcode::set_attrs( null );

		return apply_filters( 'nitro_toolkit_shortcode_product_button', force_balance_tags( $html ) );
	}
}
