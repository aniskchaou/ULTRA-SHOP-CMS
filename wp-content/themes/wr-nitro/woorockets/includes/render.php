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
 * Class that provides common render functions.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Render {
	protected static $path = '';

	protected static $style = '';

	public static $content_class = '';

	public static $sidebar = '';

	public static $sidebar_class = '';

	public static $has_cart = false;

	/**
	 * Enqueue scripts and styles.
	 *
	 * @return  void
	 */
	public static function enqueue_scripts() {
		// Get theme options.
		$wr_nitro_options = WR_Nitro::get_options();

		// Load font Awesome.
		wp_dequeue_style( 'yith-wcwl-font-awesome' );
		wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/3rd-party/font-awesome/css/font-awesome.min.css' );

		// Load required stylesheets.
		wp_register_style( 'owl-carousel' , get_template_directory_uri() . '/assets/3rd-party/owl-carousel/owl.carousel.min.css' );
		wp_register_style( 'nivo-lightbox', get_template_directory_uri() . '/assets/3rd-party/nivo-lightbox/nivo-lightbox.css'   );

		// Load WR Nitro stylesheets.
		wp_register_style( 'wr-nitro-main', get_template_directory_uri() . '/assets/woorockets/css/main.css'   );
		wp_register_style( 'wr-nitro-mobile', get_template_directory_uri() . '/assets/woorockets/css/mobile.css'   );

		// Load underscore scripts
		wp_enqueue_script( 'underscore' );

		// Load required scripts.
		wp_register_script( 'owl-carousel'    , get_template_directory_uri() . '/assets/3rd-party/owl-carousel/owl.carousel.min.js'           , array(), false, true  );
		wp_register_script( 'isotope'         , get_template_directory_uri() . '/assets/3rd-party/isotope/isotope-pkgd.min.js'                , array(), false, true  );
		wp_register_script( 'skrollr'         , get_template_directory_uri() . '/assets/3rd-party/skrollr/skrollr.min.js'                     , array(), false, true  );
		wp_register_script( 'jquery-animation', get_template_directory_uri() . '/assets/3rd-party/jquery-animation/jquery-animation.min.js'   , array(), false, true  );
		wp_register_script( 'jquery-countdown', get_template_directory_uri() . '/assets/3rd-party/jquery-countdown/jquery.countdown.js'       , array(), false, false );
		wp_register_script( 'nivo-lightbox'   , get_template_directory_uri() . '/assets/3rd-party/nivo-lightbox/nivo-lightbox.min.js'         , array(), false, true  );
		wp_register_script( 'magnific-popup'  , get_template_directory_uri() . '/assets/3rd-party/magnific-popup/jquery-magnific-popup.min.js', array(), false, true );
		wp_register_script( 'scrollreveal'    , get_template_directory_uri() . '/assets/3rd-party/scrollreveal/scrollreveal.min.js'           , array(), false, true );

		// Jquery Scrollbar
		if ( is_singular( 'product' ) ) {
			wp_enqueue_style( 'scrollbar', get_template_directory_uri() . '/assets/3rd-party/jquery-scrollbar/jquery.scrollbar.css' );
			wp_enqueue_script( 'scrollbar', get_template_directory_uri() . '/assets/3rd-party/jquery-scrollbar/jquery.scrollbar.min.js', array(), false, true );
		}


		if( wp_is_mobile() ) {
			wp_enqueue_style( 'wr-nitro-mobile' );
		}

		// Load WR Nitro scripts.
		wp_enqueue_script( 'wr-nitro-functions', get_template_directory_uri() . '/assets/woorockets/js/functions.js', array( 'jquery' ), false, true );

		// Custom localize script
		wp_localize_script( 'wr-nitro-functions', 'WR_Data_Js', self::custom_data_js() );

		// Add the comment-reply script to the single post pages.
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Init scrollreveal
		if ( $wr_nitro_options['wc_archive_item_animation'] ) {
			wp_enqueue_script( 'scrollreveal' );
		}

		// Register Ajax action to get Google+ count.
		add_action( 'wp_ajax_nopriv_get_google_plus_count', array( __CLASS__, 'get_google_plus_count' ) );
		add_action( 'wp_ajax_get_google_plus_count'       , array( __CLASS__, 'get_google_plus_count' ) );

		add_filter( 'wr_font_url', array( 'WR_Nitro_Helper', 'filter_google_font' ) );

		// Load remote fonts.
		$remote_fonts = self::fonts_url();

		if ( is_string( $remote_fonts ) ) {
			wp_enqueue_style( 'wr-nitro-web-fonts', $remote_fonts );
		} else {
			wp_enqueue_style( 'wr-nitro-web-fonts', $remote_fonts[ 'google_fonts_url' ] );

			foreach ( $remote_fonts['custom_fonts'] as $name => $url ) {
				wp_add_inline_style(
					'wr-nitro-web-fonts',
					'@font-face { font-family: "' . $name . '"; src: url("' . $url . '"); }'
				);
			}
		}
	}

	/**
	 * List string translate for javascript.
	 *
	 * @return  void
	 */
	public static function custom_data_js() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();

		$data = array(
			'ajax_add_to_cart_single' => get_option( 'woocommerce_enable_ajax_add_to_cart_single' ),
			'buy_now_button_enabled'  => $wr_nitro_options['wc_buynow_btn'],
			'buy_now_checkout_type'   => $wr_nitro_options['wc_buynow_checkout'],
			'buy_now_button_action'   => $wr_nitro_options['wc_buynow_payment_info'],
			'in_buy_now_process'      => isset($GLOBALS['wr_in_buy_now_process']) ? 1 : 0,
			'checkout_url'            => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '',
			'View Wishlist'           => __( 'View Wishlist', 'wr-nitro' ),
			'View all'                => __( 'View all', 'wr-nitro' ),
			'removed_notice'          => __( '%s has been removed from your cart.', 'wr-nitro' ),
			'wr_countdown_days'       => __( 'days', 'wr-nitro' ),
			'wr_countdown_hrs'        => __( 'hrs', 'wr-nitro' ),
			'wr_countdown_mins'       => __( 'mins', 'wr-nitro' ),
			'wr_countdown_secs'       => __( 'secs', 'wr-nitro' ),
			'wr_countdown_secs'       => __( 'secs', 'wr-nitro' ),
			'wr_noice_tooltip'        => __( 'Please choose option to enable button.', 'wr-nitro' ),
			'wr_error_cannot_add'     => __( 'You cannot add that amount to the cart &mdash; we have %d in stock and you already have %d in your cart.', 'wr-nitro' ),
			'show_less'               => __( 'Less', 'wr-nitro' ),
			'show_more'               => __( 'More', 'wr-nitro' )
		);

		// Get data post title parallax
		if ( is_single() && $wr_nitro_options['blog_single_title_full_screen'] ) {
			$data['blogParallax'] = esc_attr( $wr_nitro_options['blog_single_title_full_screen'] );
		}

		// Get data page title parallax
		if ( $wr_nitro_options['wr_page_title_parallax'] ) {
			$data['pageParallax'] = esc_attr( $wr_nitro_options['wr_page_title_parallax'] );
		}

		// Get data body parallax
		if ( $wr_nitro_options['wr_layout_boxed_parallax'] && $wr_nitro_options['wr_page_layout_bg_image'] ) {
			$data['bodyParallax'] = esc_attr( $wr_nitro_options['wr_layout_boxed_parallax'] );
		}

		$data['offset']                          = esc_attr( $wr_nitro_options['wr_layout_offset'] );
		$data['wc_archive_style'] 	             = esc_attr( $wr_nitro_options['wc_archive_style'] );
		$data['wc_archive_layout_column']        = esc_attr( $wr_nitro_options['wc_archive_layout_column'] );
		$data['wc_archive_layout_column_gutter'] = esc_attr( $wr_nitro_options['wc_archive_layout_column_gutter'] );
		$data['rtl']                             = esc_attr( $wr_nitro_options['rtl'] );

		$data['onepage_nav'] = get_post_meta( get_the_ID(), 'page_scroll_navigation', true );
		$data['onepage_pagi'] = get_post_meta( get_the_ID(), 'page_scroll_pagigation', true );

		// Get option for permalink
		$data['permalink'] = ( get_option( 'permalink_structure' ) == '' ) ? 'plain' : '';

		return $data;
	}

	/**
	 * Register google font.
	 *
	 * @return  void
	 */
	public static function fonts_url() {
		// Get options
		$wr_nitro_options = WR_Nitro::get_options();

		$font_families = array();

		// Body font
		$body_font_type = $wr_nitro_options['body_font_type'];

		if ( $body_font_type == 'google' ) {
			if ( isset( $wr_nitro_options['body_google_font']['family'] ) ) {
				$font_name = esc_attr( $wr_nitro_options['body_google_font']['family'] );
				$font_weight = array( esc_attr( $wr_nitro_options['body_google_font']['fontWeight'] ) );

				// Merge array and delete values duplicated
				$font_families[ $font_name ] = isset( $font_families[ $font_name ] ) ? array_unique( array_merge( $font_families[ $font_name ], $font_weight ) ) : $font_weight;
			}
		}

		// Heading font
		$heading_font_type = $wr_nitro_options['heading_font_type'];

		if ( $heading_font_type == 'google' ) {
			if ( isset( $wr_nitro_options['heading_google_font']['family'] ) ) {
				$font_name = esc_attr( $wr_nitro_options['heading_google_font']['family'] );
				$font_weight = array( esc_attr( $wr_nitro_options['heading_google_font']['fontWeight'] ) );

				// Merge array and delete values duplicated
				$font_families[ $font_name ] = isset( $font_families[ $font_name ] ) ? array_unique( array_merge( $font_families[ $font_name ], $font_weight ) ) : $font_weight;
			}
		}

		$font_families = apply_filters( 'wr_font_url' , $font_families );

		// Parse array to string for url Google fonts
		$font_parse = array();
		foreach ( $font_families as $font_name => $font_weight ) {
			$font_parse[] = $font_name . ':'. implode( ',' , $font_weight );
		}

		$subset = array(
			'latin',
			'latin-ext'
		);

		if( call_user_func( 'is_' . 'plugin' . '_active', 'js_composer/js_composer.php' ) ) {
			$settings = get_option( 'wpb_js_google_fonts_subsets' );
			if ( is_array( $settings ) && ! empty( $settings ) ) {
				$subset = array_merge( $subset, $settings );
			}
		}

		$query_args = array(
			'family' => urldecode( implode( '|', $font_parse ) ),
			'subset' => urlencode( implode( ',' , $subset ) ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );

		return esc_url_raw( $fonts_url );
	}

	/**
	 * Embed inline custom styles.
	 *
	 * @return  void
	 */
	public static function custom_styles() {
		// Get theme options.
		$css = $boxed_image = $page_title_image = array();

		// Get option
		$wr_nitro_options = WR_Nitro::get_options();

		$buddypress_activated = ( function_exists( 'is_' . 'plugin' . '_active' ) && call_user_func( 'is_' . 'plugin' . '_active', 'buddypress/bp-loader.php' ) && class_exists( 'BuddyPress' ) );

		// Body font
		$body_font_type = $wr_nitro_options['body_font_type'];
		if ( $body_font_type == 'google' ) {
			$body_font = $wr_nitro_options['body_google_font'];

		} elseif ( $body_font_type == 'custom' ) {
			$body_font = $wr_nitro_options['body_custom_font'];
			if ( ! empty( $body_font ) ) {
				$body_font_name = current( explode( '.', current( array_slice( explode( '/', str_replace( '\\', '/', $body_font ) ), -1 ) ), 2 ) );
				$css[] = '
					@font-face {
						font-family: "' . esc_attr( $body_font_name ) . '";
						src: url(' . esc_url( $body_font ) . ');
					}
				';
			}
		}

		$body_font_size      = $wr_nitro_options['body_font_size'];
		$body_line_height    = $wr_nitro_options['body_line_height'];
		$body_letter_spacing = $wr_nitro_options['body_letter_spacing'];
		$body_letter_spacing = $wr_nitro_options['body_letter_spacing'];

		// Heading font
		$heading_font_type = $wr_nitro_options['heading_font_type'];
		if ( $heading_font_type == 'google' ) {
			$heading_font = $wr_nitro_options['heading_google_font'];

		} elseif ( $heading_font_type == 'custom' ) {
			$heading_font = $wr_nitro_options['heading_custom_font'];

			if ( ! empty( $heading_font ) ) {
				$heading_font_name = current( explode( '.', current( array_slice( explode( '/', str_replace( '\\', '/', $heading_font ) ), -1 ) ), 2 ) );
				$css[] = '
					@font-face {
						font-family: "' . esc_attr( $heading_font_name ) . '";
						src: url(' . esc_url( $heading_font ) . ');
					}
				';
			}
		}
		$heading_font_size      = $wr_nitro_options['heading_font_size'];
		$heading_line_height    = $wr_nitro_options['heading_line_height'];
		$heading_letter_spacing = $wr_nitro_options['heading_letter_spacing'];

		// Layout offset
		$offset    = $wr_nitro_options['wr_layout_offset'];
		$offset_bg = $wr_nitro_options['wr_layout_offset_color'];

		// Content width
		$content_width_unit = ( 'pixel' == $wr_nitro_options['wr_layout_content_width_unit'] ) ? 'px' : '%';

		if ( 'px' == $content_width_unit ) {
			$content_width_layout = $wr_nitro_options['wr_layout_content_width'] . $content_width_unit;
		} else {
			$content_width_layout = $wr_nitro_options['wr_layout_content_width_percentage'] . $content_width_unit;
		}

		$gutter_width = $wr_nitro_options['wr_layout_gutter_width'];
		$boxed        = $wr_nitro_options['wr_layout_boxed'];

		$bg_mask_color = $wr_nitro_options['wr_layout_boxed_bg_mask_color'];

		// Page Loader
		$page_loader_type = $wr_nitro_options['page_loader'];
		$page_loader_css  = $wr_nitro_options['page_loader_css'];

		// Page Layout
		$page_sidebar_width = $wr_nitro_options['wr_page_layout_sidebar_width'];

		// Get Color Schemes Settings
		$body_text_color = $heading_color = $page_loader_icon_color = $page_loader_bg_color = $footer_bg_color = $footer_text_color = $footer_heading_color = $footer_bot_bgcolor = $footer_bot_textcolor = $footer_top_link = $footer_top_hover_link = $footer_bot_link = $footer_bot_hover_link = $page_title_link = $page_title_link_hover = $btn_solid = $btn_solid_hover = $btn_outline = $btn_outline_hover = $cat_column_gutter = '';

		// Blog layout
		$blog_layout               = $wr_nitro_options['blog_layout'];
		$blog_sidebar_width        = $wr_nitro_options['blog_sidebar_width'];
		$blog_single_layout        = $wr_nitro_options['blog_single_layout'];
		$blog_single_sidebar_width = $wr_nitro_options['blog_single_sidebar_width'];

		// WooCommerce layout
		$wc_layout               = $wr_nitro_options['wc_archive_layout'];
		$wc_sidebar_width        = $wr_nitro_options['wc_archive_sidebar_width'];
		$wc_single_layout        = $wr_nitro_options['wc_single_layout'];
		$wc_single_sidebar_width = $wr_nitro_options['wc_single_sidebar_width'];

		// Page title
		$page_title_pdtop      = $wr_nitro_options['wr_page_title_padding_top'];
		$page_title_pdbottom   = $wr_nitro_options['wr_page_title_padding_bottom'];
		$page_title_min_height = $wr_nitro_options['wr_page_title_heading_min_height'];
		$page_title_mask_color = $wr_nitro_options['wr_page_title_mask_color'];

		// Get background color and image
		if ( ! $wr_nitro_options['use_global'] && ! is_single() ) {
			$image_id = $wr_nitro_options['wr_page_title_bg_image'];
			$page_title_bg_image = isset( $image_id ) ? wp_get_attachment_url( $image_id ) : '';
		} else {
			$page_title_bg_image = $wr_nitro_options['wr_page_title_bg_image'];
		}

		if ( ! empty( $page_title_bg_image ) ) {
			$page_title_image[] = 'background-image: url(' . $page_title_bg_image . ');';
			$page_title_image[] = 'background-size: ' . $wr_nitro_options['wr_page_title_size'] . ';';
			$page_title_image[] = 'background-repeat: ' . $wr_nitro_options['wr_page_title_repeat'] . ';';
			$page_title_image[] = 'background-position: ' . $wr_nitro_options['wr_page_title_position'] . ';';
			$page_title_image[] = 'background-attachment: ' . $wr_nitro_options['wr_page_title_attachment'] . ';';
		}
		$page_title_bg_color = $wr_nitro_options['wr_page_title_bg_color'];

		// Page title heading font
		$page_title_heading_font           = $wr_nitro_options['wr_page_title_heading_font'];
		$page_title_heading_font_size      = $wr_nitro_options['wr_page_title_heading_font_size'];
		$page_title_heading_line_height    = $wr_nitro_options['wr_page_title_heading_line_height'];
		$page_title_heading_letter_spacing = $wr_nitro_options['wr_page_title_heading_letter_spacing'];

		// Page title description
		$page_title_desc_font           = get_post_meta( get_the_ID(), 'page_title_desc_font', true );
		$page_title_desc_font_size      = get_post_meta( get_the_ID(), 'page_title_desc_font_size', true );
		$page_title_desc_line_height    = get_post_meta( get_the_ID(), 'page_title_desc_line_height', true );
		$page_title_desc_letter_spacing = get_post_meta( get_the_ID(), 'page_title_desc_letter_spacing', true );

		// 404 Page setting
		$error_bg_color      = $wr_nitro_options['page_404_bg_color'];
		$error_font_size     = $wr_nitro_options['page_404_title_font_size'];
		$error_font_color    = $wr_nitro_options['page_404_title_color'];
		$error_bg_image      = $wr_nitro_options['page_404_bg_image'];
		$error_bg_size       = $wr_nitro_options['page_404_bg_image_size'];
		$error_bg_position   = $wr_nitro_options['page_404_bg_image_position'];
		$error_bg_attachment = $wr_nitro_options['page_404_bg_image_attachment'];
		$error_bg_repeat     = $wr_nitro_options['page_404_bg_image_repeat'];

		// Under Construction Background setting
		$construction_mode          = $wr_nitro_options['under_construction'];
		$construction_bg_color      = $wr_nitro_options['under_construction_bg_color'];
		$construction_bg_image      = $wr_nitro_options['under_construction_bg_image'];
		$construction_bg_position   = $wr_nitro_options['under_construction_bg_image_position'];
		$construction_bg_repeat     = $wr_nitro_options['under_construction_bg_image_repeat'];
		$construction_bg_size       = $wr_nitro_options['under_construction_bg_image_size'];
		$construction_bg_attachment = $wr_nitro_options['under_construction_bg_image_attachment'];

		// WooCommerce Settings
		$product_style         = $wr_nitro_options['wc_archive_style'];
		$product_column        = $wr_nitro_options['wc_archive_layout_column'];
		$product_column_gutter = $wr_nitro_options['wc_archive_layout_column_gutter'];
		$product_item_layout   = $wr_nitro_options['wc_archive_item_layout'];
		$hover_mask_bg         = $wr_nitro_options['wc_archive_item_mask_color'];
		$product_single_bg     = $wr_nitro_options['wc_single_product_custom_bg'];
		$image_bg_color        = get_post_meta( get_the_ID(), 'image_bg_color', true );
		if ( get_option( 'woocommerce_shop_page_display' ) || get_option( 'woocommerce_category_archive_display' ) ) {
			$cat_column_gutter = $wr_nitro_options['wc_categories_layout_column_gutter'];
		}
		// Get single style
		$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		if ( $single_style == 0 ) {
			$single_style = $wr_nitro_options['wc_single_style'];
		} else {
			$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		}

		// Get footer background settings.
		$footer_fullwidth     = $wr_nitro_options['footer_fullwidth'];
		$footer_bg_image      = $wr_nitro_options['footer_bg_image'];
		if ( ! empty( $footer_bg_image ) ) {
			$footer_bg_size       = $wr_nitro_options['footer_bg_image_size'];
			$footer_bg_repeat     = $wr_nitro_options['footer_bg_image_repeat'];
			$footer_bg_position   = $wr_nitro_options['footer_bg_image_position'];
			$footer_bg_attachment = $wr_nitro_options['footer_bg_image_attachment'];
		}

		// Button primary settings
		$btn_font           = $wr_nitro_options['btn_font'];
		$btn_font_size      = $wr_nitro_options['btn_font_size'];
		$btn_line_height    = $wr_nitro_options['btn_line_height'];
		$btn_letter_spacing = $wr_nitro_options['btn_letter_spacing'];
		$btn_border_width   = $wr_nitro_options['btn_border_width'];
		$btn_border_radius  = $wr_nitro_options['btn_border_radius'];
		$btn_padding        = $wr_nitro_options['btn_padding'];

		// Button primary color
		$btn_primary_bg     = $wr_nitro_options['btn_primary_bg_color'];
		$btn_primary        = $wr_nitro_options['btn_primary_color'];
		$btn_primary_border = $wr_nitro_options['btn_primary_border_color'];

		// Button secondary color
		$btn_secondary_bg     = $wr_nitro_options['btn_secondary_bg_color'];
		$btn_secondary        = $wr_nitro_options['btn_secondary_color'];
		$btn_secondary_border = $wr_nitro_options['btn_secondary_border_color'];

		if ( is_array( $wr_nitro_options['wr_page_title_link_colors'] ) ) {
			$page_title_link       = $wr_nitro_options['wr_page_title_link_colors']['normal'];
			$page_title_link_hover = $wr_nitro_options['wr_page_title_link_colors']['hover'];
		} else {
			$wr_page_title_link_colors = @unserialize( $wr_nitro_options['wr_page_title_link_colors'] );
			// Check is string serialize
			if ( $wr_page_title_link_colors != false ) {
				$page_title_link       = $wr_page_title_link_colors['normal'];
				$page_title_link_hover = $wr_page_title_link_colors['hover'];
			}
		}

		$content_bg_color = $wr_nitro_options['wr_general_container_color'];
		$body_bg_color    = $wr_nitro_options['wr_page_body_bg_color'];

		$page_title_color = $wr_nitro_options['wr_page_title_color'];

		$body_text_color = $wr_nitro_options['content_body_color']['body_text'];
		$heading_color   = $wr_nitro_options['content_body_color']['heading_text'];
		$line_color      = $wr_nitro_options['general_line_color'];
		$overlay_bg      = $wr_nitro_options['general_overlay_color'];
		$fields_bg       = $wr_nitro_options['general_fields_bg'];

		$page_loader_icon_color = $wr_nitro_options['content_loader_color']['icon'];
		$page_loader_bg_color   = $wr_nitro_options['content_loader_color']['bg'];

		$meta_text  = $wr_nitro_options['content_meta_color'];

		$footer_bg_color       = $wr_nitro_options['footer_top_bg_color'];
		$footer_text_color     = $wr_nitro_options['footer_top_color']['text'];
		$footer_heading_color  = $wr_nitro_options['footer_top_color']['heading'];
		$footer_bot_bgcolor    = $wr_nitro_options['footer_bot_color']['bg'];
		$footer_bot_textcolor  = $wr_nitro_options['footer_bot_color']['text'];
		$footer_top_link       = $wr_nitro_options['footer_top_link_color']['normal'];
		$footer_top_hover_link = $wr_nitro_options['footer_top_link_color']['hover'];
		$footer_bot_link       = $wr_nitro_options['footer_bot_link_color']['normal'];
		$footer_bot_hover_link = $wr_nitro_options['footer_bot_link_color']['hover'];

		$quotes_font           = $wr_nitro_options['quotes_font'];

		// Get settings for each page
		if ( ! $wr_nitro_options['use_global'] ) {
			$page_bg_image_id         = get_post_meta( get_the_ID(), 'wr_page_layout_bg_image', true  );
			$page_bg_image_position   = get_post_meta( get_the_ID(), 'wr_page_layout_position', true  );
			$page_bg_image_repeat     = get_post_meta( get_the_ID(), 'wr_page_layout_repeat', true  );
			$page_bg_image_size       = get_post_meta( get_the_ID(), 'wr_page_layout_size', true  );
			$page_bg_image_attachment = get_post_meta( get_the_ID(), 'wr_page_layout_attachment', true  );
			$bg_image                 = isset( $page_bg_image_id ) ? wp_get_attachment_url( $page_bg_image_id ) : '';

			if ( isset( $page_bg_image_id ) ) {
				if ( isset( $bg_image ) && $bg_image ) $body_bg_image[]            = 'background-image: url(' . $bg_image . ');';
				if ( isset( $page_bg_image_size ) && $page_bg_image_size ) $body_bg_image[]       = 'background-size: ' . $page_bg_image_size . ';';
				if ( isset( $page_bg_image_repeat ) &&  $page_bg_image_repeat ) $body_bg_image[]     = 'background-repeat: ' . $page_bg_image_repeat . ';';
				if ( isset( $page_bg_image_position ) && $page_bg_image_position ) $body_bg_image[]   = 'background-position: ' . $page_bg_image_position . ';';
				if ( isset( $page_bg_image_attachment ) && $page_bg_image_attachment ) $body_bg_image[] = 'background-attachment: ' . $page_bg_image_attachment . ';';
			}
		} else {
			$bg_image = $wr_nitro_options['wr_layout_boxed_bg_image'];
			if ( ! empty( $bg_image ) ) {
				$body_bg_image[] = 'background-image: url(' . $bg_image . ');';
				$body_bg_image[] = 'background-size: ' . $wr_nitro_options['wr_layout_boxed_size'] . ';';
				$body_bg_image[] = 'background-repeat: ' . $wr_nitro_options['wr_layout_boxed_repeat'] . ';';
				$body_bg_image[] = 'background-position: ' . $wr_nitro_options['wr_layout_boxed_position'] . ';';
				$body_bg_image[] = 'background-attachment: ' . $wr_nitro_options['wr_layout_boxed_attachment'] . ';';
			}
		}

		// Classes in customizer preview
		$preview_outer_bg = $preview_inner_bg = $preview_fields_bg = $preview_offset_bg = $preview_mask_bg = $preview_secondary_bg_normal = $preview_secondary_bg_text = $preview_secondary_bg_border = $preview_secondary_bg_border_left = $preview_secondary_bg_border_right = $preview_body_text_normal = $preview_heading_normal = $preview_heading_bg = $preview_line_color_normal = $preview_line_color_bg = $preview_btn_secondary_bg_normal = $preview_page_site_title = $preview_page_title_text = $preview_page_title_heading = $preview_page_title_link_normal = $preview_page_title_link_hover = $preview_btn_primary_normal = $preview_btn_primary_hover = $preview_btn_secondary_normal = $preview_btn_secondary_hover = $preview_footer_top_bg = $preview_footer_top_text = $preview_footer_top_heading = $preview_footer_top_link_normal = $preview_footer_top_link_hover = $preview_footer_bot_normal = $preview_footer_bot_link_normal = $preview_footer_bot_link_hover = '';

		if ( is_customize_preview() ) {
			$preview_outer_bg                   = '.preview_outer_bg,';
			$preview_inner_bg                   = '.preview_inner_bg,';
			$preview_fields_bg                  = '.preview_fields_bg,';
			$preview_offset_bg                  = '.preview_offset_bg:after,';
			$preview_mask_bg                    = '.preview_mask_bg,';
			$preview_secondary_bg_normal        = '.preview_secondary_bg_normal,';
			$preview_secondary_bg_text          = '.preview_secondary_bg_text,';
			$preview_secondary_bg_border        = '.preview_secondary_bg_border,';
			$preview_secondary_bg_border_left   = '.preview_secondary_bg_border_left,';
			$preview_secondary_bg_border_right  = '.preview_secondary_bg_border_right,';
			$preview_body_text_normal           = '.preview_body_text_normal,';
			$preview_heading_normal             = '.preview_heading_normal,';
			$preview_heading_bg                 = '.preview_heading_bg,';
			$preview_line_color_normal          = '.preview_line_color_normal,';
			$preview_line_color_bg              = '.preview_line_color_bg,';
			$preview_btn_primary_normal         = '.preview_btn_primary_normal,';
			$preview_btn_primary_hover          = '.preview_btn_primary_hover,';
			$preview_btn_secondary_normal       = '.preview_btn_secondary_normal,';
			$preview_btn_secondary_hover        = '.preview_btn_secondary_hover,';
			$preview_footer_top_bg              = '.preview_footer_top_bg,';
			$preview_footer_top_text            = '.preview_footer_top_text,';
			$preview_footer_top_heading         = '.preview_footer_top_heading,';
			$preview_footer_top_link_normal     = '.preview_footer_top_link_normal,';
			$preview_footer_top_link_hover      = '.preview_footer_top_link_hover,';
			$preview_footer_bot_normal          = '.preview_footer_bot_normal,';
			$preview_footer_bot_link_normal     = '.preview_footer_bot_link_normal,';
			$preview_footer_bot_link_hover      = '.preview_footer_bot_link_hover,';

			if ( $wr_nitro_options['use_global'] ) {
				$preview_page_site_title            = '.preview_page_site_title,';
				$preview_page_title_text            = '.preview_page_title_text,';
				$preview_page_title_heading         = '.preview_page_title_heading,';
				$preview_page_title_link_normal     = '.preview_page_title_link_normal,';
				$preview_page_title_link_hover      = '.preview_page_title_link_hover,';
			}
		}

		// Generate custom styles
		$css[] = 'body {';
			if ( $body_font_type == 'google' ) {
				if ( $body_font['family'] != 'Lato' && ! empty( $body_font['family'] ) ) {
					$css[] = 'font-family: "' . esc_attr( $body_font['family'] ) . '";';
				}
				$css[] = $body_font['fontWeight'] ? 'font-weight: ' . absint( $body_font['fontWeight'] ) . ';' : '';
			} elseif ( $body_font_type == 'custom' ) {
				if ( ! empty( $body_font ) ) {
					$css[] = 'font-family: "' . esc_attr( $body_font_name ) . '";';
				}
			} else {
				$css[] = 'font-family: "' . esc_attr( $wr_nitro_options['body_standard_font'] ) . '";';
			}
			$css[] = '
				font-size: ' . esc_attr( $body_font_size ) . '%;
				letter-spacing: ' . esc_attr( $body_letter_spacing ) . 'px;
				line-height: ' . esc_attr( $body_line_height ) . 'px;';

				if ( isset( $body_bg_image ) && ! empty( $bg_image ) ) {
					$css[] = implode( ' ', $body_bg_image );
				}
				$css[] = '
			}
			h1,h2,h3,h4,h5,h6 {
				letter-spacing: ' . esc_attr( $heading_letter_spacing ) . 'px;';

				if ( $heading_font_type == 'google' ) {
					if ( ! empty( $heading_font['family'] ) ) {
						$css[] = 'font-family: "' . esc_attr( $heading_font['family'] ) . '";';
					}
					$css[] = $heading_font['fontWeight'] ? 'font-weight: ' . absint( $heading_font['fontWeight'] ) . ';' : '';
					$css[] = $heading_font['italic']     ? 'font-style: italic;'                        : '';
					$css[] = $heading_font['underline']  ? 'text-decoration: underline;'                : '';
					$css[] = $heading_font['uppercase']  ? 'text-transform: uppercase;'                 : '';
				} elseif ( $heading_font_type == 'custom' ) {
					if ( ! empty( $heading_font ) ) {
						$css[] = 'font-family: "' . esc_attr( $heading_font_name ) . '";';
					}
				} else {
					$css[] = 'font-family: "' . esc_attr( $wr_nitro_options['heading_standard_font'] ) . '";';
				}

				$css[] = '
			}
			h1 {
				font-size: ' . esc_attr( intval( $heading_font_size * 3.998 ) ) . 'px;
				line-height: ' . esc_attr( $heading_line_height * 3.998 ) . 'px;
			}
			h2 {
				font-size: ' . esc_attr( intval( $heading_font_size * 2.827 ) ) . 'px;
				line-height: ' . esc_attr( $heading_line_height * 2.827 ) . 'px;
			}
			h3 {
				font-size: ' . esc_attr( intval( $heading_font_size * 1.999 ) ) . 'px;
				line-height: ' . esc_attr( $heading_line_height * 1.999 ) . 'px;
			}
			h4 {
				font-size: ' . esc_attr( intval( $heading_font_size * 1.414 ) ) . 'px;
				line-height: ' . esc_attr( $heading_line_height * 1.414 ) . 'px;
			}
			h5 {
				font-size: ' . esc_attr( intval( $heading_font_size ) ) . 'px;
				line-height: ' . esc_attr( $heading_line_height * 1.2 ) . 'px;
			}
			h6 {
				font-size: ' . esc_attr( intval( $heading_font_size * 0.707 ) ) . 'px;
				line-height: ' . esc_attr( $heading_line_height * 0.707 ) . 'px;
			}
		';

		$css[] = '
			.format-quote .quote-content,
			blockquote {';
				if ( ! empty( $quotes_font['family'] ) ) {
					$css[] = 'font-family: "' . esc_attr( $quotes_font['family'] ) . '";';
				}
				$css[] = $quotes_font['italic']     ? 'font-style: italic;' : '';
				$css[] = $quotes_font['underline']  ? 'text-decoration: underline;' : '';
				$css[] = $quotes_font['uppercase']  ? 'text-transform: uppercase;' : '';
				$css[] = '
			}
		';

		$css[] = '
			@media only screen and (min-width: 1024px) {
				.offset {
					position: relative;
					padding: ' . esc_attr( $offset ) . 'px;
				}
				' . $preview_offset_bg . '
				.offset:after {
					border-width: ' . esc_attr( $offset ) . 'px;
					border-color: ' . esc_attr( $offset_bg ) . ';
				}
				.woocommerce-demo-store.offset {
					padding-top: ' . ( $offset + 52 ) . 'px;
				}
				.woocommerce-demo-store.offset .demo_store {
					top: ' . $offset . 'px;
				}
				.admin-bar.woocommerce-demo-store.offset .demo_store {
					top: ' . ( $offset + 32 ) . 'px;
				}
				.mfp-wrap {
					top: ' . esc_attr( $offset + 10 ) . 'px;
					height: calc(100% - ' . esc_attr( $offset * 2 + 20 ) . 'px);
				}
				.admin-bar .mfp-wrap {
					top: ' . esc_attr( $offset + 42 ) . 'px;
					height: calc(100% - ' . esc_attr( $offset * 2 + 52 ) . 'px);
				}
			}
		';

		$css[] = '
			.row {
				margin-left: -' . esc_attr( $gutter_width ) / 2 . 'px;
				margin-right: -' . esc_attr( $gutter_width ) / 2 . 'px;
			}
			.main-content, .primary-sidebar {
				padding-left: ' . esc_attr( $gutter_width ) / 2 . 'px;
				padding-right: ' . esc_attr( $gutter_width ) / 2 . 'px;
			}
			.primary-sidebar .widget {
				margin-bottom: ' . esc_attr( $gutter_width ) . 'px;
			}
			@media screen and (max-width: 800px) {
				.style-4 .p-single-images .product__badge {
					right: ' . esc_attr( $gutter_width ) / 2 . 'px;
				}
			}
		';

		if ( $boxed ) {
			$css[] = '
				' . $preview_outer_bg . '
				body.boxed {
					background-color: ' . esc_attr( $body_bg_color ) . ';
				}
				body.boxed .wrapper {
					max-width: ' . esc_attr( $content_width_layout ) . ';
				}
				body.boxed .vc_row {
					margin-left: 0;
					margin-right: 0;
				}
				@media screen and (max-width: 1024px) {
					body.boxed .wrapper {
						max-width: 100% !important;
					}
				}
			';
		} else {
			$css[] = '
				.container {
					max-width: ' . esc_attr( $content_width_layout ) . ';
				}
				@media screen and (max-width: 1024px) {
					.container {
						max-width: 100% !important;
					}
				}
			';
		}

		if ( $bg_mask_color && $bg_image ) {
			$css[] = '
				body.mask {
					position: relative;
				}
				' . $preview_mask_bg . '
				body.mask .wrapper-outer:after {
					background-color: ' . esc_attr( $bg_mask_color ) . ';
				}
			';
		}

		// Page title
		$custom_page_title = $wr_nitro_options['wr_page_title_custom_color'];

		$css[] = $preview_page_site_title;
		$css[] = $custom_page_title ? $preview_secondary_bg_normal : '';

		// Check is page product category
		if( get_query_var( 'taxonomy' ) == 'product_cat' ) {
			$categories = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ), ARRAY_A );

			$image_banner_id = absint( get_term_meta( $categories['term_id'], 'image_banner_id', true ) );

			if( $image_banner_id ) {
				$image_url = wp_get_attachment_url( $image_banner_id );

				if( $image_url ) {
					$wr_background_size = esc_attr( get_term_meta( $categories['term_id'], 'wr_background_size', true ) );
					$wr_background_size = $wr_background_size ? $wr_background_size : 'auto';

					$wr_background_repeat = esc_attr( get_term_meta( $categories['term_id'], 'wr_background_repeat', true ) );
					$wr_background_repeat = $wr_background_repeat ? $wr_background_repeat : 'no-repeat';

					$wr_background_position = esc_attr( get_term_meta( $categories['term_id'], 'wr_background_position', true ) );
					$wr_background_position = $wr_background_position ? $wr_background_position : 'left top';

					$wr_background_attachment = esc_attr( get_term_meta( $categories['term_id'], 'wr_background_attachment', true ) );
					$wr_background_attachment = $wr_background_attachment ? $wr_background_attachment : 'scroll';

					$page_title_image = array();
					$page_title_image[] = 'background-image: url(' . $image_url . ');';
					$page_title_image[] = 'background-size: ' . $wr_background_size . ';';
					$page_title_image[] = 'background-repeat: ' . $wr_background_repeat . ';';
					$page_title_image[] = 'background-position: ' . $wr_background_position . ';';
					$page_title_image[] = 'background-attachment: ' . $wr_background_attachment . ';';
				}
			}
		}

		$css[] = '.site-title {';
			$css[] = $page_title_pdtop ? 'padding-top: ' . esc_attr( $page_title_pdtop ) . 'px;' : '';
			$css[] = $page_title_pdbottom ? 'padding-bottom: ' . esc_attr( $page_title_pdbottom ) . 'px;' : '';
			$css[] = $page_title_min_height != 0 ? 'min-height: ' . esc_attr( $page_title_min_height ) . 'px;' : '';

			if ( ! $custom_page_title ) {
				$css[] = $page_title_bg_color ? 'background-color: ' . esc_attr( $page_title_bg_color ) . ';' : '';
				$css[] = isset( $page_title_color['body'] ) ? 'color: ' . esc_attr( $page_title_color['body'] ) . ';' : '';
			} else {
				$css[] = $overlay_bg ? 'background-color: ' . esc_attr( $overlay_bg ) . ';' : '';
			}

			$css[] = implode( ' ', $page_title_image );

		$css[] = '}';

		if ( ! $custom_page_title ) {
			$css[] = '
				' . $preview_page_title_link_normal . '
				.site-title .breadcrumbs a,
				.woocommerce-breadcrumb a {
					color: ' . esc_attr( $page_title_link ) . ';
				}
				' . $preview_page_title_link_hover . '
				.site-title .breadcrumbs a:hover,
				.woocommerce-breadcrumb a:hover {
					color: ' . esc_attr( $page_title_link_hover ) . ';
				}
			';

			if ( ! empty( $page_title_bg_image ) ) {
				$css[] = '
					.site-title .mask {
						background: ' . esc_attr( $page_title_mask_color ) . ';
					}
				';
			}
		}

		$css[] = '
			' . $preview_page_title_heading . '
			.site-title h1 {
				font-size: ' . esc_attr( $page_title_heading_font_size ) . 'px;
				line-height: ' . esc_attr( $page_title_heading_line_height ) . 'px;';

				$css[] = ( $page_title_heading_letter_spacing != 0 ) ? 'letter-spacing: ' . $page_title_heading_letter_spacing . 'px;' : '';
				$css[] = ( isset( $page_title_heading_font['italic'] )  && $page_title_heading_font['italic'] ) ? 'font-style: italic;' : '';
				$css[] = ( isset( $page_title_heading_font['underline'] ) && $page_title_heading_font['underline'] ) ? 'text-decoration: underline;' : '';
				$css[] = ( isset( $page_title_heading_font['uppercase'] ) && $page_title_heading_font['uppercase'] ) ? 'text-transform: uppercase;'  : '';
				if ( ! $custom_page_title ) {
					$css[] = isset( $page_title_color['head'] ) ? 'color: ' . esc_attr( $page_title_color['head'] ) . ';' : '';
				}
				$css[] = '
			}
		';

		$css[] = '
			.site-title .desc {
				font-size: ' . esc_attr( $page_title_desc_font_size ) . 'px;
				line-height: ' . esc_attr( $page_title_desc_line_height ) . 'px;';

				$css[] = ( $page_title_desc_letter_spacing != 0 ) ? 'letter-spacing: ' . $page_title_desc_letter_spacing . 'px;' : '';
				$css[] = ( isset( $page_title_desc_font['italic'] )  && $page_title_desc_font['italic'] ) ? 'font-style: italic;' : '';
				$css[] = ( isset( $page_title_desc_font['underline'] ) && $page_title_desc_font['underline'] ) ? 'text-decoration: underline;' : '';
				$css[] = ( isset( $page_title_desc_font['uppercase'] ) && $page_title_desc_font['uppercase'] ) ? 'text-transform: uppercase;'  : '';

				$css[] = '
			}
		';

		$css[] = '
			.post-title {
				padding-top: ' . $wr_nitro_options['blog_single_title_padding_top'] . 'px;
				padding-bottom: ' . $wr_nitro_options['blog_single_title_padding_bottom'] . 'px;
			}
			.post-title .entry-title {
				font-size: ' . $wr_nitro_options['blog_single_title_font_size'] . 'px;
				line-height: ' . $wr_nitro_options['blog_single_title_font_size'] . 'px;
			}
		';

		// Grid layout
		$css[] = '
			#shop-main .products.grid-layout:not(.boxed),
			#shop-main .products.grid-layout.item-style-6 {
				margin: 0 -' . ( $product_column_gutter / 2 ) . 'px;
			}

			#woof_results_by_ajax .products {
				width: calc(100% + ' . $product_column_gutter . 'px);
			}
			#shop-main .products.grid-layout .product {
				padding: ' . ( $product_column_gutter / 2 ) . 'px;
			}
			#shop-sidebar .widget {
				margin-bottom: ' . $product_column_gutter . 'px;
			}
			@media (min-width: 769px) {
				#shop-main .products.grid-layout .product:nth-child(' . $product_column . 'n+1) {
					clear: both;
				}
			}
			@media (max-width: 768px) {
				#shop-main .products.grid-layout .product:nth-child(2n+1) {
					clear: both;
				}
			}
		';
		if ( empty( $product_column_gutter ) && '6' == $product_item_layout && $wr_nitro_options['wc_archive_border_wrap'] ) {
			$css[] = '
				.item-style-6.boxed .product {
					margin: 0 -1px -1px 0 !important;
				}
			';
		}

		// Masonry layout
		$css[] = '
			#shop-main .products.masonry-layout {
				margin: 0 -' . ( $product_column_gutter / 2 ) . 'px;
			}
			#shop-main .products.masonry-layout .product {
				padding: ' . ( $product_column_gutter / 2 ) . 'px ;
			}
			#shop-main .products.masonry-layout.item-style-4 .product {
				padding-bottom: ' . ( $product_column_gutter - 20 ) . 'px;
			}
		';

		if ( ! empty( $cat_column_gutter ) ) {
			$css[] = '
				.row.categories {
					margin: 0 -' . ( $cat_column_gutter / 2 ) . 'px 10px;
				}
				.row.categories .cat-item {
					padding: ' . ( $cat_column_gutter / 2 ) . 'px;
				}
			';
		}

		$css[] = $hover_mask_bg ? '.product__image.mask .mask-inner { background: ' . esc_attr( $hover_mask_bg ) . ' }' : '';

		if ( '1' == $single_style || '4' == $single_style ) {

			if ( $image_bg_color == '' ) {
				$css[] = '.p-single-top { background: ' . esc_attr( $product_single_bg ) . '; }';

			} else {
				$css[] = '.p-single-top { background: ' . esc_attr( $image_bg_color ) . '; }';
			}
		}

		if ( '2' == $single_style && ! empty( $offset ) ) {
			$css[] = '
				.p-single-nav > .aic.right {
					right: ' . esc_attr( $offset ) . 'px;
				}
				.p-single-nav > .aic.left {
					left: ' . esc_attr( $offset ) . 'px;
				}
			';
		}

		$css[] = $offset ? '.actions-fixed { right: ' . esc_attr( $offset + 10 ) . 'px;}' : '';

		if ( 'none' != $page_loader_type ) {
			$css[] = '
				.pageloader {
					background: ' . esc_attr( $page_loader_bg_color ) . ';
				}
			';
		}

		if ( 'css' == $page_loader_type ) {
			if ( '1' == $page_loader_css ) {
				$css[] = '
					.wr-loader-1 .wr-loader {
						-webkit-animation-delay: -.16s;
						animation-delay: -.16s;
					}
					.wr-loader-1 .wr-loader,
					.wr-loader-1 .wr-loader:before,
					.wr-loader-1 .wr-loader:after {
						background: ' . esc_attr(  $page_loader_icon_color ) . ';
						-webkit-animation: loader-1 1s infinite ease-in-out;
						animation: loader-1 1s infinite ease-in-out;
						width: .4em;
						height: 1.4em;
					}
					.wr-loader-1 .wr-loader:before,
					.wr-loader-1 .wr-loader:after {
						top: 0;
					}
					.wr-loader-1 .wr-loader:before {
						left: -.6em;
						-webkit-animation-delay: -0.32s;
						animation-delay: -0.32s;
					}
					.wr-loader-1 .wr-loader:after {
						left: .6em;
						-webkit-animation-delay: .16s;
						animation-delay: .16s;
					}
					@-webkit-keyframes loader-1 {
						0%,
						80%,
						100% {
							box-shadow: 0 0 ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						40% {
							box-shadow: 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ';
							height: 2em;
						}
					}
					@keyframes loader-1 {
						0%,
						80%,
						100% {
							box-shadow: 0 0 ' . esc_attr(  $page_loader_icon_color ) . ';
							height: 1.6em;
						}
						40% {
							box-shadow: 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ';
							height: 2em;
						}
					}
				';

			} elseif ( '2' == $page_loader_css ) {
				$css[] = '
					.wr-loader-2 .wr-loader {
						width: 3em;
						height: 3em;
						box-shadow: inset 0 0 0 .4em ' . esc_attr(  $page_loader_icon_color ) . ';
					}
					.wr-loader-2 .wr-loader,
					.wr-loader-2 .wr-loader:before,
					.wr-loader-2 .wr-loader:after {
						border-radius: 50%;
					}
					.wr-loader-2 .wr-loader:before,
					.wr-loader-2 .wr-loader:after {
						width: 1.7em;
						height: 3.2em;
						top: -0.1em;
						background: ' . esc_attr( $page_loader_bg_color ) . ';
					}
					.wr-loader-2 .wr-loader:before {
						border-radius: 3.2em 0 0 3.2em;
						left: -0.1em;
						-webkit-transform-origin: 1.7em 1.6em;
						transform-origin: 1.7em 1.6em;
						-webkit-animation: loader-2 2s infinite ease 1.5s;
						animation: loader-2 2s infinite ease 1.5s;
					}
					.wr-loader-2 .wr-loader:after {
						border-radius: 0 3.2em 3.2em 0;
						left: 1.6em;
						-webkit-transform-origin: 0px 1.6em;
						transform-origin: 0px 1.6em;
						-webkit-animation: loader-2 2s infinite ease;
						animation: loader-2 2s infinite ease;
					}
					@-webkit-keyframes loader-2 {
						0% {
							-webkit-transform: rotate(0deg);
							transform: rotate(0deg);
						}
						100% {
							-webkit-transform: rotate(360deg);
							transform: rotate(360deg);
						}
					}
					@keyframes loader-2 {
						0% {
							-webkit-transform: rotate(0deg);
							transform: rotate(0deg);
						}
						100% {
							-webkit-transform: rotate(360deg);
							transform: rotate(360deg);
						}
					}
				';

			} elseif ( '3' == $page_loader_css ) {
				$css[] = '
					.wr-loader-3 .wr-loader {
						width: 3em;
						height: 3em;
						border-radius: 50%;
						background: -moz-linear-gradient(left, ' . esc_attr(  $page_loader_icon_color ) . ' 10%, rgba(218, 218, 218, 0) 42%);
						background: -webkit-linear-gradient(left, ' . esc_attr(  $page_loader_icon_color ) . ' 10%, rgba(218, 218, 218, 0) 42%);
						background: -o-linear-gradient(left, ' . esc_attr(  $page_loader_icon_color ) . ' 10%, rgba(218, 218, 218, 0) 42%);
						background: -ms-linear-gradient(left, ' . esc_attr(  $page_loader_icon_color ) . ' 10%, rgba(218, 218, 218, 0) 42%);
						background: linear-gradient(to right, ' . esc_attr(  $page_loader_icon_color ) . ' 10%, rgba(218, 218, 218, 0) 42%);
						-webkit-animation: loader-3 1.4s infinite linear;
						animation: loader-3 1.4s infinite linear;
					}
					.wr-loader-3 .wr-loader:before {
						width: 50%;
						height: 50%;
						border-radius: 100% 0 0 0;
						position: absolute;
						top: 0;
						left: 0;
					}
					.wr-loader-3 .wr-loader:after {
						width: 75%;
						height: 75%;
						border-radius: 50%;
						margin: auto;
						position: absolute;
						top: 0;
						left: 0;
						bottom: 0;
						right: 0;
						background: ' . esc_attr( $page_loader_bg_color ) . ';
					}
					@-webkit-keyframes loader-3 {
						0% {
							-webkit-transform: rotate(0deg);
							transform: rotate(0deg);
						}
						100% {
							-webkit-transform: rotate(360deg);
							transform: rotate(360deg);
						}
					}
					@keyframes loader-3 {
						0% {
							-webkit-transform: rotate(0deg);
							transform: rotate(0deg);
						}
						100% {
							-webkit-transform: rotate(360deg);
							transform: rotate(360deg);
						}
					}
				';

			} elseif ( '4' == $page_loader_css ) {
				$css[] = '
					.wr-loader-4 .wr-loader {
						width: 1em;
						height: 1em;
						border-radius: 50%;
						-webkit-animation: loader-4 1.3s infinite linear;
						animation: loader-4 1.3s infinite linear;
					}
					@-webkit-keyframes loader-4 {
						0%,
						100% {
							box-shadow: 0 -3em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						12.5% {
							box-shadow: 0 -3em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						25% {
							box-shadow: 0 -3em 0 -0.5em ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						37.5% {
							box-shadow: 0 -3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						50% {
							box-shadow: 0 -3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						62.5% {
							box-shadow: 0 -3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						75% {
							box-shadow: 0em -3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						87.5% {
							box-shadow: 0em -3em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
					}
					@keyframes loader-4 {
						0%,
						100% {
							box-shadow: 0 -3em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						12.5% {
							box-shadow: 0 -3em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						25% {
							box-shadow: 0 -3em 0 -0.5em ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						37.5% {
							box-shadow: 0 -3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						50% {
							box-shadow: 0 -3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						62.5% {
							box-shadow: 0 -3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						75% {
							box-shadow: 0em -3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						87.5% {
							box-shadow: 0em -3em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', 2em -2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 3em 0 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 2em 2em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', 0 3em 0 -1em ' . esc_attr(  $page_loader_icon_color ) . ', -2em 2em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', -3em 0em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ', -2em -2em 0 0.2em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
					}
				';

			} elseif ( '5' == $page_loader_css ) {
				$css[] = '
					.wr-loader-5 .wr-loader {
						width: 1em;
						height: 1em;
						border-radius: 50%;
						-webkit-animation: loader-5 1.1s infinite ease;
						animation: loader-5 1.1s infinite ease;
					}
					@-webkit-keyframes loader-5 {
						0%,
						100% {
							box-shadow: 0em -2.6em 0em 0em ' . esc_attr(  $page_loader_icon_color ) . ', 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2), 2.5em 0em 0 0em rgba(218, 218, 218, 0.2), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.2), 0em 2.5em 0 0em rgba(218, 218, 218, 0.2), -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.2), -2.6em 0em 0 0em rgba(218, 218, 218, 0.5), -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.7);
						}
						12.5% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.7), 1.8em -1.8em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', 2.5em 0em 0 0em rgba(218, 218, 218, 0.2), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.2), 0em 2.5em 0 0em rgba(218, 218, 218, 0.2), -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.2), -2.6em 0em 0 0em rgba(218, 218, 218, 0.2), -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.5);
						}
						25% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.5), 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.7), 2.5em 0em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.2), 0em 2.5em 0 0em rgba(218, 218, 218, 0.2), -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.2), -2.6em 0em 0 0em rgba(218, 218, 218, 0.2), -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2);
						}
						37.5% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.2), 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.5), 2.5em 0em 0 0em rgba(218, 218, 218, 0.7), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.2), 0em 2.5em 0 0em rgba(218, 218, 218, 0.2), -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.2), -2.6em 0em 0 0em rgba(218, 218, 218, 0.2), -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2);
						}
						50% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.2), 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2), 2.5em 0em 0 0em rgba(218, 218, 218, 0.5), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.7), 0em 2.5em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.2), -2.6em 0em 0 0em rgba(218, 218, 218, 0.2), -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2);
						}
						62.5% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.2), 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2), 2.5em 0em 0 0em rgba(218, 218, 218, 0.2), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.5), 0em 2.5em 0 0em rgba(218, 218, 218, 0.7), -1.8em 1.8em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', -2.6em 0em 0 0em rgba(218, 218, 218, 0.2), -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2);
						}
						75% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.2), 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2), 2.5em 0em 0 0em rgba(218, 218, 218, 0.2), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.2), 0em 2.5em 0 0em rgba(218, 218, 218, 0.5), -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.7), -2.6em 0em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2);
						}
						87.5% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.2), 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2), 2.5em 0em 0 0em rgba(218, 218, 218, 0.2), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.2), 0em 2.5em 0 0em rgba(218, 218, 218, 0.2), -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.5), -2.6em 0em 0 0em rgba(218, 218, 218, 0.7), -1.8em -1.8em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
					}
					@keyframes loader-5 {
						0%,
						100% {
							box-shadow: 0em -2.6em 0em 0em ' . esc_attr(  $page_loader_icon_color ) . ', 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2), 2.5em 0em 0 0em rgba(218, 218, 218, 0.2), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.2), 0em 2.5em 0 0em rgba(218, 218, 218, 0.2), -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.2), -2.6em 0em 0 0em rgba(218, 218, 218, 0.5), -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.7);
						}
						12.5% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.7), 1.8em -1.8em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', 2.5em 0em 0 0em rgba(218, 218, 218, 0.2), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.2), 0em 2.5em 0 0em rgba(218, 218, 218, 0.2), -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.2), -2.6em 0em 0 0em rgba(218, 218, 218, 0.2), -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.5);
						}
						25% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.5), 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.7), 2.5em 0em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.2), 0em 2.5em 0 0em rgba(218, 218, 218, 0.2), -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.2), -2.6em 0em 0 0em rgba(218, 218, 218, 0.2), -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2);
						}
						37.5% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.2), 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.5), 2.5em 0em 0 0em rgba(218, 218, 218, 0.7), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.2), 0em 2.5em 0 0em rgba(218, 218, 218, 0.2), -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.2), -2.6em 0em 0 0em rgba(218, 218, 218, 0.2), -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2);
						}
						50% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.2), 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2), 2.5em 0em 0 0em rgba(218, 218, 218, 0.5), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.7), 0em 2.5em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.2), -2.6em 0em 0 0em rgba(218, 218, 218, 0.2), -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2);
						}
						62.5% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.2), 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2), 2.5em 0em 0 0em rgba(218, 218, 218, 0.2), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.5), 0em 2.5em 0 0em rgba(218, 218, 218, 0.7), -1.8em 1.8em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', -2.6em 0em 0 0em rgba(218, 218, 218, 0.2), -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2);
						}
						75% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.2), 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2), 2.5em 0em 0 0em rgba(218, 218, 218, 0.2), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.2), 0em 2.5em 0 0em rgba(218, 218, 218, 0.5), -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.7), -2.6em 0em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ', -1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2);
						}
						87.5% {
							box-shadow: 0em -2.6em 0em 0em rgba(218, 218, 218, 0.2), 1.8em -1.8em 0 0em rgba(218, 218, 218, 0.2), 2.5em 0em 0 0em rgba(218, 218, 218, 0.2), 1.75em 1.75em 0 0em rgba(218, 218, 218, 0.2), 0em 2.5em 0 0em rgba(218, 218, 218, 0.2), -1.8em 1.8em 0 0em rgba(218, 218, 218, 0.5), -2.6em 0em 0 0em rgba(218, 218, 218, 0.7), -1.8em -1.8em 0 0em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
					}
				';

			} elseif ( '6' == $page_loader_css ) {
				$css[] = '
					.wr-loader-6 .wr-loader {
						font-size: 80px;
						overflow: hidden;
						width: 1em;
						height: 1em;
						border-radius: 50%;
						-webkit-animation: loader-6 1.7s infinite ease;
						animation: loader-6 1.7s infinite ease;
					}
					@-webkit-keyframes loader-6 {
						0% {
							-webkit-transform: rotate(0deg);
							transform: rotate(0deg);
							box-shadow: 0 -0.83em 0 -0.4em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.42em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.44em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.46em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.477em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						5%,
						95% {
							box-shadow: 0 -0.83em 0 -0.4em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.42em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.44em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.46em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.477em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						10%,
						59% {
							box-shadow: 0 -0.83em 0 -0.4em ' . esc_attr(  $page_loader_icon_color ) . ', -0.087em -0.825em 0 -0.42em ' . esc_attr(  $page_loader_icon_color ) . ', -0.173em -0.812em 0 -0.44em ' . esc_attr(  $page_loader_icon_color ) . ', -0.256em -0.789em 0 -0.46em ' . esc_attr(  $page_loader_icon_color ) . ', -0.297em -0.775em 0 -0.477em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						20% {
							box-shadow: 0 -0.83em 0 -0.4em ' . esc_attr(  $page_loader_icon_color ) . ', -0.338em -0.758em 0 -0.42em ' . esc_attr(  $page_loader_icon_color ) . ', -0.555em -0.617em 0 -0.44em ' . esc_attr(  $page_loader_icon_color ) . ', -0.671em -0.488em 0 -0.46em ' . esc_attr(  $page_loader_icon_color ) . ', -0.749em -0.34em 0 -0.477em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						38% {
							box-shadow: 0 -0.83em 0 -0.4em ' . esc_attr(  $page_loader_icon_color ) . ', -0.377em -0.74em 0 -0.42em ' . esc_attr(  $page_loader_icon_color ) . ', -0.645em -0.522em 0 -0.44em ' . esc_attr(  $page_loader_icon_color ) . ', -0.775em -0.297em 0 -0.46em ' . esc_attr(  $page_loader_icon_color ) . ', -0.82em -0.09em 0 -0.477em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						100% {
							-webkit-transform: rotate(360deg);
							transform: rotate(360deg);
							box-shadow: 0 -0.83em 0 -0.4em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.42em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.44em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.46em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.477em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
					}
					@keyframes loader-6 {
						0% {
							-webkit-transform: rotate(0deg);
							transform: rotate(0deg);
							box-shadow: 0 -0.83em 0 -0.4em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.42em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.44em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.46em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.477em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						5%,
						95% {
							box-shadow: 0 -0.83em 0 -0.4em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.42em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.44em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.46em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.477em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						10%,
						59% {
							box-shadow: 0 -0.83em 0 -0.4em ' . esc_attr(  $page_loader_icon_color ) . ', -0.087em -0.825em 0 -0.42em ' . esc_attr(  $page_loader_icon_color ) . ', -0.173em -0.812em 0 -0.44em ' . esc_attr(  $page_loader_icon_color ) . ', -0.256em -0.789em 0 -0.46em ' . esc_attr(  $page_loader_icon_color ) . ', -0.297em -0.775em 0 -0.477em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						20% {
							box-shadow: 0 -0.83em 0 -0.4em ' . esc_attr(  $page_loader_icon_color ) . ', -0.338em -0.758em 0 -0.42em ' . esc_attr(  $page_loader_icon_color ) . ', -0.555em -0.617em 0 -0.44em ' . esc_attr(  $page_loader_icon_color ) . ', -0.671em -0.488em 0 -0.46em ' . esc_attr(  $page_loader_icon_color ) . ', -0.749em -0.34em 0 -0.477em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						38% {
							box-shadow: 0 -0.83em 0 -0.4em ' . esc_attr(  $page_loader_icon_color ) . ', -0.377em -0.74em 0 -0.42em ' . esc_attr(  $page_loader_icon_color ) . ', -0.645em -0.522em 0 -0.44em ' . esc_attr(  $page_loader_icon_color ) . ', -0.775em -0.297em 0 -0.46em ' . esc_attr(  $page_loader_icon_color ) . ', -0.82em -0.09em 0 -0.477em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						100% {
							-webkit-transform: rotate(360deg);
							transform: rotate(360deg);
							box-shadow: 0 -0.83em 0 -0.4em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.42em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.44em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.46em ' . esc_attr(  $page_loader_icon_color ) . ', 0 -0.83em 0 -0.477em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
					}
				';

			} elseif ( '7' == $page_loader_css ) {
				$css[] = '
					.wr-loader-7 .wr-loader {
						-webkit-animation-delay: -.16s;
						animation-delay: -.16s;
					}
					.wr-loader-7 .wr-loader:before,
					.wr-loader-7 .wr-loader:after,
					.wr-loader-7 .wr-loader {
						border-radius: 50%;
						width: 1em;
						height: 1em;
						-webkit-animation-fill-mode: both;
						animation-fill-mode: both;
						-webkit-animation: loader-7 1.8s infinite ease-in-out;
						animation: loader-7 1.8s infinite ease-in-out;
					}
					.wr-loader-7 .wr-loader:before {
						left: -1.5em;
						-webkit-animation-delay: -0.32s;
						animation-delay: -0.32s;
					}
					.wr-loader-7 .wr-loader:after {
						left: 1.5em;
						-webkit-animation-delay: .32s;
						animation-delay: .32s;
					}
					.wr-loader-7 .wr-loader:before,
					.wr-loader-7 .wr-loader:after {
						top: 0;
					}
					@-webkit-keyframes loader-7 {
						0%,
						80%,
						100% {
							box-shadow: 0 1em 0 -1.3em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						40% {
							box-shadow: 0 1em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ';
						}
					}
					@keyframes loader-7 {
						0%,
						80%,
						100% {
							box-shadow: 0 1em 0 -1.3em ' . esc_attr(  $page_loader_icon_color ) . ';
						}
						40% {
							box-shadow: 0 1em 0 0 ' . esc_attr(  $page_loader_icon_color ) . ';
						}
					}
				';

			} elseif ( '8' == $page_loader_css ) {
				$css[] = '
					.wr-loader-8 .wr-loader {
						width: 40px;
						height: 40px;
						-webkit-animation: loader-8 1.2s infinite ease-in-out;
						animation: loader-8 1.2s infinite ease-in-out;
						background: ' . esc_attr( $page_loader_icon_color ) . ';
					}
					@-webkit-keyframes loader-8 {
						0% {
							-webkit-transform: perspective(120px) rotateX(0deg) rotateY(0deg);
							transform: perspective(120px) rotateX(0deg) rotateY(0deg);
						}
						50% {
							-webkit-transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg);
							transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg);
						}
						100% {
							-webkit-transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg);
							transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg);
						}
					}
					@keyframes loader-8 {
						0% {
							-webkit-transform: perspective(120px) rotateX(0deg) rotateY(0deg);
							transform: perspective(120px) rotateX(0deg) rotateY(0deg);
						}
						50% {
							-webkit-transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg);
							transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg);
						}
						100% {
							-webkit-transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg);
							transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg);
						}
					}
				';

			} elseif ( '9' == $page_loader_css ) {
				$css[] = '
					.wr-loader-9 {
						width: 32px;
						height: 32px;
						margin: 0 auto;
						-webkit-transform: translateY(-50%) rotateZ(45deg) !important;
						transform: translateY(-50%) rotateZ(45deg) !important;
					}
					.wr-loader-9 .wr-loader {
						float: left;
						width: 50%;
						height: 50%;
						-webkit-transform: scale(1.1);
						-ms-transform: scale(1.1);
						transform: scale(1.1);
					}
					.wr-loader-9 .wr-loader:before {
						top: 0;
						left: 0;
						width: 100%;
						height: 100%;
						background: ' . esc_attr( $page_loader_icon_color ) . ';
						-webkit-animation: loader-9 2.4s infinite linear both;
						animation: loader-9 2.4s infinite linear both;
						-webkit-transform-origin: 100% 100%;
						-ms-transform-origin: 100% 100%;
						transform-origin: 100% 100%;
					}
					.wr-loader-9 .wr-loader-inner-2 {
						-webkit-transform: scale(1.1) rotateZ(90deg);
						transform: scale(1.1) rotateZ(90deg);
					}
					.wr-loader-9 .wr-loader-inner-3 {
						-webkit-transform: scale(1.1) rotateZ(180deg);
						transform: scale(1.1) rotateZ(180deg);
					}
					.wr-loader-9 .wr-loader-inner-4 {
						-webkit-transform: scale(1.1) rotateZ(270deg);
						transform: scale(1.1) rotateZ(270deg);
					}
					.wr-loader-9 .wr-loader-inner-2:before {
						-webkit-animation-delay: 0.3s;
						animation-delay: 0.3s;
					}
					.wr-loader-9 .wr-loader-inner-3:before {
						-webkit-animation-delay: 0.6s;
						animation-delay: 0.6s;
					}
					.wr-loader-9 .wr-loader-inner-4:before {
						-webkit-animation-delay: 0.9s;
						animation-delay: 0.9s;
					}
					@-webkit-keyframes loader-9 {
						0%, 10% {
							-webkit-transform: perspective(140px) rotateX(-180deg);
							transform: perspective(140px) rotateX(-180deg);
							opacity: 0;
						}
						25%, 75% {
							-webkit-transform: perspective(140px) rotateX(0deg);
							transform: perspective(140px) rotateX(0deg);
							opacity: 1;
						}
						90%, 100% {
							-webkit-transform: perspective(140px) rotateY(180deg);
							transform: perspective(140px) rotateY(180deg);
							opacity: 0;
						}
					}
					@keyframes loader-9 {
						0%, 10% {
							-webkit-transform: perspective(140px) rotateX(-180deg);
							transform: perspective(140px) rotateX(-180deg);
							opacity: 0;
						}
						25%, 75% {
							-webkit-transform: perspective(140px) rotateX(0deg);
							transform: perspective(140px) rotateX(0deg);
							opacity: 1;
						}
						90%, 100% {
							-webkit-transform: perspective(140px) rotateY(180deg);
							transform: perspective(140px) rotateY(180deg);
							opacity: 0;
						}
					}
				';

			} elseif ( '10' == $page_loader_css ) {
				$css[] = '
					.wr-loader-10 {
						width: 40px;
						height: 40px;
						margin: 0 auto;
					}
					.wr-loader-10 .wr-loader {
						width: 100%;
						height: 100%;
						border-radius: 50%;
						opacity: 0.6;
						position: absolute;
						top: 0;
						left: 0;
						-webkit-animation: loader-10 2s infinite ease-in-out;
						animation: loader-10 2s infinite ease-in-out;
						background: ' . esc_attr( $page_loader_icon_color ) . ';
					}
					.wr-loader-10 .wr-loader-inner-2 {
						-webkit-animation-delay: -1.0s;
						animation-delay: -1.0s;
					}
					@-webkit-keyframes loader-10 {
						0%, 100% {
							-webkit-transform: scale(0);
							transform: scale(0);
						}
						50% {
							-webkit-transform: scale(1);
							transform: scale(1);
						}
					}
					@keyframes loader-10 {
						0%, 100% {
							-webkit-transform: scale(0);
							transform: scale(0);
						}
						50% {
							-webkit-transform: scale(1);
							transform: scale(1);
						}
					}
				';

			} elseif ( '11' == $page_loader_css ) {
				$css[] = '
					.wr-loader-11 {
						width: 40px;
						height: 40px;
						margin: 0 auto;
					}
					.wr-loader-11 .wr-loader {
						width: 33%;
						height: 33%;
						float: left;
						background: ' . esc_attr( $page_loader_icon_color ) . ';
						-webkit-animation: loader-11 1.3s infinite ease-in-out;
						animation: loader-11 1.3s infinite ease-in-out;
					}
					.wr-loader-11 .wr-loader-inner-1 {
						-webkit-animation-delay: 0.2s;
						animation-delay: 0.2s;
					}
					.wr-loader-11 .wr-loader-inner-2 {
						-webkit-animation-delay: 0.3s;
						animation-delay: 0.3s;
					}
					.wr-loader-11 .wr-loader-inner-3 {
						-webkit-animation-delay: 0.4s;
						animation-delay: 0.4s;
					}
					.wr-loader-11 .wr-loader-inner-4 {
						-webkit-animation-delay: 0.1s;
						animation-delay: 0.1s;
					}
					.wr-loader-11 .wr-loader-inner-5 {
						-webkit-animation-delay: 0.2s;
						animation-delay: 0.2s;
					}
					.wr-loader-11 .wr-loader-inner-6 {
						-webkit-animation-delay: 0.3s;
						animation-delay: 0.3s;
					}
					.wr-loader-11 .wr-loader-inner-7 {
						-webkit-animation-delay: 0.0s;
						animation-delay: 0.0s;
					}
					.wr-loader-11 .wr-loader-inner-8 {
						-webkit-animation-delay: 0.1s;
						animation-delay: 0.1s;
					}
					.wr-loader-11 .wr-loader-inner-9 {
						-webkit-animation-delay: 0.2s;
						animation-delay: 0.2s;
					}
					@-webkit-keyframes loader-11 {
						0%, 70%, 100% {
							-webkit-transform: scale3D(1, 1, 1);
							transform: scale3D(1, 1, 1);
						}
						35% {
							-webkit-transform: scale3D(0, 0, 1);
							transform: scale3D(0, 0, 1);
						}
					}
					@keyframes loader-11 {
						0%, 70%, 100% {
							-webkit-transform: scale3D(1, 1, 1);
							transform: scale3D(1, 1, 1);
						}
						35% {
							-webkit-transform: scale3D(0, 0, 1);
							transform: scale3D(0, 0, 1);
						}
					}
				';

			} elseif ( '12' == $page_loader_css ) {
				$css[] = '
					.wr-loader-12 {
						margin: 0 auto;
						width: 40px;
						height: 40px;
					}
					.wr-loader-12 .wr-loader {
						background: ' . esc_attr( $page_loader_icon_color ) . ';
						width: 10px;
						height: 10px;
						position: absolute;
						top: 0;
						left: 0;
						-webkit-animation: loader-12 1.8s ease-in-out -1.8s infinite both;
						animation: loader-12 1.8s ease-in-out -1.8s infinite both;
					}
					.wr-loader-12 .wr-loader-inner-2 {
						-webkit-animation-delay: -0.9s;
						animation-delay: -0.9s;
					}
					@-webkit-keyframes loader-12 {
						0% {
							-webkit-transform: rotate(0deg);
							transform: rotate(0deg);
						}
						25% {
							-webkit-transform: translateX(30px) rotate(-90deg) scale(0.5);
							transform: translateX(30px) rotate(-90deg) scale(0.5);
						}
						50% {
							-webkit-transform: translateX(30px) translateY(30px) rotate(-179deg);
							transform: translateX(30px) translateY(30px) rotate(-179deg);
						}
						50.1% {
							-webkit-transform: translateX(30px) translateY(30px) rotate(-180deg);
							transform: translateX(30px) translateY(30px) rotate(-180deg);
						}
						75% {
							-webkit-transform: translateX(0) translateY(30px) rotate(-270deg) scale(0.5);
							transform: translateX(0) translateY(30px) rotate(-270deg) scale(0.5);
						}
						100% {
							-webkit-transform: rotate(-360deg);
							transform: rotate(-360deg);
						}
					}
					@keyframes loader-12 {
						0% {
							-webkit-transform: rotate(0deg);
							transform: rotate(0deg);
						}
						25% {
							-webkit-transform: translateX(30px) rotate(-90deg) scale(0.5);
							transform: translateX(30px) rotate(-90deg) scale(0.5);
						}
						50% {
							-webkit-transform: translateX(30px) translateY(30px) rotate(-179deg);
							transform: translateX(30px) translateY(30px) rotate(-179deg);
						}
						50.1% {
							-webkit-transform: translateX(30px) translateY(30px) rotate(-180deg);
							transform: translateX(30px) translateY(30px) rotate(-180deg);
						}
						75% {
							-webkit-transform: translateX(0) translateY(30px) rotate(-270deg) scale(0.5);
							transform: translateX(0) translateY(30px) rotate(-270deg) scale(0.5);
						}
						100% {
							-webkit-transform: rotate(-360deg);
							transform: rotate(-360deg);
						}
					}
				';
			}
		}

		switch ( $wr_nitro_options['wr_page_layout'] ) {
			case 'right-sidebar' :
				$css[] = '
					.page-content .primary-sidebar {
						width: ' . esc_attr( $page_sidebar_width ) . 'px;
					}
					.page-content .main-content {
						width: calc(100% - ' . esc_attr( $page_sidebar_width ) . 'px);
					}
				';
				break;

			case 'left-sidebar' :
					$css[] = '
						.page-content .primary-sidebar {
							width: ' . esc_attr( $page_sidebar_width ) . 'px;
							-ms-order: 1;
							order: 1;
							-webkit-order: 1;
						}
						.page-content .main-content {
							width: calc(100% - ' . esc_attr( $page_sidebar_width ) . 'px);
							-ms-order: 2;
							order: 2;
							-webkit-order: 2;
						}
					';
				break;

			case 'no-sidebar' :
			default:
				$css[] = '
					.page-content .main-content {
						width: 100%;
					}
				';
				break;
		}

		switch ( $blog_layout ) {
			case 'right-sidebar' :
				$css[] = '
					.blog .primary-sidebar {
						width: ' . esc_attr( $blog_sidebar_width ) . 'px;
					}
					.blog .main-content {
						width: calc(100% - ' . esc_attr( $blog_sidebar_width ) . 'px);
					}
				';
				break;

			case 'left-sidebar' :
					$css[] = '
						.blog .main-content {
							width: calc(100% - ' . esc_attr( $blog_sidebar_width ) . 'px);
							-ms-order: 2;
							order: 2;
							-webkit-order: 2;
						}
						.blog .primary-sidebar {
							width: ' . esc_attr( $blog_sidebar_width ) . 'px;
							-ms-order: 1;
							order: 1;
							-webkit-order: 1;
						}
					';
				break;

			case 'no-sidebar' :
			default:
				$css[] = '
					.blog .main-content {
						width: 100%;
					}
				';
				break;
		}

		switch ( $blog_single_layout ) {
			case 'right-sidebar' :
				$css[] = '
					.single-attachment .primary-sidebar,
					.single-post .primary-sidebar {
						width: ' . esc_attr( $blog_single_sidebar_width ) . 'px;
					}
					.single-attachment .main-content,
					.single-post .main-content {
						width: calc(100% - ' . esc_attr( $blog_single_sidebar_width ) . 'px);
					}
				';
				break;

			case 'left-sidebar' :
					$css[] = '
						.single-attachment .primary-sidebar,
						.single-post .primary-sidebar {
							width: ' . esc_attr( $blog_single_sidebar_width ) . 'px;
							-ms-order: 1;
							order: 1;
							-webkit-order: 1;
						}
						.single-attachment .main-content,
						.single-post .main-content {
							width: calc(100% - ' . esc_attr( $blog_single_sidebar_width ) . 'px);
							-ms-order: 2;
							order: 2;
							-webkit-order: 2;
						}
					';
				break;

			case 'no-sidebar' :
			default:
				$css[] = '
					.single-attachment .main-content,
					.single-post .main-content {
						width: 100%;
					}
					.b-single .entry-content {
						margin: 0 auto !important;
						max-width: 750px;
					}
				';
				break;
		}

		switch ( $wc_layout ) {
			case 'right-sidebar' :
				$css[] = '
					.archive #shop-sidebar,
					.archive-sidebar {
						width: ' . esc_attr( $wc_sidebar_width ) . 'px;
					}
					.archive #shop-main,
					.archive-shop {
						width: calc(100% - ' . esc_attr( $wc_sidebar_width ) . 'px);
					}
				';
				break;

			case 'left-sidebar' :
					$css[] = '
						.archive #shop-sidebar,
						.archive-sidebar {
							-ms-order: 1;
							order: 1;
							-webkit-order: 1;
							width: ' . esc_attr( $wc_sidebar_width ) . 'px;
							' . ( $wr_nitro_options['rtl'] ? 'left' : 'right' ) . ': calc(100% - ' . esc_attr( $wc_sidebar_width ) . 'px);
						}
						.archive #shop-main,
						.archive-shop {
							-ms-order: 2;
							order: 2;
							-webkit-order: 2;
							width: calc(100% - ' . esc_attr( $wc_sidebar_width ) . 'px);
							' . ( $wr_nitro_options['rtl'] ? 'right' : 'left' ) . ': ' . esc_attr( $wc_sidebar_width ) . 'px;
						}
					';
				break;

			case 'no-sidebar' :
			default:
				$css[] = '
					.archive #shop-main,
					.archive-shop {
						width: 100%;
					}
				';
				break;
		}

		switch ( $wc_single_layout ) {
			case 'right-sidebar' :
				$css[] = '
					.style-2 #shop-sidebar {
						width: ' . absint( $wc_single_sidebar_width ) . 'px;
					}
					.style-2 #shop-detail {
						width: calc(100% - ' . absint( $wc_single_sidebar_width ) . 'px);
					}
				';
				break;

			case 'left-sidebar' :
					$css[] = '
						.style-2 #shop-sidebar {
							width: ' . absint( $wc_single_sidebar_width ) . 'px;
							-ms-order: 1;
							order: 1;
							-webkit-order: 1;
						}
						.style-2 #shop-detail {
							width: calc(100% - ' . absint( $wc_single_sidebar_width ) . 'px);
							-ms-order: 2;
							order: 2;
							-webkit-order: 2;
						}
					';
				break;

			case 'no-sidebar' :
			default:
				$css[] = '
					.style-2 #shop-detail {
						width: 100%;
					}
				';
				break;
		}

		if ( class_exists( 'Nitro_Gallery' ) ) {
			// Gallery
			$gallery_single_space = $wr_nitro_options['gallery_single_gutter'];
			$gallery_space        = $wr_nitro_options['gallery_gutter'];
			$gallery_style        = $wr_nitro_options['gallery_style'];
			$gallery_layout       = $wr_nitro_options['gallery_layout'];
			$gallery_column       = $wr_nitro_options['gallery_column'];
			$gallery_filter       = $wr_nitro_options['gallery_filter'];
			$fullwidth            = $wr_nitro_options['gallery_single_fullwidth'];

			if ( ! empty( $gallery_single_space ) ) {
				$css[] = '
					.nitro-gallery > .row {
						margin-left: -' . absint( $gallery_single_space ) / 2 . 'px;
						margin-right: -' . absint( $gallery_single_space ) / 2 . 'px;
					}
					.nitro-gallery .gallery-list .item {
						padding: ' . absint( $gallery_single_space ) / 2 . 'px;
					}
				';
				if ( $fullwidth ) {
					$css[] = '
					.single .single-full {
						padding: 0 ' . absint( $gallery_single_space ) . 'px;
					}';
				}
			}

			if ( 'grid' == $gallery_layout ) {
				$css[] = '
					.archive .galleries .grid figure:nth-child(' . esc_attr( $gallery_column ) . 'n+1) {
						clear: both;
					}
				';
			}

			if ( ! empty( $gallery_space ) ) {
				$css[] = '
					.archive .galleries > .row {
						margin: -' . absint( $gallery_space ) / 2 . 'px;
					}
					.archive .galleries figure.hentry {
						padding: ' . absint( $gallery_space ) / 2 . 'px;
					}
				';
			} else {
				$css[] = '
					.archive .galleries figure.hentry {
						padding: 0;
					}
				';
			}
		}

		// Footer
		if ( $footer_fullwidth ) {
			$css[] = '
				.footer .top-inner, .footer .info {
					max-width: 100%;
				}
				.footer .info {
					padding: 0 40px;
				}
				.footer .top {
					padding: 80px 40px 15px;
				}
			';
		}

		$custom_footer = $wr_nitro_options['footer_customize_color'];

		if ( true != $custom_footer  ) {
		$css[] = '
			' . $preview_footer_top_bg . '
			.footer {';

				// Footer background image.
				if ( ! empty( $footer_bg_image ) ) {
					$css[] = 'background-image: url( ' . esc_url( $footer_bg_image ) . ' );';

					if ( ! empty( $footer_bg_repeat ) ) {
						$css[] = 'background-repeat: ' . esc_attr( $footer_bg_repeat ) . ';';
					}
					if ( ! empty( $footer_bg_size ) ) {
						$css[] = 'background-size: ' . esc_attr( $footer_bg_size ) . ';';
					}
					if ( ! empty( $footer_bg_attachment ) ) {
						$css[] = 'background-attachment: ' . esc_attr( $footer_bg_attachment ) . ';';
					}
					if ( ! empty( $footer_bg_position ) ) {
						$css[] = 'background-position: ' . esc_attr( $footer_bg_position ) . ';';
					}
				}

				$css[] = $footer_bg_color ? 'background-color: ' . esc_attr( $footer_bg_color ) . ';' : '';

				$css[] = '
			}';

		$css[] = '
			' . $preview_footer_top_text . '
			.footer .top {
				color: ' . esc_attr( $footer_text_color ) . ';
			}
			' . $preview_footer_top_heading . '
			.footer .top h1, .footer .top h2, .footer .top h3, .footer .top h4, .footer .top h5, .footer .top h6, .footer .widget_rss .widget-title a {
				color: ' . esc_attr( $footer_heading_color ) . ';
			}
			' . $preview_footer_bot_normal . '
			.footer .bot {';
				$css[] = $footer_bot_bgcolor   ? 'background-color: ' . esc_attr( $footer_bot_bgcolor ) . ';' : '';
				$css[] = $footer_bot_textcolor ? 'color: ' . esc_attr( $footer_bot_textcolor ) . ';'    : '';
				$css[] = '
			}';

			$css[] = $footer_top_link       ? $preview_footer_top_link_normal . ' .footer .top a { color: ' . esc_attr( $footer_top_link ) . '; }'             : '';
			$css[] = $footer_top_hover_link ? $preview_footer_top_link_hover . ' .footer .top a:hover { color: ' . esc_attr( $footer_top_hover_link ) . '; }' : '';

			$css[] = $footer_bot_link       ? $preview_footer_bot_link_normal . ' .footer .bot a { color: ' . esc_attr( $footer_bot_link ) . '; }'             : '';
			$css[] = $footer_bot_hover_link ? $preview_footer_bot_link_hover . ' .footer .bot a:hover { color: ' . esc_attr( $footer_bot_hover_link ) . '; }' : '';

		} else {
			$css[] = '
			' . $preview_inner_bg . '
			.footer {';
				$css[] = $content_bg_color ? 'background-color: ' . esc_attr( $content_bg_color ) . ';' : '';

				$css[] = '
			}
			' . $preview_secondary_bg_normal . '
			.footer .bot {';
				$css[] = $overlay_bg  ? 'background-color: ' . esc_attr( $overlay_bg ) . ';' : '';
				$css[] = '
			}';

		}

		// Page 404
		if ( ! empty( $error_bg_color ) || ! empty( $error_bg_image ) ) {
			$css[] = '
				.error404 .wrapper {';
					if ( ! empty( $error_bg_color ) ) {
						$css[] = 'background-color:' . esc_attr( $error_bg_color ) . ';';
					}
					if ( ! empty( $error_bg_image ) ) {
						$css[] = 'background-image: url( ' . esc_attr( $error_bg_image ) . ' );';
					}
					if ( ! empty( $error_bg_size ) ) {
						$css[] = 'background-size:' . esc_attr( $error_bg_size ) . ';';
					}
					if ( ! empty( $error_bg_position ) ) {
						$css[] = 'background-position:' . esc_attr( $error_bg_position ) . ';';
					}
					if ( ! empty( $error_bg_attachment ) ) {
						$css[] = 'background-attachment:' . esc_attr( $error_bg_attachment ) . ';';
					}
					if ( ! empty( $error_bg_repeat ) ) {
						$css[] = 'background-repeat:' . esc_attr( $error_bg_repeat ) . ';';
					}
					$css[] = '
				}
			';
		}

		$css[] = '
			.error404 .heading-404 * {
				font-size: ' . esc_attr( $error_font_size ) . 'px;
				line-height: ' . esc_attr( $error_font_size ) . 'px;
				color: ' . esc_attr( $error_font_color ) . ';
				font-weight: bold;
			}';

		if ( $construction_mode == '1' ) {
			$css[] = '
				.maintenance {';
					if ( ! empty( $construction_bg_color) ) {
						$css[] = 'background-color:' . esc_attr( $construction_bg_color ) . ';';
					}
					if ( ! empty( $construction_bg_image ) ) {
						$css[] = 'background-image: url( ' . esc_url( $construction_bg_image ) . ' );';
						if ( ! empty( $construction_bg_repeat ) ) {
							$css[] = 'background-repeat: ' . esc_attr( $construction_bg_repeat ) . ';';
						}
						if ( ! empty( $construction_bg_size ) ) {
							$css[] = 'background-size: ' . esc_attr( $construction_bg_size ) . ';';
						}
						if ( ! empty( $construction_bg_attachment ) ) {
							$css[] = 'background-attachment: ' . esc_attr( $construction_bg_attachment ) . ';';
						}
						if ( ! empty( $construction_bg_position ) ) {
							$css[] = 'background-position: ' . esc_attr( $construction_bg_position ) . ';';
						}
					}
					$css[] = '
				}
			';
		}

		// Get widget style
		$w_style         = $wr_nitro_options['w_style'];
		$w_style_bg      = $wr_nitro_options['w_style_bg'];
		$w_style_border  = $wr_nitro_options['w_style_border'];
		$w_style_divider = $wr_nitro_options['w_style_divider'];

		if ( '1' == $w_style || '2' == $w_style ) {
			$css[] = ( ! empty( $w_style_bg ) ? $preview_secondary_bg_normal : NULL ) . '
				.widget-style-' . esc_attr( $w_style ) . ' .widget {';
					if ( $w_style_bg || $w_style_border ) {
						$css[] = 'padding: 20px;';
					}

					if ( ! empty( $w_style_bg ) ) {
						$css[] = 'background-color:' . esc_attr( $overlay_bg ) . '; ';
					}

					if ( ! empty( $w_style_border ) ) {
						$css[] = 'border: 1px solid ' . esc_attr( $line_color ) . ';';
						$css[] = 'padding: 20px;';
					}
					$css[] = '
				}
			';

			if ( ! empty( $w_style_divider ) ) {
				$css[] = '
					.woof_container_inner > h4,
					.widget-style-' . esc_attr( $w_style ) . ' .widget .widget-title {
						border-bottom: 1px solid ' . esc_attr( $line_color ) . ';
					}
					.widget-style-1 .widget .widget-title {
						padding-bottom: 10px;
					}
				';
			}

		}

		if ( '3' == $w_style && ! empty( $w_style_border ) ) {
			$css[] = '
				.widget-style-3 .widget {
					padding-left: 10px;
					border-left: 2px solid ' . esc_attr( $line_color ) . ';
				}
			';
		}

		if ( '4' == $w_style && ! empty( $w_style_bg ) ) {
			$css[] = '
				.widget-style-4 .widget {
					padding: 20px 10px;
					background: ' . esc_attr( $overlay_bg ) . ';
				}
				.woof_container_inner > h4,
				.widget-style-4 .widget .widget-title {
					margin: -20px -10px 20px;
				}
			';
		}

		if ( '4' == $w_style ) {
			$css[] = '
				.widget-style-4 .widget .widget-title {
					padding: 10px 15px;
					color: ' . esc_attr( $content_bg_color ) . ';
					background: ' . esc_attr( $heading_color ) . ';
				}
				.widget-style-4 .widget .widget-title a {
					color: ' . esc_attr( $content_bg_color ) . ';
				}
			';
		}

		if ( ! empty( $meta_text ) ) {
			$css[] = '
				.meta-color,
				.entry-meta,
				.entry-meta a,
				.entry-meta span a,
				.entry-meta i,
				.sc-product-package .p-package-cat a,
				.widget li .info,
				blockquote,
				.b-single .single-nav > div > span,
				time, .irs-grid-text,
				.irs-min, .irs-max {
					color: ' . esc_attr( $meta_text ) . ';
				}
				::-webkit-input-placeholder {
					color: ' . esc_attr( $meta_text ) . ';
				}
				:-moz-placeholder {
					color: ' . esc_attr( $meta_text ) . ';
				}
				::-moz-placeholder {
					color: ' . esc_attr( $meta_text ) . ';
				}
				:-ms-input-placeholder {
					color: ' . esc_attr( $meta_text ) . ';
				}
				.irs-grid-pol, .irs-from, .irs-to, .irs-single {
					background: ' . esc_attr( $meta_text ) . ';
				}
			';
		}

		if ( ! empty( $line_color ) ) {
			$css[] = '
				' . $preview_line_color_normal . '
				.nitro-line,
				.nitro-line > *,
				.nitro-line .yith-wcwl-add-to-wishlist a,
				.btb,
				select:not(.cate-search),
				ul li,
				input:not([type="submit"]):not([type="button"]):not(.submit):not(.button):not(.extenal-bdcl),
				.p-single-action .yith-wcwl-add-to-wishlist div a,
				textarea,
				table, th, td,
				.woocommerce-cart th,
				.woocommerce-cart td,
				blockquote,
				.quantity .btn-qty a:first-child,
				.widget ul li,
				.b-masonry .entry-meta,
				.comments-area .comment-form p,
				.woocommerce-cart .cart-collaterals section,
				.style-1 .woocommerce-tabs .tabs,
				.style-2 .clean-tab *:not(.submit),
				.style-3 .accordion-tabs > div,
				.style-3 .accordion-tabs,
				.style-4 .woocommerce-tabs .tabs li:not(:last-child),
				.default-tab .woocommerce-tabs .tabs,
				.woocommerce-page input[type="checkbox"] + label:before,
				#checkout_timeline,
				.timeline-horizontal,
				.page-numbers li span:not(.dots),
				.page-numbers li a:hover,
				.style-3 ul.page-numbers,
				.sc-product-package ul li,
				.woocommerce-cart .woocommerce > form,
				.woocommerce-page .form-container,
				.woocommerce-checkout .form-row input.input-text,
				.woocommerce-checkout .select2-container,
				.woocommerce-page .select2-container .select2-choice,
				.woocommerce-page .select2-drop-active,
				.grid.boxed.pag-number .product,
				.default-tab .woocommerce-tabs .wc-tabs > li,
				.vc_tta-tabs .vc_tta-tabs-container .vc_tta-tab,
				.wr-pricing-table.style-1 .pricing-item .inner,
				.wr-pricing-table.style-1 .pricing-item .pricing-header,
				.wr-pricing-table.style-3 .pricing-item .inner,
				.wr-pricing-table.style-4 .pricing-item .inner,
				.quickview-modal .info div[itemprop="description"],
				.quickview-modal .info .quickview-button, .quickview-modal .info .p-meta,
				.sc-product-package .product__btn_cart,
				.woocommerce .wishlist_table td.product-add-to-cart a.product__btn_cart,
				.pagination.wc-pagination .page-ajax a,
				.style-3 .accordion-tabs .panel,
				.style-2 .woocommerce-tabs div .panel,
				.woocommerce-cart .cart-collaterals .coupon,
				.vc_toggle, .vc_toggle.vc_toggle_active .vc_toggle_title,
				.wr-pricing-table.style-2 .pricing-item .pricing-footer,
				.wr-custom-attribute li:not(.selected) a,
				.wr-custom-attribute.color-picker li a:after,
				.wr-pricing-table.style-2 .pricing-item .pricing-footer,
				.chosen-container-multi .chosen-choices,
				.chosen-container .chosen-drop,
				.woof_redraw_zone .irs-slider,
				.woof_list_label .woof_label_term,
				.woof_label_count,
				.woof_sid_auto_shortcode, .woof_show_auto_form, .woof_hide_auto_form,
				.booking-pricing-info,
				.grid.boxed.pag-number:not(.sc-product):not(.sc-products) .product,
				.sc-products.grid-boxed-layout .product,
				.group-quantity .product__btn_cart {
					border-color: ' . esc_attr( $line_color ) . ' !important;
				}
				.sc-cat-list ul li ul {
					border-color: ' . esc_attr( $line_color ) . ';
				}
				.sc-testimonials.style-2 .arrow,
				.sc-cat-list ul li a {
					border-bottom-color: ' . esc_attr( $line_color ) . ' !important;
				}
				' . $preview_line_color_bg . '
				.woocommerce-checkout #checkout_timeline li:not(:last-child):after,
				.vc_tta-tabs.vc_tta-style-style-7 .vc_tta-tabs-container:before,
				.vc_tta-tabs.vc_tta-style-style-4 .vc_tta-tabs-container .vc_tta-tab:before,
				.vc_tta-tabs.vc_tta-style-style-6 .vc_tta-tabs-container .vc_tta-tab:before,
				.vc_tta-tabs.vc_tta-style-style-2 .vc_tta-tabs-container:before,
				.wr-pricing-table .style-1 .pricing-item .units:before,
				.wr-pricing-table .style-3 .pricing-item .units:before,
				.widget_price_filter .ui-slider {
					background-color: ' . esc_attr( $line_color ) . ';
				}
			';
		}

		if ( $fields_bg ) {
			$css[] = '
				' . $preview_fields_bg . '
				select,
				textarea,
				.chosen-container-multi .chosen-choices,
				input:not([type="submit"]):not([type="button"]):not(.submit):not(.button),
				.woocommerce-checkout .select2-container,
				.select2-container .select2-choice,
				.select2-results .select2-highlighted,
				.select2-search {
					background-color: ' . esc_attr( $fields_bg ) . ';
				}
			';
		}

		if ( ! empty( $overlay_bg ) ) {
			$css[] = '
				' . $preview_secondary_bg_normal . '
				.overlay_bg,
				.wr-mobile.woocommerce-page.archive .wrapper,
				.default-tab .woocommerce-tabs .wc-tabs > li a:hover,
				.default-tab .woocommerce-tabs .wc-tabs > li.active a,
				.widget ul li .count,
				.style-1 .woocommerce-tabs,
				.b-classic.boxed .post,
				.style-2 .woocommerce-tabs #comments .comment-text,
				.style-3 .woocommerce-tabs #comments .comment-text,
				.style-4 .woocommerce-tabs #comments .comment-text,
				.vc_progress_bar.style-1 .vc_single_bar,
				.vc_progress_bar.style-1 .vc_single_bar .vc_bar:before,
				.vc_progress_bar .vc_single_bar,
				.wr-pricing-table.style-2 .pricing-item .inner,
				.wr-pricing-table.style-3 .pricing-item .price-value,
				.wr-pricing-table.style-4 .pricing-item .inner:hover,
				[class*="b-"].default .entry-cat a,
				.widget .tagcloud a,
				.galleries .grid .item-inner,
				.single-gallery .wr-nitro-carousel .owl-dots > div span,
				.pagination.wc-pagination .page-ajax a,
				.entry-thumb i.body_bg:hover,
				.irs-min, .irs-max,
				.search-results .search-item,
				.woof_list_label .woof_label_term {
					background-color: ' . esc_attr( $overlay_bg ) . ';
				}
				' . $preview_secondary_bg_border . '
				.vc_progress_bar.style-1 .vc_single_bar {
					border-color: ' . esc_attr( $overlay_bg ) . ';
				}
				' . $preview_secondary_bg_border_right . '
				.style-2 .woocommerce-tabs #comments .comment_container:before,
				.style-3 .woocommerce-tabs #comments .comment_container:before,
				.style-4 .woocommerce-tabs #comments .comment_container:before,
				.comments-area .comment-list .comment-content-wrap:before,
				.b-classic.small.boxed .entry-thumb:before {
					border-right-color: ' . esc_attr( $overlay_bg ) . ';
				}
				.rtl .b-classic.small.boxed .entry-thumb:before {
					border-left-color: ' . esc_attr( $overlay_bg ) . ';
				}
				.sc-product-package ul li:before {
					color: ' . esc_attr( $overlay_bg ) . ';
				}
			';
		}

		if ( ! empty( $content_bg_color ) ) {
			$css[] = '
				' . $preview_inner_bg . '
				body .wrapper,
				.body_bg,
				.single-mobile-layout .p-single-info.fixed .p-single-action,
				.single-mobile-layout.product-type-variable .p-single-info.fixed .single_variation_wrap,
				.single-mobile-layout .wishlist-btn a,
				[class*="b-"].boxed .entry-cat a,
				.b-zigzag.default .entry-cat a,
				.wr-pricing-table.style-1 .pricing-item .inner,
				.select2-results,
				.product-btn-right .product__btn,
				.product-btn-center:not(.btn-inside-thumbnail) .product__action a,
				.nivo-lightbox-theme-default.nivo-lightbox-overlay,
				.style-1 .woocommerce-tabs #comments .comment-text,
				.woof_redraw_zone .irs-slider,
				.woof_redraw_zone .irs-bar,
				.style-5 .woocommerce-tabs #comments .comment-text,
				.style-2 .woocommerce-tabs.accordion-tabs #comments .comment-text {
					background-color: ' . esc_attr( $content_bg_color ) . ';
				}
				.style-2 .woocommerce-tabs.accordion-tabs #comments .comment_container:before,
				.style-5 .woocommerce-tabs #comments .comment_container:before,
				.style-1 .woocommerce-tabs #comments .comment_container:before {
					border-right-color: ' . esc_attr( $content_bg_color ) . ';
				}
				.sc-testimonials.style-2 .arrow span {
					border-bottom-color: ' . esc_attr( $content_bg_color ) . ';
				}
				.body_bg_text,
				.irs-from, .irs-to, .irs-single {
					color: ' . esc_attr( $content_bg_color ) . ';
				}
			';
		}

		if ( ! empty( $body_text_color ) ) {
			$css[] = '
				' . $preview_body_text_normal . '
				body,
				.body_color,
				.entry-cat a,
				.p-meta a,
				.port-content .hentry .title .cat,
				.port-content .hentry .title .cat a,
				.nitro-member.style-1 .social a,
				.nitro-member.style-3 .social a,
				.select2-results li,
				.woocommerce-cart .shop_table .product-price .amount,
				.woocommerce-cart .cart_totals .cart-subtotal .amount,
				.color-dark,
				.icon_color,
				.icon_color * i,
				.single-mobile-layout .addition-product .add_to_cart_button i,
				.price del,
				.price del .amount,
				.star-rating:before,
				.wc-switch a.active,
				.select2-container .select2-choice,
				.single-gallery .wr-nitro-carousel .owl-dots > div.active span,
				.pagination.wc-pagination .page-ajax a,
				.nivo-lightbox-theme-default .nivo-lightbox-nav:before,
				.vc_toggle .vc_toggle_title .vc_toggle_icon:before,
				.vc_progress_bar.style-1 .vc_single_bar .vc_label > span,
				.wr-product-share h3,
				.woof_show_auto_form,
				.woocommerce-checkout .shop_table .cart-subtotal .amount,
				.woocommerce-checkout .shop_table .cart_item .amount,
				.style-5:not(.downloadable) .p-single-action .cart .wishlist-btn .tooltip {
					color: ' . esc_attr( $body_text_color ) . ';
				}
			';
			$css[] = '
				.single-gallery .wr-nitro-carousel .owl-dots > div.active span,
				.single-gallery .wr-nitro-carousel .owl-dots > div:hover span,
				.woof_redraw_zone .irs-slider:after,
				.woof_redraw_zone .irs-line-left,
				.woof_redraw_zone .irs-line-right,
				.woof_redraw_zone .irs-line-mid {
					background-color: ' . esc_attr( $body_text_color ) . ';
				}
			';
			$css[] = '
				.wr-nitro-carousel .owl-dots .owl-dot span {
					border-color: ' . esc_attr( $body_text_color ) . ';
				}
				.select2-container .select2-choice .select2-arrow b:after {
					border-top-color: ' . esc_attr( $body_text_color ) . ';
				}
			';
		}

		if ( ! empty( $heading_color ) ) {
			$css[] = '
				' . $preview_heading_normal . '
				h1,h2,h3,h4,h5,h6,
				.heading-color,
				.heading-color > a,
				.entry-title,
				.entry-title a,
				.title a,
				[class*="title"] > a,
				.product__title a,
				.vc_tta-container .vc_tta.vc_general .vc_tta-tab > a,
				.wr-pricing-table .pricing-item .price-value,
				.woocommerce-checkout .shop_table th.product-name,
				.woocommerce-checkout .payment_methods li label,
				a:hover,
				.widget ul li a,
				.entry-meta a:hover,
				.hover-primary:hover,
				.vc_toggle .vc_toggle_title .vc_toggle_icon:hover:before,
				.vc_progress_bar.style-1 .vc_single_bar .vc_label {
					color: ' . esc_attr( $heading_color ) . ';
				}
				' . $preview_heading_bg . '
				.heading-bg,
				.widget_price_filter .ui-slider > *,
				.wr-pricing-table.style-3 .pricing-item .units:before  {
					background-color: ' . esc_attr( $heading_color ) . ';
				}
				.widget_price_filter .ui-slider-handle {
					border-color: ' . esc_attr( $heading_color ) . ';
				}
			';
		}

		$buddypress_css = '#buddypress .comment-reply-link, #buddypress div.activity-meta a, #buddypress .generic-button a, #buddypress a.button, #buddypress button, #buddypress input[type=button], #buddypress input[type=reset], #buddypress input[type=submit], #buddypress ul.button-nav li a, a.bp-title-button,';
		$buddypress_css_hover = '#buddypress .comment-reply-link:hover, #buddypress div.activity-meta a:hover, #buddypress a.button:focus, #buddypress a.button:hover, #buddypress button:hover, #buddypress div.generic-button a:hover, #buddypress input[type=button]:hover, #buddypress input[type=reset]:hover, #buddypress input[type=submit]:hover, #buddypress ul.button-nav li a:hover, #buddypress ul.button-nav li.current a,';

		$css[] = $buddypress_activated ? $buddypress_css . $buddypress_css_hover : '';
		$css[] = '
			.wr-btn,
			.button,
			button[class*="button"],
			.submit,
			input[type="submit"],
			a.button_sg,
			.yith-woocompare-widget a.compare {
				display: inline-block;
				text-align: center;
				white-space: nowrap;
				font-size: ' . esc_attr( $btn_font_size ) . 'px;
				height: ' . esc_attr( $btn_line_height) . 'px;
				line-height: ' . ( esc_attr( $btn_line_height) - esc_attr( $btn_border_width ) * 2 ) . 'px;
				letter-spacing: ' . esc_attr( $btn_letter_spacing ) . 'px;
				padding: 0 ' . esc_attr( $btn_padding ) . 'px;
				border: ' . esc_attr( $btn_border_width ) . 'px solid;
				border-radius: ' . esc_attr( $btn_border_radius ) . 'px;';

				if ( $btn_font['italic'] ) {
					$css[] = 'font-style: italic;';
				}

				if ( $btn_font['underline'] ) {
					$css[] = 'text-decoration: underline;';
				}

				if ( $btn_font['uppercase'] ) {
					$css[] = 'text-transform: uppercase;';
				}
				$css[] = '
			}
		';

		$css[] = '
			.p-single-action .actions-button a {
				line-height: ' . ( esc_attr( $btn_line_height ) - 2 ) . 'px;
				height: ' . esc_attr( $btn_line_height ) . 'px;
				width: ' . esc_attr( $btn_line_height ) . 'px;
			}
			.plus-minus-button input {
			    height: ' . esc_attr( $btn_line_height ) . 'px;
			}
			.qty-suffix {
			    line-height: ' . ( esc_attr( $btn_line_height ) - 2 ) . 'px;
		        margin: 10px 10px 0 0 !important;
			}
			.quantity input[type="number"],
			.auction-ajax-change .quantity input {
				line-height: ' . ( esc_attr( $btn_line_height ) - esc_attr( $btn_border_width ) * 2 ) . 'px;
				height: ' . ( esc_attr( $btn_line_height )  - 2 ) . 'px;
				width: ' . ( esc_attr( $btn_line_height )  - 2 ) . 'px;
			}
			.quantity {
				width: ' . ( esc_attr( $btn_line_height ) + 32 ) . 'px;
			}
			.style-5:not(.downloadable) .p-single-action .cart .wishlist-btn {
				width: calc(100% - ' . ( esc_attr( $btn_line_height ) + 42 ) . 'px);
				width: -webkit-calc(100% - ' . ( esc_attr( $btn_line_height ) + 42 ) . 'px);
				margin-right: 0;
			}
			.quantity .btn-qty a {
				height: ' . esc_attr( $btn_line_height ) / 2 . 'px;
				line-height: ' . esc_attr( $btn_line_height ) / 2 . 'px;
			}
			.woocommerce-wishlist button[class*="button"],
			.woocommerce-wishlist .button {
				height: ' . esc_attr( $btn_line_height) . 'px !important;
				line-height: ' . ( esc_attr( $btn_line_height) - esc_attr( $btn_border_width ) * 2 ) . 'px !important;
				padding: 0 ' . esc_attr( $btn_padding ) . 'px !important;
				border: ' . esc_attr( $btn_border_width ) . 'px solid !important;
				border-radius: ' . esc_attr( $btn_border_radius ) . 'px !important;
			}
		';

		$css[] = $buddypress_activated ? $buddypress_css : '';
		$css[] = $preview_btn_primary_normal;
		$css[] = '.wr-btn-solid, .button, button[class*="button"], .submit, input[type="submit"]:not(.wr-btn-outline), a.button_sg, .yith-woocompare-widget a.compare {';
			$css[] = $btn_primary_bg['normal'] ? 'background-color: ' . esc_attr( $btn_primary_bg['normal'] ) . ';' : '';
			$css[] = $btn_primary_border['normal'] ? 'border-color: ' . esc_attr( $btn_primary_border['normal'] ) . ';' : '';
			$css[] = $btn_primary['normal'] ? 'color: ' . esc_attr( $btn_primary['normal'] ) . ';' : '';
		$css[] = '}';

		$css[] = $buddypress_activated ? $buddypress_css_hover : '';
		$css[] = $preview_btn_primary_hover;
		$css[] = '.wr-btn-solid:hover, .button:hover, button[class*="button"]:hover, .submit:hover, input[type="submit"]:not(.wr-btn-outline):not([disabled]):hover, .yith-woocompare-widget a.compare:hover {';
			$css[] = $btn_primary_bg['hover'] ? 'background-color: ' . esc_attr( $btn_primary_bg['hover'] ) . ';' : '';
			$css[] = $btn_primary_border['hover'] ? 'border-color: ' . esc_attr( $btn_primary_border['hover'] ) . ';' : '';
			$css[] = $btn_primary['hover'] ? 'color: ' . esc_attr( $btn_primary['hover'] ) . ';' : '';
		$css[] = '}';

		$css[] = $preview_btn_secondary_normal;
		$css[] = ' .wr-btn.wr-btn-outline, .woocommerce-checkout .timeline-horizontal input.button.alt.prev, .sc-product-package .product__btn_cart, .woocommerce .wishlist_table td.product-add-to-cart a.product__btn_cart {';
			$css[] = $btn_secondary_bg['normal'] ? ' background-color: ' . esc_attr( $btn_secondary_bg['normal'] ) . '; ' : '';
			$css[] = $btn_secondary_border['normal'] ? 'border-color: ' . esc_attr( $btn_secondary_border['normal'] ) . '; ' : '';
			$css[] = $btn_secondary['normal'] ? ' color: ' . esc_attr( $btn_secondary['normal'] ) . '; ' : '';
		$css[] = '}';

		$css[] = $preview_btn_secondary_hover;
		$css[] = '.wr-btn.wr-btn-outline:hover, .woocommerce-checkout .timeline-horizontal input.button.alt.prev:hover, .sc-product-package .product__btn_cart:hover, .woocommerce .wishlist_table td.product-add-to-cart a.product__btn_cart:hover {';
			$css[] = $btn_secondary_bg['hover'] ? 'background-color: ' . esc_attr( $btn_secondary_bg['hover'] ) . ';' : '';
			$css[] = $btn_secondary_border['hover'] ? 'border-color: ' . esc_attr( $btn_secondary_border['hover'] ) . ';' : '';
			$css[] = $btn_secondary['hover'] ? 'color: ' . esc_attr( $btn_secondary['hover'] ) . ';' : '';
		$css[] = '}';

		if ( ! empty( $btn_border_radius ) ) {
			$css[] = '
				.sc-product-package .product__btn_cart,
				.woocommerce .wishlist_table td.product-add-to-cart a.product__btn_cart,
				.list .product__action > div a,
				.p-single-action .product__compare > a,
				.p-single-action .yith-wcwl-add-to-wishlist a {
					border-radius: ' . esc_attr( $btn_border_radius ) . 'px;
				}
			';
		}

		// Back to top button
		$back_top            = $wr_nitro_options['back_top'];
		$back_top_size       = $wr_nitro_options['back_top_size'];
		$back_top_icon_size  = $wr_nitro_options['back_top_icon_size'];
		$back_top_style      = $wr_nitro_options['back_top_style'];
		$back_top_type       = $wr_nitro_options['back_top_type'];

		if ( $back_top  ) {
			$css[] = '
				#wr-back-top > a {
					width: ' . esc_attr( $back_top_size ) . 'px;
					height: ' . esc_attr( $back_top_size ) . 'px;
					line-height: ' . ( esc_attr( $back_top_size ) - 5 ) . 'px;';
					if ( ! empty( $back_top_icon_size ) ) {
						$css[] = '
							font-size: ' . esc_attr( $back_top_icon_size ) . 'px;
						';
					}
					if ( 'circle' == $back_top_type ) {
						$css[] = 'border-radius: 100%;';
					} elseif ( 'rounded' == $back_top_type ) {
						$css[] = 'border-radius: 5px;';
					}
					if ( 'dark' == $back_top_style ) {
						$css[] = 'color: #fff;';
					}

			$css[] = '}
				#wr-back-top > a:hover {
					color: #fff !important;
				}
			';
			if ( wp_is_mobile() && $wr_nitro_options['wc_detail_mobile_sticky_cart'] ) {
				$css[] = '
					.single-product #wr-back-top {
						bottom: 75px;
						right: 10px;
					}
				';
			}
		}

		// Custom CSS to restyle Buddypress plugin
		if ( $buddypress_activated ) {
			$css[] = '
				#buddypress .standard-form div.submit {
				    padding: 0;
				    border: none;
				    background: none;
				    width: 100%;
				    text-align: left;
				    margin-top: 10px;
				}
				#buddypress .standard-form input:not([type="submit"]):not([type="checkbox"]) {
					display: block;
					min-width: 300px;
				}
				ul.acfb-holder li.friend-tab > span > img {
					margin-right: 10px;
				}
			';
		}

		if ( $wr_nitro_options['wc_detail_mobile_sticky_cart'] && is_singular( 'product' ) && wp_is_mobile() ) {
			$css[] = '
				body.single-product {
					margin-bottom: 65px;
				}
			';
		}

		$show_woocommerce_message = false;
		if ( ( function_exists( 'is_shop' ) && is_shop() || is_tax( 'product_cat' ) ) && ( get_option( 'woocommerce_enable_ajax_add_to_cart' ) == 'no' || get_option( 'woocommerce_enable_ajax_add_to_cart_single' ) == 'no' ) ) {
			$show_woocommerce_message = true;
		}

		// Show woocommerce message if product has gravity form
		if ( is_singular( 'product' ) ) {
			if ( WR_Nitro_Helper::check_gravityforms( get_the_ID() ) ) {
				$css[] = '#shop-main .woocommerce-message { display: block; margin-left: 20px; margin-right: 20px; }';
			}

			if (
				WR_Nitro_Helper::check_gravityforms( get_the_ID() )
				||
				( get_option('woocommerce_enable_ajax_add_to_cart_single') == 'no' && ! (int) $wr_nitro_options['wc_buynow_btn'] )
				||
				isset( $_REQUEST['add_to_cart_normally'] )
			) {
				$show_woocommerce_message = true;
			}
		}

		if ( $show_woocommerce_message ) {
			$css[] = '#shop-main .woocommerce-message { display: block; margin-left: 20px; margin-right: 20px; }';
		}

		if ( ! empty( $wr_nitro_options['custom_css'] ) ) {
			$css[] = $wr_nitro_options['custom_css'];
		}

		$css[] = self::color_schemes();

		$css = preg_replace( '/\n|\t/i', '', apply_filters( 'wr_custom_styles', implode ( $css ) ) );

		// Embed inline custom styles.
		wp_add_inline_style( 'wr-nitro-main', $css );
	}

	/**
	 * Returns CSS for the color schemes.
	 *
	 * @return string Color scheme CSS.
	 */
	public static function color_schemes() {
		// Get options
		$wr_nitro_options = WR_Nitro::get_options();
		$color = $wr_nitro_options['custom_color'];
		if ( empty( $color ) ) {
			$color = '#ff4064';
		}

		$preview_main_bg = $preview_main_text = $preview_main_border  = '';
		if ( is_customize_preview() ) {
			$preview_main_bg          = '.preview_main_bg,';
			$preview_main_text        = '.preview_main_text,';
			$preview_main_border      = '.preview_main_border,';
		}

		return <<<CSS
	{$preview_main_text}
	a,
	.hover-main:hover,
	.mfp-close:hover,
	.format-audio .mejs-controls .mejs-time-rail .mejs-time-current,
	.post-title.style-2 a:hover,
	.b-single .post-tags a:hover,
	.port-cat a.selected,
	.port-content .hentry .action a:hover,
	.port-single .hentry .thumb .mask a:hover,
	.color-primary,
	.wc-switch a:hover,
	#p-preview .owl-buttons *:hover,
	.product__price .amount,
	.p-single-images .p-gallery .owl-buttons > *:hover,
	.woocommerce-cart .shop_table .cart_item:hover .remove:hover,
	.woocommerce-cart .shop_table .product-name a:hover,
	.woocommerce-cart .quantity .btn-qty a:hover,
	.woocommerce-cart .shop_table tbody .product-subtotal,
	.amount,
	[class*="title"]:hover > a,
	.widget .product-title:hover > a,
	.widget ul li a:hover,
	.widget-search button:hover,
	[class*="product"] ins,
	.woocommerce-account .user-link a:hover,
	.woocommerce-checkout #checkout_timeline.text li.active,
	.sc-social-network .info.outside a:hover,
	.vc_tta-container .vc_tta-tabs .vc_tta-tabs-container .vc_tta-tab.vc_active > a,
	.vc_tta-container .vc_tta-tabs .vc_tta-tabs-container .vc_tta-tab:hover > a,
	.page-numbers li span:not(.dots), .page-numbers li a:hover,
	.hb-minicart .mini_cart_item .info-item .title-item a:hover,
	.widget_shopping_cart_content .total .amount,
	.hb-minicart .action-top-bottom .quickview-outer .edit-cart:hover:before,
	.hb-minicart .action-top-bottom .remove-outer .remove:hover:before,
	.hb-cart-outer .dark-style .widget_shopping_cart_content .buttons .wc-forward:hover,
	.entry-cat a:hover,
	.style-2 .clean-tab .tabs li.active a,
	.style-2 .clean-tab .tabs li a:hover,
	.nitro-member .social a:hover,
	.maintenance.maintenance-style-2 .wr-countdown > div > div,
	.icon_color:hover,
	.icon_color > *:hover i,
	.gallery-fullscreen .wr-nitro-carousel .owl-nav > div:hover,
	.woocommerce .wishlist_table .remove-product:hover,
	.product__title a:hover,
	.star-rating span:before,
	.product__action-bottom > .product__btn:hover,
	.woocommerce-tabs .active a.tab-heading,
	.vc_toggle .vc_toggle_title:hover > *,
	.filters a.selected,
	.woof_label_count,
	.widget_nav_menu .current-menu-item a,
	.yith-wcwl-wishlistexistsbrowse.show i, .yith-wcwl-wishlistaddedbrowse.show i {
		color: {$color};
	}
	{$preview_main_border}
	.loader,
	.style-2 .clean-tab .woocommerce-tabs .tabs li.active a,
	.style-3 .page-numbers li span:not(.dots),
	.style-3 .page-numbers li a:hover,
	.wr-nitro-carousel .owl-dots .owl-dot:hover span,
	.wr-nitro-carousel .owl-dots .owl-dot.active span,
	.p-single-images .flex-control-paging li a:hover,
	.p-single-images .flex-control-paging li .flex-active,
	.woof_list_label li .woof_label_term:hover,
	.woof_list_label li .woof_label_term.checked,
	#wr-back-top > a:hover {
		border-color: {$color} !important;
	}
	.sc-cat-list ul li a:hover,
	.wr-onepage-nav a span:before,
	.vc_toggle.vc_toggle_active,
	.sc-cat-list ul li ul {
		border-left-color: {$color};
	}
	.rtl .sc-cat-list ul li a:hover {
		border-right-color: {$color};
	}
	{$preview_main_bg}
	.bg-primary,
	.hover-bg-primary:hover,
	.product_list_widget .remove:hover,
	.sc-product-package .p-package-cart .button:hover,
	.sc-product-button a:hover,
	.sc-product-button.light .button span.tooltip:hover,
	.hb-minicart .action-top-bottom .edit-form-outer .edit-btn,
	.style-1 .woocommerce-tabs .tabs li.active a:before,
	.vc_tta-tabs.vc_tta-style-style-2 .vc_tta-tabs-container .vc_tta-tab:before,
	.vc_tta-tabs.vc_tta-style-style-3 .vc_tta-tabs-container .vc_tta-tab:before,
	.vc_tta-tabs.vc_tta-style-style-7 .vc_tta-tabs-container .vc_tta-tab:before,
	.woof_container_inner > h4:before,
	.widget-style-2 .widget .widget-title:before,
	.widget-style-3 .widget .widget-title:before,
	.wr-onepage-nav a span,
	.wr-nitro-carousel .owl-dots .owl-dot.active span,
	.wr-nitro-carousel .owl-dots .owl-dot:hover span,
	.p-single-images .flex-control-paging li a.flex-active,
	.p-single-images .flex-control-paging li a:hover,
	.woof_list_label li .woof_label_term:hover,
	.woof_list_label li .woof_label_term.checked,
	.page-links a:hover,
	.page-links a:focus,
	.woocommerce-account .form-container .woocommerce-MyAccount-navigation li.is-active:after,
	.wr-pricing-table.style-1 .pricing-item .units:before {
		background-color: {$color};
	}

