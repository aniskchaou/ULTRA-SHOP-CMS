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
class WR_Nitro_Customize_Options_Pages {
	public static function get() {
		return array(
			'title'       => esc_html__( 'Pages', 'wr-nitro' ),
			'description' => '<a target="_blank" rel="noopener noreferrer" href="http://nitro.woorockets.com/docs/document/pages"><span class="fa fa-question-circle has-tip" title="View Documentation for this section"></span></a>',
			'priority'    => 60,
			'sections'    => array(
				'page' => array(
					'title'       => esc_html__( 'Standard Page', 'wr-nitro' ),
					'description' => '',
					'settings'    => array(
						'wr_page_layout' => array(
							'default'           => 'no-sidebar',
							'sanitize_callback' => '',
						),
						'wr_page_custom_widget' => array(
							'default'           => 1,
						),
						'wr_page_layout_sidebar' => array(
							'default'           => 'primary-sidebar',
							'sanitize_callback' => '',
						),
						'wr_page_layout_sidebar_width' => array(
							'default'           => 300,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wr_page_layout_sidebar_sticky' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'wr_page_layout' => array(
							'label'       => esc_html__( 'Sidebar Layout', 'wr-nitro' ),
							'description' => esc_html__( 'Pick up a sidebar layout for your pages.', 'wr-nitro' ),
							'section'     => 'page',
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
						'wr_page_layout_sidebar' => array(
							'label'       => esc_html__( 'Sidebar Content', 'wr-nitro' ),
							'description' => esc_html__( 'Select the sidebar to display on this position.', 'wr-nitro' ),
							'section'     => 'page',
							'type'        => 'select',
							'choices'     => WR_Nitro_Helper::get_sidebars(),
							'required' => array( 'wr_page_layout != no-sidebar' ),
						),
						'wr_page_layout_sidebar_width' => array(
							'label'       => esc_html__( 'Sidebar Width', 'wr-nitro' ),
							'description' => esc_html__( 'Custom width for sidebar.', 'wr-nitro' ),
							'section'     => 'page',
							'type'        => 'WR_Nitro_Customize_Control_Slider',
							'choices'     => array(
								'min'  => 250,
								'max'  => 575,
								'step' => 5,
								'unit' => 'px',
							),
							'required' => array( 'wr_page_layout != no-sidebar' ),
						),
						'wr_page_layout_sidebar_sticky' => array(
							'label'       => esc_html__( 'Enable Sticky Sidebar', 'wr-nitro' ),
							'section'     => 'page',
							'type'        => 'WR_Nitro_Customize_Control_Toggle',
							'required'    => array( 'wr_page_layout != no-sidebar' ),
						),
						'wr_page_custom_widget' => array(
							'section' => 'page',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<h3 class="btn-move-section"><a href="#" class="move-to-section button" data-section="widget_styles">' . esc_html__( 'Customize Widget Styles', 'wr-nitro' ) . '</a></h3>',
							),
							'required' => array( 'wr_page_layout != no-sidebar' ),
						),
					),
				),
				'page_404' => array(
					'title'       => esc_html__( '"404" Page', 'wr-nitro' ),
					'description' => '<div style="margin-bottom: 40px;">' . sprintf( __( 'We have build nice "404" page for you. <a target="_blank" rel="noopener noreferrer" href="%1$s">Take a look</a>.', 'wr-nitro' ), esc_url( home_url( '?p=404404' ) ) ) . '</div>',
					'settings'    => array(
						'page_404_content_heading' => array(),
						'page_404_styling_heading' => array(),
						'page_404_title_font_size' => array(
							'default'           => '88',
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'page_404_title_color' => array(
							'default'           => '#292929',
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'page_404_bg_color' => array(
							'default'           => '#f7f7f7',
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'page_404_bg_image' => array(
							'default'           => '',
							'sanitize_callback' => '',
						),
						'page_404_bg_image_size' => array(
							'default'           => 'auto',
							'sanitize_callback' => '',
						),
						'page_404_bg_image_repeat' => array(
							'default'           => 'no-repeat',
							'sanitize_callback' => '',
						),
						'page_404_bg_image_position' => array(
							'default'           => 'center center',
							'sanitize_callback' => '',
						),
						'page_404_bg_image_attachment' => array(
							'default'           => 'scroll',
							'sanitize_callback' => '',
						),
						'page_404_content' => array(
							'default'           => sprintf( __( '<h3>oopS! Page  not  found</h3>
<p>The page you are looking for was moved, removed, renamed or might never existed.</p>
<a href="%s" class="wr-btn wr-btn-solid mgt30">Back to homepage</a>', 'wr-nitro' ), esc_url( home_url()
							) ),
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'page_404_show_searchform' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'page_404_content_heading' => array(
							'label'   => esc_html__( 'Content', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'page_404',
						),
						'page_404_content' => array(
							'label'       => esc_html__( 'Main Content', 'wr-nitro' ),
							'section'     => 'page_404',
							'type'        => 'WR_Nitro_Customize_Control_Editor',
							'mode'        => 'htmlmixed',
							'button_text' => esc_html__( 'Set Content', 'wr-nitro' ),
						),
						'page_404_show_searchform' => array(
							'label'    => esc_html__( 'Show Search Form', 'wr-nitro' ),
							'section'  => 'page_404',
							'type'     => 'WR_Nitro_Customize_Control_Toggle',
						),
						'page_404_styling_heading' => array(
							'label'   => esc_html__( 'Styling', 'wr-nitro' ),
							'type'    => 'WR_Nitro_Customize_Control_Heading',
							'section' => 'page_404',
						),
						'page_404_bg_color' => array(
							'label'   => esc_html__( 'Background Color', 'wr-nitro' ),
							'section' => 'page_404',
							'type'    => 'WR_Nitro_Customize_Control_Colors',
						),
						'page_404_bg_image' => array(
							'label'    => esc_html__( 'Background Image', 'wr-nitro' ),
							'section'  => 'page_404',
							'type'     => 'WP_Customize_Image_Control',
						),
						'page_404_bg_image_size' => array(
							'label'   => esc_html__( 'Background Size', 'wr-nitro' ),
							'section' => 'page_404',
							'type'    => 'select',
							'choices' => array(
								'auto'    => esc_html__( 'Auto', 'wr-nitro' ),
								'cover'   => esc_html__( 'Cover', 'wr-nitro' ),
								'contain' => esc_html__( 'Contain', 'wr-nitro' ),
							),
							'required' => array( 'page_404_bg_image != ""' ),
						),
						'page_404_bg_image_repeat' => array(
							'label'   => esc_html__( 'Background Repeat', 'wr-nitro' ),
							'section' => 'page_404',
							'type'    => 'select',
							'choices' => array(
								'no-repeat' => esc_html__( 'No Repeat', 'wr-nitro' ),
								'repeat'    => esc_html__( 'Repeat', 'wr-nitro' ),
								'repeat-x'  => esc_html__( 'Repeat X', 'wr-nitro' ),
								'repeat-y'  => esc_html__( 'Repeat Y', 'wr-nitro' ),
							),
							'required' => array( 'page_404_bg_image != ""' ),
						),
						'page_404_bg_image_position' => array(
							'label'   => esc_html__( 'Background Position', 'wr-nitro' ),
							'section' => 'page_404',
							'type'    => 'select',
							'choices' => array(
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
							'required' => array( 'page_404_bg_image != ""' ),
						),
						'page_404_bg_image_attachment' => array(
							'label'   => esc_html__( 'Background Attachment', 'wr-nitro' ),
							'section' => 'page_404',
							'type'    => 'select',
							'choices' => array(
								'scroll' => esc_html__( 'Scroll', 'wr-nitro' ),
								'fixed'  => esc_html__( 'Fixed', 'wr-nitro' ),
							),
							'required' => array( 'page_404_bg_image != ""' ),
						),
						'page_404_title_font_size' => array(
							'label'       => esc_html__( '"404" Text Size', 'wr-nitro' ),
							'description' => esc_html__( 'Here you can change heading size.', 'wr-nitro' ),
							'section'     => 'page_404',
							'type'        => 'WR_Nitro_Customize_Control_Slider',
							'choices'     => array(
								'min'  => 14,
								'max'  => 200,
								'step' => 1,
								'unit' => 'px'
							),
						),
						'page_404_title_color' => array(
							'label'   => esc_html__( '"404" Color', 'wr-nitro' ),
							'section' => 'page_404',
							'type'    => 'WR_Nitro_Customize_Control_Colors',
						),
					),
				),
			),
			'type'     => 'WR_Nitro_Customize_Panel',
			'apply_to' => array( 'page' ),
		);
	}
}
