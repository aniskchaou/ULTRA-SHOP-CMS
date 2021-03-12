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

$wr_class = $wr_html = '';
$wr_style = $wr_data = array();

if ( has_post_thumbnail() ) {
	// Get post thumbnail link
	$wr_link = wp_get_attachment_url( get_post_thumbnail_id() );

	$wr_style[] = 'background:url("' . esc_url( $wr_link ) . '") repeat center center / cover;';
}

$wr_full_screen = $wr_nitro_options['blog_single_title_full_screen'];
if ( $wr_full_screen ) {
	$wr_class = ' section-full-screen';
	$wr_data  = array(
		'1' => 'data-0="background-position:0px 0px;" data-500="background-position: 0px -200px;"',
		'2' => 'data-0="opacity: 1;" data-500="opacity: 0;"'
	);
	$wr_html  .= '<a href="#single" class="arrow wr-scroll-animated"><span class="pa"></span><span class="pa"></span></a>';
}
?>
<div class="post-title style-2 fc mgb30 pr<?php echo esc_attr( $wr_class ) . ( $wr_full_screen ? ' post-title-full-screen' : '' ) . ( is_customize_preview() ? ' customizable customize-section-blog_single' : '' ); ?>" style="<?php echo esc_attr( implode( ' ', $wr_style ) ); ?>" <?php if ( isset( $wr_data[1] ) ) echo wp_kses_post( $wr_data[1] ); ?>>
	<div class="container tc pr" <?php if ( isset( $wr_data[2] ) ) echo wp_kses_post( $wr_data[2] ); ?>>
		<h1 class="entry-title mg0 mgb30" <?php WR_Nitro_Helper::schema_metadata( array( 'context' => 'entry_title' ) ); ?>><?php the_title(); ?></h1>
		<?php
			$tmp = WR_Nitro_Helper::schema_metadata( array( 'context' => 'author', 'echo' => false ) );
			echo sprintf(
				'<div class="entry-author" ' . $tmp . '>%1$s<span class="db mgt10">%2$s</span></div>',
				get_avatar( get_the_author_meta( 'user_email' ), '42', '' ),
				get_the_author_meta( 'nickname', $post->post_author )
			);
		?>
		<div class="entry-meta mgt20">
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
	</div><!-- .container -->
	<?php echo wp_kses_post( $wr_html ); ?>
</div><!-- .post-title -->
