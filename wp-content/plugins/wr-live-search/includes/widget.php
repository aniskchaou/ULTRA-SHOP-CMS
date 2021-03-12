<?php
/**
 * @version    1.0
 * @package    WR_Live_Search
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

/**
 * Widget class.
 *
 * @package  WR Live Search
 */
class WR_Live_Search_Widget extends WP_Widget {
	/**
	 * Constructor.
	 *
	 * @return  void
	 */
	public function __construct() {
		// Define widget arguments.
		$args = array(
			'classname'   => 'widget-wrls',
			'description' => __( 'A live search box that shows results instantly.', 'wr-live-search' ),
		);

		parent::__construct( 'wr_live_search', __( 'WR Live Search', 'wr-live-search' ), $args );
	}

	/**
	 * Print widget.
	 *
	 * @param   array  $args      Widget arguments.
	 * @param   array  $instance  Instance parameters.
	 *
	 * @return  void
	 */
	public function widget( $args, $instance ) {
		// Prepare widget title.
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		// Print HTML code for widget.
		echo '' . $args['before_widget'];

		if ( $title ) {
			echo '' . $args['before_title'] . $title . $args['after_title'];
		}

		echo '' . WR_Live_Search_Shortcode::generate( $instance );

		echo '' . $args['after_widget'];
	}

