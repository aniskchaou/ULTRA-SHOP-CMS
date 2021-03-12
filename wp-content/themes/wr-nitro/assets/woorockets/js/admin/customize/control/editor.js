( function( $ ) {
	$.WR_Editor_Control = function( data ) {
		var self = this;

		self.data = data;

		self.init();
	}

	$.WR_Editor_Control.prototype = {
		init: function() {
			var self = this;

			// Get container.
			self.container = $( '#wr-' + wr_nitro_customize_editor.type + '-' + self.data.id );

			// Init radio images.
			self.container.find( 'a[target="thickbox"]' ).click( function() {
				setTimeout( $.proxy( function() {
					// Update z-index of the Thickbox backdrop to make it visible.
					$( '#TB_overlay' ).css( 'z-index', '999999' );

					// Append editor template into modal.
					$( '#TB_ajaxContent' ).html( $( '#nitro_customize_control_editor_template' ).text() );

					// Instantiate CodeMirror to create an editor.
					var value = wp.customize.control( self.data.id ).setting.get();

					if ( !value ) {
						value = self.data.placeholder;
					}

					self.editor = CodeMirror( $( '#TB_ajaxContent' ).find( '.customize-control-editor .editor' )[ 0 ], {
					    value: value,
					    mode: self.data.mode,
					    autofocus: true,
					    indentUnit: 4,
					    indentWithTabs: true,
					    lineNumbers: true,
					    showCursorWhenSelecting: true,
					    lineWrapping: true
					} );

					// Track change.
					self.editor.on( 'change', function() {
						self.editor._changed = true;
					} );

					// Setup save and cancel action.
					$( '#TB_ajaxContent' ).find( '.customize-control-editor' ).on( 'click', 'button', function() {
						if ( $( this ).hasClass( 'save' ) ) {
							wp.customize.control( self.data.id ).setting.set( self.editor.getValue() );

							$( '#TB_closeWindowButton' ).trigger( 'click' );
						} else if ( self.editor._changed ) {
							if ( confirm( self.data.confirm_message ) ) {
								$( '#TB_closeWindowButton' ).trigger( 'click' );
							}
						} else {
							$( '#TB_closeWindowButton' ).trigger( 'click' );
						}
					} );

					$( '#TB_overlay' ).css( 'pointer-events', 'none' );

					// Update z-index of the Thickbox modal to make it visible.
					$( '#TB_window' ).css( 'z-index', '999999' );
				}, this ), 500 );
			} );
		},
	};
} )( jQuery );
