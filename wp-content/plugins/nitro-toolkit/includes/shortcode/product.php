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
 * Nitro Product shortcode.
 */
class Nitro_Toolkit_Shortcode_Product extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'product';

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
				wp_enqueue_style(  'owl-carousel'   );
				wp_enqueue_script( 'owl-carousel'   );
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
		$html = $id = $ids = '';

		$atts = shortcode_atts( array(
			'id'                 => '',
			'sku'                => '',
			'list_style'         => 'grid',
			'style'              => '1',
			'columns'            => '1',
			'hover_style'        => 'default',
			'mask_overlay_color' => 'rgba(0, 0, 0, 0.7)',
			'transition_effects' => 'fade',
			'slider'             => '',
			'autoplay'           => '',
			'shortcode'          => 'sc-product',
			'countdown'          => '',
			'extra_class'        => '',
		), $atts );

		$meta_query = WC()->query->get_meta_query();

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => 1,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'meta_query'     => $meta_query
		);

		if ( $atts['sku'] !== '' )
			$args['meta_query'][] = array(
				'key' 		=> '_sku',
				'value' 	=> $atts['sku'],
				'compare' 	=> '='
			);

		if ( $atts['id'] !== '' )
			$args['p'] = $atts['id'];

		$products = new WP_Query( $args );

		// Make shortcode attributes accessible from outside.
		Nitro_Toolkit_Shortcode::set_attrs( $atts );

		ob_start();

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		$css_class = array();

		$css_class[] = 'woocommerce';

		// Extra class
		if ( isset( $atts['extra_class'] ) ) {
			$css_class[] = $atts['extra_class'];
		}

		// Add id if slider enabled
		if ( $atts['slider'] ) {
			$id  = uniqid();
			$ids = 'id="nitro_custom_css_' . esc_attr( $id ) . '"';
		}

		// Enqueue countdown script
		if ( $atts['countdown'] ) {
			wp_enqueue_script( 'jquery-countdown' );
			$css_class[] = 'sale_countdown';
		}

		$html .= '<div ' . $ids . ' class="' . esc_attr( implode( ' ', $css_class ) ) . '">' . ob_get_clean() . '</div>';

		// Reset globally accessible shortcode attributes.
		Nitro_Toolkit_Shortcode::set_attrs( null );

		return apply_filters( 'nitro_toolkit_shortcode_product', force_balance_tags( $html ) );

	}
}
