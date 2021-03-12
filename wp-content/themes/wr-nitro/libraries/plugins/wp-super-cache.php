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

/**
 * Custom function check mobile for plugin wp super cache.
 *
 * @package  WR_Theme
 * @since    1.1.8
 */
function wr_is_mobile() {
	if ( empty( wr_get_server_param( 'HTTP_USER_AGENT' ) ) ) {
		$is_mobile = false;
	} elseif ( strpos( wr_get_server_param( 'HTTP_USER_AGENT' ), 'Mobile' ) !== false // many mobile devices (all iPhone, iPad, etc.)
		|| strpos( wr_get_server_param( 'HTTP_USER_AGENT' ), 'Android' ) !== false
		|| strpos( wr_get_server_param( 'HTTP_USER_AGENT' ), 'Silk/' ) !== false
		|| strpos( wr_get_server_param( 'HTTP_USER_AGENT' ), 'Kindle' ) !== false
		|| strpos( wr_get_server_param( 'HTTP_USER_AGENT' ), 'BlackBerry' ) !== false
		|| strpos( wr_get_server_param( 'HTTP_USER_AGENT' ), 'Opera Mini' ) !== false
		|| strpos( wr_get_server_param( 'HTTP_USER_AGENT' ), 'Opera Mobi' ) !== false ) {
			$is_mobile = true;
	} else {
		$is_mobile = false;
	}

	return $is_mobile;
}

if ( wr_is_mobile() ) {
	global $wp_super_cache_late_init, $cache_enabled;
	$wp_super_cache_late_init = 1;
	$cache_enabled = 0;
}
