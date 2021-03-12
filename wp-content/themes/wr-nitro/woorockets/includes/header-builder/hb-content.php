<?php
// Get theme options.
$wr_nitro_options = WR_Nitro::get_options();

// Check if WooCommerce is activated?
$wr_is_woocommerce_active = call_user_func( 'is_' . 'plugin' . '_active', 'woocommerce/woocommerce.php' );

?>
<div class="list-row-outer hb-clear">
	<div class="hb-rows list-row hb-clear">
		<div class="hb-row">
			<div class="hb-row-drag-handle"></div>
			<div class="hb-columns hb-clear">
				<div class="hb-column row-container">
					<div class="hb-items">
						<div data-relation="flex" class="header-item item-flex">
							<div class="header-item-inner">
								<div class="item-content">
									<i class="fa fa-arrows-h"></i>
								</div>
								<i class="fa fa-trash delete-item"></i>
							</div>
						</div>

						<?php if( $wr_is_woocommerce_active ) { ?>
							<div data-relation="shopping-cart" class="header-item item-shopping-cart">
								<div class="header-item-inner">
									<div class="item-content">
										<i class="fa fa-shopping-cart" data-bind="class:iconName"></i>
									</div>
									<i class="fa fa-trash delete-item"></i>
								</div>
							</div>
						 <?php } ?>

						<?php if( $wr_is_woocommerce_active && call_user_func( 'is_' . 'plugin' . '_active', 'wr-currency/main.php' ) ) { ?>
							<div data-relation="currency" class="header-item item-currency">
								<div class="header-item-inner">
									<div class="item-content">
										<div class="list-currency">
											<?php
												$data_currency = WR_Currency_Hook::get_currency();

												if( $data_currency ) {
													$wc_currencies = get_woocommerce_currencies();
													$wc_currency_code = get_woocommerce_currency();
													$wc_currency_symbol = WR_Currency_Hook::currency_symbol( $wc_currency_code );

													echo '
													<div class="item-list">
														<span class="flag"><img width="37" src="' . WR_CC_URL . 'assets/images/flag/' . strtolower( $wc_currency_code ) . '.png" /></span>
														<span class="des">' . $wc_currencies[ $wc_currency_code ] . ' (' . $wc_currency_symbol . ')</span>
														<i class="fa fa-angle-down" aria-hidden="true"></i>
													</div>';
												} else {
													esc_html_e( 'Please add currency to show', 'wr-nitro' );
												}
											?>
										</div>
									</div>
									<i class="fa fa-trash delete-item"></i>
								</div>
							</div>
						 <?php } ?>

						<div data-relation="social" class="header-item item-social">
							<div class="header-item-inner">
								<div class="item-content">
									<?php esc_html_e( 'Please enable social channel to show', 'wr-nitro' ); ?>
								</div>
								<i class="fa fa-trash delete-item"></i>
							</div>
						</div>
						<div data-relation="logo" class="header-item item-logo">
							<div class="header-item-inner">
								<div class="item-content"></div>
								<i class="fa fa-trash delete-item"></i>
							</div>
						</div>
						<div data-relation="text" class="header-item item-text">
							<div class="header-item-inner">
								<div class="item-content" data-bind="html:content">
									Text
								</div>
								<i class="fa fa-trash delete-item"></i>
							</div>
						</div>
						<div data-relation="sidebar" class="header-item item-sidebar">
							<div class="header-item-inner">
								<div class="item-content">
									<div class="icon"><i class="fa fa-th"></i></div>
									<div class="sidebar-notice"><?php esc_html_e( 'Please choose the sidebar to show.', 'wr-nitro' ) ?></div>
								</div>
								<i class="fa fa-trash delete-item"></i>
							</div>
						</div>
						<div data-relation="menu" class="header-item item-menu">
							<div class="header-item-inner">
								<div class="item-content">
									<div class="menu-main-menu-container"></div>
								</div>
								<i class="fa fa-trash delete-item"></i>
							</div>
						</div>
						<div data-relation="search" class="header-item item-search">
							<div class="header-item-inner">
								<div class="item-content">
									<span class="all-category"><?php esc_html_e( 'All categories', 'wr-nitro' ); ?></span>
									<input type="text" class="text-search" placeholder="<?php esc_html_e( 'Search keyword', 'wr-nitro' ); ?>">
									<div class="style-icon icon-button"><i class="fa fa-search"></i></div>
									<div class="style-icon text-button"><?php esc_html_e( 'Search', 'wr-nitro' ); ?></div>
								</div>
								<i class="fa fa-trash delete-item"></i>
							</div>
						</div>

						<?php if( call_user_func( 'is_' . 'plugin' . '_active', 'sitepress-multilingual-cms/sitepress.php' ) ) { ?>
							<div data-relation="wpml" class="header-item item-wpml">
								<div class="header-item-inner">
									<div class="item-content">
										<?php do_action('icl_language_selector'); ?>
									</div>
									<i class="fa fa-trash delete-item"></i>
								</div>
							</div>
						<?php } ?>

						<?php if( $wr_is_woocommerce_active && $wr_nitro_options['wc_general_wishlist'] == 1 && ( call_user_func( 'is_' . 'plugin' . '_active', 'yith-woocommerce-wishlist/init.php' ) || call_user_func( 'is_' . 'plugin' . '_active', 'yith-woocommerce-wishlist-premium/init.php' ) ) ) { ?>
							<div data-relation="wishlist" class="header-item item-wishlist">
								<div class="header-item-inner">
									<div class="item-content">
										<span class="text"></span>
										<i class="icon nitro-icon-<?php echo esc_attr( $wr_nitro_options['wc_icon_set'] ); ?>-wishlist"></i>
									</div>
									<i class="fa fa-trash delete-item"></i>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="hb-row-settings"><i class="fa fa-cog"></i><i class="fa fa-trash delete-item"></i></div>
		</div>
	</div>
</div>
<div class="add-row">
	<i class="fa fa-plus"></i><span><?php  esc_html_e( 'Add row', 'wr-nitro' ); ?></span>
</div>
