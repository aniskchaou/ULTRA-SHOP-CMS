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

global $woocommerce, $wp_registered_sidebars;

// Get theme options
$wr_nitro_options = WR_Nitro::get_options();

// Get header data
$wr_nitro_header_data  = WR_Nitro::get_header();

if( $wr_nitro_header_data == 'not_select_defaut' ) {
	return WR_Nitro_Header_Builder::prop( 'html', '<p style="text-align: center; font-weight: bold; font-size: 16px; color: #cc0000; margin: 0; padding: 10px 0;">' . esc_html__( 'Please set a Header to default', 'wr-nitro' ) . '</p>' );
} elseif( $wr_nitro_header_data == 'empty' ) {
	return WR_Nitro_Header_Builder::prop( 'html', '' );
}

$wr_nitro_header_data = json_decode( $wr_nitro_header_data, TRUE );
WR_Nitro_Header_Builder::prop( 'id', absint( $wr_nitro_header_data['id'] ) );

$wr_nitro_header_css = $wr_nitro_header_html = $wr_nitro_header_fonts = array();

$wr_is_mobile = false;

// Check device
if ( wp_is_mobile() ) {
	$wr_nitro_header_data                     = isset( $wr_nitro_header_data ['mobile'] ) ? $wr_nitro_header_data ['mobile'] : '';
	$wr_nitro_header_data['settings']['type'] = 'horizontal';
	$wr_is_mobile                             = true;
} else {
	$wr_nitro_header_data = isset( $wr_nitro_header_data ['desktop'] ) ? $wr_nitro_header_data ['desktop'] : '';
}

$wr_data_allow = WR_Nitro_Header_Builder::data_allow();

/* Render style settings header */
$wr_hb_settings = WR_Nitro_Header_Builder::array_fillter_recursive(
	$wr_data_allow['settings'],
	isset( $wr_nitro_header_data['settings'] ) ? $wr_nitro_header_data['settings'] : array()
);

$wr_item_css    = '';

$wr_hb_settings['style'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_settings['style'] );

foreach( $wr_hb_settings['style'] as $key => $val ) {
	if( $val === '' ) continue;

	$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key ) );
	if ( $key == 'backgroundImage' ) {
		$wr_item_css .= $val ? ( esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val ) . '");' ) : '';
	} elseif( $key == 'width' ) {
		if( $wr_hb_settings['type'] == 'vertical' ) {
			if( $wr_hb_settings['unit'] == '%' ) {
				$wr_nitro_header_css[] = '.header { width:' . (int) $val . '%}';
			} else {
				$wr_nitro_header_css[] = '.header { width:' . (int) $val . 'px}';
			}
		} else if ( $wr_hb_settings['type'] == 'horizontal' && $wr_hb_settings['position'] == 'fixed' ) {
			$wr_total_left_right = absint( $wr_hb_settings['style']['marginLeft'] ) + absint( $wr_hb_settings['style']['marginRight'] );

			if( $wr_total_left_right > 0 )
				$wr_nitro_header_css[] = '.header-outer.fixed .header { width: calc(100% - ' . $wr_total_left_right . 'px); width: -webkit-calc(100% - ' . $wr_total_left_right . 'px)}';
		}
	} elseif ( is_numeric( $val ) ) {
		$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val . 'px;';
	} else {
		$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . $val . ';';
	}
}
if( $wr_item_css ) {
	$wr_nitro_header_css[] = ' .header ' . ( ( $wr_hb_settings['type'] == 'vertical' ) ? '.hb-section-outer' : NULL ) . ' {' . $wr_item_css . '}';
}

// Show hide fixed
if( $wr_hb_settings['position'] == 'fixed' ) {

	$wr_show_hide = ( $wr_hb_settings['showHideFixed'] ) ? esc_attr( $wr_hb_settings['showHideFixed'] ) : 'hide';

	$wr_class_position = ( $wr_show_hide == 'show' ) ? 'inherit' : 'fixed';

	// Check is home
	if ( is_home() || is_front_page() ) {

		// Default homepage
		if ( is_front_page() && is_home() ) {
			if ( isset( $wr_hb_settings['fixedList']['miscellaneous']['home'] ) && $wr_hb_settings['fixedList']['miscellaneous']['home'] == 1 ) {
				$wr_class_position = ( $wr_show_hide == 'show' ) ? 'fixed' : 'inherit';
			}

		// Static homepage
		} elseif ( is_front_page() ) {
			if ( isset( $wr_hb_settings['fixedList']['miscellaneous']['home'] ) && $wr_hb_settings['fixedList']['miscellaneous']['home'] == 1 ) {
				$wr_class_position = ( $wr_show_hide == 'show' ) ? 'fixed' : 'inherit';
			}

		// Blog page
		} elseif ( is_home() ) {
			if ( isset( $wr_hb_settings['fixedList']['miscellaneous']['blog'] ) && $wr_hb_settings['fixedList']['miscellaneous']['blog'] == 1 ) {
				$wr_class_position = ( $wr_show_hide == 'show' ) ? 'fixed' : 'inherit';
			}
		}

	// Check is page
	} elseif ( is_page() ) {
		$wr_page_id = get_the_ID();

		if ( isset( $wr_hb_settings['fixedList']['pages'][ $wr_page_id ] ) && $wr_hb_settings['fixedList']['pages'][ $wr_page_id ] == 1 ) {
			$wr_class_position = ( $wr_show_hide == 'show' ) ? 'fixed' : 'inherit';
		}

	// Check is 404
	} elseif ( is_404() ) {
		if ( isset( $wr_hb_settings['fixedList']['miscellaneous']['404'] ) && $wr_hb_settings['fixedList']['miscellaneous']['404'] == 1 ) {
			$wr_class_position = ( $wr_show_hide == 'show' ) ? 'fixed' : 'inherit';
		}

	// Check is search
	} elseif ( is_search() ) {
		if ( isset( $wr_hb_settings['fixedList']['miscellaneous']['search'] ) && $wr_hb_settings['fixedList']['miscellaneous']['search'] == 1 ) {
			$wr_class_position = ( $wr_show_hide == 'show' ) ? 'fixed' : 'inherit';
		}

	// Check is single
	} elseif ( is_singular() ) {
		$wr_post_type_current = get_post_type();

		if ( isset( $wr_hb_settings['fixedList']['single'][ $wr_post_type_current ] ) && $wr_hb_settings['fixedList']['single'][ $wr_post_type_current ] == 1 ) {
			$wr_class_position = ( $wr_show_hide == 'show' ) ? 'fixed' : 'inherit';
		}

	// Check is for category post
	} elseif( is_category() ) {
		if ( isset( $wr_hb_settings['fixedList']['taxonomies']['category'] ) && $wr_hb_settings['fixedList']['taxonomies']['category'] == 1 ) {
			$wr_class_position = ( $wr_show_hide == 'show' ) ? 'fixed' : 'inherit';
		}

	// Check is for tag post
	} elseif( is_tag() ) {
		if ( isset( $wr_hb_settings['fixedList']['taxonomies']['post_tag'] ) && $wr_hb_settings['fixedList']['taxonomies']['post_tag'] == 1 ) {
			$wr_class_position = ( $wr_show_hide == 'show' ) ? 'fixed' : 'inherit';
		}

	// Check is taxonomy
	} elseif( is_tax() ) {
		$wr_tax_current = get_queried_object();

		if ( isset( $wr_hb_settings['fixedList']['taxonomies'][ $wr_tax_current->taxonomy ] ) && $wr_hb_settings['fixedList']['taxonomies'][ $wr_tax_current->taxonomy ] == 1 ) {
			$wr_class_position = ( $wr_show_hide == 'show' ) ? 'fixed' : 'inherit';
		}

	// Check is archives
	} elseif( is_archive() ) {
		$wr_post_type_current = get_post_type();

		if ( isset( $wr_hb_settings['fixedList']['custom_post_type_archives'][ $wr_post_type_current ] ) && $wr_hb_settings['fixedList']['custom_post_type_archives'][ $wr_post_type_current ] == 1 ) {
			$wr_class_position = ( $wr_show_hide == 'show' ) ? 'fixed' : 'inherit';
		}
	}
} else {
	$wr_class_position = 'inherit';
};

$wr_classes_setting   = array();
$wr_classes_setting[] = 'header clear';
$wr_classes_setting[] = esc_attr( $wr_hb_settings['type'] ) . '-layout';

if( $wr_hb_settings['className'] )
	$wr_classes_setting[] = esc_attr( $wr_hb_settings['className'] );

if( $wr_hb_settings['type'] == 'vertical' )
	$wr_classes_setting[] = esc_attr( $wr_hb_settings['positionVertical'] ) . '-position-vertical';

// Check activate plugins
$wr_is_revslider_activated   = call_user_func( 'is_' . 'plugin' . '_active', 'revslider/revslider.php' );
$wr_is_woocommerce_activated = call_user_func( 'is_' . 'plugin' . '_active', 'woocommerce/woocommerce.php' );
$wr_is_wrls_activated        = call_user_func( 'is_' . 'plugin' . '_active', 'wr-live-search/main.php' );
$wr_is_wrcc_activated        = ( $wr_is_woocommerce_activated && call_user_func( 'is_' . 'plugin' . '_active', 'wr-currency/main.php' ) ) ? true : false;
$wr_is_wpml_activated        = call_user_func( 'is_' . 'plugin' . '_active', 'sitepress-multilingual-cms/sitepress.php' );
$wr_is_wishlist_activated = (
$wr_is_woocommerce_activated &&
$wr_nitro_options['wc_general_wishlist'] == 1 && (
	call_user_func( 'is_' . 'plugin' . '_active', 'yith-woocommerce-wishlist/init.php' ) ||
	call_user_func( 'is_' . 'plugin' . '_active', 'yith-woocommerce-wishlist-premium/init.php' )
) ) ? true : false;

