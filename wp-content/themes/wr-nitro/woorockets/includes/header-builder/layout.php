<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 * HTML settings for Header builder.
 */

global $pagenow;

$wr_link_admin        = admin_url();
$wr_nitro_header_data = ( array ) $post_info;
$wr_nitro_header_id   = $wr_nitro_header_data['ID'];

// Get theme options.
$wr_nitro_options = WR_Nitro::get_options();

// Get all menus.
$wr_menus = wp_get_nav_menus();

// Get current header builder data.
$wr_hb_status   = (string) get_post_meta( $wr_nitro_header_id, 'hb_status', true );
$wr_list_layout = array( 'horizontal', 'vertical' );
$wr_hb_layout   = 'horizontal';

if( $pagenow == 'post-new.php' && ! empty( $_GET['layout'] ) && in_array( $_GET['layout'] , $wr_list_layout ) ) {
	$wr_hb_layout = esc_attr( $_GET['layout'] );
} elseif ( $wr_nitro_header_data['post_content'] ) {

	if( is_serialized( $wr_nitro_header_data['post_content'] ) ) {
		$wr_post_content = unserialize( $wr_nitro_header_data['post_content'] );
	} else {
		$wr_post_content = json_decode( $wr_nitro_header_data['post_content'], true );
	}

	if ( isset( $wr_post_content['hbLayout'] ) ) {
		$wr_hb_layout = esc_attr( $wr_post_content['hbLayout'] );
	}
}

// Sitepress multilingual cms
$wr_is_wpml_activated = call_user_func( 'is_' . 'plugin' . '_active', 'sitepress-multilingual-cms/sitepress.php' );

// Get layout of trid for WPML plugin
if (
	$wr_is_wpml_activated
	&& $pagenow == 'post-new.php'
	&& ! empty( $_GET['trid'] )
	&& ! empty( $_GET['source_lang'] )
) {
	$wr_hb_layout = get_post_meta( $wr_nitro_header_data['ID'], 'hb_layout', true );
}

/*** Get pages
========================================================== ***/
$wr_pages = get_posts(
	array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby'     => 'title',
		'order'       => 'ASC',
	)
);

$wr_pages_data = array();

if ( $wr_pages ) {
	foreach ( $wr_pages as $val ) {
		$wr_pages_data[ $val->ID ] = $val->post_title;
	}
};

/*** Get singles
========================================================== ***/
$wr_custom_post_types = get_post_types(
	array(
		'public' => true,
	),
	'object'
);

$wr_single_data = array();

if ( $wr_custom_post_types ) {
	foreach ( $wr_custom_post_types as $key => $val ) {
		if ( in_array( $key, array( 'revision', 'attachment', 'nav_menu_item', 'page' ) ) )
			continue;

		$wr_single_data[ $key ] = $val->labels->name;
	}
}

/*** Get custom post type archives
========================================================== ***/
$wr_custom_post_type_archives = array();
$wr_custom_post_type_archives_slug = array();

if ( $wr_custom_post_types ) {
	foreach ( $wr_custom_post_types as $key => $val ) {
		if ( in_array( $key, array( 'revision', 'attachment', 'nav_menu_item' ) ) || ! $val->has_archive )
			continue;

		if ( isset( $val->has_archive ) && $val->has_archive != 1 ) {
			$wr_slug_archives = $val->has_archive;
		} elseif ( isset( $val->rewrite['slug'] ) && $val->rewrite['slug'] ) {
			$wr_slug_archives = $val->rewrite['slug'];
		} else {
			$wr_slug_archives = $val->name;
		}

		$wr_custom_post_type_archives[ $key ] = $val->labels->name . ' (' . esc_attr( $wr_slug_archives ) . ')' ;
		$wr_custom_post_type_archives_slug[] = esc_attr( $wr_slug_archives );
	}
}

/*** Get taxonomies
========================================================== ***/
$wr_taxonomies_data = array();

if ( $wr_custom_post_types ) {
	foreach ( $wr_custom_post_types as $key => $val ) {
		if ( in_array( $key, array( 'revision', 'attachment', 'nav_menu_item' ) ) )
			continue;

		$wr_post_taxes = get_object_taxonomies( $key, 'object' );

		if ( $wr_post_taxes ) {
			foreach ( $wr_post_taxes as $key_item => $val_item ) {
				if ( $key == 'post' ) {
					if ( ! in_array( $key_item, array( 'category', 'post_tag' ) ) )
						continue;
				} elseif ( $key == 'product' ) {
					if ( ! in_array( $key_item, array( 'product_cat', 'product_tag' ) ) )
						continue;
				}

				$wr_taxonomies_data[ $key_item ] = $val_item->labels->name . ' (' . $key_item . ')';
			}
		}
	}
}

$wr_hide_show_fixed = array(
	'miscellaneous' => array(
		'title' => esc_html__( 'Miscellaneous', 'wr-nitro' ),
		'data' => array(
			'home'    => esc_html__( 'Home (Front page)', 'wr-nitro' ),
			'blog'    => esc_html__( 'Blog (Posts page)', 'wr-nitro' ),
			'404'     => esc_html__( '404', 'wr-nitro' ),
			'search'  => esc_html__( 'Search', 'wr-nitro' )
		)
	),
	'custom_post_type_archives' => array(
		'title' => esc_html__( 'Custom Post Type Archives', 'wr-nitro' ),
		'data'  => $wr_custom_post_type_archives
	),
	'taxonomies' => array(
		'title' => esc_html__( 'Taxonomies', 'wr-nitro' ),
		'data'  => $wr_taxonomies_data
	),
	'single' => array(
		'title' => esc_html__( 'Single', 'wr-nitro' ),
		'data'  => $wr_single_data
	),
	'pages' => array(
		'title' => esc_html__( 'Pages', 'wr-nitro' ),
		'data'  => $wr_pages_data
	)
);

/*** Remove page value fixed in setting more ***/
$wr_show_on_front = get_option( 'show_on_front' );

// In Reading Settings
if ( $wr_show_on_front == 'posts' ) {
	unset( $wr_hide_show_fixed[ 'miscellaneous' ][ 'data' ][ 'blog' ] );
} elseif ( $wr_show_on_front == 'page' ) {
	$wr_page_on_front  = get_option( 'page_on_front' );
	$wr_page_for_posts = get_option( 'page_for_posts' );

	unset( $wr_hide_show_fixed[ 'pages' ][ 'data' ][ $wr_page_on_front ] );
	unset( $wr_hide_show_fixed[ 'pages' ][ 'data' ][ $wr_page_for_posts ] );
	unset( $wr_hide_show_fixed[ 'pages' ][ 'data' ][ 'blog' ] );

	if ( ! $wr_page_for_posts || ! $wr_page_on_front ) {
		unset( $wr_hide_show_fixed[ 'miscellaneous' ][ 'data' ][ 'blog' ] );
	}
}

// In custom post type archives
if ( $wr_custom_post_type_archives_slug ) {
	foreach ( $wr_custom_post_type_archives_slug as $val ) {
		$wr_page_slug = get_page_by_path( $val, ARRAY_A );

		if ( $wr_page_slug ) {
			unset( $wr_hide_show_fixed[ 'pages' ][ 'data' ][ $wr_page_slug[ 'ID' ] ] );
		}
	}
}

/* Check active plugins */

// WooCommerce
$wr_is_woocommerce_activated = call_user_func( 'is_' . 'plugin' . '_active', 'woocommerce/woocommerce.php' );

// WR live search
$wr_is_live_search_activated = call_user_func( 'is_' . 'plugin' . '_active', 'wr-live-search/main.php' );

// WR Currency
$wr_is_currency_activated = call_user_func( 'is_' . 'plugin' . '_active', 'wr-currency/main.php' );

// YITH WooCommerce Wishlist
$wr_wishlist_activated = (
	$wr_is_woocommerce_activated
	&& $wr_nitro_options['wc_general_wishlist'] == 1
	&& ( call_user_func( 'is_' . 'plugin' . '_active', 'yith-woocommerce-wishlist/init.php' ) ||
		call_user_func( 'is_' . 'plugin' . '_active', 'yith-woocommerce-wishlist-premium/init.php' )
	) ) ? true : false;

$wr_google_fonts = WR_Nitro_Helper::google_fonts();

// Get font body
$wr_fonts_url = add_query_arg(
	array(
		'family' => urldecode( esc_attr( $wr_nitro_options['body_google_font']['family'] ) . ':' . absint( $wr_nitro_options['body_google_font']['fontWeight'] ) ),
		'subset' => urlencode( 'latin,latin-ext' ),
	),
	'https://fonts.googleapis.com/css'
);
?>
<link rel="stylesheet" type="text/css" href="<?php echo esc_url( $wr_fonts_url ); ?>" />

<style type="text/css">
	.hb-wrapper .hb-content .list-row-outer {
		font-family: <?php echo esc_attr( $wr_nitro_options['body_google_font']['family'] ) ?>;
		font-weight: <?php echo esc_attr( $wr_nitro_options['body_google_font']['fontWeight'] ) ?>;
	}
</style>

