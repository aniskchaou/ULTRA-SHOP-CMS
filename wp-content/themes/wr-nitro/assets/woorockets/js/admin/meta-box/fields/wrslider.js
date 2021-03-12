( function( $ ) {
	$.WR_Slider_Field = function( field ) {
		var self = this;

		self.field = field;

		self.init();
	}

	$.WR_Slider_Field.prototype = {
		init: function() {
			var self = this;

			// Get necessary elements.
			self.container = $( '#' + self.field.id + '-container' );

			// Init slider control.
			self.container.children( 'input' ).attr( 'type', 'hidden' ).after( $( '<div>' ).slider( {
			    range: 'min',
			    min: self.field.choices.min ? parseInt( self.field.choices.min ) : 0,
			    max: self.field.choices.max ? parseInt( self.field.choices.max ) : 100,
			    step: self.field.choices.step ? parseInt( self.field.choices.step ) : 1,
			    value: self.field.value_data ? parseInt( self.field.value_data ) : 0,
			    create: function( event, ui ) {
				    // Update label.
				    var unit = self.field.choices.unit ? self.field.choices.unit : '';

				    $( this ).children( '.ui-slider-handle' ).html( '<span>' + $( this ).slider( 'value' ) + unit + '</span>' );
			    },
			    slide: function( event, ui ) {
				    // Update label.
				    var unit = self.field.choices.unit ? self.field.choices.unit : '';

				    $( ui.handle ).html( '<span>' + ui.value + unit + '</span>' );

				    // Set new value.
				    self.container.children( 'input' ).val( ui.value );
			    },
			    change: function( event, ui ) {
				    // Update label.
				    var unit = self.field.choices.unit ? self.field.choices.unit : '';

				    $( ui.handle ).html( '<span>' + ui.value + unit + '</span>' );

				    // Set new value.
				    self.container.children( 'input' ).val( ui.value );
			    }
			} ) );

			// Setup reset to default action.
			self.container.on( 'click', '.reset-to-default', function( event ) {
				event.preventDefault();

				// Set default value.
				self.container.children( 'input' ).next().slider( 'value', self.field.std );
			} );
		},
	}
} )( jQuery );
