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
class WR_Nitro_Customize_Options_System {
	public static function get() {
		return array(
			'title'       => esc_html__( 'System', 'wr-nitro' ),
			'description' => '<a target="_blank" rel="noopener noreferrer" href="http://nitro.woorockets.com/docs/document/system"><span class="fa fa-question-circle has-tip" title="View Documentation for this section"></span></a>',
			'type'        => 'option',
			'priority' => 90,
			'settings' => array(
				'custom_css' => array(
					'sanitize_callback' => '',
				),
				'custom_js' => array(
					'sanitize_callback' => '',
				),
				'rtl' => array(
					'sanitize_callback' => '',
					'transport'         => 'postMessage',
				),
				'under_construction' => array(
					'sanitize_callback' => '',
				),
				'under_construction_style' => array(
					'default'           => 1,
					'transport'         => 'postMessage',
					'sanitize_callback' => '',
				),
				'under_construction_bg_color' => array(
					'default'           => '#f7f8fa',
					'transport'         => 'postMessage',
					'sanitize_callback' => '',
				),
				'under_construction_bg_image' => array(
					'default'           => get_template_directory_uri() . '/assets/woorockets/images/bg-construction.png',
					'sanitize_callback' => '',
				),
				'under_construction_bg_image_size' => array(
					'default'           => 'auto',
					'sanitize_callback' => '',
				),
				'under_construction_bg_image_repeat' => array(
					'default'           => 'no-repeat',
					'sanitize_callback' => '',
				),
				'under_construction_bg_image_position' => array(
					'default'           => 'right bottom',
					'sanitize_callback' => '',
				),
				'under_construction_bg_image_attachment' => array(
					'default'           => 'scroll',
					'sanitize_callback' => '',
				),
				'under_construction_title' => array(
					'sanitize_callback' => '',
					'transport'         => 'postMessage',
					'default'           => sprintf( __( '<h2>%1$s</h2> is coming soon', 'wr-nitro' ), 'Our website' ),
				),
				'under_construction_message' => array(
					'sanitize_callback' => '',
					'transport'         => 'postMessage',
					'default'           => esc_html__( 'Our website is offline now, but we will be back soon.', 'wr-nitro' ),
				),
				'under_construction_timer' => array(
					'sanitize_callback' => '',
					'default'           => '',
				),
				'compress_js' => array(
					'default'           => 0,
					'sanitize_callback' => 'sanitize_key',
				),
				'compress_css' => array(
					'default'           => 0,
					'sanitize_callback' => 'sanitize_key',
				),
				'max_compression_size' => array(
					'default'           => 200,
					'sanitize_callback' => array( __CLASS__, 'sanitize_max_compression_size' ),
				),
				'expert_mode' => array(
					'default'           => 0,
					'sanitize_callback' => 'sanitize_key',
				),
				'backup_restore' => array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_key',
				),
			),
			'controls' => array(
				'custom_css' => array(
					'label'           => esc_html__( 'Custom CSS', 'wr-nitro' ),
					'description'     => esc_html__( 'Paste your CSS code here. Do not place any &lt;style&gt; tags in these areas as they are already added for your convenience', 'wr-nitro' ),
					'section'         => 'system',
					'type'            => 'WR_Nitro_Customize_Control_Editor',
					'mode'            => 'css',
					'placeholder'     => esc_html__( "/**\n * Write your custom CSS code here.\n */", 'wr-nitro' ),
					'confirm_message' => esc_html__( 'The custom CSS code has been changed. Are you sure you want to cancel?', 'wr-nitro' ),
				),
				'custom_js' => array(
					'label'           => esc_html__( 'Custom JS', 'wr-nitro' ),
					'description'     => esc_html__( 'Paste your JS code here. Do not place any &lt;script&gt; tags in these areas as they are already added for your convenience', 'wr-nitro' ),
					'section'         => 'system',
					'type'            => 'WR_Nitro_Customize_Control_Editor',
					'mode'            => 'javascript',
					'placeholder'     => esc_html__( "/**\n * Write your custom Javascript code here.\n */", 'wr-nitro' ),
					'confirm_message' => esc_html__( 'The custom JS code has been changed. Are you sure you want to cancel?', 'wr-nitro' ),
				),
				'rtl' => array(
					'label'   => esc_html__( 'Enable Right To Left', 'wr-nitro' ),
					'section' => 'system',
					'type'    => 'WR_Nitro_Customize_Control_Toggle',
				),
				'under_construction' => array(
					'label'   => esc_html__( 'Maintenance Mode', 'wr-nitro' ),
					'section' => 'system',
					'type'    => 'WR_Nitro_Customize_Control_Toggle',
				),
				'under_construction_style' => array(
					'label'   => esc_html__( 'Choose Style', 'wr-nitro' ),
					'section' => 'system',
					'type'    => 'select',
					'choices' => array(
						'1' => esc_html__( 'Style 1', 'wr-nitro' ),
						'2' => esc_html__( 'Style 2', 'wr-nitro' ),
					),
					'required' => array(
						'under_construction = 1',
					),
				),
				'under_construction_bg_color' => array(
					'label'    => esc_html__( 'Background Color', 'wr-nitro' ),
					'section'  => 'system',
					'type'     => 'WR_Nitro_Customize_Control_Colors',
					'required' => array(
						'under_construction = 1',
					),
				),
				'under_construction_bg_image' => array(
					'label'    => esc_html__( 'Background Image', 'wr-nitro' ),
					'section'  => 'system',
					'type'     => 'WP_Customize_Image_Control',
					'required' => array(
						'under_construction = 1',
					),
				),
				'under_construction_bg_image_size' => array(
					'label'   => esc_html__( 'Background Size', 'wr-nitro' ),
					'section' => 'system',
					'type'    => 'select',
					'choices' => array(
						'auto'    => esc_html__( 'Auto', 'wr-nitro' ),
						'cover'   => esc_html__( 'Cover', 'wr-nitro' ),
						'contain' => esc_html__( 'Contain', 'wr-nitro' ),
					),
					'required' => array(
						'under_construction = 1',
						'under_construction_bg_image != ""',
					),
				),
				'under_construction_bg_image_repeat' => array(
					'label'   => esc_html__( 'Background Repeat', 'wr-nitro' ),
					'section' => 'system',
					'type'    => 'select',
					'choices' => array(
						'no-repeat' => esc_html__( 'No Repeat', 'wr-nitro' ),
						'repeat'    => esc_html__( 'Repeat', 'wr-nitro' ),
						'repeat-x'  => esc_html__( 'Repeat X', 'wr-nitro' ),
						'repeat-y'  => esc_html__( 'Repeat Y', 'wr-nitro' ),
					),
					'required' => array(
						'under_construction = 1',
						'under_construction_bg_image != ""',
					),
				),
				'under_construction_bg_image_position' => array(
					'label'   => esc_html__( 'Background Position', 'wr-nitro' ),
					'section' => 'system',
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
					'required' => array(
						'under_construction = 1',
						'under_construction_bg_image != ""',
					),
				),
				'under_construction_bg_image_attachment' => array(
					'label'    => esc_html__( 'Background Attachment', 'wr-nitro' ),
					'section'  => 'system',
					'type'     => 'select',
					'choices'  => array(
						'scroll' => esc_html__( 'Scroll', 'wr-nitro' ),
						'fixed'  => esc_html__( 'Fixed', 'wr-nitro' ),
					),
					'required' => array(
						'under_construction = 1',
						'under_construction_bg_image != ""',
					),
				),
				'under_construction_title' => array(
					'label'    => esc_html__( 'Page Heading', 'wr-nitro' ),
					'section'  => 'system',
					'type'     => 'text',
					'required' => array( 'under_construction = 1' ),
				),
				'under_construction_message' => array(
					'label'       => esc_html__( 'Message', 'wr-nitro' ),
					'description' => esc_html__( 'Your away message. You may use these HTML tags and attributes to produce your own maintenance mode page', 'wr-nitro' ),
					'section'     => 'system',
					'type'        => 'textarea',
					'required'    => array( 'under_construction = 1' ),
				),
				'under_construction_timer' => array(
					'label'       => esc_html__( 'Countdown timer (Format: M D, Y)', 'wr-nitro' ),
					'description' => esc_html__( 'Set countdown timer for website launch', 'wr-nitro' ),
					'section'     => 'system',
					'type'        => 'date',
					'required'    => array( 'under_construction = 1' ),
				),
				'compress_js' => array(
					'label'   => esc_html__( 'Compress JS', 'wr-nitro' ),
					'section' => 'system',
					'type'    => 'WR_Nitro_Customize_Control_Toggle',
				),
				'compress_css' => array(
					'label'   => esc_html__( 'Compress CSS', 'wr-nitro' ),
					'section' => 'system',
					'type'    => 'WR_Nitro_Customize_Control_Toggle',
				),
				'max_compression_size' => array(
					'label'       => esc_html__( 'Max compression size', 'wr-nitro' ),
					'description' => esc_html__( 'Split compression file if file size is greater than the max compression size defined here.', 'wr-nitro' ),
					'section'     => 'system',
					'type'        => 'WR_Nitro_Customize_Control_Slider',
					'choices'     => array(
						'min'  => 50,
						'max'  => 1000,
						'step' => 50,
						'unit' => ' KB',
					),
					'required' => array(
						'compress_js = 1',
						'compress_css = 1',
						'logical_operator' => 'OR',
					),
				),
				'expert_mode' => array(
					'label'       => esc_html__( 'Enable Expert Mode', 'wr-nitro' ),
					'description' => esc_html__( 'With `Expert Mode` turned off, all parameters that don`t affect current page will be disabled. If you want to edit those parameters anyway, turn on the `Expert Mode`.', 'wr-nitro' ),
					'section'     => 'system',
					'type'        => 'WR_Nitro_Customize_Control_Toggle',
				),
				'backup_restore' => array(
					'label'       => '',
					'description' => '',
					'section'     => 'system',
					'type'        => 'WR_Nitro_Customize_Control_Backup_Restore',
				),
			),
		);
	}

	/**
	 * Sanitize canvas sidebar widgets.
	 *
	 * @param   array  $value  Canvas sidebar widgets data.
	 *
	 * @return  array
	 */
	public static function sanitize_max_compression_size( $value ) {
		// Sanitize new value.
		$value = absint( $value );

		// Get current value.
		$current = get_theme_mod( 'max_compression_size' );

		// Clear all compression files if value is changed.
		if ( $value != $current && ( $path = WR_Nitro_Assets::get_location() ) ) {
			global $wp_filesystem;

			$wp_filesystem->rmdir( $path, true );
		}

		return $value;
	}
}
