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
 * This class provides Ajax actions for Header Builder.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Header_Builder {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	/**
	 * Current header ID.
	 *
	 * @var  integer
	 */
	protected static $header_id = 0;

	/**
	 * HTML for current header.
	 *
	 * @var  string
	 */
	protected static $header_html = '';

	/**
	 * CSS for current header.
	 *
	 * @var  string
	 */
	protected static $header_css = '';

	/**
	 * Method to get / set current header builder data.
	 *
	 * @param   string  $prop  Property to set.
	 * @param   mixed   $vals  Value to set.
	 *
	 * @return  mixed
	 */
	public static function prop( $prop, $vals = null ) {
		static $class_props;

		if ( ! isset( $class_props ) ) {
			$class_props = get_class_vars( __CLASS__ );
		}

		if ( false === strpos( $prop, 'header_' ) ) {
			$prop = "header_{$prop}";
		}

		if ( in_array( $prop, $class_props ) ) {
			if ( 2 == func_num_args() ) {
				self::${$prop} = $vals;
			} else {
				return self::${$prop};
			}
		}

		return $vals;
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

		global $pagenow;

		// Register necessary actions.
		add_action( 'admin_init'  , array( __CLASS__, 'enqueue_admin_assets' ), 9999 );
		add_action( 'wr_activate' , array( __CLASS__, 'creat_header_default' ) );

		// Register Ajax actions for building and managing header.
		add_action( 'wp_ajax_set_to_default' , array( __CLASS__, 'set_to_default_in_header' ) );
		add_action( 'wp_ajax_get_menu_html'  , array( __CLASS__, 'get_menu_html' ) );
		add_action( 'wp_ajax_header_builder' , array( __CLASS__, 'get_data' ) );
		add_action( 'wp_ajax_hb_get_data'    , array( __CLASS__, 'get_header_data' ) );
		add_action( 'wp_ajax_duplicate'      , array( __CLASS__, 'duplicate' ) );
		add_action( 'wp_ajax_save_data'      , array( __CLASS__, 'save_data' ) );
		add_action( 'wp_ajax_save_file'      , array( __CLASS__, 'save_file' ) );

		// Refine setting in row action
		add_filter( 'post_row_actions', array( __CLASS__, 'row_actions' ), 2, 100 );

		// Set default in customize
		add_action( 'customize_save_after' , array( __CLASS__, 'set_to_default_in_customize' ) );

		// Refine column in list header page
		add_filter( 'manage_header_builder_posts_columns', array( __CLASS__, 'columns_filter' ), 10, 1 );
		add_action( 'manage_header_builder_posts_custom_column', array( __CLASS__, 'render_columns' ), 10, 2 );

		add_filter( 'post_updated_messages', array( __CLASS__, 'updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( __CLASS__, 'bulk_updated_messages' ) );

		$post_type_current = self::get_post_type();

		// Render html after title
		if( ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) && $post_type_current == 'header_builder' ) {

			// Dequeue all assets of multisite
			add_filter( 'vc_check_post_type_validation', array( __CLASS__, 'post_type_validation' ) );

			add_action( 'edit_form_after_title'  , array( __CLASS__, 'render_html' ), 999999 );

			$current_user = wp_get_current_user();

			// Set screen layout columns
			update_user_meta( $current_user->ID, 'screen_layout_header_builder', 1 );

			add_action( 'save_post_header_builder', array( __CLASS__, 'save_post' ), 10, 1 );

			add_filter( 'tiny_mce_before_init', array( __CLASS__, 'add_blur_change_tiny_mce' ), 9999, 1 );
		}

		if( call_user_func( 'is_' . 'plugin' . '_active', 'amazon-s3-and-cloudfront/wordpress-s3.php' ) || call_user_func( 'is_' . 'plugin' . '_active', 'amazon-s3-and-cloudfront-pro/amazon-s3-and-cloudfront-pro.php' ) ) {
			add_action( 'init', array( __CLASS__, 'remove_filter_as3cf' ), 20 );
		}

		add_action( 'wp_insert_post', array( __CLASS__, 'convert_serialize' ), 9999, 2 );

		// Add header to list post type of PolyLang plugin
		add_filter( 'pll_get_post_types', array( __CLASS__, 'pll_post_types' ) );

		// Refine data when wpml copy from original
		add_filter( 'wpml_copy_from_original_custom_fields', array( __CLASS__, 'wpml_copy_from_original_custom_fields' ) );

		// State that initialization completed.
		self::$initialized = true;
	}

	/**
	 * Remove filter_post of WP Offload S3 Lite plugin.
	 *
	 * @return  void
	 */
	public static function remove_filter_as3cf() {
		$post_type_current = self::get_post_type();

		global $pagenow;

		if( $post_type_current == 'header_builder' || ( $pagenow == 'post.php' && isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == 'header_builder' ) ) {
			WR_Nitro_Helper::remove_action( 'the_content', array( 'AS3CF_Local_To_S3', 'filter_post' ), 100 );
			WR_Nitro_Helper::remove_action( 'the_excerpt', array( 'AS3CF_Local_To_S3', 'filter_post' ), 100 );
			WR_Nitro_Helper::remove_action( 'content_edit_pre', array( 'AS3CF_Local_To_S3', 'filter_post' ), 10 );
			WR_Nitro_Helper::remove_action( 'excerpt_edit_pre', array( 'AS3CF_Local_To_S3', 'filter_post' ), 10 );
			WR_Nitro_Helper::remove_action( 'content_save_pre', array( 'AS3CF_S3_To_Local', 'filter_post' ), 10 );
			WR_Nitro_Helper::remove_action( 'excerpt_save_pre', array( 'AS3CF_S3_To_Local', 'filter_post' ), 10 );
		}
	}

	/**
	 * Refine data when wpml copy from original
	 *
	 * @param  array $custom_fields
	 *
	 * @return  array
	 */
	public static function wpml_copy_from_original_custom_fields( $custom_fields ) {
		if( isset( $_REQUEST['trid'] ) &&  isset( $_REQUEST['lang'] ) && isset( $custom_fields['post_type'] ) && $custom_fields['post_type'] == 'header_builder' ) {
	        $trid = filter_input( INPUT_POST, 'trid' );
				$lang = filter_input( INPUT_POST, 'lang' );

				global $wpdb;

			$post_id = $wpdb->get_var(
			$wpdb->prepare( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid=%d AND language_code=%s",
				                $trid,
				                $lang
            ) );
			$post = get_post( $post_id );

			if( $post ) {
				$custom_fields[ 'header_data' ] = json_encode( unserialize( $post->post_content ) );
			}
		}

		return $custom_fields;
	}

	/**
	 * Add header to list post type of PolyLang plugin
	 *
	 * @param  string $type
	 *
	 * @return  string
	 */
	public static function pll_post_types( $post_types ) {
		$post_types['header_builder'] = 'header_builder';

		return $post_types;
	}

	/**
	 * Post type validation
	 *
	 * @param  string $type
	 *
	 * @return  string
	 */
	public static function post_type_validation( $type ) {
		return '';
	}

	/**
	 * Refine event blur of tiny mce
	 *
	 * @param  array $initArray
	 *
	 * @return  void
	 */
	public static function add_blur_change_tiny_mce( $initArray ) {
		$initArray['setup'] = "function( editor ) {
								editor.on('change', function(e) {
									var content    = editor.getContent();
									var input_hide = jQuery( editor.targetElm ).closest( '.hb-editor' ).find( '.hb-editor-hidden' );
									input_hide.val( content ).trigger('change');
								} );
							}";
		return $initArray;
	}

	/**
	 * Creat a header default if not isset default
	 *
	 * @return  void
	 */
	public static function creat_header_default() {
		$data = new WP_Query( array(
			'post_type' => 'header_builder',
			'posts_per_page' => 1,
			'suppress_filters' => true,
		) );

		if ( ! $data->have_posts() ) {
			error_reporting(0);

			$content = '{"desktop":{"rows":[{"cols":[{"items":[{"_rel":"logo","index":0,"style":{"paddingTop":10,"paddingBottom":10,"paddingRight":10,"paddingLeft":10,"maxWidth":138,"borderTopWidth":0,"borderBottomWidth":0,"borderLeftWidth":0,"borderRightWidth":0,"borderStyle":"none","borderColor":"","fontFamily":"Lato","fontSize":20,"lineHeight":30,"letterSpacing":0,"color":"#333333","fontWeight":400},"content":"Logo","logoType":"image","logoImage":"' . get_template_directory_uri () . '/assets/woorockets/images/logo.png","alignVertical":"left"},{"_rel":"flex","style":{},"index":1}],"unit":"px","style":{"maxWidth":"1170","borderTopWidth":0,"borderBottomWidth":0,"borderLeftWidth":0,"borderRightWidth":0,"borderStyle":"none","borderColor":"","backgroundColor":""}}],"style":{"borderTopWidth":0,"borderBottomWidth":0,"borderLeftWidth":0,"borderRightWidth":0,"borderStyle":"none","borderColor":"","backgroundColor":""},"vertical":true,"backgroundColorSticky":"","textColorSticky":"","sticky":false,"index":0}],"settings":{"type":"horizontal","position":"inherit","positionVertical":"left","unit":"px","showHideFixed":"hide","fixedList":{"miscellaneous":{},"custom_post_type_archives":{},"taxonomies":{},"single":{},"pages":{}},"style":{"width":400,"borderTopWidth":0,"borderBottomWidth":0,"borderLeftWidth":0,"borderRightWidth":0,"borderStyle":"none","borderColor":"","backgroundColor":""},"selected":false}},"mobile":{"rows":[],"settings":{"style":{},"type":1},"type":"mobile"},"listFonts":[],"layout":"desktop","hbLayout":"horizontal","switchLayout":false}';

			$post_data_insert = array(
				'post_title'   => 'Header sample',
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_type'    => 'header_builder',
			);
			$header_id = wp_insert_post( $post_data_insert );

			// Set post meta default
			update_post_meta( $header_id, 'hb_status', 'default' );

			// Update header layout in customize
			set_theme_mod( 'header_layout', $header_id );
		}
	}


	/**
	 * Change messages when a post type is updated.
	 *
	 * @param  array $messages
	 *
	 * @return  array
	 */
	public static function updated_messages( $messages ){
		global $post;

		$scheduled_date = date_i18n( 'M j, Y @ H:i', strtotime( $post->post_date ) );

		$messages['header_builder'] = array(
			 0 => '', // Unused. Messages start at index 1.
			 1 => esc_html__( 'Header updated.', 'wr-nitro' ),
			 2 => esc_html__( 'Custom field updated.', 'wr-nitro' ),
			 3 => esc_html__( 'Custom field deleted.', 'wr-nitro' ),
			 4 => esc_html__( 'Header updated.', 'wr-nitro' ),
			/* translators: %s: date and time of the revision */
			 5 => isset($_GET['revision']) ? sprintf( __( 'Header restored to revision from %s.', 'wr-nitro' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			 6 => esc_html__( 'Header published.', 'wr-nitro' ),
			 7 => esc_html__( 'Header saved.', 'wr-nitro' ),
			 8 => esc_html__( 'Header submitted.', 'wr-nitro' ),
			 9 => sprintf( __( 'Header scheduled for: %s.', 'wr-nitro' ), '<strong>' . $scheduled_date . '</strong>' ),
			10 => esc_html__( 'Header draft updated.', 'wr-nitro' ),
		);

		return $messages;
	}


	/**
	 * Change bulk messages when a post type is updated.
	 *
	 * @param  array $messages
	 *
	 * @return  array
	 */
	public static function bulk_updated_messages( $messages ){
		$bulk_counts = array(
			'updated'   => isset( $_REQUEST['updated'] )   ? absint( $_REQUEST['updated'] )   : 0,
			'locked'    => isset( $_REQUEST['locked'] )    ? absint( $_REQUEST['locked'] )    : 0,
			'deleted'   => isset( $_REQUEST['deleted'] )   ? absint( $_REQUEST['deleted'] )   : 0,
			'trashed'   => isset( $_REQUEST['trashed'] )   ? absint( $_REQUEST['trashed'] )   : 0,
			'untrashed' => isset( $_REQUEST['untrashed'] ) ? absint( $_REQUEST['untrashed'] ) : 0,
		);

		$messages['header_builder'] = array(
			'updated'   => _n( '%s header updated.', '%s headers updated.', 'wr-nitro' ),
			'locked'    => ( 1 == $bulk_counts['locked'] ) ? __( '1 header not updated, somebody is editing it.', 'wr-nitro' ) : _n( '%s header not updated, somebody is editing it.', '%s headers not updated, somebody is editing them.', 'wr-nitro' ),
			'deleted'   => _n( '%s header permanently deleted.', '%s headers permanently deleted.', 'wr-nitro' ),
			'trashed'   => _n( '%s header moved to the Trash.', '%s headers moved to the Trash.', 'wr-nitro' ),
			'untrashed' => _n( '%s header restored from the Trash.', '%s headers restored from the Trash.', 'wr-nitro' ),
		);

		return $messages;
	}

	/**
	 * Refine column in list header
	 *
	 * @param array $column
	 *
	 * @return array
	 *
	 */
	public static function columns_filter( $columns ) {
		unset( $columns['date'] );

		$columns['layout'] = esc_html__( 'Layout', 'wr-nitro' );
		$columns['status'] = esc_html__( 'Default', 'wr-nitro' );
		$columns['author'] = esc_html__( 'Author', 'wr-nitro' );
		$columns['date']   = esc_html__( 'Date', 'wr-nitro' );

		return $columns;
	}

	/**
	 * Ouput custom columns.
	 *
	 * @param string $column
	 * @param number $post_id
	 *
	 * @return string
	 *
	 */
	public static function render_columns( $column, $post_id ) {
		global $post;

		switch ( $column ) {
			case 'status' :

				if( call_user_func( 'is_' . 'plugin' . '_active', 'sitepress-multilingual-cms/sitepress.php' ) && isset( $_GET['lang'] ) && $_GET['lang'] != 'all' ) {
					global $wpdb;

					$trid = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id=%d",
							$post_id
						)
					);

					if ( $trid ) {
						$list_post = $wpdb->get_results(
							$wpdb->prepare(
								"SELECT post_id, meta_key, meta_value
								FROM {$wpdb->prefix}icl_translations
								INNER JOIN {$wpdb->postmeta}
								ON {$wpdb->prefix}icl_translations.element_id={$wpdb->postmeta}.post_id WHERE trid=%d AND meta_key='hb_status' AND meta_value='default'",
								$trid
							)
						);

						if( $list_post ) {
							foreach( $list_post as $val ){
								if( $val->meta_key == 'hb_status' &&  $val->meta_value == 'default' ) {
									$post_id_default = intval( $val->post_id );

									break;
								}
							}
						}
					}
				}

				$post_id_layout = isset( $post_id_default ) ? $post_id_default : $post_id;

				$status = get_post_meta( $post_id_layout, 'hb_status', true );

				if( $status && $status == 'default' ) {
					echo '<span class="hb-default" data-id="' . intval( $post_id ) . '">' . esc_html__( 'Default', 'wr-nitro' ) . '</span>';
				} else {
					echo '<span class="hb-set-default" data-id="' . intval( $post_id ) . '">' . esc_html__( 'Set to default', 'wr-nitro' ) . '</span>';
				}

				break;
			case 'layout' :
				$layout = get_post_meta( $post_id, 'hb_layout', true );

				$layout_translate = array(
					'horizontal' => esc_html__( 'Horizontal', 'wr-nitro'  ),
					'vertical'   => esc_html__( 'Vertical', 'wr-nitro'  ),
				);

				if( $layout && isset( $layout_translate[ $layout ] ) ) {
					echo '<span>' . $layout_translate[ $layout ] . '</span>';
				} else {
					echo '<span>' . $layout_translate['horizontal'] . '</span>';
				}

				break;
		}
	}

	/**
	 * Set row actions.
	 *
	 * @param  array $actions
	 * @param  WP_Post $post
	 *
	 * @return array
	 */
	public static function row_actions( $actions, $post ) {
		if ( $post->post_type == 'header_builder' ) {
			if ( isset( $actions['inline hide-if-no-js'] ) ) {
				unset( $actions['inline hide-if-no-js'] );
			}
		}

		return $actions;
	}

	/**
	 * Save post metadata when a post is saved.
	 *
	 * @param int $post_id The post ID.
	 */
	public static function save_post( $post_id ) {
		$list_layout    = array( 'horizontal', 'vertical' );
		$layout_current = get_post_meta( $post_id, 'hb_layout', true );

		// Set layout when creat new a header
		if( ! $layout_current && ! in_array( $layout_current, $list_layout ) ) {
			global $pagenow;

			if( ! empty( $_GET['layout'] ) && in_array( $_GET['layout'] , $list_layout ) ) {
				$layout = esc_attr( $_GET['layout'] );
			} else {
				$layout = 'horizontal';
			}

			// Get layout of trid for WPML plugin
			if ( function_exists( 'icl_object_id' ) && $pagenow == 'post-new.php' && ! empty( $_GET['source_lang'] ) && ! empty( $_GET['trid'] ) ) {
				global $wpdb;

				$lang    = esc_attr( $_GET['source_lang'] );
				$trid    = intval( $_GET['trid'] );
				$lang_id = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid=%d AND language_code=%s",
						$trid,
						$lang
					)
				);

				if( $lang_id ){
					$layout = get_post_meta( $lang_id, 'hb_layout', true );
				}
			}

			update_post_meta( $post_id, 'hb_layout', $layout );
		}
	}

	/**
	 * Convert json to serialize when a post is saved.
	 *
	 * @param int $post_id The post ID.
	 * @param WP_Post $post    Post object.
	 */
	public static function convert_serialize( $post_id, $post ) {

		// Conver JSON to serialize
		if( $post->post_type == 'header_builder' && $post->post_content ) {
			$data_content = json_decode( $post->post_content, true );

			// Check validate is json
			if( phpversion() >= 5.3 && json_last_error() !== 0 ) {
				return;
			}

			$data_content = serialize( $data_content );

			global $wpdb;

			$wpdb->update(
				$wpdb->posts,
				array(
					'post_content' => $data_content,
				),
				array(
					'ID' => $post_id,
				),
				array(
					'%s',
				)
			);
		}
	}

	/**
	 * Render html content setting
	 *
	 * @param array $post_info.
	 *
	 * @return  html
	 */
	public static function render_html( $post_info ) {
		// Creat nonce field
		wp_nonce_field( 'header_builder_key', 'nonce_header_builder' );

		include get_template_directory() . '/woorockets/includes/header-builder/layout.php';
	}

	/**
	 * Add a note edit header in bar
	 *
	 * @return  void
	 */
	public static function edit_header_in_bar(){

		if( ! is_super_admin() ) return;

		global $wp_admin_bar;

		if ( self::$header_id > 0 ) {
			$wp_admin_bar->add_node(array(
				'id'    => 'edit-header',
				'title' => esc_html__( 'Edit Header', 'wr-nitro' ),
				'href'  => admin_url() . 'post.php?post=' . self::$header_id . '&action=edit'
			));
		} else {
			$wp_admin_bar->add_node(array(
				'id'    => 'edit-header',
				'title' => esc_html__( 'List Header', 'wr-nitro' ),
				'href'  => admin_url() . 'edit.php?post_type=header_builder'
			));
		}
	}

	/**
	 * Return new neutral header
	 *
	 * @return  string
	 */

	protected static function recursive_check_name( $name_neutral, $number_name_neutral = 0 ) {
		$name_header = esc_attr( $name_neutral . ' ' . __( 'copy', 'wr-nitro' ) . ( $number_name_neutral > 0 ? ( ' ' . $number_name_neutral ) : NULL )  );

		// Verify header name.
		$data = new WP_Query( array(
			'post_type' => 'header_builder',
			'post_title' => $name_header,
			'suppress_filters' => true,
		) );

		if ( $data->have_posts() ) {
			$number_name_neutral++;

			return self::recursive_check_name( $name_neutral, $number_name_neutral );
		} else {
			return $name_header;
		}
	}

	/**
	 * Duplicate header builder
	 *
	 * @return  void
	 */
	public static function duplicate() {
		error_reporting(0);

		if ( ! ( isset( $_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], 'wr_nitro_nonce_check' ) && isset( $_POST['header_id'] ) && (int) $_POST['header_id'] > 0 ) )
			die( json_encode( array( 'status' => 'false' ) ) );

		$header_id 		= $_POST['header_id'];
		$header_name 	= esc_attr( $_POST['header_name'] );

		$header_data = new WP_Query( array(
			'post__in' => array( $header_id ),
			'post_type' => 'header_builder',
			'suppress_filters' => true,
		) );

		if ( ! $header_data->have_posts() ) {
			die( json_encode( array( 'status' => 'false' ) ) );
		} else {
			$header_data = ( array ) current( $header_data->posts );
		}

		/*** Check neutral name header old ***/
		$header_new_name = self::recursive_check_name( $header_data['post_title'] );

		$post_data_insert = array(
			'post_title'   => $header_new_name,
			'post_content' => $header_data['post_content'],
			'post_excerpt' => $header_data['post_excerpt'],
			'post_status'  => 'header_normal',
			'post_type'    => 'header_builder',
			'suppress_filters' => true,
		);

		$post_id = wp_insert_post( $post_data_insert );

		$header_duplicate = new WP_Query( array(
			'post__in' => array( $post_id ),
			'post_type' => 'header_builder',
		) );

		$header_duplicate = current( $header_duplicate->posts );

		$result = array(
			'status'          => 'true',
			'header_id'       => $post_id,
			'header_new_name' => $header_new_name,
			'author'          => get_the_author_meta( 'display_name' , $header_duplicate->post_author ),
			'time_create'     => WR_Nitro_Helper::format_column_date( $header_duplicate, TRUE ),
		);

		die( json_encode( $result ) );
	}


	/**
	 * Get post type current in page
	 *
	 * @return  string
	 */
	public static function get_post_type() {
		global $pagenow;
		$post_type_current = '';

		if( ( $pagenow == 'edit.php' || $pagenow == 'post-new.php' ) && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'header_builder' ) {
			$post_type_current = esc_attr( $_GET['post_type'] );
		} elseif( $pagenow == 'post.php' ) {
			$post_type = get_post_type( isset( $_GET['post'] ) ? $_GET['post'] : 0 );

			if ( 'header_builder' == $post_type ) {
				$post_type_current = 'header_builder';
			}
		}

		return $post_type_current;
	}

	/**
	 * Enqueue required admin assets for header builder.
	 *
	 * @return  void
	 */
	public static function enqueue_admin_assets() {
		global $pagenow;

		$post_type_current = self::get_post_type();

		if( $post_type_current == 'header_builder' ) {

			// Fix not working when activate WooImporter plugin
			wp_dequeue_script( 'jquery-ui-datepicker' );

			$uri_directory = get_template_directory_uri();

			$wr_is_wpml_activated = call_user_func( 'is_' . 'plugin' . '_active', 'sitepress-multilingual-cms/sitepress.php' );

			// Include file style for WPML plugin
			if( $wr_is_wpml_activated && ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) ) {
				wp_enqueue_style( 'wr-header-builder-wpml' , ICL_PLUGIN_URL . '/res/css/language-selector.css', array(), ICL_SITEPRESS_VERSION );
			}

			/* File global */

			// Enqueue style for header builder.
			wp_enqueue_style( 'wr-header-builder'         , $uri_directory . '/assets/woorockets/css/admin/header-builder.css' );

			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'backbone' );

			// Enqueue script for header builder.
			wp_enqueue_script( 'wr-header-builder-admin'  , $uri_directory . '/assets/woorockets/js/admin/header-builder/admin.js', array(), false, true );

			// Embed data site data.
			wp_localize_script( 'wr-header-builder-admin' , 'wr_site_data', WR_Nitro_Assets::localize_links() );

			// In detail
			if( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {

				if( $wr_is_wpml_activated ) {
					wp_enqueue_style( 'wr-header-builder-wpml' , ICL_PLUGIN_URL . '/res/css/language-selector.css', array(), ICL_SITEPRESS_VERSION );
				}

				// Embed data site data.
				wp_localize_script( 'wr-header-builder-admin' , 'wr_hb_data_allow', self::data_allow() );

				// Enqueue font Awesome.
				wp_enqueue_style( 'font-awesome'              , $uri_directory . '/assets/3rd-party/font-awesome/css/font-awesome.min.css' );

				// Enqueue spectrum color.
				wp_enqueue_style( 'spectrum-css'              , $uri_directory . '/assets/3rd-party/spectrum/spectrum.css');

				// Enqueue style for google fonts.
				wp_enqueue_style( 'wr-header-google-fonts'    , $uri_directory . '/assets/woorockets/css/admin/google-fonts.css' );

				// Enqueue jquery-ui.
				wp_enqueue_script( 'jquery-ui'                , $uri_directory . '/assets/3rd-party/jquery-ui/jquery-ui.min.js', array(), false, true );

				// Enqueue spectrum color.
				wp_enqueue_script( 'spectrum-color'           , $uri_directory . '/assets/3rd-party/spectrum/spectrum.js', array(), false, true );

				// Enqueue HB libs.
				wp_enqueue_script( 'wr-header-builder-libs'   , $uri_directory . '/assets/woorockets/js/admin/header-builder/libs.js', array(), false, true );

				// Enqueue sortable js.
				wp_enqueue_script( 'sortable-js'              , $uri_directory . '/assets/3rd-party/sortable-js/sortable.min.js', array(), false, true );

				// Enqueue Backbone Model.
				wp_enqueue_script( 'backbone-model'           , $uri_directory . '/assets/3rd-party/backbone/backbone-model.js', array(), false, true );

				// Enqueue Backbone View.
				wp_enqueue_script( 'backbone-view'            , $uri_directory . '/assets/3rd-party/backbone/backbone-view.js', array(), false, true );

				// Enqueue HB Models.
				wp_enqueue_script( 'wr-header-builder-model'  , $uri_directory . '/assets/woorockets/js/admin/header-builder/models.js', array(), false, true );

				// Enqueue HB Models.
				wp_enqueue_script( 'wr-header-builder-view'   , $uri_directory . '/assets/woorockets/js/admin/header-builder/views.js', array(), false, true );

				// Enqueue WordPress's media library.
				wp_enqueue_media();
			}
		}
	}

	/**
	 * @param: int menu_id
	 * return: string html
	 */
	public static function get_menu_html() {
		error_reporting(0);
		if ( isset( $_POST['menu_id'] ) ) {
			$id = $_POST['menu_id'];
			wp_nav_menu( array( 'menu' => $id, 'depth' => 1, 'container' => '' , 'fallback_cb' => NULL ) );
		}
		exit;
	}

	/**
	 * Print data for Header Builder.
	 *
	 * @return  mixed
	 */
	public static function get_data() {
		error_reporting(0);
		if ( isset( $_REQUEST['menu_id'] ) ) {
			wp_nav_menu( array( menu => $_REQUEST['menu_id'], depth => 1 ) );
		}

		exit;
	}

	/**
	 * Get a header profile.
	 *
	 * @return  void
	 */
	public static function get_header_data() {
		error_reporting(0);
		$result = array( 'status' => false );

		if ( isset( $_POST['header_id'] ) && is_numeric( $_POST['header_id'] ) ) {
			$post = get_post( $_POST['header_id'] );
			$data = get_post_meta( $post->ID, 'header_builder', true );

			$result = array_merge(
				$result,
				array(
					'status' => true,
					'data'   => array(
						'post_title' => $post->post_title,
						'post_meta'  => $data,
					)
				)
			);
		}

		echo json_encode( $result );

		exit;
	}

	/**
	 * Set default in header profile.
	 *
	 * @return  void
	 */
	public static function set_to_default_in_customize() {
		$header_id = get_theme_mod( 'header_layout', 0 );
		$post_type = get_post_type( $header_id );

		if ( 'header_builder' == $post_type )
			self::set_to_default( $header_id );
	}

	/**
	 * Set default in header profile.
	 *
	 * @return  void
	 */
	public static function set_to_default_in_header() {

		// Check nonce
		if ( !isset( $_POST['_nonce'] ) || !wp_verify_nonce( $_POST['_nonce'], 'wr_nitro_nonce_check' ) )
			exit( json_encode( array( 'status' => 'false' ) ) );

		// Check header id
		if( !isset( $_POST['header_id'] ) || (int) $_POST['header_id'] <= 0 )
			exit( json_encode( array( 'status' => 'false' ) ) );

		$header_id = $_POST['header_id'];
		$post_type = get_post_type( $header_id );

		if ( 'header_builder' != $post_type )
			exit( json_encode( array( 'status' => 'false' ) ) );

		// Update header layout in customize
		set_theme_mod( 'header_layout', $header_id );

		self::set_to_default( $header_id );

		exit( json_encode( array( 'status' => 'true' ) ) );

		die;
	}

	/**
	 * Set default header.
	 *
	 * @return  void
	 */
	public static function set_to_default( $header_id ) {
		$list_header = new WP_Query( array(
			'post_type'   => 'header_builder',
			'post_status' => array( 'publish', 'pending', 'draft', 'private', 'trash' ),
			'meta_query'  => array(
				array(
					'key'   => 'hb_status',
					'value' => 'default'
				),
			),
			'posts_per_page' => -1,
			'suppress_filters' => true,
		));

		// Set to normal headers default
		if( $list_header->post_count ) {
			foreach( $list_header->posts as $val ){
				update_post_meta( $val->ID, 'hb_status', '' );
			}
		}

		update_post_meta( $header_id, 'hb_status', 'default' );
	}

	/**
	 * Fillter property for style
	 *
	 * @param array $list_property.
	 *
	 * @param array $key_group.
	 *
	 * @return  array
	 */
	public static function fillter_property( $list_property ) {
		if( ! ( isset( $list_property['backgroundImage'] ) && $list_property['backgroundImage'] ) ) {
			unset( $list_property['backgroundImage'] );
			unset( $list_property['backgroundPosition'] );
			unset( $list_property['backgroundRepeat'] );
			unset( $list_property['backgroundSize'] );
		}

		if( ! ( isset( $list_property['borderStyle'] ) && $list_property['borderStyle'] != 'none' ) ) {
			unset( $list_property['borderStyle'] );
			unset( $list_property['borderBottomWidth'] );
			unset( $list_property['borderColor'] );
			unset( $list_property['borderLeftWidth'] );
			unset( $list_property['borderRightWidth'] );
			unset( $list_property['borderTopWidth'] );
		}

		return $list_property;
	}

	/**
	 * Data allow when save or show header.
	 *
	 * @return  void
	 */
	public static function data_allow() {
		return array(
			'settings' => array(
				'ID'               => '',
				'className'        => '',
				'fixedList'        => array(
					'custom_post_type_archives' => '',
					'miscellaneous'             => '',
					'pages'                     => '',
					'single'                    => '',
					'taxonomies'                => '',
				),
				'position'         => 'inherit',
				'positionVertical' => 'left',
				'selected'         => '',
				'showHideFixed'    => 'hide',
				'style' => array(
					'backgroundColor'    => '',
					'backgroundImage'    => '',
					'backgroundPosition' => 'center center',
					'backgroundRepeat'   => 'no-repeat',
					'backgroundSize'     => 'cover',
					'borderBottomWidth'  => '',
					'borderColor'        => '',
					'borderLeftWidth'    => '',
					'borderRadius'       => 0,
					'borderRightWidth'   => '',
					'borderStyle'        => 'none',
					'borderTopWidth'     => '',
					'marginBottom'       => '',
					'marginLeft'         => '',
					'marginRight'        => '',
					'marginTop'          => '',
					'paddingBottom'      => '',
					'paddingLeft'        => '',
					'paddingRight'       => '',
					'paddingTop'         => '',
					'width'              => 400,
				),
				'type'             => 'vertical',
				'unit'             => 'px',
			),
			'rows' => array(
				'ID'                    => '',
				'backgroundColorSticky' => '',
				'className'             => '',
				'cols'                  => '',
				'heightSticky'          => '',
				'index'                 => '',
				'selected'              => '',
				'sticky'                => false,
				'sticky_effect'         => 'normal',
				'style' => array(
					'backgroundColor'    => '',
					'backgroundImage'    => '',
					'backgroundPosition' => 'center center',
					'backgroundRepeat'   => 'no-repeat',
					'backgroundSize'     => 'cover',
					'borderBottomWidth'  => '',
					'borderColor'        => '',
					'borderLeftWidth'    => '',
					'borderRadius'       => 0,
					'borderRightWidth'   => '',
					'borderStyle'        => 'none',
					'borderTopWidth'     => '',
					'marginBottom'       => '',
					'marginLeft'         => '',
					'marginRight'        => '',
					'marginTop'          => '',
					'paddingBottom'      => '',
					'paddingLeft'        => '',
					'paddingRight'       => '',
					'paddingTop'         => '',
				),
				'textColorSticky'       => '',
				'themeColor'            => false,
				'vertical'              => true,
			),
			'cols' => array(
				'items' => '',
				'style' => array(
					'backgroundColor'    => '',
					'backgroundImage'    => '',
					'backgroundPosition' => 'center center',
					'backgroundRepeat'   => 'no-repeat',
					'backgroundSize'     => 'cover',
					'borderBottomWidth'  => '',
					'borderColor'        => '',
					'borderLeftWidth'    => '',
					'borderRadius'       => 0,
					'borderRightWidth'   => '',
					'borderStyle'        => 'none',
					'borderTopWidth'     => '',
					'marginBottom'       => '',
					'marginTop'          => '',
					'maxWidth'           => 1170,
					'paddingBottom'      => '',
					'paddingLeft'        => '',
					'paddingRight'       => '',
					'paddingTop'         => '',
				),
				'unit' => 'px',
			),
			'items' => array(
				'search' => array(
					'ID'            => '',
					'_rel'          => '',
					'alignVertical' => 'left',
					'animation'     => 'bottom-to-top',
					'className'     => '',
					'centerElement' => false,
					'iconColor'     => '#333333',
					'hoverIconColor'=> '#d6aa74',
					'iconFontSize'  => 14,
					'index'         => '',
					'layout'        => 'dropdown',
					'liveSearch'    => array(
						'active'          => false,
						'max_results'     => 5,
						'min_characters'  => 0,
						'thumb_size'      => 50,
						'searchIn'        => array(
							'title'	      => 1,
							'description' => 1,
							'content'	  => 1,
							'sku'	      => 1,
						),
						'show_category'   => false,
						'show_suggestion' => false,
					),
					'marginTop'   => '',
					'searchStyle' => 'light-background',
					'selected'    => '',
					'placeholder' => '',
					'style'       => array(
						'backgroundColor'    => '',
						'backgroundImage'    => '',
						'backgroundPosition' => 'center center',
						'backgroundRepeat'   => 'no-repeat',
						'backgroundSize'     => 'cover',
						'borderBottomWidth'  => 0,
						'borderColor'        => '',
						'borderLeftWidth'    => 0,
						'borderRadius'       => 0,
						'borderRightWidth'   => 0,
						'borderStyle'        => 'none',
						'borderTopWidth'     => 0,
						'marginBottom'       => '',
						'marginLeft'         => '',
						'marginRight'        => '',
						'marginTop'          => '',
						'paddingBottom'      => '',
						'paddingLeft'        => '',
						'paddingRight'       => '',
						'paddingTop'         => '',
					),
					'widthInput'           => '300',
					'buttonType'           => 'icon',
					'textButton'           => __( 'Search', 'wr-nitro' ),
					'textColorButton'      => '#ffffff',
					'bgColorButton'        => '#d6aa74',
					'hoverTextColorButton' => '#ffffff',
					'hoverBgColorButton'   => '#dea35b',
				),
				'menu' => array(
					'ID'            => '',
					'_rel'          => '',
					'alignVertical' => 'left',
					'animation'     => 'slide-in-on-top',
					'background' => array(
						'backgroundColor'    => '#ffffff',
						'backgroundImage'    => '',
						'backgroundPosition' => 'center center',
						'backgroundRepeat'   => 'no-repeat',
						'backgroundSize'     => 'cover',
					),
					'backgroundColorMobile' => '#f9f9f9',
					'className'             => '',
					'centerElement'         => false,
					'effect'                => 'fade',
					'hoverStyle'            => 'default',
					'iconColor'             => '#333333',
					'iconColorMobile'       => '#333333',
					'index'                 => '',
					'itemSpacing'           => 30,
					'layoutStyle'           => 'text',
					'layoutStyleMobile'     => 'icon',
					'link'                  => array(
						'borderRadius' => 0,
						'style'        => array(
							'backgroundColorHover' => '#ebebeb',
							'color'                => '#333333',
							'colorHover'           => '#d6aa74',
							'outlineColorHover'    => '#ebebeb',
							'underlineColorHover'  => '#ebebeb',
						),
						'underlineStyle' => 'solid',
						'underlineWidth' => 2,
					),
					'menuID'    => '',
					'menuStyle' => 'fullscreen',
					'position'  => 'left',
					'selected'  => '',
					'spacing'   => array(
						'backgroundColor'    => '',
						'backgroundImage'    => '',
						'backgroundPosition' => 'center center',
						'backgroundRepeat'   => 'no-repeat',
						'backgroundSize'     => 'cover',
						'borderBottomWidth'  => '',
						'borderColor'        => '',
						'borderLeftWidth'    => '',
						'borderRadius'       => 0,
						'borderRightWidth'   => '',
						'borderStyle'        => 'none',
						'borderTopWidth'     => '',
						'marginBottom'       => '',
						'marginLeft'         => '',
						'marginRight'        => '',
						'marginTop'          => '',
						'paddingBottom'      => 10,
						'paddingLeft'        => 10,
						'paddingRight'       => 10,
						'paddingTop'         => 10,
					),
					'subMenu'   => array(
						'animation'            => 'scale',
						'animationVertical'    => 'normal',
						'background'           => '',
						'effectNormalVertical' => 'none',
						'fontWeight'           => 400,
						'link'                 => array(
							'style' => array(
								'color'      => '',
								'colorHover' => '',
							),
						),
						'maginTop' => '',
						'style'    => array(
							'fontSize'       => '',
							'fontStyle'      => 'normal',
							'letterSpacing'  => '',
							'lineHeight'     => '',
							'textDecoration' => 'none',
							'textTransform'  => 'none',
						),
						'width' => 220,
					),
					'textAlign'    => 'left',
					'textSettings' => array(
						'fontFamily'     => 'Lato',
						'fontSize'       => 13,
						'fontStyle'      => '',
						'fontWeight'     => 400,
						'letterSpacing'  => 0,
						'lineHeight'     => 21,
						'textDecoration' => '',
						'textTransform'  => 'none',
					),
					'unitWidthSidebar' => 'px',
					'verticalAlign'    => 'middle',
					'widthSidebar'     => 300,
				),
				'sidebar' => array(
					'ID'            => '',
					'_rel'          => '',
					'alignVertical' => 'left',
					'className'     => '',
					'centerElement' => false,
					'frontCSS' => array(
						'spacing' => array(
							'backgroundColor'   => '',
							'backgroundImage'   => '',
							'backgroundPosition'=> 'center center',
							'backgroundRepeat'  => 'no-repeat',
							'backgroundSize'    => 'cover',
							'borderBottomWidth' => '',
							'borderColor'       => '',
							'borderLeftWidth'   => '',
							'borderRadius'      => 0,
							'borderRightWidth'  => '',
							'borderStyle'       => 'none',
							'borderTopWidth'    => '',
							'marginBottom'      => '',
							'marginLeft'        => '',
							'marginRight'       => '',
							'marginTop'         => '',
							'paddingBottom'     => 10,
							'paddingLeft'       => 10,
							'paddingRight'      => 10,
							'paddingTop'        => 10,
						),
						'style' => array(
							'backgroundColor'    => '',
							'backgroundImage'    => '',
							'backgroundPosition' => 'center center',
							'backgroundRepeat'   => 'no-repeat',
							'backgroundSize'     => 'cover',
							'height'             => 300,
							'textTransform'      => 'none',
							'width'              => 300,
						),
					),
					'iconColor' => '#333333',
					'icon'      => 'fa fa-th',
					'iconSize'  => '18',
					'hoverIconColor' => '#d6aa74',
					'index'     => '',
					'position'  => 'left',
					'selected'  => '',
					'sidebarID' => '',
					'unit'      => 'px',
				),
				'text' => array(
					'ID'            => '',
					'_rel'          => '',
					'alignVertical' => 'left',
					'className'     => '',
					'content'       => 'Text',
					'centerElement' => false,
					'index'         => '',
					'selected'      => '',
					'style'         => array(
						'backgroundColor'    => '',
						'backgroundImage'    => '',
						'backgroundPosition' => 'center center',
						'backgroundRepeat'   => 'no-repeat',
						'backgroundSize'     => 'cover',
						'borderBottomWidth'  => '',
						'borderColor'        => '',
						'borderLeftWidth'    => '',
						'borderRadius'       => 0,
						'borderRightWidth'   => '',
						'borderStyle'        => 'none',
						'borderTopWidth'     => '',
						'marginBottom'       => '',
						'marginLeft'         => '',
						'marginRight'        => '',
						'marginTop'          => '',
						'paddingBottom'      => 10,
						'paddingLeft'        => 10,
						'paddingRight'       => 10,
						'paddingTop'         => 10,
					),
				),
				'logo' => array(
					'ID'                    => '',
					'_rel'                  => '',
					'alignVertical'         => 'left',
					'className'             => '',
					'content'               => 'Logo',
					'centerElement'         => false,
					'index'                 => '',
					'logoImage'             => '',
					'logoImageRetina'       => '',
					'logoImageSticky'       => '',
					'logoImageStickyRetina' => '',
					'logoType'              => 'image',
					'selected'              => '',
					'maxWidthSticky'        => 138,
					'style'                 => array(
						'backgroundColor'    => '',
						'backgroundImage'    => '',
						'backgroundPosition' => 'center center',
						'backgroundRepeat'   => 'no-repeat',
						'backgroundSize'     => 'cover',
						'borderBottomWidth'  => '',
						'borderColor'        => '',
						'borderLeftWidth'    => '',
						'borderRadius'       => 0,
						'borderRightWidth'   => '',
						'borderStyle'        => 'none',
						'borderTopWidth'     => '',
						'color'              => '#333333',
						'fontFamily'         => 'Lato',
						'fontSize'           => 20,
						'fontStyle'          => '',
						'fontWeight'         => 400,
						'letterSpacing'      => 0,
						'lineHeight'         => 30,
						'marginBottom'       => '',
						'marginLeft'         => '',
						'marginRight'        => '',
						'marginTop'          => '',
						'maxWidth'           => 138,
						'paddingBottom'      => 10,
						'paddingLeft'        => 10,
						'paddingRight'       => 10,
						'paddingTop'         => 10,
						'textDecoration'     => '',
					),
				),
				'social' => array(
					'ID'              => '',
					'_rel'            => '',
					'alignVertical'   => 'left',
					'backgroundColor' => '',
					'borderColor'     => '#333333',
					'borderRadius'    => 0,
					'borderStyle'     => 'none',
					'borderWidth'     => 1,
					'className'       => '',
					'centerElement'   => false,
					'iconColor'       => '',
					'iconSize'        => 'small',
					'iconSpacing'     => 5,
					'iconStyle'       => 'none',
					'index'           => '',
					'selected'        => '',
					'socialList'      => '',
					'style'           => array(
						'backgroundColor'    => '',
						'backgroundImage'    => '',
						'backgroundPosition' => 'center center',
						'backgroundRepeat'   => 'no-repeat',
						'backgroundSize'     => 'cover',
						'borderBottomWidth'  => '',
						'borderColor'        => '',
						'borderLeftWidth'    => '',
						'borderRadius'       => 0,
						'borderRightWidth'   => '',
						'borderStyle'        => 'none',
						'borderTopWidth'     => '',
						'marginBottom'       => '',
						'marginLeft'         => '',
						'marginRight'        => '',
						'marginTop'          => '',
						'paddingBottom'      => 10,
						'paddingLeft'        => 10,
						'paddingRight'       => 10,
						'paddingTop'         => 10,
					),
				),
				'shopping-cart' => array(
					'ID'                => '',
					'_rel'              => '',
					'alignVertical'     => 'left',
					'animationDropdown' => 'dropdown-fade',
					'animationSidebar'  => 'sidebar-slide-in-on-top',
					'className'         => '',
					'centerElement'     => false,
					'colorType'         => 'light',
					'colorPrice'        => '#cc0000',
					'titleText'         => '',
					'colorTitle'        => '#333333',
					'iconName'          => 'fa fa-shopping-cart',
					'index'             => '',
					'marginTop'         => '',
					'position'          => 'position-sidebar-left',
					'selected'          => '',
					'showCartInfo'      => 'number_price',
					'style'             => array(
						'backgroundColor'    => '',
						'backgroundImage'    => '',
						'backgroundPosition' => 'center center',
						'backgroundRepeat'   => 'no-repeat',
						'backgroundSize'     => 'cover',
						'borderBottomWidth'  => 0,
						'borderColor'        => '',
						'borderLeftWidth'    => 0,
						'borderRadius'       => 0,
						'borderRightWidth'   => 0,
						'borderStyle'        => 'none',
						'borderTopWidth'     => 0,
						'marginBottom'       => '',
						'marginLeft'         => '',
						'marginRight'        => '',
						'marginTop'          => '',
						'paddingBottom'      => 10,
						'paddingLeft'        => 10,
						'paddingRight'       => 10,
						'paddingTop'         => 10,
					),
					'styleIcon' =>  array(
						'color'      => '#333333',
						'hoverColor' => '#d6aa74',
						'fontSize'   => 14,
					),
					'type' => 'dropdown',
				),
				'wpml' => array(
					'ID'            => '',
					'_rel'          => '',
					'alignVertical' => 'left',
					'className'     => '',
					'centerElement' => false,
					'index'         => '',
					'selected'      => '',
					'style'         => array(
						'borderBottomWidth' => '',
						'borderColor'       => '',
						'borderLeftWidth'   => '',
						'borderRadius'      => 0,
						'borderRightWidth'  => '',
						'borderStyle'       => 'none',
						'borderTopWidth'    => '',
						'marginBottom'      => '',
						'marginLeft'        => '',
						'marginRight'       => '',
						'marginTop'         => '',
						'paddingBottom'     => 10,
						'paddingLeft'       => 10,
						'paddingRight'      => 10,
						'paddingTop'        => 10,
					)
				),
				'wishlist' => array(
					'ID'              => '',
					'_rel'            => '',
					'alignVertical'   => 'left',
					'className'       => '',
					'centerElement'   => false,
					'index'           => '',
					'selected'        => '',
					'textLabel'       => '',
					'colorLabel'      => '#333333',
					'colorIcon'       => '#333333',
					'hoverIconColor'  => '#d6aa74',
					'iconSize'        => '14',
					'labelSize'       => '14',
					'labelPosition'   => 'right',
					'style'           => array(
						'borderBottomWidth'  => '',
						'borderColor'        => '',
						'borderLeftWidth'    => '',
						'borderRadius'       => 0,
						'borderRightWidth'   => '',
						'borderStyle'        => 'none',
						'borderTopWidth'     => '',
						'color'              => '#333333',
						'marginBottom'       => '',
						'marginLeft'         => '',
						'marginRight'        => '',
						'marginTop'          => '',
						'paddingBottom'      => 10,
						'paddingLeft'        => 10,
						'paddingRight'       => 10,
						'paddingTop'         => 10,
					)
				),
				'currency' => array(
					'ID'            => '',
					'_rel'          => '',
					'alignVertical' => 'left',
					'className'     => '',
					'centerElement' => false,
					'index'         => '',
					'selected'      => '',
					'show_flag'     => true,
					'currency_defalut' => 0,
					'textLabel'       => '',
					'labelPosition'   => 'left',
					'style'         => array(
						'color'             => '#333333',
						'borderBottomWidth' => '',
						'borderColor'       => '',
						'borderLeftWidth'   => '',
						'borderRadius'      => 0,
						'borderRightWidth'  => '',
						'borderStyle'       => 'none',
						'borderTopWidth'    => '',
						'marginBottom'      => '',
						'marginLeft'        => '',
						'marginRight'       => '',
						'marginTop'         => '',
						'paddingBottom'     => 10,
						'paddingLeft'       => 10,
						'paddingRight'      => 10,
						'paddingTop'        => 10,
					),
				),
				'flex' => array(
					'_rel'     => '',
					'index'    => '',
					'selected' => '',
				)
			),
		);
	}

	/**
	 * Array fillter and merge recursive.
	 *
	 * @param   array  $array_fillter
	 *
	 * @param   array  $array_data
	 *
	 * @return  array
	 */
	public static function array_fillter_recursive( $array_fillter, $array_data, $theme_option = array(), $key_el = null, $use_default = false ) {
		if ( $array_data ) {
			foreach( $array_data as $key => $val ) {
				if( array_key_exists( $key, $array_fillter ) == 1 ) {
					if ( $val && is_array( $val ) && is_array( $array_fillter[ $key ] ) ) {
						$array_fillter[ $key ] = self::array_fillter_recursive( $array_fillter[ $key ], $val );
					} elseif ( ! ( ! $val && is_array( $val ) ) ) {
						$array_fillter[ $key ] = $val;
					}
				}
			}
		}

		if( $use_default ) {
			$theme_defalt_color = self::data_color_theme( $key_el, $theme_option );
			$array_fillter = WR_Nitro_Helper::array_replace_recursive( $array_fillter, $theme_defalt_color );
		}

		return $array_fillter;
	}

	/**
	 * Data color theme when use theme default color.
	 *
	 * @param string $key_item.
	 *
	 * @param array $theme_option.
	 *
	 * @return  void
	 */
	public static function data_color_theme( $key_item, $theme_option ) {
		$data = array(
			'search' => array(
				'iconColor' => $theme_option[ 'content_body_color' ][ 'body_text' ],
				'hoverIconColor'=> $theme_option[ 'custom_color' ],
			),
			'menu' => array(
				'background' => array(
					'backgroundColor' => $theme_option[ 'wr_general_container_color' ],
				),
				'backgroundColorMobile' => $theme_option[ 'wr_general_container_color' ],
				'iconColor' => $theme_option[ 'content_body_color' ][ 'body_text' ],
				'iconColorMobile' => $theme_option[ 'content_body_color' ][ 'body_text' ],
				'link' => array(
					'style' => array(
						'backgroundColorHover' => $theme_option[ 'general_overlay_color' ],
						'color' => $theme_option[ 'content_body_color' ][ 'body_text' ],
						'colorHover' => $theme_option[ 'custom_color' ],
						'outlineColorHover' => $theme_option[ 'general_line_color' ],
						'underlineColorHover' => $theme_option[ 'custom_color' ],
					),
				),
				'subMenu'   => array(
					'background' => $theme_option[ 'wr_general_container_color' ],
					'link' => array(
						'style' => array(
							'color' => $theme_option[ 'content_body_color' ][ 'body_text' ],
							'colorHover' => $theme_option[ 'custom_color' ],
						),
					),
				),
			),
			'sidebar' => array(
				'frontCSS' => array(
					'style' => array(
						'backgroundColor' => $theme_option[ 'wr_general_container_color' ],
					),
				),
				'iconColor' => $theme_option[ 'content_body_color' ][ 'body_text' ],
				'hoverIconColor' => $theme_option[ 'custom_color' ],
			),
			'logo' => array(
				'style' => array(
					'color' => $theme_option[ 'content_body_color' ][ 'body_text' ],
				),
			),
			'social' => array(
				'backgroundColor' => $theme_option[ 'general_overlay_color' ],
				'borderColor' => $theme_option[ 'general_line_color' ],
				'iconColor' => $theme_option[ 'content_body_color' ][ 'body_text' ],
			),
			'shopping-cart' => array(
				'colorPrice' => $theme_option[ 'content_body_color' ][ 'body_text' ],
				'colorTitle' => $theme_option[ 'content_body_color' ][ 'body_text' ],
				'styleIcon' =>  array(
					'color' => $theme_option[ 'content_body_color' ][ 'body_text' ],
					'hoverColor' => $theme_option[ 'custom_color' ],
				),
			),
			'wishlist' => array(
				'colorLabel' => $theme_option[ 'content_body_color' ][ 'body_text' ],
				'colorIcon' => $theme_option[ 'content_body_color' ][ 'body_text' ],
				'hoverIconColor' => $theme_option[ 'custom_color' ],
			),
			'currency' => array(),
		);

		$data = isset( $data[ $key_item ] ) ? $data[ $key_item ] : array();

		return $data;
	}

	/**
	 * Save data to database.
	 *
	 * @return  string
	 */
	public static function save_data() {
		// Verify nonce.
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'wr_nitro_nonce_check' ) ) {
			$content = __( 'Nonce verification failed. This might due to your working session has been expired. <a href="javascript:window.location.reload();">Click here to refresh the page to renew your working session</a>.', 'wr-nitro' );
		} else {
			$data = ( ! empty( $_POST['data'] ) ) ? $_POST['data'] : '';
			$data = stripslashes_deep( $data );
			$data = json_decode( $data, true );
			update_option( 'wr_data_export_headerbuilder', $data );

			echo 'true';

			die;
		}

	}

	/**
	 * Save file from database.
	 *
	 * @return  json
	 */
	public static function save_file() {
		// Verify nonce.
		if ( ! isset( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], 'wr_nitro_nonce_check' ) ) {
			exit( __( 'Nonce verification failed. This might due to your working session has been expired. <a href="javascript:window.location.reload();">Click here to refresh the page to renew your working session</a>.', 'wr-nitro' ) );
		}

		$data = get_option( 'wr_data_export_headerbuilder' );
		$data = json_encode( $data );

		// Clear all output buffering
		while ( ob_get_level() ) {
			ob_end_clean();
		}

		$time_now = new DateTime();
		$time_now = $time_now->format( 'd-m-Y-i-h' );
		$name_file = 'woorockets-header' . $time_now . ".json";

		// Send inline download header.
		header( 'Content-Type: application/json; charset=utf-8'           );
		header( 'Content-Length: ' . strlen( $data )                      );
		header( 'Content-Disposition: attachment; filename=' . $name_file );
		header( 'Cache-Control: no-cache, must-revalidate, max-age=60'    );
		header( 'Expires: Sat, 01 Jan 2000 12:00:00 GMT'                  );

		// Print output content.
		echo '' . $data;

		// Exit immediately to prevent WordPress from processing further.
		exit;
	}

}
