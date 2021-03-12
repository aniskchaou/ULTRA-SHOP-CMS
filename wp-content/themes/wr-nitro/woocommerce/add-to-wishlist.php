<?php
/**
 * Add to wishlist template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.0
 */

global $product;

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

$wr_btn_class = $wr_tooltip_arrow = '';

// Get product item style
$wr_item_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['style'] : $wr_nitro_options['wc_archive_item_layout'];
if ( '2' == $wr_item_style ) {
	$wr_btn_class .= ' bts-40 btb';
} elseif ( '5' == $wr_item_style ) {
	$wr_tooltip_arrow = 'ar';
} else {
	$wr_tooltip_arrow = 'ab';
}

// Style of list product
$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];

if ( 'list' == $wr_style && ! is_product() ) {
	$wr_btn_class .= ' bts-50 btb nitro-line';
} elseif ( is_product() ) {
	$wr_btn_class .= ' btb bts-50';
}

// Icon Set
$wr_icons = $wr_nitro_options['wc_icon_set'];
?>

<div class="yith-wcwl-add-to-wishlist add-to-wishlist-<?php echo esc_attr( $product_id ); ?> icon_color">
	<?php if( ! ( $disable_wishlist && ! is_user_logged_in() ) ): ?>

		<div class="yith-wcwl-add-button pr <?php echo esc_attr( ($exists && ! $available_multi_wishlist) ? 'hide': 'show' ) ?>" style="display:<?php echo esc_attr( ($exists && ! $available_multi_wishlist) ? 'none': 'block' ) ?>">
			<?php yith_wcwl_get_template( 'add-to-wishlist-' . $template_part . '.php', $atts ); ?>
		</div>

		<div class="yith-wcwl-remove-button pr yith-wcwl-wishlistaddedbrowse <?php echo esc_attr( ($exists && ! $available_multi_wishlist) ? 'show' : 'hide' ) ?>" style="display:<?php echo esc_attr( ($exists && ! $available_multi_wishlist) ? 'block' : 'none' ) ?>">
			<a class="pr db <?php echo esc_attr( $wr_btn_class ); ?>" href="<?php echo esc_url( $wishlist_url ) ?>" rel="nofollow" data-product-id="<?php echo esc_attr( $product_id ) ?>">
				<i class="nitro-icon-<?php echo esc_attr( $wr_icons ); ?>-wishlist"></i>
				<span class="tooltip <?php echo esc_attr( $wr_tooltip_arrow ) ?>"><?php esc_attr_e( 'Remove from Wishlist', 'wr-nitro' ) ?></span>
			</a>
			<span class="ajax-loading" style="visibility:hidden"><i class="fa fa-spinner"></i></span>
		</div>

		<div class="yith-wcwl-wishlistaddresponse"></div>
	<?php else: ?>
		<a class="pr db <?php echo esc_attr( $wr_btn_class ); ?>" href="<?php echo esc_url( add_query_arg( array( 'wishlist_notice' => 'true', 'add_to_wishlist' => $product_id ), get_permalink( wc_get_page_id( 'myaccount' ) ) ) )?>" rel="nofollow" class="<?php echo str_replace( 'add_to_wishlist', '', $link_classes ) ?>" >
			<i class="nitro-icon-<?php echo esc_attr( $wr_icons ); ?>-wishlist"></i>
			<span class="tooltip ab"><?php echo esc_html( $label ); ?></span>
		</a>
	<?php endif; ?>
</div>
