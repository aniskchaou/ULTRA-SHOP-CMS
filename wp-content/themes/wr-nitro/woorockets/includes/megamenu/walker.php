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
 * Mega menu custom walker.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Megamenu_Walker extends Walker_Nav_Menu {
	private $style   = '';
	private $is_mega = false;

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 * @since 3.0.0
	 * @param string  $output Passed by reference. Used to append additional content.
	 * @param int     $depth  Depth of menu item. Used for padding.
	 * @param array   $args   An array of arguments. @see wp_nav_menu()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( $depth == 0 ) {
			if ( $this->is_mega ) {
				$output .= '';
			} else {
				$output .= '<ul class="sub-menu" ' . $this->style . '>';
			}
		} else if ( $this->is_mega ) {
			$output .= '';
		} else {
			$output .= '<ul class="sub-menu">';
		}
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 * @since 3.0.0
	 * @param string  $output Passed by reference. Used to append additional content.
	 * @param int     $depth  Depth of menu item. Used for padding.
	 * @param array   $args   An array of arguments. @see wp_nav_menu()
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( $depth == 0 ) {
			if ( $this->is_mega ) {
				$output .= '';
			} else {
				$output .= '</ul>';
			}
		} else if ( $this->is_mega ) {
			$output .= '';
		} else {
			$output .= '</ul>';
		}
	}

	/**
	 * Starting build menu element
	 *
	 * @param string  $output       Passed by reference. Used to append additional content.
	 * @param object  $item         Menu item data object.
	 * @param int     $depth        Depth of menu item. Used for padding.
	 * @param int     $current_page Menu item ID.
	 * @param object  $args
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $current_object_id = 0 ) {

		// Set true for variable check has submenu of menu current
		if( $depth == 1 ) {
			WR_Nitro_Megamenu::$active = true;
		}

		$el_styles = array();

		// Get menu item data
		$menu_id = isset( $args->menu->term_id ) ? $args->menu->term_id : $args->menu;

		$data = WR_Nitro_Megamenu::data();
		$data = isset( $data[ $menu_id ][ $item->ID ] ) ? $data[ $menu_id ][ $item->ID ] : array();
		$data['level'] = $depth;

		$data = WR_Nitro_Megamenu::merge_data_menu_item( $data );

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

		if( isset( $args->is_mobile ) && $args->is_mobile ) {
			$atts['class'] = isset( $atts['class'] ) ? $atts['class'] . ' menu-item-link' : 'menu-item-link';

			if( empty( $data['icon_position'] ) ) {
				$data['icon_position'] = 'left';
			};

			$atts['class'] = isset( $atts['class'] ) ? $atts['class'] . ' menu-item-link icon-' . esc_attr( $data['icon_position'] ) : 'menu-item-link icon-' . esc_attr( $data['icon_position'] );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output =
				'<div class="item-link-outer">
					<a ' . $attributes . '>
						' .
						( ( ! empty( $data['icon'] ) && ( $data['icon_position'] == 'left' || $data['icon_position'] == 'top-left' || $data['icon_position'] == 'top-center' || $data['icon_position'] == 'top-right' ) ) ? '<i class="menu-icon ' . esc_attr( $data['icon'] ) . '"></i>' : NULL ) .
						'
						<span class="menu_title">' . $title . '</span>
						' .
						( ( ! empty( $data['icon'] ) && $data['icon_position'] == 'right' ) ? '<i class="menu-icon ' . esc_attr( $data['icon'] ) . '"></i>' : NULL ) .
						( ( ! empty( $data['icon'] ) && ( $data['icon_position'] == 'bottom-left' || $data['icon_position'] == 'bottom-center' || $data['icon_position'] == 'bottom-right' ) ) ? '<i class="menu-icon ' . esc_attr( $data['icon'] ) . '"></i>' : NULL ) .
						'
					</a>' . ( $this->has_children ? '<i class="has-children-mobile fa fa-angle-down"></i>' : '' ) . '
				</div>';
		} else {
			if( empty( $data['icon_position'] ) ) {
				$data['icon_position'] = 'left';
			};

			$atts['class'] = isset( $atts['class'] ) ? $atts['class'] . ' menu-item-link icon-' . esc_attr( $data['icon_position'] ) : 'menu-item-link icon-' . esc_attr( $data['icon_position'] );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output =
				'<a ' . $attributes . ' >' .
					( ( ! empty( $data['icon'] ) && ( $data['icon_position'] == 'left' || $data['icon_position'] == 'top-left' || $data['icon_position'] == 'top-center' || $data['icon_position'] == 'top-right' ) ) ? '<i class="menu-icon ' . esc_attr( $data['icon'] ) . '"></i>' : NULL ) .
					'<span class="menu_title">' . $title . '</span>' .
					( ( ! empty( $data['icon'] ) && $data['icon_position'] == 'right' ) ? '<i class="menu-icon ' . esc_attr( $data['icon'] ) . '"></i>' : NULL ) .
					( ( $depth == 0 && $this->has_children ) ? '<i class="icon-has-children fa fa-angle-down"></i>' : NULL ) .
					( ( $depth > 0 && $this->has_children ) ? '<i class="icon-has-children fa fa-angle-right"></i>' : NULL ) .
					( ( ! empty( $data['icon'] ) && ( $data['icon_position'] == 'bottom-left' || $data['icon_position'] == 'bottom-center' || $data['icon_position'] == 'bottom-right' ) ) ? '<i class="menu-icon ' . esc_attr( $data['icon'] ) . '"></i>' : NULL ) .
				'</a>';
		}

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$style_inline = array();
		if ( $depth == 0 ) {
			if ( isset( $args->allow_hb ) ) {
				$id_menu = $menu_id;
			} else {
				// Get location menu current
				$locations = get_nav_menu_locations();

				$id_menu = $locations[$args->theme_location];
			}

			$count_menu_items = count( WR_Nitro_Megamenu_Submenu::get_menu_items( $id_menu, $item->ID, 1 ) );

			if ( $data['active'] == 1 && isset( $args->megamenu ) && $args->megamenu && $count_menu_items ) {
				$this->is_mega = true;
				$classes[] = 'wrmm-item';

				if ( $data['width_type'] == 'fixed' && (int) $data['width'] ) {
					$data_width = 'data-width="' . (int) $data['width'] . '"';
				} elseif( $data['width_type'] == 'full-width' ) {
					$data_width = 'data-width="full-width"';
				} else {
					$data_width = 'data-width="full"';
				}

				if ( $data['background_color'] ) {
					$style_inline[] = 'background-color:' . esc_attr( $data['background_color'] );
				}
				if ( $data['background_image'] ) {
					$style_inline[] = 'background-image: url(' . esc_url( $data['background_image'] ) . ')';
					$style_inline[] = 'background-size: ' . ( $data['background_size'] ? esc_attr( $data['background_size'] ) : 'inherit' );
					$style_inline[] = 'background-position: ' . ( $data['background_position'] ? esc_attr( $data['background_position'] ) : 'left top' );
					$style_inline[] = 'background-repeat: ' . ( $data['background_repeat'] ? esc_attr( $data['background_repeat'] ) : 'no-repeat' );
				}

				$item_output .= '<div ' . $data_width . '  class="mm-container-outer"><div class="mm-container" ' . ( $style_inline ? 'style="' . implode( ';', $style_inline ) . '"' : NULL ) . '>';

				$submenu_items_elment = WR_Nitro_Megamenu_Submenu::submenu( $id_menu, $item->ID );

				if ( $submenu_items_elment ) {
					$item_output .= $submenu_items_elment . '</ul>';
				}

				$item_output .= '</div></div>';
			} else {
				$classes[] = 'menu-default';
				$this->is_mega = false;
			}
		}

		$classes[] = 'menu-item-lv' . absint( $depth );

		// Generate class and style attribute
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$el_styles = $el_styles ? ' style="' . esc_attr( join( ';', $el_styles ) ) . '"' : '';


		if ( $depth != 0 && $this->is_mega ) {
			$output .= '';
			$item_output = '';
		} else {
 			$output .= '<li ' . $class_names . '>';
 		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 * @since 3.0.0
	 * @param string  $output Passed by reference. Used to append additional content.
	 * @param int     $depth  Depth of menu item. Used for padding.
	 * @param array   $args   An array of arguments. @see wp_nav_menu()
	 */
	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		if ( $depth != 0 && $this->is_mega ) {
			$output .= '';
		} else {
 			$output .= '</li>';
 		}
	}
}
