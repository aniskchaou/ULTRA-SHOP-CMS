( function( $ ) {
	$.WR_Slider_Control = function( data ) {
		var self = this;

		self.data = data;

		$( window ).load( self.init.bind( self ) );
	}

	$.WR_Slider_Control.prototype = {
		init: function() {
			var self = this;

			// Get necessary elements.
			self.container = $( '#wr-' + wr_nitro_customize_slider.type + '-' + self.data.id );
			self.slider_control = self.container.find( 'input[type="range"]' );

			// Init slider control.
			self.slider_control.addClass( 'hidden' ).after( $( '<div>' ).slider( {
			    range: 'min',
			    min: self.data.choices.min ? parseInt( self.data.choices.min ) : 0,
			    max: self.data.choices.max ? parseInt( self.data.choices.max ) : 100,
			    step: self.data.choices.step ? parseInt( self.data.choices.step ) : 1,
			    value: parseInt( wp.customize.control( self.data.id ).setting.get() ),
			    create: function( event, ui ) {
				    // Update label.
				    var unit = self.data.choices.unit ? self.data.choices.unit : '';

				    $( this ).children( '.ui-slider-handle' ).html( '<span>' + $( this ).slider( 'value' ) + unit + '</span>' );
			    },
			    slide: function( event, ui ) {
				    // Update label.
				    var unit = self.data.choices.unit ? self.data.choices.unit : '';

				    $( ui.handle ).html( '<span>' + ui.value + unit + '</span>' );

				    // Set new value immediately to allow live preview.
				    if ( self.data.transport == 'postMessage' ) {
					    wp.customize.control( self.data.id ).setting.set( ui.value );
				    }
			    },
			    change: function( event, ui ) {
				    // Update label.
				    var unit = self.data.choices.unit ? self.data.choices.unit : '';

				    $( ui.handle ).html( '<span>' + ui.value + unit + '</span>' );

				    // Set new value.
				    wp.customize.control( self.data.id ).setting.set( ui.value );
			    }
			} ) );

			// Setup reset to default action.
			self.container.on( 'click', '.reset-to-default', function( event ) {
				event.preventDefault();

				// Set default value.
				self.slider_control.next().slider( 'value', self.slider_control.attr( 'default-value' ) );
			} );
		},
	}
} )( jQuery );
