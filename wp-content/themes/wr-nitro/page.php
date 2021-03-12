<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

// Get theme options
$wr_nitro_options = WR_Nitro::get_options();

// Get sidebar name
$wr_sidebar = isset( $wr_nitro_options['wr_page_layout_sidebar'] ) ? $wr_nitro_options['wr_page_layout_sidebar'] : '';

$wr_enable_page_builder = get_post_meta( get_the_ID(), '_wpb_vc_js_status', true );

get_header();
?>
	<?php
		if ( ! ( function_exists( 'is_account_page' ) && is_account_page() ) ) {
			WR_Nitro_Render::get_template( 'common/page', 'title' );
		}
	?>

	<?php if ( 'false' == $wr_enable_page_builder || empty( $wr_enable_page_builder ) || 'no-sidebar' != $wr_nitro_options['wr_page_layout'] ) echo '<div class="container mgt30 mgb30">'; ?>
		<div class="row page-content">
			<div class="fc fcw <?php echo esc_attr( $wr_nitro_options['wr_page_layout'] == 'right-sidebar' ? 'right-sidebar menu-on-right' : '' ); ?>">

				<?php
					// Set page config
					$wr_args = array(
						'path'           => 'woorockets/templates',
						'layout'         => $wr_nitro_options['wr_page_layout'],
						'content_layout' => 'page',
						'sidebar'        => $wr_sidebar,
						'sidebar_class'  => 'primary-sidebar',
						'content_class'  => 'main-content',
					);

					WR_Nitro_Render::render_template( 'page', $wr_args );

					wp_link_pages( array(
						'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'wr-nitro' ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
						'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'wr-nitro' ) . ' </span>%',
						'separator'   => '<span class="screen-reader-text">, </span>',
					) );
				?>

			</div>
		</div>

	<?php if ( 'false' == $wr_enable_page_builder || empty( $wr_enable_page_builder ) || 'no-sidebar' != $wr_nitro_options['wr_page_layout'] ) echo '</div>'; ?>

<?php get_footer(); ?>
