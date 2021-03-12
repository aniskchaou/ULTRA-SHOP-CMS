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

// Get single blog layout
$wr_layout = $wr_nitro_options[ 'blog_single_layout' ];

// Get post title style
$wr_title = $wr_nitro_options['blog_single_title_style'];
?>
<div class="post-title<?php echo ( is_customize_preview() ? ' customizable customize-section-blog_single' : '' ); ?>">
	<?php echo '1' == $wr_title && 'no-sidebar' != $wr_layout ? '<div class="tl">' : '<div class="container tc">'; ?>
		<h1 class="entry-title mg0 mgb30" <?php WR_Nitro_Helper::schema_metadata( array( 'context' => 'entry_title' ) ); ?>><?php the_title(); ?></h1>
		<?php
			if ( ! ( '1' == $wr_title && ( 'left-sidebar' == $wr_layout || 'right-sidebar' == $wr_layout ) ) ) {
				$tmp = WR_Nitro_Helper::schema_metadata( array( 'context' => 'author', 'echo' => false ) );
				echo sprintf(
					'<div class="entry-author" ' . $tmp . '>%1$s<span class="db mgt10">%2$s</span></div>',
					get_avatar( get_the_author_meta( 'user_email' ), '42', '' ),
					get_the_author_meta( 'nickname', $post->post_author )
				);
			}
		?>
		<div class="entry-meta mgt20">
			<?php
				if ( '1' == $wr_title && 'no-sidebar' != $wr_layout ) {
					echo sprintf(__( '<span class="entry-author">%1$s<span class="mgl10">%2$s</span></span>', 'wr-nitro' ), get_avatar( get_the_author_meta( 'user_email' ), '32', '' ), get_the_author_meta( 'nickname', $post->post_author ) );
				}
			?>
			<?php echo WR_Nitro_Helper::get_posted_on(); ?>
			<?php
				$wr_categories_list = get_the_category_list( __( ', ', 'wr-nitro' ) );
				if ( $wr_categories_list ) {
					$wr_cats = sprintf( '<span><i class="fa fa-folder-open-o"></i>' . esc_html__( '%1$s', 'wr-nitro' ) . '</span>', $wr_categories_list );
					echo wp_kses_post( $wr_cats );
				}
			?>
			<?php
				echo '<span class="comments-number"><i class="fa fa-comments-o"></i>';
					comments_popup_link( esc_html__( '0 Comment', 'wr-nitro' ), esc_html__( '1 Comment', 'wr-nitro' ), esc_html__( '% Comments', 'wr-nitro' ) );
				echo '</span>';
			?>
		</div><!-- .entry-meta -->
	</div>
</div><!-- .post-title -->
