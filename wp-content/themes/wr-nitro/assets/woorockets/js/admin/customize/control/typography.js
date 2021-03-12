( function( $ ) {
	$( window ).load( function() {
		// Loop thru all control containers to init.
		$( 'div[id^="wr-' + wr_nitro_customize_typography.type + '-"]' ).each( function() {
			var self = $( this );

			// Get the control ID.
			self.id = self.attr( 'id' ).replace( 'wr-' + wr_nitro_customize_typography.type + '-', '' );

			// Get current value.
			var value = wp.customize.control( self.id ).setting.get(), list_font_weight = [];

			// Get necessary elements.
			self.input_color = self.find( 'input[name="color"]' );

			// Track click event.
			self.find( '.wr-image-selected' ).click( function( e ) {
				var _this = $( this );

				_this.next().toggle();

				window.wr_click_outside( _this, '.customize-control-select', function( e ) {
					_this.next().hide();
				} );

				e.stopPropagation();
			} );

			// Setup Google fonts.
			var google_fonts_container = self.find( '.google-fonts-list' );

			if ( google_fonts_container.length ) {
				for ( var i in wr_nitro_customize_typography.google_fonts ) {
					var item = $( '<li />' ).appendTo( google_fonts_container );

					item.addClass( 'wr-select-image ' + i.toLowerCase().replace( /\s+/g, '-' ) );
					item.addClass( value.family == i ? 'selected' : '' );
					item.attr( 'data-value', i );
					item.html( '<span>' + i + '</span>' );

					if ( value.family == i ) {
						list_font_weight = wr_nitro_customize_typography.google_fonts[ i ];
					}
				}
			}

			// Setup font weight list.
			var font_weight_selector = self.find( 'select[name="fontWeight"]' );

			if ( list_font_weight.length > 1 ) {
				font_weight_selector.parent().show().find( 'option' ).each( function( i, e ) {
					if ( list_font_weight.indexOf( parseInt( $( e ).attr( 'value' ) ) ) > -1 ) {
						$( e ).show();

						if ( parseInt( $( e ).attr( 'value' ) ) == parseInt( value.fontWeight ) ) {
							font_weight_selector.val( $( e ).attr( 'value' ) );
						}
					} else {
						$( e ).hide();
					}
				} );
			} else {
				font_weight_selector.val( wr_nitro_customize_typography.default_font_weight ).parent().hide();
			}

			// Init select image.
			self.find( '.wr-select-image' ).click( function() {
				var _this = $(this);

				if ( ! _this.hasClass( 'selected' ) ) {
					var font = _this.attr( 'data-value' );
					var font_class = font.toLowerCase().replace( /\s+/g, '-' );

					// Update selection status.
					self.find( '.wr-image-selected span' ).text( font );
					self.find( '.wr-image-selected' ).attr( 'class', '' ).addClass( 'wr-image-selected ' + font_class );

					// Update selected item in the list.
					self.find( '.wr-select-image.selected' ).removeClass( 'selected' );
					_this.addClass( 'selected' );

					// Set font weight
					list_font_weight = wr_nitro_customize_typography.google_fonts[ font ];

					if ( wr_nitro_customize_typography.google_fonts[ font ].length == 1 ) {
						font_weight_selector.val( wr_nitro_customize_typography.default_font_weight ).parent().hide();
					} else {
						font_weight_selector.parent().show().find( 'option' ).each( function( i, e ) {
							if ( list_font_weight.indexOf( parseInt( $( e ).attr( 'value' ) ) ) > -1 ) {
								$( e ).show();
							} else {
								$( e ).hide();
							}
						} );

						if ( list_font_weight.indexOf( font_weight_selector.val() ) < 0 ) {
							font_weight_selector.val( wr_nitro_customize_typography.default_font_weight );
						}
					}

					// Trigger input hidden
					self.find( '.data-family' ).val( font ).trigger( 'change' );
				}

				_this.closest( '.wr-select-image-container' ).hide();
			} );

			// Track checkbox input.
			self.on( 'click', 'input[type="checkbox"]', function() {
				$( this ).parent()[ $( this ).attr( 'checked' ) ? 'addClass' : 'removeClass' ]( 'active' );
			} );

			// Track data input.
			self.on( 'change', 'ul, input[name], select[name]', function() {
				// Build new value.
				var value = {};

				self.find( 'ul, input[name], select[name]' ).each( function( i, e ) {
					if ( $( e ).attr( 'name' ) !== undefined ) {
						if ( $( e ).attr( 'type' ) == 'checkbox' ) {
							value[ $( e ).attr( 'name' ) ] = $( e ).attr( 'checked' ) ? 1 : 0;
						} else if ( $( e ).attr( 'type' ) == 'radio' ) {
							if ( $( e ).attr( 'checked' ) ) {
								value[ $( e ).attr( 'name' ) ] = $( e ).val();
							}
						} else if ( $( e ).attr( 'name' ) == 'color' ) {
							var color = $( e ).spectrum( 'get' );
							value[ $( e ).attr( 'name' ) ] = color ? ( color.getAlpha() == 1 ? color.toHexString() : color.toRgbString() ) : '';
						} else {
							value[ $( e ).attr( 'name' ) ] = $( e ).val();
						}
					}
				} );

				// Set new value.
				wp.customize.control( self.id ).setting.set( value );
			} );
		} );

		// Search font
		$( 'body' ).on( 'keyup', '.wr-select-image-container .txt-sfont', function( e ) {
			var keyword = $( this ).val();
			var list_fonts = $( this ).closest( '.wr-select-image-container' ).find( 'li' );

			if ( keyword ) {
				if ( window.keyword_font_old == undefined || window.keyword_font_old != keyword || e.keyCode == 13 || e.keyCode == 86 ) {
					list_fonts.hide();
					list_fonts.each( function() {
						var textField = $( this ).attr( 'data-value' ).toLowerCase();
						var keyword_lowercase = keyword.toLowerCase().trim();

						if ( textField.indexOf( keyword_lowercase ) == -1 ) {
							$( this ).hide();
						} else {
							$( this ).fadeIn( 200 );
						}
					} );

					window.keyword_font_old = keyword;
				}
			} else {
				list_fonts.show();
			}
		} );
	} );
} )( jQuery );