<div id="hb-app" data-id="<?php echo intval( $wr_nitro_header_id ); ?>" class="hb-wrapper <?php echo 'hb-' . $wr_hb_layout ?>" style="display:none ">
	<div class="hb-header">
		<div class="settings-position">
			<div class="settings-library">
				<div class="setting-item setting" title="<?php esc_attr_e( 'Settings', 'wr-nitro' ); ?>">
					<div class="name"><i class="fa fa-cog"></i></div>
				</div>
				<div class="setting-item mobile" data-bind="visible:isDesktopView" title="<?php esc_attr_e( 'Mobile layout', 'wr-nitro' ); ?>">
					<div class="name"><i class="fa fa-mobile"></i></div>
				</div>
				<div class="setting-item desktop" data-bind="visible:isMobileView" title="<?php esc_attr_e( 'Desktop layout', 'wr-nitro' ); ?>">
					<div class="name"><i class="fa fa-desktop"></i></div>
				</div>
				<div class="setting-item import" title="<?php esc_attr_e( 'Import Header Template', 'wr-nitro' ); ?>">
					<div class="name"><i class="fa fa-download"></i></div>
				</div>
				<input type="file" class="wr-import-input" style="display:none;">
				<div class="setting-item export" title="<?php esc_attr_e( 'Export Header Template', 'wr-nitro' ); ?>">
					<div class="name"><a id="export_data"><i class="fa fa-upload"></i></a></div>
				</div>
				<div class="setting-item library hidden">
					<div class="name">
						<i class="fa fa-list-ul"></i>
						<span><?php esc_html_e( 'Header Library', 'wr-nitro' ); ?></span>
					</div>
				</div>

				<div class="setting-item default <?php echo ( ( $wr_hb_status == 'default' ) ? 'disabled' : 'set-default' ) ?>" <?php echo ( isset( $wr_nitro_header_id ) ? NULL : 'style="display: none;"' ); ?> title="<?php ( $wr_hb_status == 'default' ) ? esc_html_e( 'Default', 'wr-nitro' ) : esc_html_e( 'Set to default', 'wr-nitro' ); ?>">
					<div class="name">
						<i class="fa fa-star-o"></i>
					</div>
				</div>
				<div class="setting-item load-template">
					<div class="name">
						<?php esc_html_e( 'Load template', 'wr-nitro' ) ?>
					</div>
				</div>
			</div>
			<div class="position"><?php ( $wr_hb_layout == 'vertical' ) ? esc_html_e( 'Vertical layout', 'wr-nitro' ) : esc_html_e( 'Horizontal layout', 'wr-nitro' ); ?></div>
		</div>

		<div class="action-save"><img class="loading-icon" src="images/spinner.gif" /><div id="btn-save-header" class="button button-primary button-large"><?php
			if( $pagenow == 'post-new.php' ) {
				esc_html_e( 'Publish', 'wr-nitro' );
			} else {
				esc_html_e( 'Update', 'wr-nitro' );
			}
		  ?></div></div>

	</div>
	<div class="hb-wrapper-action">
		<div class="hb-list-element">
			<div class="header-item element-item" data-item="search">
				<i class="fa fa-search"></i>
				<span><?php esc_html_e( 'Search', 'wr-nitro' ); ?></span>
			</div>
			<div class="header-item element-item" data-item="menu">
				<i class="fa fa-location-arrow"></i>
				<span><?php esc_html_e( 'Menu', 'wr-nitro' ); ?></span>
			</div>
			<div class="header-item element-item" data-item="sidebar">
				<i class="fa fa-tasks"></i>
				<span><?php esc_html_e( 'Sidebar', 'wr-nitro' ); ?></span>
			</div>
			<div class="header-item element-item" data-item="text">
				<i class="fa fa-font"></i>
				<span><?php esc_html_e( 'Text', 'wr-nitro' ); ?></span>
			</div>
			<div class="header-item element-item" data-item="logo">
				<i class="fa fa-dot-circle-o"></i>
				<span><?php esc_html_e( 'Logo', 'wr-nitro' ); ?></span>
			</div>
			<div class="header-item element-item" data-item="social">
				<i class="fa fa-share-alt"></i>
				<span><?php esc_html_e( 'Socials', 'wr-nitro' ); ?></span>
			</div>

			<?php if( $wr_is_woocommerce_activated ) { ?>
				<div class="header-item element-item" data-item="shopping-cart">
					<i class="fa fa-shopping-cart"></i>
					<span><?php esc_html_e( 'Cart', 'wr-nitro' ); ?></span>
				</div>
			<?php } ?>

			<?php if( $wr_is_currency_activated ) { ?>
				<div class="header-item element-item" data-item="currency">
					<i class="fa fa-money"></i>
					<span><?php esc_html_e( 'Currency', 'wr-nitro' ); ?></span>
				</div>
			<?php } ?>

			<?php if( $wr_is_wpml_activated ) { ?>
				<div class="header-item element-item" data-item="wpml">
					<i class="fa fa-language"></i>
					<span><?php esc_html_e( 'WPML', 'wr-nitro' ); ?></span>
				</div>
			<?php } ?>

			<?php if( $wr_wishlist_activated ) { ?>
				<div class="header-item element-item" data-item="wishlist">
					<i class="nitro-icon-<?php echo esc_attr( $wr_nitro_options['wc_icon_set'] ); ?>-wishlist"></i>
					<span><?php esc_html_e( 'Wishlist', 'wr-nitro' ); ?></span>
				</div>
			<?php } ?>

			<div class="header-item element-item" data-item="flex">
				<i class="fa fa-arrows-h"></i>
				<span><?php esc_html_e( 'Flex', 'wr-nitro' ); ?></span>
			</div>
		</div>

		<div id="load-template">
			<h2 class="welcome"><?php esc_html_e( 'Welcome to Nitro Header Builder', 'wr-nitro' ) ?></h2>
			<p class="des-guide"><?php esc_html_e( 'You have a blank header, please adding content or get header template from our library', 'wr-nitro' ) ?></p>
			<div class="action">
				<div class="add-row"><i class="icon-row"></i><span><?php esc_html_e( 'Creat a blank Header', 'wr-nitro' ) ?></span></div>
				<div class="add-template"><i class="icon-template"></i><span><?php esc_html_e( 'Choose Template', 'wr-nitro' ) ?></span></div>
			</div>
			<p class="des-import"><?php echo wp_kses( __( 'or you can <span>import json backup file from your computer</span>.', 'wr-nitro' ), array( 'span' => array() ) ); ?></p>
		</div>

		<div class="content-container">
			<div class="hb-content">
				<div class="hb-content-inner" data-bind="visible:isDesktopView">
					<div class="hb-desktop-view">
						<?php get_template_part( 'woorockets/includes/header-builder/hb', 'content' ) ; ?>
					</div>
				</div>
				<div class="hb-content-inner" data-bind="visible:isMobileView">
					<div class="hb-mobile-view">
						<?php get_template_part( 'woorockets/includes/header-builder/hb', 'content' ) ; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="hb-panels">
		<!-- ================================================================================================================================================================ -->
		<div class="hb-settings-box hb-setting-inspector">
			<h3 class="title-setting"><?php esc_html_e( 'Header settings', 'wr-nitro' ); ?><span class="close-setting"></span></h3>
			<ul class="nav-settings">
				<li data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ); ?></li>
				<li data-nav="background"><?php esc_html_e( 'Background', 'wr-nitro' ); ?></li>
				<li data-nav="spacing"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></li>
			</ul>
			<div class="option-settings">
				<div data-option="general" class="item-option">
					<div class="row form-group">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Header position', 'wr-nitro' ); ?> <span class="wr-tooltip visible-horizontal-layout"><i class="fa fa-question-circle"></i><span class="des-tooltip"><?php esc_html_e( 'This parameter allows you to set the header area to be located in the standard "Normal" mode or "Fixed" one. To fully understand how it works just go to any pages live site after "Saving" the setting of Header Builder.', 'wr-nitro' ); ?></span></span></h5>
							<div class="content-group visible-without-vertical-desktop">
								<select class="slt" data-bind="position">
									<option value="inherit"><?php esc_html_e( 'Normal', 'wr-nitro' ); ?></option>
									<option value="fixed"><?php esc_html_e( 'Fixed', 'wr-nitro' ); ?></option>
								</select>
							</div>
							<div class="content-group visible-vertical-desktop">
								<select class="slt" data-bind="positionVertical">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-c-7" data-bind="visible:hideShowFixed">
							<h5 class="title-field"></h5>
							<div class="content-group visible-without-vertical-desktop">
								<select class="slt" data-bind="showHideFixed">
									<option value="show"><?php esc_html_e( 'Apply on checked pages', 'wr-nitro' ); ?></option>
									<option value="hide"><?php esc_html_e( 'Don\'t apply on checked pages', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
					<div class="row form-group scroll-fixed" data-bind="visible:hideShowFixed">
						<div class="col-2 visible-without-vertical-desktop">
							<div class="content-group">
								<div class="show-hide-fixed">
									<?php
										$i = 0;
										foreach( $wr_hide_show_fixed as $key => $val ) {

											$i++;

											echo '
									<div class="sh-fixed-box">
										<div class="sh-fixed-title">' . $val['title'] . ' <i class="fa fa-angle-up"></i></div>
										<div class="sh-fixed-list">';
											if( $val['data'] ) {
												foreach( $val['data'] as $key_item => $val_item ) {
													echo '<label class="sh-fixed-item"><input type="checkbox" data-bind="fixedList.' . esc_attr( $key ) . '.' . esc_attr( $key_item ) . '" /><span>' . esc_attr( $val_item ) . '</span></label>';
												}
											}
										echo '
										</div>
									</div>';

											if( ( $i % ( ceil( count( $wr_hide_show_fixed )/2 ) ) ) == 0 && $i < count( $wr_hide_show_fixed ) ) {
												echo '
								</div>
							</div>
						</div>
						<div class="col-2" data-bind="visible:hideShowFixed">
							<div class="content-group">
								<div class="show-hide-fixed">
												';
											}
										}
									?>

								</div><!-- .show-hide-fixed -->
							</div>
						</div>
					</div>
					<div class="row form-group visible-vertical-desktop">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Width', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.width" /></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"></h5>
							<div class="content-group">
								<div class="radio-list">
									<label class="radio-item">
										<input name="background-style" type="radio" class="rdo" value="px" data-bind="unit"  />
										<span>px</span>
									</label>
									<label class="radio-item">
										<input name="background-style" type="radio" class="rdo" value="%" data-bind="unit" />
										<span>%</span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
				</div>
				<div data-option="background" class="item-option">
					<div class="row form-group">
						<div class="col-3-r">
							<h5 class="title-field"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file wr-background-image" data-bind="style.backgroundImage" />
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="style.backgroundColor" />
								<span class="font-color" data-bind="text:style.backgroundColor"></span>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:style.backgroundImageNotEmpty">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG size', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundSize">
									<option value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
									<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
									<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundPosition">
									<option value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
									<option value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
									<option value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
									<option value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
									<option value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
									<option value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
									<option value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
									<option value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
									<option value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG repeat', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundRepeat">
									<option value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
									<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
									<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
									<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div data-option="spacing" class="item-option">
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="style.marginTop" placeholder="-" />
										<input type="number" step="any" class="txt input-margin right" data-bind="style.marginRight" placeholder="-" />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="style.marginBottom" placeholder="-" />
										<input type="number" step="any" class="txt input-margin left" data-bind="style.marginLeft" placeholder="-" />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="style.borderTopWidth" placeholder="-" />
											<input type="number" step="any" class="txt input-border right" data-bind="style.borderRightWidth" placeholder="-" />
											<input type="number" step="any" class="txt input-border bottom" data-bind="style.borderBottomWidth" placeholder="-" />
											<input type="number" step="any" class="txt input-border left" data-bind="style.borderLeftWidth" placeholder="-" />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="style.paddingTop" placeholder="-" />
												<input type="number" step="any" class="txt input-padding right" data-bind="style.paddingRight" placeholder="-" />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="style.paddingBottom" placeholder="-" />
												<input type="number" step="any" class="txt input-padding left" data-bind="style.paddingLeft" placeholder="-" />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="style.borderColor" />
										<span class="font-color" data-bind="text:style.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="style.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.borderRadius" /></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ================================================================================================================================================================ -->
		<div class="hb-settings-box hb-row-inspector">
			<h3 class="title-setting"><?php esc_html_e( 'Row and container settings', 'wr-nitro' ); ?><span class="close-setting"></span></h3>
			<ul class="nav-settings">
				<li data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ); ?></li>
				<li data-nav="container-setting"><?php esc_html_e( 'Container setting', 'wr-nitro' ); ?></li>
				<li data-nav="row-setting"><?php esc_html_e( 'Row setting', 'wr-nitro' ); ?></li>
			</ul>
			<div class="option-settings">
				<div data-option="general" class="item-option">
					<div class="row form-group">
						<div class="content-group">
							<label class="chb">
								<input type="checkbox" class="chb-use-theme" data-bind="themeColor" />
								<span><?php esc_html_e( 'Use theme default color', 'wr-nitro' ); ?></span>
							</label>
							<p class="des-option"><?php esc_html_e( 'Use color from the theme.', 'wr-nitro' ); ?></p>
						</div>
					</div>
					<div class="row form-group">
						<div class="content-group">
							<label class="chb">
								<input type="checkbox" data-bind="sticky" />
								<span><?php esc_html_e( 'Enable sticky', 'wr-nitro' ); ?></span>
							</label>
							<p class="des-option"><?php esc_html_e( 'If enable then sticky setting of row more will disable.', 'wr-nitro' ); ?></p>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:stickyShow">
						<div class="col-1">
							<h5 class="title-field"><?php esc_html_e( 'Sticky effect', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="radio-list">
									<label class="radio-item">
										<input name="sticky-effect" type="radio" class="rdo" value="normal" data-bind="sticky_effect"  />
										<span><?php esc_html_e( 'Normal', 'wr-nitro' ); ?></span>
									</label>
									<label class="radio-item">
										<input name="sticky-effect" type="radio" class="rdo" value="hidden" data-bind="sticky_effect" />
										<span><?php esc_html_e( 'Hidden when scroll down', 'wr-nitro' ); ?></span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:stickyShow">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Sticky height', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="heightSticky" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Sticky background color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="backgroundColorSticky" />
								<span class="font-color" data-bind="text:backgroundColorSticky"></span>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Sticky text color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="textColorSticky" />
								<span class="font-color" data-bind="text:textColorSticky"></span>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className"/></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
				</div>
				<div data-option="container-setting" class="item-option">
					<h4 class="title-form"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></h4>
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="cols[0].style.marginTop" placeholder="-" />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="cols[0].style.marginBottom" placeholder="-" />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="cols[0].style.borderTopWidth" placeholder="-" />
											<input type="number" step="any" class="txt input-border right" data-bind="cols[0].style.borderRightWidth" placeholder="-" />
											<input type="number" step="any" class="txt input-border bottom" data-bind="cols[0].style.borderBottomWidth" placeholder="-" />
											<input type="number" step="any" class="txt input-border left" data-bind="cols[0].style.borderLeftWidth" placeholder="-" />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="cols[0].style.paddingTop" placeholder="-" />
												<input type="number" step="any" class="txt input-padding right" data-bind="cols[0].style.paddingRight" placeholder="-" />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="cols[0].style.paddingBottom" placeholder="-" />
												<input type="number" step="any" class="txt input-padding left" data-bind="cols[0].style.paddingLeft" placeholder="-" />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="cols[0].style.borderColor" />
										<span class="font-color" data-bind="text:cols[0].style.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="cols[0].style.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="cols[0].style.borderRadius" /></div>
							</div>
						</div>
					</div>
					<h4 class="title-form" data-bind="visible:vertical"><?php esc_html_e( 'Background', 'wr-nitro' ); ?></h4>
					<div class="row form-group" data-bind="visible:vertical">
						<div class="col-3-r">
							<h5 class="title-field"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file wr-background-image" data-bind="cols[0].style.backgroundImage" />
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="cols[0].style.backgroundColor" />
								<span class="font-color" data-bind="text:cols[0].style.backgroundColor"></span>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:cols[0].style.backgroundImageNotEmpty">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG size', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt"  data-bind="cols[0].style.backgroundSize">
									<option value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
									<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
									<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="cols[0].style.backgroundPosition" >
									<option value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
									<option value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
									<option value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
									<option value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
									<option value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
									<option value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
									<option value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
									<option value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
									<option value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG repeat', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt"  data-bind="cols[0].style.backgroundRepeat">
									<option value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
									<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
									<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
									<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
					<h5 class="title-field" data-bind="visible:vertical"><?php esc_html_e( 'Width', 'wr-nitro' ); ?></h5>
					<div class="row form-group" data-bind="visible:vertical">
						<div class="col-3">
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="cols[0].style.maxWidth" /></div>
						</div>
						<div class="col-2">
							<div class="content-group">
								<div class="radio-list">
									<label class="radio-item">
										<input name="background-style-row" type="radio" class="rdo" value="px" data-bind="cols[0].unit"  />
										<span>px</span>
									</label>
									<label class="radio-item">
										<input name="background-style-row" type="radio" class="rdo" value="%" data-bind="cols[0].unit" />
										<span>%</span>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div data-option="row-setting" class="item-option">
					<h4 class="title-form"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></h4>
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="style.marginTop" placeholder="-" />
										<input type="number" step="any" class="txt input-margin right" data-bind="style.marginRight" placeholder="-" />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="style.marginBottom" placeholder="-" />
										<input type="number" step="any" class="txt input-margin left" data-bind="style.marginLeft" placeholder="-" />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="style.borderTopWidth" placeholder="-" />
											<input type="number" step="any" class="txt input-border right" data-bind="style.borderRightWidth" placeholder="-" />
											<input type="number" step="any" class="txt input-border bottom" data-bind="style.borderBottomWidth" placeholder="-" />
											<input type="number" step="any" class="txt input-border left" data-bind="style.borderLeftWidth" placeholder="-" />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="style.paddingTop" placeholder="-" />
												<input type="number" step="any" class="txt input-padding right" data-bind="style.paddingRight" placeholder="-" />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="style.paddingBottom" placeholder="-" />
												<input type="number" step="any" class="txt input-padding left" data-bind="style.paddingLeft" placeholder="-" />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="style.borderColor" />
										<span class="font-color" data-bind="text:style.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="style.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.borderRadius" /></div>
							</div>
						</div>
					</div>
					<h4 class="title-form" data-bind="visible:vertical"><?php esc_html_e( 'Background', 'wr-nitro' ); ?></h4>
					<div class="row form-group" data-bind="visible:vertical">
						<div class="col-3-r">
							<h5 class="title-field"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file wr-background-image" data-bind="style.backgroundImage"  />
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="style.backgroundColor" />
								<span class="font-color" data-bind="text:style.backgroundColor"></span>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:style.backgroundImageNotEmpty">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG size', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundSize" >
									<option value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
									<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
									<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundPosition">
									<option value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
									<option value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
									<option value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
									<option value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
									<option value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
									<option value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
									<option value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
									<option value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
									<option value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG repeat', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundRepeat">
									<option value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
									<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
									<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
									<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ================================================================================================================================================================ -->
		<div class="hb-settings-box hb-search-inspector">
			<h3 class="title-setting"><?php esc_html_e( 'Search settings', 'wr-nitro' ); ?><span class="close-setting"></span></h3>
			<ul class="nav-settings">
				<li data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ); ?></li>
				<li data-nav="spacing"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></li>
			</ul>
			<div class="option-settings">
				<div data-option="general" class="item-option">
					<div class="row form-group">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Layout style', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="layout">
									<option value="full-screen"><?php esc_html_e( 'Full screen', 'wr-nitro' ); ?></option>
									<option value="dropdown"><?php esc_html_e( 'Dropdown', 'wr-nitro' ); ?></option>
									<option value="boxed"><?php esc_html_e( 'Boxed', 'wr-nitro' ); ?></option>
									<option value="topbar"><?php esc_html_e( 'Topbar', 'wr-nitro' ); ?></option>
									<option value="expand-width"><?php esc_html_e( 'Expand width', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Theme', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="radio-list">
									<label class="radio-item">
										<input name="background-style-search" type="radio" class="rdo" data-bind="searchStyle" value="light-background" />
										<span><?php esc_html_e( 'Light', 'wr-nitro' ); ?></span>
									</label>
									<label class="radio-item">
										<input name="background-style-search" type="radio" class="rdo" data-bind="searchStyle" value="dark-background" />
										<span><?php esc_html_e( 'Dark', 'wr-nitro' ); ?></span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:dropdownLayout">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Form animation', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="animation" >
									<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
									<option value="fade"><?php esc_html_e( 'Fade', 'wr-nitro' ); ?></option>
									<option value="left-to-right"><?php esc_html_e( 'Left to right', 'wr-nitro' ); ?></option>
									<option value="right-to-left"><?php esc_html_e( 'Right to left', 'wr-nitro' ); ?></option>
									<option value="bottom-to-top"><?php esc_html_e( 'Bottom to top', 'wr-nitro' ); ?></option>
									<option value="scale"><?php esc_html_e( 'Scale', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Form margin top', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="marginTop" /></div>
						</div>
					</div>

					<div class="row form-group" data-bind="visible:showButton">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Button type', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="radio-list">
									<label class="radio-item">
										<input name="background-button-style" type="radio" class="rdo" data-bind="buttonType" value="icon" />
										<span><?php esc_html_e( 'Icon', 'wr-nitro' ); ?></span>
									</label>
									<label class="radio-item">
										<input name="background-button-style" type="radio" class="rdo" data-bind="buttonType" value="text" />
										<span><?php esc_html_e( 'Text', 'wr-nitro' ); ?></span>
									</label>
								</div>
							</div>
						</div>
					</div>

					<div class="row form-group" data-bind="visible:showIconButton">
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Icon color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="iconColor" />
								<span class="font-color" data-bind="text:iconColor"></span>
							</div>
						</div>
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Hover icon color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="hoverIconColor" />
								<span class="font-color" data-bind="text:hoverIconColor"></span>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Icon size', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="iconFontSize" /></div>
						</div>
					</div>

					<div data-bind="visible:showTextButton">
						<div class="row form-group" data-bind="visible:showButton">
							<div class="col-3">
								<h5 class="title-field"><?php esc_html_e( 'Text button', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="text" class="txt" data-bind="textButton" /></div>
							</div>
							<div class="col-3">
								<h5 class="title-field"><?php esc_html_e( 'Text color button', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="textColorButton" />
									<span class="font-color" data-bind="text:textColorButton"></span>
								</div>
							</div>
							<div class="col-3">
								<h5 class="title-field"><?php esc_html_e( 'Background color button', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="bgColorButton" />
									<span class="font-color" data-bind="text:bgColorButton"></span>
								</div>
							</div>
						</div>
						<div class="row form-group" data-bind="visible:showButton">
							<div class="col-3">
								<h5 class="title-field"><?php esc_html_e( 'Hover text color button', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="hoverTextColorButton" />
									<span class="font-color" data-bind="text:hoverTextColorButton"></span>
								</div>
							</div>
							<div class="col-3">
								<h5 class="title-field" style="white-space: nowrap;"><?php esc_html_e( 'Hover background color button', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="hoverBgColorButton" />
									<span class="font-color" data-bind="text:hoverBgColorButton"></span>
								</div>
							</div>
						</div>
					</div>

					<div class="row form-group">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Placeholder', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="placeholder" /></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Width text search', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="widthInput" /></div>
						</div>
					</div>
			<?php
				if ( $wr_is_live_search_activated ) {
			?>
					<div class="row form-group">
						<div class="col-3">
							<div class="content-group">
								<label class="chb">
									<input type="checkbox" value="1" data-bind="liveSearch.active" />
									<span><?php esc_html_e( 'Live search', 'wr-nitro' ); ?></span>
								</label>
							</div>
						</div>
					</div>
					<h4 data-bind="visible:showLiveSearch" style="margin-bottom: 10px;" class="title-form"><?php esc_html_e( 'Live search', 'wr-nitro' ); ?></h4>
					<div class="row form-group" data-bind="visible:showLiveSearch">
						<div class="col-2">
							<div class="content-group">
								<label class="chb">
									<input type="checkbox" value="1" data-bind="liveSearch.show_category" />
									<span><?php esc_html_e( 'Show category list', 'wr-nitro' ); ?></span>
								</label>
							</div>
						</div>
						<div class="col-2">
							<div class="content-group">
								<label class="chb">
									<input type="checkbox" value="1" data-bind="liveSearch.show_suggestion" />
									<span><?php esc_html_e( 'Show suggestion', 'wr-nitro' ); ?></span>
								</label>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:showLiveSearch">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Minimum number of characters', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="liveSearch.min_characters" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Maximum number of results', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="liveSearch.max_results" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Thumbnail size (Defined in pixel)', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="liveSearch.thumb_size" /></div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:showLiveSearch">
						<div class="col-1">
							<h5 class="title-field"><?php esc_html_e( 'Search in', 'wr-nitro' ); ?></h5>
							<div class="content-group search-in">
								<label class="chb">
									<input type="checkbox" value="1" checked="checked" data-bind="liveSearch.searchIn.title" />
									<span><?php esc_html_e( 'Title', 'wr-nitro' ); ?></span>
								</label>
								<label class="chb" style="margin-left: 25px; ">
									<input type="checkbox" value="1" checked="checked" data-bind="liveSearch.searchIn.description" />
									<span><?php esc_html_e( 'Description', 'wr-nitro' ); ?></span>
								</label>
								<label class="chb" style="margin-left: 25px; ">
									<input type="checkbox" value="1" checked="checked" data-bind="liveSearch.searchIn.content" />
									<span><?php esc_html_e( 'Content', 'wr-nitro' ); ?></span>
								</label>
								<label class="chb" style="margin-left: 25px; ">
									<input type="checkbox" value="1" checked="checked" data-bind="liveSearch.searchIn.sku" />
									<span><?php esc_html_e( 'SKU', 'wr-nitro' ); ?></span>
								</label>
							</div>
						</div>
					</div>
			<?php
				}
			?>
					<div class="content-group hide-desktop-vertical">
						<label class="chb">
							<input type="checkbox" data-bind="centerElement" />
							<span><?php esc_html_e( 'Enable center element', 'wr-nitro' ); ?></span>
						</label>
						<p class="des-option"><?php esc_html_e( 'If enabled then Center Element Setting of element More on the same row will be disabled', 'wr-nitro' ) ?></p>
					</div>
					<div class="row form-group visible-desktop-vertical">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Align self', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="alignVertical">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="center"><?php esc_html_e( 'Center', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
				</div>
				<div data-option="spacing" class="item-option">
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="style.marginTop" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin right" data-bind="style.marginRight" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="style.marginBottom" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin left" data-bind="style.marginLeft" placeholder="-"  />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="style.borderTopWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border right" data-bind="style.borderRightWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border bottom" data-bind="style.borderBottomWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border left" data-bind="style.borderLeftWidth" placeholder="-"  />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="style.paddingTop" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding right" data-bind="style.paddingRight" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="style.paddingBottom" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding left" data-bind="style.paddingLeft" placeholder="-"  />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="style.borderColor" />
										<span class="font-color" data-bind="text:style.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="style.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.borderRadius" /></div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3-r">
							<h5 class="title-field"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file wr-background-image" data-bind="style.backgroundImage" />
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="style.backgroundColor" />
								<span class="font-color" data-bind="text:style.backgroundColor"></span>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:style.backgroundImageNotEmpty">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG size', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundSize">
									<option value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
									<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
									<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundPosition">
									<option value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
									<option value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
									<option value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
									<option value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
									<option value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
									<option value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
									<option value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
									<option value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
									<option value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG repeat', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundRepeat">
									<option value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
									<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
									<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
									<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ================================================================================================================================================================ -->
		<div class="hb-settings-box hb-menu-inspector">
			<h3 class="title-setting"><?php esc_html_e( 'Menu settings', 'wr-nitro' ); ?><span class="close-setting"></span></h3>
			<ul class="nav-settings">
				<li data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ); ?></li>
				<li data-nav="text-setting"><?php esc_html_e( 'Text setting', 'wr-nitro' ); ?></li>
				<li data-nav="submenu"><?php esc_html_e( 'Submenu', 'wr-nitro' ); ?></li>
				<li data-nav="spacing"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></li>
			</ul>
			<div class="option-settings">
				<div data-option="general" class="item-option">
					<div class="visible-desktop-layout">
						<div class="row form-group">
							<div class="col-2">
								<h5 class="title-field"><?php esc_html_e( 'Choose menu', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="menuID" >
										<option value=""><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<?php
											if( $wr_menus ) {
												foreach ( $wr_menus as $val ) {
													echo '<option value="' . (int) $val->term_id . '">' . esc_attr( $val->name  ) . '</option>';
												}
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-2">
								<h5 class="title-field"><?php esc_html_e( 'Layout style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="radio-list">
										<label class="radio-item">
											<input name="layout-style" type="radio" class="rdo el-show-width-submmenu" data-bind="layoutStyle" value="text" />
											<span><?php esc_html_e( 'Text', 'wr-nitro' ); ?></span>
										</label>
										<label class="radio-item">
											<input name="layout-style" type="radio" class="rdo el-show-width-submmenu" data-bind="layoutStyle" value="icon" />
											<span><?php esc_html_e( 'Icon', 'wr-nitro' ); ?></span>
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class="row form-group" data-bind="visible:layoutStyleIcon">
							<div class="col-1">
								<h5 class="title-field"><?php esc_html_e( 'Menu style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="radio-list">
										<label class="radio-item">
											<input name="menu-style" type="radio" class="rdo el-show-width-submmenu" data-bind="menuStyle" value="fullscreen"  />
											<span><?php esc_html_e( 'Fullscreen', 'wr-nitro' ); ?></span>
										</label>
										<label class="radio-item">
											<input name="menu-style" type="radio" class="rdo el-show-width-submmenu" data-bind="menuStyle" value="sidebar" />
											<span><?php esc_html_e( 'Sidebar', 'wr-nitro' ); ?></span>
										</label>
									</div>
								</div>
							</div>
						</div>
						<h4 class="title-form" data-bind="visible:layoutStyleText"><?php esc_html_e( 'Hover', 'wr-nitro' ); ?></h4>
						<div class="row form-group" data-bind="visible:layoutStyleText">
							<div class="col-3">
								<h5 class="title-field"><?php esc_html_e( 'Hover style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="hoverStyle" >
										<option value="default"><?php esc_html_e( 'Default', 'wr-nitro' ); ?></option>
										<option value="underline"><?php esc_html_e( 'Underline', 'wr-nitro' ); ?></option>
										<option value="background"><?php esc_html_e( 'Background', 'wr-nitro' ); ?></option>
										<option value="ouline"><?php esc_html_e( 'Ouline', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="col-3 color-theme" data-bind="visible:defaultHover">
								<h5 class="title-field"><?php esc_html_e( 'Hover text color', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="link.style.colorHover" />
									<span class="font-color" data-bind="text:link.style.colorHover"></span>
								</div>
							</div>
							<div class="col-3" data-bind="visible:oulineHover">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="link.underlineStyle">
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="col-3" data-bind="visible:oulineHover">
								<h5 class="title-field"><?php esc_html_e( 'Border width', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="link.underlineWidth" /></div>
							</div>
							<div class="col-3" data-bind="visible:underlineHover">
								<h5 class="title-field"><?php esc_html_e( 'Underline style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="link.underlineStyle">
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="col-3" data-bind="visible:underlineHover">
								<h5 class="title-field"><?php esc_html_e( 'Underline width', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="link.underlineWidth" /></div>
							</div>
						</div>
						<div class="row form-group" data-bind="visible:layoutStyleText">
							<div class="col-3 color-theme" data-bind="visible:underlineHover">
								<h5 class="title-field"><?php esc_html_e( 'Hover text color', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="link.style.colorHover" />
									<span class="font-color" data-bind="text:link.style.colorHover"></span>
								</div>
							</div>
							<div class="col-3 color-theme" data-bind="visible:underlineHover">
								<h5 class="title-field"><?php esc_html_e( 'Underline color', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="link.style.underlineColorHover" />
									<span class="font-color" data-bind="text:link.style.underlineColorHover"></span>
								</div>
							</div>
							<div class="col-3 color-theme" data-bind="visible:backgroundHover">
								<h5 class="title-field"><?php esc_html_e( 'Hover text color', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="link.style.colorHover" />
									<span class="font-color" data-bind="text:link.style.colorHover"></span>
								</div>
							</div>
							<div class="col-3 color-theme" data-bind="visible:backgroundHover" >
								<h5 class="title-field"><?php esc_html_e( 'Hover background color', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="link.style.backgroundColorHover" />
									<span class="font-color" data-bind="text:link.style.backgroundColorHover"></span>
								</div>
							</div>
							<div class="col-3" data-bind="visible:backgroundHover">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="link.borderRadius" /></div>
							</div>
							<div class="col-3 color-theme" data-bind="visible:oulineHover">
								<h5 class="title-field"><?php esc_html_e( 'Hover text color', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="link.style.colorHover" />
									<span class="font-color" data-bind="text:link.style.colorHover"></span>
								</div>
							</div>
							<div class="col-3 color-theme" data-bind="visible:oulineHover">
								<h5 class="title-field"><?php esc_html_e( 'Hover outline color', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="link.style.outlineColorHover" />
									<span class="font-color" data-bind="text:link.style.outlineColorHover"></span>
								</div>
							</div>
							<div class="col-3" data-bind="visible:oulineHover">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="link.borderRadius" /></div>
							</div>
						</div>
						<div class="row form-group" data-bind="visible:layoutStyleText">
							<div class="content-group hide-desktop-vertical">
								<label class="chb">
									<input type="checkbox" data-bind="centerElement" />
									<span><?php esc_html_e( 'Enable center element', 'wr-nitro' ) ?></span>
								</label>
								<p class="des-option"><?php esc_html_e( 'If enabled then Center Element Setting of element More on the same row will be disabled', 'wr-nitro' ); ?></p>
							</div>
						</div>
						<div class="row form-group" data-bind="visible:layoutStyleText">
							<div class="col-2">
								<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
							</div>
							<div class="col-2">
								<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
							</div>
						</div>
						<div class="form-group" data-bind="visible:layoutStyleIcon">
							<!-- Settings for sidebar -->
							<div class="row form-group" data-bind="visible:menuStyleSidebar">
								<div class="col-4">
									<h5 class="title-field"><?php esc_html_e( 'Position', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<select class="slt" data-bind="position" >
											<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
											<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
								<div class="col-4">
									<h5 class="title-field"><?php esc_html_e( 'Animation', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<select class="slt" data-bind="animation">
											<option value="slide-in-on-top"><?php esc_html_e( 'Slide in on top', 'wr-nitro' ); ?></option>
											<option value="push"><?php esc_html_e( 'Push', 'wr-nitro' ); ?></option>
											<option value="fall-down"><?php esc_html_e( 'Fall down', 'wr-nitro' ); ?></option>
											<option value="fall-up"><?php esc_html_e( 'Fall up', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
								<div class="col-4">
									<h5 class="title-field"><?php esc_html_e( 'Vertical align', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<select class="slt" data-bind="verticalAlign" >
											<option value="top"><?php esc_html_e( 'Top', 'wr-nitro' ); ?></option>
											<option value="middle"><?php esc_html_e( 'Middle', 'wr-nitro' ); ?></option>
											<option value="bottom"><?php esc_html_e( 'Bottom', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
								<div class="col-4 color-theme">
									<h5 class="title-field"><?php esc_html_e( 'Icon color', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<div class="wr-hb-colors-control">
											<input type="text" class="txt wr-color" data-bind="iconColor" />
											<span class="font-color" data-bind="text:iconColor"></span>
										</div>
									</div>
								</div>
							</div>
							<div class="row form-group" data-bind="visible:menuStyleSidebar">
								<div class="col-3">
									<h5 class="title-field"><?php esc_html_e( 'Width', 'wr-nitro' ); ?></h5>
									<div class="content-group"><input type="number" step="any" class="txt" data-bind="widthSidebar" /></div>
								</div>
								<div class="col-3">
									<h5 class="title-field"></h5>
									<div class="content-group">
										<div class="radio-list">
											<label class="radio-item">
												<input name="background-style-menu" type="radio" class="rdo" value="px" data-bind="unitWidthSidebar"  />
												<span>px</span>
											</label>
											<label class="radio-item">
												<input name="background-style-menu" type="radio" class="rdo" value="%" data-bind="unitWidthSidebar" />
												<span>%</span>
											</label>
										</div>
									</div>
								</div>
							</div>

							<!-- Settings for fullscreen -->
							<div class="row form-group" data-bind="visible:menuStyleFullscreen">
								<div class="col-3">
									<h5 class="title-field"><?php esc_html_e( 'Effect', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<select class="slt" data-bind="effect" >
											<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
											<option value="fade"><?php esc_html_e( 'Fade', 'wr-nitro' ); ?></option>
											<option value="scale"><?php esc_html_e( 'Scale', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
								<div class="col-3 color-theme">
									<h5 class="title-field"><?php esc_html_e( 'Icon color', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<div class="wr-hb-colors-control">
											<input type="text" class="txt wr-color" data-bind="iconColor" />
											<span class="font-color" data-bind="text:iconColor"></span>
										</div>
									</div>
								</div>
							</div>
							<h4 data-bind="visible:layoutStyleIcon" class="title-form"><?php esc_html_e( 'Background', 'wr-nitro' ); ?></h4>
							<div class="row form-group" data-bind="visible:layoutStyleIcon">
								<div class="col-3-r color-theme">
									<h5 class="title-field"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<div class="file-image">
											<input type="text" class="txt input-file wr-background-image"  data-bind="background.backgroundImage" />
											<span class="select-image">...</span>
											<i class="remove-image fa fa-times"></i>
										</div>
									</div>
								</div>
								<div class="col-3 color-theme">
									<h5 class="title-field"><?php esc_html_e( 'BG color', 'wr-nitro' ); ?></h5>
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="background.backgroundColor"/>
										<span class="font-color" data-bind="text:background.backgroundColor"></span>
									</div>
								</div>
							</div>
							<div class="row form-group" data-bind="visible:background.backgroundImageNotEmpty">
								<div class="col-3">
									<h5 class="title-field"><?php esc_html_e( 'BG size', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<select class="slt" data-bind="background.backgroundSize">
											<option value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
											<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
											<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
								<div class="col-3">
									<h5 class="title-field"><?php esc_html_e( 'BG position', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<select class="slt" data-bind="background.backgroundPosition">
											<option value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
											<option value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
											<option value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
											<option value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
											<option value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
											<option value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
											<option value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
											<option value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
											<option value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
								<div class="col-3">
									<h5 class="title-field"><?php esc_html_e( 'BG repeat', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<select class="slt" data-bind="background.backgroundRepeat">
											<option value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
											<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
											<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
											<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
							</div>
							<h4 class="title-form"><?php esc_html_e( 'Hover', 'wr-nitro' ); ?></h4>
							<div class="row form-group">
								<div class="col-3">
									<h5 class="title-field"><?php esc_html_e( 'Hover style', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<select class="slt" data-bind="hoverStyle" >
											<option value="default"><?php esc_html_e( 'Default', 'wr-nitro' ); ?></option>
											<option value="underline"><?php esc_html_e( 'Underline', 'wr-nitro' ); ?></option>
											<option value="background"><?php esc_html_e( 'Background', 'wr-nitro' ); ?></option>
											<option value="ouline"><?php esc_html_e( 'Ouline', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
								<div class="col-3 color-theme" data-bind="visible:defaultHover">
									<h5 class="title-field"><?php esc_html_e( 'Hover text color', 'wr-nitro' ); ?></h5>
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="link.style.colorHover" />
										<span class="font-color" data-bind="text:link.style.colorHover"></span>
									</div>
								</div>
								<div class="col-3" data-bind="visible:oulineHover">
									<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<select class="slt" data-bind="link.underlineStyle">
											<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
											<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
											<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
											<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
											<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
											<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
											<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
											<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
								<div class="col-3" data-bind="visible:oulineHover">
									<h5 class="title-field"><?php esc_html_e( 'Border width', 'wr-nitro' ); ?></h5>
									<div class="content-group"><input type="number" step="any" class="txt" data-bind="link.underlineWidth" /></div>
								</div>
								<div class="col-3" data-bind="visible:underlineHover">
									<h5 class="title-field"><?php esc_html_e( 'Underline style', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<select class="slt" data-bind="link.underlineStyle">
											<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
											<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
											<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
											<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
											<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
											<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
											<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
											<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
								<div class="col-3" data-bind="visible:underlineHover">
									<h5 class="title-field"><?php esc_html_e( 'Underline width', 'wr-nitro' ); ?></h5>
									<div class="content-group"><input type="number" step="any" class="txt" data-bind="link.underlineWidth" /></div>
								</div>
							</div>
							<div class="row form-group">
								<div class="col-3 color-theme" data-bind="visible:underlineHover">
									<h5 class="title-field"><?php esc_html_e( 'Hover text color', 'wr-nitro' ); ?></h5>
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="link.style.colorHover" />
										<span class="font-color" data-bind="text:link.style.colorHover"></span>
									</div>
								</div>
								<div class="col-3 color-theme" data-bind="visible:underlineHover">
									<h5 class="title-field"><?php esc_html_e( 'Underline color', 'wr-nitro' ); ?></h5>
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="link.style.underlineColorHover" />
										<span class="font-color" data-bind="text:link.style.underlineColorHover"></span>
									</div>
								</div>
								<div class="col-3 color-theme" data-bind="visible:backgroundHover">
									<h5 class="title-field"><?php esc_html_e( 'Hover text color', 'wr-nitro' ); ?></h5>
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="link.style.colorHover" />
										<span class="font-color" data-bind="text:link.style.colorHover"></span>
									</div>
								</div>
								<div class="col-3 color-theme" data-bind="visible:backgroundHover" >
									<h5 class="title-field"><?php esc_html_e( 'Hover background color', 'wr-nitro' ); ?></h5>
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="link.style.backgroundColorHover" />
										<span class="font-color" data-bind="text:link.style.backgroundColorHover"></span>
									</div>
								</div>
								<div class="col-3" data-bind="visible:backgroundHover">
									<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
									<div class="content-group"><input type="number" step="any" class="txt" data-bind="link.borderRadius" /></div>
								</div>
								<div class="col-3 color-theme" data-bind="visible:oulineHover">
									<h5 class="title-field"><?php esc_html_e( 'Hover text color', 'wr-nitro' ); ?></h5>
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="link.style.colorHover" />
										<span class="font-color" data-bind="text:link.style.colorHover"></span>
									</div>
								</div>
								<div class="col-3 color-theme" data-bind="visible:oulineHover">
									<h5 class="title-field"><?php esc_html_e( 'Hover outline color', 'wr-nitro' ); ?></h5>
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="link.style.outlineColorHover" />
										<span class="font-color" data-bind="text:link.style.outlineColorHover"></span>
									</div>
								</div>
								<div class="col-3" data-bind="visible:oulineHover">
									<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
									<div class="content-group"><input type="number" step="any" class="txt" data-bind="link.borderRadius" /></div>
								</div>
							</div>
							<div class="row form-group hide-desktop-vertical">
								<div class="content-group">
									<label class="chb">
										<input type="checkbox" data-bind="centerElement" />
										<span><?php esc_html_e( 'Enable center element', 'wr-nitro' ); ?></span>
									</label>
									<p class="des-option"><?php esc_html_e( 'If enabled then Center Element Setting of element More on the same row will be disabled', 'wr-nitro' ); ?></p>
								</div>
							</div>
							<div class="row form-group visible-desktop-vertical">
								<div class="col-3">
									<h5 class="title-field"><?php esc_html_e( 'Align self', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<select class="slt" data-bind="alignVertical">
											<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
											<option value="center"><?php esc_html_e( 'Center', 'wr-nitro' ); ?></option>
											<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
								<div class="col-3">
									<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
									<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
								</div>
								<div class="col-3">
									<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
									<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
								</div>
							</div>
							<div class="row form-group hide-desktop-vertical">
								<div class="col-2">
									<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
									<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
								</div>
								<div class="col-2">
									<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
									<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
								</div>
							</div>
						</div>
					</div><!-- .visible-desktop-layout -->
					<div class="visible-mobile-layout">
						<div class="row form-group">
							<div class="col-2">
								<h5 class="title-field"><?php esc_html_e( 'Choose menu', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="menuID" >
										<option value="0"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<?php
											if( $wr_menus ) {
												foreach ( $wr_menus as $val ) {
													echo '<option value="' . (int) $val->term_id . '">' . esc_attr( $val->name  ) . '</option>';
												}
											}
										?>
									</select>
								</div>
							</div>
						</div>

						<div class="row form-group">
							<div class="col-2">
								<h5 class="title-field"><?php esc_html_e( 'Layout style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="radio-list">
										<label class="radio-item">
											<input name="layout-style" type="radio" class="rdo el-show-width-submmenu" data-bind="layoutStyleMobile" value="text" />
											<span>
												<?php esc_html_e( 'Text', 'wr-nitro' ); ?>
												<span class="wr-tooltip visible-horizontal-layout"><i class="fa fa-question-circle"></i><span class="des-tooltip"><?php esc_html_e( 'This option will be show menu level 1 only.', 'wr-nitro' ); ?></span></span>
											</span>
										</label>
										<label class="radio-item">
											<input name="layout-style" type="radio" class="rdo el-show-width-submmenu" data-bind="layoutStyleMobile" value="icon" />
											<span><?php esc_html_e( 'Icon', 'wr-nitro' ); ?></span>
										</label>
									</div>
								</div>
							</div>
						</div>

						<div class="row form-group" data-bind="visible:layoutStyleMobileIcon">
							<div class="col-3 color-theme">
								<h5 class="title-field"><?php esc_html_e( 'Background color', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="backgroundColorMobile" />
									<span class="font-color" data-bind="text:backgroundColorMobile"></span>
								</div>
							</div>
							<div class="col-3 color-theme">
								<h5 class="title-field"><?php esc_html_e( 'Icon color', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="iconColorMobile" />
									<span class="font-color" data-bind="text:iconColorMobile"></span>
								</div>
							</div>
						</div>
						<div class="row form-group hide-desktop-vertical">
							<div class="content-group">
								<label class="chb">
									<input type="checkbox" data-bind="centerElement" />
									<span><?php esc_html_e( 'Enable center element', 'wr-nitro' ); ?></span>
								</label>
								<p class="des-option"><?php esc_html_e( 'If enabled then Center Element Setting of element More on the same row will be disabled', 'wr-nitro' ); ?></p>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-2">
								<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
							</div>
							<div class="col-2">
								<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
							</div>
						</div>
					</div><!-- .visible-mobile-layout -->
				</div>
				<div data-option="text-setting" class="item-option">
					<div class="row form-group">
						<div class="col-4-r">
							<h5 class="title-field"><?php esc_html_e( 'Font family', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-customize-font" data-link-font-weight="textSettings-fontWeight">
									<span class="wr-image-selected none"></span>
									<div class="wr-select-image-container">
										<div class="search-font"><input type="text" class="txt-sfont" /></div>
										<ul class="wr-list-font">
											<?php foreach ( $wr_google_fonts as $font => $weight ) { ?>
												<li class="<?php echo esc_attr( strtolower( preg_replace( '/\s+/is', '-', $font ) ) ); ?>" data-value="<?php echo esc_attr( $font ); ?>" data-weigth="<?php echo implode( ',' , $weight ); ?>"><?php echo esc_attr( $font ); ?></li>
											<?php } ?>
										</ul>
									</div>
									<select class="slt hidden"  data-bind="textSettings.fontFamily" >
										<?php foreach ( $wr_google_fonts as $font => $weight ) { ?>
											<option value="<?php echo esc_attr( $font ); ?>"><?php echo esc_attr( $font ); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
						<div class="col-4">
							<h5 class="title-field"><?php esc_html_e( 'Font weight', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt slt-font-weight" data-link-font-weight="textSettings-fontWeight" data-bind="textSettings.fontWeight" >
									<option value="100">100</option>
									<option value="100i">100i</option>
									<option value="200">200</option>
									<option value="200i">200i</option>
									<option value="300">300</option>
									<option value="300i">300i</option>
									<option value="400">400</option>
									<option value="400i">400i</option>
									<option value="500">500</option>
									<option value="500i">500i</option>
									<option value="600">600</option>
									<option value="600i">600i</option>
									<option value="700">700</option>
									<option value="700i">700i</option>
									<option value="800">800</option>
									<option value="800i">800i</option>
									<option value="900">900</option>
									<option value="900i">900i</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Text transform', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="textSettings.textTransform" >
									<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
									<option value="uppercase"><?php esc_html_e( 'Uppercase', 'wr-nitro' ); ?></option>
									<option value="lowercase"><?php esc_html_e( 'Lowercase', 'wr-nitro' ); ?></option>
									<option value="capitalize"><?php esc_html_e( 'Capitalize', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Font style', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="radio-icon">
									<span class="radio-icon-item wr-checkbox-btn">
										<i class="fa fa-underline"></i>
										<input type="checkbox" class="hidden" data-bind="textSettings.textDecorationIsUnderline">
									</span>
									<span class="radio-icon-item wr-checkbox-btn">
										<i class="fa fa-italic"></i>
										<input type="checkbox" class="hidden" data-bind="textSettings.fontStyleIsItalic">
									</span>
								</div>
							</div>
						</div>
						<div class="col-3 visible-horizontal-layout">
							<div data-bind="visible:layoutStyleIcon">
								<h5 class="title-field"><?php esc_html_e( 'Text align', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="radio-icon wr-radio-group">
										<span class="radio-icon-item wr-radio-btn" data="left"> <i class="fa fa-align-left"></i> </span>
										<span class="radio-icon-item wr-radio-btn" data="center"> <i class="fa fa-align-center"></i> </span>
										<span class="radio-icon-item wr-radio-btn" data="right"> <i class="fa fa-align-right"></i> </span>
										<input type="hidden" data-bind="textAlign">
									</div>
								</div>
							</div>
						</div>
						<div class="col-3 visible-vertical-layout">
							<h5 class="title-field"><?php esc_html_e( 'Text align', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="radio-icon wr-radio-group">
									<span class="radio-icon-item wr-radio-btn" data="left"> <i class="fa fa-align-left"></i> </span>
									<span class="radio-icon-item wr-radio-btn" data="center"> <i class="fa fa-align-center"></i> </span>
									<span class="radio-icon-item wr-radio-btn" data="right"> <i class="fa fa-align-right"></i> </span>
									<input type="hidden" data-bind="textAlign">
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Font size', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="textSettings.fontSize" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Line height', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="textSettings.lineHeight" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Letter spacing', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<input type="number" step="any" class="txt" data-bind="textSettings.letterSpacing" />
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Text color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="link.style.color" />
								<span class="font-color" data-bind="text:link.style.color"></span>
							</div>
						</div>
						<div class="col-3 visible-mobile-layout color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Hover text color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="link.style.colorHover" />
								<span class="font-color" data-bind="text:link.style.colorHover"></span>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Item spacing', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<input type="number" step="any" class="txt" data-bind="itemSpacing" />
							</div>
						</div>
					</div>
				</div>
				<div data-option="submenu" class="item-option">
					<div class="visible-desktop-layout">
						<h4 class="title-form"><?php esc_html_e( 'Animation', 'wr-nitro' ); ?></h4>
						<div class="row form-group visible-horizontal-layout">
							<div class="col-3" data-bind="visible:layoutStyleText">
								<h5 class="title-field"><?php esc_html_e( 'Submenu animation', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="subMenu.animation" >
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="fade"><?php esc_html_e( 'Fade', 'wr-nitro' ); ?></option>
										<option value="left-to-right"><?php esc_html_e( 'Left to right', 'wr-nitro' ); ?></option>
										<option value="right-to-left"><?php esc_html_e( 'Right to left', 'wr-nitro' ); ?></option>
										<option value="bottom-to-top"><?php esc_html_e( 'Bottom to top', 'wr-nitro' ); ?></option>
										<option value="scale"><?php esc_html_e( 'Scale', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="col-3" data-bind="visible:layoutStyleText">
								<h5 class="title-field"><?php esc_html_e( 'Margin top', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<input type="number" step="any" class="txt" data-bind="subMenu.maginTop" />
								</div>
							</div>
							<div class="col-3" data-bind="visible:layoutStyleIcon">
								<h5 class="title-field"><?php esc_html_e( 'Submenu animation', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div data-bind="visible:menuStyleFullscreen">
										<select class="slt" data-bind="subMenu.animationVertical" >
											<option value="slide"><?php esc_html_e( 'Slide', 'wr-nitro' ); ?></option>
											<option value="accordion"><?php esc_html_e( 'Accordion', 'wr-nitro' ); ?></option>
										</select>
									</div>
									<div data-bind="visible:menuStyleSidebar">
										<select class="slt el-show-width-submmenu" data-bind="subMenu.animationVertical" >
											<option value="normal"><?php esc_html_e( 'Normal', 'wr-nitro' ); ?></option>
											<option value="slide"><?php esc_html_e( 'Slide', 'wr-nitro' ); ?></option>
											<option value="accordion"><?php esc_html_e( 'Accordion', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-3" data-bind="visible:layoutStyleIcon">
								<div data-bind="visible:submenuNormalVertical">
									<h5 class="title-field"><?php esc_html_e( 'Submenu effect', 'wr-nitro' ); ?></h5>
									<div class="content-group">
										<select class="slt" data-bind="subMenu.effectNormalVertical" >
											<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
											<option value="fade"><?php esc_html_e( 'Fade', 'wr-nitro' ); ?></option>
											<option value="left-to-right"><?php esc_html_e( 'Left to right', 'wr-nitro' ); ?></option>
											<option value="right-to-left"><?php esc_html_e( 'Right to left', 'wr-nitro' ); ?></option>
											<option value="bottom-to-top"><?php esc_html_e( 'Bottom to top', 'wr-nitro' ); ?></option>
											<option value="scale"><?php esc_html_e( 'Scale', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="row form-group visible-vertical-layout">
							<div class="col-3" data-bind="visible:layoutStyleIcon">
								<h5 class="title-field"><?php esc_html_e( 'Submenu animation', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div data-bind="visible:menuStyleFullscreen">
										<select class="slt" data-bind="subMenu.animationVertical" >
											<option value="slide"><?php esc_html_e( 'Slide', 'wr-nitro' ); ?></option>
											<option value="accordion"><?php esc_html_e( 'Accordion', 'wr-nitro' ); ?></option>
										</select>
									</div>
									<div data-bind="visible:menuStyleSidebar">
										<select class="slt el-show-width-submmenu" data-bind="subMenu.animationVertical" >
											<option value="normal"><?php esc_html_e( 'Normal', 'wr-nitro' ); ?></option>
											<option value="slide"><?php esc_html_e( 'Slide', 'wr-nitro' ); ?></option>
											<option value="accordion"><?php esc_html_e( 'Accordion', 'wr-nitro' ); ?></option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-3" data-bind="visible:layoutStyleText">
								<h5 class="title-field"><?php esc_html_e( 'Submenu animation', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt el-show-width-submmenu" data-bind="subMenu.animationVertical" >
										<option value="normal"><?php esc_html_e( 'Normal', 'wr-nitro' ); ?></option>
										<option value="slide"><?php esc_html_e( 'Slide', 'wr-nitro' ); ?></option>
										<option value="accordion"><?php esc_html_e( 'Accordion', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="col-3" data-bind="visible:submenuNormalVertical">
								<h5 class="title-field"><?php esc_html_e( 'Submenu effect', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="subMenu.effectNormalVertical" >
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="fade"><?php esc_html_e( 'Fade', 'wr-nitro' ); ?></option>
										<option value="left-to-right"><?php esc_html_e( 'Left to right', 'wr-nitro' ); ?></option>
										<option value="right-to-left"><?php esc_html_e( 'Right to left', 'wr-nitro' ); ?></option>
										<option value="bottom-to-top"><?php esc_html_e( 'Bottom to top', 'wr-nitro' ); ?></option>
										<option value="scale"><?php esc_html_e( 'Scale', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
						</div>
						<h4 class="title-form"><?php esc_html_e( 'Style', 'wr-nitro' ); ?></h4>
					</div>
					<div class="row form-group">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Text transform', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="subMenu.style.textTransform" >
									<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
									<option value="uppercase"><?php esc_html_e( 'Uppercase', 'wr-nitro' ); ?></option>
									<option value="lowercase"><?php esc_html_e( 'Lowercase', 'wr-nitro' ); ?></option>
									<option value="capitalize"><?php esc_html_e( 'Capitalize', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Font weight', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt slt-font-weight" data-link-font-weight="textSettings-fontWeight" data-bind="subMenu.fontWeight" >
									<option value="100">100</option>
									<option value="100i">100i</option>
									<option value="200">200</option>
									<option value="200i">200i</option>
									<option value="300">300</option>
									<option value="300i">300i</option>
									<option value="400">400</option>
									<option value="400i">400i</option>
									<option value="500">500</option>
									<option value="500i">500i</option>
									<option value="600">600</option>
									<option value="600i">600i</option>
									<option value="700">700</option>
									<option value="700i">700i</option>
									<option value="800">800</option>
									<option value="800i">800i</option>
									<option value="900">900</option>
									<option value="900i">900i</option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Font style', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="radio-icon">
									<span class="radio-icon-item wr-checkbox-btn">
										<i class="fa fa-underline"></i>
										<input type="checkbox" class="hidden" data-bind="subMenu.style.textDecorationIsUnderline">
									</span>
									<span class="radio-icon-item wr-checkbox-btn">
										<i class="fa fa-italic"></i>
										<input type="checkbox" class="hidden" data-bind="subMenu.style.fontStyleIsItalic">
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3 show-width-submmenu">
							<h5 class="title-field"><?php esc_html_e( 'Width', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="subMenu.width" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Font size', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="subMenu.style.fontSize" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Line height', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="subMenu.style.lineHeight" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Letter spacing', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="subMenu.style.letterSpacing" /></div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Text color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="subMenu.link.style.color" />
								<span class="font-color" data-bind="text:subMenu.link.style.color"></span>
							</div>
						</div>
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Hover text color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="subMenu.link.style.colorHover" />
								<span class="font-color" data-bind="text:subMenu.link.style.colorHover"></span>
							</div>
						</div>
						<div class="col-3 show-width-submmenu color-theme">
							<div class="visible-desktop-layout">
								<h5 class="title-field"><?php esc_html_e( 'Background color', 'wr-nitro' ); ?></h5>
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="subMenu.background" />
									<span class="font-color" data-bind="text:subMenu.background"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div data-option="spacing" class="item-option">
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="spacing.marginTop" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin right" data-bind="spacing.marginRight" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="spacing.marginBottom" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin left" data-bind="spacing.marginLeft" placeholder="-"  />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="spacing.borderTopWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border right" data-bind="spacing.borderRightWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border bottom" data-bind="spacing.borderBottomWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border left" data-bind="spacing.borderLeftWidth" placeholder="-"  />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="spacing.paddingTop" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding right" data-bind="spacing.paddingRight" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="spacing.paddingBottom" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding left" data-bind="spacing.paddingLeft" placeholder="-"  />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="spacing.borderColor" />
										<span class="font-color" data-bind="text:spacing.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="spacing.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="spacing.borderRadius" /></div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3-r">
							<h5 class="title-field"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file wr-background-image" data-bind="spacing.backgroundImage" />
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="spacing.backgroundColor" />
								<span class="font-color" data-bind="text:spacing.backgroundColor"></span>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:spacing.backgroundImageNotEmpty">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG size', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="spacing.backgroundSize">
									<option value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
									<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
									<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="spacing.backgroundPosition">
									<option value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
									<option value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
									<option value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
									<option value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
									<option value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
									<option value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
									<option value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
									<option value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
									<option value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG repeat', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="spacing.backgroundRepeat">
									<option value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
									<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
									<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
									<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- ================================================================================================================================================================ -->
		<div class="hb-settings-box hb-sidebar-inspector">
			<h3 class="title-setting"><?php esc_html_e( 'Sidebar settings', 'wr-nitro' ); ?><span class="close-setting"></span></h3>
			<ul class="nav-settings">
				<li data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ); ?></li>
				<li data-nav="spacing"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></li>
			</ul>
			<div class="option-settings">
				<div data-option="general" class="item-option">
					<div class="row form-group">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Select a sidebar', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select data-bind="sidebarID" class="slt" data-bind="position">
									<option value=""><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
									<?php
										$wr_sidebars = WR_Nitro_Helper::get_sidebars();
										if( $wr_sidebars ) {
											foreach( $wr_sidebars as $key => $val ) {
												if( ! $val ) continue;
												echo '<option value="' . esc_attr( $key ) . '">' . $val .'</option>';
											}
										}
									?>
								</select>
							</div>
						</div>
					</div>
					<div class="row form-group visible-desktop-layout">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="position">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
									<option value="top"><?php esc_html_e( 'Top', 'wr-nitro' ); ?></option>
									<option value="bottom"><?php esc_html_e( 'Bottom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
					<div class="row form-group visible-desktop-layout">
						<div class="col-3">
							<h5 data-bind="visible:widthShow" class="title-field"><?php esc_html_e( 'Width', 'wr-nitro' ); ?></h5>
							<h5 data-bind="visible:heightShow" class="title-field"><?php esc_html_e( 'Height', 'wr-nitro' ); ?></h5>
							<div data-bind="visible:widthShow" class="content-group"><input type="number" step="any" class="txt" data-bind="frontCSS.style.width" /></div>
							<div data-bind="visible:heightShow" class="content-group"><input type="number" step="any" class="txt" data-bind="frontCSS.style.height" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"></h5>
							<div class="content-group">
								<div class="radio-list">
									<label class="radio-item">
										<input name="background-style-sidebar" type="radio" class="rdo" data-bind="unit" value="px" />
										<span>px</span>
									</label>
									<label class="radio-item">
										<input name="background-style-sidebar" type="radio" class="rdo" data-bind="unit" value="%" />
										<span>%</span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-4">
							<h5 class="title-field"><?php esc_html_e( 'Select icon', 'wr-nitro' ); ?></h5>
							<div class="content-group">

								<div class="select-icon">
									<div class="icon-selected"><i class="fa fa-th"></i></div>

									<div class="list-icon-wrap">
									    <input class="txt-sfont" type="search" class="search" placeholder="Search icon...">
									    <div class="list-icon">
									        <ul>
									        </ul>
									    </div>
									</div>
									<input type="hidden" class="hidden" data-bind="icon" />
								</div>
							</div>
						</div>
						<div class="col-4">
							<h5 class="title-field"><?php esc_html_e( 'Icon size', 'wr-nitro' ); ?></h5>
							<div class="content-group"><div class="content-group"><input type="number" step="any" class="txt" data-bind="iconSize" /></div></div>
						</div>
						<div class="col-4 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Icon color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="iconColor" />
									<span class="font-color" data-bind="text:iconColor"></span>
								</div>
							</div>
						</div>
						<div class="col-4 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Hover icon color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="hoverIconColor" />
									<span class="font-color" data-bind="text:hoverIconColor"></span>
								</div>
							</div>
						</div>
					</div>
					<h4 class="title-form"><?php esc_html_e( 'Background', 'wr-nitro' ); ?></h4>
					<div class="row form-group">
						<div class="col-3-r color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file wr-background-image" data-bind="frontCSS.style.backgroundImage"/>
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'BG color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="frontCSS.style.backgroundColor" />
									<span class="font-color" data-bind="text:frontCSS.style.backgroundColor"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:frontCSS.style.backgroundImageNotEmpty">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG size', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="frontCSS.style.backgroundSize">
									<option value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
									<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
									<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="frontCSS.style.backgroundPosition">
									<option value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
									<option value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
									<option value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
									<option value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
									<option value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
									<option value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
									<option value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
									<option value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
									<option value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG repeat', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="frontCSS.style.backgroundRepeat">
									<option value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
									<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
									<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
									<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
					<div class="content-group hide-desktop-vertical">
						<label class="chb">
							<input type="checkbox" data-bind="centerElement" />
							<span><?php esc_html_e( 'Enable center element', 'wr-nitro' ); ?></span>
						</label>
						<p class="des-option"><?php esc_html_e( 'If enabled then Center Element Setting of element More on the same row will be disabled', 'wr-nitro' ) ?></p>
					</div>
					<div class="row form-group visible-desktop-vertical">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Align self', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="alignVertical">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="center"><?php esc_html_e( 'Center', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
				</div>
				<div data-option="spacing" class="item-option">
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="frontCSS.spacing.marginTop" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin right" data-bind="frontCSS.spacing.marginRight" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="frontCSS.spacing.marginBottom" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin left" data-bind="frontCSS.spacing.marginLeft" placeholder="-"  />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="frontCSS.spacing.borderTopWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border right" data-bind="frontCSS.spacing.borderRightWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border bottom" data-bind="frontCSS.spacing.borderBottomWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border left" data-bind="frontCSS.spacing.borderLeftWidth" placeholder="-"  />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="frontCSS.spacing.paddingTop" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding right" data-bind="frontCSS.spacing.paddingRight" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="frontCSS.spacing.paddingBottom" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding left" data-bind="frontCSS.spacing.paddingLeft" placeholder="-"  />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="frontCSS.spacing.borderColor" />
										<span class="font-color" data-bind="text:frontCSS.spacing.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="frontCSS.spacing.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="frontCSS.spacing.borderRadius" /></div>
							</div>
						</div>
					</div>

					<div class="row form-group">
						<div class="col-3-r">
							<h5 class="title-field"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file wr-background-image" data-bind="frontCSS.spacing.backgroundImage"/>
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="frontCSS.spacing.backgroundColor" />
									<span class="font-color" data-bind="text:frontCSS.spacing.backgroundColor"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:frontCSS.spacing.backgroundImageNotEmpty">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG size', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="frontCSS.spacing.backgroundSize">
									<option value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
									<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
									<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="frontCSS.spacing.backgroundPosition">
									<option value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
									<option value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
									<option value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
									<option value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
									<option value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
									<option value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
									<option value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
									<option value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
									<option value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG repeat', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="frontCSS.spacing.backgroundRepeat">
									<option value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
									<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
									<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
									<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
		<!-- ================================================================================================================================================================ -->
		<div class="hb-settings-box hb-text-inspector">
			<h3 class="title-setting"><?php esc_html_e( 'Text settings', 'wr-nitro' ); ?><span class="close-setting"></span></h3>
			<ul class="nav-settings">
				<li data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ); ?></li>
				<li data-nav="spacing"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></li>
			</ul>
			<div class="option-settings">
				<div data-option="general" class="item-option">
					<div class="row form-group">
						<div class="col-1">
							<h5 class="title-field"><?php esc_html_e( 'Content', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="hb-editor" data-editor="text-content">
									<?php
										wp_editor( '', 'text-content',
											array(
												'default_editor' => true,
												'editor_class'   => 'hb-text-editor'
											)
										);
									?>
									<input type="hidden" class="hb-editor-hidden" data-bind="content" />
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="content-group">
							<label class="chb">
								<input type="checkbox" data-bind="centerElement" />
								<span><?php esc_html_e( 'Enable center element', 'wr-nitro' ); ?></span>
							</label>
							<p class="des-option"><?php esc_html_e( 'If enabled then Center Element Setting of element More on the same row will be disabled', 'wr-nitro' ); ?></p>
						</div>
					</div>
					<div class="row form-group visible-desktop-vertical">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Align self', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="alignVertical">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="center"><?php esc_html_e( 'Center', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
				</div>
				<div data-option="spacing" class="item-option">
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="style.marginTop" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin right" data-bind="style.marginRight" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="style.marginBottom" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin left" data-bind="style.marginLeft" placeholder="-"  />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="style.borderTopWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border right" data-bind="style.borderRightWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border bottom" data-bind="style.borderBottomWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border left" data-bind="style.borderLeftWidth" placeholder="-"  />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="style.paddingTop" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding right" data-bind="style.paddingRight" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="style.paddingBottom" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding left" data-bind="style.paddingLeft" placeholder="-"  />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="style.borderColor" />
										<span class="font-color" data-bind="text:style.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="style.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.borderRadius" /></div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3-r">
							<h5 class="title-field"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file wr-background-image" data-bind="style.backgroundImage" />
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="style.backgroundColor" />
								<span class="font-color" data-bind="text:style.backgroundColor"></span>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:style.backgroundImageNotEmpty">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG size', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundSize">
									<option value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
									<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
									<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundPosition">
									<option value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
									<option value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
									<option value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
									<option value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
									<option value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
									<option value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
									<option value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
									<option value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
									<option value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG repeat', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundRepeat">
									<option value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
									<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
									<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
									<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- ================================================================================================================================================================ -->
		<div class="hb-settings-box hb-logo-inspector">
			<h3 class="title-setting"><?php esc_html_e( 'Logo settings', 'wr-nitro' ); ?><span class="close-setting"></span></h3>
			<ul class="nav-settings">
				<li data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ); ?></li>
				<li data-nav="spacing"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></li>
			</ul>
			<div class="option-settings">
				<div data-option="general" class="item-option">
					<h5 class="title-field"><?php esc_html_e( 'Logo type', 'wr-nitro' ); ?></h5>
					<div class="row form-group">
						<div class="col-1">
							<div class="content-group">
								<div class="radio-list">
									<label class="radio-item">
										<input name="background-style-logo" type="radio" class="rdo" data-bind="logoType" value="text" />
										<span><?php esc_html_e( 'Text', 'wr-nitro' ); ?></span>
									</label>
									<label class="radio-item">
										<input name="background-style-logo" type="radio" class="rdo" data-bind="logoType" value="image"  />
										<span><?php esc_html_e( 'Image', 'wr-nitro' ); ?></span>
									</label>
								</div>
							</div>
						</div>
					</div>

					<!--		Logo text		-->
					<div class="row form-group" data-bind="visible:isLogoText">
						<div class="col-1">
							<h5 class="title-field"><?php esc_html_e( 'Logo content', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="content" /></div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:isLogoText">
						<div class="col-4-r">
							<h5 class="title-field"><?php esc_html_e( 'Font family', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-customize-font" data-link-font-weight="style-fontWeight">
									<span class="wr-image-selected none"></span>
									<div class="wr-select-image-container">
										<div class="search-font"><input type="text" class="txt-sfont" /></div>
										<ul class="wr-list-font">
											<?php foreach ( $wr_google_fonts as $font => $weight ) { ?>
												<li class="<?php echo esc_attr( strtolower( preg_replace( '/\s+/is', '-', $font ) ) ); ?>" data-value="<?php echo esc_attr( $font ); ?>" data-weigth="<?php echo implode( ',' , $weight ); ?>"><?php echo esc_attr( $font ); ?></li>
											<?php } ?>
										</ul>
									</div>
									<select class="slt hidden"  data-bind="style.fontFamily" >
										<?php foreach ( $wr_google_fonts as $font => $weight ) { ?>
											<option value="<?php echo esc_attr( $font ); ?>"><?php echo esc_attr( $font ); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
						<div class="col-4">
							<h5 class="title-field"><?php esc_html_e( 'Font weight', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt slt-font-weight" data-link-font-weight="style-fontWeight" data-bind="style.fontWeight" >
									<option value="100">100</option>
									<option value="100i">100i</option>
									<option value="200">200</option>
									<option value="200i">200i</option>
									<option value="300">300</option>
									<option value="300i">300i</option>
									<option value="400">400</option>
									<option value="400i">400i</option>
									<option value="500">500</option>
									<option value="500i">500i</option>
									<option value="600">600</option>
									<option value="600i">600i</option>
									<option value="700">700</option>
									<option value="700i">700i</option>
									<option value="800">800</option>
									<option value="800i">800i</option>
									<option value="900">900</option>
									<option value="900i">900i</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:isLogoText">
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="style.color" />
								<span class="font-color" data-bind="text:style.color"></span>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Font size', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.fontSize" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Font style', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="radio-icon">
									<span class="radio-icon-item wr-checkbox-btn">
										<i class="fa fa-underline"></i>
										<input type="checkbox" class="hidden" data-bind="style.textDecorationIsUnderline">
									</span>
									<span class="radio-icon-item wr-checkbox-btn">
										<i class="fa fa-italic"></i>
										<input type="checkbox" class="hidden" data-bind="style.fontStyleIsItalic">
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:isLogoText">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Line height', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.lineHeight" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Letter spacing', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.letterSpacing" /></div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:isLogoImage">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Regular logo', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file" data-bind="logoImage"/>
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Retina logo', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file" data-bind="logoImageRetina"/>
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="logo-sticky">
						<div class="row form-group" data-bind="visible:isLogoImage">
							<div class="col-2">
								<h5 class="title-field"><?php esc_html_e( 'Regular logo sticky', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="file-image">
										<input type="text" class="txt input-file" data-bind="logoImageSticky"/>
										<span class="select-image">...</span>
										<i class="remove-image fa fa-times"></i>
									</div>
								</div>
							</div>
							<div class="col-2">
								<h5 class="title-field"><?php esc_html_e( 'Retina logo sticky', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="file-image">
										<input type="text" class="txt input-file" data-bind="logoImageStickyRetina"/>
										<span class="select-image">...</span>
										<i class="remove-image fa fa-times"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:isLogoImage">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Width', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.maxWidth" /></div>
						</div>
						<div class="col-2 logo-sticky">
							<h5 class="title-field"><?php esc_html_e( 'Width sticky', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="maxWidthSticky" /></div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="content-group">
							<label class="chb">
								<input type="checkbox" data-bind="centerElement" />
								<span><?php esc_html_e( 'Enable center element', 'wr-nitro' ); ?></span>
							</label>
							<p class="des-option"><?php esc_html_e( 'If enabled then Center Element Setting of element More on the same row will be disabled', 'wr-nitro' ); ?></p>
						</div>
					</div>
					<div class="row form-group visible-desktop-vertical">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Align self', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="alignVertical">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="center"><?php esc_html_e( 'Center', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
				</div>
				<div data-option="spacing" class="item-option">
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="style.marginTop" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin right" data-bind="style.marginRight" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="style.marginBottom" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin left" data-bind="style.marginLeft" placeholder="-"  />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="style.borderTopWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border right" data-bind="style.borderRightWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border bottom" data-bind="style.borderBottomWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border left" data-bind="style.borderLeftWidth" placeholder="-"  />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="style.paddingTop" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding right" data-bind="style.paddingRight" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="style.paddingBottom" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding left" data-bind="style.paddingLeft" placeholder="-"  />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="style.borderColor" />
										<span class="font-color" data-bind="text:style.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="style.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.borderRadius" /></div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3-r">
							<h5 class="title-field"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file wr-background-image" data-bind="style.backgroundImage" />
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="style.backgroundColor" />
								<span class="font-color" data-bind="text:style.backgroundColor"></span>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:style.backgroundImageNotEmpty">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG size', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundSize">
									<option value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
									<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
									<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundPosition">
									<option value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
									<option value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
									<option value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
									<option value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
									<option value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
									<option value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
									<option value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
									<option value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
									<option value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG repeat', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundRepeat">
									<option value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
									<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
									<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
									<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ================================================================================================================================================================ -->
		<div class="hb-settings-box hb-social-inspector">
			<h3 class="title-setting"><?php esc_html_e( 'Social settings', 'wr-nitro' ); ?><span class="close-setting"></span></h3>
			<ul class="nav-settings">
				<li data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ); ?></li>
				<li data-nav="spacing"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></li>
			</ul>
			<div class="option-settings">
				<div data-option="general" class="item-option">
					<div class="row form-group">
						<div class="col-1">
							<h4 class="title-form"><?php esc_html_e( 'Select social to show', 'wr-nitro' ); ?></h4>
							<div class="content-group">
								<div class="list-chb">
									<div class="list-chb social">
										<?php
										$wr_channels    = array( 'facebook', 'twitter', 'instagram', 'linkedin', 'pinterest', 'dribbble', 'behance', 'flickr', 'google-plus', 'medium', 'skype',  'slack', 'tumblr', 'vimeo', 'yahoo', 'youtube', 'rss', 'vk' );
										$wr_list_social = array();

										foreach ( $wr_channels as $value ) {
											if ( isset( $wr_nitro_options[ $value ] ) && $wr_nitro_options[ $value ] ) {
												$wr_list_social[] = $value;

												echo '
												<label class="chb-item">
													<input type="checkbox" class="chb" data-bind="socialList.' . str_replace( '-', '_', esc_attr( $value ) ) . '">
													<span>' . esc_attr( $value ) . '</span>
												</label>';
											}
										}
										?>
									</div>
								</div>
								<h5 class="title-field"><i><?php printf( __( 'Please input your socials network in social tab. Change your social account <a%s> here </a>', 'wr-nitro' ), ' target="_blanh" href="' . $wr_link_admin . 'customize.php?autofocus[section]=social" ' ); ?></i></h5>
							</div>
						</div>
					</div>
					<h4 class="title-form"><?php esc_html_e( 'Style icon', 'wr-nitro' ); ?></h4>
					<div class="row form-group">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Select style', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="iconStyle">
									<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
									<option value="custom"><?php esc_html_e( 'Custom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Icon color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="iconColor" />
									<span class="font-color" data-bind="text:iconColor"></span>
								</div>
								<p class="des-option" style="white-space: nowrap; "><?php esc_html_e( 'If empty get multiple color.', 'wr-nitro' ); ?></p>
							</div>
						</div>
						<div class="col-3 color-theme" data-bind="visible:customStyle">
							<h5 class="title-field"><?php esc_html_e( 'Background color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="backgroundColor" />
									<span class="font-color" data-bind="text:backgroundColor"></span>
								</div>
								<p class="des-option" style="white-space: nowrap;"><?php esc_html_e( 'If empty get multiple color.', 'wr-nitro' ); ?></p>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:customStyle">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="borderStyle">
									<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
									<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
									<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
									<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
									<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
									<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
									<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
									<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
									<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Border width', 'wr-nitro' ); ?></h5>
							<div class="content-group"><div class="content-group"><input type="number" step="any" class="txt" data-bind="borderWidth" /></div></div>
						</div>
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="borderColor" />
									<span class="font-color" data-bind="text:borderColor"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Icon size', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="iconSize">
									<option value="small"><?php esc_html_e( 'Small', 'wr-nitro' ); ?></option>
									<option value="normal"><?php esc_html_e( 'Normal', 'wr-nitro' ); ?></option>
									<option value="big"><?php esc_html_e( 'Large', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Icon spacing', 'wr-nitro' ); ?></h5>
							<div class="content-group"><div class="content-group"><input type="number" step="any" class="txt" data-bind="iconSpacing" /></div></div>
						</div>
						<div class="col-3" data-bind="visible:customStyle">
							<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
							<div class="content-group"><div class="content-group"><input type="number" step="any" class="txt" data-bind="borderRadius" /></div></div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="content-group">
							<label class="chb">
								<input type="checkbox" data-bind="centerElement" />
								<span><?php esc_html_e( 'Enable center element', 'wr-nitro' ); ?></span>
							</label>
							<p class="des-option"><?php esc_html_e( 'If enabled then Center Element Setting of element More on the same row will be disabled', 'wr-nitro' ); ?></p>
						</div>
					</div>
					<div class="row form-group visible-desktop-vertical">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Align self', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="alignVertical">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="center"><?php esc_html_e( 'Center', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
				</div>
				<div data-option="spacing" class="item-option">
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="style.marginTop" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin right" data-bind="style.marginRight" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="style.marginBottom" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin left" data-bind="style.marginLeft" placeholder="-"  />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="style.borderTopWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border right" data-bind="style.borderRightWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border bottom" data-bind="style.borderBottomWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border left" data-bind="style.borderLeftWidth" placeholder="-"  />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="style.paddingTop" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding right" data-bind="style.paddingRight" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="style.paddingBottom" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding left" data-bind="style.paddingLeft" placeholder="-"  />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="style.borderColor" />
										<span class="font-color" data-bind="text:style.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="style.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.borderRadius" /></div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3-r">
							<h5 class="title-field"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file wr-background-image" data-bind="style.backgroundImage" />
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="style.backgroundColor" />
								<span class="font-color" data-bind="text:style.backgroundColor"></span>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:style.backgroundImageNotEmpty">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG size', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundSize">
									<option value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
									<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
									<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundPosition">
									<option value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
									<option value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
									<option value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
									<option value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
									<option value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
									<option value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
									<option value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
									<option value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
									<option value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG repeat', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundRepeat">
									<option value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
									<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
									<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
									<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ================================================================================================================================================================ -->
		<div class="hb-settings-box hb-shopping-cart-inspector">
			<h3 class="title-setting"><?php esc_html_e( 'Cart settings', 'wr-nitro' ); ?><span class="close-setting"></span></h3>
			<ul class="nav-settings">
				<li data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ); ?></li>
				<li data-nav="spacing"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></li>
			</ul>
			<div class="option-settings">
				<div data-option="general" class="item-option">
					<div class="visible-desktop-layout">
						<h5 class="title-field"><?php esc_html_e( 'Layout style', 'wr-nitro' ); ?></h5>
						<div class="row form-group">
							<div class="col-1">
								<div class="content-group">
									<div class="radio-list">
										<label class="radio-item">
											<input name="cart-layout-style" type="radio" class="rdo" data-bind="type" value="dropdown"  />
											<span><?php esc_html_e( 'Dropdown', 'wr-nitro' ); ?></span>
										</label>
										<label class="radio-item">
											<input name="cart-layout-style" type="radio" class="rdo" data-bind="type" value="sidebar"  />
											<span><?php esc_html_e( 'Sidebar', 'wr-nitro' ); ?></span>
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Theme', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="radio-list">
									<label class="radio-item">
										<input name="color-style" type="radio" class="rdo" data-bind="colorType" value="light"  />
										<span><?php esc_html_e( 'Light', 'wr-nitro' ); ?></span>
									</label>
									<label class="radio-item">
										<input name="color-style" type="radio" class="rdo" data-bind="colorType" value="dark"  />
										<span><?php esc_html_e( 'Dark', 'wr-nitro' ); ?></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Show cart info', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="showCartInfo">
									<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
									<option value="item_number"><?php esc_html_e( 'Quantity', 'wr-nitro' ); ?></option>
									<option value="total_price"><?php esc_html_e( 'Price', 'wr-nitro' ); ?></option>
									<option value="number_price"><?php esc_html_e( 'Quantity + Price', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>

					<div class="row form-group">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Label', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input data-bind="titleText" type="text" class="txt" /></div>
						</div>
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Label color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="colorTitle" />
									<span class="font-color" data-bind="text:colorTitle"></span>
								</div>
							</div>
						</div>
						<div class="col-3 color-theme" data-bind="visible:showColorPrice">
							<h5 class="title-field"><?php esc_html_e( 'Price color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="colorPrice" />
									<span class="font-color" data-bind="text:colorPrice"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-1">
							<h5 class="title-field"><?php esc_html_e( 'Select icon', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="radio-icon wr-radio-group">
									<span class="radio-icon-item wr-radio-btn" data="fa fa-shopping-cart">
										<i class="fa fa-shopping-cart"></i>
									</span>
									<span class="radio-icon-item wr-radio-btn" data="fa fa-cart-arrow-down">
										<i class="fa fa-cart-arrow-down"></i>
									</span>
									<span class="radio-icon-item wr-radio-btn" data="nitro-icon-cart-1">
										<i class="nitro-icon-cart-1"></i>
									</span>
									<span class="radio-icon-item wr-radio-btn" data="nitro-icon-cart-2">
										<i class="nitro-icon-cart-2"></i>
									</span>
									<span class="radio-icon-item wr-radio-btn" data="nitro-icon-cart-3">
										<i class="nitro-icon-cart-3"></i>
									</span>
									<span class="radio-icon-item wr-radio-btn" data="nitro-icon-cart-4">
										<i class="nitro-icon-cart-4"></i>
									</span>
									<span class="radio-icon-item wr-radio-btn" data="nitro-icon-cart-5">
										<i class="nitro-icon-cart-5"></i>
									</span>
									<span class="radio-icon-item wr-radio-btn" data="nitro-icon-cart-6">
										<i class="nitro-icon-cart-6"></i>
									</span>
									<span class="radio-icon-item wr-radio-btn" data="nitro-icon-cart-7">
										<i class="nitro-icon-cart-7"></i>
									</span>
									<span class="radio-icon-item wr-radio-btn" data="nitro-icon-cart-8">
										<i class="nitro-icon-cart-8"></i>
									</span>
									<span class="radio-icon-item wr-radio-btn" data="nitro-icon-cart-9">
										<i class="nitro-icon-cart-9"></i>
									</span>

									<input type="hidden" data-bind="iconName">
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Icon color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="styleIcon.color" />
								<span class="font-color" data-bind="text:styleIcon.color"></span>
							</div>
						</div>
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Hover icon color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="styleIcon.hoverColor" />
								<span class="font-color" data-bind="text:styleIcon.hoverColor"></span>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Icon size', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" step="any" class="txt" data-bind="styleIcon.fontSize" /></div>
						</div>
					</div>
					<div class="visible-desktop-layout">
						<div class="row form-group" data-bind="visible:typeDropdown">
							<div class="col-2">
								<h5 class="title-field"><?php esc_html_e( 'Animation', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="animationDropdown">
										<option value="dropdown-fade"><?php esc_html_e( 'Fade', 'wr-nitro' ); ?></option>
										<option value="dropdown-left-to-right"><?php esc_html_e( 'Left to right', 'wr-nitro' ); ?></option>
										<option value="dropdown-right-to-left"><?php esc_html_e( 'Right to left', 'wr-nitro' ); ?></option>
										<option value="dropdown-bottom-to-top"><?php esc_html_e( 'Bottom to top', 'wr-nitro' ); ?></option>
										<option value="dropdown-scale"><?php esc_html_e( 'Scale', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="col-2">
								<h5 class="title-field"><?php esc_html_e( 'Margin top', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="marginTop" /></div>
							</div>
						</div>
						<div class="row form-group" data-bind="visible:typeSidebar">
							<div class="col-3">
								<h5 class="title-field"><?php esc_html_e( 'Animation', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="animationSidebar">
										<option value="sidebar-slide-in-on-top"><?php esc_html_e( 'Slide in on top', 'wr-nitro' ); ?></option>
										<option value="sidebar-push"><?php esc_html_e( 'Push', 'wr-nitro' ); ?></option>
										<option value="sidebar-fall-down"><?php esc_html_e( 'Fall down', 'wr-nitro' ); ?></option>
										<option value="sidebar-fall-up"><?php esc_html_e( 'Fall up', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="col-3">
								<h5 class="title-field"><?php esc_html_e( 'Sidebar position', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="position">
										<option value="position-sidebar-right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
										<option value="position-sidebar-left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
										<option value="position-sidebar-top"><?php esc_html_e( 'Top', 'wr-nitro' ); ?></option>
										<option value="position-sidebar-bottom"><?php esc_html_e( 'Bottom', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="content-group">
							<label class="chb">
								<input type="checkbox" data-bind="centerElement" />
								<span><?php esc_html_e( 'Enable center element', 'wr-nitro' ); ?></span>
							</label>
							<p class="des-option"><?php esc_html_e( 'If enabled then Center Element Setting of element More on the same row will be disabled', 'wr-nitro' ); ?></p>
						</div>
					</div>
					<div class="row form-group visible-desktop-vertical">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Align self', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="alignVertical">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="center"><?php esc_html_e( 'Center', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
				</div>
				<div data-option="spacing" class="item-option">
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="style.marginTop" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin right" data-bind="style.marginRight" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="style.marginBottom" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin left" data-bind="style.marginLeft" placeholder="-"  />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="style.borderTopWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border right" data-bind="style.borderRightWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border bottom" data-bind="style.borderBottomWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border left" data-bind="style.borderLeftWidth" placeholder="-"  />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="style.paddingTop" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding right" data-bind="style.paddingRight" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="style.paddingBottom" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding left" data-bind="style.paddingLeft" placeholder="-"  />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="style.borderColor" />
										<span class="font-color" data-bind="text:style.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="style.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.borderRadius" /></div>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-3-r">
							<h5 class="title-field"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="file-image">
									<input type="text" class="txt input-file wr-background-image" data-bind="style.backgroundImage" />
									<span class="select-image">...</span>
									<i class="remove-image fa fa-times"></i>
								</div>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG color', 'wr-nitro' ); ?></h5>
							<div class="wr-hb-colors-control">
								<input type="text" class="txt wr-color" data-bind="style.backgroundColor" />
								<span class="font-color" data-bind="text:style.backgroundColor"></span>
							</div>
						</div>
					</div>
					<div class="row form-group" data-bind="visible:style.backgroundImageNotEmpty">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG size', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundSize">
									<option value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
									<option value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
									<option value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundPosition">
									<option value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
									<option value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
									<option value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
									<option value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
									<option value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
									<option value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
									<option value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
									<option value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
									<option value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'BG repeat', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="style.backgroundRepeat">
									<option value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
									<option value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
									<option value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
									<option value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ================================================================================================================================================================ -->
		<div class="hb-settings-box hb-wpml-inspector">
			<h3 class="title-setting"><?php esc_html_e( 'WPML settings', 'wr-nitro' ); ?><span class="close-setting"></span></h3>
			<ul class="nav-settings">
				<li data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ); ?></li>
				<li data-nav="spacing"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></li>
			</ul>
			<div class="option-settings">
				<div data-option="general" class="item-option">
					<div class="row form-group hide-desktop-vertical">
						<div class="content-group">
							<label class="chb">
								<input type="checkbox" data-bind="centerElement" />
								<span><?php esc_html_e( 'Enable center element', 'wr-nitro' ); ?></span>
							</label>
							<p class="des-option"><?php esc_html_e( 'If enabled then Center Element Setting of element More on the same row will be disabled', 'wr-nitro' ); ?></p>
						</div>
					</div>
					<div class="row form-group visible-desktop-vertical">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Align self', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="alignVertical">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="center"><?php esc_html_e( 'Center', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
				</div>
				<div data-option="spacing" class="item-option">
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="style.marginTop" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin right" data-bind="style.marginRight" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="style.marginBottom" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin left" data-bind="style.marginLeft" placeholder="-"  />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="style.borderTopWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border right" data-bind="style.borderRightWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border bottom" data-bind="style.borderBottomWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border left" data-bind="style.borderLeftWidth" placeholder="-"  />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="style.paddingTop" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding right" data-bind="style.paddingRight" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="style.paddingBottom" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding left" data-bind="style.paddingLeft" placeholder="-"  />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="style.borderColor" />
										<span class="font-color" data-bind="text:style.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="style.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.borderRadius" /></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ================================================================================================================================================================ -->
		<div class="hb-settings-box hb-wishlist-inspector">
			<h3 class="title-setting"><?php esc_html_e( 'Wishlist settings', 'wr-nitro' ); ?><span class="close-setting"></span></h3>
			<ul class="nav-settings">
				<li data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ); ?></li>
				<li data-nav="spacing"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></li>
			</ul>
			<div class="option-settings">
				<div data-option="general" class="item-option">
					<h4 class="title-form"><?php esc_html_e( 'Icon', 'wr-nitro' ); ?></h4>
					<div class="row form-group">
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Icon color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="colorIcon" />
									<span class="font-color" data-bind="text:colorIcon"></span>
								</div>
							</div>
						</div>
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Hover icon color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="hoverIconColor" />
									<span class="font-color" data-bind="text:hoverIconColor"></span>
								</div>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Icon size', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" class="txt" data-bind="iconSize" /></div>
						</div>
					</div>
					<h4 class="title-form"><?php esc_html_e( 'Label', 'wr-nitro' ); ?></h4>
					<div class="row form-group">
						<div class="col-4">
							<h5 class="title-field"><?php esc_html_e( 'Label text', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="textLabel" /></div>
						</div>
						<div class="col-4">
							<h5 class="title-field"><?php esc_html_e( 'Label position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="labelPosition">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-4 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Label color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="colorLabel" />
									<span class="font-color" data-bind="text:colorLabel"></span>
								</div>
							</div>
						</div>
						<div class="col-4">
							<h5 class="title-field"><?php esc_html_e( 'Label size', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="number" class="txt" data-bind="labelSize" /></div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="content-group">
							<label class="chb">
								<input type="checkbox" data-bind="centerElement" />
								<span><?php esc_html_e( 'Enable center element', 'wr-nitro' ); ?></span>
							</label>
							<p class="des-option"><?php esc_html_e( 'If enabled then Center Element Setting of element More on the same row will be disabled', 'wr-nitro' ); ?></p>
						</div>
					</div>
					<div class="row form-group visible-desktop-vertical">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Align self', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="alignVertical">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="center"><?php esc_html_e( 'Center', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
				</div>
				<div data-option="spacing" class="item-option">
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="style.marginTop" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin right" data-bind="style.marginRight" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="style.marginBottom" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin left" data-bind="style.marginLeft" placeholder="-"  />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="style.borderTopWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border right" data-bind="style.borderRightWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border bottom" data-bind="style.borderBottomWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border left" data-bind="style.borderLeftWidth" placeholder="-"  />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="style.paddingTop" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding right" data-bind="style.paddingRight" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="style.paddingBottom" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding left" data-bind="style.paddingLeft" placeholder="-"  />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="style.borderColor" />
										<span class="font-color" data-bind="text:style.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="style.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.borderRadius" /></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ================================================================================================================================================================ -->
		<div class="hb-settings-box hb-currency-inspector">
			<h3 class="title-setting"><?php esc_html_e( 'Currency settings', 'wr-nitro' ); ?><span class="close-setting"></span></h3>
			<ul class="nav-settings">
				<li data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ); ?></li>
				<li data-nav="spacing"><?php esc_html_e( 'Spacing', 'wr-nitro' ); ?></li>
			</ul>
			<div class="option-settings">
				<div data-option="general" class="item-option">

					<!--
					<?php
						if( $wr_is_woocommerce_activated && $wr_is_currency_activated ) {
							$data_currency = WR_Currency_Hook::get_currency();
							if( $data_currency ) {
					?>
								<div class="row form-group">
									<div class="col-3 color-theme">
										<h5 class="title-field"><?php esc_html_e( 'Currency default', 'wr-nitro' ); ?></h5>
										<div class="content-group">
											<select class="slt" data-bind="alignVertical">
												<option value="0"><?php esc_html_e( 'Select currency', 'wr-nitro' ); ?></option>
					<?php
												foreach( $data_currency as $key => $val ) {
													echo '<option value="' . $val['id'] . '">' . $val['name'] . '</option>';
												}
					?>
											</select>
										</div>
									</div>
								</div>
					<?php
							}
						}
					?>
					-->
					<div class="form-group">
						<?php printf( __( 'Change your currency <a%s> here </a>', 'wr-nitro' ), ' target="_blanh" href="' . $wr_link_admin . 'admin.php?page=wc-settings&tab=wr-currency" ' ); ?>
					</div>

					<div class="row form-group">
						<div class="col-3 color-theme">
							<h5 class="title-field"><?php esc_html_e( 'Text color', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<div class="wr-hb-colors-control">
									<input type="text" class="txt wr-color" data-bind="style.color" />
									<span class="font-color" data-bind="text:style.color"></span>
								</div>
							</div>
						</div>
					</div>

					<div class="row form-group visible-horizontal-desktop">
						<div class="content-group">
							<label class="chb">
								<input type="checkbox" data-bind="show_flag" />
								<span><?php esc_html_e( 'Show flag', 'wr-nitro' ); ?></span>
							</label>
						</div>
					</div>

<!-- 					<h4 class="title-form"><?php esc_html_e( 'Label', 'wr-nitro' ); ?></h4>

					<div class="row form-group">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Label text', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="textLabel" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Label position', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="labelPosition">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
					</div> -->

					<div class="row form-group hide-desktop-vertical">
						<div class="content-group">
							<label class="chb">
								<input type="checkbox" data-bind="centerElement" />
								<span><?php esc_html_e( 'Enable center element', 'wr-nitro' ); ?></span>
							</label>
							<p class="des-option"><?php esc_html_e( 'If enabled then Center Element Setting of element More on the same row will be disabled', 'wr-nitro' ); ?></p>
						</div>
					</div>
					<div class="row form-group visible-desktop-vertical">
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Align self', 'wr-nitro' ); ?></h5>
							<div class="content-group">
								<select class="slt" data-bind="alignVertical">
									<option value="left"><?php esc_html_e( 'Left', 'wr-nitro' ); ?></option>
									<option value="center"><?php esc_html_e( 'Center', 'wr-nitro' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'wr-nitro' ); ?></option>
								</select>
							</div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-3">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
					<div class="row form-group hide-desktop-vertical">
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'Class', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="className" /></div>
						</div>
						<div class="col-2">
							<h5 class="title-field"><?php esc_html_e( 'ID', 'wr-nitro' ); ?></h5>
							<div class="content-group"><input type="text" class="txt" data-bind="ID" /></div>
						</div>
					</div>
				</div>
				<div data-option="spacing" class="item-option">
					<div class="row form-group">
						<div class="col-c-7">
							<div class="content-group">
								<div class="wr-spacing">
									<div class="margin-spacing">
										<span class="title-margin"><?php esc_html_e( 'margin', 'wr-nitro' ); ?></span>
										<input type="number" step="any" class="txt input-margin top" data-bind="style.marginTop" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin right" data-bind="style.marginRight" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin bottom" data-bind="style.marginBottom" placeholder="-"  />
										<input type="number" step="any" class="txt input-margin left" data-bind="style.marginLeft" placeholder="-"  />
										<div class="border-spacing">
											<span class="title-border"><?php esc_html_e( 'border', 'wr-nitro' ); ?></span>
											<input type="number" step="any" class="txt input-border top" data-bind="style.borderTopWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border right" data-bind="style.borderRightWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border bottom" data-bind="style.borderBottomWidth" placeholder="-"  />
											<input type="number" step="any" class="txt input-border left" data-bind="style.borderLeftWidth" placeholder="-"  />
											<div class="padding-spacing">
												<span class="title-padding"><?php esc_html_e( 'padding', 'wr-nitro' ); ?></span>
												<input type="number" step="any" class="txt input-padding top" data-bind="style.paddingTop" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding right" data-bind="style.paddingRight" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding bottom" data-bind="style.paddingBottom" placeholder="-"  />
												<input type="number" step="any" class="txt input-padding left" data-bind="style.paddingLeft" placeholder="-"  />
												<div class="content-spacing"><span class="dashicons dashicons-screenoptions"></span></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-c-3">
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border color', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<div class="wr-hb-colors-control">
										<input type="text" class="txt wr-color" data-bind="style.borderColor" />
										<span class="font-color" data-bind="text:style.borderColor"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border style', 'wr-nitro' ); ?></h5>
								<div class="content-group">
									<select class="slt" data-bind="style.borderStyle">
										<option value="none"><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
										<option value="solid"><?php esc_html_e( 'Solid', 'wr-nitro' ); ?></option>
										<option value="dashed"><?php esc_html_e( 'Dashed', 'wr-nitro' ); ?></option>
										<option value="dotted"><?php esc_html_e( 'Dotted', 'wr-nitro' ); ?></option>
										<option value="double"><?php esc_html_e( 'Double', 'wr-nitro' ); ?></option>
										<option value="groove"><?php esc_html_e( 'Groove', 'wr-nitro' ); ?></option>
										<option value="inset"><?php esc_html_e( 'Inset', 'wr-nitro' ); ?></option>
										<option value="outset"><?php esc_html_e( 'Outset', 'wr-nitro' ); ?></option>
										<option value="ridge"><?php esc_html_e( 'Ridge', 'wr-nitro' ); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<h5 class="title-field"><?php esc_html_e( 'Border radius', 'wr-nitro' ); ?></h5>
								<div class="content-group"><input type="number" step="any" class="txt" data-bind="style.borderRadius" /></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<textarea id="data-header" name="content" class="hidden"><?php echo ( isset( $wr_nitro_header_data['post_content'] ) && $wr_nitro_header_data['post_content'] ) ? $wr_nitro_header_data['post_content'] : '"{}"'; ?></textarea>

</div>

<?php
	$wr_list_model = array();
	if( $wr_hide_show_fixed ) {
		foreach( $wr_hide_show_fixed as $key => $val ) {
			if( $val[ 'data' ] ) {
				foreach( $val[ 'data' ] as $key_item => $val_item ) {
					$wr_list_model[ $key ][ $key_item ] = false;
				}
			}
		}
	}
	$wr_list_model = json_encode( $wr_list_model );
?>

<?php echo '<scr' . 'ipt>'; ?>
	jQuery(function($) {
		$(window).load(function () {

			window.wr_fontawesome = [];

			$.getJSON( wr_site_data.theme_url + '/assets/woorockets/fonts-json/fontawesome.json', function( response ) {
				if( response ) {
					window.wr_fontawesome = response;
				}
			});


			window.active_wrls = <?php echo esc_js( $wr_is_live_search_activated ? '1' : '0' ); ?>;

			$( '.wr-save-header' ).prop( 'disabled', true );

			<?php
			    $headerData = '';
	    		if( isset( $wr_nitro_header_data['post_content'] ) && $wr_nitro_header_data['post_content'] ) {
    				if( is_serialized( $wr_nitro_header_data['post_content'] ) ) {
    					$headerData = json_encode( unserialize( $wr_nitro_header_data['post_content'] ) );
    				} else {
    					$headerData = $wr_nitro_header_data['post_content'];
    				}
    			} else {
    				$headerData = '"{}"';
    			}
                $headerData = str_replace('rel="noopener noreferrer"','',$headerData);
                $headerData = str_replace('rel="\&quot;noopener\&quot; noopener noreferrer"','',$headerData);
    		    echo 'window.headerData = ' . $headerData;
			?>

			window.hb = new WRNitro_HeaderBuilder_View({
				el: '#hb-app',
				model: new WRNitro_HeaderBuilder_AppModel({
					desktop : { settings : { fixedList : <?php echo wp_kses_post( $wr_list_model ); ?> } },
					mobile  : { settings : { fixedList : <?php echo wp_kses_post( $wr_list_model ); ?> } }
				},{})
			});

			window.list_social = <?php echo json_encode( $wr_list_social ); ?>

			hb.model.set( headerData );
		})
	});
<?php echo '</scr' . 'ipt>'; ?>

<?php echo '<scr' . 'ipt type="text/html" id="hb-list-template">'; ?>
	<div id="hb-modal">
		<div class="modal-content-outer md-<?php echo esc_attr( $wr_hb_layout ); ?>">
			<div class="modal-content">
				<div class="modal-title"><span class="text"><?php esc_html_e( 'List template header', 'wr-nitro' ) ?></span><i class="close dashicons dashicons-no-alt"></i></div>
				<div class="modal-content-inner">
					<div class="list">
						<?php
							$wr_install = ( $wr_hb_layout == 'horizontal' ) ? esc_html__( 'Install This Header', 'wr-nitro' ) : esc_html__( 'Install', 'wr-nitro' );
							$wr_title = ( $wr_hb_layout == 'horizontal' ) ? esc_html__( 'Horizontal', 'wr-nitro' ) : esc_html__( 'Vertical', 'wr-nitro' );
							$wr_image = ( $wr_hb_layout == 'horizontal' ) ? 'hoz' : 'ver';
							$wr_number = ( $wr_hb_layout == 'horizontal' ) ? 13 : 4;

							for ( $i = 1; $i <= $wr_number; $i++) {
						?>
								<div class="item">
									<h3 class="name-header"><?php echo esc_attr( $wr_title ); ?> - <?php echo absint($i); ?> <?php if ( $wr_hb_layout == 'horizontal' && ( $i == 3 || $i == 6 ) ) echo ' (' . __( 'Background transparent', 'wr-nitro' ) . ')' ?></h3>
									<div class="img">
										<div class="install"><div class="install-inner" data-id="<?php echo absint($i); ?>"><i class="fa fa-cogs"></i> <span><?php echo esc_attr( $wr_install ); ?></span> </div></div>
										<img src="http://cdn.woorockets.com/files/header-templates/<?php echo esc_attr( $wr_image ) . '-' . absint($i); ?>.jpg" />
									</div>
								</div>
						<?php
							};
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-overlay"></div>
	</div>
<?php echo '</scr' . 'ipt>'; ?>
