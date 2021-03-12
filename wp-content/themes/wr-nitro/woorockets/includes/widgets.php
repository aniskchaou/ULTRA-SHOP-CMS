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

/**
 * Pluggable initialization class.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Widgets {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	/**
	 * Reference of the WordPress core object.
	 *
	 * @var  object
	 */
	protected static $wp;

	/**
	 * Variable to hold the initial global 'wp_query' object.
	 *
	 * @var  object
	 */
	protected static $wp_query;

	/**
	 * Variable to hold the initial global 'post' object.
	 *
	 * @var  object
	 */
	protected static $post;

	/**
	 * Initialize widgets functions.
	 *
	 * @return  void
	 */
	public static function initialize() {
		// If in admin screen, initialize widget assignment and other widget related action.
		if ( is_admin() ) {
			// Add action to load additional assets for the 'Widgets' screen.
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts') );

			// Add action to print the widget assignment form for every widget in the 'Widgets' screen.
			add_action( 'in_widget_form', array( __CLASS__, 'in_widget_form' ), 10, 3 );

			// Add action to initialize the widget assignment form to append to the edit widget form.
			add_action( 'admin_print_footer_scripts', array( __CLASS__, 'admin_print_footer_scripts' ) );

			// Register Ajax actions to work with the widget assignment form.
			add_action( 'wp_ajax_widget-assignment-search-target', array( __CLASS__, 'search_target' ) );
			add_action( 'wp_ajax_widget-assignment-load-target'  , array( __CLASS__, 'load_target'   ) );

			// Add filter to save widget assignment data.
			add_filter( 'widget_update_callback', array( __CLASS__, 'update_widget' ), 10, 3 );
		}

		// Add action to grab the initial global 'wp_query' and 'post' object.
		add_action( 'wp', array( __CLASS__, 'wp' ) );

		// Add action to register Nitro widgets.
		add_action( 'widgets_init', array( __CLASS__, 'register') );

		// Add filter to show/hide widget depending on assignment settings.
		add_filter( 'widget_display_callback', array( __CLASS__, 'prepare_widget' ) );
	}

	/**
	 * Load additional assets for the 'Widgets' screen.
	 *
	 * @return  void
	 */
	public static function admin_enqueue_scripts() {
		global $pagenow;

		if ( 'widgets.php' == $pagenow || 'customize.php' == $pagenow ) {
			// Enqueue WordPress's color picker.
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			// Enqueue Select2.
			wp_enqueue_style( 'wr-select2', get_template_directory_uri() . '/assets/3rd-party/select2/select2.min.css' );
			wp_enqueue_script( 'wr-select2', get_template_directory_uri() . '/assets/3rd-party/select2/select2.min.js' );
		}
	}

	/**
	 * Print the widget assignment form for every widget in the 'Widgets' screen.
	 *
	 * @param   WP_Widget  $widget    The widget instance, passed by reference.
	 * @param   null       $return    Return null if new fields are added.
	 * @param   array      $instance  An array of the widget's settings.
	 *
	 * @return  void
	 */
	public static function in_widget_form( $widget, $return, $instance ) {
		// Generate ID base.
		$id_base = 'widget-' . $widget->id_base . '-' . $widget->number . '-assignment-';

		// Generate name base.
		$name_base = 'widget-' . $widget->id_base . '[' . $widget->number . '][assignment]';

		// Get current assignment data.
		$assignment = wp_parse_args( isset( $instance['assignment'] ) ? $instance['assignment'] : '', array(
			'audience'   => 'all',
			'visibility' => 'hidden on',
			'target'     => ''
		) );
		?>
		<div class="wr-nitro-widget-assignment">
			<p>
				<label for="<?php echo esc_attr( $id_base . 'audience' ); ?>">
					<?php _e( 'Select audience', 'wr-nitro' ); ?>:
				</label>
				<select class="widefat wr-nitro-widget-assignment-audience" id="<?php
					echo esc_attr( $id_base . 'audience' );
				?>" name="<?php
					echo esc_attr( $name_base . '[audience]' );
				?>">
					<option value="all" <?php
						selected( 'all', $assignment['audience'] );
					?>>
						<?php _e( 'All users', 'wr-nitro' ); ?>
					</option>
					<option value="logged in" <?php
						selected( 'logged in', $assignment['audience'] );
					?>>
						<?php _e( 'Logged-in users', 'wr-nitro' ); ?>
					</option>
					<option value="not logged in" <?php
						selected( 'not logged in', $assignment['audience'] );
					?>>
						<?php _e( 'Not logged-in users', 'wr-nitro' ); ?>
					</option>
				</select>
			</p>
			<p>
				<label for="<?php echo esc_attr( $id_base . 'visibility' ); ?>">
					<?php _e( 'Visibility type', 'wr-nitro' ); ?>:
				</label>
				<select class="widefat wr-nitro-widget-assignment-visibility" id="<?php
					echo esc_attr( $id_base . 'visibility' );
				?>" name="<?php
					echo esc_attr( $name_base . '[visibility]' );
				?>">
					<option value="visible on" <?php
						selected( 'visible on', $assignment['visibility'] );
					?>>
						<?php _e( 'Visible on selected target', 'wr-nitro' ); ?>
					</option>
					<option value="hidden on" <?php
						selected( 'hidden on', $assignment['visibility'] );
					?>>
						<?php _e( 'Hidden on selected target', 'wr-nitro' ); ?>
					</option>
				</select>
			</p>
			<p class="wr-nitro-widget-assignment-target-container loading">
				<label for="<?php echo esc_attr( $id_base . 'target' ); ?>">
					<?php _e( 'Select target', 'wr-nitro' ); ?>:
				</label>
				<input type="hidden" class="widefat wr-nitro-widget-assignment-target" id="<?php
					echo esc_attr( $id_base . 'target' );
				?>" name="<?php
					echo esc_attr( $name_base . '[target]' );
				?>" value="<?php
					echo esc_attr( $assignment['target'] );
				?>" />
			</p>
		</div>
		<?php
		return $return;
	}

	/**
	 * Print necessary inline script to initialize the widget assignment form.
	 *
	 * @return  void
	 */
	public static function admin_print_footer_scripts() {
		// Generate Ajax URL to search for target.
		$ajax_url = admin_url( 'admin-ajax.php?action=widget-assignment-search-target' );
		?>
		<style type="text/css">
			.wr-nitro-widget-assignment-target-container label {
				padding: initial;
				background: initial;
			}
			.wr-nitro-widget-assignment-target-container.loading label {
				padding: 1px 30px 1px 0;
				background: url(<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>) 100% 50% no-repeat;
			}
			.wr-nitro-widget-assignment-dropdown-selector .select2-results-dept-1 .select2-result-label {
				padding: 5px 7px;
			}
			/* Hide widget assignment on the customize screen. */
			.customize-control-widget_form .wr-nitro-widget-assignment {
				display: none;
			}
		</style>
		<script type="text/javascript">
			(function($) {
				function init_select2(selector) {
					$(selector).select2({
						minimumInputLength: 2,
						closeOnSelect: false,
						placeholder: "<?php _e( 'Search for target...', 'wr-nitro' ); ?>",
						multiple: true,
						ajax: {
							url: "<?php echo esc_url( $ajax_url ); ?>",
							dataType: 'json',
							quietMillis: 500,
							data: function(term) {
								return {
									search: term
								};
							},
							results: function(result) {
								return {
									results: result.success ? result.data : []
								};
							},
							cache: true
						},
						formatResult: function(item) {
							if ( ! item.id) {
								return '<strong>' + item.text + '</strong>';
							}

							// Store item for later reference.
							if ( ! window['wr-nitro-widget-assignment-target'] ) {
								window['wr-nitro-widget-assignment-target'] = {};
							}

							if ( ! window['wr-nitro-widget-assignment-target'][item.id] ) {
								window['wr-nitro-widget-assignment-target'][item.id] = item.text;
							}

							// Get current selection.
							var selection = $(selector).select2('val');

							// Check if item is currently selected.
							var className = '';

							if (selection.indexOf(item.id) > -1) {
								className = ' selected';
							}

							return '<div class="wr-nitro-widget-assignment-target-item'
								+ className
								+ '" data-id="'
								+ item.id +
								'" onClick="jQuery(this).toggleClass(\'selected\')">'
								+ ' ' + item.text
								+ '</div>';
						},
						initSelection: function(element, callback) {
							var target = window['wr-nitro-widget-assignment-target'] || {},
								selection = $(element).select2('val'),
								selected = [],
								ids = [];

							for (var i = 0; i < selection.length; i++) {
								if (target[selection[i]]) {
									selected.push({
										id: selection[i],
										text: target[selection[i]]
									});
								} else {
									ids.push(selection[i]);
								}
							}

							if (selected.length == selection.length) {
								callback(selected);

								return setTimeout(function() {
									$(selector).trigger('scroll');
								}, 50);
							}

							$.ajax({
								url: "<?php echo esc_url( $ajax_url ); ?>",
								dataType: 'json',
								data: {
									action: 'widget-assignment-load-target',
									ids: ids
								},
								complete: function(response) {
									var res = response.responseJSON;

									if (res.success) {
										if ( ! window['wr-nitro-widget-assignment-target'] ) {
											window['wr-nitro-widget-assignment-target'] = {};
										}

										for (var i = 0; i < res.data.length; i++) {
											var id = res.data[i].id,
												text = res.data[i].text;

											if ( ! window['wr-nitro-widget-assignment-target'][id]) {
												window['wr-nitro-widget-assignment-target'][id] = text;
											}
										}

										callback(selected.concat(res.data));

										return setTimeout(function() {
											$(selector).trigger('scroll');
										}, 50);
									}
								}
							});
						},
						nextSearchTerm: function(selectedObject, currentSearchTerm) {
							return currentSearchTerm;
						}
					}).on('select2-selecting', function(event) {
						event.preventDefault();

						setTimeout($.proxy(function() {
							var selected = $(this).select2('val'), index;

							$('.wr-nitro-widget-assignment-target-item').each(function(i, e) {
								index = selected.indexOf($(e).attr('data-id'));

								if ($(e).hasClass('selected')) {
									if (index < 0) {
										selected.push($(e).attr('data-id'));
									}
								} else {
									if (index > -1) {
										delete selected[index];
									}
								}
							});

							$(this).select2('val', selected);
						}, this), 200);
					});

					// Clear loading state.
					function clear_loading() {
						var processing = false;

						if ( ! $(selector).parent().children('.select2-container').length) {
							processing = true;
						}

						else if (selector.value != '' && ! $(selector).parent().find('.select2-search-choice').length) {
							processing = true;
						}

						if (processing) {
							return setTimeout(clear_loading, 100);
						}

						$(selector).parent().removeClass('loading');
					}

					clear_loading();
				}

				$(document.body).on('click', '.widget-top', function() {
					setTimeout($.proxy(function() {
						if ($(this).parent().hasClass('open')) {
							$(this).parent().find('.wr-nitro-widget-assignment-target').each(function(i, e) {
								if ( ! $(e).data('select2')) {
									init_select2(e);
								}
							});
						}
					}, this), 500);
				});

				$(document).ajaxComplete(function(event, xhr, settings) {
					if (settings.url.indexOf('admin-ajax.php') > -1) {
						if (settings.data && settings.data.indexOf('&action=save-widget&') > -1) {
							$('.widget.open .wr-nitro-widget-assignment-target').each(function(i, e) {
								if ( ! $(e).data('select2')) {
									init_select2(e);
								}
							});
						}
					}
				});
			})(jQuery);
		</script>
		<?php
	}

	/**
	 * Search target for assigning widget to.
	 *
	 * @return  void
	 */
	public static function search_target() {
		// Get keyword to search for.
		$search = isset( $_REQUEST['search'] ) ? sanitize_text_field( $_REQUEST['search'] ) : '';

		// Make sure keyword not empty and has at least 2 characters.
		if ( ! empty( $search ) && strlen( $search ) >= 2 ) {
			// Search all registered custom post types for items that match the specified keyword.
			$post_types = get_post_types();

			foreach ( $post_types as $post_type ) {
				// Skip searching in attachments.
				if ( 'attachment' == $post_type ) {
					continue;
				}

				// Get post type object.
				$post_type = get_post_type_object( $post_type );

				if ( $post_type->public && ! $post_type->exclude_from_search ) {
					// Search post type for all items that match the specified keyword.
					$results = new WP_Query( array(
						'post_status' => 'publish',
						'post_type'   => $post_type->name,
						'nopaging'    => true,
						's'           => $search,
						'suppress_filters' => true,
					) );

					if ( $results->post_count > 0 ) {
						foreach ( $results->posts as $item ) {
							$items[] = array(
								'id'   => "cpt__{$post_type->name}::{$item->ID}",
								'text' => $item->post_title
							);
						}

						$return[] = array(
							'text'     => $post_type->label,
							'children' => $items
						);
					}

					// Reset item array.
					$items = array();
				}
			}

			// Search terms of all registered taxonomies for items that match the specified keyword.
			$taxonomies = get_taxonomies();

			foreach ( $taxonomies as $taxonomy ) {
				// Skip searching in WordPress's nav menus, link categories and post formats.
				if ( in_array( $taxonomy, array( 'nav_menu', 'link_category', 'post_format' ) ) ) {
					continue;
				}

				// Skip searching in WooCommerce's product types and shipping classes.
				elseif ( in_array( $taxonomy, array( 'product_type', 'product_shipping_class' ) ) ) {
					continue;
				}

				// Skip searching in WooCommerce's product attributes.
				elseif ( 0 === strpos( $taxonomy, 'pa_' ) ) {
					continue;
				}

				// Get taxonomy object.
				$taxonomy = get_taxonomy( $taxonomy );

				if ( $taxonomy->public && $taxonomy->publicly_queryable ) {
					// Get terms that match the specified keyword.
					$results = get_terms( array(
						'taxonomy' => $taxonomy->name,
						'number'   => 0, // Return all terms.
						'search'   => $search
					) );

					if ( count( $results ) ) {
						foreach ( $results as $item ) {
							$items[] = array(
								'id'   => "tax__{$taxonomy->name}::{$item->term_id}",
								'text' => $item->name
							);
						}

						$return[] = array(
							'text'     => sprintf( __( '%1$s (slug: %2$s)', 'wr-nitro' ), $taxonomy->label, $taxonomy->name ),
							'children' => $items
						);
					}

					// Reset item array.
					$items = array();
				}
			}
		}

		wp_send_json_success( isset( $return ) ?  $return : array() );
	}

	/**
	 * Load data for the specified targets.
	 *
	 * @return  void
	 */
	public static function load_target() {
		// Get request variable.
		$ids = isset( $_REQUEST['ids'] ) ? $_REQUEST['ids'] : array();

		foreach ( $ids as $item ) {
			list( $type, $item ) = explode( '::', $item, 2 );
			list( $type, $slug ) = explode( '__', $type, 2 );

			switch ( $type ) {
				case 'cpt':
					// Get all registered post types.
					$post_types = get_post_types();

					if ( in_array( $slug, $post_types ) ) {
						$item = get_post( $item );

						$return[] = array(
							'id'   => "{$type}__{$slug}::{$item->ID}",
							'text' => $item->post_title
						);
					}
				break;

				case 'tax':
					// Get all registered taxonomies.
					$taxonomies = get_taxonomies();

					if ( in_array( $slug, $taxonomies ) ) {
						$item = get_term( $item, $slug );

						$return[] = array(
							'id'   => "{$type}__{$slug}::{$item->term_id}",
							'text' => $item->name
						);
					}
				break;
			}
		}

		wp_send_json_success( isset( $return ) ?  $return : array() );
	}

	/**
	 * Update widget settings.
	 *
	 * @param   array  $instance      The current widget instance's settings.
	 * @param   array  $new_instance  Array of new widget settings.
	 * @param   array  $old_instance  Array of old widget settings.
	 *
	 * @return  void
	 */
	public static function update_widget( $instance, $new_instance, $old_instance ) {
		if (
			! isset( $instance['assignment'] )
			||
			( json_encode( $instance['assignment'] ) != json_encode( $new_instance['assignment'] ) )
		) {
			$instance['assignment'] = $new_instance['assignment'];
		}

		return $instance;
	}

	/**
	 * Grab the initial global 'wp_query' and 'post' object.
	 *
	 * @param   object  &$wp  WordPress core object.
	 *
	 * @return  void
	 */
	public static function wp( &$wp ) {
		self::$wp =& $wp;
		self::$wp_query = $GLOBALS['wp_query'];
		self::$post = $GLOBALS['post'];
	}

	/**
	 * Register Nitro widgets.
	 *
	 * @return  void
	 */
	public static function register() {
	    foreach ( array(
            'WR_Nitro_Widgets_Instagram',
            'WR_Nitro_Widgets_Recent_Posts',
            'WR_Nitro_Widgets_Recent_Comments',
            'WR_Nitro_Widgets_Subscription',
            'WR_Nitro_Widgets_Social'
        ) as $widget ) {
	        call_user_func('register' . '_widget', $widget);
	    }
	}

	/**
	 * Show / hide widget depending on assignment settings.
	 *
	 * @param   array  $instance  The current widget instance's settings.
	 *
	 * @return  mixed  Widget instance's settings if widget is visible, or boolean FALSE otherwise.
	 */
	public static function prepare_widget( $instance ) {
		// Stop preparing if widget instance does not have assignment settings.
		if ( ! isset( $instance['assignment'] ) ) {
			return $instance;
		}

		// Get assignment settings.
		$assignment = wp_parse_args( $instance['assignment'], array(
			'audience'   => 'all',
			'visibility' => 'hidden on',
			'target'     => ''
		) );

		// Check if widget is visible for the current audience.
		switch ( $assignment['audience'] ) {
			case 'logged in':
				if ( ! is_user_logged_in() ) {
					return false;
				}
			break;

			case 'not logged in':
				if ( is_user_logged_in() ) {
					return false;
				}
			break;
		}

		// If widget is not assigned to any page, check the 'visibility' option to see if widget is visible.
		if ( empty( $assignment['target'] ) ) {
			if ( 'visible on' == $assignment['visibility'] ) {
				return false;
			}
		}

		// Otherwise...
		else {
			// Get all taxonomies.
			$taxonomies = array_map( 'get_taxonomy', get_taxonomies() );

			// Check if widget is assigned to the current page.
			$assigned = false;

			foreach ( array_map( 'trim', explode( ',', $assignment['target'] ) ) as $item ) {
				list( $type, $item ) = explode( '::', $item, 2 );
				list( $type, $slug ) = explode( '__', $type, 2 );

				switch ( $type ) {
					case 'cpt':
						if ( is_singular( $slug ) && self::$post ) {
							if ( $slug == self::$post->post_type && $item == self::$post->ID ) {
								$assigned = true;
							}
						}

						// Handle special page, such as WooCommerce shop page.
						elseif ( false === strpos( self::$wp->request, '/' ) ) {
							// Get post slug of the selected page.
							$selected = get_post( $item );

							if ( $selected && $selected->post_name == self::$wp->request ) {
								$assigned = true;
							}
						}
					break;

					case 'tax':
						if ( isset( $taxonomies[ $slug ] ) ) {
							// Check if request contains query variable of the current taxonomy.
							$taxonomy = $taxonomies[ $slug ];

							if ( $taxonomy->query_var && isset( self::$wp_query->query_vars[ $taxonomy->query_var ] ) ) {
								// Check if the assigned term is requested.
								$terms = explode( ',', self::$wp_query->query_vars[ $taxonomy->query_var ] );
								$item  = get_term( $item, $slug );

								if ( in_array( $item->slug, $terms ) ) {
									$assigned = true;
								}
							}
						}
					break;
				}

				if ( $assigned ) {
					break;
				}
			}

			// Check the 'visibility' option to see if widget is visible on the current page.
			if (
				( $assigned && 'hidden on' == $assignment['visibility'] )
				||
				( ! $assigned && 'visible on' == $assignment['visibility'] )
			) {
				return false;
			}
		}

		return $instance;
	}
}
