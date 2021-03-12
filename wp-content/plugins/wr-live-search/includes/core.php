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
 * Define core class.
 */
class WR_Live_Search {
	/**
	 * Variable to hold class prefix supported for autoloading.
	 *
	 * @var  string
	 */
	protected static $prefix = 'WR_Live_Search_';

	/**
	 * Variable add inner join.
	 *
	 * @var  string
	 */
	protected static $inner_join = false;

	/**
	 * Initialize WR Live Search.
	 *
	 * @return  void
	 */
	public static function initialize() {
		// Register class autoloader.
		spl_autoload_register( array( __CLASS__, 'autoload' ) );

		// Register admin initialization for Live Search.
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );

		// Register admin menu for Live Search.
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );

		// Register Live Search widget.
		add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ) );

		// Load required assets.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ), 9999 );

		// Register Ajax action.
		add_action( 'wp_ajax_' . WR_LS       , array( __CLASS__, 'get_results' ) );
		add_action( 'wp_ajax_nopriv_' . WR_LS, array( __CLASS__, 'get_results' ) );

		// Add shortcode to print search form.
		add_shortcode( 'wr_live_search', array( 'WR_Live_Search_Shortcode', 'generate' ) );

		// Load plugin textdomain.
		add_action( 'init', array( __CLASS__, 'load_textdomain' ) );

		add_filter('pre_get_posts', array( __CLASS__, 'search_filter' ) );

	}

	/**
	 * Search filter for search in SKU
	 *
	 * @since 1.0.1
	 */
	public static function search_filter( $query ) {
		if( $query->is_search && ! empty( $_REQUEST['wrls_search_in'] ) ) {
			add_filter( 'posts_search', array( __CLASS__, 'posts_where_search_page' ), 9 );
			add_filter( 'posts_join', array( __CLASS__, 'posts_join' ), 9 );

			// Remove filter posts_where of WC plugin
			$search_in = empty( $_REQUEST['wrls_search_in'] ) ? array() : explode( ',' ,  $_REQUEST['wrls_search_in'] );

			if ( ! in_array( 'description', $search_in ) ) {
				self::remove_action( 'posts_where', array( 'WC_Query', 'search_post_excerpt' ), 10 );
			}

			self::$inner_join = true;
		} else {
			remove_filter( 'posts_search', array( __CLASS__, 'posts_where_search_page' ), 9 );
			remove_filter( 'posts_join', array( __CLASS__, 'posts_join' ), 9 );
		}
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.1
	 */
	public static function load_textdomain() {
		load_plugin_textdomain( 'wr-live-search', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Add settings for Live Search.
	 *
	 * @return  void
	 */
	public static function admin_init() {
		// Save settings if posted.
		global $pagenow;

		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == WR_LS && 'options-general.php' == $pagenow ) {

			// Load required assets.
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets_backend' ) );

			// Request save
			if( 'POST' == $_SERVER['REQUEST_METHOD'] )
				WR_Live_Search_Settings::save();

		}

		// Add settings link into plugins list.
		add_filter( 'plugin_action_links_' . WR_LS_BASENAME, array( 'WR_Live_Search_Settings', 'add_action_links' ) );
	}

	/**
	 * Add menu item for Live Search.
	 *
	 * @return  void
	 */
	public static function admin_menu() {
		add_options_page( 'WR Live Search', 'WR Live Search', 'manage_options', WR_LS, array( 'WR_Live_Search_Settings', 'show' ) );
	}

	/**
	 * Register Live Search widget.
	 *
	 * @return  void
	 */
	public static function widgets_init() {
		register_widget( 'WR_Live_Search_Widget' );
	}

	/**
	 * Enqueue required assets for Live Search.
	 *
	 * @return  void
	 */
	public static function enqueue_assets() {
		// Enqueue stylesheet.
		wp_enqueue_style( WR_LS, WR_LS_URL . 'assets/css/frontend.css' );

		// Enqueue script.
		wp_enqueue_script( WR_LS, WR_LS_URL . 'assets/js/frontend.js', array(), false, true );

		// Localize script.
		wp_localize_script(
			WR_LS,
			'wr_live_search',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php?action=' . WR_LS ),
				'plugin_url' => WR_LS_URL,
				'security'   => wp_create_nonce( WR_LS . '_nonce' ),
			)
		);
	}

	/**
	 * Enqueue required assets backend for Live Search.
	 *
	 * @return  void
	 */
	public static function enqueue_assets_backend() {
		// Enqueue stylesheet.
		wp_enqueue_style( WR_LS . '_backend', WR_LS_URL . 'assets/css/backend.css' );

		// Enqueue script.
		wp_enqueue_script( WR_LS . '_backend', WR_LS_URL . 'assets/js/backend.js', array(), false, true );

		// Localize script.
		wp_localize_script(
			WR_LS . '_backend',
			'wrls_settings',
			WR_Live_Search_Settings::get()
		);

		// Localize script.
		wp_localize_script(
			WR_LS . '_backend',
			'wrls_settings_default',
			WR_Live_Search_Settings::get( NULL, true )
		);
	}

	/**
	 * Get results for the requested keyword.
	 *
	 * @return  void
	*/
	public static function get_results() {
		// Verify nonce.
		if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], WR_LS . '_nonce' ) ) {
			wp_send_json( array( 'message' => __( 'Nonce verification failed.', 'wr-live-search' ) ) );
		}

		// Verify request data.
		if ( ! isset( $_POST['data'] ) || ! $_POST['data'] ) {
			wp_send_json( array( 'message' => __( 'Missing data.', 'wr-live-search' ) ) );
		}

		// Verify keyword.
		if ( ! isset( $_POST['data']['keyword'] ) || $_POST['data']['keyword'] == '' ) {
			// Nothing to find.
			exit;
		}

		// Prepare request data.
		$data = self::get_settings( $_POST['data'] );

		// Build query arguments to get results for the requested keyword.
		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'orderby'        => 'post_title',
			'order'          => 'ASC',
			'posts_per_page' => ( int ) $data['max_results'],
		);

		// Prepare suggestion setting.
		if ( $data['show_suggestion'] != 1 ) {
			$args['fields'] = 'ids';
		}

		$data['max_results'] = 1;

		// Prepare category list setting.
		if ( isset( $data['parent'] ) && ! empty( $data['parent'] ) ) {
			$args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => $data['parent'],
				)
			);
		}

		// Globalize Live Search settings.
		$GLOBALS['wr_live_search_settings'] = $data;

		// Register where filter.
		add_filter( 'posts_where', array( __CLASS__, 'posts_where' ) );
		add_filter( 'posts_groupby', array( __CLASS__, 'posts_groupby' ) );

		// Register join filter.
		if( isset( $data['search_in']['sku'] ) && $data['search_in']['sku'] == 1 ) {
			add_filter( 'posts_join', array( __CLASS__, 'posts_join' ) );
		}

		// Get thumbnail size
		$thumb_size = isset( $data['thumb_size'] ) ? $data['thumb_size'] : 50;

		// Query for results.
		$products = new WP_Query();
		$products = $products->query( $args );

		// Prepare return data.
		$return_data = array();

		if ( $products ) {
			foreach ( $products as $key => $product ) {
				$product = wc_get_product( $product );

				// Add property sku to products
				if( $data['show_suggestion'] == 1 && $data['search_in']['sku'] == 1 ) {
					$products[ $key ]->wr_sku = $product->get_sku();
				}

				if ( $product ) {
					$return_data['list_product'][] = array(
						'title' => $product->get_title(),
						'url'   => $product->get_permalink(),
						'image' => $product->get_image( array( $thumb_size ) ),
						'price' => $product->get_price_html(),
					);
				}
			}

			if ( $data['show_suggestion'] == 1 ) {
				foreach ( $products as $product ) {

					// Find keyword in title.
					if ( $data['search_in']['title'] == 1 ) {
						// Convert HTML tag and shortcode to space.
						$content_search = preg_replace( '/\\[[^\\]]*\\]|<[^>]*>/', ' ', $product->post_title );

						// Find keyword.
						$position_keyword = stripos( $content_search, $data['keyword'] );

						if ( $position_keyword !== false && $position_keyword + strlen( $data['keyword'] ) < strlen( $content_search ) ) {
							// Get suggestion of keyword in content.
							$return_data['suggestion'] = self::get_suggestion( $content_search, $data['keyword'] );

							break;
						}
					}

					// Find keyword in description.
					if ( $data['search_in']['description'] == 1 && ! isset( $return_data['suggestion'] ) ) {
						// Convert HTML tag and shortcode to space.
						$content_search = preg_replace( '/\\[[^\\]]*\\]|<[^>]*>/', ' ', $product->post_excerpt );

						// Find keyword.
						$position_keyword = stripos( $content_search, $data['keyword'] );

						if ( $position_keyword !== false && $position_keyword + strlen( $data['keyword'] ) < strlen( $content_search ) ) {
							// Get suggestion of keyword in content.
							$return_data['suggestion'] = self::get_suggestion( $content_search, $data['keyword'] );

							break;
						}
					}

					// Find keyword in content.
					if ( $data['search_in']['content'] == 1 && ! isset( $return_data['suggestion'] ) ) {
						// Convert HTML tag and shortcode to space.
						$content_search = preg_replace( '/\\[[^\\]]*\\]|<[^>]*>/', ' ', $product->post_content );

						// Find keyword.
						$position_keyword = stripos( $content_search, $data['keyword'] );

						if ( $position_keyword !== false && $position_keyword + strlen( $data['keyword'] ) < strlen( $content_search ) ) {
							// Get suggestion of keyword in content.
							$return_data['suggestion'] = self::get_suggestion( $content_search, $data['keyword'] );

							break;
						}
					}

					// Find keyword in sku.
					if ( $data['search_in']['sku'] == 1 && ! isset( $return_data['suggestion'] ) ) {
						// Convert HTML tag and shortcode to space.
						$content_search = preg_replace( '/\\[[^\\]]*\\]|<[^>]*>/', ' ', $product->wr_sku );

						// Find keyword.
						$position_keyword = stripos( $content_search, $data['keyword'] );

						if ( $position_keyword !== false && $position_keyword + strlen( $data['keyword'] ) < strlen( $content_search ) ) {
							// Get suggestion of keyword in content.
							$return_data['suggestion'] = self::get_suggestion( $content_search, $data['keyword'] );

							break;
						}
					}
				}
			}

			wp_send_json( $return_data );
		}

		wp_send_json( array( 'message' => __( 'No results.', 'wr-live-search' ) ) );
	}

	/**
	 * Prepare where clause for query statement.
	 *
	 * @param   string  $where  Current where clause.
	 *
	 * @return  string
	*/
	public static function posts_where( $where ) {
		global $wpdb, $wr_live_search_settings;

		// Convert all special characters in search keyword to HTML entities.
		$wr_live_search_settings['keyword'] = htmlentities($wr_live_search_settings['keyword'], ENT_QUOTES);

		// Refine slashes.
		$wr_live_search_settings['keyword'] = addslashes( stripslashes($wr_live_search_settings['keyword']) );

		// Replace all HTML entities with '%' character to search for similar phrases also.
		if ( preg_match_all('/&[^;]+;/', $wr_live_search_settings['keyword'], $matches, PREG_SET_ORDER) ) {
			foreach ($matches as $match) {
				$wr_live_search_settings['keyword'] = str_replace($match[0], '%', $wr_live_search_settings['keyword']);
			}
		}

		// Prepare search coverages.
		$columns = array();

		if ( isset( $wr_live_search_settings['search_in']['title'] ) && $wr_live_search_settings['search_in']['title'] == 1 ) {
			$columns[] = ' ' . $wpdb->posts . '.post_title LIKE "%' . sanitize_text_field( $wr_live_search_settings['keyword'] ) . '%" ';
		}

		if ( isset( $wr_live_search_settings['search_in']['description'] ) && $wr_live_search_settings['search_in']['description'] == 1 ) {
			$columns[] = ' ' . $wpdb->posts . '.post_excerpt LIKE "%' . sanitize_text_field( $wr_live_search_settings['keyword'] ) . '%" ';
		}

		if ( isset( $wr_live_search_settings['search_in']['content'] ) && $wr_live_search_settings['search_in']['content'] == 1 ) {
			$columns[] = ' ' . $wpdb->posts . '.post_content LIKE "%' . sanitize_text_field( $wr_live_search_settings['keyword'] ) . '%" ';
		}

		if ( isset( $wr_live_search_settings['search_in']['sku'] ) && $wr_live_search_settings['search_in']['sku'] == 1 ) {
			$columns[] = '( ' . $wpdb->postmeta . '.meta_key = "_sku" AND '  . $wpdb->postmeta . '.meta_value LIKE "%' . sanitize_text_field( $wr_live_search_settings['keyword'] ) . '%" )';
		}

		if ( count( $columns ) ) {
			$where .= ' AND ( ' . implode( ' OR ', $columns ) . ' ) ';
		}

		return $where;
	}

	/**
	 * Prepare groupby clause for query statement.
	 *
	 * @param   string  $groupby  Current groupby clause.
	 *
	 * @return  string
	*/
	public static function posts_groupby( $groupby ) {
		global $wpdb;

	    $groupby = "{$wpdb->posts}.ID";

	    return $groupby;
	}

	/**
	 * Prepare where clause for query statement on search page.
	 *
	 * @param   string  $where  Current where clause.
	 *
	 * @return  string
	*/
	public static function posts_where_search_page( $where ) {
		global $wpdb;

		// Convert all special characters in search keyword to HTML entities.
		$keyword = htmlentities($wpdb->esc_like( get_query_var('s') ), ENT_QUOTES);

		// Refine slashes.
		$keyword = addslashes( stripslashes($keyword) );

		// Replace all HTML entities with '%' character to search for similar phrases also.
		if ( preg_match_all('/&[^;]+;/', $keyword, $matches, PREG_SET_ORDER) ) {
			foreach ($matches as $match) {
				$keyword = str_replace($match[0], '%', $keyword);
			}
		}

		// Prepare search coverages.
		$columns = array();
		$search_in = empty( $_REQUEST['wrls_search_in'] ) ? array() : explode( ',' , $_REQUEST['wrls_search_in'] );
		$where_merge = array();

		if ( in_array( 'title', $search_in ) ) {
			$where_merge[] = "(" . $wpdb->posts . ".post_title LIKE '%" . $keyword . "%')";
		}

		if ( in_array( 'description', $search_in ) ) {
			$where_merge[] = "(" . $wpdb->posts . ".post_excerpt LIKE '%" . $keyword . "%')";
		}

		if ( in_array( 'content', $search_in ) ) {
			$where_merge[] = "(" . $wpdb->posts . ".post_content LIKE '%" . $keyword . "%')";
		}

		if ( in_array( 'sku', $search_in ) ) {
			$where_merge[] = '( wrls_meta.meta_key = "_sku" AND wrls_meta.meta_value LIKE "%' . $keyword . '%" )';
		}

		$where = ' AND ((' . implode( ' OR ', $where_merge ) . ')) ';

		return $where;
	}

	/**
	 * Prepare join clause for query statement.
	 *
	 * @param   string  $join  Current join clause.
	 *
	 * @return  string
	*/
	public static function posts_join( $join ) {
		global $wpdb;

		if( strpos( $join, $wpdb->postmeta ) === false ) {
			$join .= ' INNER JOIN ' . $wpdb->postmeta . ' ON ( ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ) ';
		}

		if( self::$inner_join ) {
			$join .= ' INNER JOIN ' . $wpdb->postmeta . ' AS wrls_meta ON ( ' . $wpdb->posts . '.ID = wrls_meta.post_id ) ';
		}

		return $join;
	}

	/**
	 * Get suggestion of keyword in content.
	 *
	 * @param   string  $content  Content.
	 * @param   string  $keyword  Keyword.
	 *
	 * @return  string
	*/
	public static function get_suggestion( $content, $keyword ) {
		// Get the postion of the first keyword in content.
		$index_keyword = stripos( $content, $keyword );

		// Strip the content from that keyword postion.
		$post_title = substr( $content, ( $index_keyword + strlen( $keyword ) ), 40 );

		// Get the postion of the last keyword in content.
		$index_keyword = stripos( $content, $post_title ) + strlen( $post_title );

		// Prepare the title.
		for ( $i = 0; $i < 30; $i++ ) {
			$post_title_add = substr( $content, $index_keyword + $i, 1 );

			if ( $post_title_add == ' ' ) {
				break;
			} else {
				$post_title .= $post_title_add;
			}
		}

		return $keyword . $post_title;
	}

	/**
	 * Get Live Search settings.
	 *
	 * @param   array  $settings  Current settings.
	 *
	 * @return  array
	 */
	public static function get_settings( $settings = null, $default = false ) {
		return WR_Live_Search_Settings::get( $settings, $default );
	}

	/**
	 * Method to autoload class declaration file.
	 *
	 * @param   string  $class_name  Name of class to load declaration file for.
	 *
	 * @return  mixed
	 */
	public static function autoload( $class_name ) {
		// Verify class prefix.
		if ( 0 !== strpos( $class_name, self::$prefix ) ) {
			return false;
		}

		// Generate file path from class name.
		$base = WR_LS_PATH . 'includes/';
		$path = strtolower( str_replace( '_', '/', substr( $class_name, strlen( self::$prefix ) ) ) );

		// Check if class file exists.
		$standard    = $path . '.php';
		$alternative = $path . '/' . basename( $path ) . '.php';

		while ( true ) {
			// Check if file exists in standard path.
			if ( @is_file( $base . $standard ) ) {
				$exists = $standard;

				break;
			}

			// Check if file exists in alternative path.
			if ( @is_file( $base . $alternative ) ) {
				$exists = $alternative;

				break;
			}

			// If there is no more alternative file, quit the loop.
			if ( false === strrpos( $standard, '/' ) || 0 === strrpos( $standard, '/' ) ) {
				break;
			}

			// Generate more alternative files.
			$standard    = preg_replace( '#/([^/]+)$#', '-\\1', $standard );
			$alternative = dirname( $standard ) . '/' . substr( basename( $standard ), 0, -4 ) . '/' . basename( $standard );
		}

		// Include class declaration file if exists.
		if ( isset( $exists ) ) {
			return include_once $base . $exists;
		}

		return false;
	}

	/**
	 * Remove action
	 *
	 * @return  array()
	 */
	public static function remove_action( $action, $class, $priority ) {
		global $wp_filter;

		if ( ! empty( $wp_filter[ $action ]->callbacks[ $priority ] ) ) {
			foreach( $wp_filter[ $action ]->callbacks[ $priority ] as $key => $val ) {
				if( ! empty( $val['function'][0] ) && ! empty( $val['function'][1] ) && is_object( $val['function'][0] ) && get_class( $val['function'][0] ) == $class[0] && $val['function'][1] == $class[1] ) {
					if( count( $wp_filter[ $action ]->callbacks[ $priority ] ) == 1 ) {
						unset( $wp_filter[ $action ]->callbacks[ $priority ] );
					} else {
						unset( $wp_filter[ $action ]->callbacks[ $priority ][ $key ] );
					}
				}
			}
		}
	}

}

/**
 * Generate HTML for Live Search function.
 *
 * @param   array  $atts attributes.
 *
 * @return  void
 */
function wr_live_search( $attr = array() ) {
	return WR_Live_Search_Shortcode::generate( $attr );
}
