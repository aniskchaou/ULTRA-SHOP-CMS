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

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

?>

<div id="comments" class="comments-area pdt30 mgt30 mgb30 nitro-line">
	<?php
		$wr_args = array(
			'comment_notes_before' => '',
			'fields' => array(
				'author' => '
                    <p class="comment-form-author nitro-line">
                        <input placeholder="' . esc_attr__( 'Your name', 'wr-nitro' ) . '" type="text" required="required" size="30" value="" name="author" id="author">
                    </p>
                    <p class="comment-form-email nitro-line">
                        <input placeholder="' . esc_attr__( 'Your email', 'wr-nitro' ) . '" type="email" required="required" size="30" value="" name="email" id="email">
                    </p>
                '
            ),

			// change the title of the reply section
			'title_reply'=> esc_html__( 'Leave your comment', 'wr-nitro' ),

			// remove "Text or HTML to be displayed after the set of comment fields"
			'comment_notes_after' => '',

			// redefine your own textarea (the comment body)
			'comment_field' => '<p class="comment-form-comment nitro-line"><textarea rows="6" placeholder="' . esc_attr__( 'Your comment', 'wr-nitro' ) . '" name="comment" aria-required="true"></textarea></p>',

			// change the title of send button
			'label_submit'=> esc_html__( 'Post comment', 'wr-nitro' ),
		);

		comment_form( $wr_args );

	if ( have_comments() ) : ?>
		<h3 class="comments-title">
			<?php
			printf( _nx( 'One comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'wr-nitro' ),
				number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h3>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
			<nav id="comment-nav-above" class="comment-navigation" role="navigation">
				<h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'wr-nitro' ); ?></h1>
				<div class="nav-previous mgb20"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'wr-nitro' ) ); ?></div>
				<div class="nav-next mgb20"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'wr-nitro' ) ); ?></div>
			</nav><!-- #comment-nav-above -->
		<?php endif; // check for comment navigation ?>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'    => 'ol',
					'callback' => array( 'WR_Nitro_Helper', 'comments_list' ),
				) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
			<nav id="comment-nav-below" class="comment-navigation" role="navigation">
				<h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'wr-nitro' ); ?></h1>
				<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'wr-nitro' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'wr-nitro' ) ); ?></div>
			</nav><!-- #comment-nav-below -->
		<?php endif; // check for comment navigation

	endif; // have_comments()

	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'wr-nitro' ); ?></p>
	<?php endif; ?>

</div><!-- #comments -->
