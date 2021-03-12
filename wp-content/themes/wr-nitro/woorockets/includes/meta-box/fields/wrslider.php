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
 * Slider field for RW Meta Box plugin.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class RWMB_WRSlider_Field extends RWMB_Field {
	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts()
	{
		// Enqueue custom field script.
		wp_enqueue_script( 'wr-nitro-metabox-field-wrslider', get_template_directory_uri() . '/assets/woorockets/js/admin/meta-box/fields/wrslider.js', array( 'jquery-ui-slider' ) );
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
		$meta = $meta == '' ? $field['std'] : $meta;

		// Add param value data
		$field['value_data'] = esc_attr( $meta );

		// Print HTML
		ob_start();
		?>
		<div id="<?php echo esc_attr( $field['id'] ); ?>-container" class="wr-slider">
			<input type="text" data-highlight="true" id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php
				echo esc_attr( $field['id'] );
			?>" value="<?php
				echo esc_attr( $meta );
			?>">
			<a href="javascript:void(0)" class="reset-to-default">
				<span class="dashicons dashicons-update"></span>
			</a>
		</div>
		<?php echo '<scr' . 'ipt>'; ?>
			(function($) {
				$(window).load(function() {
					new $.WR_Slider_Field( <?php echo json_encode( $field ); ?> );
				});
			})(jQuery);
		<?php echo '</scr' . 'ipt>'; ?>
		<?php
		return ob_get_clean();
	}
}
