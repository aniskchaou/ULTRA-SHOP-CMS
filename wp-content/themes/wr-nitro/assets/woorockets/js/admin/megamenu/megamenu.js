/**
 * @version    1.0
 * @package    Nitro
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

(function ( $ ) {
	"use strict";

	$.WrThemeMegamenu                 = $.WrThemeMegamenu || {};
	$.WrThemeMegamenu['data_save']    = {};
	$.WrThemeMegamenu['allow_submit'] = false;

	// Add button show modal setting
	function button_show_modal( pending ){
		var class_pending = pending ? '.pending' : '';

		$( '#menu-to-edit > .menu-item' + class_pending ).each( function(){
			var _this = $(this);

			if( _this.find( '.item-title .wrmm-show-modal' ).length ) {
				return;
			}

			// Get id menu item
			var id = parseInt( _this.attr('id').split( 'menu-item-' )[1] );

			// Get level menu item
			var level = parseInt( _this.attr( 'class' ).split( 'menu-item-depth-' )[1].split( ' ' )[0] );

			// Render button
			_this.find( '.item-title' ).append( '<span class="wrmm-show-modal button-primary">Settings</span>' );

			// Creat data
			$.WrThemeMegamenu['data_save'][id] = data_menu_item( id, level );

			// Add active icon
			if( level == 0 && $.WrThemeMegamenu['data_save'][id]['active'] == 1 ) {
				_this.addClass( 'megamenu-active' );
			}
		});
	}

	function button_expand(){
		var list_menu_item = $( '#menu-to-edit > .menu-item' );
		var has_expand     = false;

		$.each( list_menu_item, function( key, val ){
			var _this      = $(this);
			var level      = parseInt( _this.attr( 'class' ).split( 'menu-item-depth-' )[1].split( ' ' )[0] );
			var el_next    = $( list_menu_item[ key + 1 ] );
			var level_next = level + 1;

			if( el_next.hasClass( 'menu-item-depth-' + level_next ) ) {
				if( ! _this.find( '.menu-item-bar .wr-expand' ).length ) {
					_this.find( '.menu-item-bar' ).append( '<span class="wr-expand"></span>' );
				}
			} else {
				_this.find( '.menu-item-bar .wr-expand' ).remove();
			}
		});
	}

	function add_active_megaemnu(){
		var list_menu_item = $( '#menu-to-edit > .menu-item-depth-0' );
		var has_expand     = false;

		$.each( list_menu_item, function( key, val ){
			var _this = $(this);
			var id    = parseInt( _this.attr('id').split( 'menu-item-' )[1] );

			$.WrThemeMegamenu['data_save'][id]

		});
	}

	// Add expand all and collapse all
	function button_expand_collapse_all(){

		var dom = '<ul class="expand-collapse"><li class="expand-all">Expand all</li><li class="collapse-all">Collapse all</li></ul>';
		$( dom ).insertBefore( '#menu-to-edit' );


		$( 'body' ).on( 'click', '.expand-collapse .expand-all', function(){
			$( '#menu-to-edit > .menu-item:not(".menu-item-depth-0")' ).show();
			$( '#menu-to-edit > .menu-item .wr-expand.collapse' ).removeClass( 'collapse' );
		} );

		$( 'body' ).on( 'click', '.expand-collapse .collapse-all', function(){
			$( '#menu-to-edit > .menu-item:not(".menu-item-depth-0")' ).hide();
			$( '#menu-to-edit > .menu-item .wr-expand' ).addClass( 'collapse' );
		} );

		/*===*===*===*===*===*===*===*===*===*     Expand     *===*===*===*===*===*===*===*===*===*/
		$( '#menu-to-edit' ).on( 'click', '.wr-expand', function() {
			var _this = $(this);
			var hide_flag  = true;

			if( _this.hasClass( 'collapse' ) ) {
				_this.removeClass( 'collapse' );
				hide_flag = false;
			} else {
				_this.addClass( 'collapse' );
			}
			
			var parent         = _this.closest( '.menu-item' );
			var level          = parseInt( parent.attr( 'class' ).split( 'menu-item-depth-' )[1].split(' ')[0] );
			var list_menu_item = $( '#menu-to-edit .menu-item' );
			var index_current  = list_menu_item.index( parent );

			$.each( list_menu_item, function( key, val ){
				if( key > index_current ) {
					var _this          = $(this);
					var level_children = parseInt( _this.attr( 'class' ).split( 'menu-item-depth-' )[1].split(' ')[0] );

					if( level_children <= level ) {
						return false;
					} else {
						if( hide_flag ) {
							_this.hide();
						} else {
							_this.show();
							
							_this.find( '.wr-expand.collapse' ).removeClass( 'collapse' );
						}
					}
				}

			} );
		});
	
		// Delete menu parent
		$( '#menu-to-edit' ).on( 'click', '.item-delete', function(){
			var _this          = $(this);
			var parent         = _this.closest( '.menu-item' );
			var level          = parseInt( parent.attr( 'class' ).split( 'menu-item-depth-' )[1].split(' ')[0] );
			var list_menu_item = $( '#menu-to-edit .menu-item' );
			var index_current  = list_menu_item.index( parent );

			$.each( list_menu_item, function( key, val ){
				if( key > index_current ) {
					var _this          = $(this);
					var level_children = parseInt( _this.attr( 'class' ).split( 'menu-item-depth-' )[1].split(' ')[0] );

					if( level_children <= level ) {
						return false;
					} else {
						_this.show();
					}
				}
			} );
		} );

	}

	// Add fontawesome to variable from file json
	function add_fontawesome(){
		$.getJSON( wr_theme_megamenu.theme_url + '/assets/woorockets/fonts-json/fontawesome.json', function( response ) {
			if( response ) {
				$.WrThemeMegamenu.data_icon.font_awesome = response;
			}
		});
	}

	var api;
	api = wpNavMenu;
	wpNavMenu.refreshAdvancedAccessibility = function() {

		// Hide all links by default
		$( '.menu-item-settings .field-move a' ).hide();

		// Mark all menu items as unprocessed
		$( 'a.item-edit' ).data( 'needs_accessibility_refresh', true );

		// All open items have to be refreshed or they will show no links
		$( '.menu-item-edit-active a.item-edit' ).each( function() {
			api.refreshAdvancedAccessibilityOfItem( this );
		} );


		// Add listen customs for megamenu
		button_expand();
	};

	function data_menu_item( id, level ){
		var level_data_default = ( level > 1 ) ? 2 : level;
		level_data_default++;

		var data = {};

		if( $.WrThemeMegamenu['data_save'][id] != undefined ) {
			data = $.extend( {}, wrmm_data_default[ 'lv_'+ level_data_default ], $.WrThemeMegamenu['data_save'][id] );
		} else if( wr_theme_data_megamenu[id] != undefined ){
			data = $.extend( {}, wrmm_data_default[ 'lv_'+ level_data_default ], wr_theme_data_megamenu[id] );
		} else {
			data = wrmm_data_default[ 'lv_'+ level_data_default ];
		}

		data['level'] = level;

		return data;
	}

	// Show modal setting
	function list_event(){

		/*===*===*===*===*===*===*===*===*===*     MODAL     *===*===*===*===*===*===*===*===*===*/
		// Show modal settings
		$( '#menu-to-edit' ).on( 'click', '.wrmm-show-modal', function(){
			var _this       = $(this);
			var item_parent = _this.closest( '.menu-item' );

			/* Action for data render */

			// Get id menu item
			var id = parseInt( item_parent.attr('id').split( 'menu-item-' )[1] );

			// Get level menu item
			var level = parseInt( item_parent.attr( 'class' ).split( 'menu-item-depth-' )[1].split( ' ' )[0] );

			$.WrThemeMegamenu['data_save'][id] = data_menu_item( id, level );

			var active_parent        = 0;
			var total_menu_item_lv_2 = 0;

			if( level == 0 ) {
				active_parent        = parseInt( $.WrThemeMegamenu['data_save'][id]['active'] );
				total_menu_item_lv_2 = count_menu_item_lv_2( item_parent );

				/* Refine row layout */
				var row_layout_filter = ( typeof $.WrThemeMegamenu['data_save'][id]['row_layout'] != 'undefined' && typeof $.WrThemeMegamenu['data_save'][id]['row_layout'] == 'string' ) ? $.WrThemeMegamenu['data_save'][id]['row_layout'] : '';
				row_layout_filter     = row_layout_filter.split( ' + ' );
				row_layout_filter     = remove_add_column( total_menu_item_lv_2, row_layout_filter ).join( ' + ' );
				$.WrThemeMegamenu['data_save'][id]['row_layout'] = filter_row( row_layout_filter ).join( ' + ' );
			} else if( level == 1 ) {
				var parent_lv_0 = item_parent.prevAll( '.menu-item-depth-0:first' );
				var parent_id   = parseInt( parent_lv_0.attr('id').split( 'menu-item-' )[1] );

				active_parent   = ( $.WrThemeMegamenu['data_save'][parent_id] != undefined ) ? $.WrThemeMegamenu['data_save'][parent_id]['active'] : 0;
			}

			var permission_parent = get_permission_parent( item_parent );

			/* Check level 0 has menu children */
			var has_children = false;
			if( level == 0 && item_parent.next( '.menu-item-depth-1' ).length != 0 ){
				has_children = true;
			}

			// Get html
			var template_show = _.template( $( "script#wrmm-template" ).html() )({
				data_item         : $.WrThemeMegamenu['data_save'][id],
				active_parent     : active_parent,
				permission_parent : permission_parent,
				title_modal       : item_parent.find( '.menu-item-title' ).text(),
				level             : level,
				id                : id, 
				sidebars_area     : wrmm_sidebars_area,
				"$"               : jQuery,
				item_lv_2         : total_menu_item_lv_2,
				list_row          : $.WrThemeMegamenu.list_row,
				list_icon         : $.WrThemeMegamenu.data_icon,
				has_children      : has_children
			});

			$( 'body' ).append( $( 'script#wrmm-modal-html' ).html() );
			$( '.wrmm-dialog' ).html( template_show );
			$( '.wrmm-modal' ).addClass( 'main-settings' );

			// Set color picker
			if( level == 0 ) {
				set_spectrum();
			}

			// Load content element for menu item level 2
			if( level == 1 ) {
				load_content_element( $.WrThemeMegamenu['data_save'][id]['element_type'], $.WrThemeMegamenu['data_save'][id]['element_data'] );
			}

			/* Action for modal */
			var modal         = $( '.wrmm-dialog' );
			var modal_info    = modal[0].getBoundingClientRect();
			var window_el     = $(window);
			var scroll_top    = window_el.scrollTop();
			var height_window = window_el.height();
			var top_position  = 0;

			if( modal_info.height < height_window ) {
				top_position = scroll_top + ( ( height_window - modal_info.height ) / 2 );
			} else {
				top_position = scroll_top + 10;
			}

			modal.css( 'top', top_position );

		})

		// Close modal main settings
		$('body').on('click', '.wrmm-modal.main-settings .dialog-title .close', function() {
			$(this).closest( '.wrmm-modal' ).remove();
		});

		// Close sub modal settings
		$('body').on('click', '.wrmm-modal.md-all-element .dialog-title .close, .wrmm-modal.md-select-icon .dialog-title .close', function() {
			$(this).closest( '.wrmm-modal' ).remove();

			$( '.wrmm-modal.main-settings.hidden' ).removeClass( 'hidden' );
		});

		// Switch tab in modal settings
		$('body').on('click', '.wrmm-wrapper .nav-settings li', function() {
			var _this    = $(this);
			var parent   = _this.closest( '.wrmm-wrapper' );
			var box_name = _this.attr( 'data-nav' );

			$( '.wrmm-dialog .nav-settings .active' ).removeClass( 'active' );
			$( '.wrmm-dialog .option-settings .item-option.active' ).removeClass( 'active' );

			_this.addClass( 'active' );
			$( '.wrmm-dialog .option-settings .item-option[data-option="' + box_name + '"]' ).addClass( 'active' );
		});

		// Show all element modal
		$('body').on('click', '.wrmm-wrapper .select-element .action .add-element', function() {
			var _this = $(this);

			// Hide main setting modal
			$( '.wrmm-modal.main-settings' ).addClass( 'hidden' );

			$( 'body' ).append( $( 'script#wrmm-modal-html' ).html() );

			$( '.wrmm-dialog:last' ).html( $( 'script#wrmm-all-element' ).html() );
			$( '.wrmm-modal:last' ).addClass( 'md-all-element' );

			/* Action for modal */

			var modal         = $( '.wrmm-dialog:last' );
			var modal_info    = modal[0].getBoundingClientRect();
			var window_el     = $(window);
			var scroll_top    = window_el.scrollTop();
			var height_window = window_el.height();
			var top_position  = 0;

			if( modal_info.height < height_window ) {
				top_position = scroll_top + ( ( height_window - modal_info.height ) / 2 );
			} else {
				top_position = scroll_top + 10;
			}

			modal.css( 'top', top_position );
		});

		/*===*===*===*===*===*===*===*===*===*     CHANGE DATA SETTINGS     *===*===*===*===*===*===*===*===*===*/
		// Show enable megamenu
		$('body').on('click', '.wrmm-wrapper .chb-enable', function() {
			var _this = $( this );

			//Get id current
			var parent_current 	= _this.closest( '.wrmm-wrapper' );

			var id_el = $( '#menu-item-' + parent_current.attr( 'data-id' ) ) ;
			
			var menu_item 		= $( '#menu-to-edit li.menu-item' );
			if($(this).is(":checked")) {
				var data_insert = '1';
				parent_current.find( '.wrapper-option' ).stop( true, false ).slideDown();

				// Add active
				id_el.addClass( 'megamenu-active' );  
			} else {
				var data_insert = '0';
				parent_current.find( '.wrapper-option' ).stop( true, false ).slideUp();

				// Remove active
				id_el.removeClass( 'megamenu-active' );  
			}

			//Load data in input
			update_option_data( _this, data_insert, 'active');
		});

		// Choose width type
		$('body').on('change', '.wrmm-wrapper .select-width', function() {
			//Load data in input
			var value = $(this).val();
			update_option_data( $( this ), value, 'width_type');

			if( value == 'fixed' ){
				$( this ).closest( '.width' ).find( '.number-width-box' ).removeAttr( 'style' );
			} else {
				$( this ).closest( '.width' ).find( '.number-width-box' ).hide();
			}
		});

		// Change width menu
		$('body').on('blur', '.wrmm-wrapper .number-width-box .number-width', function() {
			//Load data in input
			var data_insert = $( this ).val().replace(/[^0-9]/gi, '');
			update_option_data( $( this ), data_insert, 'width');
		});

		// Select media 
		$('body').on('click', '.wr-uploader-media .wr-image-button', function() {
			var _this     = $( this );
			var parent    = _this.closest( '.wrmm-wrapper' );
			var input_url = parent.find( '.wr-image-link' );
			var bg_option = parent.find( '.background-option' );

		    // Create a new media frame
		    if ( ! window.wr_media ) {
				window.wr_media = wp.media({
					frame    : 'post',
			        multiple : false,
			        button: {
				        text: 'Insert'
				    },
				});
			}

			window.wr_media.off( 'insert' ).on( 'insert', function( e ) {
				_this.closest( '.wrmm-modal' ).removeClass( 'hidden' );

				var attachmente = window.wr_media.state().get( 'selection' ).first().toJSON();
				var size        = $( '.attachment-display-settings .size' ).val();
				var url         = attachmente['sizes'][size]['url'];

				input_url.val( url );
				input_url.focus();
				bg_option.show();
			});

			window.wr_media.off( 'close' ).on( 'close', function( e ) {
				_this.closest( '.wrmm-modal' ).removeClass( 'hidden' );
			});

			// Finally, open the modal on click
			window.wr_media.open();

			// Hide modal
			_this.closest( '.wrmm-modal' ).addClass( 'hidden' );
		});

		// Remove url link image
		$('body').on('click', '.wr-uploader-media .wr-image-remove', function() {

			var _this     = $(this);
			var parent    = _this.closest( '.wrmm-wrapper' );
			var txt_image = parent.find('.wr-image-link');
			var bg_option = parent.find( '.background-option' );

			txt_image.val( '' );
			txt_image.focus();
			bg_option.hide();
		});

		// Choose image upload
		$('body').on('blur', '.wrmm-wrapper .background-image .wr-uploader-media .wr-image-link', function() {
			var _this       = $(this);
    		var data_insert = _this.val();
    		var parent      = _this.closest( '.wrmm-wrapper' );
			var bg_option   = parent.find( '.background-option' );

			update_option_data( _this, data_insert, 'background_image');

			if( data_insert == '' ) {
				bg_option.hide();
			} else {
				bg_option.show();
			}
		});

		$('body').on('change', '.wrmm-wrapper .background-size', function() {
			var _this = $(this);
			var data_insert = _this.val();
			update_option_data( _this, data_insert, 'background_size');
		});

		$('body').on('change', '.wrmm-wrapper .background-position', function() {
			var _this = $(this);
			var data_insert = _this.val();
			update_option_data( _this, data_insert, 'background_position');
		});

		$('body').on('change', '.wrmm-wrapper .background-repeat', function() {
			var _this = $(this);
			var data_insert = _this.val();
			update_option_data( _this, data_insert, 'background_repeat');
		});

		// Set color background
		$( 'body' ).on( 'change', '.wrmm-wrapper .txt-select-color', function( ) {
			var _this = $(this);
			var data_insert = _this.val();
			update_option_data( _this, data_insert, 'background_color');
		});

		// Show collum title
		$('body').on('click', '.wrmm-wrapper .checkbox-column', function() {
			if($(this).is(":checked")) {
				var data_insert = '1';
			} else {
				var data_insert = '0';
			}

			//Load data in input
			update_option_data( $( this ), data_insert, 'disable_title');
		});

		// Choose row layout 
		$('body').on('click', '.wrmm-wrapper .list-layout li', function() {
			var _this          = $(this);
			var _parent        = _this.closest( '.wrmm-wrapper' );
			var childrent      = _parent.find( '.list-layout li' );
			var txt_show_value = _parent.find( '.row-text .row-txt' );
			var value_select   = _this.attr( 'title' );

			update_option_data( $( this ), value_select, 'row_layout');
     
			childrent.removeClass( 'active' );
			_this.addClass( 'active' );

			txt_show_value.val( value_select );
			txt_show_value.focus();
		});

		$('body').on('keyup', '.wrmm-wrapper .row-text .row-txt', _.debounce( function(){
			var _this         = $(this);
			var parent        = _this.closest( '.wrmm-wrapper' );
			var list_layout   = parent.find( '.list-layout li' );
			var value         = _this.val();
			var result_array  = filter_row( value );
			var result_string = result_array.join( ' + ' );

			list_layout.removeClass( 'active' );
			parent.find( '.list-layout li[title="' + result_string + '"]' ).addClass( 'active' );
		} , 1000 ) );

		$('body').on( 'blur', '.wrmm-wrapper .row-text .row-txt', function(){
			var _this            = $(this);
			var value            = _this.val();
			var parent           = _this.closest( '.wrmm-wrapper' );
			var item_id          = parent.attr( 'data-id' );
			var result_array     = filter_row( value );
			var parent_menu_item = $( '#menu-item-' + item_id );
			var list_layout      = parent.find( '.list-layout li' );
			var count_item_lv_2  = count_menu_item_lv_2( parent_menu_item );
			var result_string    = remove_add_column( count_item_lv_2, result_array ).join( ' + ' );

			list_layout.removeClass( 'active' );
			parent.find( '.list-layout li[title="' + result_string + '"]' ).addClass( 'active' );

			_this.val( result_string );

			update_option_data( _this, result_string, 'row_layout');
		});

		// Permission show to
		$('body').on('click', '.wrmm-wrapper .permission .list-show .chb-of', function() {
			var _this = $(this);

			if( _this.is( ':checked' ) ) {
				var value = _this.val();

				update_option_data( _this, value, 'permission_show');

				if( value == 'log-in' ) {
					_this.closest( '.permission' ).find( '.type-member-row' ).show();
				} else {
					_this.closest( '.permission' ).find( '.type-member-row' ).hide();
				}
			}
		});

		// Permission user allow
		$('body').on('click', '.wrmm-wrapper .permission .type-member .chb-of', function() {
			var _this       = $(this);
			var parent      = _this.closest( '.type-member' );
			var list_user   = parent.find( '.chb-of' );
			var data_insert = [];

			if( list_user.length ) {

				var val = _this.val();

				if( val == 'all' ) {
					if( _this.is( ':checked' ) ) {
						list_user.prop( 'checked', true );
					} else {
						list_user.prop( 'checked', false );
					}
				} else {
					if( _this.is( ':checked' ) ) {
						var length_checked = parent.find( '.chb-of:checked' ).length;

						if( length_checked >= list_user.length - 1 ) {
							parent.find( '.chb-of[value="all"]' ).prop( 'checked', true );
						}
					} else {
						parent.find( '.chb-of[value="all"]' ).prop( 'checked', false );
					}
				}

				$.each( list_user, function( ) {
					if( $(this).is( ':checked' ) ) {
						data_insert.push( $(this).val() );
					}
				} );
			};

			update_option_data( _this, data_insert, 'permission_user');
		});

		// Search icon
		$('body').on('keyup', '.md-select-icon .wrmm-list-icon .search', function() {
			var _this     = $(this);
			var keyword   = _this.val().toLowerCase();
			var list_icon = _this.closest( '.wrmm-list-icon' ).find( '.list-icon ul li' );
			search_icon( keyword, list_icon );
		});

		// Choose icon
		$('body').on('click', '.md-select-icon .wrmm-list-icon li', function() {
			var _this       = $(this);
			var value       = _this.html();
			var parent      = $( '.wrmm-modal.main-settings .menu-icon' );
			var data_insert = _this.find( 'i' ).attr( 'class' );

			parent.find( '.delete-position.data-empty' ).removeClass( 'data-empty' );
			parent.find( '.delete-position .get-delete .get' ).html( value );

			update_option_data( $( '.wrmm-modal.main-settings .menu-icon' ), data_insert, 'icon');

			// Remove modal list icon
			$( '.wrmm-modal.md-select-icon' ).remove();

			// Show main setting modal
			$( '.wrmm-modal.main-settings.hidden' ).removeClass( 'hidden' );
		});

		// Remove icon
		$('body').on('click', '.wrmm-wrapper .menu-icon .get-delete .delete', function() {
			var _this  = $(this);

			var delete_confirm = confirm( 'Do you really want to delete this icon?' );

			if( ! delete_confirm ) return;

			var parent = _this.closest( '.menu-icon' );

			$( '.wrmm-wrapper .menu-icon .list-icon li.active' ).removeClass( 'active' );
			_this.closest( '.delete-position' ).addClass( 'data-empty' );

			update_option_data( _this, '', 'icon');
		});

		// Add or edit icon
		$('body').on('click', '.wrmm-wrapper .menu-icon .get-delete .add-icon, .wrmm-wrapper .menu-icon .get-delete .added-icon .get', function() {
			var _this = $(this);

			// Hide main setting modal
			$( '.wrmm-modal.main-settings' ).addClass( 'hidden' );

			$( 'body' ).append( $( 'script#wrmm-modal-html' ).html() );

			var data_active = '';
			if( ! _this.hasClass( 'add-icon' ) ) {
				var id = _this.closest( '.wrmm-wrapper' ).attr( 'data-id' );
				data_active = $.WrThemeMegamenu['data_save'][id]['icon'];
			}

			var template_list_icon = _.template( $( "script#wrmm-select-icon" ).html() )({
				list_icon   : $.WrThemeMegamenu.data_icon.font_awesome,
				icon_active : data_active
			});

			$( '.wrmm-dialog:last' ).html( template_list_icon );

			$( '.wrmm-modal:last' ).addClass( 'md-select-icon' );

			/* Action for modal */

			var modal         = $( '.wrmm-dialog:last' );
			var modal_info    = modal[0].getBoundingClientRect();
			var window_el     = $(window);
			var scroll_top    = window_el.scrollTop();
			var height_window = window_el.height();
			var top_position  = 0;

			if( modal_info.height < height_window ) {
				top_position = scroll_top + ( ( height_window - modal_info.height ) / 2 );
			} else {
				top_position = scroll_top + 10;
			}

			modal.css( 'top', top_position );
		});

		// Choose icon position
		$('body').on('click', '.wrmm-wrapper .menu-icon .delete-position .position li', function() {
			var _this = $(this);

			$( '.wrmm-wrapper .menu-icon .delete-position .position li.active' ).removeClass( 'active' );
			_this.addClass( 'active' );

			var data_insert = _this.attr( 'data-value' );
			update_option_data( _this, data_insert, 'icon_position');
		});

		// Choose element
		$('body').on('click', '.wrmm-list-element .item', function() {
			var _this         = $(this);
			var value         = 'element-' + _this.attr( 'data-value' );
			var main_setting  = $( '.wrmm-modal.main-settings' );
			var title_element = '';

			$( '.wrmm-modal.md-all-element' ).remove();

			// Show main setting modal
			$( '.wrmm-modal.main-settings.hidden' ).removeClass( 'hidden' );

			load_content_element( value, '' );

			switch( value ){
				case 'element-text':
					title_element = 'Text element';
					break;

				case 'element-products':
					title_element = 'Products element';
					break;

				case 'element-categories':
					title_element = 'Product categories element';
					break;

				case 'element-widget':
					title_element = 'Widget element';
					break;
			}

			$( '.wrmm-wrapper .select-element .action.not-element' ).removeClass( 'not-element' );
			$( '.wrmm-wrapper .select-element .action .added-element span' ).html( title_element );

			update_option_data( $( '.wrmm-modal.main-settings .select-element' ), value, 'element_type');
		});
	
		// Remove element
		$('body').on('click', '.wrmm-wrapper .select-element .action .added-element .delete', function() {
			var _this = $(this);

			var delete_confirm = confirm( 'Do you really want to delete this element?' );

			if( ! delete_confirm ) return;

			$( '.wrmm-wrapper .select-element .action' ).addClass( 'not-element' );

			$( '.wrmm-wrapper .select-element .content-element' ).html( '' );

			update_option_data( _this, '', 'element_type');
			update_option_data( _this, '', 'element_data');
		});

		// Event blur of editor text element
		$('body').on('blur', '.wrmm-wrapper .wrmm-text-element .wrmm-editor', function( e ) {
		 	var _this      = $( e.currentTarget );
            var content    = window.switchEditors._wp_Autop ( _this.val() );     // Changes double line-breaks in the text into HTML paragraphs (<p>...</p>).
            var input_hide = _this.closest( '.editor-wrapper' ).find( '.wrmm-editor-hidden' );

            input_hide.val( content ).trigger('change');
		});

		// Change value text element
		$('body').on('change', '.wrmm-wrapper .wrmm-text-element .wrmm-editor-hidden', function( e ) {
			var _this       = $(this);
			var data_insert = _this.val();

			update_option_data( _this, data_insert, 'element_data');
		});

		// Choose widget area
		$('body').on('change', '.wrmm-wrapper .wrmm-widget-element .wrmm-list-widget', function( e ) {
			var _this       = $(this);
			var data_insert = _this.val();

			update_option_data( _this, data_insert, 'element_data');
		});

		// Search product by ajax
		var timer_product, last_keyword_product = true;
		$('body').on('keyup', '.wrmm-wrapper .wrmm-products-element .search-product .search-ajax .product-ajax', function() {
			var _this         = $(this);
			var container 	  = _this.closest( '.search-ajax' );

			if ( timer_product ) {
				clearTimeout( timer_product );
			}

			timer_product = setTimeout( function() {

				// Get keyword.
				var keyword = _this.val();

				container.find( '.loading-search' ).remove();
				container.find( '.results-search' ).remove();

				if( last_keyword_product !== true && keyword == last_keyword_product && ! container.find( '.loading-search' ).length ) {
					return;
				}

				last_keyword_product = keyword;

				if ( keyword == '' || keyword.length < 3 ) {
					return;
				}

				// Show loading indicator.
				container.append( '<img class="loading-search" src="images/spinner.gif">' );

				// Custom for Nitro theme
				_this.closest( '.element-item' ).addClass( 'loading-wrls' );

				$.ajax( {
					type : "POST",
					url  : wr_theme_megamenu.ajaxurl,
					data : {
						action 	: 'wrmm_products',
						keyword : keyword,
						_nonce 	: wr_theme_megamenu._nonce,
					},
					success  : function( response ) {

						var response = ( response ) ? JSON.parse( response ) : '';

						container.find( '.loading-search' ).remove();
						container.find( '.results-search' ).remove();

						container.append( '<div class="results-search"></div>' );

						// Prepare response.
						if ( response.message ) {
							container.find( '.results-search' ).append( '<div class="wrmm-no-results">' + response.message + '</div>' );
						} else {
							container.find( '.results-search' ).append( '<div class="list-products"></div>' );

							// Show results.
							$.each( response.list_product, function( key, value ) {
								container.find( '.list-products' ).append( '<div class="item" data-id="' + value.id + '"><div class="img">' + value.image + '</div><div class="title-price"><div class="name-product">' + value.title + '</div><div class="price">' + value.price + '</div></div></div>' );
							} );
						}
					}
				} );
			}, 300 );
		});
		$('body').on('focus', '.wrmm-wrapper .wrmm-products-element .search-product .search-ajax .product-ajax', function() {
			var parent = $(this).closest('.search-ajax');

			parent.find('.loading-search').remove();
			parent.find('.results-search').show();
		});

		$('body').on('blur', '.wrmm-wrapper .wrmm-products-element .search-product .search-ajax .product-ajax', function() {
			var parent = $(this).closest('.search-ajax');

			parent.find('.loading-search').remove();
			parent.find('.results-search').hide();
		});
		
		// Choose product
		$('body').on('mousedown', '.wrmm-wrapper .search-ajax .results-search .list-products .item', function() {
			var _this           = $(this);
			var parent          = _this.closest( '.wrmm-wrapper' );
			var product_id      = _this.attr( 'data-id' );
			var item_id         = parent.attr( 'data-id' );
			var product_content = _this[ 0 ].outerHTML;

			// Move DOM to product added
			parent.find( '.wrmm-products-element .products-added .list-products' ).append( product_content );

			// Update data
			if( typeof $.WrThemeMegamenu.data_save[item_id]['element_data'] == 'undefined' ) {
				var data_insert = product_id;
			} else {
				var element_data = $.WrThemeMegamenu.data_save[item_id]['element_data'].split( ',' );
				element_data.push( product_id );
				var data_insert = element_data.join( ',' );
			}
			update_option_data( _this, data_insert, 'element_data');

			// Add buttom delete product
			parent.find( '.products-added .item:last' ).append( '<i class="del-product dashicons dashicons-no-alt"></i>' );

			parent.find( '.wrmm-products-element .search-ajax .results-search' ).hide();

			_this.remove();
		});

		// Delete product
		$('body').on( 'click', '.wrmm-wrapper .products-added .del-product', function() {
			var _this       = $(this);
			var wrapper     = _this.closest( '.wrmm-wrapper' );
			var parent      = _this.closest( '.item' );
			var product_id  = parent.attr( 'data-id' );
			var item_id     = wrapper.attr( 'data-id' );

			if( ( typeof $.WrThemeMegamenu.data_save[ item_id ] != 'undefined' ) && ( typeof $.WrThemeMegamenu.data_save[ item_id ]['element_data'] != 'undefined' ) ) {
				var data_insert = $.WrThemeMegamenu.data_save[ item_id ]['element_data'];
				data_insert = data_insert.split( ',' );
				
				// Unset product_id value in array
				data_insert = _.without( data_insert, product_id );
				
				data_insert = data_insert.join();

				update_option_data( _this, data_insert, 'element_data');
			}

			parent.remove();
		});

		/* Action for category element */
		$('body').on( 'focus', '.wrmm-wrapper .categories-ajax', function() {
			var _this = $(this);
			
			_this.trigger( 'keyup' );
		});

		$('body').on( 'keyup', '.wrmm-wrapper .categories-ajax', function() {
			var _this         = $(this);
	        var keyword       = _this.val();
	        var parent        = _this.parent();
	        var list_category = parent.find( '.item-categories' );

	        if( list_category.length ) {
	        	parent.find( '.list-categories' ).show();
	        }

	        if( keyword ) {
	            if( window.keyword_font_old == undefined || window.keyword_font_old != keyword ) {
	                list_category.hide();
	                list_category.each( function () {
	                	var _this_list = $(this);
	                    var textField = ( _this_list.attr( 'data-search' ) != undefined ) ? _this_list.attr( 'data-search' ).toLowerCase() : '' ;
	                    var keyword_lowercase = keyword.toLowerCase().trim();
	                    if( textField.indexOf( keyword_lowercase ) == -1 ) {
	                        _this_list.hide();
	                    } else {
	                        _this_list.show();
	                    }
	                } );

	                window.keyword_font_old = keyword; 
	            }
	        } else {
	            list_category.show();
	        }
		});

		$('body').on( 'blur', '.wrmm-wrapper .categories-ajax', function() {
			var _this = $(this);
			var parent = _this.parent();
			parent.find( '.list-categories' ).hide();
		});
		
		// Choose category
		$('body').on('mousedown', '.wrmm-wrapper .search-categories .item-categories', function() {
			var _this           = $(this);
			var parent          = _this.closest( '.wrmm-wrapper' );
			var category_id      = _this.attr( 'data-id' );
			var item_id         = parent.attr( 'data-id' );
			var category_content = _this[ 0 ].outerHTML;

			// Move DOM to product added
			parent.find( '.wrmm-category-element .category-added' ).append( category_content );

			// Update data
			if( typeof $.WrThemeMegamenu.data_save[item_id]['element_data'] == 'undefined' ) {
				var data_insert = category_id;
			} else {
				var element_data = $.WrThemeMegamenu.data_save[item_id]['element_data'].split( ',' );
				element_data.push( category_id );
				var data_insert = element_data.join( ',' );
			}
			update_option_data( _this, data_insert, 'element_data');

			// Add buttom delete product
			parent.find( '.category-added .item-categories:last' ).append( '<i class="del-category dashicons dashicons-no-alt"></i>' );

			parent.find( '.wrmm-category-element .search-ajax .list-categories' ).hide();
		});

		// Delete a category
		$('body').on( 'click', '.wrmm-category-element .category-added .del-category', function() {
			var _this       = $(this);
			var wrapper     = _this.closest( '.wrmm-wrapper' );
			var parent      = _this.closest( '.item-categories' );
			var category_id  = parent.attr( 'data-id' );
			var item_id     = wrapper.attr( 'data-id' );

			if( ( typeof $.WrThemeMegamenu.data_save[ item_id ] != 'undefined' ) && ( typeof $.WrThemeMegamenu.data_save[ item_id ]['element_data'] != 'undefined' ) ) {
				var data_insert = $.WrThemeMegamenu.data_save[ item_id ]['element_data'];
				data_insert = data_insert.split( ',' );
				
				// Unset category_id value in array
				data_insert = _.without( data_insert, category_id );
				
				data_insert = data_insert.join();

				update_option_data( _this, data_insert, 'element_data');
			}

			parent.remove();
		});

		/*===*===*===*===*===*===*===*===*===*     SAVE DATA     *===*===*===*===*===*===*===*===*===*/
		// Save data menu
		$( '.wp-admin #update-nav-menu' ).on( "submit", function( e ) {
			if( Object.keys( $.WrThemeMegamenu.data_save ).length ) {
				
				// Add loading
				if( !$( '.wrmm-loading' ).length ) {
					$( '.major-publishing-actions .publishing-action' ).prepend( '<img class="wrmm-loading" src="images/spinner.gif" />' );
				}
				
				// Remove error if has
				if( $( '.wrmm-error' ).length ) {
					$( '.wrmm-error' ).remove();
				}

				if( $.WrThemeMegamenu.allow_submit == false ) {
					e.preventDefault();
					
					// Save data
					save_ajax();
				}
			}
		});
	}

	function load_content_element( type, content ) {
		var html_render = '';

		switch( type ){
			case 'element-text':

				var wp_editor = $( 'script#wrmm-text-element' ).html();

				wp_editor = wp_editor.replace( '_WR_CONTENT_', content );

				$( '.wrmm-wrapper .select-element .content-element' ).html( wp_editor );

				var render_editor = function(){
					var intTimeout = 5000;
			        var intAmount  = 100;
			        var iframe_load_completed = true;

			        var ifLoadedInt = setInterval(function(){
			            if (iframe_load_completed || intAmount >= intTimeout) {

			                ( function() {
			                    var init, id, $wrap;

			                    // Render Visual Tab
			                    for ( id in tinyMCEPreInit.mceInit ) {
			                        if ( id != 'wrmm-editor' )
			                            continue;

			                        init  = tinyMCEPreInit.mceInit[id];
			                        $wrap = tinymce.$( '#wp-' + id + '-wrap' );

			                        tinymce.remove(tinymce.get('wrmm-editor'));
			                        tinymce.init( init );

			                        setTimeout( function(){
			                            $( '#wp-wrmm-editor-wrap' ).removeClass( 'html-active' );
			                            $( '#wp-wrmm-editor-wrap' ).addClass( 'tmce-active' );
			                        }, 10 );

			                        if ( ! window.wpActiveEditor )
			                                window.wpActiveEditor = id;

			                        break;
			                    }

			                    // Render Text tab
			                    for ( id in tinyMCEPreInit.qtInit ) {
			                        if ( id != 'wrmm-editor' )
			                            continue;

			                        quicktags( tinyMCEPreInit.qtInit[id] );

			                        // Re call inset quicktags button
			                        QTags._buttonsInit();

			                        if ( ! window.wpActiveEditor )
			                            window.wpActiveEditor = id;

			                        break;
			                    }
			                }());

			                iframe_load_completed = false;
			                window.clearInterval(ifLoadedInt);
			            }
			        },
			        intAmount
			        );
				};

				render_editor();

				break;
			case 'element-products':

				if( wr_theme_megamenu.active_wc != 1 ) { break; }

				if( content ) {
					var content_element = $( '.wrmm-wrapper .select-element .content-element' );

					content_element.html( '<img class="loading-search" src="images/spinner.gif" />' );

					$.ajax( {
						type : "POST",
						url  : wr_theme_megamenu.ajaxurl,
						data : {
							action 	: 'wrmm_get_products',
							list_id : content,
							_nonce 	: wr_theme_megamenu._nonce,
						},
						success  : function( response ) {
							var response = ( response ) ? JSON.parse( response ) : '';

							var template_show = _.template( $( "script#wrmm-products-element" ).html() )({
								"$"          : jQuery,
								list_product : response
							});
							content_element.html( template_show );
						}
					} );
				} else {
					var template_show = _.template( $( "script#wrmm-products-element" ).html() )({
						"$"          : jQuery,
						list_product : content
					});

					$( '.wrmm-wrapper .select-element .content-element' ).html( template_show );
				}

				break;
			case 'element-categories':

				if( wr_theme_megamenu.active_wc != 1 ) { break; }

				var template_show = _.template( $( "script#wrmm-category-element" ).html() )({
					"$"             : jQuery,
					list_categories : content,
					all_categories  : wrmm_category
				});

				$( '.wrmm-wrapper .select-element .content-element' ).html( template_show );

				break;
			case 'element-widget':

				var template_show = _.template( $( "script#wrmm-widget-element" ).html() )({
					"$"           : jQuery,
					sidebars_area : wrmm_sidebars_area,
					value         : content,
				});

				$( '.wrmm-wrapper .select-element .content-element' ).html( template_show );

				$( '.wrmm-wrapper .wrmm-widget-element .wrmm-list-widget' ).trigger( 'change' );

				break;
		}

		return html_render;
	}

	function search_icon( keyword, list_icon ) {
		if( keyword ) {
			list_icon.each( function(){
				var _this = $(this);
                var textField = $(this).attr("data-value").toLowerCase();
				if ( textField.indexOf( keyword ) == -1 ) {
                    _this.hide();
                } else {
                    _this.fadeIn(300);
                }
			});
		} else{
			list_icon.show();
		}
	};

	function save_ajax(){
		// Remove data null before udpate
		var data_save = {};
		
		$.each( $.WrThemeMegamenu.data_save, function( key, val ){
			data_save[key] = {};

			$.each( val, function( key_item, val_item ) {
				if( typeof val_item == 'string' )
					val_item = val_item.trim();

				if( val_item !== '' ) {
					switch( val['level'] ) {
					    case 0:
					    	if ( typeof wrmm_data_default['lv_1'][key_item] != 'undefined' && wrmm_data_default['lv_1'][key_item] != val_item )
					    		data_save[key][key_item] = val_item;

					        break;
					    case 1:
					    	if ( typeof wrmm_data_default['lv_2'][key_item] != 'undefined' && wrmm_data_default['lv_2'][key_item] != val_item )
					    		data_save[key][key_item] = val_item;

					        break;
					    default:
					    	if ( typeof wrmm_data_default['lv_3'][key_item] != 'undefined' && wrmm_data_default['lv_3'][key_item] != val_item )
					    		data_save[key][key_item] = val_item;
					}
				}

			});
		});

		$.ajax( {
			type   : "POST",
			url    : wr_theme_megamenu.ajaxurl,
			data   : {
				action           : 'wr_save_megamenu',
				_nonce           : wr_theme_megamenu._nonce,
				menu_id          : wr_theme_megamenu.menu_id,
				data             : data_save,
				data_last_update : 'ok',
			},
			success: function ( data_return ) {
				
				// Parse data
				var data_return = ( data_return ) ? JSON.parse( data_return ) : '';
				if( data_return.status == 'true' ) {
					if( $( '.wrmm-error' ).length ) {
						$( '.wrmm-error' ).remove();
					}
					
					// Submit form
					$.WrThemeMegamenu.allow_submit = true;
					$( '.wp-admin #update-nav-menu' ).submit();
				} else if( data_return.status == 'updating' ) {
					$.each( data_return.list_id_updated , function ( value, key ) {
						delete $.WrThemeMegamenu.data_save[ key ];
					});
					
					// Update next data
					$.WrThemeMegamenu.save_ajax();

				} else if( data_return.status == 'false' ) {
					if( $( '.wrmm-loading' ).length ) {
						$( '.wrmm-loading' ).remove();
					}
					
					// Show error
					$( '.major-publishing-actions .publishing-action' ).prepend( '<p class="wrmm-error">' + data_return.message + '</p>' );
				}
			}
		});
	}

	function count_menu_item_lv_2( element ){
		var item_next_lv_2  = element.next( '.menu-item-depth-1' );
		var count_item_lv_2 = 0;

		if( item_next_lv_2.length ) {
			var item_next_lv_0 = element.nextAll( '.menu-item-depth-0' );

			if( item_next_lv_0.length ) {
				var list_menu_item       = $( '#menu-to-edit > .menu-item' );
				var index_menu_item_next = list_menu_item.index( item_next_lv_0[0] );
				var index_menu_item      = list_menu_item.index( element );

				for( var i = index_menu_item + 2; i <= index_menu_item_next; i++ ) {
					if( $( '#menu-to-edit .menu-item:nth-child(' + i + ')' ).hasClass( 'menu-item-depth-1' ) ) {
						count_item_lv_2++;
					}
				}
			} else {
				var menu_item_next_lv_2   = element.nextAll( '.menu-item-depth-1' );
				count_item_lv_2 = menu_item_next_lv_2.length;
			}
		}

		return count_item_lv_2;
	}

	function get_permission_parent( _this ){
		var permission = { permission_show: null, permission_user: null };

		// Get id menu item
		var id = parseInt( _this.attr('id').split( 'menu-item-' )[1] );

		// Get level menu item
		var level = parseInt( _this.attr( 'class' ).split( 'menu-item-depth-' )[1].split( ' ' )[0] );

		if( level == 0 ){
			permission = {
				permission_show: $.WrThemeMegamenu['data_save'][id]['permission_show'],
				permission_user: $.WrThemeMegamenu['data_save'][id]['permission_user']
			};
		} else {
			var all_el_prev        = _this.prevAll();
			var length_all_el_prev = all_el_prev.length;
			var list_parent        = {};

			for( var i = length_all_el_prev - 1; i >= 0; i-- ){
				var prev_item = $( all_el_prev[i] );

				// Get id menu item
				var prev_id = parseInt( prev_item.attr('id').split( 'menu-item-' )[1] );

				// Get level menu item
				var prev_level = parseInt( prev_item.attr( 'class' ).split( 'menu-item-depth-' )[1].split( ' ' )[0] );

				if( prev_level < level ){
					list_parent[prev_level] = {
						permission_show: $.WrThemeMegamenu['data_save'][prev_id]['permission_show'],
						permission_user: $.WrThemeMegamenu['data_save'][prev_id]['permission_user']
					};
				}
			};

			$.each( list_parent, function( level, val ) {
				var flag_set_log_in = false;

				if( level == 0 ) {
					if( val['permission_show'] == 'log-in' ) {
						permission['permission_user'] = val['permission_user'];
					} else {
						permission['permission_user'] = 'all';
					}
				} else if( permission['permission_user'].length == 0 ) {
					permission['permission_user'] = '';
					flag_set_log_in = true;
				} else if( permission['permission_show'] != 'log-in' ){
					if( val['permission_show'] == 'log-in' ) {
						permission['permission_user'] = val['permission_user'];
					} else {
						permission['permission_user'] = 'all';	
					}
				} else {

					// Check permission user of parent
					if( permission['permission_user'].indexOf( 'all' ) != -1 ) {
						permission['permission_user'] = val['permission_user'];
					} else if( val['permission_user'].indexOf( 'all' ) != -1 ) {
						permission['permission_user'] = permission['permission_user'];
					} else {
						permission['permission_user'] = array_intersect( val['permission_user'], permission['permission_user'] );
					}
				}

				permission['permission_show'] = flag_set_log_in ? 'log-in' : val['permission_show'] ;
			});
		}

		return permission;
	}

	function array_intersect( array_1, array_2 ){
	    return $.grep( array_1, function(i) {
	        return $.inArray( i, array_2 ) > -1;
	    });
	}

	function update_option_data( element, val, key ){
		if( key ) {
			var id = element.closest( '.wrmm-wrapper' ).attr( 'data-id' );

			// Set value for option
    		$.WrThemeMegamenu.data_save[id][key] = val;
		}
	}

	function filter_row( value ){
		var list_column  = value.split( '+' );
		var result_array = [];

		var filter_column = function( value ){
			if( value.search( '/' ) != -1 ) {
				var val_all   = value.split( '/' );
				var val_first = Math.abs( parseInt( val_all[0] ) ) ? Math.abs( parseInt( val_all[0] ) ) : 1;
				var val_last  = Math.abs( parseInt( val_all[1] ) ) ? Math.abs( parseInt( val_all[1] ) ) : 1;

				return val_first + '/' + val_last;
			} else {
				var val_all = Math.abs( parseInt( value ) ) ? Math.abs( parseInt( value ) ) : 1;

				return val_all + '/1';
			}
		}

		if( list_column.length ) {
			$.each( list_column, function( key, val ) {
				var column = filter_column( val );

				result_array.push( column );
			} )
		} else {
			var column = filter_column( val );

			result_array.push( column );
		}

		return result_array;
	}

	function remove_add_column( count_item_lv_2, list_column ){
		if( count_item_lv_2 == 0 )
			return [];

		var column_surplus  = list_column.length - count_item_lv_2;

		if( column_surplus > 0 ) {
			for( var i = column_surplus; i > 0 ; i-- ) {
				list_column.splice( count_item_lv_2 + i - 1 , 1);
			}
		} else if ( column_surplus < 0 ) {
			for( var i = Math.abs( column_surplus ); i > 0 ; i-- ) {
				list_column.push( '1/1' );
			}
		}

		return list_column;
	}

	function set_spectrum(){
		$( '.wrmm-wrapper .txt-select-color' ).spectrum( {
			color: '',
		    showInput: true,
		    showInitial: true,
		    allowEmpty: true,
		    showAlpha: true,
		    clickoutFiresChange: true,
		    showButtons: true,
		    cancelText: 'Cancel',
		    chooseText: 'Choose',
		    preferredFormat: 'rgb',
		    show: function ( color ) {

		        $( '.sp-button-container .sp-default' ).remove();
		        $( '.sp-button-container' ).prepend( '<a class="sp-default" href="#">Default</a>' );
		        
		        var _this           = $(this);
		        var color_default   = color ? ( color.getAlpha() == 1 ? color.toHexString() : color.toRgbString() ) : '';
		        var container       = _this.parents('.wr-hb-colors-control');

		        $('.sp-default').off('click').on('click', function( event ) {;
		            event.preventDefault();
		            _this.spectrum( 'set', color_default );
		            _this.siblings('.show-color').text( color_default );
		            _this.val( color_default ).trigger('blur');
		            $('.sp-container:visible').find('.sp-input').val( color_default );
		        });

		        $('.sp-clear').off('click').on('click', function( event ) {;
		            event.preventDefault();
		            _this.spectrum( 'set', '' );
		            _this.siblings('.show-color').text( '' );
		            _this.val( '' ).trigger('blur');
		            $('.sp-container:visible').find('.sp-input').val( '' );
		        });

		        $('.sp-container:visible').find('.sp-input').val( color_default );
		    },
		    move: function ( color ) {
		    	if( ! color )
		    		return;

	            var val = color.getAlpha() == 1 ? color.toHexString() : color.toRgbString();
	            var _this = $(this);

	            _this.siblings('.show-color').text(val);
	            $('.sp-container:visible').find('.sp-input').val(val);
	            _this.val(val).trigger('change');
		    },
		    change: function( color ) {
		    	if( ! color )
		    		return;

	            var val = color.getAlpha() == 1 ? color.toHexString() : color.toRgbString();
	            var _this = $(this);

	            _this.siblings('.show-color').text(val);
	            _this.val(val).trigger('change');
	            $('.sp-container:visible').find('.sp-input').val(val);
		    }
		});
	}

	function add_button_ajax(){
		$( document ).ajaxComplete( function( event, xhr, settings ) {
			var url          = settings.url;
			var data_request = ( typeof settings.data != 'undefined' ) ? settings.data : '';

			if ( data_request.search( 'action=add-menu-item' ) != -1 ) {
				// Render button
				button_show_modal( true );
			}
		} );
	}

	$.WrThemeMegamenu['list_row'] = [ 
		'1/1',

		'1/2 + 1/2',
		'2/3 + 1/3',
		'1/3 + 2/3',
		'1/4 + 3/4',
		'3/4 + 1/4',
		'1/5 + 4/5',
		'4/5 + 1/5',
		'1/6 + 5/6',
		'5/6 + 1/6',

		'1/3 + 1/3 + 1/3',
		'1/4 + 1/4 + 2/4',
		'2/4 + 1/4 + 1/4',
		'1/5 + 1/5 + 3/5',
		'3/5 + 1/5 + 1/5',
		'1/6 + 1/6 + 4/6',
		'4/6 + 1/6 + 1/6',

		'1/4 + 1/4 + 1/4 + 1/4',
		'1/5 + 1/5 + 1/5 + 2/5',
		'2/5 + 1/5 + 1/5 + 1/5',
		'1/6 + 1/6 + 1/6 + 3/6',
		'3/6 + 1/6 + 1/6 + 1/6',

		'1/5 + 1/5 + 1/5 + 1/5 + 1/5',
		'1/6 + 1/6 + 1/6 + 1/6 + 2/6',
		'2/6 + 1/6 + 1/6 + 1/6 + 1/6',

		'1/6 + 1/6 + 1/6 + 1/6 + 1/6 + 1/6'
	];

	$.WrThemeMegamenu.data_icon = {
		'font_awesome' : {},
		'font_icomoon' : {},
		'dashicons' : {}
	};

	// Call function
	button_show_modal( false );
	list_event();
	add_button_ajax();
	button_expand();
	button_expand_collapse_all();
	add_fontawesome();

})( jQuery );