	/**
	 * Print form to configure widget instance.
	 *
	 * @param   array  $instance  Instance parameters.
	 *
	 * @return  void
	 */
	public function form( $instance ) {

		$instance = wp_parse_args( $instance, array( 'title' => '' ) );

		?>

		<p>
			<label>
				<?php _e( 'Title:', 'wr-live-search' ); ?>
				<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php  echo isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : ''; ?>">
			</label>
		</p>

		<p>
			<label>
				<?php _e( 'Placeholder:', 'wr-live-search' ); ?>
				<input type="text" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'placeholder' ) ); ?>" value="<?php echo isset( $instance['placeholder'] ) ? esc_attr( $instance['placeholder'] ) :  __( 'Search product', 'wr-live-search' ) ; ?>">
			</label>
		</p>

		<p>
			<?php _e( 'Show button:', 'wr-live-search' ); ?><br />
			<label style="padding: 2px 0 2px 5px; display: inline-block; ">
				<input onchange="jQuery(this).parents( '.widget-content' ).find( '.wr-ls-button-text' )[jQuery(this).is( ':checked' ) ? 'show' : 'hide']();" value="1" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_button' ) ); ?>" <?php if( isset( $instance['show_button'] ) ) checked( $instance['show_button'], '1' ); ?> />
				<?php _e( 'Yes', 'wr-live-search' ); ?>
			</label>
		</p>

		<p class="wr-ls-button-text" style="display:<?php echo ( ( isset( $instance['show_button'] ) && $instance['show_button'] == '1' ) ? 'block' : 'none' ); ?>">
			<label>
				<?php _e( 'Button text:', 'wr-live-search' ); ?><br />
				<input class="wr-ls-button-text widefat" name="<?php echo esc_attr( $this->get_field_name( 'text_button' ) ); ?>" type="text" value="<?php echo isset( $instance['text_button'] ) ? esc_attr( $instance['text_button'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<?php _e( 'Show category list:', 'wr-live-search' ); ?><br />
			<label style="padding: 2px 0 2px 5px; display: inline-block; ">
				<input value="1" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_category' ) ); ?>" <?php if( isset( $instance['show_category'] ) ) checked( $instance['show_category'], '1' ); ?> />
				<?php _e( 'Yes', 'wr-live-search' ); ?>
			</label>
		</p>

		<p>
			<?php _e( 'Show suggestion:', 'wr-live-search' ); ?><br />
			<label style="padding: 2px 0 2px 5px; display: inline-block; ">
				<input value="1" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_suggestion' ) ); ?>" <?php if( isset( $instance['show_suggestion'] ) ) checked( $instance['show_suggestion'], '1' ); ?> />
				<?php _e( 'Yes', 'wr-live-search' ); ?>
			</label>
		</p>

		<p>
			<?php _e( 'Search in:', 'wr-live-search' ); ?><br />
			<label style="padding: 2px 0 2px 5px; display: inline-block; ">
				<input <?php isset( $instance['search_in']['title'] ) ? checked( $instance['search_in']['title'], 1 ) : checked( 1, 1 ) ; ?> type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'search_in' ) ) . '[title]'; ?>" value="1" />
				<?php _e( 'Title', 'wr-live-search' ); ?>
			</label><br />
			<label style="padding: 2px 0 2px 5px; display: inline-block; ">
				<input <?php if( isset( $instance['search_in']['description'] ) ) checked( $instance['search_in']['description'], 1 ); ?> type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'search_in' ) ) . '[description]'; ?>" value="1" />
				<?php _e( 'Description', 'wr-live-search' ); ?>
			</label><br />
			<label style="padding: 2px 0 2px 5px; display: inline-block; ">
				<input <?php if( isset( $instance['search_in']['content'] ) ) checked( $instance['search_in']['content'], 1 ); ?> type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'search_in' ) ) . '[content]'; ?>" value="1" />
				<?php _e( 'Content', 'wr-live-search' ); ?>
			</label><br />
			<label style="padding: 2px 0 2px 5px; display: inline-block; ">
				<input <?php if( isset( $instance['search_in']['sku'] ) ) checked( $instance['search_in']['sku'], 1 ); ?> type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'search_in' ) ) . '[sku]'; ?>" value="1" />
				<?php _e( 'SKU', 'wr-live-search' ); ?>
			</label>
		</p>

		<p>
			<label>
				<?php _e( 'Minimum number of characters:', 'wr-live-search' ); ?>
				<input type="number" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'min_characters' ) ); ?>" value="<?php echo isset( $instance['min_characters'] ) ? (int) $instance['min_characters'] : 0; ?>">
			</label>
		</p>

		<p>
			<label>
				<?php _e( 'Maximum number of results:', 'wr-live-search' ); ?>
				<input type="number" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'max_results' ) ); ?>" value="<?php echo isset( $instance['max_results'] ) ? (int) $instance['max_results'] : 5; ?>">
			</label>
		</p>

		<p>
			<label>
				<?php _e( 'Thumbnail size:', 'wr-live-search' ); ?>
				<input type="number" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'thumb_size' ) ); ?>" value="<?php echo isset( $instance['thumb_size'] ) ? (int) $instance['thumb_size'] : 50; ?>">
			</label>
		</p>

		<p>
			<label>
				<?php _e( 'Class:', 'wr-live-search' ); ?>
				<input type="text" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'class' ) ); ?>" value="<?php  echo isset( $instance['class'] ) ? esc_attr( $instance['class'] ) : ''; ?>" />
			</label>
		</p>

		<p>
			<label>
				<?php _e( 'ID:', 'wr-live-search' ); ?>
				<input type="text" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>" value="<?php  echo isset( $instance['id'] ) ? esc_attr( $instance['id'] ) : ''; ?>" />
			</label>
		</p>

		<?php
	}

	/**
	 * Save widget instance settings.
	 *
	 * @param   array  $new_instance  New instance parameters.
	 * @param   array  $old_instance  Old instance parameters.
	 *
	 * @return  array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] 						= isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['placeholder'] 				= isset( $new_instance['placeholder'] ) ? sanitize_text_field( $new_instance['placeholder'] ) : '';
		$instance['show_button'] 				= isset( $new_instance['show_button'] ) ? (int) $new_instance['show_button'] : 0;
		$instance['text_button'] 				= isset( $new_instance['text_button'] ) ? sanitize_text_field( $new_instance['text_button'] ) : '';
		$instance['show_category'] 				= isset( $new_instance['show_category'] ) ? (int) $new_instance['show_category'] : 0;
		$instance['show_suggestion'] 			= isset( $new_instance['show_suggestion'] ) ? (int) $new_instance['show_suggestion'] : 0;

		$instance['search_in']['title'] 		= isset( $new_instance['search_in']['title'] ) ? (int) $new_instance['search_in']['title'] : 0;
		$instance['search_in']['description'] 	= isset( $new_instance['search_in']['description'] ) ? (int) $new_instance['search_in']['description'] : 0;
		$instance['search_in']['content'] 		= isset( $new_instance['search_in']['content'] ) ? (int) $new_instance['search_in']['content'] : 0;
		$instance['search_in']['sku'] 	     	= isset( $new_instance['search_in']['sku'] ) ? (int) $new_instance['search_in']['sku'] : 0;

		$instance['min_characters'] 			= isset( $new_instance['min_characters'] ) ? (int) $new_instance['min_characters'] : 0;
		$instance['max_results'] 				= isset( $new_instance['max_results'] ) ? (int) $new_instance['max_results'] : 0;
		$instance['thumb_size'] 				= isset( $new_instance['thumb_size'] ) ? (int) $new_instance['thumb_size'] : 50;

		$instance['class'] 						= isset( $new_instance['class'] ) ? sanitize_text_field( $new_instance['class'] ) : '';
		$instance['id'] 						= isset( $new_instance['id'] ) ? sanitize_text_field( $new_instance['id'] ) : '';

		return $instance;
	}
}
