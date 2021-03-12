( function( $ ) {
	$.WR_Preset_Control = function( data ) {
		var self = this;

		self.data = data;

		self.init();
	}

	$.WR_Preset_Control.prototype = {
		init: function() {
			var self = this;

			// Get container.
			self.container = $( '#wr-' + wr_nitro_customize_preset.type + '-' + self.data.id );

			// Track click event.
			self.container.find( '.wr-image-selected' ).click( function( e ) {
				var _this = $( this );

				_this.next().toggle();

				window.wr_click_outside( _this, '.customize-control-content', function( e ) {
					_this.next().hide();
				} );

				e.stopPropagation();
			} );

			// Init select image.
			self.container.find( '.wr-select-image' ).click( function() {
				$( this ).closest('.wr-select-image-container').hide();

				if ( ! $( this ).hasClass( 'selected' ) ) {
					var value = $( this ).find( 'input' ).val();

					// Update selection.
					self.container.find( '.wr-image-selected' )
						.attr( 'class', 'wr-image-selected ' + value )
						.attr( 'data-title', $( this ).data( 'title' ) );

					$( this ).addClass( 'selected' ).siblings().removeClass( 'selected' );
					$( this ).children( 'input' ).attr( 'checked', 'checked' );
					$( this ).siblings().children( 'input' ).removeAttr( 'checked' );

					// Set new value.
					wp.customize.control( self.data.id ).setting.set( value );

					// Loop thru selected preset data to apply.
					if ( self.data.preset[ value ] && self.data.preset[ value ].data ) {
						for ( var i in self.data.preset[ value ].data ) {
							var control = wp.customize.control( i );

							if ( control ) {
								var preset = self.data.preset[ value ].data[ i ];

								switch ( control.params.type ) {
									case 'colors':
										if ( typeof preset == 'string' ) {
											$( control.selector )
												.find( 'input[type="text"]' )
													.attr( 'default-value', preset )
													.spectrum( 'set', preset )
												.parent()
													.children( '.color-hex' ).text( preset );
										} else {
											for ( var j in preset ) {
												$( control.selector )
													.find( 'input[name="' + j + '"]' )
														.attr( 'default-value', preset[ j ] )
														.spectrum( 'set', preset[ j ] )
													.parent().children( '.color-hex' ).text( preset[ j ] );
											}
										}
									break;

									case 'radio-image':
										$( control.selector )
											.find( 'input[type="radio"]:checked' )
												.removeAttr( 'checked' )
											.parent()
												.removeClass( 'selected' );

										$( control.selector )
											.find( 'input[value="' + preset + '"]' )
												.attr( 'checked', 'checked' )
											.parent()
												.addClass( 'selected' );
									break;

									case 'select-image':
										$( control.selector )
											.find( 'input[type="radio"]:checked' )
												.removeAttr( 'checked' )
											.parent()
												.removeClass( 'selected' );

										$( control.selector )
											.find( 'input[value="' + preset + '"]' )
												.attr( 'checked', 'checked' )
											.parent()
												.addClass( 'selected' );

										$( control.selector )
											.find( '.wr-image-selected' )
												.attr( 'class', 'wr-image-selected ' + preset );
									break;

									case 'slider':
										$( control.selector )
											.find( 'input[type="range"]' )
												.attr( 'default-value', preset )
											.next( 'div' )
												.slider( 'option', 'value', preset );
									break;

									case 'toggle':
										if ( preset ) {
											$( control.selector ).find( 'input[type="checkbox"]' ).attr( 'checked', 'checked' );
										} else {
											$( control.selector ).find( 'input[type="checkbox"]' ).removeAttr( 'checked' );
										}
									break;

									case 'typography':
										// Preset font family.
										var selected = $( control.selector ).find( 'input[name="family"]:checked' ).val();

										$( control.selector )
											.find( 'input[name="family"]:checked' )
												.removeAttr( 'checked' )
											.parent()
												.removeClass( 'selected' );

										$( control.selector )
											.find( 'input[value="' + preset.family + '"]' )
												.attr( 'checked', 'checked' )
											.parent()
												.addClass( 'selected' );

										if ( selected ) {
											$( control.selector ).find( '.wr-image-selected' ).removeClass( selected.toLowerCase() );
										}

										$( control.selector ).find( '.wr-image-selected' ).addClass( preset.family.toLowerCase() );

										// Preset text style.
										$( control.selector ).find( 'input[type="checkbox"]' ).each( function() {
											if ( preset[ $( this ).attr( 'name' ) ] ) {
												$( this ).attr( 'checked', 'checked' ).parent().addClass( 'active' );
											} else {
												$( this ).removeAttr( 'checked' ).parent().removeClass( 'active' );
											}
										} );

										// Preset text color.
										$( control.selector )
											.find( 'input[name="color"]' )
												.spectrum( 'set', preset.color )
											.parent()
												.children( '.font-color' ).text( preset.color ? preset.color : '' );
									break;

									case 'checkbox':
										if ( preset ) {
											$( control.selector ).find( 'input[type="checkbox"]' ).attr( 'checked', 'checked' );
										} else {
											$( control.selector ).find( 'input[type="checkbox"]' ).removeAttr( 'checked' );
										}
									break;

									case 'date':
										$( control.selector ).find( 'input[type="date"]' ).val( preset );
									break;

									case 'number':
										$( control.selector ).find( 'input[type="number"]' ).val( preset );
									break;

									case 'radio':
										$( control.selector ).find( 'input[type="radio"]:checked' ).removeAttr( 'checked' );
										$( control.selector ).find( 'input[value="' + preset + '"]' ).attr( 'checked', 'checked' );
									break;

									case 'text':
										$( control.selector ).find( 'input[type="text"]' ).val( preset );
									break;

									case 'select':
									case 'option':
										$( control.selector ).find( 'select' ).val( preset );
									break;

									case 'textarea':
										$( control.selector ).find( 'textarea' ).val( preset );
									break;
								}

								control.setting.set( preset );
							}
						}
					}

					// Manually trigger 'change' event to refresh the live preview.
					$( '[data-customize-setting-link]' ).trigger( 'change' );
				}
			} );

			// Track change to create custom preset.
			wp.customize.bind( 'change', function( setting ) {
				// Get the current preset.
				var preset = wp.customize.control( self.data.id ).setting.get();

				if ( 'custom' == preset ) {
					return;
				}

				if ( self.data.preset[ preset ].data[ setting.id ]
					&& self.data.preset[ preset ].data[ setting.id ] != setting.get() )
				{
					// Create new custom preset once.
					if ( ! self.data.preset['custom'] ) {
						self.data.preset[ 'custom' ] = {
							title: wr_nitro_customize_preset.custom_preset_title,
							data: {},
						};
					}

					// Store customized setting to the custom preset.
					self.data.preset['custom'].data[ setting.id ] = setting.get();

					// Create new option for selecting custom preset once.
					var option = self.container.find( 'input[value="custom"]' );

					if ( ! option.length ) {
						option = $( '<li class="wr-select-image custom">' )
							.attr( 'data-title', wr_nitro_customize_preset.custom_preset_title )
							.html( '<input type="radio" name="' + self.data.id + '" value="custom">' )
							.appendTo( self.container.find( 'ul.wr-select-image-container' ) );

						// Generate HTML for previewing colors.
						var preview = $( '<ul class="colors-preset" />' );

						for ( var i = 0; i < 7; i++ ) {
							preview.append( '<li />' );
						}

						option.find( 'input[value="custom"]' ).before( preview );
					} else {
						option = option.parent();
					}

					// Select the custom preset.
					self.container.find( '.wr-image-selected' )
						.attr( 'class', 'wr-image-selected custom' )
						.attr( 'data-title', wr_nitro_customize_preset.custom_preset_title );

					option.addClass( 'selected' ).siblings().removeClass( 'selected' );
					option.children( 'input' ).attr( 'checked', 'checked' );
					option.siblings().children( 'input' ).removeAttr( 'checked' );

					// Set new value for the preset setting.
					wp.customize.control( self.data.id ).setting.set( 'custom' );
				}
			} );
		},
	};
} )( jQuery );
