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
class WR_Nitro_Customize_Options_Typography {
	public static function get() {
		return array(
			'title'       => esc_html__( 'Typography', 'wr-nitro' ),
			'description' => '<a target="_blank" rel="noopener noreferrer" href="http://nitro.woorockets.com/docs/document/typography"><span class="fa fa-question-circle has-tip" title="View Documentation for this section"></span></a>',
			'priority'    => 12,
			'sections' => array(
				'typo_general' => array(
					'title'    => esc_html__( 'General', 'wr-nitro' ),
					'settings' => array(
						'body_font_heading' => array(),
						'body_font_type' => array(
							'default'           => 'google',
							'sanitize_callback' => 'sanitize_key',
						),
						'body_custom_font' => array(
							'default'           => '',
							'sanitize_callback' => 'esc_url_raw',
						),
						'body_google_font' => array(
							'default' => array(
								'family'     => 'Lato',
								'fontWeight' => 400,
							),
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'body_standard_font' => array(
							'default'           => 'Verdana',
							'sanitize_callback' => '',
						),
						'body_font_size' => array(
							'default'           => 100,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'body_line_height' => array(
							'default'           => 24,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'body_letter_spacing' => array(
							'default'           => 0,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'heading_font_heading' => array(),
						'heading_font_type' => array(
							'default'           => 'google',
							'sanitize_callback' => '',
						),
						'heading_google_font' => array(
							'default' => array(
								'family'     => 'Lato',
								'italic'     => 0,
								'underline'  => 0,
								'uppercase'  => 0,
								'fontWeight' => 400,
							),
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'heading_custom_font' => array(
							'default'           => '',
							'sanitize_callback' => 'esc_url_raw',
						),
						'heading_standard_font' => array(
							'default'           => 'Verdana',
							'sanitize_callback' => '',
						),
						'heading_font_size' => array(
							'default'           => 16,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'heading_line_height' => array(
							'default'           => 18,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'heading_letter_spacing' => array(
							'default'           => 0,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'typo_general_custom_color' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'body_font_heading' => array(
							'label'       => esc_html__( 'Body Font', 'wr-nitro' ),
							'description' => esc_html__( 'Customize the typography style of main body.', 'wr-nitro' ),
							'type'        => 'WR_Nitro_Customize_Control_Heading',
							'section'     => 'typo_general',
						),
						'body_font_type' => array(
							'label'   => esc_html__( 'Body Font Type', 'wr-nitro' ),
							'section' => 'typo_general',
							'type'    => 'select',
							'choices' => array(
								'standard' => esc_html__( 'Standard Fonts', 'wr-nitro' ),
								'google'   => esc_html__( 'Google Fonts', 'wr-nitro' ),
								'custom'   => esc_html__( 'Custom Fonts', 'wr-nitro' ),
							),
						),
						'body_custom_font' => array(
							'section'  => 'typo_general',
							'type'     => 'WR_Nitro_Customize_Control_Upload_Font',
							'required' => array( 'body_font_type = custom' ),
						),
						'body_google_font' => array(
							'section' => 'typo_general',
							'type'    => 'WR_Nitro_Customize_Control_Typography',
							'choices' => array(
								'family', 'fontWeight'
							),
							'required' => array( 'body_font_type = google' ),
						),
						'body_standard_font' => array(
							'section' => 'typo_general',
							'type'    => 'select',
							'choices' => array(
								'Verdana'      => 'Verdana',
								'Georgia'      => 'Georgia',
								'Courier New'  => 'Courier New',
								'Arial'        => 'Arial',
								'Tahoma'       => 'Tahoma',
								'Trebuchet MS' => 'Trebuchet MS'
							),
							'required' => array( 'body_font_type = standard' ),
						),
						'body_font_size' => array(
							'label'   => esc_html__( 'Body Text Size', 'wr-nitro' ),
							'section' => 'typo_general',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 50,
								'max'  => 250,
								'step' => 5,
								'unit' => '%',
							),
						),
						'body_line_height' => array(
							'label'   => esc_html__( 'Body Line Height', 'wr-nitro' ),
							'section' => 'typo_general',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 10,
								'max'  => 60,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'body_letter_spacing' => array(
							'label'   => esc_html__( 'Body Letter Spacing', 'wr-nitro' ),
							'section' => 'typo_general',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 0,
								'max'  => 10,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'heading_font_heading' => array(
							'label'       => esc_html__( 'Heading Font', 'wr-nitro' ),
							'description' => esc_html__( 'Customize the typography style of heading area. You can see changes on Page Title area.', 'wr-nitro' ),
							'type'        => 'WR_Nitro_Customize_Control_Heading',
							'section'     => 'typo_general',
						),
						'heading_font_type' => array(
							'label'   => esc_html__( 'Heading Font Type', 'wr-nitro' ),
							'section' => 'typo_general',
							'type'    => 'select',
							'choices' => array(
								'standard' => esc_html__( 'Standard Fonts', 'wr-nitro' ),
								'google'   => esc_html__( 'Google Fonts', 'wr-nitro' ),
								'custom'   => esc_html__( 'Custom Fonts', 'wr-nitro' ),
							),
						),
						'heading_google_font' => array(
							'section'     => 'typo_general',
							'type'        => 'WR_Nitro_Customize_Control_Typography',
							'choices'     => array(
								'family', 'fontWeight', 'italic', 'underline', 'uppercase',
							),
							'required' => array( 'heading_font_type = google' ),
						),
						'heading_custom_font' => array(
							'section'  => 'typo_general',
							'type'     => 'WR_Nitro_Customize_Control_Upload_Font',
							'required' => array( 'heading_font_type = custom' ),
						),
						'heading_standard_font' => array(
							'section' => 'typo_general',
							'type'    => 'select',
							'choices' => array(
								'Verdana'      => 'Verdana',
								'Georgia'      => 'Georgia',
								'Courier New'  => 'Courier New',
								'Arial'        => 'Arial',
								'Tahoma'       => 'Tahoma',
								'Trebuchet MS' => 'Trebuchet MS'
							),
							'required' => array( 'heading_font_type = standard' ),
						),
						'heading_font_size' => array(
							'label'   => esc_html__( 'Heading Base Size', 'wr-nitro' ),
							'section' => 'typo_general',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 10,
								'max'  => 30,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'heading_line_height' => array(
							'label'   => esc_html__( 'Heading Line Height', 'wr-nitro' ),
							'section' => 'typo_general',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 10,
								'max'  => 60,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'heading_letter_spacing' => array(
							'label'   => esc_html__( 'Heading Letter Spacing', 'wr-nitro' ),
							'section' => 'typo_general',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 0,
								'max'  => 10,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'typo_general_custom_color' => array(
							'section' => 'typo_general',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<div class="btn-move-section"><a href="#" class="move-to-section button" data-section="color_general">' . esc_html__( 'Edit Color', 'wr-nitro' ) . '</a><a href="#" class="move-to-section button" data-section="layout_general">' . esc_html__( 'Edit Layout', 'wr-nitro' ) . '</a></div>',
							),
						),
					),
				),
				'typo_page_title' => array(
					'title'    => esc_html__( 'Page Title', 'wr-nitro' ),
					'settings'    => array(
						'wr_page_title_heading_font' => array(
							'default' => array(
								'italic'     => 0,
								'underline'  => 0,
								'uppercase'  => 0,
							),
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wr_page_title_heading_font_size' => array(
							'default'           => 44,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wr_page_title_heading_line_height' => array(
							'default'           => 44,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'wr_page_title_heading_letter_spacing' => array(
							'default'           => 0,
							'transport'         => 'postMessage',
							'sanitize_callback' => '',
						),
						'typo_page_title_custom_color' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'wr_page_title_heading_font' => array(
							'label'   => esc_html__( 'Heading Style', 'wr-nitro' ),
							'section' => 'typo_page_title',
							'type'    => 'WR_Nitro_Customize_Control_Typography',
							'choices' => array(
								'italic', 'underline', 'uppercase',
							),
							'required' => array( 'wr_page_title = 1' ),
						),
						'wr_page_title_heading_font_size' => array(
							'label'   => esc_html__( 'Heading Text Size', 'wr-nitro' ),
							'section' => 'typo_page_title',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 10,
								'max'  => 100,
								'step' => 1,
								'unit' => 'px',
							),
							'required' => array( 'wr_page_title = 1' ),
						),
						'wr_page_title_heading_line_height' => array(
							'label'   => esc_html__( 'Heading Line Height', 'wr-nitro' ),
							'section' => 'typo_page_title',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 10,
								'max'  => 100,
								'step' => 1,
								'unit' => 'px',
							),
							'required' => array( 'wr_page_title = 1' ),
						),
						'wr_page_title_heading_letter_spacing' => array(
							'label'   => esc_html__( 'Heading Letter Spacing', 'wr-nitro' ),
							'section' => 'typo_page_title',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 0,
								'max'  => 10,
								'step' => 1,
								'unit' => 'px',
							),
							'required' => array( 'wr_page_title = 1' ),
						),
						'typo_page_title_custom_color' => array(
							'section' => 'typo_page_title',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<div class="btn-move-section"><a href="#" class="move-to-section button" data-section="color_pages">' . esc_html__( 'Edit Color', 'wr-nitro' ) . '</a><a href="#" class="move-to-section button" data-section="page_title">' . esc_html__( 'Edit Layout', 'wr-nitro' ) . '</a></div>',
							),
						),
					),
				),
				'typo_button' => array(
					'title'    => esc_html__( 'Button', 'wr-nitro' ),
					'settings' => array(
						'btn_font' => array(
							'default' => array(
								'italic'     => 0,
								'underline'  => 0,
								'uppercase'  => 1,
							),
							'sanitize_callback' => '',
							'transport'         => 'postMessage',
						),
						'btn_font_size' => array(
							'default'           => 13,
							'sanitize_callback' => '',
							'transport'         => 'postMessage',
						),
						'btn_line_height' => array(
							'default'           => 45,
							'sanitize_callback' => '',
							'transport'         => 'postMessage',
						),
						'btn_letter_spacing' => array(
							'default'           => 0,
							'sanitize_callback' => '',
							'transport'         => 'postMessage',
						),
						'typo_button_custom_color' => array(
							'default'           => 1,
							'sanitize_callback' => '',
						),
					),
					'controls' => array(
						'btn_font' => array(
							'section' => 'typo_button',
							'type'    => 'WR_Nitro_Customize_Control_Typography',
							'choices' => array(
								'italic', 'underline', 'uppercase',
							),
						),
						'btn_font_size' => array(
							'label'   => esc_html__( 'Text Size', 'wr-nitro' ),
							'section' => 'typo_button',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 10,
								'max'  => 24,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'btn_line_height' => array(
							'label'   => esc_html__( 'Line Height', 'wr-nitro' ),
							'section' => 'typo_button',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 30,
								'max'  => 100,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'btn_letter_spacing' => array(
							'label'   => esc_html__( 'Letter Spacing', 'wr-nitro' ),
							'section' => 'typo_button',
							'type'    => 'WR_Nitro_Customize_Control_Slider',
							'choices' => array(
								'min'  => 0,
								'max'  => 10,
								'step' => 1,
								'unit' => 'px',
							),
						),
						'typo_button_custom_color' => array(
							'section' => 'typo_button',
							'type'    => 'WR_Nitro_Customize_Control_HTML',
							'choices' => array(
								'1'  => '<div class="btn-move-section"><a href="#" class="move-to-section button" data-section="color_button">' . esc_html__( 'Edit Color', 'wr-nitro' ) . '</a><a href="#" class="move-to-section button" data-section="layout_button">' . esc_html__( 'Edit Layout', 'wr-nitro' ) . '</a></div>',
							),
						),
					),
				),
				'typo_quotes' => array(
					'title'    => esc_html__( 'Quotes', 'wr-nitro' ),
					'settings' => array(
						'quotes_font' => array(
							'default' => array(
								'family'     => 'Lato',
								'italic'     => 0,
								'underline'  => 0,
								'uppercase'  => 0,
								'fontWeight' => 400,
							),
							'sanitize_callback' => '',
							'transport'         => 'postMessage',
						),
					),
					'controls' => array(
						'quotes_font' => array(
							'section'     => 'typo_quotes',
							'type'        => 'WR_Nitro_Customize_Control_Typography',
							'choices'     => array(
								'family', 'fontWeight', 'italic', 'underline', 'uppercase',
							),
						),
					),
				),
			)
		);
	}
}
