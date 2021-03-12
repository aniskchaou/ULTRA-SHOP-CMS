<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
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

global $product;

if ( ! $product->is_purchasable() ) {
	return;
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

// Show compare
$show_compare = $wr_nitro_options['wc_general_compare'];

// Show wishlist
$show_wishlist = $wr_nitro_options['wc_general_wishlist'];

// Icon Set
$icons = $wr_nitro_options['wc_icon_set'];

// Get product type settings
$simple_downloadable = get_post_meta( get_the_ID(), '_downloadable', true );

$check_gravityforms = WR_Nitro_Helper::check_gravityforms( $product->get_id() );

// Sticky add to cart
$sticky = $wr_nitro_options['wc_detail_mobile_sticky_cart'];

$add_to_cart_ajax = true;
if ( $check_gravityforms || ( get_option('woocommerce_enable_ajax_add_to_cart_single') == 'no' && ! (int) $wr_nitro_options['wc_buynow_btn'] ) ) {
	$add_to_cart_ajax = false;
}
?>

<?php
	if ( $single_style == 2 || $single_style == 3 || $single_style == 4 ) {
		echo '<div class="p-single-action nitro-line btn-inline pdb20 fc aic aife">';
	} elseif ( $single_style == 5 ) {
		echo '<div class="p-single-action">';
	}
?>
	<?php if ( $product->is_in_stock() ) : ?>

		<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

		<form class="cart pr" method="post" enctype='multipart/form-data'>
			<?php
				do_action( 'woocommerce_before_add_to_cart_button' );

				// Begin sticky
				if ( wp_is_mobile() && $sticky ) echo '<div class="p-action-sticky body_bg fc jcc">';

				/**
				 * @since 3.0.0.
				 */
				do_action( 'woocommerce_before_add_to_cart_quantity' );

				if ( ! $product->is_sold_individually() ) {
					if ( 'yes' == $simple_downloadable ) {
						echo '<div class="hidden">';
					}
						woocommerce_quantity_input( array(
			 				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
			 				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
			 				'input_value' => ( isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 )
			 			) );
		 			if ( 'yes' == $simple_downloadable ) {
		 				echo '</div>';
		 			}
				}

				/**
				 * @since 3.0.0.
				 */
				do_action( 'woocommerce_after_add_to_cart_quantity' );
			?>

			<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />

			<?php
				if ( $single_style == 5 && $simple_downloadable == 'yes' ) {
					echo '<div class="fc clb">';
				}
					$add_to_cart_button = '<button type="submit" class="' . ( $add_to_cart_ajax ? 'wr_single_add_to_cart_ajax' : NULL ) . ' single_add_to_cart_button wr_add_to_cart_button button alt btr-50 db pdl20 pdr20 bgd fl mgr10 mgt10 br-3"><i class="nitro-icon-' . esc_attr( $icons ) . '-cart mgr10"></i>' . $product->single_add_to_cart_text() . '</button>';

					// Add to cart button
					if ( ( $wr_nitro_options['wc_buynow_btn'] && ! $wr_nitro_options['wc_disable_btn_atc'] ) || ! $wr_nitro_options['wc_buynow_btn'] ) {
						echo wp_kses_post( $add_to_cart_button );
					}

					// Quick buy button
					if ( $wr_nitro_options['wc_buynow_btn'] && ! $check_gravityforms ) {
						echo '<button type="submit" class="single_buy_now wr_add_to_cart_button button alt btr-50 db pdl20 pdr20 bgd fl mgr10 mgt10 br-3"><i class="nitro-icon-' . esc_attr( $icons ) . '-quickbuy mgr10"></i>' . esc_html__( 'Buy now', 'wr-nitro' ) . '</button>';
					}

					// Add Wishlist button
					if ( class_exists( 'YITH_WCWL' ) && $show_wishlist && ! wp_is_mobile() ) {
						echo '<div class="wishlist-btn fl mgr10 mgt10 actions-button">' . do_shortcode( '[yith_wcwl_add_to_wishlist]' ) . '</div>';
					}

					// Add compare button
					if ( class_exists( 'YITH_WOOCOMPARE' ) && $show_compare ) {
						echo '
							<div class="product__compare icon_color fl actions-button mgt10">
								<a class="product__btn bts-50 mg0 db nitro-line btb pr" href="#"><i class="nitro-icon-' . esc_attr( $icons ) . '-compare"></i><span class="tooltip ab">' . esc_html__( 'Compare', 'wr-nitro' ) . '</span></a>
								<div class="hidden">' . do_shortcode( '[yith_compare_button container="no"]' ) . '</div>
							</div>
						';
					}
				if ( $single_style == 5 && $simple_downloadable == 'yes' ) {
					echo '</div>';
				}

				// End sticky
				if ( wp_is_mobile() && $sticky ) echo '</div>';
			?>

			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
		</form>

		<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

	<?php endif; ?>
<?php
	if ( $single_style != 1 ) {
		echo '</div>';
	}
?>