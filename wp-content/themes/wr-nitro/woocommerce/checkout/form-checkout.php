<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get theme options
$wr_nitro_options = WR_Nitro::get_options();
$sidebar = $wr_nitro_options['wc_checkout_content_before'];

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', wc_get_checkout_url() ); ?>

<div class="form-container">
	<?php
		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-before-checkout mgb30">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	?>
	<div class="checkout-notices">
		<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
	</div>

	<form name="checkout" method="post" class="checkout woocommerce-checkout row<?php echo ( ( isset( $_GET['wr-buy-now'] ) && $_GET['wr-buy-now'] == 'check-out' ) ? ' in-modal' : '' ); ?>" action="<?php echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data">

		<div class="cm-6 cs-12">
			<div class="col-left">
				<?php
					// If checkout registration is disabled and not logged in, the user cannot checkout
					if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
						echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', esc_html__( 'You must be logged in to checkout.', 'wr-nitro' ) );
						return;
					}
				?>

				<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<div class="col2-set" id="customer_details">
						<div class="row-1">
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
						</div>

						<div class="row-2">
							<?php do_action( 'woocommerce_checkout_shipping' ); ?>
						</div>
					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

				<?php endif; ?>
			</div>
		</div>
		<div class="cm-6 cs-12 pdr50 pdl50">
			<div class="col-right">
				<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'wr-nitro' ); ?></h3>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</div>
		</div>

	</form>

	<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</div>