$wr_nitro_header_html[] = '<div class="header-outer clear ' . $wr_class_position . '" data-id="' . WR_Nitro_Header_Builder::prop( 'id' ) . '" ' . WR_Nitro_Helper::schema_metadata( array( 'context' => 'header', 'echo' => false ) ) . '>';
$wr_nitro_header_html[] = '<header class="' . ( is_customize_preview() ? 'customizable customize-section-header ' : '' ) . implode( ' ' , $wr_classes_setting ) . '"' . ( $wr_hb_settings['ID'] ? ' id="' . esc_attr( $wr_hb_settings['ID'] ) . '"' : '' ) . '>';

	if ( isset( $wr_nitro_header_data['rows'] ) && @count( $wr_nitro_header_data['rows'] ) ) {
		foreach ( $wr_nitro_header_data['rows'] as $key => $val ) {
			$wr_has_el_wpml = false;
			$wr_use_color_default = ( isset( $val['themeColor'] ) && $val['themeColor'] == 1 ) ? true : false;
			$wr_hb_row = WR_Nitro_Header_Builder::array_fillter_recursive( $wr_data_allow['rows'], $val, $wr_nitro_options, 'rows', $wr_use_color_default );

			$wr_classes_row   = array();
			$wr_classes_row[] = 'clear hb-section section-' . ( $key + 1 );
			if( $wr_hb_row['className'] ) {
				$wr_classes_row[] = esc_attr( $wr_hb_row['className'] );
			}

			$wr_hb_col = WR_Nitro_Header_Builder::array_fillter_recursive( $wr_data_allow['cols'], $val['cols'][0] );

			$wr_nitro_header_html[] = '
			<div class="hb-section-outer clear">' . ( ( $wr_hb_row['sticky'] == 1 ) ? '<div data-height="' . ( intval( $wr_hb_row['heightSticky'] ) > 0 ? intval( $wr_hb_row['heightSticky'] ) : 0 ) . '" class="sticky-row ' . ( $wr_hb_row['sticky_effect'] == 'normal' ? 'sticky-normal' : NULL ) . '">' : NULL ) . '
				<div class="' . implode( ' ', $wr_classes_row ) . '" ' . ( $wr_hb_row['ID'] ? ( 'id="' . esc_attr( $wr_hb_row['ID'] ) . '"' ) : NULL ) . '>';
				$wr_nitro_header_html[] = '
					<div class="container clear">';

					if( $wr_hb_col['items'] ) {
						$wr_center_element_once = false;
						$wr_has_layout_mobile_text = false;

						foreach ( $wr_hb_col['items'] as $key_item => $val_item ) {

							$wr_hb_item = WR_Nitro_Header_Builder::array_fillter_recursive( $wr_data_allow['items'][ $val_item['_rel'] ], $val_item, $wr_nitro_options, $val_item['_rel'], $wr_use_color_default  );

							// Element search
							switch ( $wr_hb_item['_rel'] ) {

								case 'search':

									$wr_classes_item   = array();
									$wr_classes_item[] = 'element-item hb-search hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 );
									$wr_classes_item[] = esc_attr( $wr_hb_item['layout'] );
									$wr_classes_item[] = esc_attr( $wr_hb_item['searchStyle'] );

									if( $wr_hb_item['centerElement'] && $wr_hb_settings['type'] == 'horizontal' && ! $wr_center_element_once ) {
										$wr_classes_item[] = 'center-element';
										$wr_center_element_once = true;
									}

									// Class align for vertical layout
									if( $wr_hb_settings['type'] == 'vertical' ) {
										$wr_classes_item[] = 'vertical-align-' . esc_attr( $wr_hb_item['alignVertical'] );
									}

									$wr_class_wrls_cate = ( $wr_hb_item['liveSearch']['active'] == 1 && $wr_is_wrls_activated && $wr_hb_item['liveSearch']['active'] == 1 && $wr_hb_item['liveSearch']['show_category'] == 1 ) ? ' has-category' : NULL;

									if( $wr_class_wrls_cate ) {
										$wr_classes_item[] = 'has-category-outer';
									}

									if( $wr_hb_item['className'] ) {
										$wr_classes_item[] = esc_attr( $wr_hb_item['className'] );
									}

									if( $wr_hb_item['layout'] == 'dropdown' ) {
										$wr_classes_item[] = 'animation-' . esc_attr( $wr_hb_item['animation'] );
									}

									$wr_class_outer = ( $wr_hb_item['layout'] == 'full-screen' || $wr_hb_item['layout'] == 'topbar' ) ? 'hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) : NULL;

									if( $wr_hb_item['layout'] == 'boxed' &&  $wr_hb_item['buttonType'] == 'text' ) {
										$button = esc_html( $wr_hb_item['textButton'] );
										$wr_classes_item[] = 'button-text';
									} else {
										$button = '<i class="fa fa-search"></i>';
									}

									$wr_nitro_header_html[] = '
										<div class="' . implode( ' ',  $wr_classes_item ) . '" ' . ( $wr_hb_item['layout'] == 'dropdown' ? ( 'data-margin-top="' . ( ( $wr_hb_item['marginTop'] !== '' || $wr_hb_settings['type'] == 'vertical' ) ? absint( $wr_hb_item['marginTop'] ) : 'empty' ) . '"' ) : NULL ) . ( $wr_hb_item['ID'] ? ( 'id="' . esc_attr( $wr_hb_item['ID'] ) . '"' ) : NULL ) . '>';
										$wr_nitro_header_html[] = '
											<div class="search-inner">
												<div class=" search-form ' . ( ( $wr_hb_item['layout'] == 'full-screen' || $wr_hb_item['layout'] == 'topbar' ) ? 'hb-search-fs' : '' ) . '">
													<div class="search-form-inner ' . $wr_class_wrls_cate . '" >';

													if( $wr_hb_item['liveSearch']['active'] == 1 && $wr_is_wrls_activated ) {

														$wr_attr_wrls = array(
															'placeholder'     => esc_attr( $wr_hb_item['placeholder'] ),
															'show_category'   => ( $wr_hb_item['liveSearch']['show_category'] == 1 ? 1 : 0 ),
															'min_characters'  => absint( $wr_hb_item['liveSearch']['min_characters'] ),
															'max_results'     => absint( $wr_hb_item['liveSearch']['max_results'] ),
															'thumb_size'      => absint( $wr_hb_item['liveSearch']['thumb_size'] ),
															'search_in'       => array(
																'title'       => ( $wr_hb_item['liveSearch']['searchIn']['title'] == 1 ? 1 : 0 ),
																'description' => ( $wr_hb_item['liveSearch']['searchIn']['description'] == 1 ? 1 : 0 ),
																'content'     => ( $wr_hb_item['liveSearch']['searchIn']['content'] == 1 ? 1 : 0 ),
																'sku'         => ( $wr_hb_item['liveSearch']['searchIn']['sku'] == 1 ? 1 : 0 ),
															),
															'show_suggestion' => ( $wr_hb_item['liveSearch']['show_suggestion'] == 1 ? 1 : 0 ),
															'class' 		  => 'wrls-header ' . $wr_class_outer
														);

														$wr_attr_wrls = WR_Live_Search_Settings::get( $wr_attr_wrls );

														$wr_nitro_header_html[] = '
														<div class="wrls-header-outer">' . WR_Live_Search_Shortcode::generate( $wr_attr_wrls ) . '</div>';

													} else {
										$wr_nitro_header_html[] = '
														<form class="' . $wr_class_outer . '" action="' . esc_url( home_url( '/' ) ) . '" method="get" role="search" ' . WR_Nitro_Helper::schema_metadata( array( "context" => "search_form", "echo" => false  ) ) . '>
															<input required type="text" placeholder="' . esc_attr( $wr_hb_item['placeholder'] ) . '" name="s" class="txt-search extenal-bdcl">
															<input type="submit" class="btn-search" />
														</form>';
													}

										$wr_nitro_header_html[] = '
														<div class="close"></div>
													</div>
												</div>
												<span class="open ' . ( ( $wr_hb_item['layout'] == 'full-screen' || $wr_hb_item['layout'] == 'topbar' ) ? 'show-full-screen' : '' ) . '" data-layout="' . esc_attr( $wr_hb_item['layout'] ) . '" data-background-style="' . esc_attr( $wr_hb_item['searchStyle'] ) . '">' . $button . '</span>
												</div>
										</div>';

									// Css for item current
									$wr_item_css = '';

									$wr_hb_item['style'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_item['style'] );

									foreach( $wr_hb_item['style'] as $key_sub_item => $val_sub_item ) {
										if( $val_sub_item === '' ) continue;

										$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
										if ( $key_sub_item == 'backgroundImage' ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
										} elseif ( is_numeric( $val_sub_item ) ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
										} else {
											$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
										}
									}

									if( $wr_item_css ) {
										$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '{' . $wr_item_css . '}';
									}

									// Set style for text button
									if( $wr_hb_item['layout'] == 'boxed' &&  $wr_hb_item['buttonType'] == 'text' ) {
										$wr_item_css = 'color: ' . esc_attr( $wr_hb_item['textColorButton'] ) . ';background-color:' . esc_attr( $wr_hb_item['bgColorButton'] );

										$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .open:hover{color: ' . esc_attr( $wr_hb_item['hoverTextColorButton'] ) . ';background-color:' . esc_attr( $wr_hb_item['hoverBgColorButton'] ) . '}';

									// Set style for icon button
									} else {
										$wr_item_css = $wr_hb_item['iconColor'] ? ( 'color:' . esc_attr( $wr_hb_item['iconColor'] ) . ';' ) : NULL;
										$wr_item_css .= 'font-size:' . absint( $wr_hb_item['iconFontSize'] ) . 'px;';

										// Color hover
										$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .open:hover{color:' . esc_attr( $wr_hb_item['hoverIconColor'] ) . '}';
									}

									$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .open{' . $wr_item_css . '}';

									// Margin top for dropdown layout
									if( $wr_hb_item['layout'] == 'dropdown' && (int) $wr_hb_item['marginTop'] > 0 ){
										$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .search-form{ top: calc(100% + ' . absint( $wr_hb_item['marginTop'] ) . 'px); top: -webkit-calc(100% + ' . absint( $wr_hb_item['marginTop'] ) . 'px)}';
									}

									// Set width input text search
									if( absint( $wr_hb_item['widthInput'] ) != 0 ) {
										$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' ' . ( (  $wr_hb_item['liveSearch']['active'] == 1 && $wr_is_wrls_activated  ) ? '.txt-livesearch' : '.txt-search' ) . '{ width: ' . absint( $wr_hb_item['widthInput'] ) . 'px !important }';
									}

									break;

								case 'menu':

									// For desktop
									if ( ! $wr_is_mobile ) {

										$wr_classes_item   = array();
										$wr_classes_item[] = 'element-item hb-menu hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 );
										$wr_classes_item[] = esc_attr( $wr_hb_item['layoutStyle'] ) . '-layout';

										if( $wr_hb_item['centerElement'] && $wr_hb_settings['type'] == 'horizontal' && ! $wr_center_element_once ) {
											$wr_classes_item[] = 'center-element';
											$wr_center_element_once = true;
										}

										// Class align self and text align for vertical layout
										if( $wr_hb_settings['type'] == 'vertical' )
											$wr_classes_item[] = 'vertical-align-' . esc_attr( $wr_hb_item['alignVertical'] );

										if( $wr_hb_item['className'] )
											$wr_classes_item[] = esc_attr( $wr_hb_item['className'] );

										$wr_nitro_header_html[] = '
											<div class="' . implode( ' ' , $wr_classes_item ) . '" ' . ( ( $wr_hb_settings['type'] == 'horizontal' && $wr_hb_item['layoutStyle'] == 'text' ) ? ( ' data-animation="' . esc_attr( $wr_hb_item['subMenu']['animation'] ) . '"' ) : NULL ) . ( ( $wr_hb_settings['type'] == 'horizontal' && $wr_hb_item['layoutStyle'] == 'text' ) ? ( ' data-margin-top="' . ( $wr_hb_item['subMenu']['maginTop'] !== '' ? absint( $wr_hb_item['subMenu']['maginTop'] ) : 'empty' ) . '"' ) : NULL ) . ( $wr_hb_item['ID'] ? ( ' id="' . esc_attr( $wr_hb_item['ID'] ) . '"' ) : NULL ) . '>';

											if( (int) $wr_hb_item['menuID'] > 0 && is_nav_menu( (int) $wr_hb_item['menuID'] ) ) {

												// Initialized variable global check has submenu of menu current
												WR_Nitro_Megamenu::$active = false;
											 	$wr_show_close        = false;
											 	$wr_class             = '';

												if( $wr_hb_item['layoutStyle'] == 'icon' ) {
													$wr_nitro_header_html[] = '
													<div class="menu-icon-action"';
														$wr_nitro_header_html[] = ' data-layout="' . esc_attr( $wr_hb_item['menuStyle'] ) . '" ';
														if( $wr_hb_item['menuStyle'] == 'fullscreen' ) {
															$wr_nitro_header_html[] = ' data-effect="' . esc_attr( $wr_hb_item['effect'] ) . '" ';
															$wr_class = ' fullscreen-style ' . esc_attr( $wr_hb_item['effect'] ) . '-effect ';

															$wr_show_close = true;
														} elseif( $wr_hb_item['menuStyle'] == 'sidebar' ) {
															$wr_nitro_header_html[] = ' data-animation="' . esc_attr( $wr_hb_item['animation'] ) . '" ';
															$wr_nitro_header_html[] = ' data-position="' . esc_attr( $wr_hb_item['position'] ) . '" ';

															$wr_class = esc_attr( $wr_hb_item['verticalAlign'] ) . '-sidebar-vertical sidebar-style ' . esc_attr( $wr_hb_item['position'] ) . '-position ';
														}
													$wr_nitro_header_html[] = '>
														<div class="wr-burger-scale">
															<span class="wr-burger-top"></span>
															<span class="wr-burger-middle"></span>
															<span class="wr-burger-bottom"></span>
														</div>
													</div>';
												}

												$wr_args = array(
													'menu'        => (int) $wr_hb_item['menuID'],
													'menu_class'  => 'site-navigator',
													'container'   => false,
													'items_wrap'  => '<ul class="%2$s">%3$s</ul>',
													'fallback_cb' => '',
													'allow_hb'    => true,
													'echo'        => false,
													'megamenu'    => ( ( ( $wr_hb_settings['type'] == 'horizontal' && $wr_hb_item['layoutStyle'] == 'text' ) || ( ( ( $wr_hb_item['layoutStyle'] == 'icon' ) || ( $wr_hb_settings['type'] == 'vertical' ) ) && ( $wr_hb_item['subMenu']['animationVertical'] == 'normal' ) ) ) ? true : false ),
												);

												$wr_site_navigator = wp_nav_menu( apply_filters( 'header_builder_nav_menu_args', $wr_args ) );

												$wr_classes_item_outer   = array();
												$wr_classes_item_outer[] = 'site-navigator-outer hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 );
												$wr_classes_item_outer[] = esc_attr( $wr_hb_item['hoverStyle'] ) . '-hover';
												$wr_classes_item_outer[] = $wr_class;

												if( ! ( $wr_hb_settings['type'] == 'horizontal' && $wr_hb_item['layoutStyle'] == 'text' ) )
													$wr_classes_item_outer[] = 'text-align-' . esc_attr( $wr_hb_item['textAlign'] );

												if( ! WR_Nitro_Megamenu::$active )
													$wr_classes_item_outer[] = 'not-submenu';

												if( ! ( $wr_hb_settings['type'] == 'horizontal' && $wr_hb_item['layoutStyle'] == 'text' ) )
													$wr_classes_item_outer[] = 'animation-vertical-' . esc_attr( $wr_hb_item['subMenu']['animationVertical'] );

												$wr_nitro_header_html[] = '
												<div ' . WR_Nitro_Helper::schema_metadata( array( 'context' => 'nav', 'echo' => false ) ) . ' class="' . implode( ' ' , $wr_classes_item_outer ) . '"' . ( ( ! ( $wr_hb_settings['type'] == 'horizontal' && $wr_hb_item['layoutStyle'] == 'text' ) ) ? ' data-effect-vertical="' . esc_attr( $wr_hb_item['subMenu']['effectNormalVertical'] ) . '"' : NULL ) . '>';

												$wr_nitro_header_html[] = '
													<div class="navigator-column">
														<div class="navigator-column-inner">';

													// Add breadcrumbs for vertical layout
													if( !( $wr_hb_settings['type'] == 'horizontal' && $wr_hb_item['layoutStyle'] == 'text' ) ) {
														$wr_nitro_header_html[] = ( $wr_hb_item['subMenu']['animationVertical'] == 'slide' ) ? '
																<div class="menu-breadcrumbs-outer">
																	<div class="menu-breadcrumbs">
																		<div class="element-breadcrumbs all-breadcrumbs"><span class="title-breadcrumbs" data-level="all">' . esc_html__( 'All', 'wr-nitro' ) . '</span></div>
																	</div>
																</div>' : NULL;
													}

												$wr_nitro_header_html[] = '
															<div class="site-navigator-inner ' . ( WR_Nitro_Megamenu::$active ? 'has-submenu' : 'not-submenu' ) . '">' . $wr_site_navigator . '</div>
														</div>
													</div>
												';

												$wr_nitro_header_html[] = ( $wr_show_close ? '<div data-effect="' . esc_attr( $wr_hb_item['effect'] ) . '" ' . ' class="close"></div>' : NULL );

												$wr_nitro_header_html[] = '
												</div>';
											} else {
												$wr_nitro_header_html[] = esc_html__( 'Please choose menu to show.', 'wr-nitro' );
											}

										$wr_nitro_header_html[] = '
											</div>';

										if( $wr_hb_item['layoutStyle'] == 'icon' ) {
											// Set width for sidebar icon
											if( $wr_hb_item['menuStyle'] == 'sidebar' && (int) $wr_hb_item['widthSidebar'] > 0 && $wr_hb_item['unitWidthSidebar'] )
												$wr_nitro_header_css[] = '.hb-menu-outer .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' {width:' . (int) $wr_hb_item['widthSidebar'] . esc_attr( $wr_hb_item['unitWidthSidebar'] ) . '}';

											// Css for icon menu
											if( $wr_hb_item['iconColor'] )
												$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .wr-burger-scale span{background: ' . esc_attr( $wr_hb_item['iconColor'] ) . '}';
										}

										// Css padding left and right for vertical layout
										$wr_nitro_header_css_padding_vertical = '';

										// Css padding left and right if submenu not children
										$wr_nitro_header_css_padding_not_icon = '';

										/* Css for menu wrapper */
										$wr_item_css = '';

										$wr_hb_item['spacing'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_item['spacing'] );

										foreach( $wr_hb_item['spacing'] as $key_sub_item => $val_sub_item ) {
											if( $val_sub_item === '' ) continue;

											$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
											if ( $key_sub_item == 'backgroundImage' ) {
												$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
											} elseif ( is_numeric( $val_sub_item ) ) {

												// Css padding left and right for vertical layout
												if( $wr_hb_settings['type'] == 'vertical' && $wr_hb_item['layoutStyle'] == 'text' && ( $key_sub_item == 'paddingRight' || $key_sub_item == 'paddingLeft' ) ) {
													$wr_nitro_header_css_padding_not_icon .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';

													if( ( $wr_hb_item['textAlign'] == 'left' || $wr_hb_item['textAlign'] == 'center' ) && $key_sub_item == 'paddingRight'  ) {
														$wr_nitro_header_css_padding_vertical .= esc_attr( $wr_attr_css ) . ':' . ( (int) $val_sub_item + ( WR_Nitro_Megamenu::$active ? 42 : 0 ) ) . 'px;';
													} elseif( $wr_hb_item['textAlign'] == 'right' && $key_sub_item == 'paddingLeft' ) {
														$wr_nitro_header_css_padding_vertical .= esc_attr( $wr_attr_css ) . ':' . ( (int) $val_sub_item + ( WR_Nitro_Megamenu::$active ? 42 : 0 ) ) . 'px;';
													} else {
														$wr_nitro_header_css_padding_vertical .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
													}

													/*** Set right or left for icon arrow ***/
													if( $key_sub_item == 'paddingLeft' && $wr_hb_item['textAlign'] == 'right' ) {
														$wr_nitro_header_css[] = '.header.vertical-layout .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .icon-has-children{left:' . (int) $val_sub_item . 'px; right: initial}';
													} elseif( $key_sub_item == 'paddingRight' && $wr_hb_item['textAlign'] != 'right' ) {
														$wr_nitro_header_css[] = '.header.vertical-layout .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .icon-has-children{right:' . (int) $val_sub_item . 'px' . '}';
													}
												} else {
													$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
												}
											} else {
												$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
											}
										}

										if( $wr_item_css ) {
											$wr_nitro_header_css[] = '.header .element-item.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '{' . $wr_item_css . '}';
										}

										if( $wr_nitro_header_css_padding_vertical ) {
											$wr_nitro_header_css[] = '.header.vertical-layout .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator > .menu-item > .menu-item-link ' .
											( ( $wr_hb_item['subMenu']['animationVertical'] == 'slide' || $wr_hb_item['subMenu']['animationVertical'] == 'accordion' ) ? ',.header.vertical-layout .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator .menu-default ul a ' : NULL ) . '{' . $wr_nitro_header_css_padding_vertical . '}';

											// Set padding for menu breadcrumbs
											if( $wr_hb_item['subMenu']['animationVertical'] == 'slide' ) {
												$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .menu-breadcrumbs{' .
															'padding-left: ' . ( $wr_hb_item['textAlign'] == 'right' ? ( absint( $wr_hb_item['spacing']['paddingLeft'] ) + 15 ) : absint( $wr_hb_item['spacing']['paddingLeft'] ) ) . 'px;
															padding-right: ' . absint( $wr_hb_item['spacing']['paddingRight'] ) . 'px;'
														. '}';
											}
										}

										// Set padding left and right if submenu not children
										if( $wr_nitro_header_css_padding_not_icon ) {
											$wr_nitro_header_css[] = '.header.vertical-layout .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .'.text-layout .site-navigator .not-padding-icon > li > .menu-item-link{' . $wr_nitro_header_css_padding_not_icon . '}';
										}

										// Css for spacing
										if( $wr_hb_item['background'] && $wr_hb_item['layoutStyle'] == 'icon' ) {
											$wr_item_css = '';

											$wr_hb_item['background'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_item['background'] );

											foreach( $wr_hb_item['background'] as $key_sub_item => $val_sub_item ) {
												if( $val_sub_item === '' ) continue;

												$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
												if ( $key_sub_item == 'backgroundImage' ) {
													$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
												} elseif ( is_numeric( $val_sub_item ) ) {
													$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
												} else {
													$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
												}
											}

											if( $wr_item_css ) {
												$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer{' . $wr_item_css . '}';
											}
										}

										// Css for menu content
										if( $wr_hb_item['textSettings'] ) {
											$wr_item_css = '';
											foreach( $wr_hb_item['textSettings'] as $key_sub_item => $val_sub_item ) {
												if( $val_sub_item === '' ) continue;

												$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );

												if ( $key_sub_item == 'backgroundImage' ) {
													$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
												} elseif( $key_sub_item == 'fontWeight' ) {
													$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
												} elseif ( is_numeric( $val_sub_item ) ) {
													$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
												} elseif ( $key_sub_item == 'fontFamily' ) {
													$wr_font_name		= esc_attr( $val_sub_item );
													$wr_font_weight 	= array();

													// Add font weight for text settting
													$wr_font_weight[] 	= ( $wr_hb_item['textSettings']['fontWeight'] ) ? esc_attr( $wr_hb_item['textSettings']['fontWeight'] ) : '400';

													// Add font weight for submenu
													$wr_font_weight[]	= ( $wr_hb_item['subMenu']['fontWeight'] ) ? esc_attr( $wr_hb_item['subMenu']['fontWeight'] ) : '400';

													// Delete values duplicated
													$wr_font_weight = array_unique( $wr_font_weight );

													// Merge array and delete values duplicated
													$wr_nitro_header_fonts[ $wr_font_name ] = isset( $wr_nitro_header_fonts[ $wr_font_name ] ) ? array_unique( array_merge( $wr_nitro_header_fonts[ $wr_font_name ], $wr_font_weight ) ) : $wr_font_weight;

													$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . $wr_font_name . ';';

												} else {
													$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
												}
											}
											if( $wr_item_css ) {
												$wr_nitro_header_css[] = '
												.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ',
												.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' a{' . $wr_item_css . '}';
											}
										}

										// Css for link
										if( $wr_hb_item['link']['style']['color'] ) {
												$wr_nitro_header_css[] = '
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer a,
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .menu-more .icon-more,
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .menu-breadcrumbs{color: ' . esc_attr( $wr_hb_item['link']['style']['color'] ) . '}';

											$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .menu-more .icon-more .wr-burger-menu:before{background: ' . esc_attr( $wr_hb_item['link']['style']['color'] ) . '}';
											$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .menu-more .icon-more .wr-burger-menu:after{border-top-color: ' . esc_attr( $wr_hb_item['link']['style']['color'] ) . '; border-bottom-color: ' . esc_attr( $wr_hb_item['link']['style']['color'] ) . '}';
										}

										/*** Add font style bold, underline, italic for submenu ***/
										$wr_hb_item['subMenu']['style']['fontWeight'] = ( isset( $wr_hb_item['subMenu']['fontWeight'] ) && $wr_hb_item['subMenu']['fontWeight'] ) ? esc_attr( $wr_hb_item['subMenu']['fontWeight'] ) : '400';

										// Css for sub menu content
										if( $wr_hb_item['subMenu']['style'] ) {
											$wr_item_css = '';
											foreach( $wr_hb_item['subMenu']['style'] as $key_sub_item => $val_sub_item ) {
												if( $val_sub_item === '' ) continue;
												$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
												if ( $key_sub_item == 'backgroundImage' ) {
													$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
												} elseif( $key_sub_item == 'fontWeight' ) {
													$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
												} elseif ( is_numeric( $val_sub_item ) ) {
													$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
												} else {
													$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
												}
											}
											if( $wr_item_css ) {
												$wr_nitro_header_css[] = '
												.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .menu-default ul a,
												.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .mm-container,
												.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .mm-container a{' . $wr_item_css . '}';
											}
										}

										if( ( $wr_hb_item['layoutStyle'] == 'text' && ( $wr_hb_settings['type'] == 'horizontal' || ( $wr_hb_settings['type'] == 'vertical' && $wr_hb_item['subMenu']['animationVertical'] == 'normal' ) ) ) || ( $wr_hb_item['layoutStyle'] == 'icon' && $wr_hb_item['menuStyle'] == 'sidebar' && $wr_hb_item['subMenu']['animationVertical'] == 'normal' ) ) {
											// Background
											if( $wr_hb_item['subMenu']['background'] ) {
												$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer li.menu-default ul,
														.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .mm-container-outer{background-color: ' . esc_attr( $wr_hb_item['subMenu']['background'] ) . '}';
											}

											// Width submenu
											if( (int) $wr_hb_item['subMenu']['width'] > 0 ) {
												$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer li.menu-default ul{width:' . absint( $wr_hb_item['subMenu']['width'] ) . 'px}';
											}
										}

										// Menu item spacing
										if( $wr_hb_settings['type'] == 'horizontal' && $wr_hb_item['layoutStyle'] == 'text' ) {
											$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator > .menu-item > .menu-item-link,
											.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator > li.menu-item-language > a { padding-left: ' . absint( $wr_hb_item['itemSpacing'] ) / 2 . 'px; padding-right: ' . absint( $wr_hb_item['itemSpacing'] ) / 2 . 'px}';

											if( $wr_hb_item['hoverStyle'] == 'underline' ) {
												$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.underline-hover .site-navigator > li:hover > .menu-item-link:after,
												.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.underline-hover .site-navigator > .current-menu-ancestor > .menu-item-link:after,
												.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.underline-hover .site-navigator > .current-menu-item > .menu-item-link:after { width: calc(100% - ' . absint( $wr_hb_item['itemSpacing'] ) . 'px); width: -webkit-calc(100% - ' . absint( $wr_hb_item['itemSpacing'] ) . 'px) } ';
											}
										} elseif( ( $wr_hb_item['layoutStyle'] == 'icon' && $wr_hb_item['subMenu']['animationVertical'] == 'normal' ) || ( $wr_hb_settings['type'] == 'vertical' && $wr_hb_item['layoutStyle'] == 'text' && $wr_hb_item['subMenu']['animationVertical'] == 'normal' ) ) {
											$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .menu-item > .menu-item-link { padding-top: ' . absint( $wr_hb_item['itemSpacing'] ) / 2 . 'px; padding-bottom: ' . absint( $wr_hb_item['itemSpacing'] ) / 2 . 'px} ';
										} else {
											$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator .menu-item > .menu-item-link { padding-top: ' . absint( $wr_hb_item['itemSpacing'] ) / 2 . 'px; padding-bottom: ' . absint( $wr_hb_item['itemSpacing'] ) / 2 . 'px} ';
										}

										// Css for sub menu link
										if( $wr_hb_item['subMenu']['link']['style']['color'] ) {
											$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer li.menu-default ul a,
												.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .menu-more .nav-more .site-navigator > .menu-item > .menu-item-link,
												.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .mm-container-outer a{ color: ' . esc_attr( $wr_hb_item['subMenu']['link']['style']['color'] ) . '}';
										}

										// Set font size for megamenu column title
										if( (int) $wr_hb_item['subMenu']['style']['fontSize'] > 0 ) {
											$wr_font_size_column_title = absint( $wr_hb_item['subMenu']['style']['fontSize'] );
										} else if( (int) $wr_hb_item['textSettings']['fontSize'] > 0 ) {
											$wr_font_size_column_title = absint( $wr_hb_item['textSettings']['fontSize'] );
										}

										if( isset( $wr_font_size_column_title ) ) {
											$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .mm-container .title-column { font-size: ' . ( $wr_font_size_column_title + 3 ) . 'px}';
										}

										/* Css for hover style */
										// Style all text
										if( $wr_hb_item['link']['style']['colorHover'] ) {
											$wr_nitro_header_css[] = '
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer li.menu-item:hover > .menu-item-link,
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .current-menu-ancestor > .menu-item-link,
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .current-menu-item > .menu-item-link,
											' . ( $wr_hb_row['sticky'] == 1 ? ( '.header .sticky-row-scroll .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator > li.menu-item:hover > .menu-item-link,' ) : NULL ) . '
											' . ( $wr_hb_row['sticky'] == 1 ? ( '.header .sticky-row-scroll .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator > .current-menu-ancestor > .menu-item-link,' ) : NULL ) . '
											' . ( $wr_hb_row['sticky'] == 1 ? ( '.header .sticky-row-scroll .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator > .current-menu-item > .menu-item-link,' ) : NULL ) . '
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .menu-more .nav-more .site-navigator li.menu-item:hover > .menu-item-link,
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .menu-item-link:hover,
											.header.vertical-layout .text-layout .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer.animation-vertical-accordion .active-accordion > .menu-item-link,
											.hb-menu-outer .animation-vertical-accordion.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer.animation-vertical-accordion .active-accordion > .menu-item-link,
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .mm-container-outer .menu-item-link:hover,
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .menu-breadcrumbs .element-breadcrumbs:not(:last-child) span:hover { color:' . esc_attr( $wr_hb_item['link']['style']['colorHover'] ) . '}';
										}

										// Style all text of submenu
										if( $wr_hb_item['subMenu']['link']['style']['colorHover'] ) {
											$wr_nitro_header_css[] = '
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer li.menu-default li.menu-item:hover > .menu-item-link,
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer li.menu-default ul .menu-item-link:hover,
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .menu-more .nav-more .site-navigator li.menu-item:hover > .menu-item-link,
											.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .mm-container-outer .menu-item-link:hover,
											.header.vertical-layout .text-layout .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer.animation-vertical-accordion li.menu-default ul .active-accordion > .menu-item-link,
											.hb-menu-outer .animation-vertical-accordion.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer.animation-vertical-accordion ul ul .active-accordion > .menu-item-link { color:' . esc_attr( $wr_hb_item['subMenu']['link']['style']['colorHover'] ) . '}';
										}

										switch ( $wr_hb_item['hoverStyle'] ) {

											case 'default':

											break;

											case 'underline':
												if( $wr_hb_item['link']['style']['underlineColorHover'] )
													$wr_nitro_header_css[] = '
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li > .menu-item-link:hover:after,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li:hover > .menu-item-link:after,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .current-menu-ancestor > .menu-item-link:after,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .current-menu-item > .menu-item-link:after,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li.active-accordion > .menu-item-link:after{border-bottom-color:' . esc_attr( $wr_hb_item['link']['style']['underlineColorHover'] ) . '}
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li > .menu-item-link:after,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li.active-accordion > .menu-item-link:after{
														border-bottom-width: ' . ( (int) $wr_hb_item['link']['underlineWidth'] > 0 ? (int) $wr_hb_item['link']['underlineWidth'] : 0 ) . 'px;
														border-bottom-style: ' . ( $wr_hb_item['link']['underlineStyle'] ? esc_attr( $wr_hb_item['link']['underlineStyle'] ) : 'solid' ) . ' ;
													} ';
											break;

											case 'background':
												if( $wr_hb_item['link']['style']['backgroundColorHover'] )
													$wr_nitro_header_css[] = '
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li > .menu-item-link:hover,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .current-menu-ancestor > .menu-item-link,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .current-menu-item > .menu-item-link,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li:hover > .menu-item-link,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li.active-accordion > .menu-item-link{background:' . esc_attr( $wr_hb_item['link']['style']['backgroundColorHover'] ) . '}';

												if( (int) $wr_hb_item['link']['borderRadius'] > 0 )
													$wr_nitro_header_css[] = '
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li > .menu-item-link,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .current-menu-ancestor > .menu-item-link,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .current-menu-item > .menu-item-link{ border-radius: ' . (int) $wr_hb_item['link']['borderRadius'] . 'px; -moz-border-radius: ' . (int) $wr_hb_item['link']['borderRadius'] . 'px; -webkit-border-radius: ' . (int) $wr_hb_item['link']['borderRadius'] . 'px}';

											break;

											case 'ouline':
												if( $wr_hb_item['link']['style']['outlineColorHover'] )
													$wr_nitro_header_css[] = '
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li > .menu-item-link:hover,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .current-menu-ancestor > .menu-item-link,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .current-menu-item > .menu-item-link,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li.active-accordion > .menu-item-link{border-color:' . esc_attr( $wr_hb_item['link']['style']['outlineColorHover'] ) . '}
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li > .menu-item-link,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li.active-accordion > .menu-item-link {
														border-width: ' . ( (int) $wr_hb_item['link']['underlineWidth'] > 0 ? (int) $wr_hb_item['link']['underlineWidth'] : 0 ) . 'px;
														border-style: ' . ( $wr_hb_item['link']['underlineStyle'] ? esc_attr( $wr_hb_item['link']['underlineStyle'] ) : 'solid' ) . ' ;
													} ';

												if( (int) $wr_hb_item['link']['borderRadius'] > 0 )
													$wr_nitro_header_css[] = '
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '.site-navigator-outer .site-navigator > li > .menu-item-link,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .current-menu-ancestor > .menu-item-link,
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .site-navigator > .current-menu-item > .menu-item-link { border-radius: ' . (int) $wr_hb_item['link']['borderRadius'] . 'px; -moz-border-radius: ' . (int) $wr_hb_item['link']['borderRadius'] . 'px; -webkit-border-radius: ' . (int) $wr_hb_item['link']['borderRadius'] . 'px}';
											break;
										}

									// For mobile
									} else {
										$wr_classes_item   = array();
										$wr_classes_item[] = 'element-item hb-menu hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 );

										if( $wr_hb_item['className'] ) {
											$wr_classes_item[] = esc_attr( $wr_hb_item['className'] );
										}

										if( $wr_hb_item['layoutStyleMobile'] ) {
											$wr_classes_item[] = 'mobile-' . esc_attr( $wr_hb_item['layoutStyleMobile'] );
										}

										if( $wr_hb_item['centerElement'] && $wr_hb_settings['type'] == 'horizontal' && ! $wr_center_element_once ) {
											$wr_classes_item[] = 'center-element';
											$wr_center_element_once = true;
										}

										$wr_nitro_header_html[] = '
											<div class="' . implode( ' ' , $wr_classes_item ) . '" ' . ( $wr_hb_item['ID'] ? ( 'id="' . esc_attr( $wr_hb_item['ID'] ) . '"' ) : NULL ) . '>';

											if( (int) $wr_hb_item['menuID'] > 0 && is_nav_menu( (int) $wr_hb_item['menuID'] ) ) {
												if( $wr_hb_item['layoutStyleMobile'] == 'icon' ) {
													$wr_nitro_header_html[] = '
													<div class="menu-icon-action">
														<div class="wr-burger-scale">
															<span class="wr-burger-top"></span>
															<span class="wr-burger-middle"></span>
															<span class="wr-burger-bottom"></span>
														</div>
													</div>';
												}

												$wr_args = array(
													'menu'        => (int) $wr_hb_item['menuID'],
													'menu_class'  => 'site-navigator',
													'container'   => false,
													'items_wrap'  => '<ul class="%2$s">%3$s</ul>',
													'fallback_cb' => '',
													'allow_hb'    => true,
													'echo'        => false,
													'megamenu'    => false,
													'is_mobile'   => true
												);

												// Just get only menu level 1
												if( $wr_hb_item['layoutStyleMobile'] == 'text' ) {
													$wr_has_layout_mobile_text = true;
													$wr_args['depth'] = 1;
												}

												$wr_nitro_header_html[] = '
												<div class="site-navigator-inner">' . wp_nav_menu( apply_filters( 'header_builder_nav_menu_args', $wr_args ) ) . '</div>';
											} else {
												$wr_nitro_header_html[] = esc_html__( 'Please choose menu to show.', 'wr-nitro' );
											}
										$wr_nitro_header_html[] = '
											</div>';

										// Icon color
										if( $wr_hb_item['iconColorMobile'] && $wr_hb_item['layoutStyleMobile'] == 'icon' )
											$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .wr-burger-scale span{background: ' . esc_attr( $wr_hb_item['iconColorMobile'] ) . '}';

										// Background color
										if( $wr_hb_item['backgroundColorMobile'] && $wr_hb_item['layoutStyleMobile'] == 'icon' )
											$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator-inner{background: ' . esc_attr( $wr_hb_item['backgroundColorMobile'] ) . '}';

										// Css for menu content
										if( $wr_hb_item['textSettings'] ) {
											$wr_item_css = '';
											foreach( $wr_hb_item['textSettings'] as $key_sub_item => $val_sub_item ) {
												if( $val_sub_item === '' ) continue;

												$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );

												if ( $key_sub_item == 'backgroundImage' ) {
													$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
												} elseif( $key_sub_item == 'fontWeight' ) {
													$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
												} elseif ( is_numeric( $val_sub_item ) ) {
													$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
												} elseif ( $key_sub_item == 'fontFamily' ) {

													$wr_font_name		= esc_attr( $val_sub_item );
													$wr_font_weight 	= array();

													// Add font weight for text settting
													$wr_font_weight[] 	= $wr_hb_item['textSettings']['fontWeight'] ? esc_attr( $wr_hb_item['textSettings']['fontWeight'] ) : '400';

													// Add font weight for submenu
													$wr_font_weight[]	= $wr_hb_item['subMenu']['fontWeight'] ? esc_attr( $wr_hb_item['subMenu']['fontWeight'] ) : '400';

													// Delete values duplicated
													$wr_font_weight = array_unique( $wr_font_weight );

													// Merge array and delete values duplicated
													$wr_nitro_header_fonts[ $wr_font_name ] = isset( $wr_nitro_header_fonts[ $wr_font_name ] ) ? array_unique( array_merge( $wr_nitro_header_fonts[ $wr_font_name ], $wr_font_weight ) ) : $wr_font_weight;

													$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . $wr_font_name . ';';

												} else {
													$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
												}
											}

											if( $wr_item_css ) {
												$wr_nitro_header_css[] = '
												.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ',
												.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' a{' . $wr_item_css . '}';
											}
										}

										// Text color
										if( $wr_hb_item['link']['style']['color'] )
											$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator a,
													.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator .has-children-mobile{ color: ' . esc_attr( $wr_hb_item['link']['style']['color'] ) . '}';

										// Color hover
										if( $wr_hb_item['link']['style']['colorHover'] )
											$wr_nitro_header_css[] = '
												.hb-s1i2 .site-navigator > .current-menu-ancestor > .item-link-outer .menu-item-link,
												.hb-s1i2 .site-navigator > .current-menu-item > .item-link-outer .menu-item-link,
												.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator .item-link-outer.active-submenu a,
												.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator .item-link-outer.active-submenu i{ color:' . esc_attr( $wr_hb_item['link']['style']['colorHover'] ) . '}';

										// Item spacing
										if( $wr_hb_item['layoutStyleMobile'] == 'icon' ) {
											$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator a { padding-top: ' . absint( $wr_hb_item['itemSpacing'] ) / 2 . 'px; padding-bottom: ' . absint( $wr_hb_item['itemSpacing'] ) / 2 . 'px} ';
										} else {
											$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator a { padding-left: ' . absint( $wr_hb_item['itemSpacing'] ) / 2 . 'px; padding-right: ' . absint( $wr_hb_item['itemSpacing'] ) / 2 . 'px} ';
										}

										/*** Add font style bold, underline, italic for submenu ***/
										$wr_hb_item['subMenu']['style']['fontWeight'] = ( $wr_hb_item['subMenu']['fontWeight'] ) ? esc_attr( $wr_hb_item['subMenu']['fontWeight'] ) : '400';

										// Css for submenu content
										if( $wr_hb_item['subMenu']['style'] ) {
											$wr_item_css = '';
											foreach( $wr_hb_item['subMenu']['style'] as $key_sub_item => $val_sub_item ) {
												if( $val_sub_item === '' ) continue;

												$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
												if ( $key_sub_item == 'backgroundImage' ) {
													$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
												} elseif( $key_sub_item == 'fontWeight' ) {
													$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
												} elseif ( is_numeric( $val_sub_item ) ) {
													$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
												} else {
													$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
												}
											}

											if( $wr_item_css )
												$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator ul,
												.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator ul a{' . $wr_item_css . '}';
										}

										// Text color submenu
										if( $wr_hb_item['subMenu']['link']['style']['color'] )
											$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator ul a{ color: ' . esc_attr( $wr_hb_item['subMenu']['link']['style']['color'] ) . '}';

										// Submenu color hover
										if( $wr_hb_item['subMenu']['link']['style']['colorHover'] )
											$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator ul .item-link-outer.active-submenu a,
													.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .site-navigator ul .item-link-outer.active-submenu i{color:' . esc_attr( $wr_hb_item['subMenu']['link']['style']['colorHover'] ) . '}';

										/* Css for menu wrapper */
										$wr_hb_item['spacing'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_item['spacing'] );

										$wr_item_css = '';
										foreach( $wr_hb_item['spacing'] as $key_sub_item => $val_sub_item ) {
											if( $val_sub_item === '' ) continue;

											$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
											if ( $key_sub_item == 'backgroundImage' ) {
												$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
											} elseif ( is_numeric( $val_sub_item ) ) {
												$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
											} else {
												$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
											}
										}

										if( $wr_item_css )
											$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .'{' . $wr_item_css . '}';
									}

									break;

								case 'sidebar':

									$wr_classes_item   = array();
									$wr_classes_item[] = 'element-item hb-sidebar hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 );

									if( $wr_hb_item['centerElement'] && $wr_hb_settings['type'] == 'horizontal' && ! $wr_center_element_once ) {
										$wr_classes_item[] = 'center-element';
										$wr_center_element_once = true;
									}

									if( $wr_hb_settings['type'] == 'vertical' )
										$wr_classes_item[] = 'vertical-align-' . esc_attr( $wr_hb_item['alignVertical'] );

									if( $wr_hb_item['className'] )
										$wr_classes_item[] = esc_attr( $wr_hb_item['className'] );

									$wr_nitro_header_html[] = '
										<div class="' . implode( ' ' , $wr_classes_item ) . '" ' . ( $wr_hb_item['ID'] ? ( 'id="' . esc_attr( $wr_hb_item['ID'] ) . '"' ) : NULL ) . '>';

											if( $wr_hb_item['sidebarID'] ) {

												$wr_sidebar_name = ( isset( $wp_registered_sidebars[ $wr_hb_item['sidebarID'] ] ) ? $wp_registered_sidebars[$wr_hb_item['sidebarID']]['name'] : NULL );

												$wr_nitro_header_html[] = '
											<div class="icon-sidebar">
												<i class="' . esc_attr( $wr_hb_item['icon'] ) . '"></i>
											</div>';

												ob_start();
												dynamic_sidebar( $wr_hb_item['sidebarID'] );
												$wr_sidebar = ob_get_clean();

												$wr_nitro_header_html[] = '
											<div class="content-sidebar">
												<div class="animation-sidebar hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '-inner ' . esc_attr( $wr_hb_item['position'] ) . '-position ' . ( ( $wr_sidebar === '' ) ? 'sidebar-empty' : NULL ) . '">
													<div class="sidebar-inner">
														' .  ( ( $wr_sidebar ) ? do_shortcode( $wr_sidebar ) : '<p>' . sprintf( __( 'Please add widget to %s sidebar <a%s> here </a>', 'wr-nitro' ), $wr_sidebar_name, ' target="_blanh" href="' . admin_url() . 'widgets.php" ' ) . '</p>' ) . '
													</div>
												</div>
												<div class="wr-close-mobile"><span></span></div>
												<div class="overlay"></div>
											</div>';

											} else {
												$wr_nitro_header_html[] = esc_html__( 'Please choose the sidebar to show.', 'wr-nitro' );
											}

									$wr_nitro_header_html[] = '
										</div>';

									$wr_icon_sidebar = array();

									// Set color for icon
									if( $wr_hb_item['iconColor'] ) {
										$wr_icon_sidebar[] = 'color:' . esc_attr( $wr_hb_item['iconColor'] );
									}

									// Set font size for icon
									if( $wr_hb_item['iconSize'] ) {
										$wr_icon_sidebar[] = 'font-size:' . absint( $wr_hb_item['iconSize'] ) . 'px';

									}

									if( $wr_icon_sidebar ) {
										$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .icon-sidebar{' . implode( ';' , $wr_icon_sidebar) . '}';
									}

									// Set hover color for icon
									if( $wr_hb_item['hoverIconColor'] ) {
										$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .icon-sidebar:hover{color:' . esc_attr( $wr_hb_item['hoverIconColor'] ) . '}';
									}

									// Css for sidebar animation
									if( $wr_hb_item['frontCSS']['style'] ) {
										$wr_item_css = '';

										foreach( $wr_hb_item['frontCSS']['style'] as $key_sub_item => $val_sub_item ) {

											if( $val_sub_item === '' ) continue;

											$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
											if ( $key_sub_item == 'backgroundImage' ) {
												$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
											} elseif ( $key_sub_item == 'height' ) {
												if( $wr_hb_item['position'] == 'top' || $wr_hb_item['position'] == 'bottom' ) {
													$wr_item_css .= 'height:' . ( $wr_hb_item['unit'] == '%' ? ( absint( $val_sub_item ) . '%;' ) : ( absint( $val_sub_item ) . 'px;' ) );
												}
											} elseif ( $key_sub_item == 'width' ) {
												if( $wr_hb_item['position'] == 'left' || $wr_hb_item['position'] == 'right' ) {
													$wr_item_css .= 'width:' . ( $wr_hb_item['unit'] == '%' ? ( absint( $val_sub_item ) . '%;' ) : ( absint( $val_sub_item ) . 'px;' ) );
												}
											} elseif ( is_numeric( $val_sub_item ) ) {
												$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
											} else {
												$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
											}
										}

										if( $wr_item_css ) {
											$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '-inner.animation-sidebar{' . $wr_item_css . '}';

											if( $wr_hb_settings['type'] == 'horizontal' && $wr_hb_item['unit'] == 'px' ) {
												$wr_nitro_header_css[] = '
												@media (max-width: ' . ( absint( $wr_hb_item['frontCSS']['style']['width'] ) + 40 ) . 'px) {
													.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '-inner.animation-sidebar{ width: calc(100% - 40px) }';
													if( $wr_is_mobile ) {
														$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .wr-close-mobile{ left: auto !important; right: 5px; }';
													}
												$wr_nitro_header_css[] = ' } ';
											}
										}

									}

									// Style for icon
									if( $wr_hb_item['frontCSS']['spacing'] ) {
										$wr_item_css = '';

										$wr_hb_item['frontCSS']['spacing'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_item['frontCSS']['spacing'] );

										foreach( $wr_hb_item['frontCSS']['spacing'] as $key_sub_item => $val_sub_item ) {

											if( $val_sub_item === '' ) continue;

											$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
											if ( $key_sub_item == 'backgroundImage' ) {
												$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
											} elseif ( $key_sub_item == 'height' ) {
												$wr_item_css .= 'height:' . ( $wr_hb_item['unit'] == '%' ? ( absint( $val_sub_item ) . '%;' ) : ( absint( $val_sub_item ) . 'px;' ) );
											} elseif ( $key_sub_item == 'width' ) {
												$wr_item_css .= 'width:' . ( $wr_hb_item['unit'] == '%' ? ( absint( $val_sub_item ) . '%;' ) : ( absint( $val_sub_item ) . 'px;' ) );
											} elseif ( is_numeric( $val_sub_item ) ) {
												$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
											} else {
												$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
											}
										}

										if( $wr_item_css ) {
											$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .'{' . $wr_item_css . '}';
										}
									}

									break;

								case 'text':

									$wr_classes_item   = array();
									$wr_classes_item[] = 'element-item hb-text hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 );

									if( $wr_hb_item['centerElement'] && $wr_hb_settings['type'] == 'horizontal' && ! $wr_center_element_once ) {
										$wr_classes_item[] = 'center-element';
										$wr_center_element_once = true;
									}

									if( $wr_hb_settings['type'] == 'vertical' )
										$wr_classes_item[] = 'vertical-align-' . esc_attr( $wr_hb_item['alignVertical'] );

									if( $wr_hb_item['className'] )
										$wr_classes_item[] = esc_attr( $wr_hb_item['className'] );

									$wr_nitro_header_html[] = '
										<div class="' . implode( ' ' , $wr_classes_item ) . '"' . ( $wr_hb_item['ID'] ? ( ' id="' . esc_attr( $wr_hb_item['ID'] ) . '"' ) : NULL ) . '>
											<div class="content-text">' . do_shortcode( $wr_hb_item['content'] ) . '</div>
										</div>';

									$wr_item_css = '';

									$wr_hb_item['style'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_item['style'] );

									// Css for font icon
									foreach( $wr_hb_item['style'] as $key_sub_item => $val_sub_item ) {
										if( $val_sub_item === '' ) continue;

										$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
										if ( $key_sub_item == 'backgroundImage' ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
										} elseif ( is_numeric( $val_sub_item ) ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
										} else {
											$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
										}
									}

									// Check active and has shortcode revslider
									if( $wr_is_revslider_activated && ( has_shortcode( $wr_hb_item['content'], 'rev_slider_vc' ) || has_shortcode( $wr_hb_item['content'], 'rev_slider' ) ) ) {
										$wr_item_css .= 'width: 100%;';
									}

									if( $wr_item_css )
										$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .'{' . $wr_item_css . '}';

									break;

								case 'logo':

									$wr_classes_item   = array();
									$wr_classes_item[] = 'element-item hb-logo hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 );
									$wr_classes_item[] = esc_attr( $wr_hb_item['logoType'] ) . '-type';

									if( $wr_hb_item['centerElement'] && $wr_hb_settings['type'] == 'horizontal' && ! $wr_center_element_once ) {
										$wr_classes_item[] = 'center-element';
										$wr_center_element_once = true;
									}

									if( $wr_hb_item['className'] ) {
										$wr_classes_item[] = esc_attr( $wr_hb_item['className'] );
									}

									if( $wr_hb_settings['type'] == 'vertical' ) {
										$wr_classes_item[] = 'vertical-align-' . esc_attr( $wr_hb_item['alignVertical'] );
									}

									$wr_hb_item['style'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_item['style'] );

									$wr_nitro_header_html[] = '<div class="' . implode( ' ' , $wr_classes_item ) . '"' . ( $wr_hb_item['ID'] ? ( ' id="' . esc_attr( $wr_hb_item['ID'] ) . '"' ) : NULL ) . '>';
										$wr_nitro_header_html[] = '<div class="content-logo"><a href="' . esc_url( home_url( '/' ) ) . '">';

											// Logo text
											if( $wr_hb_item['logoType'] == 'text' ) {
												$wr_nitro_header_html[] = esc_attr( $wr_hb_item['content'] );

											// Logo image
											} else if( $wr_hb_item['logoType'] == 'image' && $wr_hb_item['logoImage'] ) {

												$wr_logo_retina = '';

												if( $wr_hb_item['logoImageRetina'] ) {
													$wr_logo_retina = esc_url( $wr_hb_item['logoImageRetina'] );
												}

												$wr_alt = ' alt="' . esc_attr( get_option( 'blogname' ) ) . '" ';

												// Logo sticky
												if( $wr_hb_row['sticky'] == 1 && $wr_hb_item['logoImageSticky'] ) {
													$wr_logo_retina_sticky = ( $wr_hb_item['logoImageStickyRetina'] ) ? esc_url( $wr_hb_item['logoImageStickyRetina'] ) : NULL;
													$wr_logo_retina_sticky = '';

													if( $wr_hb_item['logoImageStickyRetina'] ) {
														$wr_logo_retina_sticky = esc_url( $wr_hb_item['logoImageStickyRetina'] );
													}

													$wr_nitro_header_html[] = '
														<img width="' . absint( $wr_hb_item['style']['maxWidth'] ) . '" height="10" class="logo-origin ' . ( $wr_logo_retina ? 'logo-retina-hide' : NULL ) . '" src="' . esc_url( $wr_hb_item['logoImage'] ) . '" ' . $wr_alt . ' />
														<img width="' . absint( $wr_hb_item['maxWidthSticky'] ) . '" height="10" class="logo-sticky ' . ( $wr_logo_retina_sticky ? 'logo-retina-hide' : NULL ) . '" src="' . esc_url( $wr_hb_item['logoImageSticky'] ) . '" ' . $wr_alt . ' />
													';

													$wr_nitro_header_html[] = $wr_logo_retina ? '<img width="' . absint( $wr_hb_item['style']['maxWidth'] ) . '" height="10" class="logo-origin logo-retina-show" src="' . $wr_logo_retina . '" ' . $wr_alt .  '/>' : NULL;
													$wr_nitro_header_html[] = $wr_logo_retina_sticky ? '<img width="' . absint( $wr_hb_item['maxWidthSticky'] ) . '" height="10" class="logo-sticky logo-retina-show" src="' . $wr_logo_retina_sticky . '" ' . $wr_alt . '/>' : NULL;

												} else {
													$wr_nitro_header_html[] = '<img width="' . absint( $wr_hb_item['style']['maxWidth'] ) . '" height="10" ' . ( $wr_logo_retina ? 'class="logo-retina-hide"' : NULL ) . ' src="' . esc_url( $wr_hb_item['logoImage'] ) . '" ' . $wr_alt . '/>';
													$wr_nitro_header_html[] = $wr_logo_retina ? '<img width="' . absint( $wr_hb_item['style']['maxWidth'] ) . '" height="10" class="logo-retina-show" src="' . $wr_logo_retina . '" ' . $wr_alt . '/>' : NULL;
												}
											}
										$wr_nitro_header_html[] = '</a></div>';
									$wr_nitro_header_html[] = '</div>';

									/* Css for font icon */
									$wr_item_css = '';

									foreach( $wr_hb_item['style'] as $key_sub_item => $val_sub_item ) {
										if( $val_sub_item === '' ) continue;

										$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );

										if ( $key_sub_item == 'backgroundImage' ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
										} elseif( $key_sub_item == 'maxWidth' && (int) $val_sub_item > 0 ) {
											$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' img{max-width: ' . (int) $wr_hb_item['style']['maxWidth'] . 'px}';
										} elseif( $key_sub_item == 'fontWeight' ) {
											$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
										} elseif ( is_numeric( $val_sub_item ) ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
										} elseif ( $key_sub_item == 'fontFamily' ) {
											if( $wr_hb_item['logoType'] == 'text' ) {
												$wr_font_name		= esc_attr( $val_sub_item );
												$wr_font_weight 	= array( ( $wr_hb_item['style']['fontWeight'] ) ? esc_attr( $wr_hb_item['style']['fontWeight'] ) : '400' );

												// Merge array and delete values duplicated
												$wr_nitro_header_fonts[ $wr_font_name ] = isset( $wr_nitro_header_fonts[ $wr_font_name ] ) ? array_unique( array_merge( $wr_nitro_header_fonts[ $wr_font_name ], $wr_font_weight ) ) : $wr_font_weight;

												$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . $wr_font_name . ';';
											}
										} else {
											$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
										}
									}

									if( $wr_hb_row['sticky'] == 1 && $wr_hb_item['maxWidthSticky'] ) {
										$wr_nitro_header_css[] = '.header .sticky-row-scroll .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' img{max-width: ' . absint( $wr_hb_item['maxWidthSticky'] ) . 'px}';
									}

									if( $wr_item_css ) {
										$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . '{' . $wr_item_css . '}';
									}

									break;

								case 'social':

									$wr_classes_item   = array();
									$wr_classes_item[] = 'element-item hb-social hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 );
									$wr_classes_item[] = esc_attr( $wr_hb_item['iconStyle'] ) . '-style';
									$wr_classes_item[] = esc_attr( $wr_hb_item['iconSize'] ) . '-size';

									if( $wr_hb_item['centerElement'] && $wr_hb_settings['type'] == 'horizontal' && ! $wr_center_element_once ) {
										$wr_classes_item[] = 'center-element';
										$wr_center_element_once = true;
									}

									if( $wr_hb_settings['type'] == 'vertical' ) {
										$wr_classes_item[] = 'vertical-align-' . esc_attr( $wr_hb_item['alignVertical'] );
									}

									if( $wr_hb_item['className'] ) {
										$wr_classes_item[] = esc_attr( $wr_hb_item['className'] );
									}

									$wr_nitro_header_html[] = '<div class="' . implode( ' ' , $wr_classes_item ) . '"' . ( $wr_hb_item['ID'] ? ( ' id="' . esc_attr( $wr_hb_item['ID'] ) . '"' ) : NULL ) . '>';

										if( $wr_hb_item['socialList'] ) {
											if( $wr_hb_item['iconColor'] === '' ) {
												$wr_is_background = 'color';
											} elseif( $wr_hb_item['backgroundColor'] === '' && $wr_hb_item['iconStyle'] != 'none' ) {
												$wr_is_background = 'background';
											}

											foreach( $wr_hb_item['socialList'] as $key_sub_item => $val_sub_item ) {
												if( $val_sub_item != 1 ) continue;

												$key_sub_item = str_replace( '_', '-', esc_attr( $key_sub_item ) );

												$wr_nitro_header_html[] = ( isset( $wr_nitro_options[ $key_sub_item ] ) && $wr_nitro_options[ $key_sub_item ] != '' )
													? ( '<a class="'. ( isset( $wr_is_background ) ? ( 'wr-' . $wr_is_background . '-' . $key_sub_item )
													: 'wr-color-hover-' . $key_sub_item ) . '" href="' . esc_attr( $wr_nitro_options[ $key_sub_item ] ) . '" title="' . str_replace( '-', ' ', ucfirst( esc_attr( $key_sub_item ) ) ) . '" target="_blank" rel="noopener noreferrer"><i class="fa fa-' . $key_sub_item . '"></i></a>' ) : NULL;
											}
										}

									$wr_nitro_header_html[] = '</div>';

									/* Css for font icon */
									$wr_item_css = '';

									$wr_hb_item['style'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_item['style'] );

									foreach( $wr_hb_item['style'] as $key_sub_item => $val_sub_item ) {
										if( $val_sub_item === '' ) continue;

										$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
										if ( $key_sub_item == 'backgroundImage' ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
										} elseif ( $key_sub_item == 'lineHeight' ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . ';';
										} elseif ( is_numeric( $val_sub_item ) ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
										} else {
											$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
										}
									}

									if( $wr_item_css )
										$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .'{' . $wr_item_css . '}';

									$wr_item_css = '';

									switch ( $wr_hb_item['iconStyle'] ) {
										case 'none':
											$wr_item_css .= $wr_hb_item['iconColor'] ? 'color:' . esc_attr( $wr_hb_item['iconColor'] ) . ';' : NULL;

											break;

										case 'custom':
											$wr_item_css .= $wr_hb_item['backgroundColor'] ? 'background-color:' . esc_attr( $wr_hb_item['backgroundColor'] ) . ';' : NULL;
											$wr_item_css .= $wr_hb_item['iconColor'] ? 'color:' . esc_attr( $wr_hb_item['iconColor'] ) . ';' : NULL;
											$wr_item_css .= $wr_hb_item['borderColor'] ? 'border-color:' . esc_attr( $wr_hb_item['borderColor'] ) . ';' : NULL;
											$wr_item_css .= $wr_hb_item['borderStyle'] ? 'border-style:' . esc_attr( $wr_hb_item['borderStyle'] ) . ';' : NULL;
											$wr_item_css .= $wr_hb_item['borderWidth'] ? 'border-width:' . esc_attr( $wr_hb_item['borderWidth'] ) . 'px;' : NULL;
											$wr_item_css .= $wr_hb_item['borderRadius'] ? 'border-radius:' . esc_attr( $wr_hb_item['borderRadius'] ) . 'px;' : NULL;

											break;
									}

									// Set spacing icon
									$wr_item_css .= 'margin-right: ' . absint( $wr_hb_item['iconSpacing'] ) . 'px;';

									$wr_nitro_header_css[] = '.header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' a{' . $wr_item_css . '}';

									break;

								case 'shopping-cart':

									if( ! $wr_is_woocommerce_activated )
										break;

									// Set had cart element
									WR_Nitro_Render::$has_cart = true;

									if( $wr_is_mobile ) {
										$wr_hb_item['type'] = 'sidebar';
									}

									$wr_classes_item   = array();
									$wr_classes_item[] = 'element-item hb-cart hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 );
									$wr_classes_item[] = esc_attr( $wr_hb_item['type'] );

									if( $wr_hb_item['centerElement'] && $wr_hb_settings['type'] == 'horizontal' && ! $wr_center_element_once ) {
										$wr_classes_item[] = 'center-element';
										$wr_center_element_once = true;
									}

									if( $wr_hb_settings['type'] == 'vertical' ) {
										$wr_classes_item[] = 'vertical-align-' . esc_attr( $wr_hb_item['alignVertical'] );
									}

									if( $wr_hb_item['className'] ) {
										$wr_classes_item[] = esc_attr( $wr_hb_item['className'] );
									}

									if( $wr_hb_item['type'] == 'dropdown' ) {
										$wr_classes_item[] = esc_attr( $wr_hb_item['animationDropdown'] );
									}

									$wr_shopping_cart = '<div class="' . implode( ' ' , $wr_classes_item ) . '" ' . ( $wr_hb_item['type'] == 'dropdown' ? ( 'data-margin-top="' . ( ( $wr_hb_item['marginTop'] !== '' || $wr_hb_settings['type'] == 'vertical' ) ? absint( $wr_hb_item['marginTop'] ) : 'empty' ) . '"' ) : NULL ) . ( $wr_hb_item['ID'] ? ( ' id="' . esc_attr( $wr_hb_item['ID'] ) . '"' ) : '' ) . '>';

										if( $wr_hb_item['titleText'] !== '' ) {
											$wr_shopping_cart .= '<div class="title-cart">' . esc_attr( $wr_hb_item['titleText'] ) . '</div>';
											$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .title-cart {color:' . esc_attr( $wr_hb_item['colorTitle'] ) . '}';
										}

										$wr_shopping_cart .= '<span ' . ( $wr_hb_item['type'] == 'sidebar' ? ( ' ' . 'data-animation="' . esc_attr( $wr_hb_item['animationSidebar'] ) . '" data-position="' . esc_attr( $wr_hb_item['position'] ) . '"' ) : NULL ) . ' class="cart-control ' . ( $wr_hb_item['type'] == 'sidebar' ? 'cart-control-sidebar' : NULL ) . '">';

											$wr_shopping_cart .= '<i class="' . esc_attr( $wr_hb_item['iconName'] ) . '"></i>';

											if ( $wr_hb_item['showCartInfo'] == 'item_number' || $wr_hb_item['showCartInfo'] == 'number_price' ) {
												$wr_shopping_cart .= '<span class="count">' . WC()->cart->get_cart_contents_count() . '</span>';
											}

										$wr_shopping_cart .= '</span>';

										if ( $wr_hb_item['showCartInfo'] == 'total_price' || $wr_hb_item['showCartInfo'] == 'number_price' ) {
											$wr_shopping_cart .= '<span class="mini-price ' . ( $wr_hb_item['showCartInfo'] == 'number_price' ? 'number-price' : '' ) . '">' . WC()->cart->get_cart_subtotal() . '</span>';
										}


										$wr_classes_content = array();
										$wr_classes_content[] = 'hb-minicart';
										$wr_classes_content[] = esc_attr( $wr_hb_item['colorType'] ) . '-style';

										if( $wr_hb_item['type'] == 'sidebar' ) {
											$wr_classes_content[] = 'sidebar';

											if ( $wr_hb_item['position'] == 'position-sidebar-left' || $wr_hb_item['position'] == 'position-sidebar-right' ) {
												$wr_classes_content[] = 'minicart-vertical';
											} else {
												$wr_classes_content[] = 'minicart-horizontal';
											}
										}

										$wr_shopping_cart .= '
										<div class="hb-minicart-outer">
											<div class="' . implode( ' ' , $wr_classes_content ) . '">
												<div class="widget_shopping_cart_content"></div>
											</div>
										</div>';

										if( $wr_hb_item['type'] == 'dropdown' ) {
											$wr_shopping_cart .= '<a class="link-cart" href="' . esc_url( wc_get_cart_url() ) . '"></a>';
										}

									$wr_shopping_cart .= '</div>';

									$wr_nitro_header_html[] = $wr_shopping_cart;

									// Set color for price
									$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .mini-price .amount{color:' . esc_attr( $wr_hb_item['colorPrice'] ) . '}';

									/* Css for item current */
									$wr_item_css = '';

									$wr_hb_item['style'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_item['style'] );

									foreach( $wr_hb_item['style'] as $key_sub_item => $val_sub_item ) {
										if( $val_sub_item === '' ) continue;
										$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
										if ( $key_sub_item == 'backgroundImage' ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
										} elseif ( is_numeric( $val_sub_item ) ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
										} else {
											$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
										}
									}
									if( $wr_item_css ) {
										$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .'{' . $wr_item_css . '}';
									}

									// Css for font icon
									$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ' .cart-control i{ color: ' . esc_attr( $wr_hb_item['styleIcon']['color'] ) . '; font-size: ' . absint( $wr_hb_item['styleIcon']['fontSize'] ) . 'px }';

									// Set hover icon
									$wr_nitro_header_css[] = '.hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) . ':hover .cart-control > i{ color: ' . esc_attr( $wr_hb_item['styleIcon']['hoverColor'] ) . ' }';

									break;

								case 'wpml':

									if( ! $wr_is_wpml_activated )
										break;

									$wr_has_el_wpml = true;

									$wr_classes_item   = array();
									$wr_classes_item[] = 'element-item hb-wpml hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 );

									if( $wr_hb_item['centerElement'] && $wr_hb_settings['type'] == 'horizontal' && ! $wr_center_element_once ) {
										$wr_classes_item[] = 'center-element';
										$wr_center_element_once = true;
									}

									if( $wr_hb_settings['type'] == 'vertical' )
										$wr_classes_item[] = 'vertical-align-' . esc_attr( $wr_hb_item['alignVertical'] );

									if( $wr_hb_item['className'] )
										$wr_classes_item[] = esc_attr( $wr_hb_item['className'] );

									ob_start();
								 	do_action('icl_language_selector');
									$wr_wpml_html = ob_get_clean();

									$wr_nitro_header_html[] = '
										<div class="' . implode( ' ' , $wr_classes_item ) . '"' . ( $wr_hb_item['ID'] ? ( ' id="' . esc_attr( $wr_hb_item['ID'] ) . '"' ) : NULL ) . '>
											' . $wr_wpml_html . '
										</div>';

									/* Css for font icon */
									$wr_item_css = '';

									$wr_hb_item['style'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_item['style'] );
									foreach( $wr_hb_item['style'] as $key_sub_item => $val_sub_item ) {

										if( $val_sub_item === '' ) continue;

										$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
										if ( $key_sub_item == 'backgroundImage' ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
										} elseif ( is_numeric( $val_sub_item ) ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
										} else {
											$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
										}
									}

									if( $wr_item_css ) {
										$wr_nitro_header_css[] = ' .header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .'{' . $wr_item_css . '}';
									}

									break;

								case 'wishlist':

									if( ! $wr_is_wishlist_activated ) {
										break;
									}

									$wr_classes_item   = array();
									$wr_classes_item[] = 'element-item hb-wishlist hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 );

									if( $wr_hb_item['centerElement'] && $wr_hb_settings['type'] == 'horizontal' && ! $wr_center_element_once ) {
										$wr_classes_item[] = 'center-element';
										$wr_center_element_once = true;
									}

									$wr_label          = esc_attr( $wr_hb_item['textLabel'] );
									$wr_label_position = esc_attr( $wr_hb_item['labelPosition'] );
									$wr_wishlist_url   = YITH_WCWL()->get_wishlist_url();

									$wr_classes_item[] = $wr_label_position;

									if( $wr_hb_settings['type'] == 'vertical' )
										$wr_classes_item[] = 'vertical-align-' . esc_attr( $wr_hb_item['alignVertical'] );

									if( $wr_hb_item['className'] )
										$wr_classes_item[] = esc_attr( $wr_hb_item['className'] );


									$wr_nitro_header_html[] = '
										<div class="' . implode( ' ' , $wr_classes_item ) . '"' . ( $wr_hb_item['ID'] ? ( ' id="' . esc_attr( $wr_hb_item['ID'] ) . '"' ) : NULL ) . '>
											' . ( $wr_label_position == 'right' ? '<i class="icon nitro-icon-' . esc_attr( $wr_nitro_options['wc_icon_set'] ) . '-wishlist"></i>' : NULL ) .
											( $wr_label ? '<span class="text">' . $wr_label . '</span>' : NULL ) . '
											<a class="link" href="' . esc_url( $wr_wishlist_url ) . '"></a>
											' . ( $wr_label_position == 'left' ? '<i class="nitro-icon-' . esc_attr( $wr_nitro_options['wc_icon_set'] ) . '-wishlist"></i>' : NULL ) .
										'</div>';

									/* Css for font icon */
									$wr_item_css = '';

									$wr_hb_item['style'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_item['style'] );
									foreach( $wr_hb_item['style'] as $key_sub_item => $val_sub_item ) {

										if( $val_sub_item === '' ) continue;

										$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
										if ( $key_sub_item == 'backgroundImage' ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
										} elseif ( is_numeric( $val_sub_item ) ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
										} else {
											$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
										}
									}

									if( $wr_item_css ){
										$wr_nitro_header_css[] = ' .header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .'{' . $wr_item_css . '}';
									}

									/* Set color for label */
									$wr_nitro_header_css[] = ' .header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .text{
										color: ' . esc_attr( $wr_hb_item['colorLabel'] ) . ';
										font-size: ' . absint( $wr_hb_item['labelSize'] ) . 'px
									}';

									/* Set color for icon */
									$wr_nitro_header_css[] = ' .header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .' .icon{
										color: ' . esc_attr( $wr_hb_item['colorIcon'] ) . ';
										font-size: ' . absint( $wr_hb_item['iconSize'] ) . 'px
									}';
									$wr_nitro_header_css[] = ' .header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .':hover .icon {
										color: ' . esc_attr( $wr_hb_item['hoverIconColor'] ) . '
									}';

									break;

								case 'currency':

									if( ! $wr_is_wrcc_activated )
										break;

									$wr_classes_item   = array();
									$wr_classes_item[] = 'element-item hb-currency hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 );

									if( $wr_hb_item['centerElement'] && $wr_hb_settings['type'] == 'horizontal' && ! $wr_center_element_once ) {
										$wr_classes_item[] = 'center-element';
										$wr_center_element_once = true;
									}

									if( $wr_hb_settings['type'] == 'vertical' ) {
										$wr_classes_item[] = 'vertical-align-' . esc_attr( $wr_hb_item['alignVertical'] );
									}

									if( $wr_hb_item['className'] ) {
										$wr_classes_item[] = esc_attr( $wr_hb_item['className'] );
									}

									$data_currency = WR_Currency_Hook::get_currency();
									$current_active = WR_Currency_Hook::prop( 'current' );
									$wc_currency_code = get_woocommerce_currency();
									$currency_code_options = get_woocommerce_currencies();
									$currency_content = '';

									foreach ( $currency_code_options as $code => $name ) {
										$currency_code_options[ $code ] = '<span>' . $name . '</span> (' . WR_Currency_Hook::currency_symbol( $code ) . ')';
									}

									if( $data_currency ) {
										$list_currency = $item_active = '';

										foreach( $data_currency as $currency ) {
											if( isset( $current_active['id'] ) && $current_active['id'] == $currency['id'] ) {
												$item_active = '
													<div class="item active-currency">' .
														( $wr_hb_item['show_flag'] ? '<img width="37" src="' . WR_CC_URL . 'assets/images/flag/' . strtolower( $currency['code'] ) . '.png" />' : NULL ) .
														$currency_code_options[  $currency['code'] ]. '
														<i class="fa fa-angle-down" aria-hidden="true"></i>
													</div>';
											} else {
												$list_currency .= '
												<div data-id="' . $currency['id'] . '" class="item">' .
													( $wr_hb_item['show_flag'] ? '<img width="37" src="' . WR_CC_URL . 'assets/images/flag/' . strtolower( $currency['code'] ) . '.png" />' : NULL ) .
													$currency_code_options[  $currency['code'] ] . '
												</div>';
											}
										}

										if( $item_active ) {
											$currency_content .= $item_active;
											$list_currency = '
											<div data-id="normal" class="item">' .
											 	( $wr_hb_item['show_flag'] ? '<img width="37" src="' . WR_CC_URL . 'assets/images/flag/' . strtolower( $wc_currency_code ) . '.png" />' : NULL ) .
												$currency_code_options[ $wc_currency_code ] . '
											</div>' . $list_currency;
										} else {
											$currency_content .= '
											<div class="item active-currency">' .
											 	( $wr_hb_item['show_flag'] ? '<img width="37" src="' . WR_CC_URL . 'assets/images/flag/' . strtolower( $wc_currency_code ) . '.png" />' : NULL ) .
												$currency_code_options[ $wc_currency_code ] . '
												<i class="fa fa-angle-down" aria-hidden="true"></i>
											</div>';
										}

										if( $list_currency ) {
											$currency_content .= '
											<div class="list">' . $list_currency . '</div>
											<input type="hidden" name="' . WR_CC . '" class="currency-value" />';
										}

									} else {
										$currency_content = __( 'Please add currency to show.', 'wr-nitro' );
									}

									$wr_nitro_header_html[] = '
										<div class="' . implode( ' ' , $wr_classes_item ) . '"' . ( $wr_hb_item['ID'] ? ( ' id="' . esc_attr( $wr_hb_item['ID'] ) . '"' ) : NULL ) . '>
											<form method="POST">
												' . $currency_content . '
											</form>
										</div>';

									/* Css for font icon */
									$wr_item_css = '';

									$wr_hb_item['style'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_item['style'] );
									foreach( $wr_hb_item['style'] as $key_sub_item => $val_sub_item ) {

										if( $val_sub_item === '' ) continue;

										$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_sub_item ) );
										if ( $key_sub_item == 'backgroundImage' ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_sub_item ) . '");';
										} elseif ( is_numeric( $val_sub_item ) ) {
											$wr_item_css .= esc_attr( $wr_attr_css ) . ':' . (int) $val_sub_item . 'px;';
										} else {
											$wr_item_css .= "{$wr_attr_css}: {$val_sub_item};";
										}
									}

									if( $wr_item_css ){
										$wr_nitro_header_css[] = ' .header .hb-s' . ( $key + 1 ) . 'i' . ( $key_item + 1 ) .'{' . $wr_item_css . '}';
									}

									break;

								case 'flex':

									$wr_nitro_header_html[] = '<div class="element-item hb-flex"></div>';

									break;
							}
						}
					}

				$wr_nitro_header_html[] = '
					</div>' . ( $wr_hb_row['sticky'] == 1 ? '</div>' : NULL ) . '
				</div>
			</div>';

			if( $wr_has_layout_mobile_text ) {
				$wr_nitro_header_css[] = ' .header .section-' . ( $key + 1 ) .' .container{
					-ms-flex-wrap: nowrap;
					flex-wrap: nowrap;
					-webkit-flex-wrap: nowrap;
				}';
			}

			/* Css for row */
			$wr_nitro_header_css[] = ' .header .section-' . ( $key + 1 ) .'{';

				$wr_hb_row['style'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_row['style'] );

				foreach( $wr_hb_row['style'] as $key_item => $val_item ) {
					if( $val_item === '' ) continue;

					$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_item ) );
					if ( $key_item == 'backgroundImage' ) {
						$wr_nitro_header_css[] = esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_item ) . '");';
					} elseif ( is_numeric( $val_item ) ) {
						$wr_nitro_header_css[] = esc_attr( $wr_attr_css ) . ':' . (int) $val_item . 'px;';
					} else {
						$wr_nitro_header_css[] = esc_attr( $wr_attr_css ) . ':' . $val_item . ';';
					}
				}
			$wr_nitro_header_css[] = '}';

			// Background default for row sticky when scroll
			if( $wr_hb_row['sticky'] == 1 ) {

				$wr_nitro_header_css_sticky_section = '';

				if ( $wr_hb_row['backgroundColorSticky'] ) {
					$wr_nitro_header_css_sticky_section .= 'background: ' . esc_attr( $wr_hb_row['backgroundColorSticky'] ) . ';';
				}

				if ( intval( $wr_hb_row['heightSticky'] ) > 0 ) {
					$wr_heightSticky = absint( $wr_hb_row['heightSticky'] );
					$wr_nitro_header_css_sticky_section .= 'height: ' . absint( $wr_hb_row['heightSticky'] ) . 'px !important';
				}

				if ( $wr_nitro_header_css_sticky_section ) {
					$wr_nitro_header_css[] = ' .header .sticky-row-scroll .section-' . ( $key + 1 ) . '{' . $wr_nitro_header_css_sticky_section . '}';
				}

				// Set color
				if( $wr_hb_row['textColorSticky'] ) {
					$wr_color_sticky = esc_attr( $wr_hb_row['textColorSticky'] );

					$wr_nitro_header_css[] = '
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-text,
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-text a,
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-text p,
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-text span,
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-logo a,
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .menu-more .icon-more,
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-currency,
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-cart .cart-control i,
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-cart .mini-price .amount,
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-cart .title-cart,
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-search .open { color: ' . $wr_color_sticky . '!important; }
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-social a { color: ' . $wr_color_sticky . '; background: initial; }
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-wishlist .icon,
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .hb-wishlist .text { color: ' . $wr_color_sticky . ';border-color: ' . $wr_color_sticky . ' }

					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .menu-more .icon-more .wr-burger-menu:after { border-top-color: ' . $wr_color_sticky . '; border-bottom-color: ' . $wr_color_sticky . ' }

					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .menu-more .icon-more .wr-burger-menu:before,
					.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .wr-burger-scale span { background: ' . $wr_color_sticky . ' }
					.header .sticky-row-scroll .section-' . ( $key + 1 ) . ' .icon-sidebar{color:' . $wr_color_sticky . '}
					';

					$wr_nitro_header_css[] = '.header .sticky-row-scroll .section-' . ( $key + 1 ) .' .site-navigator > li > .menu-item-link{color: ' . $wr_color_sticky . ';}';
				}

				// Set width follow boxed layout
				if( $wr_nitro_options['wr_layout_boxed'] == 1 ) {
					if ( $wr_nitro_options['wr_layout_content_width_unit'] == 'pixel' ) {
						$wr_content_width_layout = (int) $wr_nitro_options['wr_layout_content_width'] . 'px';
					} else {
						$wr_content_width_layout = (int) $wr_nitro_options['wr_layout_content_width_percentage'] . '%';
					}
					$wr_nitro_header_css[] = '.header .sticky-row-scroll .section-' . ( $key + 1 ) . '{ max-width: ' . $wr_content_width_layout . '; margin-right: auto; margin-left: auto}';
				}
			}

			/* Css for container */
			$wr_nitro_header_css[] = '.header .section-' . ( $key + 1 ) .' .container{';

				$wr_hb_col['style'] = WR_Nitro_Header_Builder::fillter_property( $wr_hb_col['style'] );

				foreach( $wr_hb_col['style'] as $key_item => $val_item ) {
					if( $val_item === '' ) continue;

					$wr_attr_css = strtolower( preg_replace( '#([A-Z])#', '-$1', $key_item ) );
					if ( $key_item == 'backgroundImage' ) {
						$wr_nitro_header_css[] = esc_attr( $wr_attr_css ) . ':url("' . esc_attr( $val_item ) . '");';
					} elseif ( $key_item == 'maxWidth' ) {
						$wr_nitro_header_css[] =  esc_attr( $wr_attr_css ) . ':' . absint( $val_item ) . esc_attr( $wr_hb_col['unit'] ) . ';' ;
					} elseif ( is_numeric( $val_item ) ) {
						$wr_nitro_header_css[] = esc_attr( $wr_attr_css ) . ':' . (int) $val_item . 'px;';
					} else {
						$wr_nitro_header_css[] = esc_attr( $wr_attr_css ) . ':' . $val_item . ';';
					}
				}
			$wr_nitro_header_css[] = '}';

		}
	}

