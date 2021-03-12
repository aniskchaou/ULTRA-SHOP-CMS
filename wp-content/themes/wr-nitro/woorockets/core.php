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

// Prevent direct access to this file
defined( 'ABSPATH' ) || die( 'Direct access to this file is not allowed.' );

/**
 * Core class.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro {
	/**
	 * Define theme version.
	 *
	 * @var  string
	 */
	const VERSION = '1.7.9';

	/**
	 * Define valid class prefix for autoloading.
	 *
	 * @var  string
	 */
	protected static $prefix = 'WR_Nitro_';

	/**
	 * Variable to hold page options.
	 *
	 * @var  array
	 */
	protected static $page_options = array();

	/**
	 * Initialize WR Nitro.
	 *
	 * @return  void
	 */
	public static function initialize() {
		// Register class autoloader.
		spl_autoload_register( array( __CLASS__, 'autoload' ) );

		// Include function plugins if not include.
		self::include_function_plugins();

		// Register action to prepare Nitro options.
		add_action( 'wp', array( __CLASS__, 'get_options' ) );

		// Register necessary actions.
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );

		// Add custom css to admin
		add_action( 'admin_head', array( __CLASS__, 'admin_custom_css' ) );

		// Migrate theme customize options from old theme name.
		if ( $old_options = get_option( 'theme_mods_nitro' ) ) {
			update_option( 'theme_mods_wr-nitro', $old_options );
			delete_option( 'theme_mods_nitro' );
		}

		// Plug into WordPress and supported 3rd-party plugins.
		WR_Nitro_Pluggable::initialize();
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
		$base = get_template_directory() . '/woorockets/includes/';
		$path = strtolower( str_replace( '_', '/', substr( $class_name, strlen( self::$prefix ) ) ) );

		// Check if class file exists.
		$standard    = $path . '.php';
		$alternative = $path . '/' . current( array_slice( explode( '/', str_replace( '\\', '/', $path ) ), -1 ) ) . '.php';

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
			$alternative = implode( '/', array_slice( explode( '/', str_replace( '\\', '/', $standard ) ), 0, -1 ) ) . '/' . substr( current( array_slice( explode( '/', str_replace( '\\', '/', $standard ) ), -1 ) ), 0, -4 ) . '/' . current( array_slice( explode( '/', str_replace( '\\', '/', $standard ) ), -1 ) );
		}

		// Include class declaration file if exists.
		if ( isset( $exists ) ) {
			return include_once $base . $exists;
		}

		return false;
	}

	/**
	 * Include function plugins if not include.
	 *
	 * @return  void
	 *
	 * @since  1.1.8
	 *
	 */
	public static function include_function_plugins() {
        if ( ! function_exists( 'is_' . 'plugin' . '_active' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
	}

	/**
	 * Get header data
	 *
	 * @return  array
	 */
	public static function get_header() {
		// Get id page
		if ( is_home() ) {
			$id = get_option( 'page_for_posts' );
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$id = get_option( 'woocommerce_shop_page_id' );
		} elseif( is_page() ) {
			$id = get_the_ID();
		}

		$header_id = 0;

		$wr_nitro_options = WR_Nitro::get_options();

		$wr_is_wpml_activated = call_user_func( 'is_' . 'plugin' . '_active', 'sitepress-multilingual-cms/sitepress.php' );
		$wr_is_pll_activated = call_user_func( 'is_' . 'plugin' . '_active', 'polylang/polylang.php' );

		// Get header ID from page option, before get header data from header ID
		if ( isset( $id ) && $id ) {

			// Check user global setting
			if( ! $wr_nitro_options['use_global'] ) {
				$header_id = (int) $wr_nitro_options['header_layout'];

				// Compatible with WPML
				if( $wr_is_wpml_activated || $wr_is_pll_activated ){
					$header_id = icl_object_id( $header_id, 'post', true, ICL_LANGUAGE_CODE );
				}

				if( $header_id > 0 ) {
					$header_data = new WP_Query( array(
						'post__in' => array( $header_id ),
						'post_type' => 'header_builder',
						'post_status' => 'publish',
						'suppress_filters' => true,
					) );

					if ( $header_data->have_posts() ) {
						$header_data = current( $header_data->posts );
					} else {
						return 'empty';
					}
				} else {
					return 'empty';
				}
			} elseif( isset( $wr_nitro_options['header_layout'] ) && (int) $wr_nitro_options['header_layout'] > 0 ) {
				$header_id   = (int) $wr_nitro_options['header_layout'];

				// Compatible with WPML
				if( $wr_is_wpml_activated || $wr_is_pll_activated ){
					$header_id = icl_object_id( $header_id, 'post', true, ICL_LANGUAGE_CODE );
				}

				$header_data = new WP_Query( array(
					'post__in' => array( $header_id ),
					'post_type' => 'header_builder',
					'post_status' => 'publish',
					'suppress_filters' => true,
				) );

				if ( $header_data->have_posts() ) {
					$header_data = current( $header_data->posts );
				} else {
					$header_data = null;
				}
			}
		}

		// Get header data deafault
		if( ! isset( $header_data ) || !$header_data ) {
			if ( isset( $wr_nitro_options['header_layout'] ) && (int) $wr_nitro_options['header_layout'] > 0  ) {
				$header_id   = (int) $wr_nitro_options['header_layout'];

				// Compatible with WPML
				if( $wr_is_wpml_activated || $wr_is_pll_activated ){
					$header_id = icl_object_id( $header_id, 'post', true, ICL_LANGUAGE_CODE );
				}

				$header_data = new WP_Query( array(
					'post__in' => array( $header_id ),
					'post_type' => 'header_builder',
					'post_status' => 'publish',
					'suppress_filters' => true,
				) );

				if ( $header_data->have_posts() ) {
					$header_data = current( $header_data->posts );
				} else {
					$header_data = null;
				}
			} else {
				$header_data = new WP_Query( array(
					'post_type'      => 'header_builder',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'suppress_filters' => true,
					'meta_query'  => array(
						array(
							'key'   => 'hb_status',
							'value' => 'default'
						),
					),
				));

				if( $header_data->post_count ) {
					$header_data = (array) $header_data->posts[0];
			 		$header_id   = intval( $header_data['ID'] );

			 		// Compatible with WPML
			 		if( $wr_is_wpml_activated || $wr_is_pll_activated ){
						$header_id = icl_object_id( $header_id, 'post', true, ICL_LANGUAGE_CODE );
						$header_data = new WP_Query( array(
							'post__in' => array( $header_id ),
							'post_type' => 'header_builder',
							'post_status' => 'publish',
							'suppress_filters' => true,
						) );

						if ( $header_data->have_posts() ) {
							$header_data = current( $header_data->posts );
						} else {
							$header_data = null;
						}
					}
				} else {
					$header_data = null;
				}
			}
		}

		// Get header data normal
		if ( ! isset( $header_data ) || ! $header_data ) {
			return 'not_select_defaut';
		}

		$header_data       = ( array ) $header_data;
		$header_data       = ! empty( $header_data['post_content'] ) ? ( is_serialized( $header_data['post_content'] ) ? unserialize( $header_data['post_content'] ) : json_decode( $header_data['post_content'], TRUE ) ) : array();
		$header_data['id'] = $header_id;
		$header_data       = json_encode( $header_data );

		return $header_data;
	}

	/**
	 * Method to prepare and return active Nitro options.
	 *
	 * @return  array
	 */
	public static function get_options() {
		// Get theme options.
		$options = WR_Nitro_Customize::get_options();

		if ( ! is_admin() ) {
			// Get page options.
			$page_id = get_the_ID();

			if ( $page_id ) {
				// Check if global options should be used or not?
				$use_global = get_post_meta( $page_id, 'global_opt', true );
				$use_global = ( false === $use_global || '' === $use_global ) ? 1 : intval( $use_global );

				if ( ! $use_global ) {
					if ( ! isset( self::$page_options[ $page_id ] ) ) {
						// Get options for current page.
						$meta_data = get_post_meta( $page_id, '' );

						if ( $meta_data ) {
							foreach ( $meta_data as $key => $value ) {
								if ( 'wr_' == substr( $key, 0, 3 ) || $key == 'header_layout' ) {
									self::$page_options[ $page_id ][ $key ] = maybe_unserialize( $value[0] );
								}
							}
						}

						if ( isset( self::$page_options[ $page_id ] ) ) {
							// Mark page options.
							self::$page_options[ $page_id ]['use_global'] = 0;
						}
					}
				}
			}

			if ( is_customize_preview() ) {
				// Detect the type of the current page.
				static $type;

				if ( ! isset( $type ) ) {
					if ( is_front_page() && ! is_home() ) {
						$type = 'page';
					}

					elseif ( is_home() ) {
						$type = 'blog';
						$view = 'blog_list';
					}

					elseif ( ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) || ( function_exists( 'is_product' ) && is_product() ) || ( function_exists( 'is_checkout' ) && is_checkout() ) || ( function_exists( 'is_cart' ) && is_cart() ) ) {
						$type = 'woocommerce';
						$view = 'wc_archive';

						if ( function_exists( 'is_product' ) && is_product() ) {
							$view = 'wc_single';
						} elseif ( function_exists( 'is_checkout' ) && is_checkout() ) {
							$view = 'wc_checkout';
						} elseif ( function_exists( 'is_cart' ) && is_cart() ) {
							$view = 'wc_cart';
						}
					}

					elseif ( is_post_type_archive( 'post' ) ) {
						$type = 'blog';
						$view = 'blog_list';
					}

					elseif ( is_post_type_archive( 'nitro-gallery' ) ) {
						$type = 'gallery';
						$view = 'gallery_archive';
					}

					elseif ( is_singular( 'post' ) ) {
						$type = 'blog';
						$view = 'blog_single';
					}

					elseif ( is_singular( 'nitro-gallery' ) ) {
						$type = 'gallery';
						$view = 'gallery_single';
					}

					elseif ( is_page( 'blog' ) ) {
						$type = 'blog';
						$view = 'blog_list';
					}

					elseif ( is_category() ) {
						$type = 'blog';
						$view = 'blog_list';
					}

					elseif ( is_page() ) {
						$type = 'page';
					}

					// Pass the type of the current page to client-side script.
					wp_localize_script(
						'wr-customize-actions',
						'wr_customize_previewing',
						array(
							'type'      => $type,
							'view'      => isset( $view ) ? $view : '',
							'message'   => esc_html__( 'Options in this panel does not affect the current page and has been disabled. You can go to &#39;Expert Mode&#39; to be able to edit options in all disabled panel.', 'wr-nitro' ),
							'btn_label' => esc_html__( 'Expert Mode', 'wr-nitro' ),
						)
					);

					if ( 'page' == $type ) {
						// If current screen has own page options, set customize disabled message.
						if ( $page_id && isset( self::$page_options[ $page_id ] ) && ! self::$page_options[ $page_id ]['use_global'] ) {
							wp_localize_script(
								'wr-customize-actions',
								'wr_customize_disable',
								array(
									'disabled'     => true,
									'message'      => '<span class="dashicons dashicons-warning"></span><span class="message-tooltip">' . esc_html__( 'This page has some custom page options that overrides settings defined in Theme Customizer; hence while you might see live preview of setting change here, it might not effective at the public site.', 'wr-nitro' ) . '</span>',
									'page_options' => array_keys( self::$page_options[ $page_id ] )
								)
							);
						}

						// Pass action links to client-side.
						static $passed_links;

						if ( ! isset( $passed_links ) ) {
							wp_localize_script(
								'wr-customize-actions',
								'wr_customize_page_action',
								array(
									'customize' => '',
									'edit_page' => ( ! is_post_type_archive() ? sprintf( __( '<a target="_blank" rel="noopener noreferrer" href="%s"><span class="dashicons dashicons-edit"></span> Edit Page</a>', 'wr-nitro' ), esc_url( admin_url( 'post.php?post=' . $page_id . '&action=edit' ) ) ) : '' ),
								)
							);

							$passed_links = true;
						}
					}

					// Pass link to edit page header to client-side.
					static $passed_header;

					if ( ! isset( $passed_header ) ) {
						if ( WR_Nitro_Header_Builder::prop( 'id' ) ) {
							wp_localize_script(
								'wr-customize-actions',
								'wr_customize_header_action',
								array(
									'edit_header' => sprintf( __( '<a target="_blank" rel="noopener noreferrer" href="%s"><span class="dashicons dashicons-edit"></span> Edit Header</a>', 'wr-nitro' ), esc_url( admin_url( 'post.php?post=' . WR_Nitro_Header_Builder::prop( 'id' ) . '&action=edit' ) ) ),
								)
							);

							$passed_header = true;
						}
					}
				}
			}
		}

		if ( ! doing_action( 'wp' ) ) {
			// Prepare options to return.
			if ( isset( $page_id ) && isset( self::$page_options[ $page_id ] ) ) {
				$options = array_merge( $options, self::$page_options[ $page_id ] );
			}

			return apply_filters( 'wr_nitro_options', $options );
		}
	}

	/**
	 * Creates a new top level menu section.
	 *
	 * @return  void
	 */
	public static function admin_menu() {
		global $submenu, $pagenow;

		if ( current_user_can( 'edit_theme_options' ) ) {
			$menu = 'add_menu_' . 'page';
			// Add Nitro root menu item.
			$menu(
				esc_html__( 'Nitro', 'wr-nitro' ),
				esc_html__( 'Nitro', 'wr-nitro' ),
				'manage_options',
				'wr-intro',
				array( 'WR_Nitro_Welcome', 'html' ),
				get_template_directory_uri() . '/assets/woorockets/images/admin/logo.png',
				2
			);

			// Add Nitro submenu items.
			$sub_menu = 'add_submenu_' . 'page';
			$sub_menu(
				'wr-intro',
				esc_html__( 'Nitro Dashboard', 'wr-nitro' ),
				esc_html__( 'Dashboard', 'wr-nitro' ),
				'manage_options',
				'wr-intro',
				array( 'WR_Nitro_Welcome', 'html' )
			);

			// Sort submenu submenu of wr intro
			if( isset( $submenu['wr-intro'][0][2] ) && $submenu['wr-intro'][0][2] == 'edit.php?post_type=header_builder' ) {
				$header = $submenu['wr-intro'][0];
				$welcome = $submenu['wr-intro'][1];

				$submenu['wr-intro'][0] = $welcome;
				$submenu['wr-intro'][1] = $header;
			}
		}

		// Redirect to Nitro welcome page after activating theme.
		if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) && $_GET['activated'] == 'true' ) {

			// Add do action
			do_action( 'wr_activate' );

			// Redirect
			wp_redirect( admin_url ( 'admin.php?page=wr-intro' ) );
		}
	}

	/**
	 * This function transforms the php.ini notation for numbers (e.g. 2M) to an integer.
	 *
	 * @param   string  $size
	 *
	 * @return  int
	 */
	public static function str_to_num( $size ) {
		$l   = substr( $size, -1 );
		$ret = substr( $size, 0, -1 );

		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			break;

			case 'T':
				$ret *= 1024;
			break;

			case 'G':
				$ret *= 1024;
			break;

			case 'M':
				$ret *= 1024;
			break;

			case 'K':
				$ret *= 1024;
			break;
		}

		return $ret;
	}

	/**
	 * Method to check an uploaded file for potential security risks.
	 *
	 * @param   array  $file     An uploaded file descriptor as stored in $_FILES.
	 * @param   array  $options  Verification options.
	 *
	 * @return  boolean
	 */
	public static function check_upload( $file, $options = array() ) {

		// Prepare options.
		$options = wp_parse_args( $options, array(
			'null_byte'            => true,  // Check for null byte in file name.
			'forbidden_extensions' => array( // Check if file extension contains forbidden string (e.g. php matched .php, .xxx.php, .php.xxx and so on).
				'php', 'phps', 'php5', 'php3', 'php4', 'inc', 'pl', 'cgi', 'fcgi', 'java', 'jar', 'py'
			),
			'php_tag_in_content'  => true,  // Check if file content contains <?php tag.
			'shorttag_in_content' => true,  // Check if file content contains short open tag.
			'shorttag_extensions' => array( // File extensions that need to check if file content contains short open tag.
				'inc', 'phps', 'class', 'php3', 'php4', 'php5', 'txt', 'dat', 'tpl', 'tmpl'
			),
			'fobidden_ext_in_content' => true,  // Check if file content contains forbidden extensions.
			'fobidden_ext_extensions' => array( // File extensions that need to check if file content contains forbidden extensions.
				'zip', 'rar', 'tar', 'gz', 'tgz', 'bz2', 'tbz'
			),
		) );

		// Check file name.
		$temp_name     = $file['tmp_name'];
		$intended_name = $file['name'    ];

		// Check for null byte in file name.
		if ( $options['null_byte'] && strstr( $intended_name, "\x00" ) ) {
			return false;
		}

		// Check if file extension contains forbidden string (e.g. php matched .php, .xxx.php, .php.xxx and so on).
		if ( ! empty( $options['forbidden_extensions'] ) ) {
			$exts = explode( '.', $intended_name );
			$exts = array_reverse( $exts );

			array_pop( $exts );

			$exts = array_map( 'strtolower', $exts );

			foreach ( $options['forbidden_extensions'] as $ext )
			{
				if ( in_array( $ext, $exts ) ) {
					return false;
				}
			}
		}

		// Check file content.
		global $wp_filesystem;

		if ( $options['php_tag_in_content'] || $options['shorttag_in_content'] || ( $options['fobidden_ext_in_content'] && ! empty( $options['forbidden_extensions'] ) ) ) {
			$data = ( $data = call_user_func('file_' . 'get' . '_contents', $temp_name) ) ? $data : $wp_filesystem->get_contents($temp_name);

			// Check if file content contains <?php tag.
			if ( $options['php_tag_in_content'] && stristr( $data, '<?php' ) ) {
				return false;
			}

			// Check if file content contains short open tag.
			if ( $options['shorttag_in_content'] ) {
				$suspicious_exts = $options['shorttag_extensions'];

				if ( empty( $suspicious_exts ) ) {
					$suspicious_exts = array( 'inc', 'phps', 'class', 'php3', 'php4', 'txt', 'dat', 'tpl', 'tmpl' );
				}

				// Check if file extension is in the list that need to check file content for short open tag.
				$found = false;

				foreach ( $suspicious_exts as $ext ) {
					if ( in_array( $ext, $exts ) ) {
						$found = true;

						break;
					}
				}
			}

			// Check if file content contains forbidden extensions.
			if ( $options['fobidden_ext_in_content'] && ! empty( $options['forbidden_extensions'] ) ) {
				$suspicious_exts = $options['fobidden_ext_extensions'];

				if ( empty( $suspicious_exts ) ) {
					$suspicious_exts = array( 'zip', 'rar', 'tar', 'gz', 'tgz', 'bz2', 'tbz' );
				}

				// Check if file extension is in the list that need to check file content for forbidden extensions.
				$found = false;

				foreach ( $suspicious_exts as $ext ) {
					if ( in_array( $ext, $exts ) ) {
						$found = true;

						break;
					}
				}

				if ( $found ) {
					foreach ( $options['forbidden_extensions'] as $ext ) {
						if ( strstr( $data, '.' . $ext ) ) {
							return false;
						}
					}
				}
			}

			// Make sure any string, that need to be check in file content, does not truncated due to read boundary.
			$data = substr( $data, -10 );
		}

		return true;
	}

	/**
	 * Method to check a file for potential XSS content.
	 *
	 * @param   string  $file  Absolute path to the file needs to be checked.
	 *
	 * @return  boolean
	 */
	public static function check_xss( $file ) {
		global $wp_filesystem;

		// Make sure the specified file does not contain unwanted tags.
		$xss_check = ( $xss_check = call_user_func('file_' . 'get' . '_contents', $file) ) ? $xss_check : $wp_filesystem->get_contents($file);
		$xss_check = substr( $xss_check, -1, 256 );

		$html_tags = array(
			'abbr', 'acronym', 'address', 'applet', 'area', 'audioscope', 'base', 'basefont', 'bdo', 'bgsound', 'big',
			'blackface', 'blink', 'blockquote', 'body', 'bq', 'br', 'button', 'caption', 'center', 'cite', 'code', 'col',
			'colgroup', 'comment', 'custom', 'dd', 'del', 'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'fn',
			'font', 'form', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html', 'iframe', 'ilayer',
			'img', 'input', 'ins', 'isindex', 'keygen', 'kbd', 'label', 'layer', 'legend', 'li', 'limittext', 'link', 'listing',
			'map', 'marquee', 'menu', 'meta', 'multicol', 'nobr', 'noembed', 'noframes', 'noscript', 'nosmartquotes', 'object',
			'ol', 'optgroup', 'option', 'param', 'plaintext', 'pre', 'rt', 'ruby', 's', 'samp', 'script', 'select', 'server',
			'shadow', 'sidebar', 'small', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'td',
			'textarea', 'tfoot', 'th', 'thead', 'title', 'tr', 'tt', 'ul', 'var', 'wbr', 'xml', 'xmp', '!DOCTYPE', '!--',
		);

		foreach ( $html_tags as $tag ) {
			// A tag is '<tagname ', so we need to add < and a space or '<tagname>'.
			if ( stristr( $xss_check, '<' . $tag . ' ' ) || stristr( $xss_check, '<' . $tag . '>' ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Add some custom css to control notice of 3rd-plugin
	 *
	 * @since  1.1.8
	 */
	public static function admin_custom_css() {
		echo '
			<style>
				.vc_license-activation-notice.updated,
				.rs-update-notice-wrap.updated,
				.installer-q-icon {
					display: none;
				}
			</style>
		';
	}
}

/**
 * Helper function to get value from the super-global SERVER variable.
 *
 * @param   string  $key  Key to get value for.
 *
 * @return  mixed
 */
function wr_get_server_param( $key ) {
	return array_key_exists( $key, $_SERVER ) ? $_SERVER[$key] : '';
}

/**
 * Helper function to set value to the super-global SERVER variable.
 *
 * @param   string  $key    Key to set value for.
 * @param   string  $value  Value to set.
 *
 * @return  void
 */
function wr_set_server_param( $key, $value ) {
	$_SERVER[$key] = $value;
}

// Load TGM Plugin Activation.
get_template_part( 'libraries/plugins/class-tgm-plugin-activation' );

// Initialize WR Nitro.
WR_Nitro::initialize();
