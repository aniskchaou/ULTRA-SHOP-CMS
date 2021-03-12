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

// Get theme options
$wr_nitro_options = WR_Nitro::get_options();

if ( ! empty( $wr_nitro_options['sidebar_before_blog_content'] ) && is_active_sidebar( $wr_nitro_options['sidebar_before_blog_content'] ) ) {
	echo '<div class="mgt30 mgb30 sidebar-before-blog">';
		dynamic_sidebar( $wr_nitro_options['sidebar_before_blog_content'] );
	echo '</div>';
}