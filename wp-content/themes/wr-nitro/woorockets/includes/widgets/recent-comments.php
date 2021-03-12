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

class WR_Nitro_Widgets_Recent_Comments extends WP_Widget {
	function __construct() {
		$widget_ops  = array(
			'description' => __( 'Displays the recent comments', 'wr-nitro' )
		);

		$control_ops = array(
			'width'  => 'auto',
			'height' => 'auto'
		);

		parent::__construct( 'recent_comments', __( 'Nitro - Recent Comments', 'wr-nitro' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title     = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$thumbtype = empty( $instance['thumbtype'] ) ? 'thumbnail' : $instance['thumbtype'];
		$limit     = empty( $instance['limit'] ) ? 5 : ( int ) $instance['limit'];

		echo wp_kses_post( $before_widget );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $before_title . $title . $after_title );
		}

		/**
		 * Filter the arguments for the Recent Comments widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Comment_Query::query() for information on accepted arguments.
		 *
		 * @param array $comment_args An array of arguments used to retrieve the recent comments.
		 */
		$post_types = get_post_types();

		$comments = get_comments( apply_filters( 'widget_comments_args', array(
			'number'      => $limit,
			'status'      => 'approve',
			'post_status' => 'publish',
			'post_type'   => $instance['posttype'],
		) ) );
		?>

		<ul class="widget-recent recent-comments">
			<?php
				if ( is_array( $comments ) && $comments ) {
				// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
				$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
				_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

				foreach ( $comments as $comment ) {
					echo '<li class="fc">';
						echo '<a class="entry-thumb" href="' . esc_url( get_comment_link( $comment ) ) . '">' . get_avatar( $comment, 45 ) . '</a>';
						echo '<div class="info"><a href="' . esc_url( get_comment_link( $comment ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a><span class="tu">' . get_comment_author_link( $comment ) . '</span></div>';
					echo '</li>';
				}
			}
			?>
		</ul>

		<?php
		echo wp_kses_post( $after_widget );
	}

	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posttype']  = $new_instance['posttype'];
		$instance['limit'] = strip_tags( $new_instance['limit'] );

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( ( array ) $instance, array( 'title' => '', 'limit' => 5 ) );
		$title    = strip_tags( $instance['title'] );
		$time     = isset( $instance['time'] ) ? ( bool ) $instance['time'] : true;
		$type     = isset( $instance['posttype'] ) ? $instance['posttype'] : array();
		$limit    = ( int ) $instance['limit'];

		// Get all WordPress post type
		$post_types = get_post_types( array( 'public' => true, ), 'object' );
	?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wr-nitro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>"><?php esc_html_e( 'Which post types to show ?', 'wr-nitro' ); ?></label>
			<select class="widefat" multiple="multiple" id="<?php echo esc_attr( $this->get_field_id( 'posttype' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'posttype' ) ); ?>[]">
				<?php
					if ( $post_types ) {
						foreach( $post_types as $key => $val ) {
							if ( in_array( $key, array( 'revision', 'attachment', 'nav_menu_item' ) ) )
								continue;
							echo '<option ' . ( in_array( $key, $type ) ? 'selected' : '' ) . ' value="' . esc_attr( $key ) . '">' . $val->labels->name . '</option>';
						}
					}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Number of comments to show:', 'wr-nitro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>" />
		</p>

	<?php
	}
}
