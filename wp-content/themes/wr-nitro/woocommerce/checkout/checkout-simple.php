<?php
/**
 * Checkout Form Simple
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', wc_get_checkout_url() ); ?>

<div class="form-container">
	<div class="checkout-notices">
		<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
	</div>

	<form name="checkout" method="post" class="checkout woocommerce-checkout row" action="<?php echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data">

		<div class="cm-6">
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

					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

				<?php endif; ?>
			</div>
		</div>
		<div class="cm-6">
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
