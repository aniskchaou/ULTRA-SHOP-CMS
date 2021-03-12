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

global $post, $woocommerce, $product;

get_header(); ?>

	<?php WR_Nitro_Render::get_template( 'common/page', 'title' ); ?>

	<div class="container">
		<div class="row">
			<div class="cm-12">
				<h2 class="result-count mgt30"><?php printf( esc_html__( '%s Results for: %s', 'wr-nitro' ), $wp_query->post_count, '<span>' . get_search_query() . '</span>' ); ?></h2>

				<div class="result-list mgt30 mgb30" data-key="<?php echo esc_attr( get_search_query() ); ?>">
					<?php if ( have_posts() ) : ?>
						<?php while ( have_posts() ) : the_post(); ?>
							<div id="post-<?php the_ID(); ?>" <?php post_class( 'mgb30 pd30 oh pr search-item' ); ?> >
								<?php
									if ( has_post_thumbnail() ) {
										echo '<div class="entry-thumb mgr30 fl pr">';
											echo '<a href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( get_the_title() ) . '">' . get_the_post_thumbnail( get_the_ID(), 'thumbnail' ) . '</a>';
										if ( 'product' === get_post_type() ) {
											if ( $product->is_on_sale() ) {
												woocommerce_show_product_loop_sale_flash();
											}
										}
										echo '</div>';
									}
								?>

								<div class="entry-content oh">
									<h3 class="entry-title mg0 mgb20"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h3>
									<?php if ( 'product' === get_post_type() ) : ?>
										<?php wc_get_template( 'loop/rating.php' ); ?>
										<div class="p-info">
											<?php wc_get_template( 'loop/price.php' ); ?>
										</div>
									<?php endif; ?>

									<div class="desc">
										<?php the_excerpt(); ?>
									</div>
								</div><!-- entry-content -->
							</div><!-- #post-ID -->
						<?php endwhile;
						else :
							esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wr-nitro' );
						endif;
					?>
				</div>
				<?php WR_Nitro_Helper::pagination(); ?>
			</div><!-- .cm-12 -->
		</div><!-- .row -->
	</div><!-- .container -->
<?php get_footer(); ?>
