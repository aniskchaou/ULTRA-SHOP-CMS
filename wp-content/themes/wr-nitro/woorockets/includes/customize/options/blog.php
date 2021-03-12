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
class WR_Nitro_Customize_Options_Blog {
	public static function get() {
		return array(
			'title'       => esc_html__( 'Blog', 'wr-nitro' ),
			'description' => '<a target="_blank" rel="noopener noreferrer" href="http://nitro.woorockets.com/docs/document/blog"><span class="fa fa-question-circle has-tip" title="View Documentation for this section"></span></a>',
			'priority'    => 50,
			'sections'    => array(
				'blog_list' => array(
					'title'    => esc_html__( 'Blog List', 'wr-nitro' ),
					'settings' => array(
						'blog_style' => array(
							'default'           => 'classic',
							'sanitize_callback' => '',
						),
						'blog_full_width' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'blog_color' => array(
							'default'           => 'boxed',
							'sanitize_callback' => '',
							'transport'         => 'postMessage',
						),
						'blog_masonry_column' => array(
							'default'           => '3',
							'sanitize_callback' => '',
						),
						'blog_layout' => array(
							'default'           => 'right-sidebar',
							'sanitize_callback' => '',
						),
						'blog_sidebar_sticky' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'blog_custom_widget' => array(
							'default'           => 1,
						),
						'blog_sidebar' => array(
							'default'           => 'primary-sidebar',
							'sanitize_callback' => '',
						),
						'blog_sidebar_width' => array(
							'default'           => 300,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'blog_style' => array(
							'label'       => esc_html__( 'Layout', 'wr-nitro' ),
							'description' => esc_html__( 'Select a layout for your blog list.', 'wr-nitro' ),
							'section'     => 'blog_list',
							'type'        => 'WR_Nitro_Customize_Control_HTML',
							'choices'     => array(
								'classic'  => array(
									'html' => '<div class="icon-cols icon-blog-classic"><span></span></div>',
									'title' => esc_html__( 'Classic', 'wr-nitro' ),
								),
								'simple'  => array(
									'html' => '<div class="icon-cols icon-blog-simple"><span></span></div>',
									'title' => esc_html__( 'Simple', 'wr-nitro' ),
								),
								'zigzag'  => array(
									'html' => '<div class="icon-cols icon-blog-zigzag"><span></span></div>',
									'title' => esc_html__( 'Zigzag', 'wr-nitro' ),
								),
								'masonry'  => array(
									'html' => '<div class="icon-cols icon-masonry"><span></span></div>',
									'title' => esc_html__( 'Masonry', 'wr-nitro' ),
								),
							),
						),
						'blog_masonry_column' => array(
							'label'       => esc_html__( 'Number of Columns', 'wr-nitro' ),
							'description' => esc_html__( 'Number of columns to show.', 'wr-nitro' ),
							'section'     => 'blog_list',
							'type'        => 'WR_Nitro_Customize_Control_HTML',
							'choices'     => array(
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
							'required' => array( 'blog_style = masonry' ),
						),
						'blog_color' => array(
							'label'       => esc_html__( 'Item style', 'wr-nitro' ),
							'section'     => 'blog_list',
							'type'        => 'WR_Nitro_Customize_Control_HTML',
							'choices'     => array(
								'default'  => array(
									'html' => '<div class="icon-cols icon-blog-classic"><span></span></div>',
									'title' => esc_html__( 'Default', 'wr-nitro' ),
								),
								'boxed'  => array(
									'html' => '<div class="icon-cols icon-blog-boxed icon-blog-classic"><span><span></span></span></div>',
									'title' => esc_html__( 'Boxed', 'wr-nitro' ),
								),
							),
							'required' => array( 'blog_style != zigzag' ),
						),
						'blog_full_width' => array(
							'label'    => esc_html__( 'Enable Full Width', 'wr-nitro' ),
							'section'  => 'blog_list',
							'type'     => 'WR_Nitro_Customize_Control_Toggle',
						),
						'blog_layout' => array(
							'label'       => esc_html__( 'Sidebar layout', 'wr-nitro' ),
							'description' => esc_html__( 'Select a sidebar layout for your blog list.', 'wr-nitro' ),
							'section'     => 'blog_list',
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
						'blog_sidebar' => array(
							'label'       => esc_html__( 'Sidebar Content', 'wr-nitro' ),
							'description' => esc_html__( 'Pick up a default sidebar.', 'wr-nitro' ),
							'section'     => 'blog_list',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
							'required' => array( 'blog_layout != no-sidebar' ),
						),
						'blog_sidebar_width' => array(
							'label'       => esc_html__( 'Sidebar Width', 'wr-nitro' ),
							'description' => esc_html__( 'Custom width for sidebar.', 'wr-nitro' ),
							'section'     => 'blog_list',
							'type'        => 'WR_Nitro_Customize_Control_Slider',
							'choices'     => array(
								'min'  => 250,
								'max'  => 575,
								'step' => 5,
								'unit' => 'px',
							),
							'required' => array( 'blog_layout != no-sidebar' ),
						),
						'blog_sidebar_sticky' => array(
							'label'       => esc_html__( 'Enable Sticky Sidebar', 'wr-nitro' ),
							'section'     => 'blog_list',
							'type'        => 'WR_Nitro_Customize_Control_Toggle',
							'required'    => array( 'blog_layout != no-sidebar' ),
						),
						'blog_custom_widget' => array(
							'section' => 'blog_list',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<h3 class="btn-move-section"><a href="#" class="move-to-section button" data-section="widget_styles">' . esc_html__( 'Customize Widget Styles', 'wr-nitro' ) . '</a></h3>',
							),
							'required' => array( 'blog_layout != no-sidebar' ),
						),
					),
				),
				'blog_single' => array(
					'title'    => esc_html__( 'Single Post', 'wr-nitro' ),
					'settings' => array(
						'blog_single_title_heading' => array(),
						'blog_single_layout' => array(
							'default'           => 'no-sidebar',
							'sanitize_callback' => '',
						),
						'blog_single_custom_widget' => array(
							'default'           => 1,
						),
						'blog_single_sidebar' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'blog_single_sidebar_width' => array(
							'default'           => 300,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'blog_single_sidebar_sticky' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'blog_single_title_style' => array(
							'default'           => '1',
							'sanitize_callback' => '',
						),
						'blog_single_title_font_size' => array(
							'default'           => 45,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'blog_single_title_padding_top' => array(
							'default'           => 100,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'blog_single_title_padding_bottom' => array(
							'default'           => 100,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'blog_single_title_full_screen' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'blog_single_heading' => array(),
						'blog_single_social_share' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'blog_single_author'    => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'blog_single_navigation' => array(
							'default'            => 1,
							'sanitize_callback'  => '',
						),
						'blog_single_comment' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'blog_single_title_heading' => array(
							'label'   => esc_html__( 'Post Title', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'blog_single',
						),
						'blog_single_title_style' => array(
							'label'   => esc_html__( 'Layout', 'wr-nitro' ),
							'section' => 'blog_single',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => array(
									'html' => '<div class="icon-item-layout icon-blog-title-1"><span></span></div>',
									'title' => esc_html__( 'Title outside post thumbnail', 'wr-nitro' ),
								),
								'2'  => array(
									'html' => '<div class="icon-item-layout icon-blog-title-2"><span></span></div>',
									'title' => esc_html__( 'Title inside post thumbnail', 'wr-nitro' ),
								),
							),
						),
						'blog_single_title_font_size' => array(
							'label'   => esc_html__( 'Font Size', 'wr-nitro' ),
							'section' => 'blog_single',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 12,
								'max'  => 80,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'blog_single_title_padding_top' => array(
							'label'   => esc_html__( 'Padding Top', 'wr-nitro' ),
							'section' => 'blog_single',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 0,
								'max'  => 500,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'blog_single_title_padding_bottom' => array(
							'label'   => esc_html__( 'Padding Bottom', 'wr-nitro' ),
							'section' => 'blog_single',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 0,
								'max'  => 500,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'blog_single_title_full_screen' => array(
							'label'    => esc_html__( 'Enable Full Screen', 'wr-nitro' ),
							'section'  => 'blog_single',
							'type'     => 'WR_Nitro_Customize_Control_Toggle',
							'required' => array( 'blog_single_title_style = 2' ),
						),
						'blog_single_heading' => array(
							'label'   => esc_html__( 'Post Content', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'blog_single',
						),
						'blog_single_layout' => array(
							'label'       => esc_html__( 'Sidebar Layout', 'wr-nitro' ),
							'description' => esc_html__( 'Choose global layout settings: Left sidebar, No sidebar, Right sidebar.', 'wr-nitro' ),
							'section'     => 'blog_single',
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
						'blog_single_sidebar' => array(
							'label'       => esc_html__( 'Sidebar Content', 'wr-nitro' ),
							'description' => esc_html__( 'Select the sidebar to display on this position.', 'wr-nitro' ),
							'section'     => 'blog_single',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
							'required' => array( 'blog_single_layout != no-sidebar' ),
						),
						'blog_single_sidebar_width' => array(
							'label'       => esc_html__( 'Sidebar Width', 'wr-nitro' ),
							'description' => esc_html__( 'Custom width for sidebar.', 'wr-nitro' ),
							'section'     => 'blog_single',
							'type'        => 'WR_Nitro_Customize_Control_Slider',
							'choices'     => array(
								'min'  => 250,
								'max'  => 575,
								'step' => 5,
								'unit' => 'px',
							),
							'required' => array( 'blog_single_layout != no-sidebar' ),
						),
						'blog_single_sidebar_sticky' => array(
							'label'       => esc_html__( 'Enable Sticky Sidebar', 'wr-nitro' ),
							'section'     => 'blog_single',
							'type'        => 'WR_Nitro_Customize_Control_Toggle',
							'required'    => array( 'blog_single_layout != no-sidebar' ),
						),
						'blog_single_custom_widget' => array(
							'section' => 'blog_single',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<h3 class="btn-move-section"><a href="#" class="move-to-section button" data-section="widget_styles">' . esc_html__( 'Customize Widget Styles', 'wr-nitro' ) . '</a></h3>',
							),
							'required' => array( 'blog_single_layout != no-sidebar' ),
						),
						'blog_single_social_share' => array(
							'label'   => esc_html__( 'Show Social Sharing', 'wr-nitro' ),
							'section' => 'blog_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'blog_single_author' => array(
							'label'   => esc_html__( 'Show Author Info', 'wr-nitro' ),
							'section' => 'blog_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'blog_single_navigation' => array(
							'label'   => esc_html__( 'Show Post Navigation', 'wr-nitro' ),
							'section' => 'blog_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'blog_single_comment' => array(
							'label'   => esc_html__( 'Show Comment Area', 'wr-nitro' ),
							'section' => 'blog_single',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
					),
				),
			),
			'type'     => 'WR_Nitro_Customize_Panel',
			'apply_to' => array( 'blog' ),
		);
	}
}
