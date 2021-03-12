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
 * Mega menu custom walker for sub menu.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Megamenu_Walkersub extends Walker_Nav_Menu {
	var $is_not_insert_first = true;
	var $order_column = 0;

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		// Get data menu item
		$megamenu = WR_Nitro_Megamenu::data();
		$data = isset( $megamenu[ $args->menu ][ $item->ID ] ) ? $megamenu[ $args->menu ][ $item->ID ] : NULL;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		if( $item->sub_level == 1 ) {
			/* Parse get row percent */
			$row_layout   = isset( $megamenu[ $args->menu ][ $item->menu_item_parent ]['row_layout'] ) ? $megamenu[ $args->menu ][ $item->menu_item_parent ]['row_layout'] : '';
			$row_layout   = explode( ' + ', $row_layout );
			$row_layout   = isset( $row_layout[ $this->order_column ] ) ? $row_layout[ $this->order_column ] : '1/1';
			$row_layout   = explode( '/', $row_layout );
			$row_layout_1 = ( isset( $row_layout[0] ) && absint( $row_layout[0] ) > 0 ) ? absint( $row_layout[0] ) : 1;
			$row_layout_2 = ( isset( $row_layout[1] ) && absint( $row_layout[1] ) > 0 ) ? absint( $row_layout[1] ) : 1;

			$row_percent  = ( $row_layout_1 / $row_layout_2 ) * 100;

			if( $this->is_not_insert_first ) {
				$output .= '<ul class="mm-col" style="width:' . $row_percent . '%">';

				$this->is_not_insert_first = false;
			} else {
				$output .= '</ul><ul class="mm-col" style="width:' . $row_percent . '%">';
			}

			$this->order_column++;
		} else if( $item->sub_level == 0 ) {
			$this->order_column = 0;
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= $indent . '<li ' . $value . $class_names . '>';

		// Set tag a
		$item_output = ( isset( $args->before ) ? $args->before : '' );

		if ( ! ( $item->sub_level == 1 && isset( $data['disable_title'] ) && $data['disable_title'] == 1 ) ) {

			$atts = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
			$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
			$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

			/**
			 * Filters the HTML attributes applied to a menu item's anchor element.
			 *
			 * @since 3.6.0
			 * @since 4.1.0 The `$depth` parameter was added.
			 *
			 * @param array $atts {
			 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
			 *
			 *     @type string $title  Title attribute.
			 *     @type string $target Target attribute.
			 *     @type string $rel    The rel attribute.
			 *     @type string $href   The href attribute.
			 * }
			 * @param object $item  The current menu item.
			 * @param array  $args  An array of wp_nav_menu() arguments.
			 * @param int    $depth Depth of menu item. Used for padding.
			 */
			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

			/** This filter is documented in wp-includes/post-template.php */
			$title = apply_filters( 'the_title', $item->title, $item->ID );

			/**
			 * Filters a menu item's title.
			 *
			 * @since 4.4.0
			 *
			 * @param string $title The menu item's title.
			 * @param object $item  The current menu item.
			 * @param array  $args  An array of wp_nav_menu() arguments.
			 * @param int    $depth Depth of menu item. Used for padding.
			 */
			$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

			$atts['class'] = isset( $atts['class'] ) ? $atts['class'] . ' menu-item-link' : 'menu-item-link';


			if( $this->has_children ){
				$atts['class'] .= ' has-children';
			}

			if( $item->sub_level == 1 ){
				$atts['class'] .= ' title-column';
			}

			if( ! empty( $data['icon'] ) ) {
				$atts['class'] .= ' icon-left';
			}

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output .= '<a ' . $attributes . ' >'
			. ( (  ! empty( $data['icon'] ) && $data['icon'] ) ? '<i class="menu-icon ' . esc_attr( $data['icon'] ) . '"></i>' : NULL )
			. '<span class="menu_title">' . $title . '</span>'
			. ( $this->has_children ? '<i class="fa fa-angle-right mm-has-children"></i>' : NULL )
			.'</a>' ;
		}

		// Insert element
		if ( $item->sub_level == 1 && isset( $data['element_type'] ) && $data['element_type'] && isset( $data['element_data'] ) && $data['element_data'] ) {
			$element_content = NULL;
			if ( $data['element_type'] == 'element-text' ) {
				$element_content = do_shortcode( $data['element_data'] ) ;
			} elseif ( $data['element_type'] == 'element-widget' ) {
				$element_content = WR_Nitro_Megamenu_Submenu::widget_content( $data['element_data'] );
			} elseif ( $data['element_type'] == 'element-products' ) {
				$element_content = WR_Nitro_Megamenu_Submenu::element_product( $data['element_data'] );
			} elseif ( $data['element_type'] == 'element-categories' ) {
				$element_content = WR_Nitro_Megamenu_Submenu::element_category( $data['element_data'] );
			}

			$item_output .= $element_content ? '<div class="content-element ' . $data['element_type'] . '">' . $element_content . '</div>' : NULL;
		}

		$item_output .= ( isset( $args->after ) ? $args->after : '' );

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

}
