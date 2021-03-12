<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Extra post classes
$wr_classes = array();

// Grid column
$wr_columns = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['columns'] : $wr_nitro_options['wc_archive_layout_column'];
if ( $wr_nitro_options['wc_archive_layout_column'] ) {
	array_push( $wr_classes, 'cxs-12 cs-6 cm-' . (int) ( 12 / $wr_columns ) );
}

// Get masonry settings
$wr_masonry_image_size = get_post_meta( get_the_ID(), 'wc_masonry_product_size', true );
if ( 'wc-large-square' == $wr_masonry_image_size || 'wc-large-rectangle' == $wr_masonry_image_size ) {
	array_push( $wr_classes, 'large' );
}

$wr_layout = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['style'] : $wr_nitro_options['wc_archive_item_layout'];

// Style of list product
$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];

// Show Compare
$wr_show_compare = $wr_nitro_options['wc_general_compare'];

// Show Wishlist
$wr_show_wishlist = $wr_nitro_options['wc_general_wishlist'];

// Catalog mode
$wr_catalog_mode = $wr_nitro_options['wc_archive_catalog_mode'];

// Show price
$wr_show_price = $wr_nitro_options['wc_archive_catalog_mode_price'];

// Icon Set
$wr_icons = $wr_nitro_options['wc_icon_set'];

$is_shop = ( ( function_exists( 'is_shop' ) && is_shop() ) || is_post_type_archive( 'product' ) || ( function_exists( 'is_product_category' ) && is_product_category() ) || ( function_exists( 'is_product_tag' ) && is_product_tag() ) || ( function_exists( 'is_woocommerce' ) && is_woocommerce() && is_tax() ) );

?>

<?php if ( $is_shop && wp_is_mobile() ) : ?>
	<?php wc_get_template( 'woorockets/content-product/style-mobile.php' ); ?>
<?php else: ?>
	<div <?php post_class( $wr_classes ); ?>>
		<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

		<?php
			if ( $wr_nitro_options['wc_archive_item_animation'] && ! ( ! empty( $wr_nitro_shortcode_attrs ) && $wr_nitro_shortcode_attrs['slider'] && 'sc-products' == $wr_nitro_shortcode_attrs['shortcode'] ) ) {
				echo '<div class="wr-item-animation">';
			}
			
			if ( 'list' == $wr_style ) :
				wc_get_template( 'woorockets/content-product/style-list.php' );
			else :
				wc_get_template( 'woorockets/content-product/style-' . esc_attr( $wr_layout ) . '.php' );
			endif;

			if ( $wr_nitro_options['wc_archive_item_animation'] && ! ( ! empty( $wr_nitro_shortcode_attrs ) && $wr_nitro_shortcode_attrs['slider'] && 'sc-products' == $wr_nitro_shortcode_attrs['shortcode'] ) ) {
				echo '</div>';
			}
		?>
		
		<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
	</div>
<?php endif; ?>