$wr_nitro_header_html[] = '</header>';

$wr_nitro_header_html[] = '</div><!-- .header-outer -->';

// Creat js offset variables
if( (int) $wr_nitro_options['wr_layout_offset'] > 0 ) {
	$wr_layout_offset = (int) $wr_nitro_options['wr_layout_offset'];

	$wr_nitro_header_css[] = '
		@media only screen and (min-width: 1024px) {
			body > .search-form.hb-search-fs.topbar { margin:' . $wr_layout_offset . 'px ' . $wr_layout_offset . 'px 0 ' . $wr_layout_offset . 'px; width: calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); width: -webkit-calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px);}

			.animation-sidebar { margin:' . $wr_layout_offset . 'px }

			.animation-sidebar.top-position,
			.animation-sidebar.bottom-position { width:calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); width:-webkit-calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px) }

			.animation-sidebar.left-position,
			.animation-sidebar.right-position { height: calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); height: -webkit-calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px) }

			body.admin-bar .animation-sidebar.left-position,
			body.admin-bar .animation-sidebar.right-position { height: calc(100% - ' . ( ( $wr_layout_offset * 2 ) + 32 ) . 'px); height: -webkit-calc(100% - ' . ( ( $wr_layout_offset * 2 ) + 32 ) . 'px) }
			.hb-cart-outer .minicart-horizontal { width: calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); width: -webkit-calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px) }

			.hb-cart-outer .minicart-vertical { height: calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); height: -webkit-calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px) }
			body.admin-bar .hb-cart-outer .minicart-vertical { height: calc(100% - ' . ( ( $wr_layout_offset * 2 ) + 32 ) . 'px); height: -webkit-calc(100% - ' . ( ( $wr_layout_offset * 2 ) + 32 ) . 'px) }

			.hb-cart-outer .hb-minicart { margin:' . $wr_layout_offset . 'px }

			.hb-menu-outer .fullscreen-style,
			.hb-overlay-menu,
			.hb-overlay-sidebar,
			.overlay-sidebar { margin: ' . $wr_layout_offset . 'px; width: calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); width: -webkit-calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); height: calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); height: -webkit-calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); }

			body.admin-bar .hb-menu-outer .fullscreen-style,
			body.admin-bar > .search-form.hb-search-fs[data-layout="full-screen"] { margin-top: ' . ( $wr_layout_offset + 32 ) . 'px; height: calc(100% - ' . ( ( $wr_layout_offset * 2 ) + 32 ) . 'px); height: -webkit-calc(100% - ' . ( ( $wr_layout_offset * 2 ) + 32 ) . 'px); }

			body.admin-bar .hb-overlay-menu,
			body.admin-bar .hb-overlay-sidebar,
			body.admin-bar .overlay-sidebar { margin-top: ' . ( $wr_layout_offset + 32 ) . 'px; height:calc(100% - ' . ( ( $wr_layout_offset * 2 ) + 32 ) . 'px); height:-webkit-calc(100% - ' . ( ( $wr_layout_offset * 2 ) + 32 ) . 'px); }

			.header .sticky-row-scroll { margin: ' . $wr_layout_offset . 'px; width: calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); width: -webkit-calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); }

			.hb-menu-outer .sidebar-style.left-position,
			.hb-menu-outer .sidebar-style.right-position { margin:' . $wr_layout_offset . 'px; height: calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); height: -webkit-calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); }

			body.admin-bar .hb-menu-outer .sidebar-style.left-position,
			body.admin-bar .hb-menu-outer .sidebar-style.right-position { height: calc(100% - ' . ( ( $wr_layout_offset * 2 ) + 32 ) . 'px); height: -webkit-calc(100% - ' . ( ( $wr_layout_offset * 2 ) + 32 ) . 'px) }
		}

		@media only screen and (max-width: 1024px) {
			.header .sticky-row-scroll { margin: 0; width: 100%; }
			.overlay-sidebar,
			body.admin-bar .overlay-sidebar { margin: 0; width: 100%; height: 100%; }
		}
	';

	if( $wr_hb_settings['type'] == 'vertical' )
		$wr_nitro_header_css[] = '
			.vertical-layout { top: ' . $wr_layout_offset . 'px; height: calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); height: -webkit-calc(100% - ' . ( $wr_layout_offset * 2 ) . 'px); }
			.vertical-layout.left-position-vertical { left: ' . $wr_layout_offset . 'px; }
			.vertical-layout.right-position-vertical { right: ' . $wr_layout_offset . 'px; }
			body.admin-bar .vertical-layout { top: ' . ( $wr_layout_offset + 32 ) . 'px; height: calc(100% - ' . ( ( $wr_layout_offset * 2 ) + 32 ). 'px); height: -webkit-calc(100% - ' . ( ( $wr_layout_offset * 2 ) + 32 ). 'px); }

			@media only screen and (max-width: 1024px) {
				.hb-menu-outer .fullscreen-style,
				.hb-overlay-menu,
				.hb-overlay-sidebar,
				.overlay-sidebar { margin: 0px; width: 100%; height: 100%; }

				body.admin-bar .hb-menu-outer .fullscreen-style,
				body.admin-bar > .search-form.hb-search-fs[data-layout="full-screen"] { margin-top: 32px; height: calc(100% - 32px); height: -webkit-calc(100% - 32px); }

				body.admin-bar .hb-overlay-menu,
				body.admin-bar .hb-overlay-sidebar,
				body.admin-bar .overlay-sidebar { margin-top: 32px; height:calc(100% - 32px); height:-webkit-calc(100% - 32px); }

				.header .sticky-row-scroll { margin: 0px; width:100%; }

				.hb-menu-outer .sidebar-style.left-position,
				.hb-menu-outer .sidebar-style.right-position { margin:0px; height: 100%; }

				body.admin-bar .hb-menu-outer .sidebar-style.left-position,
				body.admin-bar .hb-menu-outer .sidebar-style.right-position { height: calc(100% - 32px); height: -webkit-calc(100% - 32px) }

				.animation-sidebar { margin:0 }

				.animation-sidebar.top-position,
				.animation-sidebar.bottom-position { width:100% !important; }

				.animation-sidebar.left-position,
				.animation-sidebar.right-position { height: 100% !important; }

				body.admin-bar .animation-sidebar.left-position,
				body.admin-bar .animation-sidebar.right-position { height: calc(100% - 32px) !important; height: -webkit-calc(100% - 32px) !important }

				.hb-cart-outer .hb-minicart { margin:0 }

				.hb-cart-outer .minicart-vertical { height: 100% !important }

				.hb-cart-outer .minicart-horizontal { width: 100% !important }

				body.admin-bar .hb-cart-outer .minicart-vertical { height: calc(100% - 32px) !important; height: -webkit-calc(100% - 32px) !important }

				.vertical-layout { top: 0px; height: 100% }
				.vertical-layout.left-position-vertical { left: 0 }
				.vertical-layout.right-position-vertical { right: 0 }
				body.admin-bar .vertical-layout { top: 32px; height: calc(100% - 32px); height: -webkit-calc(100% - 32px); }
			}
		';
}

