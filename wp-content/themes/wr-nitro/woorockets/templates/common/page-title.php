<?php
/**
 * @version    1.0
 * @package    Nitro
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 * Render page title.
 */

// Get options
$wr_nitro_options = WR_Nitro::get_options();

if ( ( function_exists( 'is_shop' ) && is_shop() ) || ( function_exists( 'is_product_category' ) && is_product_category() ) || ( function_exists( 'is_product_tag' ) && is_product_tag() ) ) {

	$show_page_title = $wr_nitro_options['wc_archive_page_title'];

} elseif ( ( function_exists( 'is_product' ) && is_product() ) && $wr_nitro_options['wc_single_title'] ) {

	$show_page_title = $wr_nitro_options['wc_single_title'];

} else {

	$show_page_title = $wr_nitro_options['wr_page_title'];

}

if ( $show_page_title ) {
	WR_Nitro_Render::get_template( 'page/title/' . $wr_nitro_options['wr_page_title_layout'] );
}

// Renber sidebar after page title
if ( ! empty( $wr_nitro_options['sidebar_after_page_title'] ) && is_active_sidebar( $wr_nitro_options['sidebar_after_page_title'] ) ) {
	echo '<div class="mgt30 mgb30 sidebar-after-page-title">';
		echo '<div class="container">';
			dynamic_sidebar( $wr_nitro_options['sidebar_after_page_title'] );
		echo '</div>';
	echo '</div>';
}