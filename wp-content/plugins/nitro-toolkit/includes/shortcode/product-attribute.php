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
 * Nitro Product Attributes shortcode.
 */
class Nitro_Toolkit_Shortcode_Product_Attribute extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'product_attribute';

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
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_product_attribute_custom_css';

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
					'slider'       => '',
					'gutter_width' => 30,
					'pagination'   => 'false',
					'navigation'   => 'false',
					'product_attribute_custom_id' => ''
				),
				$atts
			)
		);

		// Generate custom ID.
		$id = $product_attribute_custom_id;

		if ( $slider && ! empty( $gutter_width ) ) {
		$css .= '
	#' . esc_attr( $id ) . ' .owl-item {
		padding: 0 ' . ( is_numeric( trim( $gutter_width / 2 ) ) ? trim( $gutter_width / 2 ) . 'px' : trim( $gutter_width / 2 ) ) . ';
	}
		';
		}

		if ( 'true' == $pagination && 'true' == $navigation ) {
		$css .= '
	#' . esc_attr( $id ) . ' .owl-nav > div {
		-webkit-transform: translateY(calc(-50% - 22px));
		-ms-transform: translateY(calc(-50% - 22px));
		-o-transform: translateY(calc(-50% - 22px));
		transform: translateY(calc(-50% - 22px));
	}';
		} elseif ( 'true' != $pagination && 'true' == $navigation ) {
			$css .= '
	#' . esc_attr( $id ) . ' .owl-nav > div {
		-webkit-transform: translateY(-50%);
		-ms-transform: translateY(-50%);
		-o-transform: translateY(-50%);
		transform: translateY-50%;
	}';
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
		$atts = shortcode_atts( array(
			'columns'            => '4',
			'orderby'            => 'date',
			'order'              => 'ASC',
			'attribute'          => '',
			'filter'             => '',
			'per_page'           => '12',
			'list_style'         => 'grid',
			'style'              => '1',
			'hover_style'        => 'default',
			'mask_overlay_color' => 'rgba(0, 0, 0, 0.7)',
			'transition_effects' => 'fade',
			'shortcode'          => 'sc-products',
			'slider'             => '',
			'items'              => 4,
			'auto_play'          => '',
			'navigation'         => '',
			'pagination'         => '',
			'gutter_width'       => '',
			'extra_class'        => '',
			'product_attribute_custom_id' => ''
		), $atts );

		// Generate custom ID.
		$id = $atts['product_attribute_custom_id'];

		$classes = array( $atts['extra_class'] );

		if ( $atts['slider'] ) {
			$classes[] = 'oh';
		}

		if ( 'masonry' == $atts['list_style'] ) {
			wp_enqueue_script( 'isotope' );
		}

		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby'             => $atts['orderby'],
			'order'               => $atts['order'],
			'posts_per_page'      => $atts['per_page'],
			'meta_query'          => WC()->query->get_meta_query(),
			'tax_query'           => array(
				array(
					'taxonomy' => strstr( $atts['attribute'], 'pa_' ) ? sanitize_title( $atts['attribute'] ) : 'pa_' . sanitize_title( $atts['attribute'] ),
					'terms'    => array_map( 'sanitize_title', explode( ',', $atts['filter'] ) ),
					'field'    => 'slug'
				)
			)
		);

		$products = new WP_Query( $query_args );

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

		woocommerce_reset_loop();

		wp_reset_postdata();

		// Reset globally accessible shortcode attributes.
		Nitro_Toolkit_Shortcode::set_attrs( null );

		return '<div id="' . esc_attr( $id ) . '" class="woocommerce ' . esc_attr( implode( ' ', $classes ) ) . '">' . ob_get_clean() . '</div>';
	}
}
