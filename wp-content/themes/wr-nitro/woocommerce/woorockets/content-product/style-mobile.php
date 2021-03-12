<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post;

$wr_nitro_options = WR_Nitro::get_options();

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

$wr_product_class = '';

$wr_is_custom_attribute_activated = class_exists( 'WR_Custom_Attributes' ) ? true : false;

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

?>
<div <?php post_class('oh body_bg pr'); ?>>
	<div class="product__wrap">
		<div class="product__info">
			<?php
				woocommerce_show_product_loop_sale_flash();
			?>
		</div>
		<div class="product__image pr">
			<?php

				$stock_status = get_post_meta( $post->ID, '_stock_status', true );
				if ( $stock_status == 'outofstock' ) {
					echo '<div class="product__status pa tu color-white fwb">' . esc_html__( 'Out Of Stock', 'wr-nitro' ) . '</div>';
				}

				/**
				 * woocommerce_before_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 * @hooked woocommerce_template_loop_product_thumbnail - 10
				 */
				remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
				echo '<a class="db" href="' . esc_url( get_permalink() ) . '">';
					do_action( 'woocommerce_before_shop_loop_item_title' ) ;
				echo '</a>';
			?>
		</div><!-- .product__image -->

		<div class="product__content">

			<?php 
				if ( $wr_nitro_options['wc_display_custom_attr_position'] == 'before-title' && $wr_is_custom_attribute_activated ) {
					do_action( 'wc_display_custom_attr' ); 
				} 
			?>
			<div class="product__title tc mgb10">
				<h3><a class="hover-primary" href="<?php esc_url( the_permalink() ); ?>" title="<?php esc_attr( the_title() ); ?>"><?php the_title(); ?></a></h3>
			</div><!-- .product__title -->

			<div class="product__price tc">
				<?php
					if ( ! $wr_catalog_mode || $wr_show_price ) {
						wc_get_template( 'loop/price.php' );
					}
				?>
			</div><!-- .product__price -->
			<?php 
				if ( $wr_nitro_options['wc_display_custom_attr_position'] == 'after-title' && $wr_is_custom_attribute_activated ) {
					do_action( 'wc_display_custom_attr' ); 
				} 
			?>
			<div class="product__description mgt10">
				<p>
					<?php
						$excerpt = wp_strip_all_tags( $post->post_excerpt );
						echo wp_trim_words( $excerpt, apply_filters( 'wr_nitro_control_text_length_wc_mobile', 30 ) );
					?>
				</p>
			</div><!-- .product__description -->

			<?php if ( ! $wr_catalog_mode ) : ?>
				<div class="product__action pa fc jcsa mgt20 nitro-line">
					<?php

						// Add to wishlist button
						if ( class_exists( 'YITH_WCWL' ) && $wr_show_wishlist== '1' ) :
							echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
						endif;

						// Add to cart button
						if ( ( $wr_nitro_options['wc_buynow_btn'] && ! $wr_nitro_options['wc_disable_btn_atc'] ) || ! $wr_nitro_options['wc_buynow_btn'] || ( $wr_nitro_options['wc_buynow_btn'] && $wr_nitro_options['wc_disable_btn_atc'] && ! $product->is_type( 'simple' ) ) ) {
							wc_get_template( 'loop/add-to-cart.php' );
						}

						// Quick buy button
						if ( $wr_nitro_options['wc_buynow_btn'] && $product->is_purchasable() && $product->is_type( 'simple' ) && ! WR_Nitro_Helper::check_gravityforms( $post->ID ) && ! WR_Nitro_Helper::yith_wc_product_add_ons( $post->ID ) && ! WR_Nitro_Helper::wc_measurement_price_calculator( $post->ID ) ) {
							echo '<a class="product__btn bts-50 btn-buynow icon_color" href="#" data-product-id="' . get_the_ID() . '"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-quickbuy"></i></a>';
						}

					?>
				</div><!-- .p-action -->
			<?php endif; ?>
		</div><!-- .p-info -->
	</div>
</div>
