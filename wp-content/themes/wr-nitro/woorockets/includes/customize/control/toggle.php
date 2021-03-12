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
 * Toggle custom control for WordPress Theme Customize.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Customize_Control_Toggle extends WP_Customize_Control {
	public $type = 'toggle';

	/**
	 * Render the control's content.
	 *
	 * @return  void
	 */
	public function render_content() {
		// Generate name for this custom control.
		$name = '_customize-toggle-' . $this->id;

		if ( ! empty( $this->label ) ) :
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php
		endif;

		if ( ! empty( $this->description ) ) :
		?>
		<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>
		<div class="wr-toggle-control switch" id="<?php echo esc_attr( $name ); ?>">
			<input type="checkbox" class="wr-toggle" id="<?php echo esc_attr( $this->id ); ?>-checkbox" value="1" <?php
				$this->link();
				checked( $this->value(), 1 );
			?>>
			<label for="<?php echo esc_attr( $this->id ); ?>-checkbox"></label>
		</div>
		<?php
	}
}
