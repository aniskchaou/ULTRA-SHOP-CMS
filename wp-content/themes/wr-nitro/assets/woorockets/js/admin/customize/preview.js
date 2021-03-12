/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */

(function($) {
	// Add Rules to Stylesheets
	window.addRule = function (selector, styles, sheet) {
		styles = (function (styles) {
			if (typeof styles === "string") return styles;
			var clone = "";
			for (var p in styles) {
				if (styles.hasOwnProperty(p)) {
					var val = styles[p];
					p = p.replace(/([A-Z])/g, "-$1").toLowerCase(); // convert to dash-case
					clone += p + ":" + (p === "content" ? '"' + val + '"' : val) + "; ";
				}
			}

			return clone;
		}(styles));

		sheet = sheet || document.styleSheets[document.styleSheets.length - 1];

		if (sheet.insertRule && typeof sheet.cssRules != 'undefined' && sheet.cssRules != null && typeof sheet.cssRules.length != 'undefined')
			sheet.insertRule(selector + " {" + styles + "}", sheet.cssRules.length);
		else if (sheet.addRule)
			sheet.addRule(selector, styles);

		return this;
	};

	if ($) {
		$.fn.addRule = function (styles, sheet) {
			addRule(this.selector, styles, sheet);
			return this;
		};
	}

	// Funtion to render CSS Inline
	var update_live_preview = _.throttle(function ( list_param ) {
		var css_inline = $( '#wr-nitro-main-inline-css' );
		var css_output = css_inline.html();

		for (i = 0; i < list_param.length; i++) {
			var val = list_param[i];
			var rg = new RegExp( '\\' + val['classes'], 'gi' );

			css_output = css_output.replace(/([^}]*){([^}]*)}/g, function( search, name, rules ) {
				if( name.match( rg ) ) {
					if( val['type'] == 'color' ) {
						var output = rules.replace( /([^-])(color\s?):([^;]*);/gi, '$1$2:' + val['color'] + ';' );
					} else {
						var output = rules.replace( new RegExp( '(' + val['type'] + '\s?):([^;]*);', 'gi' ), '$1:' + val['color'] + ';' );
					}
					return name + ' { ' + output + '}';
				} else {
					return search;
				}
			});
		};

		css_inline.html( css_output );
	}, 750)


	/**
	 * Live-update changed settings in real time in the Customizer preview.
	 */
	var api = wp.customize;

	// Update body font in real time.
	api( 'body_google_font', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css({
				'font-family': '"' + to['family'] + '"',
				'color': to['color'],
				'font-weight': ( to['fontWeight'] ? to['fontWeight'] : '400' )
			});

			if( $( '#body_font_link' ).length ) {
				$( '#body_font_link' ).attr( 'href', "https://fonts.googleapis.com/css?family=" + to['family'] + ':' + ( to['fontWeight'] ? to['fontWeight'] : '400' ) );
			} else {
				$( 'head' ).append( "<link id='body_font_link' href='https://fonts.googleapis.com/css?family=" + to['family'] + ':' + ( to['fontWeight'] ? to['fontWeight'] : '400' ) + "' rel='stylesheet' type='text/css'>" );
			}

		} );
	} );
	api( 'content_body_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_body_text_normal', 'color': to['body_text'],  'type': 'color' }, { 'classes': '.preview_heading_normal', 'color': to['heading_text'], 'type': 'color' }, { 'classes': '.preview_heading_bg', 'color': to['heading_text'], 'type': 'background-color' } ] );
			$( '.widget-style-4 .widget-title' ).css({ 'background-color': to['heading_text'] });
		} );
	} );
	api( 'body_font_size', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'font-size', to + '%' );
		} );
	} );
	api( 'body_line_height', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'line-height', to + 'px' );
		} );
	} );
	api( 'body_letter_spacing', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'letter-spacing', to + 'px' );
		} );
	} );

	// Update heading font in real time.
	api( 'heading_google_font', function( value ) {
		value.bind( function( to ) {
			$( 'h1,h2,h3,h4,h5,h6' ).css({
				'font-family': '"' + to['family'] + '"',
				'font-weight': ( to['fontWeight'] ? to['fontWeight'] : '400' ),
				'font-style': ( 1 == to['italic'] ) ? 'italic' : 'normal',
				'text-decoration': ( 1 == to['underline'] ) ? 'underline' : 'none',
				'text-transform': ( 1 == to['uppercase'] ) ? 'uppercase' : 'none'
			} );

			if( $( '#heading_font_link' ).length ) {
				$( '#heading_font_link' ).attr( 'href', "https://fonts.googleapis.com/css?family=" + to['family'] + ':' + ( to['fontWeight'] ? to['fontWeight'] : '400' ) );
			} else {
				$( 'head' ).append( "<link id='heading_font_link' href='https://fonts.googleapis.com/css?family=" + to['family'] + ':' + ( to['fontWeight'] ? to['fontWeight'] : '400' ) + "' rel='stylesheet' type='text/css'>" );
			}

		} );
	} );
	api( 'heading_font_size', function( value ) {
		value.bind( function( to ) {
			$( '.entry-content h1' ).css( 'font-size', parseInt( to ) * 3.998 + 'px' );
			$( 'h2' ).css( 'font-size', parseInt( to ) * 2.827 + 'px' );
			$( 'h3:not(.widget-title)' ).css( 'font-size', parseInt( to ) * 1.999 + 'px' );
			$( 'h4:not(.entry-title)' ).css( 'font-size', parseInt( to ) * 1.414 + 'px' );
			$( 'h5' ).css( 'font-size', parseInt( to ) + 'px' );
			$( 'h6' ).css( 'font-size', parseInt( to ) * 0.707 + 'px' );
		} );
	} );
	api( 'heading_line_height', function( value ) {
		value.bind( function( to ) {
			$( '.entry-content h1' ).css( 'line-height', parseInt( to ) * 3.998 + 'px' );
			$( 'h2' ).css( 'line-height', parseInt( to ) * 2.827 + 'px' );
			$( 'h3:not(.widget-title)' ).css( 'line-height', parseInt( to ) * 1.999 + 'px' );
			$( 'h4:not(.entry-title)' ).css( 'line-height', parseInt( to ) * 1.414 + 'px' );
			$( 'h5' ).css( 'line-height', parseInt( to ) + 'px' );
			$( 'h6' ).css( 'line-height', parseInt( to ) * 0.707 + 'px' );
		} );
	} );
	api( 'heading_letter_spacing', function( value ) {
		value.bind( function( to ) {
			$( '.entry-content h1,h2,h3,h4,h5,h6' ).css( 'letter-spacing', to + 'px' );
		} );
	} );

	// Update setting for quotes in real time.
	api( 'quotes_font', function( value ) {
		value.bind( function( to ) {
			$( '.format-quote .quote-content, blockquote' ).css({
				'font-family': '"' + to['family'] + '"',
				'font-weight': ( to['fontWeight'] ? to['fontWeight'] : '400' ),
				'font-style': ( 1 == to['italic'] ) ? 'italic' : 'normal',
				'text-decoration': ( 1 == to['underline'] ) ? 'underline' : 'none',
				'text-transform': ( 1 == to['uppercase'] ) ? 'uppercase' : 'none'
			} );

			if( $( '#quote_font_link' ).length ) {
				$( '#quote_font_link' ).attr( 'href', "https://fonts.googleapis.com/css?family=" + to['family'] + ':' + ( to['fontWeight'] ? to['fontWeight'] : '400' ) );
			} else {
				$( 'head' ).append( "<link id='quote_font_link' href='https://fonts.googleapis.com/css?family=" + to['family'] + ':' + ( to['fontWeight'] ? to['fontWeight'] : '400' ) + "' rel='stylesheet' type='text/css'>" );
			}

		} );
	} );

	// Update layout offset in real time.
	api( 'wr_layout_offset', function( value ) {
		value.bind( function( to ) {
			if ( 0 != to ) {
				$( 'body:not(.wr-setting-overrided)' ).addClass( 'offset' );
				$( 'body:not(.wr-setting-overrided)' ).css( 'padding', to + 'px' );
				$( 'body:not(.wr-setting-overrided):after' ).addRule({
					'border-width': to + 'px !important'
				});
			} else {
				$( 'body:not(.wr-setting-overrided)' ).removeClass( 'offset' );
				$( 'body:not(.wr-setting-overrided)' ).css( 'padding', '0' );
				$( 'body:not(.wr-setting-overrided):after' ).addRule({
					'border-width': '0 !important'
				});
			}
		} );
	} );
	api( 'wr_layout_offset_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_offset_bg', 'color': to,  'type': 'border-color' } ] );
		} );
	} );

	// Update content width & gutter width in real time.
	var header_width = $( '.header-outer .container' ).css('max-width');
	api( 'wr_layout_content_width_unit', function( value ) {
		value.bind( function( to ) {
			// Get child option.
			var pixel  = ['wr_layout_content_width'],
				percen = ['wr_layout_content_width_percentage'],
				boxed  = ['wr_layout_boxed'],
				pixelValue  = window.parent.wp.customize.control(pixel).setting.get(),
				percenValue = window.parent.wp.customize.control(percen).setting.get(),
				boxedValue  = window.parent.wp.customize.control(boxed).setting.get();

			if ( boxedValue ) {
				if ( 'pixel' == to ) {
					$( 'body:not(.wr-setting-overrided) .wrapper, .container' ).css( 'max-width', pixelValue + 'px' );
				} else {
					$( 'body:not(.wr-setting-overrided) .wrapper' ).css( 'max-width', percenValue + '%' );
					$( 'body:not(.wr-setting-overrided) .container' ).css( 'max-width', '100%' );
				}
			} else {
				if ( 'pixel' == to ) {
					$( 'body:not(.wr-setting-overrided) .container' ).css( 'max-width', pixelValue + 'px' );
				} else {
					$( 'body:not(.wr-setting-overrided) .container' ).css( 'max-width', percenValue + '%' );
				}
			}
			$( 'body:not(.wr-setting-overrided) .header-outer .container' ).css( 'max-width', header_width );

			if ( typeof $.fn.isotope != 'undefined' ) {
				$( '.wr-nitro-masonry' ).isotope();
			}
		} );
	} );
	api( 'wr_layout_content_width', function( value ) {
		value.bind( function( to ) {
			boxed  = ['wr_layout_boxed'];
			boxedValue = window.parent.wp.customize.control(boxed).setting.get();
			if ( boxedValue ) {
				$( 'body:not(.wr-setting-overrided) .wrapper, .container' ).css( 'max-width', to + 'px' );
			} else {
				$( 'body:not(.wr-setting-overrided) .container' ).css( 'max-width', to + 'px' );
			}
			$( 'body:not(.wr-setting-overrided) .header-outer .container' ).css( 'max-width', header_width );

			if ( typeof $.fn.isotope != 'undefined' ) {
				$( '.wr-nitro-masonry' ).isotope();
			}
		} );
	} );
	api( 'wr_layout_content_width_percentage', function( value ) {
		value.bind( function( to ) {
			boxed  = ['wr_layout_boxed'];
			boxedValue = window.parent.wp.customize.control(boxed).setting.get();
			if ( boxedValue ) {
				$( 'body:not(.wr-setting-overrided) .wrapper' ).css( 'max-width', to + '%' );
				$( 'body:not(.wr-setting-overrided) .container' ).css( 'max-width', '100%' );
			} else {
				$( 'body:not(.wr-setting-overrided) .container' ).css( 'max-width', to + '%' );
			}
			$( '.header-outer .container' ).css( 'max-width', header_width );

			if ( typeof $.fn.isotope != 'undefined' ) {
				$( '.wr-nitro-masonry' ).isotope();
			}
		} );
	} );
	api( 'wr_layout_boxed_size', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'background-size', to )
		} );
	} );
	api( 'wr_layout_boxed_repeat', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'background-repeat', to )
		} );
	} );
	api( 'wr_layout_boxed_position', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'background-position', to )
		} );
	} );
	api( 'wr_layout_boxed_attachment', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'background-attachment', to )
		} );
	} );

	api( 'wr_layout_boxed_bg_mask_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_mask_bg', 'color': to,  'type': 'background-color' } ] );
		} );
	} );
	api( 'wr_general_container_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_inner_bg', 'color': to,  'type': 'background-color' } ] );
		} );
	} );

	// Main color
	api( 'custom_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_main_text', 'color': to,  'type': 'color' }, { 'classes': '.preview_main_bg', 'color': to, 'type': 'background-color' }, { 'classes': '.preview_main_border', 'color': to, 'type': 'border-color' } ] );
		} );
	} );
	api( 'wr_page_body_bg_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_outer_bg', 'color': to,  'type': 'background-color' } ] );
		} );
	} );
	api( 'general_fields_bg', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_fields_bg', 'color': to,  'type': 'background-color' } ] );
		} );
	} );
	api( 'general_line_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_line_color_normal', 'color': to + ' !important',  'type': 'border-color' }, { 'classes': '.preview_line_color_bg', 'color': to,  'type': 'background-color' } ] );
			$( '.widget-bordered .widget, .nitro-line .yith-wcwl-add-to-wishlist a' ).css( 'border-color', to );
			$( '.widget-bordered .widget .widget-title' ).css( 'border-color', to );
		} );
	} );
	api( 'general_overlay_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_secondary_bg_normal', 'color': to,  'type': 'background-color' }, { 'classes': '.preview_secondary_bg_border_right', 'color': to, 'type': 'border-right-color' } ] );
		} );
	} );

	// Page Loader
	api( 'page_loader_bg_color', function( value ) {
		value.bind( function( to ) {
			$( '.pageloader' ).css( 'background', to );
		} );
	} );

	// Widget Style
	var line_color     = window.parent.wp.customize.control( 'general_line_color' ).setting(),
		w_style        = window.parent.wp.customize.control( 'w_style' ).setting(),
		bg_color       = window.parent.wp.customize.control( 'general_overlay_color' ).setting(),
		content_bg     = window.parent.wp.customize.control( 'wr_general_container_color' ).setting(),
		heading_color  = window.parent.wp.customize.control( 'content_body_color' ).setting(),
		widget         = $( '.primary-sidebar .widget' );
		widget_title   = $( '.primary-sidebar .widget .widget-title' );

	api( 'w_style', function( value ) {
		value.bind( function( to ) {
			$( '.primary-sidebar' ).removeClass( function() {
				var toReturn = '',
					classes = this.className.split(' ');
				for(var i = 0; i < classes.length; i++ ) {
					if( /widget-style-\d{1,3}/.test( classes[i] ) ) {
						toReturn += classes[i] +' ';
					}
				}
				return toReturn;
			}).addClass( 'widget-style-' + to );
			var widget_bg  = window.parent.wp.customize.control( 'w_style_bg' ).setting(),
				widget_border  = window.parent.wp.customize.control( 'w_style_border' ).setting(),
				widget_divider = window.parent.wp.customize.control( 'w_style_divider' ).setting();
			if ( ( to == '1' || to == '2' ) && widget_bg == true ) {
				widget.css({ 'padding': '20px' });
				widget.addClass('overlay_bg');
			} else if ( ( to == '1' || to == '2' ) && widget_bg == false ) {
				widget.css({ 'background': 'none' });
				widget.removeClass('overlay_bg');
			}
			if ( ( to == '1' || to == '2' ) && widget_border == true ) {
				widget.css({ 'padding': '20px', 'border': '1px solid' + line_color });
			} else if ( ( to == '1' || to == '2' ) && widget_border == false ) {
				widget.css({ 'border': 'none' });
			}
			if ( to == '3' && widget_border == true ) {
				widget.css({ 'padding-left': '10px', 'border': 'none', 'border-left': '2px solid' + line_color });
			} else if ( to == '3' && widget_border == false ) {
				widget.css({ 'padding-left': '10px', 'border-left': 'none' });
			}
			if ( to == '4' ) {
				widget_title.css({'padding': '10px 15px', 'background': heading_color['heading_text'], 'border-bottom': 'none', 'color': content_bg });
				$( '.widget .widget-title > a' ).css('color', content_bg);
				if ( widget_bg == true ) {
					widget.css({ 'padding': '20px 10px', 'border': 'none' });
					widget.addClass('overlay_bg');
					widget_title.css({ 'margin': '-20px -10px 20px' });
				} else {
					widget.css({ 'background': 'none', 'padding': '0' });
					widget_title.css( 'margin', '0 0 18px' );
					widget.removeClass('overlay_bg');
				}
			}
			if ( ( to == '1' || to == '2' ) && widget_divider == true ) {
				widget_title.css({ 'border-bottom': '1px solid' + line_color, 'padding-bottom': '15px' });
			} else if ( ( to == '1' || to == '2' ) && widget_divider == false ) {
				widget_title.css( 'border-bottom', 'none' );
			}
			if ( to == '1' && widget_divider == false ) {
				widget_title.css( 'padding-bottom', '0' );
			}
			if ( to != '4' ) {
				widget_title.css({ 'margin': '0 0 18px', 'background': 'none', 'color': heading_color['heading_text'] });
				$( '.widget .widget-title > a' ).css('color', heading_color['heading_text']);
			}
			if ( to == '3' ) {
				widget.css({ 'background': 'none', 'padding': '0', 'padding-left': '10px' });
				widget_title.css({ 'border-bottom': 'none', 'padding': '0' });
			}
			if ( to == '1' || to == '2' ) {
				widget_title.css({ 'padding-left': '0', 'padding-top': '0' });
			}
			if ( to == '2' ) {
				widget_title.css( 'padding-bottom', '15px' );
			}

		} );
	} );
	api( 'w_style_bg', function( value ) {
		value.bind( function( to ) {
			var widget_border  = window.parent.wp.customize.control( 'w_style_border' ).setting();
				w_style        = window.parent.wp.customize.control( 'w_style' ).setting();
			if ( w_style == '1' || w_style == '2' ) {
				if ( to == true ) {
					widget.css({ 'background-color': bg_color, 'padding': '20px' });
					widget.addClass('overlay_bg');
				} else {
					widget.css( 'background', 'none' );
					widget.removeClass('overlay_bg');
				}
				if ( widget_border == false && to == false ) {
					widget.css({ 'padding': '0' });
				}
			}

			if ( w_style == '4' ) {
				if ( to == true ) {
					widget.css({ 'background-color': bg_color, 'padding': '20px 10px' });
					widget.addClass('overlay_bg');
					widget_title.css( 'margin', '-20px -10px 20px' );
				} else {
					widget.css({ 'background': 'none', 'padding': '0' });
					widget_title.css({ 'margin': '0', 'margin-bottom': '20px' });
					widget.removeClass('overlay_bg');
				}
			}
		} );
	} );

	api( 'w_style_border', function( value ) {
		value.bind( function( to ) {
			var widget_bg  = window.parent.wp.customize.control( 'w_style_bg' ).setting(),
				w_style    = window.parent.wp.customize.control( 'w_style' ).setting();

			if ( w_style == '1' || w_style == '2' ) {
				if ( to == true ) {
					widget.css({ 'border': '1px solid' + line_color, 'padding': '20px' });
				} else {
					widget.css( 'border', 'none' );
				}
				if ( widget_bg == false && to == false ) {
					widget.css({ 'padding': '0' });
				}
			}
			if ( w_style == '3' ) {
 				if ( to == true ) {
					widget.css( 'border-left', '2px solid' + line_color );
				} else {
					widget.css( 'border', 'none' );
				}
			}
		} );
	} );
	api( 'w_style_divider', function( value ) {
		value.bind( function( to ) {
			var w_style = window.parent.wp.customize.control( 'w_style' ).setting();
			if ( to == true ) {
				widget_title.css({ 'border-bottom': '1px solid' + line_color, 'padding-bottom': '15px' });
			} else {
				widget_title.css( 'border-bottom', 'none' );
			}
			if ( w_style == '1' && to == false ) {
				widget_title.css( 'padding-bottom', '0' );
			}
		} );
	} );

	// Pagination Style
	api( 'pagination_style', function( value ) {
		value.bind( function( to ) {
			$( '.pagination, .woocommerce-pagination' ).removeClass( function() {
				var toReturn = '',
					classes = this.className.split(' ');
				for(var i = 0; i < classes.length; i++ ) {
					if( /style-\d{1,3}/.test( classes[i] ) ) {
						toReturn += classes[i] +' ';
					}
				}
				return toReturn;
			}).addClass( to );
		} );
	} );

	// Update WooCommerce layout in real time.
	api( 'wc_archive_sidebar_width', function( value ) {
		value.bind( function( to ) {
			var archive_layout = window.parent.wp.customize.control( 'wc_archive_layout' ).setting();
			if ( 'right-sidebar' == archive_layout ) {
				$( '#shop-sidebar' ).css( 'width', to + 'px' );
				$( '#shop-main' ).css( 'width', 'calc(100% - ' + to + 'px)' );
			} else if ( 'left-sidebar' == archive_layout ) {
				$( '#shop-sidebar' ).css({
					'width': to + 'px'
				});
				$( '#shop-main' ).css({
					'width': 'calc(100% - ' + to + 'px)'
				});
			}
			var sidebar_sticky = $( '.primary-sidebar-sticky' );
			if( sidebar_sticky.length ) {
				$( '.primary-sidebar-inner' ).width( sidebar_sticky.width() );
			}

			if ( typeof $.fn.isotope != 'undefined' ) {
				$( '.wr-nitro-masonry' ).isotope();
			}
		} );
	} );
	api( 'wc_archive_item_transition', function( value ) {
		value.bind( function( to ) {
			$( '.product__image' ).removeClass( 'fade slide-from-left slide-from-right slide-from-top slide-from-bottom zoom-in zoom-out flip-back flip' ).addClass( to );
		} );
	} );
	api( 'wc_archive_layout_column_gutter', function( value ) {
		value.bind( function( to ) {
			// Get parent option.
		var parent           = window.parent.wp.customize.control( 'wc_archive_layout_column' ).setting(),
			width_pixel      = to * ( parent - 1 ) / parent + 'px',
			width_percentage = 100 / parent + '%';

		var archive_style 	 = window.parent.wp.customize.control( 'wc_archive_style' ).setting();

			if( archive_style == 'masonry' ) {
				$( '.wr-nitro-masonry .product' ).css({
					'padding-left' :  to / 2,
					'padding-right' :  to / 2,
					'padding-bottom' :  to,
				})
			} else if ( archive_style == 'grid' ) {
				$( '.products .product' ).css({
					'padding' : to / 2,
				})
				$( '.products.un-boxed' ).css({
					'margin-left' :  -to / 2,
					'margin-right' :  -to / 2,
				})
			}
			$( '#shop-sidebar .widget' ).css( 'margin-bottom', to );
		} );
	} );
	api( 'wc_archive_page_title_content', function( value ) {
		value.bind( function( to ) {
			$( '.post-type-archive-product .site-title h1' ).html( to );
		} );
	} );

	// Update gutter width for product category
	api( 'wc_categories_layout_column_gutter', function( value ) {
		value.bind( function( to ) {
			// Get parent option.
			var archive_style  = window.parent.wp.customize.control( 'wc_categories_style' ).setting();

			$( '.row.categories' ).css({
				'margin-left' :  -to / 2,
				'margin-right' : -to / 2,
			});
			$( '.cat-item' ).css({
				'padding' : to / 2,
			})
		} );
	} );

	// Update WooCommerce shop detail layout in real time.
	api( 'wc_single_product_custom_bg', function( value ) {
		value.bind( function( to ) {
			$( '.product .p-single-top' ).css( 'background', to );
		} );
	} );

	var blog_style = window.parent.wp.customize.control('blog_style').setting.get();

	// Update Blog layout in real time.
	api( 'blog_color', function( value ) {
		value.bind( function( to ) {
			var blog = $( '.row [class*="b-"]' ),
				content = $( '.row [class*="b-"] .entry-content' );

			blog.removeClass( 'boxed default' );
			blog.addClass( to );

			if ( to == 'boxed' ) {
				content.addClass( 'overlay_bg' );
			} else {
				content.removeClass( 'overlay_bg' );
			}

			if ( blog_style == 'classic' && to == 'boxed' ) {
				content.addClass( 'pd20' );
			} else if ( blog_style == 'classic' && to == 'default' ) {
				content.removeClass( 'pd20' );
			}

		} );
	} );
	api( 'blog_sidebar_width', function( value ) {
		value.bind( function( to ) {
			var bloglayout  = ['blog_layout'],
				layoutValue = window.parent.wp.customize.control(bloglayout).setting.get();
			if ( 'right-sidebar' == layoutValue ) {
				$( '.blog .primary-sidebar' ).css( 'width', to + 'px' );
				$( '.blog .main-content' ).css( 'width', 'calc(100% - ' + to + 'px)' );
			} else if ( 'left-sidebar' == layoutValue ) {
				$( '.blog .primary-sidebar' ).css({
					'width': to + 'px'
				});
				$( '.blog .main-content' ).css({
					'width': 'calc(100% - ' + to + 'px)'
				});
			}

			var sidebar_sticky = $( '.primary-sidebar-sticky' );
			if( sidebar_sticky.length ) {
				$( '.primary-sidebar-inner' ).width( sidebar_sticky.width() );
			}

			if ( typeof $.fn.isotope != 'undefined' ) {
				$( '.wr-nitro-masonry' ).isotope();
			}
		} );
	} );
	api( 'blog_single_sidebar_width', function( value ) {
		value.bind( function( to ) {
			var singlelayout = ['blog_single_layout'],
				layoutValue  = window.parent.wp.customize.control(singlelayout).setting.get();
			if ( 'right-sidebar' == layoutValue ) {
				$( '.single-post .primary-sidebar' ).css( 'width', to + 'px' );
				$( '.single-post .main-content' ).css( 'width', 'calc(100% - ' + to + 'px)' );
			} else if ( 'left-sidebar' == layoutValue ) {
				$( '.single-post .primary-sidebar' ).css({
					'width': to + 'px'
				});
				$( '.single-post .main-content' ).css({
					'width': 'calc(100% - ' + to + 'px)'
				});
			}
			var sidebar_sticky = $( '.primary-sidebar-sticky' );
			if( sidebar_sticky.length ) {
				$( '.primary-sidebar-inner' ).width( sidebar_sticky.width() );
			}

		} );
	} );

	api( 'wr_page_title_fullscreen', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( 'body:not(.wr-setting-overrided) .site-title' ).addClass( 'full' );
			} else {
				$( 'body:not(.wr-setting-overrided) .site-title' ).removeClass( 'full' );
			}
		} );
	} );
	api( 'wr_page_title_padding_top', function( value ) {
		value.bind( function( to ) {
			$( 'body:not(.wr-setting-overrided) .site-title' ).css( 'padding-top', to );
		} );
	} );
	api( 'wr_page_title_padding_bottom', function( value ) {
		value.bind( function( to ) {
			$( 'body:not(.wr-setting-overrided) .site-title' ).css( 'padding-bottom', to );
		} );
	} );
	api( 'wr_page_title_size', function( value ) {
		value.bind( function( to ) {
			$( 'body:not(.wr-setting-overrided) .site-title' ).css( 'background-size', to );
		} );
	} );
	api( 'wr_page_title_repeat', function( value ) {
		value.bind( function( to ) {
			$( 'body:not(.wr-setting-overrided) .site-title' ).css( 'background-repeat', to );
		} );
	} );
	api( 'wr_page_title_position', function( value ) {
		value.bind( function( to ) {
			$( 'body:not(.wr-setting-overrided) .site-title' ).css( 'background-position', to );
		} );
	} );
	api( 'wr_page_title_attachment', function( value ) {
		value.bind( function( to ) {
			$( 'body:not(.wr-setting-overrided) .site-title' ).css( 'background-attachment', to );
		} );
	} );

	api( 'wr_page_title_bg_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_page_site_title', 'color': to,  'type': 'background-color' } ] );
		} );
	} );
	api( 'wr_page_title_mask_color', function( value ) {
		value.bind( function( to ) {
			$( '.site-title .mask' ).css( 'background-color', to );
		} );
	} );
	api( 'wr_page_title_heading_font', function( value ) {
		value.bind( function( to ) {
			$( '.site-title h1' ).css({
				'font-style': ( 1 == to['italic'] ) ? 'italic' : 'normal',
				'text-decoration': ( 1 == to['underline'] ) ? 'underline' : 'none',
				'text-transform': ( 1 == to['uppercase'] ) ? 'uppercase' : 'none'
			} );
		} );
	} );
	api( 'wr_page_title_link_colors', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_page_title_link_normal', 'color': to['normal'],  'type': 'color' }, { 'classes': '.preview_page_title_link_hover', 'color': to['hover'],  'type': 'color' } ] );
		} );
	} );
	api( 'wr_page_title_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_page_site_title', 'color': to['body'],  'type': 'color' }, { 'classes': '.preview_page_title_heading', 'color': to['head'],  'type': 'color' } ] );
		} );
	} );
	api( 'wr_page_title_heading_font_size', function( value ) {
		value.bind( function( to ) {
			$( '.site-title h1' ).css( 'font-size', to + 'px' );
		} );
	} );
	api( 'wr_page_title_heading_line_height', function( value ) {
		value.bind( function( to ) {
			$( '.site-title h1' ).css( 'line-height', to + 'px' );
		} );
	} );
	api( 'wr_page_title_heading_letter_spacing', function( value ) {
		value.bind( function( to ) {
			$( '.site-title h1' ).css( 'letter-spacing', to + 'px' );
		} );
	} );
	api( 'wr_page_layout_sidebar_width', function( value ) {
		value.bind( function( to ) {
			var pagelayout  = ['wr_page_layout'],
				sidebar_sticky = $( '.primary-sidebar-sticky' ),
				layoutValue = window.parent.wp.customize.control(pagelayout).setting.get();
			if ( 'right-sidebar' == layoutValue ) {
				$( '.page-content .primary-sidebar' ).css( 'width', to + 'px' );
				$( '.page-content .main-content' ).css( 'width', 'calc(100% - ' + to + 'px)' );
			} else if ( 'left-sidebar' == layoutValue ) {
				$( '.page-content .primary-sidebar' ).css({
					'width': to + 'px'
				});
				$( '.page-content .main-content' ).css({
					'width': 'calc(100% - ' + to + 'px)'
				});
			}
			if( sidebar_sticky.length ) {
				$( '.primary-sidebar-inner' ).width( sidebar_sticky.width() );
			}

		} );
	} );
	api( 'page_404_title_font_size', function( value ) {
		value.bind( function( to ) {
			$( '.error404 h2' ).css({'font-size': to, 'line-height': to + 'px' });
		} );
	} );
	api( 'page_404_title_color', function( value ) {
		value.bind( function( to ) {
			$( '.heading-404 h2' ).css( 'color', to );
		} );
	} );
	api( 'page_404_bg_color', function( value ) {
		value.bind( function( to ) {
			$( '.error404 .content-404' ).css( 'background-color', to );
		} );
	} );
	api( 'page_404_bg_image_size', function( value ) {
		value.bind( function( to ) {
			$( '.error404 .content-404' ).css( 'background-size', to );
		} );
	} );
	api( 'page_404_bg_image_repeat', function( value ) {
		value.bind( function( to ) {
			$( '.error404 .content-404' ).css( 'background-repeat', to );
		} );
	} );
	api( 'page_404_bg_image_position', function( value ) {
		value.bind( function( to ) {
			$( '.error404 .content-404' ).css( 'background-position', to );
		} );
	} );
	api( 'page_404_bg_image_attachment', function( value ) {
		value.bind( function( to ) {
			$( '.error404 .content-404' ).css( 'background-attachment', to );
		} );
	} );
	api( 'page_404_content', function( value ) {
		value.bind( function( to ) {
			$( '.content-404 .content-inner' ).html( to );
		} );
	} );

	// Update Single post title in real time.
	api( 'blog_single_title_font_size', function( value ) {
		value.bind( function( to ) {
			$( '.post-title .entry-title' ).css({
				'font-size': to,
				'line-height': to + 'px'
			});
		} );
	} );
	api( 'blog_single_title_padding_top', function( value ) {
		value.bind( function( to ) {
			$( '.post-title' ).css( 'padding-top', to );
		} );
	} );
	api( 'blog_single_title_padding_bottom', function( value ) {
		value.bind( function( to ) {
			$( '.post-title' ).css( 'padding-bottom', to );
		} );
	} );

	// Update Footer in real time.
	api( 'footer_fullwidth', function( value ) {
		value.bind( function( to ) {
			if ( '1' == to ) {
				$( '.footer .top-inner, .footer .info' ).css( 'max-width', '100%' );
			} else {
				$( '.footer .top-inner, .footer .info' ).css( 'max-width', '1170px' );
			}
		} );
	} );
	api( 'footer_top_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_footer_top_text', 'color': to['text'],  'type': 'color' }, { 'classes': '.preview_footer_top_heading', 'color': to['heading'],  'type': 'color' } ] );
		} );
	} );
	api( 'footer_top_bg_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_footer_top_bg', 'color': to,  'type': 'background-color' } ] );
		} );
	} );
	api( 'footer_bg_image_size', function( value ) {
		value.bind( function( to ) {
			$( '.footer' ).css( 'background-size', to );
		} );
	} );
	api( 'footer_bg_image_repeat', function( value ) {
		value.bind( function( to ) {
			$( '.footer' ).css( 'background-repeat', to );
		} );
	} );
	api( 'footer_bg_image_position', function( value ) {
		value.bind( function( to ) {
			$( '.footer' ).css( 'background-position', to );
		} );
	} );
	api( 'footer_bg_image_attachment', function( value ) {
		value.bind( function( to ) {
			$( '.footer' ).css( 'background-attachment', to );
		} );
	} );
	api( 'footer_bot_text', function( value ) {
		value.bind( function( to ) {
			$( '.footer .bot .info' ).html( to );
		} );
	} );
	api( 'footer_bot_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_footer_bot_normal', 'color': to['text'],  'type': 'color' }, { 'classes': '.preview_footer_bot_normal', 'color': to['bg'],  'type': 'background-color' } ] );
		} );
	} );
	api( 'footer_top_link_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_footer_top_link_normal', 'color': to['normal'],  'type': 'color' }, { 'classes': '.preview_footer_top_link_hover', 'color': to['hover'],  'type': 'color' } ] );
		} );
	} );
	api( 'footer_bot_link_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_footer_bot_link_normal', 'color': to['normal'],  'type': 'color' }, { 'classes': '.preview_footer_bot_link_hover', 'color': to['hover'],  'type': 'color' } ] );
		} );
	} );

	// Update Meta settings in real time.
	api( 'content_meta_color', function( value ) {
		value.bind( function( to ) {
			$( '.entry-meta, .entry-meta i, .entry-meta a,.entry-meta span a, time, .sc-product-package .p-package-cat a, .widget li .info' ).css({ 'color': to });
		} );
	} );

	// Update Button settings in real time.
	api( 'btn_font', function( value ) {
		value.bind( function( to ) {
			$( '.wr-btn,button,.button,.submit' ).css({
				'font-family': '"' + to['family'] + '"',
				'font-weight': ( to['fontWeight'] ? to['fontWeight'] : '400' ),
				'font-style': ( 1 == to['italic'] ) ? 'italic' : 'normal',
				'text-decoration': ( 1 == to['underline'] ) ? 'underline' : 'none',
				'text-transform': ( 1 == to['uppercase'] ) ? 'uppercase' : 'none'
			} );
			$( 'input[type="submit"]' ).addRule({
				'font-family': '"' + to['family'] + '"',
				'font-weight': ( to['fontWeight'] ? to['fontWeight'] : '400' ),
				'font-style': ( 1 == to['italic'] ) ? 'italic' : 'normal',
				'text-decoration': ( 1 == to['underline'] ) ? 'underline' : 'none',
				'text-transform': ( 1 == to['uppercase'] ) ? 'uppercase' : 'none'
			} );

			if( $( '#btn_font_link' ).length ) {
				$( '#btn_font_link' ).attr( 'href', "https://fonts.googleapis.com/css?family=" + to['family'] + ':' + ( to['fontWeight'] ? to['fontWeight'] : '400' ) );
			} else {
				$( 'head' ).append( "<link id='btn_font_link' href='https://fonts.googleapis.com/css?family=" + to['family'] + ':' + ( to['fontWeight'] ? to['fontWeight'] : '400' ) + "' rel='stylesheet' type='text/css'>" );
			}
		} );
	} );
	api( 'btn_font_size', function( value ) {
		value.bind( function( to ) {
			$( '.wr-btn,.button,.submit,.wpcf7-submit' ).css( 'font-size', to );
			$( 'input[type="submit"]' ).addRule( 'font-size', to );
		} );
	} );
	api( 'btn_line_height', function( value ) {
		value.bind( function( to ) {
			var border_width  = layoutValue = window.parent.wp.customize.control(['btn_border_width']).setting.get();
			$( '.wr-btn,.button,.submit,.wpcf7-submit' ).css({ 'line-height': ( to - border_width * 2 )+ 'px', 'height': to + 'px' });
			$( 'input[type="submit"]' ).addRule({ 'line-height': ( to - border_width * 2 )+ 'px', 'height': to + 'px' });
			$( '.p-single-action > div a, .p-single-action .variations .actions-button a' ).css({ 'line-height': ( to - border_width * 2 )+ 'px', 'height': to + 'px', 'width': to + 'px' });
			$( '.quantity input[type="number"]' ).css({ 'line-height': ( to - border_width * 2 )+ 'px', 'height': ( to - 2 ) + 'px', 'width': ( to - 2 ) + 'px' });
			$( '.quantity' ).css( 'width', ( to + 32 )+ 'px');
			$( '.quantity .btn-qty a' ).css({ 'height' : ( to / 2 ) + 'px', 'height' : ( to / 2 )+ 'px' });
		} );
	} );
	api( 'btn_letter_spacing', function( value ) {
		value.bind( function( to ) {
			$( '.wr-btn,.button,.submit,.wpcf7-submit' ).css( 'letter-spacing', to );
			$( 'input[type="submit"]' ).addRule( 'letter-spacing', to );
		} );
	} );
	api( 'btn_border_radius', function( value ) {
		value.bind( function( to ) {
			$( '.wr-btn,.button,.submit, .p-single-action .yith-wcwl-add-to-wishlist a, .p-single-action .product__compare > a, .wpcf7-submit' ).css( 'border-radius', to );
			$( 'input[type="submit"]' ).addRule( 'border-radius', to );
		} );
	} );
	api( 'btn_border_width', function( value ) {
		value.bind( function( to ) {
			var line_height  = layoutValue = window.parent.wp.customize.control(['btn_line_height']).setting.get();
			$( '.wr-btn,.button,.submit,.wpcf7-submit' ).css({ 'border-width': to, 'line-height': ( line_height - to * 2 )+ 'px', 'height': line_height + 'px' });
			$( 'input[type="submit"]' ).addRule( 'border-width', to );
		} );
	} );
	api( 'btn_padding', function( value ) {
		value.bind( function( to ) {
			$( '.wr-btn,.button,.submit, .wpcf7-submit' ).css({ 'padding-left': to, 'padding-right': to });
			$( 'input[type="submit"]' ).addRule({ 'padding-left': to, 'padding-right': to });
		} );
	} );
	api( 'btn_primary_bg_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_btn_primary_normal', 'color': to['normal'],  'type': 'background-color' }, { 'classes': '.preview_btn_primary_hover', 'color': to['hover'],  'type': 'background-color' } ] );
		} );
	} );
	api( 'btn_primary_border_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_btn_primary_normal', 'color': to['normal'],  'type': 'border-color' }, { 'classes': '.preview_btn_primary_hover', 'color': to['hover'],  'type': 'border-color' } ] );
		} );
	} );
	api( 'btn_primary_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_btn_primary_normal', 'color': to['normal'],  'type': 'color' }, { 'classes': '.preview_btn_primary_hover', 'color': to['hover'],  'type': 'color' } ] );
		} );
	} );
	api( 'btn_secondary_bg_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_btn_secondary_normal', 'color': to['normal'],  'type': 'background-color' }, { 'classes': '.preview_btn_secondary_hover', 'color': to['hover'],  'type': 'background-color' } ] );
		} );
	} );
	api( 'btn_secondary_border_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_btn_secondary_normal', 'color': to['normal'],  'type': 'border-color' }, { 'classes': '.preview_btn_secondary_hover', 'color': to['hover'],  'type': 'border-color' } ] );
		} );
	} );
	api( 'btn_secondary_color', function( value ) {
		value.bind( function( to ) {
			update_live_preview( [ { 'classes': '.preview_btn_secondary_normal', 'color': to['normal'],  'type': 'color' }, { 'classes': '.preview_btn_secondary_hover', 'color': to['hover'],  'type': 'color' } ] );
		} );
	} );

	// Back to top
	var backtop = $( '#wr-back-top a' );
	api( 'back_top_type', function( value ) {
		value.bind( function( to ) {
			if ( 'circle' == to ) {
				backtop.css( 'border-radius', '100%' );
			} else if ( 'rounded' == to ) {
				backtop.css( 'border-radius', '5px' );
			} else {
				backtop.css( 'border-radius', '0' );
			}
		});
	} );
	api( 'back_top_style', function( value ) {
		value.bind( function( to ) {
			if ( 'light' == to ) {
				backtop.addClass( 'overlay_bg nitro-line' ).removeClass( 'heading-bg' ).css( 'color', '#333' );
			} else {
				backtop.addClass( 'heading-bg' ).removeClass( 'overlay_bg nitro-line' ).css( 'color', '#fff' );
			}
		});
	} );
	api( 'back_top_size', function( value ) {
		value.bind( function( to ) {
			backtop.css({ 'height': to, 'width': to, 'line-height': ( to - 5 ) + 'px' });
		});
	} );
	api( 'back_top_icon_size', function( value ) {
		value.bind( function( to ) {
			backtop.css( 'font-size', to );
		});
	} );
	// Right to left
	api( 'rtl', function( value ) {
		value.bind( function( to ) {
			if ( to == 1 ) {
				$( 'html' ).attr( 'dir', 'rtl' );
				$( 'body' ).addClass( 'rtl' );
			} else {
				$( 'html' ).attr( 'dir', 'ltr' );
				$( 'body' ).removeClass( 'rtl' );
			}
		} );
	} );
})(jQuery);
