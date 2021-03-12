( function( $ ) {
	$( window ).load( function() {
		// Loop thru all control containers to init.
		$( 'div[id^="wr-' + wr_nitro_customize_upload_font.type + '-"]' ).each( function() {
			var self = $( this );

			// Get the control ID.
			self.id = self.attr( 'id' ).replace( 'wr-' + wr_nitro_customize_upload_font.type + '-', '' );

			// Setup font upload field.
			self.on( 'click', '.upload-font', function( event ) {
				event.preventDefault();

				// Setup media selector once.
				var frame = window.wr_custom_font_selector;

				if ( ! frame ) {
					// Create the media frame.
					frame = wp.media( {
						button: {
							text: $( this ).text(),
						},
						states: [
							new wp.media.controller.Library( {
								title: $( this ).text(),
								library: wp.media.query( { type: [
									'application/vnd.ms-fontobject',
									'application/x-font-opentype',
									'application/x-font-ttf',
									'application/font-woff',
									'application/font-woff2',
								] } ),
								multiple: false,
								date: false,
							} )
						]
					} );

					// When a file is selected, run a callback.
					frame.on( 'select', function() {
						// Grab the selected attachment.
						var attachment = frame.state().get( 'selection' ).first();

						// Verify the selected file.
						if ( attachment.attributes.url.match( /\.(eot|otf|ttf|woff|woff2)$/ ) ) {
							// Update the option value.
							wp.customize.control( frame.setting ).setting.set( attachment.attributes.url );
						}
					} );

					// Work-around to deselect the uploaded file if it is not a supported font file.
					frame.on( 'open', function() {
						frame._checking_interval = setInterval( function() {
							// Check if there is any file selected.
							var attachment = frame.state().get( 'selection' ).first();

							// Verify the selected file.
							if ( attachment && attachment.attributes && attachment.attributes.url ) {
								if ( ! attachment.attributes.url.match( /\.(eot|otf|ttf|woff|woff2)$/ ) ) {
									frame.reset();
								}
							}
						}, 50 );
					} );

					frame.on( 'close', function() {
						clearInterval( frame._checking_interval );
					} );

					// Store media selector object for later reference
					window.wr_custom_font_selector = frame;
				}

				// Store affected control and setting to media frame object.
				frame.control = $( this ).closest( '.customize-control-content' ),
				frame.setting = frame.control.attr( 'id' ).replace( 'wr-' + wr_nitro_customize_upload_font.type + '-', '' );

				frame.open();
			} ).on( 'click', 'button.remove-font', function( event ) {
				event.preventDefault();

				// Clear the option value.
				wp.customize.control( self.id ).setting.set( '' );
			} );

			// Setup font preview.
			function preview_font( url ) {
				// Hide preview and the button to remove current font.
				self.find( '.preview-font, .remove-font').addClass( 'hidden' );

				// Update label for the button to select font.
				self.find( '.upload-font').text( wr_nitro_customize_upload_font.select_font_label );

				if ( url ) {
					// Load font.
					$.ajax( {
						timeout: 500,
						url: url,
						complete: function( response ) {
							if ( response.status == 200 ) {
								// Generate then add CSS rules for previewing font.
								var sheet = document.styleSheets[ document.styleSheets.length - 1 ],

								fontFamily = url.match( /\/([^\/\.]+)\.(eot|otf|ttf|woff|woff2)$/ ),

								cssRules = {
									'@font-face':
										'font-family: "' + fontFamily[1] + '"; src: url("' + url + '");',
								};

								cssRules[ '#' + self.attr( 'id' ) + ' > .preview-font' ] = 'font-family: "' + fontFamily[1] + '";';

								for ( var selector in cssRules ) {
									if ( sheet.insertRule && typeof sheet.cssRules != 'undefined'
										&& sheet.cssRules != null && typeof sheet.cssRules.length != 'undefined' )
									{
										sheet.insertRule( selector + " {" + cssRules[ selector ] + "}", sheet.cssRules.length );
									} else if (sheet.addRule) {
										sheet.addRule( selector, cssRules[ selector ] );
									}
								}

								// Show preview and the button to remove current font.
								self.find( '.preview-font, .remove-font').removeClass( 'hidden' );

								// Update label for the button to change font.
								self.find( '.upload-font').text( wr_nitro_customize_upload_font.change_font_label );
							}
						},
					} );
				}
			}

			wp.customize.control( self.id ).setting.bind( 'change', preview_font );

			// Preview current font.
			preview_font( wp.customize.control( self.id ).setting.get() );
		} );
	} );
} )( jQuery );
