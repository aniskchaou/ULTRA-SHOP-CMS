/**
 * @version 1.0
 * @package Nitro
 * @author WooRockets Team <support@woorockets.com>
 * @copyright Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

/*
 * [ Check browser support storage  ] - - - - - - - - - - - - - - - - - - - -
 */
var isLocalStorageSupported = function() {
    var testKey = 'test', storage = window.sessionStorage;
    try {
        storage.setItem(testKey, '1');
        storage.removeItem(testKey);
        return true;
    } catch (error) {
        return false;
    }
};

( function( $ ) {
	"use strict";

	$.WR = $.WR || {};

	/*
	 * [ List function callback when rotate_device ] - - - - - - - - - - - - - - - - - - - -
	 */
	$.function_rotate_device = {};

	/*
	 * [ Check all images are done yet ] - - - - - - - - - - - - - - - - - - - -
	 */
	$.fn.WR_ImagesLoaded = function( callback ) {
		var WR_Images = function( src, callback ) {
			var img = new Image;
			img.onload = callback;
			img.src = src;
		};
		var images = this.find( 'img' ).toArray().map( function( element ) {
			return element.src;
		} );

		if ( images.length ) {
			var loaded = 0;
			$( images ).each( function( i, src ) {
				WR_Images( src, function() {
					loaded++;
					if ( loaded == images.length ) {
						callback();
					}
				} )
			} );
		} else {
			callback();
		}
	}

	/*
	 * [ Image Lazyload ] - - - - - - - - - - - - - - - - - - - -
	 */
	$.fn.WR_ImagesLazyload = function(threshold, callback) {
		var $w = $(window),
		th = threshold || 0,
		retina = window.devicePixelRatio > 1,
		attrib = retina ? 'data-src-retina' : 'data-src-lazyload',
		images = this,
		loaded;

		this.one( 'WR_ImagesLazyload', function() {
			var source = this.getAttribute(attrib);
			source = source || this.getAttribute( 'data-src-lazyload' );
			if ( source ) {
				this.setAttribute( 'src', source );
				if ( typeof callback === 'function' ) callback.call( this );
			}
		});

		function WR_ImagesLazyload() {
			var inview = images.filter(function() {
				var $e = $( this );
				if ( $e.is( ':hidden' ) ) return;

				var wt = $w.scrollTop(),
				wb = wt + $w.height(),
				et = $e.offset().top,
				eb = et + $e.height();

				return eb >= wt - th && et <= wb + th;
			});

			loaded = inview.trigger( 'WR_ImagesLazyload' );
			images = images.not( loaded );
		}

		$w.on( 'scroll.WR_ImagesLazyload resize.WR_ImagesLazyload lookup.WR_ImagesLazyload', WR_ImagesLazyload );

		WR_ImagesLazyload();

		return this;
	};

	/*
	 * [ Check mobile device ] - - - - - - - - - - - - - - - - - - - -
	 */
	var isMobile = function() {
		return ( /Android|iPhone|iPad|iPod|BlackBerry/i ).test( navigator.userAgent || navigator.vendor || window.opera );
	};

	/*
	 * [ Find a key on data object form ] - - - - - - - - - - - - - - - - - - - - - - - - -
	 */
	function WR_Find_Key_Object_Form( object, find ) {
		var flag = false;

		$.each( object, function( key, val ){
			if( val['name'] == find ) {
				flag = true;
				return;
			}
		} );

		return flag;
	}

	/*
	 * [ Replace a key on data object form ] - - - - - - - - - - - - - - - - - - - - - - - - -
	 */
	function WR_Replace_Key_Object_Form( object, find, replace ) {
		$.each( object, function( key, val ){
			if( val['name'] == find ) {

				// Add key replace
				object.push( { name: replace, value: val['value'] } );

				// Delete key old
				delete object[ key ];
			}
		} );

		return object;
	}

	/*
	 * [ Replace a key on data object form ] - - - - - - - - - - - - - - - - - - - - - - - - -
	 */
	function WR_Parse_Object_Form( object ) {
		var data = {};

		$.each( object, function( key, val ){
			if( val != undefined ) {
				data[ val[ 'name' ] ] = val[ 'value' ];
			}
		} );

		return data;
	}

	/*
	 * [ Parse URL to array ] - - - - - - - - - - - - - - - - - - - - - - - - -
	 */
	function WR_Parse_Url_To_Array( url ) {
		if ( typeof url != 'string' || url.search( '&' ) == -1 )
			return false;

		var data = {}, queries, temp, i;

		// Split into key/value pairs
		queries = url.split( "&" );

		// Convert the array of strings into an object
		for ( i = 0; i < queries.length; i++ ) {
			temp = queries[ i ].split( '=' );
			data[ temp[ 0 ] ] = decodeURIComponent( temp[ 1 ] );
		}
		return data;
	}

	/*
	 * [ Header Builder - Element Search Box ] - - - - - - - - - - - - - - - - -
	 */
	function HB_Element_SearchBox() {

		$( '.hb-search .open.show-full-screen' ).on( 'click', function() {
			var _this = $( this );
			var parent = _this.parents( '.hb-search' );

			/*******************************************************************
			 * Render HTML for effect *
			 ******************************************************************/
			var sidebar_content = parent.find( '.hb-search-fs' )[ 0 ].outerHTML;
			$( 'body' ).append( sidebar_content );

			var background_style = $( this ).attr( 'data-background-style' );
			var layout = $( this ).attr( 'data-layout' );
			var search_form = $( 'body > .hb-search-fs' );

			if ( layout == 'topbar' && $( this ).hasClass( 'active-topbar' ) ) {
				search_close();
			} else {
				switch ( layout ) {
					case 'full-screen':
						search_form.fadeIn( 300 );
						$( 'html' ).addClass( 'no-scroll' );
					break;

					case 'topbar':
						var admin_bar = $( '#wpadminbar' );
						var margin_top = admin_bar.length ? admin_bar.height() : '0';

						$( this ).addClass( 'active-topbar' );

						search_form.css( {
							'display': 'block',
							'top': ( margin_top - 80 ) + 'px'
						} ).animate( {
							'top': margin_top + 'px'
						} );
						$( 'body > .wrapper-outer' ).css( {
							'position': 'relative',
							'top': '0px'
						} ).animate( {
							'top': '80px'
						} );
					break;
				}

				search_form.addClass( background_style + ' ' + layout );
				search_form.find( '.close' ).attr( 'data-layout', layout );
				search_form.find( 'form input' ).focus();
			}
		} );

		function search_close() {
			var _this = $( 'body > .hb-search-fs .close' );
			var layout = _this.attr( 'data-layout' );

			switch ( layout ) {
				case 'full-screen':
					$( 'body > .hb-search-fs' ).fadeOut( 300, function() {
						$( 'html' ).removeClass( 'no-scroll' );
						$( 'body > .hb-search-fs' ).remove();
						$( 'body > .wrapper-outer' ).removeAttr( 'style' );
					} );
				break;

				case 'topbar':
					var admin_bar = $( '#wpadminbar' );
					var margin_top = admin_bar.length ? admin_bar.height() : '0';

					$( 'body > .hb-search-fs' ).animate( {
						'top': ( margin_top - 80 ) + 'px'
					}, function() {
						$( this ).remove();
					} );

					$( 'body > .wrapper-outer' ).animate( {
						'top': '0px'
					}, function() {
						$( this ).removeAttr( 'style' );
					} );
				break;
			}

			$( '.header .hb-search' ).find( '.open.active-topbar' ).removeClass( 'active-topbar' );
		}

		$( 'body' ).on( 'click', '.hb-search-fs .close', function() {
			search_close();
		} );

		$( '.header .hb-search.dropdown .open' ).click( function() {
			var _this = $( this );
			var parents = _this.closest( '.hb-search' );
			var search_form = parents.find( '.search-form:first' );
			var index_current = $( '.header .hb-search.dropdown' ).index( parents );
			var parents_info = parents[ 0 ].getBoundingClientRect();
			var border_top_width = parseInt( parents.css( 'borderTopWidth' ) );
			var border_bottom_width = parseInt( parents.css( 'borderBottomWidth' ) );

			// Remove active element item more
			$( '.header .hb-search.dropdown:not(:eq(' + index_current + '))' ).removeClass( 'active-dropdown' );

			if ( parents.hasClass( 'active-dropdown' ) ) {
				parents.removeClass( 'active-dropdown' );
				search_form.removeClass( 'set-width' );
			} else {
				WR_Click_Outside( _this, '.hb-search', function( e ) {
					parents.removeClass( 'active-dropdown' );
					search_form.removeClass( 'set-width' );
				} );

				// Reset style
				search_form.removeAttr( 'style' );

				var width_content_broswer = $( window ).width();

				if ( search_form.width() > ( width_content_broswer - 10 ) ) {
					search_form.css( 'width', ( width_content_broswer - 10 ) );
					search_form.addClass( 'set-width' );
				}

				var width_content_broswer = $( window ).width();
				var search_form_info = search_form[ 0 ].getBoundingClientRect();
				var current_info = _this[ 0 ].getBoundingClientRect();

				// Get offset
				var offset_option = ( width_content_broswer > 1024 ) ? parseInt( WR_Data_Js[ 'offset' ] ) : 0;

				// Set left search form if hide broswer because small
				if ( width_content_broswer < ( search_form_info.right + 5 ) ) {
					var left_search_form = ( search_form_info.right + 5 + offset_option ) - width_content_broswer;
					search_form.css( 'left', -left_search_form + 'px' );
				} else if ( search_form_info.left < ( 5 + offset_option ) ) {
					search_form.css( 'left', '5px' );
				}

				var margin_top = ( parents.attr( 'data-margin-top' ) == 'empty' ) ? parents.attr( 'data-margin-top' ) : parseInt( parents.attr( 'data-margin-top' ) );

				// Remove margin top when stick
				if ( _this.closest( '.sticky-row-scroll' ).length || margin_top == 'empty' ) {
					var parent_sticky_info = _this.closest( ( _this.closest( '.sticky-row-scroll' ).length ? '.sticky-row' : '.hb-section-outer' ) )[ 0 ].getBoundingClientRect();
					var offset_top = parseInt( ( parent_sticky_info.bottom - parents_info.bottom ) + ( parents_info.height - border_top_width ) );

					search_form.css( 'top', offset_top );
				} else if ( margin_top > 0 ) {
					search_form.css( 'top', ( margin_top + ( parents_info.height - ( border_top_width + border_bottom_width ) ) ) );
				}

				parents.addClass( 'active-dropdown' );

				// Set width input if overflow
				var ls_form = parents.find( '.wrls-form' );

				if( ls_form.length ) {
					var width_cate = parents.find( '.cate-search-outer' ).width();
				}

				setTimeout( function() {
					parents.find( '.txt-search' ).focus();
				}, 300 );
			}
		} );

		/* Action for expand width */
		$( '.header .hb-search.expand-width .open' ).on( 'click', function( event ) {
			var _this = $( this );
			var parents = _this.closest( '.hb-search' );
			var form_search = parents.find( '.search-form form' )
			var info_form = form_search[ 0 ].getBoundingClientRect();
			var width_form = info_form.width;
			var header = _this.closest( '.header' );
			var is_vertical = header.hasClass( 'vertical-layout' );
			var is_expand_right = true;

			if ( parents.hasClass( 'expan-width-active' ) ) {
				form_search.stop( true, true ).css( {
					overflow: 'hidden'
				} ).animate( {
					width: '0px'
				}, 200, function() {
					parents.removeClass( 'expan-width-active' );
					form_search.removeAttr( 'style' );

					/*** Show elements element current ***/
					var parents_container = _this.closest( '.container' ).find( '.hide-expand-search' );

					parents_container.css( 'visibility', '' ).animate( {
						opacity: 1
					}, 200, function() {
						parents_container.removeClass( 'hide-expand-search' );
						$( this ).css( 'opacity', '' );
					} );
				} );
			} else {
				WR_Click_Outside( _this, '.hb-search', function( e ) {
					form_search.stop( true, true ).css( {
						overflow: 'hidden'
					} ).animate( {
						width: '0px'
					}, 200, function() {
						parents.removeClass( 'expan-width-active' );
						form_search.removeAttr( 'style' );

						/*** Show elements element current ***/
						var parents_container = _this.closest( '.container' ).find( '.hide-expand-search' );

						parents_container.css( 'visibility', '' ).animate( {
							opacity: 1
						}, 200, function() {
							parents_container.removeClass( 'hide-expand-search' );
							$( this ).css( 'opacity', '' );
						} );
					} );
				} );

				var info_search_current = _this[ 0 ].getBoundingClientRect();
				var width_ofset_left = info_search_current.left + info_search_current.width / 2;
				var width_broswer = document.body.offsetWidth;
				var width_open = parents.outerWidth();

				if ( is_vertical ) {

					var info_parents = parents[ 0 ].getBoundingClientRect();
					var info_header = header[ 0 ].getBoundingClientRect();

					// Left position
					if ( header.hasClass( 'left-position-vertical' ) ) {
						is_expand_right = ( info_parents.left - info_header.left - 10 ) >= info_form.width ? false : true;

						// Right position
					} else {
						is_expand_right = ( info_header.right - info_parents.right - 10 ) >= info_form.width ? true : false
					}

				} else {
					is_expand_right = width_ofset_left * 2 < width_broswer;
				}

				// Expand right
				if ( is_expand_right ) {

					/** * Hide elements right element current ** */
					var list_next_all = parents.nextUntil();

					if ( list_next_all.length ) {
						var width_next = 0;

						var handle_animate = function() {
							form_search.stop( true, true ).css( {
								left: width_open + 5,
								width: 0,
								overflow: 'hidden',
								visibility: 'initial'
							} ).animate( {
								width: width_form
							}, 200, function() {
								$( this ).css( 'overflow', '' );
							} );
						};

						if ( !is_vertical ) {
							list_next_all.each( function( key, val ) {
								if ( width_next < width_form ) {
									$( val ).animate( {
										opacity: 0
									}, 200, function() {
										$( val ).css( 'visibility', 'hidden' )
									} );
									$( val ).addClass( 'hide-expand-search' );
								}

								width_next += $( val ).outerWidth( true );

								if ( width_next > width_form )
									return false;
							} );

							setTimeout( handle_animate, 200 );
						} else {
							handle_animate();
						}

					} else {

						// Expand width form search
						form_search.stop( true, true ).css( {
							left: width_open + 5,
							width: 0,
							overflow: 'hidden',
							visibility: 'initial'
						} ).animate( {
							width: width_form
						}, 200, function() {
							$( this ).css( 'overflow', '' );
						} );



					}

					// Expand left
				} else {

					/*** Hide elements left near element current ***/
					var list_prev_all = parents.prevUntil();

					if ( list_prev_all.length ) {
						var width_prev = 0;

						var handle_animate = function() {
							form_search.stop( true, true ).css( {
								right: width_open + 5,
								width: 0,
								overflow: 'hidden',
								visibility: 'initial'
							} ).animate( {
								width: width_form
							}, 200, function() {
								$( this ).css( 'overflow', '' );
							} );
						};

						if ( !is_vertical ) {
							list_prev_all.each( function( key, val ) {
								if ( width_prev < width_form ) {
									$( val ).animate( {
										opacity: 0
									}, 200, function() {
										$( val ).css( 'visibility', 'hidden' )
									} );
									$( val ).addClass( 'hide-expand-search' );
								}

								width_prev += $( val ).outerWidth( true );

								if ( width_prev > width_form )
									return false;
							} );

							setTimeout( handle_animate, 200 );
						} else {
							handle_animate();
						}

					} else {

						// Expand width form search
						form_search.stop( true, true ).css( {
							right: width_open + 5,
							width: 0,
							overflow: 'hidden',
							visibility: 'initial'
						} ).animate( {
							width: width_form
						}, 200, function() {
							$( this ).css( 'overflow', '' );
						} );
					}
				}
				parents.addClass( 'expan-width-active' );

				setTimeout( function() {
					parents.find( '.txt-search' ).focus();
				}, 300 );
			}
		} );

		/* Action for Boxed */
		$( '.header .hb-search.boxed .open' ).on( 'click', function() {
			var _this = $( this );
			var parents = _this.parents( '.hb-search' );
			parents.find( 'input[type="submit"]' ).trigger( 'click' );
		} );
	}

	/*
	 * [ Header Builder - Element Cart ] - - - - - - - - - - - - - - - - - - - -
	 */
	function HB_Element_Cart() {
		$( '.hb-cart.sidebar' ).click( function() {
			var _this = $( this );
			var icon_cart = _this.find( '.cart-control-sidebar' );

			/*******************************************************************
			 * Render HTML for effect *
			 ******************************************************************/
			var cart_content = _this.find( '.hb-minicart' )[ 0 ].outerHTML;

			// Render menu content
			if ( !$( 'body > .hb-cart-outer' ).length ) {
				$( 'body' ).append( '<div class="hb-cart-outer"></div>' );
			}
			$( 'body > .hb-cart-outer' ).html( '<span class="wr-close-mobile"><span></span></span>' + cart_content );

			// Render overlay
			if ( !$( 'body > .overlay-sidebar' ).length ) {
				var overlay_cart = $( '<div class="overlay-sidebar"></div>' ).click( function() {
					close_cart( $( this ) );
				} );

				$( 'body' ).append( overlay_cart );
			}

			$( 'html' ).addClass( 'no-scroll' );

			/*******************************************************************
			 * Animation *
			 ******************************************************************/
			var animation = icon_cart.attr( 'data-animation' );
			var position = icon_cart.attr( 'data-position' );
			var overlay = $( 'body > .overlay-sidebar' );
			var wrapper_animation = $( 'body > .wrapper-outer' );
			var shop_item = $( '.hb-cart-outer .hb-minicart' );
			var widget_shop = $( '.hb-cart-outer .hb-minicart .widget_shopping_cart_content' );

			// Add attributes for overlay
			overlay.addClass( 'active' ).attr( 'data-animation', animation ).attr( 'data-position', position );

			shop_item.attr( 'style', '' );
			wrapper_animation.attr( 'style', '' );
			overlay.attr( 'style', '' );
			widget_shop.attr( 'style', '' );

			overlay.css( {
				'display': 'block'
			} ).animate( {
				'opacity': 1
			} );

			shop_item.addClass( position );

			shop_item.css( 'opacity', 1 );

			var shop_item_info = shop_item[ 0 ].getBoundingClientRect();

			var cart_slider = function(){
				var cart_list = $( '.hb-cart-outer .widget_shopping_cart_content > .cart_list-outer' );
				var cart_width = $( '.hb-cart-outer .hb-minicart' ).width();
				var list_width = cart_list.width();
				var total_price_with = $( '.hb-cart-outer .widget_shopping_cart_content > .price-checkout' ).outerWidth( true );

				if( cart_width < ( list_width + total_price_with ) ) {
					var cart_outer = $( '.hb-cart-outer' );
					var cart_list_outer = $( '.hb-cart-outer .cart_list-outer' );
					var cart_list = $( '.hb-cart-outer .cart_list' );
					cart_outer.addClass( 'cart-slider' );

					var cart_total_with = cart_width - total_price_with;
					var width_cart_list = cart_list.width();

					var item_count = parseInt( ( ( width_cart_list - cart_total_with ) + 50 ) / 80 ) + 1;

					cart_outer.attr( 'data-items', item_count );

					cart_list_outer.width( cart_total_with );
					cart_list_outer.prepend( '<div class="control"><div class="prev control-item"><div class="prev-inner control-inner"></div></div><div class="disabled next control-item"><div class="next-inner control-inner"></div></div></div>' );
				}
			};

			switch ( position ) {
				case 'position-sidebar-right':
					shop_item.css( {
						'visibility': 'visible',
						'right': '-' + shop_item_info.width + 'px'
					} ).animate( {
						'right': '0px'
					} );

					if ( animation == 'sidebar-push' || animation == 'sidebar-fall-down' || animation == 'sidebar-fall-up' )
						wrapper_animation.css( {
							'position': 'relative',
							'right': '0px'
						} ).animate( {
							'right': shop_item_info.width + 'px'
						} );

					switch ( animation ) {
						case 'sidebar-slide-in-on-top':
						break;

						case 'sidebar-push':
						break;

						case 'sidebar-fall-down':
							widget_shop.css( {
								'position': 'relative',
								'top': '-300px'
							} ).animate( {
								'top': '0px'
							} );
						break;

						case 'sidebar-fall-up':
							widget_shop.css( {
								'position': 'relative',
								'top': '300px'
							} ).animate( {
								'top': '0px'
							} );
						break;
					}

				break;

				case 'position-sidebar-left':
					shop_item.css( {
						'visibility': 'visible',
						'left': '-' + shop_item_info.width + 'px'
					} ).animate( {
						'left': '0px'
					} );

					if ( animation == 'sidebar-push' || animation == 'sidebar-fall-down' || animation == 'sidebar-fall-up' )
						wrapper_animation.css( {
							'position': 'relative',
							'left': '0px'
						} ).animate( {
							'left': shop_item_info.width + 'px'
						} );

					switch ( animation ) {
						case 'sidebar-slide-in-on-top':
						break;

						case 'sidebar-push':
						break;

						case 'sidebar-fall-down':
							widget_shop.css( {
								'position': 'relative',
								'top': '-300px'
							} ).animate( {
								'top': '0px'
							} );
						break;

						case 'sidebar-fall-up':
							widget_shop.css( {
								'position': 'relative',
								'top': '300px'
							} ).animate( {
								'top': '0px'
							} );
						break;
					}

				break;

				case 'position-sidebar-top':

					shop_item.addClass( 'active' );

					if ( animation == 'sidebar-slide-in-on-top' || animation == 'sidebar-push' || animation == 'sidebar-fall-down' ){
						shop_item.css( {
							'visibility': 'visible',
							'transform': 'translate(0%, -100%)'
						} ).animate( {
							'transform': 'translate(0%, 0%)'
						} );
					}

					// Add slider
					cart_slider();

					switch ( animation ) {
						case 'sidebar-slide-in-on-top':
						break;

						case 'sidebar-push':
						break;

						case 'sidebar-fall-down':
							widget_shop.css( {
								'position': 'relative',
								'top': '-150px',
								'opacity': 0
							} ).animate( {
								'top': '0px',
								'opacity': 1
							} );
						break;

						case 'sidebar-fall-up':
							shop_item.css( {
								'overflow': 'hidden',
								'visibility': 'visible',
								'transform': 'translate(0%, -100%)'
							} ).animate( {
								'transform': 'translate(0%, 0%)'
							}, function() {
								$( this ).css( 'overflow', '' );
							} );

							widget_shop.css( {
								'position': 'relative',
								'top': '150px',
								'opacity': 0
							} ).animate( {
								'top': '0px',
								'opacity': 1
							} );
						break;
					}

				break;

				case 'position-sidebar-bottom':

					if ( animation == 'sidebar-slide-in-on-top' || animation == 'sidebar-push' || animation == 'sidebar-fall-up' ) {
						shop_item.css( {
							'visibility': 'visible',
							'transform': 'translate(0%, 100%)'
						} ).animate( {
							'transform': 'translate(0%, 0%)'
						} );

					}

					// Add slider
					cart_slider();

					switch ( animation ) {
						case 'sidebar-slide-in-on-top':
						break;

						case 'sidebar-push':
						break;

						case 'sidebar-fall-down':
							shop_item.css( {
								'overflow': 'hidden',
								'visibility': 'visible',
								'transform': 'translate(0%, 100%)'
							} ).animate( {
								'transform': 'translate(0%, 0%)'
							}, function() {
								shop_item.css( 'overflow', '' );
							} );

							widget_shop.css( {
								'position': 'relative',
								'top': '-150px',
								'opacity': 0
							} ).animate( {
								'top': '0px',
								'opacity': 1
							} );
						break;

						case 'sidebar-fall-up':
							widget_shop.css( {
								'position': 'relative',
								'top': '150px',
								'opacity': 0
							} ).animate( {
								'top': '0px',
								'opacity': 1
							} );
						break;
					}

				break;
			}
		} );

		function close_cart( _this ) {
			_this.removeClass( 'active' );

			var animation = _this.attr( 'data-animation' );
			var position = _this.attr( 'data-position' );
			var widget_shop = $( '.hb-cart-outer .hb-minicart .widget_shopping_cart_content' );
			var sidebar_icon = $( '.active-icon-cart-sidebar' );
			var shop_item = $( '.hb-cart-outer .hb-minicart' );
			var wrapper_animation = $( 'body > .wrapper-outer' );

			_this.animate( {
				'opacity': 0
			}, function() {
				_this.hide();
			} );

			// Remove all style
			setTimeout( function() {
				// Synchronize cart content with the original mini-cart.
				$('.hb-cart.sidebar .hb-minicart')
				.html( $('body > .hb-cart-outer .hb-minicart').html() )
				.find( 'input.edit-number').each( function(i, e) {
					if ( parseInt( $(e).data('value-old') ) && $(e).attr('value') != $(e).data('value-old') ) {
						$(e).attr( 'value', $(e).data('value-old') );
					}
				} );

				$( 'body > .hb-cart-outer' ).remove();
				_this.remove();
				$( 'html' ).removeClass( 'no-scroll' );
				wrapper_animation.removeAttr( 'style' );
			}, 500 );

			var shop_item_info = shop_item[ 0 ].getBoundingClientRect();

			switch ( position ) {

				case 'position-sidebar-right':
					shop_item.animate( {
						'right': '-' + shop_item_info.width + 'px'
					} );

					if ( animation == 'sidebar-push' || animation == 'sidebar-fall-down' || animation == 'sidebar-fall-up' )
						wrapper_animation.animate( {
							'right': '0px'
						} );

					switch ( animation ) {
						case 'sidebar-slide-in-on-top':
						break;

						case 'sidebar-push':
						break;

						case 'sidebar-fall-down':

							widget_shop.animate( {
								'top': '-300px'
							} );

						break;

						case 'sidebar-fall-up':

							widget_shop.animate( {
								'top': '300px'
							} );

						break;
					}
				break;

				case 'position-sidebar-left':
					shop_item.animate( {
						'left': '-' + shop_item_info.width + 'px'
					} );

					if ( animation == 'sidebar-push' || animation == 'sidebar-fall-down' || animation == 'sidebar-fall-up' )
						wrapper_animation.animate( {
							'left': '0px'
						} );

					switch ( animation ) {

						case 'sidebar-slide-in-on-top':
						break;

						case 'sidebar-push':
						break;

						case 'sidebar-fall-down':

							widget_shop.animate( {
								'top': '-300px'
							} );

						break;

						case 'sidebar-fall-up':
							widget_shop.animate( {
								'top': '300px'
							} );
						break;
					}

				break;

				case 'position-sidebar-top':
					$( '.hb-cart-outer .hb-minicart .action-top-bottom' ).remove();

					if ( animation == 'sidebar-slide-in-on-top' || animation == 'sidebar-push' || animation == 'sidebar-fall-down' )
						shop_item.animate( {
							'top': '-120px'
						} );

					if ( animation == 'sidebar-push' || animation == 'sidebar-fall-down' || animation == 'sidebar-fall-up' )
						wrapper_animation.animate( {
							'top': '0px'
						} );

					switch ( animation ) {
						case 'sidebar-slide-in-on-top':
						break;

						case 'sidebar-push':
						break;

						case 'sidebar-fall-down':
							widget_shop.animate( {
								'top': '-150px',
								'opacity': 0
							} );
						break;

						case 'sidebar-fall-up':
							shop_item.css( 'overflow', 'hidden' ).animate( {
								'top': '-120px'
							}, function() {
								$( this ).css( 'overflow', '' );
							} );
							widget_shop.animate( {
								'top': '150px',
								'opacity': 0
							} );
						break;
					}

				break;

				case 'position-sidebar-bottom':

					$( '.hb-cart-outer .hb-minicart .action-top-bottom' ).remove();

					if ( animation == 'sidebar-slide-in-on-top' || animation == 'sidebar-push' || animation == 'sidebar-fall-up' )
						shop_item.animate( {
							'bottom': '-120px'
						} );

					if ( animation == 'sidebar-push' || animation == 'sidebar-fall-down' || animation == 'sidebar-fall-up' )
						wrapper_animation.animate( {
							'bottom': '0px'
						} );

					switch ( animation ) {

						case 'sidebar-slide-in-on-top':
						break;

						case 'sidebar-push':
						break;

						case 'sidebar-fall-down':
							shop_item.css( 'overflow', 'hidden' ).animate( {
								'bottom': '-120px'
							}, function() {
								$( this ).css( 'overflow', 'initial' );
							} );
							widget_shop.animate( {
								'top': '-150px',
								'opacity': 0
							} );
						break;

						case 'sidebar-fall-up':
							widget_shop.animate( {
								'top': '150px',
								'opacity': 0
							} );
						break;
					}

				break;

			}

			// Remove all style
			setTimeout( function() {
				wrapper_animation.removeAttr( 'style' );
				shop_item.removeAttr( 'style' );
				$( '.hb-cart-outer .hb-minicart' ).removeAttr( 'style' );
				widget_shop.removeAttr( 'style' );
				_this.removeAttr( 'style' );
			}, 500 );
		};

		if ( $.fn.hoverIntent ) {
			$( 'body' ).hoverIntent( {
				over: function() {
					var _this = $( this );
					var shopping_outer = _this.find( '.hb-minicart-outer:first' );
					var link_cart_info = _this.find( '.link-cart:first' )[ 0 ].getBoundingClientRect();

					// Reset style
					shopping_outer.removeAttr( 'style' );

					var shopping_outer_info = shopping_outer[ 0 ].getBoundingClientRect();

					var width_content_broswer = $( window ).width(),
						height_content_broswer = $( window ).height();

					// Get offset
					var offset_option = ( width_content_broswer > 1024 ) ? parseInt( WR_Data_Js[ 'offset' ] ) : 0;

					// Set left search form if hide broswer because small
					if ( width_content_broswer < ( shopping_outer_info.right + 5 + offset_option ) ) {
						var left_search_form = ( ( shopping_outer_info.right + 5 ) - width_content_broswer ) + offset_option;
						shopping_outer.css( 'left', -left_search_form + 'px' );
					} else if ( shopping_outer_info.left < ( 5 + offset_option ) ) {
						shopping_outer.css( 'left', '5px' );
					};

					_this.addClass( 'active-dropdown' );

					var margin_top = ( _this.attr( 'data-margin-top' ) == 'empty' ) ? _this.attr( 'data-margin-top' ) : parseInt( _this.attr( 'data-margin-top' ) );

					// Remove margin top when stick
					if ( _this.closest( '.sticky-row-scroll' ).length || margin_top == 'empty' ) {
						var current_info = _this[ 0 ].getBoundingClientRect();
						var parent_info = _this.closest( ( _this.closest( '.sticky-row-scroll' ).length ? '.sticky-row' : '.hb-section-outer' ) )[ 0 ].getBoundingClientRect();
						var hover_area_height = parseInt( parent_info.bottom - link_cart_info.bottom );
						var offset_top_cart = parseInt( parent_info.bottom - link_cart_info.top );

						if ( _this.find( '.hover-area' ).length == 0 ) {
							_this.append( '<span class="hover-area" style="height:' + hover_area_height + 'px"></span>' );
						}

						shopping_outer.css( 'top', offset_top_cart );
					} else if ( margin_top > 0 ) {
						if ( _this.find( '.hover-area' ).length == 0 )
							_this.append( '<span class="hover-area" style="height:' + margin_top + 'px"></span>' );

						var current_info = _this[ 0 ].getBoundingClientRect();
						shopping_outer.css( 'top', ( margin_top + link_cart_info.height ) );
					}

					// Set scroll if mini cart hidden
					shopping_outer_info = shopping_outer[ 0 ].getBoundingClientRect();

					if( shopping_outer_info.bottom > height_content_broswer ) {
						var height_shopping = shopping_outer_info.height - ( shopping_outer_info.bottom - height_content_broswer ) - 5 ;

						shopping_outer.css( { overflowY : 'scroll', height: height_shopping } );
					}

				},
				out: function() {
					var _this = $( this );
					_this.removeClass( 'active-dropdown' );
					_this.find( '.hover-area' ).remove();
				},
				timeout: 0,
				sensitivity: 1,
				interval: 0,
				selector: '.hb-cart.dropdown'
			} );
		}

		/*
		 * [ Cart slider ] - - - - - - - - - - - - - - - - - - - -
		 */
		$( 'body' ).on( 'click', '.hb-cart-outer.cart-slider .control .prev', function(){
			var _this = $(this);
			var parent = _this.closest( '.hb-cart-outer' );
			var data_items = parseInt( parent.attr( 'data-items' ) );

			if( parent.attr( 'data-item' ) >= data_items ) {
				return;
			}

			var data_item = ( parent.attr( 'data-item' ) == undefined ) ? 1 : ( parseInt( parent.attr( 'data-item' ) ) + 1 );
			var cart_list = parent.find( '.cart_list' );
			parent.attr( 'data-item', data_item );

			cart_list.css( 'right', -( data_item * 80 ) );

			if( data_items == data_item ) {
				_this.addClass( 'disabled' );
			}

			$( '.hb-cart-outer.cart-slider .control .next' ).removeClass( 'disabled' );
		} );

		$( 'body' ).on( 'click', '.hb-cart-outer.cart-slider .control .next', function(){
			var _this = $(this);
			var parent = _this.closest( '.hb-cart-outer' );

			if( parent.attr( 'data-item' ) == undefined || parent.attr( 'data-item' ) == 0 ) {
				return;
			}

			var data_item = parseInt( parent.attr( 'data-item' ) ) - 1;
			var cart_list = parent.find( '.cart_list' );
			parent.attr( 'data-item', data_item );

			if ( data_item == 0 ) {
				_this.addClass( 'disabled' );
			}

			$( '.hb-cart-outer.cart-slider .control .prev' ).removeClass( 'disabled' );

			cart_list.css( 'right', -( data_item * 80 ) );
		} );

		/*
		 * [ Remove product ajax ] - - - - - - - - - - - - - - - - - - - -
		 */
		$( 'body' ).on( 'click', '.widget_shopping_cart_content .remove-item .remove', function( e ) {
			e.preventDefault();

			var _this = $( this ),
				parent = _this.closest( '.hb-minicart' ),
				cart_item_key = _this.attr( "data-product_id" );

			// Add class loading
			_this.addClass( 'loading' );

			// Schedule updating mini cart of header builder.
			var update_hb_mini_cart_timer;

			function update_hb_mini_cart_after_removing_product() {
				$.ajax( {
					type: 'POST',
					url: WRAjaxURL,
					data: {
						action: 'wr_product_remove',
						cart_item_key: cart_item_key
					},
					success: function( val ) {
						if ( val ) {
							val = $.parseJSON( val );

							if ( parent.hasClass( 'position-sidebar-top' ) || parent.hasClass( 'position-sidebar-bottom' ) ) {
								var cart_slider = _this.closest( '.cart-slider' );
								if( cart_slider.length ) {
									var cart_slider_item = parseInt( cart_slider.attr( 'data-items' ) );
									cart_slider.attr( 'data-items', ( cart_slider_item - 1 ) );

									if( cart_slider_item == 1 ) {
										cart_slider.removeClass( 'cart-slider' );
										cart_slider.find( '.cart_list-outer' ).removeAttr( 'style' );
									}
								}

								$( 'li[data-key="' + cart_item_key + '"]' ).hide( 300, function() {
									$( 'li[data-key="' + cart_item_key + '"]' ).remove();
								} )
							} else {
								$( 'li[data-key="' + cart_item_key + '"]' ).slideUp( 300, function() {
									$( 'li[data-key="' + cart_item_key + '"]' ).remove();
								} );
							}

							if ( $( '.mini-price' ).length ) {
								$( '.mini-price' ).html( val.price_total );
							}

							if ( $( '.hb-cart .cart-control .count' ).length ) {
								$( '.hb-cart .cart-control .count' ).html( val.count_product );
							}

							if ( val.count_product == 0 ) {
								if ( $( '.hb-minicart .total' ).length ) {
									$( '.hb-minicart .total' ).hide();
								}

								if ( $( '.hb-minicart .buttons' ).length ) {
									$( '.hb-minicart .buttons' ).hide();
								}

								if ( !$( '.hb-minicart .product_list_widget .empty' ).length ) {
									$( '.hb-minicart .product_list_widget' ).append( '<li class="empty">' + val.empty + '</li>' );
								}
							}
						}
					}
				} );
			}

			update_hb_mini_cart_timer = setTimeout(update_hb_mini_cart_after_removing_product, 500);

			// Listen to ajaxComplete event to update the mini cart of header builder.
			$(document).ajaxComplete(function(event, xhr, settings) {
				var url = settings.url;

				if (url.search('wc-ajax=remove_from_cart' ) > -1) {
					update_hb_mini_cart_timer && clearTimeout(update_hb_mini_cart_timer);
					update_hb_mini_cart_after_removing_product();
				}
			});
		} );

		$( document ).ajaxComplete( function( event, xhr, settings ) {
			var url = settings.url;
			var data_request = ( typeof settings.data != 'undefined' ) ? settings.data : '';

			if ( url.search( 'wc-ajax=add_to_cart' ) != -1 ) {

				if ( ! isLocalStorageSupported() ) {
					return window.location.reload();
				}

				if ( settings.data != undefined && xhr.responseJSON != undefined && xhr.responseJSON.cart_hash != undefined ) {
					var data_array_url = WR_Parse_Url_To_Array( settings.data );

					$.ajax( {
						type: 'POST',
						url: WRAjaxURL,
						data: {
							action: 'wr_add_to_cart_message',
							product_id: data_array_url.product_id,
						},
						success: function( val ) {
							if ( val.message == undefined )
								return false;

							$( 'body > .wr-notice-cart-outer' ).remove();
							var content_notice = '<div class="wr-notice-cart-outer pf ptr"><div class="wr-notice-cart"><div class="icon-notice"><i class="nitro-icon-cart-9"></i></div><div class="text-notice">' + val.message + '</div></div></div>';
							$( 'body' ).append( content_notice );

							var close = $( '<span class="close-notice"></span>' ).click( function() {
								$( this ).closest( '.wr-notice-cart-outer' ).removeClass( 'active' );
							} );

							$( 'body .wr-notice-cart' ).prepend( close );

							setTimeout( function() {
								$( 'body > .wr-notice-cart-outer' ).addClass( 'active' );
							}, '10' );

							setTimeout( function() {
								$( 'body > .wr-notice-cart-outer' ).removeClass( 'active' );
							}, '5000' );
						}
					} );

				} else if(  settings.data != undefined && xhr.responseJSON != undefined && xhr.responseJSON.error == true  ){
					$.ajax( {
						type: 'POST',
						url: WRAjaxURL,
						data: {
							action: 'wr_add_to_cart_error'

						},
						success: function( val ) {
							if ( val.message == undefined )
								return false;

							$( 'body > .wr-notice-cart-outer' ).remove();

							var content_notice = '<div class="wr-notice-cart-outer pf ptr error"><div class="wr-notice-cart"><div class="icon-notice"><i class="nitro-icon-cart-9"></i></div><div class="text-notice">' + val.message + '</div></div></div>';


							$( 'body' ).append( content_notice );

							var close = $( '<span class="close-notice"></span>' ).click( function() {
								$( this ).closest( '.wr-notice-cart-outer' ).removeClass( 'active' );
							} );

							$( 'body .wr-notice-cart' ).prepend( close );

							setTimeout( function() {
								$( 'body > .wr-notice-cart-outer' ).addClass( 'active' );
							}, '10' );

							setTimeout( function() {
								$( 'body > .wr-notice-cart-outer' ).removeClass( 'active' );
							}, '5000' );
						}
					} );
				}

			}

			if ( settings.data != undefined ) {
				var data_array_url = WR_Parse_Url_To_Array( settings.data );

				if ( data_array_url.action == 'add_to_wishlist' ) {

					$( 'body > .wr-notice-cart-outer' ).remove();
					var content_notice = '<div class="wr-notice-cart-outer pf ptr"><div class="wr-notice-cart"><div class="icon-notice"><i class="fa fa-heart-o"></i></div><div class="text-notice"><div> ' + xhr.responseJSON.message + ' </div><a class="db" href="' + xhr.responseJSON.wishlist_url + '">' + WR_Data_Js[ 'View Wishlist' ] + '</a></div></div></div>';
					$( 'body' ).append( content_notice );

					var close = $( '<span class="close-notice"></span>' ).click( function() {
						$( this ).closest( '.wr-notice-cart-outer' ).removeClass( 'active' );
					} );

					$( 'body .wr-notice-cart' ).prepend( close );

					setTimeout( function() {
						$( 'body > .wr-notice-cart-outer' ).addClass( 'active' );
					}, '10' );

					setTimeout( function() {
						$( 'body > .wr-notice-cart-outer' ).removeClass( 'active' );
					}, '5000' );

				}

			}

			// Add to cart from Wishlist
			if ( settings.data != undefined ) {
				var data_array_url = WR_Parse_Url_To_Array( settings.data );

				if ( url.search( 'wc-ajax=add_to_cart' ) != -1  && data_array_url.remove_from_wishlist_after_add_to_cart != undefined ) {
					$( '.woocommerce-message' ).hide();
					setTimeout( function() {
						if ( $( '.wishlist_table tbody tr' ).length <= 1 ) {
							$('.wishlist_table').remove();
							$( '#yith-wcwl-form' ).addClass('empty');
						}
					}, 1000 );
				}
			}

		});

		/*
		 * [ Update total price ] - - - - - - - - - - - - - - - - - - - -
		 */
		$( document.body ).on( 'wc_fragments_loaded wc_fragments_refreshed added_to_cart' , function() {
			if ( isLocalStorageSupported() ) {
				if ( window.wc_cart_fragments_params !== undefined && wc_cart_fragments_params.fragment_name !== undefined ) {
					var wc_fragments = $.parseJSON( sessionStorage.getItem( wc_cart_fragments_params.fragment_name ) );

					if( wc_fragments && typeof wc_fragments['wr_total_price'] != 'undefined' && typeof wc_fragments['wr_count_item'] != 'undefined' ) {
						$( '.hb-cart .cart-control .count' ).html( wc_fragments['wr_count_item'] );
						$( '.mini-price' ).html( wc_fragments['wr_total_price'] );
					}
				}
			}
		} );

		/*
		 * [ Edit product ajax ] - - - - - - - - - - - - - - - - - - - -
		 */
		var timer_product;
		$( 'body' ).on( 'blur change', '.widget_shopping_cart_content .edit-number', function() {
			var _this            = $( this ),
				parent           = _this.closest( '.mini_cart_item' ),
				cart_item_key    = parent.attr( 'data-key' ),
				cart_item_number = _this.val(),
				position         = _this.closest( '.hb-minicart' ).attr( 'data-slidebar-position' ),
				max_number       = _this.attr( 'data-max' ),
				value_old        = _this.attr( 'data-value-old' ),
				multiplication   = parent.find( '.multiplication' );

			multiplication.removeClass( 'loading' );

			if ( timer_product ) {
				clearTimeout( timer_product );
			}

			timer_product = setTimeout( function() {
				if ( cart_item_number == '' || cart_item_number == 0 || value_old == cart_item_number ) {
					return;
				}

				if ( max_number && parseInt( cart_item_number ) > parseInt( max_number ) ) {
					var wr_error_cannot_add = WR_Data_Js[ 'wr_error_cannot_add' ],
					wr_error_cannot_add = wr_error_cannot_add.replace( /%d/g, max_number );
                    _this.focus();
                    _this.val(value_old);
					alert( wr_error_cannot_add );
					return;
				}

				// Add class loading
			 	multiplication.addClass( 'loading' );

				$.ajax( {
					type: 'POST',
					url: WR_CART_URL,
					data: {
						'wr-action-cart': 'update_cart',
						cart_item_key: cart_item_key,
						cart_item_number: cart_item_number
					},
					success: function( val ) {
						if ( val.count_product == 0 ) {
							if ( $( '.hb-minicart .total' ).length ) {
								$( '.hb-minicart .total' ).hide();
							}

							if ( $( '.hb-minicart .buttons' ).length ) {
								$( '.hb-minicart .buttons' ).hide();
							}

							if ( !$( '.hb-minicart .product_list_widget .empty' ).length ) {
								$( '.hb-minicart .product_list_widget' ).append( '<li class="empty">' + val.empty + '</li>' );
							}
						}

						if ( $( '.mini-price' ).length ) {
							$( '.mini-price' ).html( val.price_total );
						}

						if ( $( '.hb-cart .cart-control .count' ).length ) {
							$( '.hb-cart .cart-control .count' ).html( val.count_product );
						}

						multiplication.removeClass( 'loading' );
						_this.attr( 'data-value-old', cart_item_number );
					}
				} );
			}, 50 );
		} );

		/*
		 * [ Add product ajax ] - - - - - - - - - - - - - - - - - - - -
		 */
		if ( WR_Data_Js.ajax_add_to_cart_single != 'no' || parseInt(WR_Data_Js.buy_now_button_enabled) ) {
			// Backup then remove all event handles attached to 'add to cart' form.
			$( window ).load( function() {
				var form_add_to_cart = document.querySelector( 'form.cart' ), event_handles;

				if ( form_add_to_cart ) {
					event_handles = $._data( form_add_to_cart, 'events' );

					$.WR.form_add_to_cart_events = {};

					for ( var e in event_handles ) {
						if ( ['click', 'submit'].indexOf(e) < 0 ) {
							continue;
						}

						// Get attached event handlers.
						$.WR.form_add_to_cart_events[ e ] = [];

						for ( var i = 0; i < event_handles[ e ].length; i++ ) {
							// Make sure 'click' event is listen to submit button.
							if ( e == 'click' ) {
								var target = $(form_add_to_cart).find(event_handles[ e ][ i ].selector);

								if (target[0] && target[0].type != 'submit') {
									continue;
								}
							}

							$.WR.form_add_to_cart_events[ e ].push( {
								handler: event_handles[ e ][ i ].handler,
								selector: event_handles[ e ][ i ].selector
							} );
						}

						for ( var i = 0, n = $.WR.form_add_to_cart_events[ e ].length; i < n; i++) {
							// Remove attached event handlers.
							$( 'form.cart' ).off( e, $.WR.form_add_to_cart_events[ e ][ i ].selector, $.WR.form_add_to_cart_events[ e ][ i ].handler );
						}
					}

					$( 'form.cart' ).on( 'submit', function( e ) {
						if ( $.WR.form_add_to_cart_processing ) {
							e.preventDefault();
						}
					} );
				}
			} );

			$( 'body' ).on( 'click', '.quickview-modal form.cart .wr_single_add_to_cart_ajax, .product-type-subscription .cart .single_add_to_cart_button', function( e ) {
				e.preventDefault();
				e.stopPropagation();

				var _this = $( this );
				window.wr_add_to_cart_ajax( _this, e );
			} );

			$( 'form.cart .wr_single_add_to_cart_ajax, .product-type-subscription .cart .single_add_to_cart_button' ).click( function( e ) {
				var _this = $( this );

				// Prevent default event handler.
				e.preventDefault();

				// Check flag to see if 'add to cart' action is in progress.
				if ( ! $.WR.form_add_to_cart_processing ) {
					$.WR.form_add_to_cart_processing = true;

					// Check requied YITH WooCommerce Product Add-Ons plugin
					if( typeof yith_wapo_general != 'undefined' ) {
						setTimeout( function(){
							if( yith_wapo_general.do_submit ) {
								window.wr_add_to_cart_ajax( _this, e );
							}
						}, 100 );
					} else {
						window.wr_add_to_cart_ajax( _this, e );
					}
				} else {
					e.stopPropagation();
				}
			} );

			$( '.floating-add-to-cart .floating_button' ).click( function( e ) {
				e.preventDefault();
				e.stopPropagation();

				var _this = $( this );

				$( 'form.cart .single_add_to_cart_button' ).trigger( 'click' );
			} );

			window.wr_add_to_cart_ajax = function( _this, _event, callback ) {
				var
				form_cart = _this.closest( 'form' ),
				data_post = form_cart.serializeArray(),
				floating = $( '.floating-add-to-cart .floating_button' ),

				handle_response = function(val) {
					var
					exp = new RegExp('<scr' + 'ipt' + ' id="tp-notice-html"[^>]*>(\\{"status":[^\\r\\n]+\\})<\/scr' + 'ipt>'),
					val = val.match(exp);

					// Redirect to the cart page after successful addition
					if( ! val ) {
						// Find ID of the product just added to cart.
						var pID;

						for (var i = 0; i < data_post.length; i++) {
							if (data_post[i].name == 'add-to-cart') {
								pID = data_post[i].value;

								break;
							}
						}

						if (pID) {
							$.ajax( {
								type: 'POST',
								url: WRAjaxURL,
								data: {
									action: 'wr_add_to_cart_message',
									url_only: 'true',
									product_id: pID
								},
								success: function( url_redirect ) {
									if( url_redirect != undefined ) {
										window.location = url_redirect;
										return;
									}
								}
							} );
						}

						return;
					}

					val = $.parseJSON( val[1] );

					if ( typeof callback == 'function' ) {
						return callback(val);
					}

					if ( val.status == 'true' ) {
						// Redirect to cart option
						if( val.redirect != undefined ) {
							window.location = val.redirect;
							return;
						}

						$( 'body > .wr-notice-cart-outer' ).remove();

						var custom_notice = val.notice;

						custom_notice = custom_notice.replace(/&quot;/g, '"')

						var link = custom_notice.match(/<a[^>]+>.?[^<]*<\/a[^>]*>/)[0];

						custom_notice.replace(/(<a[^>]+>.?[^<]*<\/a[^>]*>)(.*$)/);
						custom_notice = custom_notice.replace(/(<a[^>]+>.?[^<]*<\/a[^>]*>)(.*$)/, '$2');
						custom_notice = custom_notice.replace(/(".?[^"]*")(.*)/, '<div><b>$1</b>$2</div>');

						val.notice = link + custom_notice;

						var content_notice = '<div class="wr-notice-cart-outer pf ptr"><div class="wr-notice-cart"><div class="icon-notice"><i class="nitro-icon-cart-9"></i></div><div class="text-notice">' + val.notice + '</div></div></div>';

						$( 'body' ).append( content_notice );

						var close = $( '<span class="close-notice"></span>' ).click( function() {
							$( this ).closest( '.wr-notice-cart-outer' ).removeClass( 'active' );
						} );

						$( 'body .wr-notice-cart' ).prepend( close );

						setTimeout( function() {
							$( 'body > .wr-notice-cart-outer' ).addClass( 'active' );
						}, 10 );

						setTimeout( function() {
							$( 'body > .wr-notice-cart-outer' ).removeClass( 'active' );
						}, 5000 );

						_this.addClass( 'added' );
						floating.addClass( 'added' );

						// Update DOM of mini cart
						$( document.body ).trigger( 'updated_wc_div' );
					}

					else if ( val.status == 'false' ) {
						$( 'body > .wr-notice-cart-outer' ).remove();

						var content_notice = '<div class="wr-notice-cart-outer pf ptr error"><div class="wr-notice-cart"><div class="icon-notice"><i class="nitro-icon-cart-9"></i></div><div class="text-notice">' + (typeof val.notice === 'object' ? val.notice.notice : val.notice) + '</div></div></div>';

						$( 'body' ).append( content_notice );

						var close = $( '<span class="close-notice"></span>' ).click( function() {
							$( this ).closest( '.wr-notice-cart-outer' ).removeClass( 'active' );
						} );

						$( 'body .wr-notice-cart' ).prepend( close );

						setTimeout( function() {
							$( 'body > .wr-notice-cart-outer' ).addClass( 'active' );
						}, 10 );

						setTimeout( function() {
							$( 'body > .wr-notice-cart-outer' ).removeClass( 'active' );
						}, 5000 );

						_this.addClass( 'error' );
						floating.addClass( 'error' );
					}

					_this.removeClass( 'loading' );
					_this.prop( 'disabled', false );

					floating.removeClass( 'loading' );
					floating.prop( 'disabled', false );
				};

				// Set processing state.
				_this.prop( 'disabled', true );
				_this.addClass( 'loading' );
				_this.removeClass( 'added error' );

				floating.addClass( 'loading' );
				floating.removeClass( 'added error' );

				// Prepare form for submission.
				if ( ! form_cart.find( 'input[name="wr-action-cart"]' ).length ) {
					form_cart.append( '<input type="hidden" name="wr-action-cart" value="add_to_cart" />' );
				}

				// Execute all event handles attached to 'add to cart' form.
				for ( var e in $.WR.form_add_to_cart_events ) {
					for ( var i = 0; i < $.WR.form_add_to_cart_events[ e ].length; i++ ) {
						if ( $.WR.form_add_to_cart_events[ e ][ i ].handler ) {
							var this_elm = _this.closest( 'form' );

							if ( $.WR.form_add_to_cart_events[ e ][ i ].selector ) {
								this_elm = this_elm.find( $.WR.form_add_to_cart_events[ e ][ i ].selector );
							}

							if ( this_elm.length ) {
								$.WR.form_add_to_cart_events[ e ][ i ].handler.call( this_elm[0], _event );
							}
						}
					}
				}

				if ( ! isLocalStorageSupported() ) {
					form_cart.append( '<input type="hidden" name="add_to_cart_normally" value="1" />' );
				} else {
					// Create an iframe dynamically to submit form.
					var iframe = $( 'iframe#wr_nitro_add_to_cart_iframe' );

					if ( ! iframe.length ) {
						iframe = $( '<iframe />', {
							id: 'wr_nitro_add_to_cart_iframe',
							name: 'wr_nitro_add_to_cart_iframe',
							src: 'about:blank'
						} ).css( {
							position: 'absolute',
							top: _this.offset().top + 'px',
							left: _this.offset().left + 'px',
							width: _this.outerWidth() + 'px',
							height: _this.outerHeight() + 'px',
							opacity: 0,
							visibility: 'hidden'
						} );

						$(document.body).append( iframe );
					}

					iframe.show().off( 'load' ).on( 'load', function( event ) {
						handle_response(
							typeof event.target.contentDocument.documentElement.outerHTML != 'undefined'
							? event.target.contentDocument.documentElement.outerHTML
							: event.target.contentDocument.documentElement.innerHTML
						);

						iframe.hide();
					} );

					form_cart.attr( 'target', 'wr_nitro_add_to_cart_iframe' );
				}

				// Reset flag to state that 'add to cart' action is ready.
				$.WR.form_add_to_cart_processing = false;

				form_cart.submit();
			}
		}

		/*
		 * [ Product single Quick Buy Button ] - - - - - - - - - - - - - - - - - - - - - - - -
		 */
		$( 'body' ).on( 'show_variation', 'form.cart.variations_form', function( event, variation, purchasable ) {
			event.preventDefault();
			var _this = $( this );

			if ( purchasable ) {
				// Enable button
				_this.find( '.single_buy_now' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
				_this.find( '.single_add_to_cart_button' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
				_this.find( '.woocommerce-variation-add-to-cart' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
				$( '.floating-add-to-cart button' ).removeAttr( 'disabled' );

				// Remove notice title
				_this.find( '.single_buy_now' ).removeClass( 'wr-notice-tooltip' );
				_this.find( '.single_buy_now .notice-tooltip' ).remove();
				_this.find( '.single_add_to_cart_button' ).removeClass( 'wr-notice-tooltip' );
				_this.find( '.single_add_to_cart_button .notice-tooltip' ).remove();
				_this.find( '.woocommerce-variation-add-to-cart' ).removeClass( 'wr-notice-tooltip' );
				_this.find( '.woocommerce-variation-add-to-cart .notice-tooltip' ).remove();
				$( '.floating-add-to-cart button' ).removeClass( 'wr-notice-tooltip' );
				$( '.floating-add-to-cart button .notice-tooltip' ).remove();
			} else {
				// Disabled button
				_this.find( '.single_buy_now' ).attr( 'disabled', 'disabled' ).addClass( 'disabled' );
				_this.find( '.single_add_to_cart_button' ).attr( 'disabled', 'disabled' ).addClass( 'disabled' );
				_this.find( '.woocommerce-variation-add-to-cart' ).attr( 'disabled', 'disabled' ).addClass( 'disabled' );
				$( '.floating-add-to-cart button' ).attr( 'disabled', 'disabled' ).addClass( 'disabled' );

				// Add notice title
				if( _this.find( '.single_buy_now .notice-tooltip' ).length == 0 ) {
					_this.find( '.single_buy_now' ).addClass( 'wr-notice-tooltip' );
					_this.find( '.single_buy_now' ).append( '<span class="notice-tooltip">' + WR_Data_Js['wr_noice_tooltip'] + '</span>' );
				}
				if( _this.find( '.single_add_to_cart_button .notice-tooltip' ).length == 0 ) {
					_this.find( '.single_add_to_cart_button' ).addClass( 'wr-notice-tooltip' );
					_this.find( '.single_add_to_cart_button' ).append( '<span class="notice-tooltip">' + WR_Data_Js['wr_noice_tooltip'] + '</span>' );
				}
				if( _this.find( '.woocommerce-variation-add-to-cart .notice-tooltip' ).length == 0 ) {
					_this.find( '.woocommerce-variation-add-to-cart' ).addClass( 'wr-notice-tooltip' );
					_this.find( '.woocommerce-variation-add-to-cart' ).append( '<span class="notice-tooltip">' + WR_Data_Js['wr_noice_tooltip'] + '</span>' );
				}
				if( $( '.floating-add-to-cart button .notice-tooltip' ).length == 0 ) {
					$( '.floating-add-to-cart button' ).addClass( 'wr-notice-tooltip' );
					$( '.floating-add-to-cart button' ).append( '<span class="notice-tooltip">' + WR_Data_Js['wr_noice_tooltip'] + '</span>' );
				}
			}
		} );

		$( 'body' ).on( 'hide_variation', 'form.cart.variations_form', function( event, variation, purchasable ) {
			event.preventDefault();
			var _this = $( this );

			// Disabled button
			_this.find( '.single_buy_now' ).attr( 'disabled', 'disabled' ).addClass( 'disabled' );
			_this.find( '.single_add_to_cart_button' ).attr( 'disabled', 'disabled' ).addClass( 'disabled' );
			_this.find( '.woocommerce-variation-add-to-cart' ).attr( 'disabled', 'disabled' ).addClass( 'disabled' );
			$( '.floating-add-to-cart button' ).attr( 'disabled', 'disabled' ).addClass( 'disabled' );

			// Add notice title
			if( _this.find( '.single_buy_now .notice-tooltip' ).length == 0 ) {
				_this.find( '.single_buy_now' ).addClass( 'wr-notice-tooltip' );
				_this.find( '.single_buy_now' ).append( '<span class="notice-tooltip">' + WR_Data_Js['wr_noice_tooltip'] + '</span>' );
			}
			if( _this.find( '.single_add_to_cart_button .notice-tooltip' ).length == 0 ) {
				_this.find( '.single_add_to_cart_button' ).addClass( 'wr-notice-tooltip' );
				_this.find( '.single_add_to_cart_button' ).append( '<span class="notice-tooltip">' + WR_Data_Js['wr_noice_tooltip'] + '</span>' );
			}
			if( _this.find( '.woocommerce-variation-add-to-cart .notice-tooltip' ).length == 0 ) {
				_this.find( '.woocommerce-variation-add-to-cart' ).addClass( 'wr-notice-tooltip' );
				_this.find( '.woocommerce-variation-add-to-cart' ).append( '<span class="notice-tooltip">' + WR_Data_Js['wr_noice_tooltip'] + '</span>' );
			}
			if( $( '.floating-add-to-cart button .notice-tooltip' ).length == 0 ) {
				$( '.floating-add-to-cart button' ).addClass( 'wr-notice-tooltip' );
				$( '.floating-add-to-cart button' ).append( '<span class="notice-tooltip">' + WR_Data_Js['wr_noice_tooltip'] + '</span>' );
			}
		} );

		var buy_now = function( _event ) {
			var redirect = function(val) {
				if ( val.status == 'true' ) {
					var checkout_url = WR_Data_Js.checkout_url;

					if ( parseInt(WR_Data_Js.buy_now_button_action) == 2 ) {
						window.location.href = checkout_url;
					} else {
						// Add method get buy-now
						if ( checkout_url.indexOf( '?' ) > -1 ) {
							checkout_url = checkout_url + '&wr-buy-now=check-out';
						} else {
							checkout_url = checkout_url + '?wr-buy-now=check-out'
						}

						if ( typeof $.fn.magnificPopup != 'undefined' ) {
							$.magnificPopup.close();

							setTimeout( function() {
								$.magnificPopup.open( {
									items: {
										src: checkout_url
									},
									type: 'iframe',
									mainClass: 'mfp-fade wr-buy-now'
								} );
							}, 300 );
						}
					}

					// Update DOM of mini cart
					$( document.body ).trigger( 'updated_wc_div' );
				}

				else if ( val.status == 'false' ) {
					$( 'body > .wr-notice-cart-outer' ).remove();

					var content_notice = '<div class="wr-notice-cart-outer pf ptr error"><div class="wr-notice-cart"><div class="icon-notice"><i class="nitro-icon-cart-9"></i></div><div class="text-notice">' + val.notice + '</div></div></div>';

					$( 'body' ).append( content_notice );

					var close = $( '<span class="close-notice"></span>' ).click( function() {
						$( this ).closest( '.wr-notice-cart-outer' ).removeClass( 'active' );
					} );

					$( 'body .wr-notice-cart' ).prepend( close );

					setTimeout( function() {
						$( 'body > .wr-notice-cart-outer' ).addClass( 'active' );
					}, 10 );

					setTimeout( function() {
						$( 'body > .wr-notice-cart-outer' ).removeClass( 'active' );
					}, 5000 );

					_this.addClass( 'error' );
					floating.addClass( 'error' );
				}

				_this.removeClass( 'loading' );
				_this.prop( 'disabled', false );

				floating.removeClass( 'loading' );
				floating.prop( 'disabled', false );
			};

			// Resume buy now process if requested.
			if ( _event === true ) {
				return redirect({status: 'true'});
			}

			// Prepare event.
			_event.preventDefault();
			_event.stopPropagation();

			// Define function to handle 'Buy Now' action.
			var
			_this = $(this),
			form_cart = _this.closest( 'form' ),
			floating = $( '.floating-add-to-cart .floating_button' ),

			handle_buy_now = function() {
				// Add product to cart first.
				if ( ! isLocalStorageSupported() ) {
					form_cart.append( '<input type="hidden" name="buy_now" value="1" />' );
				}

				window.wr_add_to_cart_ajax( _this, _event, redirect );
			};

			// Clear cart data if checkout type is 'Current Product Only'.
			if ( parseInt(WR_Data_Js.buy_now_checkout_type) == 1 ) {
				_this.prop( 'disabled', true );
				_this.addClass( 'loading' );
				_this.removeClass( 'added error' );

				floating.addClass( 'loading' );
				floating.removeClass( 'error' );

				$.ajax({
					url: WRAjaxURL,
					data: {
						action: 'wr_clear_cart',
						_nonce: _nonce_wr_nitro
					},
					complete: function(res) {
						handle_buy_now();
					}
				})
			} else {
				handle_buy_now();
			}
		};

		if ( parseInt(WR_Data_Js.in_buy_now_process) ) {
			buy_now(true);
		}

		$( 'form.cart .single_buy_now' ).click( buy_now );
		$( 'body' ).on( 'click', '.quickview-modal form.cart .single_buy_now', buy_now );

		$( '.floating-add-to-cart .single_buy_now' ).click( function( e ) {
			e.preventDefault();
			e.stopPropagation();

			$( 'form.cart .single_buy_now' ).trigger( 'click' );
		} );

		/*
		 * [ Check hide button buy now conditional logic for woocommerce-gravityforms-product-addons plugin ] - - - - - - - - - - - - - - - - - - - -
		 */
		$( document ).on( 'gform_pre_conditional_logic', function( e, formId ) {
			setTimeout( function(){
				var check_show = $( '#gform_submit_button_' + formId ).is(':visible');

				if( ! check_show ) {
					var list_button = $( '[id="gform_submit_button_' + formId + '"]' );
					list_button.show().prop( 'disabled', true );
				} else {
					var check_show  = $( '#gform_submit_button_' + formId ).is(':disabled'),
						list_button = $( '[id="gform_submit_button_' + formId + '"]' );

					if( check_show ) {
						list_button.show().prop( 'disabled', false );
					} else {
						list_button.show().prop( 'disabled', true );
					}
				}
			} );
		});

		/*
		 * [ Get notice when remove product in cart ] - - - - - - - - - - - - - - - - - - - -
		 */
		$( 'body' ).on( 'click', '.woocommerce-cart .cart-table .product-remove a', function(){
			var _this        = $(this),
				product_name = _this.closest( 'tr' ).find( '.product-name > a' ).text().trim();

			$( document ).ajaxComplete( function( event, xhr, settings ) {
				var url          = settings.url,
					data_request = ( typeof settings.data != 'undefined' ) ? settings.data : '';

				if ( url.indexOf( '\?remove_item\=' ) != -1 ) {
					var wc_message = $( '.woocommerce-message' ),
						undo_html  = wc_message.find( 'a' )[0].outerHTML,
						message    = WR_Data_Js[ 'removed_notice' ].replace( '%s', '"' + product_name + '"' );

					$( '.woocommerce-message' ).html( message + undo_html );
				}
			});
		} );
	}

	/*
	 * [ Header Builder - Element Sidebar ] - - - - - - - - - - - - - - - - - - -
	 */
	function HB_Element_Sidebar() {
		$( '.hb-sidebar .icon-sidebar' ).click( function() {
			// Add class active
			$(this).closest( '.hb-sidebar' ).addClass( 'active' );
			$( 'html' ).addClass( 'no-scroll' );
		} );

		$( '.hb-sidebar .content-sidebar > .overlay' ).click( function() {
			// Remove class active
			$(this).closest( '.hb-sidebar' ).removeClass( 'active' );
			$( 'html' ).removeClass( 'no-scroll' );
		} );
	}

	/*
	 * [ Header Builder - Element Currency ] - - - - - - - - - - - - - - - - - - -
	 */
	function HB_Element_Currency() {
		$( '.hb-currency .list .item' ).click( function() {
			var _this  = $(this),
			form       = _this.closest( 'form' ),
			input_hidden = form.find( '.currency-value' ),
			value = _this.attr( 'data-id' );


			// Update value for post method
			input_hidden.val( value );

			// Submit form
			form.submit();

		} );
	}

	/*
	 * [ Header Builder - Element Menu ] - - - - - - - - - - - - - - - - - - - -
	 */
	function HB_Element_Menu() {
		$( 'body.wr-desktop' ).on( 'click', '.hb-menu .menu-icon-action', function() {
			var _this = $( this );
			var parent = _this.parents( '.hb-menu' );

			// Add class active for icon
			_this.find( '.wr-burger-scale' ).addClass( 'wr-acitve-burger' );

			/*******************************************************************
			 * Render HTML for effect *
			 ******************************************************************/
			var menu_content = parent.find( '.site-navigator-outer' )[ 0 ].outerHTML;

			// Render menu content
			if ( !$( 'body > .hb-menu-outer' ).length ) {
				$( 'body' ).append( '<div class="hb-menu-outer"></div>' );
			}
			$( 'body > .hb-menu-outer' ).html( menu_content );

			// Calculator menu vertical if height content longer browser height
			setTimeout( function(){
				var height_navigator_outer = $( '.hb-menu-outer .navigator-column' ).height();
				var height_navigator_inner = $( '.hb-menu-outer .navigator-column-inner' ).height();

				if( height_navigator_outer < height_navigator_inner ) {
					$( '.hb-menu-outer' ).addClass( 'hb-menu-scroll' );
				}
			}, 500 );

			// Render overlay
			if ( !$( 'body > .hb-overlay-menu' ).length ) {
				$( 'body' ).append( '<div class="hb-overlay-menu"></div>' );
			}

			/*******************************************************************
			 * Animation *
			 ******************************************************************/
			var layout = _this.attr( 'data-layout' );
			var effect = _this.attr( 'data-effect' );
			var position = _this.attr( 'data-position' );
			var animation = _this.attr( 'data-animation' );
			var wrapper_animation = $( 'body > .wrapper-outer' );
			var sidebar_animation = $( 'body > .hb-menu-outer .sidebar-style' );
			var sidebar_animation_outer = $( 'body > .hb-menu-outer' );
			var sidebar_animation_inner = $( 'body > .hb-menu-outer ul.site-navigator' );
			var overlay = $( 'body > .hb-overlay-menu' );

			var fullscreen = $( 'body > .hb-menu-outer .fullscreen-style' )

			// Add attributes general
			$( 'html' ).addClass( 'no-scroll' );

			if ( layout == 'fullscreen' ) {

				switch ( effect ) {
					case 'none':
						fullscreen.show();

					break;

					case 'fade':
						fullscreen.fadeIn( 100 );

					break;

					case 'scale':
						setTimeout( function() {
							fullscreen.addClass( 'scale-active' );
						}, 100 );

					break;
				}

			} else if ( layout == 'sidebar' ) {

				var width_sidebar = sidebar_animation.innerWidth();

				// Add attributes for overlay
				overlay.attr( 'data-position', position );
				overlay.attr( 'data-animation', animation );

				overlay.fadeIn();

				sidebar_animation.css( 'opacity', 1 );

				var admin_bar = $( '#wpadminbar' );
				if ( admin_bar.length ) {
					sidebar_animation.css( 'top', admin_bar.height() + 'px' );
				} else {
					sidebar_animation.css( 'top', '0px' );
				}

				switch ( position ) {
					case 'left':

						sidebar_animation.css( {
							'visibility': 'visible',
							'left': '-' + width_sidebar + 'px'
						} ).animate( {
							'left': '0px'
						} );

						if ( animation == 'push' || animation == 'fall-down' || animation == 'fall-up' )
							wrapper_animation.css( {
								'position': 'relative',
								'left': '0px'
							} ).animate( {
								'left': width_sidebar + 'px'
							} );

						switch ( animation ) {
							case 'slide-in-on-top':
							break;

							case 'push':
							break;

							case 'fall-down':

								sidebar_animation_inner.css( {
									'position': 'relative',
									'top': '-300px'
								} ).animate( {
									'top': '0px'
								} );

							break;

							case 'fall-up':

								sidebar_animation_inner.css( {
									'position': 'relative',
									'top': '300px'
								} ).animate( {
									'top': '0px'
								} );

							break;
						}

					break;

					case 'right':

						sidebar_animation.css( {
							'visibility': 'visible',
							'right': '-' + width_sidebar + 'px'
						} ).animate( {
							'right': '0px'
						} );

						if ( animation == 'push' || animation == 'fall-down' || animation == 'fall-up' )
							wrapper_animation.css( {
								'position': 'relative',
								'right': '0px'
							} ).animate( {
								'right': width_sidebar + 'px'
							} );

						switch ( animation ) {
							case 'slide-in-on-top':
							break;

							case 'push':
							break;

							case 'fall-down':
								sidebar_animation_inner.css( {
									'position': 'relative',
									'top': '-300px'
								} ).animate( {
									'top': '0px'
								} );
							break;

							case 'fall-up':
								sidebar_animation_inner.css( {
									'position': 'relative',
									'top': '300px'
								} ).animate( {
									'top': '0px'
								} );
							break;
						}
					break;
				}

			}
		} );

		$( 'body' ).on( 'click', '.fullscreen-style .close', function() {

			// Remove class active for icon
			$( '.wr-burger-scale' ).removeClass( 'wr-acitve-burger' );

			var _this = $( this );
			var parent = _this.parents( '.hb-menu-outer' );
			var effect = _this.attr( 'data-effect' );

			switch ( effect ) {
				case 'none':
					parent.remove();
				break;

				case 'fade':
					parent.find( '.site-navigator-outer' ).fadeOut( 300, function() {
						parent.remove();
					} );
				break;

				case 'scale':
					parent.find( '.site-navigator-outer' ).removeClass( 'scale-active' );
					setTimeout( function() {
						parent.remove();
					}, 300 );
				break;
			}

			$( 'html' ).removeClass( 'no-scroll' );
			$( 'body > .wrapper-outer' ).removeAttr( 'style' );
		} )

		$( 'body' ).on( 'click', '.hb-overlay-menu', function() {

			// Remove class active for icon
			$( '.wr-burger-scale' ).removeClass( 'wr-acitve-burger' );

			var _this = $( this );
			var position = _this.attr( 'data-position' );
			var animation = _this.attr( 'data-animation' );
			var wrapper_animation = $( 'body > .wrapper-outer' );
			var sidebar_animation = $( 'body > .hb-menu-outer .sidebar-style' );
			var sidebar_animation_inner = $( 'body > .hb-menu-outer ul.site-navigator' );
			var width_sidebar = sidebar_animation.innerWidth();
			var height_sidebar = sidebar_animation.innerHeight();

			_this.fadeOut();

			// Remove all style
			setTimeout( function() {
				$( 'body > .hb-menu-outer' ).remove();
				_this.remove();
				$( 'html' ).removeClass( 'no-scroll' );
				$( 'body > .wrapper-outer' ).removeAttr( 'style' );
			}, 500 );

			switch ( position ) {
				case 'left':

					sidebar_animation.animate( {
						'left': '-' + width_sidebar + 'px'
					} );

					if ( animation == 'push' || animation == 'fall-down' || animation == 'fall-up' )
						wrapper_animation.animate( {
							'left': '0px'
						} );

					switch ( animation ) {
						case 'slide-in-on-top':
						break;

						case 'push':
						break;

						case 'fall-down':
							sidebar_animation_inner.animate( {
								'top': '-300px'
							} );
						break;

						case 'fall-up':
							sidebar_animation_inner.animate( {
								'top': '300px'
							} );
						break;
					}

				break;

				case 'right':

					sidebar_animation.animate( {
						'right': '-' + width_sidebar + 'px'
					} );

					if ( animation == 'push' || animation == 'fall-down' || animation == 'fall-up' )
						wrapper_animation.animate( {
							'right': '0px'
						} );

					switch ( animation ) {
						case 'slide-in-on-top':
						break;

						case 'push':
						break;

						case 'fall-down':
							sidebar_animation_inner.animate( {
								'top': '-300px'
							} );
						break;

						case 'fall-up':
							sidebar_animation_inner.animate( {
								'top': '300px'
							} );
						break;
					}

				break;

			}
		} );

		$( 'body' ).on( 'click', '.header .menu-more .icon-more', function( e ) {
			var _this = $( this );
			var parent = _this.closest( '.site-navigator-inner' );
			var menu_more = _this.closest( '.menu-more' );
			var nav = parent.find( '.site-navigator' );
			var nav_more = parent.find( '.nav-more' );
			var nav_item_hidden = parent.find( ' > .site-navigator .item-hidden' );
			var index_current = $( '.header .menu-more' ).index( menu_more );
			var element_item = _this.closest( '.element-item' );

			// Remove active element item more
			$( '.header .menu-more:not(:eq(' + index_current + '))' ).removeClass( 'active-more' );

			if ( menu_more.hasClass( 'active-more' ) ) {
				menu_more.removeClass( 'active-more' );
			} else {

				WR_Click_Outside( _this, '.hb-menu', function( e ) {
					menu_more.removeClass( 'active-more' );
				} );

				// Reset
				nav_more.html( '' );
				nav_more.removeAttr( 'style' );

				var width_content_broswer = $( window ).width();
				var nav_info = nav_more[ 0 ].getBoundingClientRect();

				// Get offset
				var offset_option = ( width_content_broswer > 1024 ) ? parseInt( WR_Data_Js[ 'offset' ] ) : 0;

				// Set left search form if hide broswer because small
				if ( width_content_broswer < ( nav_info.right + 5 ) ) {
					var left_nav = ( nav_info.right + 5 + offset_option ) - width_content_broswer;
					nav_more.css( 'left', -left_nav + 'px' );
				} else if ( nav_info.left < ( 5 + offset_option ) ) {
					nav_more.css( 'left', '5px' );
				}

				// Remove margin top when stick or margin top empty
				var margin_top = ( element_item.attr( 'data-margin-top' ) == 'empty' ) ? element_item.attr( 'data-margin-top' ) : parseInt( element_item.attr( 'data-margin-top' ) );
				var menu_more_info = menu_more[ 0 ].getBoundingClientRect();

				if ( _this.closest( '.sticky-row-scroll' ).length || margin_top == 'empty' ) {
					var parent_sticky_info = _this.closest( ( _this.closest( '.sticky-row-scroll' ).length ? '.sticky-row' : '.hb-section-outer' ) )[ 0 ].getBoundingClientRect();
					var offset_bottom_current = menu_more_info.top + menu_more_info.height;
					var offset_bottom_parent = parent_sticky_info.top + parent_sticky_info.height;
					var padding_bottom = parseInt( offset_bottom_parent - offset_bottom_current );
					var offset_top = parseInt( padding_bottom + menu_more_info.height );

					nav_more.css( 'top', offset_top );
				} else if ( margin_top > 0 ) {
					nav_more.css( 'top', ( margin_top + menu_more_info.height ) );
				}

				if ( nav_item_hidden.length ) {
					var nav_item_html = '';
					$.each( nav_item_hidden, function() {
						nav_item_html += $( this )[ 0 ].outerHTML;
					} );
					nav_more.html( '<ul class="animation-' + element_item.attr( 'data-animation' ) + ' ' + nav.attr( 'class' ) + '">' + nav_item_html + '</ul>' );
				}

				setTimeout( function() {
					menu_more.addClass( 'active-more' );
				}, 10 );

			}
		} );

		// Hover normal animation
		if ( $.fn.hoverIntent ) {
			var horizontal_layout_over = function( _this ){
				var style_animate = '';
				var current_info = _this[ 0 ].getBoundingClientRect();
				var width_content_broswer = $( window ).width();
				var offset = ( width_content_broswer > 1024 ) ? parseInt( WR_Data_Js[ 'offset' ] ) : 0;
				var margin_top = ( _this.closest( '.hb-menu' ).attr( 'data-margin-top' ) == 'empty' ) ? _this.closest( '.hb-menu' ).attr( 'data-margin-top' ) : parseInt( _this.closest( '.hb-menu' ).attr( 'data-margin-top' ) );

				if ( _this.hasClass( 'wrmm-item' ) ) { // For megamenu

					var menu_animate = _this.find( ' > .mm-container-outer' );

					// Show menu animate for get attribute
					menu_animate.attr( 'style', 'display:block' );

					var parent_info = _this.closest( '.container' )[ 0 ].getBoundingClientRect();
					var width_content_broswer = $( window ).width();
					var width_parent = parent_info.width;
					var right_parent = parent_info.right;
					var width_megamenu = 0;
					var left_megamenu = 0;
					var width_type = menu_animate.attr( 'data-width' );

					// Full container
					if ( width_type === 'full' ) {
						width_megamenu = width_parent;

						if ( ( width_megamenu + 10 + offset * 2 ) >= width_content_broswer ) {
							width_megamenu = width_parent - 10;
							right_parent -= 5;
						}

						// Full container
					} else if ( width_type === 'full-width' ) {
						width_megamenu = width_content_broswer - 10 - ( offset * 2 );
						right_parent = 5 + offset;

						// Fixed width
					} else {
						width_megamenu = parseInt( width_type ) ? parseInt( width_type ) : width_parent;

						if ( ( width_megamenu + 10 + offset * 2 ) >= width_content_broswer ) {
							width_megamenu = width_content_broswer - 10 - ( offset * 2 );
							right_parent -= 5;
						}
					}

					menu_animate.width( width_megamenu );

					var megamenu_info = menu_animate[ 0 ].getBoundingClientRect();

					/* Convert numbers positive to negative */

					if ( width_type == 'full-width' ) {
						left_megamenu = -( megamenu_info.left - right_parent );
					} else if ( width_type == 'full' ) {
						left_megamenu = ( ( megamenu_info.right - right_parent ) > 0 ) ? -( parseInt( megamenu_info.right - right_parent ) ) : 0;
					} else {
						left_megamenu = ( megamenu_info.right > ( width_content_broswer - 5 - ( offset * 2 ) ) ) ? ( -( megamenu_info.right - ( width_content_broswer - 5 - offset ) ) ) : 0;
					}

					style_animate = {
						display: 'block',
						left: left_megamenu,
						width: width_megamenu
					};

					/** * Set offset top for submenu ** */
					if ( _this.closest( '.sticky-row-scroll' ).length || margin_top == 'empty' ) {
						var parent_sticky_info = _this.closest( ( _this.closest( '.sticky-row-scroll' ).length ? '.sticky-row' : '.hb-section-outer' ) )[ 0 ].getBoundingClientRect();
						var offset_bottom_current = current_info.top + current_info.height;
						var offset_bottom_parent = parent_sticky_info.top + parent_sticky_info.height;
						var padding_bottom = parseInt( offset_bottom_parent - offset_bottom_current );
						var offset_top = parseInt( padding_bottom + current_info.height );
						style_animate[ 'top' ] = offset_top

						if ( _this.children( '.hover-area' ).length == 0 )
							_this.append( '<span class="hover-area" style="height:' + ( offset_top - current_info.height ) + 'px"></span>' );
					} else if ( margin_top > 0 ) {
						style_animate[ 'top' ] = margin_top + current_info.height;

						if ( _this.children( '.hover-area' ).length == 0 )
							_this.append( '<span class="hover-area" style="height:' + margin_top + 'px"></span>' );
					}

					/* Add class col last row */
					var mm_container_width = menu_animate.find( '.mm-container' ).width();
					var width_col = 0;

					$.each( menu_animate.find( '.mm-container > .mm-col' ), function() {
						var _this_col = $( this );
						var width_current = _this_col.outerWidth();

						width_col += width_current;

						_this_col.removeClass( 'mm-last-row' );

						if ( width_col == mm_container_width ) {
							_this_col.addClass( 'mm-last-row' );
							width_col = 0;
						} else if ( width_col > mm_container_width ) {
							_this_col.prev().addClass( 'mm-last-row' );
							width_col = width_current;
						}
					} );

				} else { // For menu normal
					var menu_animate = _this.find( ' > ul.sub-menu' );

					// Show menu animate for get attribute
					menu_animate.attr( 'style', 'display:block' );

					if ( menu_animate.length == 0 )
						return false;

					var megamenu_info = menu_animate[ 0 ].getBoundingClientRect();
					var width_content_broswer = $( window ).width();
					var left_megamenu = Math.round( megamenu_info.right - width_content_broswer + offset );

					if ( _this.hasClass( 'menu-default' ) ) { // For top menu normal

						// Convert numbers positive to negative
						left_megamenu = ( left_megamenu > 0 ) ? ( -left_megamenu - 5 ) : 0;

						/** * Set offset top for submenu in row sticky ** */
						if ( _this.closest( '.sticky-row-scroll' ).length || margin_top == 'empty' ) {

							var parent_sticky_info = _this.closest( ( _this.closest( '.sticky-row-scroll' ).length ? '.sticky-row' : '.hb-section-outer' ) )[ 0 ].getBoundingClientRect();
							var offset_bottom_current = current_info.top + current_info.height;
							var offset_bottom_parent = parent_sticky_info.top + parent_sticky_info.height;
							var padding_bottom = parseInt( offset_bottom_parent - offset_bottom_current );
							var offset_top_menu_animate = parseInt( padding_bottom + current_info.height );

							if ( _this.children( '.hover-area' ).length == 0 )
								_this.append( '<span class="hover-area" style="height:' + ( offset_top_menu_animate - current_info.height ) + 'px"></span>' );
						} else if ( margin_top > 0 ) {
							var offset_top_menu_animate = margin_top + current_info.height;

							if ( _this.children( '.hover-area' ).length == 0 )
								_this.append( '<span class="hover-area" style="height:' + margin_top + 'px"></span>' );
						}
					} else { // For sub menu normal

						var submenu_parent = _this.closest( 'ul' );

						// Get left css current
						var left = parseInt( submenu_parent.css( 'left' ) );

						if ( left < 0 ) { // Show all submenu to left
							var submenu_parent_info = submenu_parent[ 0 ].getBoundingClientRect();
							left_megamenu = ( megamenu_info.width < ( submenu_parent_info.left - offset ) ) ? -megamenu_info.width : megamenu_info.width;
						} else { // Show submenu normal
							if( WR_Data_Js['rtl'] == 1 && _this.hasClass( 'menu-item-lv1' ) ) {
								left_megamenu = -megamenu_info.width;
							} else {
								left_megamenu = ( left_megamenu > 0 ) ? -megamenu_info.width : megamenu_info.width;
							}
						}

						/** * Set top when animate hide because broswer short ** */
						var height_content_broswer = $( window ).height();
						var height_wpadminbar = $( '#wpadminbar' ).length ? ( ( $( '#wpadminbar' ).css( 'position' ) == 'fixed' ) ? $( '#wpadminbar' ).height() : 0 ) : 0;
						var top_menu_animate = height_content_broswer - ( megamenu_info.top + megamenu_info.height ) - offset;
						if ( megamenu_info.height > ( height_content_broswer - 10 - height_wpadminbar - offset ) ) {
							top_menu_animate = -( megamenu_info.top - height_wpadminbar - 5 - offset );
						} else {
							top_menu_animate = top_menu_animate < 5 ? ( top_menu_animate - 5 ) : 0;
						}

					}

					style_animate = {
						display: 'block',
						left: left_megamenu
					};

					// Set offset top for when animate hide because broswer short
					if ( typeof top_menu_animate !== 'undefined' )
						style_animate[ 'top' ] = top_menu_animate;

					// Set offset top for submenu in row sticky
					if ( typeof offset_top_menu_animate !== 'undefined' )
						style_animate[ 'top' ] = offset_top_menu_animate;

				}

				// Set style before run effect
				menu_animate.css( style_animate );

				/***********************************************************
				 * Animation *
				 **********************************************************/

				var animation = _this.closest( '.hb-menu' ).attr( 'data-animation' );

				switch ( animation ) {
					case 'none':
						menu_animate.css( {
							opacity: '1'
						} );
					break;

					case 'fade':
						menu_animate.stop( true, true ).css( {
							pointerEvents: 'none'
						} ).animate( {
							opacity: '1'
						}, 150, function() {
							style_animate[ 'pointerEvents' ] = '';
							menu_animate.css( style_animate );
						} );
					break;

					case 'left-to-right':
						var left_megamenu = parseInt( menu_animate.css( 'left' ) );
						menu_animate.stop( true, true ).css( {
							pointerEvents: 'none',
							left: ( left_megamenu - 50 ) + 'px'
						} ).animate( {
							opacity: '1',
							left: left_megamenu + 'px'
						}, 300, function() {
							style_animate[ 'pointerEvents' ] = '';
							menu_animate.css( style_animate );
						} );
					break;

					case 'right-to-left':
						var left_megamenu = parseInt( menu_animate.css( 'left' ) );
						menu_animate.stop( true, true ).css( {
							pointerEvents: 'none',
							left: ( left_megamenu + 50 ) + 'px'
						} ).animate( {
							opacity: '1',
							left: left_megamenu + 'px'
						}, 300, function() {
							style_animate[ 'pointerEvents' ] = '';
							menu_animate.css( style_animate );
						} );

					break;

					case 'bottom-to-top':
						var top_megamenu = parseInt( menu_animate.css( 'top' ) ); // Get offset top menu_animate
						var left_megamenu = parseInt( menu_animate.css( 'left' ) );
						menu_animate.stop( true, true ).css( {
							pointerEvents: 'none',
							left: left_megamenu + 'px',
							top: ( top_megamenu + 30 ) + 'px'
						} ).animate( {
							opacity: '1',
							top: top_megamenu + 'px'
						}, 300, function() {
							style_animate[ 'pointerEvents' ] = '';
							menu_animate.css( style_animate );
						} );
					break;

					case 'scale':
						var left_megamenu = parseInt( menu_animate.css( 'left' ) );
						menu_animate.css( {
							pointerEvents: 'none',
							left: left_megamenu + 'px',
							transform: 'scale(0.8)'
						} ).animate( {
							opacity: '1',
							transform: 'scale(1)'
						}, 250, function() {
							style_animate[ 'pointerEvents' ] = '';
							menu_animate.css( style_animate );
						} );
					break;
				}

				_this.addClass( 'menu-hover' );
			}

			setTimeout( function(){
				$( '.wr-desktop header.header.horizontal-layout .active-menu' ).each( function(){
					horizontal_layout_over( $(this) );
				} );
			}, 1000 );

			// For horizontal layout
			$( '.wr-desktop header.header.horizontal-layout' ).hoverIntent( {
				over: function() {
					var _this = $( this );

					if( _this.hasClass( 'active-menu' ) ) {
						return;
					}

					horizontal_layout_over( _this );
				},
				out: function() {
					var _this = $( this );

					if( _this.hasClass( 'active-menu' ) ) {
						return;
					}

					_this.children( '.hover-area' ).remove();
					if ( _this.hasClass( 'wrmm-item' ) ) {
						var menu_animate = _this.find( ' > .mm-container-outer' );
					} else {
						var menu_animate = _this.find( 'ul.sub-menu' );
					}

					// Remove style hover-area in row sticky
					_this.find( ' > .menu-item-link .hover-area' ).removeAttr( 'style' );

					/***********************************************************
					 * Animation *
					 **********************************************************/

					var animation = _this.closest( '.hb-menu' ).attr( 'data-animation' );

					switch ( animation ) {
						case 'none':
							_this.removeClass( 'menu-hover' );
							menu_animate.removeAttr( 'style' );

						break;

						case 'fade':
							menu_animate.stop( true, true ).animate( {
								opacity: '0'
							}, 150, function() {
								_this.removeClass( 'menu-hover' );
								menu_animate.removeAttr( 'style' );
							} );

						break;

						case 'left-to-right':
							var left_megamenu = parseInt( menu_animate.css( 'left' ) ) - 50;

							menu_animate.stop( true, true ).animate( {
								opacity: '0',
								left: left_megamenu + 'px'
							}, 300, function() {
								_this.removeClass( 'menu-hover' );
								menu_animate.removeAttr( 'style' );
							} );

						break;

						case 'right-to-left':
							var left_megamenu = parseInt( menu_animate.css( 'left' ) ) + 50;

							menu_animate.stop( true, true ).animate( {
								opacity: '0',
								left: left_megamenu + 'px'
							}, 300, function() {
								_this.removeClass( 'menu-hover' );
								menu_animate.removeAttr( 'style' );
							} );

						break;

						case 'bottom-to-top':
							// Get offset top menu_animate
							var top_megamenu = parseInt( menu_animate.css( 'top' ) ) + 50;

							menu_animate.stop( true, true ).animate( {
								opacity: '0',
								top: top_megamenu + 'px'
							}, 300, function() {
								_this.removeClass( 'menu-hover' );
								menu_animate.removeAttr( 'style' );
							} );

						break;

						case 'scale':
							menu_animate.stop( true, true ).animate( {
								opacity: '0',
								transform: 'scale(0.8)'
							}, 250, function() {
								_this.removeClass( 'menu-hover' );
								menu_animate.removeAttr( 'style' );
							} );

						break;
					}
				},
				timeout: 0,
				sensitivity: 1,
				interval: 0,
				selector: '.site-navigator li.menu-item'
			} );

			var vertical_layout_over = function( _this ) {
				var style_animate = '';
				var width_content_broswer = $( window ).width();
				var is_right_position = 0;

				// Check is right position for menu more
				if ( _this.closest( '.menu-more' ).length == 1 ) {
					var menu_more = _this.closest( '.menu-more' );
					var menu_more_info = menu_more[ 0 ].getBoundingClientRect();
					var menu_more_right = width_content_broswer - menu_more_info.right;

					if ( menu_more_info.left > menu_more_right ) {
						is_right_position = 1;
					}
				} else {
					is_right_position = _this.closest( '.vertical-layout.right-position-vertical' ).length || _this.closest( '.sidebar-style.right-position' ).length;
				}
				var offset = ( width_content_broswer > 1024 ) ? parseInt( WR_Data_Js[ 'offset' ] ) : 0;
				var current_info = _this[ 0 ].getBoundingClientRect();

				/***********************************************************
				 * Animation *
				 **********************************************************/

				var height_content_broswer = $( window ).height();

				if ( _this.hasClass( 'wrmm-item' ) ) { // For megamenu

					var menu_animate = _this.find( ' > .mm-container-outer' );

					// Show menu animate for get attribute
					menu_animate.attr( 'style', 'display:block' );

					var width_megamenu = menu_animate.attr( 'data-width' );

					if ( is_right_position == 1 ) {

						var width_content = current_info.left - offset;

						// Check setting full width
						if ( width_megamenu == 'full' || width_megamenu > width_content ) {
							width_megamenu = width_content - 5;
						}

						/** * Set top when heigh animate greater broswer ** */
						menu_animate.width( width_megamenu );
						var menu_animate_info = menu_animate[ 0 ].getBoundingClientRect();
						var height_wpadminbar = $( '#wpadminbar' ).length ? ( ( $( '#wpadminbar' ).css( 'position' ) == 'fixed' ) ? $( '#wpadminbar' ).height() : 0 ) : 0;
						var top_menu_animate = height_content_broswer - ( menu_animate_info.top + menu_animate_info.height ) - offset;
						if ( menu_animate_info.height > ( height_content_broswer - 10 - height_wpadminbar - offset ) ) {
							top_menu_animate = -( menu_animate_info.top - height_wpadminbar - 5 - offset );
						} else {
							top_menu_animate = top_menu_animate < 5 ? ( top_menu_animate - 5 ) : 0;
						}

						style_animate = {
							display: 'block',
							width: width_megamenu,
							left: -width_megamenu,
							top: top_menu_animate
						};

					} else {
						var width_content = width_content_broswer - current_info.right - offset;

						// Check setting full width
						if ( width_megamenu == 'full' || width_megamenu > width_content )
							width_megamenu = width_content - 5;

						/** * Set top when heigh animate greater broswer ** */
						menu_animate.width( width_megamenu );
						var menu_animate_info = menu_animate[ 0 ].getBoundingClientRect();
						var height_wpadminbar = $( '#wpadminbar' ).length ? ( ( $( '#wpadminbar' ).css( 'position' ) == 'fixed' ) ? $( '#wpadminbar' ).height() : 0 ) : 0;
						var top_menu_animate = height_content_broswer - ( menu_animate_info.top + menu_animate_info.height ) - offset;

						if ( menu_animate_info.height > ( height_content_broswer - 10 - height_wpadminbar - offset ) ) {
							top_menu_animate = -( menu_animate_info.top - height_wpadminbar - 5 - offset );
						} else {
							top_menu_animate = top_menu_animate < 5 ? ( top_menu_animate - 5 ) : 0;
						}

						style_animate = {
							display: 'block',
							width: width_megamenu,
							left: parseInt( current_info.width ),
							top: top_menu_animate
						};

					}
				} else { // For menu normal

					var menu_animate = _this.find( ' > ul.sub-menu' );

					if ( !menu_animate.length ) {
						return false;
					}

					// Show menu animate for get attribute
					menu_animate.attr( 'style', 'display:block' );

					var menu_animate_info = menu_animate[ 0 ].getBoundingClientRect();

					if ( _this.hasClass( 'menu-default' ) ) { // For top menu normal
						if ( is_right_position == 1 ) {
							var left_megamenu = -parseInt( menu_animate_info.width );
						} else {
							var left_megamenu = parseInt( current_info.width );
						}
					} else { // For sub menu normal
						var submenu_parent = _this.closest( 'ul' );
						var submenu_parent_info = submenu_parent[ 0 ].getBoundingClientRect();

						var left_megamenu = ( menu_animate_info.width > ( width_content_broswer - submenu_parent_info.right - offset - 5 ) ) ? -menu_animate_info.width : menu_animate_info.width;

						// Get left css current
						var left = parseInt( submenu_parent.css( 'left' ) );

						if ( left < 0 ) { // Show all submenu to left
							var left_megamenu = ( menu_animate_info.width < submenu_parent_info.left - 5 - offset ) ? -menu_animate_info.width : menu_animate_info.width;
						}
					}

					/** * Set top when heigh animate greater broswer ** */
					var height_wpadminbar = $( '#wpadminbar' ).length ? ( ( $( '#wpadminbar' ).css( 'position' ) == 'fixed' ) ? $( '#wpadminbar' ).height() : 0 ) : 0;
					var top_menu_animate = height_content_broswer - ( menu_animate_info.top + menu_animate_info.height ) - offset;

					if ( menu_animate_info.height > ( height_content_broswer - 10 - height_wpadminbar - offset ) ) {
						top_menu_animate = -( menu_animate_info.top - height_wpadminbar - 5 - offset );
					} else {
						top_menu_animate = top_menu_animate < 5 ? ( top_menu_animate - 5 ) : 0;
					}

					style_animate = {
						display: 'block',
						left: left_megamenu,
						top: top_menu_animate
					};
				}

				var animation_effect = ( _this.closest( '.menu-more' ).length == 1 ) ? _this.closest( '.element-item' ).attr( 'data-animation' ) : _this.closest( '.site-navigator-outer' ).attr( 'data-effect-vertical' );

				if( _this.closest( '.hb-menu-scroll' ).length && _this.hasClass( 'menu-item-lv0' ) ) {
					if( menu_animate_info.height > ( height_content_broswer - 10 - offset*2 - height_wpadminbar ) ) {
						style_animate.top = 5;
					} else if( ( current_info.top + menu_animate_info.height ) > ( height_content_broswer - 5 - offset ) ) {
						style_animate.top = current_info.top - ( ( current_info.top + menu_animate_info.height ) - height_content_broswer );
						style_animate.top -= offset*2 + height_wpadminbar + 5;
					} else {
						style_animate.top = current_info.top - offset - height_wpadminbar;
					}
				}

				// Set style before run effect
				menu_animate.css( style_animate );

				switch ( animation_effect ) {
					case 'none':
						menu_animate.css( {
							visibility: 'visible',
							opacity: '1'
						} );
					break;

					case 'fade':
						menu_animate.stop( true, true ).animate( {
							opacity: '1'
						}, 300, function() {
							menu_animate.css( style_animate );
						} );
					break;

					case 'left-to-right':
						var left_megamenu = parseInt( menu_animate.css( 'left' ) );
						menu_animate.stop( true, true ).css( {
							left: ( left_megamenu - 50 ) + 'px'
						} ).animate( {
							opacity: '1',
							left: left_megamenu + 'px'
						}, 300, function() {
							menu_animate.css( style_animate );
						} );

					break;

					case 'right-to-left':
						var left_megamenu = parseInt( menu_animate.css( 'left' ) );
						menu_animate.stop( true, true ).css( {
							left: ( left_megamenu + 50 ) + 'px'
						} ).animate( {
							opacity: '1',
							left: left_megamenu + 'px'
						}, 300, function() {
							menu_animate.css( style_animate );
						} );
					break;

					case 'bottom-to-top':
						var top_megamenu = parseInt( menu_animate.css( 'top' ) );
						var left_megamenu = parseInt( menu_animate.css( 'left' ) );
						menu_animate.stop( true, true ).css( {
							left: left_megamenu + 'px',
							top: ( top_megamenu + 50 ) + 'px'
						} ).animate( {
							opacity: '1',
							top: top_megamenu + 'px'
						}, 300, function() {
							menu_animate.css( style_animate );
						} );

					break;

					case 'scale':
						menu_animate.css( {
							left: left_megamenu + 'px',
							transform: 'scale(0.8)'
						} ).animate( {
							opacity: '1',
							transform: 'scale(1)'
						}, 300, function() {
							menu_animate.css( style_animate );
						} );
					break;
				}
				_this.addClass( 'menu-hover' );
			};

			setTimeout( function(){
				$( '.vertical-layout .text-layout .animation-vertical-normal .active-menu' ).each( function(){
					vertical_layout_over( $(this) );
				} );
			}, 1000 );

			// For vertical layout
			$( 'body' ).hoverIntent( {
				over: function() {
					var _this = $( this );

					if( _this.hasClass( 'active-menu' ) ) {
						return;
					}

					vertical_layout_over( _this );
				},
				out: function() {
					var _this = $( this );

					if( _this.hasClass( 'active-menu' ) ) {
						return;
					}

					if ( _this.hasClass( 'wrmm-item' ) ) {
						var menu_animate = _this.find( ' > .mm-container-outer' );
					} else {
						var menu_animate = _this.find( 'ul.sub-menu' );
					}

					/***********************************************************
					 * Animation *
					 **********************************************************/

					var animation_effect = ( _this.closest( '.menu-more' ).length == 1 ) ? _this.closest( '.element-item' ).attr( 'data-animation' ) : _this.closest( '.site-navigator-outer' ).attr( 'data-effect-vertical' );

					switch ( animation_effect ) {
						case 'none':
							_this.removeClass( 'menu-hover' );
							menu_animate.removeAttr( 'style' );
						break;

						case 'fade':
							menu_animate.stop( true, true ).animate( {
								opacity: '0'
							}, 300, function() {
								_this.removeClass( 'menu-hover' );
								menu_animate.removeAttr( 'style' );
							} );
						break;

						case 'left-to-right':
							var left_megamenu = parseInt( menu_animate.css( 'left' ) ) - 50;
							menu_animate.stop( true, true ).animate( {
								opacity: '0',
								left: left_megamenu + 'px'
							}, 300, function() {
								_this.removeClass( 'menu-hover' );
								menu_animate.removeAttr( 'style' );
							} );
						break;

						case 'right-to-left':
							var left_megamenu = parseInt( menu_animate.css( 'left' ) ) + 50;
							menu_animate.stop( true, true ).animate( {
								opacity: '0',
								left: left_megamenu + 'px'
							}, 300, function() {
								_this.removeClass( 'menu-hover' );
								menu_animate.removeAttr( 'style' );
							} );
						break;

						case 'bottom-to-top':
							// Get offset top menu_animate
							var top_megamenu = parseInt( menu_animate.css( 'top' ) ) + 50;
							menu_animate.stop( true, true ).animate( {
								opacity: '0',
								top: top_megamenu + 'px'
							}, 300, function() {
								_this.removeClass( 'menu-hover' );
								menu_animate.removeAttr( 'style' );
							} );
						break;

						case 'scale':
							menu_animate.stop( true, true ).animate( {
								opacity: '0',
								transform: 'scale(0.8)'
							}, 300, function() {
								_this.removeClass( 'menu-hover' );
								menu_animate.removeAttr( 'style' );
							} );
						break;
					}

				},
				timeout: 1,
				sensitivity: 6,
				interval: 0,
				selector: '.vertical-layout .text-layout .animation-vertical-normal .site-navigator li.menu-item, .hb-menu-outer .sidebar-style.animation-vertical-normal .site-navigator li.menu-item, .menu-more .nav-more .site-navigator li.menu-item'
			} );
		}

		var element_breadcrumbs = {};

		// Slide animation of vertical layout
		$( 'body' ).on( 'click', '.mm-container .mm-has-children', function( e ) {
			e.preventDefault();

			var _this = $( this );
			var parent_ul = _this.closest( 'ul' );
			var parent_li = _this.closest( 'li' );
			var parent_col = _this.closest( '.mm-col' );
			var siblings_ul = parent_li.find( ' > ul' );

			parent_ul.addClass( 'slide-hide' );
			siblings_ul.addClass( 'slide-show' );

			if ( ! parent_col.find( '.prev-slide' ).length ) {
				parent_col.find( ' > li > ul.sub-menu' ).prepend( '<li class="prev-slide"><i class="fa fa-angle-left"></i></li>' );
			}

			var siblings_ul_top = _this.closest( '.mm-col' ).find( ' > li > ul' );

			var height_siblings_ul = siblings_ul.height();
			var height_sprev_slide = siblings_ul_top.find( '.prev-slide' ).outerHeight();
			var height_set = height_siblings_ul + height_sprev_slide;

			if( siblings_ul_top.height() < height_set ) {
				siblings_ul_top.height( height_set );
			}
		} );

		$( 'body' ).on( 'click', '.mm-container .prev-slide', function( e ) {
			var _this = $( this );
			var parent_ul = _this.closest( '.mm-col' );
			var container = _this.closest( '.mm-container' );
			var show_last = parent_ul.find( '.slide-show:last' ).removeClass( 'slide-show' );
			var hide_last = parent_ul.find( '.slide-hide:last' );

			if ( parent_ul.find( '.slide-hide' ).length == 1 ) {
				_this.closest( 'ul' ).css( 'height', '' );
				_this.remove();
			}

			hide_last.removeClass( 'slide-hide' );
		} );

		// Slide animation of vertical layout
		$( 'body' ).on( 'click', '.vertical-layout .hb-menu .animation-vertical-slide .icon-has-children, .hb-menu-outer .animation-vertical-slide .icon-has-children', function( e ) {
			e.preventDefault();

			var _this = $( this );
			var parent_menu_elment = _this.closest( '.site-navigator-outer' );
			var parent_li = _this.closest( 'li' );
			var children_sub = parent_li.find( ' > ul > li' );
			var parent_ul = _this.closest( 'ul' );
			var children_parent_ul = parent_ul.find( ' > li ' );
			var menu_level = Object.keys( element_breadcrumbs ).length + 1;
			var text_title = _this.closest( 'a' ).find( '.menu_title' ).text();
			var menu_show = parent_li.find( ( parent_li.hasClass( 'wrmm-item' ) ? ' .mm-container-outer ' : ' > ul ' ) );
			var height_menu_show = menu_show.height();
			var menu_outer = parent_menu_elment.find( '.site-navigator' );
			var height_menu_outer = menu_outer.height();
			var list_breadcrumbs = '';

			// Set height for menu if content hide
			if ( height_menu_show > height_menu_outer ) {
				menu_outer.attr( 'style', 'height:' + height_menu_show + 'px;' );
			}

			parent_li.addClass( 'active-slide' ).addClass( 'slide-level-' + menu_level );

			// Add class no padding icon if not children
			if ( !parent_li.find( ' > ul > li.menu-item-has-children' ).length )
				parent_li.find( ' > ul ' ).addClass( 'not-padding-icon' );

			// Hide menu
			if ( children_parent_ul.length ) {
				var length_slide = children_parent_ul.length;
				children_parent_ul.each( function( key, val ) {
					setTimeout( function() {
						$( val ).addClass( 'slide-left' );

						// To last
						if ( length_slide == ( key + 1 ) ) {
							// Animation for megamenu
							if ( parent_li.hasClass( 'wrmm-item' ) ) {
								parent_li.addClass( 'slide-normal' );
							}
						}

					}, 100 * key );
				} );
			}
			;

			// Show menu
			if ( children_sub.length && !parent_li.hasClass( 'wrmm-item' ) ) {
				setTimeout( function() {
					children_sub.each( function( key, val ) {
						setTimeout( function() {
							$( val ).addClass( 'slide-normal' );
						}, 100 * key );
					} );
				}, 100 );
			}
			;

			/** * Add breadcrumbs ** */

			// Add item to list breadcrumbs
			element_breadcrumbs[ menu_level ] = text_title;

			// Show breadcrumbs
			parent_menu_elment.find( '.menu-breadcrumbs-outer' ).addClass( 'show-breadcrumbs' );

			// Remove all item breadcrumbs old
			parent_menu_elment.find( '.item-breadcrumbs' ).remove();

			if ( Object.keys( element_breadcrumbs ).length ) {
				$.each( element_breadcrumbs, function( key, val ) {
					list_breadcrumbs += '<div class="element-breadcrumbs item-breadcrumbs"><i class="fa fa-long-arrow-right"></i><span class="title-breadcrumbs" data-level="' + key + '">' + val + '</span></div>';
				} );
			}

			// Add all new item breadcrumbs
			parent_menu_elment.find( '.menu-breadcrumbs' ).append( list_breadcrumbs );

			// Set width breadcrumbs for fullscreen style
			if ( parent_menu_elment.hasClass( 'fullscreen-style' ) ) {

				var navigator_inner_info = _this.closest( '.navigator-column-inner' )[ 0 ].getBoundingClientRect();
				var width_content_broswer = $( window ).width();
				var width_breadcrumbs = width_content_broswer - navigator_inner_info.left;

				parent_menu_elment.find( '.menu-breadcrumbs-outer' ).css( 'width', parseInt( width_breadcrumbs ) );
				_this.closest( '.navigator-column-inner' ).width( navigator_inner_info.width );

			}
		} );

		// Breadcrumbs slide animation of vertical layout
		$( 'body' ).on( 'click', '.vertical-layout .menu-breadcrumbs .element-breadcrumbs .title-breadcrumbs, .hb-menu-outer .animation-vertical-slide .menu-breadcrumbs .element-breadcrumbs .title-breadcrumbs', function() {

			var _this = $( this );
			var level = _this.attr( 'data-level' );
			var parent_top = _this.closest( '.site-navigator-outer' );
			var length_breadcrumbs = Object.keys( element_breadcrumbs ).length;
			var parent_breadcrumbs = _this.closest( '.menu-breadcrumbs' );

			// Disable item breadcrumbs last
			if ( level == length_breadcrumbs ) {
				return;
			}

			if ( parent_top.find( '.slide-level-' + length_breadcrumbs + '.wrmm-item' ).length ) {
				parent_top.find( '.slide-level-' + length_breadcrumbs + '.wrmm-item' ).removeClass( 'slide-normal' );
			} else {
				// Remove animate last level
				parent_top.find( '.slide-level-' + length_breadcrumbs + '> ul > li' ).each( function( key, val ) {
					setTimeout( function() {
						$( val ).removeClass( 'slide-normal' );
					}, 100 * key );
				} );
			}

			if ( level == 'all' ) {
				setTimeout( function() {
					var length_slide = parent_top.find( '.site-navigator > li' ).length;
					parent_top.find( '.site-navigator > li' ).each( function( key, val ) {
						setTimeout( function() {
							$( val ).removeClass( 'slide-left' );

							// To last
							if ( length_slide == ( key + 1 ) ) {

								// Conver to heigh menu normal
								$( val ).closest( '.site-navigator' ).removeAttr( 'style' );

								/** * Remove all class releated ** */
								parent_top.find( '.slide-normal' ).removeClass( 'slide-normal' );
								parent_top.find( '.slide-left' ).removeClass( 'slide-left' );
								for ( var i = 1; i <= length_breadcrumbs; i++ ) {
									parent_top.find( '.slide-level-' + i ).removeClass( 'slide-level-' + i );
								}
								;

								parent_top.find( '.active-slide' ).removeClass( 'active-slide' );

								// Hide breadcrumbs
								_this.closest( '.menu-breadcrumbs-outer' ).removeClass( 'show-breadcrumbs' );

								setTimeout( function() {
									// Remove item breadcrumbs
									element_breadcrumbs = {};
									parent_breadcrumbs.find( '.item-breadcrumbs' ).remove();
								}, 300 );

							}

						}, 100 * key );
					} );
				}, 100 );

			} else {
				setTimeout( function() {
					var length_slide = parent_top.find( '.slide-level-' + level + ' > ul > li' ).length;
					parent_top.find( '.slide-level-' + level + ' > ul > li' ).each( function( key, val ) {
						setTimeout( function() {
							$( val ).removeClass( 'slide-left' );

							// To last
							if ( length_slide == ( key + 1 ) ) {

								// Remove class releated
								parent_top.find( '.slide-level-' + level + ' ul ul .slide-normal' ).removeClass( 'slide-normal' );
								parent_top.find( '.slide-level-' + level + ' ul ul .slide-left' ).removeClass( 'slide-left' );
								for ( var i = level; i <= length_breadcrumbs; i++ ) {
									if ( i != level ) {
										parent_top.find( '.slide-level-' + i ).removeClass( 'slide-level-' + i );
									}
								};

								parent_top.find( '.slide-level-' + level + ' .active-slide' ).removeClass( 'active-slide' );

								// Remove item breadcrumbs
								for ( var i = level; i <= length_breadcrumbs; i++ ) {
									if ( i != level ) {
										delete element_breadcrumbs[ i ];
										parent_breadcrumbs.find( '.title-breadcrumbs[data-level="' + i + '"]' ).parent().remove();
									}
								};

							}

						}, 100 * key );
					} );
				}, 100 );

			}
		} );

		// Accordion animation of vertical layout
		$( 'body' ).on( 'click', '.vertical-layout .hb-menu .animation-vertical-accordion .icon-has-children, .hb-menu-outer .animation-vertical-accordion .icon-has-children', function( e ) {
			e.preventDefault();

			var _this = $( this );
			var parent_li = _this.closest( 'li' );

			if ( parent_li.hasClass( 'active-accordion' ) ) {
				parent_li.removeClass( 'active-accordion' );
				if ( parent_li.find( ' > .mm-container-outer' ).length ) {
					parent_li.find( ' > .mm-container-outer' ).stop( true, true ).slideUp( 300 );
				} else {
					parent_li.find( ' > .sub-menu' ).stop( true, true ).slideUp( 300 );
				}
			} else {
				parent_li.addClass( 'active-accordion' );
				if ( parent_li.find( ' > .mm-container-outer' ).length ) {
					parent_li.find( ' > .mm-container-outer' ).stop( true, true ).slideDown( 300 );
				} else {
					parent_li.find( ' > .sub-menu' ).stop( true, true ).slideDown( 300 );
				}
			}
		} );

		function get_width_menu_center( element ){
			var width_all = 0;

			$.each( element, function(){
				var _this = $(this);
				if( _this.hasClass( 'hb-menu' ) && _this.hasClass( 'text-layout' ) ) {
					var width = ( _this.outerWidth( true ) - _this.find( '.site-navigator-outer' ).width() ) + 47;
					width_all += width;
				} else {
					var width = _this.outerWidth( true );
					width_all += width;
				}
			} );

			return width_all;
		}

		function calc_element_center( el_prev, spacing_average, center_element ){
			var width_all_el = 0;
			var margin_left = 0;

			$.each( el_prev, function(){
				var _this = $(this);
				var width = _this.outerWidth( true );
				width_all_el += width;
			} );

			if( width_all_el < spacing_average ) {
				margin_left = spacing_average - width_all_el;
			}

			if( margin_left ) {
				var lits_flex = center_element.prevAll( '.hb-flex' );

				if( lits_flex.length ) {
					var width_flex = parseInt( margin_left/lits_flex.length )
					lits_flex.width( width_flex );
					lits_flex.addClass( 'not-flex' );
				} else {
					center_element.css( 'marginLeft', ( margin_left + parseInt( center_element.css( 'marginLeft' ) ) ) );
				}
			}
		}

		function resize_menu() {
			// Each rows
			$.each( $( '.horizontal-layout .hb-section-outer' ), function() {
				var row = $(this);
				var menu_row = row.find( '.hb-menu.text-layout' );
				var center_element = row.find( '.element-item.center-element' );
				var list_flex = row.find( '.hb-flex' );

				// Set center element and menu more has menu element in row
				if ( menu_row.length ) {

					/* Reset */
					menu_row.find( '.site-navigator > .menu-item' ).removeClass( 'item-hidden' );
					menu_row.find( '.menu-more' ).remove();
					row.find( '.center-element' ).removeAttr( 'style' );
					list_flex.removeAttr( 'style' );
					list_flex.removeClass( 'not-flex' );

					// Menu element is center element
					if ( center_element.hasClass( 'hb-menu' ) && center_element.hasClass( 'text-layout' ) ) {

						var parent = row.find( '.hb-section > .container' );
						var parent_info = parent[ 0 ].getBoundingClientRect();
						var width_parent = parent_info.width - ( parseInt( parent.css( 'borderLeftWidth' ) ) + parseInt( parent.css( 'borderRightWidth' ) ) + parseInt( parent.css( 'paddingLeft' ) ) + parseInt( parent.css( 'paddingRight' ) ) );

						var prev_menu = center_element.prevAll( ':not(".hb-flex")' );
						var next_menu = center_element.nextAll( ':not(".hb-flex")' );
						var width_prev_menu = get_width_menu_center( prev_menu );
						var width_next_menu = get_width_menu_center( next_menu );
						var width_spacing_center = ( width_prev_menu > width_next_menu ) ? width_prev_menu : width_next_menu;
						var width_menu_center = center_element.outerWidth( true );
						var width_calc_center = width_parent - ( width_spacing_center * 2 );

						if( width_menu_center >= width_calc_center ) {
							resize_menu_list( center_element, width_calc_center );
						}

						var spacing_average = parseInt( ( width_parent - center_element.outerWidth( true ) ) / 2 );

						resize_menu_list( prev_menu, spacing_average );
						resize_menu_list( next_menu, spacing_average );

						// Set margin left for element center
						calc_element_center( prev_menu, spacing_average, center_element );

					// Menu element isn't center element but has center element
					} else if ( center_element.length ) {
						/* Reset */
						center_element.removeAttr( 'style' );

						var parent = row.find( '.hb-section > .container' );
						var parent_info = parent[ 0 ].getBoundingClientRect();
						var width_parent = parent_info.width - ( parseInt( parent.css( 'borderLeftWidth' ) ) + parseInt( parent.css( 'borderRightWidth' ) ) + parseInt( parent.css( 'paddingLeft' ) ) + parseInt( parent.css( 'paddingRight' ) ) );
						var spacing_average = parseInt( ( width_parent - center_element.outerWidth( true ) ) / 2 );
						var prev_menu = center_element.prevAll( ':not(".hb-flex")' );
						var next_menu = center_element.nextAll( ':not(".hb-flex")' );

						resize_menu_list( prev_menu, spacing_average );
						resize_menu_list( next_menu, spacing_average );

						// Set margin left for element center
						calc_element_center( prev_menu, spacing_average, center_element );

					// Haven't center element
					} else {
						var parent = row.find( '.hb-section > .container' );
						var parent_info = parent[ 0 ].getBoundingClientRect();
						var width_parent = parent_info.width - ( parseInt( parent.css( 'borderLeftWidth' ) ) + parseInt( parent.css( 'borderRightWidth' ) ) + parseInt( parent.css( 'paddingLeft' ) ) + parseInt( parent.css( 'paddingRight' ) ) );

						resize_menu_list( row.find( '.element-item:not(.hb-flex)' ), width_parent );
					}

				// Set center element not menu element in row
				} else if ( center_element.length ) {
					/* Reset */
					row.find( '.center-element' ).removeAttr( 'style' );
					row.find( '.hb-flex' ).removeAttr( 'style' );
					list_flex.removeClass( 'not-flex' );

					var parent = row.find( '.hb-section > .container' );
					var parent_info = parent[ 0 ].getBoundingClientRect();
					var width_parent = parent_info.width - ( parseInt( parent.css( 'borderLeftWidth' ) ) + parseInt( parent.css( 'borderRightWidth' ) ) + parseInt( parent.css( 'paddingLeft' ) ) + parseInt( parent.css( 'paddingRight' ) ) );

					var spacing_average = parseInt( ( width_parent - center_element.outerWidth( true ) ) / 2 );
					var prev_menu = center_element.prevAll( ':not(".hb-flex")' );

					// Set margin left for element center
					calc_element_center( prev_menu, spacing_average, center_element );
				}
			} );
		}

		function resize_menu_list( list_element, width_parent ) {
			var list_menu = [];
			var el_not_menu_flex = [];

			$.each( list_element, function() {
				var _this = $( this );
				if ( _this.hasClass( 'hb-menu' ) && _this.hasClass( 'text-layout' ) ) {
					list_menu.push( _this );
				} else {
					el_not_menu_flex.push( _this );
				}
			} )

			var count_menu = list_menu.length;

			$.each( el_not_menu_flex, function() {
				width_parent -= $( this ).outerWidth( true );
			} );

			var width_rest = parseInt( width_parent / count_menu );
			var width_rest_long = 0;
			var is_plus_rest_long = false;
			var menus_more = [];

			// Plus for width rest if menu not exceeds
			var i = 0;
			$.each( list_menu, function() {
				var width_menu = $( this ).outerWidth( true );
				if ( width_menu < width_rest ) {
					width_rest_long += width_rest - width_menu;
				} else {
					menus_more.push( i );
				}
				i++;
			} );

			width_rest += parseInt( width_rest_long / menus_more.length );

			$.each( menus_more, function( key, val ) {
				var _this = $( list_menu[ val ] );
				var menu_items = _this.find( '.site-navigator > .menu-item' );

				if ( ! menu_items.length ) {
					return;
				}

				var width_this = _this.outerWidth( true );
				var width_outer = _this.find( '.site-navigator-outer' ).width();
				var width_rest_item = width_rest - ( ( width_this - width_outer ) + 52 );
				var width_menu_items = 0;
				var show_menu_more = false;

				$.each( menu_items, function( key, val ) {
					width_menu_items += $( this ).outerWidth( true );
					if ( width_menu_items >= width_rest_item ) {
						$( this ).addClass( 'item-hidden' );
						show_menu_more = true;
					}
					;
				} );

				if ( show_menu_more ) {
					_this.find( '.site-navigator-inner' ).append( '<div class="menu-more"><div class="icon-more"><span class="wr-burger-menu"></span><i class="fa fa-caret-down"></i></div><div class="nav-more"></div></div>' );
				}
			} );
		}

		resize_menu();

		$( window ).resize( _.debounce( function() {
			resize_menu();
		}, 300 ) );
	}

	/*
	 * [ Heder Builder - Sticky Row ] - - - - - - - - - - - - - - - - - - - - - -
	 */
	function HB_Sticky_Row() {
		function call_header_sticky_row() {
			var _this = $( '.header .sticky-row' );
			var parent = _this.closest( '.hb-section-outer' );
			var parent_height = parent.height();
			var parent_offset = parent.offset();
			var parent_offset_bottom = parent_offset.top + parent_height;
			var top_scroll = $( window ).scrollTop();
			var last_scroll_top = 0;
			var admin_bar_height = 0;
			var wpadminbar = $( '#wpadminbar' );
			var search_element = $( '.header .sticky-row .hb-search.dropdown' );
			var shopping_cart_element = $( '.header .sticky-row .hb-cart.dropdown' );
			var menu_more = $( '.wr-desktop .header .sticky-row .menu-more' );
			var menu_element = $( '.wr-desktop .header.horizontal-layout .sticky-row .hb-menu.text-layout .menu-item' );

			if ( wpadminbar.length ) {
				var width_content_broswer = $( window ).width();
				if( width_content_broswer > 600 ) {
					admin_bar_height = wpadminbar.height();
					_this.css( 'top', admin_bar_height + 'px' );
				}
			}

			parent.height( parent_height + 'px' ); // Set height parent

			if ( top_scroll > parent_offset_bottom ) {
				_this.addClass( 'sticky-row-scroll' );
			}

			$.function_rotate_device.sticky_row = function() {

				/* Reset style for get height */
				_this.removeClass( 'sticky-row-scroll' ).removeClass( 'sticky-row-scroll-down' ).removeClass( 'sticky-row-scroll-up' );
				parent.removeAttr( 'style' );

				parent_height = parent.height();

				parent.height( parent_height + 'px' );

				parent_offset = parent.offset();
				parent_offset_bottom = parent_offset.top + parent_height;
			}

			var number_scroll = 106;

			var anim_frame_handler_hidden = function() {
				top_scroll = $( window ).scrollTop();

				var check_scroll_up = _this.hasClass( 'sticky-row-scroll-up' );
				var check_scrolling = _this.hasClass( 'sticky-row-scroll' );

				if ( ! check_scroll_up && !check_scrolling && ( top_scroll > ( parent_offset_bottom - admin_bar_height ) ) ) {
					search_element.removeClass( 'active-dropdown' );
					shopping_cart_element.removeClass( 'active-dropdown' );
					menu_more.removeClass( 'active-more' );
					menu_element.trigger( 'mouseleave' );
				}

				if ( top_scroll > parent_offset_bottom ) {
					if ( !check_scrolling ) {
						_this.addClass( 'sticky-row-scroll' );
					}

					// Animation hide
					if ( top_scroll < ( parent_offset_bottom + 150 ) ) {

						if( number_scroll < 106 ) {
							number_scroll += 6;
							_this.css( 'transform', 'translateY(-' + number_scroll + '%)' );
						}

						// Animation down
					} else if ( top_scroll > last_scroll_top ) {
						if( number_scroll < 106 ) {
							number_scroll += 6;
							_this.css( 'transform', 'translateY(-' + number_scroll + '%)' );
						}

						var check_scroll_down = _this.hasClass( 'sticky-row-scroll-down' );
						if ( ! check_scroll_down ) {
							_this.addClass( 'sticky-row-scroll-down' ).removeClass( 'sticky-row-scroll-up' );
						}

						// Animation up
					} else {
						if( number_scroll > 0 ) {
							number_scroll -= 6;
							_this.css( 'transform', 'translateY(-' + number_scroll + '%)' );
						}

						if ( ! check_scroll_up ) {
							_this.addClass( 'sticky-row-scroll-up' ).removeClass( 'sticky-row-scroll-down' );
						}
					}

					// Reset style to nomal
				} else {
					if ( check_scrolling ) {
						_this.removeClass( 'sticky-row-scroll' ).removeClass( 'sticky-row-scroll-up' ).removeClass( 'sticky-row-scroll-down' );
						_this.css( 'transform', '' );
						number_scroll = 106;
					}
				}
				last_scroll_top = top_scroll;
			};

			var anim_frame_handler_normal = function() {
				top_scroll = $( window ).scrollTop();

				if( top_scroll > ( parent_offset.top - admin_bar_height ) ) {
					_this.addClass( 'sticky-row-scroll' );
				} else {
					_this.removeClass( 'sticky-row-scroll' );
				}
			};

			if( $( '.header .sticky-row' ).hasClass( 'sticky-normal' ) ) {
				$( window ).scroll( anim_frame_handler_normal );
				parent.find( '.hb-section' ).height( parent_height + 'px' ); // Set height parent
			} else {
				$( window ).scroll( anim_frame_handler_hidden );
			}

		}

		/*
		 * [ Call Sticky function row element header builder ] - - - - - - - - - - -
		 */
		if ( $( '.header .sticky-row' ).length ) {

			// Set time out of element menu more calculate
			setTimeout( function() {
				$( '.header' ).WR_ImagesLoaded( function() {
					call_header_sticky_row();
				} );
			}, 50 );
		}
	}

	/*
	 * [ Page Loader ] - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	 */
	function WR_PageLoader() {
		var el = $( '.pageloader' );

		if( ! el.length ) {
			return;
		}

		// Prevent Safari loading from cache when back button is clicked
		$( window ).on( 'pageshow', function(event) {
		    if ( event.originalEvent != undefined && event.originalEvent.persisted ) {
		        el.hide();
				el.children().hide();
		    }
		});

		$( window ).on('beforeunload', function() {
			el.fadeIn( 300, function() {
				el.children().fadeIn( 300 )
			});
		});

		el.fadeOut( 800 );
		el.children().fadeOut( 'slow' );
	}

	/*
	 * [ Scroll animate when click to link ] - - - - - - - - - - - - - - - - - - -
	 */
	function WR_Scroll_Animated() {
		var list_id = [];
		var menu_li = $( '.header .site-navigator > li' );
		var adminBar = $( '#wpadminbar' ).outerHeight() || 0;

		$( '.wr-scroll-animated, .wr-scroll-animated *, .menu-item-link' ).click( function() {
			var elm = this.nodeName !== 'A' ? this.closest('a') : this

			if ( location.pathname.replace( /^\//, '' ) == elm.pathname.replace( /^\//, '' ) && location.hostname == elm.hostname ) {
				if ( elm.hash.length <= 1 ) {
					return;
				}

				var target = $( elm.hash );
				target = target.length ? target : $( '[name=' + elm.hash.slice( 1 ) + ']' );

				if ( target.length ) {
					var width_content_broswer = $( window ).width();
					var offset = ( width_content_broswer > 1024 ) ? parseInt( WR_Data_Js.offset ) : 0;
					var sticky_row = $( '.header .sticky-row' );
					var height_sticky = parseInt( sticky_row.attr( 'data-height' ) || sticky_row.height() || 0 );

					$( 'html,body' ).stop().animate( {
						scrollTop: target.offset().top - adminBar - offset + 1 - height_sticky + 'px'
					}, 1200 );

					return false;
				}
			}
		} );

		$.each( $( '.header .site-navigator > li > a' ), function(){
			var _this = $(this);
			var href = _this.attr( 'href' );

			if ( href != undefined ){
				if( href.match(/^#/gi) != null ) {
					var el_current = $( href );

					if( el_current.length ) {
						list_id.push( el_current );
					}
				}
			}
		} );

		var frame_handler = _.debounce( function() {
			var _this = $(this);
			var scrollTop = _this.scrollTop();

			$.each( list_id, function(){
				var _this_id = $(this);
				var offset = _this_id.offset();
				var this_height = _this_id.outerHeight();
				var href_id = _this_id.attr( 'id' );
				var height_row_sticky = $( '.header .sticky-normal.sticky-row-scroll' ).height();

				if( scrollTop >= ( offset.top - adminBar - height_row_sticky ) && scrollTop <= ( offset.top + this_height - adminBar - height_row_sticky ) ) {
					var menu_current = $( '.header .site-navigator > li > a[href="#' + href_id + '"]' );
					var li_menu_current = menu_current.closest( 'li' );

					menu_li.removeClass( 'current-menu-ancestor' ).removeClass( 'current-menu-item' );
					li_menu_current.addClass( 'current-menu-item' );

					return;
				}
			} );
		}, 10 );

		var scroll_handler = function() {
			requestAnimationFrame( frame_handler );
		};

		if( list_id.length ) {
			$( window ).scroll( scroll_handler );
		}
	}

	/*
	 * [ Scroll animate when click to link ] - - - - - - - - - - - - - - - - - - -
	 */
	function WR_Sidebar_Sticky() {
		var sidebar = $( '.primary-sidebar-sticky' );

		if( ! sidebar.length ) {
			return;
		}

		var window_info       = $( window ),
			sidebar_inner     = $( '.primary-sidebar-inner' ),
			mgt_sidebar       = parseInt( sidebar.css( 'marginTop' ) ),
			mgt_sidebar_inner = parseInt( sidebar_inner.css( 'marginTop' ) ),
			adminbar          = $( '#wpadminbar' ),
			sticky_row        = $( '.header .sticky-row' ),
			height_sticky_row = sticky_row.attr( 'data-height' ),
			height_bar        = adminbar.length ? parseInt( adminbar.height() ) : 0,
			mgb_last_widget   = parseInt( sidebar.find( '.widget:last' ).css( 'marginBottom' ) ),
			sidebar_0         = false,
			sidebar_inner_0   = false;

		if( mgt_sidebar_inner == 0 ) {
			if( mgt_sidebar == 0 ) {
				sidebar.addClass( 'fixed-margin' );
				mgt_sidebar = 30;
				sidebar_inner_0 = true;
			}
		} else {
			mgt_sidebar = 0;
			sidebar_0   = true;
		}

		sidebar_inner.width( sidebar_inner.width() );

		var frame_handler = _.debounce( function() {
			var width_broswer = window_info.width();

			if( width_broswer <= 785 ) {
				sidebar.removeClass( 'fixed-bottom fixed-top' );
				return;
			}

			if( width_broswer <= 1008 ) {
				var offset_body = 0;
			} else {
				var offset_body = parseInt( WR_Data_Js[ 'offset' ] );
			}

			var height_sidebar_inner = sidebar_inner.height(),
				oheight_sidebar_inner = sidebar_inner.outerHeight( true ),
				height_sidebar       = sidebar.height(),
				height_broswer       = window_info.height(),
				height_sticky        = sticky_row.length ? ( ( height_sticky_row != 0 ) ? parseInt( height_sticky_row ) : sticky_row.height() ) : 0,
				height_more          = height_bar + height_sticky + mgt_sidebar + offset_body,
				height_compare       = height_broswer - height_more - offset_body;

			if( height_sidebar_inner > height_compare || oheight_sidebar_inner >= height_sidebar ) {
				sidebar.removeClass( 'fixed-bottom fixed-top' );
				return;
			}

			var _this          = $(this),
				scrollTop      = _this.scrollTop(),
				offset_sidebar = sidebar.offset(),
				scrollBottom   = scrollTop + height_broswer,
				height_sidebar = sidebar.height(),
				bottom_compare = offset_sidebar.top + height_sidebar + ( height_broswer - height_sidebar_inner ) - height_more,
				top_sidebar_inner = sidebar_inner_0 ? ( height_more - mgt_sidebar ) : height_more;

			if( sidebar_0 ) {
				bottom_compare -= mgt_sidebar_inner;
			}

			// Set css top
			sidebar_inner.css( 'top', top_sidebar_inner );

			// Top fixed
			if( scrollTop > ( offset_sidebar.top - height_more ) && scrollBottom < bottom_compare ) {
				if( sidebar.hasClass( 'fixed-bottom' ) ) {
					sidebar.removeClass( 'fixed-bottom' );
				}
				sidebar.addClass( 'fixed-top' );
				sidebar.addClass( 'fixing' );

			// Bottom fixed
			} else if( scrollBottom > bottom_compare ) {
				if( sidebar.hasClass( 'fixed-top' ) ) {
					sidebar.removeClass( 'fixed-top' );
				}
				sidebar.addClass( 'fixed-bottom' );
				sidebar.addClass( 'fixing' );

			// Normal
			} else {
				sidebar.removeClass( 'fixed-bottom fixed-top fixing' );
			}

		}, 10 );

		var scroll_handler = function() {
			requestAnimationFrame( frame_handler );
		};

		$( window ).scroll( scroll_handler );

		scroll_handler();
	}

	/*
	 * [ Init nivo lightbox ] - - - - - - - - - - - - - - - - - - -
	 */
	$.WR.Lightbox = function() {
		// Check if NivoLightbox plugin for jQuery is loaded before setting up modal.
		if ( typeof $.fn.nivoLightbox == 'undefined' ) {
			return setTimeout( function() {
				$.WR.Lightbox();
			}, 100 );
		}

		$( 'a[data-lightbox^="nivo"]' ).each( function() {
			if ( ! $( this ).data( 'nivo-lightbox-initialized' ) ) {
				$( this ).nivoLightbox( {
					effect: 'fall',
					keyboardNav: true,
					clickOverlayToClose: true,
				} );

				$( this ).data( 'nivo-lightbox-initialized', true );
			}
		} );
	};

	/*
	 * [ Pagination loadmore & Infinite scroll ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Item_Loadmore() {
		// Pagination for loadmore & infinite layout
		if ( $( '.pagination[layout]' ).length > 0 ) {
			var
			totalPage = parseInt( $( '.pagination' ).find( '.page-ajax.enable' ).attr( 'data-page' ) ),
			layout = $( '.pagination[layout]' ).attr( 'layout' ),
			layoutStyle = $( '.pagination[layout-style]' ).attr( 'layout-style' ),
			container = $( '.' + ( 'masonry' == layoutStyle ? 'wr-nitro-masonry' : 'products' ) ),
			product_number = $( '.products .product' ).length,
			button = $( '.page-ajax' ),
			link = $( button ).find( 'a' ).attr( 'href' ),
			content = '.products',
			anchor = '.page-ajax a',
			next = $( anchor ).attr( 'href' ),
			isLoading = false,
			i = 2,

			// Define function to init isotope.
			init_isotope = function() {
				if ( container.length ) {
					container.WR_ImagesLoaded( function() {
						if ( 'masonry' == layoutStyle ) {
							container.isotope( {
								itemSelector: '.product',
								masonry: {
									columnWidth: '.grid-sizer',
								}
							} );
						} else {
							container.isotope( {
								itemSelector: '.product',
								layoutMode: 'fitRows',
							} );
						}
					} );
				}
			},

			// Define function to get next page content.
			getContentAjax = function() {
				// If WOOF - WooCommerce Products Filter is activated, simply trigger a click event on the appropriated pagination link.
				if (window.location.href.indexOf('?swoof=1&') > -1 && typeof woof_get_submit_link == 'function') {
					woof_ajax_page_num = i;
					link = woof_get_submit_link();
				}

				// Verify link...
				var test = new RegExp('(\/page\/' + i + '\/|&paged=' + i + ')', 'i');

				if ( ! link.match(test) ) {
					link += '&paged=' + i;
				}

				$.get(link, function(data) {
					var new_content = $( content, data ).wrapInner( '' ).html(), new_element = $( content, data ).find( '.product' );

					product_number += new_element.length;

					$( '.woocommerce-result-count span' ).html( product_number );

					$( new_content ).WR_ImagesLoaded( function() {
						next = $( anchor, data ).attr( 'href' );

						container.append( new_element );

						if ( container.data('isotope') ) {
							container.isotope( 'appended', new_element );
						} else {
							init_isotope();
						}
					} );

					$( anchor ).text( '...' );

					if ( totalPage > i ) {
						// Change the next URL
						if ( WR_Data_Js['permalink'] == 'plain' ) {
							var link_next = link.replace( /paged=+[0-9]+/gi, 'paged=' + ( i + 1 ) );
						} else {
							var link_next = link.replace( /page\/+[0-9]+\//gi, 'page/' + ( i + 1 ) + '/' );
						}

						$( anchor ).attr( 'href', link_next );
					} else {
						$( anchor ).removeAttr( 'href' ).addClass( 'disabled' );
					}

					isLoading = false;
					i++;
				});
			};

			if ( 'loadmore' == layout ) {
				$( '.page-ajax a' ).on( 'click', function( e ) {
					e.preventDefault();
					button = $( '.page-ajax' );
					link = $( button ).find( 'a' ).attr( 'href' );
					content = '.products';
					anchor = '.page-ajax a';
					next = $( anchor ).attr( 'href' );

					if ( ! link ){
						return;
					}

					$( anchor ).html( '<i class="fa fa-circle-o-notch fa-spin"></i>' );

					getContentAjax();
				} );
			} else if ( 'infinite' == layout ) {
				var products_frame_handler = function() {
					container = $( '.' + ( 'masonry' == layoutStyle ? 'wr-nitro-masonry' : 'products' ) );
					button = $( '.page-ajax' );
					link = $( button ).find( 'a' ).attr( 'href' );
					content = '.products';
					anchor = '.page-ajax a';
					next = $( anchor ).attr( 'href' );

					var bottomOffset = container.offset().top + container.height() - $( window ).scrollTop();

					if ( bottomOffset < window.innerHeight && bottomOffset > 0 && ! isLoading ) {
						if ( ! link ) {
							return;
						}

						isLoading = true;

						$( anchor ).html( '<i class="fa fa-circle-o-notch fa-spin"></i>' );

						getContentAjax();
					}
				};

				var products_scroll_handler = function() {
					requestAnimationFrame( products_frame_handler );
				};

				$( window ).scroll( products_scroll_handler );
			}

			init_isotope();
		}
	}

	/*
	 * [ Init Scroll Reveal ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Item_Animation() {
		if ( typeof ScrollReveal != 'undefined' ) {
			window.sr = ScrollReveal().reveal( '.wr-item-animation', {
				duration: 700
			} );
		}
	}

	/*
	 * [ Init Owl Carousel ] - - - - - - - - - - - - - - - - - - - - - - - -
	 */
	$.WR.Carousel = function() {
		// Check if OwlCarousel plugin for jQuery is loaded before setting up carousel.
		if ( typeof $.fn.owlCarousel == 'undefined' ) {
			return setTimeout( $.WR.Carousel, 100 );
		}

		$( '.wr-nitro-carousel' ).each( function() {
			var self = $( this );

			if ( ! self.data( 'owl-carousel-initialized' ) ) {
				if ( self.hasClass( 'exclude-carousel' ) ) {
					return;
				}

				var _option = self.data( 'owl-options' );

				if ( _option !== undefined ) {
					var _autoplay = ( 'true' == _option.autoplay ) ? true : false,
						_autotime = ( _option.autoplayTimeout ) ? _option.autoplayTimeout : '5000',
						_items = _option.items,
						_nav = ( 'true' == _option.nav ) ? true : false,
						_dots = ( 'true' == _option.dots ) ? true : false,
						_pause = ( 'true' == _option.autoplayHoverPause ) ? true : false,
						_desktop = _option.desktop,
						_tablet = _option.tablet,
						_mobile = _option.mobile,
						_sm_mobile = _option.sm_mobile,
						_custom_responsive = _option.custom_responsive,
						_rtl = ( 'true' == _option.rtl ) ? true : false,
						_loop = ( _option.loop ) ? _option.loop : true,
						_autoHeight = ( 'true' == _option.autoHeight ) ? true : false,
						_animateIn = ( _option.animateIn ) ? _option.animateIn : '',
						_animateOut = ( _option.animateOut ) ? _option.animateOut : '',

					init = {
						items: 1,
						autoplay: _autoplay,
						autoplayTimeout: _autotime,
						autoplayHoverPause: _pause,
						nav: _nav,
						dots: _dots,
						loop: _loop,
						autoHeight: _autoHeight,
						smartSpeed: 400,
						navText: [ '<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>' ],
						rtl: _rtl
					};

					init.items = _items;
					if ( ! _animateIn && ! _autoHeight && '1' != _items && 'true' != _custom_responsive ) {
						init.responsive = {
							0: {
								items: _mobile,
							},
							584: {
								items: _tablet
							},
							784: {
								items: _items
							}
						}
					} else {
						init.responsive = {
							0: {
								items: _sm_mobile,
							},
							376: {
								items: _mobile
							},
							601: {
								items: _tablet
							},
							769: {
								items: _desktop
							},
							993: {
								items: _items
							}
						}
					}

					if ( _animateIn ) {
						init.animateIn = _animateIn;
					}

					if ( _animateOut ) {
						init.animateOut = _animateOut;
					}

					self.owlCarousel( init );
				}

				self.data( 'owl-carousel-initialized', true );
			}
		} );
	};

	/*
	 * [ Init Masonry Layout ] - - - - - - - - - - - - - - - - - - - - - - -
	 */
	function WR_Masonry() {
		var el = $( '.wr-nitro-masonry' );

		el.each( function( i, val ) {
			var _option = $( this ).data( 'masonry' );

			if ( _option !== undefined ) {
				var _selector = _option.selector, _width = _option.columnWidth;

				$( this ).WR_ImagesLoaded( function() {
					$( val ).isotope( {
						percentPosition: true,
						itemSelector: _selector,
						masonry: {
							columnWidth: _width
						}
					} );
				} )
			}
		} );
	}

	/*
	 * [ Init jQuery Countdown ] - - - - - - - - - - - - - - - - - - - - - -
	 */
	function WR_Countdown() {
		var el = $( '.wr-nitro-countdown' );

		el.each( function( i, val ) {
			var _option = $( this ).data( 'time' );

			if ( _option !== undefined ) {
				var _day = _option.day, _month = _option.month, _year = _option.year, _end = _month + '/ ' + _day + '/ ' + _year + ' 00:00:00';

				$( val ).countdown( {
					date: _end,
					render: function( data ) {
						$( this.el ).html( "<div class='pr'><span class='db color-primary'>" + this.leadingZeros( data.days, 2 ) + "</span><span class='db tu ls-1 color-dark'>" + WR_Data_Js['wr_countdown_days'] + "</span></div><div class='pr'><span class='db color-primary'>" + this.leadingZeros( data.hours, 2 ) + "</span><span class='db tu ls-1 color-dark'>" + WR_Data_Js['wr_countdown_hrs'] + "</span></div><div class='pr'><span class='db color-primary'>" + this.leadingZeros( data.min, 2 ) + "</span><span class='db tu ls-1 color-dark'>" + WR_Data_Js['wr_countdown_mins'] + "</span></div><div class='pr'><span class='db color-primary'>" + this.leadingZeros( data.sec, 2 ) + "</span><span class='db tu ls-1 color-dark'>" + WR_Data_Js['wr_countdown_secs'] + "</span></div>" );
					}
				} );
			}
		} );
	}

	/*
	 * [ Init Back to top ] - - - - - - - - - - - - - - - - - - - - - -
	 */
	function WR_Backtop() {
		var backtop = $( '#wr-back-top' );
		$(window).scroll( function() {

			if( $( this ).scrollTop() != 0 ) {
				backtop.fadeIn();
			} else {
				backtop.fadeOut();
			}
		});

		backtop.click( function() {
			$( 'body, html' ).animate({ scrollTop: 0 }, 800 );
		});

	}

	/*
	 * [ Highlight keyword in search result ] - - - - - - - - - - -
	 */
	function WR_Highlight_key() {
		$( '.search-results .search-item .entry-content p, .search-results .search-item .entry-title a' ).each( function( i, el ) {
			var keyword = $( '.search-results .result-list' ).attr( 'data-key' );
			var text = $( el ).text();
			var keywords = keyword.split( ' ' );

			$.each( keywords, function( i, key ) {
				var regex = new RegExp( '(' + key + ')', 'gi' );
				text = text.replace( regex, '<span class="highlight">$1</span>' );
				$( el ).html( text )
			} )
		} )
	}

	/*
	 * [ Change button click of YITH compare product ] - - - - - - - - - - -
	 */
	function WC_Trigger_Compare_Button() {
		$( 'body' ).delegate( '.product__compare .product__btn', 'click', function() {
			$( this ).next().find( '.compare' ).trigger( 'click' );
			return false;
		} );
	}

	/*
	 * [ Show notice for product type booking ] - - - - - - - - - - -
	 */
	function WC_Show_Notice_Booking() {
		if( $( '.single-product .product-type-booking' ).length ) {
			$( '.woocommerce-message' ).show();
		}
	}

	/*
	 * [ Product quantity adjust ] - - - - - - - - - - - - - - - - - - - - -
	 */
	function WC_Adjust_Cart_Quantity() {

		var converInt = function( value ) {
			if( parseInt( Number( value ) ) == value ) {
				return value;
			}

		  	return value.toFixed(2);
		}

		$( 'body' ).on( 'click', '.quantity a.plus', function( e ) {
			var $input = $( this ).parent().parent().find( 'input' ),
				step   = Number( $input.attr( 'step' ) ),
                max   = Number( $input.attr( 'max' ) ),
				value  = converInt( Number( $input.val() ) + step );

            if ( 0 != max && value > max ) {
                value = max;
            }
			$input.val( value );
			$input.trigger( 'change' );
		} );
		$( 'body' ).on( 'click', '.quantity a.minus', function( e ) {
			var $input = $( this ).parent().parent().find( 'input' ),
				step   = Number( $input.attr( 'step' ) ),
				value  = converInt( Number( $input.val() ) - step );

			if ( value < step ) {
				value = step;
			}

			$input.val( value );
			$input.trigger( 'change' );
		} );
	}

	/*
	 * [ Product Quickview ] - - - - - - - - - - - - - - - - - - - - - - - -
	 */
	function WC_Product_Quickview() {
		$( 'body' ).on( 'click', '.btn-quickview', function( e ) {
			var _this = $( this );

			_this.addClass( 'loading' );

			var id = _this.attr( 'data-prod' ), data = {
				action: 'wr_quickview',
				product: id,
				wr_view_image: 'wr_quickview',
			};

			$.post( WRAjaxURL, data, function( response ) {

				// Replace link for custom attribute
				response = $( response );
				response.find( '.wr-custom-attribute .has-image-gallery[data-value]' ).each( function() {
					var data_attribute = $( this ).attr( 'data-href' ),
						link           = data_attribute + '&wr_view_image=wr_quickview';
					$( this ).attr( 'data-href', link );
				} );

				if ( typeof $.fn.magnificPopup != 'undefined' ) {
					$.magnificPopup.open( {
						items: {
							src: response
						},
						mainClass: 'mfp-fade mfp-quickview',
						removalDelay: 300,
						callbacks: {
							open: function(){
								if ( typeof wc_add_to_cart_variation_params !== 'undefined' ) {
									$( '.variations_form' ).each( function() {
										$( this ).wc_variation_form().find('.variations select:eq(0)').change();
									});
								}
							}
						}
					} );
				}

				_this.removeClass( 'loading' );

				setTimeout( function() {
					if ( $( '.quickview-modal form' ).hasClass( 'variations_form' ) ) {
						$( '.quickview-modal form.variations_form' ).wc_variation_form();
					}

					$( '.wr-images-quickview' ).WR_ImagesLoaded( function() {
						var image_height = $( '.wr-images-quickview' ).outerHeight();

						$( '.quickview-modal .info' ).css({
							'height': image_height,
							'overflow': 'auto'
						});
					} );
				}, 100 );
			} );

			e.preventDefault();
			e.stopPropagation();
		} );

		// Show sizechart on Quickview modal
		$( 'body' ).on( 'click', '.mfp-quickview .open-popup-link', function( e ) {
			e.preventDefault();
			e.stopPropagation();

			$( '.quickview-modal' ).addClass( 'active-sizeguide' );
			$( '.quickview-modal-inner' ).hide();
		} );

		// Back to detail modal
		$( 'body' ).on( 'click', '.wr-sizeguide .sizeguide-close', function( e ) {
			$( '.quickview-modal' ).removeClass( 'active-sizeguide' );
			$( '.quickview-modal-inner' ).show();
		} );
	}

	/*
	 * [ Product Quick Buy Button ] - - - - - - - - - - - - - - - - - - - - - - - -
	 */
	function WC_Product_Quickbuy() {
		$( 'body' ).on( 'click', '.product-type-simple .btn-buynow, .wr-buy-now .btn-buynow', function( e ) {

			var _this = $( this );

			// Add loading.
			_this.addClass( 'loading' );

			var data_request = {
				action: 'wr_quickbuy',
				product_id: _this.attr( 'data-product-id' )
			};

			// Data of shortcode
			if ( _this.attr( 'data-checkout' ) != undefined && _this.attr( 'data-payment-info' ) != undefined ) {
				data_request[ 'shortcode_checkout' ] = _this.attr( 'data-checkout' );
				data_request[ 'shortcode_payment' ] = _this.attr( 'data-payment-info' );
			}

			$.ajax( {
				type: 'POST',
				url: WRAjaxURL,
				data: data_request,
				success: function( val ) {
					if ( val.status == 'true' ) {
						if ( val.type == 'redirect' ) {
							window.location.href = val.checkout_url;
						} else if ( val.type == 'modal' ) {

							// Add method get buy-now
							if ( val.checkout_url.indexOf( '?' ) != -1 ) {
								val.checkout_url = val.checkout_url + '&wr-buy-now=check-out';
							} else {
								val.checkout_url = val.checkout_url + '?wr-buy-now=check-out'
							}

							if ( typeof $.fn.magnificPopup != 'undefined' ) {
								$.magnificPopup.open( {
									items: {
										src: val.checkout_url
									},
									type: 'iframe',
									mainClass: 'mfp-fade wr-buy-now',
									removalDelay: 300,
								} );
							}
						}

					} else if ( val.status == 'false' ) {

					}

					// Remove loading.
					_this.removeClass( 'loading' );
				}
			} );

			e.preventDefault();
			e.stopPropagation();
		} );
	}

	/*
	 * [ Switch Product Layout ] - - - - - - - - - - - - - - - - - - - - - -
	 */
	function WC_Switch_Product_Layout() {
		$( 'body' ).on('click', '.wc-switch a', function(e) {
			e.preventDefault();

			var _this = $( this );

			if ( _this.hasClass( 'active' ) || _this.hasClass( 'loading' ) ) {
				return;
			}

			/* Active switch button */
			var parent = _this.closest( '.wc-switch' );
			parent.find( 'a' ).removeClass( 'active' );
			_this.addClass( 'active' );

			/* Create link get data */
			var layout = _this.attr( 'data-layout' );
			var link_layout = function( url_current ) {
				var url = '';

				if ( url_current.indexOf( '?' ) != -1 ) {

					// Get value archive style
					var archive_style = ( WR_Data_Js[ 'wc_archive_style' ] == 'list' ) ? 'grid' : WR_Data_Js[ 'wc_archive_style' ];

					if ( url_current.indexOf( 'switch=' + archive_style ) != -1 ) {
						url = url_current.replace( 'switch=' + archive_style, 'switch=' + layout );
					} else if ( url_current.indexOf( 'switch=list' ) != -1 ) {
						url = url_current.replace( 'switch=list', 'switch=' + layout );
					} else if ( url_current.indexOf( '?switch=' ) != -1 || url_current.indexOf( '&switch=' ) != -1 ) {
						url = url_current.replace( 'switch=', '' );
						url = url + '&switch=' + layout;
					} else {
						url = url_current + '&switch=' + layout;
					}
				} else {
					url = url_current + '?switch=' + layout;
				}

				return url;
			};

			var url_get_data = link_layout( window.location.href );

			// Replace link browser
			history.pushState( {}, "", url_get_data );

			// Replace link pagination
			$( '#shop-main .woocommerce-pagination ul li a.page-numbers' ).each( function( key, val ) {
				var _this = $( this );
				var url_page = _this.attr( 'href' );

				_this.attr( 'href', link_layout( url_page ) );
			} );

			// Get data by ajax
			if ( $( '#shop-main .products' ).length == 1 ) {
				_this.addClass( 'loading' );

				$.get( url_get_data, function( data ) {
					var products = $( '.products', data );

					if ( products.length ) {
						products.addClass( 'products-ajax' ).hide();

						var new_content = products[ 0 ].outerHTML;
						var shop_main_products = $( '#shop-main .products' );

						shop_main_products.after( new_content );

						shop_main_products.fadeOut( 200, function() {
							$( '#shop-main .products-ajax' ).show();
						} );
					}

					_this.removeClass( 'loading' );

				} );

				// Get data from data ready
			} else {
				var layout_show = $( '#shop-main .products.' + layout + '-layout' );
				var layout_hide = $( '#shop-main .products:not(.' + layout + '-layout)' );

				layout_hide.hide();
				layout_show.show();
			}
		} );
	}

	/*
	 * [ Switch Login / Register form ] - - - - - - - - - - - - - - - - - - - -
	 */
	function WC_Toggle_Login_Form() {
		$( '.btn-newacc, .register .btn-backacc' ).on( 'click', function( e ) {
			$( '.form-container.login, .form-container.register' ).toggleClass( 'opened' );
		} );
		$( '.btn-lostpw, .lost-password .btn-backacc' ).on( 'click', function( e ) {
			$( '.form-container.login, .form-container.lost-password' ).toggleClass( 'opened' );
		} );
	}

	/*
	 * [ Custom tabs for WC single style ] - - - - - - - - - - - - - - - - -
	 */
	function WC_Switch_Tab_To_Accordion() {
		$( '#tab-description' ).show().closest('.description_tab').addClass('active');

		$( '.accordion-tabs .tab-heading' ).click( function( e ) {
			e.preventDefault();

			var _this = $( this );
			var parent = _this.closest( '.accordion_item' );
			var parent_top = _this.closest( '.accordion-tabs' );

			if ( parent.hasClass( 'active' ) ) {
				parent.removeClass( 'active' );
				parent.find( '.entry-content' ).stop( true, true ).slideUp();
			} else {
				parent_top.find( '.accordion_item' ).removeClass( 'active' );
				parent.addClass( 'active' );
				parent_top.find( '.entry-content' ).stop( true, true ).slideUp();
				parent.find( '.entry-content' ).stop( true, true ).slideDown();
			}
		} );
	}

	/*
	 * [ Floating Add to cart ] - - - - - - - - - - - - - - - - -
	 */
	function WC_Floating_Addcart() {

		if ( $( '.p-single-action .single_add_to_cart_button' ).length == 0 )
			return;

		$( window ).load( function() {

			var footer      = $( '.footer' ),
				cart_button = $( '.p-single-action .single_add_to_cart_button' ),
				cartTop     = cart_button.offset().top + cart_button.height(),
				fh          = footer.height(),
				wh          = $( window ).height(),
				dh          = $( document ).height(),
				ww          = $( window ).width(),
				offset      = ( ( ww > 1024 ) ? parseInt( WR_Data_Js[ 'offset' ] ) : 0 ) + 10;

			var addcart_frame_handler = function() {
				var scrollTop = $( window ).scrollTop(), cart = $( '.actions-fixed' ), max = footer.offset().top - cart.height() - 15, top = max - scrollTop;

				if ( scrollTop > cartTop ) {
					cart.slideDown();
				} else {
					cart.slideUp();
				}

				if ( scrollTop + wh < dh - fh ) {
					cart.css( {
						'bottom': offset + 'px',
						'top': 'auto'
					} );
				} else {
					cart.css( {
						'bottom': 'auto',
						'top': top
					} );
				}
			};

			var addcart_scroll_handler = function() {
				requestAnimationFrame( addcart_frame_handler );
			};

			$( window ).scroll( addcart_scroll_handler );

			// Scroll to the variation selectors
			$( '.wr_add_to_cart_button i' ).on( 'click', function() {

				// Check if user selected the variable or not
				if ( $( this ).parent().hasClass( 'wr-notice-tooltip' ) ) {

					var $container = $( 'html, body' ),
						$scrollTo  = $( '.variations_form' ),
						adminBar   = $( '#wpadminbar' ).length ? $( '#wpadminbar' ).outerHeight() : '';

					// Animation scroll
					$container.animate({
						scrollTop: $scrollTo.offset().top - $container.offset().top + $container.scrollTop() - adminBar - 20
					}, 800 );
				}
			});
		});
	}

	/*
	 * [ Widget Accordion ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Widget_Accordion() {

		$( '.product-categories .cat-parent > .children' ).before( '<span class="fa fa-angle-down pa tc"></span>' );


		if( isMobile() ) {
			$( '.wr-mobile .widget_nav_menu .menu-item-has-children > .sub-menu' ).before( '<span class="fa fa-angle-down pa tc"></span>' );
		}

		// Categories widget
		$( 'body' ).on( 'click', '.product-categories .cat-parent .fa', function() {
			$( this ).closest( '.cat-parent' ).toggleClass( 'active' ).find( '> .children' ).stop( true, false ).slideToggle();
		} );

		// Custom Menu widget
		$( 'body' ).on( 'click', '.widget_nav_menu .menu-item-has-children .fa', function() {
			$( this ).closest( '.menu-item-has-children' ).toggleClass( 'active' ).find( '> .sub-menu' ).stop( true, false ).slideToggle();
		} );

		$( ".product-categories .count" ).each( function() {
			var _this = $( this );
			var count = _this.text().replace( '(', '' ).replace( ')', '' );
			_this.text( count );
		} );
	}

	/*
	 * [ Click outside ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Click_Outside( selector, parent_selector, callback ) {
		$( '.wrapper-outer' ).on( 'mousedown vmousedown', function clickHandler( e ) {

			var index_selector = $( parent_selector ).index( selector.closest( parent_selector ) );
			var index_current = $( parent_selector ).index( $( e.target ).closest( parent_selector ) );
			if ( index_selector != index_current ) {
				$( 'body' ).off( 'mousedown vmousedown' );
				callback.call( e );
			}
		} );
	}

	/*
	 * [ Click outside redirect ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Click_Outside_Redirect( selector, redirect, callback ) {
		$( '.wrapper-outer' ).on( 'mousedown vmousedown', function clickHandler( e ) {
			var index_current = $( redirect ).index( $( e.target ).closest( redirect ) );
			if ( index_current == -1 ) {
				$( 'body' ).off( 'mousedown vmousedown' );
				callback.call( e );
			}
		} );
	}

	/*
	 * [ Check device is rotate ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Rotate_Mobile() {
		$( window ).resize( function() {
			var height_broswer = $( window ).height();
			var width_broswer = $( window ).width();

			if ( typeof window.is_vertical_mobile === 'undefined' )
				window.is_vertical_mobile = ( height_broswer < width_broswer ) ? true : false;

			if ( height_broswer < width_broswer && window.is_vertical_mobile ) { // Horizontal
				window.is_vertical_mobile = false;
				callback_resize();
			} else if ( height_broswer > width_broswer && !window.is_vertical_mobile ) { // Vertical
				window.is_vertical_mobile = true;
				callback_resize();
			}
		} );

		function callback_resize() {
			$.each( $.function_rotate_device, function( key, val ) {
				val.call();
			} );
		}
	}

	/*
	 * [ Single gallery horizontal ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Horizontal_Scroll() {
		var horizontal = $( '.wr-nitro-horizontal' );
		if ( horizontal.length > 0 ) {
			var _option = horizontal.data( 'owl-options' );

			if ( _option !== undefined ) {
				var _autoplay = ( 'true' == _option.autoplay ) ? true : false, _dots = ( 'true' == _option.dots ) ? true : false, _loop = ( 'true' == _option.loop ) ? true : false, _mousewheel = ( 'true' == _option.mousewheel ) ? true : false;

				horizontal.owlCarousel( {
					items: 4,
					loop: true,
					nav: false,
					autoplay: _autoplay,
					dots: _dots,
					autoWidth: true
				} );

				if ( _mousewheel == true ) {
					horizontal.on( 'mousewheel', '.owl-stage', function( e ) {
						if ( e.deltaY > 0 ) {
							horizontal.trigger( 'prev.owl' );
						} else {
							horizontal.trigger( 'next.owl' );
						}
						e.preventDefault();
					} );
				}
			}

		}
	}

	/*
	 * [ Single gallery full-screen ] - - - - - - - - - - - - - - - -
	 */
	function WR_Gallery_Fullscreen() {

		var sync1 = $( '.gallery-cover' );
		var sync2 = $( '.gallery-thumb' );
		var slidesPerPage = 6;
		var syncedSecondary = true;

		if ( !sync1.length > 0 )
			return;

		sync1.owlCarousel( {
			items: 1,
			slideSpeed: 2000,
			nav: true,
			animateOut: 'fadeOut',
			animateIn: 'fadeIn',
			autoplay: true,
			dots: false,
			loop: true,
			navText: [ "<i class=\'fa fa-angle-left\'></i>", "<i class=\'fa fa-angle-right\'></i>" ],
		} ).on( 'changed.owl.carousel', syncPosition );

		sync2.on( 'initialized.owl.carousel', function() {
			sync2.find( ".owl-item" ).eq( 0 ).addClass( "current" );
		} ).owlCarousel( {
			items: slidesPerPage,
			dots: false,
			nav: false,
			smartSpeed: 200,
			slideSpeed: 500,
			slideBy: slidesPerPage,
			responsiveRefreshRate: 100
		} ).on( 'changed.owl.carousel', syncPosition2 );

		function syncPosition( el ) {

			var count = el.item.count - 1;
			var current = Math.round( el.item.index - ( el.item.count / 2 ) - .5 );
			if ( current < 0 ) {
				current = count;
			}
			if ( current > count ) {
				current = 0;
			}

			sync2.find( ".owl-item" ).removeClass( "current" ).eq( current ).addClass( "current" );
			var onscreen = sync2.find( '.owl-item.active' ).length - 1;
			var start = sync2.find( '.owl-item.active' ).first().index();
			var end = sync2.find( '.owl-item.active' ).last().index();

			if ( current > end ) {
				sync2.data( 'owl.carousel' ).to( current, 100, true );
			}
			if ( current < start ) {
				sync2.data( 'owl.carousel' ).to( current - onscreen, 100, true );
			}
		}

		function syncPosition2( el ) {
			if ( syncedSecondary ) {
				var number = el.item.index;
				sync1.data( 'owl.carousel' ).to( number, 100, true );
			}
		}

		sync2.on( "click", ".owl-item", function( e ) {
			e.preventDefault();
			var number = $( this ).index();
			sync1.data( 'owl.carousel' ).to( number, 300, true );
		} );
	}

	/*
	 * [ Show menu in mobile ] - - - - - - - - - - - - - - - - -
	 */
	function HB_ShowMenuMobile() {
		$( '.wr-mobile .hb-menu .has-children-mobile' ).click( function() {
			var _this = $( this );
			var parent = _this.closest( '.item-link-outer' );
			var parent_li = _this.closest( 'li' );
			var submenu = parent_li.find( ' > ul:first' );

			if ( parent.hasClass( 'active-submenu' ) ) {
				submenu.stop( true, true ).slideUp( function() {
					var menu = _this.closest( '.site-navigator-inner' );
					var menu_inner = _this.closest( '.site-navigator' );
					var menu_info = menu_inner[ 0 ].getBoundingClientRect();
					var height_broswer = $( window ).height();
					var height_scroll = height_broswer - menu_info.top;

					if ( menu_info.height <= height_scroll ) {
						menu.css( 'height', '' );
					}
				} );
				parent.removeClass( 'active-submenu' );
			} else {
				submenu.stop( true, true ).slideDown( function() {
					var menu = _this.closest( '.site-navigator-inner' );
					var menu_info = menu[ 0 ].getBoundingClientRect();
					var height_broswer = $( window ).height();
					var height_scroll = height_broswer - menu_info.top;

					if ( menu_info.height > height_scroll ) {
						menu.height( height_scroll );
					}
				} );
				parent.addClass( 'active-submenu' );
			}
		} );

		$( '.wr-mobile .hb-menu .menu-icon-action' ).click( function() {
			var _this = $( this );
			var parent = _this.closest( '.hb-menu' );
			var menu = parent.find( '.site-navigator-inner' );

			if ( _this.hasClass( 'active-menu' ) ) {
				menu.stop( true, true ).slideUp();
				_this.removeClass( 'active-menu' );
			} else {
				WR_Click_Outside( _this, '.hb-menu', function( e ) {
					menu.stop( true, true ).slideUp();
					_this.removeClass( 'active-menu' );
				} );

				menu.stop( true, true ).slideDown( function() {
					var menu_info = menu[ 0 ].getBoundingClientRect();
					var height_broswer = $( window ).height();
					var height_scroll = height_broswer - menu_info.top

					if ( menu_info.height > height_scroll ) {
						$( this ).height( height_scroll );
					}
				} );
				_this.addClass( 'active-menu' );
			}
		} );

		$.function_rotate_device.menu_mobile = function() {
			$.each( $( '.wr-mobile .hb-menu .menu-icon-action.active-menu' ), function( key, val ) {
				var _this = $( val );
				var parent = _this.closest( '.hb-menu' );
				var menu = parent.find( '.site-navigator-inner' );
				menu.css( 'height', '' );

				var menu_info = menu[ 0 ].getBoundingClientRect();
				var height_broswer = $( window ).height();
				var height_scroll = height_broswer - menu_info.top;

				if ( menu_info.height > height_scroll ) {
					menu.height( height_scroll );
				} else {
					menu.css( 'height', '' );
				}
			} );
		};

	}

	/*
	 * [ Member shortcode trigger ] - - - - - - - - - - - - - - - - - - - -
	 */
	function WR_Shortcode_Member() {
		var memberAvatar = $( '.nitro-member .member a' );
		var infoHeight = $( '.nitro-member .info > p' ).height();
		$( '.nitro-member.style-2 .info' ).css( 'bottom', -( infoHeight + 16 ) );

		memberAvatar.mouseenter( function( e ) {
			$( this ).find( '.name' ).fadeIn();
		} );
		memberAvatar.mouseleave( function( e ) {
			$( this ).find( '.name' ).hide();
		} );
		memberAvatar.mousemove( function( e ) {
			var x = e.pageX, y = e.pageY, offset = $( this ).offset(), dX = x - offset.left, dY = y - offset.top;

			$( this ).find( '.name' ).css( {
				top: ( dY + 20 ) + 'px',
				left: ( dX - 15 ) + 'px'
			} );
		} );

		memberAvatar.on( 'click', function() {
			var _this        = $(this),
			parent           = _this.closest( '.member' ),
			grandParent      = _this.closest( '.nitro-member' ),
			member_item      = grandParent.find( '.member' ),
			currentItem      = member_item.index( parent ) + 1,
			countItem        = member_item.length,
			grandParentWidth = grandParent.width(),
			parentWidth      = parent[0].getBoundingClientRect(),
			column           = parseInt( grandParentWidth / parentWidth.width ),
			memberContent    = _this.next();

			if ( countItem <= column ) {
				var indexItem = countItem;
			} else if ( currentItem <= column ) {
				var indexItem = ( column - currentItem ) + currentItem;
			} else {
				var indexItem = ( parseInt( currentItem / column ) + ( currentItem % column == 0 ? 0 : 1 ) ) * column;
				if ( countItem < indexItem ) {
					indexItem = countItem;
				}
			}

			var el_next = grandParent.find( '.member' ).get( indexItem - 1 );
			el_next = $( el_next ).next();

			if ( parent.hasClass( 'active-member' ) ) {
				parent.removeClass( 'active-member' );
				$( '.member-container' ).slideUp( 500, function() {
					$( this ).remove();
				} );
			} else {
				member_item.removeClass( 'active-member' );
				parent.addClass( 'active-member' );

				var memberContainer = grandParent.find( '.member' ).get( indexItem - 1 );

				if ( el_next.hasClass( 'member-container' ) ) {

					$( '.member-container' ).fadeOut( 300, function() {
						$( this ).html( memberContent.html() );
						$( this ).fadeIn( 300 );
					} );
				} else {
					if ( $( '.member-container' ).length ) {
						$( '.member-container' ).slideUp( 500, function() {
							$( this ).remove();
							var memberContainer = grandParent.find( '.member' ).get( indexItem - 1 );
							$( memberContainer ).after( '<div class="member-container clear">' + memberContent.html() + '</div>' );
							$( '.member-container' ).slideDown();
						} );
					} else {
						$( '.member-container' ).remove();
						var memberContainer = grandParent.find( '.member' ).get( indexItem - 1 );

						$( memberContainer ).after( '<div class="member-container clear">' + memberContent.html() + '</div>' );
						$( '.member-container' ).slideDown( 500 );
					}
				}
			}
		} );
	}

	/*
	 * [ Gallery filter ] - - - - - - - - - - - - - - - - - - - -
	 */
	function WR_Shortcode_Gallery() {
		var container = $( '.galleries .nitro-gallery-masonry' );
		if ( container.length ) {
			container.each(function() {
				var _this = $( this ),
					layout = _this.attr( 'data-layout' );
				_this.WR_ImagesLoaded( function() {

					if ( layout == 'masonry' ) {
						_this.isotope( {
							filter: '*',
							percentPosition: true,
							masonry: {
								columnWidth: '.grid-sizer',
							},
						} );
					} else {
						_this.isotope( {
							filter: '*',
							percentPosition: true,
							layoutMode: 'fitRows'
						} );
					}
				} );
			});
		}

		$( '.gallery-cat a' ).click( function() {
			var selector = $( this ).attr( 'data-filter' );
			$( this ).closest( '.galleries' ).find( '.nitro-gallery-masonry' ).isotope( {
				filter: selector,
				transitionDuration: '0.3s',
			} );
		} );
		var $optionSets = $( '.gallery-cat' ), $optionLinks = $optionSets.find( 'a' );

		$optionLinks.click( function() {
			var $this = $( this );
			// don't proceed if already selected
			if ( $this.hasClass( 'selected' ) ) {
				return false;
			}
			var $optionSet = $this.parents( '.gallery-cat' );
			$optionSet.find( '.selected' ).removeClass( 'selected' );
			$this.addClass( 'selected' );
		} );

		if ( window.innerWidth <= 769 ) {
			$( '.filter-on-mobile' ).on( 'click', function() {
				$( this ).next().slideToggle();
			} );

			$( '.gallery-cat a[data-filter]' ).on( 'click', function() {
				var text = $( this ).text();
				$( this ).parent().siblings( '.filter-on-mobile' ).find( 'span' ).text( text );
				$( this ).parent().slideToggle();
			} );
		}
	}

	/*
	 * [ Product categories ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Shortcode_Product_Categories() {
		var button = $( '.sc-cat-list[data-expand="true"]' ).children( 'a' );
		$( '.sc-cat-list[data-expand="true"] ul' ).hide();

		button.on( 'click', function() {
			$( this ).next().slideToggle();
		} );
		if ( $( '.sc-cat-mobile' ).length > 0 ) {
			$( '.sc-cat-mobile' ).on( 'click', function() {
				$( this ).toggleClass( 'expanded' ).next().toggleClass( 'expanded' );
			});
		}
	}

	/*
	 * [ Remove product to Wishlist ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Remove_Product_Wishlist() {
		$( 'body' ).on( 'click', '.yith-wcwl-add-button .add_to_wishlist', function( e ) {
			e.preventDefault();

			$( this ).css( 'opacity', '0' )
		});
		$( 'body' ).on( 'click', '.wishlist-submit.add_to_wishlist', function( e ) {
			e.preventDefault();
			$( this ).find( '.ajax-loading' ).show();
			$( this ).find( '.wishlist-icon' ).hide();

		});
		$( 'body' ).on( 'click', '.yith-wcwl-remove-button a', function( e ) {
			e.preventDefault();

			var _this   = $(this);
			var parent  = _this.closest( '.yith-wcwl-add-to-wishlist' );
			var loading = parent.find( '.yith-wcwl-remove-button .ajax-loading' );
			var add     = parent.find( '.yith-wcwl-add-button .add_to_wishlist' );

			_this.css( 'opacity', '0' )
			loading.css( 'visibility', 'visible' );
			add.css( 'opacity', '1' );

			var data_request = {
				action: 'wr_remove_product_wishlish',
				_nonce: _nonce_wr_nitro,
				product_id: _this.attr( 'data-product-id' )
			};

			$.ajax( {
				type: 'POST',
				url: WRAjaxURL,
				data: data_request,
				success: function( val ) {
					if( val.status == 'true' ) {
						// Remove
						loading.css( 'visibility', 'hidden' );

						// Hide remove
						parent.find( '.yith-wcwl-remove-button' ).hide();

						// Show add
						// parent.find( '.yith-wcwl-add-button' ).show();

						// Show remove
						_this.css( 'opacity', '1' )
					}
				}
			} );

		} );
	}

	/*
	 * [ Buy Now button ] - - - - - - - - - - - - - - - - - - - -
	 */
	function WR_Shortcode_Buy_Now() {
		$( '.wr-buy-now .btn-buynow' ).click( function() {
			if ( ! $( 'body' ).hasClass( 'woocommerce-page' ) ) {
				$( 'body' ).addClass( 'woocommerce-page' );
			}
		} );
	}

	/*
	 * [ Calculate text width ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Shortcode_Calc_Sep() {
		$( '.nitro-separator' ).each( function() {
			var width = $( this ).find( 'span' ).width(), align = $( this ).attr( 'data-align' );

			switch ( align ) {
				case 'left':
					if ( $( 'body' ).hasClass( 'rtl' ) ) {
						$( this ).find( '.sep' ).css( 'margin-right', width + 20 );
					} else {
						$( this ).find( '.sep' ).css( 'margin-left', width + 20 );
					}
				break;

				case 'right':
					if ( $( 'body' ).hasClass( 'rtl' ) ) {
						$( this ).find( '.sep' ).css( 'margin-left', width + 20 );
					} else {
						$( this ).find( '.sep' ).css( 'margin-right', width + 20 );
					}
				break;

				case 'center':
					var haftwidth = ( $( this ).width() - width ) / 2 - 20;
					$( this ).find( '.sep-left, .sep-right' ).css( 'width', haftwidth );
				break;
			}
		} );
	}

	/*
	 * [ Init video player ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Shortcode_Video() {
		if ( typeof $.fn.magnificPopup == 'undefined' ) {
			return setTimeout( WR_Shortcode_Video, 100 );
		}

		if ( $( '.sc-video' ).length > 0 ) {
			$( '.sc-video-popup' ).each( function( i, val ) {
				var _option = $( this ).data( 'popup' );

				if ( _option !== undefined ) {
					var _control = ( 'true' == _option.control ) ? 'controls=1' : 'controls=0';

					$( val ).magnificPopup( {
						type: 'iframe',
						mainClass: 'mfp-fade',
						removalDelay: 300,
						iframe: {
							markup: '<div class="mfp-iframe-scaler">' + '<button type="button" class="mfp-close">×</button>' + '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' + '</div>',
							patterns: {
								youtube: {
									index: 'youtube.com/',
									id: 'v=',
									src: '//www.youtube.com/embed/%id%?autoplay=1&showinfo=0&' + _control
								},
								vimeo: {
									index: 'vimeo.com/',
									id: '/',
									src: '//player.vimeo.com/video/%id%?autoplay=1'
								},
							}
						}
					} );
				}
			} );

			if ( $( '.sc-yt-trigger' ).length > 0 ) {
				$.getScript( 'https://www.youtube.com/iframe_api' );

				$( '.sc-yt-trigger' ).each( function() {
					var _this = $( this ), iframe = _this.next();
					setTimeout( function checkYT() {
						if ( typeof YT !== 'object' )
							return setTimeout( checkYT, 1000 );

						if ( typeof YT.Player !== 'function' )
							return setTimeout( checkYT, 1000 );

						var player;
						player = new YT.Player( iframe.get( 0 ), {
							events: {
								"onReady": function( e ) {
									_this.on( 'click', function( i ) {
										e.target.playVideo();
										_this.css( 'opacity', 0 );
										iframe.show();
										i.preventDefault();
									} );
								}
							}
						} );
					}, 1000 );
				} );
			}

			if ( $( '.sc-vm-trigger' ).length > 0 ) {
				$( '.sc-vm-trigger' ).each( function() {
					var _this = $( this ), iframe = _this.next(), playerOrigin = '*';

					// Call the API when a button is pressed
					_this.on( 'click', function( e ) {
						post( 'play' );
						_this.css( 'opacity', 0 );
						iframe.show();
						e.preventDefault();
					} );

					// Helper function for sending a message to the player
					function post( action, value ) {
						var data = {
							method: action
						};
						if ( value ) {
							data.value = value;
						}
						var message = JSON.stringify( data );
						iframe[ 0 ].contentWindow.postMessage( message, playerOrigin );
					}
				} );
			}
		}
	}

	/*
	 * [ Init Timeline ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Shortcode_Timeline() {
		var timeline = $( '.nitro-timeline.style-2' );
		var changeClass = function() {
			timeline.removeClass( 'style-2' ).addClass( 'style-1' );
			if ( $( window ).width() < 568 ) {
				timeline.removeClass( 'style-2' ).addClass( 'style-1' );
			} else {
				timeline.removeClass( 'style-1' ).addClass( 'style-2' );
			}
		}

		if ( timeline.length > 0 ) {
			changeClass();
			$(window).resize(function(){
			    changeClass();
			});
		}
	}

	/*
	 * [ Init Blog List ] - - - - - - - - - - - - - - - - -
	 */
	function WR_Shortcode_Blog_List() {
		var blog_list = function(){
			$.each( $( '.list-blog.has-featured-img .has-post-thumbnail .entry-title' ), function(){
				var _this  = $(this);
				var parent = _this.closest( '.has-post-thumbnail' );

				parent.removeClass( 'blog-res' );

				if( _this.width() < 180 ) {
					parent.addClass( 'blog-res' );
				}
			} );
		};

		blog_list();

		$(window).resize(function(){
		    blog_list();
		});
	}

	/*
	 * [ Overwrite style WOOF - WooCommerce Products Filter plugin ] - - - - - - - - - - - - - - - - -
	 */
	function WR_WOOF_Plugin() {
		// Add class Set Filter Automatically setting
		$( '.woof_auto_show' ).parent().addClass( 'woof_auto_show_outer' );
	}

	/*
	 * [ Init magnific popup for product video ] - - - - - - - - - - - - - - - - -
	 */
	function WC_Product_Video() {
		if ( $( '.p-video' ).length > 0 ) {
			$( '.p-video-link' ).magnificPopup({
				type: 'iframe'
			});

			$( '.p-video-file' ).magnificPopup({
				type: 'inline'
			});
		}
	}

	/*
	 * [ Init magnific popup for contact form 7 in catalog mode ] - - - - - - - - - - - - - - - - -
	 */
	function WC_CF7_Catalog_Mode() {
		if ( $( '.wr-open-cf7' ).length > 0 ) {
			$( '.wr-open-cf7' ).magnificPopup({
				type: 'inline',
				removalDelay: 300,
          		mainClass: 'mfp-fade'
			});
		}
	}

	/*
	 * [ Click to show mobile WC sidebar ] - - - - - - - - - - - - - - - - -
	 */
	function WC_Show_Mobile_Sidebar() {
		$( 'body' ).on( 'click', '.wc-show-sidebar', function( e ) {
			$( 'body' ).toggleClass( 'slide-to-left' );
			$( 'html' ).addClass( 'no-scroll' );
			$( '#shop-mobile-sidebar' ).before( '<div class="mask-overlay"></div>' )
			// Click body to close panel
			WR_Click_Outside_Redirect( $( this ), '#shop-mobile-sidebar', function() {
				$( 'body' ).removeClass( 'slide-to-left' );
				$( 'html' ).removeClass( 'no-scroll' );
				$( '.mask-overlay' ).remove();
			});
		});
	}

	/*
	 * [ Show more WC category description ] - - - - - - - - - - - - - - - - -
	 */
	function WC_Category_Description_Read_More() {
		if ( $( '.term-description' ).length > 0 ) {
			var term        = $( '.term-description' ),
				term_height = term.height();

			if ( term_height > 78 ) {
				term.wrapInner( '<div class="term-description-inner"></div>');
				term.append( '<a class="term-more dib mgt10 bg-primary color-white" href="#">' + WR_Data_Js[ 'show_more' ] + '</a>' );

				term.children( '.term-description-inner' ).css({
					'height': 78,
					'overflow': 'hidden'
				});

				$( 'body' ).on( 'click', '.term-more', function() {
					term.children( '.term-description-inner' ).toggleClass( 'term-show-hide' );
					( $( this ).text() == WR_Data_Js[ 'show_more' ] ) ? $( this ).text( WR_Data_Js[ 'show_less' ] ) : $( this ).text( WR_Data_Js[ 'show_more' ] );
				});
			}
		}
	}

	/*
	 * [ Custom init flexslider ] - - - - - - - - - - - - - - - - -
	 */
	function WC_Init_Flexslider() {
		if ( $( '.flex-control-thumbs' ).length > 0 ) {
			var thumb = $( '.flex-control-thumbs li' ).length;

			if ( thumb > 5 ) {
				$( '.woocommerce-product-gallery__wrapper' ).WR_ImagesLoaded( function() {
					setTimeout( function() {
						$( '.flex-control-thumbs' ).scrollbar();
					}, 50);
				});
			}
		}

		if ( $( '.woocommerce-product-gallery--with-nav' ).length > 0 ) {
			$( '.woocommerce-product-gallery--with-nav' ).flexslider({
				animation: "slide",
				controlNav: false,
				animationLoop: false,
				slideshow: false,
				itemWidth: 90,
				itemMargin: 10,
				asNavFor: '.woocommerce-product-gallery--with-images'
			});
		}
	}

	/**
	 * Init full screen for blog post title.
	 *
	 * @return  void
	 */
	function WR_InitFullScreenBlogPostTitle() {
		var title = $('.post-title-full-screen');

		if (title.length) {
			var height = ($(window).height() - $('.header-outer').height() - $('#wpadminbar').height()),
				offset = WR_Data_Js.offset;

			title.next().attr('id', 'single');
			title.css('height', (height - offset * 2));

			$(window).on('resize', function () {
				var height = ($(window).height() - $('.header-outer').height() - $('#wpadminbar').height());

				title.css('height', height - offset * 2);
			});
		}
	}

	$( document ).ready( function() {
		// Disable scroll to div when click to tab
		$( 'body' ).on( 'click', '.vc_tta-tab > a', function( e ) {
			e.preventDefault();
		} );

		/*
		 * [ Init skrollr parallax ] - - - - - - - - - - - - - - - - - - - -
		 */
		if ( ( WR_Data_Js != undefined && WR_Data_Js[ 'blogParallax' ] == 1 || WR_Data_Js[ 'pageParallax' ] == 1 || WR_Data_Js[ 'bodyParallax' ] == 1 ) && !isMobile() ) {
			if ( window.skrollr ) {
				skrollr.init( {
					forceHeight: false
				} );
			}
		}

		// Add class to button size chart
		if ( $( '.open-popup-link' ).length ) {
			$( '.addition-product .open-popup-link, .addition-product .price br' ).remove();
		}

		// Header builder function
		HB_ShowMenuMobile();
		HB_Sticky_Row();
		HB_Element_SearchBox();
		HB_Element_Cart();
		HB_Element_Sidebar();
		HB_Element_Menu();
		HB_Element_Currency();

		// WooRockets function
		WR_Scroll_Animated();
		WR_Sidebar_Sticky();
		$.WR.Lightbox();
		$.WR.Carousel();
		WR_Masonry();
		WR_Countdown();
		WR_Backtop();
		WR_Highlight_key();
		WR_Rotate_Mobile();
		WR_Gallery_Fullscreen();
		WR_Item_Loadmore();
		WR_Item_Animation();
		WR_WOOF_Plugin();
		WR_InitFullScreenBlogPostTitle();

		// WooCommerce function
		WR_Remove_Product_Wishlist();
		WC_Show_Notice_Booking();
		WC_Trigger_Compare_Button();
		WC_Adjust_Cart_Quantity();
		WC_Product_Quickview();
		WC_Product_Quickbuy();
		WC_Toggle_Login_Form();
		WC_Switch_Product_Layout();
		WC_Switch_Tab_To_Accordion();
		WC_Floating_Addcart();
		WC_Product_Video();
		WC_CF7_Catalog_Mode();
		WC_Show_Mobile_Sidebar();
		WC_Category_Description_Read_More();
		WC_Init_Flexslider();

		// Nitro shortcode trigger function
		WR_Shortcode_Member();
		WR_Shortcode_Gallery();
		WR_Shortcode_Product_Categories();
		WR_Shortcode_Buy_Now();
		WR_Shortcode_Calc_Sep();
		WR_Shortcode_Video();
		WR_Shortcode_Timeline();
		WR_Shortcode_Blog_List();

		WR_Widget_Accordion();
		WR_PageLoader();
	} );

	$( window ).load( function() {
		WR_Horizontal_Scroll();
	} );
} )( jQuery );
