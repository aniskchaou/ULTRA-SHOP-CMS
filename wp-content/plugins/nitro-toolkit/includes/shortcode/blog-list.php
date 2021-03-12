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
 * Nitro Blog List shortcode.
 */
class Nitro_Toolkit_Shortcode_Blog_List extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'blog_list';

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
	protected $metakey = '_wr_shortcode_blog_list_custom_css';

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
					'orderby'             => 'date',
					'contain'             => '',
					'exclude'             => '',
					'exclude_by_cat'      => '',
					'style'               => 'standard',
					'slider'              => '',
					'pagination'          => '',
					'navigation'          => '',
					'autoplay'            => '',
					'columns'             => '3',
					'limit'               => '',
					'gap'                 => '30',
					'image'               => 'yes',
					'excerpt'             => 'yes',
					'show_content'        => 'yes',
					'excerpt_length'      => '20',
					'meta'                => 'yes',
					'divider'             => '',
					'divider_color'       => '#eaeaea',
					'font_size'           => 16,
					'readmore'            => 'yes',
					'align'               => 'tl',
					'content_position'    => 'bottom',
					'el_class'            => '',
					'blog_list_custom_id' => '',
				),
				$atts
			)
		);

		// Generate custom ID.
		$id = $blog_list_custom_id;

		if ( ! empty( $gap )  && ! $slider ) {
			$css .= '
				#' . esc_attr( $id ) . '.list-blog {
					margin: 0 -' . ( is_numeric( trim( $gap / 2 ) ) ? trim( $gap / 2 ) . 'px' : trim( $gap / 2 ) ) . ';
				}
			';
		}

		$css .= '#' . esc_attr( $id ) . '.list-blog article {';

			if ( ! empty( $gap ) ) {
				$css .= '
					padding: ' . ( is_numeric( trim( $gap / 2 ) ) ? trim( $gap / 2 ) . 'px' : trim( $gap / 2 ) ) . ';
				';
			} else {
				$css .= 'padding: 0;';
			}

			if ( ! empty( $gap ) && $divider ) {
				$css .= 'border-right: 1px solid ' . esc_attr( $divider_color ) . ';';
			}

		$css .= '}';

		if ( $divider ) {
			$css .= '
				#' . esc_attr( $id ) . '.list-blog article:nth-child(' . esc_attr( $columns ) . 'n),
				#' . esc_attr( $id ) . '.list-blog:not(.wr-nitro-carousel) article:last-child,
				#' . esc_attr( $id ) . '.list-blog.wr-nitro-carousel .owl-item:last-child article {
					border-right: none;
				}
			';
		}

		if ( $divider && ! empty( $gap ) && $limit > $columns  ) {
			$css .= '
				#' . esc_attr( $id ) . '.list-blog .entry-row:nth-child(odd) article:after {
					content: "";
					height: 1px;
					position: absolute;
					background:' . esc_attr( $divider_color ) . ';
					left:' . ( is_numeric( trim( $gap / 2 + 25 ) ) ? trim( $gap / 2 + 25 ) . 'px' : trim( $gap / 2 + 25 ) ) . ';
					right:' . ( is_numeric( trim( $gap / 2 + 25 ) ) ? trim( $gap / 2 + 25 ) . 'px' : trim( $gap / 2 + 25 ) ) . ';
					bottom:-' . ( is_numeric( trim( $gap / 2 ) ) ? trim( $gap / 2 ) . 'px' : trim( $gap / 2 ) ) . ';
				}
			';
		}

		if ( 'zigzag' == $style ) {
			$css .= '
				#' . esc_attr( $id ) . '.list-blog .entry-content:after {
					content: "";
					background: #f6f6f6;
					position: absolute;
					z-index: -1;
					top: 0;
					height: 100%;
					right:' . ( is_numeric( trim( $gap / 2 ) ) ? trim( $gap / 2 ) . 'px' : trim( $gap / 2 ) ) . ';
					left:' . ( is_numeric( trim( $gap / 2 ) ) ? trim( $gap / 2 ) . 'px' : trim( $gap / 2 ) ) . ';
				}
			';
		}

		if ( ! empty( $gap ) && 'inner' == $style && ( 'top' == $content_position || 'middle' == $content_position ) ) {
			$css .= '
				#' . esc_attr( $id ) . '.list-blog.top .entry-thumb a:after,
				#' . esc_attr( $id ) . '.list-blog.middle .entry-thumb a:after {
					content: "";
					background: rgba(0,0,0,.5);
					position: absolute;
					top: 0;
					height: 100%;
					right:' . ( is_numeric( trim( $gap / 2 ) ) ? trim( $gap / 2 ) . 'px' : trim( $gap / 2 ) ) . ';
					left:' . ( is_numeric( trim( $gap / 2 ) ) ? trim( $gap / 2 ) . 'px' : trim( $gap / 2 ) ) . ';
				}
			';
		}

		if ( ! empty( $gap ) && 'inner' == $style ) {
			$css .= '
				#' . esc_attr( $id ) . '.list-blog .entry-content {
					right: 0;
					left: 0;
				}
			';
		}

		if ( ! empty( $font_size ) ) {
			$css .= '
				#' . esc_attr( $id ) . '.list-blog .entry-content .entry-title {
					font-size:' . ( is_numeric( trim( $font_size ) ) ? trim( $font_size ) . 'px' : trim( $font_size ) ) . ';
					line-height: 1.5em;
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
		$html = $data = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'orderby'             => 'date',
					'sort_order'          => 'asc',
					'contain'             => '',
					'exclude'             => '',
					'exclude_by_cat'      => '',
					'style'               => 'standard',
					'thumbnail'           => 'medium',
					'slider'              => '',
					'pagination'          => '',
					'navigation'          => '',
					'autoplay'            => '',
					'autotime'            => '5000',
					'pause'               => '',
					'columns'             => '3',
					'gap'                 => '30',
					'limit'               => '12',
					'image'               => 'yes',
					'show_content'        => 'yes',
					'excerpt'             => 'yes',
					'excerpt_length'      => '20',
					'meta'                => 'yes',
					'divider'             => '',
					'readmore'            => 'yes',
					'align'               => 'tl',
					'content_position'    => 'bottom',
					'el_class'            => '',
					'blog_list_custom_id' => '',
				),
				$atts
			)
		);

		// Filter post type.
		$args = array(
			'post_type'           => 'post',
			'orderby'             => $orderby,
			'order'               => $sort_order,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => $limit,
			'no_found_rows' => true,
		);

		if ( ! empty( $contain ) && $orderby == 'specials' ) {
			$args['post__in'] = array_map( 'trim', explode( ',', $atts['contain'] ) );
		}

		if ( ! empty( $exclude ) ) {
			$args['post__not_in'] = array_map( 'trim', explode( ',', $atts['exclude'] ) );
		}

		if ( ! empty( $exclude_by_cat ) ) {
			$args['category__not_in'] = array_map( 'trim', explode( ',', $atts['exclude_by_cat'] ) );
		}

		// Generate custom ID.
		$id = $blog_list_custom_id;

		// Prepare class.
		$classes = array( $el_class );

		// Get style.
		if ( $style ) {
			$classes[] = $style;
		}

		// Get align content.
		$classes[] = $align;

		// Get hover effect.
		if ( 'inner' == $style && ! $show_content ) {
			$classes[] = 'hide-content';
		}

		// Get thumbnail size.
		if ( 'list' == $style ) {
			$classes[] = $thumbnail;
		}

		// Get columns.
		if ( ! empty( $columns ) && ! $slider ) {
			$classes[] = 'columns-' . $columns;
		}

		// Get content position.
		if ( ! empty( $content_position ) && 'inner' == $style ) {
			$classes[] = $content_position;
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
				$data = 'data-owl-options=\'{' . esc_attr( implode( ', ', $attr ) ) . ',"tablet":"2","mobile":"1"' . ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ) . '}\'';
			}

			$classes[] = 'wr-nitro-carousel';
		}

		// Get query object.
		$the_query = new WP_Query( $args );

		if( 'yes' == $image ) {
			$classes[] = 'has-featured-img';
		}

		// Generate HTML code.
		$html .= '<div id="' . esc_attr( $id ) . '" class="list-blog oh ' . esc_attr( implode( ' ', $classes ) ) . '" ' . $data . '>';

		$i = 0;

		if ( 'list' == $style || 'minimal' == $style ) {

			while ( $the_query->have_posts() ) {

				$the_query->the_post();

				if ( $i%esc_attr( $columns ) == 0 && $limit > $columns && ! $slider ) {
					$html .= '<div class="entry-row oh">';
				}

				$html .= '<article id="post-' . get_the_ID() . '" class="' . esc_attr( join( ' ', get_post_class(  'pr cs-6 cxs-12 cm-' . (int) ( 12 / $columns )  ) ) ) . '" >';

				// Get thumbnail size.
				if ( has_post_thumbnail() && 'yes' == $image && 'list' == $style ) {

					$img_link = wp_get_attachment_image_src( get_post_thumbnail_id(), '405x300' );

					$html .= '<div class="entry-thumb fl"><a class="db" href="' . esc_url( get_permalink() ) . '"><img src="' . esc_url( $img_link[0] ) . '" class="ts-03" alt="' . get_the_title() . '" width="405" height ="300" /></a></div>';
				}

				if ( $meta && 'minimal' == $style ) {
					$html .= '<div class="entry-meta fl mgr30">';

					$time = sprintf( '<div class="entry-time tc"><time class="published updated dib"><span class="days db">%s</span><span class="mon db">%s</span></time></div>', esc_attr( get_the_date( 'j' ) ), esc_html( get_the_date( 'F' ) ) );
					$html .= $time;

					$html .= '</div>';
				}

				$html .= '<div class="entry-content fl">';
				$html .= '<h3 class="entry-title ' . ( ( ! empty( $gap ) ) ? 'mg0 mgb10': '' ) . '"><a href="' . esc_url( get_permalink() ) . '" title="' . get_the_title() . '" class="color-dark hover-primary">' . get_the_title() . '</a></h3>';

				if ( $meta && 'list' == $style ) {
					$html .= '<div class="entry-meta">';
					if ( class_exists( 'WR_Nitro_Helper' ) ) {
						$html .= WR_Nitro_Helper::get_author();
						$html .= WR_Nitro_Helper::get_posted_on();
					}

					ob_start();

					if ( class_exists( 'WR_Nitro_Helper' ) ) {
						WR_Nitro_Helper::get_comment_count();
					}

					$html .= ob_get_clean();

					$html .= '</div>';
				}

				if ( $excerpt && $excerpt_length && class_exists( 'WR_Nitro_Helper' ) ) {
					$html .= '<p class="' . ( ( 'list' == $style ) ? 'mgt10' : 'mg0' ) . '">' . WR_Nitro_Helper::get_excerpt( esc_html( $excerpt_length ), '...' ) . '</p>';
				}

				if ( class_exists( 'WR_Nitro_Helper' ) ) {
					$html .= WR_Nitro_Helper::read_more();
				}

				$html .= '</div>';
				$html .= '</article>';

				$i++;
				if ( $i%esc_attr( $columns ) == 0 && $limit > $columns && ! $slider ) {
					$html .= '</div>';
				}

			}

		} else {

			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				if ( $i % esc_attr( $columns ) == 0 && $limit > $columns && ! $slider ) {
					$html .= '<div class="entry-row oh">';
				}

				$html .= '<article id="post-' . get_the_ID() . '" class="' . esc_attr( join( ' ', get_post_class( 'cs-12 cxs-12 cm-' . (int) ( 12 / $columns ) ) ) ) . ' ' . ( ( 'inner' == $style ) ? 'mgb30' : '' ) . '" >';
				$html .= '<div class="pr">';

				// Get thumbnail size.
				if ( has_post_thumbnail() && 'yes' == $image ) {
					$img_link = wp_get_attachment_image_src( get_post_thumbnail_id(), '405x300' );

					$html .= '<div class="entry-thumb"><a href="' . esc_url( get_permalink() ) . '"><img src="' . esc_url( $img_link[0] ) . '" class="ts-03" alt="' . get_the_title() . '" width="405" height ="300" /></a></div>';
				}

				$html .= '<div class="entry-content ts-03">';
				$html .= '<h3 class="ts-03 entry-title"><a href="' . esc_url( get_permalink() ) . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';

				if ( $meta ) {
					$html .= '<div class="entry-meta ts-03">';
					if ( class_exists( 'WR_Nitro_Helper' ) ) {
						$html .= WR_Nitro_Helper::get_author();
						$html .= WR_Nitro_Helper::get_posted_on();
					}

					ob_start();

					if ( class_exists( 'WR_Nitro_Helper' ) ) {
						WR_Nitro_Helper::get_comment_count();
					}

					$html .= ob_get_clean();

					$html .= '</div>';
				}

				if ( $excerpt && $excerpt_length && class_exists( 'WR_Nitro_Helper' ) ) {
					$html .= '<p class="mgt10 ts-03">' . WR_Nitro_Helper::get_excerpt( esc_html( $excerpt_length ), '...' ) . '</p>';
				}

				if ( $readmore && 'inner' != $style ) {
					$html .= WR_Nitro_Helper::read_more();
				}

				$html .= '</div>';
				$html .= '</div>';
				$html .= '</article>';

				$i++;

				if ( $i % esc_attr( $columns ) == 0 && $limit > $columns && ! $slider ) {
					$html .= '</div>';
				}
			}
		}

		wp_reset_postdata();

		$html .= '</div>';
		return apply_filters( 'nitro_toolkit_shortcode_blog_list', force_balance_tags( $html ) );
	}

}
