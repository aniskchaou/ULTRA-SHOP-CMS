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
 * Slider custom control for WordPress Theme Customize.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Customize_Control_Slider extends WP_Customize_Control {
	public $type = 'slider';

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @return  void
	 */
	public function enqueue() {
		static $enqueued;

		if ( ! isset( $enqueued ) ) {
			// Load jQuery UI
			wp_enqueue_style( 'jquery-ui-slider' );
			wp_enqueue_script( 'jquery-ui-slider' );

			wp_enqueue_script( 'wr-nitro-customize-slider', get_template_directory_uri() . '/assets/woorockets/js/admin/customize/control/slider.js', array( 'jquery-ui-slider' ), '1.0.0', true );

			wp_localize_script( 'wr-nitro-customize-slider', 'wr_nitro_customize_slider', array(
				'type' => $this->type,
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
		<div class="customize-control-content wr-slider" id="wr-<?php echo esc_attr( $this->type ); ?>-<?php echo esc_attr( $this->id ); ?>">
			<input type="range" value="<?php echo esc_attr( $value ); ?>" default-value="<?php
				echo esc_js( $this->settings['default']->default );
			?>" data-highlight="true">
			<a href="javascript:void(0)" class="reset-to-default">
				<span class="fa fa-refresh"></span>
			</a>
		</div>
		<?php echo '<scr' . 'ipt type="text/javascript">'; ?>
			new jQuery.WR_Slider_Control( {
				id: '<?php echo esc_js( $this->id ); ?>',
				transport: '<?php echo esc_js( $this->settings['default']->transport ); ?>',
				choices: <?php echo json_encode( count( $this->choices ) ? $this->choices : new stdClass ); ?>,
			} );
		<?php
		echo '</scr' . 'ipt>';
	}
}
