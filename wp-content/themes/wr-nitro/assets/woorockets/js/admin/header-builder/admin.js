/**
 * @version    1.0
 * @package    Nitro
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

(function($) {
	"use strict";

	function save() {

		$( '#titlewrap #title, #post input' ).keydown(function(event){
			if(event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});

		$( '#btn-save-header' ).click( function( e ){
			e.preventDefault();

			var _this = $(this);

			 _this.addClass( 'disabled' );
			 _this.closest( '.action-save' ).addClass( 'loading' );

			var header_data = JSON.stringify( parse_data_allow( hb.model.toJSON() ) );

			$( '#data-header' ).html( header_data );

			setTimeout( function () {
				$( '#publish' ).trigger( 'click' );

				// Re-trigger if page reload
				var beforeunload = false;
				$( window ).on('beforeunload', function() {
					beforeunload = true;
				});
				setTimeout( function(){
					if( ! beforeunload ) {
						$( '#publish' ).trigger( 'click' );
					}
				}, 1500 );

			}, 500 );
		} );

		// $( '#titlewrap #title' ).keyup( function(){
		// 	$( '#btn-save-header' ).removeClass( 'disabled' )
		// } );
	}

	function add_select_layout(){
		var page_title = $( '.post-type-header_builder .wrap .page-title-action' );
		var href_add   = page_title.attr( 'href' );
  
		page_title.append( '<div class="list-layout"><a href="' + href_add + '&layout=horizontal">Horizontal Layout</a><a href="' + href_add + '&layout=vertical">Vertical Layout</a></div>' );

		page_title.click( function( e ){
			e.stopPropagation();
			e.preventDefault();

			var _this = $(this);

			_this.toggleClass( 'active' );
		})

		$( '.post-type-header_builder .wrap .page-title-action' ).on( 'click', 'a', function(){
			var _this = $(this);
			var href  = _this.attr( 'href' );
			
			window.location.href = href;
		})

		$(document).click( function () {
			var _this = $( '.post-type-header_builder .wrap .page-title-action.active' );

			if( _this.length ) {
	            _this.removeClass( 'active' );
	        }

        })
	}

	function parse_data_allow( data ) {
		$.each( data, function( key, val ) {
			if( $.inArray( key , [ 'desktop', 'mobile' ] ) != -1 ) {

				data[key]['settings'] = recursive_data_allow( val['settings'], wr_hb_data_allow['settings'], true );

				$.each( val['rows'], function( key_row, val_row ) {
					data[key]['rows'][key_row] = recursive_data_allow( val_row, wr_hb_data_allow['rows'], false );

					$.each( val_row['cols'], function( key_col, val_col ) {
						data[key]['rows'][key_row]['cols'][key_col] = recursive_data_allow( val_col, wr_hb_data_allow['cols'], false );

						$.each( val_col['items'], function( key_item, val_item ) {
							var name_item = val_item['_rel'];

							data[key]['rows'][key_row]['cols'][key_col]['items'][key_item] = recursive_data_allow( val_item, wr_hb_data_allow['items'][name_item], true );
						});
					});
				});

			}
		});

		return data;
	}

	function recursive_data_allow( data, data_compare, recursive_allow ) {
		$.each( data, function( key, val ) {
			if( ( typeof( data_compare ) === 'object' && data_compare[key] == undefined ) || val === null ) {
				delete data[key];
			} else if ( typeof( val ) === 'object' ) {
				if( Object.keys( val ).length > 0 ) {
					data[key] = recursive_allow ? recursive_data_allow( val, data_compare[key], true ) : val;
				} else {
					delete data[key];
				}
			} else if ( typeof( val ) === 'string' ) {
				val = val.trim();

				if( data_compare[key] === val || val === '' ) {
					delete data[key];
				} else {
					data[key] = val;
				}
			} else if( data_compare[key] === val ) {
				delete data[key];
			} else {
				data[key] = val;
			}
		});

		return data;
	}

	function set_default() {

		// On the list
		$( '#the-list' ).on( 'click', '.hb-set-default:not(.loading)', function(){
			var set_to_default_confirm = confirm( 'Do you really want to set this header by default?' );

            if( ! set_to_default_confirm ) {
            	return;
            }

			var _this     = $(this);
			var header_id = _this.attr( 'data-id' );

			// Add loading
			_this.addClass( 'loading' );
			_this.append( '<img class="loading-img" src="images/spinner.gif" />' );

			$.ajax( {
				type : 'POST',
				url  : wr_site_data.ajax_url,
				data : {
					action    : 'set_to_default',
					header_id : header_id,
					_nonce    : wr_site_data._nonce
				},
				success: function( val ) {
					if( val ) {
						val = $.parseJSON(val);

						if( val.status == 'true' ) {
							var el_default_old = _this.closest( 'table' ).find( '.hb-default' );

							// Change text default
							_this.text( 'Default' );
							el_default_old.text( 'Set to default' );


							// Add class for header defaut current
							el_default_old.addClass( 'hb-set-default' ).removeClass( 'hb-default' );
							_this.addClass( 'hb-default' ).removeClass( 'hb-set-default' );

							// Remove loading
							_this.find( '.loading-img' ).remove();
							_this.removeClass( 'loading' );

							return;
						}
					}

					// Reload page if header does not exist.
					location.reload();

				}
			});
		});

		// On the detail
		$( '.settings-library' ).on( 'click', '.setting-item.set-default', function(){
			var set_to_default_confirm = confirm( 'Do you really want to set this header by default?' );

            if( ! set_to_default_confirm )
            	return;

			var _this = $(this);

			var header_id = $( '#hb-app' ).attr( 'data-id' );

			_this.removeClass( 'set-default' );
			_this.addClass( 'loading' );

			$.ajax({
				type: 'POST',
				url: wr_site_data.ajax_url,
				data: {
					action			: 'set_to_default',
					header_id		: header_id,
					_nonce			: wr_site_data._nonce
				},
				success: function( val ) {
					if( val ) {
						val = $.parseJSON(val);

						if( val.status == 'true' ) {
							_this.removeClass( 'loading' );
							_this.addClass( 'disabled' );
							_this.attr( 'title', 'Default' );
						}

					}
				}
			});
		});
	}

	function event_add_new() {
		$( '.hb-wrapper .add-new-header' ).click( function() {
			var _this = $(this);

			if( _this.hasClass( 'active' ) ) {
				_this.removeClass( 'active' );
				_this.next( '.select-layout' ).hide();
			} else {
				_this.addClass( 'active' );
				_this.next( '.select-layout' ).show();
			}

			return false;
		})

		$(document).click( function () {
			var _this = $( '.hb-wrapper .add-new-header' );
            _this.removeClass( 'active' );
			_this.next( '.select-layout' ).hide();
        });

	}

	function confirm_delete () {
		$( 'body' ).on( 'click', '.wr-delete', function() {
			var delete_confirm = confirm( "Are you sure delete?" );
			if( !delete_confirm )
				return false;
		});
	}

	function duplicate() {

		// On the list
		$( 'body' ).on( 'click', '.duplicate:not(.loaded)', function(){
			var _this 		= $(this);
			var parent 		= _this.closest( 'tr' );
			var header_id 	= parent.attr( 'data-id' );

			// Add loading
			_this.addClass( 'loaded' );
			_this.append( '<img class="loading" src="images/spinner.gif" />' );

			$.ajax({
				type	: 'POST',
				url 	: wr_site_data.ajax_url,
				data 	: {
					action		: 'duplicate',
					header_id	: header_id,
					_nonce		: wr_site_data._nonce
				},
				success: function( val ) {
					if( val ) {
						val = $.parseJSON(val);

						if( val.status == 'true' ) {

							// Remove loading
							_this.removeClass( 'loaded' );
							_this.find( '.loading' ).remove();

							// Add row
							var header_row = parent.html().replace( new RegExp( header_id, 'g'), val.header_id );
							parent.after( '<tr class="row-duplicate" data-id="' + val.header_id + '">' + header_row + '</tr>' );

							$( '.row-duplicate .title a' ).text( val.header_new_name );
							$( '.row-duplicate .author-create' ).text( val.author );
							$( '.row-duplicate .time-create' ).html( val.time_create );

							if( parent.hasClass( 'hb-default' ) ) {
								$( '.row-duplicate' ).removeClass( 'hb-default' );
								$( '.row-duplicate .action-default' ).text( 'Set to default' );
							}

							$( '.row-duplicate' ).removeClass( 'row-duplicate' );

							return;
						}
					}

					// Reload page if header does not exist.
					location.reload();
				}
			});
		});
	}

	$(document).ready(function() {
		add_select_layout();
		save();
		event_add_new();
		set_default();
		duplicate();
		confirm_delete();
	});

})(jQuery);
