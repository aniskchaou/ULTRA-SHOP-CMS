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

$tab_style       = $wr_nitro_options['wc_single_tab_style'];
$related         = $wr_nitro_options['wc_single_product_related'];
$recent_viewed   = $wr_nitro_options['wc_single_product_recent_viewed'];
$upsell          = $wr_nitro_options['wc_single_product_upsell'];
if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
	$upsells = $product->get_upsells();
} else {
	$upsells = wc_products_array_orderby( array_filter( array_map( 'wc_get_product', $product->get_upsell_ids() ), 'wc_products_array_filter_visible' ), 'rand', 'desc' );
}
$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
$viewed_products = array_filter( array_map( 'absint', $viewed_products ) );
$floating_cart   = $wr_nitro_options['wc_single_floating_button'];

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
<div id="product-<?php the_ID(); ?>" <?php post_class( 'style-3 ' . ( is_customize_preview() ? ' customizable customize-section-product_single' : '' ) ); ?>>
	<div class="oh row">
		<div class="cm-6 w800-12">
			<div class="p-single-images pr">
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
		</div>

		<div class="cm-6 w800-12 pdr30 p-single-info">
			<?php
				if ( $wr_nitro_options['wc_single_breadcrumb'] ) {
					echo '<div class="mgb10 mgt10">';
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
					echo '<div class="p-single-action nitro-line pdb20 mgb20">';
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

			<div class="product-tabs <?php echo esc_attr( $tab_style ); ?>-tab">
				<?php
					if ( 'accordion' == $tab_style ) {
						wc_get_template( 'single-product/tabs/tabs-accordion.php' );
					} else {
						woocommerce_output_product_data_tabs();
					}
				?>
			</div>
		</div>

		<?php wc_get_template( 'woorockets/single-product/floating-button.php' ); ?>
	</div>

	<?php if ( $related || $recent_viewed || $upsell ) : ?>
		<div class="row default-tab">
			<div class="woocommerce-tabs wc-tabs-custom addition-product mgt60 cm-12">
				<ul class="tabs wc-tabs">
					<?php if ( $related ) : ?>
						<li class="related_tab">
							<a href="#tab-related" class="tab-heading body_color"><?php esc_attr_e( 'Related Products', 'wr-nitro' ); ;?></a>
						</li>
					<?php endif; ?>
					<?php if ( $upsell && $upsells ) : ?>
						<li class="upsell_tab">
							<a href="#tab-upsell" class="tab-heading body_color"><?php esc_attr_e( 'Upsell Products', 'wr-nitro' ); ;?></a>
						</li>
					<?php endif; ?>
					<?php if ( $recent_viewed && $viewed_products ) : ?>
						<li class="recent_viewed_tab">
							<a href="#tab-recent-viewed" class="tab-heading body_color"><?php esc_attr_e( 'Recent Viewed Products', 'wr-nitro' ); ;?></a>
						</li>
					<?php endif; ?>
				</ul>
				<?php if ( $related ) : ?>
					<div class="panel entry-content wc-tab" id="tab-related">
						<?php woocommerce_output_related_products(); ?>
					</div>
				<?php endif; ?>

				<?php if ( $upsell && $upsells ) : ?>
					<div class="panel entry-content wc-tab" id="tab-upsell">
						<?php woocommerce_upsell_display(); ?>
					</div>
				<?php endif; ?>

				<?php if ( $recent_viewed && $viewed_products ) : ?>
					<div class="panel entry-content wc-tab" id="tab-recent-viewed">
						<?php wc_get_template( 'single-product/recent-viewed.php' ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
	<?php wc_get_template( 'woorockets/single-product/builder.php' ); ?>
</div><!-- #product-<?php the_ID(); ?> -->
