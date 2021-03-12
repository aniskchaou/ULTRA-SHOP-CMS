( function( $ ) {
	var api = window.parent.wp.customize;
	var jqr = window.parent.jQuery;
	var msg = jqr( '#customize-controls' ).siblings( '.customize-bar' ).children( '.action' ).html( '' );

	// Hide page loader.
	$(window).load(function() {
		jqr('.nitro-customizer-loader').fadeOut(500);
	});

	if ( window.wr_customize_page_action || window.wr_customize_header_action ) {
		var html;

		if ( window.wr_customize_page_action ) {
			// Customize page on customize bar
			html = wr_customize_page_action.customize;

			if ( window.wr_customize_header_action ) {
				html += wr_customize_header_action.edit_header + ' ';
			}

			html += wr_customize_page_action.edit_page;
		} else {
			html = wr_customize_header_action.edit_header;
		}

		msg.html( html ).find( 'a' ).click( function() {
			if ( $( this ).attr( 'href' ) != '#' ) {
				window.open( $( this ).attr( 'href' ), '_blank' );
			} else if ( window.wr_customize_previewing && wr_customize_previewing.type ) {
				// Find and activate option panel that directly affects the previewing page.
				api.panel.each( function( panel ) {
					if ( panel.params.apply_to && panel.params.apply_to.length ) {
						var enable = false;

						for ( var i = 0; i < panel.params.apply_to.length; i++ ) {
							if ( panel.params.apply_to[ i ] == 'all' || panel.params.apply_to[ i ] == wr_customize_previewing.type ) {
								enable = true;

								break;
							}
						}

						if ( enable ) {
							// Make sure the customize panel is not collapsed.
							jqr( '.wp-full-overlay' ).removeClass( 'collapsed' ).addClass( 'expanded' );

							// Activate panel.
							if ( jqr( '#accordion-panel-' + panel.id + ' > ul' ).css( 'display' ) == 'none' ) {
								jqr( '#accordion-panel-' + panel.id + ' > h3' ).trigger( 'click' );
							}

							if ( wr_customize_previewing.view && wr_customize_previewing.view != '' ) {
								// Find and activate option section that directly affects the previewing page.
								api.section.each( function( section ) {
									if ( section.panel() == panel.id && section.id == wr_customize_previewing.view ) {
										// Activate section.
										if ( jqr( '#accordion-section-' + section.id + ' > ul' ).css( 'display' ) == 'none' ) {
											jqr( '#accordion-section-' + section.id + ' > h3' ).trigger( 'click' );
										}
									}
								} );
							}
						}
					}
				} );
			}
		} );
	}

	// Show message if changing options in Customize panel does not affect the previewing page.
	if ( window.wr_customize_disable && wr_customize_disable.disabled ) {
		msg.append( wr_customize_disable.message );

		// Add notice message to options that are overidden by page options.
		jqr( '.overridden_by_page_options_notice' ).remove();

		for ( var i = 0, n = wr_customize_disable.page_options.length; i < n; i++ ) {
			var container = jqr( '#customize-control-' + wr_customize_disable.page_options[i] ).parent().children().first();

			if ( ! container.next( '.overridden_by_page_options_notice' ).length ) {
				container.after( '<li class="overridden_by_page_options_notice" />' );
			}

			container = container.next( '.overridden_by_page_options_notice' );

			if ( ! container.children( '.section-override' ).length ) {
				container.append(
					'<div class="section-override">'
					+ '<span class="dashicons dashicons-warning"></span>'
					+ wr_customize_disable.message.split( '<span class="message-tooltip">' )[1].split( '</span>' )[0]
					+ '</li>'
				);
			}
		}
	}

	// Find option panels that not affect the previewing page.
	if ( window.wr_customize_previewing && wr_customize_previewing.type ) {
		$.check_effective_panels = function() {
			api.panel.each( function( panel ) {
				if ( panel.params.apply_to && panel.params.apply_to.length ) {
					var enable = false;

					for ( var i = 0; i < panel.params.apply_to.length; i++ ) {
						if ( panel.params.apply_to[ i ] == 'all' || panel.params.apply_to[ i ] == wr_customize_previewing.type ) {
							enable = true;

							break;
						}
					}

					if ( !enable ) {
						// Option panel is not effective.
						if ( !api.control( 'expert_mode' ).setting.get() ) {
							// Expert Mode is not enabled, mask the option section.
							if ( jqr( '#accordion-panel-' + panel.id ).hasClass( 'current-panel' ) ) {
								// Collapse the panel.
								panel.deactivate();
							}

							if ( !jqr( '#accordion-panel-' + panel.id + ' > .wr-mask' ).length ) {
								// Create the mask.
								var mask = jqr( '<div class="wr-mask wr-mask-big has-tip" />' ).append( jqr( '<button type="button" class="button enable-expert-mode hidden" />' ).css( {
								    position: 'absolute',
								    top: '7px',
								    right: '7px',
								    'z-index': 2,
								} ).text( wr_customize_previewing.btn_label ).attr( 'title', wr_customize_previewing.message ) );

								jqr( '#accordion-panel-' + panel.id ).append( mask );

								// Setup button to enable `Export Mode`.
								mask = jqr( '#accordion-panel-' + panel.id + ' > .wr-mask' );

								mask.mouseenter( function() {
									mask.children( '.enable-expert-mode' ).removeClass( 'hidden' ).tooltip();
								} ).mouseleave( function() {
									mask.children( '.enable-expert-mode' ).addClass( 'hidden' ).tooltip( 'destroy' );
								} );

								mask.children( '.enable-expert-mode' ).click( function() {
									// Remove all masks.
									jqr( 'li[id^="accordion-panel-"] > .wr-mask' ).remove();

									// Switch off Expert Mode.
									jqr( '#_customize-toggle-expert_mode > input' ).trigger( 'click' );

									// Save the change automatically.
									jqr( '#save' ).trigger( 'click' );
								} );
							}
						} else {
							// Expert Mode is enabled, remove all masks.
							jqr( 'li[id^="accordion-panel-"] > .wr-mask' ).remove();

							// Then, mark the option panel as disabled.
							jqr( '#accordion-panel-' + panel.id ).data( 'disabled', true );
						}
					} else {
						// Option panel is effective.
						if ( !api.control( 'expert_mode' ).setting.get() ) {
							// Expert Mode is not enabled, remove the mask.
							jqr( '#accordion-panel-' + panel.id + ' > .wr-mask' ).remove();
						} else {
							// Expert Mode is enabled, mark the option panel as enabled.
							jqr( '#accordion-panel-' + panel.id ).data( 'disabled', false );
						}
					}
				}
			} );
		};

		$.check_effective_panels();
	}

	// Track mouse over event to highlight customizable object.
	$( document ).on( 'mouseover', '.customizable', function( event ) {
		event.preventDefault();
		event.stopPropagation();

		if ( ! this._highlighted ) {
			// Get information.
			var info = this.className.match( /customize-(panel|section)-([^\s]+)/ );

			// var section_header = this.getAttribute( 'class' );

			if ( info ) {
				// Get the highlight mask.
				var mask = window.wr_highlight_mask
					?  window.wr_highlight_mask
					: ( window.wr_highlight_mask = document.getElementById( 'wr_highlight_mask' ) );

				// Setup the mask once.
				if ( ! mask._initialized ) {
					// Setup the customize link.
					mask.wr_customize_link = document.getElementById( 'wr_customize_link' );

					$( mask.wr_customize_link ).click( function( event ) {
						event.preventDefault();
						event.stopPropagation();

						// Make sure the customize panel is not collapsed.
						jqr( '.wp-full-overlay' ).removeClass( 'collapsed' ).addClass( 'expanded' );

						// Activate the appropriated customize panel / section.
						var panel = jqr(
							'#accordion-'
							+ wr_highlight_mask.wr_customizable_type
							+ '-'
							+ wr_highlight_mask.wr_customizable_id
						),

						child = panel.children( 'ul' );

						// Make it compatible with WordPress 4.7
						if ( ! child.length && panel.attr( 'aria-owns' ) ) {
							child = jqr( '#' + panel.attr( 'aria-owns' ) );
						}

						if ( child.length && ( child.css( 'display' ) == 'none' || child.css( 'visibility' ) == 'hidden' ) ) {
							panel.children( 'h3' ).trigger( 'click' );
						}
					} );

					// Handle window resize event.
					$( window ).resize( function() {
						// Re-calculate dimension for the mask.
						wr_highlight_mask.wr_customizable_area._highlighted = false;
						$( wr_highlight_mask.wr_customizable_area ).trigger( 'mouseover' );
					} );

					mask._initialized = true;
				}

				// Clear previously highlighted customizable object.
				mask.wr_customizable_area && ( mask.wr_customizable_area._highlighted = false );

				// Store information to the highlight mask.
				mask.wr_customizable_area = this;
				mask.wr_customizable_type = info[1];
				mask.wr_customizable_id   = info[2];

				// Position the highlight mask.
				var rect = this.getBoundingClientRect();
				if ( $( '.header-outer' ).hasClass( 'fixed' ) && ( mask.wr_customizable_id == 'page' || mask.wr_customizable_id == 'page_title' ) ) {
					var headerFixed = $( '.header' ).outerHeight();

					mask.style.top  = ( rect.top  + document.body.scrollTop  ) + headerFixed + 'px';
				} else {
					mask.style.top  = ( rect.top  + document.body.scrollTop  ) + 'px';
				}
				mask.style.left = ( rect.left + document.body.scrollLeft ) + 'px';

				mask.style.width  = rect.width  + 'px';
				mask.style.height = rect.height + 'px';

				// Set label for the customize link.
				mask.wr_customize_link.textContent = mask.wr_customize_link.getAttribute( 'data-title' ).replace(
					'%s',
					mask.wr_customizable_id.replace( /_/g, ' ' )
				);

				// Reset styles for the customize link.
				mask.wr_customize_link.style.width  = 'auto';
				mask.wr_customize_link.style.height = 'auto';
				mask.wr_customize_link.style.left   = '50%';
				mask.wr_customize_link.style.top    = 'auto';

				mask.wr_customize_link.style.webkitTransform
					= mask.wr_customize_link.style.transform
					= 'translateX(-50%)';

				mask.wr_customize_link.style.position = 'absolute';

				// Then, display it.
				mask.style.display = 'block';

				// Trigger 'scroll' event on document to make
				// the customize link visible if off-screen.
				$( document ).trigger( 'scroll' );

				// State that the customizable object is highlighted.
				mask.wr_customizable_area._highlighted = true;
			}
		}
	} );

	$( document ).on( 'mouseout', function( event ) {
		if ( event.target.id != 'wr_customize_link' && event.target.id != 'wr_highlight_mask'
			&& ! $( event.target ).closest( '.customizable' ).length )
		{
			event.preventDefault();
			event.stopPropagation();

			// Hide the highlight mask.
			if ( window.wr_highlight_mask && wr_highlight_mask.wr_customizable_area ) {
				wr_highlight_mask.style.display = 'none';
				wr_highlight_mask.wr_customizable_area._highlighted = false;
			}
		}
	} );

	$( document ).on( 'scroll', function( event ) {
		// Check if the customize link is visible and off-screen?
		if ( window.wr_highlight_mask && wr_highlight_mask.style.display != 'none' ) {
			// Get the bounding rect of the customize link.
			var link = window.wr_highlight_mask.wr_customize_link,
				rect = window.wr_highlight_mask.getBoundingClientRect();

			if ( link && rect.top <= 0 && link.style.position != 'fixed' ) {
				// Stick the customize link to the top edge of the view port.
				rect = link.getBoundingClientRect();

				link.style.width  = rect.width  + 'px';
				link.style.height = rect.height + 'px';
				link.style.left   = rect.left   + 'px';
				link.style.top    = 0;

				link.style.webkitTransform = link.style.transform = 'initial';

				link.style.position = 'fixed';
			}

			else if ( link && rect.top > 0 && link.style.position == 'fixed' ) {
				// Reset styles for the customize link.
				link.style.width  = 'auto';
				link.style.height = 'auto';
				link.style.left   = '50%';
				link.style.top    = 'auto';

				link.style.webkitTransform = link.style.transform = 'translateX(-50%)';

				link.style.position = 'absolute';
			}
		}
	} );

	// Load all lazy loaded sources.
	jqr.wr_load_delayed_sources();
} )( jQuery );
