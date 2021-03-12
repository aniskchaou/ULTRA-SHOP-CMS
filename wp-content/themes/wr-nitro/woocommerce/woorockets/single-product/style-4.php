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

global $post, $woocommerce, $product;

$wr_nitro_options = WR_Nitro::get_options();
$floating_cart  = $wr_nitro_options['wc_single_floating_button'];

// Get sale price dates
$countdown = get_post_meta( get_the_ID(), '_show_countdown', true );
$start     = get_post_meta( get_the_ID(), '_sale_price_dates_from', true );
$end       = get_post_meta( get_the_ID(), '_sale_price_dates_to', true );
$now       = date( 'd-m-y' );

$is_bookings = ( class_exists( 'WC_Bookings' ) && $product->is_type( 'booking' ) );

// Catalog mode
$wr_catalog_mode = $wr_nitro_options['wc_archive_catalog_mode'];

// Show price
$wr_show_price = $wr_nitro_options['wc_archive_catalog_mode_price'];

// Show custom button
$wr_show_button = $wr_nitro_options['wc_archive_catalog_mode_button'];

// Custom button action
$wr_show_button_action = $wr_nitro_options['wc_archive_catalog_mode_button_action'];

// Custom button action text
$wr_show_button_text = $wr_nitro_options['wc_archive_catalog_mode_button_action_simple'];
?>
<div id="product-<?php the_ID(); ?>" <?php post_class( 'style-4' . ( is_customize_preview() ? ' customizable customize-section-product_single' : '' ) ); ?>>
	<div class="p-single-top oh pr">
		<div class="cm-6 w800-12">
			<div class="p-single-info pa ">
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

				<?php
					if ( $wr_catalog_mode && $wr_show_button ) {
						echo '<div class="p-single-action mgt20">';
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
			</div>
		</div>
		<div class="cm-6 w800-12">
			<div class="p-single-images pa">
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
			<?php if ( 'yes' == $countdown && $end && date( 'd-m-y', $start ) <= $now ) : ?>
				<div class="product__countdown pa bgw">
					<div class="wr-nitro-countdown fc jcsb tc aic" data-time='{"day": "<?php echo date( 'd', $end ); ?>", "month": "<?php echo date( 'm', $end ); ?>", "year": "<?php echo date( 'Y', $end ); ?>"}'></div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $is_bookings ) : ?>
		<div class="p-single-booking pdt50 pdb50 overlay_bg">
			<div class="container">
				<?php woocommerce_template_single_add_to_cart(); ?>
			</div>
		</div><!-- .p-single-booking -->
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
	</div>

	<?php wc_get_template( 'woorockets/single-product/builder.php' ); ?>

	<?php wc_get_template( 'woorockets/single-product/floating-button.php' ); ?>

</div><!-- #product-<?php the_ID(); ?> -->
