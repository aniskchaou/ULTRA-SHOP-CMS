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
 * Plug WR Nitro theme options into WordPress Theme Customize.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Customize_Options_WooCommerce {
	public static function get() {
		// Check some plugins is activate
		$wr_is_cf7_activated                = call_user_func( 'is_' . 'plugin' . '_active', 'contact-form-7/wp-contact-form-7.php' );
		$wr_is_yith_wish_list_activated     = call_user_func( 'is_' . 'plugin' . '_active', 'yith-woocommerce-wishlist/init.php' );
		$wr_is_yith_wish_list_pre_activated = call_user_func( 'is_' . 'plugin' . '_active', 'yith-woocommerce-wishlist-premium/init.php' );
		$wr_is_yith_compare_activated       = call_user_func( 'is_' . 'plugin' . '_active', 'yith-woocommerce-compare/init.php' );
		$wr_is_yith_compare_pre_activated   = call_user_func( 'is_' . 'plugin' . '_active', 'yith-woocommerce-compare-premium/init.php' );
		$wr_is_custom_attribute_activated   = call_user_func( 'is_' . 'plugin' . '_active', 'wr-custom-attributes/main.php' );

		// Get all contact form 7.
		$wr_cf7_query = new WP_Query(
			array(
				'posts_per_page' => -1,
				'post_type'      => 'wpcf7_contact_form',
				'post_status'    => 'publish',
			)
		);
		$wr_cf7_list = array( '0' => '-- Select Form --' );

		if ( $wr_cf7_query->post_count ) {
			foreach( $wr_cf7_query->posts as $val ){
				$wr_cf7_list[ $val->ID ] = $val->post_title;
			}
		};

		return array(
			'title'       => esc_html__( 'WooCommerce', 'wr-nitro' ),
			'description' => '<a target="_blank" rel="noopener noreferrer" href="http://nitro.woorockets.com/docs/document/woocommerce"><span class="fa fa-question-circle has-tip" title="View Documentation for this section"></span></a>',
			'priority'    => 40,
			'sections' => array(
				'wc_general' => array(
					'title'    => esc_html__( 'General', 'wr-nitro' ),
					'settings' => array(
						'wc_archive_catalog_mode' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_archive_catalog_mode_price' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_archive_catalog_mode_button' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_archive_catalog_mode_button_text' => array(
							'default'           => esc_html__( 'Call Me', 'wr-nitro' ),
							'sanitize_callback' => '',
						),
						'wc_archive_catalog_mode_button_action' => array(
							'default'           => 'simple',
							'sanitize_callback' => '',
						),
						'wc_archive_catalog_mode_button_action_simple' => array(
							'default'           => 'skype:your_skype?chat',
							'sanitize_callback' => '',
						),
						'wc_archive_catalog_mode_button_action_cf7' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_general_compare' =>
							( ( $wr_is_yith_compare_activated || $wr_is_yith_compare_pre_activated ) ? array(
								'default'           => 1,
								'sanitize_callback' => ''
							) : array()
						),
						'wc_general_wishlist' =>
							( ( $wr_is_yith_wish_list_activated || $wr_is_yith_wish_list_pre_activated ) ? array(
								'default'           => 1,
								'sanitize_callback' => '',
							) : array()
						),
						'wc_general_quickview' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_buynow_btn' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_disable_btn_atc' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_buynow_checkout' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_buynow_payment_info' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_icon_set' => array(
							'default'           => 'set-6',
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'wc_archive_catalog_mode' => array(
							'label'       => esc_html__( 'Enable Catalog Mode', 'wr-nitro' ),
							'description' => esc_html__( 'Turn off all e-commerce elements and transform your eCommerce store into an online catalog.', 'wr-nitro' ),
							'section'     => 'wc_general',
							'type'        => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_archive_catalog_mode_price' => array(
							'label'    => esc_html__( 'Enable Price', 'wr-nitro' ),
							'section'  => 'wc_general',
							'type'     => 'WR_Nitro_Customize_Control_Toggle',
							'required' => array( 'wc_archive_catalog_mode == 1' ),
						),
						'wc_archive_catalog_mode_button' => array(
							'label'       => esc_html__( 'Enable Custom Button', 'wr-nitro' ),
							'description' => esc_html__( 'Add a custom button in single product.', 'wr-nitro' ),
							'section'     => 'wc_general',
							'type'        => 'WR_Nitro_Customize_Control_Toggle',
							'required'    => array( 'wc_archive_catalog_mode == 1' ),
						),
						'wc_archive_catalog_mode_button_text' => array(
							'label'    => esc_html__( 'Button Text', 'wr-nitro' ),
							'section'  => 'wc_general',
							'type'     => 'text',
							'required' => array(
								'wc_archive_catalog_mode_button == 1',
								'wc_archive_catalog_mode == 1'
							),
						),
						'wc_archive_catalog_mode_button_action' => array(
							'label'    => esc_html__( 'Button Type', 'wr-nitro' ),
							'section'  => 'wc_general',
							'type'     => 'select',
							'choices'  =>
								( $wr_is_cf7_activated ? array(
									'simple' => esc_html__( 'Simple Text or Link', 'wr-nitro' ),
									'cf7'    => esc_html__( 'Contact Form 7', 'wr-nitro' ),
								): array(
									'simple' => esc_html__( 'Simple Text or Link', 'wr-nitro' ),
								) ),
							'required' => array(
								'wc_archive_catalog_mode_button == 1',
								'wc_archive_catalog_mode == 1'
							),
						),
						'wc_archive_catalog_mode_button_action_simple' => array(
							'label'    => esc_html__( 'Action For Button', 'wr-nitro' ),
							'section'  => 'wc_general',
							'type'     => 'text',
							'required' => array(
								'wc_archive_catalog_mode_button_action == simple',
								'wc_archive_catalog_mode_button == 1',
								'wc_archive_catalog_mode == 1'
							),
						),
						'wc_archive_catalog_mode_button_action_cf7' => array(
							'label'    => esc_html__( 'Select Form', 'wr-nitro' ),
							'section'  => 'wc_general',
							'type'     => 'select',
							'choices'  => $wr_cf7_list,
							'required' => array(
								'wc_archive_catalog_mode_button_action == cf7',
								'wc_archive_catalog_mode_button == 1',
								'wc_archive_catalog_mode == 1'
							),
						),
						'wc_general_compare' =>
							( ( $wr_is_yith_compare_activated || $wr_is_yith_compare_pre_activated ) ? array(
								'label'    => esc_html__( 'Enable Product Comparision', 'wr-nitro' ),
								'section'  => 'wc_general',
								'type'     => 'WR_Nitro_Customize_Control_Toggle',
								'required' => array( 'wc_archive_catalog_mode == 0' )
							): array()
						),
						'wc_general_wishlist' =>
							( ( $wr_is_yith_wish_list_activated || $wr_is_yith_wish_list_pre_activated ) ? array(
								'label'   => esc_html__( 'Enable Wishlist', 'wr-nitro' ),
								'section' => 'wc_general',
								'type'    => 'WR_Nitro_Customize_Control_Toggle',
								'required' => array( 'wc_archive_catalog_mode == 0' ),
							) : array()
						),
						'wc_general_quickview' => array(
							'label'   => esc_html__( 'Enable Quickview', 'wr-nitro' ),
							'section' => 'wc_general',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_buynow_btn' => array(
							'label'       => esc_html__( 'Enable "buy now" button', 'wr-nitro' ),
							'description' => esc_html__( 'The "Buy Now" button you can see on the single item of Shop Category page.', 'wr-nitro' ),
							'section'     => 'wc_general',
							'type'        => 'WR_Nitro_Customize_Control_Toggle',
							'required'    => array( 'wc_archive_catalog_mode == 0' ),
						),
						'wc_disable_btn_atc' => array(
							'label'    => esc_html__( 'Disable "add to cart" button', 'wr-nitro' ),
							'section'  => 'wc_general',
							'type'     => 'WR_Nitro_Customize_Control_Toggle',
							'required' => array(
								'wc_archive_catalog_mode = 0',
								'wc_buynow_btn = 1'
							),
						),
						'wc_buynow_checkout' => array(
							'label'    => esc_html__( 'Checkout Product', 'wr-nitro' ),
							'section'  => 'wc_general',
							'type'     => 'select',
							'choices'  => array(
								'1' => esc_html__( 'Checkout Current Product Only', 'wr-nitro' ),
								'2' => esc_html__( 'Checkout All Products In Cart', 'wr-nitro' ),
							),
							'required' => array(
								'wc_archive_catalog_mode = 0',
								'wc_buynow_btn = 1'
							),
						),
						'wc_buynow_payment_info' => array(
							'label'       => esc_html__( 'Button Action', 'wr-nitro' ),
							'description' => esc_html__( 'After clicking on "Buy Now" button the user would see payment information type.', 'wr-nitro' ),
							'section'     => 'wc_general',
							'type'        => 'select',
							'choices'     => array(
								'1' => esc_html__( 'Show Popup Window', 'wr-nitro' ),
								'2' => esc_html__( 'Redirect To Checkout Page', 'wr-nitro' ),
							),
							'required' => array(
								'wc_archive_catalog_mode = 0',
								'wc_buynow_btn = 1'
							),
						),
						'wc_icon_set' => array(
							'label'   => esc_html__( 'Icon Set', 'wr-nitro' ),
							'section' => 'wc_general',
							'type'    => 'WR_Nitro_Customize_Control_Select_Image',
							'choices' => array(
								'set-1'  => '',
								'set-2'  => '',
								'set-3'  => '',
								'set-4'  => '',
								'set-5'  => '',
								'set-6'  => '',
							),
						),
					),
				),
				'product_list' => array(
					'title'    => esc_html__( 'Product Category', 'wr-nitro' ),
					'settings' => array(
						'wc_archive_general_heading' => array(),
						'wc_archive_style' => array(
							'default'           => 'grid',
							'sanitize_callback' => '',
						),
						'wc_archive_border_wrap' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_archive_layout' => array(
							'default'           => 'no-sidebar',
							'sanitize_callback' => '',
						),
						'wc_archive_custom_widget' => array(
							'default'           => 1,
						),
						'wc_archive_sidebar_width' => array(
							'default'           => 300,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wc_archive_sidebar_sticky' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_archive_layout_column' => array(
							'default'           => 4,
							'sanitize_callback' => '',
						),
						'wc_archive_layout_column_gutter' => array(
							'default'           => 30,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wc_archive_number_products' => array(
							'default'           => 12,
							'sanitize_callback' => '',
						),
						'wc_archive_style_heading' => array(),
						'wc_archive_pagination_type' => array(
							'default'           => 'number',
							'sanitize_callback' => '',
						),
						'wc_archive_page_title' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_archive_page_title_content' => array(
							'default'           => esc_html__( 'Welcome to My Store', 'wr-nitro' ),
							'sanitize_callback' => '',
							'transport'         => 'postMessage',
						),
						'wc_archive_item_heading' => array(),
						'wc_archive_item_layout' => array(
							'default'           => '1',
							'sanitize_callback' => '',
						),
						'wc_archive_item_hover_style' => array(
							'default'           => 'default',
							'sanitize_callback' => '',
						),
						'wc_archive_item_mask_color' => array(
							'default'           => 'rgba(0, 0, 0, 0.7)',
							'sanitize_callback' => '',
						),
						'wc_archive_item_transition' => array(
							'default'           => 'fade',
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wc_archive_item_animation' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_archive_full_width' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_display_custom_attr' =>
							( $wr_is_custom_attribute_activated ? array(
								'default'           => 0,
								'sanitize_callback' => '',
							) : array()
						),
						'wc_display_custom_attr_position' =>
							( $wr_is_custom_attribute_activated ? array(
								'default'           => 'after-title',
								'sanitize_callback' => '',
							) : array()
						),
					),
					'controls' => array(
						'wc_archive_general_heading' => array(
							'label'   => esc_html__( 'General', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'product_list',
						),
						'wc_archive_page_title' => array(
							'label'   => esc_html__( 'Show Page Title', 'wr-nitro' ),
							'section' => 'product_list',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_archive_page_title_content' => array(
							'label'    => esc_html__( 'Page Title Content', 'wr-nitro' ),
							'section'  => 'product_list',
							'type'     => 'text',
							'required' => array( 'wc_archive_page_title != 0' ),
						),
						'wc_archive_layout' => array(
							'label'       => esc_html__( 'Sidebar Layout', 'wr-nitro' ),
							'description' => esc_html__( 'Select a sidebar layout', 'wr-nitro' ),
							'section'     => 'product_list',
							'type'        => 'WR_Nitro_Customize_Control_HTML',
							'choices'     => array(
								'left-sidebar'  => array(
									'html'  => '<div class="icon-cols icon-sidebar icon-left-sidebar"></div>',
									'title' => esc_html__( 'Sidebar on the left content', 'wr-nitro' ),
								),
								'no-sidebar'  => array(
									'html'  => '<div class="icon-cols icon-sidebar icon-no-sidebar"></div>',
									'title' => esc_html__( 'Without Sidebar', 'wr-nitro' ),
								),
								'right-sidebar'  => array(
									'html'  => '<div class="icon-cols icon-sidebar icon-right-sidebar"></div>',
									'title' => esc_html__( 'Sidebar on the right content', 'wr-nitro' ),
								),
							),
						),
						'wc_archive_sidebar_width' => array(
							'label'       => esc_html__( 'Sidebar Width', 'wr-nitro' ),
							'description' => esc_html__( 'Custom width for sidebar.', 'wr-nitro' ),
							'section'     => 'product_list',
							'type'        => 'WR_Nitro_Customize_Control_Slider',
							'choices'     => array(
								'min'  => 250,
								'max'  => 575,
								'step' => 5,
								'unit' => 'px',
							),
							'required' => array( 'wc_archive_layout != no-sidebar' ),
						),
						'wc_archive_sidebar_sticky' => array(
							'label'       => esc_html__( 'Enable Sticky Sidebar', 'wr-nitro' ),
							'section'     => 'product_list',
							'type'        => 'WR_Nitro_Customize_Control_Toggle',
							'required'    => array( 'wc_archive_layout != no-sidebar' ),
						),
						'wc_archive_custom_widget' => array(
							'section' => 'product_list',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<h3 class="btn-move-section"><a href="#" class="move-to-section button" data-section="widget_styles">' . esc_html__( 'Customize Widget Styles', 'wr-nitro' ) . '</a></h3>',
							),
							'required' => array( 'wc_archive_layout != no-sidebar' ),
						),
						'wc_archive_style_heading' => array(
							'label'   => esc_html__( 'Product List', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'product_list',
						),
						'wc_archive_style' => array(
							'label'       => esc_html__( 'Layout', 'wr-nitro' ),
							'description' => esc_html__( 'Select a layout for product list.', 'wr-nitro' ),
							'section'     => 'product_list',
							'type'        => 'WR_Nitro_Customize_Control_HTML',
							'choices'     => array(
								'grid'  => array(
									'html'  => '<div class="icon-cols icon-grid"></div>',
									'title' => esc_html__( 'Grid', 'wr-nitro' ),
								),
								'masonry'  => array(
									'html' => '<div class="icon-cols icon-masonry"><span></span></div>',
									'title' => esc_html__( 'Masonry', 'wr-nitro' ),
								),
								'list'  => array(
									'html' => '<div class="icon-cols icon-list"><span></span></div>',
									'title' => esc_html__( 'List', 'wr-nitro' ),
								),
							),
						),
						'wc_archive_border_wrap' => array(
							'label'       => esc_html__( 'Enable Border Wrap', 'wr-nitro' ),
							'description' => esc_html__( 'Enable border wrap to each product item.', 'wr-nitro' ),
							'section'     => 'product_list',
							'type'        => 'WR_Nitro_Customize_Control_Toggle',
							'required'    => array(
								'wc_archive_style = grid',
								'wc_archive_pagination_type = number'
							),
						),
						'wc_archive_layout_column' => array(
							'label'   => esc_html__( 'Number Of Columns', 'wr-nitro' ),
							'section' => 'product_list',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'2'  => array(
									'html'  => '<div class="icon-cols cols-small icon-2cols"></div>',
									'title' => esc_html__( '2 Columns', 'wr-nitro' ),
								),
								'3'  => array(
									'html' => '<div class="icon-cols cols-small icon-3cols"></div>',
									'title' => esc_html__( '3 Columns', 'wr-nitro' ),
								),
								'4'  => array(
									'html' => '<div class="icon-cols cols-small icon-4cols"></div>',
									'title' => esc_html__( '4 Columns', 'wr-nitro' ),
								),
								'5'  => array(
									'html' => '<div class="icon-cols cols-small icon-5cols"></div>',
									'title' => esc_html__( '5 Columns', 'wr-nitro' ),
								),
								'6'  => array(
									'html' => '<div class="icon-cols cols-small icon-6cols"></div>',
									'title' => esc_html__( '6 Columns', 'wr-nitro' ),
								),
							),
							'required' => array( 'wc_archive_style != list' ),
						),
						'wc_archive_layout_column_gutter' => array(
							'label'       => esc_html__( 'Column Gutter Width', 'wr-nitro' ),
							'description' => esc_html__( 'Space between 2 products.', 'wr-nitro' ),
							'section'     => 'product_list',
							'type'        => 'WR_Nitro_Customize_Control_Slider',
							'choices'     => array(
								'min'  => 0,
								'max'  => 60,
								'step' => 1,
								'unit' => 'px',
							),
							'required' => array( 'wc_archive_style != list' ),
						),
						'wc_archive_full_width' => array(
							'label'    => esc_html__( 'Enable Full Width', 'wr-nitro' ),
							'section'  => 'product_list',
							'type'     => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_archive_item_animation' => array(
							'label'       => esc_html__( 'Enable Item Animation', 'wr-nitro' ),
							'description' => esc_html__( 'Enable or disable product item animation on mouse scrolling.', 'wr-nitro' ),
							'section'     => 'product_list',
							'type'        => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_archive_number_products' => array(
							'label'       => esc_html__( 'Number of products per page', 'wr-nitro' ),
							'description' => esc_html__( 'Change number of products displayed per page.', 'wr-nitro' ),
							'section'     => 'product_list',
							'type'        => 'number',
							'input_attrs' => array(
								'min'   => 1,
								'step'  => 1,
							),
						),
						'wc_archive_pagination_type' => array(
							'label'       => esc_html__( 'Pagination Type', 'wr-nitro' ),
							'description' => esc_html__( 'Choose your page loading style.', 'wr-nitro' ),
							'section'     => 'product_list',
							'type'        => 'select',
							'choices'     => array(
								'number'   => esc_html__( 'Number', 'wr-nitro' ),
								'loadmore' => esc_html__( 'Load More', 'wr-nitro' ),
								'infinite' => esc_html__( 'Infinite Scroll', 'wr-nitro' ),
							),
						),
						'wc_archive_item_heading' => array(
							'label'   => esc_html__( 'Product Item', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'product_list',
						),
						'wc_archive_item_layout' => array(
							'label'       => esc_html__( 'Layout', 'wr-nitro' ),
							'description' => esc_html__( 'Choose layout for a product item.', 'wr-nitro' ),
							'section'     => 'product_list',
							'type'        => 'WR_Nitro_Customize_Control_HTML',
							'choices'     => array(
								'1'  => array(
									'html' => '<div class="icon-item-layout icon-item-1"><span></span></div>',
									'title' => esc_html__( 'Button inside thumbnail', 'wr-nitro' ),
								),
								'2'  => array(
									'html' => '<div class="icon-item-layout icon-item-2"><span></span></div>',
									'title' => esc_html__( 'Button outsite thumbnail', 'wr-nitro' ),
								),
								'3'  => array(
									'html' => '<div class="icon-item-layout icon-item-3"></div>',
									'title' => esc_html__( 'Button slide from bottom', 'wr-nitro' ),
								),
								'4'  => array(
									'html' => '<div class="icon-item-layout icon-item-4"></div>',
									'title' => esc_html__( 'Button slide from bottom', 'wr-nitro' ),
								),
								'5'  => array(
									'html' => '<div class="icon-item-layout icon-item-5"><span></span></div>',
									'title' => esc_html__( 'Title and Button inside thumbnail', 'wr-nitro' ),
								),
								'6'  => array(
									'html' => '<div class="icon-item-layout icon-item-1 icon-item-6"><span></span><span></span></div>',
									'title' => esc_html__( 'Button inside thumbnail', 'wr-nitro' ),
								),
							),
							'required'    => array( 'wc_archive_style != list' ),
						),
						'wc_archive_item_hover_style' => array(
							'label'       => esc_html__( 'Hover Effects', 'wr-nitro' ),
							'description' => esc_html__( 'Pick up an animation style for product item on mouse hover', 'wr-nitro' ),
							'section'     => 'product_list',
							'type'        => 'select',
							'choices'     => array(
								'default'   => esc_html__( 'None', 'wr-nitro' ),
								'scale'     => esc_html__( 'Zoom In', 'wr-nitro' ),
								'mask'      => esc_html__( 'Mask Overlay', 'wr-nitro' ),
								'flip-back' => esc_html__( '2-nd Image Preview', 'wr-nitro' ),
							),
						),
						'wc_archive_item_mask_color' => array(
							'label'    => esc_html__( 'Mask Overlay Color', 'wr-nitro' ),
							'section'  => 'product_list',
							'type'     => 'WR_Nitro_Customize_Control_Colors',
							'required' => array( 'wc_archive_item_hover_style = mask' ),
						),
						'wc_archive_item_transition' => array(
							'label'    => esc_html__( 'Transition Effects', 'wr-nitro' ),
							'section'  => 'product_list',
							'type'     => 'select',
							'required' => array( 'wc_archive_item_hover_style = flip-back' ),
							'choices'  => array(
								'fade'              => esc_html__( 'Fade In', 'wr-nitro' ),
								'slide-from-left'   => esc_html__( 'Slide From Left', 'wr-nitro' ),
								'slide-from-right'  => esc_html__( 'Slide From Right', 'wr-nitro' ),
								'slide-from-top'    => esc_html__( 'Slide From Top', 'wr-nitro' ),
								'slide-from-bottom' => esc_html__( 'Slide From Bottom', 'wr-nitro' ),
								'zoom-in'           => esc_html__( 'Zoom In', 'wr-nitro' ),
								'zoom-out'          => esc_html__( 'Zoom Out', 'wr-nitro' ),
								'flip'              => esc_html__( 'Flip', 'wr-nitro' ),
							),
						),
						'wc_display_custom_attr' =>
							( $wr_is_custom_attribute_activated ? array(
								'label'   => esc_html__( 'Show custom attribute color', 'wr-nitro' ),
								'section' => 'product_list',
								'type'	  => 'WR_Nitro_Customize_Control_Toggle'
							) : array()
						),
						'wc_display_custom_attr_position' =>
							( $wr_is_custom_attribute_activated ? array(
								'label'   => esc_html__( 'Position', 'wr-nitro' ),
								'section' => 'product_list',
								'type'    => 'select',
								'choices' => array(
									'after-title'  => esc_html__( 'After title', 'wr-nitro' ),
									'before-title' => esc_html__( 'Before title', 'wr-nitro' ),
								),
								'required' => array( 'wc_display_custom_attr == 1' ),
							) : array()
						),
					),
				),
				'product_categories' => array(
					'title'    => esc_html__( 'Product Categories', 'wr-nitro' ),
					'settings' => array(
						'wc_categories_style' => array(
							'default'           => 'grid',
							'sanitize_callback' => '',
						),
						'wc_categories_layout_column' => array(
							'default'           => 4,
							'sanitize_callback' => '',
						),
						'wc_categories_layout_column_gutter' => array(
							'default'           => 30,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'wc_categories_style' => array(
							'label'       => esc_html__( 'Layout', 'wr-nitro' ),
							'description' => esc_html__( 'Select a layout for product list.', 'wr-nitro' ),
							'section'     => 'product_categories',
							'type'        => 'WR_Nitro_Customize_Control_HTML',
							'choices'     => array(
								'grid'  => array(
									'html'  => '<div class="icon-cols icon-grid"></div>',
									'title' => esc_html__( 'Grid', 'wr-nitro' ),
								),
								'masonry'  => array(
									'html' => '<div class="icon-cols icon-masonry"><span></span></div>',
									'title' => esc_html__( 'Masonry', 'wr-nitro' ),
								),
							),
						),
						'wc_categories_layout_column' => array(
							'label'   => esc_html__( 'Number Of Columns', 'wr-nitro' ),
							'section' => 'product_categories',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'2'  => array(
									'html'  => '<div class="icon-cols icon-2cols"></div>',
									'title' => esc_html__( '2 Columns', 'wr-nitro' ),
								),
								'3'  => array(
									'html' => '<div class="icon-cols icon-3cols"></div>',
									'title' => esc_html__( '3 Columns', 'wr-nitro' ),
								),
								'4'  => array(
									'html' => '<div class="icon-cols icon-4cols"></div>',
									'title' => esc_html__( '4 Columns', 'wr-nitro' ),
								),
							),
						),
						'wc_categories_layout_column_gutter' => array(
							'label'       => esc_html__( 'Column Gutter Width', 'wr-nitro' ),
							'description' => esc_html__( 'Space between 2 products.', 'wr-nitro' ),
							'section'     => 'product_categories',
							'type'        => 'WR_Nitro_Customize_Control_Slider',
							'choices'     => array(
								'min'  => 0,
								'max'  => 60,
								'step' => 1,
								'unit' => 'px',
							),
							'required' => array( 'wc_archive_style != list' ),
						),
					),
				),
				'product_single' => array(
					'title'    => esc_html__( 'Product Details', 'wr-nitro' ),
					'settings' => array(
						'wc_single_general' => array(),
						'wc_single_style' => array(
							'default'           => 2,
							'sanitize_callback' => '',
						),
						'wc_single_layout' => array(
							'default'           => 'no-sidebar',
							'sanitize_callback' => '',
						),
						'wc_single_custom_widget' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_single_sidebar' => array(
							'default'           => 'wc-sidebar',
							'sanitize_callback' => '',
						),
						'wc_single_sidebar_width' => array(
							'default'           => 300,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wc_single_sidebar_sticky' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_single_title' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_single_thumb_position' => array(
							'default'           => 'bottom',
							'sanitize_callback' => '',
						),
						'wc_single_tab_style' => array(
							'default'           => 'accordion',
							'sanitize_callback' => '',
						),
						'wc_single_tab_position' => array(
							'default'           => 'default',
							'sanitize_callback' => '',
						),
						'wc_single_product_custom_style' => array(),
						'wc_single_product_custom_bg' => array(
							'default'           => '#f9f9fb',
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wc_single_single_nav' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_single_floating_button' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_single_breadcrumb' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_single_product_meta' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_single_social_share' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_single_image_zoom' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_single_product_tab' => array(),
						'wc_single_product_tab_description' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_single_product_tab_info' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_single_product_tab_review' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_single_product_custom_content_data' => array(
							'default'           => '',
							'sanitize_callback' => '',
						),
						'wc_single_product_custom_content_position' => array(
							'default'           => 'after_except',
							'sanitize_callback' => '',
						),
						'wc_single_product_related_heading' => array(),
						'wc_single_product_related' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_single_product_upsell' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_single_product_recent_viewed' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_single_product_related_full' => array(
							'default'           => 'boxed',
							'sanitize_callback' => '',
						),
						'wc_single_product_show' => array(
							'default'           => 4,
							'sanitize_callback' => '',
						)
					),
					'controls' => array(
						'wc_single_general' => array(
							'label'    => esc_html__( 'General', 'wr-nitro' ),
							'type'     => 'WR_Nitro_Customize_Control_Heading',
							'section'  => 'product_single',
						),
						'wc_single_title' => array(
							'label'    => esc_html__( 'Show Page Title', 'wr-nitro' ),
							'section'  => 'product_single',
							'type'     => 'WR_Nitro_Customize_Control_Toggle',
							'required' => array(
								'wc_single_style = 2',
							),
						),
						'wc_single_floating_button' => array(
							'label'   => esc_html__( 'Enable Floating "Add To Cart"', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_single_breadcrumb' => array(
							'label'    => esc_html__( 'Show Breadcrumb', 'wr-nitro' ),
							'section'  => 'product_single',
							'type'     => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_single_product_meta' => array(
							'label'    => esc_html__( 'Show Product Meta', 'wr-nitro' ),
							'section'  => 'product_single',
							'type'     => 'WR_Nitro_Customize_Control_Toggle',
							'required' => array( 'wc_single_style != 5' )
						),
						'wc_single_social_share' => array(
							'label'   => esc_html__( 'Show Social Share', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_single_product_custom_style' => array(
							'label'    => esc_html__( 'Main Information', 'wr-nitro' ),
							'type'     => 'WR_Nitro_Customize_Control_Heading',
							'section'  => 'product_single',
						),
						'wc_single_style' => array(
							'label'       => esc_html__( 'Layout', 'wr-nitro' ),
							'description' => esc_html__( 'Choose the layout for a single product.', 'wr-nitro' ),
							'section'     => 'product_single',
							'type'        => 'WR_Nitro_Customize_Control_HTML',
							'choices'     => array(
								'1'  => array(
									'html'  => '<div class="icon-cols icon-single-layout-1"><span></span><span></span></div>',
									'title' => esc_html__( 'Full Image', 'wr-nitro' ),
								),
								'2'  => array(
									'html'  => '<div class="icon-cols icon-single-layout-2"><span></span></div>',
									'title' => esc_html__( 'Small Image', 'wr-nitro' ),
								),
								'3'  => array(
									'html'  => '<div class="icon-cols icon-single-layout-3"><span></span></div>',
									'title' => esc_html__( 'Large Image', 'wr-nitro' ),
								),
								'4'  => array(
									'html'  => '<div class="icon-cols icon-single-layout-4"><span></span><span></span></div>',
									'title' => esc_html__( 'Medium Image', 'wr-nitro' ),
								),
								'5'  => array(
									'html'  => '<div class="icon-cols icon-single-layout-5"><span></span></div>',
									'title' => esc_html__( 'Medium Image', 'wr-nitro' ),
								),
							),
						),
						'wc_single_layout' => array(
							'label'       => esc_html__( 'Sidebar Layout', 'wr-nitro' ),
							'description' => esc_html__( 'Select a sidebar layout.', 'wr-nitro' ),
							'section'     => 'product_single',
							'type'        => 'WR_Nitro_Customize_Control_HTML',
							'choices'     => array(
								'left-sidebar'  => array(
									'html'  => '<div class="icon-cols icon-sidebar icon-left-sidebar"></div>',
									'title' => esc_html__( 'Sidebar on the left content', 'wr-nitro' ),
								),
								'no-sidebar'  => array(
									'html'  => '<div class="icon-cols icon-sidebar icon-no-sidebar"></div>',
									'title' => esc_html__( 'Without Sidebar', 'wr-nitro' ),
								),
								'right-sidebar'  => array(
									'html'  => '<div class="icon-cols icon-sidebar icon-right-sidebar"></div>',
									'title' => esc_html__( 'Sidebar on the right content', 'wr-nitro' ),
								),
							),
							'required' => array(
								'wc_single_style = 2',
							),
						),
						'wc_single_sidebar' => array(
							'label'   => esc_html__( 'Sidebar Content', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'select',
							'choices' => WR_Nitro_Helper::get_sidebars(),
							'required'    => array(
								'wc_single_style  = 2',
								'wc_single_layout != no-sidebar',
							),
						),
						'wc_single_sidebar_width' => array(
							'label'       => esc_html__( 'Sidebar Width', 'wr-nitro' ),
							'description' => esc_html__( 'Custom width for sidebar.', 'wr-nitro' ),
							'section'     => 'product_single',
							'type'        => 'WR_Nitro_Customize_Control_Slider',
							'choices'     => array(
								'min'  => 250,
								'max'  => 575,
								'step' => 5,
								'unit' => 'px',
							),
							'required'    => array(
								'wc_single_style  = 2',
								'wc_single_layout != no-sidebar',
							),
						),
						'wc_single_sidebar_sticky' => array(
							'label'       => esc_html__( 'Enable Sticky Sidebar', 'wr-nitro' ),
							'section'     => 'product_single',
							'type'        => 'WR_Nitro_Customize_Control_Toggle',
							'required'    => array(
								'wc_single_layout != no-sidebar',
								'wc_single_style  = 2',
							),
						),
						'wc_single_custom_widget' => array(
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<h3 class="btn-move-section"><a href="#" class="move-to-section button" data-section="widget_styles">' . esc_html__( 'Customize Widget Styles', 'wr-nitro' ) . '</a></h3>',
							),
							'required' => array(
								'wc_single_style  = 2',
								'wc_single_layout != no-sidebar',
							),
						),
						'wc_single_thumb_position' => array(
							'label'       => esc_html__( 'Thumbnail Position', 'wr-nitro' ),
							'section'     => 'product_single',
							'type'        => 'select',
							'choices'     => array(
								'left'   => esc_html__( 'Left', 'wr-nitro' ),
								'right'  => esc_html__( 'Right', 'wr-nitro' ),
								'bottom' => esc_html__( 'Bottom', 'wr-nitro' ),
							),
							'required' => array(
								'wc_single_style = 2',
							),
						),
						'wc_single_image_zoom' => array(
							'label'    => esc_html__( 'Enable image zoom on hover', 'wr-nitro' ),
							'section'  => 'product_single',
							'type'     => 'WR_Nitro_Customize_Control_Toggle',
							'required' => array(
								'wc_single_style = 2',
							),
						),
						'wc_single_product_custom_bg' => array(
							'label'   => esc_html__( 'Background Color', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Colors',
							'section' => 'product_single',
							'required' => array(
								'wc_single_style == "1|4"',
							),
						),
						'wc_single_single_nav' => array(
							'label'   => esc_html__( 'Enable Product Navigation', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
							'required' => array(
								'wc_single_style = 2',
							),
						),
						'wc_single_product_tab' => array(
							'label'   => esc_html__( 'Extra Information', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_Heading',
						),
						'wc_single_tab_style' => array(
							'label'       => esc_html__( 'Layout', 'wr-nitro' ),
							'section'     => 'product_single',
							'type'        => 'select',
							'choices'     => array(
								'default'   => esc_html__( 'Horizontal Tabs', 'wr-nitro' ),
								'clean'     => esc_html__( 'Vertical Tabs', 'wr-nitro' ),
								'accordion' => esc_html__( 'Accordions', 'wr-nitro' ),
							),
							'required'    => array(
								'wc_single_style = "2|3"',
							),
						),
						'wc_single_tab_position' => array(
							'label'       => esc_html__( 'Position', 'wr-nitro' ),
							'section'     => 'product_single',
							'type'        => 'select',
							'choices'     => array(
								'default'        => esc_html__( 'Above Other Products', 'wr-nitro' ),
								'below_details'  => esc_html__( 'Below Product Details', 'wr-nitro' ),
							),
							'required'    => array(
								'wc_single_style  = 2',
								'wc_single_layout = no-sidebar',
							),
						),
						'wc_single_product_tab_description' => array(
							'label'   => esc_html__( 'Show Description', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_single_product_tab_info' => array(
							'label'   => esc_html__( 'Show Attributes', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_single_product_tab_review' => array(
							'label'   => esc_html__( 'Show Reviews', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_single_product_custom_content_data' => array(
							'label'   => esc_html__( 'Custom Content', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_Editor',
							'mode'        => 'htmlmixed',
							'placeholder' => '',
							'button_text' => esc_html__( 'Edit Content', 'wr-nitro' ),
						),
						'wc_single_product_custom_content_position' => array(
							'label'       => esc_html__( 'Position', 'wr-nitro' ),
							'section'     => 'product_single',
							'type'        => 'select',
							'choices'     => array(
								'after_title'     => esc_html__( 'After product title', 'wr-nitro' ),
								'after_price'     => esc_html__( 'After product price', 'wr-nitro' ),
								'after_except'   	=> esc_html__( 'After product excerpt', 'wr-nitro' ),
								'after_add_cart'    => esc_html__( 'After add to cart button', 'wr-nitro' ),
							),
						),
						'wc_single_product_related_heading' => array(
							'label'   => esc_html__( 'Other Products', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'product_single',
						),
						'wc_single_product_related_full' => array(
							'label'       => esc_html__( 'Layout', 'wr-nitro' ),
							'description' => esc_html__( 'These settings can be applied only to style 1 and style 2 without sidebar', 'wr-nitro' ),
							'section'     => 'product_single',
							'type'        => 'WR_Nitro_Customize_Control_HTML',
							'choices'     => array(
								'boxed'  => array(
									'html'  => '<div class="icon-wc-related icon-related-boxed"><span></span><span></span></div>',
									'title' => esc_html__( 'Boxed', 'wr-nitro' ),
								),
								'full'  => array(
									'html'  => '<div class="icon-wc-related icon-related-full"><span></span><span></span></div>',
									'title' => esc_html__( 'Full Width', 'wr-nitro' ),
								),
							),
							'required'    => array(
								'wc_single_style = "1|2|5"',
								'wr_layout_boxed = 0'
							),
						),
						'wc_single_product_related' => array(
							'label'   => esc_html__( 'Show Related Products', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_single_product_upsell' => array(
							'label'   => esc_html__( 'Show Upsell Products', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_single_product_recent_viewed' => array(
							'label'   => esc_html__( 'Show Recently Viewed Products', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_single_product_show' => array(
							'label'   => esc_html__( 'Number Of Products To Show', 'wr-nitro' ),
							'section' => 'product_single',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 1,
								'max'  => 6,
								'step' => 1,
							),
						),
					),
				),
				'wc_thankyou' => array(
					'title'    => esc_html__( 'Thank you Page', 'wr-nitro' ),
					'settings' => array(
						'wc_thankyou_content' => array(
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'wc_thankyou_content' => array(
							'label'       => esc_html__( 'Page content', 'wr-nitro' ),
							'description' => esc_html__( 'The content will be placed after "Thank You" content. HTML tags are allowed.', 'wr-nitro' ),
							'section'     => 'wc_thankyou',
							'type'        => 'WR_Nitro_Customize_Control_Editor',
							'mode'        => 'htmlmixed',
							'button_text' => esc_html__( 'Set Content', 'wr-nitro' ),
							'placeholder' => esc_html__( "/**\n * Write your custom content here.\n */", 'wr-nitro' ),
						),
					),
				),
				'product_mobile' => array(
					'title'    => esc_html__( 'Mobile Layout', 'wr-nitro' ),
					'settings' => array(
						'wc_archive_mobile_general_heading' => array(),
						'wc_detail_mobile_general_heading' => array(),
						'wc_archive_mobile_categories' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_archive_mobile_style' => array(
							'default'           => 'mobile-list',
							'sanitize_callback' => '',
						),
						'wc_archive_mobile_layout_column' => array(
							'default'           => 2,
							'sanitize_callback' => '',
						),
						'wc_archive_mobile_sidebar' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wc_archive_mobile_sidebar_content' => array(
							'default'           => 'wc-sidebar',
							'sanitize_callback' => '',
						),
						'wc_detail_mobile_sticky_cart' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'wc_archive_mobile_general_heading' => array(
							'label'   => esc_html__( 'Product Category', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'product_mobile',
						),
						'wc_archive_mobile_categories' => array(
							'label'   => esc_html__( 'Show Categories', 'wr-nitro' ),
							'section' => 'product_mobile',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_archive_mobile_sidebar' => array(
							'label'   => esc_html__( 'Show Sidebar', 'wr-nitro' ),
							'section' => 'product_mobile',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wc_archive_mobile_sidebar_content' => array(
							'label'       => esc_html__( 'Sidebar Content', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display.', 'wr-nitro' ),
							'section'     => 'product_mobile',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
							'required' => array( 'wc_archive_mobile_sidebar != 0' ),
						),
						'wc_archive_mobile_style' => array(
							'label'       => esc_html__( 'Layout', 'wr-nitro' ),
							'description' => esc_html__( 'Select a layout for product list.', 'wr-nitro' ),
							'section'     => 'product_mobile',
							'type'        => 'WR_Nitro_Customize_Control_HTML',
							'choices'     => array(
								'mobile-grid'  => array(
									'html'  => '<div class="icon-cols icon-grid grid-2cols"></div>',
									'title' => esc_html__( 'Grid', 'wr-nitro' ),
								),
								'mobile-list'  => array(
									'html' => '<div class="icon-cols icon-list"><span></span></div>',
									'title' => esc_html__( 'List', 'wr-nitro' ),
								),
							),
						),
						'wc_archive_mobile_layout_column' => array(
							'label'   => esc_html__( 'Number Of Columns', 'wr-nitro' ),
							'section' => 'product_mobile',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => array(
									'html'  => '<div class="icon-cols icon-1cols"></div>',
									'title' => esc_html__( '2 Columns', 'wr-nitro' ),
								),
								'2'  => array(
									'html'  => '<div class="icon-cols icon-2cols"></div>',
									'title' => esc_html__( '2 Columns', 'wr-nitro' ),
								),
							),
							'required' => array( 'wc_archive_mobile_style == mobile-grid' ),
						),
						'wc_detail_mobile_general_heading' => array(
							'label'   => esc_html__( 'Product Details', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'product_mobile',
						),
						'wc_detail_mobile_sticky_cart' => array(
							'label'   => esc_html__( 'Sticky Add To Cart', 'wr-nitro' ),
							'section' => 'product_mobile',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
					),
				),
			),
			'type'     => 'WR_Nitro_Customize_Panel',
			'apply_to' => array( 'woocommerce' ),
		);
	}
}
