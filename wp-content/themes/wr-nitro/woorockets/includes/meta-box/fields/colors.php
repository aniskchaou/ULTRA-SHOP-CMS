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
 * Colors field for RW Meta Box plugin.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class RWMB_Colors_Field extends RWMB_Field {
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

		// Enqueue custom field script.
		wp_enqueue_script( 'wr-nitro-metabox-field-colors', get_template_directory_uri() . '/assets/woorockets/js/admin/meta-box/fields/colors.js' );
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
		if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
			$meta = wp_parse_args(
				$meta,
				array_combine( array_keys( $field['options'] ), array_map( 'is_null', array_keys( $field['options'] ) ) )
			);
		}

		// Prepare field options
		if ( ! isset( $field['options'] ) || ! is_array( $field['options'] ) ) {
			$field['options'] = array( '_' => '_' );
		}

		// Print HTML
		ob_start();
		?>
		<div id="<?php echo esc_attr( $field['id'] ); ?>">
			<?php foreach ( $field['options'] as $name => $label ) : ?>
			<div>
				<?php if ($label != '_') : ?>
				<label class="customize-control-title"><?php echo esc_html( $label ); ?></label>
				<?php endif; ?>
				<div class="wr-colors-control">
					<input class="color-picker" type="text" name="<?php
						if ( $name == '_' )
							echo esc_attr( $field['id'] );
						else
							echo esc_attr( $field['id'] ) . '[' . esc_attr( $name ) . ']';
					?>" value="<?php
						echo esc_attr( $name == '_' ? $meta : $meta[ $name ] );
					?>" default-value="<?php
						echo esc_attr( $name == '_' ? $field['std'] : $field['std'][ $name ] );
					?>">
					<span class="color-hex"><?php
						echo esc_attr( $name == '_' ? $meta : $meta[ $name ] );
					?></span>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php echo '<scr' . 'ipt>'; ?>
			(function($) {
				$(window).load(function() {
					new $.WR_Colors_Field(
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
