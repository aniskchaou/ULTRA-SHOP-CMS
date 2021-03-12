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

// Get layout
$layout         = $wr_nitro_options['wc_single_layout'];
$tab_style      = $wr_nitro_options['wc_single_tab_style'];
$tab_position   = $wr_nitro_options['wc_single_tab_position'];
$thumb_position = $wr_nitro_options['wc_single_thumb_position'];
$product_nav    = $wr_nitro_options['wc_single_single_nav'];
$page_title     = $wr_nitro_options['wc_single_title'];
$sticky         = $wr_nitro_options['wc_single_sidebar_sticky'];

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

// Get widget style
$w_style = $wr_nitro_options['w_style'];

// Get sidebar add to above product detail
$sidebar = $wr_nitro_options['wc_single_content_before'];
?>
<?php
	// Get page title
	if ( $page_title ) {
		WR_Nitro_Render::get_template( 'common/page', 'title' );
	}
	if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
		echo '<div class="widget-before-product-detail">';
			dynamic_sidebar( $sidebar );
		echo '</div>';
	}
?>
<div id="product-<?php the_ID(); ?>" <?php post_class( 'style-2 social-circle ' . $layout . ( is_customize_preview() ? ' customizable customize-section-product_single' : '' ) ); ?>>
	<div class="oh pdt30 <?php if ( 'no-sidebar' != $layout ) echo 'container' ?>">
		<div class="fc fcw<?php if ( 'right-sidebar' == $layout ) echo ' right-sidebar' ?>">
			<div id="shop-detail">
				<div class="container">
					<div class="row">
						<div class="w667-12 <?php echo esc_attr( $layout != 'no-sidebar' ? 'cm-5' : 'cm-6' ); ?>">
							<div class="p-single-images pr clear thumb-<?php echo esc_attr( $thumb_position ) ?>">
								<?php
									/**
									 * woocommerce_before_single_product_summary hook
									 *
									 * @hooked woocommerce_show_product_sale_flash - 10
									 * @hooked woocommerce_show_product_images - 20
									 */
									do_action( 'woocommerce_before_single_product_summary' );
								?>
							</div><!-- .p-single-image -->
						</div><!-- .cm-5 -->

						<div class="w667-12 <?php echo esc_attr( $layout != 'no-sidebar' ? 'cm-7' : 'cm-6' ); ?>">
							<div class="p-single-info">
								<?php
									// Get page title
									if ( $page_title ) {
										echo '<h2 itemprop="name" class="hidden">' . get_the_title() . '</h2>';
									}
									if ( ! $page_title && $wr_nitro_options['wc_single_breadcrumb'] ) {
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
									 // remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
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

								<?php if ( 'below_details' == $tab_position && 'no-sidebar' == $layout ) : ?>
									<div class="product-tabs <?php echo esc_attr( $tab_style ); ?>-tab">
										<?php
											if ( 'accordion' == $tab_style ) {
												wc_get_template( 'single-product/tabs/tabs-accordion.php' );
											} else {
												woocommerce_output_product_data_tabs();
											}
										?>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<?php if ( 'default' == $tab_position || 'no-sidebar' != $layout ) : ?>
							<div class="product-tabs cm-12 <?php echo esc_attr( $tab_style ); ?>-tab">
								<?php
									if ( 'accordion' == $tab_style ) {
										wc_get_template( 'single-product/tabs/tabs-accordion.php' );
									} else {
										woocommerce_output_product_data_tabs();
									}
								?>
							</div>
						<?php endif; ?>
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
					do_action( 'woocommerce_after_single_product_summary' );
				?>
			</div>

			<?php if ( $layout != 'no-sidebar' ) : ?>
				<div id="shop-sidebar" class="primary-sidebar <?php if ( $sticky == true ) echo 'primary-sidebar-sticky'; ?> widget-style-<?php echo esc_attr( $w_style ) . ' ' . ( is_customize_preview() ? 'customizable customize-section-widget_styles ' : '' ); ?> mgt30">
					<?php if ( $sticky == true ) echo '<div class="primary-sidebar-inner">'; ?>
						<?php dynamic_sidebar( $wr_nitro_options['wc_single_sidebar'] ); ?>
					<?php if ( $sticky == true ) echo '</div>'; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $product_nav && is_singular( 'product' ) ) : ?>
		<div class="p-single-nav">
			<div class="left fc aic pf">
				<?php
					$prev_post = get_previous_post();
					if ( is_a( $prev_post , 'WP_Post' ) ) {
						$prev_product = new WC_Product( $prev_post->ID );
						$prev_price   = $prev_product->get_price_html();

						echo get_the_post_thumbnail( $prev_post->ID, '60x60' );
						echo '<div class="ts-03 overlay_bg fc fcc jcc">';
							echo '<a class="fwb db color-dark" href="' . esc_url( get_permalink( $prev_post->ID ) ) . '">' . get_the_title( $prev_post->ID ) . '</a>';
							echo '<span class="price db">' . $prev_price . '</span>';
						echo '</div>';
					}
				?>
			</div>
			<div class="right fc aic pf">
				<?php
					$next_post = get_next_post();
					if ( is_a( $next_post , 'WP_Post' ) ) {
						$next_product = new WC_Product( $next_post->ID );
						$next_price   = $next_product->get_price_html();

						echo '<div class="ts-03 overlay_bg fc fcc jcc">';
							echo '<a class="fwb db color-dark" href="' . esc_url( get_permalink( $next_post->ID ) ) . '">' . get_the_title( $next_post->ID ) . '</a>';
							echo '<span class="price db">' . $next_price . '</span>';
						echo '</div>';
						echo get_the_post_thumbnail( $next_post->ID, '60x60' );
					}
				?>
			</div>
		</div>
	<?php endif; ?>

	<?php wc_get_template( 'woorockets/single-product/floating-button.php' ); ?>


</div><!-- #product-<?php the_ID(); ?> -->
