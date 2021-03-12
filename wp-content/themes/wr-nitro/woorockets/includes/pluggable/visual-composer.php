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
 * Plug into Visual Composer.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Pluggable_Visual_Composer {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	/**
	 * Initialize pluggable functions for Visual Composer.
	 *
	 * @return  void
	 */
	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}

		// Get list font of shortcode VC in post content
		add_action( 'wp_enqueue_scripts' , array( __CLASS__, 'google_font'  ), 9997 );
		add_action( 'wp_footer'          , array( __CLASS__, 'remove_google_font'  ) );

		// Plug into Visual Composer.
		add_action( 'vc_before_init', array( __CLASS__, 'map_shortcode'   ) );
		add_action( 'vc_after_init' , array( __CLASS__, 'update_elements' ) );

		// Hook to VC auto complete to add custom ajax search
		add_filter( 'vc_autocomplete_nitro_product_id_callback',          array( __CLASS__, 'autocomplete_suggestion', ), 1, 1 );
		add_filter( 'vc_autocomplete_nitro_product_button_id_callback',   array( __CLASS__, 'autocomplete_suggestion', ), 1, 1 );
		add_filter( 'vc_autocomplete_nitro_products_ids_callback',        array( __CLASS__, 'autocomplete_suggestion', ), 1, 1 );
		add_filter( 'vc_autocomplete_nitro_buy_now_id_callback',          array( __CLASS__, 'autocomplete_suggestion', ), 1, 1 );
		add_filter( 'vc_autocomplete_nitro_product_package_ids_callback', array( __CLASS__, 'autocomplete_suggestion', ), 1, 1 );
		add_filter( 'vc_autocomplete_nitro_blog_list_contain_callback',   array( __CLASS__, 'autocomplete_suggestion', ), 1, 1 );
		add_filter( 'vc_autocomplete_nitro_blog_list_exclude_callback',   array( __CLASS__, 'autocomplete_suggestion', ), 1, 1 );
		add_filter( 'vc_autocomplete_nitro_gallery_ids_callback',         array( __CLASS__, 'autocomplete_suggestion', ), 1, 1 );

		// Render
		add_filter( 'vc_autocomplete_nitro_products_ids_render', array( __CLASS__, 'autocomplete_render_value', ), 1, 1 );
		add_filter( 'vc_autocomplete_nitro_product_package_ids_render', array( __CLASS__, 'autocomplete_render_value', ), 1, 1 );
		add_filter( 'vc_autocomplete_nitro_product_id_render', array( __CLASS__, 'autocomplete_render_value', ), 1, 1 );
		add_filter( 'vc_autocomplete_nitro_product_button_id_render', array( __CLASS__, 'autocomplete_render_value', ), 1, 1 );
		add_filter( 'vc_autocomplete_nitro_buy_now_id_render', array( __CLASS__, 'autocomplete_render_value', ), 1, 1 );
		add_filter( 'vc_autocomplete_nitro_blog_list_contain_render', 'vc_include_field_render', 1, 1 );
		add_filter( 'vc_autocomplete_nitro_blog_list_exclude_render', 'vc_exclude_field_render', 1, 1 );
		add_filter( 'vc_autocomplete_nitro_gallery_ids_render', 'vc_exclude_field_render', 1, 1 );

		// State that initialization completed.
		self::$initialized = true;
	}

	/**
	 * Suggester for autocomplete by id/name/title/sku
	 *
	 * @param $query
	 *
	 * @return array - id's from posts with title/ID.
	 */
	public static function autocomplete_suggestion ( $query ) {
		$current_filter = current_filter();

		switch ( $current_filter ) {
			case 'vc_autocomplete_nitro_product_id_callback':

			case 'vc_autocomplete_nitro_product_button_id_callback':

			case 'vc_autocomplete_nitro_buy_now_id_callback':

				$suggestions = apply_filters( 'vc_autocomplete_product_id_callback', $query );

				break;

			case 'vc_autocomplete_nitro_products_ids_callback':

			case 'vc_autocomplete_nitro_product_package_ids_callback':

				$suggestions = apply_filters( 'vc_autocomplete_products_ids_callback', $query );

				break;

			case 'vc_autocomplete_nitro_blog_list_contain_callback':

			case 'vc_autocomplete_nitro_blog_list_exclude_callback':

				$query = array(
					'query' => 'post',
					'term'  => $query
				);

				$suggestions = apply_filters( 'vc_autocomplete_vc_basic_grid_exclude_callback', $query );

				break;

			case 'vc_autocomplete_nitro_gallery_ids_callback':
				$query = array(
					'query' => 'nitro-gallery',
					'term'  => $query
				);

				$suggestions = apply_filters( 'vc_autocomplete_vc_basic_grid_exclude_callback', $query );

				break;
		}

		if ( is_array( $suggestions ) && ! empty( $suggestions ) ) {
			die( json_encode( $suggestions ) );
		}

		die( '' ); // if nothing found..

	}


	/**
	 * Suggester for autocomplete render value
	 *
	 * @param $value
	 *
	 * @return array
	 */
	public static function autocomplete_render_value ( $value ) {
		$current_filter = current_filter();

		switch ( $current_filter ) {
			case 'vc_autocomplete_nitro_product_id_render':

			case 'vc_autocomplete_nitro_product_button_id_render':

			case 'vc_autocomplete_nitro_buy_now_id_render':

			case 'vc_autocomplete_nitro_products_ids_render':

			case 'vc_autocomplete_nitro_product_package_ids_render':

				$value = apply_filters( 'vc_autocomplete_products_ids_render', $value );

				break;

			case 'vc_autocomplete_nitro_blog_list_contain_render':

			case 'vc_autocomplete_nitro_blog_list_exclude_render':

			case 'vc_autocomplete_nitro_gallery_ids_callback':

				// Nothing

				break;
		}

		return $value;
	}

	/**
	 * Customize Visual Composer elements.
	 *
	 * @since  1.0
	 * @see    https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524329
	 */
	public static function update_elements() {
		// Update elements.
		vc_map_update( 'vc_row'               , array( 'icon' => 'fa fa-bars'               ) );
		vc_map_update( 'vc_column_text'       , array( 'icon' => 'fa fa-file-text-o'        ) );
		vc_map_update( 'vc_icon'              , array( 'icon' => 'fa fa-smile-o'            ) );
		vc_map_update( 'vc_message'           , array( 'icon' => 'fa fa-newspaper-o'        ) );
		vc_map_update( 'vc_facebook'          , array( 'icon' => 'fa fa-facebook-square'    ) );
		vc_map_update( 'vc_tweetmeme'         , array( 'icon' => 'fa fa-twitter'            ) );
		vc_map_update( 'vc_googleplus'        , array( 'icon' => 'fa fa-google-plus-square' ) );
		vc_map_update( 'vc_pinterest'         , array( 'icon' => 'fa fa-pinterest-square'   ) );
		vc_map_update( 'vc_toggle'            , array( 'icon' => 'fa fa-question-circle'    ) );
		vc_map_update( 'vc_single_image'      , array( 'icon' => 'fa fa-picture-o'          ) );
		vc_map_update( 'vc_gallery'           , array( 'icon' => 'fa fa-picture-o'          ) );
		vc_map_update( 'vc_images_carousel'   , array( 'icon' => 'fa fa-picture-o'          ) );
		vc_map_update( 'vc_tta_tabs'          , array( 'icon' => 'fa fa-list-alt'           ) );
		vc_map_update( 'vc_tta_tour'          , array( 'icon' => 'fa fa-list-alt'           ) );
		vc_map_update( 'vc_tta_accordion'     , array( 'icon' => 'fa fa-align-justify'      ) );
		vc_map_update( 'vc_tta_pageable'      , array( 'icon' => 'fa fa-sort-amount-desc'   ) );
		vc_map_update( 'vc_posts_slider'      , array( 'icon' => 'fa fa-sliders'            ) );
		vc_map_update( 'vc_widget_sidebar'    , array( 'icon' => 'fa fa-cubes'              ) );
		vc_map_update( 'vc_raw_html'          , array( 'icon' => 'fa fa-html5'              ) );
		vc_map_update( 'vc_raw_js'            , array( 'icon' => 'fa fa-code'               ) );
		vc_map_update( 'vc_flickr'            , array( 'icon' => 'fa fa-flickr'             ) );
		vc_map_update( 'vc_progress_bar'      , array( 'icon' => 'fa fa-tasks'              ) );
		vc_map_update( 'vc_pie'               , array( 'icon' => 'fa fa-pie-chart'          ) );
		vc_map_update( 'vc_round_chart'       , array( 'icon' => 'fa fa-bar-chart'          ) );
		vc_map_update( 'vc_line_chart'        , array( 'icon' => 'fa fa-line-chart'         ) );
		vc_map_update( 'vc_empty_space'       , array( 'icon' => 'fa fa-square-o'           ) );
		vc_map_update( 'vc_custom_heading'    , array( 'icon' => 'fa fa-header'             ) );
		vc_map_update( 'vc_btn'               , array( 'icon' => 'fa fa-stop'               ) );
		vc_map_update( 'vc_cta'               , array( 'icon' => 'fa fa-credit-card'        ) );
		vc_map_update( 'vc_basic_grid'        , array( 'icon' => 'fa fa-th'                 ) );
		vc_map_update( 'vc_media_grid'        , array( 'icon' => 'fa fa-th-large'           ) );
		vc_map_update( 'vc_masonry_grid'      , array( 'icon' => 'fa fa-th-list'            ) );
		vc_map_update( 'vc_masonry_media_grid', array( 'icon' => 'fa fa-th-list'            ) );
		vc_map_update( 'vc_wp_search'         , array( 'icon' => 'fa fa-wordpress'          ) );
		vc_map_update( 'vc_wp_meta'           , array( 'icon' => 'fa fa-wordpress'          ) );
		vc_map_update( 'vc_wp_recentcomments' , array( 'icon' => 'fa fa-wordpress'          ) );
		vc_map_update( 'vc_wp_calendar'       , array( 'icon' => 'fa fa-wordpress'          ) );
		vc_map_update( 'vc_wp_pages'          , array( 'icon' => 'fa fa-wordpress'          ) );
		vc_map_update( 'vc_wp_tagcloud'       , array( 'icon' => 'fa fa-wordpress'          ) );
		vc_map_update( 'vc_wp_custommenu'     , array( 'icon' => 'fa fa-wordpress'          ) );
		vc_map_update( 'vc_wp_text'           , array( 'icon' => 'fa fa-wordpress'          ) );
		vc_map_update( 'vc_wp_posts'          , array( 'icon' => 'fa fa-wordpress'          ) );
		vc_map_update( 'vc_wp_categories'     , array( 'icon' => 'fa fa-wordpress'          ) );
		vc_map_update( 'vc_wp_archives'       , array( 'icon' => 'fa fa-wordpress'          ) );
		vc_map_update( 'vc_wp_rss'            , array( 'icon' => 'fa fa-wordpress'          ) );
		vc_map_update( 'vc_zigzag'            , array( 'icon' => 'fa fa-gg'                 ) );
		vc_map_update( 'vc_hoverbox'          , array( 'icon' => 'fa fa-magic'              ) );

		if ( function_exists( 'wpcf7' ) ) {
			vc_map_update( 'contact-form-7', array( 'icon' => 'fa fa-envelope-o' ) );
		}

		// Remove elements.
		vc_remove_element( 'vc_gmaps'          );
		vc_remove_element( 'vc_text_separator' );
		vc_remove_element( 'vc_separator'      );
		vc_remove_element( 'vc_tabs'           );
		vc_remove_element( 'vc_tour'           );
		vc_remove_element( 'vc_accordion'      );
		vc_remove_element( 'vc_button'         );
		vc_remove_element( 'vc_button2'        );
		vc_remove_element( 'vc_cta_button'     );
		vc_remove_element( 'vc_cta_button2'    );
		vc_remove_element( 'vc_video'          );

		vc_remove_element( 'product_category'       );
		vc_remove_element( 'product_categories'     );
		vc_remove_element( 'add_to_cart'            );
		// vc_remove_element( 'woocommerce_cart'       );
		// vc_remove_element( 'woocommerce_checkout'   );
		// vc_remove_element( 'woocommerce_my_account' );
		vc_remove_element( 'add_to_cart_url'        );
		vc_remove_element( 'product_page'        	);
		vc_remove_element( 'product'        	    );
		vc_remove_element( 'products'        	    );
		vc_remove_element( 'recent_products'        );
		vc_remove_element( 'featured_products'      );
		vc_remove_element( 'related_products'       );
		vc_remove_element( 'sale_products'        	);
		vc_remove_element( 'best_selling_products'  );
		vc_remove_element( 'top_rated_products'     );
		vc_remove_element( 'product_attribute'      );

		vc_map_update( 'woocommerce_order_tracking' , array( 'icon' => 'fa fa-shopping-cart' ) );

		// Remove parameters.
		vc_remove_param( 'vc_tta_tabs', 'shape' );
		vc_remove_param( 'vc_tta_tabs', 'color' );

		// Disable frontend editing mode
		vc_disable_frontend();
	}

	/**
	 * Map new parameters and elements.
	 *
	 * @since  1.0
	 * @see    https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332
	 */
	public static function map_shortcode() {
		/**
		 * Force Visual Composer to initialize as "built into the theme",
		 * this will hide certain tabs under the Settings -> Visual Composer page.
		 */
		vc_set_as_theme();

		/**
		 * To enable Visual Composer page builder for theme defined post types by default.
		 */
		vc_set_default_editor_post_types( array( 'page', 'product' ) );

		// Get the list of Google fonts.
		$fonts_origin = WR_Nitro_Helper::google_fonts();

		$fonts = array();

		$fonts[ __( 'Use theme default font family', 'wr-nitro' ) ] = '';

		// Set key equal value for Google fonts.
		foreach( $fonts_origin as $key => $val ) {
			$fonts[ $key ] = $key;
		}

		// Define font weights.
		$weights = array(
			'1'  => '100',
			'2'  => '100i',
			'3'  => '200',
			'4'  => '200i',
			'5'  => '300',
			'6'  => '300i',
			'7'  => '400',
			'8'  => '400i',
			'9'  => '500',
			'10' => '500i',
			'11' => '600',
			'12' => '600i',
			'13' => '700',
			'14' => '700i',
			'15' => '800',
			'16' => '800i',
			'17' => '900',
			'18' => '900i',
		);

		$order_by_values = array(
			'',
			esc_html__( 'Date', 'wr-nitro' )          => 'date',
			esc_html__( 'ID', 'wr-nitro' )            => 'ID',
			esc_html__( 'Author', 'wr-nitro' )        => 'author',
			esc_html__( 'Title', 'wr-nitro' )         => 'title',
			esc_html__( 'Modified', 'wr-nitro' )      => 'modified',
			esc_html__( 'Random', 'wr-nitro' )        => 'rand',
			esc_html__( 'Comment count', 'wr-nitro' ) => 'comment_count',
			esc_html__( 'Menu order', 'wr-nitro' )    => 'menu_order',
		);
		$order_way_values = array(
			'',
			esc_html__( 'Descending', 'wr-nitro' ) => 'DESC',
			esc_html__( 'Ascending', 'wr-nitro' )  => 'ASC',
		);

		// Update parameters for Row.
		vc_add_params(
			'vc_row',
			array(
				array(
					'param_name'       => 'background_position',
					'heading'          => esc_html__( 'Background Position', 'wr-nitro' ),
					'group'            => esc_html__( 'Design Options', 'wr-nitro' ),
					'type'             => 'dropdown',
					'edit_field_class' => 'vc_col-xs-6 mgt60m',
					'value'      => array(
						esc_html__( 'Left Top', 'wr-nitro' )      => 'left_top',
						esc_html__( 'Left Center', 'wr-nitro' )   => 'left center',
						esc_html__( 'Left Bottom', 'wr-nitro' )   => 'left bottom',
						esc_html__( 'Right Top', 'wr-nitro' )     => 'right top',
						esc_html__( 'Right Center', 'wr-nitro' )  => 'right center',
						esc_html__( 'Right Bottom', 'wr-nitro' )  => 'right bottom',
						esc_html__( 'Center Top', 'wr-nitro' )    => 'center top',
						esc_html__( 'Center Center', 'wr-nitro' ) => 'center center',
						esc_html__( 'Center Bottom', 'wr-nitro' ) => 'center bottom',
					),
				)
			)
		);
		vc_add_params(
			'vc_row_inner',
			array(
				array(
					'param_name'       => 'background_position',
					'heading'          => esc_html__( 'Background Position', 'wr-nitro' ),
					'group'            => esc_html__( 'Design Options', 'wr-nitro' ),
					'type'             => 'dropdown',
					'edit_field_class' => 'vc_col-xs-6 mgt60m',
					'value'      => array(
						esc_html__( 'Left Top', 'wr-nitro' )      => 'left_top',
						esc_html__( 'Left Center', 'wr-nitro' )   => 'left center',
						esc_html__( 'Left Bottom', 'wr-nitro' )   => 'left bottom',
						esc_html__( 'Right Top', 'wr-nitro' )     => 'right top',
						esc_html__( 'Right Center', 'wr-nitro' )  => 'right center',
						esc_html__( 'Right Bottom', 'wr-nitro' )  => 'right bottom',
						esc_html__( 'Center Top', 'wr-nitro' )    => 'center top',
						esc_html__( 'Center Center', 'wr-nitro' ) => 'center center',
						esc_html__( 'Center Bottom', 'wr-nitro' ) => 'center bottom',
					),
				),
			)
		);

		// Update parameters for Column.
		vc_add_params(
			'vc_column',
			array(
				array(
					'param_name'       => 'background_position',
					'heading'          => esc_html__( 'Background Position', 'wr-nitro' ),
					'group'            => esc_html__( 'Design Options', 'wr-nitro' ),
					'type'             => 'dropdown',
					'edit_field_class' => 'vc_col-xs-6 mgt60m',
					'value'      => array(
						esc_html__( 'Left Top', 'wr-nitro' )      => 'left_top',
						esc_html__( 'Left Center', 'wr-nitro' )   => 'left center',
						esc_html__( 'Left Bottom', 'wr-nitro' )   => 'left bottom',
						esc_html__( 'Right Top', 'wr-nitro' )     => 'right top',
						esc_html__( 'Right Center', 'wr-nitro' )  => 'right center',
						esc_html__( 'Right Bottom', 'wr-nitro' )  => 'right bottom',
						esc_html__( 'Center Top', 'wr-nitro' )    => 'center top',
						esc_html__( 'Center Center', 'wr-nitro' ) => 'center center',
						esc_html__( 'Center Bottom', 'wr-nitro' ) => 'center bottom',
					),
				),
			)
		);
		vc_add_params(
			'vc_column_inner',
			array(
				array(
					'param_name'       => 'background_position',
					'heading'          => esc_html__( 'Background Position', 'wr-nitro' ),
					'group'            => esc_html__( 'Design Options', 'wr-nitro' ),
					'type'             => 'dropdown',
					'edit_field_class' => 'vc_col-xs-6 mgt60m',
					'value'      => array(
						esc_html__( 'Left Top', 'wr-nitro' )      => 'left_top',
						esc_html__( 'Left Center', 'wr-nitro' )   => 'left center',
						esc_html__( 'Left Bottom', 'wr-nitro' )   => 'left bottom',
						esc_html__( 'Right Top', 'wr-nitro' )     => 'right top',
						esc_html__( 'Right Center', 'wr-nitro' )  => 'right center',
						esc_html__( 'Right Bottom', 'wr-nitro' )  => 'right bottom',
						esc_html__( 'Center Top', 'wr-nitro' )    => 'center top',
						esc_html__( 'Center Center', 'wr-nitro' ) => 'center center',
						esc_html__( 'Center Bottom', 'wr-nitro' ) => 'center bottom',
					),
				),
			)
		);

		// Update parameters for Tabs element.
		vc_add_params(
			'vc_tta_tabs',
			array(
				array(
					'param_name' => 'style',
					'heading'    => esc_html__( 'Select style', 'wr-nitro' ),
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'Style 1', 'wr-nitro' ) => 'style-1',
						esc_html__( 'Style 2', 'wr-nitro' ) => 'style-2',
						esc_html__( 'Style 3', 'wr-nitro' ) => 'style-3',
						esc_html__( 'Style 4', 'wr-nitro' ) => 'style-4',
						esc_html__( 'Style 5', 'wr-nitro' ) => 'style-5',
						esc_html__( 'Style 6', 'wr-nitro' ) => 'style-6',
						esc_html__( 'Style 7', 'wr-nitro' ) => 'style-7',
					),
				),
			)
		);

		// Update parameters for Toggle element.
		vc_add_params(
			'vc_toggle',
			array(
				array(
					'param_name' => 'style',
					'heading'    => esc_html__( 'Select style', 'wr-nitro' ),
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'Style 1', 'wr-nitro' ) => 'style-1',
						esc_html__( 'Style 2', 'wr-nitro' ) => 'style-2',
						esc_html__( 'Style 3', 'wr-nitro' ) => 'style-3',
						esc_html__( 'Style 4', 'wr-nitro' ) => 'style-4',
					),
				),
			)
		);

		// Update parameters for Accordion element.
		vc_add_params(
			'vc_accordion',
			array(
				array(
					'param_name' => 'style',
					'heading'    => esc_html__( 'Select style', 'wr-nitro' ),
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'Style 1', 'wr-nitro' ) => 'style-1',
						esc_html__( 'Style 2', 'wr-nitro' ) => 'style-2',
						esc_html__( 'Style 3', 'wr-nitro' ) => 'style-3',
						esc_html__( 'Style 4', 'wr-nitro' ) => 'style-4',
					),
				),
			)
		);

		// Update parameters for Progress Bar element.
		vc_add_params(
			'vc_progress_bar',
			array(
				array(
					'param_name' => 'style',
					'heading'    => esc_html__( 'Select style', 'wr-nitro' ),
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'Default', 'wr-nitro' ) => '',
						esc_html__( 'Style 1', 'wr-nitro' ) => 'style-1',
					),
				),
			)
		);

		// Map new Heading element.
		vc_map(
			array(
				'name'     => esc_html__( 'Nitro Custom Heading', 'wr-nitro' ),
				'base'     => 'nitro_heading',
				'icon'     => 'fa fa-header',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name'       => 'align',
						'heading'          => esc_html__( 'Heading Alignment', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-6 mgt15',
						'group'            => esc_html__( 'General', 'wr-nitro' ),
						'value'            => array(
							__( 'Left', 'wr-nitro' )   => 'left',
							__( 'Center', 'wr-nitro' ) => 'center',
							__( 'Right', 'wr-nitro' )  => 'right',
						),
					),
					array(
						'param_name'       => 'tag',
						'heading'          => esc_html__( 'Heading Tags', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-6',
						'group'            => esc_html__( 'General', 'wr-nitro' ),
						'value'            => array(
							__( 'H1', 'wr-nitro' ) => 'h1',
							__( 'H2', 'wr-nitro' ) => 'h2',
							__( 'H3', 'wr-nitro' ) => 'h3',
							__( 'H4', 'wr-nitro' ) => 'h4',
							__( 'H5', 'wr-nitro' ) => 'h5',
							__( 'H6', 'wr-nitro' ) => 'h6',
						),
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
						'group'       => esc_html__( 'General', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'heading_custom_id',
						'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'group'            => esc_html__( 'General', 'wr-nitro' ),
						'edit_field_class' => 'hidden',
					),
					array(
						'param_name' => 'sub_1',
						'heading'    => esc_html__( 'Sub heading settings', 'wr-nitro' ),
						'type'       => 'sub_heading',
						'group'      => esc_html__( 'Typography', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'sub_text',
						'heading'          => esc_html__( 'Text', 'wr-nitro' ),
						'value'            => esc_html__( 'This is custom heading element', 'wr-nitro' ),
						'type'             => 'textarea',
						'edit_field_class' => 'vc_col-sm-12 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'sub_font_family',
						'heading'          => esc_html__( 'Font Family', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => $fonts,
					),
					array(
						'param_name'       => 'sub_font_weight',
						'heading'          => esc_html__( 'Font Weight', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => $weights,
						'std' 			   => '400',
						'dependency' => array(
							'element'   => 'sub_font_family',
							'not_empty' => true
						),
					),
					array(
						'param_name'       => 'sub_text_transform',
						'heading'          => esc_html__( 'Text Transform', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => array(
							__( 'None', 'wr-nitro' )       => 'none',
							__( 'Uppercase', 'wr-nitro' )  => 'uppercase',
							__( 'Capitalize', 'wr-nitro' ) => 'capitalize',
						),
					),
					array(
						'param_name'       => 'sub_color',
						'heading'          => esc_html__( 'Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'dependency' => array(
							'element'   => 'sub_font_family',
							'not_empty' => true
						),
					),
					array(
						'param_name'       => 'sub_font_size',
						'heading'          => esc_html__( 'Font Size (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => 20,
					),
					array(
						'param_name'       => 'sub_line_height',
						'heading'          => esc_html__( 'Line Height', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => '20px',
					),
					array(
						'param_name'       => 'sub_spacing',
						'heading'          => esc_html__( 'Letter Spacing (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => 0,
					),
					array(
						'param_name' => 'sub_2',
						'heading'    => esc_html__( 'Heading settings', 'wr-nitro' ),
						'type'       => 'sub_heading',
						'group'      => esc_html__( 'Typography', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'text',
						'heading'          => esc_html__( 'Text', 'wr-nitro' ),
						'value'            => esc_html__( 'This is custom heading element', 'wr-nitro' ),
						'type'             => 'textarea',
						'edit_field_class' => 'vc_col-sm-12 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'holder'           => 'h2'
					),
					array(
						'param_name'       => 'font_family',
						'heading'          => esc_html__( 'Font Family', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => $fonts,
					),
					array(
						'param_name'       => 'font_weight',
						'heading'          => esc_html__( 'Font Weight', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => $weights,
						'std' 			   => '400',
						'dependency' => array(
							'element'   => 'font_family',
							'not_empty' => true
						),
					),
					array(
						'param_name'       => 'text_transform',
						'heading'          => esc_html__( 'Text Transform', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => array(
							__( 'None', 'wr-nitro' )       => 'none',
							__( 'Uppercase', 'wr-nitro' )  => 'uppercase',
							__( 'Capitalize', 'wr-nitro' ) => 'capitalize',
						),
					),
					array(
						'param_name'       => 'color',
						'heading'          => esc_html__( 'Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'dependency' => array(
							'element'   => 'font_family',
							'not_empty' => true
						),
					),
					array(
						'param_name'       => 'font_size',
						'heading'          => esc_html__( 'Font Size (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => 44,
					),
					array(
						'param_name'       => 'line_height',
						'heading'          => esc_html__( 'Line Height', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => '44px',
					),
					array(
						'param_name'       => 'spacing',
						'heading'          => esc_html__( 'Letter Spacing (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => 0,
					),
					array(
						'param_name'       => 'margin_top',
						'heading'          => esc_html__( 'Margin Top (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => 10,
					),
					array(
						'param_name'       => 'margin_bottom',
						'heading'          => esc_html__( 'Margin Bottom (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => 20,
					),
					array(
						'param_name' => 'link_to',
						'heading'    => esc_html__( 'Link To', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Typography', 'wr-nitro' ),
					),
					array(
						'param_name' => 'sub_3',
						'heading'    => esc_html__( 'Description settings', 'wr-nitro' ),
						'type'       => 'sub_heading',
						'group'      => esc_html__( 'Typography', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'desc_text',
						'heading'          => esc_html__( 'Text', 'wr-nitro' ),
						'value'            => esc_html__( 'This is custom heading element', 'wr-nitro' ),
						'type'             => 'textarea',
						'edit_field_class' => 'vc_col-sm-12 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'desc_font_family',
						'heading'          => esc_html__( 'Font Family', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => $fonts,
					),
					array(
						'param_name'       => 'desc_font_weight',
						'heading'          => esc_html__( 'Font Weight', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => $weights,
						'std' 			   => '400',
						'dependency' => array(
							'element'   => 'desc_font_family',
							'not_empty' => true
						),
					),
					array(
						'param_name'       => 'desc_text_transform',
						'heading'          => esc_html__( 'Text Transform', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => array(
							__( 'None', 'wr-nitro' )       => 'none',
							__( 'Uppercase', 'wr-nitro' )  => 'uppercase',
							__( 'Capitalize', 'wr-nitro' ) => 'capitalize',
						),
					),
					array(
						'param_name'       => 'desc_color',
						'heading'          => esc_html__( 'Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'dependency' => array(
							'element'   => 'desc_font_family',
							'not_empty' => true
						),
					),
					array(
						'param_name'       => 'desc_font_size',
						'heading'          => esc_html__( 'Font Size (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => 10,
					),
					array(
						'param_name'       => 'desc_line_height',
						'heading'          => esc_html__( 'Line Height', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => '15px',
					),
					array(
						'param_name'       => 'desc_spacing',
						'heading'          => esc_html__( 'Letter Spacing (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Typography', 'wr-nitro' ),
						'value'            => 3,
					),
					array(
						'param_name'       => 'separator',
						'heading'          => esc_html__( 'Select Style', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-12 vc_column',
						'group'            => esc_html__( 'Separator', 'wr-nitro' ),
						'value'            => array(
							__( 'None', 'wr-nitro' )       => 'none',
							__( 'Short Line', 'wr-nitro' ) => 'line',
							__( 'Icon', 'wr-nitro' )       => 'icon',
							__( 'Image', 'wr-nitro' )      => 'image',
						),
					),
					array(
						'param_name'       => 'separator_mgt',
						'heading'          => esc_html__( 'Margin Top (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
						'group'            => esc_html__( 'Separator', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'separator_mgb',
						'heading'          => esc_html__( 'Margin Bottom (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
						'group'            => esc_html__( 'Separator', 'wr-nitro' ),
					),
					array(
						'param_name' => 'separator_width',
						'heading'    => esc_html__( 'Width (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => 48,
						'dependency' => array(
							'element' => 'separator',
							'value'   => 'line',
						),
					),
					array(
						'param_name' => 'separator_height',
						'heading'    => esc_html__( 'Height (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => 1,
						'dependency' => array(
							'element' => 'separator',
							'value'   => 'line',
						),
					),
					array(
						'param_name' => 'separator_position',
						'heading'    => esc_html__( 'Position', 'wr-nitro' ),
						'type'       => 'dropdown',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => array(
							__( 'Top', 'wr-nitro' )    => 'top',
							__( 'Bottom', 'wr-nitro' ) => 'bottom',
						),
						'dependency' => array(
							'element' => 'separator',
							'value'   => 'line',
						),
					),
					array(
						'param_name' => 'separator_color',
						'heading'    => esc_html__( 'Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => '#8e8e8e',
						'dependency' => array(
							'element' => 'separator',
							'value'   => 'line',
						),
					),
					array(
						'param_name'  => 'icon_type',
						'heading'     => esc_html__( 'Icon Library', 'wr-nitro' ),
						'type'        => 'dropdown',
						'group'       => esc_html__( 'Separator', 'wr-nitro' ),
						'value'       => array(
							__( 'Font Awesome', 'wr-nitro' ) => 'fontawesome',
							__( 'Open Iconic', 'wr-nitro' )  => 'openiconic',
							__( 'Typicons', 'wr-nitro' )     => 'typicons',
							__( 'Entypo', 'wr-nitro' )       => 'entypo',
							__( 'Linecons', 'wr-nitro' )     => 'linecons',
						),
						'dependency'  => array(
							'element' => 'separator',
							'value'   => 'icon',
						),
					),
					array(
						'param_name'  => 'icon_fontawesome',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'group'       => esc_html__( 'Separator', 'wr-nitro' ),
						'value'       => 'fa fa-adjust',
						'settings'    => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'fontawesome',
						),
					),
					array(
						'param_name'  => 'icon_openiconic',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'group'       => esc_html__( 'Separator', 'wr-nitro' ),
						'value'       => 'vc-oi vc-oi-dial',
						'settings'    => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'openiconic',
						),
					),
					array(
						'param_name'  => 'icon_typicons',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'group'       => esc_html__( 'Separator', 'wr-nitro' ),
						'value'       => 'typcn typcn-adjust-brightness',
						'settings'    => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'typicons',
						),
					),
					array(
						'param_name' => 'icon_entypo',
						'heading'    => esc_html__( 'Icon', 'wr-nitro' ),
						'type'       => 'iconpicker',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => 'entypo-icon entypo-icon-note',
						'settings'   => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value'   => 'entypo',
						),
					),
					array(
						'param_name'  => 'icon_linecons',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'group'       => esc_html__( 'Separator', 'wr-nitro' ),
						'value'       => 'vc_li vc_li-heart',
						'settings'    => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'linecons',
						),
					),
					array(
						'param_name' => 'icon_color',
						'heading'    => esc_html__( 'Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => '#646464',
						'dependency' => array(
							'element' => 'separator',
							'value'   => 'icon',
						),
					),
					array(
						'param_name' => 'icon_position',
						'heading'    => esc_html__( 'Position', 'wr-nitro' ),
						'type'       => 'dropdown',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => array(
							__( 'Top', 'wr-nitro' )    => 'top',
							__( 'Bottom', 'wr-nitro' ) => 'bottom',
						),
						'dependency' => array(
							'element' => 'separator',
							'value'   => 'icon',
						),
					),
					array(
						'param_name' => 'icon_size',
						'heading'    => esc_html__( 'Icon Size (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => 14,
						'dependency' => array(
							'element' => 'separator',
							'value'   => 'icon',
						),
					),
					array(
						'param_name' => 'icon_line',
						'heading'    => esc_html__( 'Enable line?', 'wr-nitro' ),
						'type'       => 'toggle',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => true,
						'dependency' => array(
							'element' => 'separator',
							'value'   => 'icon',
						),
					),
					array(
						'param_name' => 'graphic',
						'heading'    => esc_html__( 'Upload image', 'wr-nitro' ),
						'type'       => 'attach_image',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'separator',
							'value'   => 'image',
						),
					),
					array(
						'param_name' => 'image_position',
						'heading'    => esc_html__( 'Position', 'wr-nitro' ),
						'type'       => 'dropdown',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => array(
							__( 'Top', 'wr-nitro' )    => 'top',
							__( 'Bottom', 'wr-nitro' ) => 'bottom',
						),
						'dependency' => array(
							'element' => 'separator',
							'value'   => 'image',
						),
					),
					array(
						'param_name' => 'image_radius',
						'heading'    => esc_html__( 'Border radius (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => 100,
						'dependency' => array(
							'element' => 'separator',
							'value'   => 'image',
						),
					),
					array(
						'param_name' => 'image_line',
						'heading'    => esc_html__( 'Enable line?', 'wr-nitro' ),
						'type'       => 'toggle',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => true,
						'dependency' => array(
							'element' => 'separator',
							'value'   => 'image',
						),
					),
				)
			)
		);

		// Map new Separator element.
		vc_map(
			array(
				'name'     => esc_html__( 'Separator', 'wr-nitro' ),
				'base'     => 'nitro_separator',
				'icon'     => 'fa fa-minus',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name' => 'style',
						'heading'    => esc_html__( 'Separator style', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Solid', 'wr-nitro' )   => 'solid',
							__( 'Double', 'wr-nitro' )  => 'double',
							__( 'Dashed', 'wr-nitro' )  => 'dashed',
							__( 'Pattern', 'wr-nitro' ) => 'pattern',
						),
					),
					array(
						'param_name' => 'align',
						'heading'    => esc_html__( 'Separator Alignment', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Left', 'wr-nitro' )   => 'left',
							__( 'Center', 'wr-nitro' ) => 'center',
							__( 'Right', 'wr-nitro' )  => 'right',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'width',
						'heading'    => esc_html__( 'Separator width (Example: 50px or 50%)', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => '100%',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'height',
						'heading'    => esc_html__( 'Separator Height (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'dependency' => array(
							'element' => 'style',
							'value'   => 'solid',
						),
					),
					array(
						'param_name' => 'color',
						'heading'    => esc_html__( 'Separator Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#ebebeb',
					),
					array(
						'param_name' => 'symbol',
						'heading'    => esc_html__( 'Separator with symbol', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Icon', 'wr-nitro' )  => 'icon',
							__( 'Image', 'wr-nitro' ) => 'image',
							__( 'Text', 'wr-nitro' )  => 'text',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name'  => 'icon_type',
						'heading'     => esc_html__( 'Icon Library', 'wr-nitro' ),
						'type'        => 'dropdown',
						'value'       => array(
							__( 'Font Awesome', 'wr-nitro' ) => 'fontawesome',
							__( 'Open Iconic', 'wr-nitro' )  => 'openiconic',
							__( 'Typicons', 'wr-nitro' )     => 'typicons',
							__( 'Entypo', 'wr-nitro' )       => 'entypo',
							__( 'Linecons', 'wr-nitro' )     => 'linecons',
						),
						'dependency'  => array(
							'element' => 'symbol',
							'value'   => 'icon',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name'  => 'icon_fontawesome',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'settings'    => array(
							'emptyIcon'    => true,
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'fontawesome',
						),
					),
					array(
						'param_name'  => 'icon_openiconic',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'settings'    => array(
							'emptyIcon'    => true,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'openiconic',
						),
					),
					array(
						'param_name'  => 'icon_typicons',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'settings'    => array(
							'emptyIcon'    => true,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'typicons',
						),
					),
					array(
						'param_name' => 'icon_entypo',
						'heading'    => esc_html__( 'Icon', 'wr-nitro' ),
						'type'       => 'iconpicker',
						'settings'   => array(
							'emptyIcon'    => true,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value'   => 'entypo',
						),
					),
					array(
						'param_name'  => 'icon_linecons',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'settings'    => array(
							'emptyIcon'    => true,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'linecons',
						),
					),
					array(
						'param_name' => 'icon_color',
						'heading'    => esc_html__( 'Icon Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#646464',
						'dependency' => array(
							'element' => 'symbol',
							'value'   => 'icon',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'icon_size',
						'heading'    => esc_html__( 'Icon Size (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => '14',
						'dependency' => array(
							'element' => 'symbol',
							'value'   => 'icon',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'graphic',
						'heading'    => esc_html__( 'Upload image', 'wr-nitro' ),
						'type'       => 'attach_image',
						'dependency' => array(
							'element' => 'symbol',
							'value'   => 'image',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'image_radius',
						'heading'    => esc_html__( 'Border radius (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => '100',
						'dependency' => array(
							'element' => 'symbol',
							'value'   => 'image',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'text_editor',
						'heading'    => esc_html__( 'Content', 'wr-nitro' ),
						'type'       => 'textarea',
						'dependency' => array(
							'element' => 'symbol',
							'value'   => 'text',
						),
					),
					array(
						'param_name' => 'text_transform',
						'heading'    => esc_html__( 'Text Transform', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'None', 'wr-nitro' )       => 'none',
							__( 'Uppercase', 'wr-nitro' )  => 'uppercase',
							__( 'Capitalize', 'wr-nitro' ) => 'capitalize',
						),
						'dependency' => array(
							'element' => 'symbol',
							'value'   => 'text',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'spacing',
						'heading'    => esc_html__( 'Letter Spacing (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => 0,
						'dependency' => array(
							'element' => 'symbol',
							'value'   => 'text',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'font_family',
						'heading'    => esc_html__( 'Font Family', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => $fonts,
						'dependency' => array(
							'element' => 'symbol',
							'value'   => 'text',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'font_weight',
						'heading'    => esc_html__( 'Font Weight', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => $weights,
						'dependency' => array(
							'element' => 'symbol',
							'value'   => 'text',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'font_size',
						'heading'    => esc_html__( 'Font Size (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => 16,
						'dependency' => array(
							'element' => 'symbol',
							'value'   => 'text',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'text_color',
						'heading'    => esc_html__( 'Text Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#646464',
						'dependency' => array(
							'element' => 'symbol',
							'value'   => 'text',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'margin_top',
						'heading'    => esc_html__( 'Margin Top (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'margin_bottom',
						'heading'    => esc_html__( 'Margin Bottom (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
						'param_name'  => 'el_class',
					),
					array(
						'param_name'       => 'separator_custom_id',
						'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new Service Box element.
		vc_map(
			array(
				'name'     => esc_html__( 'Icon Box', 'wr-nitro' ),
				'base'     => 'nitro_services',
				'icon'     => 'fa fa-credit-card',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name'       => 'style',
						'heading'          => esc_html__( 'Style', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-6 mgt15',
						'group'            => esc_html__( 'General', 'wr-nitro' ),
						'value'            => array(
							__( 'Character', 'wr-nitro' ) => 'character',
							__( 'Icon', 'wr-nitro' )      => 'icon',
							__( 'Image', 'wr-nitro' )     => 'image',
						),
					),
					array(
						'param_name'       => 'align',
						'heading'          => esc_html__( 'Box Alignment', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-6',
						'group'            => esc_html__( 'General', 'wr-nitro' ),
						'value'            => array(
							__( 'Left', 'wr-nitro' )   => 'left',
							__( 'Center', 'wr-nitro' ) => 'center',
							__( 'Right', 'wr-nitro' )  => 'right',
						),
					),
					array(
						'param_name' => 'content_box',
						'heading'    => esc_html__( 'Content', 'wr-nitro' ),
						'type'       => 'textarea',
						'group'      => esc_html__( 'General', 'wr-nitro' ),
						'holder'     => 'p'
					),
					array(
						'param_name' => 'text_color',
						'heading'    => esc_html__( 'Text Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'group'      => esc_html__( 'General', 'wr-nitro' ),
					),
					array(
						'param_name' => 'read_more',
						'heading'    => esc_html__( 'Enable Read More?', 'wr-nitro' ),
						'type'       => 'checkbox',
						'group'      => esc_html__( 'General', 'wr-nitro' ),
					),
					array(
						'param_name' => 'read_more_link',
						'heading'    => esc_html__( 'Read more link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'General', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'read_more',
							'value'   => 'true',
						),
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
						'group'       => esc_html__( 'General', 'wr-nitro' ),
					),
					array(
						'param_name'  => 'character_text',
						'heading'     => esc_html__( 'Character', 'wr-nitro' ),
						'type'        => 'textfield',
						'group'       => esc_html__( 'Character Settings', 'wr-nitro' ),
						'value'       => 1,
						'dependency'  => array(
							'element' => 'style',
							'value'   => 'character',
						),
					),
					array(
						'param_name' => 'character_color',
						'heading'    => esc_html__( 'Character color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'group'      => esc_html__( 'Character Settings', 'wr-nitro' ),
						'value'      => '#e2e2e2',
						'dependency' => array(
							'element' => 'style',
							'value'   => 'character',
						),
					),
					array(
						'param_name' => 'character_font_family',
						'heading'    => esc_html__( 'Font Family', 'wr-nitro' ),
						'type'       => 'dropdown',
						'group'      => esc_html__( 'Character Settings', 'wr-nitro' ),
						'value'      => $fonts,
						'dependency' => array(
							'element' => 'style',
							'value'   => 'character',
						),
					),
					array(
						'param_name' => 'character_font_weight',
						'heading'    => esc_html__( 'Font Weight', 'wr-nitro' ),
						'type'       => 'dropdown',
						'group'      => esc_html__( 'Character Settings', 'wr-nitro' ),
						'value'      => $weights,
						'dependency' => array(
							'element' => 'style',
							'value'   => 'character',
						),
					),
					array(
						'param_name' => 'character_font_size',
						'heading'    => esc_html__( 'Font Size (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Character Settings', 'wr-nitro' ),
						'value'      => 90,
						'dependency' => array(
							'element' => 'style',
							'value'   => 'character',
						),
					),
					array(
						'param_name' => 'character_margin_bottom',
						'heading'    => esc_html__( 'Margin Bottom (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Character Settings', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => 'character',
						),
					),
					array(
						'param_name' => 'character_width',
						'heading'    => esc_html__( 'Character Width (px, for center alignment)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Character Settings', 'wr-nitro' ),
						'value'      => 50,
						'dependency' => array(
							'element' => 'style',
							'value'   => 'character',
						),
					),
					array(
						'param_name'  => 'icon_type',
						'heading'     => esc_html__( 'Icon Library', 'wr-nitro' ),
						'type'        => 'dropdown',
						'group'       => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'       => array(
							__( 'Font Awesome', 'wr-nitro' ) => 'fontawesome',
							__( 'Open Iconic', 'wr-nitro' )  => 'openiconic',
							__( 'Typicons', 'wr-nitro' )     => 'typicons',
							__( 'Entypo', 'wr-nitro' )       => 'entypo',
							__( 'Linecons', 'wr-nitro' )     => 'linecons',
						),
						'dependency'  => array(
							'element' => 'style',
							'value'   => 'icon',
						),
					),
					array(
						'param_name'  => 'icon_fontawesome',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'group'       => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'       => 'fa fa-adjust',
						'settings'    => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'fontawesome',
						),
					),
					array(
						'param_name'  => 'icon_openiconic',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'group'       => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'       => 'vc-oi vc-oi-dial',
						'settings'    => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'openiconic',
						),
					),
					array(
						'param_name'  => 'icon_typicons',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'group'       => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'       => 'typcn typcn-adjust-brightness',
						'settings'    => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'typicons',
						),
					),
					array(
						'param_name' => 'icon_entypo',
						'heading'    => esc_html__( 'Icon', 'wr-nitro' ),
						'type'       => 'iconpicker',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'      => 'entypo-icon entypo-icon-note',
						'settings'   => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value'   => 'entypo',
						),
					),
					array(
						'param_name'  => 'icon_linecons',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'group'       => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'       => 'vc_li vc_li-heart',
						'settings'    => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'linecons',
						),
					),
					array(
						'param_name' => 'icon_color',
						'heading'    => esc_html__( 'Icon color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'      => '#ff4064',
						'dependency' => array(
							'element' => 'style',
							'value'   => 'icon',
						),
					),
					array(
						'param_name' => 'icon_hover_color',
						'heading'    => esc_html__( 'Icon Hover Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => 'icon',
						),
					),
					array(
						'param_name' => 'icon_size',
						'heading'    => esc_html__( 'Icon Size (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'      => 24,
						'dependency' => array(
							'element' => 'style',
							'value'   => 'icon',
						),
					),
					array(
						'param_name' => 'icon_mgb',
						'heading'    => esc_html__( 'Margin Bottom (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'      => 0,
						'dependency' => array(
							'element' => 'style',
							'value'   => 'icon',
						),
					),
					array(
						'param_name' => 'icon_box',
						'heading'    => esc_html__( 'Enable Box Wrap Icon?', 'wr-nitro' ),
						'type'       => 'checkbox',
						'value'      => 'false',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => 'icon',
						),
					),
					array(
						'param_name' => 'icon_box_position',
						'heading'    => esc_html__( 'Icon Box Position', 'wr-nitro' ),
						'type'       => 'dropdown',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'      => array(
							__( 'Top', 'wr-nitro' )   => '',
							__( 'Left', 'wr-nitro' )  => 'left',
							__( 'Right', 'wr-nitro' ) => 'right',
						),
						'dependency' => array(
							'element' => 'icon_box',
							'value'   => 'true',
						),
						'edit_field_class' => 'vc_col-sm-4 vc_column',
					),
					array(
						'param_name' => 'icon_box_width',
						'heading'    => esc_html__( 'Icon Box Width', 'wr-nitro' ),
						'type'       => 'dropdown',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'      => array(
							__( 'Large (80x80px)', 'wr-nitro' )  => 'large',
							__( 'Medium (64x64px)', 'wr-nitro' ) => 'medium',
							__( 'Small (48x48px)', 'wr-nitro' )  => 'small',
							__( 'Custom Size', 'wr-nitro' )      => 'custom',
						),
						'dependency' => array(
							'element' => 'icon_box',
							'value'   => 'true',
						),
						'edit_field_class' => 'vc_col-sm-4 vc_column',
					),
					array(
						'param_name' => 'icon_box_style',
						'heading'    => esc_html__( 'Icon Box Style', 'wr-nitro' ),
						'type'       => 'dropdown',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'      => array(
							__( 'Circle', 'wr-nitro' ) => 'circle',
							__( 'Square', 'wr-nitro' ) => 'square',
						),
						'dependency' => array(
							'element' => 'icon_box',
							'value'   => 'true',
						),
						'edit_field_class' => 'vc_col-sm-4 vc_column',
					),
					array(
						'param_name' => 'icon_box_custom',
						'heading'    => esc_html__( 'Custom Size (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'value'      => 80,
						'dependency' => array(
							'element' => 'icon_box_width',
							'value'   => 'custom',
						),
					),
					array(
						'param_name' => 'icon_border_color',
						'heading'    => esc_html__( 'Border Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'icon_box',
							'value'   => 'true',
						),
					),
					array(
						'param_name' => 'icon_bg_color',
						'heading'    => esc_html__( 'Background Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'icon_box',
							'value'   => 'true',
						),
					),
					array(
						'param_name' => 'icon_hover_border_color',
						'heading'    => esc_html__( 'Border Hover', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'icon_box',
							'value'   => 'true',
						),
					),
					array(
						'param_name' => 'icon_hover_bg_color',
						'heading'    => esc_html__( 'Background Hover', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'icon_box',
							'value'   => 'true',
						),
					),
					array(
						'param_name' => 'icon_box_link',
						'heading'    => esc_html__( 'Link to', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'icon_box',
							'value'   => 'true',
						),
					),
					array(
						'param_name' => 'graphic',
						'heading'    => esc_html__( 'Upload Image', 'wr-nitro' ),
						'type'       => 'attach_image',
						'group'      => esc_html__( 'Image Settings', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => 'image',
						),
					),
					array(
						'param_name' => 'image_radius',
						'heading'    => esc_html__( 'Border Radius (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Image Settings', 'wr-nitro' ),
						'value'      => 0,
						'dependency' => array(
							'element' => 'style',
							'value'   => 'image',
						),
					),
					array(
						'param_name' => 'image_mgb',
						'heading'    => esc_html__( 'Margin Bottom (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Image Settings', 'wr-nitro' ),
						'value'      => 20,
						'dependency' => array(
							'element' => 'style',
							'value'   => 'image',
						),
					),
					array(
						'param_name' => 'image_opacity',
						'heading'    => esc_html__( 'Image Opacity', 'wr-nitro' ),
						'type'       => 'range',
						'group'      => esc_html__( 'Image Settings', 'wr-nitro' ),
						'value'      => 1,
						'min'        => 0,
						'max'        => 1,
						'step'       => 0.1,
						'dependency' => array(
							'element' => 'style',
							'value'   => 'image',
						),
					),
					array(
						'param_name'       => 'title_text',
						'heading'          => esc_html__( 'Heading', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-12 mgb20',
						'group'            => esc_html__( 'Heading Settings', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'font_family',
						'heading'          => esc_html__( 'Font Family', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 mgb20',
						'group'            => esc_html__( 'Heading Settings', 'wr-nitro' ),
						'value'            => $fonts,
					),
					array(
						'param_name'       => 'font_weight',
						'heading'          => esc_html__( 'Font Weight', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 mgb20 vc_column',
						'group'            => esc_html__( 'Heading Settings', 'wr-nitro' ),
						'value'            => $weights,
						'dependency' => array(
							'element'   => 'font_family',
							'not_empty' => true
						),
					),
					array(
						'param_name'       => 'title_color',
						'heading'          => esc_html__( 'Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-sm-4 mgb20 vc_column',
						'group'            => esc_html__( 'Heading Settings', 'wr-nitro' ),
						'dependency' => array(
							'element'   => 'font_family',
							'not_empty' => true
						),
					),
					array(
						'param_name'       => 'text_transform',
						'heading'          => esc_html__( 'Text Transform', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Heading Settings', 'wr-nitro' ),
						'value'            => array(
							__( 'None', 'wr-nitro' )       => 'none',
							__( 'Uppercase', 'wr-nitro' )  => 'uppercase',
							__( 'Capitalize', 'wr-nitro' ) => 'capitalize',
						),
					),
					array(
						'param_name'       => 'title_spacing',
						'heading'          => esc_html__( 'Letter Spacing (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Heading Settings', 'wr-nitro' ),
						'value'            => 0,
					),
					array(
						'param_name'       => 'font_size',
						'heading'          => esc_html__( 'Font Size (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Heading Settings', 'wr-nitro' ),
						'value'            => 24,
					),
					array(
						'param_name'       => 'heading_mgb',
						'heading'          => esc_html__( 'Margin Bottom (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Heading Settings', 'wr-nitro' ),
						'value'            => 15,
					),
					array(
						'param_name' => 'sep',
						'heading'    => esc_html__( 'Enable Separator?', 'wr-nitro' ),
						'type'       => 'checkbox',
						'value'      => 'false',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
					),
					array(
						'param_name' => 'sep_color',
						'heading'    => esc_html__( 'Separator Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'group'      => esc_html__( 'Separator', 'wr-nitro' ),
						'value'      => '#bf9869',
						'dependency' => array(
							'element' => 'sep',
							'value'   => 'true',
						),
					),
					array(
						'param_name' => 'setting',
						'type'       => 'css_editor',
						'group'      => esc_html__( 'Background Settings', 'wr-nitro' ),
					),
					array(
						'param_name' => 'sub_1',
						'heading'    => esc_html__( 'Hover Settings', 'wr-nitro' ),
						'type'       => 'sub_heading',
						'group'      => esc_html__( 'Background Settings', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'border_hover_color',
						'heading'          => esc_html__( 'Border Hover color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-sm-4 mgb20',
						'group'            => esc_html__( 'Background Settings', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'text_hover_color',
						'heading'          => esc_html__( 'Text Hover color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-sm-4 mgb20',
						'group'            => esc_html__( 'Background Settings', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'bg_hover_color',
						'heading'          => esc_html__( 'Background Hover color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-sm-4 mgb20',
						'group'            => esc_html__( 'Background Settings', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'services_custom_id',
						'heading'          => esc_html__( 'ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'group'            => esc_html__( 'General', 'wr-nitro' ),
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new Member element. sddd
		vc_map(
			array(
				'name'     => esc_html__( 'Member', 'wr-nitro' ),
				'base'     => 'nitro_member',
				'icon'     => 'fa fa-users',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name'  => 'style',
						'heading'     => esc_html__( 'Style', 'wr-nitro' ),
						'type'        => 'dropdown',
						'admin_label' => true,
						'value'       => array(
							__( 'Style 1', 'wr-nitro' ) => 'style-1',
							__( 'Style 2', 'wr-nitro' ) => 'style-2',
							__( 'Style 3', 'wr-nitro' ) => 'style-3',
						),
					),
					array(
						'param_name' => 'avatar',
						'heading'    => esc_html__( 'Avatar', 'wr-nitro' ),
						'type'       => 'attach_image',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'name',
						'heading'    => esc_html__( 'Name', 'wr-nitro' ),
						'type'       => 'textfield',
						'holder'     => 'h2',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'position',
						'heading'    => esc_html__( 'Position', 'wr-nitro' ),
						'type'       => 'textfield',
						'holder'     => 'h2',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'bio',
						'heading'    => esc_html__( 'Biographical info', 'wr-nitro' ),
						'type'       => 'textarea',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-2' ),
						),
					),
					array(
						'param_name' => 'facebook',
						'heading'    => esc_html__( 'Facebook Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'twitter',
						'heading'    => esc_html__( 'Twitter Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'dribbble',
						'heading'    => esc_html__( 'Dribbble Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'behance',
						'heading'    => esc_html__( 'Behance Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'linkedin',
						'heading'    => esc_html__( 'Linkedin Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'tumblr',
						'heading'    => esc_html__( 'Tumblr Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'pinterest',
						'heading'    => esc_html__( 'Pinterest Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'googleplus',
						'heading'    => esc_html__( 'Google Plus Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'instagram',
						'heading'    => esc_html__( 'Instagram Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'skype',
						'heading'    => esc_html__( 'Skype ID', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-1', 'style-2' ),
						),
					),
					array(
						'param_name' => 'columns',
						'heading'    => esc_html__( 'Columns', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( '1 Column', 'wr-nitro' )  => '1',
							__( '2 Columns', 'wr-nitro' ) => '2',
							__( '3 Columns', 'wr-nitro' ) => '3',
							__( '4 Columns', 'wr-nitro' ) => '4',
							__( '5 Columns', 'wr-nitro' ) => '5',
							__( '6 Columns', 'wr-nitro' ) => '6',
							__( '7 Columns', 'wr-nitro' ) => '7',
							__( '8 Columns', 'wr-nitro' ) => '8',
						),
						'std' => 4,
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-3' ),
						),
					),
					array(
						'heading'    => esc_html__( 'Members', 'wr-nitro' ),
						'type'       => 'param_group',
						'param_name' => 's3_member',
						'params'     => array(
							array(
								'param_name' => 'avatar',
								'heading'    => esc_html__( 'Small Avatar', 'wr-nitro' ),
								'description' => esc_html__( 'Image dimensions minimum: 300x400px', 'wr-nitro' ),
								'type'       => 'attach_image',
							),
							array(
								'param_name' => 'avatar_large',
								'heading'    => esc_html__( 'Big Avatar', 'wr-nitro' ),
								'description' => esc_html__( 'Image dimensions minimum: 300x400px', 'wr-nitro' ),
								'type'       => 'attach_image',
							),
							array(
								'param_name' => 'name',
								'heading'    => esc_html__( 'Name', 'wr-nitro' ),
								'type'       => 'textfield',
							),
							array(
								'param_name' => 'position',
								'heading'    => esc_html__( 'Position', 'wr-nitro' ),
								'type'       => 'textfield',
							),
							array(
								'param_name' => 'bio',
								'heading'    => esc_html__( 'Biographical info', 'wr-nitro' ),
								'type'       => 'textarea',
							),
							array(
								'param_name' => 'facebook',
								'heading'    => esc_html__( 'Facebook Link', 'wr-nitro' ),
								'type'       => 'textfield',
								'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
							),
							array(
								'param_name' => 'twitter',
								'heading'    => esc_html__( 'Twitter Link', 'wr-nitro' ),
								'type'       => 'textfield',
								'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
							),
							array(
								'param_name' => 'dribbble',
								'heading'    => esc_html__( 'Dribbble Link', 'wr-nitro' ),
								'type'       => 'textfield',
								'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
							),
							array(
								'param_name' => 'behance',
								'heading'    => esc_html__( 'Behance Link', 'wr-nitro' ),
								'type'       => 'textfield',
								'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
							),
							array(
								'param_name' => 'linkedin',
								'heading'    => esc_html__( 'Linkedin Link', 'wr-nitro' ),
								'type'       => 'textfield',
								'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
							),
							array(
								'param_name' => 'tumblr',
								'heading'    => esc_html__( 'Tumblr Link', 'wr-nitro' ),
								'type'       => 'textfield',
								'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
							),
							array(
								'param_name' => 'pinterest',
								'heading'    => esc_html__( 'Pinterest Link', 'wr-nitro' ),
								'type'       => 'textfield',
								'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
							),
							array(
								'param_name' => 'googleplus',
								'heading'    => esc_html__( 'Google Plus Link', 'wr-nitro' ),
								'type'       => 'textfield',
								'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
							),
							array(
								'param_name' => 'instagram',
								'heading'    => esc_html__( 'Instagram Link', 'wr-nitro' ),
								'type'       => 'textfield',
								'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
							),
							array(
								'param_name' => 'skype',
								'heading'    => esc_html__( 'Skype ID', 'wr-nitro' ),
								'type'       => 'textfield',
								'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
							),
						),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'style-3' ),
						),
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
					),
					array(
						'param_name'       => 'member_custom_id',
						'heading'          => esc_html__( 'ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new Pricing Table element.
		vc_map(
			array(
				'name'                    => esc_html__( 'Pricing Table', 'wr-nitro' ),
				'icon'                    => 'fa fa-table',
				'base'                    => 'nitro_pricing',
				'category'                => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'as_parent'               => array( 'only' => 'nitro_pricing_single' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'param_name'  => 'style',
						'heading'     => esc_html__( 'Style', 'wr-nitro' ),
						'description' => esc_html__( 'Choose style for pricing table.', 'wr-nitro' ),
						'type'        => 'dropdown',
						'value'       => array(
							__( 'Style 1', 'wr-nitro' ) => 'style-1',
							__( 'Style 2', 'wr-nitro' ) => 'style-2',
							__( 'Style 3', 'wr-nitro' ) => 'style-3',
							__( 'Style 4', 'wr-nitro' ) => 'style-4',
						),
					),
					array(
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
						'param_name'  => 'el_class',
					),
					array(
						'param_name'       => 'pricing_custom_id',
						'heading'          => esc_html__( 'ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);
		// Map new Pricing Item element.
		vc_map(
			array(
				'name'            => esc_html__( 'Pricing Item', 'wr-nitro' ),
				'icon'            => 'fa fa-columns',
				'base'            => 'nitro_pricing_single',
				'content_element' => true,
				'as_child'        => array( 'only' => 'nitro_pricing' ),
				'params'          => array(
					array(
						'heading'     => esc_html__( 'Featured Item', 'wr-nitro' ),
						'description' => 'Checked to active',
						'type'        => 'checkbox',
						'value'       => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'param_name'  => 'pricing_featured',
					),
					array(
						'heading'          => esc_html__( 'Heading', 'wr-nitro' ),
						'type'             => 'textfield',
						'param_name'       => 'pricing_title',
						'edit_field_class' => 'vc_col-sm-12 mgb20 mgt20',
					),
					array(
						'heading'    => esc_html__( 'Description', 'wr-nitro' ),
						'type'       => 'textfield',
						'param_name' => 'pricing_title_desc',
						'dependency' => array(
							'element'            => 'style',
							'value_not_equal_to' => 'style-1',
						),
					),
					array(
						'heading'          => esc_html__( 'Price (eg: 100)', 'wr-nitro' ),
						'type'             => 'textfield',
						'param_name'       => 'pricing_price',
						'edit_field_class' => 'vc_col-sm-4 mgb20 mgt20',
					),
					array(
						'heading'          => esc_html__( 'Unit (eg: $)', 'wr-nitro' ),
						'type'             => 'textfield',
						'param_name'       => 'pricing_price_units',
						'edit_field_class' => 'vc_col-sm-4 mgb20 mgt20',
					),
					array(
						'heading'          => esc_html__( 'Value (eg: Monthly)', 'wr-nitro' ),
						'type'             => 'textfield',
						'param_name'       => 'pricing_units',
						'edit_field_class' => 'vc_col-sm-4 mgb20 mgt20',
					),
					array(
						'heading'    => esc_html__( 'Show Icon', 'wr-nitro' ),
						'type'       => 'checkbox',
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'param_name' => 'show_option_icon',
						'edit_field_class' => 'vc_col-sm-4 mgb20 mgt20',
					),
					array(
						'heading'    => esc_html__( 'Options', 'wr-nitro' ),
						'type'       => 'param_group',
						'param_name' => 'pricing_content',
						'params'     => array(
							array(
								'type'        => 'iconpicker',
								'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
								'param_name'  => 'item_icon_fontawesome',
								'value'       => 'fa fa-info-circle',
								'settings'    => array(
									'emptyIcon'    => false, // default true, display an "EMPTY" icon?
									'iconsPerPage' => 200, // default 100, how many icons per/page to display
								),
								'description' => esc_html__( 'Select icon from library.', 'wr-nitro' ),
							),
							array(
								'type'        => 'textarea',
								'heading'     => esc_html__( 'Option Text', 'wr-nitro' ),
								'param_name'  => 'pricing_item',
							),
						),
					),
					array(
						'heading'          => esc_html__( 'Button Text', 'wr-nitro' ),
						'description'      => 'Enter pricing table button text',
						'type'             => 'textfield',
						'holder'           => 'span',
						'class'            => 'pricing-button',
						'param_name'       => 'button_text',
						'edit_field_class' => 'vc_col-sm-6 vc_column mgb20',
						'dependency' => array(
							'element'            => 'style',
							'value_not_equal_to' => 'style-2',
						),
					),
					array(
						'heading'          => esc_html__( 'Button Link', 'wr-nitro' ),
						'description'      => 'Enter button link',
						'type'             => 'textfield',
						'param_name'       => 'button_link',
						'edit_field_class' => 'vc_col-sm-6 mgb20 mgt15',
					),
				)
			)
		);

		// Map new Social icons element.
		vc_map(
			array(
				'name'            => esc_html__( 'Social Icons', 'wr-nitro' ),
				'icon'            => 'fa fa-share-alt-square',
				'base'            => 'nitro_social',
				'category'        => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'content_element' => true,
				'params'          => array(
					array(
						'param_name'       => 'style',
						'heading'          => esc_html__( 'Style', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-12 mgb20',
						'admin_label'      => true,
						'value'            => array(
							__( 'Default', 'wr-nitro' )         => 'default',
							__( 'Square', 'wr-nitro' )          => 'solid_square',
							__( 'Circle', 'wr-nitro' )          => 'solid_circle',
							__( 'Rounded', 'wr-nitro' )         => 'solid_rounded',
							__( 'Square Outline', 'wr-nitro' )  => 'outline_square',
							__( 'Circle Outline', 'wr-nitro' )  => 'outline_circle',
							__( 'Rounded Outline', 'wr-nitro' ) => 'outline_rounded',
						),
					),
					array(
						'heading'     => esc_html__( 'Display Multi Colors', 'wr-nitro' ),
						'description' => esc_html__( 'When this option is checked, color or background is auto selected by its default branding accordingly.', 'wr-nitro' ),
						'type'        => 'checkbox',
						'value'       => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'param_name'  => 'multicolor',
					),
					array(
						'param_name'       => 'size',
						'heading'          => esc_html__( 'Size', 'wr-nitro' ),
						'description'      => esc_html__( 'Select size for icon.', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-12 mgb20 mgt20',
						'value'            => array(
							__( 'Small', 'wr-nitro' )      => 'small',
							__( 'Normal', 'wr-nitro' )     => 'normal',
							__( 'Large', 'wr-nitro' )      => 'large',
						),
					),
					array(
						'param_name'  => 'icon_color',
						'heading'     => esc_html__( 'Icon Color', 'wr-nitro' ),
						'type'        => 'colorpicker',
						'value'       => '#ff4064',
						'description' => esc_html__( 'When Multi colors option is checked, this option is not applicable.', 'wr-nitro' ),
					),
					array(
						'param_name'  => 'bg_color',
						'heading'     => esc_html__( 'Background Color', 'wr-nitro' ),
						'type'        => 'colorpicker',
						'value'       => '#ff4064',
						'description' => esc_html__( 'When Multi colors option is checked, this option is not applicable.', 'wr-nitro' ),
						'dependency'  => array(
							'element' => 'style',
							'value'   => array( 'solid_square', 'solid_circle', 'solid_rounded' ),
						),
					),
					array(
						'param_name' => 'border_color',
						'heading'    => esc_html__( 'Border Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#ff4064',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'outline_square', 'outline_circle', 'outline_rounded' ),
						),
					),
					array(
						'param_name' => 'icon_hover_color',
						'heading'    => esc_html__( 'Icon Hover Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#646464',
					),
					array(
						'param_name' => 'bg_hover_color',
						'heading'    => esc_html__( 'Background Hover Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#ff4064',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'solid_square', 'solid_circle', 'solid_rounded' ),
						),
					),
					array(
						'param_name' => 'border_hover_color',
						'heading'    => esc_html__( 'Border Hover Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#363636',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'outline_square', 'outline_circle', 'outline_rounded' ),
						),
					),
					array(
						'param_name' => 'border_width',
						'heading'    => esc_html__( 'Border width (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => '1',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'outline_square', 'outline_circle', 'outline_rounded' ),
						),
					),
					array(
						'param_name' => 'facebook',
						'heading'    => esc_html__( 'Facebook Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'twitter',
						'heading'    => esc_html__( 'Twitter Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'instagram',
						'heading'    => esc_html__( 'Instagram Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'gplus',
						'heading'    => esc_html__( 'Google Plus Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'skype',
						'heading'    => esc_html__( 'Skype Name', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'linkedin',
						'heading'    => esc_html__( 'Linkedin Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'dribbble',
						'heading'    => esc_html__( 'Dribbble Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'behance',
						'heading'    => esc_html__( 'Behance Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'github',
						'heading'    => esc_html__( 'Github Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'foursquare',
						'heading'    => esc_html__( 'Foursquare Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'youtube',
						'heading'    => esc_html__( 'Youtube Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'tumblr',
						'heading'    => esc_html__( 'Tumblr Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'pinterest',
						'heading'    => esc_html__( 'Pinterest Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'rss',
						'heading'    => esc_html__( 'RSS Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'param_name' => 'vk',
						'heading'    => esc_html__( 'VK Link', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Social Network', 'wr-nitro' ),
					),
					array(
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
						'param_name'  => 'extra_class',
					),
					array(
						'param_name'       => 'social_custom_id',
						'heading'          => esc_html__( 'ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new List element.
		vc_map(
			array(
				'name'            => esc_html__( 'List', 'wr-nitro' ),
				'icon'            => 'fa fa-list-alt',
				'base'            => 'nitro_lists',
				'category'        => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'content_element' => true,
				'params'          => array(
					array(
						'param_name' => 'style',
						'heading'    => esc_html__( 'List Style', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'None', 'wr-nitro' )           => 'none',
							__( 'Square Bullet', 'wr-nitro' )  => 'square',
							__( 'Dot Bullet', 'wr-nitro' )     => 'dot',
							__( 'Number', 'wr-nitro' )         => 'number-list',
							__( 'Icon', 'wr-nitro' )           => 'icon-list',
						),
						'std'        => 'none',
					),
					array(
						'heading'    => esc_html__( 'List Content', 'wr-nitro' ),
						'type'       => 'param_group',
						'param_name' => 'list_content',
						'params'     => array(
							array(
								'type'       => 'iconpicker',
								'heading'    => esc_html__( 'Icon', 'wr-nitro' ),
								'param_name' => 'icon_fontawesome',
								'value'      => 'fa fa-info-circle',
								'settings'   => array(
									'emptyIcon'    => false,
									'iconsPerPage' => 200,
								),
								'description' => esc_html__( 'Icon show only when you select Icon style.', 'wr-nitro' ),
							),
							array(
								'param_name'  => 'icon_color',
								'heading'     => esc_html__( 'Icon Color', 'wr-nitro' ),
								'type'        => 'colorpicker',
								'value'       => '#363636',
								'description' => esc_html__( 'Support both styles', 'wr-nitro' ),
							),
							array(
								'type'       => 'textfield',
								'heading'    => esc_html__( 'Text', 'wr-nitro' ),
								'param_name' => 'list_item',
							),
							array(
								'param_name' => 'link',
								'heading'    => esc_html__( 'Link To', 'wr-nitro' ),
								'type'       => 'vc_link',
							),
						),
					),
					array(
						'param_name' => 'line_height',
						'heading'    => esc_html__( 'Line spacing', 'wr-nitro' ),
						'group'      => esc_html__( 'Design settings', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => '20',
					),
					array(
						'param_name'  => 'bg_color',
						'heading'     => esc_html__( 'Icon Background', 'wr-nitro' ),
						'group'      => esc_html__( 'Design settings', 'wr-nitro' ),
						'type'        => 'colorpicker',
						'value'       => '#fff',
						'dependency' => array(
							'element' => 'style',
							'value'   => 'icon-list',
						),
					),
					array(
						'param_name' => 'divider',
						'heading'    => esc_html__( 'Enable divider', 'wr-nitro' ),
						'group'      => esc_html__( 'Design settings', 'wr-nitro' ),
						'type'       => 'checkbox',
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
					),
					array(
						'param_name' => 'divider_style',
						'heading'    => esc_html__( 'Divider style', 'wr-nitro' ),
						'group'      => esc_html__( 'Design settings', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Solid', 'wr-nitro' )  => 'solid',
							__( 'Dotted', 'wr-nitro' ) => 'dotted',
							__( 'Dashed', 'wr-nitro' ) => 'dashed',
							__( 'Double', 'wr-nitro' ) => 'double',
							__( 'Groove', 'wr-nitro' ) => 'groove',
							__( 'Ridge', 'wr-nitro' )  => 'ridge',
							__( 'Inset', 'wr-nitro' )  => 'inset',
							__( 'Outset', 'wr-nitro' ) => 'outset',
						),
						'dependency' => array(
							'element' => 'divider',
							'value'   => 'yes',
						),
					),
					array(
						'param_name' => 'divider_width',
						'heading'    => esc_html__( 'Divider width', 'wr-nitro' ),
						'group'      => esc_html__( 'Design settings', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( '1px', 'wr-nitro' ) => '1',
							__( '2px', 'wr-nitro' ) => '2',
							__( '3px', 'wr-nitro' ) => '3',
						),
						'dependency' => array(
							'element' => 'divider',
							'value'   => 'yes',
						),
					),
					array(
						'param_name'  => 'divider_color',
						'heading'     => esc_html__( 'Divider color', 'wr-nitro' ),
						'group'      => esc_html__( 'Design settings', 'wr-nitro' ),
						'type'        => 'colorpicker',
						'value'       => '#c5c5c5',
						'dependency' => array(
							'element' => 'divider',
							'value'   => 'yes',
						),
					),
					array(
						'param_name' => 'border',
						'heading'    => esc_html__( 'Enable icon border', 'wr-nitro' ),
						'group'      => esc_html__( 'Design settings', 'wr-nitro' ),
						'type'       => 'checkbox',
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
					),
					array(
						'param_name' => 'border_style',
						'heading'    => esc_html__( 'Border style', 'wr-nitro' ),
						'group'      => esc_html__( 'Design settings', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Solid', 'wr-nitro' )  => 'solid',
							__( 'Dotted', 'wr-nitro' ) => 'dotted',
							__( 'Dashed', 'wr-nitro' ) => 'dashed',
							__( 'Double', 'wr-nitro' ) => 'double',
							__( 'Groove', 'wr-nitro' ) => 'groove',
							__( 'Ridge', 'wr-nitro' )  => 'ridge',
							__( 'Inset', 'wr-nitro' )  => 'inset',
							__( 'Outset', 'wr-nitro' ) => 'outset',
						),
						'dependency' => array(
							'element' => 'border',
							'value'   => 'yes',
						),
					),
					array(
						'param_name' => 'border_width',
						'heading'    => esc_html__( 'Border width', 'wr-nitro' ),
						'group'      => esc_html__( 'Design settings', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( '1px', 'wr-nitro' ) => '1',
							__( '2px', 'wr-nitro' ) => '2',
							__( '3px', 'wr-nitro' ) => '3',
						),
						'dependency' => array(
							'element' => 'border',
							'value'   => 'yes',
						),
					),
					array(
						'param_name' => 'border_radius',
						'heading'    => esc_html__( 'Border radius', 'wr-nitro' ),
						'group'      => esc_html__( 'Design settings', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => '0',
						'dependency' => array(
							'element' => 'border',
							'value'   => 'yes',
						),
					),
					array(
						'param_name'  => 'border_color',
						'heading'     => esc_html__( 'Border color', 'wr-nitro' ),
						'group'      => esc_html__( 'Design settings', 'wr-nitro' ),
						'type'        => 'colorpicker',
						'value'       => '#363636',
						'dependency' => array(
							'element' => 'border',
							'value'   => 'yes',
						),
					),
					array(
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
						'param_name'  => 'list_custom_class',
					),
					array(
						'param_name'       => 'lists_custom_id',
						'heading'          => esc_html__( 'ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new Timeline element.
		vc_map(
			array(
				'name'            => esc_html__( 'Timeline', 'wr-nitro' ),
				'icon'            => 'fa fa-list-ol',
				'base'            => 'nitro_timeline',
				'category'        => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'content_element' => true,
				'params'          => array(
					array(
						'param_name' => 'style',
						'heading'    => esc_html__( 'Timeline Style', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Style 1', 'wr-nitro' )  => 'style-1',
							__( 'Style 2', 'wr-nitro' )  => 'style-2',
						),
					),
					array(
						'param_name'  => 'pin_color',
						'heading'     => esc_html__( 'Pin Color', 'wr-nitro' ),
						'type'        => 'colorpicker',
						'value'       => '#000',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name'  => 'pin_border',
						'heading'     => esc_html__( 'Pin Border', 'wr-nitro' ),
						'type'        => 'colorpicker',
						'value'       => '#bfbfbf',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'heading'    => esc_html__( 'Timeline Content', 'wr-nitro' ),
						'type'       => 'param_group',
						'param_name' => 'timeline_content',
						'params'     => array(
							array(
								'type'       => 'textfield',
								'heading'    => esc_html__( 'Heading', 'wr-nitro' ),
								'param_name' => 'heading',
							),
							array(
								'type'       => 'textfield',
								'heading'    => esc_html__( 'Date time', 'wr-nitro' ),
								'param_name' => 'datetime',
							),
							array(
								'type'       => 'textarea',
								'heading'    => esc_html__( 'Content', 'wr-nitro' ),
								'param_name' => 'content',
							),
						),
					),
					array(
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
						'param_name'  => 'timeline_custom_class',
					),
					array(
						'param_name'       => 'timeline_custom_id',
						'heading'          => esc_html__( 'ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new Blockquote element.
		vc_map(
			array(
				'name'            => esc_html__( 'Blockquote', 'wr-nitro' ),
				'icon'            => 'fa fa-quote-left',
				'base'            => 'nitro_quote',
				'category'        => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'content_element' => true,
				'params'          => array(
					array(
						'param_name' => 'style',
						'heading'    => esc_html__( 'Style', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Border', 'wr-nitro' )           => 'border',
							__( 'Quote With Icon', 'wr-nitro' )  => 'quote-icon',
							__( 'Solid Background', 'wr-nitro' ) => 'solid-bg',
							__( 'Outline', 'wr-nitro' )          => 'outline',
						),
					),
					array(
						'param_name' => 'border_position',
						'heading'    => esc_html__( 'Border Position', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Left', 'wr-nitro' )  => 'tl',
							__( 'Right', 'wr-nitro' ) => 'tr',
						),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'border' ),
						),
					),
					array(
						'param_name'       => 'icon_margin_top',
						'heading'          => esc_html__( 'Icon Margin Top (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'quote-icon' ),
						),
					),
					array(
						'param_name'       => 'icon_margin_left',
						'heading'          => esc_html__( 'Icon Margin Left (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'quote-icon' ),
						),
					),
					array(
						'param_name' => 'icon_color',
						'heading'    => esc_html__( 'Icon Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#363636',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'quote-icon' ),
						),
					),
					array(
						'param_name' => 'border_color',
						'heading'    => esc_html__( 'Border Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#363636',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'border', 'outline' ),
						),
					),
					array(
						'heading'    => esc_html__( 'Border Width (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'param_name' => 'border_width',
						'value'      => '3',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'border', 'outline' ),
						),
					),
					array(
						'param_name' => 'outline_style',
						'heading'    => esc_html__( 'Outline Style', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Solid', 'wr-nitro' ) => 'solid',
							__( 'Dashed', 'wr-nitro' ) => 'dashed',
							__( 'Double', 'wr-nitro' ) => 'double',
							__( 'Groove', 'wr-nitro' ) => 'groove',
						),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'outline' ),
						),
					),
					array(
						'param_name' => 'bg_color',
						'heading'    => esc_html__( 'Background Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#646464',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'solid-bg' ),
						),
					),
					array(
						'type'        => 'textarea',
						'heading'     => esc_html__( 'Content', 'wr-nitro' ),
						'param_name'  => 'quote_content',
						'description' => esc_html__( 'Enter the content.', 'wr-nitro' ),
						'holder'      => 'blockquote'
					),
					array(
						'param_name'       => 'text_color',
						'heading'          => esc_html__( 'Text Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'value'            => '#222',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
					),
					array(
						'param_name'       => 'font_size',
						'heading'          => esc_html__( 'Font Size (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
					),
					array(
						'param_name'       => 'line_height',
						'heading'          => esc_html__( 'Line Height (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
					),
					array(
						'param_name'       => 'spacing',
						'heading'          => esc_html__( 'Letter Spacing (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
					),
					array(
						'heading'     => esc_html__( 'Author', 'wr-nitro' ),
						'type'        => 'textfield',
						'param_name'  => 'author',
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
					),
					array(
						'param_name'       => 'quote_custom_id',
						'heading'          => esc_html__( 'ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new Blog Post element.
		vc_map(
			array(
				'name'     => esc_html__( 'Blog Post', 'wr-nitro' ),
				'base'     => 'nitro_blog_list',
				'icon'     => 'fa fa-file-text-o',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name' => 'orderby',
						'heading'    => esc_html__( 'Order by', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Recent posts', 'wr-nitro' )       => 'date',
							__( 'Post ID', 'wr-nitro' )            => 'ID',
							__( 'Title', 'wr-nitro' )              => 'title',
							__( 'Last modified date', 'wr-nitro' ) => 'modified',
							__( 'Random order', 'wr-nitro' )       => 'rand',
							__( 'Number of comments', 'wr-nitro' ) => 'comment_count',
							__( 'Special posts', 'wr-nitro' )      => 'specials',
						),
					),
					array(
						'param_name' => 'sort_order',
						'heading'    => esc_html__( 'Sort Order', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => $order_way_values,
						'std'        => 'ASC',
					),
					array(
						'param_name'  => 'contain',
						'heading'     => esc_html__( 'Include special posts', 'wr-nitro' ),
						'description' => esc_html__( 'Enter posts title to display only those records (Note: separate values by commas (,)).', 'wr-nitro' ),
						'type'        => 'autocomplete',
						'settings'    => array(
							'multiple'      => true,
							'sortable'      => true,
							'unique_values' => true,
						),
						'dependency' => array(
							'element' => 'orderby',
							'value'   => 'specials'
						),
					),
					array(
						'param_name'  => 'exclude',
						'heading'     => esc_html__( 'Exclude posts', 'wr-nitro' ),
						'description' => esc_html__( 'Enter posts title to exclude (Note: separate values by commas (,)).', 'wr-nitro' ),
						'type'        => 'autocomplete',
						'settings'    => array(
							'multiple'      => true,
							'sortable'      => true,
							'unique_values' => true,
						),
						'dependency'  => array(
							'element'            => 'orderby',
							'value_not_equal_to' => 'specials'
						),
					),
					array(
						'param_name'  => 'exclude_by_cat',
						'heading'     => esc_html__( 'Exclude post by category', 'wr-nitro' ),
						'description' => esc_html__( 'Enter category ID to exclude the post (Note: separate values by commas (,)).', 'wr-nitro' ),
						'type'        => 'textfield',
						'dependency'  => array(
							'element'            => 'orderby',
							'value_not_equal_to' => 'specials'
						),
					),
					array(
						'param_name' => 'style',
						'heading'    => esc_html__( 'Layout', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Standard', 'wr-nitro' )      => 'standard',
							__( 'Horizontal', 'wr-nitro' )    => 'horizontal',
							__( 'List', 'wr-nitro' )          => 'list',
							__( 'Minimal', 'wr-nitro' )       => 'minimal',
							__( 'Inner Content', 'wr-nitro' ) => 'inner',
							__( 'Zigzag', 'wr-nitro' )        => 'zigzag',
						),
						'edit_field_class' => 'vc_col-sm-6 mgb20 mgt20',
					),
					array(
						'param_name'       => 'limit',
						'heading'          => esc_html__( 'Total posts', 'wr-nitro' ),
						'description'      => esc_html__( 'Set max limit for posts in list or enter -1 to display all', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => '12',
						'edit_field_class' => 'vc_col-sm-6 mgt20 mgb20',
					),
					array(
						'param_name'  => 'columns',
						'heading'     => esc_html__( 'Number of columns displayed', 'wr-nitro' ),
						'type'        => 'dropdown',
						'value'       => array(
							__( '1 column', 'wr-nitro' ) => '1',
							__( '2 columns', 'wr-nitro' ) => '2',
							__( '3 columns', 'wr-nitro' ) => '3',
							__( '4 columns', 'wr-nitro' ) => '4',
							__( '5 columns', 'wr-nitro' ) => '5',
							__( '6 columns', 'wr-nitro' ) => '6',
						),
						'std'         => '3',
						'edit_field_class' => 'vc_col-sm-6 vc_column mgt10 mgb20',
					),
					array(
						'param_name'  => 'gap',
						'heading'     => esc_html__( 'Gutter', 'wr-nitro' ),
						'description' => esc_html__( 'Enter space between post (Unit: pixel).', 'wr-nitro' ),
						'type'        => 'range',
						'value'       => '30',
						'max'         => '100',
						'min'         => '0',
						'step'        => '1',
						'unit'        => 'px',
						'edit_field_class' => 'vc_col-sm-6 mgt10 mgb20',
					),
					array(
						'param_name'  => 'thumbnail',
						'heading'     => esc_html__( 'Thumbnail size', 'wr-nitro' ),
						'type'        => 'dropdown',
						'value'       => array(
							__( 'Small', 'wr-nitro' )  => 'small',
							__( 'Medium', 'wr-nitro' ) => 'medium',
							__( 'Large', 'wr-nitro' )  => 'large',
						),
						'std'         => 'medium',
						'dependency'  => array(
							'element' => 'style',
							'value'   => 'list'
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column mgt10 mgb20',
					),
					array(
						'param_name' => 'slider',
						'heading'    => esc_html__( 'Enable Slider', 'wr-nitro' ),
						'type'       => 'checkbox',
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'std'        => 'no',
						'dependency' => array(
							'element'            => 'style',
							'value_not_equal_to' => array( 'list', 'zigzag' )
						),
					),
					array(
						'param_name' => 'navigation',
						'heading'    => esc_html__( 'Enable Navigation', 'wr-nitro' ),
						'type'       => 'checkbox',
						'group'      => esc_html__( 'Slider settings', 'wr-nitro' ),
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'std'        => 'no',
						'dependency' => array(
							'element' => 'slider',
							'value'   => 'yes'
						),
					),
					array(
						'param_name' => 'pagination',
						'heading'    => esc_html__( 'Enable Pagination', 'wr-nitro' ),
						'type'       => 'checkbox',
						'group'      => esc_html__( 'Slider settings', 'wr-nitro' ),
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'std'        => 'no',
						'dependency' => array(
							'element' => 'slider',
							'value'   => 'yes'
						),
					),
					array(
						'param_name' => 'autoplay',
						'heading'    => esc_html__( 'Autoplay', 'wr-nitro' ),
						'type'       => 'checkbox',
						'group'      => esc_html__( 'Slider settings', 'wr-nitro' ),
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'std'        => 'no',
						'dependency' => array(
							'element' => 'slider',
							'value'   => 'yes'
						),
					),
					array(
						'param_name' => 'autotime',
						'heading'    => esc_html__( 'Autoplay interval timeout', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Slider settings', 'wr-nitro' ),
						'value'      => '5000',
						'dependency' => array(
							'element' => 'autoplay',
							'value'   => 'yes'
						),
					),
					array(
						'param_name' => 'pause',
						'heading'    => esc_html__( 'Pause on mouse hover', 'wr-nitro' ),
						'type'       => 'checkbox',
						'group'      => esc_html__( 'Slider settings', 'wr-nitro' ),
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'std'        => 'no',
						'dependency' => array(
							'element' => 'autoplay',
							'value'   => 'yes'
						),
					),
					array(
						'heading'    => esc_html__( 'Show default content', 'wr-nitro' ),
						'param_name' => 'show_content',
						'type'       => 'checkbox',
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'std'        => 'yes',
						'dependency' => array(
							'element' => 'style',
							'value'   => 'inner'
						),
					),
					array(
						'heading'    => esc_html__( 'Display settings', 'wr-nitro' ),
						'param_name' => 'sub',
						'type'       => 'sub_heading',
					),
					array(
						'param_name' => 'excerpt',
						'type'       => 'checkbox',
						'value'      => array( __( 'Display excerpt', 'wr-nitro' ) => 'yes' ),
						'std'        => 'yes',
					),
					array(
						'param_name' => 'image',
						'type'       => 'checkbox',
						'value'      => array( __( 'Display featured image', 'wr-nitro' ) => 'yes' ),
						'std'        => 'yes',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'list' ),
						),
					),
					array(
						'param_name' => 'meta',
						'type'       => 'checkbox',
						'value'      => array( __( 'Display meta post', 'wr-nitro' ) => 'yes' ),
						'std'        => 'yes',
					),
					array(
						'param_name' => 'readmore',
						'type'       => 'checkbox',
						'value'      => array( __( 'Show Read more?', 'wr-nitro' ) => 'yes' ),
						'std'        => 'yes',
						'dependency' => array(
							'element'            => 'style',
							'value_not_equal_to' => 'inner'
						),
					),
					array(
						'param_name' => 'divider',
						'type'       => 'checkbox',
						'value'      => array( __( 'Display divider?', 'wr-nitro' ) => 'yes' ),
						'std'        => 'no',
						'dependency' => array(
							'element'            => 'style',
							'value_not_equal_to' => 'zigzag'
						),
					),
					array(
						'param_name' => 'divider_color',
						'type'       => 'colorpicker',
						'heading'    => esc_html__( 'Divider color', 'wr-nitro' ),
						'value'      => '#eaeaea',
						'dependency' => array(
							'element' => 'divider',
							'value'   => 'yes',
						),
					),
					array(
						'param_name' => 'font_size',
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Heading font size (px)', 'wr-nitro' ),
						'value'      => '16',
						'edit_field_class' => 'vc_col-sm-6 vc_column mgt20',
					),
					array(
						'param_name' => 'excerpt_length',
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Excerpt length by words', 'wr-nitro'),
						'value'      => '20',
						'dependency' => array(
							'element' => 'excerpt',
							'value'   => 'yes',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column mgt20',
					),
					array(
						'param_name' => 'align',
						'heading'    => esc_html__( 'Content alignment', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Left', 'wr-nitro' )   => 'tl',
							__( 'Center', 'wr-nitro' ) => 'tc',
							__( 'Right', 'wr-nitro' )  => 'tr',
						),
					),
					array(
						'param_name' => 'content_position',
						'heading'    => esc_html__( 'Content position', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Top', 'wr-nitro' )    => 'top',
							__( 'Middle', 'wr-nitro' ) => 'middle',
							__( 'Bottom', 'wr-nitro' ) => 'bottom',
						),
						'std'        => 'bottom',
						'dependency' => array(
							'element' => 'style',
							'value'   => 'inner'
						),
					),
					array(
						'param_name'  => 'el_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
					),
					array(
						'param_name'       => 'blog_list_custom_id',
						'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new Counter Up element.
		vc_map(
			array(
				'name'     => esc_html__( 'Counter Up', 'wr-nitro' ),
				'base'     => 'nitro_counter_up',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'icon'     => 'fa fa-sort-numeric-asc',
				'params'   => array(
					array(
						'param_name' => 'horizontal',
						'heading'    => esc_html__( 'Enable Horizontal Layout', 'wr-nitro' ),
						'type'       => 'checkbox',
						'value'      => array(
							'Yes' => 'true',
						),
						'edit_field_class' => 'vc_col-sm-6 mgb20',
					),
					array(
						'param_name' => 'icon',
						'heading'    => esc_html__( 'Enable Icon', 'wr-nitro' ),
						'type'       => 'checkbox',
						'value'      => array(
							'Yes' => 'true',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column mgb20',
					),
					array(
						'type'             => 'iconpicker',
						'heading'          => esc_html__( 'Icon', 'wr-nitro' ),
						'param_name'       => 'icon_fontawesome',
						'value'            => 'fa fa-info-circle',
						'settings'         => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 200,
						),
						'edit_field_class' => 'vc_col-sm-4 vc_column mgb20',
						'description'      => esc_html__( 'Select Icon From Library.', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'icon',
							'value'   => 'true',
						),
					),
					array(
						'param_name'       => 'icon_fontsize',
						'heading'          => esc_html__( 'Icon Font Size (Number only)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-4 vc_column mgb20',
						'dependency' => array(
							'element' => 'icon',
							'value'   => 'true',
						),
						'value'              => 14,
						'edit_field_class' => 'vc_col-sm-4 vc_column mgb20',
					),
					array(
						'param_name'       => 'icon_color',
						'heading'          => esc_html__( 'Icon Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'value'            => '#ff4064',
						'edit_field_class' => 'vc_col-sm-4 vc_column mgb20',
						'dependency' => array(
							'element' => 'icon',
							'value'   => 'true',
						),
						'edit_field_class' => 'vc_col-sm-4 vc_column mgb20',
					),
					array(
						'param_name'       => 'title',
						'heading'          => esc_html__( 'Title', 'wr-nitro' ),
						'value'            => esc_html__( 'Sample Title', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-12 mgb20',
						'admin_label'      => true,
					),
					array(
						'param_name'       => 'title_fontsize',
						'heading'          => esc_html__( 'Title Font Size (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 26,
						'edit_field_class' => 'vc_col-sm-6 mgb20',
					),
					array(
						'param_name'       => 'title_color',
						'heading'          => esc_html__( 'Title Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'value'            => '#646464',
						'edit_field_class' => 'vc_col-sm-6 mgb20',
					),
					array(
						'param_name'       => 'number',
						'heading'          => esc_html__( 'Number', 'wr-nitro' ),
						'description'      => esc_html__( 'Number only', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 999,
						'edit_field_class' => 'vc_col-sm-12 mgb20',
						'admin_label'      => true,
					),
					array(
						'param_name'       => 'number_fontsize',
						'heading'          => esc_html__( 'Number Font Size  (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 46,
						'edit_field_class' => 'vc_col-sm-6 mgb20',
					),
					array(
						'param_name'       => 'number_color',
						'heading'          => esc_html__( 'Number Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'value'            => '#646464',
						'edit_field_class' => 'vc_col-sm-6 mgb20',
					),
					array(
						'param_name'       => 'description',
						'heading'          => esc_html__( 'Description', 'wr-nitro' ),
						'description'      => esc_html__( 'Display info of Counter', 'wr-nitro' ),
						'value'            => esc_html__( 'Sample description', 'wr-nitro' ),
						'type'             => 'textarea',
						'edit_field_class' => 'vc_col-sm-12 mgb20',
					),
					array(
						'param_name'       => 'description_fontsize',
						'heading'          => esc_html__( 'Description Font Size  (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 10,
						'edit_field_class' => 'vc_col-sm-6 mgb20',
					),
					array(
						'param_name'       => 'description_color',
						'heading'          => esc_html__( 'Description Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'value'            => '#cacaca',
						'edit_field_class' => 'vc_col-sm-6 mgb20',
					),
				)
			)
		);

		// Map new Google Maps element.
		vc_map(
			array(
				'base'            => 'nitro_google_map',
				'name'            => esc_html__( 'Google Maps', 'wr-nitro' ),
				'icon'            => 'fa fa-map-marker',
				'category'        => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'content_element' => true,
				'params'          => array(
					array(
						'param_name'       => 'api',
						'heading'          => esc_html__( 'Maps API', 'wr-nitro' ),
						'description' => sprintf( __( 'Get a <a target="_blank" rel="noopener noreferrer" href="%s">API key</a></a>', 'wr-nitro' ), 'https://developers.google.com/maps/documentation/javascript/get-api-key' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
					),
					array(
						'param_name'       => 'address',
						'heading'          => esc_html__( 'Address', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
						'admin_label'      => true,
					),
					array(
						'param_name'       => 'z',
						'heading'          => esc_html__( 'Zoom Level', 'wr-nitro' ),
						'description'      => esc_html__( 'Between 0-20', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 14,
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name'       => 'lat',
						'heading'          => esc_html__( 'Latitude', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name'       => 'lon',
						'heading'          => esc_html__( 'Longitude', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name'       => 'w',
						'heading'          => esc_html__( 'Width', 'wr-nitro' ),
						'description'      => esc_html__( 'Numeric value only, Unit is Pixel.', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name'       => 'h',
						'heading'          => esc_html__( 'Height', 'wr-nitro' ),
						'description'      => esc_html__( 'Numeric value only, Unit is Pixel.', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'marker',
						'type'       => 'checkbox',
						'value'      => array(
							__( 'Map Marker', 'wr-nitro' ) => 'true',
						),
					),
					array(
						'param_name'  => 'markerimage',
						'heading'     => esc_html__( 'Marker Image', 'wr-nitro' ),
						'description' => esc_html__( 'Change default Marker.', 'wr-nitro' ),
						'type'        => 'attach_image',
						'dependency' => array(
							'element' => 'marker',
							'value'   => array( 'true' ),
						),
					),
					array(
						'param_name'  => 'infowindow',
						'heading'     => esc_html__( 'Info Box', 'wr-nitro' ),
						'description' => esc_html__( 'Strong, br are accepted.', 'wr-nitro' ),
						'type'        => 'textfield',
						'dependency' => array(
							'element' => 'marker',
							'value'   => array( 'true' ),
						),
					),
					array(
						'param_name' => 'infowindowdefault',
						'type'       => 'checkbox',
						'value'      => array( __( 'Always show info box', 'wr-nitro' ) => 'true' ),
						'dependency' => array(
							'element' => 'marker',
							'value'   => array( 'true' ),
						),
					),
					array(
						'param_name' => 'traffic',
						'type'       => 'checkbox',
						'value'      => array( __( 'Show Traffic', 'wr-nitro' ) => 'true' )
					),
					array(
						'param_name' => 'draggable',
						'type'       => 'checkbox',
						'value'      => array( __( 'Draggable', 'wr-nitro' ) => 'true' ),
						'dependency' => array(
							'element' => 'marker',
							'value'   => array( 'true' ),
						),
					),
					array(
						'param_name' => 'hidecontrols',
						'type'       => 'checkbox',
						'value'      => array( __( 'Hide Control', 'wr-nitro' ) => 'true' ),
					),
					array(
						'param_name' => 'scrollwheel',
						'type'       => 'checkbox',
						'value'      => array( __( 'Scrollwheel zooming', 'wr-nitro' ) => 'true' )
					),
					array(
						'param_name' => 'maptype',
						'heading'    => esc_html__( 'Map Type', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'ROADMAP', 'wr-nitro' ) => 'ROADMAP',
							__( 'SATELLITE', 'wr-nitro' ) => 'SATELLITE',
							__( 'HYBRID', 'wr-nitro' ) => 'HYBRID',
							__( 'TERRAIN', 'wr-nitro' ) => 'TERRAIN',
						),
					),
					array(
						'param_name' => 'mapstype',
						'heading'    => esc_html__( 'Map style', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'None', 'wr-nitro' ) => '',
							__( 'Subtle Grayscale', 'wr-nitro' ) => 'grayscale',
							__( 'Blue water', 'wr-nitro' ) => 'blue_water',
							__( 'Pale Dawn', 'wr-nitro' ) => 'pale_dawn',
							__( 'Shades of Grey', 'wr-nitro' ) => 'shades_of_grey',
						),
					),
				)
			)
		);

		// Map new Testimonial element.
		vc_map(
			array(
				'base'   => 'nitro_testimonial',
				'name'   => esc_html__( 'Testimonial', 'wr-nitro' ),
				'icon'   => 'fa fa-comments',
				'params' => array(
					array(
						'param_name' => 'style',
						'heading'    => esc_html__( 'Style', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Style 1', 'wr-nitro' ) => 'style-1',
							__( 'Style 2', 'wr-nitro' ) => 'style-2',
						),
						'edit_field_class' => 'vc_col-sm-6 mgt15',
					),
					array(
						'param_name'  => 'columns',
						'heading'     => esc_html__( 'Number of columns displayed', 'wr-nitro' ),
						'type'        => 'dropdown',
						'value'       => array(
							__( '1 column', 'wr-nitro' )  => '1',
							__( '2 columns', 'wr-nitro' ) => '2',
							__( '3 columns', 'wr-nitro' ) => '3',
							__( '4 columns', 'wr-nitro' ) => '4',
							__( '5 columns', 'wr-nitro' ) => '5',
							__( '6 columns', 'wr-nitro' ) => '6',
							__( '7 columns', 'wr-nitro' ) => '7',
							__( '8 columns', 'wr-nitro' ) => '8',
						),
						'std'         => '3',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'slider',
						'heading'    => esc_html__( 'Enable carousel', 'wr-nitro' ),
						'type'       => 'checkbox',
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'std'        => 'no',
					),
					array(
						'param_name' => 'masonry',
						'heading'    => esc_html__( 'Enable Masonry Layout', 'wr-nitro' ),
						'type'       => 'checkbox',
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'std'        => 'no',
					),
					array(
						'param_name' => 'navigation',
						'heading'    => esc_html__( 'Enable Navigation', 'wr-nitro' ),
						'type'       => 'checkbox',
						'group'      => esc_html__( 'Slider settings', 'wr-nitro' ),
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'std'        => 'no',
						'dependency' => array(
							'element' => 'slider',
							'value'   => 'yes'
						),
					),
					array(
						'param_name' => 'pagination',
						'heading'    => esc_html__( 'Enable Pagination', 'wr-nitro' ),
						'type'       => 'checkbox',
						'group'      => esc_html__( 'Slider settings', 'wr-nitro' ),
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'std'        => 'no',
						'dependency' => array(
							'element' => 'slider',
							'value'   => 'yes'
						),
					),
					array(
						'param_name' => 'autoplay',
						'heading'    => esc_html__( 'Autoplay', 'wr-nitro' ),
						'type'       => 'checkbox',
						'group'      => esc_html__( 'Slider settings', 'wr-nitro' ),
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'std'        => 'no',
						'dependency' => array(
							'element' => 'slider',
							'value'   => 'yes'
						),
					),
					array(
						'param_name' => 'autotime',
						'heading'    => esc_html__( 'Autoplay interval timeout', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Slider settings', 'wr-nitro' ),
						'value'      => '5000',
						'dependency' => array(
							'element' => 'autoplay',
							'value'   => 'yes'
						),
					),
					array(
						'param_name' => 'pause',
						'heading'    => esc_html__( 'Pause on mouse hover', 'wr-nitro' ),
						'type'       => 'checkbox',
						'group'      => esc_html__( 'Slider settings', 'wr-nitro' ),
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
						'std'        => 'no',
						'dependency' => array(
							'element' => 'autoplay',
							'value'   => 'yes'
						),
					),
					array(
						'param_name' => 'align',
						'heading'    => esc_html__( 'Content alignment', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Left', 'wr-nitro' )   => 'tl',
							__( 'Center', 'wr-nitro' ) => 'tc',
							__( 'Right', 'wr-nitro' )  => 'tr',
						),
						'dependency' => array(
							'element' => 'style',
							'value'   => 'style-1',
						),
					),
					array(
						'param_name' => 's1_position',
						'heading'    => esc_html__( 'Avatar position', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Top', 'wr-nitro' )    => 'top',
							__( 'Left', 'wr-nitro' )   => 'left',
							__( 'Right', 'wr-nitro' )  => 'right',
							__( 'Bottom', 'wr-nitro' ) => 'bottom',
						),
						'dependency' => array(
							'element' => 'style',
							'value'   => 'style-1',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column mgt10',
					),
					array(
						'param_name' => 's2_position',
						'heading'    => esc_html__( 'Avatar position', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Top', 'wr-nitro' )    => 'top',
							__( 'Bottom', 'wr-nitro' ) => 'bottom',
						),
						'dependency' => array(
							'element' => 'style',
							'value'   => 'style-2',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column mgt10',
					),
					array(
						'param_name' => 'avt_shape',
						'heading'    => esc_html__( 'Avatar style', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Square', 'wr-nitro' ) => 'square',
							__( 'Circle', 'wr-nitro' ) => 'circle',
						),
						'std'        => 'circle',
						'edit_field_class' => 'vc_col-sm-6 vc_column mgt10',
					),
					array(
						'heading'    => esc_html__( 'Testimonial content', 'wr-nitro' ),
						'type'       => 'param_group',
						'param_name' => 'testimonials_content',
						'params'     => array(
							array(
								'param_name' => 'avatar',
								'heading'    => esc_html__( 'Avatar', 'wr-nitro' ),
								'type'       => 'attach_image',
							),
							array(
								'param_name' => 'testimonial',
								'heading'    => esc_html__( 'Testimonial', 'wr-nitro' ),
								'type'       => 'textarea',
							),
							array(
								'param_name' => 'name',
								'heading'    => esc_html__( 'Name', 'wr-nitro' ),
								'type'       => 'textfield',
							),
							array(
								'param_name' => 'job',
								'heading'    => esc_html__( 'Job', 'wr-nitro' ),
								'type'       => 'textfield',
							),
						),
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
					),
				)
			)
		);

		// Map new Subscription Form element.
		vc_map(
			array(
				'base'     => 'nitro_subscribe_form',
				'name'     => esc_html__( 'Subscription Form', 'wr-nitro' ),
				'icon'     => 'fa fa-list-alt',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name' => 'link',
						'heading'    => esc_html__( 'Link To Mailchimp Form', 'wr-nitro' ),
						'type'       => 'textfield',
					),
					array(
						'param_name' => 'placeholder',
						'heading'    => esc_html__( 'Email Input Placehoder', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => esc_html__( 'Enter your email', 'wr-nitro' ),
					),
					array(
						'param_name' => 'form_width',
						'heading'    => esc_html__( 'Form Width', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => '300px',
						'edit_field_class' => 'vc_col-sm-6 mgb20 mgt20'
					),
					array(
						'param_name' => 'form_height',
						'heading'    => esc_html__( 'Form Height', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => '50px',
						'edit_field_class' => 'vc_col-sm-6 mgb20 mgt20'
					),
					array(
						'param_name' => 'submit_button',
						'heading'    => esc_html__( 'Submit Button Type', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Icon', 'wr-nitro' )   => 'icon-submit',
							__( 'Button', 'wr-nitro' ) => 'button-submit',
						),
						'std'        => 'button-submit'
					),
					array(
						'param_name' => 'icon_size',
						'heading'    => esc_html__( 'Icon Font Size (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => '16',
						'dependency' => array(
							'element' => 'submit_button',
							'value'   => 'icon-submit',
						),
						'edit_field_class' => 'vc_col-sm-4 vc_column mgt20'
					),
					array(
						'param_name' => 'icon_position',
						'heading'    => esc_html__( 'Icon Position', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Inside Form', 'wr-nitro' )  => 'inside',
							__( 'Outside Form', 'wr-nitro' ) => 'outside',
						),
						'dependency' => array(
							'element' => 'submit_button',
							'value'   => 'icon-submit',
						),
						'edit_field_class' => 'vc_col-sm-4 vc_column mgt20'
					),
					array(
						'param_name' => 'icon_color',
						'heading'    => esc_html__( 'Icon Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#333',
						'dependency' => array(
							'element' => 'submit_button',
							'value'   => 'icon-submit',
						),
						'edit_field_class' => 'vc_col-sm-4 vc_column mgt20'
					),
					array(
						'param_name' => 'button_text',
						'heading'    => esc_html__( 'Button Text', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => esc_html__( 'Subscribe', 'wr-nitro' ),
						'dependency' => array(
							'element' => 'submit_button',
							'value'   => 'button-submit',
						),
					),
					array(
						'param_name' => 'text_size',
						'heading'    => esc_html__( 'Button Text Size (Number only)', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => '14',
						'dependency' => array(
							'element' => 'submit_button',
							'value'   => 'button-submit',
						),
						'edit_field_class' => 'vc_col-sm-4 vc_column mgt20'
					),
					array(
						'param_name' => 'bg_color',
						'heading'    => esc_html__( 'Background Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#333',
						'dependency' => array(
							'element' => 'submit_button',
							'value'   => 'button-submit',
						),
						'edit_field_class' => 'vc_col-sm-4 vc_column mgt20'
					),
					array(
						'param_name' => 'text_color',
						'heading'    => esc_html__( 'Button Text Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#fff',
						'dependency' => array(
							'element' => 'submit_button',
							'value'   => 'button-submit',
						),
						'edit_field_class' => 'vc_col-sm-4 vc_column mgt20'
					),
					array(
						'param_name' => 'border_width',
						'heading'    => esc_html__( 'Border Width (eg: 1px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => '1px',
						'edit_field_class' => 'vc_col-sm-4 vc_column mgt20'
					),
					array(
						'param_name' => 'border_radius',
						'heading'    => esc_html__( 'Border Radius (Number only)', 'wr-nitro' ),
						'type'       => 'textfield',
						'edit_field_class' => 'vc_col-sm-4 vc_column mgt20'
					),
					array(
						'param_name' => 'border_color',
						'heading'    => esc_html__( 'Border Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#ebebeb',
						'edit_field_class' => 'vc_col-sm-4 vc_column mgt20'
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
					),
					array(
						'param_name'       => 'subscribe_form_custom_id',
						'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new Banner element.
		vc_map(
			array(
				'base'     => 'nitro_banner',
				'name'     => esc_html__( 'Banner', 'wr-nitro' ),
				'icon'     => 'fa fa-picture-o',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name' => 'hover_effects',
						'heading'    => esc_html__( 'Hover Effect', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Effect 1', 'wr-nitro' )  => 'style-1',
							__( 'Effect 2', 'wr-nitro' )  => 'style-2',
							__( 'Effect 3', 'wr-nitro' )  => 'style-3',
							__( 'Effect 4', 'wr-nitro' )  => 'style-4',
							__( 'Effect 5', 'wr-nitro' )  => 'style-5',
							__( 'Effect 6', 'wr-nitro' )  => 'style-6',
							__( 'Effect 7', 'wr-nitro' )  => 'style-7',
							__( 'Effect 8', 'wr-nitro' )  => 'style-8',
							__( 'Effect 9', 'wr-nitro' )  => 'style-9',
							__( 'Effect 10', 'wr-nitro' ) => 'style-10',
							__( 'Effect 11', 'wr-nitro' ) => 'style-11',
							__( 'Effect 12', 'wr-nitro' ) => 'style-12',
							__( 'Effect 13', 'wr-nitro' ) => 'style-13',
						),
						'admin_label' => true,
					),
					array(
						'param_name' => 'image',
						'heading'    => esc_html__( 'Image', 'wr-nitro' ),
						'type'       => 'attach_image',
					),
					array(
						'param_name' => 'image_alignment',
						'heading'    => esc_html__( 'Banner Alignment', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Left', 'wr-nitro' )   => 'tl',
							__( 'Center', 'wr-nitro' ) => 'tc',
							__( 'Right', 'wr-nitro' )  => 'tr',
						),
					),
					array(
						'param_name' => 'image_content',
						'heading'    => esc_html__( 'Content Inside Banner', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Image', 'wr-nitro' ) => 'content_image',
							__( 'Text', 'wr-nitro' )  => 'content_text',
						),
					),
					array(
						'param_name' => 'image_inner',
						'heading'    => esc_html__( 'Inner Image', 'wr-nitro' ),
						'type'       => 'attach_image',
						'dependency' => array(
							'element' => 'image_content',
							'value'   => 'content_image',
						),
					),
					array(
						'param_name' => 'text_heading',
						'heading'    => esc_html__( 'Heading Text', 'wr-nitro' ),
						'type'       => 'textarea',
						'dependency' => array(
							'element' => 'image_content',
							'value'   => 'content_text',
						),
					),
					array(
						'param_name' => 'text_heading_size',
						'heading'    => esc_html__( 'Heading Font Size (Number Only)', 'wr-nitro' ),
						'type'       => 'textfield',
						'dependency' => array(
							'element' => 'image_content',
							'value'   => 'content_text',
						),
					),
					array(
						'param_name' => 'text_description',
						'heading'    => esc_html__( 'Description Text', 'wr-nitro' ),
						'type'       => 'textarea',
						'dependency' => array(
							'element' => 'image_content',
							'value'   => 'content_text',
						),
					),
					array(
						'param_name' => 'text_description_size',
						'heading'    => esc_html__( 'Description Font Size (Number Only)', 'wr-nitro' ),
						'type'       => 'textfield',
						'dependency' => array(
							'element' => 'image_content',
							'value'   => 'content_text',
						),
					),
					array(
						'param_name' => 'text_color',
						'heading'    => esc_html__( 'Text Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#fff',
						'dependency' => array(
							'element' => 'image_content',
							'value'   => 'content_text',
						),
					),
					array(
						'param_name' => 'mask_color',
						'heading'    => esc_html__( 'Mask Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => 'rgba(0,0,0,.5)',
						'dependency' => array(
							'element' => 'hover_effects',
							'value'   => 'style-13',
						),
					),
					array(
						'param_name' => 'link',
						'heading'    => esc_html__( 'Link To', 'wr-nitro' ),
						'type'       => 'textfield',
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
					),
					array(
						'param_name'       => 'banner_custom_id',
						'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new Dropcap element.
		vc_map(
			array(
				'base'     => 'nitro_dropcaps',
				'name'     => esc_html__( 'Drop Caps', 'wr-nitro' ),
				'icon'     => 'fa fa-text-height',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name' => 'dropcaps',
						'heading'    => esc_html__( 'Dropcaps Letter', 'wr-nitro' ),
						'type'       => 'textfield',
					),
					array(
						'param_name'  => 'text',
						'heading'     => esc_html__( 'Content', 'wr-nitro' ),
						'type'        => 'textarea',
						'admin_label' => true,
					),
					array(
						'param_name' => 'color',
						'heading'    => esc_html__( 'Dropcaps Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#323232',
					),
					array(
						'param_name' => 'style',
						'heading'    => esc_html__( 'Style', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'No Background', 'wr-nitro' )  => 'no-bg',
							__( 'Square Outline', 'wr-nitro' ) => 'square-outline',
							__( 'Square Solid', 'wr-nitro' )   => 'square-solid',
							__( 'Circle Outline', 'wr-nitro' ) => 'circle-outline',
							__( 'Circle Solid', 'wr-nitro' )   => 'circle-solid',
						),
					),
					array(
						'param_name' => 'bg_color',
						'heading'    => esc_html__( 'Background Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#d4a769',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'square-solid', 'circle-solid' ),
						),
					),
					array(
						'param_name' => 'border_color',
						'heading'    => esc_html__( 'Border Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#d4a769',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'square-outline', 'circle-outline' ),
						),
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
					),
					array(
						'param_name'       => 'dropcaps_custom_id',
						'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		if ( function_exists( 'is_' . 'plugin' . '_active' ) && call_user_func( 'is_' . 'plugin' . '_active', 'woocommerce/woocommerce.php' ) ) {

			$grid_columns = array(
				__( '1 Column', 'wr-nitro' )   => '1',
				__( '2 Columns', 'wr-nitro' )  => '2',
				__( '3 Columns', 'wr-nitro' )  => '3',
				__( '4 Columns', 'wr-nitro' )  => '4',
				__( '5 Columns', 'wr-nitro' )  => '5',
				__( '6 Columns', 'wr-nitro' )  => '6',
				__( '7 Columns', 'wr-nitro' )  => '7',
				__( '8 Columns', 'wr-nitro' )  => '8',
				__( '9 Columns', 'wr-nitro' )  => '9',
				__( '10 Columns', 'wr-nitro' ) => '10',
			);
			$item_styles = array(
				__( 'Style 1', 'wr-nitro' ) => '1',
				__( 'Style 2', 'wr-nitro' ) => '2',
				__( 'Style 3', 'wr-nitro' ) => '3',
				__( 'Style 4', 'wr-nitro' ) => '4',
				__( 'Style 5', 'wr-nitro' ) => '5',
				__( 'Style 6', 'wr-nitro' ) => '6',
			);
			$hover_styles = array(
				__( 'Default', 'wr-nitro' )         => 'default',
				__( 'Scale Image', 'wr-nitro' )     => 'scale',
				__( 'Mask Overlay', 'wr-nitro' )    => 'mask',
				__( '2-Image Preview', 'wr-nitro' ) => 'flip-back',
			);
			$transition_effects = array(
				__( 'Fade In', 'wr-nitro' )           => 'fade',
				__( 'Slide From Left', 'wr-nitro' )   => 'slide-from-left',
				__( 'Slide From Right', 'wr-nitro' )  => 'slide-from-right',
				__( 'Slide From Top', 'wr-nitro' )    => 'slide-from-top',
				__( 'Slide From Bottom', 'wr-nitro' ) => 'slide-from-bottom',
				__( 'Zoom In', 'wr-nitro' )           => 'zoom-in',
				__( 'Zoom Out', 'wr-nitro' )          => 'zoom-out',
				__( 'Flip', 'wr-nitro' )              => 'flip',
			);

			// Map new Product Package element.
			vc_map(
				array(
					'base'     => 'nitro_product_package',
					'name'     => esc_html__( 'Product Package', 'wr-nitro' ),
					'icon'     => 'fa fa-shopping-cart',
					'category' => esc_html__( 'WooCommerce', 'wr-nitro' ),
					'params'   => array(
						array(
							'type'        => 'autocomplete',
							'heading'     => esc_html__( 'Products', 'wr-nitro' ),
							'description' => esc_html__( 'Input product ID or product SKU or product title to see suggestions', 'wr-nitro' ),
							'param_name'  => 'ids',
							'settings' => array(
								'multiple'      => true,
								'sortable'      => true,
								'unique_values' => true,
							),
							'save_always' => true,
							'admin_label' => true
						),
						array(
							'type'       => 'hidden',
							'param_name' => 'skus',
						),
						array(
							'param_name'       => 'extra_class',
							'heading'          => esc_html__( 'Extra Class Name', 'wr-nitro' ),
							'description'      => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
							'type'             => 'textfield',
						),
					)
				)
			);

			// Get all terms of woocommerce
			$product_cat = array();
			$terms = get_terms( 'product_cat' );

			if ( $terms && ! isset( $terms->errors ) ) {
				$list_category_children_along = array();
				foreach ( $terms as $key => $val ) {
					if ( $val->parent == 0 ) {
						$val->level = 0;
						$list_category_children_along[] = $val;

						WR_Nitro_Helper::set_term_recursive( $val, $list_category_children_along, $terms );
					}
				}
				foreach ( $list_category_children_along as $key => $value ) {
					$product_cat[ str_repeat( '|--', $value->level ) . $value->name ] = $value->term_id;
				}
			}
			vc_map(
				array(
					'name'     => esc_html__( 'Product Menu', 'wr-nitro' ),
					'category' => esc_html__( 'WooCommerce', 'wr-nitro' ),
					'icon'     => 'fa fa-shopping-cart',
					'base'     => 'nitro_product_menu',
					'params'   => array(
						array(
							'heading'     => esc_html__( 'Menu Title', 'wr-nitro' ),
							'type'        => 'textfield',
							'param_name'  => 'title',
							'admin_label' => true
						),
						array(
							'heading'     => esc_html__( 'Sub-menu Title', 'wr-nitro' ),
							'type'        => 'textfield',
							'param_name'  => 'sub_title',
							'admin_label' => true
						),
						array(
							'heading'     => esc_html__( 'Product Category', 'wr-nitro' ),
							'param_name'  => 'cat_id',
							'type'        => 'dropdown',
							'value'       => $product_cat,
							'admin_label' => true
						),
						array(
							'heading'     => esc_html__( 'Per Page', 'wr-nitro' ),
							'description' => esc_html__( 'How much items per page to show (-1 to show all products)', 'wr-nitro' ),
							'type'        => 'textfield',
							'param_name'  => 'limit',
						),
						array(
							'heading'    => esc_html__( 'Style', 'wr-nitro' ),
							'param_name' => 'style',
							'type'       => 'dropdown',
							'value'      => array(
								__( 'Default', 'wr-nitro' )   => '',
								__( 'Has Image', 'wr-nitro' ) => 'has-image',
							),
						),
						array(
							'heading'    => esc_html__( 'Order By', 'wr-nitro' ),
							'type'       => 'dropdown',
							'param_name' => 'orderby',
							'value'      => $order_by_values,
							'std'        => 'date',
						),
						array(
							'heading'    => esc_html__( 'Order By', 'wr-nitro' ),
							'type'       => 'dropdown',
							'param_name' => 'order',
							'value'      => $order_way_values,
							'std'        => 'ASC',
						),
						array(
							'param_name'  => 'extra_class',
							'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
							'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
							'type'        => 'textfield',
						),
					)
				)
			);

			// Map new Product Category element.
			vc_map(
				array(
					'name'     => esc_html__( 'Product Category', 'wr-nitro' ),
					'base'     => 'nitro_product_category',
					'category' => esc_html__( 'WooCommerce', 'wr-nitro' ),
					'icon'     => 'fa fa-shopping-cart',
					'params'   => array(
						array(
							'heading'     => esc_html__( 'Product Category', 'wr-nitro' ),
							'param_name'  => 'cat_id',
							'type'        => 'dropdown',
							'value'       => $product_cat,
							'admin_label' => true
						),
						array(
							'param_name' => 'show_count',
							'heading'    => esc_html__( 'Show count?', 'wr-nitro' ),
							'type'       => 'checkbox',
							'value'      => 'true',
						),
						array(
							'param_name'  => 'extra_class',
							'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
							'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
							'type'        => 'textfield',
						),
					)
				)
			);

			// Map new Product Attributes element.
			$attributes_tax = function_exists( 'wc_get_attribute_taxonomies' ) ? wc_get_attribute_taxonomies() : array();
			$attributes = array('');
			foreach ( $attributes_tax as $attribute ) {
				$attributes[ $attribute->attribute_label ] = $attribute->attribute_name;
			}

			vc_map(
				array(
					'name' => esc_html__( 'Product Attribute', 'wr-nitro' ),
					'base' => 'nitro_product_attribute',
					'icon' => 'fa fa-shopping-cart',
					'category' => esc_html__( 'WooCommerce', 'wr-nitro' ),
					'description' => esc_html__( 'List products with an attribute shortcode', 'wr-nitro' ),
					'params' => array(
						array(
							'type'        => 'textfield',
							'heading'     => esc_html__( 'Per page', 'wr-nitro' ),
							'value'       => 12,
							'param_name'  => 'per_page',
							'save_always' => true,
							'description' => esc_html__( 'How much items per page to show', 'wr-nitro' ),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => esc_html__( 'Columns', 'wr-nitro' ),
							'value'       => $grid_columns,
							'std'         => '4',
							'param_name'  => 'columns',
							'save_always' => true,
							'description' => esc_html__( 'How much columns grid ?', 'wr-nitro' ),
							'dependency' => array(
								'element'   => 'list_style',
								'value_not_equal_to' => 'list',
							),
						),
						array(
							'param_name' => 'slider',
							'heading'    => esc_html__( 'Enable Slider', 'wr-nitro' ),
							'type'       => 'checkbox',
							'dependency'  => array(
								'element' => 'list_style',
								'value'   => array( 'grid', 'grid-boxed' ),
							),
						),
						array(
							'param_name' => 'auto_play',
							'heading'    => esc_html__( 'Enable Autoplay', 'wr-nitro' ),
							'group'      => esc_html__( 'Slider Settings', 'wr-nitro' ),
							'type'       => 'checkbox',
							'dependency' => array(
								'element'   => 'slider',
								'not_empty' => true
							),
						),
						array(
							'param_name' => 'navigation',
							'heading'    => esc_html__( 'Enable Navigation', 'wr-nitro' ),
							'group'      => esc_html__( 'Slider Settings', 'wr-nitro' ),
							'type'       => 'checkbox',
							'dependency' => array(
								'element'   => 'slider',
								'not_empty' => true
							),
						),
						array(
							'param_name' => 'pagination',
							'heading'    => esc_html__( 'Enable Pagination', 'wr-nitro' ),
							'group'      => esc_html__( 'Slider Settings', 'wr-nitro' ),
							'type'       => 'checkbox',
							'dependency' => array(
								'element'   => 'slider',
								'not_empty' => true
							),
						),
						array(
							'param_name' => 'gutter_width',
							'heading'    => esc_html__( 'Gutter Width (px)', 'wr-nitro' ),
							'group'      => esc_html__( 'Slider Settings', 'wr-nitro' ),
							'type'       => 'textfield',
							'value'      => '30',
							'dependency' => array(
								'element'   => 'slider',
								'not_empty' => true
							),
						),
						array(
							'param_name' => 'list_style',
							'heading'    => esc_html__( 'Layout', 'wr-nitro' ),
							'type'       => 'dropdown',
							'value'      => array(
								__( 'Grid', 'wr-nitro' ) 		   => 'grid',
								__( 'Bordered Grid', 'wr-nitro' ) => 'grid-boxed',
								__( 'Masonry', 'wr-nitro' ) 	   => 'masonry',
								__( 'List', 'wr-nitro' )          => 'list',
							),
							'std'         => 'grid',
							'save_always' => true,
						),
						array(
							'param_name' => 'style',
							'heading'    => esc_html__( 'Item Style', 'wr-nitro' ),
							'type'       => 'dropdown',
							'value'      => $item_styles,
							'dependency' => array(
								'element' => 'list_style',
								'value'   => array( 'grid', 'grid-boxed', 'masonry' ),
							),
							'std'         => '1',
							'save_always' => true,
						),
						array(
							'param_name'  => 'hover_style',
							'heading'     => esc_html__( 'Hover style', 'wr-nitro' ),
							'type'        => 'dropdown',
							'value'       => $hover_styles,
							'std'         => 'default',
							'save_always' => true,
							'dependency'  => array(
								'element' => 'list_style',
								'value'   => array( 'grid', 'grid-boxed', 'masonry', 'list' ),
							),
						),
						array(
							'param_name' => 'mask_overlay_color',
							'heading'    => esc_html__( 'Mask overlay color', 'wr-nitro' ),
							'type'       => 'colorpicker',
							'value'      => 'rgba(0, 0, 0, 0.7)',
							'dependency' => array(
								'element' => 'hover_style',
								'value'	  => 'mask',
							),
						),
						array(
							'param_name' => 'transition_effects',
							'heading'    => esc_html__( 'Transition effects', 'wr-nitro' ),
							'type'       => 'dropdown',
							'value'      => $transition_effects,
							'dependency' => array(
								'element'   => 'hover_style',
								'value' 	=> 'flip-back',
							),
							'std'         => 'fade',
							'save_always' => true,
						),
						array(
							'type'        => 'dropdown',
							'heading'     => esc_html__( 'Order by', 'wr-nitro' ),
							'param_name'  => 'orderby',
							'value'       => $order_by_values,
							'std'         => 'date',
							'save_always' => true,
							'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'wr-nitro' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank" rel="noopener noreferrer">WordPress codex page</a>' ),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => esc_html__( 'Sort order', 'wr-nitro' ),
							'param_name'  => 'order',
							'value'       => $order_way_values,
							'std'         => 'ASC',
							'save_always' => true,
							'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'wr-nitro' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank" rel="noopener noreferrer">WordPress codex page</a>' ),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => esc_html__( 'Attribute', 'wr-nitro' ),
							'param_name'  => 'attribute',
							'value'       => $attributes,
							'save_always' => true,
							'description' => esc_html__( 'List of product taxonomy attribute', 'wr-nitro' ),
						),
						array(
							'type'        => 'checkbox',
							'heading'     => esc_html__( 'Filter', 'wr-nitro' ),
							'param_name'  => 'filter',
							'value'       => array( 'empty' => 'empty' ),
							'save_always' => true,
							'description' => esc_html__( 'Taxonomy values', 'wr-nitro' ),
							'dependency'  => array(
								'callback'  => 'vcWoocommerceProductAttributeFilterDependencyCallback',
							),
						),
						array(
							'param_name'  => 'extra_class',
							'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
							'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
							'type'        => 'textfield',
						),
						array(
							'param_name'       => 'product_attribute_custom_id',
							'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
							'type'             => 'textfield',
							'value'            => 22334455,
							'edit_field_class' => 'hidden',
						),
					),
				)
			);

			// Map new Product Categories element.
			vc_map(
				array(
					'name'     => esc_html__( 'Product Categories', 'wr-nitro' ),
					'base'     => 'nitro_product_categories',
					'category' => esc_html__( 'WooCommerce', 'wr-nitro' ),
					'icon'     => 'fa fa-shopping-cart',
					'params'   => array(
						array(
							'param_name'  => 'title',
							'heading'     => esc_html__( 'Title', 'wr-nitro' ),
							'type'        => 'textfield',
							'admin_label' => true,
						),
						array(
							'param_name' => 'type',
							'heading'    => esc_html__( 'Style', 'wr-nitro' ),
							'type'       => 'dropdown',
							'value'      => array(
								__( 'Alway expand', 'wr-nitro' ) => '1',
								__( 'Expand on mouse click', 'wr-nitro' ) => '2',
							),
							'dependency' => array(
								'element'   => 'title',
								'not_empty' => true,
							),
						),
						array(
							'param_name'  => 'exclude',
							'heading'     => esc_html__( 'Exclude', 'wr-nitro' ),
							'description' => esc_html__( 'Enter category id to exclude (Note: separate values by commas ",").', 'wr-nitro' ),
							'type'        => 'textfield',
						),
						array(
							'type'       => 'checkbox',
							'heading'    => esc_html__( 'Enable Thumbnail ?', 'wr-nitro' ),
							'param_name' => 'thumb'
						),
						array(
							'param_name'  => 'extra_class',
							'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
							'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
							'type'        => 'textfield',
						),
					)
				)
			);

			// Map new Single Product element.
			vc_map(
				array(
					'name'     => esc_html__( 'Add To Cart', 'wr-nitro' ),
					'base'     => 'nitro_product_button',
					'icon'     => 'fa fa-shopping-cart',
					'category' => esc_html__( 'WooCommerce', 'wr-nitro' ),
					'params'   => array(
						array(
							'param_name'  => 'button_style',
							'heading'     => esc_html__( 'Style', 'wr-nitro' ),
							'type'        => 'dropdown',
							'admin_label' => true,
							'value'       => array(
								__( 'Light', 'wr-nitro' ) => 'light',
								__( 'Dark', 'wr-nitro' )  => 'dark',
							),
						),
						array(
							'param_name' => 'alignment',
							'heading'    => esc_html__( 'Button Alignment', 'wr-nitro' ),
							'type'       => 'dropdown',
							'value'      => array(
								__( 'Left', 'wr-nitro' )   => '',
								__( 'Center', 'wr-nitro' ) => 'jcc',
								__( 'Right', 'wr-nitro' )  => 'jcfe',
							),
						),
						array(
							'type'        => 'autocomplete',
							'heading'     => esc_html__( 'Select identificator', 'wr-nitro' ),
							'param_name'  => 'id',
							'description' => esc_html__( 'Input product ID  or product SKU or product title to see suggestions', 'wr-nitro' ),
							'admin_label' => true,
						),
						array(
							'type'       => 'hidden',
							'param_name' => 'sku',
						),
						array(
							'param_name'       => 'radius',
							'heading'          => esc_html__( 'Circle Icon', 'wr-nitro' ),
							'type'             => 'checkbox',
							'edit_field_class' => 'mgt15 vc_col-xs-12',
						),
						array(
							'param_name'       => 'wishlist',
							'heading'          => esc_html__( 'Enable Wishlist Button', 'wr-nitro' ),
							'type'             => 'checkbox',
							'edit_field_class' => 'mgt15 mgb15 vc_col-xs-12',
						),
						array(
							'param_name'  => 'extra_class',
							'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
							'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
							'type'        => 'textfield',
						),
					)
				)
			);

			// Map product.
			vc_map(
				array(
					'name'        => esc_html__( 'Product', 'wr-nitro' ),
					'base'        => 'nitro_product',
					'icon'        => 'fa fa-shopping-cart',
					'category'    => esc_html__( 'WooCommerce', 'wr-nitro' ),
					'description' => esc_html__( 'Show a single product by ID or SKU', 'wr-nitro' ),
					'params'      => array(
						array(
							'type'        => 'autocomplete',
							'heading'     => esc_html__( 'Select identificator', 'wr-nitro' ),
							'param_name'  => 'id',
							'description' => esc_html__( 'Input product ID or product SKU or product title to see suggestions', 'wr-nitro' ),
							'admin_label' => true
						),
						array(
							'type'       => 'hidden',
							'param_name' => 'sku',
						),
						array(
							'param_name'       => 'style',
							'heading'          => esc_html__( 'Style', 'wr-nitro' ),
							'type'             => 'dropdown',
							'edit_field_class' => 'mgt15 mgb15 vc_col-xs-12',
							'admin_label'      => true,
							'value'            => $item_styles,
							'std'              => '1',
							'save_always'      => true,
						),
						array(
							'param_name'  => 'hover_style',
							'heading'     => esc_html__( 'Hover Style', 'wr-nitro' ),
							'type'        => 'dropdown',
							'value'       => $hover_styles,
							'std'         => 'default',
							'save_always' => true,
						),
						array(
							'param_name' => 'mask_overlay_color',
							'heading'    => esc_html__( 'Mask Overlay Color', 'wr-nitro' ),
							'type'       => 'colorpicker',
							'value'      => 'rgba(0, 0, 0, 0.7)',
							'dependency' 	=> array(
								'element'   => 'hover_style',
								'value' 	=> 'mask',
							),
						),
						array(
							'param_name'  => 'transition_effects',
							'heading'     => esc_html__( 'Transition Effects', 'wr-nitro' ),
							'description' => esc_html__( 'This feature will not working if you enable slider for product thumbnail.', 'wr-nitro' ),
							'type'        => 'dropdown',
							'value'       => $transition_effects,
							'dependency' => array(
								'element'   => 'hover_style',
								'value' 	=> 'flip-back',
							),
							'std' => 'fade',
							'save_always' => true,
						),
						array(
							'param_name' => 'slider',
							'heading'    => esc_html__( 'Enable carousel for product thumnail', 'wr-nitro' ),
							'type'       => 'checkbox',
							'dependency' => array(
								'element'   => 'hover_style',
								'value' 	=> 'flip-back'
							),
						),
						array(
							'param_name' => 'autoplay',
							'heading'    => esc_html__( 'Enable Autoplay?', 'wr-nitro' ),
							'type'       => 'checkbox',
							'dependency' => array(
								'element' => 'slider',
								'value'   => 'true'
							),
						),
						array(
							'param_name'  => 'countdown',
							'heading'     => esc_html__( 'Enable countdown for sale product', 'wr-nitro' ),
							'description' => esc_html__( 'Setup sale schedule in product first.', 'wr-nitro' ),
							'type'        => 'checkbox',
						),
						array(
							'param_name'  => 'extra_class',
							'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
							'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
							'type'        => 'textfield',
						),
					)
				)
			);

			// Map products.
			vc_map(
				array(
					'name'     		=> esc_html__( 'Products', 'wr-nitro' ),
					'base'     		=> 'nitro_products',
					'icon'     		=> 'fa fa-shopping-cart',
					'category' 		=> esc_html__( 'WooCommerce', 'wr-nitro' ),
					'description' 	=> esc_html__( 'Show multiple products by ID or SKU.', 'wr-nitro' ),
					'params'   		=> array(
						array(
							'type' 	     => 'dropdown',
							'heading'    => esc_html__( 'Order', 'wr-nitro' ),
							'param_name' => 'order_fillter',
							'value'      => array(
								__( 'All products', 'wr-nitro' )          => 'all',
								__( 'Recent products', 'wr-nitro' )       => 'recent',
								__( 'Featured products', 'wr-nitro' )     => 'featured',
								__( 'Sale-off products', 'wr-nitro' )     => 'sale',
								__( 'Best selling products', 'wr-nitro' ) => 'best_selling',
								__( 'Top Rated Products', 'wr-nitro' )    => 'top_rated',
								__( 'Category', 'wr-nitro' )              => 'by_cat',
							),
							'std'         => 'all',
							'save_always' => true
						),
						array(
							'heading'    => esc_html__( 'Choose Category', 'wr-nitro' ),
							'param_name' => 'cat_id',
							'type'       => 'dropdown',
							'value'      => $product_cat,
							'dependency' => array(
								'element' => 'order_fillter',
								'value'   => 'by_cat',
							),
						),
						array(
							'type'       => 'autocomplete',
							'heading'    => esc_html__( 'Products', 'wr-nitro' ),
							'param_name' => 'ids',
							'settings'   => array(
								'multiple'      => true,
								'sortable'      => true,
								'unique_values' => true,
							),
							'save_always' => true,
							'description' => esc_html__( 'Input product ID or product SKU or product title to see suggestions', 'wr-nitro' ),
							'admin_label' => true,
							'dependency'  => array(
								'element' => 'order_fillter',
								'value'   => 'all',
							),
						),
						array(
							'type'       => 'hidden',
							'param_name' => 'skus',
							'dependency' => array(
								'element' => 'order',
								'value'   => 'all',
							),
						),
						array(
							'type' 			   => 'textfield',
							'heading' 		   => esc_html__( 'Per Page', 'wr-nitro' ),
							'value' 		   => 12,
							'save_always' 	   => true,
							'param_name' 	   => 'per_page',
							'description' 	   => esc_html__( 'How much items per page to show (-1 to show all products)', 'wr-nitro' ),
							'edit_field_class' => 'mgt15 mgb15 vc_col-xs-12',
						),
						array(
							'type' 	      => 'dropdown',
							'heading'     => esc_html__( 'Columns', 'wr-nitro' ),
							'description' => esc_html__( 'How much columns grid ?', 'wr-nitro' ),
							'value'       => $grid_columns,
							'std'         => '4',
							'param_name'  => 'columns',
							'save_always' => true,
							'dependency'  => array(
								'element' => 'order_fillter',
								'value'   => array( 'all', 'featured', 'sale', 'top_rated' ),
							),
							'dependency'  => array(
								'element' => 'list_style',
								'value'   => array( 'grid', 'grid-boxed', 'masonry' ),
							),
						),
						array(
							'param_name' => 'slider',
							'heading'    => esc_html__( 'Enable Slider', 'wr-nitro' ),
							'type'       => 'checkbox',
							'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
							'dependency'  => array(
								'element' => 'list_style',
								'value'   => array( 'grid', 'grid-boxed' ),
							),
						),
                        array(
                            'param_name' => 'auto_play',
                            'heading'    => esc_html__( 'Enable Autoplay', 'wr-nitro' ),
                            'group'      => esc_html__( 'Slider Settings', 'wr-nitro' ),
                            'type'       => 'checkbox',
                            'dependency' => array(
                                'element'   => 'slider',
                                'not_empty' => true
                            ),
                        ),
                        array(
                            'param_name' => 'timeout',
                            'heading'    => esc_html__( 'Autoplay Timeout', 'wr-nitro' ),
                            'description'=> esc_html__( 'Unit: ms.', 'wr-nitro' ),
                            'group'      => esc_html__( 'Slider Settings', 'wr-nitro' ),
                            'type'       => 'textfield',
                            'value'      => '5000',
                            'dependency' => array(
                                'element'   => 'auto_play',
                                'not_empty' => true
                            ),
                        ),
						array(
							'param_name' => 'navigation',
							'heading'    => esc_html__( 'Enable Navigation', 'wr-nitro' ),
							'group'      => esc_html__( 'Slider Settings', 'wr-nitro' ),
							'type'       => 'checkbox',
							'dependency' => array(
								'element'   => 'slider',
								'not_empty' => true
							),
						),
						array(
							'param_name' => 'pagination',
							'heading'    => esc_html__( 'Enable Pagination', 'wr-nitro' ),
							'group'      => esc_html__( 'Slider Settings', 'wr-nitro' ),
							'type'       => 'checkbox',
							'dependency' => array(
								'element'   => 'slider',
								'not_empty' => true
							),
						),
                        array(
                            'param_name' => 'gutter_width',
                            'heading'    => esc_html__( 'Gutter Width (px)', 'wr-nitro' ),
                            'group'      => esc_html__( 'Slider Settings', 'wr-nitro' ),
                            'type'       => 'textfield',
                            'value'      => '30',
                            'dependency' => array(
                                'element'   => 'slider',
                                'not_empty' => true
                            ),
                        ),
						array(
							'type' 		  => 'dropdown',
							'heading' 	  => esc_html__( 'Order by', 'wr-nitro' ),
							'param_name'  => 'orderby',
							'value' 	  => $order_by_values,
							'std'         => 'date',
							'save_always' => true,
							'description' => sprintf( __( 'Select how to sort retrieved products. More at %s. Default by Title', 'wr-nitro' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank" rel="noopener noreferrer">WordPress codex page</a>' ),
							'dependency'  => array(
								'element' => 'order_fillter',
								'value'	  => array( 'all', 'featured', 'sale', 'top_rated', 'by_cat' ),
							),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => esc_html__( 'Sort order', 'wr-nitro' ),
							'param_name'  => 'order',
							'value' 	  => $order_way_values,
							'std'         => 'ASC',
							'save_always' => true,
							'description' => sprintf( __( 'Designates the ascending or descending order. More at %s. Default by ASC', 'wr-nitro' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank" rel="noopener noreferrer">WordPress codex page</a>' ),
							'dependency'  => array(
								'element' => 'order_fillter',
								'value'   => array( 'all', 'featured', 'sale', 'top_rated', 'by_cat' ),
							),
						),
						array(
							'param_name' => 'list_style',
							'heading'    => esc_html__( 'Layout', 'wr-nitro' ),
							'type'       => 'dropdown',
							'value'      => array(
								__( 'Grid', 'wr-nitro' ) 		              => 'grid',
								__( 'Bordered Grid', 'wr-nitro' )             => 'grid-boxed',
								__( 'Masonry', 'wr-nitro' ) 	              => 'masonry',
								__( 'List With Big Thumbnail', 'wr-nitro' )   => 'list',
								__( 'List With Small Thumbnail', 'wr-nitro' ) => 'list-small',
							),
							'std'         => 'grid',
							'save_always' => true,
						),
						array(
							'param_name' => 'style',
							'heading'    => esc_html__( 'Style', 'wr-nitro' ),
							'type'       => 'dropdown',
							'value'      => $item_styles,
							'dependency' => array(
								'element' => 'list_style',
								'value'   => array( 'grid', 'grid-boxed', 'masonry' ),
							),
							'std'         => '1',
							'save_always' => true,
						),
						array(
							'param_name'  => 'hover_style',
							'heading'     => esc_html__( 'Hover style', 'wr-nitro' ),
							'type'        => 'dropdown',
							'value'       => $hover_styles,
							'std'         => 'default',
							'save_always' => true,
							'dependency'  => array(
								'element' => 'list_style',
								'value'   => array( 'grid', 'grid-boxed', 'masonry', 'list' ),
							),
						),
						array(
							'param_name' => 'mask_overlay_color',
							'heading'    => esc_html__( 'Mask overlay color', 'wr-nitro' ),
							'type'       => 'colorpicker',
							'value'      => 'rgba(0, 0, 0, 0.7)',
							'dependency' => array(
								'element' => 'hover_style',
								'value'	  => 'mask',
							),
						),
						array(
							'param_name' => 'transition_effects',
							'heading'    => esc_html__( 'Transition effects', 'wr-nitro' ),
							'type'       => 'dropdown',
							'value'      => $transition_effects,
							'dependency' => array(
								'element'   => 'hover_style',
								'value' 	=> 'flip-back',
							),
							'std'         => 'fade',
							'save_always' => true,
						),
						array(
							'param_name'  => '992',
							'heading'     => esc_html__( 'Number item to show on device < 993px', 'wr-nitro' ),
							'type'        => 'textfield',
							'value'       => 4,
							'edit_field_class' => 'vc_column vc_col-sm-6 mgt15',
							'group'     => esc_html__( 'Responsive Settings', 'wr-nitro' ),
							'dependency' => array(
								'element'   => 'slider',
								'not_empty' => true
							),
						),
						array(
							'param_name'  => '768',
							'heading'     => esc_html__( 'Number item to show on device < 769px', 'wr-nitro' ),
							'type'        => 'textfield',
							'value'       => 3,
							'edit_field_class' => 'vc_column vc_col-sm-6',
							'group'     => esc_html__( 'Responsive Settings', 'wr-nitro' ),
							'dependency' => array(
								'element'   => 'slider',
								'not_empty' => true
							),
						),
						array(
							'param_name'  => '600',
							'heading'     => esc_html__( 'Number item to show on device < 601px', 'wr-nitro' ),
							'type'        => 'textfield',
							'value'       => 2,
							'edit_field_class' => 'vc_column vc_col-sm-6',
							'group'     => esc_html__( 'Responsive Settings', 'wr-nitro' ),
							'dependency' => array(
								'element'   => 'slider',
								'not_empty' => true
							),
						),
						array(
							'param_name'  => '375',
							'heading'     => esc_html__( 'Number item to show on device < 376px', 'wr-nitro' ),
							'type'        => 'textfield',
							'value'       => 1,
							'edit_field_class' => 'vc_column vc_col-sm-6',
							'group'     => esc_html__( 'Responsive Settings', 'wr-nitro' ),
							'dependency' => array(
								'element'   => 'slider',
								'not_empty' => true
							),
						),
						array(
							'param_name'  => 'extra_class',
							'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
							'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
							'type'        => 'textfield',
						),
						array(
							'param_name'       => 'nitro_products_custom_id',
							'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
							'type'             => 'textfield',
							'value'            => 22334455,
							'edit_field_class' => 'hidden',
						),
					)
				)
			);

			// Map Buy Now.
			vc_map(
				array(
					'name'     => esc_html__( 'Buy Now', 'wr-nitro' ),
					'base'     => 'nitro_buy_now',
					'icon'     => 'fa fa-shopping-cart',
					'category' => esc_html__( 'WooCommerce', 'wr-nitro' ),
					'description' => esc_html__( 'Insert buy now button by product ID, SKU or title', 'wr-nitro' ),
					'params'   => array(
						array(
							'type'        => 'autocomplete',
							'heading'     => esc_html__( 'Select identificator', 'wr-nitro' ),
							'param_name'  => 'id',
							'description' => esc_html__( 'Input product ID product SKU or product title to see suggestions', 'wr-nitro' ),
							'admin_label' => true,
						),
						array(
							'param_name' => 'checkout',
							'heading'    => esc_html__( 'Checkout type', 'wr-nitro' ),
							'type'       => 'dropdown',
							'value'      => array(
								__( 'Checkout Current Product Only', 'wr-nitro' )      => 1,
								__( 'Checkout With All Products In Cart', 'wr-nitro' ) => 2,
							),
							'std'         => 1,
							'save_always' => true,
						),
						array(
							'param_name' => 'payment_info',
							'heading'    => esc_html__( 'Payment information', 'wr-nitro' ),
							'type'       => 'dropdown',
							'value'      => array(
								__( 'Show Modal Popup', 'wr-nitro' )          => 1,
								__( 'Redirect To Checkout Page', 'wr-nitro' ) => 2,
							),
							'std'         => 1,
							'save_always' => true,
						),
						array(
							'param_name' => 'style',
							'heading'    => esc_html__( 'Button type', 'wr-nitro' ),
							'type'       => 'dropdown',
							'value'      => array(
								__( 'Text with icon', 'wr-nitro' ) => 'text-icon',
								__( 'Text only', 'wr-nitro' )      => 'text',
								__( 'Icon only', 'wr-nitro' )      => 'icon',
							),
							'std'         => 1,
						),
						array(
							'param_name' => 'button_style',
							'heading'    => esc_html__( 'Button style', 'wr-nitro' ),
							'type'       => 'dropdown',
							'value'      => array(
								__( 'Solid', 'wr-nitro' )   => 'wr-btn-solid',
								__( 'Outline', 'wr-nitro' ) => 'wr-btn-outline',
							),
							'std'         => 1,
						),
						array(
							'param_name'  => 'button_text',
							'heading'     => esc_html__( 'Button Text', 'wr-nitro' ),
							'type'        => 'textfield',
							'value'       => esc_html__( 'Buy Now', 'wr-nitro' ),
							'dependency' => array(
								'element'            => 'style',
								'value_not_equal_to' => 'icon',
							),
						),
						array(
							'param_name'  => 'extra_class',
							'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
							'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
							'type'        => 'textfield',
						),
					)
				)
			);

		} // End if woocommerce is activated

		// Map new Carousel element.
		vc_map(
			array(
				'name'                    => esc_html__( 'Nitro Carousel', 'wr-nitro' ),
				'category'                => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'icon'                    => 'fa fa-sliders',
				'base'                    => 'nitro_carousel',
				'as_parent'               => array( 'only' => 'nitro_banner, nitro_product, nitro_gallery_single, vc_single_image, nitro_product_category' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'js_view'                 => 'VcColumnView',
				'params'                  => array(
					array(
						'param_name'  => 'items',
						'heading'     => esc_html__( 'Items (Number only)', 'wr-nitro' ),
						'description' => esc_html__( 'Set the maximum amount of items displayed at a time with the widest browser width', 'wr-nitro' ),
						'type'        => 'textfield',
						'value'       => '4',
						'admin_label' => true,
					),
					array(
						'param_name'  => 'auto_play',
						'heading'     => esc_html__( 'Enable Autoplay', 'wr-nitro' ),
						'type'        => 'checkbox',
						'admin_label' => true,
					),
					array(
						'param_name'  => 'navigation',
						'heading'     => esc_html__( 'Enable Navigation', 'wr-nitro' ),
						'type'        => 'checkbox',
						'admin_label' => true,
					),
					array(
						'param_name'  => 'pagination',
						'heading'     => esc_html__( 'Enable Pagination', 'wr-nitro' ),
						'type'        => 'checkbox',
						'admin_label' => true,
					),
					array(
						'param_name'  => 'gutter_width',
						'heading'     => esc_html__( 'Gutter Width (px)', 'wr-nitro' ),
						'type'        => 'textfield',
						'value'       => '30',
						'admin_label' => true,
					),
					array(
						'param_name'  => 'sc_992',
						'heading'     => esc_html__( 'Number item to show on device < 993px', 'wr-nitro' ),
						'type'        => 'textfield',
						'value'       => 4,
						'edit_field_class' => 'vc_column vc_col-sm-6 mgt15',
						'group'     => esc_html__( 'Responsive Settings', 'wr-nitro' )
					),
					array(
						'param_name'  => 'sc_768',
						'heading'     => esc_html__( 'Number item to show on device < 769px', 'wr-nitro' ),
						'type'        => 'textfield',
						'value'       => 3,
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group'     => esc_html__( 'Responsive Settings', 'wr-nitro' )
					),
					array(
						'param_name'  => 'sc_600',
						'heading'     => esc_html__( 'Number item to show on device < 601px', 'wr-nitro' ),
						'type'        => 'textfield',
						'value'       => 2,
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group'     => esc_html__( 'Responsive Settings', 'wr-nitro' )
					),
					array(
						'param_name'  => 'sc_375',
						'heading'     => esc_html__( 'Number item to show on device < 376px', 'wr-nitro' ),
						'type'        => 'textfield',
						'value'       => 1,
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group'     => esc_html__( 'Responsive Settings', 'wr-nitro' )
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
					),
					array(
						'param_name'       => 'nitro_carousel_custom_id',
						'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 22334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new masonry builder element.
		vc_map(
			array(
				'name'         => esc_html__( 'Masonry Layout', 'wr-nitro' ),
				'icon'         => 'fa fa-th-list',
				'base'         => 'nitro_masonry',
				'category'     => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'as_parent'    => array( 'only' => 'nitro_masonry_element' ),
				'is_container' => true,
				'js_view'      => 'VcColumnView',
				'params'       => array(
					array(
						'param_name' => 'column',
						'heading'    => esc_html__( 'Number of columns', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( '3 Columns', 'wr-nitro' ) => '3',
							__( '4 Columns', 'wr-nitro' ) => '4',
							__( '5 Columns', 'wr-nitro' ) => '5',
							__( '6 Columns', 'wr-nitro' ) => '6',
							__( '7 Columns', 'wr-nitro' ) => '7',
							__( '8 Columns', 'wr-nitro' ) => '8',
						),
					),
					array(
						'param_name' => 'border',
						'heading'    => esc_html__( 'Enable Border Outline', 'wr-nitro' ),
						'type'       => 'checkbox',
						'value'      => array( __( 'Yes', 'wr-nitro' ) => 'yes' ),
					),
					array(
						'param_name' => 'border_color',
						'heading'    => esc_html__( 'Border Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#ddd',
						'dependency' => array(
							'element'   => 'border',
							'not_empty' => true,
						),
					),
					array(
						'param_name' => 'gutter_width',
						'heading'    => esc_html__( 'Gutter Width (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => '',
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
					),
					array(
						'param_name'       => 'wr_masonry_custom_id',
						'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);
		vc_map(
			array(
				'name'         => esc_html__( 'Masonry Element', 'wr-nitro' ),
				'icon'         => 'fa fa-stop',
				'base'         => 'nitro_masonry_element',
				'as_child'     => array( 'only' => 'nitro_masonry' ),
				'as_parent'    => array( 'only' => 'vc_column_text, nitro_product, nitro_blog_single, nitro_carousel, nitro_banner, nitro_gallery_single, nitro_counter_up, nitro_google_map, vc_icon, nitro_member, nitro_subscribe_form, nitro_services, nitro_quote, nitro_heading' ),
				'is_container' => true,
				'js_view'      => 'VcColumnView',
				'params'       => array(
					array(
						'param_name' => 'size',
						'heading'    => esc_html__( 'Element Size', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Small Square', 'wr-nitro' )          => 'small-square',
							__( 'Large Square', 'wr-nitro' )          => 'large-square',
							__( 'Rectangle (Portrait)', 'wr-nitro' )  => 'rectangle-portrait',
							__( 'Rectangle (Landscape)', 'wr-nitro' ) => 'rectangle-landscape',
						),
					),
					array(
						'param_name' => 'setting',
						'type'       => 'css_editor',
						'group'      => esc_html__( 'Background Settings', 'wr-nitro' ),
					),
				)
			)
		);

		// Map single post for masonry layout.
		vc_map(
			array(
				'name'     => esc_html__( 'Blog Post', 'wr-nitro' ),
				'base'     => 'nitro_blog_single',
				'icon'     => 'fa fa-picture-o',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'as_child' => array( 'only' => 'nitro_masonry' ),
				'params'   => array(
					array(
						'param_name' => 'post_id',
						'heading'    => esc_html__( 'Display post by ID', 'wr-nitro' ),
						'type'       => 'textfield',
					),
				)
			)
		);

		// Map new button element
		vc_map(
			array(
				'name'     => esc_html__( 'Nitro Button', 'wr-nitro' ),
				'base'     => 'nitro_button',
				'icon'     => 'fa fa-square',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name'       => 'text',
						'heading'          => esc_html__( 'Button Text', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-4',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'std'              => esc_html__( 'Button Text', 'wr-nitro' ),
						'admin_label'      => true
					),
					array(
						'param_name'       => 'link',
						'heading'          => esc_html__( 'Link To', 'wr-nitro' ),
						'type'             => 'vc_link',
						'edit_field_class' => 'vc_col-sm-8',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name' => 'sub_1',
						'heading'    => esc_html__( 'General Settings', 'wr-nitro' ),
						'type'       => 'sub_heading',
						'group'      => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'font_family',
						'heading'          => esc_html__( 'Font Family', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'            => $fonts,
					),
					array(
						'param_name'       => 'font_weight',
						'heading'          => esc_html__( 'Font Weight', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'            => $weights,
						'dependency' => array(
							'element'   => 'font_family',
							'not_empty' => true,
						),
					),
					array(
						'param_name'       => 'font_size',
						'heading'          => esc_html__( 'Font Size (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'            => 14,
					),
					array(
						'param_name'       => 'font_style',
						'heading'          => esc_html__( 'Font Style', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'            => array(
							__( 'Normal', 'wr-nitro' ) => 'normal',
							__( 'Italic', 'wr-nitro' ) => 'italic',
						),
					),
					array(
						'param_name'       => 'text_transform',
						'heading'          => esc_html__( 'Text Transform', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'            => array(
							__( 'None', 'wr-nitro' )       => 'none',
							__( 'Uppercase', 'wr-nitro' )  => 'uppercase',
							__( 'Capitalize', 'wr-nitro' ) => 'capitalize',
						),
					),
					array(
						'param_name'       => 'alignment',
						'heading'          => esc_html__( 'Button Alignment', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'            => array(
							__( 'Left', 'wr-nitro' )   => 'tl',
							__( 'Center', 'wr-nitro' ) => 'tc',
							__( 'Right', 'wr-nitro' )  => 'tr',
						),
					),
					array(
						'param_name'       => 'color',
						'heading'          => esc_html__( 'Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'            => '#fff',
					),
					array(
						'param_name'       => 'bg_color',
						'heading'          => esc_html__( 'Background Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'            => '#3d3d3d',
					),
					array(
						'param_name' => 'sub_2',
						'heading'    => esc_html__( 'Border Settings', 'wr-nitro' ),
						'type'       => 'sub_heading',
						'group'      => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'border_width',
						'heading'          => esc_html__( 'Border Width (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'border_style',
						'heading'          => esc_html__( 'Border Style', 'wr-nitro' ),
						'type'             => 'dropdown',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'            => array(
							__( 'Solid', 'wr-nitro' )  => 'solid',
							__( 'Dashed', 'wr-nitro' ) => 'dasded',
							__( 'Dotted', 'wr-nitro' ) => 'dotted',
							__( 'Double', 'wr-nitro' ) => 'double',
						),
						'dependency' => array(
							'element'   => 'border_width',
							'not_empty' => true,
						),
					),
					array(
						'param_name'       => 'border_radius',
						'heading'          => esc_html__( 'Border Radius (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'border_color',
						'heading'          => esc_html__( 'Border Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-sm-6 vc_column vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'            => '#3d3d3d',
						'dependency' => array(
							'element'   => 'border_width',
							'not_empty' => true,
						),
					),
					array(
						'param_name' => 'sub_3',
						'heading'    => esc_html__( 'Spacing', 'wr-nitro' ),
						'type'       => 'sub_heading',
						'group'      => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'line_height',
						'heading'          => esc_html__( 'Line Height', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'            => '40px'
					),
					array(
						'param_name'       => 'padding',
						'heading'          => esc_html__( 'Padding Left + Right (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'            => 15
					),
					array(
						'param_name'       => 'spacing',
						'heading'          => esc_html__( 'Letter Spacing (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-4 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'margin_top',
						'heading'          => esc_html__( 'Margin Top (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'margin_right',
						'heading'          => esc_html__( 'Margin Right (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'margin_bottom',
						'heading'          => esc_html__( 'Margin Bottom (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'margin_left',
						'heading'          => esc_html__( 'Margin Left (px)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-sm-3 vc_column',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name' => 'sub_4',
						'heading'    => esc_html__( 'Icon Settings', 'wr-nitro' ),
						'type'       => 'sub_heading',
						'group'      => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name' => 'icon',
						'heading'    => esc_html__( 'Enable Icon?', 'wr-nitro' ),
						'type'       => 'checkbox',
						'group'      => 'Normal State',
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name'  => 'icon_type',
						'heading'     => esc_html__( 'Icon Library', 'wr-nitro' ),
						'type'        => 'dropdown',
						'group'       => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'       => array(
							__( 'Font Awesome', 'wr-nitro' ) => 'fontawesome',
							__( 'Open Iconic', 'wr-nitro' )  => 'openiconic',
							__( 'Typicons', 'wr-nitro' )     => 'typicons',
							__( 'Entypo', 'wr-nitro' )       => 'entypo',
							__( 'Linecons', 'wr-nitro' )     => 'linecons',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
						'dependency'  => array(
							'element'   => 'icon',
							'not_empty' => true,
						),
					),
					array(
						'param_name'  => 'icon_fontawesome',
						'heading'     => esc_html__( 'Icon', 'wr-nitro' ),
						'type'        => 'iconpicker',
						'group'       => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'       => 'fa fa-adjust',
						'settings'    => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'fontawesome',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'icon_openiconic',
						'heading'    => esc_html__( 'Icon', 'wr-nitro' ),
						'type'       => 'iconpicker',
						'group'      => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'      => 'vc-oi vc-oi-dial',
						'settings'   => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'openiconic',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'icon_typicons',
						'heading'    => esc_html__( 'Icon', 'wr-nitro' ),
						'type'       => 'iconpicker',
						'group'      => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'      => 'typcn typcn-adjust-brightness',
						'settings'   => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'typicons',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'icon_entypo',
						'heading'    => esc_html__( 'Icon', 'wr-nitro' ),
						'type'       => 'iconpicker',
						'group'      => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'      => 'entypo-icon entypo-icon-note',
						'settings'   => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value'   => 'entypo',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'icon_linecons',
						'heading'    => esc_html__( 'Icon', 'wr-nitro' ),
						'type'       => 'iconpicker',
						'group'      => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'      => 'vc_li vc_li-heart',
						'settings'   => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
						),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => 'linecons',
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'icon_color',
						'heading'    => esc_html__( 'Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'group'      => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'      => '#fff',
						'dependency'  => array(
							'element'   => 'icon',
							'not_empty' => true,
						),
						'edit_field_class' => 'vc_col-sm-6 mgt15 vc_column',
					),
					array(
						'param_name' => 'icon_position',
						'heading'    => esc_html__( 'Position', 'wr-nitro' ),
						'type'       => 'dropdown',
						'group'      => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'      => array(
							__( 'Left', 'wr-nitro' )  => 'left',
							__( 'Right', 'wr-nitro' ) => 'right',
						),
						'dependency'  => array(
							'element'   => 'icon',
							'not_empty' => true,
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name' => 'icon_size',
						'heading'    => esc_html__( 'Icon Size (px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'group'      => esc_html__( 'Normal State', 'wr-nitro' ),
						'value'      => 14,
						'dependency'  => array(
							'element'   => 'icon',
							'not_empty' => true,
						),
						'edit_field_class' => 'vc_col-sm-6 vc_column',
					),
					array(
						'param_name'       => 'hover_color',
						'heading'          => esc_html__( 'Text Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'group'            => esc_html__( 'Hover State', 'wr-nitro' ),
						'value'            => '#fff',
						'edit_field_class' => 'vc_col-sm-6 mgb20',
					),
					array(
						'param_name'       => 'hover_border_color',
						'heading'          => esc_html__( 'Border Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-sm-4 vc_column mgb20',
						'group'            => esc_html__( 'Hover State', 'wr-nitro' ),
						'value'            => '#3d3d3d',
						'edit_field_class' => 'vc_col-sm-6 mgb20',
					),
					array(
						'param_name'       => 'hover_bg_color',
						'heading'          => esc_html__( 'Background Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'group'            => esc_html__( 'Hover State', 'wr-nitro' ),
						'value'            => '#000',
						'edit_field_class' => 'vc_col-sm-6 mgb20',
					),
					array(
						'param_name'       => 'hover_icon_color',
						'heading'          => esc_html__( 'Icon Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'group'            => esc_html__( 'Hover State', 'wr-nitro' ),
						'value'            => '#fff',
						'edit_field_class' => 'vc_col-sm-6 mgb20',
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
						'group'       => esc_html__( 'Normal State', 'wr-nitro' ),
					),
					array(
						'param_name'       => 'button_custom_id',
						'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
						'group'            => esc_html__( 'Normal State', 'wr-nitro' ),
					),
				)
			)
		);

		// Map new social network element
		vc_map(
			array(
				'name'     => esc_html__( 'Social Network', 'wr-nitro' ),
				'base'     => 'nitro_social_network',
				'icon'     => 'fa fa-dribbble',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name'  => 'network',
						'heading'     => esc_html__( 'Network', 'wr-nitro' ),
						'type'        => 'dropdown',
						'admin_label' => true,
						'value'       => array(
							__( 'Dribbble', 'wr-nitro' )  => 'dribbble',
							__( 'Flickr', 'wr-nitro' )    => 'flickr',
							__( 'Instagram', 'wr-nitro' ) => 'instagram',
						),
					),
					array(
						'param_name'  => 'user_id',
						'heading'     => esc_html__( 'User ID', 'wr-nitro' ),
						'description' => sprintf( __( 'Lookup User ID for <a target="_blank" rel="noopener noreferrer" href="%s">Instagram</a> - <a target="_blank" rel="noopener noreferrer" href="%s">Flickr</a>', 'wr-nitro' ), 'https://smashballoon.com/instagram-feed/find-instagram-user-id/', 'http://idgettr.com/' ),
						'type'        => 'textfield',
						'admin_label' => true,
					),
					array(
						'param_name'  => 'access_token',
						'heading'     => esc_html__( 'Instagram Access Token', 'wr-nitro' ),
						'description' => sprintf( __( 'Generate Access Token <a target="_blank" rel="noopener noreferrer" href="%s">Instagram</a>', 'wr-nitro' ), 'http://instagram.pixelunion.net/'),
						'type'        => 'textfield',
						'dependency' => array(
							'element' => 'network',
							'value'   => 'instagram',
						),
					),
					array(
						'param_name' => 'layout',
						'heading'    => esc_html__( 'Layout', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Grid', 'wr-nitro' )    => 'grid',
							__( 'Masonry', 'wr-nitro' ) => 'masonry',
						),
						'dependency' => array(
							'element' => 'slider',
							'value'   => '0',
						),
					),
					array(
						'param_name'  => 'item_large',
						'heading'     => esc_html__( 'Item Large', 'wr-nitro' ),
						'description' => esc_html__( 'Number of item you want to set larger (Note: separate values by commas ",")', 'wr-nitro' ),
						'type'        => 'textfield',
						'dependency' => array(
							'element' => 'layout',
							'value'   => 'masonry',
						),
					),
					array(
						'param_name' => 'columns',
						'heading'    => esc_html__( 'Columns', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( '2 Columns', 'wr-nitro' ) => '2',
							__( '3 Columns', 'wr-nitro' ) => '3',
							__( '4 Columns', 'wr-nitro' ) => '4',
							__( '5 Columns', 'wr-nitro' ) => '5',
							__( '6 Columns', 'wr-nitro' ) => '6',
							__( '7 Columns', 'wr-nitro' ) => '7',
							__( '8 Columns', 'wr-nitro' ) => '8',
						),
						'std' => 4,
						'dependency' => array(
							'element' => 'slider',
							'value'   => '0',
						),
					),
					array(
						'param_name'  => 'limit',
						'heading'     => esc_html__( 'Per Page', 'wr-nitro' ),
						'description' => esc_html__( 'How much items per page to show', 'wr-nitro' ),
						'type'        => 'textfield',
						'value'       => 10
					),
					array(
						'param_name' => 'gutter',
						'heading'    => esc_html__( 'Gutter Width', 'wr-nitro' ),
						'type'       => 'textfield',
					),
					array(
						'param_name' => 'slider',
						'heading'    => esc_html__( 'Enable Carousel', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value' => array(
							__( 'Yes', 'wr-nitro' )    => 1,
							__( 'No', 'wr-nitro' ) => 0,
						),
					),
					array(
						'param_name'  => 'item',
						'heading'     => esc_html__( 'Item', 'wr-nitro' ),
						'description' => esc_html__( 'The number of items you want to see on the screen.', 'wr-nitro' ),
						'type'        => 'textfield',
						'value'       => 4,
						'dependency' => array(
							'element' => 'slider',
							'value'   => '1',
						),
					),
					array(
						'param_name' => 'autoplay',
						'heading'    => esc_html__( 'Autoplay', 'wr-nitro' ),
						'type'       => 'checkbox',
						'std'        => 'no',
						'dependency' => array(
							'element' => 'slider',
							'value'   => '1',
						),
					),
					array(
						'param_name' => 'enable_info',
						'heading'    => esc_html__( 'Enable Shot Info', 'wr-nitro' ),
						'type'       => 'checkbox',
						'dependency' => array(
							'element' => 'network',
							'value'   => array( 'dribbble', 'instagram' )
						),
					),
					array(
						'param_name' => 'info_style',
						'heading'    => esc_html__( 'Info Style', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value' => array(
							__( 'Inside Shot', 'wr-nitro' )  => 'inside',
							__( 'Outside Shot', 'wr-nitro' ) => 'outside',
						),
						'dependency' => array(
							'element' => 'enable_info',
							'value'   => 'true',
						),
					),
					array(
						'param_name' => 'mask_bg',
						'heading'    => esc_html__( 'Mask Overlay', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => 'rgba(0, 0, 0, .85)',
						'dependency' => array(
							'element' => 'info_style',
							'value'   => 'inside',
						),
					),
					array(
						'param_name' => 'text_color',
						'heading'    => esc_html__( 'Text Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'dependency' => array(
							'element' => 'info_style',
							'value'   => 'inside',
						),
					),
					array(
						'param_name'       => 'social_network_custom_id',
						'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new video element
		vc_map(
			array(
				'name'     => esc_html__( 'Video', 'wr-nitro' ),
				'base'     => 'nitro_video',
				'icon'     => 'fa fa-video-camera',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name'  => 'source',
						'heading'     => esc_html__( 'Video Source', 'wr-nitro' ),
						'type'        => 'dropdown',
						'admin_label' => true,
						'value'       => array(
							__( 'Youtube', 'wr-nitro' ) => 'youtube',
							__( 'Vimeo', 'wr-nitro' )   => 'vimeo',
						),
					),
					array(
						'param_name' => 'url',
						'heading'    => esc_html__( 'Link To Video', 'wr-nitro' ),
						'type'       => 'textfield',
					),
					array(
						'param_name' => 'style',
						'heading'    => esc_html__( 'Style', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Video Screen Preview', 'wr-nitro' ) => 'screen',
							__( 'Player Icon', 'wr-nitro' )          => 'icon',
							__( 'Image Thumbnail', 'wr-nitro' )      => 'image',
						),
					),
					array(
						'param_name' => 'icon_color',
						'heading'    => esc_html__( 'Icon Color', 'wr-nitro' ),
						'type'       => 'colorpicker',
						'value'      => '#2d2d2d2',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'image', 'icon' ),
						),
					),
					array(
						'param_name'  => 'width',
						'heading'     => esc_html__( 'Video Width (number only)', 'wr-nitro' ),
						'description' => esc_html__( 'This parameter is not work on video popup', 'wr-nitro' ),
						'type'        => 'textfield',
						'value'       => 500,
					),
					array(
						'param_name' => 'graphic',
						'heading'    => esc_html__( 'Upload image', 'wr-nitro' ),
						'type'       => 'attach_image',
						'dependency' => array(
							'element' => 'style',
							'value'   => 'image',
						),
					),
					array(
						'param_name' => 'align',
						'heading'    => esc_html__( 'Video Align', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'Left', 'wr-nitro' )   => 'tl',
							__( 'Center', 'wr-nitro' ) => 'tc',
							__( 'Right', 'wr-nitro' )  => 'tr',
						),
					),
					array(
						'param_name' => 'shadow',
						'heading'    => esc_html__( 'Shadow', 'wr-nitro' ),
						'type'       => 'dropdown',
						'value'      => array(
							__( 'None', 'wr-nitro' )    => '',
							__( 'Style 1', 'wr-nitro' ) => '1',
							__( 'Style 2', 'wr-nitro' ) => '2',
							__( 'Style 3', 'wr-nitro' ) => '3',
							__( 'Style 4', 'wr-nitro' ) => '4',
							__( 'Style 5', 'wr-nitro' ) => '5',
							__( 'Style 6', 'wr-nitro' ) => '6',
							__( 'Style 7', 'wr-nitro' ) => '7',
							__( 'Style 8', 'wr-nitro' ) => '8',
							__( 'Style 9', 'wr-nitro' ) => '9',
						),
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'image' )
						),
					),
					array(
						'param_name' => 'popup',
						'heading'    => esc_html__( 'Play in popup?', 'wr-nitro' ),
						'type'       => 'checkbox',
						'dependency' => array(
							'element' => 'style',
							'value'   => 'image',
						),
					),
					array(
						'param_name' => 'control',
						'heading'    => esc_html__( 'Enable Control', 'wr-nitro' ),
						'type'       => 'checkbox',
						'dependency' => array(
							'element' => 'source',
							'value'   => 'youtube',
						),
					),
					array(
						'param_name' => 'autoplay',
						'heading'    => esc_html__( 'Enable Auto Play', 'wr-nitro' ),
						'type'       => 'checkbox',
						'dependency' => array(
							'element' => 'style',
							'value'   => array( 'screen' ),
						),
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
					),
					array(
						'param_name'       => 'video_custom_id',
						'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);

		// Map new countdown element
		vc_map(
			array(
				'name'     => esc_html__( 'Countdown', 'wr-nitro' ),
				'base'     => 'nitro_countdown',
				'icon'     => 'fa fa-bomb',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name'  => 'style',
						'heading'     => esc_html__( 'Style', 'wr-nitro' ),
						'type'        => 'dropdown',
						'admin_label' => true,
						'value'       => array(
							__( 'Vertical', 'wr-nitro' )   => 'vertical',
							__( 'Horizontal', 'wr-nitro' ) => 'horizontal',
						),
					),
					array(
						'param_name'       => 'year',
						'heading'          => esc_html__( 'Year (eg: 2020)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-xs-4 vc_column',
					),
					array(
						'param_name'       => 'month',
						'heading'          => esc_html__( 'Month (eg: 01)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-xs-4 vc_column',
					),
					array(
						'param_name'       => 'day',
						'heading'          => esc_html__( 'Date (eg: 01)', 'wr-nitro' ),
						'type'             => 'textfield',
						'edit_field_class' => 'vc_col-xs-4 vc_column',
					),
					array(
						'param_name'       => 'bg_color',
						'heading'          => esc_html__( 'Background Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-xs-4 vc_column',
					),
					array(
						'param_name'       => 'number_color',
						'heading'          => esc_html__( 'Number Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-xs-4 vc_column',
					),
					array(
						'param_name'       => 'time_color',
						'heading'          => esc_html__( 'Time Unit Color', 'wr-nitro' ),
						'type'             => 'colorpicker',
						'edit_field_class' => 'vc_col-xs-4 vc_column',
					),
					array(
						'param_name' => 'line',
						'heading'    => esc_html__( 'Enable Line?', 'wr-nitro' ),
						'type'       => 'checkbox',
					),
					array(
						'param_name' => 'space',
						'heading'    => esc_html__( 'Space Between Timer (Unit px)', 'wr-nitro' ),
						'type'       => 'textfield',
						'value'      => 60,
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
					),
					array(
						'param_name'       => 'countdown_custom_id',
						'heading'          => esc_html__( 'Hidden ID', 'wr-nitro' ),
						'type'             => 'textfield',
						'value'            => 1122334455,
						'edit_field_class' => 'hidden',
					),
				)
			)
		);
		// Map new spotlight element
		vc_map(
			array(
				'name'     => esc_html__( 'Spotlight', 'wr-nitro' ),
				'base'     => 'nitro_spotlight',
				'icon'     => 'fa fa-clone',
				'category' => esc_html__( 'Nitro Elements', 'wr-nitro' ),
				'params'   => array(
					array(
						'param_name'  => 'title',
						'heading'     => esc_html__( 'Heading', 'wr-nitro' ),
						'type'        => 'textfield',
					),
					array(
						'param_name'  => 'link',
						'heading'     => esc_html__( 'Link', 'wr-nitro' ),
						'type'        => 'textfield',
					),
					array(
						'param_name' => 'front_image',
						'heading'    => esc_html__( 'Normal Image', 'wr-nitro' ),
						'type'       => 'attach_image',
						'admin_label' => true
					),
					array(
						'param_name' => 'back_image',
						'heading'    => esc_html__( 'Spotlight Image', 'wr-nitro' ),
						'type'       => 'attach_image',
					),
					array(
						'param_name'  => 'extra_class',
						'heading'     => esc_html__( 'Extra Class Name', 'wr-nitro' ),
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wr-nitro' ),
						'type'        => 'textfield',
					)
				)
			)
		);
	}

	/**
	 * List handle style of VC.
	 *
	 * @var  array
	 */
	protected static $vc_google_fonts = array();

	/**
	 * List handle style of VC for remove.
	 *
	 * @var  array
	 */
	protected static $google_fonts_style_handle = array();

	public static function google_font(){
		if ( is_singular() ) {
			global $post;

			$list_shortcode_vc = array(
				'vc_custom_heading' => array(
					'google_fonts'
				),
				'vc_gitem_post_author' => array(
					'google_fonts'
				),
				'vc_gitem_post_data' => array(
					'google_fonts'
				),
			);

			foreach( $list_shortcode_vc as $shortcode => $font_params ) {
				if ( has_shortcode( $post->post_content, $shortcode ) ) {

					self::$vc_google_fonts = array();

					self::get_attr( $post->post_content, $shortcode );

					$vc_google_fonts = self::$vc_google_fonts;

					if( $vc_google_fonts ) {
						foreach( $vc_google_fonts as $params ){
							foreach( $params as $param => $val ) {
								if( in_array( $param, $font_params ) ) {
									$val = urldecode( $val );

									$style_handle = explode( 'font_family:' , $val );
									$style_handle = $style_handle[1];
									$style_handle = explode( '|' , $style_handle );
									$style_handle = $style_handle[0];

									self::$google_fonts_style_handle[] = 'vc_google_fonts_' . vc_build_safe_css_class( $style_handle );

									$font_weigth = explode( ':' , $val );
									$leng_weigth = count( $font_weigth );

									$font_style = $font_weigth[ ( $leng_weigth - 1 ) ];
									$font_style = ( $font_style == 'italic' ) ? 'italic' : NULL;

									$font_weigth = $font_weigth[ ( $leng_weigth - 2 ) ] . $font_style;

									$font_family = explode( 'font_family:' , $val );
									$font_family = $font_family[1];

									$font_family = explode( ':' , $font_family );
									$font_family = $font_family[0];

									WR_Nitro_Helper::add_google_font( array( $font_family => array( $font_weigth ) ) );
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Dequeue style of google font.
	 *
	 * @return  void
	 */
	public static function remove_google_font(){
		if( ! self::$google_fonts_style_handle ) {
			return;
		}

		$handles = array_unique( self::$google_fonts_style_handle );

		foreach( $handles as $val ) {
			wp_dequeue_style( $val );
		}
	}

	/**
	 * Get shortcode attributes from outside function add_shortcode().
	 *
	 * @return  void
	 */
	public static function get_attr( $content, $shortcode, &$atts = array() ) {
		if ( preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes ) ) {

			foreach ( $shortcodes[2] as $index => $tag ) {
				if ( $tag == $shortcode ) {
					self::$vc_google_fonts[] = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
				}
			}

			foreach ( $shortcodes[5] as $shortcode_content ) {
				$atts[] = self::get_attr( $shortcode_content, $shortcode );
			}

			return $atts;
		}
	}

}
// Content element should extend WPBakeryShortCodesContainer class to inherit all required functionality
class WPBakeryShortCode_Nitro_Pricing extends WPBakeryShortCodesContainer {};
class WPBakeryShortCode_Nitro_Carousel extends WPBakeryShortCodesContainer {};
class WPBakeryShortCode_Nitro_Masonry extends WPBakeryShortCodesContainer {};
class WPBakeryShortCode_Nitro_Masonry_Element extends WPBakeryShortCodesContainer {};
class WPBakeryShortCode_Nitro_Pricing_Single extends WPBakeryShortCode {};

// Make placeholder image for shortcode banner
class WPBakeryShortCode_Nitro_Banner extends WPBakeryShortCode {
	protected function outputTitle( $title ) {
		$icon = $this->settings( 'icon' );
		if ( filter_var( $icon, FILTER_VALIDATE_URL ) ) {
			$icon = '';
		}
		$params = array(
			'icon' => $icon,
			'is_container' => $this->settings( 'is_container' ),
		);

		return '<h4 class="wpb_element_title"> ' . esc_attr( $title ) . '<img width="150" height="150" src="' . '" class="attachment-thumbnail vc_general vc_element-icon" data-name="image" alt="' . '" title="' . '" style="' . '"></h4>';
	}
}
