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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post;

$wr_nitro_options = WR_Nitro::get_options();

// Get sale price dates
$countdown = get_post_meta( get_the_ID(), '_show_countdown', true );
$start     = get_post_meta( get_the_ID(), '_sale_price_dates_from', true );
$end       = get_post_meta( get_the_ID(), '_sale_price_dates_to', true );
$now       = date( 'd-m-y' );

$is_bookings = ( class_exists( 'WC_Bookings' ) && $product->is_type( 'booking' ) );
$class_gravity_form = WR_Nitro_Helper::check_gravityforms( $post->ID ) ? ' custom-gravity-form' : NULL;

// Catalog mode
$wr_catalog_mode = $wr_nitro_options['wc_archive_catalog_mode'];

// Show custom button
$wr_show_button = $wr_nitro_options['wc_archive_catalog_mode_button'];

// Custom button action
$wr_show_button_action = $wr_nitro_options['wc_archive_catalog_mode_button_action'];

// Custom button action text
$wr_show_button_text = $wr_nitro_options['wc_archive_catalog_mode_button_action_simple'];

//Get custom content
$wr_custom_content_position = $wr_nitro_options['wc_single_product_custom_content_position'];
$wr_custom_content_data		= $wr_nitro_options['wc_single_product_custom_content_data'];

//Get custom message for sale product
$mes = get_post_meta( get_the_ID(), '_message_product_sale', true );
?>
<div id="product-<?php the_ID(); ?>" <?php post_class( 'style-1' . ( is_customize_preview() ? ' customizable customize-section-product_single' : '' ) . $class_gravity_form ); ?>>
	<div class="p-single-top oh pr">
		<div class="p-single-images">
			<?php
				/**
				 * woocommerce_before_single_product_summary hook
				 *
				 * @hooked woocommerce_show_product_sale_flash - 10
				 * @hooked woocommerce_show_product_images - 20
				 */
				do_action( 'woocommerce_before_single_product_summary' );
			?>
		</div>
		<div class="p-single-info">
			<?php
				if ( $wr_nitro_options['wc_single_breadcrumb'] ) {
					echo '<div class="mgb10">';
						woocommerce_breadcrumb();
					echo '</div>';
				}
			?>
			<?php
				/**
				 * woocommerce_single_product_summary hook.
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 */
				do_action( 'woocommerce_single_product_summary' );
			?>
		</div>
		<?php if ( 'yes' == $countdown && $end && date( 'd-m-y', $start ) <= $now ) : ?>
			<div class="product__countdown pa bgw">
				<div class="wr-nitro-countdown fc jcsb tc aic" data-time='{"day": "<?php echo date( 'd', $end ); ?>", "month": "<?php echo date( 'm', $end ); ?>", "year": "<?php echo date( 'Y', $end ); ?>"}'></div>
			</div>
		<?php endif; ?>
	</div>

	<div class="p-single-middle clear">
		<div class="fl mgt10">
			 <?php
				 if ( class_exists( 'WR_Share_For_Discounts' ) ) {
				 	$product_id   = $product->get_id();
					$sfd          = get_option( 'wr_share_for_discounts' );
					$settings     = $sfd['enable_product_discount'];
					$product_data = WR_Share_For_Discounts::get_meta_data( $product_id );

					if ( $settings != 1 || $product_data['enable'] != 1 ) {
						echo WR_Nitro_Pluggable_WooCommerce::woocommerce_share();
					}
				} else {
					echo WR_Nitro_Pluggable_WooCommerce::woocommerce_share();
				}
 			?>
		</div>

		<?php
			if ( ! $is_bookings ) :
				echo '<div class="p-single-action fr clearfix">';
					woocommerce_template_single_add_to_cart();
				echo '</div>';
			endif;

			if ( $wr_catalog_mode && $wr_show_button ) {
				echo '<div class="p-single-action fr clearfix">';
					if ( $wr_show_button_action == 'simple' ) {
						echo '<a target="_blank" rel="noopener noreferrer" class="button wr-btn wr-btn-solid" href="' . esc_attr( $wr_show_button_text ) . '">' . esc_attr( $wr_nitro_options['wc_archive_catalog_mode_button_text'] ) . '</a>';
					} else {
						echo '<a class="button wr-btn wr-btn-solid wr-open-cf7" href="#wr-cf7-form">' . esc_attr( $wr_nitro_options['wc_archive_catalog_mode_button_text'] ) . '</a>';
						echo '<div id="wr-cf7-form" class="mfp-hide">';
							echo do_shortcode( '[contact-form-7 id="' . esc_attr( $wr_nitro_options['wc_archive_catalog_mode_button_action_cf7'] ) . '"]' );
						echo '</div>';
					}
				echo '</div>';
			}
		?>

		<div class="p-meta tu fr mgt10 mgr10">
			<span class="availability mgl10">
				<?php $availability = $product->get_availability(); ?>
				<span class="meta-left"><?php esc_html_e( 'Availability:', 'wr-nitro' ); ?></span>
				<span class="stock <?php echo esc_attr( $product->is_in_stock() ? 'in-stock' : 'out-stock' ); ?>">
					<?php
						if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
							if ( $product->manage_stock == 'yes' && ! empty( $availability['availability'] ) ) :
								echo esc_html( $availability['availability'] );
							elseif ( $product->manage_stock == 'no' && $product->is_in_stock() ) :
								esc_html_e( 'In Stock', 'wr-nitro' );
							else :
								esc_html_e( 'Out Of Stock', 'wr-nitro' );
							endif;
						} else {
							if ( $product->get_manage_stock() && ! empty( $availability['availability'] ) ) :
								echo esc_html( $availability['availability'] );
							elseif ( ! $product->get_manage_stock() && $product->is_in_stock() ) :
								esc_html_e( 'In Stock', 'wr-nitro' );
							else :
								esc_html_e( 'Out Of Stock', 'wr-nitro' );
							endif;
						}
					?>
				</span>
			</span>
		</div>
	</div>

	<?php if ( $is_bookings ) : ?>
		<div class="p-single-booking pdt50 pdb50">
			<div class="container">
				<?php woocommerce_template_single_add_to_cart(); ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="p-single-bot">
		<?php
			/**
			 * woocommerce_after_single_product_summary hook.
			 *
			 * @hooked woocommerce_output_product_data_tabs - 10
			 * @hooked woocommerce_upsell_display - 15
			 * @hooked woocommerce_output_related_products - 20
			 */
			do_action( 'woocommerce_after_single_product_summary' );
		?>

		<?php wc_get_template( 'woorockets/single-product/builder.php' ); ?>
	</div>

	<?php wc_get_template( 'woorockets/single-product/floating-button.php' ); ?>


</div><!-- #product-<?php the_ID(); ?> -->
