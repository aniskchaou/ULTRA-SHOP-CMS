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
 * Colors custom control for WordPress Theme Customize.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Customize_Control_Colors extends WP_Customize_Control {
	public $type = 'colors';

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @return  void
	 */
	public function enqueue() {
		static $enqueued;

		if ( ! isset( $enqueued ) ) {
			// Enqueue Spectrum library for picking color.
			wp_enqueue_style( 'spectrum-color-picker', get_template_directory_uri() . '/assets/3rd-party/spectrum/spectrum.css', array(), '1.7.1' );
			wp_enqueue_script( 'spectrum-color-picker', get_template_directory_uri() . '/assets/3rd-party/spectrum/spectrum.js', array(), '1.7.1', true );

			// Enqueue custom control script.
			wp_enqueue_script( 'wr-nitro-customize-colors', get_template_directory_uri() . '/assets/woorockets/js/admin/customize/control/colors.js', array( 'spectrum-color-picker' ), '1.0.0', true );

			wp_localize_script( 'wr-nitro-customize-colors', 'wr_nitro_customize_colors', array(
				'type'    => $this->type,
				'cancel'  => esc_html__( 'Cancel', 'wr-nitro' ),
				'choose'  => esc_html__( 'Choose', 'wr-nitro' ),
				'default' => esc_html__( 'Default', 'wr-nitro' ),
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
		if ( ! count( $this->choices ) ) {
			$this->choices = array( '_' => '_' );
			$_class = 'color-single';
		} else {
			$_class = 'color-group';
		}

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
		<div class="customize-control-content <?php echo esc_attr( $_class ); ?>" id="wr-<?php echo esc_attr( $this->type ); ?>-<?php echo esc_attr( $this->id ); ?>">
			<?php foreach ( $this->choices as $name => $label ) { ?>
			<div>
				<?php if ( $label != '_' ) { ?>
				<label class="customize-control-title"><?php echo esc_html( $label, 'wr-nitro' ); ?></label>
				<?php } ?>
				<div class="wr-colors-control">
					<?php
					$v = ( $label == '_' ) ? $value : $value[ $name ];
					$default = ( $label == '_' ) ? $this->settings['default']->default : $this->settings['default']->default[ $name ];
					?>
					<input type="text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $v ); ?>" default-value="<?php echo esc_attr( $default ); ?>">
					<span class="color-hex"><?php echo esc_html( $v ); ?></span>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php
	}
}
