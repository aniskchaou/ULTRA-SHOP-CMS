<?php
/**
 * Add to wishlist button template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.8
 */

global $product;

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

$wr_btn_class = $wr_tooltip_arrow = '';

// Get product item style
$wr_item_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['style'] : $wr_nitro_options['wc_archive_item_layout'];

if ( '2' == $wr_item_style ) {
	$wr_btn_class .= ' bts-40 btb';
}
if ( '5' == $wr_item_style ) {
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

<a href="<?php echo esc_url( add_query_arg( 'add_to_wishlist', $product_id ) )?>" rel="nofollow" data-product-id="<?php echo esc_attr( $product_id ) ?>" data-product-type="<?php echo esc_attr( $product_type ); ?>" class="<?php echo esc_attr( $link_classes . $wr_btn_class ); ?> pr db">
   <i class="nitro-icon-<?php echo esc_attr( $wr_icons ); ?>-wishlist"></i>
    <span class="tooltip <?php echo esc_attr( $wr_tooltip_arrow ) ?>"><?php echo esc_html( $label ) ?></span>
</a>
<span class="ajax-loading" style="visibility:hidden"><i class="fa fa-spinner"></i></span>
