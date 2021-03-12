<?php
/**
 * @version    1.0
 * @package    WR_Live_Search
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

?>

<div class="wrap">
	<h2><?php _e( 'WR Live Search Settings' ); ?></h2>

	<?php
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
		echo "<div class='error'><p>" . __( 'This plugin requires the following plugin: <strong>WooCommerce</strong>', 'wr-live-search' ) . '</p></div>';
	?>

	<p class="update-nag"><?php _e( 'Using Shortcode <code id="shortcode-render">[wr_live_search]</code><br>or<br>PHP function <code id="function-render">wr_live_search();</code><br>to display live search form.', 'wr-ls' ); ?></p>

	<form method="POST">
		<?php
		settings_fields( WR_LS );
		do_settings_sections( WR_LS );
		submit_button();
		?>
	</form>
</div>
