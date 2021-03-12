/**
 * @version 1.0
 * @package Nitro
 * @author WooRockets Team <support@woorockets.com>
 * @copyright Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */
( function( $ ) {
	"use strict";

	$.fn.WR_ImagesLoaded = function( callback ) {
		var WR_Images = function( src, callback ) {
			var img = new Image;
			img.onload = callback;
			img.src = src;
		};
		var images = this.find( 'img' ).toArray().map( function( element ) {
			return element.src;
		} );

		if ( images.length ) {
			var loaded = 0;
			$( images ).each( function( i, src ) {
				WR_Images( src, function() {
					loaded++;
					if ( loaded == images.length ) {
						callback();
					}
				} )
			} );
		} else {
			callback();
		}
	}

	// Init tabs.
	function init_tabs() {
		var navTabs = $( '#tabs-container .nitro-nav' ),
			tabPanels = $( '#tabs-container .tab-pane' ),
			jumpTo = $( '.trigger-tab' ),
			hash = window.location.hash;

		navTabs.click( function( e ) {
			e.preventDefault();

			$( this ).addClass( 'active' ).siblings().removeClass( 'active' );
			tabPanels.removeClass( 'active' ).filter( $( e.target ).attr( 'href' ) ).addClass( 'active' );

			history.pushState( {}, '', $( e.target ).attr( 'href' ) );
		} );

		jumpTo.on( 'click', function( e ) {
			e.preventDefault();

			navTabs.filter( '[href="' + $( e.target ).attr( 'href' ) + '"]' ).trigger( 'click' );
		} );

		if ( hash ) {
			navTabs.removeClass( 'active' ).filter( '[href="' + hash + '"]' ).addClass( 'active' );
			tabPanels.removeClass( 'active' ).filter( hash ).addClass( 'active' );
		} else {
			navTabs.eq(0).addClass('active').siblings().removeClass( 'active' );
			tabPanels.removeClass( 'active' ).filter( navTabs.eq(0).attr( 'href' ) ).addClass('active');
		}
	}

	// Animated scroll when click
	function animated_scroll() {
		$( '.wr-scroll-animated' ).click( function() {
			if ( location.pathname.replace( /^\//, '' ) == this.pathname.replace( /^\//, '' ) && location.hostname == this.hostname ) {
				var target = $( this.hash );

				var adminBar = $( '#wpadminbar' ).outerHeight();
				target = target.length ? target : $( '[name=' + this.hash.slice( 1 ) + ']' );
				if ( target.length ) {
					$( 'html,body' ).animate( {
						scrollTop: target.offset().top - adminBar - 100 + "px"
					}, 800 );
					return false;
				}
			}
		} );
	}

	// Override default ThickBox handle.
	function override_thickbox() {
		$( 'a[target="thickbox"]' ).each( function( i, e ) {
			if ( !$( e ).prop( 'wr_override_thickbox' ) ) {
				$( e ).click( function( event ) {
					event.preventDefault();

					// Calculate width and height for ThickBox window.
					var width = $( this ).attr( 'data-width' ), height = $( this ).attr( 'data-height' );

					if ( width.substr( -1 ) == '%' ) {
						width = $( window ).width() * ( parseInt( width ) / 100 );
					}

					if ( height.substr( -1 ) == '%' ) {
						height = $( window ).height() * ( parseInt( height ) / 100 );
					}

					// Finalize the URL for opening ThickBox window.
					var url = $( this ).attr( 'href' ) + ( $( this ).attr( 'href' ).indexOf( '?' ) > -1 ? '&' : '?' ) + 'width=' + width + '&height=' + height;

					tb_show( $( this ).attr( 'title' ), url );

					// Remove default close handler.
					$( '#TB_closeWindowButton, #TB_overlay' ).off( 'click', tb_remove );

					// Handle window resize event to resize ThickBox window.
					var self = this,

					resize = function() {
						// Calculate new width and height for ThickBox window.
						var width = $( self ).attr( 'data-width' ), height = $( self ).attr( 'data-height' );

						if ( width.substr( -1 ) == '%' ) {
							width = $( window ).width() * ( parseInt( width ) / 100 );
						}

						if ( height.substr( -1 ) == '%' ) {
							height = $( window ).height() * ( parseInt( height ) / 100 );
						}

						// Update width and height for ThickBox window.
						TB_WIDTH = ( width * 1 ) + 30;
						TB_HEIGHT = ( height * 1 ) + 40;

						ajaxContentW = TB_WIDTH - 30;
						ajaxContentH = TB_HEIGHT - 45;

						$( '#TB_ajaxContent' ).css( {
							width: ajaxContentW,
							height: ajaxContentH,
						} );

						$( '#TB_iframeContent' ).css( {
							width: ajaxContentW + 29,
							height: ajaxContentH + 17,
						} );

						tb_position();
					}

					$( window ).on( 'resize', resize );

					$( '#TB_closeWindowButton, #TB_overlay' ).click( function( event ) {
						// Prevent default event handle.
						event.preventDefault();

						// Check if closing modal is prevented?
						var prevent_close = $( '#TB_closeWindowButton' ).attr( 'data-prevent-close' );

						if ( prevent_close ) {
							if ( $( this ).attr( 'id' ) == 'TB_closeWindowButton' ) {
								// Show alert.
								return alert( prevent_close );
							}
						} else {
							// Close modal.
							tb_remove( event );
						}

						// Remove window resize handle.
						$( window ).off( 'resize', resize );
					} );

					return false;
				} );

				$( e ).prop( 'wr_override_thickbox', true );
			}
		} );
	}

	// Init Plugins tab.
	function init_plugins() {
		// Init action to install / uninstall plugin.
		var installing;

		$( '#plugins' ).on( 'click', '.install-plugin, .uninstall-plugin', function( event ) {
			event.preventDefault();

			// Make sure button is not disabled.
			if ( $( this ).attr( 'disabled' ) ) {
				return;
			}

			// Get action and plugin to manipulate.
			var action = $( this ).hasClass( 'install-plugin' ) ? 'install' : 'uninstall';
			var plugin = $.trim( $( this ).prev().text().replace( /[\s\t\r\n]{2,99}/g, ' ' ) );

			if ( confirm( wr_nitro_admin[ 'confirm_' + action + '_plugin' ].replace( '%PLUGIN%', plugin ) ) ) {
				// Switch button status.
				$( this ).hide().after( '<span class="spinner is-active"></span>' );

				var install = $.proxy( function() {
					if ( 'install' == action ) {
						// Some plugins always redirect the next admin page request to their welcome page after activation,
						// this causes the next Ajax request fails after installing those plugins. So, let's prevent it.
						switch ( installing ) {
							case 'js_composer':
								// Re-try after 1 second.
								return setTimeout( install, 1000 );
							break;
						}

						installing = $( this ).attr( 'data-plugin' );
					}

					// Send Ajax request to install plugin.
					$.ajax( {
						context: this,
						url: wr_nitro_admin[ action + '_plugin_url' ],
						type: 'POST',
						dataType: 'json',
						data: {
							plugin: $( this ).attr( 'data-plugin' ),
							nonce: wr_nitro_admin[ action + '_plugin_nonce' ],
						},
						complete: function( response ) {
							// Parse response manually if needed.
							if ( !response.responseJSON && ( response.responseJSON = response.responseText.match( /\{"success":.+\}/ ) ) ) {
								response.responseJSON = $.parseJSON( response.responseJSON[ 0 ] );
							}

							// Verify response.
							if ( !response.responseJSON || !response.responseJSON.success ) {
								// Show error message.
								if ( response.responseJSON && response.responseJSON.data ) {
									alert( response.responseJSON.data );
								} else {
									alert( response.responseText ? response.responseText : wr_nitro_admin.unknown_error );
								}

								'install' == action && ( installing = null );
							} else {
								// Update button class and label.
								$( this ).removeClass( action + '-plugin' ).addClass( ( action == 'install' ? 'uninstall' : 'install' ) + '-plugin' );
								$( this ).text( wr_nitro_admin[ action == 'install' ? 'uninstall' : 'install' ] );

								if ( 'install' == action ) {
									// Some plugins always redirect the next admin page request to their welcome page after activation,
									// this causes the next Ajax request fails after installing those plugins. So, let's prevent it.
									switch ( installing ) {
										case 'js_composer':
											$.ajax( {
												url: ajaxurl.replace( 'admin-ajax.php', 'index.php' ),
												complete: function() {
													installing = null;
												}
											} );
										break;

										default:
											installing = null;
										break;
									}
								}
							}

							// Switch button status.
							$( this ).show().next().remove();
						},
					} );
				}, this );

				install();
			}
		} );
	}

	// Init Demos tab.
	function init_demos() {
		// var container = $( '.box-wrap.demos' );
		// container.WR_ImagesLoaded( function() {
		// 	container.isotope({
		// 		filter: '*',
		// 		layoutMode: 'fitRows'
		// 	});
		// });

		// $( '.filter-by-tag a' ).click( function( e ) {
		// 	e.preventDefault();
		// 	var selector = $( this ).attr( 'data-tag' );
		// 	$( '.box-wrap.demos' ).isotope( {
		// 		filter: selector,
		// 		transitionDuration: '0.3s',
		// 	} );
		// } );
		// var $optionSets = $( '.filter-by-tag' ), $optionLinks = $optionSets.find( 'a' );

		// $optionLinks.click( function() {
		// 	var $this = $( this );
		// 	// don't proceed if already selected
		// 	if ( $this.hasClass( 'current' ) ) {
		// 		return false;
		// 	}
		// 	var $optionSet = $this.parents( '.filter-by-tag' );
		// 	$optionSet.find( '.current' ).removeClass( 'current' );
		// 	$this.addClass( 'current' );
		// } );

		// Init install / uninstall sample data action.
		$( '#demos .install-sample, #demos .uninstall-sample' ).click( function( event ) {
			event.preventDefault();

			// Make sure button is not disabled.
			if ( $( this ).attr( 'disabled' ) ) {
				return;
			}

			// Get action and sample data package to manipulate.
			var action = $( this ).hasClass( 'install-sample' ) ? 'install' : 'uninstall';
			var sample = $.trim( $( this ).parent().children( 'h5' ).text().replace( /[\s\t\r\n]{2,99}/g, ' ' ) );

			// Define necessary functions to install sample data.
			var download_sample_package = function( id ) {
				// Prevent modal window from being closed.
				$( '#TB_closeWindowButton' ).attr( 'data-prevent-close', wr_nitro_admin.close_prevented );

				// Send request to download sample data package.
				var data = 'package=' + id + '&step=2&nonce=' + wr_nitro_admin.install_sample_nonce
					+ '&' + $('#sample-data-installation-options input').serialize();


				$.ajax( {
					url: wr_nitro_admin.install_sample_url,
					dataType: 'json',
					data: data,
					complete: function( response ) {
						if ( !response.responseJSON || !response.responseJSON.success ) {
							// Show error message.
							$( '#wr-install-sample-data-download-package .spinner' ).removeClass( 'spinner' ).addClass( 'dashicons dashicons-no-alt' );
							$( '#wr-install-sample-data-download-package .wr-status' ).removeClass( 'hidden' );

							if ( response.responseJSON && response.responseJSON.data ) {
								$( '#wr-install-sample-data-download-package .wr-status' ).html( response.responseJSON.data );
							} else if ( response.responseText ) {
								$( '#wr-install-sample-data-download-package .wr-status' ).html( response.responseText );
							} else {
								$( '#wr-install-sample-data-download-package .wr-status' ).html( wr_nitro_admin.unknown_error );
							}

							// If user want to install a single page demo, stop right now.
							if ( $( '#sample-data-installation-options input[name="option"]:checked' ).val() == 'page' ) {
								// Show error message.
								return show_message();
							}

							// Try manual upload.
							return upload_sample_package( id );
						}

						// Everything went fine, go to next step.
						$( '#wr-install-sample-data-download-package .spinner' ).removeClass( 'spinner' ).addClass( 'dashicons dashicons-yes' );

						import_sample_data( id );
					},
				} )
			},

			upload_sample_package = function( id ) {
				// Allow modal window to be closed.
				$( '#TB_closeWindowButton' ).removeAttr( 'data-prevent-close' );

				// Show manual upload form.
				$( '#wr-install-sample-data-manually' ).removeClass( 'hidden' );

				// Init upload action.
				$( '#wr-upload-sample-data' ).load( function( event ) {
					var responseText = $( this ).contents().find( 'body' ).text(), responseJSON = $.parseJSON( responseText );

					if ( !responseJSON || !responseJSON.success ) {
						$( '#wr-install-sample-data-upload-package .spinner' ).removeClass( 'spinner' ).addClass( 'dashicons dashicons-no-alt' );
						$( '#wr-install-sample-data-upload-package .wr-status' ).removeClass( 'hidden' );

						if ( responseJSON && responseJSON.data ) {
							$( '#wr-install-sample-data-upload-package .wr-status' ).html( responseJSON.data );
						} else if ( responseText ) {
							$( '#wr-install-sample-data-upload-package .wr-status' ).html( responseText );
						} else {
							$( '#wr-install-sample-data-upload-package .wr-status' ).html( wr_nitro_admin.unknown_error );
						}

						// Show error message.
						return show_message();
					}

					// Everything went fine, go to next step.
					$( '#wr-install-sample-data-upload-package .spinner' ).removeClass( 'spinner' ).addClass( 'dashicons dashicons-yes' );

					import_sample_data( id );
				} );

				$( '#wr-upload-sample-data-package' ).click( function() {
					$( '#wr-install-sample-data-upload-package' ).removeClass( 'hidden' );
					$( '#wr-install-sample-data-manually' ).addClass( 'hidden' );

					// Append nonce field.
					var nonce_field = $( this.form ).find( 'input[name="nonce"]' );

					if ( !nonce_field.length ) {
						nonce_field = $( '<input name="nonce" value="" />' ).appendTo( this.form );
					}

					nonce_field.val( wr_nitro_admin.install_sample_nonce );

					$( this.form ).submit();
				} );
			},

			import_sample_data = $.proxy( function( id ) {
				// Prevent modal window from being closed.
				$( '#TB_closeWindowButton' ).attr( 'data-prevent-close', wr_nitro_admin.close_prevented );

				$( '#wr-install-sample-data-import-data' ).removeClass( 'hidden' );

				// Send request to import sample data via iframe instead of Ajax.
				var src = wr_nitro_admin.install_sample_url
					+ '&package=' + id + '&step=3&nonce=' + wr_nitro_admin.install_sample_nonce
					+ '&' + $('#sample-data-installation-options input').serialize();

				$( '#nitro-manipulate-sample-data' ).off( 'load' ).load( $.proxy( function( event ) {
					// Parse response.
					var response = {
						responseText: $( event.target ).contents().find( 'body' ).text(),
						responseJSON: $( event.target ).contents().find( 'body' ).text().match( /\{"success":.+\}/ ),
					}

					if ( response.responseJSON ) {
						response.responseJSON = $.parseJSON( response.responseJSON[ 0 ] );
					}

					// Verify response.
					if ( !response.responseJSON || !response.responseJSON.success ) {
						$( '#wr-install-sample-data-import-data .spinner' ).removeClass( 'spinner' ).addClass( 'dashicons dashicons-no-alt' );
						$( '#wr-install-sample-data-import-data .wr-status' ).removeClass( 'hidden' );

						if ( response.responseJSON && response.responseJSON.data ) {
							$( '#wr-install-sample-data-import-data .wr-status' ).html( response.responseJSON.data );
						} else if ( response.responseText ) {
							$( '#wr-install-sample-data-import-data .wr-status' ).html( response.responseText );
						} else {
							$( '#wr-install-sample-data-import-data .wr-status' ).html( wr_nitro_admin.unknown_error );
						}

						// Show error message.
						return show_message();
					}

					var finalize_import_sample_data = $.proxy( function() {
						// Everything went fine, go to next step.
						$( '#wr-install-sample-data-import-data .spinner' ).removeClass( 'spinner' ).addClass( 'dashicons dashicons-yes' );

						// Hide current uninstall button.
						$( '#demos .uninstall-sample' ).addClass( 'hidden' );
						$( '#demos .install-sample' ).removeClass( 'hidden' );

						// Swap install and uninstall visibility.
						$( this ).addClass( 'hidden' ).parent().children( '.uninstall-sample' ).removeClass( 'hidden' );

						// Install required plugins if needed.
						if ( response.responseJSON.data && response.responseJSON.data.required_plugins ) {
							return install_required_plugins( id, response.responseJSON.data );
						}

						// Download all demo assets if needed.
						if ( response.responseJSON.data && response.responseJSON.data.demo_assets ) {
							return download_demo_assets( id, response.responseJSON.data.demo_assets );
						}

						// Show success message.
						show_message( true );
					}, this );

					// Update security nonces.
					if ( response.responseJSON.data && response.responseJSON.data.refresh_nonce ) {
						window.wr_nitro_refresh = response.responseJSON.data.refresh_nonce;

						refresh_security_nonces( finalize_import_sample_data );
					} else {
						finalize_import_sample_data();
					}
				}, this ) ).attr( 'src', src );
			}, this ),

			refresh_security_nonces = function( callback ) {
				$.ajax( {
					url: wr_nitro_admin.refresh_nonce_url,
					dataType: 'json',
					data: {
						nonce: wr_nitro_refresh,
					},
					complete: function( response ) {
						if ( response.responseJSON && response.responseJSON.success ) {
							for ( var i in response.responseJSON.data ) {
								wr_nitro_admin[ i ] = response.responseJSON.data[ i ];
							}
						}

						if ( typeof callback == 'function' ) {
							callback();
						}
					}
				} );
			},

			install_required_plugins = function( id, data ) {
				if ( data.required_plugins.length ) {
					var install = function( i ) {
						i = i || 0;

						// Update installation progress.
						$( '#wr-install-sample-data-required-plugins .install-status' ).text( ( i + 1 ) + '/' + data.required_plugins.length + ': ' + data.required_plugins[ i ] );
						$( '#wr-install-sample-data-required-plugins .progress-bar' ).css( 'width', Math.round( ( i / data.required_plugins.length ) * 100 ) + '%' );
						$( '#wr-install-sample-data-required-plugins .percentage' ).text( Math.round( ( i / data.required_plugins.length ) * 100 ) );

						// Send Ajax request to install plugin.
						return $.ajax( {
							url: wr_nitro_admin.install_plugin_url,
							type: 'POST',
							dataType: 'json',
							data: {
								plugin: data.required_plugins[ i ],
								nonce: wr_nitro_admin.install_plugin_nonce,
							},
							complete: function( response ) {
								// Parse response manually if needed.
								if ( !response.responseJSON && ( response.responseJSON = response.responseText.match( /\{"success":.+\}/ ) ) ) {
									response.responseJSON = $.parseJSON( response.responseJSON[ 0 ] );
								}

								// Verify response.
								if ( !response.responseJSON || !response.responseJSON.success ) {
									if ( response.responseJSON && response.responseJSON.data ) {
										// Try to get detailed error message printed out by TGM Plugin Activation.
										var message = response.responseText.match( /<div class="error">(.+)<\/div>/ );

										if ( message ) {
											message = message[ 1 ];
										} else {
											message = response.responseJSON.data;
										}

										if ( message == 'NONCE_EXPIRED' ) {
											// Wait for WordPress's login modal to pop-up.
											var opened_login_modal = false, start_time = ( new Date() ).getTime(),

											login_check = function() {
												var iframe = document.getElementById( 'wp-auth-check-frame' );

												if ( ! opened_login_modal ) {
													if ( iframe ) {
														opened_login_modal = true;
													}
												} else {
													if ( ! iframe ) {
														// Refresh security nonces.
														return refresh_security_nonces( function() {
															install( i );
														} );
													}
												}

												// Allow max 1 minutes timeout.
												if ( ( new Date() ).getTime() - start_time < ( 60 * 1000 ) ) {
													setTimeout( login_check, 200 );
												} else {
													// Show error message.
													$( '#wr-install-sample-data-required-plugins .wr-status' ).removeClass( 'hidden' );

													$( '#wr-install-sample-data-required-plugins .wr-status' ).append(
														'<span class="session-expired">' +
														( $( '#wr-install-sample-data-required-plugins .wr-status' ).html() != '' ? '<br>' : '' ) +
														wr_nitro_admin.session_expired + ' ' +
														'<a href="' + wr_nitro_admin.login_link + '" target="_blank" rel="noopener noreferrer">' +
														wr_nitro_admin.login_again + '</a>' +
														'</span>'
													);

													// Schedule refreshing nonces.
													window.wr_nitro_refesh_nonces_interval = setInterval( function() {
														refresh_security_nonces( function() {
															install( i );
														} );
													}, 5000 );
												}
											}

											if ( ! window.wr_nitro_refeshed_nonces ) {
												refresh_security_nonces( function() {
													install( i );
												} );

												window.wr_nitro_refeshed_nonces = true;
											} else {
												login_check();
											}

											return;
										}
									} else {
										message = wr_nitro_admin.install_plugin_failed.replace( '%s', data.required_plugins[ i ] );
									}

									// Show error message.
									$( '#wr-install-sample-data-required-plugins .wr-status' ).removeClass( 'hidden' );

									if ( $( '#wr-install-sample-data-required-plugins .wr-status' ).html() != '' ) {
										$( '#wr-install-sample-data-required-plugins .wr-status' ).append( '<br>' );
									}

									$( '#wr-install-sample-data-required-plugins .wr-status' ).append( message );
								} else if ( window.wr_nitro_refesh_nonces_interval ) {
									// Remove session expired message.
									$( '#wr-install-sample-data-required-plugins .wr-status .session-expired' ).remove();

									// Clear refresh nonces interval.
									clearInterval( wr_nitro_refesh_nonces_interval );
								}

								if ( i + 1 == data.required_plugins.length ) {
									// Hide installation progress.
									$( '#wr-install-sample-data-required-plugins .install-status' ).addClass( 'hidden' );
									$( '#wr-install-sample-data-required-plugins .progress' ).addClass( 'hidden' );

									if ( $( '#wr-install-sample-data-required-plugins .wr-status' ).hasClass( 'hidden' ) ) {
										// Everything went fine, go to next step.
										$( '#wr-install-sample-data-required-plugins .spinner' ).removeClass( 'spinner' ).addClass( 'dashicons dashicons-yes' );

										// Download all demo assets if needed.
										if ( data.demo_assets ) {
											return download_demo_assets( id, data.demo_assets );
										}

										// Show success message.
										show_message( true );
									} else {
										// Failed to install one or more required plugin.
										$( '#wr-install-sample-data-required-plugins .spinner' ).removeClass( 'spinner' ).addClass( 'dashicons dashicons-no-alt' );

										// Show error message.
										show_message();
									}

									return;
								}

								// Some plugins always redirect the next admin page request to their welcome page after activation,
								// this causes the next Ajax request fails after installing those plugins. So, let's prevent it.
								switch ( data.required_plugins[ i ] ) {
									case 'js_composer':
										$.ajax( {
											url: ajaxurl.replace( 'admin-ajax.php', 'index.php' ),
											complete: function() {
												// Install next plugin.
												install( i + 1 );
											}
										} );
									break;

									default:
										// Install next plugin.
										install( i + 1 );
									break;
								}
							},
						} );
					};

					// Show install required plugins progress.
					$( '#wr-install-sample-data-required-plugins' ).removeClass( 'hidden' );

					install();
				} else {
					// Download all demo assets if needed.
					if ( data.demo_assets ) {
						return download_demo_assets( id, data.demo_assets );
					}

					// Show success message.
					show_message( true );
				}
			},

			download_demo_assets = function( id, demo_assets ) {
				var download = function( i ) {
					i = i || 0;

					// Update downloading progress.
					$( '#wr-install-sample-data-demo-assets .download-status' ).text( ( i + 1 ) + '/' + demo_assets.length + ': ' + demo_assets[ i ] );
					$( '#wr-install-sample-data-demo-assets .progress-bar' ).css( 'width', Math.round( ( i / demo_assets.length ) * 100 ) + '%' );
					$( '#wr-install-sample-data-demo-assets .percentage' ).text( Math.round( ( i / demo_assets.length ) * 100 ) );

					// Send request to download asset.
					$.ajax( {
						url: wr_nitro_admin.install_sample_url,
						data: {
							'package': id,
							step: 4,
							asset: i,
							nonce: wr_nitro_admin.install_sample_nonce,
						},
						complete: function() {
							if ( i + 1 == demo_assets.length ) {
								$( '#wr-install-sample-data-demo-assets .spinner' ).removeClass( 'spinner' ).addClass( 'dashicons dashicons-yes' );
								$( '#wr-install-sample-data-demo-assets .download-status' ).addClass( 'hidden' );
								$( '#wr-install-sample-data-demo-assets .progress' ).addClass( 'hidden' );

								// Show success message.
								return show_message( true );
							}

							// Download next asset.
							download( i + 1 );
						},
					} );
				};

				// Show download demo assets progress.
				$( '#wr-install-sample-data-demo-assets' ).removeClass( 'hidden' );

				download();
			},

			show_message = function( success ) {
				// Allow modal window to be closed.
				$( '#TB_closeWindowButton' ).removeAttr( 'data-prevent-close' );

				if ( success ) {
					$( '#wr-install-sample-data-success-message' ).removeClass( 'hidden' );
				} else {
					$( '#wr-install-sample-data-failure-message' ).removeClass( 'hidden' );
				}
			};

			switch ( action ) {
				case 'install':
					// Load content to append to sample data installation modal.
					$.ajax( {
						context: this,
						url: wr_nitro_admin.install_sample_url,
						dataType: 'json',
						data: {
							'package': $( this ).attr( 'data-package' ),
							nonce: wr_nitro_admin.install_sample_nonce,
						},
						complete: function( response ) {
							if ( !response.responseJSON || !response.responseJSON.data ) {
								// Show error message.
								$( '#TB_ajaxContent' ).append( response.responseText ? response.responseText : wr_nitro_admin.unknown_error );
							} else {
								// Append content to modal.
								$( '#TB_ajaxContent' ).append( response.responseJSON.data );

								// Init sample data installation.
								$( '#TB_ajaxContent' )

								.on( 'click', '#sample-data-installation-options input[name="option"]', function( event ) {
									// Toggle sample data options.
									$( '#sample-data-installation-options .select-page' )[
										this.value == 'full' ? 'hide' : 'show'
									]();
								} )

								.on( 'click', '#sample-data-installation-options .select-page .box', function( event ) {
									// Toggle demo page selection.
									$( this ).toggleClass( 'selected' );

									if ( $( this ).hasClass( 'selected' ) ) {
										$( this ).children( 'input[type="checkbox"]' ).attr( 'checked', 'checked' );
									} else {
										$( this ).children( 'input[type="checkbox"]' ).removeAttr( 'checked' );
									}
								} )

								.on( 'click', '#confirm-sample-data-installation', function( event ) {
									// Toggle state for button to install sample data.
									if ( event.target.checked ) {
										$( '#go-to-sample-data-installation-step-2' ).removeAttr( 'disabled' );
									} else {
										$( '#go-to-sample-data-installation-step-2' ).attr( 'disabled', 'disabled' );
									}
								} )

								.on( 'click', '#cancel-sample-data-installation', function( event ) {
									// Hide modal.
									$( '#TB_closeWindowButton' ).trigger( 'click' );
								} )

								.on( 'click', '#go-to-sample-data-installation-step-2', function( event ) {
									// Show installation progress.
									$( '#sample-data-installation-step-1' ).addClass( 'hidden' );
									$( '#sample-data-installation-step-2' ).removeClass( 'hidden' );

									// Download the selected package.
									download_sample_package( $( '#sample-data-installation-step-2' ).attr( 'data-package' ) );
								} );
							}
						},
					} );
				break;

				case 'uninstall':
					// Load content to append to sample data uninstallation modal.
					$.ajax( {
						context: this,
						url: wr_nitro_admin.uninstall_sample_url,
						dataType: 'json',
						data: {
							'package': $( this ).attr( 'data-package' ),
							nonce: wr_nitro_admin.uninstall_sample_nonce,
						},
						complete: function( response ) {
							if ( !response.responseJSON || !response.responseJSON.data ) {
								// Show error message.
								$( '#TB_ajaxContent' ).append( response.responseText ? response.responseText : wr_nitro_admin.unknown_error );
							} else {
								// Append content to modal.
								$( '#TB_ajaxContent' ).append( response.responseJSON.data );

								// Init sample data uninstallation.
								$( '#TB_ajaxContent' ).on( 'click', '#confirm-sample-data-uninstallation', function( event ) {
									if ( event.target.checked ) {
										$( '#go-to-sample-data-installation-step-2' ).removeAttr( 'disabled' );
									} else {
										$( '#go-to-sample-data-installation-step-2' ).attr( 'disabled', 'disabled' );
									}
								} ).on( 'click', '#cancel-sample-data-installation', function( event ) {
									// Hide modal.
									$( '#TB_closeWindowButton' ).trigger( 'click' );
								} ).on( 'click', '#go-to-sample-data-installation-step-2', $.proxy( function( event ) {
									// Disable all buttons.
									$( '#demos' ).find( '.install-sample, .uninstall-sample' ).attr( 'disabled', 'disabled' );

									// Switch button status.
									$( this ).addClass( 'hidden' ).after( '<span class="spinner is-active"></span>' );

									// Hide modal.
									$( '#TB_closeWindowButton' ).trigger( 'click' );

									// Send request to import sample data via iframe instead of Ajax.
									$( '#nitro-manipulate-sample-data' ).off( 'load' ).load( $.proxy( function( event ) {
										// Parse response.
										var response = {
											responseText: $( event.target ).contents().find( 'body' ).text(),
											responseJSON: $( event.target ).contents().find( 'body' ).text().match( /\{"success":.+\}/ ),
										}

										if ( response.responseJSON ) {
											response.responseJSON = $.parseJSON( response.responseJSON[ 0 ] );
										}

										if ( !response.responseJSON || !response.responseJSON.success ) {
											// Show error message.
											if ( response.responseJSON && response.responseJSON.data ) {
												alert( response.responseJSON.data );
											} else {
												alert( response.responseText ? response.responseText : wr_nitro_admin.unknown_error );
											}

											// Switch button status.
											$( this ).removeClass( 'hidden' ).next().remove();

											// Enable all buttons.
											return $( '#demos' ).find( '.install-sample, .uninstall-sample' ).removeAttr( 'disabled' );
										}

										window.location.reload();
									}, this ) ).attr( 'src', wr_nitro_admin.uninstall_sample_url + '&step=2&package=' + $( this ).attr( 'data-package' ) + '&nonce=' + wr_nitro_admin.uninstall_sample_nonce );
								}, this ) );
							}
						},
					} );
				break;
			}
		} );
	}

	// Init magnific popup
	function init_magnific_popup() {
		if ( typeof $.fn.magnificPopup == 'undefined' ) {
			return setTimeout( init_magnific_popup, 100 );
		}

		$( '.open-purchase-code' ).magnificPopup( {
			type: 'image',
			mainClass: 'mfp-fade',
			removalDelay: 160
		} );
	}

	// Init Nitro welcome page.
	$( document ).ready( function() {
		init_tabs();
		animated_scroll();
		override_thickbox();
		init_plugins();
		init_demos();

		// Init magnific popup for video.
		if ( $( '.open-purchase-code' ).length ) {
			init_magnific_popup();
		}
	} );
} )( jQuery );
