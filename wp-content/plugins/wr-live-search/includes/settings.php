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
 * Class to handle settings.
 *
 * @package  WR Live Search
 */

class WR_Live_Search_Settings {
	/**
	 * Register Live Search settings with WordPress.
	 *
	 * @return  void
	 */
	public static function register() {
		// Get current settings.
		$settings = self::get();

		// Add settings section.
		$section = WR_LS . '-settings';

		add_settings_section( $section, '', '__return_null', WR_LS );

		// Define settings.
		$fields = array(
			array(
				'id'    => 'placeholder',
				'title' => __( 'Placeholder', 'wr-live-search' ),
			),
			array(
				'id'    => 'show_button',
				'title' => __( 'Show button', 'wr-live-search' ),
			),
			array(
				'class' => 'show-button ' . ( ( isset( $settings['show_button'] ) && $settings['show_button'] == 1 ) ? 'show' : '' ),
				'id'    => 'text_button',
				'title' => __( 'Text button', 'wr-live-search' ),
			),
			array(
				'id'    => 'show_category',
				'title' => __( 'Show category list', 'wr-live-search' ),
			),
			array(
				'id'    => 'show_suggestion',
				'title' => __( 'Show suggestion', 'wr-live-search' ),
			),
			array(
				'id'    => 'min_characters',
				'title' => __( 'Minimum number of characters', 'wr-live-search' ),
			),
			array(
				'id'    => 'max_results',
				'title' => __( 'Maximum number of results', 'wr-live-search' ),
			),
			array(
				'id'    => 'thumb_size',
				'title' => __( 'Thumbnail size', 'wr-live-search' ),
			),
			array(
				'id'    => 'search_in',
				'title' => __( 'Search in', 'wr-live-search' ),
			),
			array(
				'id'    => 'class',
				'title' => __( 'Class', 'wr-live-search' ),
			),
			array(
				'id'    => 'id',
				'title' => __( 'ID', 'wr-live-search' ),
			),
		);

		foreach ( $fields as $field ) {

			$arg = array(
					'id'        => $field['id'],
					'data'      => $settings[ $field['id'] ],
				);

			if( isset( $field['class'] ) )
				$arg['class'] = $field['class'];

			// Register settings field.
			add_settings_field(
				$field['id'],
				$field['title'],
				array( __CLASS__, 'setting_' . $field['id'] ),
				WR_LS,
				$section,
				$arg
			);
		}

	}

	/**
	 * Get current Live Search settings.
	 *
	 * @param   array  $settings  Custom settings.
	 *
	 * @param   boolean  $default Settings default.
	 *
	 * @return  array
	 */
	public static function get( $settings = null, $default = false ) {

		// Get saved settings.
		if ( ! $settings ) {
			$settings = get_option( 'wr_live_search' );
		}

		$settings_default = array(
			'placeholder'     => __( 'Search product...', 'wr-live-search' ),
			'show_button'     => 0,
			'text_button'     => __( 'Search', 'wr-live-search' ),
			'show_category'   => 0,
			'min_characters'  => 0,
			'max_results'     => 5,
			'thumb_size'      => 50,
			'search_in'       => array(
				'title'       => 1,
				'description' => 0,
				'content'     => 0,
				'sku'         => 0,
			),
			'show_suggestion' => 0,
			'class'           => '',
			'id'              => '',
		);

		// Apply default values.
		$settings = wp_parse_args(
			$settings,
			$settings_default
		);

		return $default ? $settings_default : $settings;
	}

	/**
	 * Save Live Search settings.
	 *
	 * @return  void
	 */
	public static function save() {
		// Verify nonce.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], WR_LS . '-options' ) ) {
			return;
		}

		// Sanitize setting values.
		$settings = array();

		if ( isset( $_POST['placeholder'] ) )
			$settings['placeholder'] = sanitize_text_field( $_POST['placeholder'] );

		if ( isset( $_POST['text_button'] ) )
			$settings['text_button'] = sanitize_text_field( $_POST['text_button'] );

		if ( isset( $_POST['min_characters'] ) )
			$settings['min_characters'] = ( int ) $_POST['min_characters'];

		if ( isset( $_POST['max_results'] ) )
			$settings['max_results'] = ( int ) $_POST['max_results'];

		if ( isset( $_POST['thumb_size'] ) )
			$settings['thumb_size'] = ( int ) $_POST['thumb_size'];

