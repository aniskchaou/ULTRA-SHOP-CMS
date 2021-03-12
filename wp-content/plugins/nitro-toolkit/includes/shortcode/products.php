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
 * Nitro Products shortcode.
 */
class Nitro_Toolkit_Shortcode_Products extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'products';

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
	protected $metakey = '_wr_shortcode_products_custom_css';

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
					'columns'      => '4',
					'nitro_products_custom_id' => ''
				),
				$atts
			)
		);

		// Generate custom ID.
		$id = $nitro_products_custom_id;

		if ( $slider && ! empty( $gutter_width ) ) {
			$css .=
				'#' . esc_attr( $id ) . ' .owl-item {
					padding: 0 ' . ( is_numeric( trim( $gutter_width / 2 ) ) ? trim( $gutter_width / 2 ) . 'px' : trim( $gutter_width / 2 ) ) . ';
				}
			';
		}

		if ( '5' == $columns ) {
			$css .= '
				.sc-products .product:nth-child(5n +1) {
					clear: both;
				}
			';
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
			'order_fillter'      => 'all',
			'columns'            => '4',
			'orderby'            => 'date',
			'order'              => 'asc',
			'cat_id'             => '',
			'ids'                => '',
			'skus'               => '',
			'per_page'           => '12',
			'list_style'         => 'grid',
			'style'              => '1',
			'hover_style'        => 'default',
			'mask_overlay_color' => 'rgba(0, 0, 0, 0.7)',
			'transition_effects' => 'fade',
			'shortcode'          => 'sc-products',
			'slider'             => '',
            'auto_play'          => '',
            'timeout'          	 => '',
			'navigation'         => '',
			'pagination'         => '',
			'992'                => 4,
			'768'                => 3,
			'600'                => 2,
			'375'                => 1,
			'gutter_width'       => '',
			'extra_class'        => '',
			'nitro_products_custom_id' => ''
		), $atts );

		// Generate custom ID.
		$id = $atts['nitro_products_custom_id'];

		$ids = $script = '';
		$classes = array( $atts['extra_class'] );

		if ( $atts['slider'] ) {
			$ids = 'id="' . esc_attr( $id ) . '"';
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
			'tax_query'           => WC()->query->get_tax_query()
		);

		switch ( $atts['order_fillter'] ) {
			case 'all':

				if ( $atts['skus'] !== '' )
					$query_args['meta_query'][] = array(
						'key'     => '_sku',
						'value'   => array_map( 'trim', explode( ',', $atts['skus'] ) ),
						'compare' => 'IN'
					);

				if ( $atts['ids'] !== '' )
					$query_args['post__in'] = array_map( 'trim', explode( ',', $atts['ids'] ) );

				break;

			case 'recent':

				$query_args['orderby'] = 'date';
				$query_args['order'] = 'desc';

				break;

			case 'featured':

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
					'operator' => 'IN',
 				);

				break;

			case 'sale':

				$query_args['no_found_rows'] = 1;
				$query_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );

				break;

			case 'best_selling':

				$query_args['meta_key'] = 'total_sales';
				$query_args['orderby'] 	= 'meta_value_num';
				$query_args['order'] 	= 'desc';

				break;

			case 'top_rated':

				add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

				break;

			case 'by_cat':
				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => array_map( 'trim', explode( ',', $atts['cat_id'] ) ),
				);

				break;

		}

		$products = new WP_Query( $query_args );

		// Make shortcode attributes accessible from outside.
		Nitro_Toolkit_Shortcode::set_attrs( $atts );

		if ( 'list-small' != $atts['list_style'] ) {
			ob_start();

			if ( $products->have_posts() ) : ?>

				<?php woocommerce_product_loop_start(); ?>

					<?php while ( $products->have_posts() ) : $products->the_post(); ?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php endwhile; // end of the loop. ?>

				<?php woocommerce_product_loop_end(); ?>

			<?php endif;

			if ( $atts['order_fillter'] == 'top_rated' )
				remove_filter( 'posts_clauses', array( __CLASS__, 'order_by_rating_post_clauses' ) );

			woocommerce_reset_loop();

			wp_reset_postdata();

			// Reset globally accessible shortcode attributes.
			Nitro_Toolkit_Shortcode::set_attrs( null );

			return '<div ' . $ids . ' class="woocommerce ' . esc_attr( implode( ' ', $classes ) ) . '">' . ob_get_clean() . '</div>';
		} else {
			$html = '';
			$html .= '<div class="sc-product-list widget">';
				$html .= '<ul class="product_list_widget">';
					while ( $products->have_posts() ) {
						$products->the_post();
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), '60x60' );

						$html .= '<li>';
							$html .= '<a href="' . esc_url( get_permalink() ) . '" title="' . get_the_title() . '">';
								$html .= '<img src="' . esc_url( $image[0] ) . '" width="60" height="60" alt="' . get_the_title() . '" />';
							$html .= '</a>';
							$html .= '<div class="product-info">';
								$html .= '<h3 class="product-title"><a class="hover-primary" href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></h3>';
								$html .= '<div class="p-price">';

									ob_start();

									do_action( 'woocommerce_after_shop_loop_item_title' );

									$html .= ob_get_contents();

									ob_end_clean();

								$html .= '</div>';
							$html .= '</div>';
						$html .= '</li>';
					}
				$html .= '</ul>';
			$html .= '</div>';
			wp_reset_postdata();

			return force_balance_tags( $html );
		}
	}
}
