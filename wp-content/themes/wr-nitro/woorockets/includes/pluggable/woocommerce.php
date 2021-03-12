<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 * Custom functions for WooCommerce.
 */

/**
 * Plug into WooCommerce.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Pluggable_WooCommerce {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	/**
	 * Variable for check nonce.
	 *
	 * @var  string
	 */
	protected static $uid;

	/**
	 * Initialize pluggable functions for WooCommerce.
	 *
	 * @return  voidz
	 */
	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}

		// Remove some default action handlers.
		remove_action( 'woocommerce_after_shop_loop_item_title'  , 'woocommerce_template_loop_rating'   , 5  );
		remove_action( 'woocommerce_before_main_content'         , 'woocommerce_breadcrumb'             , 20 );
		remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );

		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'nitro_template_single_rating' ), 9 );
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'nitro_template_single_price' ), 11 );
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'nitro_template_single_share' ), 50 );
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'nitro_message_sale' ), 11 );

		add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'nitro_before_upsell_display' ), 14 );
		add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'woocommerce_output_recent_viewed_products' ), 21 );
		add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'nitro_after_related_products' ), 22 );

		// Support WooCommerce Simple Auction plugin
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'nitro_simple_aution_plugin' ), 15 );

		// Change product rating position.
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 15 );

		// Add sidebar to shop page
		add_action( 'woocommerce_before_product_list', array( __CLASS__, 'update_before_archive_product' ) );
		add_action( 'woocommerce_after_shop_loop', array( __CLASS__, 'update_after_archive_product' ) );
		add_action( 'wr_nitro_before_header', array( __CLASS__, 'woocommerce_mobile_sidebar' ) );

		// Add sidebar to product details
		add_action( 'woocommerce_before_single_product', array( __CLASS__, 'update_before_single_product' ) );
		add_action( 'woocommerce_after_single_product', array( __CLASS__, 'update_after_single_product' ) );

		// Add sidebar to cart page
		add_action( 'woocommerce_before_cart', array( __CLASS__, 'update_before_cart_page' ) );
		add_action( 'woocommerce_after_cart', array( __CLASS__, 'update_after_cart_page' ) );

		// Add sidebar to checkout page
		add_action( 'woocommerce_after_checkout_form', array( __CLASS__, 'update_after_checkout_page' ) );

		// Change layout of single product
		add_action( 'template_redirect', array( __CLASS__, 'nitro_template_single' ) );

		// Update notice success when reset password
		add_action( 'woocommerce_reset_password_notification', array( __CLASS__, 'setcookie_first_reset_pass' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'show_notice_reset_pass' ), 30 );

		// Register additional sidebar for WooCommerce.
		add_action( 'widgets_init',  array( __CLASS__, 'widgets_init' ) );

		// Customize product quick view.
		add_action( 'wp_ajax_wr_quickview', array( __CLASS__, 'wr_quickview' ) );
		add_action( 'wp_ajax_nopriv_wr_quickview', array( __CLASS__, 'wr_quickview' ) );
		add_action( 'wp_ajax_wr_quickbuy', array( __CLASS__, 'wr_quickbuy' ) );
		add_action( 'wp_ajax_nopriv_wr_quickbuy', array( __CLASS__, 'wr_quickbuy' ) );

		// Delete product in wishlish
		add_action( 'wp_ajax_wr_remove_product_wishlish', array( __CLASS__, 'remove_product_wishlish' ) );
		add_action( 'wp_ajax_nopriv_wr_remove_product_wishlish', array( __CLASS__, 'remove_product_wishlish' ) );

		// Delete product in cart
		add_action( 'wp_ajax_wr_product_remove', array( __CLASS__, 'product_remove' ) );
		add_action( 'wp_ajax_nopriv_wr_product_remove', array( __CLASS__, 'product_remove' ) );

		// Get add to cart message
		add_action( 'wp_ajax_wr_add_to_cart_message', array( __CLASS__, 'add_to_cart_message' ) );
		add_action( 'wp_ajax_nopriv_wr_add_to_cart_message', array( __CLASS__, 'add_to_cart_message' ) );

		// Get add to cart error
		add_action( 'wp_ajax_wr_add_to_cart_error', array( __CLASS__, 'add_to_cart_error' ) );
		add_action( 'wp_ajax_nopriv_wr_add_to_cart_error', array( __CLASS__, 'add_to_cart_error' ) );

		// Register Ajax action to clear cart.
		add_action( 'wp_ajax_wr_clear_cart'       , array( __CLASS__, 'clear_cart' ) );
		add_action( 'wp_ajax_nopriv_wr_clear_cart', array( __CLASS__, 'clear_cart' ) );

		// Change button class of variable product
		add_action( 'woocommerce_single_variation', array( __CLASS__, 'change_single_variation_add_to_cart_button' ), 20 );

		// Enqueue custom scripts and styles.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		// Print Ajax URL for front-end.
		add_action( 'wp_head', array( __CLASS__, 'wp_head' ) );

		// Add custom fields to product options
		add_action( 'woocommerce_product_options_pricing',   array( __CLASS__, 'add_custom_general_fields' ) );
		add_action( 'woocommerce_product_options_downloads', array( __CLASS__, 'add_custom_general_fields_options_downloadable' ) );
		add_action( 'woocommerce_process_product_meta',      array( __CLASS__, 'add_custom_general_fields_save' ) );

		// General init action
		add_action( 'init', array( __CLASS__, 'general_wc_init' ) );

		add_action( 'template_redirect', array( __CLASS__, 'add_product_viewed' ) );

		// Add icon switch layout
		add_action( 'woocommerce_before_shop_loop', array( __CLASS__, 'switch_layout' ), 9999 );

		// Remove WooCommerce default styles.
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );

		// Customize search form.
		add_filter( 'get_product_search_form', array( __CLASS__, 'get_product_search_form' ) );

		// Customize product tabs.
		add_filter( 'woocommerce_product_tabs', array( __CLASS__, 'woocommerce_product_tabs' ), 100 );

		// Change number of products displayed per page
		add_filter( 'loop_shop_per_page', array( __CLASS__, 'change_product_per_page' ) );

		// Switch layout grid and list in product list.
		add_filter( 'wr_nitro_options', array( __CLASS__, 'switch_layout_products' ), 20 );

		// Add product title when added to wishlist
		add_filter( 'yith_wcwl_product_added_to_wishlist_message', array( __CLASS__, 'add_title_to_wishlist' ) );

		// Add form fields Image title for product category
		add_action( 'product_cat_add_form_fields', array( __CLASS__, 'add_category_fields' ), 20 );
		add_action( 'product_cat_edit_form_fields', array( __CLASS__, 'edit_category_fields' ), 20 );

		add_action( 'created_term',array( __CLASS__, 'save_category_fields' ), 20, 3 );
		add_action( 'edit_term',array( __CLASS__, 'save_category_fields' ), 20, 3 );

		add_filter( 'woocommerce_available_variation', array( __CLASS__, 'woocommerce_available_variation' ), 20, 3 );

		add_filter( 'woocommerce_cart_redirect_after_error', array( __CLASS__, 'woocommerce_cart_redirect_after_error' ) );

		// Change image size of product category
		add_filter( 'subcategory_archive_thumbnail_size', array( __CLASS__, 'wr_adjust_product_cat_thumbnail_size' ) );

		// Remove some settings unused of plugin YITH Compare premium
		if ( function_exists( 'yith_woocompare_premium_constructor' ) ) {
			add_filter( 'yith_woocompare_general_settings', array( __CLASS__, 'remove_settings_yith_compare_premium' ), 10, 1 );
		}

		// Add param total price
		add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'total_price_cart' ) );

		// Overwrite filter of Woocommerce Gift Card plugin
		if( call_user_func( 'is_' . 'plugin' . '_active', 'woocommerce-giftcard/woocommerce-giftcard.php' ) ) {
			add_filter( 'woocommerce_loop_add_to_cart_link', array( __CLASS__, 'woocommerce_loop_add_to_cart_link' ), 20, 2 );
		}

		// Remove action of content product
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

		// Create action with ajax
		add_action( 'get_header', array( __CLASS__, 'action_cart_ajax' ), 999999999 );

		// Custom flexslider of WC
		add_filter( 'woocommerce_single_product_carousel_options', array( __CLASS__, 'custom_flexslider' ) );

		// Handle cart updated action to support 'Buy Now' feature.
		add_action( 'woocommerce_cart_updated', array( __CLASS__, 'handle_cart_updated' ) );

		// Change number of related products on product page
		add_filter( 'woocommerce_output_related_products_args', array( __CLASS__, 'wr_related_products_args' ) );

		// Add filter to prepare data for WooCommerce Products Filter.
		add_filter( 'woof_get_request_data', array(__CLASS__, 'woof_get_request_data') );

		// Add filter to refine video URL associated with a product.
        add_filter( 'wr_nitro_refine_video_url', array(__CLASS__, 'refine_video_url') );

		// State that initialization completed.
		self::$initialized = true;

	}

	/**
	 * Add template before rating and after price
	 *
	 * @since 1.1.9
	 */
	public static function nitro_template_single() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();

		// Get single style
		$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		if ( $single_style == 0 ) {
			$single_style = $wr_nitro_options['wc_single_style'];
		} else {
			$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		}

		// Single layout 1
		if ( $single_style == 1 && ! wp_is_mobile()  ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 25 );

			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

            /*Update WooCommerce Flexslider options*/
            add_filter( 'woocommerce_single_product_carousel_options', self::nitro_update_woo_flexslider_options(array('slideshow'=>true)) );
		}

		// Single layout 2
		if ( $single_style == 2 && ! wp_is_mobile()  ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
		}

		// Single layout 4
		if ( $single_style == 4 && ! wp_is_mobile() ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

            /*Update WooCommerce Flexslider options*/
            add_filter( 'woocommerce_single_product_carousel_options', self::nitro_update_woo_flexslider_options(array('slideshow'=>true)) );
		}

		// Remove action of YITH plugins
		WR_Nitro_Helper::remove_action( 'woocommerce_single_product_summary', array( 'YITH_Woocompare_Frontend', 'add_compare_link' ), 35 );
	}

	/**
	 * Add template before rating and after price
	 *
	 * @since 1.1.9
	 */
	public static function nitro_template_single_rating() {
		echo '<div class="fc jcsb aic mgb20">';
	}
	public static function nitro_template_single_price() {
		echo '</div>';
	}

	/**
	 * Support WooCommerce Simple Auction plugin
	 *
	 * @since 1.1.9
	 */
	public static function nitro_simple_aution_plugin() {
		if ( ! is_admin() || defined('DOING_AJAX') ) {
			if ( function_exists( 'woocommerce_auction_add_to_cart' ) ) {
				woocommerce_auction_add_to_cart();
			}
			if ( function_exists( 'woocommerce_auction_bid' ) ) {
				woocommerce_auction_bid();
			}
			if ( is_user_logged_in() && function_exists( 'woocommerce_auction_pay' ) ) {
				woocommerce_auction_pay();
			}
		}
	}

	/**
	 * Add share for discount button
	 *
	 * @since 1.1.9
	 */
	public static function nitro_template_single_share() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();

		// Get single style
		$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		if ( $single_style == 0 ) {
			$single_style = $wr_nitro_options['wc_single_style'];
		} else {
			$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		}

		if ( $single_style != 1 ) {
			if ( class_exists( 'WR_Share_For_Discounts' ) ) {
				global $product;

				// Get value option of WR Share For Discount
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
		}
	}

	/**
	 * Custom layout of Addition Product
	 *
	 * @since 1.1.9
	 */
	public static function nitro_before_upsell_display() {
		$wr_nitro_options = WR_Nitro::get_options();
		$boxed = '';
		if ( $wr_nitro_options['wc_single_product_related_full'] == 'boxed' ) {
			$boxed = ' container';
		}
		echo '<div class="addition-product' . $boxed . '">';
	}
	public static function nitro_after_related_products() {
		echo '</div>';
	}


	/**
	 * Render recent viewed products
	 *
	 * @since 1.1.9
	 */
	public static function woocommerce_output_recent_viewed_products() {
		$wr_nitro_options = WR_Nitro::get_options();
		$single_style = $wr_nitro_options['wc_single_style'];

		if ( '1' == $single_style || '2' == $single_style || wp_is_mobile() ) {
			wc_get_template( 'single-product/recent-viewed.php' );
		}
	}

	/**
	 * Add param total price
	 *
	 * @param array $content
	 *
	 * @return array
	 *
	 * @since 1.1.9
	 */
	public static function total_price_cart( $content ) {
		$content[ 'wr_total_price' ] = WC()->cart->get_cart_subtotal();
		$content[ 'wr_count_item' ] = WC()->cart->get_cart_contents_count();

		return $content;
	}

	/**
	 * Refine all product type to product variable
	 *
	 * @param string $add_to_cart_html
	 * @param object $product
	 *
	 * @since 1.1.7
	 */
	public static function woocommerce_loop_add_to_cart_link( $add_to_cart_html, $product ) {
		$post_id = $product->get_id();
		$is_giftcard = get_post_meta( $post_id, '_giftcard', true );
		if ( ! $is_giftcard ) {
			return $add_to_cart_html;
		} else {
			$wr_nitro_options = WR_Nitro::get_options();
			$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

			if ( $wr_nitro_options['wc_archive_catalog_mode'] ) return;

			// Get product item style
			$wr_item_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['style'] : $wr_nitro_options['wc_archive_item_layout'];

			// Style of list product
			$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];

			// Icon Set
			$wr_icons = $wr_nitro_options['wc_icon_set'];

			if ( 'list' == $wr_style && ! is_singular( 'product' ) || is_cart() ) {
				$class_icon = 'btr-50 button %s product_type_%s"><i class="mgr10 nitro-icon-' . esc_attr( $wr_icons ) . '-cart"></i>%s</a>';
			} else {
				if ( '1' == $wr_item_style ) {
					$class_icon = 'db bts-40 color-dark bgw %s product_type_%s hover-primary"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart"></i><span class="tooltip ar">%s</span></a>';
				} elseif ( '2' == $wr_item_style ) {
					$class_icon = 'bgw btb btr-40 color-dark %s product_type_%s"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart mgr10"></i><span>%s</span></a>';
				} elseif ( '3' == $wr_item_style || '4' == $wr_item_style ) {
					$class_icon = 'pr color-dark hover-primary %s product_type_%s"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart"></i><span class="tooltip ab">%s</span></a>';
				} elseif ( '5' == $wr_item_style ) {
					$class_icon = 'button db textup aligncenter pr %s product_type_%s"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart mgr10"></i>%s</a>';
				}
			}

			return sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="product__btn_cart product__btn ' . $class_icon,
				esc_url( $product->get_permalink() ),
				esc_attr( $product->get_id() ),
				esc_attr( $product->get_sku() ),
				esc_attr( isset( $quantity ) ? $quantity : 1 ),
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				esc_attr( $product->get_type() ),
				__('Select options' , 'wr-nitro')
			);
		}
	}

	/**
	 * Adjust size of product category thumbnail
	 *
	 * @since 1.1.4
	 */
	public static function wr_adjust_product_cat_thumbnail_size( $size ) {
		$wr_nitro_options = WR_Nitro::get_options();

		// Get product list style
		$layout = $wr_nitro_options['wc_categories_style'];

		if ( $layout == 'masonry' ) {
			$size = 'full';
		}

		return $size;
	}

	/**
	 * Add attribute image shop thumbnail
	 *
	 * @param array $attribute
	 * @param class $wc_product_variable
	 * @param array $variation
	 */
	public static function woocommerce_available_variation( $attribute, $wc_product_variable, $variation ) {
		$attachment_id = get_post_thumbnail_id( $attribute['variation_id'] );

		if( $attachment_id == 0 ) {
			$attachment_id = get_post_thumbnail_id( $variation->get_id() );
		}

		$attachment   = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
		$image_srcset = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id, 'shop_single' ) : false;
		$image_srcset = $image_srcset ? $image_srcset : '';
		$image_sizes  = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $attachment_id, 'shop_single' ) : false;
		$image_sizes  = $image_sizes ? $image_sizes : '';

		$attribute['thumb_image_src']    = $attachment[0];
		$attribute['thumb_image_srcset'] = $image_srcset;
		$attribute['thumb_image_sizes']  = $image_sizes;

		return $attribute;
	}

	/**
	 * save_category_fields function.
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param string $taxonomy
	 */
	public static function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if( 'product_cat' == $taxonomy ) {
			if ( isset( $_POST['product_cat_banner_id'] ) ) {
				update_woocommerce_term_meta( $term_id, 'image_banner_id', absint( $_POST['product_cat_banner_id'] ) );
			}

			if ( isset( $_POST['wr_background_size'] ) ) {
				update_woocommerce_term_meta( $term_id, 'wr_background_size', esc_attr( $_POST['wr_background_size'] ) );
			}

			if ( isset( $_POST['wr_background_repeat'] ) ) {
				update_woocommerce_term_meta( $term_id, 'wr_background_repeat', esc_attr( $_POST['wr_background_repeat'] ) );
			}

			if ( isset( $_POST['wr_background_position'] ) ) {
				update_woocommerce_term_meta( $term_id, 'wr_background_position', esc_attr( $_POST['wr_background_position'] ) );
			}

			if ( isset( $_POST['wr_background_attachment'] ) ) {
				update_woocommerce_term_meta( $term_id, 'wr_background_attachment', esc_attr( $_POST['wr_background_attachment'] ) );
			}
		}
	}

	/**
	 * Category banner fields.
	 */
	public static function add_category_fields() {
		?>
		<div class="form-field term-banner-wrap">
			<label><?php esc_html_e( 'Background image title', 'wr-nitro' ); ?></label>
			<div id="product_cat_banner" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
			<div style="line-height: 60px;">
				<input type="hidden" id="product_cat_banner_id" name="product_cat_banner_id" />
				<button type="button" class="upload_image_banner_button button"><?php esc_html_e( 'Upload/Add image', 'wr-nitro' ); ?></button>
				<button type="button" class="remove_banner_button button"><?php esc_html_e( 'Remove image', 'wr-nitro' ); ?></button>
			</div>
			<script type="text/javascript">

				// Only show the "remove image" button when needed
				if ( ! jQuery( '#product_cat_banner_id' ).val() ) {
					jQuery( '.remove_banner_button' ).hide();
				}

				// Uploading files
				var file_frame_img_banner;

				jQuery( document ).on( 'click', '.upload_image_banner_button', function( event ) {

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame_img_banner ) {
						file_frame_img_banner.open();
						return;
					}

					// Create the media frame.
					file_frame_img_banner = wp.media.frames.downloadable_file = wp.media({
						title: '<?php esc_html_e( "Choose an image", "wr-nitro" ); ?>',
						button: {
							text: '<?php esc_html_e( "Use image", "wr-nitro" ); ?>'
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame_img_banner.on( 'select', function() {
						var attachment = file_frame_img_banner.state().get( 'selection' ).first().toJSON();

						jQuery( '#product_cat_banner_id' ).val( attachment.id );
						jQuery( '#product_cat_banner' ).find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
						jQuery( '.remove_banner_button, .attr-cat-banner' ).show();
					});

					// Finally, open the modal.
					file_frame_img_banner.open();
				});

				jQuery( document ).on( 'click', '.remove_banner_button', function() {
					jQuery( '#product_cat_banner' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
					jQuery( '#product_cat_banner_id' ).val( '' );
					jQuery( '.remove_banner_button, .attr-cat-banner' ).hide();
					return false;
				});

				jQuery( document ).ajaxComplete( function( event, request, options ) {
					if ( request && 4 === request.readyState && 200 === request.status
						&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {

						var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
						if ( ! res || res.errors ) {
							return;
						}
						// Clear Thumbnail fields on submit
						jQuery( '#product_cat_banner' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
						jQuery( '#product_cat_banner_id' ).val( '' );
						jQuery( '.remove_banner_button, .attr-cat-banner' ).hide();
						jQuery( '.attr-cat-banner select option:first-child' ).attr('selected','selected');
						return;
					}
				} );

			</script>
			<div class="clear"></div>
		</div>

		<div style="display: none;" class="attr-cat-banner">
			<div class="form-field term-display-type-wrap">
				<label for="wr_background_size"><?php esc_html_e( 'Background size', 'wr-nitro' ); ?></label>
				<select name="wr_background_size" id="wr_background_size">
					<option value="auto"><?php esc_html_e( 'Auto', 'wr-nitro' ); ?></option>
					<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
					<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
				</select>
			</div>
			<div class="form-field term-display-type-wrap">
				<label for="wr_background_repeat"><?php esc_html_e( 'Background repeat', 'wr-nitro' ); ?></label>
				<select name="wr_background_repeat" id="wr_background_repeat">
					<option value="no-repeat"><?php esc_html_e( 'No Repeat', 'wr-nitro' ); ?></option>
					<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
					<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
					<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
				</select>
			</div>
			<div class="form-field term-display-type-wrap">
				<label for="wr_background_position"><?php esc_html_e( 'Background position', 'wr-nitro' ); ?></label>
				<select name="wr_background_position" id="wr_background_position">
					<option value="left top"><?php esc_html_e( 'Left Top', 'wr-nitro' ); ?></option>
					<option value="left center"><?php esc_html_e( 'Left Center', 'wr-nitro' ); ?></option>
					<option value="left bottom"><?php esc_html_e( 'Left Bottom', 'wr-nitro' ); ?></option>
					<option value="right top"><?php esc_html_e( 'Right Top', 'wr-nitro' ); ?></option>
					<option value="right center"><?php esc_html_e( 'Right Center', 'wr-nitro' ); ?></option>
					<option value="right bottom"><?php esc_html_e( 'Right Bottom', 'wr-nitro' ); ?></option>
					<option value="center top"><?php esc_html_e( 'Center Top', 'wr-nitro' ); ?></option>
					<option value="center center"><?php esc_html_e( 'Center Center', 'wr-nitro' ); ?></option>
					<option value="center bottom"><?php esc_html_e( 'Center Bottom', 'wr-nitro' ); ?></option>
				</select>
			</div>
			<div class="form-field term-display-type-wrap">
				<label for="wr_background_attachment"><?php esc_html_e( 'Background attachment', 'wr-nitro' ); ?></label>
				<select name="wr_background_attachment" id="wr_background_attachment">
					<option value="scroll"><?php esc_html_e( 'Scroll', 'wr-nitro' ); ?></option>
					<option value="fixed"><?php esc_html_e( 'Fixed', 'wr-nitro' ); ?></option>
				</select>
			</div>
		</div>

		<?php
	}

	/**
	 * Edit category thumbnail field.
	 *
	 * @param mixed $term Term (category) being edited
	 */
	public static function edit_category_fields( $term ) {

		$image_banner_id = absint( get_term_meta( $term->term_id, 'image_banner_id', true ) );
		$wr_background_size = esc_attr( get_term_meta( $term->term_id, 'wr_background_size', true ) );
		$wr_background_repeat = esc_attr( get_term_meta( $term->term_id, 'wr_background_repeat', true ) );
		$wr_background_position = esc_attr( get_term_meta( $term->term_id, 'wr_background_position', true ) );
		$wr_background_attachment = esc_attr( get_term_meta( $term->term_id, 'wr_background_attachment', true ) );

		if ( $image_banner_id ) {
			$image = wp_get_attachment_thumb_url( $image_banner_id );
		} else {
			$image = wc_placeholder_img_src();
		}
		?>

		<style type="text/css">
			.attr-cat-banner {
				clear: both;
			}
			.attr-cat-banner .item {
				margin-bottom: 20px;
			}
			.attr-cat-banner .item .title-attr {
				margin-bottom: 5px;
				font-size: 13px;
			}
			.attr-cat-banner .item select {
				width: 170px;
				margin: 0;
				font-size: 13px;
			}
		</style>

		<tr class="form-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Background image title', 'wr-nitro' ); ?></label></th>
			<td>
				<div id="product_cat_banner" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="product_cat_banner_id" name="product_cat_banner_id" value="<?php echo esc_attr( $image_banner_id ); ?>" />
					<button type="button" class="upload_image_banner_button button"><?php esc_html_e( 'Upload/Add image', 'wr-nitro' ); ?></button>
					<button type="button" class="remove_banner_button button"><?php esc_html_e( 'Remove image', 'wr-nitro' ); ?></button>
				</div>

				<div class="attr-cat-banner" <?php echo ( ! $image_banner_id ? 'style="display: none;"' : NULL ); ?> >
					<div class="item">
						<label>
							<div class="title-attr"><?php esc_html_e( 'Background size', 'wr-nitro' ); ?></div>
							<select name="wr_background_size">
								<option value="auto"><?php esc_html_e( 'Auto', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_size == 'cover' ? 'selected="selected"' : NULL ); ?> value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_size == 'contain' ? 'selected="selected"' : NULL ); ?> value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
							</select>
						</label>
					</div>
					<div class="item">
						<label>
							<div class="title-attr"><?php esc_html_e( 'Background repeat', 'wr-nitro' ); ?></div>
							<select name="wr_background_repeat">
								<option value="no-repeat"><?php esc_html_e( 'No Repeat', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_repeat == 'repeat' ? 'selected="selected"' : NULL ); ?> value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_repeat == 'repeat-x' ? 'selected="selected"' : NULL ); ?> value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_repeat == 'repeat-y' ? 'selected="selected"' : NULL ); ?> value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
							</select>
						</label>
					</div>
					<div class="item">
						<label>
							<div class="title-attr"><?php esc_html_e( 'Background position', 'wr-nitro' ); ?></div>
							<select name="wr_background_position">
								<option value="left top"><?php esc_html_e( 'Left Top', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_position == 'left center' ? 'selected="selected"' : NULL ); ?> value="left center"><?php esc_html_e( 'Left Center', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_position == 'left bottom' ? 'selected="selected"' : NULL ); ?> value="left bottom"><?php esc_html_e( 'Left Bottom', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_position == 'right top' ? 'selected="selected"' : NULL ); ?> value="right top"><?php esc_html_e( 'Right Top', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_position == 'right center' ? 'selected="selected"' : NULL ); ?> value="right center"><?php esc_html_e( 'Right Center', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_position == 'right bottom' ? 'selected="selected"' : NULL ); ?> value="right bottom"><?php esc_html_e( 'Right Bottom', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_position == 'center top' ? 'selected="selected"' : NULL ); ?> value="center top"><?php esc_html_e( 'Center Top', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_position == 'center center' ? 'selected="selected"' : NULL ); ?> value="center center"><?php esc_html_e( 'Center Center', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_position == 'center bottom' ? 'selected="selected"' : NULL ); ?> value="center bottom"><?php esc_html_e( 'Center Bottom', 'wr-nitro' ); ?></option>
							</select>
						</label>
					</div>
					<div class="item">
						<label>
							<div class="title-attr"><?php esc_html_e( 'Background attachment', 'wr-nitro' ); ?></div>
							<select name="wr_background_attachment">
								<option value="scroll"><?php esc_html_e( 'Scroll', 'wr-nitro' ); ?></option>
								<option <?php echo '' . ( $wr_background_attachment == 'fixed' ? 'selected="selected"' : NULL ); ?> value="fixed"><?php esc_html_e( 'Fixed', 'wr-nitro' ); ?></option>
							</select>
						</label>
					</div>
				</div>

				<script type="text/javascript">

					// Only show the "remove image" button when needed
					if ( '0' === jQuery( '#product_cat_banner_id' ).val() ) {
						jQuery( '.remove_banner_button, .attr-cat-banner' ).hide();
					}

					// Uploading files
					var file_frame_img_banner;

					jQuery( document ).on( 'click', '.upload_image_banner_button', function( event ) {

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( file_frame_img_banner ) {
							file_frame_img_banner.open();
							return;
						}

						// Create the media frame.
						file_frame_img_banner = wp.media.frames.downloadable_file = wp.media({
							title: '<?php esc_html_e( "Choose an image", "wr-nitro" ); ?>',
							button: {
								text: '<?php esc_html_e( "Use image", "wr-nitro" ); ?>'
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						file_frame_img_banner.on( 'select', function() {
							var attachment = file_frame_img_banner.state().get( 'selection' ).first().toJSON();

							jQuery( '#product_cat_banner_id' ).val( attachment.id );
							jQuery( '#product_cat_banner' ).find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
							jQuery( '.remove_banner_button, .attr-cat-banner' ).show();
						});

						// Finally, open the modal.
						file_frame_img_banner.open();
					});

					jQuery( document ).on( 'click', '.remove_banner_button', function() {
						jQuery( '#product_cat_banner' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
						jQuery( '#product_cat_banner_id' ).val( '' );
						jQuery( '.remove_banner_button, .attr-cat-banner' ).hide();
						return false;
					});

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Add product to list viewed
	 *
	 * @since  1.0
	 *
	 * @return  array
	 *
	 */
	public static function add_product_viewed( ) {

		if ( ! is_singular( 'product' ) ) {
			return;
		}

		global $post;

		if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) )
			$viewed_products = array();
		else
			$viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] );

		if ( ! in_array( $post->ID, $viewed_products ) ) {
			$viewed_products[] = $post->ID;
		}

		if ( sizeof( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only
		wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
	}

	/**
	 * Render icon switch layout in product list
	 *
	 * @since  1.0
	 *
	 * @return  array
	 *
	 */
	public static function switch_layout( ) {
		$wr_nitro_options = WR_Nitro::get_options();
		$wc_archive_style = esc_attr( $wr_nitro_options['wc_archive_style'] );

		if ( $wr_nitro_options['wc_archive_pagination_type'] == 'number' ) : ?>
			<div class="wc-switch clear nitro-line">
				<a title="<?php esc_html_e( 'Grid', 'wr-nitro' ); ?>" data-layout="<?php echo esc_attr( $wc_archive_style != 'list' ? $wc_archive_style : 'grid' ); ?>" class="wc-grid <?php echo esc_attr( $wc_archive_style != 'list' ? 'active' : NULL ); ?>" href="#"><i class="fa fa-th"></i></a>
				<a title="<?php esc_html_e( 'List', 'wr-nitro' ); ?>" data-layout="list" class="wc-list nitro-line <?php echo esc_attr( $wc_archive_style == 'list' ? 'active' : NULL ); ?>" href="#"><i class="fa fa-list"></i></a>
			</div>
		<?php endif;

		if ( wp_is_mobile() && $wr_nitro_options['wc_archive_mobile_sidebar'] ) {
			echo '<a href="javascript:void(0);" class="wc-show-sidebar bts-40 nitro-line btb mgl10"><i class="fa fa-filter"></i></a>';
		}
	}

	/**
	 * Switch layout gril and list in product list.
	 *
	 * @param array $wr_nitro_options
	 *
	 * @since  1.0
	 *
	 * @return  array
	 *
	 */
	public static function switch_layout_products( $wr_nitro_options ) {

		// Check if product list screen is toggled between list and grid view?
		if ( isset( $_GET['switch'] ) && in_array( $_GET['switch'] , array( 'grid', 'masonry', 'list' ) ) ) {
			$wr_nitro_options['wc_archive_style'] = esc_attr( $_GET['switch'] );
		}

		return $wr_nitro_options;
	}

	/**
	 * Remove wr page loader html.
	 *
	 * @return  NULL
	 */
	public static function empty_callback() {
		return NULL;
	}

	/**
	 * Remove wr page loader html.
	 *
	 * @return  link
	 */
	public static function checkout_order_received_url( $url ) {
		return $url . '&wr-buy-now=thankyou';
	}

	/**
	 * Remove custom styles.
	 *
	 * @return  string
	 */
	public static function wr_custom_styles() {
		$wr_nitro_options = WR_Nitro::get_options();
		$main_color = $wr_nitro_options['custom_color'];
		if ( empty( $main_color ) ) {
			$main_color = '#ff4064';
		}

		// Get button Settings
		$btn_solid = $btn_solid_hover = $btn_outline = $btn_outline_hover = '';
		$btn_font           = $wr_nitro_options['btn_font'];
		$btn_font_size      = $wr_nitro_options['btn_font_size'];
		$btn_line_height    = $wr_nitro_options['btn_line_height'];
		$btn_letter_spacing = $wr_nitro_options['btn_letter_spacing'];
		$btn_border_width   = $wr_nitro_options['btn_border_width'];
		$btn_border_radius  = $wr_nitro_options['btn_border_radius'];
		$btn_padding        = $wr_nitro_options['btn_padding'];
		$btn_primary_bg     = $wr_nitro_options['btn_primary_bg_color'];
		$btn_primary        = $wr_nitro_options['btn_primary_color'];

		$css = '
			body > *:not(.wrapper-outer),
			#wpadminbar,
			.header-outer,
			.footer,
			.site-title {
				display:none;
			}
			.wrapper > .container {
				margin: 0;
				padding: 15px;
			}
			.woocommerce-checkout .main-content .woocommerce {
				padding: 0;
			}
			body.logged-in {
				margin-top: -32px;
			}
			a {
				color: ' . esc_attr( $main_color ) . ';
			}
			.button {
				font-size: ' . esc_attr( $btn_font_size ) . 'px;
				height: ' . esc_attr( $btn_line_height) . 'px;
				line-height: ' . ( esc_attr( $btn_line_height) - esc_attr( $btn_border_width ) * 2 ) . 'px;
				letter-spacing: ' . esc_attr( $btn_letter_spacing ) . 'px;
				border: ' . esc_attr( $btn_border_width ) . 'px solid ' . esc_attr( $btn_primary_bg['normal'] ) . ';
				border-radius: ' . esc_attr( $btn_border_radius ) . 'px;
				padding: 0;
				background-color: ' . esc_attr( $btn_primary_bg['normal'] ) . ';
				color: ' . esc_attr( $btn_primary['normal'] ) . ';
			}
			.button:hover {
				background-color: ' . esc_attr( $btn_primary_bg['hover'] ) . ';
				border-color: ' . esc_attr( $btn_primary_bg['hover'] ) . ';
				color: ' . esc_attr( $btn_primary['hover'] ) . ';
			}

		';

		return $css;
	}

	/**
	 * General init action
	 *
	 * @since  1.0
	 *
	 * @return  void
	 *
	 */
	public static function general_wc_init() {
		$wr_nitro_options = WR_Nitro::get_options();

		// Get custom content options
		$position = $wr_nitro_options['wc_single_product_custom_content_position'];

		// Support slider for product gallery
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		// Get single style
		$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		if ( $single_style == 0 ) {
			$single_style = $wr_nitro_options['wc_single_style'];
		} else {
			$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		}

		// Get preview image zoom settings
		if ( ! wp_is_mobile() ) {
			$zoom = $wr_nitro_options['wc_single_image_zoom'];
			if ( $zoom && $single_style == '2' ) {
				add_theme_support( 'wc-product-gallery-zoom' );
			}
		}

		if ( isset( $_GET[ 'wr-buy-now' ] ) ) {
			// Remove wr page loader html
			add_filter( 'wr_page_loader', array( __CLASS__, 'empty_callback' ) );

			// Remove custom styles
			add_filter( 'wr_custom_styles', array( __CLASS__, 'wr_custom_styles' ) );

			// Remove custom styles
			add_filter( 'wr_header_custom_css', array( __CLASS__, 'empty_callback' ) );

			// Add value buy now to url Thank you page
			add_filter( 'woocommerce_get_checkout_order_received_url', array( __CLASS__, 'checkout_order_received_url' ), 10, 1 );
		}

		if ( $single_style !== '5' ) {

			switch ( $position ) {
				case 'after_title':

					add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'nitro_render_custom_content' ), 5 );

					break;

				case 'after_price':

					add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'nitro_render_custom_content' ), 12 );

					break;

				case 'after_except':

					add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'nitro_render_custom_content' ), 20 );

					break;

				case 'after_add_cart':

					if ( $single_style != '1') {
						add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'nitro_render_custom_content' ), 32 );
					}

					break;
			}
		}
	}

	/**
	 * Change number of products displayed per page.
	 *
	 * @since  1.0
	 *
	 * @return  number
	 *
	 */
	public static function change_product_per_page() {
		$wr_nitro_options = WR_Nitro::get_options();
		$number  = $wr_nitro_options['wc_archive_number_products'];

		return $number;
	}

	/**
	 * Add to cart message.
	 *
	 * @since  1.0
	 *
	 * @return  json
	 *
	 */
	public static function add_to_cart_message() {

		if ( ! ( isset( $_REQUEST['product_id'] ) && ( int ) $_REQUEST['product_id'] > 0 ) )
			return;

		$titles 	= array();
		$product_id = (int) $_REQUEST['product_id'];

		if ( is_array( $product_id ) ) {
			foreach ( $product_id as $id ) {
				$titles[] = get_the_title( $id );
			}
		} else {
			$titles[] = get_the_title( $product_id );
		}

		if ( $url = apply_filters( 'woocommerce_add_to_cart_redirect', false ) ) {
			$url_redirect_cart = $url;
		} else {
			$url_redirect_cart = wc_get_cart_url();
		}

		if ( isset( $_REQUEST['url_only'] ) && $_REQUEST['url_only'] == 'true' ) {
			echo esc_attr( $url_redirect_cart );

			exit;
		}

		$titles     = array_filter( $titles );
		$added_text = sprintf( _n( '<div><b>%s</b> has been added to your cart.</div>', '%s have been added to your cart.', sizeof( $titles ), 'wr-nitro' ), '"' . wc_format_list_of_items( $titles ) . '"' );
		$message    = sprintf( '%s <a href="%s" class="wc-forward db">%s</a>', wp_kses_post( $added_text ), $url_redirect_cart, esc_html__( 'View Cart', 'wr-nitro' ) );
		$data       = array( 'message' => apply_filters( 'wc_add_to_cart_message', $message, $product_id ) );

		wp_send_json( $data );

		exit;
	}

	/**
	 * Add to cart message error.
	 *
	 * @since  1.0
	 *
	 * @return  json
	 *
	 */
	public static function add_to_cart_error() {
		$all_notices = WC()->session->get( 'wc_notices', array() );
		wc_clear_notices();

		$message = isset( $all_notices[ 'error' ][ 0 ] ) ? $all_notices[ 'error' ][ 0 ] : '';
		$data    = array( 'message' => $message );

		wp_send_json( $data );

		exit();
	}

	/**
	 * Remove link cart redirect after error for case get notice error.
	 *
	 * @since  1.1.3
	 *
	 * @return  string
	 *
	 */
	public static function woocommerce_cart_redirect_after_error() {
		return '';
	}

	/**
	 * Add to cart message for product detail.
	 *
	 * @since  1.0
	 *
	 * @return  json
	 *
	 */
	public static function add_to_cart_message_product_detail( $product_id ) {
		$titles     = get_the_title( intval( $product_id ) );
		$added_text = sprintf( __( '%s has been added to your cart.', 'wr-nitro' ), '"' . $titles . '"' );
		$message    = sprintf( '<a href="%s" class="wc-forward">%s</a> %s', esc_url( wc_get_page_permalink( 'cart' ) ), esc_html__( 'View Cart', 'wr-nitro' ), esc_html( $added_text ) );
		$message    = apply_filters( 'wc_add_to_cart_message', $message, $product_id );

		return $message;
	}

	/**
	 * Delete product in cart by ajax.
	 *
	 * @since  1.0
	 *
	 * @return  json
	 *
	 */
	public static function product_remove() {
		$cart = WC()->instance()->cart;
		$cart_item_key = sanitize_title( $_POST['cart_item_key'] );

		if ( $cart_item_key ) {
			$cart->set_quantity( $cart_item_key,0 );
		}

		$print_r = array(
			'count_product' => WC()->cart->get_cart_contents_count(),
			'price_total' => WC()->cart->get_cart_subtotal()
		);

		// Show text empty if count = 0
		if ( $print_r['count_product'] == 0 ) {
			$print_r['empty'] = __( 'No products in the cart.', 'wr-nitro' );
		}

		echo json_encode( $print_r );

		exit();
	}

	/**
	 * Remove product in wishlish.
	 *
	 * @since  1.0
	 *
	 * @return  json
	 *
	 */
	public static function remove_product_wishlish() {
		if ( ! ( isset ( $_POST['product_id'] ) && isset( $_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], 'bb_wr_nitro' ) ) ) {
			wp_send_json ( array(
				'status' => 'false',
				'notice' => __( 'Not validate.', 'wr-nitro' )
			));
		}

		$product_id = intval( $_POST['product_id'] );

		$user_id = get_current_user_id();

		if( $user_id ) {
			global $wpdb;
			$sql = "DELETE FROM {$wpdb->yith_wcwl_items} WHERE user_id = %d AND prod_id = %d";
			$sql_args = array(
				$user_id,
				$product_id
			);
			$wpdb->query( $wpdb->prepare( $sql, $sql_args ) );
		} else {
			$wishlist = yith_getcookie( 'yith_wcwl_products' );
			foreach( $wishlist as $key => $item ){
				if( $item['prod_id'] == $product_id ){
					unset( $wishlist[ $key ] );
				}
			}
			yith_setcookie( 'yith_wcwl_products', $wishlist );
		}
		$data = array(
			'status' => 'true',
		);

		wp_send_json( $data );

		die();
	}

	/**
	 * Add product title to wishlist notice.
	 *
	 * @since  1.0
	 */
	public static function add_title_to_wishlist() {
		$product_id = isset( $_POST['add_to_wishlist'] ) ? intval( $_POST['add_to_wishlist'] ) : 0;

		if( ! $product_id ) return;

		$product_title = get_the_title( $product_id );

		return sprintf( __( '<b>%s</b> has been added to your Wishlist', 'wr-nitro' ), $product_title );
	}

	/**
	 * Customize quick buy button.
	 *
	 * @since  1.0
	 */
	public static function wr_quickbuy() {

		if ( ! ( isset ( $_POST['product_id'] ) && (int) $_POST['product_id'] > 0 ) ) {
			wp_send_json ( array(
				'status' => 'false',
				'notice' => __( 'Not validate.', 'wr-nitro' )
			));
		}

		// Get theme options
		$wr_nitro_options    = WR_Nitro::get_options();

		// Check is shortcode
		if( isset( $_POST['shortcode_checkout'] ) && isset( $_POST['shortcode_payment'] ) ) {
			$wc_buynow_checkout     = absint( $_POST['shortcode_checkout'] );
			$wc_buynow_payment_info = absint( $_POST['shortcode_payment'] );
			$wc_buynow_btn          = 1;

		// Get setting in customizer
		} else {
			$wc_buynow_checkout     = $wr_nitro_options['wc_buynow_checkout'];
			$wc_buynow_payment_info = $wr_nitro_options['wc_buynow_payment_info'];
			$wc_buynow_btn          = $wr_nitro_options['wc_buynow_btn'];
		}

		// Check turn on buy now button
		if ( $wc_buynow_btn == 1 ) {
			global $woocommerce;

			// Checkout Current Product Only
			if ( $wc_buynow_checkout == 1 ) {
				// Delete all products in cart
				WC()->cart->empty_cart( true );
			}

			$product_id = (int) $_POST['product_id'];

			// Add to cart
			$data = self::add_to_cart_action( $product_id, false );

			// Show Modal Popup
			if ( $wc_buynow_payment_info == 1 ) {
				$data[ 'type' ] = 'modal';

			// Redirect To Checkout Page
			} else if ( $wc_buynow_payment_info == 2 ) {
				$data[ 'type' ] = 'redirect';
			}

			$data[ 'checkout_url' ] = wc_get_checkout_url();

			wp_send_json ( $data );
		}

		wp_send_json ( array(
			'status' => 'false',
			'notice' => __( 'Not validate.', 'wr-nitro' )
		) );

		die();
	}

	/**
	 * Add to cart action.
	 *
	 * @param int $product_id
	 *
	 * @param bool $get_mini_cart
	 *
	 * @return array()
	 *
	 * @since  1.0
	 */
	private static function add_to_cart_action ( $product_id, $get_mini_cart = true ) {
		ob_start();

		// Call action front-end for yith-woocommerce-product-add-ons
		if( call_user_func( 'is_' . 'plugin' . '_active', 'yith-woocommerce-product-add-ons/init.php' ) ) {
			// $yith_wapo = new YITH_WAPO_Frontend( YITH_WAPO_VERSION );
		}

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
		$was_added_to_cart = false;
		$adding_to_cart    = wc_get_product( $product_id );

		if ( ! $adding_to_cart ) {
			return array(
				'status' => 'false',
				'notice' => __( 'Not validate.', 'wr-nitro' )
			);
		}

		$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->get_type(), $adding_to_cart );

		// Variable product handling
		if ( 'variable' === $add_to_cart_handler ) {
			$was_added_to_cart = self::add_to_cart_handler_variable ( $product_id );

		// Grouped Products
		} elseif ( 'grouped' === $add_to_cart_handler ) {
			$was_added_to_cart = self::add_to_cart_handler_grouped ( $product_id );

		// Simple Products
		} else {
			$was_added_to_cart = self::add_to_cart_handler_simple( $product_id );
		}

		// If we added the product to the cart we can now optionally do a redirect.
		if ( $was_added_to_cart && wc_notice_count( 'error' ) === 0 ) {

			// Fragments and mini cart are returned
			$notices_success = self::add_to_cart_message_product_detail( $product_id );

			$data = array(
				'status' 	=> 'true',
				'notice' 	=> $notices_success,
			);

			wc_clear_notices();

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			return $data;
		} else {
			$notices_error = wc_get_notices( 'error' );
			$data = array(
				'status' => 'false',
				'notice' => isset( $notices_error[0] ) ? $notices_error[0] : __( 'Not validate.', 'wr-nitro' ),
			);

			wc_clear_notices();

			return $data;
		}
	}

	/**
	 * Handle adding simple products to the cart.
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 *
	 * @since  1.0
	 */
	private static function add_to_cart_handler_simple( $product_id ) {
		$quantity 			= empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
		$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

		if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity ) !== false ) {
			wc_add_to_cart_message( $product_id );
			return true;
		}
		return false;
	}

	/**
	 * Handle adding variable products to the cart.
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 *
	 * @since  1.0
	 */
	private static function add_to_cart_handler_variable( $product_id ) {
		$adding_to_cart     = wc_get_product( $product_id );
		$variation_id       = empty( $_REQUEST['variation_id'] ) ? '' : absint( $_REQUEST['variation_id'] );
		$quantity           = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
		$missing_attributes = array();
		$variations         = array();
		$attributes         = $adding_to_cart->get_attributes();
		$variation          = wc_get_product( $variation_id );

		// Verify all attributes
		foreach ( $attributes as $attribute ) {
			if ( ! $attribute['is_variation'] ) {
				continue;
			}

			$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

			if ( isset( $_REQUEST[ $taxonomy ] ) ) {

				// Get value from post data
				if ( $attribute['is_taxonomy'] ) {
					// Don't use wc_clean as it destroys sanitized characters
					$value = sanitize_title( stripslashes( $_REQUEST[ $taxonomy ] ) );
				} else {
					$value = wc_clean( stripslashes( $_REQUEST[ $taxonomy ] ) );
				}

				// Get valid value from variation
				$valid_value = isset( $variation->variation_data[ $taxonomy ] ) ? $variation->variation_data[ $taxonomy ] : '';

				// Allow if valid
				if ( '' === $valid_value || $valid_value === $value ) {
					$variations[ $taxonomy ] = $value;
					continue;
				}

			} else {
				$missing_attributes[] = wc_attribute_label( $attribute['name'] );
			}
		}

		if ( $missing_attributes ) {
			wc_add_notice( sprintf( _n( '%s is a required field', '%s are required fields', sizeof( $missing_attributes ), 'wr-nitro' ), wc_format_list_of_items( $missing_attributes ) ), 'error' );
		} elseif ( empty( $variation_id ) ) {
			wc_add_notice( __( 'Please choose product options&hellip;', 'wr-nitro' ), 'error' );
		} else {
			// Add to cart validation
			$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

			if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) !== false ) {
				wc_add_to_cart_message( $product_id );
				return true;
			}
		}
		return false;
	}

	/**
	 * Handle adding grouped products to the cart.
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 *
	 * @since  1.0
	 */
	private static function add_to_cart_handler_grouped( $product_id ) {
		$was_added_to_cart = false;
		$added_to_cart     = array();

		if ( ! empty( $_REQUEST['quantity'] ) && is_array( $_REQUEST['quantity'] ) ) {
			$quantity_set = false;

			foreach ( $_REQUEST['quantity'] as $item => $quantity ) {
				if ( $quantity <= 0 ) {
					continue;
				}
				$quantity_set = true;

				// Add to cart validation
				$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $item, $quantity );

				if ( $passed_validation && WC()->cart->add_to_cart( $item, $quantity ) !== false ) {
					$was_added_to_cart = true;
					$added_to_cart[]   = $item;
				}
			}

			if ( ! $was_added_to_cart && ! $quantity_set ) {
				wc_add_notice( __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'wr-nitro' ), 'error' );
			} elseif ( $was_added_to_cart ) {
				wc_add_to_cart_message( $added_to_cart );
				return true;
			}

		} elseif ( $product_id ) {
			/* Link on product archives */
			wc_add_notice( __( 'Please choose a product to add to your cart&hellip;', 'wr-nitro' ), 'error' );
		}
		return false;
	}

	/**
	 * Register additional sidebar for WooCommerce.
	 *
	 * @since  1.0
	 */
	public static function widgets_init() {
		register_sidebar(
			array(
				'name'          => __( 'WooCommerce Sidebar', 'wr-nitro' ),
				'id'            => 'wc-sidebar',
				'description'   => __( 'Widgets in this area will be shown on shop page.', 'wr-nitro' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}

	/**
	 * Customize product quick view.
	 *
	 * @since  1.0
	 */
	public static function wr_quickview() {
		// Get product from request.
		if ( isset( $_POST['product'] ) && (int) $_POST['product'] ) {
			global $post, $product, $woocommerce;

			$id      = ( int ) $_POST['product'];
			$post    = get_post( $id );
			$product = function_exists('wc_get_product') ? wc_get_product($id) : get_product($id);

			if ( $product ) {

				// Get quickview template.
				wc_get_template( 'woorockets/product-quickview.php' );
			}
		}

		exit;
	}

	/**
	 * Customize product search form.
	 *
	 * @since  1.0
	 */
	public static function get_product_search_form( $form ) {
		$form = '
			<form class="widget-search" role="search" method="get" action="' . esc_url( home_url( '/'  ) ) . '">
				<input type="text" value="' . esc_attr( get_search_query() ) . '" name="s" placeholder="' . __( 'Search products...', 'wr-nitro' ) . '" />
				<button type="submit" class="search-submit"><i class="fa fa-search"></i></button>
				<input type="hidden" name="post_type" value="product" />
			</form>';

		return $form;
	}

	/**
	 * Customize product tabs.
	 *
	 * @since  1.0
	 */
	public static function woocommerce_product_tabs( $tabs = array() ) {
		global $product, $post;

		$wr_nitro_options = WR_Nitro::get_options();

		// Get product setting
		$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		if ( $single_style == 0 ) {
			$single_style = $wr_nitro_options['wc_single_style'];
		} else {
			$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		}

		$tab_description    = $wr_nitro_options['wc_single_product_tab_description'];
		$tab_additional     = $wr_nitro_options['wc_single_product_tab_info'];
		$tab_review         = $wr_nitro_options['wc_single_product_tab_review'];
		$wpc_disable_review = get_option( 'wpc_tab_show_hide' );

		// Description tab - shows product content
		if ( $post->post_content ) {
			$tabs['description'] = array(
				'title'    => __( 'Description', 'wr-nitro' ),
				'priority' => 10,
				'callback' => 'woocommerce_product_description_tab',
			);
		}

		// Additional information tab - shows attributes
		if ( $product && ( $product->has_attributes() || ( $product->has_dimensions() && ( $product->child_has_weight() || $product->has_weight() || $product->child_has_dimensions() ) ) ) ) {
			$tabs['additional_information'] = array(
				'title'    => __( 'Additional Information', 'wr-nitro' ),
				'priority' => 20,
				'callback' => 'woocommerce_product_additional_information_tab',
			);
		}

		// Reviews tab - shows comments
		if ( comments_open() ) {
			$tabs['reviews'] = array(
				'title'    => sprintf( __( 'Reviews (%d)', 'wr-nitro' ), $product->get_review_count() ),
				'priority' => 30,
				'callback' => 'comments_template',
			);
		}

		if ( '4' == $single_style && ! wp_is_mobile() ) {
			if ( $wr_nitro_options['wc_single_product_related'] ) {
				// Related products tab
				$tabs['related'] = array(
					'title'    => __( 'Related products', 'wr-nitro' ),
					'priority' => 40,
					'callback' => 'woocommerce_output_related_products',
				);
			}

			if ( $wr_nitro_options['wc_single_product_upsell'] ) {
				// Upsell products tab
				$tabs['upsell'] = array(
					'title'    => __( 'Upsell products', 'wr-nitro' ),
					'priority' => 50,
					'callback' => 'woocommerce_upsell_display',
				);
			}

			if ( $wr_nitro_options['wc_single_product_recent_viewed'] ) {
				// Recent viewed products tab
				$tabs['recent_viewed'] = array(
					'title'    => __( 'Recent viewed products', 'wr-nitro' ),
					'priority' => 60,
					'callback' => 'WR_Nitro_Pluggable_WooCommerce::woocommerce_recent_viewed_products',
				);
			}
		}

		// Enable VC page builder for single product
		$builder = get_post_meta( get_the_ID(), 'enable_builder', true );

		// Remove some default tabs
		if ( ! $tab_description || $builder ) {
			unset( $tabs['description'] );
		}
		if ( ! $tab_additional ) {
			unset( $tabs['additional_information'] );
		}
		if ( ! $tab_review ) {
			unset( $tabs['reviews'] );
		}

		return $tabs;
	}

	/**
	 * Add Recent viewed products
	 *
	 * @since  1.0
	 */
	public static function woocommerce_recent_viewed_products() {
		wc_get_template( 'single-product/recent-viewed.php' );
	}

	/**
	 * Customize social share in single product view.
	 *
	 * @since  1.0
	 */
	public static function woocommerce_share() {
		global $post;

		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		if ( ! $wr_nitro_options['wc_single_social_share'] ) return;

		// Get post thumbnail
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		?>
		<div class="product-share">
			<span class="fwb dib mgb10"><?php esc_html_e( 'Share this', 'wr-nitro' ); ?></span>
			<ul class="social-share clear pd0">
				<?php if ( $wr_nitro_options['social_network_share_facebook'] ) : ?>
					<li class="fl">
						<a class="db tc br-2 color-dark nitro-line" title="Facebook" href="http://www.facebook.com/sharer.php?u=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
							<i class="fa fa-facebook"></i>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $wr_nitro_options['social_network_share_twitter'] ) : ?>
					<li class="fl">
						<a class="db tc br-2 color-dark nitro-line" title="Twitter" href="https://twitter.com/share?url=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
							<i class="fa fa-twitter"></i>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $wr_nitro_options['social_network_share_google'] ) : ?>
					<li class="fl">
						<a class="db tc br-2 color-dark nitro-line" title="Googleplus" href="https://plus.google.com/share?url=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
							<i class="fa fa-google-plus"></i>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $wr_nitro_options['social_network_share_pinterest'] ) : ?>
					<li class="fl">
						<a class="db tc br-2 color-dark nitro-line" title="Pinterest" href="//pinterest.com/pin/create/button/?url=<?php esc_url( the_permalink() ); ?>&media=<?php echo esc_attr( $src[0] ); ?>&description=<?php echo wp_kses( get_the_title(), '' ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
							<i class="fa fa-pinterest"></i>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $wr_nitro_options['social_network_share_vk'] ) : ?>
					<li class="fl">
						<a class="db tc br-2 color-dark nitro-line" title="VK" href="http://vk.com/share.php?url==<?php esc_url( the_permalink() ); ?>&media=<?php echo esc_attr( $src[0] ); ?>&description=<?php echo wp_kses( get_the_title(), '' ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
							<i class="fa fa-vk"></i>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $wr_nitro_options['social_network_share_email'] ) : ?>
					<li class="fl">
						<a class="db tc br-2 color-dark nitro-line" title="Email" href="mailto:?subject=<?php the_title() ?>">
							<i class="fa fa-envelope"></i>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	<?php
	}

	/**
	 * Custom button of variable product.
	 *
	 * @since  1.0
	 */
	public static function change_single_variation_add_to_cart_button() {
		global $product;
		$wr_nitro_options = WR_Nitro::get_options();

		$check_gravityforms = WR_Nitro_Helper::check_gravityforms( $product->get_id() );

		$add_to_cart_ajax = true;
		if ( $check_gravityforms || ( get_option('woocommerce_enable_ajax_add_to_cart_single') == 'no' && ! (int) $wr_nitro_options['wc_buynow_btn'] ) ) {
			$add_to_cart_ajax = false;
		}

		?>
			<?php if ( wp_is_mobile() && $wr_nitro_options['wc_detail_mobile_sticky_cart'] ) echo '<div class="p-action-sticky body_bg fc jcc">'; ?>
			<div class="woocommerce-variation-add-to-cart variations_button clearfix">
				<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
				<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
				<input type="hidden" name="variation_id" class="variation_id" value="" />
				<?php
					/**
					 * @since 3.0.0.
					 */
					do_action( 'woocommerce_before_add_to_cart_quantity' );

					woocommerce_quantity_input( array( 'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 ) );

					/**
					 * @since 3.0.0.
					 */
					do_action( 'woocommerce_after_add_to_cart_quantity' );

					// Show compare
					$show_compare = $wr_nitro_options['wc_general_compare'];

					// Show wishlist
					$show_wishlist = $wr_nitro_options['wc_general_wishlist'];

					// Icon Set
					$icons = $wr_nitro_options['wc_icon_set'];

					$add_to_cart_button = '<button type="submit" class="' . ( $add_to_cart_ajax ? 'wr_single_add_to_cart_ajax' : NULL ) . ' variation single_add_to_cart_button wr_add_to_cart_button button alt btr-50 db pdl20 pdr20 fl mgr10 mgt10 br-3"><i class="nitro-icon-' . esc_attr( $icons ) . '-cart mgr10"></i>' . esc_html( $product->single_add_to_cart_text() ) . '</button>';

					// Add to cart button
					if ( ( $wr_nitro_options['wc_buynow_btn'] && ! $wr_nitro_options['wc_disable_btn_atc'] ) || ! $wr_nitro_options['wc_buynow_btn'] ) {
						echo wp_kses_post( $add_to_cart_button );
					}

					// Quick buy button
					if ( $wr_nitro_options['wc_buynow_btn'] && ! $check_gravityforms ) {
						echo '<button type="submit" class="variation single_buy_now wr_add_to_cart_button button alt btr-50 db pdl20 pdr20 bgd color-white fl mgr10 mgt10 br-3"><i class="fa fa-cart-arrow-down mgr10"></i><a class="color-white" href="#signle_variation_form">' . __( 'Buy now', 'wr-nitro' ) . '</a></button>';
					}

					// Add Wishlist button
					if ( class_exists( 'YITH_WCWL' ) && $show_wishlist && ! wp_is_mobile() ) {
						echo '<div class="wishlist-btn fl mgr10 mgt10 actions-button">' . do_shortcode( '[yith_wcwl_add_to_wishlist]' ) . '</div>';
					}

					// Add compare button
					if ( class_exists( 'YITH_WOOCOMPARE' ) && $show_compare ) {
						echo '
							<div class="product__compare icon_color fl actions-button mgt10">
								<a class="product__btn bts-50 mg0 db nitro-line btb pr" href="#"><i class="nitro-icon-' . esc_attr( $icons ) . '-compare"></i><span class="tooltip ab">' . esc_html__( 'Compare', 'wr-nitro' ) . '</span></a>
								<div class="hidden">' . do_shortcode( '[yith_compare_button container="no"]' ) . '</div>
							</div>
						';
					}

				?>
			</div>
			<?php if ( wp_is_mobile() && $wr_nitro_options['wc_detail_mobile_sticky_cart'] ) echo '</div>'; ?>
		<?php
	}

	/**
	 * Add sidebar before archive product.
	 *
	 * @since  1.0
	 */
	public static function update_before_archive_product() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();

		if ( wp_is_mobile() ) {
			$sidebar = $wr_nitro_options['wc_archive_mobile_content_before'];
		} else {
			$sidebar = $wr_nitro_options['wc_archive_content_before'];
		}

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-before-product-list mgb30">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Add sidebar after archive product.
	 *
	 * @since  1.0
	 */
	public static function update_after_archive_product() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();

		if ( wp_is_mobile() ) {
			$sidebar = $wr_nitro_options['wc_archive_mobile_content_after'];
		} else {
			$sidebar = $wr_nitro_options['wc_archive_content_after'];
		}

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-after-product-list mgt30">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Add sidebar before single product.
	 *
	 * @since  1.0
	 */
	public static function update_before_single_product() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		$sidebar = $wr_nitro_options['wc_single_content_before'];

		// Get single style
		$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		if ( $single_style == 0 ) {
			$single_style = $wr_nitro_options['wc_single_style'];
		} else {
			$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		}

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) && $single_style != 2 ) {
			echo '<div class="widget-before-product-detail">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Add sidebar after single product.
	 *
	 * @since  1.0
	 */
	public static function update_after_single_product() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		$sidebar = $wr_nitro_options['wc_single_content_after'];

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-after-product-detail mgt30">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Add sidebar before cart page.
	 *
	 * @since  1.0
	 */
	public static function update_before_cart_page() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		$sidebar = $wr_nitro_options['wc_cart_content_before'];

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-before-product-list mgb30">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Add sidebar after cart page.
	 *
	 * @since  1.0
	 */
	public static function update_after_cart_page() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		$sidebar = $wr_nitro_options['wc_cart_content_after'];

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-after-product-list mgt30">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Add sidebar after checkout page.
	 *
	 * @since  1.0
	 */
	public static function update_after_checkout_page() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		$sidebar = $wr_nitro_options['wc_checkout_content_after'];

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-before-checkout mgt30">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Mobile sidebar
	 * @since  2.0
	 */
	public static function woocommerce_mobile_sidebar() {
		$wr_nitro_options = WR_Nitro::get_options();
		$is_shop          = ( ( function_exists( 'is_shop' ) && is_shop() ) || is_post_type_archive( 'product' ) || ( function_exists( 'is_product_category' ) && is_product_category() ) || ( function_exists( 'is_product_tag' ) && is_product_tag() ) || ( function_exists( 'is_woocommerce' ) && is_woocommerce() && is_tax() ) );

		if ( wp_is_mobile() && $wr_nitro_options['wc_archive_mobile_sidebar'] && $is_shop ) : ?>
			<div id="shop-mobile-sidebar" class="mobile-sidebar">
					<?php dynamic_sidebar( $wr_nitro_options['wc_archive_mobile_sidebar_content'] ); ?>
			</div>
		<?php endif;
	}


	/**
	 * Floating add to cart button for single product.
	 * @since  1.0
	 * @param  String $product_id
	 * @return String
	 */
	public static function floating_add_to_cart( $product_id ) {
		global $product;

		$wr_nitro_options = WR_Nitro::get_options();

		// Icon Set
		$icons = $wr_nitro_options['wc_icon_set'];

		$add_to_cart = '<button type="submit" class="floating_button wr_add_to_cart_button button alt btr-50 db pdl20 pdr20 bgd color-white br-3"><i class="nitro-icon-' . esc_attr( $icons ) . '-cart mgr10"></i>' . $product->single_add_to_cart_text() .'</button>';

		return $add_to_cart;
	}

	/**
	 * Enqueue custom scripts and styles.
	 *
	 * @since  1.0
	 */
	public static function enqueue_scripts() {
		// Enqueue style for WooCommerce.
		wp_register_style( 'wr-nitro-woocommerce', get_template_directory_uri() . '/assets/woorockets/css/woocommerce.css' );
	}

	/**
	 * Customize check out template
	 *
	 * @since  1.0
	 */
	public static function customize_checkout_template() {
		ob_start();

		// Get checkout object
		$checkout = WC()->checkout();

		wc_get_template( 'checkout/checkout-simple.php', array( 'checkout' => $checkout ) );

	}

	/**
	 * Add custom fields to general settings
	 *
	 * @since  1.0
	 */
	public static function add_custom_general_fields() {

		global $woocommerce, $post;

		woocommerce_wp_checkbox(
			array(
				'id'            => '_show_countdown',
				'wrapper_class' => 'show_if_simple show_if_external show_if_sale_schedule',
				'label'         => __( 'Show Countdown Timer', 'wr-nitro' ),
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'          => '_message_product_sale',
				'label'       => __( 'Sale Message', 'wr-nitro' ),
				'placeholder' => 'The sale will end in....',
			)
		);

	}

	/**
	 * Add custom fields to options downloadable
	 *
	 * @since  1.0
	 */
	public static function add_custom_general_fields_options_downloadable() {

		global $woocommerce, $post;

		woocommerce_wp_text_input(
			array(
				'id'          => '_file_type',
				'label'       => __( 'File Type', 'wr-nitro' ),
				'placeholder' => 'transition, effect, loops ...',
				'description' => __( 'Enter the file type.', 'wr-nitro' )
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'          => '_file_format',
				'label'       => __( 'File Format', 'wr-nitro' ),
				'placeholder' => 'mp3, mp4, png, jpg, svg ...',
				'description' => __( 'Enter the file format.', 'wr-nitro' )
			)
		);

	}

	/**
	 * Save custom fields to general settings
	 *
	 * @since  1.0
	 */
	public static function add_custom_general_fields_save( $post_id ){

		$show_countdown = isset( $_POST['_show_countdown'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_show_countdown', $show_countdown );

		if ( isset( $_POST['_file_format'] ) ) {
			update_post_meta( $post_id, '_file_format', esc_attr( $_POST['_file_format'] ) );
		}
		if ( isset( $_POST['_file_type'] ) ) {
			update_post_meta( $post_id, '_file_type', esc_attr( $_POST['_file_type'] ) );
		}
		if ( isset( $_POST['_message_product_sale'] ) ) {
			update_post_meta( $post_id, '_message_product_sale', esc_attr( $_POST['_message_product_sale'] ) );
		}
	}

	/**
	 * Print Ajax URL for front-end.
	 *
	 * @since  1.0
	 */
	public static function wp_head() {
		echo '<scr' . 'ipt>'; ?>
			var WRAjaxURL 	    = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';
			var WR_CURRENT_URL 	= '<?php echo get_permalink(); ?>';
			var WR_URL 	        = '<?php echo esc_js( site_url() ); ?>';
			var _nonce_wr_nitro = '<?php echo wp_create_nonce( 'bb_wr_nitro' ); ?>';
			var _WR_THEME_URL   = '<?php echo get_template_directory_uri(); ?>';
			var WR_CART_URL    = '<?php echo wc_get_page_permalink( 'cart' ); ?>';
		<?php echo '</scr' . 'ipt>';
	}

	/**
	 * Remove some settings unused of plugin YITH Compare premium.
	 *
	 * @since  1.1.6
	 */
	public static function remove_settings_yith_compare_premium( $options ) {
		if ( function_exists( 'yith_woocompare_premium_constructor' ) ) {
			$options = array(
				'general' => array(
					array(
						'name' => __( 'General Settings', 'wr-nitro' ),
						'type' => 'title',
						'desc' => '',
						'id'   => 'yith_woocompare_general'
					),
					array(
						'name'    => __( 'Open lightbox automatically', 'wr-nitro' ),
						'desc'    => __( 'Open the link after clicking on the "Compare" button.', 'wr-nitro' ),
						'id'      => 'yith_woocompare_auto_open',
						'std'     => 'yes',
						'default' => 'yes',
						'type'    => 'checkbox'
					),
					array(
						'name'    => __( 'Open lightbox when adding a second item', 'wr-nitro' ),
						'desc'    => __( 'Open the comparison lightbox after adding a second item to compare.', 'wr-nitro' ),
						'id'      => 'yith_woocompare_open_after_second',
						'std'     => 'no',
						'default' => 'no',
						'type'    => 'checkbox'
					),
					array(
						'name'    => __( 'Compare by category', 'wr-nitro' ),
						'desc'    => __( 'Compare products by category.', 'wr-nitro' ),
						'id'      => 'yith_woocompare_use_category',
						'std'     => 'no',
						'default' => 'no',
						'type'    => 'checkbox'
					),
					array(
						'name' => __( 'Exclude category', 'wr-nitro' ),
						'desc' => __( 'Choose category to exclude from the comparison.', 'wr-nitro' ),
						'id'   => 'yith_woocompare_excluded_category',
						'type' => 'yith_woocompare_select_cat'
					),
					array(
						'name'    => __( 'Reverse exclusion list', 'wr-nitro' ),
						'desc'    => __( 'Only categories in the exclusion list will have the compare feature', 'wr-nitro' ),
						'id'      => 'yith_woocompare_excluded_category_inverse',
						'type'    => 'checkbox',
						'std'     => 'no',
						'default' => 'no',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'yith_woocompare_general_end'
					)
				)
			);

			return $options;
		}
	}

	/**
	 * Set cookie when submit for reset password
	 *
	 * @since  2.0.0
	 */
	public static function setcookie_first_reset_pass() {
		setcookie( 'add_notice_reset_pass', 1, 0, '/' );
	}

	/**
	 * Show notice reset pass.
	 *
	 * @since  2.0.0
	 */
	public static function show_notice_reset_pass() {
		if( isset( $_COOKIE[ 'add_notice_reset_pass' ] ) ) {
			if( $_COOKIE[ 'add_notice_reset_pass' ] == 1 ) {
				wc_add_notice( __( 'Password reset email has been sent.', 'wr-nitro' ), 'success' );
				setcookie( 'add_notice_reset_pass', 2, 0, '/' );
			} else {
				setcookie( 'add_notice_reset_pass', 3, 0, '/' );
			}
		}
	}

	/**
	 * Variable for cart action notice when add to cart or edit cart.
	 *
	 * @var  string
	 */
	protected static $cart_action_notice;

	/**
	 * Print cart notice.
	 *
	 * @since  1.2.5
	 *
	 * @return  string
	 *
	 */
	public static function print_cart_notice() {
		echo self::$cart_action_notice;
	}

	/**
	 * Add product to cart.
	 *
	 * @since  1.2.5
	 *
	 * @return  json
	 *
	 */
	public static function add_to_cart_ajax() {
		if( ! isset(  $_REQUEST['add-to-cart'] ) ) {
			return;
		}

		$product_id = intval( $_REQUEST['add-to-cart'] );

		if ( wc_notice_count( 'error' ) === 0 ) {
			// Fragments and mini cart are returned
			$notices_success = self::add_to_cart_message_product_detail( $product_id );

			$data = array(
				'status' 	=> 'true',
				'notice' 	=> $notices_success,
			);
		} else {
			$notices_error = wc_get_notices( 'error' );

			$data = array(
				'status' => 'false',
				'notice' => isset( $notices_error[0] ) ? $notices_error[0] : __( 'Not validate.', 'wr-nitro' ),
			);
		}

		// Clear all messages.
		wc_clear_notices();

		echo '<script id="tp-notice-html" type="text/html">' . json_encode( $data ) . '</script>';

		// Exit immediately to prevent WordPress from processing further.
		exit;
	}

	/**
	 * Update count product item in cart.
	 *
	 * @since  1.2.5
	 *
	 * @return  json
	 *
	 */
	public static function update_cart_ajax() {
		if( WC()->cart->is_empty() ) {
			return;
		}

		$cart = WC()->cart->get_cart();

		if( ! empty( $_REQUEST['cart_item_key'] ) && ! empty( $_REQUEST['cart_item_number'] ) && isset( $cart[ $_REQUEST['cart_item_key'] ] ) ) {
			$cart_item_key = esc_attr( $_REQUEST['cart_item_key'] );
			$cart_item_number = esc_attr( $_REQUEST['cart_item_number'] );

			$_product = $cart[ $cart_item_key ]['data'];

			$cart_updated = false;

			// Sanitize
			$quantity = apply_filters( 'woocommerce_stock_amount_cart_item', wc_stock_amount( preg_replace( "/[^0-9\.]/", '', $cart_item_number ) ), $cart_item_key );

			if ( '' === $quantity || $cart_item_number == $cart[ $cart_item_key ]['quantity'] ) {
				return;
			}

			// Update cart validation
			$passed_validation 	= apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $_product, $quantity );

			// is_sold_individually
			if ( $_product->is_sold_individually() && $quantity > 1 ) {
				// wc_add_notice( sprintf( __( 'You can only have 1 %s in your cart.', 'woocommerce' ), $_product->get_title() ), 'error' );
				$passed_validation = false;
			}

			if ( $passed_validation ) {
				WC()->cart->set_quantity( $cart_item_key, $quantity, false );
				$cart_updated = true;
			}

			// Trigger action - let 3rd parties update the cart if they need to and update the $cart_updated variable
			$cart_updated = apply_filters( 'woocommerce_update_cart_action_cart_updated', $cart_updated );

			if ( $cart_updated ) {
				// Recalc our totals
				WC()->cart->calculate_totals();

				$cart = WC()->cart->get_cart();
				$_product = apply_filters( 'woocommerce_cart_item_product', $cart[ $cart_item_key ]['data'], $cart[ $cart_item_key ], $cart_item_key );
				$data = array(
					'count_product' => WC()->cart->get_cart_contents_count(),
					'price_total'   => WC()->cart->get_cart_subtotal(),
				);

				// Show text empty if count = 0
				if ( $data['count_product'] == 0 ) {
					$data['empty'] = __( 'No products in the cart.', 'wr-nitro' );
				}

				wc_clear_notices();

				wp_send_json( $data );
				die;
			}
		}
	}

	/**
	 * Add to cart by ajax
	 *
	 * @since  1.2.5
	 */
	public static function action_cart_ajax() {
		if ( ! empty( $_REQUEST['wr-action-cart'] ) ) {
			if( $_REQUEST['wr-action-cart'] == 'add_to_cart' ) {
				self::add_to_cart_ajax();
			} elseif( $_REQUEST['wr-action-cart'] == 'update_cart' ) {
				self::update_cart_ajax();
			}
		}
	}

	/**
	 * Custom flexslider.
	 *
	 * @return array
	 * @since  1.3.8
	 */
	public static function custom_flexslider() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();

		// Get single style
		$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		if ( $single_style == 0 ) {
			$single_style = $wr_nitro_options['wc_single_style'];
		} else {
			$single_style = get_post_meta( get_the_ID(), 'single_style', true );
		}

		// Get thumbnail position
		$thumb_position = $wr_nitro_options['wc_single_thumb_position'];

		$flexslider = array(
			'rtl'            => is_rtl(),
			'animation'      => 'slide',
			'smoothHeight'   => false,
			'slideshow'      => false,
			'directionNav'   => false,
			'controlNav'     => false,
			'animationSpeed' => 500,
			'animationLoop'  => false
		);

		if ( $single_style == 1 ) {
			$flexslider['directionNav'] = true;
		}

		if ( $single_style == 2 ) {
			if ( $thumb_position == 'bottom' ) {
				$flexslirder['sync'] = '.woocommerce-product-gallery--with-nav';
			} else {
				$flexslider['controlNav'] = 'thumbnails';
			}
		}

		if ( $single_style == 3 ) {
			$flexslider['controlNav'] = 'thumbnails';
			$flexslider['animation'] = 'fade';
		}

		if ( $single_style == 4 || wp_is_mobile() ) {
			$flexslider['controlNav'] = true;
		}

		return $flexslider;
	}

	/**
	 * Clear cart.
	 *
	 * @return  void
	 */
	public static function clear_cart() {
		// Verify nonce.
		if ( ! wp_verify_nonce($_REQUEST['_nonce'], 'bb_wr_nitro') ) {
			wp_send_json_error('Nonce verification failed.');
		}

		// Clear cart.
		WC()->cart->empty_cart( true );

		wp_send_json_success();
	}

	/**
	 * Handle cart updated action to support 'Buy Now' feature.
	 *
	 * @return  void
	 */
	public static function handle_cart_updated() {
		if ( isset($_REQUEST['buy_now']) && (int) $_REQUEST['buy_now'] ) {
			// Get theme options.
			$wr_nitro_options = WR_Nitro::get_options();

			if ( $wr_nitro_options['wc_buynow_payment_info'] == 2 ) {
				wp_redirect( wc_get_checkout_url() );

				exit;
			} else {
				$GLOBALS['wr_in_buy_now_process'] = true;
			}
		}
	}

	/**
	 * Render custom content in product single page
	 *
	 * @return void
	 */
	public static function nitro_render_custom_content() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();

		$content = $wr_nitro_options['wc_single_product_custom_content_data'];
		if ( ! empty( $content ) ) {
			echo '<div class="custom-content">'.do_shortcode( $content ).'</div>';
		}
	}

	/**
	 * Change number of related products on product page
	 *
	 * @param array $args post
	 * @return array;
	 */
	public static function wr_related_products_args( $args ) {
		// Get theme options.
		$wr_nitro_options = WR_Nitro::get_options();
		$limit = $wr_nitro_options['wc_single_product_show'];

		$args['posts_per_page'] = $limit;

		return $args;
	}

	/**
	 * Render custom sale message in single page
	 *
	 * @return void
	 */
	public static function nitro_message_sale() {
		global $product;
		$is_sale 			= $product->is_on_sale();
		$product_id 		= $product->get_id();

		//Get text field message in product sale
		$mes = get_post_meta( $product_id, '_message_product_sale', true );
		if ( $mes != '' && $is_sale) {
			echo '<div class="message-on-sale">' . $mes . '</div>';
		}
	}

	/**
	 * Prepare data for WooCommerce Products Filter.
	 *
	 * @param   array  $data  WooCommerce Products Filter data.
	 *
	 * @return  array
	 */
	public static function woof_get_request_data($data) {
		foreach ($data as $k => $v) {
			$data[$k] = addslashes( stripslashes($v) );
		}

		return $data;
	}

    /**
     * Update WooCommerce Flexslider options.
     *
     * @return  array
     */
    public static function nitro_update_woo_flexslider_options($options) {
        if( empty($options) ){
            $options = array(
                'rtl'            => is_rtl(),
                'animation'      => 'slide',
                'smoothHeight'   => true,
                'directionNav'   => false,
                'controlNav'     => 'thumbnails',
                'slideshow'      => true,
                'animationSpeed' => 500,
                'animationLoop'  => false, // Breaks photoswipe pagination if true.
                'allowOneSlide'  => false,
            );
        }

        return $options;
    }

    /**
     * Method to refine video URL associated with a product.
     *
     * @param   string  $link  Video URL.
     *
     * @return  string
     */
    public function refine_video_url($link) {
        if (preg_match('#^https?://(www\.)?youtube\.com/embed/(.+)#', $link, $match)) {
            $link = 'https://www.youtube.com/watch?v=' . $match[2];
        }

        return $link;
    }
}
