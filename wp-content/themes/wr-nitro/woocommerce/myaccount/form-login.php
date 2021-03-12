<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$args = array( 'form' => 'lost_password' );

$wr_class_login = $wr_class_register = $wr_class_password = '';

$wr_nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
$wr_nonce_value = isset( $_POST['woocommerce-register-nonce'] ) ? $_POST['woocommerce-register-nonce'] : $wr_nonce_value;

// Register
if ( ! empty( $_POST['register'] ) && wp_verify_nonce( $wr_nonce_value, 'woocommerce-register' ) ) {
	$wr_class_login = $wr_class_register = 'opened';
}

// Lost password
if ( isset( $_POST['wc_reset_password'] ) && isset( $_POST['user_login'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'lost_password' ) ) {
	$wr_class_login = $wr_class_password = 'opened';
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<div class="row" id="customer_login">

	<div class="form-container login <?php echo esc_attr( $wr_class_login ); ?>">
		<form class="woocommerce-form woocommerce-form-login login" method="post" id="login">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<div class="form-row form-row-wide">
				<div class="username"><input type="text" class="input-text" name="username" id="username" placeholder="<?php esc_attr_e( 'Username', 'wr-nitro' ); ?>" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" /></div>
			</div>
			<div class="form-row form-row-wide">
				<div class="password"><input class="input-text" type="password" placeholder="<?php esc_attr_e( '********', 'wr-nitro' ); ?>" name="password" id="password" /></div>
			</div>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<div class="form-row">
				<?php wp_nonce_field( 'woocommerce-login' ); ?>
				<input type="submit" class="button" name="login" value="<?php esc_attr_e( 'Login', 'wr-nitro' ); ?>" />
			</div>

			<div class="form-row">
				<span class="inline rememberme">
					<input name="rememberme" type="checkbox" checked="checked" id="rememberme" value="forever" />
					<label for="rememberme"><?php esc_html_e( 'Keep me logged in', 'wr-nitro' ); ?></label>
				</span>
			</div>
			<?php do_action( 'woocommerce_login_form_end' ); ?>
		</form>
		<div class="form-row user-link pa overlay_bg">
			<span><a class="btn-lostpw" href="javascript:void(0)"><i class="fa fa-key"></i> <?php esc_html_e( 'Forgot your password?', 'wr-nitro' ); ?></a></span>
			<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
				<span><a href="javascript:void(0);" class="btn-newacc"><i class="fa fa-user"></i><?php esc_html_e( 'Create a new Account', 'wr-nitro' ); ?></a></span>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
		<div class="form-container register <?php echo esc_attr( $wr_class_register ); ?>">
			<form class="register" method="post" id="register">

				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

					<div class="form-row form-row-wide">
						<label for="reg_username"><?php esc_html_e( 'Username', 'wr-nitro' ); ?> <span class="required">*</span></label>
						<div class="username"><input type="text" placeholder="<?php esc_attr_e( 'Username', 'wr-nitro' ); ?>" class="input-text" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" /></div>
					</div>

				<?php endif; ?>

				<div class="form-row form-row-wide">
					<div class="email"><input type="email" placeholder="<?php esc_attr_e( 'Email', 'wr-nitro' ); ?>" class="input-text" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" /></div>
				</div>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

					<div class="form-row form-row-wide">
						<div class="password"><input type="password" placeholder="<?php esc_attr_e( '********', 'wr-nitro' ); ?>" class="input-text" name="password" id="reg_password" /></div>
					</div>

				<?php endif; ?>

				<!-- Spam Trap -->
				<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php esc_html_e( 'Anti-spam', 'wr-nitro' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

				<?php do_action( 'woocommerce_register_form' ); ?>
				<?php do_action( 'register_form' ); ?>

				<div class="form-row">
					<?php wp_nonce_field( 'woocommerce-register' ); ?>
					<input type="submit" class="button" name="register" value="<?php esc_attr_e( 'Create Account', 'wr-nitro' ); ?>" />
				</div>

				<?php do_action( 'woocommerce_register_form_end' ); ?>
			</form>
			<div class="form-row user-link pa overlay_bg">
				<a class="btn-backacc" href="javascript:void(0)"><?php esc_html_e( 'You already have an account ? Back to login page', 'wr-nitro' ); ?></a>
			</div>
		</div>
	<?php endif; ?>

	<div class="form-container lost-password <?php echo esc_attr( $wr_class_password ); ?>">
		<form method="post" class="lost_reset_password">

			<?php if ( 'lost_password' === $args['form'] ) : ?>

				<div><p class="mgb30"><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'wr-nitro' ) ); ?></p></div>

				<div class="form-row form-row-wide form-row-first">
					<div class="username"><input type="text" class="input-text" name="user_login" id="user_login" placeholder="<?php esc_attr_e( 'Username or email', 'wr-nitro' ); ?>" /></div>
				</div>

			<?php else : ?>

				<div><?php echo apply_filters( 'woocommerce_reset_password_message', esc_html__( 'Enter a new password below.', 'wr-nitro') ); ?></div>

				<div class="form-row form-row-first">
					<label for="password_1"><?php esc_html_e( 'New password', 'wr-nitro' ); ?> <span class="required">*</span></label>
					<input type="password" class="input-text" name="password_1" id="password_1" />
				</div>
				<div class="form-row form-row-last">
					<label for="password_2"><?php esc_html_e( 'Re-enter new password', 'wr-nitro' ); ?> <span class="required">*</span></label>
					<input type="password" class="input-text" name="password_2" id="password_2" />
				</div>

				<input type="hidden" name="reset_key" value="<?php echo isset( $args['key'] ) ? $args['key'] : ''; ?>" />
				<input type="hidden" name="reset_login" value="<?php echo isset( $args['login'] ) ? $args['login'] : ''; ?>" />

			<?php endif; ?>

			<div class="clear"></div>

			<?php do_action( 'woocommerce_lostpassword_form' ); ?>

			<div class="form-row form-row-wide">
				<input type="hidden" name="wc_reset_password" value="true" />
				<input type="submit" class="button" value="<?php echo 'lost_password' === $args['form'] ? esc_attr__( 'Reset Password', 'wr-nitro' ) : esc_attr__( 'Save', 'wr-nitro' ); ?>" />
			</div>

			<?php wp_nonce_field( $args['form'] ); ?>

		</form>
		<div class="form-row user-link pa overlay_bg">
			<a class="btn-backacc" href="javascript:void(0)"><?php esc_html_e( 'Back to login page', 'wr-nitro' ); ?></a>
		</div>
	</div>

</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
