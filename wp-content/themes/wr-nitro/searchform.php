<?php
/**
 * @version    1.0
 * @package    Nitro
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 * Render built-in HTML5 search form.
 */
?>
<form role="search" method="get" class="widget-search" action="<?php echo esc_url( home_url( '/' ) ); ?>" <?php WR_Nitro_Helper::schema_metadata( array( 'context' => 'search_form' ) ); ?>>
	<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search ...', 'wr-nitro' ); ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php esc_attr_e( 'Search for', 'wr-nitro' ); ?>" />
	<button type="submit" class="search-submit"><i class="fa fa-search"></i></button>
</form>
