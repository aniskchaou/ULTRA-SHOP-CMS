<?php
/**
 * The template for displaying content of quick view product
 *
 */

global $post, $woocommerce, $product;

$wr_nitro_options = WR_Nitro::get_options();

// Catalog mode
$wr_catalog_mode = $wr_nitro_options['wc_archive_catalog_mode'];

// Show price
$wr_show_price = $wr_nitro_options['wc_archive_catalog_mode_price'];

// Icon Set
$icons = $wr_nitro_options['wc_icon_set'];

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="quickview-modal">

	<?php
		if( call_user_func( 'is_' . 'plugin' . '_active', 'sizeguide/ctSizeGuidePlugin.php' ) )	{
			$wr_sizeguide = new ctSizeGuideDisplay();
			echo '
				<div class="wr-sizeguide">
					<div class="sizeguide-close"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></div>
				';
				$wr_sizeguide->displaySizeGuide( $product->get_id() );
			echo '</div>';
		}
	?>
	<div <?php post_class(); ?>>
		<div class="row quickview-modal-inner">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="cm-6">
					<div class="p-lightbox-img pr">
						<?php
							if ( $product->is_on_sale() ) {
								wc_get_template( 'loop/sale-flash.php' );
							}
							wc_get_template( 'single-product/product-image.php' );
						?>
					</div>
				</div>
			<?php endif; ?>

			<div class="cm-6 info<?php if ( ! has_post_thumbnail() ) echo ' full'; ?>">
				<h1 itemprop="name" class="product_title entry-title mgb10"><a href="<?php esc_url( the_permalink() ); ?>" title="<?php esc_attr( the_title() ); ?>"><?php the_title(); ?></a></h1>
				<div class="fc jcsb aic mgb20">
					<?php
						if ( ! $wr_catalog_mode || $wr_show_price ) {
							wc_get_template( 'loop/price.php' );
						}

						wc_get_template( 'loop/rating.php' );
					?>
				</div>
				<div class="mgb20" itemprop="description">
					<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>

					<h3 class="view_detail"><a href="<?php esc_url( the_permalink() ); ?>" title="<?php esc_attr( the_title() ); ?>"><?php esc_html_e( 'View Full Details', 'wr-nitro' ); ?></a></h3>
				</div>
				<?php
					if ( ! $wr_nitro_options['wc_archive_catalog_mode'] && $product->is_in_stock() ) {
						if ( WR_Nitro_Helper::check_gravityforms( $post->ID ) || WR_Nitro_Helper::yith_wc_product_add_ons( $post->ID ) || WR_Nitro_Helper::wc_measurement_price_calculator( $post->ID ) || WR_Nitro_Helper::wc_fields_factory( $post->ID ) ) {
							echo '<div class="mgtb20"><a class="button" href="' . get_the_permalink() . '"><i class="nitro-icon-' . esc_attr( $icons ) . '-cart mgr10"></i></i>' . __( 'Select options', 'wr-nitro' ) . '</a></div>';
						} else {
				?>
							<div class="quickview-button clear mgb20 pdt10">
								<?php woocommerce_template_single_add_to_cart(); ?>
							</div>
				<?php
						}
					}
				?>
				<div class="p-meta pdt20">
                    <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in db mgb10">' . '<span class="fwb dib">'._n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'wr-nitro' ) . '</span><span class="posted_in_cat"> ', '</span></span>' ); ?>

					<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

						<span class="sku_wrapper"><span class="fwb dib"><?php esc_html_e( 'SKU:', 'wr-nitro' ); ?></span><span class="sku"><?php echo esc_html( ($sku = $product->get_sku()) ? $sku : esc_html__( 'N/A', 'wr-nitro' ) ); ?></span></span>

					<?php endif; ?>

					<span class="availability mgb10">
						<?php $availability = $product->get_availability(); ?>
						<span class="dib"><?php esc_html_e( 'Availability:', 'wr-nitro' ); ?></span>:
						<span class="stock <?php echo esc_attr( $availability['class'] ); ?>">
							<?php
								if ( $product->get_manage_stock() && ! empty( $availability['availability'] ) ) :
									echo esc_html( $availability['availability'] );
								elseif ( ! $product->get_manage_stock() && $product->is_in_stock() ) :
									esc_html_e( 'In Stock', 'wr-nitro' );
								else :
									esc_html_e( 'Out Of Stock', 'wr-nitro' );
								endif;
							?>
						</span>
					</span>

                    <?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . '<span class="fwb dib db">'._n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'wr-nitro' ) . '</span> ', '</span>' ); ?>
				</div>

				<?php

				if( call_user_func( 'is_' . 'plugin' . '_active', 'sizeguide/ctSizeGuidePlugin.php' ) )	{
					$sizeguide_position = get_option( 'wc_size_guide_button_position', false );

					if ( 'ct-position-summary' == $sizeguide_position ) {
						/**
						 * woocommerce_single_product_summary hook.
						 */
						echo '<div class="product-summary">';
						echo do_shortcode('[ct_size_guide]');
						echo '</div>';
					}
				}
				?>

			</div>
		</div>
	</div>
</div>
<?php do_action( 'woocommerce_after_quickview_modal' ); ?>
