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

if ( ! isset( $_GET['screen'] ) || empty( $_GET['screen'] ) || ! isset( $_GET['load'] ) || empty( $_GET['load'] ) ) {
	exit;
}

// Load assets compression library.
$wr_nitro_path = explode( '/', str_replace( '\\', '/', __FILE__ ) );

array_splice( $wr_nitro_path, -2 );

$wr_nitro_path = implode( '/', $wr_nitro_path );

require_once $wr_nitro_path . '/woorockets/includes/assets.php';

$assets_screen = WR_Nitro_Assets::scripts( $_GET['screen'], NULL, false );

// Validate screen.
if ( ! $assets_screen ) {
	exit;
}

// Load minification library.
if ( ! class_exists( 'JShrink_Minifier' ) ) {
	include_once $wr_nitro_path . '/libraries/jshrink/minifier.php';
}

// Get all requested scripts.
$wr_nitro_load = array_unique( explode( ',', $_GET['load'] ) );

// Define required constants.
$wr_wp_path = explode( '/', str_replace( '\\', '/', __FILE__ ) );

array_splice( $wr_wp_path, -5 );

$wr_wp_path = implode( '/', $wr_wp_path );

define( 'ABSPATH', $wr_wp_path . '/' );

// Define function to get site URL.
function site_url() {
	// Strip query string out of request URI.
	$request_uri = current( explode( '?', wr_get_server_param( 'REQUEST_URI' ) ) );

	$wp_content = current( explode( '/themes/', $request_uri ) );
	$wp_content = explode( '/', $wp_content );
	$wp_content = '/' . end( $wp_content );

	// Strip script path out of request URI.
	$request_uri = current( explode( $wp_content, $request_uri ) );

	// Return site URL.
	return wr_get_server_param( 'REQUEST_SCHEME' ) . '://' . wr_get_server_param( 'HTTP_HOST' ) . $request_uri;
}

// Disable error reporting.
error_reporting( 0 );

// Loop thru all requested scripts to read content.
foreach ( $wr_nitro_load as $k => $handle ) {
	$assets_handle = WR_Nitro_Assets::scripts( $_GET['screen'], $handle, false );
	$source = $assets_handle ? $assets_handle : '';

	if ( empty( $source ) ) {
		unset( $wr_nitro_load[ $k ] );

		continue;
	}

	// Read and compress the content of this script file.
	$wr_nitro_load[ $k ] = JShrink_Minifier::minify(
		call_user_func( 'file_' . 'get' . '_contents', ABSPATH . $source ),
		array( 'flaggedComments' => false )
	);
}

$wr_nitro_load = implode( "\n", $wr_nitro_load );

// Check if compressed content is requested?
$wr_nitro_compress = ( isset( $_GET['c'] ) && $_GET['c'] );
$wr_nitro_force_gzip = ( $wr_nitro_compress && 'gzip' == $_GET['c'] );

// Define seconds for browser to cache content.
$wr_nitro_expires_offset = 31536000;

// Set necessary headers.
header( 'Content-Type: application/javascript; charset=UTF-8' );
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $wr_nitro_expires_offset ) . ' GMT' );
header( 'Cache-Control: public, max-age=' . $wr_nitro_expires_offset );

if (
	$wr_nitro_compress
	&& ! ini_get( 'zlib.output_compression' )
	&& 'ob_gzhandler' != ini_get( 'output_handler' )
	&& wr_get_server_param( 'HTTP_ACCEPT_ENCODING' )
) {
	header( 'Vary: Accept-Encoding' );

	if (
		false !== stripos( wr_get_server_param( 'HTTP_ACCEPT_ENCODING' ), 'deflate' )
		&& function_exists( 'gzdeflate' )
		&& ! $wr_nitro_force_gzip
	) {
		header( 'Content-Encoding: deflate' );

		$wr_nitro_load = gzdeflate( $wr_nitro_load, 3 );
	} elseif (
		false !== stripos( wr_get_server_param( 'HTTP_ACCEPT_ENCODING' ), 'gzip' )
		&& function_exists( 'gzencode' )
	) {
		header( 'Content-Encoding: gzip' );

		$wr_nitro_load = gzencode( $wr_nitro_load, 3 );
	}
}

// Print combined and compressed content.
function esc_html( $text ) {
	return $text;
}

echo esc_html( $wr_nitro_load );

exit;
