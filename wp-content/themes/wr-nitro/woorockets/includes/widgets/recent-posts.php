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

class WR_Nitro_Widgets_Recent_Posts extends WP_Widget {
	function __construct() {
		$widget_ops  = array(
			'description' => __( 'Displays the recent posts', 'wr-nitro' )
		);

		$control_ops = array(
			'width'  => 'auto',
			'height' => 'auto'
		);

		parent::__construct( 'recent_posts', __( 'Nitro - Recent Posts', 'wr-nitro' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title     = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$time      = $instance['time'] ? '1' : '0';
		$thumbtype = empty( $instance['thumbtype'] ) ? 'thumbnail' : $instance['thumbtype'];
		$limit     = empty( $instance['limit'] ) ? 5 : ( int ) $instance['limit'];

		echo wp_kses_post( $before_widget );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $before_title . $title . $after_title );
		}

		$the_query = new WP_Query(
			array(
				'posts_per_page'      => $limit,
				'no_found_rows'       => true,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'tax_query'      => array(
					array(
						'taxonomy' => 'category',
						'field'    => 'term_id',
						'terms'    => $instance['cats']
					)
				),
				'suppress_filters' => true,
			)
		);
		?>

		<ul class="widget-recent recent-posts">
			<?php
				while( $the_query->have_posts() ) :
					$the_query->the_post();

					$title  = get_the_title();
					$url    = get_permalink();
					$img    = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), '60x60' );
					$format = get_post_format();
			?>
			<li class="fc">
				<?php if ( 'thumbnail' == $thumbtype ) : ?>
					<?php
						if ( has_post_thumbnail() ) : ?>
							<a href="<?php echo esc_url( $url ); ?>" class="entry-thumb">
								<img class="br-2" src="<?php echo esc_url( $img[0] ); ?>" alt="<?php echo esc_attr( $title ); ?>" width="60" height="60" />
							</a>
						<?php
						endif;
					?>
				<?php elseif ( 'post_format' == $thumbtype ) : ?>
					<a href="<?php echo esc_url( $url ); ?>">
						<?php
							switch ( $format ) :
								case 'gallery' :
									echo '<i class="fa fa-image"></i>';
									break;
								case 'audio' :
									echo '<i class="fa fa-music"></i>';
									break;
								case 'video' :
									echo '<i class="fa fa-play-circle-o"></i>';
									break;
								case 'quote' :
									echo '<i class="fa fa-quote-left"></i>';
									break;
								default :
									echo '<i class="fa fa-edit"></i>';
								break;
							endswitch;
						?>
					</a>
				<?php endif; ?>

				<div class="info">
					<a href="<?php echo esc_url( $url ); ?>" title="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $title ); ?></a>
					<?php if ( $time == true ) : ?>
						<time><?php echo get_the_date( 'M j, Y' ); ?></time>
					<?php endif; ?>
				</div>
			</li>

			<?php
				endwhile;
				wp_reset_postdata();
			?>
		</ul>

		<?php
		echo wp_kses_post( $after_widget );
	}

	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['time']  = ! empty( $new_instance['time'] ) ? 1 : 0;
		$instance['cats']  = $new_instance['cats'];
		$instance['limit'] = strip_tags( $new_instance['limit'] );
		if ( in_array( $new_instance['thumbtype'], array( 'thumbnail', 'post_format' ) ) ) {
			$instance['thumbtype'] = $new_instance['thumbtype'];
		} else {
			$instance['thumbtype'] = 'thumbnail';
		}
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( ( array ) $instance, array( 'title' => '', 'thumbtype' => 'thumbnail', 'limit' => 5 ) );
		$title    = strip_tags( $instance['title'] );
		$time     = isset( $instance['time'] ) ? ( bool ) $instance['time'] : true;
		$cats     = isset( $instance['cats'] ) ? $instance['cats'] : array();
		$limit    = ( int ) $instance['limit'];

		$categories = get_categories( 'orderby=name&hide_empty=0' );

	?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wr-nitro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'time' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'time' ) ); ?>"<?php checked( $time ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'time' ) ); ?>"><?php esc_html_e( 'Show Date', 'wr-nitro' ); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>"><?php esc_html_e( 'Which Categories to show ?', 'wr-nitro' ); ?></label>
			<select class="widefat" multiple="multiple" id="<?php echo esc_attr( $this->get_field_id( 'cats' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cats' ) ); ?>[]">
				<?php foreach ( $categories as $category ) : ?>
					<option value="<?php echo esc_attr( $category->term_id ); ?>"<?php echo in_array( $category->term_id, $cats ) ? ' selected="selected"':''; ?>><?php echo esc_html( $category->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'thumbtype' ) ); ?>"><?php esc_html_e( 'Thumbnail Type:', 'wr-nitro' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'thumbtype' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'thumbtype' ) ); ?>" class="widefat">
				<option value="thumbnail"<?php selected( $instance['thumbtype'], 'thumbnail' ); ?>><?php esc_html_e( 'Image', 'wr-nitro' ); ?></option>
				<option value="post_format"<?php selected( $instance['thumbtype'], 'post_format' ); ?>><?php esc_html_e( 'Icon', 'wr-nitro' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Number of posts to show:', 'wr-nitro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>" />
		</p>

	<?php
	}
}