// Set margin wrapper for vertical layout
if( $wr_hb_settings['type'] == 'vertical' ) {
	$wr_nitro_header_css[] = ' .wrapper-outer{ margin-' . ( ( $wr_hb_settings['positionVertical'] == 'left' ) ? 'left' : 'right' ) . ': ' . absint( $wr_hb_settings['style']['width'] ) . ( ( $wr_hb_settings['unit'] == '%' ) ? '%' : 'px' ) . '}';
}

function asset_header(){
	// Check if has cart elment then enqueue library relate for cart
	if( WR_Nitro_Render::$has_cart ) {
		wp_enqueue_script( 'wc-cart-fragments' );
		wp_enqueue_script( 'wc-add-to-cart' );
	}

}
add_action( 'wp_enqueue_scripts', 'asset_header', 9999999998 );

WR_Nitro_Helper::add_google_font( $wr_nitro_header_fonts );

WR_Nitro_Header_Builder::prop( 'css', apply_filters( 'wr_header_custom_css', implode( '' , $wr_nitro_header_css ) ) );

function hb_asset_css( $content ) {
	return $content . WR_Nitro_Header_Builder::prop( 'css' );
}
add_filter( 'wr_custom_styles', 'hb_asset_css' );

// Turn on output buffering HTML
ob_start();
echo preg_replace( '/\n|\t/i', '', implode( '' , $wr_nitro_header_html ) );
WR_Nitro_Header_Builder::prop( 'html', ob_get_contents() );
ob_end_clean();
