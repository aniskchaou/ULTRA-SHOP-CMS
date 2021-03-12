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
class WR_Nitro_Customize_Options_Sidebar {
	public static function get() {
		return array(
			'title'       => esc_html__( 'Sidebar', 'wr-nitro' ),
			'priority'    => 105,
			'sections'    => array(
				'sidebar_page' => array(
					'title'    => esc_html__( 'Page', 'wr-nitro' ),
					'settings' => array(
						'sidebar_after_page_title' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'sidebar_before_page_content' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'sidebar_after_page_content' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'sidebar_page_move_to_widget' => array(
							'default'   => 1,
						),
					),
					'controls' => array(
						'sidebar_after_page_title' => array(
							'label'       => esc_html__( 'Sidebar Below Page Title', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display below page title.', 'wr-nitro' ),
							'section'     => 'sidebar_page',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_before_page_content' => array(
							'label'       => esc_html__( 'Sidebar Above Page Content', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display above page content.', 'wr-nitro' ),
							'section'     => 'sidebar_page',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_after_page_content' => array(
							'label'       => esc_html__( 'Sidebar Below Page Content', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display below page content.', 'wr-nitro' ),
							'section'     => 'sidebar_page',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_page_move_to_widget' => array(
							'section' => 'sidebar_page',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<h3 class="btn-move-section"><a href="#" class="move-to-panel button" data-section="widgets">' . esc_html__( 'Edit Widget Content', 'wr-nitro' ) . '</a></h3>',
							),
						),
					),
				),
				'sidebar_blog' => array(
					'title'    => esc_html__( 'Blog', 'wr-nitro' ),
					'settings' => array(
						'sidebar_blog_list_heading' => array(),
						'sidebar_before_blog_content' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'sidebar_after_blog_content' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'sidebar_blog_single_heading' => array(),
						'blog_single_before_post' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'blog_single_before_author' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'blog_single_after_comment' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'sidebar_blog_move_to_widget' => array(
							'default' => 1,
						),
					),
					'controls' => array(
						'sidebar_blog_list_heading' => array(
							'label'   => esc_html__( 'Blog List', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'sidebar_blog',
						),
						'sidebar_before_blog_content' => array(
							'label'       => esc_html__( 'Sidebar Above Blog', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display above blog content.', 'wr-nitro' ),
							'section'     => 'sidebar_blog',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_after_blog_content' => array(
							'label'       => esc_html__( 'Sidebar Below Blog', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display below blog content.', 'wr-nitro' ),
							'section'     => 'sidebar_blog',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_blog_single_heading' => array(
							'label'   => esc_html__( 'Blog Detail', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'sidebar_blog',
						),
						'blog_single_before_post' => array(
							'label'       => esc_html__( 'Sidebar above post', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display above post.', 'wr-nitro' ),
							'section'     => 'sidebar_blog',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'blog_single_before_author' => array(
							'label'       => esc_html__( 'Sidebar below post', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display below post.', 'wr-nitro' ),
							'section'     => 'sidebar_blog',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'blog_single_after_comment' => array(
							'label'       => esc_html__( 'Sidebar below comment area', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display comment area.', 'wr-nitro' ),
							'section'     => 'sidebar_blog',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_blog_move_to_widget' => array(
							'section' => 'sidebar_blog',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<h3 class="btn-move-section"><a href="#" class="move-to-panel button" data-section="widgets">' . esc_html__( 'Edit Widget Content', 'wr-nitro' ) . '</a></h3>',
							),
						),
					),
				),
				'sidebar_wc' => array(
					'title'    => esc_html__( 'WooCommerce', 'wr-nitro' ),
					'settings' => array(
						'sidebar_wc_heading_product_cat' => array(),
						'wc_archive_content_before' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_archive_content_after' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'sidebar_wc_heading_product_details' => array(),
						'wc_single_content_before' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_single_content_after' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'sidebar_wc_heading_cart' => array(),
						'wc_cart_content_before' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_cart_content_after' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'sidebar_wc_heading_checkout' => array(),
						'wc_checkout_content_before' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'wc_checkout_content_after' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'sidebar_wc_heading_mobile_layout' => array(),
						'wc_archive_mobile_content_before' => array(
							'default'           => '0',
							'sanitize_callback' => '',
						),
						'wc_archive_mobile_content_after' => array(
							'default'           => '0',
							'sanitize_callback' => '',
						),
						'sidebar_wc_to_widget' => array(
							'default' => 1,
						),
					),
					'controls' => array(
						'sidebar_wc_heading_product_cat' => array(
							'label'   => esc_html__( 'Product Category', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'sidebar_wc',
						),
						'wc_archive_content_before' => array(
							'label'       => esc_html__( 'Sidebar Above Product List', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display above product list.', 'wr-nitro' ),
							'section'     => 'sidebar_wc',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'wc_archive_content_after' => array(
							'label'       => esc_html__( 'Sidebar Below Product List', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display below product list.', 'wr-nitro' ),
							'section'     => 'sidebar_wc',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_wc_heading_product_details' => array(
							'label'   => esc_html__( 'Product Details', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'sidebar_wc',
						),
						'wc_single_content_before' => array(
							'label'       => esc_html__( 'Sidebar Above Product Details', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display above product details.', 'wr-nitro' ),
							'section'     => 'sidebar_wc',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'wc_single_content_after' => array(
							'label'       => esc_html__( 'Sidebar Below Product Details', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display below product details.', 'wr-nitro' ),
							'section'     => 'sidebar_wc',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_wc_heading_cart' => array(
							'label'   => esc_html__( 'Cart Page', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'sidebar_wc',
						),
						'wc_cart_content_before' => array(
							'label'       => esc_html__( 'Sidebar Above Cart Page', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display above cart page.', 'wr-nitro' ),
							'section'     => 'sidebar_wc',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'wc_cart_content_after' => array(
							'label'       => esc_html__( 'Sidebar Below Cart Page', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display below cart page.', 'wr-nitro' ),
							'section'     => 'sidebar_wc',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_wc_heading_checkout' => array(
							'label'   => esc_html__( 'Checkout Page', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'sidebar_wc',
						),
						'wc_checkout_content_before' => array(
							'label'       => esc_html__( 'Sidebar Above Checkout Form', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display above checkout page.', 'wr-nitro' ),
							'section'     => 'sidebar_wc',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'wc_checkout_content_after' => array(
							'label'       => esc_html__( 'Sidebar Below Checkout Form', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display below checkout page.', 'wr-nitro' ),
							'section'     => 'sidebar_wc',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_wc_heading_mobile_layout' => array(
							'label'   => esc_html__( 'Mobile Layout', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'sidebar_wc',
						),
						'wc_archive_mobile_content_before' => array(
							'label'       => esc_html__( 'Sidebar Above Product List', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display above product list.', 'wr-nitro' ),
							'section'     => 'sidebar_wc',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'wc_archive_mobile_content_after' => array(
							'label'       => esc_html__( 'Sidebar Below Product List', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display below product list.', 'wr-nitro' ),
							'section'     => 'sidebar_wc',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_wc_to_widget' => array(
							'section' => 'sidebar_wc',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<h3 class="btn-move-section"><a href="#" class="move-to-panel button" data-section="widgets">' . esc_html__( 'Edit Widget Content', 'wr-nitro' ) . '</a></h3>',
							),
						),
					),
				),
				'sidebar_footer' => array(
					'title'    => esc_html__( 'Footer', 'wr-nitro' ),
					'settings' => array(
						'sidebar_before_footer_widget' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'sidebar_after_footer_widget' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'sidebar_footer_to_widget' => array(
							'default' => 1,
						),
					),
					'controls' => array(
						'sidebar_before_footer_widget' => array(
							'label'       => esc_html__( 'Sidebar Above Footer Widget', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display above footer widget.', 'wr-nitro' ),
							'section'     => 'sidebar_footer',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_after_footer_widget' => array(
							'label'       => esc_html__( 'Sidebar Below Footer Widget', 'wr-nitro' ),
							'description' => esc_html__( 'Select sidebar to display below footer widget.', 'wr-nitro' ),
							'section'     => 'sidebar_footer',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
						),
						'sidebar_footer_to_widget' => array(
							'section' => 'sidebar_footer',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<h3 class="btn-move-section"><a href="#" class="move-to-panel button" data-section="widgets">' . esc_html__( 'Edit Widget Content', 'wr-nitro' ) . '</a></h3>',
							),
						),
					),
				),
			),
			'type' => 'WR_Nitro_Customize_Panel',
		);
	}
}
