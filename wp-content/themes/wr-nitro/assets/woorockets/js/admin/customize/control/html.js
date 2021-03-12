( function( $ ) {
	$( window ).load( function() {
		// Loop thru all control containers to init.
		$( 'div[id^="wr-' + wr_nitro_customize_html.type + '-"]' ).each( function() {
			var self = $( this );

			// Get the control ID.
			self.id = self.attr( 'id' ).replace( 'wr-' + wr_nitro_customize_html.type + '-', '' );

			// Init radio images.
			self.find( '.wr-html' ).click( function() {
				if ( !$( this ).hasClass( 'selected' ) ) {
					$( this ).parent().children( '.selected' ).removeClass( 'selected' );
					$( this ).addClass( 'selected' );

					if ( !$( this ).children( 'input[type="radio"]' ).attr( 'checked' ) ) {
						$( this ).children( 'input[type="radio"]' ).trigger( 'click' );
					}

					// Set new value.
					wp.customize.control( self.id ).setting.set( $( this ).children( 'input[type="radio"]' ).val() );
				}
			} );
		} );
	} );
} )( jQuery );
