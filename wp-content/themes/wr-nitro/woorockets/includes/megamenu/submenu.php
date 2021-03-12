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
 * Sub menu.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Megamenu_Submenu {
	/**
	 * Get sub-menus.
	 *
	 * @param   string   $menu_type
	 * @param   integer  $menu_id
	 *
	 * @return  array
	 */
	public static function submenu( $menu_type, $menu_id ) {
		// Get all menu items.
		$menu_items = self::get_menu_items( $menu_type, $menu_id, 99 );

		// Prepare nav menu arguments.
		$args = array(
			'menu'        => $menu_type,
			'container'   => false,
			'menu_class'  => 'menu',
			'echo'        => true,
			'items_wrap'  => '<ul class="%2$s">%3$s</ul>',
			'count_items' => count( $menu_items ),
		);

		// Get mega menu data.
		$data = WR_Nitro_Megamenu::data();
		$data = $data[ $menu_type ][ $menu_id ];

		$submenu_items_elment = self::submenu_child( $menu_items, 0, ( object ) $args );

		return $submenu_items_elment;
	}

	/**
	 * Process sub menu items.
	 *
	 * @return  mixed
	 */
	public static function submenu_child() {
		$args   = func_get_args();
		$walker = new WR_Nitro_Megamenu_Walkersub;

		return call_user_func_array( array( &$walker, 'walk' ), $args );
	}

	/**
	 * Get widget content.
	 *
	 * @param   string  $widget_area
	 *
	 * @return  string
	 */
	public static function widget_content( $widget_area ) {
		ob_start();

		dynamic_sidebar( $widget_area );

		return ob_get_clean();
	}

	/**
	 * Get element product content.
	 *
	 * @param   string  $list_product
	 *
	 * @return  string
	 */
	public static function element_product( $list_product ) {

		if( ! $list_product || ! call_user_func( 'is_' . 'plugin' . '_active', 'woocommerce/woocommerce.php' ) ) return false;

		$list_product = explode( ',' , $list_product );

		$product_data = '';

		foreach( $list_product as $val ) {
			$product = wc_get_product( $val );

			if( $product ) {
				$product_data .= '
					<div class="product-item">
						<div class="img-product"><a href="' . get_permalink( $val ) .'">' . $product->get_image( 'shop_catalog' ) . '</a></div>
						<h5 class="title-product"><a href="' . get_permalink( $val ) .'">' . esc_attr( $product->get_title() ) . '</a></h5>
						<div class="price-product">' . $product->get_price_html() . '</div>
					</div>
				';
			}
		}

		return $product_data;
	}

	/**
	 * Get element category content.
	 *
	 * @param   string  $list_product
	 *
	 * @return  string
	 */
	public static function element_category( $list_category ) {
 		if( ! $list_category || ! call_user_func( 'is_' . 'plugin' . '_active', 'woocommerce/woocommerce.php' ) ) return false;

		$list_category = explode( ',' , $list_category );

		$category_data = '';

		foreach( $list_category as $val ) {
			$val = (int) $val;

			if( $val <= 0 ) continue;

			$categories = get_term( $val, 'product_cat', ARRAY_A );

			if( $categories ) {

				$image = WR_Nitro_Megamenu::get_image_term_product_category( $val, array( 300, 300 ) );
				$link = esc_url( get_term_link( $val, 'product_cat' ) );

				$category_data .= '
					<div class="category-item">
						<div class="img-category"><a href="' . $link .'"><img src="' . esc_url( $image ) . '" /></a></div>
						<h5 class="title-category"><a href="' . $link .'">' . esc_attr( $categories['name'] ) . '</a></h5>
						<div class="count-category">'. $categories['count'] . ' ' . ( $categories['count'] > 1 ? esc_html__( 'items', 'wr-nitro' ) : esc_html__( 'item', 'wr-nitro' ) ) . '</div>
					</div>
				';
			}
		}

		return $category_data;
	}

	/**
	 * Get menu items.
	 *
	 * @param   mixed    $menu
	 * @param   integer  $parent_id
	 * @param   integer  $depth
	 *
	 * @return  array
	 */
	public static function get_menu_items( $menu, $parent_id = 0, $depth = 1 ) {
		// Get all nav menu items.
		$menu_items = wp_get_nav_menu_items( $menu );
		$extracted_items = array();

		if ( $menu_items ) {
			$parents_set = array();

			foreach ( $menu_items as $item ) {
				if ( ! $parent_id ) {
					if ( $depth == 1 ) {
						// Get only the 1st level items.
						if ( ! $item->menu_item_parent ) {
							array_push( $extracted_items, $item );
						}
					}
				} else {
					// Get all sub menu items.
					if ( $item->menu_item_parent == $parent_id || in_array( $item->menu_item_parent, $parents_set ) ) {
						if ( $item->menu_item_parent == $parent_id ) {
							$parents_set[0] = $parent_id;
						}

						// Push current item id to parents list
						// used for calculating menuitem level
						// and get children menu items without recursiving.
						$sub_level = array_search( $item->menu_item_parent, $parents_set );
						$parents_set[ $sub_level + 1 ] = $item->ID;

						// Set level for current menu item.
						$item->sub_level = $sub_level + 1;

						// Place current item in the list.
						if ( $sub_level < $depth ) {
							array_push( $extracted_items, $item );
						}
					}
				}
			}
		}

		return $extracted_items;
	}
}
