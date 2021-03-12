<?php
/**
 * Content wrappers
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/wrapper-end.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Get theme options
$wr_nitro_options = WR_Nitro::get_options();

$cols = $html = '';

// Wrap container if not single product page
if ( ! is_product() ) :
	$html = '</div>';
endif;

// Get settings
$layout = $wr_nitro_options['wc_archive_layout'];
$sticky = $wr_nitro_options['wc_archive_sidebar_sticky'];
$style  = $wr_nitro_options['w_style'];
?>
			</main><!-- .shop-main -->

			<?php if ( ! is_product() && 'no-sidebar' != $layout && ! wp_is_mobile() ) : ?>

				<div id="shop-sidebar" class="primary-sidebar<?php if ( $sticky == true ) echo ' primary-sidebar-sticky'; ?><?php if ( is_shop() ) echo ' archive-sidebar'; ?> widget-style-<?php echo esc_attr( $style ) . ' ' . ( is_customize_preview() ? 'customizable customize-section-widget_styles ' : '' ); ?>">
					<?php if ( $sticky == true ) echo '<div class="primary-sidebar-inner">'; ?>
						<?php dynamic_sidebar( 'wc-sidebar' ); ?>
					<?php if ( $sticky == true ) echo '</div>'; ?>
				</div>

			<?php endif; ?>
		<?php if ( ! is_singular( 'product' ) ) echo '</div>'; ?>
	</div><!-- .row -->
<?php echo wp_kses_post( $html ); ?>