CSS;
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array
	 */
	public static function body_class( $classes ) {
		// Get options
		$wr_nitro_options = WR_Nitro::get_options();

		// Stretch row
		$stretch = get_post_meta( get_the_ID(), 'wr_layout_stretch', true );

		// Boxed layout
		$boxed =& $wr_nitro_options['wr_layout_boxed'];

		// Offset
		$offset =& $wr_nitro_options['wr_layout_offset'];

		// Mask background color
		$mask =& $wr_nitro_options['wr_layout_boxed_bg_mask_color'];

		// Under Construction mode
		$under_construction       =& $wr_nitro_options['under_construction'];
		$under_construction_style =& $wr_nitro_options['under_construction_style'];

		// Right to left
		$rtl =& $wr_nitro_options['rtl'];

		if ( $stretch ) {
			$classes[] = 'stretch';
		}
		if ( $boxed ) {
			$classes[] = 'boxed';
		}
		if ( ! empty( $mask ) ) {
			$classes[] = 'mask';
		}
		if ( $offset ) {
			$classes[] = 'offset';
		}
		if ( $wr_nitro_options['under_construction'] && ! is_super_admin() ) {
			$classes[] = 'maintenance';
			if ( '1' == $under_construction_style ) {
				$classes[] = 'maintenance-style-1';
			} else {
				$classes[] = 'maintenance-style-2';
			}
		}

		// Single gallery full-screen
		if ( is_singular( 'nitro-gallery' ) && 'slider' == $wr_nitro_options['gallery_single_layout'] && 'full' == $wr_nitro_options['gallery_single_fullscreen'] ) {
			$classes[] = 'gallery-fullscreen';
		}

		if ( wp_is_mobile() ) {
			$classes[] = 'wr-mobile';
		} else {
			$classes[] = 'wr-desktop';
		}

		if ( $rtl ) {
			$classes[] = 'rtl';
		}

		if ( $wr_nitro_options['wc_buynow_btn'] ) {
			$classes[] = 'buynow_activated';

			if ( $wr_nitro_options['wc_disable_btn_atc'] ) {
				$classes[] = 'disable_add_to_cart';
			}
		}

		if ( ! $wr_nitro_options['use_global'] ) {
			$classes[] = 'wr-setting-overrided';
		}

		// Check Visual composer status
		$wr_enable_page_builder = get_post_meta( get_the_ID(), '_wpb_vc_js_status', true );
		if ( function_exists( 'is_' . 'plugin' . '_active' ) && call_user_func( 'is_' . 'plugin' . '_active', 'js_composer/js_composer.php' ) && $wr_enable_page_builder == 'true' ) {
			$classes[] = 'vc-activated';
		}
		// Catalog mode class
		if ( $wr_nitro_options['wc_archive_catalog_mode'] ) {
			$classes[] = 'catalog';
		}

		return $classes;
	}

	/**
	 * Print custom javscript code.
	 *
	 * @return  string
	 */
	public static function custom_inline_js() {
		// Print custom script if defined.
		$wr_nitro_options = WR_Nitro::get_options();

		if ( ! empty( $wr_nitro_options['custom_js'] ) ) {
			echo '<scr' . 'ipt>' . $wr_nitro_options['custom_js'] . '</scr' . 'ipt>';
		}

		// Print highlight mask if the page is previewed in customize screen.
		if ( is_customize_preview() ) {
			echo '<div id="wr_highlight_mask"><a id="wr_customize_link" href="#" data-title="'
				. esc_attr__( 'Customize %s', 'wr-nitro' ) . '"></a></div>';
		}
	}

	/**
	 * Add new attributes to body.
	 *
	 * @return  string
	 */
	public static function body_animation() {
		// Get options
		$wr_nitro_options = WR_Nitro::get_options();

		$attr = array();

		$use_global = get_post_meta( get_the_ID(), 'global_opt', true  );

		// Get page meta
		$parallax = $wr_nitro_options['wr_layout_boxed_parallax'];

		if ( ! $use_global ) {
			$bg_image = get_post_meta( get_the_ID(), 'wr_page_layout_bg_image', true  );
		} else {
			$bg_image = $wr_nitro_options['wr_layout_boxed_bg_image'];
		}

		// Parallax for background image
		if ( $parallax && $bg_image ) {
			$attr[] = 'data-0="background-position:0px 0px" data-1000="background-position: 0px -1000px;"';
		}

		echo '' . implode( ' ', $attr );
	}

	/**
	 * Print HTML code from appropriate template file.
	 *
	 * @param   string  $name  Template name.
	 * @param   string  $path  Path to look for template file.
	 *
	 * @return  void
	 */
	public static function render_layout( $name, $path ) {
		// Load template file.
		$tmp = "{$path}/{$name}";

		get_template_part( $tmp );
	}

	/**
	 * Method to print appropriate HTML code for the specified content.
	 *
	 * @param   string  $style  Content style.
	 * @param   string  $args   Additional arguments.
	 *
	 * @return  void
	 */
	public static function render_template( $style, $args = array() ) {

		self::$style         = $style;
		self::$path          = isset( $args['path']          ) ? $args['path'] . '/' . $style : '';
		self::$sidebar       = isset( $args['sidebar']       ) ? $args['sidebar'] : '';
		self::$sidebar_class = isset( $args['sidebar_class'] ) ? $args['sidebar_class'] : '';

		$layout = $args['layout'];

		// Print HTML for the specified content.
		self::render_content( $style, $args );
		if ( $layout != 'no-sidebar' ) {
			self::render_sidebar();
		}
	}

	/**
	 * Render main content.
	 *
	 * @param   string  $style  Content style.
	 * @param   string  $args   Additional arguments.
	 *
	 * @return  void
	 */
	public static function render_content( $style, $args = array() ) {
		if ( isset( $args['content_class'] ) ) {
			self::$content_class = $args['content_class'];
		}

		if ( $style ) {
			$path = $args['path'] . '/' . $style;
		} else {
			$path = $args['path'];
		}
		self::render_layout( $args['content_layout'], $path );

	}

	/**
	 * Render sidebar.
	 *
	 * @return string
	 */
	public static function render_sidebar() {
		get_sidebar();
	}

	/**
	 * Load WR Nitro template file.
	 *
	 * @return  void
	 */
	public static function get_template( $tmpl, $extension = NULL ) {
		get_template_part( 'woorockets/templates/' . $tmpl, $extension );
	}

	/**
	 * @return int
	 */
	public static function get_google_plus_count() {
		$count = 0;

		if ( isset( $_GET['url'] ) ) {
			$url = $_GET['url'];

			$html = wp_remote_get( 'https://plusone.google.com/_/+1/fastbutton?url=' . urlencode( $url ) );
			$html = explode( '<div id="aggregateCount" class="Oy">', $html['body'] );
			$html = $html[1];
			$count = explode( '</div>', $html );
			$count = $count[0];
			$count = is_numeric( $count ) ? $count : 0;

		}

		echo '' . $count;

		exit;
	}

	/**
	 * Render page loader.
	 *
	 * @return  string
	 */
	public static function page_loader( $output = NULL ) {
		$wr_nitro_options = WR_Nitro::get_options();

		// Loader type
		$type = $wr_nitro_options['page_loader'];

		// Loader effects css
		$effects_css = $wr_nitro_options['page_loader_css'];

		// Loader effects css
		$effects_image = $wr_nitro_options['page_loader_image'];

		if ( 'css' == $type ) {
			$output .= '<div class="pageloader">';

			if ( '9' == $effects_css ) {
				$output .= '<div class="wr-loader-9"><div class="wr-loader"></div><div class="wr-loader wr-loader-inner-2"></div><div class="wr-loader wr-loader-inner-3"></div><div class="wr-loader wr-loader-inner-4"></div></div>';
			} elseif ( '10' == $effects_css ) {
				$output .= '<div class="wr-loader-10"><div class="wr-loader"></div><div class="wr-loader wr-loader-inner-2"></div></div>';
			} elseif ( '11' == $effects_css ) {
				$output .= '<div class="wr-loader-11"><div class="wr-loader"></div><div class="wr-loader wr-loader-inner-2"></div><div class="wr-loader wr-loader-inner-3"></div><div class="wr-loader wr-loader-inner-4"></div><div class="wr-loader wr-loader-inner-5"></div><div class="wr-loader wr-loader-inner-6"></div><div class="wr-loader wr-loader-inner-7"></div><div class="wr-loader wr-loader-inner-8"></div><div class="wr-loader wr-loader-inner-9"></div></div>';
			} elseif ( '12' == $effects_css ) {
				$output .= '<div class="wr-loader-12"><div class="wr-loader"></div><div class="wr-loader wr-loader-inner-2"></div></div>';
			} else {
				$output .= '<div class="wr-loader-' . esc_attr( $effects_css ) . '"><div class="wr-loader"></div></div>';
			}

			$output .= '</div>';
		} elseif ( 'image' == $type && ! empty( $effects_image ) ) {
			$output .= '<div class="pageloader"><div class="loader-img">';
			$output .= '<img src="' . esc_url( $effects_image ) . '" />';
			$output .= '</div></div>';
		}

		return apply_filters( 'wr_page_loader', $output );
	}
}
