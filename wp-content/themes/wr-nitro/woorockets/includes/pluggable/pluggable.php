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
 * Pluggable initialization class.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Pluggable {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	/**
	 * Variable to hold required / recommended plugins.
	 *
	 * @var  array
	 */
	public static $plugins;

	/**
	 * Define options allowed to backup / restore.
	 *
	 * @var  array
	 */
	protected static $options;

	/**
	 * Initialize pluggable functions.
	 *
	 * @return  void
	 */
	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}

		self::set_user_agent();

		// Register neccessary actions to setup theme.
		add_action( 'after_setup_theme', array( __CLASS__, 'after_setup_theme'       ) );
		add_action( 'tgmpa_register'   , array( __CLASS__, 'tgmpa_register'          ) );
		add_action( 'init'             , array( __CLASS__, 'widgets_init'            ) );
		add_action( 'template_redirect', array( __CLASS__, 'under_construction_page' ) );
		add_action( 'wr_activate'      , array( __CLASS__, 'active_required_plugin'  ) );
		add_action( 'init'             , array( __CLASS__, 'plugins_wp_super_cache'  ) );

		// Register Ajax action to backup / restore settings.
		add_action( 'wp_ajax_nitro_backup_settings' , array( __CLASS__, 'backup_settings'  ) );
		add_action( 'wp_ajax_nitro_restore_settings', array( __CLASS__, 'restore_settings' ) );

		// Register filter to prepare theme option values.
		add_filter( 'option_theme_mods_' . get_stylesheet()           , array( __CLASS__, 'prepare_settings' ) );
		add_filter( 'pre_update_option_theme_mods_' . get_stylesheet(), array( __CLASS__, 'prepare_settings' ) );

		// Register Ajax action to install / uninstall plugin.
		add_action( 'wp_ajax_nitro_install_plugin'       , array( __CLASS__, 'install_plugin'   ) );
		add_action( 'wp_ajax_nopriv_nitro_install_plugin', array( __CLASS__, 'install_plugin'   ) );
		add_action( 'wp_ajax_nitro_uninstall_plugin'     , array( __CLASS__, 'uninstall_plugin' ) );

		// Register filter to add more supported MIME types.
		add_filter( 'upload_mimes', array( __CLASS__, 'upload_mimes' ) );

		// Plug into WordPress Theme Customize.
		add_action( 'customize_register'                , array( 'WR_Nitro_Customize', 'initialize'               ) );
		add_action( 'customize_controls_enqueue_scripts', array( 'WR_Nitro_Customize', 'customize_assets'         ) );
		add_action( 'customize_controls_print_scripts'  , array( 'WR_Nitro_Customize', 'create_loading_mask'      ) );
		add_action( 'customize_preview_init'            , array( 'WR_Nitro_Customize', 'customize_preview_assets' ) );
		add_action( 'customize_save_after'              , array( 'WR_Nitro_Customize', 'post_save_theme_mods'     ) );

		// Setup actions / filters to render theme.
		add_action( 'wp_enqueue_scripts', array( 'WR_Nitro_Render', 'enqueue_scripts'   ), 10000 ); // Priority more than Nitro_Toolkit_Shortcode::enqueue_scripts
		add_action( 'wp_enqueue_scripts', array( 'WR_Nitro_Render', 'custom_styles'     ), 10001 );
		add_action( 'wp_footer'         , array( 'WR_Nitro_Render', 'custom_inline_js'  )        );

		add_action( 'wp_ajax_nopriv_get_google_plus_count', array( 'WR_Nitro_Render', 'get_google_plus_count' ) );
		add_action( 'wp_ajax_get_google_plus_count'       , array( 'WR_Nitro_Render', 'get_google_plus_count' ) );
		add_action( 'wp_ajax_get_google_plus_count'       , array( 'WR_Nitro_Render', 'get_google_plus_count' ) );

		add_filter( 'body_class' , array( 'WR_Nitro_Render', 'body_class' ) );

		// Add setting enable AJAX add to cart buttons on single product
		add_filter( 'woocommerce_product_settings' , array( __CLASS__, 'setting_ajax_single_product' ) );

		// Add custom image size.
		add_image_size( '60x60'  , 60 , 60 , true );
		add_image_size( '370x480', 370, 480, true );
		add_image_size( '405x300', 405, 300, true );
		add_image_size( '420x521', 420, 521, true );
		add_image_size( '450x450', 450, 450, true );
		add_image_size( '450x900', 450, 900, true );
		add_image_size( '585x400', 585, 400, true );

		// Init file-system handler.
		self::init_file_system();

		// Initialize Custom Widgets.
		WR_Nitro_Widgets::initialize();

		if ( ! defined( 'DOING_AJAX' ) ) {
			// Initialize assets compression.
			WR_Nitro_Assets::initialize();

			// Initialize automatic update.
			WR_Nitro_Update::initialize();

			if ( is_admin() ) {
				// Initialize meta boxes.
				if ( class_exists( 'RW_Meta_Box' ) ) {
					WR_Nitro_Meta_Box::initialize();
				}
			}
		}

		// Initialize Mega Menu.
		WR_Nitro_Megamenu::initialize();

		// Initialize sample data installation.
		WR_Nitro_Sample_Data::initialize();

		// Initialize sidebars.
		WR_Nitro_Sidebar::initialize();

		if ( is_admin() ) {
			// Initialize Header Builder.
			WR_Nitro_Header_Builder::initialize();

			// Initialize welcome page.
			WR_Nitro_Welcome::initialize();

			// Register Ajax action to enable / disable usage data collector.
			add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
			add_action( 'wp_ajax_nitro_enable_collector', array( __CLASS__, 'enable_data_collector' ) );
		} else {
			// Add admin header nodes.
			add_action('wp_before_admin_bar_render', array( 'WR_Nitro_Header_Builder', 'edit_header_in_bar' ) );
		}

		// Plug into Visual Composer if available.
		if ( class_exists( 'VC_Manager' ) ) {
			WR_Nitro_Pluggable_Visual_Composer::initialize();
		}

		// Plug into WooCommerce if available.
		if ( class_exists( 'WooCommerce' ) ) {
			WR_Nitro_Pluggable_WooCommerce::initialize();
		}

		// State that initialization completed.
		self::$initialized = true;
	}

	/**
	 * Add setting enable AJAX add to cart buttons on single product.
	 *
	 * @param array $settings
	 *
	 * @return  array.
	 */
	public static function setting_ajax_single_product( $settings ) {

		$data = array();

		if( $settings ) {
			foreach( $settings as $val ){
				if( isset( $val['id'] ) && $val['id'] == 'woocommerce_enable_ajax_add_to_cart' ) {
					// Set position center
					$val['checkboxgroup'] = '';

					$data[] = $val;

					$data[] = array(
						'desc'          => __( 'Enable AJAX add to cart buttons on single', 'wr-nitro' ),
						'id'            => 'woocommerce_enable_ajax_add_to_cart_single',
						'default'       => 'yes',
						'type'          => 'checkbox',
						'checkboxgroup' => 'end'
					);
				} else {
					$data[] = $val;
				}
			}

		}

		return $data;
	}

	/**
	 * Setup theme.
	 *
	 * @return  void
	 */
	public static function after_setup_theme() {
		// Load language translation.
		load_theme_textdomain( 'wr-nitro', get_template_directory() . '/languages' );

		// Indicate widget sidebars can use selective refresh in the Customizer.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Support WooCommerce plugin.
		add_theme_support( 'woocommerce' );

		// Enable support for Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// Add Excerpts to Your Pages
		add_post_type_support( 'page', 'excerpt' );

		// Switch default core markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' )
		);

		// Add supported post format.
		add_theme_support(
			'post-formats',
			array( 'gallery', 'video', 'quote', 'audio' )
		);

		// Register nav menu locations.
		register_nav_menus(
			array(
				'main_menu'   => esc_html__( 'Main Menu', 'wr-nitro' ),
			)
		);

		// Tell TinyMCE editor to use a custom stylesheet.
		add_editor_style();

		// Support the shortcode in the excerpt
		add_filter( 'the_excerpt', 'shortcode_unautop');
		add_filter( 'the_excerpt', 'do_shortcode');
	}

	/**
	 * Register required and recommended plugins.
	 *
	 * @param   boolean  $all  Whether to register all plugins or just plugins the installed demo depends on.
	 *
	 * @return  void
	 */
	public static function tgmpa_register( $all = false ) {
		// Define list of required and recommended plugins.
		
			self::$plugins = array(
				'js_composer' => array(
					'name'               => 'Visual Composer',
					'slug'               => 'js_composer',
					'thumb'              => 'http://www.woorockets.com/files/plugins/visual-composer.png',
					'source'             => 'http://www.woorockets.com/files/plugins/js_composer.zip',
					'link'               => 'https://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431',
					'source_type'        => 'external',
					'version'            => '6.5.0',
					'required'           => true,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'nitro-toolkit' => array(
					'name'               => 'Nitro Toolkit',
					'slug'               => 'nitro-toolkit',
					'thumb'              => 'http://www.woorockets.com/files/plugins/nitro-toolkit.png',
					'source'             => 'http://www.woorockets.com/files/plugins/nitro-toolkit.zip',
					'source_type'        => 'external',
					'version'            => '1.1.13',
					'required'           => true,
					'force_activation'   => false,
					'force_deactivation' => true,
				),

				'nitro-gallery' => array(
					'name'               => 'Nitro Gallery',
					'slug'               => 'nitro-gallery',
					'thumb'              => 'http://www.woorockets.com/files/plugins/gallery.png',
					'source'             => 'http://www.woorockets.com/files/plugins/nitro-gallery.zip',
					'source_type'        => 'external',
					'version'            => '1.0.4',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'meta-box' => array(
					'name'               => 'Meta Boxes',
					'slug'               => 'meta-box',
					'thumb'              => 'http://www.woorockets.com/files/plugins/metabox.png',
					'source_type'        => 'repo',
					'version'            => '5.1.2',
					'required'           => true,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'meta-box-conditional-logic' => array(
					'name'               => 'Meta Boxes Conditional Logic',
					'slug'               => 'meta-box-conditional-logic',
					'thumb'              => 'http://www.woorockets.com/files/plugins/metabox.png',
					'source'             => 'http://www.woorockets.com/files/plugins/meta-box-conditional-logic.zip',
					'source_type'        => 'external',
					'version'            => '1.3.4',
					'required'           => true,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'revslider' => array(
					'name'               => 'Revolution Slider',
					'slug'               => 'revslider',
					'thumb'              => 'http://www.woorockets.com/files/plugins/revslider.png',
					'source'             => 'http://www.woorockets.com/files/plugins/revslider.zip',
					'link'               => 'https://codecanyon.net/item/slider-revolution-responsive-wordpress-plugin/2751380',
					'source_type'        => 'external',
					'version'            => '6.3.9',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'contact-form-7' => array(
					'name'               => 'Contact Form 7',
					'slug'               => 'contact-form-7',
					'thumb'              => 'http://www.woorockets.com/files/plugins/contact-form-7.jpg',
					'source_type'        => 'repo',
					'version'            => '4.9.1',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'wr-custom-attributes' => array(
					'name'               => 'Custom Attributes',
					'slug'               => 'wr-custom-attributes',
					'thumb'              => 'http://www.woorockets.com/files/plugins/custom-attributes.png',
					'source'             => 'http://www.woorockets.com/files/plugins/wr-custom-attributes.zip',
					'source_type'        => 'external',
					'version'            => '1.1.11',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'wr-in-stock-alert' => array(
					'name'               => 'In Stock Alert',
					'slug'               => 'wr-in-stock-alert',
					'thumb'              => 'http://www.woorockets.com/files/plugins/in-stock-alert.png',
					'source'             => 'http://www.woorockets.com/files/plugins/wr-in-stock-alert.zip',
					'source_type'        => 'external',
					'version'            => '1.0.9',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'wr-live-search' => array(
					'name'               => 'Live Search',
					'slug'               => 'wr-live-search',
					'thumb'              => 'http://www.woorockets.com/files/plugins/live-search.png',
					'source'             => 'http://www.woorockets.com/files/plugins/wr-live-search.zip',
					'source_type'        => 'external',
					'version'            => '1.0.11',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'wr-share-for-discounts' => array(
					'name'               => 'Share For Discounts',
					'slug'               => 'wr-share-for-discounts',
					'thumb'              => 'http://www.woorockets.com/files/plugins/share-4-discount.png',
					'source'             => 'http://www.woorockets.com/files/plugins/wr-share-for-discounts.zip',
					'source_type'        => 'external',
					'version'            => '1.0.11',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'wr-mapper' => array(
					'name'               => 'Product Mapper',
					'slug'               => 'wr-mapper',
					'thumb'              => 'http://www.woorockets.com/files/plugins/product-mapper.png',
					'source'             => 'http://www.woorockets.com/files/plugins/wr-mapper.zip',
					'source_type'        => 'external',
					'version'            => '1.0.12',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'wr-currency' => array(
					'name'               => 'Currency Converter',
					'slug'               => 'wr-currency',
					'thumb'              => 'http://www.woorockets.com/files/plugins/woorockets.png',
					'source'             => 'http://www.woorockets.com/files/plugins/wr-currency.zip',
					'source_type'        => 'external',
					'version'            => '1.0.11',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'woocommerce-gateway-stripe' => array(
					'name'               => 'Stripe',
					'slug'               => 'woocommerce-gateway-stripe',
					'thumb'              => 'http://www.woorockets.com/files/plugins/stripe.jpg',
					'source_type'        => 'repo',
					'version'            => '3.2.3',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'woocommerce' => array(
					'name'               => 'WooCommerce',
					'slug'               => 'woocommerce',
					'thumb'              => 'http://www.woorockets.com/files/plugins/woocommerce.png',
					'source_type'        => 'repo',
					'version'            => '3.2.4',
					'required'           => true,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'yith-woocommerce-wishlist' => array(
					'name'               => 'YITH WooCommerce Wishlist',
					'slug'               => 'yith-woocommerce-wishlist',
					'thumb'              => 'http://www.woorockets.com/files/plugins/yith-wishlist.png',
					'source_type'        => 'repo',
					'version'            => '2.1.2',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'woocommerce-products-filter' => array(
					'name'               => 'Advanced Products Filter',
					'slug'               => 'woocommerce-products-filter',
					'thumb'              => 'http://www.woorockets.com/files/plugins/products-filter.jpg',
					'source'             => 'http://www.woorockets.com/files/plugins/woocommerce-products-filter.zip',
					'link'               => 'https://codecanyon.net/item/woof-woocommerce-products-filter/11498469',
					'source_type'        => 'external',
					'version'            => '2.2.4.2',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'sizeguide' => array(
					'name'               => 'Size Guide',
					'slug'               => 'sizeguide',
					'thumb'              => 'http://www.woorockets.com/files/plugins/sizeguide.jpg',
					'source'             => 'http://www.woorockets.com/files/plugins/sizeguide.zip',
					'link'               => 'https://codecanyon.net/item/woocommerce-product-size-guide/9168678',
					'source_type'        => 'external',
					'version'            => '3.6',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),

				'arscode-ninja-popups' => array(
					'name'               => 'Ninja Popup',
					'slug'               => 'arscode-ninja-popups',
					'thumb'              => 'http://www.woorockets.com/files/plugins/ninja-popups.jpg',
					'source'             => 'http://www.woorockets.com/files/plugins/arscode-ninja-popups.zip',
					'link'               => 'https://codecanyon.net/item/ninja-popups-for-wordpress/3476479',
					'source_type'        => 'external',
					'version'            => '4.7.2',
					'required'           => false,
					'force_activation'   => false,
					'force_deactivation' => false,
				),
			);

			// Get download token.
			$token = WR_Nitro_Update::get_download_token();

			if ( $token ) {
				$token = md5( $token );
			}

			// Prepare plugins.
			foreach ( self::$plugins as $slug => $define ) {
				if ( isset( $define['source'] ) ) {
					// Update source URL.
					self::$plugins[ $slug ]['source'] = add_query_arg(
						array(
							'plugin' => substr( current( array_slice( explode( '/', $define['source'] ), -1 ) ), 0, -4 ),
							'token'  => $token,
						),
						WR_Nitro_Update::ENVATO_APP_SERVER
					);

					if ( ! isset( $define['source_type'] ) ) {
						// Define source type.
						if ( preg_match( '#^https?://#', $define['source_type'] ) ) {
							self::$plugins[ $slug ]['source_type'] = 'external';
						} else {
							self::$plugins[ $slug ]['source_type'] = 'bundled';
						}
					}
				}
			}

			if ( ! $all ) {
				// Get sample data dependencies.
				$dependencies = get_transient( 'wr_nitro_dependencies' );

				if ( $dependencies ) {
					foreach ( $dependencies as $slug => $define ) {
						if ( array_key_exists( $slug, self::$plugins ) ) {
							$dependencies[ $slug ] = self::$plugins[ $slug ];
						} else {
							// Update source URL.
							$dependencies[ $slug ]['source'] = add_query_arg(
								array(
									'plugin' => substr( current( array_slice( explode( '/', $define['source'] ), -1 ) ), 0, -4 ),
									'token'  => $token,
								),
								WR_Nitro_Update::ENVATO_APP_SERVER
							);

							if ( ! isset( $define['source_type'] ) ) {
								// Define source type.
								if ( preg_match( '#^https?://#', $define['source_type'] ) ) {
									$dependencies[ $slug ]['source_type'] = 'external';
								} else {
									$dependencies[ $slug ]['source_type'] = 'bundled';
								}
							}
						}
					}
				} else {
					$dependencies = self::$plugins;
				}
			}
		

		// Define configuration for TGM Plugin Activation.
		$config = array(
			'id'           => 'tgmpa',
			'default_path' => '',
			'menu'         => 'tgmpa-install-plugins',
			'parent_slug'  => '',
			'capability'   => 'edit_theme_options',
			'has_notices'  => true,
			'dismissable'  => true,
			'dismiss_msg'  => '',
			'is_automatic' => false,
			'message'      => '',
			'strings'      => array(
				'page_title' => esc_html__( 'Install Required Plugins', 'wr-nitro' ),
				'menu_title' => esc_html__( 'Install Plugins', 'wr-nitro' ),
				'installing' => esc_html__( 'Installing Plugin: %s', 'wr-nitro' ),
				'oops'       => esc_html__( 'Something went wrong with the plugin API.', 'wr-nitro' ),

				'notice_can_install_required' => _n_noop(
					'This theme requires the following plugin: %1$s.',
					'This theme requires the following plugins: %1$s.',
					'wr-nitro'
				),
				'notice_can_install_recommended' => _n_noop(
					'This theme recommends the following plugin: %1$s.',
					'This theme recommends the following plugins: %1$s.',
					'wr-nitro'
				),
				'notice_cannot_install' => _n_noop(
					'Sorry, but you do not have the correct permissions to install the %1$s plugin.',
					'Sorry, but you do not have the correct permissions to install the %1$s plugins.',
					'wr-nitro'
				),
				'notice_ask_to_update' => _n_noop(
					'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
					'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
					'wr-nitro'
				),
				'notice_ask_to_update_maybe' => _n_noop(
					'There is an update available for: %1$s.',
					'There are updates available for the following plugins: %1$s.',
					'wr-nitro'
				),
				'notice_cannot_update' => _n_noop(
					'Sorry, but you do not have the correct permissions to update the %1$s plugin.',
					'Sorry, but you do not have the correct permissions to update the %1$s plugins.',
					'wr-nitro'
				),
				'notice_can_activate_required' => _n_noop(
					'The following required plugin is currently inactive: %1$s.',
					'The following required plugins are currently inactive: %1$s.',
					'wr-nitro'
				),
				'notice_can_activate_recommended' => _n_noop(
					'The following recommended plugin is currently inactive: %1$s.',
					'The following recommended plugins are currently inactive: %1$s.',
					'wr-nitro'
				),
				'notice_cannot_activate' => _n_noop(
					'Sorry, but you do not have the correct permissions to activate the %1$s plugin.',
					'Sorry, but you do not have the correct permissions to activate the %1$s plugins.',
					'wr-nitro'
				),
				'install_link' => _n_noop(
					'Begin installing plugin',
					'Begin installing plugins',
					'wr-nitro'
				),
				'update_link' => _n_noop(
					'Begin updating plugin',
					'Begin updating plugins',
					'wr-nitro'
				),
				'activate_link' => _n_noop(
					'Begin activating plugin',
					'Begin activating plugins',
					'wr-nitro'
				),

				'return'                      => esc_html__( 'Return to Required Plugins Installer', 'wr-nitro' ),
				'plugin_activated'            => esc_html__( 'Plugin activated successfully.', 'wr-nitro' ),
				'activated_successfully'      => esc_html__( 'The following plugin was activated successfully:', 'wr-nitro' ),
				'plugin_already_active'       => esc_html__( 'No action taken. Plugin %1$s was already active.', 'wr-nitro' ),
				'plugin_needs_higher_version' => esc_html__( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'wr-nitro' ),
				'complete'                    => esc_html__( 'All plugins installed and activated successfully. %1$s', 'wr-nitro' ),
				'contact_admin'               => esc_html__( 'Please contact the administrator of this site for help.', 'wr-nitro' ),
				'nag_type'                    => 'updated',
			),
		);

		tgmpa( $all ? self::$plugins : $dependencies, $config );
	}

	/**
	 * Active required plugin
	 *
	 * @return  void
	 */
	public static function active_required_plugin() {
		activate_plugin( 'nitro-toolkit/nitro-toolkit.php' );
	}

	/**
	 * Register widget area.
	 *
	 * @return  void
	 */
	public static function widgets_init() {
		$wr_nitro_options = WR_Nitro::get_options();

		// Get setting of sidebar in footer
		$sidebar = $wr_nitro_options['footer_layout'];

		// Register primary sidebar.
		register_sidebar(
			array(
				'name'          => esc_html__( 'Primary Sidebar', 'wr-nitro' ),
				'id'            => 'primary-sidebar',
				'description'   => esc_html__( 'This is the primary sidebar if you are using a two columns site layout option.', 'wr-nitro' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		// Register footer sidebars.
		for ( $i = 1, $n = 5; $i <= $n; $i++ ) {
			register_sidebar(
				array(
					'name'          => esc_html__( 'Footer Area #', 'wr-nitro' ) . $i,
					'id'            => 'footer-' . $i,
					'description'   => sprintf( __( 'The #%s column in footer area', 'wr-nitro' ), $i ),
					'before_widget' => '<aside id="%1$s" class="widget %2$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h3 class="widget-title">',
					'after_title'   => '</h3>',
				)
			);
		}

		// Register canvas sidebar.
		register_sidebar(
			array(
				'name'          => esc_html__( 'Canvas Sidebar', 'wr-nitro' ),
				'id'            => 'canvas-sidebar',
				'description'   => sprintf( __( 'Canvas sidebar', 'wr-nitro' ) ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}

	/**
	 * Redirect to under construction page
	 *
	 * @return  void
	 */
	public static function under_construction_page() {
		$wr_nitro_options = WR_Nitro::get_options();

		// Check if under construction page is enabled
		if ( $wr_nitro_options['under_construction'] ) {
			if ( ! is_feed() ) {
				// Check if user is not logged in
				if ( ! is_user_logged_in() ) {
					// Load under construction page
					WR_Nitro_Render::get_template( 'common/under', 'construction' );
					exit;
				}
			}

			// Check if user is logged in
			if ( is_user_logged_in() ) {

				// Get current user
				$current_user = wp_get_current_user();
				$user_roles = $current_user->roles[0];

				// If user role is not 'administrator' then redirect to under construction page
				if ( 'administrator' != $user_roles ) {
					if ( ! is_feed() ) {
						WR_Nitro_Render::get_template( 'common/under', 'construction' );
						exit;
					}
				}
			}
		}
	}

	/**
	 * Method to backup Nitro theme customize settings.
	 *
	 * @return  void
	 */
	public static function backup_settings() {
		// Verify nonce.
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'nitro_backup_settings' ) ) {
			exit( __( 'Nonce verification failed. This might due to your working session has been expired. <a href="javascript:window.location.reload();">Click here to refresh the page to renew your working session</a>.', 'wr-nitro' ) );
		}

		// Get options to backup.
		if ( ! isset( self::$options ) ) {
			self::$options = apply_filters(
				'wr_nitro_backup_options',
				array(
					'theme_mods_' . get_template()
				)
			);
		}

		// Get current settings for all options.
		$content = array();

		foreach ( self::$options as $option ) {
			$value = null;

			// Allow backup child theme settings.
			if ( 0 === strpos( $option, 'theme_mods_' ) ) {
				// Get theme data.
				$theme = wp_get_theme();

				if ( $theme->get_template() != $theme->get_stylesheet() ) {
					$value = get_option( 'theme_mods_' . $theme->get_stylesheet() );
				}
			}

			$content[ $option ] = is_null( $value ) ? get_option( $option ) : $value;
		}

		// Generate output content.
		$content = json_encode( $content );

		// Clear all output buffering
		while ( ob_get_level() ) {
			ob_end_clean();
		}

		// Send inline download header.
		header( 'Content-Type: application/json; charset=utf-8'                 );
		header( 'Content-Length: ' . strlen( $content )                         );
		header( 'Content-Disposition: attachment; filename=nitro-settings.json' );
		header( 'Cache-Control: no-cache, must-revalidate, max-age=60'          );
		header( 'Expires: Sat, 01 Jan 2000 12:00:00 GMT'                        );

		// Print output content.
		echo '' . $content;

		// Exit immediately to prevent WordPress from processing further.
		exit;
	}

	/**
	 * Method to backup Nitro theme customize settings.
	 *
	 * @return  void
	 */
	public static function restore_settings() {
		// Verify nonce.
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'nitro_restore_settings' ) ) {
			wp_send_json_error( __( 'Nonce verification failed. This might due to your working session has been expired. <a href="javascript:window.location.reload();">Click here to refresh the page to renew your working session</a>.', 'wr-nitro' ) );
		}

		// Verify uploaded file.
		if ( ! isset( $_REQUEST['file'] ) || empty( $_REQUEST['file'] ) ) {
			wp_send_json_error( __( 'Missing backup file.', 'wr-nitro' ) );
		}

		// Read settings from uploaded file.
		$settings = wp_remote_get( $_REQUEST['file'] );

		if ( ! $settings || is_wp_error( $settings ) ) {
			wp_send_json_error( __( 'Failed to read backup file.', 'wr-nitro' ) );
		}

		$settings = json_decode( $settings['body'], true );

		if ( ! $settings ) {
			wp_send_json_error( $settings );
		}

		// Get options to restore.
		if ( ! isset( self::$options ) ) {
			self::$options = apply_filters(
				'wr_nitro_restore_options',
				array(
					'theme_mods_' . get_template()
				)
			);
		}

		// Restore settings.
		$error = array();

		foreach ( $settings as $option => $value ) {
			// Verify option.
			if ( ! in_array( $option, self::$options ) ) {
				$error[] = sprintf( __( 'The option &#39;%s&#39; is not supported.', 'wr-nitro' ), $option );

				continue;
			}

			// Allow restore parent settings to child theme.
			if ( 0 === strpos( $option, 'theme_mods_' ) ) {
				// Get theme data.
				$theme = wp_get_theme();

				if ( $theme->get_template() != $theme->get_stylesheet() ) {
					$option = 'theme_mods_' . $theme->get_stylesheet();
				}
			}

			update_option( $option, $value );
		}

		wp_send_json_success();
	}

	/**
	 * Prepare theme option values.
	 *
	 * @param   array  $options  Current theme options.
	 *
	 * @return  array
	 */
	public static function prepare_settings( $options ) {
		foreach ( $options as $opt => $value ) {
			if ( is_array( $value ) ) {
				$value = self::prepare_settings( $value );
			}

			elseif ( is_string( $value ) ) {
				if ( 0 === strpos( doing_filter(), 'pre_update_' ) ) {
					if ( preg_match( '/\\(f\d+)/', $value, $match ) ) {
						$value = str_replace( $match[0], '\\\\' . $match[1], $value );
					}
				}

				else {
					if ( preg_match( '/\\\\\\\\(f\d+)/', $value, $match ) ) {
						$value = str_replace( $match[0], '\\' . $match[1], $value );
					}

					elseif ( preg_match( '/\\\\(f\d+)/', $value, $match ) ) {
						$value = str_replace( $match[0], '\\' . $match[1], $value );
					}
				}

				$options[ $opt ] = $value;
			}
		}

		return $options;
	}

	/**
	 * Method to install and activate plugin.
	 *
	 * @return  void
	 */
	public static function install_plugin() {
		// Verify nonce.
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'nitro-install-plugin' ) ) {
			wp_send_json_error( 'NONCE_EXPIRED' );
		}

		// Verify request variables.
		if ( ! isset( $_POST['plugin'] ) || empty( $_POST['plugin'] ) ) {
			wp_send_json_error( __( 'No plugin specified.', 'wr-nitro' ) );
		}

		// Disable error reporting.
		error_reporting( 0 );

		// Initialize TGM Plugin Activation.
		self::tgmpa_register( true );

		$tgmpa = TGM_Plugin_Activation::get_instance();

		// Emulate request variables to execute bulk install acrion of TGM Plugin Activation.
		$_GET['page'            ] = $_POST['page'            ] = $_REQUEST['page'            ] = $tgmpa->menu;
		$_GET['tgmpa-page'      ] = $_POST['tgmpa-page'      ] = $_REQUEST['tgmpa-page'      ] = $tgmpa->menu;
		$_GET['plugin_status'   ] = $_POST['plugin_status'   ] = $_REQUEST['plugin_status'   ] = 'all';
		$_GET['_wpnonce'        ] = $_POST['_wpnonce'        ] = $_REQUEST['_wpnonce'        ] = wp_create_nonce( 'bulk-plugins' );
		$_GET['_wp_http_referer'] = $_POST['_wp_http_referer'] = $_REQUEST['_wp_http_referer'] = admin_url( "themes.php?page={$tgmpa->menu}" );
		$_GET['action'          ] = $_POST['action'          ] = $_REQUEST['action'          ] = 'tgmpa-bulk-install';
		$_GET['plugin'          ] = $_POST['plugin'          ] = $_REQUEST['plugin'          ] = ( array ) $_POST['plugin'];

		// Verify plugin installation status.
		$plugins = array();

		foreach ( ( array ) $_POST['plugin'] as $plugin ) {
			if ( $path = self::get_plugin_path( $plugin ) ) {
				$result = activate_plugin( $path );

				if ( is_wp_error( $result ) ) {
					$activation_fails[] = self::$plugins[ $plugin ]['name'];
				}

				// Disable Visual Composer welcome page redirection.
				if ( 'js_composer' == $plugin ) {
					delete_transient( '_vc_page_welcome_redirect' );
				}
			} else {
				$plugins[] = $plugin;
			}
		}

		$_POST['plugin'] = $plugins;

		// Increase download timeout.
		function wr_install_plugin_increase_download_timeout( $timeout ) {
			return HOUR_IN_SECONDS;
		}

		add_filter( 'http_request_timeout', 'wr_install_plugin_increase_download_timeout' );

		// Let TGM Plugin Activation install plugins.
		if ( ! class_exists( 'TGM_Bulk_Installer' ) ) {
			tgmpa_load_bulk_installer();
		}

		$tgmpa->install_plugins_page();

		// Verify plugin installation status.
		foreach ( $plugins as $plugin ) {
			if ( $path = self::get_plugin_path( $plugin ) ) {
				$result = activate_plugin( $path );

				if ( is_wp_error( $result ) ) {
					$activation_fails[] = self::$plugins[ $plugin ]['name'];
				}

				// Disable Visual Composer welcome page redirection.
				if ( 'js_composer' == $plugin ) {
					delete_transient( '_vc_page_welcome_redirect' );
				}
			} else {
				$installation_fails[] = self::$plugins[ $plugin ]['name'];
			}
		}

		// Send response.
		if ( isset( $activation_fails ) ) {
			wp_send_json_error(
				( count( $activation_fails ) > 1 )
				? sprintf( __( 'Failed to activate following plugins: %s', 'wr-nitro' ), implode( ', ', $activation_fails ) )
				: sprintf( __( 'Failed to activate %s plugin', 'wr-nitro' ), current( $activation_fails ) )
			);
		}

		elseif ( isset( $installation_fails ) ) {
			wp_send_json_error(
				( count( $installation_fails ) > 1 )
				? sprintf( __( 'Failed to install following plugins: %s', 'wr-nitro' ), implode( ', ', $installation_fails ) )
				: sprintf( __( 'Failed to install %s plugin', 'wr-nitro' ), current( $installation_fails ) )
			);
		}

		wp_send_json_success();
	}

	/**
	 * Method to uninstall plugin.
	 *
	 * @return  void
	 */
	public static function uninstall_plugin() {
		// Verify nonce.
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'nitro-uninstall-plugin' ) ) {
			wp_send_json_error( __( 'Nonce verification failed. This might due to your working session has been expired. Please reload the page to renew your working session.', 'wr-nitro' ) );
		}

		// Verify request variables.
		if ( ! isset( $_POST['plugin'] ) || empty( $_POST['plugin'] ) ) {
			wp_send_json_error( __( 'No plugin specified.', 'wr-nitro' ) );
		}

		// Disable error reporting.
		error_reporting( 0 );

		// Uninstall plugin.
		foreach ( ( array ) $_POST['plugin'] as $plugin ) {
			$path = self::get_plugin_path( $plugin );

			if ( $path ) {
				if ( call_user_func( 'is_' . 'plugin' . '_active', $path ) ) {
					// Deactivate the plugin first.
					deactivate_plugins( $path );
				}

				// Let WordPress uninstall the plugin.
				uninstall_plugin( $path );

				// If the plugin directory still exists, remove it.
				if ( @is_file( WP_PLUGIN_DIR . '/' . $path ) ) {
					global $wp_filesystem;

					$wp_filesystem->rmdir( WP_PLUGIN_DIR . '/' . current( explode( '/', $path ) ), true );
				}
			}
		}

		// Send response.
		wp_send_json_success();
	}

	/**
	 * Add more supported MIME types.
	 *
	 * @param   array  $t  Mime types keyed by the file extension regex corresponding to
	 *                     those types. 'swf' and 'exe' removed from full list. 'htm|html' also
	 *                     removed depending on '$user' capabilities.
	 *
	 * @return  array
	 */
	public static function upload_mimes( $t ) {
		// Add supported font extensions and MIME types.
		$t['eot'  ] = 'application/vnd.ms-fontobject';
		$t['otf'  ] = 'application/x-font-opentype';
		$t['ttf'  ] = 'application/x-font-ttf'; // Or 'application/x-font-truetype'
		$t['woff' ] = 'application/font-woff';
		$t['woff2'] = 'application/font-woff2';

		// Add file extension and MIME type for JSON.
		$t['json'] = 'application/json';

		return $t;
	}

	/**
	 * Method to check if a plugin is activated?
	 *
	 * @param   string  $slug  Plugin folder name or file name without extension.
	 *
	 * @return  Relative path from plugins directory to the plugin's main file if the plugin is activated. Boolean FALSE otherwise.
	 */
	public static function plugin_activated( $slug ) {
		$path = self::get_plugin_path( $slug );

		if ( $path && call_user_func( 'is_' . 'plugin' . '_active', $path ) ) {
			return $path;
		}

		return false;
	}

	/**
	 * Method to get relative path to a plugin's main file.
	 *
	 * @param   string  $slug  Plugin folder name or file name without extension.
	 *
	 * @return  Relative path from plugins directory to the plugin's main file if the plugin is installed. Boolean FALSE otherwise.
	 */
	public static function get_plugin_path( $slug ) {
		if (
			'sizeguide' == $slug
			&&
			@is_file( WP_PLUGIN_DIR . '/sizeguide/ctSizeGuidePlugin.php' )
		) {
			return 'sizeguide/ctSizeGuidePlugin.php';
		}

		if ( @is_file( WP_PLUGIN_DIR . '/' . ( $path = "{$slug}/init.php" ) ) ) {
			return $path;
		}

		if ( @is_file( WP_PLUGIN_DIR . '/' . ( $path = "{$slug}/main.php" ) ) ) {
			return $path;
		}

		if ( @is_file( WP_PLUGIN_DIR . '/' . ( $path = "{$slug}/{$slug}.php" ) ) ) {
			return $path;
		}

		if ( @is_file( WP_PLUGIN_DIR . '/' . ( $path = "{$slug}/wp-{$slug}.php" ) ) ) {
			return $path;
		}

		if ( @is_file( WP_PLUGIN_DIR . '/' . ( $path = $slug .'/' . str_replace( '-', '_', $slug ) . '.php' ) ) ) {
			return $path;
		}

		if ( @is_file( WP_PLUGIN_DIR . '/' . ( $path = $slug .'/' . str_replace( 'wordpress', 'wp', $slug ) . '.php' ) ) ) {
			return $path;
		}

		if ( @is_file( WP_PLUGIN_DIR . '/' . ( $path = "{$slug}.php" ) ) ) {
			return $path;
		}

		if ( @is_file( WP_PLUGIN_DIR . '/' . ( $path = "{$slug}/index.php" ) ) ) {
			return $path;
		}

		return false;
	}

	/**
	 * Connect to file system.
	 *
	 * @return  void
	 */
	protected static function init_file_system() {
		global $wp_filesystem;

		if ( ! isset( $wp_filesystem ) ) {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				include_once ABSPATH . 'wp-admin/includes/file.php';
			}

			WP_Filesystem();
		}
	}

	/**
	 * Add file plugins to WP Super Cache.
	 *
	 * @return  void
	 * @since 1.1.8
	 */
	public static function plugins_wp_super_cache() {
		// Check active plugin
		if ( ! ( call_user_func( 'is_' . 'plugin' . '_active', 'wp-super-cache/wp-cache.php' ) ) ) {
			return;
		}

		$uri_directory = get_template_directory();
		$wp_cache_file = $uri_directory . '/libraries/plugins/wp-super-cache.php';
		$wp_cache_link = $uri_directory . '/../../plugins/wp-super-cache/plugins/wr-nitro-theme.php';

		if ( ! file_exists( $wp_cache_link ) ) {
			global $wp_filesystem;

			$content = ( $content = call_user_func('file_' . 'get' . '_contents', $wp_cache_file) ) ? $content : $wp_filesystem->get_contents($wp_cache_file);

			call_user_func( 'file_' . 'put' . '_contents', $wp_cache_link, $content) || $wp_filesystem->put_contents($wp_cache_link, $content);
		}
	}

	/**
	 * Set HTTP_USER_AGENT is mobile.
	 *
	 * @return  void
	 */
	public static function set_user_agent() {
		if( ! empty( wr_get_server_param( 'HTTP_USER_AGENT' ) ) && ! empty( $_REQUEST['wp_customize'] ) && $_REQUEST['wp_customize'] == 'on' && ! empty( $_REQUEST['customized'] ) ) {

			$customized = json_decode( stripslashes( $_REQUEST['customized'] ) );

			if( isset( $customized->wr_preview ) && $customized->wr_preview != 'desktop' ) {
				wr_set_server_param(
					'HTTP_USER_AGENT',
					wr_get_server_param('HTTP_USER_AGENT') . ' Mobile'
				);
			}
		}
	}

	/**
	 * Display notices to admin user.
	 *
	 * @param   boolean  $force  Force display notices.
	 *
	 * @return  void
	 */
	public static function admin_notices( $force = false) {
		global $pagenow;

		if ( 'index.php' == $pagenow || ( 'admin.php' == $pagenow && isset( $_REQUEST['page'] ) && 'wr-intro' == $_REQUEST['page'] ) ) {
			if ( $force || ( false === get_option( 'nitro_usage_data_collector' ) ) ) {
				include_once( get_template_directory() . '/woorockets/templates/admin/usage-data-collector.php' );
			}
		}
	}

	/**
	 * Method to enable / disable Nitro usage data collector.
	 *
	 * @return  void
	 */
	public static function enable_data_collector() {
		// Get request variables.
		$enable = isset( $_GET['enable'] ) ? ( int ) $_GET['enable'] : null;

		if ( ! is_null( $enable ) ) {
			update_option( 'nitro_usage_data_collector', $enable );

			// Send usage data for the first time.
			if ( $enable ) {
				self::collect_usage_data();
			}
		}
	}

	/**
	 * Collect usage data and send to WR server.
	 *
	 * @return  void
	 */
	protected static function collect_usage_data() {
		if ( 1 === ( int ) get_option( 'nitro_usage_data_collector' ) ) {
			// Get WordPress environment.
			$wordpress_env = array(
				'home_url'     => home_url(),
				'wp_version'   => $GLOBALS['wp_version'],
				'wc_version'   => function_exists( 'WC' ) ? WC()->version : 'unavailable',
				'multisite'    => is_multisite() ? 'on' : 'off',
				'memory_limit' => size_format( max( WR_Nitro::str_to_num( WP_MEMORY_LIMIT ), function_exists( 'memory_get_usage' ) ? memory_get_usage() : 0 ) ),
				'debug_mode'   => ( defined('WP_DEBUG') && WP_DEBUG ) ? 'on' : 'off',
				'language'     => get_locale(),
			);

			// Get server environment.
			$server_env = array(
				'software'          => wr_get_server_param( 'SERVER_SOFTWARE' ),
				'mysql_version'     => $GLOBALS['wpdb']->db_version(),
				'php_version'       => phpversion(),
				'suhosin_extension' => extension_loaded( 'suhosin' ) ? 'on' : 'off',
				'max_upload_size'   => size_format( wp_max_upload_size() ),
				'default_time_zone' => date_default_timezone_get(),
			);

			if ( function_exists( 'ini_get' ) ) {
				$server_env['post_max_size'     ] = size_format( WR_Nitro::str_to_num( ini_get( 'post_max_size' ) ) );
				$server_env['max_execution_time'] = ( ini_get( 'max_execution_time' ) <= 0 ) ? 'unlimited' : ini_get( 'max_execution_time' ) . ' seconds';
				$server_env['max_input_vars'    ] = ( ini_get( 'max_input_vars' ) <= 0 ) ? 'unlimited' : ini_get( 'max_input_vars' );
				$server_env['safe_mode'         ] = ini_get('safe_mode') ? 'on' : 'off';
			}

			// Build post data.
			$niche = get_option( 'wr_nitro_sample_package' );

			// Send data to WooRockets server.
			wp_remote_post( 'http://demo.woorockets.com/ittools/collect', array(
				'body' => array(
					'siteurl'   => site_url(),
					'nichedemo' => ( empty( $niche ) ? 'main-demo' : $niche ),
					'wpenv'     => json_encode( $wordpress_env ),
					'serverenv' => json_encode( $server_env )
				)
			) );
		}
	}
}
