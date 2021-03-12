<?php
/**
 * Plugin Name: WR Live Search
 * Plugin URI:  http://www.woorockets.com
 * Description: WR Live Search is a WordPress plugin developed by WooRockets. It help you create a search box that will display search results instantly with rich content like thumbnail images, prices, title, description... You can also choose to search from WooCommerce products or blog content.
 * Version:     1.0.12
 * Author:      WooRockets <admin@woorockets.com>
 * Author URI:  http://www.woorockets.com
 * License:     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wr-live-search
 * WC tested up to: 4.6.0
 */

// Define plugin textdomain.
define( 'WR_LS', 'wr-live-search' );

// Define path to plugin directory.
define( 'WR_LS_PATH', plugin_dir_path( __FILE__ ) );

// Define URL to plugin directory.
define( 'WR_LS_URL', plugin_dir_url( __FILE__ ) );

// Define plugin base file.
define( 'WR_LS_BASENAME', plugin_basename( __FILE__ ) );

// Load the core class.
require_once WR_LS_PATH . 'includes/core.php';

// Instantiate an object of the core class.
WR_Live_Search::initialize();
