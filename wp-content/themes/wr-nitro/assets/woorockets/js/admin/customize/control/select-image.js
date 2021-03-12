( function( $ ) {
	$( window ).load( function() {
		// Loop thru all control containers to init.
		$( 'div[id^="wr-' + wr_nitro_customize_select_image.type + '-"]' ).each( function() {
			var self = $( this );

			// Get the control ID.
			self.id = self.attr( 'id' ).replace( 'wr-' + wr_nitro_customize_select_image.type + '-', '' );

			// Track click event.
			self.find( '.wr-image-selected' ).click( function( e ) {
				var _this = $( this );

				_this.next().toggle();

				window.wr_click_outside( _this, '.customize-control-content', function( e ) {
					_this.next().hide();
				} );

				e.stopPropagation();
			} );

			// Init select image.
			self.find( '.wr-select-image' ).click( function() {
				if ( !$( this ).hasClass( 'selected' ) ) {
					var value = $( this ).find( 'input' ).val();

					self.find( '.wr-image-selected' ).attr( 'class', '' ).addClass( 'wr-image-selected ' + value );

					$( this ).parent().children( '.selected' ).removeClass( 'selected' );
					$( this ).addClass( 'selected' );

					if ( !$( this ).children( 'input[type="radio"]' ).attr( 'checked' ) ) {
						$( this ).children( 'input[type="radio"]' ).trigger( 'click' );
					}

					// Set new value.
					wp.customize.control( self.id ).setting.set( $( this ).children( 'input[type="radio"]' ).val() );
				}

				self.find( '.wr-image-selected' ).next().toggle();

			} );
		} );
	} );
} )( jQuery );
