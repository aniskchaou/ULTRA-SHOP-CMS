<?php
/**
 * Plugin Name: Nitro Toolkit
 * Plugin URI:  http://www.woorockets.com
 * Description: Nitro toolkit for Nitro theme. Currently supports the following theme functionality: shortcodes, CPT.
 * Version:     1.1.13
 * Author:      WooRockets Team <support@www.woorockets.com>
 * Author URI:  http://www.woorockets.com
 * License:     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: nitro-toolkit
 */

// Define url to this plugin file.
define( 'NITRO_TOOLKIT_URL', plugin_dir_url( __FILE__ ) );

// Define path to this plugin file.
define( 'NITRO_TOOLKIT_PATH', plugin_dir_path( __FILE__ ) );

// Include function plugins if not include.
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

//Create Add to cart
function nitro_add_to_cart_shortcode($atts) {
    global $post;

    if ( empty( $atts ) ) {
        return '';
    }

    $atts = shortcode_atts( array(
        'id'         => '',
        'class'      => '',
        'quantity'   => '1',
        'sku'        => '',
        'style'      => 'border:4px solid #ccc; padding: 12px;',
        'show_price' => 'true',
    ), $atts, 'product_add_to_cart' );

    if ( ! empty( $atts['id'] ) ) {
        $product_data = get_post( $atts['id'] );
    } elseif ( ! empty( $atts['sku'] ) ) {
        $product_id   = wc_get_product_id_by_sku( $atts['sku'] );
        $product_data = get_post( $product_id );
    } else {
        return '';
    }

    $product = is_object( $product_data ) && in_array( $product_data->post_type, array( 'product', 'product_variation' ), true ) ? wc_setup_product_data( $product_data ) : false;

    if ( ! $product ) {
        return '';
    }

    ob_start();

    echo '<p class="product woocommerce add_to_cart_inline ' . esc_attr( $atts['class'] ) . '" style="' . ( empty( $atts['style'] ) ? '' : esc_attr( $atts['style'] ) ) . '">';

    if ( wc_string_to_bool( $atts['show_price'] ) ) {
        // @codingStandardsIgnoreStart
        echo $product->get_price_html();
        // @codingStandardsIgnoreEnd
    }

    woocommerce_template_loop_add_to_cart( array(
        'quantity' => $atts['quantity'],
    ) );

    echo '</p>';

    // Restore Product global in case this is shown inside a product post.
    wc_setup_product_data( $post );

    return ob_get_clean();
}
add_shortcode( 'nitro_add_to_cart', 'nitro_add_to_cart_shortcode' );

/**
 * Load plugin textdomain.
 *
 * @since 1.0.3
 */
function nitro_toolkit_load_textdomain() {
	load_plugin_textdomain( 'nitro-toolkit', false, dirname( plugin_basename(__FILE__) ) . '/languages' );
}
add_action( 'init', 'nitro_toolkit_load_textdomain' );

// Load basic initialization
include_once( NITRO_TOOLKIT_PATH . '/includes/base.php' );

// Run shortcode in widget text
add_filter( 'widget_text', 'do_shortcode' );

// Register custom shortcodes
if ( class_exists( 'Vc_Manager' ) ) {
	include_once( NITRO_TOOLKIT_PATH . '/includes/shortcode.php' );
}

// Register custom post types
include_once( NITRO_TOOLKIT_PATH . '/includes/post-type.php' );
