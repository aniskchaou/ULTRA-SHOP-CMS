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

	function render_shortcode_function( settings ) {

		var short_code = [],
		function_array = [],
		show_button_flag = false;

		$.each( settings, function( key, val ) {

			if( val !== '' && wrls_settings_default[ key ] != val ) {

				if( key == 'search_in' ) {

					// Check default
					var update = false;
					$.each( val, function( key_item, val_item ) {
						if( wrls_settings_default[ key ][ key_item ] != val_item )
							update = true;
					} );

					if( update ) {
						var search_in = [];

						$.each( val, function( key_item, val_item ) {
							if( val_item == 1 ) {
								search_in.push( key_item );
							}
						} );

						if( search_in.length ) {
							short_code.push( key + '="' + search_in.join( ', ' ) + '"' );
							function_array.push( '"' + key + '" => "' + search_in.join( ', ' ) + '"' );
						}
					}

				} else if( key == 'show_button' ) {

					short_code.push( key +'="' + ( val ? 'yes' : 'no' ) + '"' );
					function_array.push( '"' + key + '" => "' + ( val ? 'yes' : 'no' ) + '"' );

					if( val == 1 ) {
						show_button_flag = true;
					}

				} else if( key == 'text_button' && ! show_button_flag ) {
					// Not action
				} else {

					if( key == 'show_category' || key == 'show_suggestion' )
						val = val ? 'yes' : 'no';

					short_code.push( key +'="' + val + '"' );
					function_array.push( '"' + key + '" => "' + val + '"' );
				}
			}

		} );

		$( '#shortcode-render' ).html( '[wr_live_search' + ( short_code.length ? ( ' ' + short_code.join( ' ' ) ) : '' ) + ']' )

		$( '#function-render' ).html( 'wr_live_search(' + ( function_array.length ? ( ' array( ' + function_array.join( ', ' ) + ' ) ' ) : '' ) + ');' )

	}

	function disable_search_in( ) {

		var search_in_count = 0;

		// Disable setting search in if isset a opption
		$.each( $( '#search_in-setting input[type="checkbox"]' ), function() {
			if( $(this).is(":checked") )
				search_in_count++;
		} );

		if( search_in_count == 1 ) {
			$( '#search_in-setting input[type="checkbox"]:checked' ).closest( '.search-in-item' ).addClass( 'disable' );
		} else {
			$( '#search_in-setting .search-in-item.disable' ).removeClass( 'disable' );
		}

	}

	function setting_event() {

		// Placeholder
		$( 'body' ).on( 'keyup', '#placeholder-setting', function() {
			wrls_settings['placeholder'] = $(this).val();
			render_shortcode_function( wrls_settings );
		} );

		// Placeholder
		$( 'body' ).on( 'change', '#show_button-setting', function() {
			wrls_settings['show_button'] = $(this).is(":checked") ? '1' : '0';
			render_shortcode_function( wrls_settings );
		} );

		// Text button
		$( 'body' ).on( 'keyup', '#text_button-setting', function() {
			wrls_settings['text_button'] = $(this).val();
			render_shortcode_function( wrls_settings );
		} );

		// Show category list
		$( 'body' ).on( 'change', '#show_category-setting', function() {
			wrls_settings['show_category'] = $(this).is(":checked") ? '1' : '0';
			render_shortcode_function( wrls_settings );
		} );

		// Show suggestion
		$( 'body' ).on( 'change', '#show_suggestion-setting', function() {
			wrls_settings['show_suggestion'] = $(this).is(":checked") ? 1 : 0;
			render_shortcode_function( wrls_settings );
		} );

		// Minimum number of characters
		$( 'body' ).on( 'keyup', '#min_characters-setting', function() {
			wrls_settings['min_characters'] = $(this).val();
			render_shortcode_function( wrls_settings );
		} );

		// Maximum number of results
		$( 'body' ).on( 'keyup', '#max_results-setting', function() {
			wrls_settings['max_results'] = $(this).val();
			render_shortcode_function( wrls_settings );
		} );

		$( 'body' ).on( 'keyup', '#thumb_size-setting', function() {
			wrls_settings['thumb_size'] = $(this).val();
			render_shortcode_function( wrls_settings );
		} );

		// Search in Title
		$( 'body' ).on( 'change', '#search_in-setting input[name="search_in[title]"]', function() {
			wrls_settings['search_in']['title'] = $(this).is(":checked") ? 1 : 0;
			render_shortcode_function( wrls_settings );
			disable_search_in();
		} );

		// Search in Description
		$( 'body' ).on( 'change', '#search_in-setting input[name="search_in[description]"]', function() {
			wrls_settings['search_in']['description'] = $(this).is(":checked") ? 1 : 0;
			render_shortcode_function( wrls_settings );
			disable_search_in();
		} );

		// Search in Content
		$( 'body' ).on( 'change', '#search_in-setting input[name="search_in[content]"]', function() {
			wrls_settings['search_in']['content'] = $(this).is(":checked") ? 1 : 0;
			render_shortcode_function( wrls_settings );
			disable_search_in();
		} );

		// Search in SKU
		$( 'body' ).on( 'change', '#search_in-setting input[name="search_in[sku]"]', function() {
			wrls_settings['search_in']['sku'] = $(this).is(":checked") ? 1 : 0;
			render_shortcode_function( wrls_settings );
			disable_search_in();
		} );

		// Class
		$( 'body' ).on( 'keyup', '#class-setting', function() {
			wrls_settings['class'] = $(this).val();
			render_shortcode_function( wrls_settings );
		} );

		// ID
		$( 'body' ).on( 'keyup', '#id-setting', function() {
			wrls_settings['id'] = $(this).val();
			render_shortcode_function( wrls_settings );
		} );

	}

	$(document).ready(function() {

		render_shortcode_function( wrls_settings );

		setting_event();

		disable_search_in();

	});
})(jQuery)
