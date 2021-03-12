<?php
/**
 * Add to wishlist popup template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
global $product;
$unique_id = mt_rand();

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

$link_classes = $wr_tooltip_arrow = '';

// Get product item style
$wr_item_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['style'] : $wr_nitro_options['wc_archive_item_layout'];
if ( '2' == $wr_item_style ) {
	$link_classes .= ' bts-40 btb';
} elseif ( '5' == $wr_item_style ) {
	$wr_tooltip_arrow = 'ar';
} else {
	$wr_tooltip_arrow = 'ab';
}

// Style of list product
$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];

if ( 'list' == $wr_style && ! is_product() ) {
	$link_classes .= ' bts-50 btb nitro-line';
} elseif ( is_product() ) {
	$link_classes .= ' btb bts-50';
}

// Icon Set
$wr_icons = $wr_nitro_options['wc_icon_set'];
?>
<!-- WISHLIST POPUP OPENER -->
<a href="#add_to_wishlist_popup_<?php echo esc_attr( $product_id ) ?>_<?php echo esc_attr( $unique_id ) ?>" rel="nofollow" class="<?php echo esc_attr( $link_classes ); echo esc_attr( $exists ? ' hide' : ' show' ); ?> open-pretty-photo yith-wcwl-add-button " data-rel="prettyPhoto[add_to_wishlist_<?php echo esc_attr( $product_id ) ?>_<?php echo esc_attr( $unique_id ) ?>]" style="display:<?php echo esc_attr( $exists ? 'none': 'block' ) ?>">
	<i class="nitro-icon-<?php echo esc_attr( $wr_icons ); ?>-wishlist"></i>
	<span class="tooltip <?php echo esc_attr( $wr_tooltip_arrow ) ?>"><?php echo esc_html( $label ) ?></span>
</a>

<!-- ALREADY IN A WISHLIST MESSAGE -->
<div class="yith-wcwl-remove-button pr yith-wcwl-wishlistaddedbrowse <?php echo esc_attr( $exists ? 'show' : 'hide' ) ?>" style="display:<?php echo esc_attr( $exists ? 'block' : 'none' ) ?>">
	<a class="pr db <?php echo esc_attr( $link_classes ); ?>" href="<?php echo esc_url( $wishlist_url ) ?>" rel="nofollow" data-product-id="<?php echo esc_attr( $product_id ) ?>">
		<i class="nitro-icon-<?php echo esc_attr( $wr_icons ); ?>-wishlist"></i>
		<span class="tooltip <?php echo esc_attr( $wr_tooltip_arrow ) ?>"><?php esc_attr_e( 'Remove from Wishlist', 'wr-nitro' ) ?></span>
	</a>
	<span class="ajax-loading" style="visibility:hidden"><i class="fa fa-spinner"></i></span>
</div>

<!-- WISHLIST POPUP -->
<div id="add_to_wishlist_popup_<?php echo esc_attr( $product_id ) ?>_<?php echo esc_attr( $unique_id ) ?>" class="yith-wcwl-popup mfp-hide">
    <form class="yith-wcwl-popup-form" method="post" action="<?php echo esc_url( add_query_arg( array( 'add_to_wishlist' => $product_id ) ) )?>">
        <div class="yith-wcwl-popup-content">

            <div class="yith-wcwl-first-row">
                <div class="yith-wcwl-wishlist-select-container">
                    <h3><?php echo esc_html( $popup_title ) ?></h3>
                    <select name="wishlist_id" class="wishlist-select">
                        <option value="0" <?php selected( true ) ?> ><?php echo apply_filters( 'yith_wcwl_default_wishlist_name', __( 'My Wishlist', 'wr-nitro' ) )?></option>
                        <?php if( ! empty( $lists ) ): ?>
                            <?php foreach( $lists as $list ):?>
                                <?php if( ! $list['is_default'] ): ?>
                                <option value="<?php echo esc_attr( $list['ID'] ) ?>"><?php echo esc_html( $list['wishlist_name'] ) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <option value="new"><?php echo apply_filters( 'yith_wcwl_create_new_list_text', __( 'Create a new list', 'wr-nitro' ) ) ?></option>
                    </select>
                </div>
            </div>

            <div class="yith-wcwl-second-row">
                <div class="yith-wcwl-popup-new">
                    <input name="wishlist_name" class="wishlist-name" type="text" class="wishlist-name" placeholder="<?php echo apply_filters( 'yith_wcwl_new_list_title_text', __( 'Wishlist name', 'wr-nitro' ) ) ?>" />
                </div>
                <div class="yith-wcwl-visibility">
                    <select name="wishlist_visibility" class="wishlist-visibility">
                        <option value="0" class="public-visibility"><?php echo apply_filters( 'yith_wcwl_public_wishlist_visibility', __( 'Public', 'wr-nitro' ) )?></option>
                        <option value="1" class="shared-visibility"><?php echo apply_filters( 'yith_wcwl_shared_wishlist_visibility', __( 'Shared', 'wr-nitro' ) )?></option>
                        <option value="2" class="private-visibility"><?php echo apply_filters( 'yith_wcwl_private_wishlist_visibility', __( 'Private', 'wr-nitro' ) )?></option>
                    </select>
                </div>
            </div>
        </div>

        <div class="yith-wcwl-popup-footer">
            <div class="yith-wcwl-popup-button mgt20">
                <a rel="nofollow" class="wishlist-submit button <?php echo esc_attr( $link_popup_classes ) ?>" data-product-id="<?php echo esc_attr( $product_id ) ?>" data-product-type="<?php echo esc_attr( $product_type ) ?>">
                    <i class="nitro-icon-<?php echo esc_attr( $wr_icons ); ?>-wishlist wishlist-icon"></i>
                    <span class="ajax-loading" style="display: none"><i class="fa fa-spinner fa-pulse"></i></span>
                    <?php echo esc_html( $label_popup ) ?>
                </a>
            </div>
        </div>
    </form>
</div>
