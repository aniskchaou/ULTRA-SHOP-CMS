(function($) {
	$( document ).ready( function() {
		setTimeout( function() {
			var vcSwitchButton = $( '.post-type-product .composer-switch' );

			// Hide VC Switch button in single product
			vcSwitchButton.hide();

			// Trigger change VC Switch button
			if( $( '#enable_builder-checkbox' ).is( ':checked' ) ) {
				vcSwitchButton.trigger( 'click' );
			} else {
				vcSwitchButton.trigger( 'click' );
			}
		}, 100);

		$('#enable_builder-checkbox').change( function() {
			if( $( this ).is( ':checked' ) ) {
				$( '.composer-switch:not(.vc_backend-status) .wpb_switch-to-composer' ).trigger( 'click' );
			} else {
				$( '.composer-switch.vc_backend-status .wpb_switch-to-composer' ).trigger( 'click' );
			}
		});

		// Sale price schedule
		$( '.sale_price_dates_fields' ).each( function() {
			var $this = $( this );
			var $wrap = $this.closest( 'div, table' );

			$this.find( 'input' ).each( function() {
				if ( $( this ).val() !== '' ) {
					$wrap.find( '.show_if_sale_schedule' ).show();
				} else {
					$wrap.find( '.show_if_sale_schedule' ).hide();
				}
			});

		});

		$( '#woocommerce-product-data' ).on( 'click', '.sale_schedule', function() {
			var $wrap = $( this ).closest( 'div, table' );

			$( this ).hide();
			$wrap.find( '.show_if_sale_schedule' ).show();

			return false;
		});
		$( '#woocommerce-product-data' ).on( 'click', '.cancel_sale_schedule', function() {
			var $wrap = $( this ).closest( 'div, table' );

			$wrap.find( '.show_if_sale_schedule' ).hide();

			return false;
		});
	});
})(jQuery);