		$settings['show_category'] 				= isset( $_POST['show_category'] ) ? ( int ) $_POST['show_category'] : '0';
		$settings['show_button'] 				= isset( $_POST['show_button'] ) ? ( int ) $_POST['show_button'] : '0';
		$settings['search_in']['title'] 		= isset( $_POST['search_in']['title'] ) ? ( int ) $_POST['search_in']['title'] : '0';
		$settings['search_in']['description'] 	= isset( $_POST['search_in']['description'] ) ? ( int ) $_POST['search_in']['description'] : '0';
		$settings['search_in']['content'] 		= isset( $_POST['search_in']['content'] ) ? ( int ) $_POST['search_in']['content'] : '0';
		$settings['search_in']['sku'] 		    = isset( $_POST['search_in']['sku'] ) ? ( int ) $_POST['search_in']['sku'] : '0';
		$settings['show_suggestion'] 			= isset( $_POST['show_suggestion'] ) ? ( int ) $_POST['show_suggestion'] : '0'; 

		if ( isset( $_POST['class'] ) )
			$settings['class'] = sanitize_text_field( $_POST['class'] );

		if ( isset( $_POST['id'] ) )
			$settings['id'] = sanitize_text_field( $_POST['id'] );

		// Save Live Search settings.
		update_option( 'wr_live_search', $settings );
	}

	/**
	 * Render HTML code for `Placeholder` field.
	 *
	 * @param   array  $args  Arguments passed to add_settings_field() function call to pass back to callback function.
	 *
	 * @return  void
	 */
	public static function setting_placeholder( $args ) {
		?>
		<div class="wr-live-search-setting-field">
			<input id="<?php echo esc_attr( $args['id'] ) . '-setting'; ?>" name="<?php echo esc_attr( $args['id'] ); ?>" type="text" value="<?php echo esc_attr( $args['data'] ); ?>">
		</div>
		<p><?php _e( 'Enter your search term suggestion here.', 'wr-live-search' ); ?></p>
		<?php
	}

	/**
	 * Render HTML code for `Show button` field.
	 *
	 * @return  void
	 */
	public static function setting_show_button( $args ) {
	?>
		<label>
			<input id="<?php echo esc_attr( $args['id'] ) . '-setting'; ?>" type="checkbox" <?php checked( $args['data'], '1' ); ?> onchange="jQuery( '.show-button' )[jQuery(this).is( ':checked' ) ? 'show' : 'hide'](); " name="<?php echo esc_attr( $args['id'] ); ?>" value="1" />
			<?php _e( 'Yes', 'wr-live-search' ); ?>
		</label>
		<p><?php _e( 'Enable this to display the search button.', 'wr-live-search' ); ?></p>
	<?php
	}

	/**
	 * Render HTML code for `Text button` field.
	 *
	 * @return  void
	 */
	public static function setting_text_button( $args ) {
		?>
		<div class="wr-live-search-setting-field">
			<input id="<?php echo esc_attr( $args['id'] ) . '-setting'; ?>" name="<?php echo esc_attr( $args['id'] ); ?>" type="text" value="<?php echo esc_attr( $args['data'] ); ?>" />
		</div>
		<p><?php _e( 'Enter your keyword for search button.', 'wr-live-search' ); ?></p>
		<?php
	}

	/**
	 * Render HTML code for `Show category list` field.
	 *
	 * @return  void
	 */
	public static function setting_show_category( $args ) {
	?>
		<label>
			<input id="<?php echo esc_attr( $args['id'] ) . '-setting'; ?>" type="checkbox" <?php checked( $args['data'], '1' ); ?> name="<?php echo esc_attr( $args['id'] ); ?>" value="1" />
			<?php _e( 'Yes', 'wr-live-search' ); ?>
		</label>
		<p><?php _e( 'Enable this to show drop-down category list.', 'wr-live-search' ); ?></p>
	<?php
	}

	/**
	 * Render HTML code for `Minimum number of characters` field.
	 *
	 * @return  void
	 */
	public static function setting_min_characters( $args ) {
		?>
		<div class="wr-live-search-setting-field">
			<input id="<?php echo esc_attr( $args['id'] ) . '-setting'; ?>" name="<?php echo esc_attr( $args['id'] ); ?>" type="number" value="<?php echo (int) $args['data']; ?>" style="width:50px;">
		</div>
		<p><?php _e( 'Enter your minimum number of characters for each search term.', 'wr-live-search' ); ?></p>
		<?php
	}

	/**
	 * Render HTML code for `Maximum number of results` field.
	 *
	 * @return  void
	 */
	public static function setting_max_results( $args ) {
		?>
		<div class="wr-live-search-setting-field">
			<input id="<?php echo esc_attr( $args['id'] ) . '-setting'; ?>" name="<?php echo esc_attr( $args['id'] ); ?>" type="number" value="<?php echo (int) $args['data']; ?>" style="width:50px;">
		</div>
		<p><?php _e( 'Enter your maximum number of search results for each search term.', 'wr-live-search' ); ?></p>
		<?php
	}

	/**
	 * Render HTML code for `Thumbnail size` field.
	 *
	 * @return  void
	 */
	public static function setting_thumb_size( $args ) {
		?>
		<div class="wr-live-search-setting-field">
			<input id="<?php echo esc_attr( $args['id'] ) . '-setting'; ?>" name="<?php echo esc_attr( $args['id'] ); ?>" type="number" value="<?php echo (int) $args['data']; ?>" style="width:50px;">
		</div>
		<p><?php _e( 'The width and height of thumbnail size. Defined in pixel.', 'wr-live-search' ); ?></p>
		<?php
	}

	/**
	 * Render HTML code for `Maximum number of results` field.
	 *
	 * @return  void
	 */
	public static function setting_search_in( $args ) {
		?>
		<div class="wr-live-search-setting-field" id="<?php echo esc_attr( $args['id'] ) . '-setting'; ?>">
			<div class="search-in-item"><label><input value="1" name="<?php echo esc_attr( $args['id'] ); ?>[title]" <?php checked( $args['data']['title'], 1 ); ?> type="checkbox" /> <?php _e( 'Title', 'wr-live-search' ); ?></label></div>
			<div class="search-in-item"><label><input value="1" name="<?php echo esc_attr( $args['id'] ); ?>[description]" <?php checked( $args['data']['description'], 1 ); ?> type="checkbox" /> <?php _e( 'Description', 'wr-live-search' ); ?></label></div>
			<div class="search-in-item"><label><input value="1" name="<?php echo esc_attr( $args['id'] ); ?>[content]" <?php checked( $args['data']['content'], 1 ); ?> type="checkbox" /> <?php _e( 'Content', 'wr-live-search' ); ?></label></div>
			<div class="search-in-item"><label><input value="1" name="<?php echo esc_attr( $args['id'] ); ?>[sku]" <?php checked( $args['data']['sku'], 1 ); ?> type="checkbox" /> <?php _e( 'SKU', 'wr-live-search' ); ?></label></div>
			<p><?php _e( 'Tick or un-tick each square box to enable or disable search terms scanning on "Title", "Description", or "Content" blocks.', 'wr-live-search' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render HTML code for `Maximum number of results` field.
	 *
	 * @return  void
	 */
	public static function setting_show_suggestion( $args ) {
	?>
		<label>
			<input id="<?php echo esc_attr( $args['id'] ) . '-setting'; ?>" type="checkbox" <?php checked( $args['data'], '1' ); ?> name="<?php echo esc_attr( $args['id'] ); ?>" value="1" />
			<?php _e( 'Yes', 'wr-live-search' ); ?>
		</label>
		<p><?php _e( 'Enable this to display search term suggestion.', 'wr-live-search' ); ?></p>
	<?php
	}

	/**
	 * Render HTML code for `Clas` field.
	 *
	 * @param array 	$args 		Args of add_settings_field() function for callback function.
	 *
	 * @return  void
	 */
	public static function setting_class( $args ) {
		?>
		<div class="wr-live-search-setting-field">
			<input id="<?php echo esc_attr( $args['id'] ) . '-setting'; ?>" name="<?php echo esc_attr( $args['id'] ); ?>" type="text" value="<?php echo esc_attr( $args['data'] ); ?>">
		</div>
		<?php
	}

	/**
	 * Render HTML code for `ID` field.
	 *
	 * @param array 	$args 		Args of add_settings_field() function for callback function.
	 *
	 * @return  void
	 */
	public static function setting_id( $args ) {
		?>
		<div class="wr-live-search-setting-field">
			<input id="<?php echo esc_attr( $args['id'] ) . '-setting'; ?>" name="<?php echo esc_attr( $args['id'] ); ?>" type="text" value="<?php echo esc_attr( $args['data'] ); ?>">
		</div>
		<?php
	}

	/**
	 * Add link setting in list plugin.
	 *
	 * @param string 	$links		List link current.
	 *
	 * @return  array
	 */
	public static function add_action_links( $links ) {
		$links['settings'] = '<a title="' . __( 'Open Live Search Settings', 'wr-live-search' ) . '" href="' . admin_url( 'options-general.php?page=' . WR_LS ) . '">' . __( 'Settings', 'wr-live-search' ) . '</a>';

		return $links;
	}

	/**
	 * Print HTML for setting page.
	 *
	 * @return  void
	 */
	public static function show() {
		// Register settings.
		self::register();

		// Load template for settings page.
		include_once WR_LS_PATH . 'templates/settings.php';
	}
}
