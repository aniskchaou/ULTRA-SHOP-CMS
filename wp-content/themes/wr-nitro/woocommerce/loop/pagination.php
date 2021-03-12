<?php
/**
 * Pagination - Show numbered pagination for catalog pages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/pagination.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;

$wr_nitro_options = WR_Nitro::get_options();

$total   = isset( $total ) ? $total : wc_get_loop_prop( 'total_pages' );
$current = isset( $current ) ? $current : wc_get_loop_prop( 'current_page' );
$base    = isset( $base ) ? $base : esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
$format  = isset( $format ) ? $format : '';

if ( $total <= 1 ) {
	return;
}

// Pagination type
$type = $wr_nitro_options['wc_archive_pagination_type'];

// Pagination style
$style = $wr_nitro_options['pagination_style'];

// Style of list product
$layout_style = $wr_nitro_options['wc_archive_style'];

if ( 'loadmore' == $type || 'infinite' == $type ) : ?>

	<div class="pagination wc-pagination" layout="<?php echo esc_attr( $type ); ?>" layout-style="<?php echo esc_attr( $layout_style ); ?>">
		<div class="page-ajax enable" data-page="<?php echo esc_attr( $wp_query->max_num_pages ); ?>">
			<?php echo next_posts_link( '...' ); ?>
		</div>
	</div>

<?php else : ?>

	<nav class="woocommerce-pagination nitro-line <?php echo esc_attr( $style ) . ' ' . ( is_customize_preview() ? 'customizable customize-section-pagination ' : '' ); ?>">
		<?php
			$end_max_size = wp_is_mobile() ? 1 : 3;
			
			echo paginate_links(
				apply_filters(
					'woocommerce_pagination_args',
					array(
						'base'      => $base,
						'format'    => $format,
						'add_args'  => false,
						'current'   => max( 1, $current ),
						'total'     => $total,
						'prev_text' => '&larr;',
						'next_text' => '&rarr;',
						'type'      => 'list',
						'end_size'  => $end_max_size,
						'mid_size'  => $end_max_size
					)
				)
			);
		?>
	</nav>

<?php endif; ?>
