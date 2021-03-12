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
class WR_Nitro_Customize_Control_Typography extends WP_Customize_Control {
	public $type = 'typography';

	public $choices = array(
		'family', 'bold', 'italic', 'underline', 'uppercase', 'color',
	);

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @return  void
	 */
	public function enqueue() {
		static $enqueued;

		if ( ! isset( $enqueued ) ) {

			// Enqueue custom control script.
			wp_enqueue_script( 'wr-nitro-customize-typography', get_template_directory_uri() . '/assets/woorockets/js/admin/customize/control/typography.js', array( 'spectrum-color-picker' ), '1.0.0', true );

			wp_localize_script( 'wr-nitro-customize-typography', 'wr_nitro_customize_typography', array(
				'type'                => $this->type,
				'google_fonts'        => WR_Nitro_Helper::google_fonts(),
				'default_font_weight' => 400,
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

		// Prepare default value.
		$default = wp_parse_args( $this->settings['default']->default, array(
			'family'     => '',
			'bold'       => '',
			'italic'     => '',
			'underline'  => '',
			'uppercase'  => '',
			'fontWeight' => '',
		) );

		// Prepare current value.
		$value = wp_parse_args( $this->value(), array(
			'family'     => '',
			'bold'       => '',
			'italic'     => '',
			'underline'  => '',
			'uppercase'  => '',
			'fontWeight' => '',
		) );
		?>
		<div class="customize-control-content" id="wr-<?php echo esc_attr( $this->type ); ?>-<?php echo esc_attr( $this->id ); ?>">
			<?php if ( in_array( 'family', $this->choices ) ) { ?>
			<div class="customize-control-select wr-customize-font">
				<input class="data-family" type="hidden" name="family" value="<?php echo esc_attr( $value['family'] ); ?>">
				<a class="wr-image-selected <?php echo esc_attr( preg_replace( '/\s+/', '-', strtolower( $value['family'] ) ) ); ?>" href="#"><span><?php
					echo esc_html( $value['family'] );
				?></span></a>
				<div class="wr-select-image-container">
					<div class="search-font"><input type="text" class="txt-sfont"></div>
					<ul class="google-fonts-list"></ul>
				</div>
			</div>
			<?php } ?>

			<div class="customize-control-style">
				<div class="customize-control-typography-group">
					<?php if ( in_array( 'italic', $this->choices ) ) { ?>
					<label class="font-italic <?php if ( $value['italic'] ) echo ' active'; ?>">
						<input name="italic" type="checkbox" class="hidden" <?php
							if ( $value['italic'] ) echo ' checked="checked"';
						?>>
						<i class="fa fa-italic"></i>
					</label>
					<?php } ?>

					<?php if ( in_array( 'underline', $this->choices ) ) { ?>
					<label class="font-underline <?php if ( $value['underline'] ) echo ' active'; ?>">
						<input name="underline" type="checkbox" class="hidden" <?php
							if ( $value['underline'] ) echo ' checked="checked"';
						?>>
						<i class="fa fa-underline"></i>
					</label>
					<?php } ?>

					<?php if ( in_array( 'uppercase', $this->choices ) ) { ?>
					<label class="font-uppercase <?php if ( $value['uppercase'] ) echo ' active'; ?>">
						<input name="uppercase" type="checkbox" class="hidden" <?php
							if ( $value['uppercase'] ) echo ' checked="checked"';
						?>>
						<i class="fa fa-text-height"></i>
					</label>
					<?php } ?>
				</div>

				<div class="customize-control-font-weight">
					<select name="fontWeight">
						<option value="400"><?php esc_html_e( 'Normal', 'wr-nitro' ) ?></option>
						<option value="100">100</option>
						<option value="200">200</option>
						<option value="300">300</option>
						<option value="400">400</option>
						<option value="500">500</option>
						<option value="600">600</option>
						<option value="700">700</option>
						<option value="800">800</option>
						<option value="900">900</option>
					</select>
				</div>
			</div>
		</div>
		<?php
	}
}
