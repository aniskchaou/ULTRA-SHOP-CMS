( function( $ ) {
	$( window ).load( function() {
		// Loop thru all control containers to init.
		$( 'div[id^="wr-' + wr_nitro_customize_colors.type + '-"]' ).each( function() {
			var self = $( this );

			// Get the control ID.
			self.id = self.attr( 'id' ).replace( 'wr-' + wr_nitro_customize_colors.type + '-', '' );

			// Init color picker.
			self.find( 'input[type="text"]' ).each( function( i, e ) {
				$( e ).spectrum( {
				    color: $( e ).val(),
				    showInput: true,
				    showInitial: true,
				    allowEmpty: true,
				    showAlpha: true,
				    clickoutFiresChange: true,
				    cancelText: wr_nitro_customize_colors.cancel ? wr_nitro_customize_colors.cancel : 'Cancel',
				    chooseText: wr_nitro_customize_colors.choose ? wr_nitro_customize_colors.choose : 'Choose',
				    preferredFormat: 'hex',
				    show: function() {
					    if ( !$( '.sp-default' ).length ) {
						    $( '.sp-cancel' ).after( '<a class="sp-default" href="#">' + ( wr_nitro_customize_colors[ 'default' ] ? wr_nitro_customize_colors[ 'default' ] : 'Default' ) + '</a>' );
					    }

					    $( '.sp-default' ).off( 'click' ).on( 'click', function( event ) {
						    event.preventDefault();

						    $( e ).spectrum( 'set', $( e ).attr( 'default-value' ) );

						    $( e ).parent().children( '.color-hex' ).text( $( e ).attr( 'default-value' ) );

						    $( e ).trigger( 'change' );
					    } );
				    },
				    move: function( color ) {
					    $( e ).parent().children( '.color-hex' ).text( '' );

					    if ( color ) {
						    $( e ).parent().children( '.color-hex' ).text( color.getAlpha() == 1 ? color.toHexString() : color.toRgbString() );
					    }
					    $( e ).trigger( 'change' );
				    },
				    change: function( color ) {
					    $( e ).parent().children( '.color-hex' ).text( '' );

					    if ( color ) {
						    $( e ).parent().children( '.color-hex' ).text( color.getAlpha() == 1 ? color.toHexString() : color.toRgbString() );
					    }
				    },
				    hide: function( color ) {
						if( ! color ) {
							$(this).siblings('.color-hex').text('');
							$(this).val('').trigger('change');
						} else {
							var val = color.getAlpha() == 1 ? color.toHexString() : color.toRgbString();
							$(this).siblings('.color-hex').text(val);
							$(this).val(val).trigger('change');
							$('.sp-container:visible').find('.sp-input').val(val);
						}
					}
				} );
			} );

			// Track data input.
			self.on( 'change', 'input[name]', function() {
				// Build new value.
				var value = {};

				self.find( 'input[name]' ).each( function( i, e ) {
					var color = $( e ).spectrum( 'get' );

					if ( $( e ).attr( 'name' ) == '_' ) {
						value = color ? ( color.getAlpha() == 1 ? color.toHexString() : color.toRgbString() ) : '';
					} else {
						value[ $( e ).attr( 'name' ) ] = color ? ( color.getAlpha() == 1 ? color.toHexString() : color.toRgbString() ) : '';
					}
				} );

				// Set new value.
				wp.customize.control( self.id ).setting.set( value );
			} );
		} );
	} );
} )( jQuery );
