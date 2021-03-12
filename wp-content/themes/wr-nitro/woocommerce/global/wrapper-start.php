<?php
/**
 * Content wrappers
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/wrapper-start.php.
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

$layout     = $wr_nitro_options['wc_archive_layout'];
$fullwidth  = $wr_nitro_options['wc_archive_full_width'];

$html = '';

// Wrap container if not single product page
if ( $fullwidth ) {
	$html = '<div class="archive-full-width">';
} elseif ( ! is_product() ) {
	$html = '<div class="container">';
}
?>
	<?php echo wp_kses_post( $html ); ?>
		<div class="row">
		<?php echo ( ! is_singular( 'product' ) && $layout == 'right-sidebar'  ) ? '<div class="fc fcw mgt30 mgb30 menu-on-right">' : '<div class="fc fcw mgt30 mgb30 single-wrap">'; ?>
				<main id="shop-main" class="main-content<?php if ( is_shop() ) echo ' archive-shop'; ?><?php if ( is_shop() && $layout == 'right-sidebar'  ) echo ' right-sidebar'; ?>">


