( function( $ ) {
	$( window ).load( function() {
		// Loop thru all control containers to init.
		$( 'div[id^="wr-' + wr_nitro_customize_backup_restore.type + '-"]' ).each( function() {
			var self = $( this );

			// Get the control ID.
			self.id = self.attr( 'id' ).replace( 'wr-' + wr_nitro_customize_backup_restore.type + '-', '' );

			// Method to display a message box.
			function message( message, type ) {
				// Prepare parameters.
				message = typeof message != 'undefined' ? message : '';
				type    = typeof type    != 'undefined' ? type    : 'success';

				// Remove all previous messages.
				self.find( '.notice' ).remove();

				// Create message box.
				var class_name  = 'notice notice-' + type + ' is-dismissible';
				var message_box = $('<div class="' + class_name + '">').append( '<p>' + message + '</p>' ).append(
					$( '<button type="button" class="notice-dismiss">' ).append(
						'<span class="screen-reader-text">' + wr_nitro_customize_backup_restore.dismiss + '</span>'
					).click( function() {
						$( this ).closest( '.notice' ).fadeOut();
					} )
				);

				// Show message box.
				self.append( message_box );

				return message_box;
			}

			// Setup click event handle for restore button.
			self.on( 'click', '.nitro-restore-settings', function() {
				self.find( '.nitro-restore-settings-form' ).toggleClass( 'hidden' );
			} );

			// Setup action to select backup file.
			self.on( 'click', '.select-file', function( event ) {
				event.preventDefault();

				// Setup media selector once.
				var frame = window.wr_backup_file_selector;

				if ( ! frame ) {
					// Create the media frame.
					frame = wp.media( {
						button: {
							text: wr_nitro_customize_backup_restore.select_button,
						},
						states: [
							new wp.media.controller.Library( {
								title: $( this ).text(),
								library: wp.media.query( { type: [
									'application/json',
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
						if ( attachment.attributes.url.match( /\.json$/ ) ) {
							// Update selected backup file.
							self.find( '[name="backup-file"]' ).val( attachment.attributes.url );

							// Show name of selected file.
							self.find( '.selected-file' ).text( attachment.attributes.url.split( '/' ).pop() );

							// Update text for the link to select file.
							self.find( '.select-file' ).text( wr_nitro_customize_backup_restore.change_backup );

							// Show link to remove selected file.
							self.find( '.remove-file' ).removeClass( 'hidden' );

							// Show the link to restore backup.
							self.find( '.restore-backup' ).removeClass( 'hidden' );
						}
					} );

					// Work-around to deselect the uploaded file if it is not a supported file.
					frame.on( 'open', function() {
						frame._checking_interval = setInterval( function() {
							// Check if there is any file selected.
							var attachment = frame.state().get( 'selection' ).first();

							// Verify the selected file.
							if ( attachment && attachment.attributes && attachment.attributes.url ) {
								if ( ! attachment.attributes.url.match( /\.json$/ ) ) {
									frame.reset();
								}
							}
						}, 50 );
					} );

					frame.on( 'close', function() {
						clearInterval( frame._checking_interval );
					} );

					// Store media selector object for later reference
					window.wr_backup_file_selector = frame;
				}

				// Store affected control and setting to media frame object.
				frame.control = $( this ).closest( '[class^="customize-control-content"]' ),
				frame.setting = frame.control.attr( 'id' ).replace( 'wr-' + wr_nitro_customize_backup_restore.type + '-', '' );

				frame.open();
			} );

			self.on( 'click', '.remove-file', function( event ) {
				event.preventDefault();

				// Hide the link to remove selected file.
				$( this ).addClass( 'hidden' );

				// Clear selected file.
				self.find( '[name="backup-file"]' ).val( '' );

				// Clear name of selected file.
				self.find( '.selected-file' ).text( '' );

				// Update text for the link to select file.
				self.find( '.select-file' ).text( wr_nitro_customize_backup_restore.select_backup );

				// Hide the link to restore backup.
				self.find( '.restore-backup' ).addClass( 'hidden' );
			} );

			self.on( 'click', '.restore-backup', function( event ) {
				event.preventDefault();

				// Toggle processing status.
				$( this ).addClass( 'spinner is-active' ).css( 'float', 'none' ).text( '' );

				$.ajax( {
					url: wr_nitro_customize_backup_restore.restore_url,
					data: {
						file: self.find( '[name="backup-file"]' ).val(),
						nonce: wr_nitro_customize_backup_restore.restore_nonce,
					},
					complete: $.proxy( function( response ) {
						// Toggle processing status.
						$( this ).removeClass( 'spinner is-active' ).text( wr_nitro_customize_backup_restore.restore_button );

						if ( response.responseJSON.success ) {
							// Reset restore settings form.
							self.find( '.remove-file' ).trigger( 'click' );

							message( wr_nitro_customize_backup_restore.restore_success );
						} else {
							message( response.responseJSON.data, 'error' );
						}
					}, this ),
				} );
			} );
		} );
	} );
} )( jQuery );
