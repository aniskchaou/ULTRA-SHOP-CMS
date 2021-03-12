<?php
/**
 * @version    1.0
 * @package    Nitro_Toolkit
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

/**
 * Class Toolkit Post Type
 *
 * @since    1.0
 */
class Nitro_Toolkit_Posttype {
	/**
	 * Constructor loads API functions, adds required wp actions.
	 *
	 * @return  void
	 */
	public function __construct() {
		add_action( 'init', array( &$this, 'register_header' ) );
	}

	/**
	 * Register the header custom post type.
	 *
	 * @return  void
	 */
	public static function register_header() {
		register_post_type(
			'header_builder',
			array(
				'labels' => array(
					'name'               => __( 'Header', 'nitro-toolkit' ),
					'singular_name'      => __( 'Header', 'nitro-toolkit' ),
					'menu_name'          => __( 'Header', 'nitro-toolkit' ),
					'name_admin_bar'     => __( 'Header', 'nitro-toolkit' ),
					'add_new'            => __( 'Add New', 'nitro-toolkit' ),
					'add_new_item'       => __( 'Add New header', 'nitro-toolkit' ),
					'new_item'           => __( 'New header', 'nitro-toolkit' ),
					'edit_item'          => __( 'Edit header', 'nitro-toolkit' ),
					'view_item'          => __( 'View header', 'nitro-toolkit' ),
					'all_items'          => __( 'Header Builder', 'nitro-toolkit' ),
					'search_items'       => __( 'Search header', 'nitro-toolkit' ),
					'parent_item_colon'  => __( 'Parent header:', 'nitro-toolkit' ),
					'not_found'          => __( 'No header found.', 'nitro-toolkit' ),
					'not_found_in_trash' => __( 'No header found in Trash.', 'nitro-toolkit' )
				),
				'public'              => false,
				'show_ui'             => true,
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_in_menu'        => 'wr-intro',
				'hierarchical'        => false,
				'rewrite'             => false,
				'query_var'           => false,
				'has_archive'         => true,
				'supports'            => array( 'title' ),
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => true,
				'hierarchical'        => true, // Set true for hide excerpt in list
			)
		);
	}
}
$register_post_type = new Nitro_Toolkit_Posttype;