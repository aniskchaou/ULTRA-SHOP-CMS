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
?>

<div class="<?php echo esc_attr( WR_Nitro_Render::$content_class ) . ( is_customize_preview() ? ' customizable customize-section-blog_list' : '' ); ?>">

	<?php WR_Nitro_Render::get_template( 'blog/sidebar/before' ); ?>

	<div class="b-zigzag mgt30 mgb30 <?php echo esc_attr( $wr_nitro_options['blog_color'] ); ?>">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php
				// Get post format
				$wr_format = get_post_format();
				if ( false === $wr_format ) $wr_format = 'standard';
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'mgb60 oh pr' ); ?> <?php WR_Nitro_Helper::schema_metadata( array( 'context' => 'entry' ) ); ?>>

				<?php WR_Nitro_Render::get_template( 'blog/format/' . $wr_format . '' ); ?>

				<div class="entry-content oh pr overlay_bg pd20">

					<?php echo WR_Nitro_Helper::get_cat(); ?>

					<?php if ( $wr_format == 'quote' ) {
						WR_Nitro_Render::get_template( 'blog/content/quote' );
					} else {
						WR_Nitro_Render::get_template( 'blog/content/standard' );
					} ?>

				</div><!-- entry-content -->

			</article><!-- #post-ID -->

		<?php endwhile; ?>

	</div><!-- .b-zigzag -->

	<?php WR_Nitro_Helper::pagination(); ?>

	<?php WR_Nitro_Render::get_template( 'blog/sidebar/after' ); ?>

</div>
