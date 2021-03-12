<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 * Custom functions for WooCommerce.
 */

/**
 * Assets compression class.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Assets {
	/**
	 * Define whether or not to compress assets for customize screen.
	 */
	const COMPRESS_CUSTOMIZE_ASSETS = false;

	/**
	 * Define whether or not to compress assets for page options.
	 */
	const COMPRESS_PAGE_OPTIONS_ASSETS = false;

	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	/**
	 * Define directory name to store compressed assets.
	 *
	 * @var  string
	 */
	protected static $directory = 'assets';

	/**
	 * Variable to hold the list of assets should be lazy loaded.
	 *
	 * @var  array
	 */
	protected static $lazy_load_sources = array();

	/**
	 * Variable to hold handles of compressed scripts.
	 *
	 * @var  array
	 */
	protected static $compressed_handles = array();

	/**
	 * Admin stylesheets that can be combined and compressed.
	 *
	 * @param $level_1 string
	 * @param $level_2 string
	 * @param $wp_content boolean
	 *
	 * @return  string or array
	 */
	public static function stylesheets( $level_1, $level_2 = NULL, $wp_content = true ) {

		if( $wp_content ) {
			$wp_content = WR_Nitro_Helper::wp_content();
		} else {
			$wp_content = current( explode( '/themes/', __FILE__ ) );
			$wp_content = explode( '/', $wp_content );
			$wp_content = '/' . end( $wp_content );
		}

		$data = array(
			'customize' => array(
				'imgareaselect'         => 'wp-includes/js/imgareaselect/imgareaselect.css',
				'mediaelement'          => 'wp-includes/js/mediaelement/mediaelementplayer.min.css',
				'wp-mediaelement'       => 'wp-includes/js/mediaelement/wp-mediaelement.css',
				'jquery-chosen'         => $wp_content . '/plugins/yith-woocommerce-wishlist/plugin-fw/assets/css/chosen/chosen.css',
				'thickbox'              => 'wp-includes/js/thickbox/thickbox.css',
				'codemirror'            => $wp_content . '/themes/wr-nitro/assets/3rd-party/codemirror/lib/codemirror.css',
				'spectrum-color-picker' => $wp_content . '/themes/wr-nitro/assets/3rd-party/spectrum/spectrum.css',
				'wr-nitro-google-fonts' => $wp_content . '/themes/wr-nitro/assets/woorockets/css/admin/google-fonts.css',
			),

			'page' => array(
				'thickbox'              => 'wp-includes/js/thickbox/thickbox.css',
				'mediaelement'          => 'wp-includes/js/mediaelement/mediaelementplayer.min.css',
				'wp-mediaelement'       => 'wp-includes/js/mediaelement/wp-mediaelement.css',
				'imgareaselect'         => 'wp-includes/js/imgareaselect/imgareaselect.css',
				'wr-metabox'            => $wp_content . '/themes/wr-nitro/assets/woorockets/css/admin/meta-box.css',
				'wr-google-fonts'       => $wp_content . '/themes/wr-nitro/assets/woorockets/css/admin/google-fonts.css',
				'jquery-chosen'         => $wp_content . '/plugins/yith-woocommerce-wishlist/plugin-fw/assets/css/chosen/chosen.css',
				'rwmb'                  => $wp_content . '/plugins/meta-box/css/style.css',
				'rwmb-select'           => $wp_content . '/plugins/meta-box/css/select.css',
				'rwmb-divider'          => $wp_content . '/plugins/meta-box/css/divider.css',
				'rwmb-input-list'       => $wp_content . '/plugins/meta-box/css/input-list.css',
				'spectrum-color-picker' => $wp_content . '/themes/wr-nitro/assets/3rd-party/spectrum/spectrum.css',
				'rwmb-media'            => $wp_content . '/plugins/meta-box/css/media.css',
				'rwmb-image-advanced'   => $wp_content . '/plugins/meta-box/css/image-advanced.css',
				'rwmb-image-select'     => $wp_content . '/plugins/meta-box/css/image-select.css',
			),
		);

		if( $level_2 !== NULL && isset( $data[ $level_1 ][ $level_2 ] ) ) {
			return $data[ $level_1 ][ $level_2 ];
		} elseif( isset( $data[ $level_1 ] ) ){
			return $data[ $level_1 ];
		} else {
			return false;
		}
	}

	/**
	 * Admin scripts that can be combined and compressed.
	 *
	 * @param $level_1 string
	 * @param $level_2 string
	 * @param $wp_content boolean
	 *
	 * @var  array
	 */
	public static function scripts( $level_1, $level_2 = NULL, $wp_content = true ) {

		if( $wp_content ) {
			$wp_content = WR_Nitro_Helper::wp_content();
		} else {
			$wp_content = current( explode( '/themes/', __FILE__ ) );
			$wp_content = explode( '/', $wp_content );
			$wp_content = '/' . end( $wp_content );
		}

		$data = array(
			'customize' => array(
				'jquery-chosen'                   => $wp_content . '/plugins/yith-woocommerce-wishlist/plugin-fw/assets/js/chosen/chosen.jquery.js',
				'wr-nitro-customize-select-image' => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/customize/control/select-image.js',
				'codemirror'                      => $wp_content . '/themes/wr-nitro/assets/3rd-party/codemirror/lib/codemirror.js',
				'codemirror-css-mode'             => $wp_content . '/themes/wr-nitro/assets/3rd-party/codemirror/mode/css/css.js',
				'codemirror-js-mode'              => $wp_content . '/themes/wr-nitro/assets/3rd-party/codemirror/mode/javascript/javascript.js',
				'codemirror-html-mode'            => $wp_content . '/themes/wr-nitro/assets/3rd-party/codemirror/mode/htmlmixed/htmlmixed.js',
				'wr-nitro-customize-editor'       => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/customize/control/editor.js',
				'wr-nitro-customize-html'         => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/customize/control/html.js',
				'spectrum-color-picker'           => $wp_content . '/themes/wr-nitro/assets/3rd-party/spectrum/spectrum.js',
				'wr-nitro-customize-colors'       => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/customize/control/colors.js',
				'wr-nitro-customize-slider'       => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/customize/control/slider.js',
				'wr-nitro-customize-typography'   => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/customize/control/typography.js',
				'wr-nitro-customize-preset'       => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/customize/control/preset.js',
				'wr-nitro-customize-radio-image'  => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/customize/control/radio-image.js',
				'wr-customize-control'            => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/customize/customize.js',
			),

			'page' => array(
				'wr-nitro-jquery-backcompat'        => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/jquery-backcompat.js',
				'wr-nitro-metabox-field-wrslider'   => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/meta-box/fields/wrslider.js',
				'spectrum-color-picker'             => $wp_content . '/themes/wr-nitro/assets/3rd-party/spectrum/spectrum.js',
				'wr-nitro-metabox-field-colors'     => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/meta-box/fields/colors.js',
				'wr-nitro-metabox-field-typography' => $wp_content . '/themes/wr-nitro/assets/woorockets/js/admin/meta-box/fields/typography.js',
				'vc-icon-picker'                    => $wp_content . '/plugins/js_composer/assets/lib/bower/vcIconPicker/jquery.fonticonpicker.min.js',
				'jquery-chosen'                     => $wp_content . '/plugins/yith-woocommerce-wishlist/plugin-fw/assets/js/chosen/chosen.jquery.js',
				'rwmb-select'                       => $wp_content . '/plugins/meta-box/js/select.js',
				'rwmb-input-list'                   => $wp_content . '/plugins/meta-box/js/input-list.js',
				'rwmb-image-advanced'               => $wp_content . '/plugins/meta-box/js/image-advanced.js',
				'rwmb-image-select'                 => $wp_content . '/plugins/meta-box/js/image-select.js',
				'rwmb-autosave'                     => $wp_content . '/plugins/meta-box/js/autosave.js',
				'jquery-validate'                   => $wp_content . '/plugins/meta-box/js/jquery.validate.min.js',
				'rwmb-validate'                     => $wp_content . '/plugins/meta-box/js/validate.js',
				'vc_accordion_script'               => $wp_content . '/plugins/js_composer/assets/lib/vc_accordion/vc-accordion.min.js',
				'wpb_php_js'                        => $wp_content . '/plugins/js_composer/assets/lib/php.default/php.default.min.js',
				'wpb_json-js'                       => $wp_content . '/plugins/js_composer/assets/lib/bower/json-js/json2.min.js',
				'vc-backend-actions-js'             => $wp_content . '/plugins/js_composer/assets/js/dist/backend-actions.min.js',
				'vc-backend-min-js'                 => $wp_content . '/plugins/js_composer/assets/js/dist/backend.min.js',
				'vc_vendor_woocommerce_backend'     => $wp_content . '/plugins/js_composer/assets/js/vendors/woocommerce.js',
				'wr-nitro-toolkit-admin'            => $wp_content . '/plugins/nitro-toolkit/assets/js/admin.js',
			),
		);

		if( $level_2 !== NULL && isset( $data[ $level_1 ][ $level_2 ] ) ) {
			return $data[ $level_1 ][ $level_2 ];
		} elseif( isset( $data[ $level_1 ] ) ){
			return $data[ $level_1 ];
		} else {
			return false;
		}
	}

	/**
	 * Plug into WordPress.
	 *
	 * @return  void
	 */
	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}

		// Register init actions.
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_action( 'init'      , array( __CLASS__, 'init'       ) );

		// State that initialization completed.
		self::$initialized = true;
	}

	/**
	 * Initialize assets compression for back-end.
	 *
	 * @return  void
	 */
	public static function admin_init() {
		global $pagenow;

		if ( self::COMPRESS_CUSTOMIZE_ASSETS && 'customize.php' == $pagenow ) {
			add_action( 'customize_controls_enqueue_scripts', array( __CLASS__, 'compress_customize_assets' ), 9999999999 );

			// Register actions to print inline scripts.
			add_action( 'customize_controls_print_footer_scripts', array( __CLASS__, 'print_inline_scripts' ), 9999999999 );
		}

		elseif ( self::COMPRESS_PAGE_OPTIONS_ASSETS && in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) ) {
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'compress_page_assets' ), 9999999999 );
			add_action( 'admin_footer'         , array( __CLASS__, 'compress_page_assets' ), 9999999999 );

			// Register actions to print inline scripts.
			add_action( 'admin_print_footer_scripts', array( __CLASS__, 'print_inline_scripts' ), 9999999999 );
		}
	}

	/**
	 * Initialize assets compression for front-end.
	 *
	 * @return  void
	 */
	public static function init() {
		// Register action to optimize assets loading.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'optimize_site_assets' ), 9999999996 );

		// Get theme options.
		$wr_nitro_options = WR_Nitro::get_options();

		if ( ( int ) $wr_nitro_options['compress_css'] ) {
			// Register actions to compress and combine stylesheets.
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'compress_site_styles'  ), 9999999997 );
		}

		if ( ( int ) $wr_nitro_options['compress_js'] ) {
			// Register actions to compress and combine scripts.
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'compress_site_scripts' ), 9999999998 );

			// Register actions to print inline scripts.
			add_action( 'wp_print_footer_scripts', array( __CLASS__, 'print_inline_scripts' ), 9999999999 );
		}
	}

	/**
	 * Compress and load assets for the Customize screen.
	 *
	 * @return  void
	 */
	public static function compress_customize_assets() {
		// Dequeue unnecessary stylesheets.
		function nitro_dequeue_unnecessary_stylesheets( $to_do ) {
			// Define unnecessary stylesheets.
			$unnecessary_stylesheets = array(
				'revslider-global-styles',
				'woocommerce-activation',
				'woocommerce_admin_menu_styles',
				'woocommerce_admin_styles',
				'yit-plugin-sidebar-style',
			);

			return array_diff( $to_do, $unnecessary_stylesheets );
		}
		add_filter( 'nitro_customize_stylesheets', 'nitro_dequeue_unnecessary_stylesheets' );

		// Dequeue 'woocommerce-activation' stylesheet.
		function nitro_dequeue_woocommerce_activation_stylesheet() {
			wp_dequeue_style( 'woocommerce-activation' );
		}
		add_filter( 'admin_print_styles', 'nitro_dequeue_woocommerce_activation_stylesheet' );

		// Dequeue unnecessary scripts.
		function nitro_dequeue_unnecessary_scripts( $to_do ) {
			// Define unnecessary scripts.
			$unnecessary_scripts = array(
				'woocommerce_settings',
				'yit-plugin-sidebar-js',
			);

			return array_diff( $to_do, $unnecessary_scripts );
		}
		add_filter( 'nitro_customize_scripts', 'nitro_dequeue_unnecessary_scripts' );

		// Compress and load assets for the Customize screen.
		self::compress_admin_styles( 'customize' );
		self::compress_admin_scripts( 'customize' );
	}

	/**
	 * Compress and load assets for the Single Post/Page admin screen.
	 *
	 * @return  void
	 */
	public static function compress_page_assets() {
		if ( ! function_exists( 'nitro_dequeue_unnecessary_stylesheets' ) ) {
			// Dequeue unnecessary stylesheets.
			function nitro_dequeue_unnecessary_stylesheets( $to_do ) {
				// Get curren screen
				$screen = get_current_screen();
				$unnecessary_stylesheets = array();

				if ( $screen->post_type != 'product' ) {
					$unnecessary_stylesheets[] = 'woocommerce-activation';
					$unnecessary_stylesheets[] = 'woocommerce_admin_menu_styles';
					$unnecessary_stylesheets[] = 'woocommerce_admin_styles';
				}

				// Define unnecessary stylesheets.
				$unnecessary_stylesheets = array(
					'yit-plugin-sidebar-style',
				);

				return array_diff( $to_do, $unnecessary_stylesheets );
			}
			add_filter( 'nitro_page_stylesheets', 'nitro_dequeue_unnecessary_stylesheets' );

			// Dequeue 'woocommerce-activation' stylesheet.
			function nitro_dequeue_woocommerce_activation_stylesheet() {
				wp_dequeue_style( 'woocommerce-activation' );
			}
			add_filter( 'admin_print_styles', 'nitro_dequeue_woocommerce_activation_stylesheet' );

			// Dequeue unnecessary scripts.
			function nitro_dequeue_unnecessary_scripts( $to_do ) {
				// Define unnecessary scripts.
				$unnecessary_scripts = array(
					'revslider-tinymce-shortcode-script',
					'woocommerce_settings',
					'yit-plugin-sidebar-js',
				);

				return array_diff( $to_do, $unnecessary_scripts );
			}
			add_filter( 'nitro_page_scripts', 'nitro_dequeue_unnecessary_scripts' );
		}

		// Compress and load assets for the Single Post/Page admin screen.
		self::compress_admin_styles( 'page' );
		self::compress_admin_scripts( 'page' );
	}

	/**
	 * Enqueue stylesheets for the specified admin screen.
	 *
	 * @param   string  $screen  Screen to load stylesheets for.
	 *
	 * @return  void
	 */
	protected static function compress_admin_styles( $screen ) {
		global $wp_styles;

		// Verify WordPress's $wp_styles object.
		if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
			return;
		}

		// Resolve all dependencies.
		$wp_styles->all_deps( $wp_styles->queue );

		// Get all stylesheets including dependencies.
		$styles = $inline = $queue = array();
		$index = wp_generate_password( 8, false );

		// Allow to do list customizable from outside.
		$to_do = apply_filters( "nitro_{$screen}_stylesheets", $wp_styles->to_do );

		foreach ( $to_do as $handle ) {
			$skip = false;

			// Do not compress stylesheet without recognizable source.
			if ( ! is_string( $wp_styles->registered[ $handle ]->src ) ) {
				$skip = true;
			}

			// Combine and compress supported stylesheets only.
			elseif ( isset( $wp_scripts ) && false !== strpos( $wp_scripts->registered[ $handle ]->src, '/wp-admin/' ) ) {
				$skip = true;
			}

			elseif ( ! array_key_exists( $handle, self::stylesheets( $screen ) ) ) {
				$skip = true;
			}

			// Prepare source.
			if ( ! $skip ) {
				$source = current( explode( '?', $wp_styles->registered[ $handle ]->src, 2 ) );

				if ( '//' == substr( $source, 0, 2 ) ) {
					$source = set_url_scheme( $source );
				}

				if ( preg_match( '#^(https?:|//)#i', $source ) ) {
					// Do not compress remote stylesheet.
					if ( stripos( $source, site_url() ) === false ) {
						$skip = true;
					}

					// Do not compress dynamic generation stylesheet.
					if ( '.css' != substr( $source, -4 ) ) {
						$skip = true;
					}
				}
			}

			// Determine the media the stylesheet associated with.
			$media = 'all';

			if ( isset( $wp_styles->registered[ $handle ]->args ) && 'all' != $wp_styles->registered[ $handle ]->args ) {
				$media = $wp_styles->registered[ $handle ]->args;
			}

			// Check if the stylesheet should be skipped.
			if ( $skip ) {
				if ( false !== strpos( $wp_styles->registered[ $handle ]->src, '/fonts.googleapis.com/' ) ) {
					// Do not load Google Fonts, add them to the lazy loading list instead.
					self::$lazy_load_sources[] = $wp_styles->registered[ $handle ]->src;
				} else {
					$queue[] = $handle;

					if ( is_string( $wp_styles->registered[ $handle ]->src ) && ! preg_match( '/(wp-admin|wp-includes)/', $wp_styles->registered[ $handle ]->src ) && ! array_key_exists( $handle, self::stylesheets( $screen ) ) ) {
						if ( isset( $last_media ) && $last_media == $media && isset( $last_index ) && $last_index == $index ) {
							$index = wp_generate_password( 8, false );
						}
					}
				}

				continue;
			}

			// Generate handle for the loader.
			$loader = $media . '-' . $index;

			// Enqueue loader.
			if ( ! in_array( $loader, $queue ) ) {
				$queue[] = $loader;
			}

			// Store inline styles if has.
			if ( isset( $wp_styles->registered[ $handle ]->extra['after'] ) && ! empty( $wp_styles->registered[ $handle ]->extra['after'] ) ) {
				$inline[ $loader ][] = implode( ' ', ( array ) $wp_styles->registered[ $handle ]->extra['after'] );
			}

			// Store stylesheet handle.
			$styles[ $loader ][] = $handle;

			// Set a property for reference later.
			$wp_styles->registered[ $handle ]->_compressed = true;

			// Store last media and index.
			$last_media = $media;
			$last_index = $index;
		}

		// Make sure we have stylesheets to compress.
		if ( ! count( $styles ) ) {
			return;
		}

		// Enqueue stylesheets loaders.
		foreach ( $styles as $handle => $load ) {
			$wp_styles->add(
				$handle,
				get_template_directory_uri() . "/assets/load-styles.php?screen={$screen}&load=" . implode( ',', $load ),
				array(),
				false,
				substr( $handle, 0, strpos( $handle, '-' ) )
			);

			if ( isset( $inline ) && isset( $inline[ $handle ] ) ) {
				$wp_styles->registered[ $handle ]->extra['after'] = ( array ) self::minify_css( implode( ' ', $inline[ $handle ] ) );
			}
		}

		// Remove all original stylesheets by unsetting required values.
		foreach ( $wp_styles->to_do as $handle ) {
			if ( isset( $wp_styles->registered[ $handle ]->_compressed ) && $wp_styles->registered[ $handle ]->_compressed ) {
				$wp_styles->registered[ $handle ]->src  = false;
				$wp_styles->registered[ $handle ]->deps = array();
			}
		}

		// Reset stylesheets queue.
		$wp_styles->reset();

		$wp_styles->queue = $queue;
		$wp_styles->to_do = array();
	}

	/**
	 * Enqueue scripts for the specified admin screen.
	 *
	 * @param   string  $screen  Screen to load scripts for.
	 *
	 * @return  void
	 */
	protected static function compress_admin_scripts( $screen ) {
		global $wp_scripts;

		// Verify WordPress's $wp_scripts object.
		if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
			return;
		}

		// Resolve all dependencies.
		$wp_scripts->all_deps( $wp_scripts->queue );

		// Get all scripts including dependencies.
		$scripts = $inline = $queue = array();
		$index = wp_generate_password( 8, false );

		// Allow to do list customizable from outside.
		$to_do = apply_filters( "nitro_{$screen}_scripts", $wp_scripts->to_do );

		// Prepare to do list for compression.
		foreach ( $to_do as $handle ) {
			$skip = false;

			// Do not compress script without recognizable source.
			if ( ! is_string( $wp_scripts->registered[ $handle ]->src ) ) {
				$skip = true;
			}

			// Combine and compress supported scripts only.
			elseif ( false !== strpos( $wp_scripts->registered[ $handle ]->src, '/wp-admin/' ) ) {
				$skip = true;
			}

			elseif ( ! array_key_exists( $handle, self::scripts( $screen ) ) ) {
				$skip = true;
			}

			// Prepare source.
			if ( ! $skip ) {
				$source = current( explode( '?', $wp_scripts->registered[ $handle ]->src, 2 ) );

				if ( '//' == substr( $source, 0, 2 ) ) {
					$source = set_url_scheme( $source );
				}

				if ( preg_match( '#^(https?:|//)#i', $source ) ) {
					// Do not compress remote script.
					if ( stripos( $source, site_url() ) === false ) {
						$skip = true;
					}

					// Do not compress dynamic generation script.
					if ( '.js' != substr( $source, -3 ) ) {
						$skip = true;
					}
				}
			}

			// Determine the position the script should be loaded in.
			$position = 'header';

			if ( isset( $wp_scripts->registered[ $handle ]->extra['group'] ) && 1 == $wp_scripts->registered[ $handle ]->extra['group'] ) {
				$position = 'footer';
			} elseif ( isset( $wp_scripts->groups ) && isset( $wp_scripts->groups[ $handle ] ) && $wp_scripts->groups[ $handle ] > 0 ) {
				$position = 'footer';
			} elseif ( isset( $wp_scripts->in_footer ) && in_array( $handle, $wp_scripts->in_footer, true ) ) {
				$position = 'footer';
			}

			// Check if the script should be skipped.
			$queue[] = $handle;

			if ( $skip ) {
				if ( is_string( $wp_scripts->registered[ $handle ]->src ) && ! preg_match( '/(wp-admin|wp-includes)/', $wp_scripts->registered[ $handle ]->src ) && ! array_key_exists( $handle, self::scripts( $screen ) ) ) {
					if ( isset( $last_position ) && $last_position == $position && isset( $last_index ) && $last_index == $index ) {
						$index = wp_generate_password( 8, false );
					}
				}

				continue;
			}

			// Generate handle for the loader.
			$loader = $position . '-' . $index;

			// Enqueue loader.
			if ( ! in_array( $loader, $queue ) ) {
				$queue[] = $loader;
			}

			// Store script handle.
			$scripts[ $loader ][] = $handle;

			// Store associated inline scripts.
			if ( isset( $wp_scripts->registered[ $handle ]->extra['data'] ) && ! empty( $wp_scripts->registered[ $handle ]->extra['data'] ) ) {
				if ( ! isset( $inline[ $loader ] ) ) {
					$inline[ $loader ] = array();
				}

				$inline[ $loader ] = array_merge(
					$inline[ $loader ],
					( array ) $wp_scripts->registered[ $handle ]->extra['data']
				);
			}

			// Store handle of script being compressed for reference later.
			self::$compressed_handles[] = $handle;

			// Store last position and index.
			$last_position = $position;
			$last_index    = $index;
		}

		// Make sure we have scripts to compress.
		if ( ! count( $scripts ) ) {
			return;
		}

		// Enqueue scripts loaders.
		foreach ( $scripts as $handle => $load ) {
			$wp_scripts->add(
				$handle,
				get_template_directory_uri() . "/assets/load-scripts.php?screen={$screen}&load=" . implode( ',', $load ),
				array(),
				false,
				null
			);

			if ( 'footer' == substr( $handle, 0, strpos( $handle, '-' ) ) ) {
				$wp_scripts->add_data( $handle, 'group', 1 );
			}

			// Set associated inline scripts.
			if ( isset( $inline[ $handle ] ) ) {
				$wp_scripts->add_data( $handle, 'data', implode( "\n", $inline[ $handle ] ) );
			}
		}

		// Remove all original scripts by unsetting required values.
		foreach ( self::$compressed_handles as $handle ) {
			$wp_scripts->registered[ $handle ]->src  = false;
			$wp_scripts->registered[ $handle ]->deps = array();
		}

		// Reset scripts queue.
		$wp_scripts->reset();

		$wp_scripts->queue = $queue;
		$wp_scripts->to_do = array();
	}

	/**
	 * Method to remove unnecessary assets from being loaded.
	 *
	 * @return  void
	 */
	public static function optimize_site_assets() {
		// Get global variables.
		global $wp_query, $post;

		if ( empty( $wp_query->query_vars['post_type'] ) ) {
			$wp_query->query_vars['post_type'] = 'post';
		}

		// Get Nitro options.
		$wr_nitro_options = WR_Nitro::get_options();
		$header_data      = WR_Nitro::get_header();

		// Dequeue Contact Form 7 related assets if not necessary.
		if ( isset( $post->post_content ) && ! ( has_shortcode( $post->post_content, 'contact-form-7' ) || has_shortcode( $header_data, 'contact-form-7' ) ) ) {
			wp_dequeue_style(  'contact-form-7' );
			wp_dequeue_script( 'contact-form-7' );
		}

		// Dequeue Revolution Slider assets if not necessary.
		if ( isset( $post->post_content ) && ( ! ( has_shortcode( $post->post_content, 'rev_slider' ) || has_shortcode( $post->post_content, 'rev_slider_vc' ) || has_shortcode( $header_data, 'rev_slider' ) || has_shortcode( $header_data, 'rev_slider_vc' ) ) ) ) {
			wp_dequeue_style( 'rs-plugin-settings' );

			wp_dequeue_script( 'tp-tools' );
			wp_dequeue_script( 'revmin'   );
		}

		// Dequeue WooCommerce related assets if not necessary.
		$is_product = ( function_exists( 'is_product' ) && is_product() );
		$is_shop    = ( ( function_exists( 'is_shop' ) && is_shop() ) || is_post_type_archive( 'product' ) || ( function_exists( 'is_product_category' ) && is_product_category() ) || ( function_exists( 'is_product_tag' ) && is_product_tag() ) || ( function_exists( 'is_woocommerce' ) && is_woocommerce() && is_tax() ) );
		$is_wc      = ( ( function_exists( 'is_product_category' ) && is_product_category() ) || ( function_exists( 'is_product_tag' ) && is_product_tag() ) || ( function_exists( 'is_cart' ) && is_cart() ) || ( function_exists( 'is_checkout' ) && is_checkout() ) || ( function_exists( 'is_account_page' ) && is_account_page() ) || ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'yith_wcwl_wishlist' ) ) );
		$format     = get_post_format();

		// Dequeue WooCommerce assets.
		if ( ! ( $is_shop || $is_product || function_exists( 'is_cart' ) && is_cart() || ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'nitro_product' ) || has_shortcode( $post->post_content, 'nitro_products' ) || has_shortcode( $post->post_content, 'nitro_product_package' ) || has_shortcode( $post->post_content, 'nitro_product_menu' ) || has_shortcode( $post->post_content, 'nitro_product_category' ) || has_shortcode( $post->post_content, 'nitro_product_categories' ) || has_shortcode( $post->post_content, 'nitro_product_button' ) || has_shortcode( $post->post_content, 'nitro_product_attribute' ) || has_shortcode( $post->post_content, 'yith_wcwl_wishlist' ) || has_shortcode( $post->post_content, 'wr_mapper' ) || has_shortcode( $post->post_content, 'nitro_video' ) || has_shortcode( $post->post_content, 'add_to_cart' ) || has_shortcode( $post->post_content, 'yith_woocompare_table' ) || has_shortcode( $post->post_content, 'wcpv_registration' ) ) ) ) ) {// Dequeue Nitro assets.

			wp_dequeue_script( 'magnific-popup' );

			// Dequeue WooCommerce assets.
//			wp_dequeue_script( 'woocommerce' );
			wp_dequeue_script( 'wc-cart-fragments' );

			// Dequeue WooCommerce assets.
			wp_dequeue_script( 'wc-add-to-cart'   );
			wp_dequeue_script( 'jquery-blockui'   );
			wp_dequeue_script( 'jquery-cookie'    );
			wp_dequeue_script( 'prettyPhoto'      );
			wp_dequeue_script( 'prettyPhoto-init' );

			// Dequeue YITH WooCommerce Ajax Navigation assets.
			wp_dequeue_style(  'yith-wcan-frontend' );
			wp_dequeue_script( 'yith-wcan-script'   );

			// Dequeue YITH WooCommerce Compare assets.
			wp_dequeue_style( 'jquery-colorbox' );

			wp_dequeue_script( 'jquery-colorbox'      );

			// Dequeue YITH WooCommerce Wishlist assets.
			wp_dequeue_style( 'jquery-selectBox' );
			wp_dequeue_style( 'yith-wcwl-main'   );

			wp_dequeue_script( 'jquery-selectBox' );
			wp_dequeue_script( 'jquery-yith-wcwl' );

			// Dequeue YITH WooCommerce Multi-step Checkout assets.
			wp_dequeue_style( 'yith-wcms-checkout' );

			// Dequeue Visual Composer assets.
			wp_dequeue_script( 'vc_woocommerce-add-to-cart-js' );
		}

		if ( isset( $post ) && has_shortcode( $post->post_content, 'yith_wcwl_add_to_wishlist' ) ) {
			wp_enqueue_style(  'yith-wcan-frontend' );
			wp_enqueue_script( 'yith-wcan-script'   );
			wp_enqueue_script( 'jquery-selectBox' );
			wp_enqueue_script( 'jquery-yith-wcwl' );
		}

		if ( $is_product || is_post_type_archive( 'nitro-gallery' ) || is_singular( 'nitro-gallery' ) || ( is_single() && 'gallery' == $format ) || is_tax( 'gallery_cat' ) || has_shortcode( $post->post_content, 'product_page' ) ) {
			wp_enqueue_style( 'nivo-lightbox' );
			wp_enqueue_script( 'nivo-lightbox' );
		}

		if ( has_shortcode( $header_data, 'nitro_buy_now' ) ) {
			wp_enqueue_script( 'magnific-popup' );
			wp_enqueue_style( 'wr-nitro-woocommerce' );
		}

		if ( ! empty( $post ) && ( has_shortcode( $post->post_content, 'yith_woocompare_table' ) || has_shortcode( $post->post_content, 'yith_ywraq_request_quote' ) ) ) {
			wp_enqueue_style( 'wr-nitro-woocommerce' );
		}

		// Enqueue WooCommerce assets
		if ( ! is_404() ) {
			if ( $is_shop || $is_product || ( isset( $post ) && has_shortcode( $post->post_content, 'nitro_buy_now' ) ) ) {
				// WC assets
				wp_enqueue_script( 'magnific-popup' );

				// Owl Carousel
				wp_enqueue_style( 'owl-carousel'  );
				wp_enqueue_script( 'owl-carousel' );
			}
			if ( $is_shop || $is_product || $is_wc || is_search() || ( isset( $post ) && ( has_shortcode( $post->post_content, 'nitro_buy_now' ) || has_shortcode( $post->post_content, 'woocommerce_order_tracking' ) || has_shortcode( $post->post_content, 'nitro_product_attribute' ) || has_shortcode( $post->post_content, 'nitro_product' ) || has_shortcode( $post->post_content, 'product_page' ) || has_shortcode( $post->post_content, 'nitro_products' ) || has_shortcode( $post->post_content, 'add_to_cart' ) ) ) ) {
				wp_enqueue_style( 'wr-nitro-woocommerce' );
				wp_enqueue_script( 'wc-add-to-cart-variation' );
			}
		}

		// Enqueue script for single blog post
		if ( ( is_single() && 'gallery' == $format ) || ( ( is_post_type_archive( 'nitro-gallery' ) || is_tax( 'gallery_cat' ) ) && $wr_nitro_options['gallery_thumbnail_slide'] ) || ( is_singular( 'nitro-gallery' ) && ( 'slider' == $wr_nitro_options['gallery_single_layout'] || 'horizontal' == $wr_nitro_options['gallery_single_layout'] || $wr_nitro_options['gallery_single_related'] ) ) ) {
			// Owl Carousel
			wp_enqueue_style( 'owl-carousel'  );
			wp_enqueue_script( 'owl-carousel' );
		}

		// Enqueue script in gallery archive
		if ( is_post_type_archive( 'nitro-gallery' ) || is_tax( 'gallery_cat' ) || ( is_singular( 'nitro-gallery' ) && 'masonry' == $wr_nitro_options['gallery_single_layout'] ) ) {
			wp_enqueue_script( 'isotope' );
		}

		// Enqueue script in shop archive
		if ( ( $is_shop && ( $wr_nitro_options['wc_archive_style'] == 'masonry' || ( isset( $wr_nitro_options['wc_categories_style'] ) && $wr_nitro_options['wc_categories_style'] == 'masonry' ) || $wr_nitro_options['wc_archive_pagination_type'] == 'loadmore' || $wr_nitro_options['wc_archive_pagination_type'] == 'infinite' ) ) || ( ( is_home() || is_archive() ) && 'masonry' == $wr_nitro_options['blog_style'] ) ) {
			wp_enqueue_script( 'isotope' );
		}

		// Enqueue scripts when list post page has gallery format
		if ( isset( $wp_query->posts ) && $wp_query->posts ) {
			foreach( $wp_query->posts as $val ) {
				$format = get_the_terms( $val->ID, 'post_format' );

				if ( isset( $format[0] ) && $format[0]->slug == 'post-format-gallery' ) {
					// Nivo lightbox
					wp_enqueue_style( 'nivo-lightbox' );
					wp_enqueue_script( 'nivo-lightbox' );

					// Owl Carousel
					wp_enqueue_style( 'owl-carousel'  );
					wp_enqueue_script( 'owl-carousel' );
				}
			}
		}

		// Enqueue Parallax script if not necessary.
		$is_parallax = ( ! empty( $wr_nitro_options['wr_page_layout_bg_image'] ) && intval( $wr_nitro_options['wr_layout_boxed_parallax'] ) );
		$is_parallax = ( $is_parallax || intval( $wr_nitro_options['wr_page_title_parallax'] ) || intval( $wr_nitro_options['blog_single_title_full_screen'] ) );

		if ( $is_parallax ) {
			wp_enqueue_script( 'skrollr' );
		}

		wp_enqueue_script( 'jquery-animation' );

		// Enqueue style Multistep checkout
		if ( class_exists( 'YITH_Multistep_Checkout' ) && function_exists( 'is_checkout' ) && is_checkout() ) {
			wp_enqueue_style( 'yith-wcms-checkout' );
		}

		// Get sale price dates
		$countdown = get_post_meta( get_the_ID(), '_show_countdown', true );
		$start     = get_post_meta( get_the_ID(), '_sale_price_dates_from', true );
		$end       = get_post_meta( get_the_ID(), '_sale_price_dates_to', true );
		$now       = date( 'd-m-y' );

		// Dequeue countdown script if not necessary.
		if ( intval( $wr_nitro_options['under_construction'] ) || ( $is_product && 'yes' == $countdown && $end && date( 'd-m-y', $start ) <= $now ) ) {
			wp_enqueue_script( 'jquery-countdown' );
		}

		// Load WR Nitro stylesheets.
		wp_enqueue_style( 'wr-nitro-main' );
	}

	/**
	 * Method to compress all loaded stylesheet files.
	 *
	 * @return  void
	 */
	public static function compress_site_styles() {
		global $wp_styles, $wp_filesystem;

		// Get theme options.
		$wr_nitro_options = WR_Nitro::get_options();

		// Verify WordPress's $wp_styles object.
		if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
			return;
		}

		// Get location to store compression file.
		if ( ! $path = self::get_location() ) {
			return;
		}

		// Get upload directory info.
		$upload = wp_upload_dir();

		// Resolve all dependencies.
		$wp_styles->all_deps( $wp_styles->queue );

		// Get all stylesheets including dependencies.
		$styles = $queue = $inline = $last_modification = array();
		$index = wp_generate_password( 8, false );

		foreach ( $wp_styles->to_do as $handle ) {
			$skip = false;

			// Do not compress stylesheet without recognizable source.
			if ( ! is_string( $wp_styles->registered[ $handle ]->src ) ) {
				$skip = true;
			}

			// Do not compress main stylesheet if previewing.
			elseif ( is_customize_preview() && 'wr-nitro-main' == $handle ) {
				$skip = true;
			}

			// Do not compress stylesheet of Ninja Popup plugin.
			elseif ( false !== strpos( $wp_styles->registered[ $handle ]->src, '/arscode-ninja-popups/' ) ) {
				$skip = true;
			}

			// Do not compress stylesheet of Revolution Slider plugin.
			elseif ( false !== strpos( $wp_styles->registered[ $handle ]->src, '/revslider/' ) ) {
				$skip = true;
			}

			// Prepare source.
			if ( ! $skip ) {
				$source = current( explode( '?', $wp_styles->registered[ $handle ]->src, 2 ) );

				if ( '//' == substr( $source, 0, 2 ) ) {
					$source = set_url_scheme( $source );
				}

				if ( preg_match( '#^(https?:|//)#i', $source ) ) {
					// Do not compress remote stylesheet.
					if ( stripos( $source, site_url() ) === false ) {
						$skip = true;
					}

					// Do not compress dynamic generation stylesheet.
					if ( '.css' != substr( $source, -4 ) ) {
						$skip = true;
					}
				}
			}

			// Determine the media the stylesheet associated with.
			$media = 'all';

			if ( isset( $wp_styles->registered[ $handle ]->args ) && 'all' != $wp_styles->registered[ $handle ]->args ) {
				$media = $wp_styles->registered[ $handle ]->args;
			}

			// Check if the stylesheet should be skipped.
			if ( $skip ) {
				if ( false !== strpos( $wp_styles->registered[ $handle ]->src, '/fonts.googleapis.com/' ) ) {
					array_unshift( $queue, $handle );
				} else {
					$queue[] = $handle;

					if ( is_string( $wp_styles->registered[ $handle ]->src ) ) {
						if ( isset( $last_media ) && $last_media == $media && isset( $last_index ) && $last_index == $index ) {
							$index = wp_generate_password( 8, false );
						}
					}
				}

				continue;
			}

			// Check if media type of this stylesheet is not the same as the previous stylesheet.
			if ( isset( $last_media ) && $last_media != $media ) {
				$index = wp_generate_password( 8, false );
			}

			// Generate handle for the compression.
			$compression = $media . '-' . $index;

			// Enqueue compression file.
			if ( ! in_array( $compression, $queue ) ) {
				$queue[] = $compression;
			}

			// Store inline styles if has.
			if ( isset( $wp_styles->registered[ $handle ]->extra['after'] ) && ! empty( $wp_styles->registered[ $handle ]->extra['after'] ) ) {
				$inline[ $compression ][] = implode( ' ', ( array ) $wp_styles->registered[ $handle ]->extra['after'] );
			}

			// Get last file mofication time.
			$source = ltrim( str_replace( site_url(), '', $source ), '/' );
			$tmp    = filemtime( ABSPATH . $source );

			if ( ! isset( $last_modification[ $compression ] ) || $last_modification[ $compression ] < $tmp ) {
				$last_modification[ $compression ] = $tmp;
			}

			// Store stylesheet source.
			$styles[ $compression ][] = $source;

			// Set a property for reference later.
			$wp_styles->registered[ $handle ]->_compressed = true;

			// Store last media and index.
			$last_media = $media;
			$last_index = $index;
		}

		// Make sure we have stylesheets to compress.
		if ( ! count( $styles ) ) {
			return;
		}

		// Loop thru stylesheets to compress.
		foreach ( $styles as $handle => $sources ) {
			// Generate name for compression file.
			$compression = $path . '/' . md5( implode( $sources ) ) . '.css';

			// Make sure compression file not outdated if exists.
			unset( $num_files );

			if ( ! @is_file( $compression ) || filemtime( $compression ) < $last_modification[ $handle ] ) {
				// Loop thru stylesheet files to compress and combine.
				$compressed = '';
				$num_files  = 1;
				$max_size   = $wr_nitro_options['max_compression_size'] * 1000;

				foreach ( $sources as $source ) {
					// Read stylesheet file.
					$compressed .= ( $compressed == '' ? '' : "\n" );
					$compressed .= "/* {$source} */\n";
					$compressed .= self::minify_css( ABSPATH . $source );
					$compressed .= "\n";

					// Check if compression size is greater than the maximum allowed size.
					if ( strlen( $compressed ) >= $max_size ) {
						if ( $num_files == 1 ) {
							if ( ! ( call_user_func( 'file_' . 'put' . '_contents', $compression, $compressed) || $wp_filesystem->put_contents($compression, $compressed) ) ) {
								return;
							}
						} else {
							$nextFile = substr($compression, 0, -4) . "-{$num_files}.css";

							if ( ! ( call_user_func( 'file_' . 'put' . '_contents', $nextFile, $compressed) || $wp_filesystem->put_contents($nextFile, $compressed) ) ) {
								return;
							}
						}

						// Increase the total number of compression file.
						$num_files++;

						// Reset compressed content.
						$compressed = '';
					}
				}

				// Write compressed content to compression file.
				if ( $compressed != '' ) {
					if ( $num_files == 1 ) {
						if ( ! ( call_user_func( 'file_' . 'put' . '_contents', $compression, $compressed) || $wp_filesystem->put_contents($compression, $compressed) ) ) {
							return;
						}
					} else {
						$nextFile = substr($compression, 0, -4) . "-{$num_files}.css";

						if ( ! ( call_user_func( 'file_' . 'put' . '_contents', $nextFile, $compressed) || $wp_filesystem->put_contents($nextFile, $compressed) ) ) {
							return;
						}
					}
				} elseif ( $num_files > 1 ) {
					$num_files--;
				}
			}

			// Load compression file.
			if ( ! isset( $num_files ) ) {
				$num_files = glob( substr( $compression, 0, -4 ) . '*.css' );
				$num_files = count( $num_files );
			}

			$media       = substr( $handle, 0, strpos( $handle, '-' ) );
			$handling    = $handle . ( $num_files > 1 ? '-1' : '' );
			$compression = str_replace( $upload['basedir'], $upload['baseurl'], $compression );

			$wp_styles->add( $handling, $compression, array(), false, $media );

			// If there are more than 1 compression file, register every file depends on the preceding one.
			if ( $num_files > 1 ) {
				for ( $i = 2; $i <= $num_files; $i++ ) {
					$handling = $handle . ( $i == $num_files ? '' : "-{$i}" );
					$compress = substr( $compression, 0, -4 ) . "-{$i}.css";
					$depends  = array( $handle . '-' . ( $i - 1 ) );

					$wp_styles->add( $handling, $compress, $depends, false, $media );
				}
			}

			// Set associated inline styles.
			if ( isset( $inline[ $handle ] ) && count( $inline[ $handle ] ) ) {
				$wp_styles->registered[ $handling ]->extra['after'] = ( array ) self::minify_css( implode( ' ', $inline[ $handle ] ) );
			}
		}

		// Remove all original stylesheets by unsetting required values.
		foreach ( $wp_styles->to_do as $handle ) {
			if ( isset( $wp_styles->registered[ $handle ]->_compressed ) && $wp_styles->registered[ $handle ]->_compressed ) {
				$wp_styles->registered[ $handle ]->src  = false;
				$wp_styles->registered[ $handle ]->deps = array();
			}
		}

		// Reset stylesheets queue.
		$wp_styles->reset();

		$wp_styles->queue = $queue;
		$wp_styles->to_do = array();
	}

	/**
	 * Method to compress all loaded Javascript files.
	 *
	 * @return  void
	 */
	public static function compress_site_scripts() {
		global $wp_scripts, $wp_filesystem;

		// Get theme options.
		$wr_nitro_options = WR_Nitro::get_options();

		// Verify WordPress's $wp_scripts object.
		if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
			return;
		}

		// Get location to store compression file.
		if ( ! $path = self::get_location() ) {
			return;
		}

		// Get upload directory info.
		$upload = wp_upload_dir();

		// Resolve all dependencies.
		$wp_scripts->all_deps( $wp_scripts->queue );

		// Get all scripts including dependencies.
		$scripts = $inline = $queue = $last_modification = array();
		$index = wp_generate_password( 8, false );

		foreach ( $wp_scripts->to_do as $handle ) {
			$skip = false;

			// Do not compress script without recognizable source.
			if ( ! is_string( $wp_scripts->registered[ $handle ]->src ) ) {
				$skip = true;
			}

			// Prepare source.
			if ( ! $skip ) {
				$source = current( explode( '?', $wp_scripts->registered[ $handle ]->src, 2 ) );

				if ( '//' == substr( $source, 0, 2 ) ) {
					$source = set_url_scheme( $source );
				}

				if ( preg_match( '#^(https?:|//)#i', $source ) ) {
					// Do not compress remote script.
					if ( stripos( $source, site_url() ) === false ) {
						$skip = true;
					}

					// Do not compress dynamic generation script.
					if ( '.js' != substr( $source, -3 ) ) {
						$skip = true;
					}
				}

				// Do not compress scripts of Revolution Slider plugin.
				if ( false !== strpos( $source, '/plugins/revslider/' ) ) {
					$skip = true;
				}

				// Do not compress Isotope script of Visual Composer plugin.
				elseif (
					false !== strpos( $source, '/plugins/js_composer/' )
					&&
					false !== strpos( $source, '/isotope.pkgd.' )
				) {
					$skip = true;
				}
			}

			// Determine the position the script should be loaded in.
			$position = 'header';

			if ( isset( $wp_scripts->registered[ $handle ]->extra['group'] ) && 1 == $wp_scripts->registered[ $handle ]->extra['group'] ) {
				$position = 'footer';
			} elseif ( isset( $wp_scripts->groups ) && isset( $wp_scripts->groups[ $handle ] ) && $wp_scripts->groups[ $handle ] > 0 ) {
				$position = 'footer';
			} elseif ( isset( $wp_scripts->in_footer ) && in_array( $handle, $wp_scripts->in_footer, true ) ) {
				$position = 'footer';
			}

			// Check if the script should be skipped.
			$queue[] = $handle;

			if ( $skip ) {
				if ( is_string( $wp_scripts->registered[ $handle ]->src ) ) {
					if ( isset( $last_position ) && $last_position == $position && isset( $last_index ) && $last_index == $index ) {
						$index = wp_generate_password( 8, false );
					}
				}

				continue;
			}

			// Generate handle for the compression.
			$compression = $position . '-' . $index;

			// Enqueue compression file.
			if ( ! in_array( $compression, $queue ) ) {
				$queue[] = $compression;
			}

			// Get last file mofication time.
			$source = ltrim( str_replace( site_url(), '', $source ), '/' );
			$tmp    = filemtime( ABSPATH . $source );

			if ( ! isset( $last_modification[ $compression ] ) || $last_modification[ $compression ] < $tmp ) {
				$last_modification[ $compression ] = $tmp;
			}

			// Store script source.
			$scripts[ $compression ][] = $source;

			// Store associated inline scripts.
			if ( isset( $wp_scripts->registered[ $handle ]->extra['data'] ) && ! empty( $wp_scripts->registered[ $handle ]->extra['data'] ) ) {
				if ( ! isset( $inline[ $compression ] ) ) {
					$inline[ $compression ] = array();
				}

				$inline[ $compression ] = array_merge(
					$inline[ $compression ],
					( array ) $wp_scripts->registered[ $handle ]->extra['data']
				);
			}

			// Store handle of script being compressed for reference later.
			self::$compressed_handles[] = $handle;

			// Store last position and index.
			$last_position = $position;
			$last_index    = $index;
		}

		// Make sure we have scripts to compress.
		if ( ! count( $scripts ) ) {
			return;
		}

		// Load minification library.
		if ( ! class_exists( 'JShrink_Minifier' ) ) {
			get_template_part( 'libraries/jshrink/minifier' );
		}

		// Loop thru scripts to compress.
		global $wp_filesystem;

		foreach ( $scripts as $handle => $sources ) {
			// Generate name for compression file.
			$compression = $path . '/' . md5( implode( $sources ) ) . '.js';

			// Make sure compression file not outdated if exists.
			unset( $num_files );

			if ( ! @is_file( $compression ) || filemtime( $compression ) < $last_modification[ $handle ] ) {
				// Loop thru script files to compress and combine.
				$compressed = '';
				$num_files  = 1;
				$max_size   = $wr_nitro_options['max_compression_size'] * 1000;

				foreach ( $sources as $source ) {
					// Read script file.
					$compressed .= ( $compressed == '' ? '' : "\n" );
					$compressed .= "/* {$source} */\n";

					if ( false !== strpos( $source, '.min.js' ) ) {
						$compressed .= ( $buffer = call_user_func('file_' . 'get' . '_contents', ABSPATH . $source) ) ? $buffer : $wp_filesystem->get_contents(ABSPATH . $source);
					} else {
						$buffer = ( $buffer = call_user_func('file_' . 'get' . '_contents', ABSPATH . $source) ) ? $buffer : $wp_filesystem->get_contents(ABSPATH . $source);

						try {
							$tmp = JShrink_Minifier::minify( $buffer, array( 'flaggedComments' => false ) );
						} catch (Exception $e) {
							$tmp = $buffer;
						}

						$compressed .= $tmp;
					}

					$compressed .= "\n";

					// Check if compression size is greater than the maximum allowed size.
					if ( strlen( $compressed ) >= $max_size ) {
						if ( $num_files == 1 ) {
							if ( ! ( call_user_func( 'file_' . 'put' . '_contents', $compression, $compressed) || $wp_filesystem->put_contents($compression, $compressed) ) ) {
								return;
							}
						} else {
							$nextFile = substr($compression, 0, -3) . "-{$num_files}.js";

							if ( ! ( call_user_func( 'file_' . 'put' . '_contents', $nextFile, $compressed) || $wp_filesystem->put_contents($nextFile, $compressed) ) ) {
								return;
							}
						}

						// Increase the total number of compression file.
						$num_files++;

						// Reset compressed content.
						$compressed = '';
					}
				}

				// Write compressed content to compression file.
				if ( $compressed != '' ) {
					if ( $num_files == 1 ) {
						if ( ! ( call_user_func( 'file_' . 'put' . '_contents', $compression, $compressed) || $wp_filesystem->put_contents($compression, $compressed) ) ) {
							return;
						}
					} else {
						$nextFile = substr($compression, 0, -3) . "-{$num_files}.js";

						if ( ! ( call_user_func( 'file_' . 'put' . '_contents', $nextFile, $compressed) || $wp_filesystem->put_contents($nextFile, $compressed) ) ) {
							return;
						}
					}
				} elseif ( $num_files > 1 ) {
					$num_files--;
				}
			}

			// Load compression file.
			if ( ! isset( $num_files ) ) {
				$num_files = glob( substr( $compression, 0, -3 ) . '*.js' );
				$num_files = count( $num_files );
			}

			$position    = substr( $handle, 0, strpos( $handle, '-' ) );
			$handling    = $handle . ( $num_files > 1 ? '-1' : '' );
			$compression = str_replace( $upload['basedir'], $upload['baseurl'], $compression );

			$wp_scripts->add( $handling, $compression, array(), false, null );

			if ( 'footer' == $position ) {
				$wp_scripts->add_data( $handling, 'group', 1 );
			}

			// Set associated inline scripts.
			if ( isset( $inline[ $handle ] ) ) {
				$wp_scripts->add_data( $handling, 'data', implode( "\n", $inline[ $handle ] ) );
			}

			// If there are more than 1 compression file, register every file depends on the preceding one.
			if ( $num_files > 1 ) {
				for ( $i = 2; $i <= $num_files; $i++ ) {
					$handling = $handle . ( $i == $num_files ? '' : "-{$i}" );
					$compress = substr( $compression, 0, -3 ) . "-{$i}.js";
					$depends  = array( $handle . '-' . ( $i - 1 ) );

					$wp_scripts->add( $handling, $compress, $depends, false, null );

					if ( 'footer' == $position ) {
						$wp_scripts->add_data( $handling, 'group', 1 );
					}
				}
			}
		}

		// Remove all original scripts by unsetting required values.
		foreach ( self::$compressed_handles as $handle ) {
			$wp_scripts->registered[ $handle ]->src  = false;
			$wp_scripts->registered[ $handle ]->deps = array();
		}

		// Reset scripts queue.
		$wp_scripts->reset();

		$wp_scripts->queue = $queue;
		$wp_scripts->to_do = array();
	}

	/**
	 * Method to print inline scripts.
	 *
	 * @return  void
	 */
	public static function print_inline_scripts() {
		// Pass lazy loading list to client-side.
		$inline_scripts[] = 'var nitro_lazy_load_sources = ' . wp_unslash( json_encode( self::$lazy_load_sources ) );

		// Load minification library.
		if ( ! class_exists( 'JShrink_Minifier' ) ) {
			get_template_part( 'libraries/jshrink/minifier' );
		}

		// Print inline scripts.
		if ( count( $inline_scripts ) ) {
			echo '<scr' . 'ipt type="text/javascript">' . JShrink_Minifier::minify( implode( "\n", $inline_scripts ) ) . "</scr"."ipt>\n";
		}
	}

	/**
	 * Method to get the location to store compressed assets files.
	 *
	 * @return  mixed
	 */
	public static function get_location() {
		// Generate path to directory to store compressed assets files.
		$path = wp_upload_dir();
		$path = $path['basedir'] . '/' . self::$directory;

		// Verify directory.
		if ( ! @is_dir( $path ) ) {
			global $wp_filesystem;

			if ( ! ( wp_mkdir_p($path) || call_user_func(array($wp_filesystem, 'mk' . 'dir'), $path) ) ) {
				return false;
			}
		}

		return $path;
	}

	/**
	 * Method to minify CSS code.
	 *
	 * @param   string  $stylesheet  Either path to stylesheet file or CSS code to be minified.
	 *
	 * @return  string
	 */
	public static function minify_css( $stylesheet ) {
		global $wp_filesystem;

		// Minify stylesheet file.
		$css = ( is_file($stylesheet) || $wp_filesystem->is_file($stylesheet) )
			? ( ( $buffer = call_user_func('file_' . 'get' . '_contents', $stylesheet) ) ? $buffer : $wp_filesystem->get_contents($stylesheet) )
			: $stylesheet;

		$css = preg_replace( '#\s+#', ' ', $css );
		$css = preg_replace( '#/\*.*?\*/#s', '', $css );
		$css = str_replace( '; ', ';', $css );
		$css = str_replace( ': ', ':', $css );
		$css = str_replace( ' {', '{', $css );
		$css = str_replace( '{ ', '{', $css );
		$css = str_replace( ', ', ',', $css );
		$css = str_replace( '} ', '}', $css );
		$css = str_replace( ';}', '}', $css );

		// Rewrite all relative URLs if stylesheet file is specififed.
		if ( @is_file( $stylesheet ) ) {
			$parts = preg_split( '/url\s*\([\s\'"]*/i', $css );
			$n     = count( $parts );

			// If the stylesheet has link, convert relative path to absolute URL.
			if ( $n > 1 ) {
				$dir_path = implode( '/', array_slice( explode( '/', str_replace( '\\', '/', $stylesheet ) ), 0, -1 ) );
				$abs_url  = str_replace( ABSPATH, site_url() . '/', $dir_path );
				$css      = $parts[0];

				for ( $i = 1; $i < $n; $i++ ) {
					// Get the link.
					list( $url, $tmp ) = explode( ')', $parts[ $i ], 2 );

					// Prepare the link.
					$url = current( preg_split( '/[\?\'"]/', $url ) );

					// Ignore inline data.
					if ( false !== strpos( $url, 'data:' ) ) {
						$css .= 'url(' . $url . ')' . $tmp;

						continue;
					}

					// Ignore absolute URL.
					if ( preg_match( '#^(https?:)*//#', $url ) ) {
						$css .= 'url(' . $url . ')' . $tmp;

						continue;
					}

					// Convert relative path to absolute URL.
					$items = explode( '/', $url );
					$url   = $abs_url;

					for ( $j = 0; $j < count( $items ); $j++ ) {
						if ( '.' == $items[ $j ] ) {
							unset( $items[ $j ] );
						} elseif ( '..' == $items[ $j ] ) {
							$url = implode( '/', array_slice( explode( '/', str_replace( '\\', '/', $url ) ), 0, -1 ) );

							unset( $items[ $j ] );
						}
					}

					$url .= '/' . implode( '/', $items );

					// Replace the relative path with the absolute URL.
					$css .= 'url(' . $url . ')' . $tmp;
				}
			}
		}

		return trim( $css );
	}

	/**
	 * Embed inline script.
	 *
	 * @return  array
	 */
	public static function localize_links() {
		return array(
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'admin_root' => admin_url(),
			'site_url'   => site_url(),
			'theme_url'  => get_template_directory_uri(),
			'_nonce'     => wp_create_nonce( 'wr_nitro_nonce_check' ),
		);
	}
}
