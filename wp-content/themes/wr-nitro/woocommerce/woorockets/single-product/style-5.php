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

// Get layout
$layout         = $wr_nitro_options['wc_single_layout'];
$floating_cart  = $wr_nitro_options['wc_single_floating_button'];
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

// Get sidebar add to above product detail
$sidebar = $wr_nitro_options['wc_single_content_before'];

//Get custom content
$wr_custom_content_position = $wr_nitro_options['wc_single_product_custom_content_position'];
$wr_custom_content_data		= $wr_nitro_options['wc_single_product_custom_content_data'];

// Get downloadable settings
$simple_downloadable   = get_post_meta( get_the_ID(), '_downloadable', true );
$downloadable_files    = get_post_meta( get_the_ID(), '_downloadable_files', false );
$file_type             = get_post_meta( get_the_ID(), '_file_type', true );
$file_format           = get_post_meta( get_the_ID(), '_file_format', true );

//Get custom message for sale product
$mes = get_post_meta( get_the_ID(), '_message_product_sale', true );
?>

<div id="product-<?php the_ID(); ?>" <?php post_class( 'style-5 social-circle ' . $layout . ( is_customize_preview() ? ' customizable customize-section-product_single' : '' ) ); ?>>
	<div class="oh pdt30 <?php if ( 'no-sidebar' != $layout ) echo 'container' ?>">
		<div id="shop-detail">
			<div class="container">
				<div class="row">
					<div class="w667-12 cm-8">
						<div class="block-info overlay_bg pd30 btb nitro-line">
							<?php
							if ( $wr_nitro_options['wc_single_breadcrumb'] ) {
								echo '<div class="mgb10">';
									woocommerce_breadcrumb();
								echo '</div>';
							}
							?>
							<h1 class="product-title mg0 mgb10" itemprop="name"><?php the_title(); ?></h1>
							<?php
							if ( ! empty( $wr_custom_content_data ) && $wr_custom_content_position == 'after_title' ) {
								echo '<div class="custom-content">' . do_shortcode( $wr_custom_content_data ) . '</div>';
							}
							?>
							<div class="product-categories tu mgb20">
								<?php if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) : ?>
									<?php echo '' . $product->get_categories(); ?>
								<?php else : ?>
									<?php echo wc_get_product_category_list( $product->get_id() ); ?>
								<?php endif; ?>
							</div>
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
							</div><!-- .p-single-image -->
							<?php
								$tabs = apply_filters( 'woocommerce_product_tabs', array() );
								if ( ! empty( $tabs ) ) : ?>

									<div class="product-infomation mgt30">
										<?php foreach ( $tabs as $key => $tab ) : ?>
											<h3><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></h3>
											<div class="panel entry-content woocommerce-tabs" id="tab-<?php echo esc_attr( $key ); ?>">
												<?php call_user_func( $tab['callback'], $key, $tab ); ?>
											</div>
										<?php endforeach; ?>
									</div><!-- .product-infomation -->

								<?php endif;
							?>
						</div><!-- .block-info -->
					</div><!-- .cm-8 -->
					<div class="w667-12 cm-4">
						<div class="p-single-info">

							<div class="block-info mgb30 overlay_bg pd30 btb nitro-line">
								<?php
									if ( ! $wr_catalog_mode || $wr_show_price ) {
										woocommerce_template_single_price();

										if ( $mes != '' && $product->is_on_sale() ) {
											echo '<div class="message-on-sale">' . $mes . '</div>';
										}

										if ( ! empty( $wr_custom_content_data ) && $wr_custom_content_position == 'after_price' ) {
											echo '<div class="custom-content">' . do_shortcode( $wr_custom_content_data ) . '</div>';
										}
									}
								?>

								<div class="desc mgb20 mgt20" itemprop="description">
									<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt );
										if ( ! empty( $wr_custom_content_data ) && $wr_custom_content_position == 'after_except' ) {
											echo '<div class="custom-content">' . do_shortcode( $wr_custom_content_data ) . '</div>';
										}
									?>
								</div>

								<?php if ( ! $wr_catalog_mode ) : ?>
									<?php echo woocommerce_template_single_add_to_cart();

										if ( ! empty( $wr_custom_content_data ) && $wr_custom_content_position == 'after_add_cart' ) {
											echo '<div class="custom-content">' . do_shortcode( $wr_custom_content_data ) . '</div>';
										}
									?>
								<?php endif; ?>

								<?php
									if ( $wr_catalog_mode && $wr_show_button ) {
										echo '<div class="p-single-action">';
											if ( $wr_show_button_action == 'simple' ) {
												echo '<a target="_blank" rel="noopener noreferrer" class="button wr-btn wr-btn-solid wr-btn-custom" href="' . esc_attr( $wr_show_button_text ) . '">' . esc_attr( $wr_nitro_options['wc_archive_catalog_mode_button_text'] ) . '</a>';
											} else {
												echo '<a class="button wr-btn wr-btn-solid wr-btn-custom wr-open-cf7" href="#wr-cf7-form">' . esc_attr( $wr_nitro_options['wc_archive_catalog_mode_button_text'] ) . '</a>';
												echo '<div id="wr-cf7-form" class="mfp-hide">';
													echo do_shortcode( '[contact-form-7 id="' . esc_attr( $wr_nitro_options['wc_archive_catalog_mode_button_action_cf7'] ) . '"]' );
												echo '</div>';
											}
										echo '</div>';
									}
								?>
							</div><!-- .block-info -->
							<?php if ( 'yes' == $simple_downloadable && ! empty( $downloadable_files[0] ) ) : ?>
								<div class="block-info mgb30 overlay_bg pd30 list-files btb nitro-line">
									<div class="product_meta">
										<span class="fwb tu"><?php esc_html_e( 'Files Included', 'wr-nitro' ); ?></span>
										<ul>
											<?php
												if ( ! empty( $downloadable_files ) ) {
													foreach ( $downloadable_files as $files ) {
														foreach ( $files as $file ) {
															if ( ! empty( $file['name'] ) ) {
																echo '<li class="nitro-line"><i class="fa fa-file-text-o mgr10"></i> ' . esc_html( $file['name'] ) . '</li>';
															}
														}
													}
												}
											?>
										</ul>
									</div>
								</div><!-- .list-files -->
							<?php endif; ?>
							<div class="block-info overlay_bg pd30 btb nitro-line">
								<div class="product_meta">
									<?php
                                        $tags = get_the_terms( $post->ID, 'product_tag' );
										$tag_count = is_array( $tags ) ? count( $tags ) : 0;

										do_action( 'woocommerce_product_meta_start' );
									?>
									<?php
									$rating_count = $product->get_rating_count();
									if ( $rating_count > 0 ) : ?>
										<div class="ratings fc jcsb nitro-line">
											<span class="fwb tu"><?php esc_html_e( 'Ratings', 'wr-nitro' ); ?></span>
											<span><?php woocommerce_template_single_rating(); ?></span>
										</div>
									<?php endif; ?>

									<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

										<div class="sku_wrapper fc jcsb nitro-line">
											<span class="fwb tu"><?php esc_html_e( 'SKU', 'wr-nitro' ); ?> </span> <span class="sku" itemprop="sku"><?php echo esc_attr( ($sku = $product->get_sku()) ? $sku : esc_html__( 'N/A', 'wr-nitro' ) ); ?></span>
										</div>

									<?php endif; ?>

									<?php if ( 'yes' != $simple_downloadable ) : ?>
										<div class="availability fc jcsb nitro-line">
											<?php $availability = $product->get_availability(); ?>
											<span class="fwb tu"><?php esc_html_e( 'Availability', 'wr-nitro' ); ?></span>
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
										</div><!-- .availability -->
									<?php endif; ?>

									<?php if ( ! empty( $file_type ) ) : ?>
										<div class="file-type fc jcsb nitro-line">
											<span class="fwb tu"><?php esc_html_e( 'Type', 'wr-nitro' ) ?></span>
											<span><?php echo esc_html( $file_type ); ?></span>
										</div><!-- .file-type -->
									<?php endif; ?>

									<?php if ( ! empty( $file_format ) ) : ?>
										<div class="file-format fc jcsb nitro-line">
											<span class="fwb tu"><?php esc_html_e( 'File format', 'wr-nitro' ) ?></span>
											<span><?php echo esc_html( $file_format ); ?></span>
										</div><!-- .file-type -->
									<?php endif; ?>

									<?php if ( $tag_count > 1 ) : ?>
										<div class="tagged_as fc jcsb nitro-line">
											<span class="fwb tu">
												<?php echo _n( 'Tag ', 'Tags', $tag_count, 'wr-nitro' ); ?>
											</span>
											<span class="tr"><?php echo wp_kses_post( $product->get_tags() ); ?></span>
										</div><!-- .tagged_as -->
									<?php endif; ?>

									<?php do_action( 'woocommerce_product_meta_end' ); ?>

									<?php
										do_action( 'woocommerce_share' );
										echo WR_Nitro_Pluggable_WooCommerce::woocommerce_share();
									?>
								</div><!-- .product_meta -->
							</div><!-- .block-info -->

							<?php
								/**
								 * woocommerce_single_product_summary hook.
								 */
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
								remove_action( 'woocommerce_single_product_summary', array( 'WR_Nitro_Pluggable_WooCommerce', 'nitro_template_single_share' ), 50 );

								echo '<div class="block-info product-summary">';
									do_action( 'woocommerce_single_product_summary' );
								echo '</div>';
							?>
						</div>
					</div>
				</div>
			</div>

			<?php wc_get_template( 'woorockets/single-product/builder.php' ); ?>

			<div class="addition-product <?php if ( $wr_nitro_options['wc_single_product_related_full'] == 'boxed' ) echo 'container'; ?>">
				<?php woocommerce_upsell_display(); ?>

				<?php woocommerce_output_related_products(); ?>

				<?php wc_get_template( 'single-product/recent-viewed.php' ); ?>
			</div>
		</div>
	</div>

	<?php wc_get_template( 'woorockets/single-product/floating-button.php' ); ?>

</div><!-- #product-<?php the_ID(); ?> -->
