<?php
/**
 * @version    1.0
 * @package    Nitro_Toolkit
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

if ( class_exists( 'Vc_Manager' ) ) :

/**
 * Register the new toggle button parameter type with Visual Composer.
 *
 * @param   array  $settings  Current settings.
 * @param   mixed  $value     Current value.
 *
 * @return  string
 */
function nitro_toolkit_create_param_toggle_button( $settings, $value ) {
	$dependency = '';
	$id   = uniqid();
	$name = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
	$type = isset( $settings['type'] ) ? $settings['type'] : '';

	$html = '
		<div class="wr-toggle">
			<input type="hidden" class="wpb_vc_param_value ' . esc_attr( $name ) . ' ' . esc_attr( $type ) . ' ' . esc_attr( $dependency ) . '" value="' . esc_attr( $value ) . '" ' . $dependency . ' name="' . esc_attr( $name ) . '">
			<button id="button-toggle-' . esc_attr( $id ) . '" type="toggle" data-on="' . esc_attr( $value ) . '"></button>
		</div>
		<script>
			(function($) {
				$("body").on("click", "#button-toggle-' . esc_attr( $id ) . '", function(e) {
					e.preventDefault();
					$(this)
						.attr("data-on", $(this).attr("data-on") == "true" ? false : true)
						.trigger("change");
					$(this)
						.siblings("input")
						.val( $(this).attr("data-on") );
				});
			})(jQuery);
		</script>';

	return $html;
}
vc_add_shortcode_param( 'toggle', 'nitro_toolkit_create_param_toggle_button' );

/**
 * Register the new range parameter type with Visual Composer.
 *
 * @param   array  $settings  Current settings.
 * @param   mixed  $value     Current value.
 *
 * @return  string
 */
function nitro_toolkit_create_param_range( $settings, $value ) {
	$id   = uniqid();
	$name = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
	$type = isset( $settings['type'] ) ? $settings['type'] : '';
	$min  = isset( $settings['min'] ) ? $settings['min'] : '';
	$max  = isset( $settings['max'] ) ? $settings['max'] : '';
	$step = isset( $settings['step'] ) ? $settings['step'] : '';
	$unit = isset( $settings['unit'] ) ? $settings['unit'] : '';

	$attr_unit = '';
	if ( ! empty( $unit ) ) {
		$attr_unit = '<span>' . esc_attr( $unit ) . '</span>';
	}

	$html = '
		<div class="vc-ui-slider wr-slider">
			<div id="input-range-' . esc_attr( $id ) . '" data-value="' . esc_attr( $value ) . '" data-min="' . esc_attr( $min ) . '" data-max="' . esc_attr( $max ) . '" data-step="' . esc_attr( $step ) . '">
				<div class="input-wrap">
					<input name="' . esc_attr( $name ) . '"  class="input-range-number wpb_vc_param_value ' . esc_attr( $name ) . '" type="text" value="' . esc_attr( $value ) . '" disabled />
					' . $attr_unit . '
				</div>
			</div>
		</div>';

	$script = '
		<script>
			(function($) {
				var range = $( "#input-range-' . $id . '" );

				range.slider({
					value: parseFloat( range.attr( "data-value" ) ),
					min: parseFloat( range.attr( "data-min" ) ),
					max: parseFloat( range.attr( "data-max" ) ),
					step: parseFloat( range.attr( "data-step" ) ),
					slide: _.debounce(function( event, ui ) {
						range.parent().find( ".input-range-number" ).val( ui.value );
					})
				});
			})(jQuery);
		</script>';

	return $html . $script;
}
vc_add_shortcode_param( 'range', 'nitro_toolkit_create_param_range' );

/**
 * Register the new font parameter type with Visual Composer.
 *
 * @param   array  $settings  Current settings.
 * @param   mixed  $value     Current value.
 *
 * @return  string
 */
function nitro_toolkit_create_param_fonts( $settings, $value ) {
	$id   = wp_generate_password( 8, false );
	$name = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
	$type = isset( $settings['type'] ) ? $settings['type'] : '';

	// Get the list of Google Fonts
	$fonts = WR_Core_Helpers::google_fonts();

	$html = '<select class="wpb_vc_param_value ' . esc_attr( $name ) . '" data-option="' . $value . '" name="' . $name . '">';

	foreach ( $fonts as $font ) {
		$selected = '';

		if ( $value == $font ) {
			$selected = ' selected="selected"';
		}

		$html .= '<option value="' . $font . '"' . $selected . '>' . $font . '</option>';
	}

	$html .= '</select>';

	$script = '
		<script>
			(function($) {
				$("body").on("click", "#button-toggle-' . $id . '", function(e) {
					e.preventDefault();
					$(this)
						.attr("data-on", $(this).attr("data-on") == "true" ? false : true)
						.trigger("change");
					$(this)
						.siblings("input")
						.val( $(this).attr("data-on") );
				});
			})(jQuery);
		</script>';

	return $html . $script;
}
vc_add_shortcode_param( 'fonts', 'nitro_toolkit_create_param_fonts' );

endif;
