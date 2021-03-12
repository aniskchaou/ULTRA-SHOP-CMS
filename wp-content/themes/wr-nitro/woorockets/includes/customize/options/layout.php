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
class WR_Nitro_Customize_Options_Layout {
	public static function get() {
		return array(
			'title'       => esc_html__( 'Layout', 'wr-nitro' ),
			'description' => '<a target="_blank" rel="noopener noreferrer" href="http://nitro.woorockets.com/docs/document/layout"><span class="fa fa-question-circle has-tip" title="View Documentation for this section"></span></a>',
			'priority'    => 11,
			'sections'    => array(
				'layout_general' => array(
					'title'       => esc_html__( 'General', 'wr-nitro' ),
					'description' => '',
					'settings' => array(
						'wr_layout_offset' => array(
							'default'           => 0,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wr_layout_content_width_unit' => array(
							'default'           => 'pixel',
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wr_layout_content_width' => array(
							'default'           => 1170,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wr_layout_content_width_percentage' => array(
							'default'           => 100,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wr_layout_gutter_width' => array(
							'default'           => 30,
							'sanitize_callback' => '',
						),
						'wr_layout_boxed' => array(
							'default'           => 0,
							'sanitize_callback' => '',
						),
						'general_custom_color' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wr_preview' => array(
							'default'           => 'desktop',
							'transport'         => 'postMessage',
						),
					),
					'controls' => array(
						'wr_layout_offset' => array(
							'label'       => esc_html__( 'Offset Width', 'wr-nitro' ),
							'description' => esc_html__( 'Add a border around body', 'wr-nitro' ),
							'section'     => 'layout_general',
							'type'        => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 0,
								'max'  => 100,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'wr_layout_content_width_unit' => array(
							'label'       => esc_html__( 'Content Width', 'wr-nitro' ),
							'description' => esc_html__( 'Set the maximum allowed width for content', 'wr-nitro' ),
							'section'     => 'layout_general',
							'type'        => 'radio',
							'choices'  => array(
								'pixel'      => esc_html__( 'px', 'wr-nitro' ),
								'percentage' => esc_html__( '%', 'wr-nitro' ),
							),
						),
						'wr_layout_content_width' => array(
							'section' => 'layout_general',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 760,
								'max'  => 1920,
								'step' => 10,
								'unit' => 'px',
							),
							'required' => array( 'wr_layout_content_width_unit == pixel' ),
						),
						'wr_layout_content_width_percentage' => array(
							'section' => 'layout_general',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 20,
								'max'  => 100,
								'step' => 1,
								'unit' => '%',
							),
							'required' => array( 'wr_layout_content_width_unit == percentage' ),
						),
						'wr_layout_gutter_width' => array(
							'label'       => esc_html__( 'Gutter Width', 'wr-nitro' ),
							'description' => esc_html__( 'The width of the space between columns', 'wr-nitro' ),
							'section'     => 'layout_general',
							'type'        => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 20,
								'max'  => 60,
								'step' => 10,
								'unit' => 'px',
							),
						),
						'wr_layout_boxed' => array(
							'label'   => esc_html__( 'Enable Boxed Layout', 'wr-nitro' ),
							'section' => 'layout_general',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'general_custom_color' => array(
							'section' => 'layout_general',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1' => '<div class="btn-move-section"><a href="#" class="move-to-section button" data-section="color_general">' . esc_html__( 'Edit Color', 'wr-nitro' ) . '</a><a href="#" class="move-to-section button" data-section="typo_general">' . __( 'Edit Typography', 'wr-nitro' ) . '</a></div>',
							),
						),
						'wr_preview' => array(
							'section' => 'layout_general',
							'type'    => 'hidden',
						),
					)
				),
				'page_title' => array(
					'title'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'settings'    => array(
						'wr_page_title' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wr_page_title_layout' => array(
							'default'           => 'layout-1',
							'sanitize_callback' => '',
						),
						'wr_page_title_fullscreen' => array(
							'default'           => 0,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wr_page_title_breadcrumbs' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
						'wr_page_title_padding_top' => array(
							'default'           => 80,
							'sanitize_callback' => '',
							'transport'         => 'postMessage',
						),
						'wr_page_title_padding_bottom' => array(
							'default'           => 80,
							'sanitize_callback' => '',
							'transport'         => 'postMessage',
						),
						'wr_page_title_heading_min_height' => array(
							'default'           => 214,
							'sanitize_callback' => '',
						),
						'page_title_custom_color' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'wr_page_title' => array(
							'label'   => esc_html__( 'Show Page Title', 'wr-nitro' ),
							'section' => 'page_title',
							'type'    => 'WR_Nitro_Customize_Control_Toggle',
						),
						'wr_page_title_fullscreen' => array(
							'label'    => esc_html__( 'Enable Full Width', 'wr-nitro' ),
							'section'  => 'page_title',
							'type'     => 'WR_Nitro_Customize_Control_Toggle',
							'required' => array( 'wr_page_title = 1' ),
						),
						'wr_page_title_layout' => array(
							'label'       => esc_html__( 'Layout', 'wr-nitro' ),
							'section'     => 'page_title',
							'type'        => 'WR_Nitro_Customize_Control_Select_Image',
							'choices'     => array(
								'layout-1'  => esc_html__( 'Layout 1', 'wr-nitro' ),
								'layout-2'  => esc_html__( 'Layout 2', 'wr-nitro' ),
								'layout-3'  => esc_html__( 'Layout 3', 'wr-nitro' ),
								'layout-4'  => esc_html__( 'Layout 4', 'wr-nitro' ),
								'layout-5'  => esc_html__( 'Layout 5', 'wr-nitro' ),
							),
							'required' => array( 'wr_page_title = 1' ),
						),
						'wr_page_title_breadcrumbs' => array(
							'label'    => esc_html__( 'Show Breadcrumb', 'wr-nitro' ),
							'section'  => 'page_title',
							'type'     => 'WR_Nitro_Customize_Control_Toggle',
							'required' => array( 'wr_page_title = 1' ),
						),
						'wr_page_title_padding_top' => array(
							'label'    => esc_html__( 'Padding Top', 'wr-nitro' ),
							'section'  => 'page_title',
							'type'     => 'WR_Nitro_Customize_Control_Slider',
							'required' => array( 'wr_page_title = 1' ),
							'choices' => array(
								'min'  => 0,
								'max'  => 500,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'wr_page_title_padding_bottom' => array(
							'label'    => esc_html__( 'Padding Bottom', 'wr-nitro' ),
							'section'  => 'page_title',
							'type'     => 'WR_Nitro_Customize_Control_Slider',
							'required' => array( 'wr_page_title = 1' ),
							'choices' => array(
								'min'  => 0,
								'max'  => 500,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'wr_page_title_heading_min_height' => array(
							'label'   => esc_html__( 'Min Height', 'wr-nitro' ),
							'section' => 'page_title',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 0,
								'max'  => 1000,
								'step' => 5,
								'unit' => 'px',
							),
							'required' => array( 'wr_page_title = 1' ),
						),
						'page_title_custom_color' => array(
							'section' => 'page_title',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<div class="btn-move-section"><a href="#" class="move-to-section button" data-section="color_pages">' . esc_html__( 'Edit Color', 'wr-nitro' ) . '</a><a href="#" class="move-to-section button" data-section="typo_page_title">' . __( 'Edit Typography', 'wr-nitro' ) . '</a></div>',
							),
							'required' => array( 'wr_page_title = 1' ),
						),
					),
				),
				'layout_button' => array(
					'title'    => esc_html__( 'Button', 'wr-nitro' ),
					'settings' => array(
						'btn_border_width' => array(
							'default'           => 2,
							'sanitize_callback' => '',
							'transport'         => 'postMessage',
						),
						'btn_border_radius' => array(
							'default'           => 2,
							'sanitize_callback' => '',
							'transport'         => 'postMessage',
						),
						'btn_padding' => array(
							'default'           => 20,
							'sanitize_callback' => '',
							'transport'         => 'postMessage',
						),
						'button_custom_color' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'btn_padding' => array(
							'label'   => esc_html__( 'Padding ( Left + Right )', 'wr-nitro' ),
							'section' => 'layout_button',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 5,
								'max'  => 50,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'btn_border_radius' => array(
							'label'   => esc_html__( 'Border Radius', 'wr-nitro' ),
							'section' => 'layout_button',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 0,
								'max'  => 50,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'btn_border_width' => array(
							'label'       => esc_html__( 'Border Width', 'wr-nitro' ),
							'section'     => 'layout_button',
							'type'        => 'WR_Nitro_Customize_Control_Slider',
							'choices'     => array(
								'min'  => 1,
								'max'  => 10,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'button_custom_color' => array(
							'section' => 'layout_button',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<div class="btn-move-section"><a href="#" class="move-to-section button" data-section="color_button">' . esc_html__( 'Edit Color', 'wr-nitro' ) . '</a><a href="#" class="move-to-section button" data-section="typo_button">' . esc_html__( 'Edit Typography', 'wr-nitro' ) . '</a></div>',
							),
						),
					),
				),
			)
		);
	}
}
