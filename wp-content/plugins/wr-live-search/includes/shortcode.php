<?php
/**
 * @version    1.0
 * @package    WR_Live_Search
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

/**
 * Class to handle Live Search shortcode.
 *
 * @package  WR Live Search
 */
class WR_Live_Search_Shortcode {
	/**
	 * Generate HTML for Live Search shortcode.
	 *
	 * @param   array  $atts  Shortcode attributes.
	 *
	 * @return  void
	 */
	public static function generate( $atts = array() ) {
		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return '<div class="error"><p>' . __( 'This plugin requires the following plugin: <strong>WooCommerce</strong>', 'wr-live-search' ) . '</p></div>';
		}

		// Get saved settings.
		$settings = WR_Live_Search::get_settings();

		// Prepare shortcode attributes.
		if ( $atts ) {

			// Get coverages to search in.
			if ( isset( $atts['search_in'] ) && $atts['search_in'] != '' && !is_array( $atts['search_in'] ) ) {
				$search_in = explode( ',', $atts['search_in'] );

				if( $search_in ) {

					// Conver string to array()
					$atts['search_in'] = array();

					foreach ( $search_in as $coverage ) {
						$atts['search_in'][ trim( $coverage ) ] = 1;
					}
				}
			}

			// Merge $atts and $settings array
			$settings = array_merge(
				$settings,
				$atts
			);

		}

		// Generate HTML for live search.
		$html = '
<form ' . ( $settings['id'] != '' ? 'id="' . esc_attr( $settings['id'] ) . '"' : '' ) . ' role="search" method="get" class="wrls-form ' . esc_attr( $settings['class'] ) . '" action="' . esc_url( home_url( '/' ) ) . '">';

		// Check if category should be show.
		if ( $settings['show_category'] == 1 || $settings['show_category'] === 'yes' ) {
			// Get all WooCommerce categories.
			$list_category = get_terms( 'product_cat' );

			$list_category_children_along = array();

			if ( $list_category ) {

				foreach ( $list_category as $key => $val ) {
					if ( $val->parent == 0 ) {
						$val->level = 0;
						$list_category_children_along[] = $val;

						self::set_term_recursive( $val, $list_category_children_along, $list_category );
					}
				}
			}

			if ( $list_category_children_along ) {
				$html .= '
	<div class="cate-search-outer"><select class="cate-search" name="product_cat">
		<option value="">' . __( 'All categories', 'wr-live-search' ) . '</option>';

				foreach ( $list_category_children_along as $key => $val ) {
					$html .= '
		<option ' . ( ( $val->slug == get_query_var( 'product_cat' ) ) ? 'selected="selected"' : '' ) . ' value="' . esc_attr( $val->slug ) . '">' . str_repeat( '&nbsp;&nbsp;&nbsp;', $val->level ) . esc_attr( $val->name ) . '</option>';
				}

				$html .= '
	</select></div>';
			}
		}

		$html .= '
	<div class="results-search">
		<input type="hidden" name="post_type" value="product">';


	$search_in = array();
	if( ! empty( $settings['search_in'] ) ) {
		foreach( $settings['search_in'] as $key => $val ) {
			if( $val == 1 ) {
				$search_in[] = $key;
			}
		}
	}

	if( $search_in ) {
		$html .= '
		<input type="hidden" name="wrls_search_in" value="' . implode( ',', $search_in ) . '">';
	}

	$html .= '
		<input required data-max-results="' . (int) $settings['max_results'] . '" data-thumb-size="' . (int) $settings['thumb_size'] . '" data-min-characters="' . (int) $settings['min_characters'] . '" data-search-in=' . "'" . json_encode( $settings['search_in'] ) . "'" . ' data-show-suggestion="' . ( ( $settings['show_suggestion'] === 'yes' || $settings['show_suggestion'] == 1 ) ? 1 : 0 ) . '" value="' . esc_attr( get_query_var( 's' ) ) . '" placeholder="' . esc_attr( $settings['placeholder'] ) . '" class="txt-livesearch suggestion-search" type="search" name="s" autocomplete="off">
	</div>';

			$html .= '
	<input class="btn-livesearch ' . ( ! ( $settings['show_button'] == 1 || $settings['show_button'] === 'yes' ) ? 'hidden' : NULL )  . '" type="submit" value="' . esc_attr( $settings['text_button'] ) . '">';

		$html .= '
</form>';

		return $html;
	}

	/**
	 * Set term item for recursive
	 *
	 * @param object $term_item
	 * @param array $list_category_children_along
	 * @param object $all_terms
	 *
	 * @return  void
	 */
	public static function set_term_recursive( $term_item, &$list_category_children_along, $all_terms ) {
		foreach ( $all_terms as $key => $val ) {
			if ( $val->parent == $term_item->term_id ) {
				$val->level = $term_item->level + 1;
				$list_category_children_along[] = $val;

				// Call recursive.
				self::set_term_recursive( $val, $list_category_children_along, $all_terms );
			}
		}
	}
}
