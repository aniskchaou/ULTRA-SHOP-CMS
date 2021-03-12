<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */
$wr_output = '';
$wr_nitro_options = WR_Nitro::get_options();

// Get blog style
$wr_style = $wr_nitro_options['blog_style'];

if ( 'masonry' == $wr_style ) {
	$wr_output .= '<h4 class="entry-title" ' . WR_Nitro_Helper::schema_metadata( array( 'context' => 'entry_title', 'echo' => false ) ) . '><a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></h4>';
	$wr_output .= '<p class="entry-text">' . WR_Nitro_Helper::get_excerpt( 15, '...' ) . '</p>';
	$wr_output .= '<div class="entry-meta mgt10 pdt10 fc jcsb">';
		$wr_output .= WR_Nitro_Helper::get_author();
		$wr_output .= WR_Nitro_Helper::get_posted_on();
	$wr_output .= '</div>';
} else {
	$wr_output .= '<h4 class="entry-title" ' . WR_Nitro_Helper::schema_metadata( array( 'context' => 'entry_title', 'echo' => false ) ) . '><a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></h4>';
	$wr_output .= '<div class="entry-meta mgt10 mgb10">';
		$wr_output .= WR_Nitro_Helper::get_author();
		$wr_output .= WR_Nitro_Helper::get_posted_on();
	$wr_output .= '</div>';
	$wr_output .= '<p class="entry-text">' . WR_Nitro_Helper::get_excerpt( 35, '...' ) . '</p>';
	$wr_output .= WR_Nitro_Helper::read_more();
}
echo '' . $wr_output;
