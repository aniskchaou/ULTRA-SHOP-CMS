<?php
/**
 * External product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/external.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Get theme option
$wr_nitro_options = WR_Nitro::get_options();

// Catalog mode
$wr_catalog_mode = $wr_nitro_options['wc_archive_catalog_mode'];
if ( $wr_catalog_mode ) return;

// Get single style
$single_style = get_post_meta( get_the_ID(), 'single_style', true );
if ( $single_style == 0 ) {
	$single_style = $wr_nitro_options['wc_single_style'];
} else {
	$single_style = get_post_meta( get_the_ID(), 'single_style', true );
}

// Icon Set
$icons = $wr_nitro_options['wc_icon_set'];

do_action( 'woocommerce_before_add_to_cart_form' );
?>
<?php
	if ( $single_style != 1 ) {
		echo '<div class="p-single-action nitro-line btn-inline pdb20 fc aic aife">';
	}
?>
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<p class="cart mg0">
		<a href="<?php echo esc_url( $product_url ); ?>" rel="nofollow" class="single_add_to_cart_button button alt btr-50 db pdl20 pdr20 fl"><i class="nitro-icon-<?php echo esc_attr( $icons ); ?>-cart mgr10 mgt10"></i><?php echo esc_html( $button_text ); ?></a>
	</p>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
<?php
	if ( $single_style != 1 ) {
		echo '</div>';
	}
?>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>