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

<div class="<?php echo esc_attr( WR_Nitro_Render::$content_class ) . ( is_customize_preview() ? ' customizable customize-section-page' : '' ); ?>" <?php WR_Nitro_Helper::schema_metadata( array( 'context' => 'entry' ) ); ?>>

	<?php
		if ( ! empty( $wr_nitro_options['sidebar_before_page_content'] ) && is_active_sidebar( $wr_nitro_options['sidebar_before_page_content'] ) ) {
			echo '<div class="mgb30 sidebar-before-page">';
				dynamic_sidebar( $wr_nitro_options['sidebar_before_page_content'] );
			echo '</div>';
		}
	?>

	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php WR_Nitro_Helper::schema_metadata( array( 'context' => 'entry_content' ) ); ?>>

			<?php the_content(); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					echo '<div class="container oh">';
						comments_template();
					echo '</div>';
				endif;
			?>

		</article>

	<?php endwhile; ?>

	<?php
		if ( ! empty( $wr_nitro_options['sidebar_after_page_content'] ) && is_active_sidebar( $wr_nitro_options['sidebar_after_page_content'] ) ) {
			echo '<div class="mgt30 sidebar-after-page">';
				dynamic_sidebar( $wr_nitro_options['sidebar_after_page_content'] );
			echo '</div>';
		}
	?>

</div>
