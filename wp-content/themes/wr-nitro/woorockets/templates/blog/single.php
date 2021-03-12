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

$wr_nitro_options = WR_Nitro::get_options();

// Get post format
$wr_format = get_post_format();

// Get content post format
$wr_video   = WR_Nitro_Helper::video_embed();
$wr_audio   = WR_Nitro_Helper::audio_embed();
$wr_content = get_post_meta( get_the_ID(), 'format_quote_content', true );
$wr_author  = get_post_meta( get_the_ID(), 'format_quote_author', true );

// Get single blog layout
$wr_layout = $wr_nitro_options[ 'blog_single_layout' ];

// Get post title style
$wr_post_title = $wr_nitro_options['blog_single_title_style'];

$wr_data = $wr_class = '';
// Render carousel
if ( 'gallery' == $wr_format ) {
	$wr_data  = 'data-owl-options=\'{"autoplay": "true", "items": "1"' . ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ) . '}\'';
	$wr_class = ' wr-nitro-carousel';
}

if ( '2' == $wr_nitro_options['blog_single_title_style'] ) {
	$wr_class = 'thumb-no-mg';
}

?>

<div class="<?php
		echo esc_attr( WR_Nitro_Render::$content_class ) . ( is_customize_preview() ? ' customizable customize-section-blog_single' : '' );
		if ( $wr_nitro_options['blog_single_social_share'] ) echo ' has-social-share';
		if ( '1' == $wr_post_title && 'no-sidebar' != $wr_layout ) echo ' mgt30';
		if ( $wr_layout == 'right-sidebar' ) echo ' right-sidebar';
	?>">

	<?php
		if ( '1' == $wr_post_title && 'no-sidebar' != $wr_layout ) :
			WR_Nitro_Render::get_template( 'blog/title/style', $wr_post_title );
		endif;
	?>

	<div class="b-single">

		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php WR_Nitro_Helper::schema_metadata( array( 'context' => 'entry' ) ); ?>>

				<?php
					echo '<div class="entry-thumb tc mgb30 ' . esc_attr( $wr_class ) . '" ' . $wr_data . '>';
						if ( $wr_format == 'gallery' ) {
							// Get gallery image
							$wr_photos = WR_Nitro_Helper::gallery();

							// Render gallery
							if ( ! empty( $wr_photos ) ) {
								foreach ( $wr_photos as $wr_photo ) {
									$wr_img_data = getimagesize( $wr_photo );
									$wr_output = '<a data-lightbox="nivo" data-lightbox-gallery="' . get_the_ID() . '" href="' . esc_url( $wr_photo ) . '"><img class="ts-03" src="' . esc_url( $wr_photo ) . '" alt="' . esc_attr( get_the_title() ) . '"  width="' . esc_attr( $wr_img_data[0] ) . '" height="' . esc_attr( $wr_img_data[1] ) . '" ></a>';
									echo '' . $wr_output;
								}
							}
						} elseif ( 'video' == $wr_format && $wr_video ) {
							echo '' . $wr_video;
						} elseif ( 'audio' == $wr_format && ! empty( $wr_audio ) ) {
							echo '' . $wr_audio;
						} elseif ( 'quote' == $wr_format && ! empty( $wr_content ) ) {
							$wr_image = wp_get_attachment_url( get_post_thumbnail_id() );
							echo '<div class="quote-content pr tc color-white pd30 pdt40 pdb40" ' . ( ( ! empty( $wr_image ) ) ? 'style="background: url(' . esc_url( $wr_image ) . ');background-size: cover"' : '' ) . '><div class="mask"></div><p class="color-white pr">' .  $wr_content . '</p><span class="pr"><strong>' . $wr_author . '</strong></span></div>';
						} elseif ( '1' == $wr_nitro_options['blog_single_title_style'] ) {
							the_post_thumbnail();
						}

					echo '</div>';
				?>

				<?php
					if ( $wr_nitro_options['blog_single_social_share'] ) {
						WR_Nitro_Helper::social_share();
					}
				?>

				<div class="entry-content">
					<?php if ( ! empty( $wr_nitro_options['blog_single_before_post'] ) && is_active_sidebar( $wr_nitro_options['blog_single_before_post'] ) ) : ?>
						<div class="widget-before-single-post mgb30">
							<?php dynamic_sidebar( $wr_nitro_options['blog_single_before_post'] ); ?>
						</div>
					<?php endif; ?>

					<div class="content" <?php WR_Nitro_Helper::schema_metadata( array( 'context' => 'entry_content' ) ); ?>>
						<?php the_content(); ?>
					</div>

					<?php
						wp_link_pages( array(
							'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'wr-nitro' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
							'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'wr-nitro' ) . ' </span>%',
							'separator'   => '<span class="screen-reader-text">, </span>',
						) );
					?>

					<?php echo WR_Nitro_Helper::get_tags(); ?>

					<?php if ( ! empty( $wr_nitro_options['blog_single_before_author'] ) && is_active_sidebar( $wr_nitro_options['blog_single_before_author'] ) ) : ?>
						<div class="widget-above-author mgt30">
							<?php dynamic_sidebar( $wr_nitro_options['blog_single_before_author'] ); ?>
						</div>
					<?php endif; ?>

					<?php
						if ( $wr_nitro_options['blog_single_author'] ) {
							$wr_author = WR_Nitro_Helper::schema_metadata( array( 'context' => 'author', 'echo' => false ) );
							$wr_author = sprintf(
								'<div class="post-author overlay_bg" ' . $wr_author . '>%1$s%2$s</div>',
								'<div class="pull-left">' . get_avatar( get_the_author_meta( 'user_email' ), '68', '' ) . '</div>',
								'<div class="author-info"><a class="name" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_the_author() . '</a><p>' . get_the_author_meta( 'description' ) . '</p></div>'

							);
							echo wp_kses_post( $wr_author );
						}
					?>

					<?php if ( $wr_nitro_options['blog_single_navigation'] ) { ?>
						<nav class="single-nav clear nitro-line" role="navigation">
							<?php
								previous_post_link( '<div class="prev">%link<span>' .  __( 'Previous post', 'wr-nitro' ) . '</span></div>', _x( '<span class="meta-nav"><i class="fa fa-long-arrow-left"></i></span>%title', 'Previous post', 'wr-nitro' ) );
								next_post_link(     '<div class="next">%link<span>' .  __( 'Next post', 'wr-nitro' ) . '</span></div>',     _x( '%title<span class="meta-nav"><i class="fa fa-long-arrow-right"></i></span>', 'Next post', 'wr-nitro' ) );
							?>
						</nav><!-- .single-nav -->
					<?php } ?>

					<?php
						if ( $wr_nitro_options['blog_single_comment'] ) {
							// If comments are open or we have at least one comment, load up the comment template
							if ( comments_open() || '0' != get_comments_number() ) :
								comments_template();
							endif;
						}
					?>

					<?php if ( ! empty( $wr_nitro_options['blog_single_after_comment'] ) && is_active_sidebar( $wr_nitro_options['blog_single_after_comment'] ) ) : ?>
						<div class="widget-after-comment mgt30">
							<?php dynamic_sidebar( $wr_nitro_options['blog_single_after_comment'] ); ?>
						</div><!-- .widget-above-author -->
					<?php endif; ?>

				</div><!-- .entry-content -->
			</article>

		<?php endwhile; ?>

	</div><!-- .b-single -->
</div>
