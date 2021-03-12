<?php
/**
 * Wishlist page template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.12
 */
global $post, $product;

$wr_nitro_options = WR_Nitro::get_options();

// Icon Set
$icons = $wr_nitro_options['wc_icon_set'];
?>

<?php do_action( 'yith_wcwl_before_wishlist_form', $wishlist_meta ); ?>

<form id="yith-wcwl-form" action="<?php echo esc_url( YITH_WCWL()->get_wishlist_url( 'view' . ( $wishlist_meta['is_default'] != 1 ? '/' . $wishlist_meta['wishlist_token'] : '' ) ) ) ?>" method="post" class="woocommerce <?php if( count( $wishlist_items ) == 0 ) echo 'empty'; ?>">

	<?php wp_nonce_field( 'yith-wcwl-form', 'yith_wcwl_form_nonce' ) ?>

	<!-- TITLE -->
	<?php
	do_action( 'yith_wcwl_before_wishlist_title' );

	if( ! empty( $page_title ) ) :
	?>
		<div class="wishlist-title <?php echo esc_attr( ($wishlist_meta['is_default'] != 1 && $is_user_owner) ? 'wishlist-title-with-form' : '' )?>">
			<div>
				<?php echo apply_filters( 'yith_wcwl_wishlist_title', '<h2>' . $page_title . '</h2>' ); ?>
				<?php if( $wishlist_meta['is_default'] != 1 && $is_user_owner ): ?>
					<button class="button show-title-form">
						<?php echo apply_filters( 'yith_wcwl_edit_title_icon', '<i class="fa fa-pencil"></i>' )?>
						<?php esc_html_e( 'Edit title', 'wr-nitro' ) ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
		<?php if( $wishlist_meta['is_default'] != 1 && $is_user_owner ): ?>
			<div class="hidden-title-form">
				<input type="text" value="<?php echo esc_attr( $page_title ); ?>" name="wishlist_name"/>
				<button class="button">
					<?php echo apply_filters( 'yith_wcwl_save_wishlist_title_icon', '<i class="fa fa-check"></i>' )?>
					<?php esc_html_e( 'Save', 'wr-nitro' )?>
				</button>
				<button class="hide-title-form">
					<?php echo apply_filters( 'yith_wcwl_cancel_wishlist_title_icon', '<i class="fa fa-remove"></i>' )?>
				</button>
			</div>
		<?php endif; ?>
	<?php
	endif;

	 do_action( 'yith_wcwl_before_wishlist' ); ?>

	<!-- WISHLIST TABLE -->
	<?php if( count( $wishlist_items ) > 0 ) : ?>
		<table class="shop_table cart wishlist_table shop_table_responsive" data-pagination="<?php echo esc_attr( $pagination )?>" data-per-page="<?php echo esc_attr( $per_page )?>" data-page="<?php echo esc_attr( $current_page )?>" data-id="<?php echo ( is_user_logged_in() ) ? esc_attr( $wishlist_meta['ID'] ) : '' ?>" data-token="<?php echo ( ! empty( $wishlist_meta['wishlist_token'] ) && is_user_logged_in() ) ? esc_attr( $wishlist_meta['wishlist_token'] ) : '' ?>">

			<?php $column_count = 2; ?>

			<thead>
			<tr>
				<?php if( $show_cb ) : ?>

					<th class="product-checkbox">
						<input type="checkbox" value="" name="" id="bulk_add_to_cart"/>
					</th>

				<?php
					$column_count ++;
				endif;
				?>

				<th class="product-thumbnail">
					<span class="nobr">&nbsp;</span>
				</th>

				<th class="product-name heading-color">
					<span class="nobr"><?php echo apply_filters( 'yith_wcwl_wishlist_view_name_heading', esc_html__( 'Product', 'wr-nitro' ) ) ?></span>
				</th>

				<?php if( $show_price ) : ?>

					<th class="product-price heading-color">
						<span class="nobr">
							<?php echo apply_filters( 'yith_wcwl_wishlist_view_price_heading', esc_html__( 'Price', 'wr-nitro' ) ) ?>
						</span>
					</th>

				<?php
					$column_count ++;
				endif;
				?>

				<?php if( $show_stock_status ) : ?>

					<th class="product-stock-status heading-color">
						<span class="nobr">
							<?php echo apply_filters( 'yith_wcwl_wishlist_view_stock_heading', esc_html__( 'Stock Status', 'wr-nitro' ) ) ?>
						</span>
					</th>

				<?php
					$column_count ++;
				endif;
				?>

				<?php if( $show_last_column ) : ?>

					<th class="product-add-to-cart"></th>

				<?php
					$column_count ++;
				endif;
				?>
				<th class="product-remove">&nbsp;</th>
			</tr>
			</thead>

			<tbody>
			<?php
				foreach( $wishlist_items as $item ) :
					global $product;
					if( function_exists( 'wc_get_product' ) ) {
						$product = wc_get_product( $item['prod_id'] );
					}
					else{
						$product = function_exists('wc_get_product') ? wc_get_product($item['prod_id']) : get_product($item['prod_id']);
					}

					if( $product !== false && $product->exists() ) :
						$availability = $product->get_availability();
						$stock_status = $availability['class'];
						?>
						<tr id="yith-wcwl-row-<?php echo esc_attr( $item['prod_id'] ); ?>" data-row-id="<?php echo esc_attr( $item['prod_id'] ); ?>" class="<?php echo esc_attr( $stock_status ); ?>">
							<?php if( $show_cb ) : ?>
								<td class="product-checkbox">
									<input type="checkbox" value="<?php echo esc_attr( $item['prod_id'] ) ?>" name="add_to_cart[]" <?php echo ( ! $product->is_type( 'simple' ) ) ? 'disabled="disabled"' : '' ?>/>
								</td>
							<?php endif ?>

							<td class="product-thumbnail">
								<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item['prod_id'] ) ) ) ?>">
									<?php echo wp_kses_post( $product->get_image() ); ?>
								</a>
							</td>

							<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'wr-nitro' ); ?>">
								<a class="heading-color" href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item['prod_id'] ) ) ) ?>"><?php echo apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ?></a>
								<?php do_action( 'yith_wccl_table_after_product_name', $item ); ?>
							</td>

							<?php if( $show_price ) : ?>
								<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'wr-nitro' ); ?>">
									<?php
									if( is_a( $product, 'WC_Product_Bundle' ) ){
										if( $product->min_price != $product->max_price ){
											echo sprintf( '%s - %s', wc_price( $product->min_price ), wc_price( $product->max_price ) );
										}
										else{
											echo wc_price( $product->min_price );
										}
									}
									elseif( $product->price != '0' ) {
										echo wp_kses_post( $product->get_price_html() );
									}
									else {
										echo apply_filters( 'yith_free_text', esc_html__( 'Free!', 'wr-nitro' ) );
									}
									?>
								</td>
							<?php endif ?>

							<?php if( $show_stock_status ) : ?>
								<td class="product-stock-status" data-title="<?php esc_attr_e( 'Stock Status', 'wr-nitro' ); ?>">
									<?php
									if( $stock_status == 'out-of-stock' ) {
										$stock_status = "Out";
										echo '<span class="wishlist-out-of-stock">' . esc_html__( 'Out of Stock', 'wr-nitro' ) . '</span>';
									} else {
										$stock_status = "In";
										echo '<span class="wishlist-in-stock">' . esc_html__( 'In Stock', 'wr-nitro' ) . '</span>';
									}
									?>
								</td>
							<?php endif ?>

							<?php if( $show_last_column ): ?>
							<td class="product-add-to-cart pr" data-title="<?php esc_attr_e( 'Add To Cart', 'wr-nitro' ); ?>">
								<!-- Date added -->
								<?php

								if( $show_dateadded && isset( $item['dateadded'] ) ):
									echo '<span class="dateadded">' . sprintf( esc_html__( 'Added on : %s', 'wr-nitro' ), date_i18n( get_option( 'date_format' ), strtotime( $item['dateadded'] ) ) ) . '</span>';
								endif;
								?>

								<!-- Add to cart button -->
								<?php if ( $show_add_to_cart && isset( $stock_status ) && $stock_status != 'Out' ): ?>
									<?php
										echo apply_filters( 'woocommerce_wishlist_add_to_cart_link',
											sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="' . ( ( $product->is_type( 'simple' ) ) ? 'ajax_add_to_cart' : '' ) . ' product__btn_cart product__btn btr-50 button %s product_type_%s"><i class="nitro-icon-' . esc_attr( $icons ) . '-cart"></i>%s</a>',
												esc_url( $product->add_to_cart_url() ),
												esc_attr( $product->get_id() ),
												esc_attr( $product->get_sku() ),
												esc_attr( isset( $quantity ) ? $quantity : 1 ),
												$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
												esc_attr( $product->get_type() ),
												esc_html( $product->add_to_cart_text() )
											),
										$product );
									?>
								<?php endif ?>

								<!-- Change wishlist -->
								<?php if( $available_multi_wishlist && is_user_logged_in() && count( $users_wishlists ) > 1 && $move_to_another_wishlist ): ?>
								<select class="change-wishlist selectBox">
									<option value=""><?php esc_attr_e( 'Move', 'wr-nitro' ) ?></option>
									<?php
									foreach( $users_wishlists as $wl ):
										if( $wl['wishlist_token'] == $wishlist_meta['wishlist_token'] ){
											continue;
										}

									?>
										<option value="<?php echo esc_attr( $wl['wishlist_token'] ) ?>">
											<?php
											$wl_title = ! empty( $wl['wishlist_name'] ) ? esc_html( $wl['wishlist_name'] ) : esc_html( $default_wishlsit_title );
											if( $wl['wishlist_privacy'] == 1 ){
												$wl_privacy = esc_html__( 'Shared', 'wr-nitro' );
											}
											elseif( $wl['wishlist_privacy'] == 2 ){
												$wl_privacy = esc_html__( 'Private', 'wr-nitro' );
											}
											else{
												$wl_privacy = esc_html__( 'Public', 'wr-nitro' );
											}

											echo sprintf( '%s - %s', $wl_title, $wl_privacy );
											?>
										</option>
									<?php
									endforeach;
									?>
								</select>
								<?php endif; ?>
							</td>
							<?php if( $is_user_owner ): ?>
								<td class="product-remove" data-title="<?php esc_attr_e( 'Remove', 'wr-nitro' ); ?>">
									<a class="remove-product remove_from_wishlist" href="<?php echo esc_url( add_query_arg( 'remove_from_wishlist', $item['prod_id'] ) ) ?>" class="remove remove_from_wishlist" title="<?php esc_attr_e( 'Remove this product', 'wr-nitro' ) ?>"><i class="fa fa-trash-o"></i></a>
								</td>
							<?php endif; ?>
						<?php endif; ?>
						</tr>
					<?php
					endif;
				endforeach;
			?>

			<?php

			if( ! empty( $page_links ) ) : ?>
				<tr class="pagination-row">
					<td colspan="<?php echo esc_attr( $column_count ) ?>"><?php echo wp_kses_post( $page_links ); ?></td>
				</tr>
			<?php endif ?>
			</tbody>

			<tfoot>
			<tr>
				<td colspan="6">
					<div class="fc jcsb mgb10">
						<?php if( $show_cb ) : ?>
							<div class="custom-add-to-cart-button-cotaniner">
								<a href="<?php echo esc_url( add_query_arg( array( 'wishlist_products_to_add_to_cart' => '', 'wishlist_token' => $wishlist_meta['wishlist_token'] ) ) ) ?>" class="button alt" id="custom_add_to_cart"><?php echo apply_filters( 'yith_wcwl_custom_add_to_cart_text', esc_html__( 'Add the selected products to the cart', 'wr-nitro' ) ) ?></a>
							</div>
						<?php endif; ?>

						<?php if ( is_user_logged_in() && $is_user_owner && $show_ask_estimate_button && $count > 0 ): ?>
							<div class="ask-an-estimate-button-container">
								<a href="<?php echo esc_attr( $additional_info ? '#ask_an_estimate_popup' : $ask_estimate_url ) ?>" class="btn button ask-an-estimate-button" <?php echo '' . ( $additional_info ? 'data-rel="prettyPhoto[ask_an_estimate]"' : '' ) ?>>
								<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_icon', '<i class="fa fa-shopping-cart"></i>' )?>
								<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_text', esc_html__( 'Ask for an estimate', 'wr-nitro' ) ) ?>
							</a>
							</div>
						<?php endif; ?>
					</div>

					<?php
					do_action( 'yith_wcwl_before_wishlist_share' );

					if ( is_user_logged_in() && $is_user_owner && $wishlist_meta['wishlist_privacy'] != 2 && $share_enabled ){
						yith_wcwl_get_template( 'share.php', $share_atts );
					}

					do_action( 'yith_wcwl_after_wishlist_share' );
					?>
				</td>
			</tr>
			</tfoot>

		</table>
	<?php endif; ?>

	<div class="form-empty hidden">
		<p class="cart-empty">
			<?php esc_html_e( 'No products were added to the wishlist.', 'wr-nitro' ) ?>
		</p>

		<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
			<p class="return-to-shop">
				<a class="button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
					<?php esc_html_e( 'Return To Shop', 'wr-nitro' ) ?>
				</a>
			</p>
		<?php endif; ?>
	</div>

	<?php wp_nonce_field( 'yith_wcwl_edit_wishlist_action', 'yith_wcwl_edit_wishlist' ); ?>

	<?php if( $wishlist_meta['is_default'] != 1 ): ?>
		<input type="hidden" value="<?php echo esc_attr( $wishlist_meta['wishlist_token'] ); ?>" name="wishlist_id" id="wishlist_id">
	<?php endif; ?>

	<?php do_action( 'yith_wcwl_after_wishlist' ); ?>

</form>

<?php do_action( 'yith_wcwl_after_wishlist_form', $wishlist_meta ); ?>

<?php if( $additional_info ): ?>
	<div id="ask_an_estimate_popup">
		<form action="<?php echo esc_url( $ask_estimate_url ); ?>" method="post" class="wishlist-ask-an-estimate-popup">
			<?php if( ! empty( $additional_info_label ) ):?>
				<label for="additional_notes"><?php echo esc_html( $additional_info_label ) ?></label>
			<?php endif; ?>
			<textarea id="additional_notes" name="additional_notes"></textarea>

			<button class="btn button ask-an-estimate-button ask-an-estimate-button-popup" >
				<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_icon', '<i class="fa fa-shopping-cart"></i>' )?>
				<?php esc_html_e( 'Ask for an estimate', 'wr-nitro' ) ?>
			</button>
		</form>
	</div>
<?php endif; ?>
