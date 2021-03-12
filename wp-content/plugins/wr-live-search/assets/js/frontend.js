/**
 * @version    1.0
 * @package    WR_Live_Search
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

(function($) {
	"use strict";

	$(document).ready(function() {

		setTimeout( function() { // Set Time Out for event off
			var timer, last_keyword = true;

			$('body').on( 'keydown', '.wrls-form .txt-livesearch', function( e ) {
				var key_board = e.keyCode;

				if( key_board == 38 || key_board == 40 ) // Key up or down
					e.preventDefault();
			} );

			$('body').on( 'change', '.wrls-form .cate-search', function( e ) {
				var _this 		= $(this);
				var container 	= _this.closest( '.wrls-form' );

				container.find('.products-search').remove();
				container.find('.loading-search').remove();
				container.find('.suggestion-search-data').remove();
				container.find('.not-results-search').remove();
			} );

			$('body').off( '.txt-livesearch' ).on( 'paste blur keyup', '.wrls-form .txt-livesearch', function( e ) {
				var _this         = $(this);
				var container 	  = _this.closest( '.wrls-form' );
				var key_board     = e.keyCode;
				var list_products = container.find( '.products-search .product-search' );

				if ( key_board == 40 ) { // Key down
					if( list_products.length ) {
						var active_key = container.find( '.products-search .product-search.active-key' );
						if( active_key.length ) {
							var index_next 	= $( list_products ).index( active_key );
							if( list_products.length == ( index_next + 1 ) )
								index_next = -1;

							var el_next = $( list_products ).get( index_next + 1 );
							list_products.removeClass( 'active-key' );
							$( el_next ).addClass( 'active-key' );
						} else {
							var el_next = $( list_products ).get( 0 );
							$( el_next ).addClass( 'active-key' );
						}
					}
					return;
				} else if ( key_board == 38 ) { // Key up
					if( list_products.length ) {
						var active_key = container.find( '.products-search .product-search.active-key' );
						if( active_key.length ) {
							var index_next 	= $( list_products ).index( active_key );

							if( index_next == 0 )
								index_next = list_products.length;

							var el_next 	= $( list_products ).get( index_next - 1 );

							list_products.removeClass( 'active-key' );

							if( index_next !== 0 )
								$( el_next ).addClass( 'active-key' );
						} else {
							var el_next = $( list_products ).get( list_products.length - 1 );
							$( el_next ).addClass( 'active-key' );
						}
					}
					return;
				} else if ( key_board == 13 ) { // Key enter
					var active_key = container.find( '.products-search .product-search.active-key' );
					if( active_key.hasClass( 'view-all' ) ) {
						container.submit();
					} else if ( active_key.length ) {
						window.location = active_key.find( '.mask-link' ).attr( 'href' );
					}
				}

				if ( timer )
					clearTimeout(timer);

				timer = setTimeout( function() {

					// Get keyword.
					var keyword = _this.val();

					container.find( '.loading-search' ).remove();
					container.find( '.suggestion-search-data' ).remove();
					container.find( '.not-results-search' ).remove();
					container.find( '.products-search' ).remove();

					// Custom for Nitro theme
					_this.closest( '.element-item' ).removeClass( 'loading-wrls' );

					if( last_keyword !== true && keyword == last_keyword && ! container.find( '.loading-search' ).length )
						return;

					last_keyword = keyword;

					if ( keyword == '' || keyword.length <= parseInt( _this.attr( 'data-min-characters' ) ) ) {
						return;
					}

					// Show loading indicator.
					container.find( '.results-search' ).append( '<img class="loading-search" src="' + wr_live_search.plugin_url + 'assets/images/loading.gif">' );

					// Custom for Nitro theme
					_this.closest( '.element-item' ).addClass( 'loading-wrls' );

					// Request results.
					var data = {
						keyword         : keyword,
						max_results     : _this.attr( 'data-max-results' ),
						thumb_size      : _this.attr( 'data-thumb-size' ),
						search_in       : JSON.parse(_this.attr( 'data-search-in' )),
						show_suggestion : _this.attr( 'data-show-suggestion' ),
						parent          : container.find( '.cate-search' ).val(),
					};

					$.ajax( {
						url      : wr_live_search.ajax_url,
						type     : 'POST',
						dataType : 'json',
						data     : {
							data     : data,
							security : wr_live_search.security,
						},
						success  : function( response ) {

							container.find( '.loading-search' ).remove();
							_this.closest( '.element-item' ).removeClass( 'loading-wrls' );
							container.find( '.suggestion-search-data' ).remove();
							container.find( '.not-results-search' ).remove();
							container.find( '.products-search' ).remove();

							// Prepare response.
							if ( response.message ) {
								container.find( '.results-search' ).append( '<div class="not-results-search">' + response.message + '</div>' );
							} else {
								container.find( '.results-search' ).append( '<div class="products-search"></div>' );

								// Show suggestion.
								if ( response.suggestion ) {
									container.find( '.results-search' ).append( '<div class="suggestion-search suggestion-search-data">' + response.suggestion + '</div>' );
								}

								// Show results.
								$.each( response.list_product, function( key, value ) {
									container.find( '.products-search' ).append( '<div class="product-search"><a class="mask-link" href="' + value.url + '"></a><div class="product-image">' + value.image + '</div><div class="product-title-price"><div class="product-title">' + value.title.replace( new RegExp( '(' + keyword + ')', 'ig' ), '<span class="keyword-current">$1</span>') + '</div><div class="product-price">' + value.price + '</div></div></div>' );
								} );

								container.find( '.products-search' ).append( '<div class="product-search view-all">' + WR_Data_Js['View all'] + '</div>' );
							}
						}
					} );

				}, 300 );
			} );

			$('body').on('focus', '.wrls-form .txt-livesearch', function() {
				var container = $(this).closest('.wrls-form');

				container.find('.loading-search').remove();
				container.find('.suggestion-search-data').show();
				container.find('.not-results-search').show();
				container.find('.products-search').show();

			});

			$('body').on('blur', '.wrls-form .txt-livesearch', function() {
				var container = $(this).closest('.wrls-form');

				container.find('.loading-search').remove();
				container.find('.suggestion-search-data').hide();
				container.find('.not-results-search').hide();
				container.find('.products-search').fadeOut(300);
			});

			$('body').on( 'click', '.wrls-form .view-all', function() {
				var _this = $(this);
				var parent = _this.closest( '.wrls-form ' ).submit();
			} );

		}, 1 )

	});
})(jQuery);
