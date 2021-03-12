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
 * Plug additional meta boxes into WordPress.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Meta_Box extends RW_Meta_Box {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

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

		// Disable page options in WooCommerce shop page.
		global $pagenow;

		if ( 'post.php' == $pagenow && isset( $_REQUEST['post'] ) && $_REQUEST['post'] == get_option( 'woocommerce_shop_page_id' ) ) {
			return;
		}

		// Remove original RW Meta Box init action.
		remove_action( 'admin_init', 'rwmb_register_meta_boxes' );

		foreach ( $GLOBALS['wp_filter']['admin_init'] as $p => $handles ) {
			foreach ( $handles as $k => $handle ) {
				if ( is_array( $handle['function'] ) ) {
					if ( is_object( $handle['function'][0] ) && 'RWMB_Core' == get_class( $handle['function'][0] ) ) {
						if ( 'register_meta_boxes' == $handle['function'][1] ) {
							unset( $GLOBALS['wp_filter']['admin_init'][ $p ][ $k ] );
						}
					}
				}
			}
		}

		// Add action to init RW Meta Box.
		add_action( 'admin_init', array( __CLASS__, 'register_meta_boxes' ) );

		// Register necessary actions / filters to hook WR Nitro meta boxes into WordPress.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );

		add_filter( 'rwmb_meta_boxes'        , array( __CLASS__, 'meta_boxes'         ) );
		add_filter( 'rwmb_outside_conditions', array( __CLASS__, 'outside_conditions' ) );

		// Register filter to verify page option values before saving.
		add_filter( 'update_post_metadata', array( __CLASS__, 'verify' ), 10, 5 );

		// Register filter to synchronize default option values with current theme customizer values.
		add_filter( 'rwmb_field_meta', array( __CLASS__, 'get_meta' ), 10, 3 );

		// Register filter to allow saving empty post.
		add_filter( 'wp_insert_post_empty_content', array( __CLASS__, 'allow_saving_empty_post' ), 10, 2 );

		// State that initialization completed.
		self::$initialized = true;
	}

	/**
	 * Initialize RW Meta Box.
	 *
	 * @return  void
	 */
	public static function register_meta_boxes() {
		// Get meta boxes to register.
		$meta_boxes = apply_filters( 'rwmb_meta_boxes', array() );

		if ( is_array( $meta_boxes ) ) {
			// Load all custom fields.
			static $loaded;

			if ( ! isset( $loaded ) ) {
				foreach ( glob( implode( '/', array_slice( explode( '/', str_replace( '\\', '/', __FILE__ ) ), 0, -1 ) ) . '/fields/*.php' ) as $file ) {
					include_once $file;
				}

				$loaded = true;
			}

			// Instantiate all meta boxes.
			foreach ( $meta_boxes as $meta_box ) {
				$meta_box = new self( $meta_box );
			}
		}
	}

	/**
	 * Enqueue required admin assets.
	 *
	 * @return  void
	 */
	public static function enqueue_admin_assets() {
		global $post;

		// Enqueue scripts and styles for registered pages (post types) only
		$types = array( 'post', 'page', 'nitro-gallery', 'product' );

		if ( isset( $post->post_type ) && in_array( $post->post_type, $types ) ) {
			// Load custom style.
			wp_enqueue_style( 'wr-metabox', get_template_directory_uri() . '/assets/woorockets/css/admin/meta-box.css' );
			wp_enqueue_style( 'wr-google-fonts', get_template_directory_uri() . '/assets/woorockets/css/admin/google-fonts.css' );

			// Enqueue jQuery backward compatible script.
			wp_enqueue_script( 'wr-nitro-jquery-backcompat', get_template_directory_uri() . '/assets/woorockets/js/admin/jquery-backcompat.js', array( 'jquery' ) );
		}

		if ( isset( $post->post_type ) && in_array( $post->post_type, array( 'product' ) ) ) {
			wp_enqueue_script( 'wr-metabox', get_template_directory_uri() . '/assets/woorockets/js/admin/meta-box/metabox.js' , array(), false, true );
		}
	}

	/**
	 * Register additional meta boxes.
	 *
	 * @param   array  $meta_boxes  Current meta boxes.
	 *
	 * @return  array
	 */
	public static function meta_boxes( $meta_boxes ) {

		$list_header = new WP_Query( array(
			'posts_per_page'   => -1,
			'post_type'        => 'header_builder',
			'post_status'      => 'publish',
			'suppress_filters' => true,
		));
		$header_layout = array(
			'0' => esc_html__( '-- Select Header --', 'wr-nitro' )
		);

		// Set to normal headers default
		if ( $list_header->post_count ) {
			foreach( $list_header->posts as $val ) {
				$header_layout[ $val->ID ] = $val->post_title;
			}
		};

		$wr_nitro_options = WR_Nitro::get_options();

		// Additional meta box for post.
		$meta_boxes[] = array(
			'id'         => 'wr_post_option',
			'post_types' => array( 'post' ),
			'title'      => esc_html__( 'Post Settings', 'wr-nitro' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'autosave'   => true,
			'fields'     => array(
				array(
					'name' => esc_html__( 'Enable large post', 'wr-nitro' ),
					'id'   => 'masonry_large',
					'type' => 'checkbox',
					'desc' => esc_html__( 'Support Masonry layout only', 'wr-nitro' ),
					'std'  => 0,
				),
				array(
					'name'    => esc_html__( 'Add image gallery', 'wr-nitro' ),
					'id'      => 'format_gallery',
					'type'    => 'image_advanced',
					'visible' => array( 'post_format', 'gallery' )
				),
				array(
					'name'     => esc_html__( 'Video Source', 'wr-nitro' ),
					'id'       => 'format_video',
					'type'     => 'select',
					'options'  => array(
						'link' => esc_html__( 'Video Link', 'wr-nitro' ),
						'file' => esc_html__( 'Video Upload File', 'wr-nitro' ),
					),
					'visible' => array( 'post_format', 'video' ),
				),
				array(
					'name'    => esc_html__( 'Video Link', 'wr-nitro' ),
					'id'      => 'format_video_url',
					'desc'    => esc_html__( '(Support Youtube and Vimeo video)', 'wr-nitro' ),
					'type'    => 'oembed',
					'visible' => array( 'format_video', '=', 'link' ),
				),
				array(
					'name'             => esc_html__( 'Upload video', 'wr-nitro' ),
					'id'               => 'format_video_file',
					'desc'             => esc_html__( 'Support .mp4 file format only', 'wr-nitro' ),
					'type'             => 'file_advanced',
					'max_file_uploads' => 1,
					'mime_type'        => 'video',
					'visible'          => array( 'format_video', '=', 'file' ),
				),
				array(
					'name'     => esc_html__( 'Audio Source', 'wr-nitro' ),
					'id'       => 'format_audio',
					'type'     => 'select',
					'options'  => array(
						'link' => esc_html__( 'Soundcloud Link', 'wr-nitro' ),
						'file' => esc_html__( 'Upload audio', 'wr-nitro' ),
					),
					'visible' => array( 'post_format', 'audio' ),
				),
				array(
					'name'    => esc_html__( 'Soundcloud Link', 'wr-nitro' ),
					'id'      => 'format_audio_url',
					'type'    => 'oembed',
					'visible' => array( 'format_audio', '=', 'link' ),
				),
				array(
					'name'             => esc_html__( 'Upload Audio', 'wr-nitro' ),
					'id'               => 'format_audio_file',
					'desc'             => esc_html__( 'Support .mp3 file format only', 'wr-nitro' ),
					'type'             => 'file_advanced',
					'max_file_uploads' => 1,
					'mime_type'        => 'audio',
					'visible'          => array( 'format_audio', '=', 'file' ),
				),
				array(
					'name'    => esc_html__( 'Quote content', 'wr-nitro' ),
					'id'      => 'format_quote_content',
					'type'    => 'textarea',
					'cols'    => '30',
					'rows'    => '6',
					'visible' => array( 'post_format', 'quote' ),
				),
				array(
					'name'    => esc_html__( 'Quote author', 'wr-nitro' ),
					'id'      => 'format_quote_author',
					'type'    => 'text',
					'clone'   => false,
					'visible' => array( 'post_format', 'quote' ),
				),
				array(
					'name'    => esc_html__( 'Link to', 'wr-nitro' ),
					'id'      => 'format_link_url',
					'type'    => 'text',
					'visible' => array( 'post_format', 'link' ),
				),
			)
		);

		$meta_boxes[] = array(
			'id'         => 'gallery_option',
			'post_types' => array( 'nitro-gallery' ),
			'title'      => esc_html__( 'Image Gallery', 'wr-nitro' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'autosave'   => true,
			'fields'     => array(
				array(
					'name' => esc_html__( 'Gallery Type', 'wr-nitro' ),
					'id'   => 'gallery_type',
					'type' => 'radio',
					'options' => array(
						'image'    => esc_html__( 'Image', 'wr-nitro' ),
						'external' => esc_html__( 'External', 'wr-nitro' ),
					),
					'std'  => 'image'
				),
				array(
					'name' => esc_html__( 'External URL', 'wr-nitro' ),
					'id'   => 'external_url',
					'type' => 'text',
					'visible' => array( 'gallery_type', 'external' ),
				),
				array(
					'name' => esc_html__( 'Button Text', 'wr-nitro' ),
					'id'   => 'external_button',
					'type' => 'text',
					'std'  => esc_html__( 'View Detail', 'wr-nitro' ),
					'visible' => array( 'gallery_type', 'external' ),
				),
				array(
					'name' => esc_html__( 'Images', 'wr-nitro' ),
					'id'   => 'multiple_image',
					'type' => 'image_advanced',
					'visible' => array( 'gallery_type', 'image' ),
				),
			)
		);

		// Additional meta box for page.
		$meta_boxes[] = array(
			'id'         => 'wr_page_option',
			'post_types' => array( 'page' ),
			'title'      => esc_html__( 'Page Options', 'wr-nitro' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'autosave'   => true,
			'fields'     => array(
				array(
					'name'   => esc_html__( 'Use Global Settings', 'wr-nitro' ),
					'id'     => 'global_opt',
					'type'   => 'toggle',
					'std'    => 1,
					'tab'    => esc_html__( 'General', 'wr-nitro' ),
					'tab_id' => 'general',
				),
				array(
					'name'   => esc_html__( 'Stretch Row And Content', 'wr-nitro' ),
					'id'     => 'wr_layout_stretch',
					'type'   => 'toggle',
					'std'    => 0,
					'tab'    => esc_html__( 'General', 'wr-nitro' ),
					'tab_id' => 'general',
				),
				array(
					'name'    => esc_html__( 'Select Header', 'wr-nitro' ),
					'id'      => 'header_layout',
					'type'    => 'select',
					'options' => $header_layout,
					'hidden'  => array( 'global_opt', '=', 1 ),
					'tab'     => esc_html__( 'General', 'wr-nitro' ),
					'tab_id'  => 'general',
				),
				array(
					'type'   => 'divider',
					'id'     => 'divider_01',
					'hidden' => array( 'global_opt', '=', 1 ),
					'tab'    => esc_html__( 'General', 'wr-nitro' ),
					'tab_id' => 'general',
				),
				array(
					'name' => esc_html__( 'Content Width', 'wr-nitro' ),
					'id'   => 'wr_layout_content_width_unit',
					'type' => 'radio',
					'options'  => array(
						'pixel'      => esc_html__( 'px', 'wr-nitro' ),
						'percentage' => esc_html__( '%', 'wr-nitro' ),
					),
					'std'    => 'pixel',
					'hidden' => array( 'global_opt', '=', 1 ),
					'tab'    => esc_html__( 'General', 'wr-nitro' ),
					'tab_id' => 'general',
				),
				array(
					'name'    => '&nbsp;',
					'id'      => 'wr_layout_content_width',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 760,
						'max'  => 1920,
						'step' => 10,
						'unit' => 'px',
					),
					'std'    => 1170,
					'hidden' => array( 'wr_layout_content_width_unit', '!=', 'pixel' ),
					'tab'    => esc_html__( 'General', 'wr-nitro' ),
					'tab_id' => 'general',
				),
				array(
					'name'    => '&nbsp;',
					'id'      => 'wr_layout_content_width_percentage',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 20,
						'max'  => 100,
						'step' => 1,
						'unit' => '%',
					),
					'std'    => 100,
					'hidden' => array( 'wr_layout_content_width_unit', '!=', 'percentage' ),
					'tab'    => esc_html__( 'General', 'wr-nitro' ),
					'tab_id' => 'general',
				),
				array(
					'name'    => esc_html__( 'Gutter Width', 'wr-nitro' ),
					'id'      => 'wr_layout_gutter_width',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 20,
						'max'  => 60,
						'step' => 10,
						'unit' => 'px',
					),
					'std'    => 30,
					'hidden' => array( 'global_opt', '=', 1 ),
					'tab'    => esc_html__( 'General', 'wr-nitro' ),
					'tab_id' => 'general',
				),
				array(
					'name'    => esc_html__( 'Offset Width', 'wr-nitro' ),
					'id'      => 'wr_layout_offset',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
						'unit' => 'px',
					),
					'std'    => 0,
					'hidden' => array( 'global_opt', '=', 1 ),
					'tab'    => esc_html__( 'General', 'wr-nitro' ),
					'tab_id' => 'general',
				),
				array(
					'name'   => esc_html__( 'Offset Background Color', 'wr-nitro' ),
					'id'     => 'wr_layout_offset_color',
					'type'   => 'colors',
					'std'    => '#ffffff',
					'hidden' => array( 'global_opt', '=', 1 ),
					'tab'    => esc_html__( 'General', 'wr-nitro' ),
					'tab_id' => 'general',
				),
				array(
					'name'   => esc_html__( 'Enable Boxed Layout', 'wr-nitro' ),
					'id'     => 'wr_layout_boxed',
					'type'   => 'toggle',
					'hidden' => array( 'global_opt', '=', 1 ),
					'std'    => 0,
					'tab'    => esc_html__( 'General', 'wr-nitro' ),
					'tab_id' => 'general',
				),
				array(
					'name'   => esc_html__( 'Show Page Title', 'wr-nitro' ),
					'id'     => 'wr_page_title',
					'type'   => 'toggle',
					'std'    => 1,
					'hidden' => array( 'global_opt', '=', 1 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'   => esc_html__( 'Enable Full Width', 'wr-nitro' ),
					'id'     => 'wr_page_title_fullscreen',
					'type'   => 'toggle',
					'std'    => 0,
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name' => esc_html__( 'Layout', 'wr-nitro' ),
					'id'   => 'wr_page_title_layout',
					'type' => 'select',
					'options'  => array(
						'layout-1' => esc_html__( 'Layout 1', 'wr-nitro' ),
						'layout-2' => esc_html__( 'Layout 2', 'wr-nitro' ),
						'layout-3' => esc_html__( 'Layout 3', 'wr-nitro' ),
						'layout-4' => esc_html__( 'Layout 4', 'wr-nitro' ),
						'layout-5' => esc_html__( 'Layout 5', 'wr-nitro' ),
					),
					'std'         => 'layout-1',
					'hidden'      => array( 'wr_page_title', '=', 0 ),
					'placeholder' => esc_html__( '-- Select Layout --', 'wr-nitro' ),
					'tab'         => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id'      => 'page-title',
				),
				array(
					'name'   => esc_html__( 'Show Breadcrumb', 'wr-nitro' ),
					'id'     => 'wr_page_title_breadcrumbs',
					'type'   => 'toggle',
					'std'    => 0,
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'   => esc_html__( 'Description', 'wr-nitro' ),
					'id'     => 'wr_page_title_desc',
					'type'   => 'textarea',
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'    => esc_html__( 'Padding Top', 'wr-nitro' ),
					'id'      => 'wr_page_title_padding_top',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 10,
						'max'  => 500,
						'step' => 1,
						'unit' => 'px',
					),
					'std'    => 80,
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'    => esc_html__( 'Padding Bottom', 'wr-nitro' ),
					'id'      => 'wr_page_title_padding_bottom',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 10,
						'max'  => 500,
						'step' => 1,
						'unit' => 'px',
					),
					'std'    => 80,
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'    => esc_html__( 'Min Height', 'wr-nitro' ),
					'id'      => 'wr_page_title_heading_min_height',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
						'unit' => 'px',
					),
					'std'    => 0,
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'type'   => 'divider',
					'id'     => 'divider_04',
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name' => esc_html__( 'Heading Style', 'wr-nitro' ),
					'id'   => 'wr_page_title_heading_font',
					'type' => 'typography',
					'std'  => array(
						'italic'    => 0,
						'underline' => 0,
						'uppercase' => 0,
					),
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'    => esc_html__( 'Heading Text Size', 'wr-nitro' ),
					'id'      => 'wr_page_title_heading_font_size',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
						'unit' => 'px',
					),
					'std'    => 44,
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'    => esc_html__( 'Heading Line Height', 'wr-nitro' ),
					'id'      => 'wr_page_title_heading_line_height',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
						'unit' => 'px',
					),
					'std'    => 44,
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'    => esc_html__( 'Heading Letter Spacing', 'wr-nitro' ),
					'id'      => 'wr_page_title_heading_letter_spacing',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 1,
						'unit' => 'px',
					),
					'std'    => 0,
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'type'   => 'divider',
					'id'     => 'divider_05',
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name' => esc_html__( 'Description Style', 'wr-nitro' ),
					'id'   => 'page_title_desc_font',
					'type' => 'typography',
					'std'  => array(
						'italic'    => 0,
						'underline' => 0,
						'uppercase' => 0,
					),
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'    => esc_html__( 'Description Text Size', 'wr-nitro' ),
					'id'      => 'page_title_desc_font_size',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
						'unit' => 'px',
					),
					'std'    => 14,
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'    => esc_html__( 'Description Line Height', 'wr-nitro' ),
					'id'      => 'page_title_desc_line_height',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
						'unit' => 'px',
					),
					'std'    => 24,
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'    => esc_html__( 'Description Letter Spacing', 'wr-nitro' ),
					'id'      => 'page_title_desc_letter_spacing',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 1,
						'unit' => 'px',
					),
					'std'    => 0,
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'type'   => 'divider',
					'id'     => 'divider_03',
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'    => esc_html__( 'Normal Color', 'wr-nitro' ),
					'id'      => 'wr_page_title_color',
					'type'    => 'colors',
					'options' => array(
						'head' => esc_html__( 'Heading', 'wr-nitro' ),
						'body' => esc_html__( 'Body', 'wr-nitro' ),
					),
					'std' => array(
						'head' => '#323232',
						'body' => '#646464',
					),
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'    => esc_html__( 'Link Color', 'wr-nitro' ),
					'id'      => 'wr_page_title_link_colors',
					'type'    => 'colors',
					'options' => array(
						'normal' => esc_html__( 'Link', 'wr-nitro' ),
						'hover'  => esc_html__( 'Link Hover', 'wr-nitro' ),
					),
					'std' => array(
						'normal' => '#ff4064',
						'hover'  => '#323232',
					),
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'   => esc_html__( 'Background Color', 'wr-nitro' ),
					'id'     => 'wr_page_title_bg_color',
					'type'   => 'colors',
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'             => esc_html__( 'Background Image', 'wr-nitro' ),
					'id'               => 'wr_page_title_bg_image',
					'type'             => 'image_advanced',
					'max_file_uploads' => 1,
					'hidden'           => array( 'wr_page_title', '=', 0 ),
					'tab'              => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id'           => 'page-title',
				),
				array(
					'name' => esc_html__( 'Background Position', 'wr-nitro' ),
					'id'   => 'wr_page_title_position',
					'type' => 'select',
					'options'  => array(
						'left top'      => esc_html__( 'Left Top', 'wr-nitro' ),
						'left center'   => esc_html__( 'Left Center', 'wr-nitro' ),
						'left bottom'   => esc_html__( 'Left Bottom', 'wr-nitro' ),
						'right top'     => esc_html__( 'Right Top', 'wr-nitro' ),
						'right center'  => esc_html__( 'Right Center', 'wr-nitro' ),
						'right bottom'  => esc_html__( 'Right Bottom', 'wr-nitro' ),
						'center top'    => esc_html__( 'Center Top', 'wr-nitro' ),
						'center center' => esc_html__( 'Center Center', 'wr-nitro' ),
						'center bottom' => esc_html__( 'Center Bottom', 'wr-nitro' ),
					),
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name' => esc_html__( 'Background Repeat', 'wr-nitro' ),
					'id'   => 'wr_page_title_repeat',
					'type' => 'select',
					'options'  => array(
						'no-repeat' => esc_html__( 'No Repeat', 'wr-nitro' ),
						'repeat'    => esc_html__( 'Repeat', 'wr-nitro' ),
						'repeat-x'  => esc_html__( 'Repeat X', 'wr-nitro' ),
						'repeat-y'  => esc_html__( 'Repeat Y', 'wr-nitro' ),
					),
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name' => esc_html__( 'Background Size', 'wr-nitro' ),
					'id'   => 'wr_page_title_size',
					'type' => 'select',
					'options'  => array(
						'auto' => esc_html__( 'auto', 'wr-nitro' ),
						'cover'   => esc_html__( 'Cover', 'wr-nitro' ),
						'contain' => esc_html__( 'Contain', 'wr-nitro' ),
						'initial' => esc_html__( 'Initial', 'wr-nitro' ),
					),
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name' => esc_html__( 'Background Attachment', 'wr-nitro' ),
					'id'   => 'wr_page_title_attachment',
					'type' => 'select',
					'options'  => array(
						'scroll' => esc_html__( 'Scroll', 'wr-nitro' ),
						'fixed'  => esc_html__( 'Fixed', 'wr-nitro' ),
					),
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'   => esc_html__( 'Parallax Background', 'wr-nitro' ),
					'id'     => 'wr_page_title_parallax',
					'type'   => 'toggle',
					'std'    => 1,
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name'   => esc_html__( 'Mask Overlay Color', 'wr-nitro' ),
					'id'     => 'wr_page_title_mask_color',
					'type'   => 'colors',
					'std'    => 'rgba(0, 0, 0, 0)',
					'hidden' => array( 'wr_page_title', '=', 0 ),
					'tab'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'tab_id' => 'page-title',
				),
				array(
					'name' => esc_html__( 'Sidebar Layout', 'wr-nitro' ),
					'id'   => 'wr_page_layout',
					'type' => 'image_select',
					'options'  => array(
						'left-sidebar'  => get_template_directory_uri() . '/assets/woorockets/images/admin/lc.png',
						'no-sidebar'    => get_template_directory_uri() . '/assets/woorockets/images/admin/c.png',
						'right-sidebar' => get_template_directory_uri() . '/assets/woorockets/images/admin/cr.png',
					),
					'std'    => 'no-sidebar',
					'hidden' => array( 'global_opt', '=', 1 ),
					'tab'    => esc_html__( 'Page Content', 'wr-nitro' ),
					'tab_id' => 'page-content',
				),
				array(
					'name'        => esc_html__( 'Sidebar Content', 'wr-nitro' ),
					'id'          => 'wr_page_layout_sidebar',
					'type'        => 'select',
					'options'     => WR_Nitro_Helper::get_sidebars(),
					'placeholder' => esc_html__( '-- Select Sidebar --', 'wr-nitro' ),
					'std'         => 'primary-sidebar',
					'hidden'      => array( 'wr_page_layout', '=', 'no-sidebar' ),
					'tab'         => esc_html__( 'Page Content', 'wr-nitro' ),
					'tab_id'      => 'page-content',
				),
				array(
					'name'    => esc_html__( 'Sidebar Width', 'wr-nitro' ),
					'id'      => 'wr_page_layout_sidebar_width',
					'type'    => 'wrslider',
					'choices' => array(
						'min'  => 250,
						'max'  => 575,
						'step' => 5,
						'unit' => esc_html__( 'px', 'wr-nitro' ),
					),
					'std'    => 300,
					'hidden' => array( 'wr_page_layout', '=', 'no-sidebar' ),
					'tab'    => esc_html__( 'Page Content', 'wr-nitro' ),
					'tab_id' => 'page-content',
				),
				array(
					'name'   => esc_html__( 'Outer Background Color', 'wr-nitro' ),
					'id'     => 'wr_page_body_bg_color',
					'type'   => 'colors',
					'hidden' => array( 'wr_layout_boxed', '=', 0 ),
					'tab'    => esc_html__( 'Page Content', 'wr-nitro' ),
					'tab_id' => 'page-content',
				),
				array(
					'name'   => esc_html__( 'Inner Background Color', 'wr-nitro' ),
					'id'     => 'wr_general_container_color',
					'type'   => 'colors',
					'hidden' => array( 'global_opt', '=', 1 ),
					'tab'    => esc_html__( 'Page Content', 'wr-nitro' ),
					'tab_id' => 'page-content',
				),
				array(
					'name'             => esc_html__( 'Outer Background Image', 'wr-nitro' ),
					'id'               => 'wr_page_layout_bg_image',
					'type'             => 'image_advanced',
					'max_file_uploads' => 1,
					'hidden'           => array( 'wr_layout_boxed', '=', 0 ),
					'tab'              => esc_html__( 'Page Content', 'wr-nitro' ),
					'tab_id'           => 'page-content',
				),
				array(
					'name' => esc_html__( 'Background Position', 'wr-nitro' ),
					'id'   => 'wr_page_layout_position',
					'type' => 'select',
					'options' => array(
						'left top'      => esc_html__( 'Left Top', 'wr-nitro' ),
						'left center'   => esc_html__( 'Left Center', 'wr-nitro' ),
						'left bottom'   => esc_html__( 'Left Bottom', 'wr-nitro' ),
						'right top'     => esc_html__( 'Right Top', 'wr-nitro' ),
						'right center'  => esc_html__( 'Right Center', 'wr-nitro' ),
						'right bottom'  => esc_html__( 'Right Bottom', 'wr-nitro' ),
						'center top'    => esc_html__( 'Center Top', 'wr-nitro' ),
						'center center' => esc_html__( 'Center Center', 'wr-nitro' ),
						'center bottom' => esc_html__( 'Center Bottom', 'wr-nitro' ),
					),
					'visible' => array( 'wr_page_layout_bg_image', '>', 0 ),
					'tab'     => esc_html__( 'Page Content', 'wr-nitro' ),
					'tab_id'  => 'page-content',
				),
				array(
					'name' => esc_html__( 'Background Repeat', 'wr-nitro' ),
					'id'   => 'wr_page_layout_repeat',
					'type' => 'select',
					'options'  => array(
						'no-repeat' => esc_html__( 'No Repeat', 'wr-nitro' ),
						'repeat'    => esc_html__( 'Repeat', 'wr-nitro' ),
						'repeat-x'  => esc_html__( 'Repeat X', 'wr-nitro' ),
						'repeat-y'  => esc_html__( 'Repeat Y', 'wr-nitro' ),
					),
					'visible' => array( 'wr_page_layout_bg_image', '>', 0 ),
					'tab'     => esc_html__( 'Page Content', 'wr-nitro' ),
					'tab_id'  => 'page-content',
				),
				array(
					'name' => esc_html__( 'Background Size', 'wr-nitro' ),
					'id'   => 'wr_page_layout_size',
					'type' => 'select',
					'options' => array(
						'auto'    => esc_html__( 'auto', 'wr-nitro' ),
						'cover'   => esc_html__( 'Cover', 'wr-nitro' ),
						'contain' => esc_html__( 'Contain', 'wr-nitro' ),
						'initial' => esc_html__( 'Initial', 'wr-nitro' ),
					),
					'visible' => array( 'wr_page_layout_bg_image', '>', 0 ),
					'tab'     => esc_html__( 'Page Content', 'wr-nitro' ),
					'tab_id'  => 'page-content',
				),
				array(
					'name' => esc_html__( 'Background Attachment', 'wr-nitro' ),
					'id'   => 'wr_page_layout_attachment',
					'type' => 'select',
					'options'  => array(
						'scroll' => esc_html__( 'Scroll', 'wr-nitro' ),
						'fixed'  => esc_html__( 'Fixed', 'wr-nitro' ),
					),
					'visible' => array( 'wr_page_layout_bg_image', '>', 0 ),
					'tab'     => esc_html__( 'Page Content', 'wr-nitro' ),
					'tab_id'  => 'page-content',
				),
				array(
					'name'    => esc_html__( 'Parallax Background', 'wr-nitro' ),
					'id'      => 'wr_layout_boxed_parallax',
					'type'    => 'toggle',
					'std'     => 0,
					'visible' => array( 'wr_page_layout_bg_image', '>', 0 ),
					'tab'     => esc_html__( 'Page Content', 'wr-nitro' ),
					'tab_id'  => 'page-content',
				),
				array(
					'name'    => esc_html__( 'Mask Overlay Color', 'wr-nitro' ),
					'id'      => 'wr_layout_boxed_bg_mask_color',
					'type'    => 'colors',
					'std'     => '#000',
					'visible' => array( 'wr_page_layout_bg_image', '>', 0 ),
					'tab'     => esc_html__( 'Page Content', 'wr-nitro' ),
					'tab_id'  => 'page-content',
				),
			)
		);

		// Additional meta box for product.
		$meta_boxes[] = array(
			'id'         => 'product_option',
			'post_types' => 'product',
			'title'      => esc_html__( 'Image size for masonry layout', 'wr-nitro' ),
			'context'    => 'side',
			'priority'   => 'low',
			'fields'     => array(
				array(
					'id'   => 'wc_masonry_product_size',
					'type' => 'select',
					'options'  => array(
						'wc-small-square'    => esc_html__( 'Small Square', 'wr-nitro' ),
						'wc-large-square'    => esc_html__( 'Large Square', 'wr-nitro' ),
						'wc-small-rectangle' => esc_html__( 'Small Rectangle', 'wr-nitro' ),
						'wc-large-rectangle' => esc_html__( 'Large Rectangle', 'wr-nitro' ),
					),
					'std' => 'wc-small-square',
					'placeholder' => esc_html__( '-- Select Size For Image --', 'wr-nitro' ),
				)
			)
		);
		$meta_boxes[] = array(
			'id'         => 'product_builder',
			'post_types' => 'product',
			'title'      => esc_html__( 'Product Settings', 'wr-nitro' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name' => esc_html__( 'Enable Product Builder', 'wr-nitro' ),
					'id'   => 'enable_builder',
					'type' => 'toggle',
					'std'  => 0,
				),
				array(
					'name' => esc_html__( 'Choose Style', 'wr-nitro' ),
					'desc' => esc_html__( 'If you choose style here, the settings on theme customize for this product will be overrided.', 'wr-nitro' ),
					'id'   => 'single_style',
					'type' => 'radio',
					'options'  => array(
						'0' => esc_html__( 'Global Style', 'wr-nitro' ),
						'1' => esc_html__( 'Style 1', 'wr-nitro' ),
						'2' => esc_html__( 'Style 2', 'wr-nitro' ),
						'3' => esc_html__( 'Style 3', 'wr-nitro' ),
						'4' => esc_html__( 'Style 4', 'wr-nitro' ),
						'5' => esc_html__( 'Style 5', 'wr-nitro' ),
					),
					'std'  => 0,
				),
				array(
					'name' => esc_html__( 'Image Thumbnail Background', 'wr-nitro' ),
					'desc' => esc_html__( 'This setting affect for style 1 & 4 only', 'wr-nitro' ),
					'id'   => 'image_bg_color',
					'type' => 'colors',
				),
			)
		);
		$meta_boxes[] = array(
			'id'         => 'product_video',
			'post_types' => 'product',
			'title'      => esc_html__( 'Product Video', 'wr-nitro' ),
			'context'    => 'side',
			'priority'   => 'low',
			'fields'     => array(
				array(
					'id'   => 'wc_product_video',
					'type' => 'select',
					'options'  => array(
						'file' => esc_html__( 'Upload Your Video', 'wr-nitro' ),
						'url'  => esc_html__( 'Other Source', 'wr-nitro' )
					),
				),
				array(
					'name'             => esc_html__( 'Upload video', 'wr-nitro' ),
					'id'               => 'wc_product_video_file',
					'desc'             => esc_html__( 'Support .mp4 file format only', 'wr-nitro' ),
					'type'             => 'file_advanced',
					'max_file_uploads' => 1,
					'mime_type'        => 'video',
					'visible'          => array( 'wc_product_video', '=', 'file' ),
				),
				array(
					'name'    => esc_html__( 'Video Link', 'wr-nitro' ),
					'id'      => 'wc_product_video_url',
					'type'    => 'oembed',
					'visible' => array( 'wc_product_video', '=', 'url' ),
				),
			)
		);

		// Get current theme customize options.
		$theme_options = WR_Nitro_Customize::get_options();

		// Apply current theme customize options as default values for page options.
		foreach ( $meta_boxes as $i => $meta_box ) {
			if ( isset( $meta_box['fields'] ) ) {
				foreach ( $meta_box['fields'] as $k => $field ) {
					if ( isset( $field['id'] ) && isset( $theme_options[ $field['id'] ] ) ) {
						if ( is_bool( $theme_options[ $field['id'] ] ) ) {
							$meta_boxes[ $i ]['fields'][ $k ]['std'] = $theme_options[ $field['id'] ] ? 1 : 0;
						} else {
							$meta_boxes[ $i ]['fields'][ $k ]['std'] = $theme_options[ $field['id'] ];
						}
					}
				}
			}
		}

		return $meta_boxes;
	}

	/**
	 * Hide Tabs with Conditional Logic.
	 *
	 * @param   array  $conditions  Current conditions.
	 *
	 * @return  array
	 */
	public static function outside_conditions() {
		global $post;

		$types = array( 'page' );

		if ( isset( $post->post_type ) && in_array( $post->post_type, $types ) ) {
			$conditions['.wr-tab-page-title, .wr-tab-page-content'] = array(
				'hidden' => array( 'global_opt', 1 )
			);
			return $conditions;
		}
		return array( '' => 'empty' );
	}

	/**
	 * Method to verify page option values before saving.
	 *
	 * @param   null|bool  $check       Whether to allow updating metadata for the given type.
	 * @param   int        $object_id   Object ID.
	 * @param   string     $meta_key    Meta key.
	 * @param   mixed      $meta_value  Meta value. Must be serializable if non-scalar.
	 * @param   mixed      $prev_value  Optional. If specified, only update existing metadata entries with the specified value.
	 *                                  Otherwise, update all entries.
	 *
	 * @return  mixed
	 */
	public static function verify( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
		// Get current theme customize options.
		$theme_options = WR_Nitro_Customize::get_options();

		// Get saved meta data.
		$saved = get_post_meta( $object_id, $meta_key, true );

		if ( isset( $theme_options[ $meta_key ] ) && false === $saved ) {
			if ( is_bool( $theme_options[ $meta_key ] ) ) {
				if ( $meta_value == ( $theme_options[ $meta_key ] ? 1 : 0 ) ) {
					$check = false;
				}
			} elseif ( is_array( $theme_options[ $meta_key ] ) ) {
				if ( ! count( array_diff( $theme_options[ $meta_key ], ( array ) $meta_value ) ) ) {
					$check = false;
				}
			} elseif ( $meta_value == $theme_options[ $meta_key ] ) {
				$check = false;
			}
		}

		return $check;
	}

	/**
	 * Get current meta value.
	 *
	 * @param   mixed    $meta   Current meta value.
	 * @param   array    $field  Field declaration.
	 * @param   boolean  $saved  Whether the meta was saved before.
	 *
	 * @return  mixed
	 */
	public static function get_meta( $meta, $field, $saved ) {
		global $post;

		// Get ID of editing post.
		$id = isset($_REQUEST['post']) ? $_REQUEST['post'] : $post->ID;

		// Get value from post meta if it was saved before.
		if (($saved = get_post_meta($id, $field['id'], ($field['clone'] || ! $field['multiple']) ? true : false)) !== false) {
			$meta = $saved;
		}

		// Otherwise, get current theme customize options.
		else {
			$theme_options = WR_Nitro_Customize::get_options();

			if ( isset( $theme_options[ $field['id'] ] ) ) {
				$meta = $theme_options[ $field['id'] ];
			}
		}

		return $meta;
	}

	/**
	 * Method to allow saving empty post.
	 *
	 * @param   boolean  $maybe_empty  Whether the post should be considered "empty".
	 * @param   array    $postarr      Array of post data.
	 *
	 * @return  boolean
	 */
	public static function allow_saving_empty_post( $maybe_empty, $postarr ) {
		if ( $maybe_empty ) {
			// Get current post type.
			$post_type = empty( $postarr['post_type'] ) ? 'post' : $postarr['post_type'];

			// Allow saving empty blog post.
			if ( 'post' == $post_type ) {
				$maybe_empty = false;
			}
		}

		return $maybe_empty;
	}

	/**
	 * Callback function to show fields in meta box.
	 *
	 * @return  void
	 */
	function show() {
		global $post;

		$saved = $this->is_saved();

		// Container
		printf(
			'<div class="rwmb-meta-box" data-autosave="%s">',
			$this->meta_box['autosave'] ? 'true' : 'false'
		);

		wp_nonce_field( "rwmb-save-{$this->meta_box['id']}", "nonce_{$this->meta_box['id']}" );

		// Allow users to add custom code before meta box content
		// 1st action applies to all meta boxes
		// 2nd action applies to only current meta box
		do_action( 'rwmb_before', $this );
		do_action( "rwmb_before_{$this->meta_box['id']}", $this );

		// Print HTML code for all fields
		$current_tab = null;
		$tab_heading = $tab_body = '';

		foreach ( $this->fields as $field ) {
			if ( isset( $field['tab'] ) && $current_tab != $field['tab'] ) {
				$tab_id = sanitize_key( isset( $field['tab_id'] ) ? $field['tab_id'] : $field['tab'] );

				// Update tab heading.
				$tab_heading .= '
					<li class="wr-tab-' . $tab_id . ( empty( $current_tab ) ? ' active' : '' ) . '">
						<a href="#' . $tab_id . '">' . $field['tab'] . '</a>
					</li>';

				// Update tab body.
				$tab_body .= ( empty( $current_tab ) ? '' : '</div>' ) . '
					<div id="' . $tab_id . '" class="wr-nitro-tabs-content ' . ( empty( $current_tab ) ? '' : 'hidden' ) . '">';

				$current_tab = $field['tab'];
			}

			// Start output buffering to hold field output.
			ob_start();

			if ( method_exists( __CLASS__, 'get_class_name' ) ) {
				call_user_func( array( self::get_class_name( $field ), 'show' ), $field, $saved );
			} elseif ( class_exists( 'RWMB_Field' ) && method_exists( 'RWMB_Field', 'call' ) ) {
				RWMB_Field::call( 'show', $field, $saved );
			}

			$tab_body .= ob_get_contents();

			ob_end_clean();
		}

		if ( ! empty( $tab_heading ) ) {
			echo '
				<div class="wr-nitro-tabs" id="' . $this->meta_box['id'] . '">
					<ul class="wr-nitro-tabs-nav">' . $tab_heading . '</ul>
					' . $tab_body . '</div>
				</div>
				<scr' . 'ipt>
					(function($) {
						$("#' . $this->meta_box['id'] . '").on("click", ".wr-nitro-tabs-nav a", function(e) {
							e.preventDefault();
							$("#' . $this->meta_box['id'] . ' .wr-nitro-tabs-nav li").removeClass("active");
							$(this).parent().addClass("active");
							$("#' . $this->meta_box['id'] . ' .wr-nitro-tabs-content").addClass("hidden").filter($(this).attr("href")).removeClass("hidden");
						});
					})(jQuery);
				</scr' . 'ipt>';
		} else {
			echo '' . $tab_body;
		}

		// Include validation settings for this meta-box
		if ( isset( $this->validation ) && $this->validation ) {
			echo '
				<scr' . 'ipt>
				if ( typeof rwmb == "undefined" )
				{
					var rwmb = {
						validationOptions : jQuery.parseJSON( \'' , json_encode( $this->validation ) , '\' ),
						summaryMessage : "' , esc_js( __( 'Please correct the errors highlighted below and try again.', 'wr-nitro' ) ) , '"
					};
				}
				else
				{
					var tempOptions = jQuery.parseJSON( \'' , json_encode( $this->validation ) . '\' );
					jQuery.extend( true, rwmb.validationOptions, tempOptions );
				}
				</scr' . 'ipt>
			';
		}

		// Allow users to add custom code after meta box content
		// 1st action applies to all meta boxes
		// 2nd action applies to only current meta box
		do_action( 'rwmb_after', $this );
		do_action( "rwmb_after_{$this->meta_box['id']}", $this );

		// End container
		echo '</div>';
	}
}
