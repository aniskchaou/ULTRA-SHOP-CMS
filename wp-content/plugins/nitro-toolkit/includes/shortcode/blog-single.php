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
 * Nitro Blog shortcode.
 */
class Nitro_Toolkit_Shortcode_Blog_Single extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'blog_single';

	/**
	 * Generate HTML code based on shortcode parameters.
	 *
	 * @param   array   $atts     Shortcode parameters.
	 * @param   string  $content  Current content.
	 *
	 * @return  string
	 */
	public function generate_html( $atts, $content = null ) {
		$html = $bg_image = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'post_id' => '',
				),
				$atts
			)
		);

		// Filter post type.
		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'p'              => $post_id
		);

		// Get query object.
		$the_query = new WP_Query( $args );

		// Generate HTML code.
		$html .= '<div class="element-blog">';

			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				if ( has_post_thumbnail() ) {
					$link = wp_get_attachment_url( get_post_thumbnail_id() );
					$bg_image = 'style="background: url( ' . esc_url( $link ) . ' ) no-repeat 0 0 / cover;"';
				}

				$html .= '<article class="' . esc_attr( join( ' ', get_post_class() ) ) . ' pr" ' . $bg_image . '>';

					$html .= '<div class="entry-content pa">';
						$html .= '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></h4>';
						$html .= '<div class="entry-meta">';
							$html .= WR_Nitro_Helper::get_author();
							$html .= WR_Nitro_Helper::get_posted_on();
						$html .= '</div>';
					$html .= '</div>';
				$html .= '</article>';
			}
			wp_reset_postdata();

		$html .= '</div>';

		return apply_filters( 'nitro_toolkit_shortcode_blog_single', force_balance_tags( $html ) );
	}
}
