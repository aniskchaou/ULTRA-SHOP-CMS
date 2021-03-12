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

// Get masonry columns
$wr_columns = $wr_nitro_options['blog_masonry_column'];

// Get blog list color
$wr_color = $wr_nitro_options['blog_color']
?>

<div class="<?php echo esc_attr( WR_Nitro_Render::$content_class ) . ( is_customize_preview() ? ' customizable customize-section-blog_list' : '' ); ?>">

	<?php WR_Nitro_Render::get_template( 'blog/sidebar/before' ); ?>

	<div class="b-masonry mgt30 mgb30 wr-nitro-masonry <?php echo esc_attr( $wr_color ); ?>" data-masonry='{"selector":".hentry", "columnWidth":".grid-sizer"}'>

		<div class="grid-sizer cm-<?php echo esc_attr( (int) 12 / $wr_columns ); ?> cs-6 cxs-12"></div>

		<?php
			while ( have_posts() ) : the_post();

			// Get post layout large
			$wr_large = get_post_meta( get_the_ID(), 'masonry_large', true );
			if ( ! empty( $wr_large ) ) {
				$wr_classes = (int) 24 / $wr_columns;
			} else {
				$wr_classes = (int) 12 / $wr_columns;
			}

			// Get post format
			$wr_format = get_post_format();
			if ( false === $wr_format ) $wr_format = 'standard';
		?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'mgb30 cs-6 cxs-12 cm-' . $wr_classes ); ?> <?php WR_Nitro_Helper::schema_metadata( array( 'context' => 'entry' ) ); ?>>

				<?php WR_Nitro_Render::get_template( 'blog/format/' . $wr_format . '' ); ?>

				<div class="entry-content <?php if ( 'boxed' == $wr_color ) echo esc_attr( 'overlay_bg' ); ?>">

					<?php if ( $wr_format == 'quote' ) {
						WR_Nitro_Render::get_template( 'blog/content/quote' );
					} else {
						WR_Nitro_Render::get_template( 'blog/content/standard' );
					} ?>

				</div><!-- entry-content -->
			</article><!-- #post-ID -->

		<?php endwhile; ?>

	</div><!-- .b-masonry -->

	<?php WR_Nitro_Helper::pagination(); ?>

	<?php WR_Nitro_Render::get_template( 'blog/sidebar/after' ); ?>

</div>
