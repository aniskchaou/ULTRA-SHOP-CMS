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

if ( function_exists( 'is_woocommerce' ) && is_woocommerce() || function_exists( 'is_account_page' ) && is_account_page() || function_exists( 'is_cart' ) && is_cart() || function_exists( 'is_checkout' ) && is_checkout() || function_exists( 'yith_wcwl_is_wishlist' ) &&yith_wcwl_is_wishlist_page()  ) return;

$wr_sidebar = isset( WR_Nitro_Render::$sidebar ) ? WR_Nitro_Render::$sidebar : 'primary-sidebar';

$wr_nitro_options = WR_Nitro::get_options();

$wr_style  = $wr_nitro_options['w_style'];

if ( is_home() && get_option( 'page_for_posts' ) || is_category() || is_tag() ) {
	$wr_sticky = $wr_nitro_options['blog_sidebar_sticky'];
} elseif ( is_page() || is_archive() ) {
	$wr_sticky = $wr_nitro_options['wr_page_layout_sidebar_sticky'];
} elseif ( is_singular( 'post' ) ) {
	$wr_sticky = $wr_nitro_options['blog_single_sidebar_sticky'];
} else {
	$wr_sticky = false;
}
$wr_sidebar_border = $wr_nitro_options['w_style_border'];
if ( $wr_sidebar_border && $wr_style != 4 ) {
	$wr_sidebar_border = 'widget-bordered';
}
?>

<div class="primary-sidebar <?php if ( $wr_sticky == true ) echo 'primary-sidebar-sticky'; ?> widget-style-<?php echo esc_attr( $wr_style ) . ' ' . ( is_customize_preview() ? 'customizable customize-section-widget_styles ' . $wr_sidebar_border : '' ); ?>" <?php WR_Nitro_Helper::schema_metadata( array( 'context' => 'sidebar' ) ); ?>>

	<?php if ( is_active_sidebar( $wr_sidebar ) ) : ?>

		<div class="mgt30 <?php if ( $wr_sticky == true ) echo 'primary-sidebar-inner'; ?>">

			<?php dynamic_sidebar( $wr_sidebar ); ?>

		</div><!-- .sidebar -->

	<?php endif; ?>

</div>
