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
 * Typography field for RW Meta Box plugin.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class RWMB_Typography_Field extends RWMB_Field {
	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts()
	{
		// Enqueue Spectrum library for picking color.
		wp_enqueue_style( 'spectrum-color-picker', get_template_directory_uri() . '/assets/3rd-party/spectrum/spectrum.css' );
		wp_enqueue_script( 'spectrum-color-picker', get_template_directory_uri() . '/assets/3rd-party/spectrum/spectrum.js' );

		// Enqueue font awesome
		wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/3rd-party/font-awesome/css/font-awesome.min.css' );

		// Enqueue custom field script.
		wp_enqueue_script( 'wr-nitro-metabox-field-typography', get_template_directory_uri() . '/assets/woorockets/js/admin/meta-box/fields/typography.js' );
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function html( $meta, $field )
	{
		// Prepare meta value
		$meta = wp_parse_args(
			$meta,
			array(
				'italic'    => 0,
				'underline' => 0,
				'uppercase' => 0,
				'color'     => '',
			)
		);

		// Print HTML
		ob_start();
		?>
		<div id="<?php echo esc_attr( $field['id'] ); ?>">
			<div class="wr-field-typography-group">
				<label class="font-italic <?php if ( ( int ) $meta['italic'] ) echo 'active'; ?>">
					<input name="<?php echo esc_attr( $field['id'] ); ?>[italic]" value="1" type="checkbox" class="hidden" <?php
						checked( ( int ) $meta['italic'], 1 );
					?>>
					<i class="fa fa-italic"></i>
				</label>

				<label class="font-underline <?php if ( ( int ) $meta['underline'] ) echo 'active'; ?>">
					<input name="<?php echo esc_attr( $field['id'] ); ?>[underline]" value="1" type="checkbox" class="hidden" <?php
						checked( ( int ) $meta['underline'], 1 );
					?>>
					<i class="fa fa-underline"></i>
				</label>

				<label class="font-uppercase <?php if ( ( int ) $meta['uppercase'] ) echo 'active'; ?>">
					<input name="<?php echo esc_attr( $field['id'] ); ?>[uppercase]" value="1" type="checkbox" class="hidden" <?php
						checked( ( int ) $meta['uppercase'], 1 );
					?>>
					<i class="fa fa-text-height"></i>
				</label>

				<?php if ( isset( $field['std']['color'] ) ) { ?>
					<label class="font-color wr-colors-control">
						<input type="text" class="color-picker" name="<?php echo esc_attr( $field['id'] ); ?>[color]" value="<?php
							echo esc_attr( $meta['color'] );
						?>" default-value="<?php
							echo isset( $field['std']['color'] ) ? esc_attr( $field['std']['color'] ) : NULL;
						?>">
						<span class="font-color"><?php echo esc_html( $meta['color'] ); ?></span>
					</label>
				<?php } ?>
			</div>
		</div>
		<?php echo '<scr' . 'ipt>'; ?>
			(function($) {
				$(window).load(function() {
					new $.WR_Typography_Field(
						<?php echo json_encode( $field ); ?>,
						{
							'cancel': '<?php esc_html_e( 'Cancel', 'wr-nitro' ); ?>',
							'choose': '<?php esc_html_e( 'Choose', 'wr-nitro' ); ?>',
							'default': '<?php esc_html_e( 'Default', 'wr-nitro' ); ?>',
						}
					);
				});
			})(jQuery);
		<?php echo '</scr' . 'ipt>'; ?>
		<?php
		return ob_get_clean();
	}
}
