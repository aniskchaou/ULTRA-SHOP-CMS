<?php
/**
 * @version    1.0
 * @package    Nitro
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */
// Get options
$wr_nitro_options = WR_Nitro::get_options();

// Settings
if ( $wr_nitro_options['rtl'] ) {
	$wr_sep   = '<i class="fa fa-angle-left"></i>';
} else {
	$wr_sep   = '<i class="fa fa-angle-right"></i>';
}
$wr_class = 'breadcrumbs clear';
$wr_home  = __( 'Home', 'wr-nitro' );
$wr_blog  = __( 'Blog', 'wr-nitro' );
$wr_shop  = __( 'Shop', 'wr-nitro' );

// Get the query & post information
global $post, $wp_query;

// Get post category
$wr_category = get_the_category();

// Get gallery category
$wr_gallery_category = wp_get_post_terms( get_the_ID(), 'gallery_cat' );

// Get product category
$wr_product_cat = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

if ( $wr_product_cat ) {
	$wr_tax_title = $wr_product_cat->name;
}

$wr_output = '';

// Build the breadcrums
$wr_output .= '<ul class="' . esc_attr( $wr_class ) . '">';

// Do not display on the homepage
if ( ! is_front_page() ) {

	if ( is_home() ) {

		// Home page
		$wr_output .= '<li class="item home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_home . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';
		$wr_output .= '<li class="separator"> ' . $wr_blog . ' </li>';

	} elseif ( function_exists( 'is_shop' ) && is_shop() ) {

		$wr_output .= '<li class="item">' . $wr_shop . '</li>';

	} else if ( function_exists( 'is_product' ) && is_product() || function_exists( 'is_cart' ) && is_cart() || function_exists( 'is_checkout' ) && is_checkout()  || function_exists( 'is_account_page' ) && is_account_page() ) {

		$wr_output .= '<li class="item"><a href="' . esc_url( get_post_type_archive_link( 'product' ) ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_shop . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';
		$wr_output .= '<li class="item">' . get_the_title() . '</li>';

	} else if ( function_exists( 'is_product_category' ) && is_product_category() ) {

		$wr_output .= '<li class="item"><a href="' . esc_url( get_post_type_archive_link( 'product' ) ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_shop . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';
		$wr_output .= '<li class="item">' . $wr_tax_title . '</li>';

	} else if ( function_exists( 'is_product_tag' ) && is_product_tag() ) {

		$wr_output .= '<li class="item"><a href="' . esc_url( get_post_type_archive_link( 'product' ) ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_shop . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';
		$wr_output .= '<li class="item">' . $wr_tax_title . '</li>';

	} else if ( is_post_type_archive( 'nitro-gallery' ) ) {
		// Home page
		$wr_output .= '<li class="item home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_home . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';
		$wr_output .= '<li class="item current">' . $wr_nitro_options['gallery_archive_title'] . '</li>';
	} else if ( is_post_type_archive() ) {

		// Home page
		$wr_output .= '<li class="item home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_home . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		ob_start();
		post_type_archive_title();

		$wr_output .= '<li class="item current">' . ob_get_clean() . '</li>';

	} else if ( is_singular( 'nitro-gallery' ) ) {
		// Home page
		$wr_output .= '<li class="item home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_home . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		$wr_output .= '<li class="item"><a href="' . esc_url( get_post_type_archive_link( 'nitro-gallery' ) ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_nitro_options['gallery_archive_title'] . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		// First gallery category
		if ( ! empty( $wr_gallery_category ) ) {
			$wr_output .= '<li class="item"><a href="' . esc_url( get_category_link( $wr_gallery_category[0]->term_id ) ) . '" title="' . esc_attr( $wr_gallery_category[0]->name ) . '">' . $wr_gallery_category[0]->name . '</a></li>';
			$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';
		}

		$wr_output .= '<li class="item current">' . get_the_title() . '</li>';

	} else if ( is_single() ) {

		$post_type = get_post_type();
		// Home page
		$wr_output .= '<li class="item home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_home . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		if ( 'post' == $post_type && ! empty( $wr_category ) ) {
			// First post category
			$wr_output .= '<li class="item"><a href="' . esc_url( get_category_link( $wr_category[0]->term_id ) ) . '" title="' . esc_attr( $wr_category[0]->cat_name ) . '">' . $wr_category[0]->cat_name . '</a></li>';
			$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		} elseif ( 'nitro-gallery' == $post_type && ! empty( $wr_gallery_category ) ) {
			// First gallery category
			$wr_output .= '<li class="item"><a href="' . esc_url( get_category_link( $wr_gallery_category[0]->term_id ) ) . '" title="' . esc_attr( $wr_gallery_category[0]->name ) . '">' . $wr_gallery_category[0]->name . '</a></li>';
			$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';
		}

		$wr_output .= '<li class="item current">' . get_the_title() . '</li>';

	} else if ( is_archive() && is_tax() && ! is_category() && ! is_tag() ) {
		$tax_object = get_queried_object();

		// Home page
		$wr_output .= '<li class="item home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_home . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		if ( ! empty( $tax_object ) ) {
			$wr_output .= '<li class="item current">' . $tax_object->name . '</li>';
		}

	} else if ( is_category() ) {
		// Home page
		$wr_output .= '<li class="item home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_home . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		// Category page
		$wr_output .= '<li class="item current">' . single_cat_title( '', false ) . '</li>';

	} else if ( is_page() ) {

		$wr_output .= '<li class="item home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_home . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		// Standard page
		if ( $post->post_parent ) {

			// If child page, get parents
			$wr_anc = get_post_ancestors( $post->ID );

			// Get parents in the right order
			$wr_anc = array_reverse( $wr_anc );

			// Parent page loop
			foreach ( $wr_anc as $wr_ancestor ) {
				$wr_parents = '<li class="item"><a href="' . esc_url( get_permalink( $wr_ancestor ) ) . '" title="' . esc_attr( get_the_title( $wr_ancestor ) ) . '">' . get_the_title( $wr_ancestor ) . '</a></li>';
				$wr_parents .= '<li class="separator"> ' . $wr_sep . ' </li>';
			}

			// Display parent pages
			$wr_output .= $wr_parents;

			// Current page
			$wr_output .= '<li class="item current"> ' . get_the_title() . '</li>';

		} else {

			// Just display current page if not parents
			$wr_output .= '<li class="item current"> ' . get_the_title() . '</li>';

		}

	} else if ( is_tag() ) {

		// Tag page
		$wr_output .= '<li class="item home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_home . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		// Get tag information
		$wr_term_id  = get_query_var( 'tag_id' );
		$wr_taxonomy = 'post_tag';
		$wr_args     = 'include=' . $wr_term_id;
		$wr_terms    = get_terms( $wr_taxonomy, $wr_args );

		// Display the tag name
		if ( isset( $wr_terms[0]->name ) )
			$wr_output .= '<li class="item current">' . $wr_terms[0]->name . '</li>';

	} elseif ( is_day() ) {

		// Day archive

		// Year link
		$wr_output .= '<li class="item"><a href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '" title="' . esc_attr( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . esc_html__( ' Archives', 'wr-nitro' ) . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		// Month link
		$wr_output .= '<li class="item"><a href="' . esc_url( get_month_link( get_the_time('Y'), get_the_time( 'm' ) ) ) . '" title="' . esc_attr( get_the_time( 'M' ) ) . '">' . get_the_time( 'M' ) . esc_html__( ' Archives', 'wr-nitro' ) . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		// Day display
		$wr_output .= '<li class="item current"> ' . get_the_time('jS') . ' ' . get_the_time('M') . esc_html__( ' Archives', 'wr-nitro' ) . '</li>';

	} else if ( is_month() ) {

		// Month Archive

		// Year link
		$wr_output .= '<li class="item"><a href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '" title="' . esc_attr( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . esc_html__( ' Archives', 'wr-nitro' ) . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		// Month display
		$wr_output .= '<li class="item">' . get_the_time( 'M' ) . esc_html__( ' Archives', 'wr-nitro' ) . '</li>';

	} else if ( is_year() ) {

		// Display year archive
		$wr_output .= '<li class="item current">' . get_the_time('Y') . esc_html__( 'Archives', 'wr-nitro' ) . '</li>';

	} else if ( is_author() ) {

		// Home page
		$wr_output .= '<li class="item home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_home . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';

		// Get the author information
		global $author;
		$wr_userdata = get_userdata( $author );

		// Display author name
		$wr_output .= '<li class="item current">' . __( 'Author: ', 'wr-nitro' ) . '<a href="' . get_author_posts_url( $wr_userdata->ID, $wr_userdata->nice_name ) . '">' . $wr_userdata->display_name . '</a></li>';

	} else if ( get_query_var( 'paged' ) ) {

		// Paginated archives
		$wr_output .= '<li class="item current">' .  __( 'Page', 'wr-nitro' ) . ' ' . get_query_var( 'paged', 'wr-nitro' ) . '</li>';

	} else if ( is_search() ) {

		// Search results page
		$wr_output .= '<li class="item current">' .  __( 'Keyword: ', 'wr-nitro' ) . get_search_query() . '</li>';

	} elseif ( is_404() ) {

		// 404 page
		$wr_output .= '<li class="item home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $wr_home ) . '">' . $wr_home . '</a></li>';
		$wr_output .= '<li class="separator"> ' . $wr_sep . ' </li>';
		$wr_output .= '<li class="item current">' . __( 'Error 404', 'wr-nitro' ) . '</li>';
	}

}

$wr_output .= '</ul>';

echo wp_kses_post( $wr_output );
