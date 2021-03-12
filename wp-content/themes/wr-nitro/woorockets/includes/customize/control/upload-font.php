<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 * Custom functions for WooCommerce.
 */

/**
 * Typography custom control for WordPress Theme Customize.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Customize_Control_Upload_Font extends WP_Customize_Control {
	public $type = 'upload-font';

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @return  void
	 */
	public function enqueue() {
		static $enqueued;

		if ( ! isset( $enqueued ) ) {
			// Enqueue media.
			wp_enqueue_media();

			// Enqueue custom control script.
			wp_enqueue_script( 'wr-nitro-customize-upload-font', get_template_directory_uri() . '/assets/woorockets/js/admin/customize/control/upload-font.js', array(), '1.0.0', true );

			wp_localize_script( 'wr-nitro-customize-upload-font', 'wr_nitro_customize_upload_font', array(
				'type'              => $this->type,
				'select_font_label' => esc_html__( 'Select Font', 'wr-nitro' ),
				'change_font_label' => esc_html__( 'Change Font', 'wr-nitro' ),
			) );

			$enqueued = true;
		}
	}

	/**
	 * Render the control's content.
	 *
	 * @return  void
	 */
	public function render_content() {
		if ( $this->label ) {
			?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php
		}

		if ( $this->description ) {
			?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php
		}

		$value = $this->value();
		?>
		<div class="customize-control-content" id="wr-<?php echo esc_attr( $this->type ); ?>-<?php echo esc_attr( $this->id ); ?>">
			<div class="preview-font" style="margin:0 0 1em 0;border:1px dashed lightgray;padding:.75em 1em;background:#fff;"><?php
				esc_html_e( 'The quick brown fox jumps over the lazy dog', 'wr-nitro' );
			?></div>
			<div class="flex wr-custom-fonts">
				<button type="button" class="button upload-font"></button>
				<button type="button" class="button remove-font"><?php esc_html_e( 'Remove Font', 'wr-nitro' ); ?></button>
			</div>
		<?php
	}
}
