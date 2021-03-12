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

// Sticky add to cart
$sticky = $wr_nitro_options['wc_detail_mobile_sticky_cart'];

?>

<div id="product-<?php the_ID(); ?>" <?php post_class( 'style-2 single-mobile-layout' ); ?>>
	<div class="oh pdt30">
		<div id="shop-detail">
			<div class="container">
				<div class="row">
					<div class="cm-12">
						<div class="p-single-images pr clear">
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
						<div class="p-single-info pdt30 mgt30 nitro-line <?php echo esc_attr( $sticky ? 'fixed' : 'fc fcc' ); ?>">
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
									echo '<div class="p-single-action pdb20 mgb20 nitro-line">';
										echo '<span>';
											if ( $wr_show_button_action == 'simple' ) {
												echo '<a target="_blank" rel="noopener noreferrer" class="button wr-btn wr-btn-solid" href="' . esc_attr( $wr_show_button_text ) . '">' . esc_attr( $wr_nitro_options['wc_archive_catalog_mode_button_text'] ) . '</a>';
											} else {
												echo '<a class="button wr-btn wr-btn-solid wr-open-cf7" href="#wr-cf7-form">' . esc_attr( $wr_nitro_options['wc_archive_catalog_mode_button_text'] ) . '</a>';
												echo '<div id="wr-cf7-form" class="mfp-hide">';
													echo do_shortcode( '[contact-form-7 id="' . esc_attr( $wr_nitro_options['wc_archive_catalog_mode_button_action_cf7'] ) . '"]' );
												echo '</div>';
											}
										echo '</span>';
									echo '</div>';
								}
							?>
							<div class="product-tabs accordion-tab">
								<?php wc_get_template( 'single-product/tabs/tabs-accordion.php' ); ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php wc_get_template( 'woorockets/single-product/builder.php' ); ?>

			<?php
				/**
				 * woocommerce_after_single_product_summary hook.
				 *
				 * @hooked woocommerce_output_product_data_tabs - 10
				 * @hooked woocommerce_upsell_display - 15
				 * @hooked woocommerce_output_related_products - 20
				 */
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				do_action( 'woocommerce_after_single_product_summary' );
			?>
		</div>
	</div>


</div><!-- #product-<?php the_ID(); ?> -->
