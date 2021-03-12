(function($) {
	$.WR_Typography_Field = function(field, text) {
		var self = this;

		self.field = field;
		self.text = text;

		self.init();
	}

	$.WR_Typography_Field.prototype = {
		init: function() {
			var self = this;

			// Get necessary elements.
			self.container = $('#' + self.field.id);
			self.input_color = self.container.find('input.color-picker');

			// Init color picker.
			self.input_color.spectrum({
				color: self.input_color.val(),
				showInput: true,
				showInitial: true,
				allowEmpty: true,
				showAlpha: true,
				clickoutFiresChange: true,
				cancelText: self.text.cancel ? self.text.cancel : 'Cancel',
				chooseText: self.text.choose ? self.text.choose : 'Choose',
				preferredFormat: 'hex',
				show: function() {
					if (!$('.sp-default').length) {
						$('.sp-cancel').after('<a class="sp-default" href="#">' + (self.text['default'] ? self.text['default'] : 'Default') + '</a>');
					}

					$('.sp-default').off('click').on('click', function(event) {;
						event.preventDefault();
						$(self.input_color).spectrum('set', $(self.input_color).attr('default-value'));
						$(self.input_color).parent().children('.font-color').text($(self.input_color).attr('default-value'));
					});
				},
				move: function(color) {
					$(self.input_color).parent().children('.font-color').text('');

					if (color) {
						$(self.input_color).parent().children('.font-color').text(color.getAlpha() == 1 ? color.toHexString() : color.toRgbString());
					}
				},
			});

			self.container.find('.wr-image-selected').click(function(event) {
				event.preventDefault();
				$(this).next().toggle();
			});

			// Init select image.
			self.container.find('.wr-select-image').click(function() {
				if (!$(this).hasClass('selected')) {
					var fonts = $(this).attr("data-value");

					self.container.find('.wr-image-selected span').text( fonts );
					$(this).parent().next('input').val( fonts );

					fonts = fonts.toLowerCase().replace(/\s+/g, '-');
					self.container.find('.wr-image-selected').attr('class', '').addClass('wr-image-selected ' + fonts);

					$(this).parent().parent().toggle();

					$(this).parent().children('.selected').removeClass('selected');
					$(this).addClass('selected');
				}
			});

			// Track checkbox input.
			self.container.on('click', 'input[type="checkbox"]', function() {
				$(this).parent()[$(this).attr('checked') ? 'addClass' : 'removeClass']('active');
			});

			// Track color input.
			self.container.on('change', 'input.color-picker', function() {
				var color = $(this).spectrum('get'), value = color ? (color.getAlpha() == 1 ? color.toHexString() : color.toRgbString()) : '';

				if ($(this).val() != value) {
					$(this).val(value);
				}
			});

			// Search font
			$( 'body' ).on( 'keyup', '.wr-select-image-container .txt-sfont', function( e ) {
				var _this    	= $(this);
				var keyword     = _this.val();
				var list_fonts  = _this.closest( '.wr-select-image-container' ).find( 'li' );

				if( keyword ) {
					if( window.keyword_font_old == undefined || window.keyword_font_old != keyword || e.keyCode == 13 || e.keyCode == 86 ) {
						list_fonts.hide();
						list_fonts.each( function () {
							var textField = ( $(this).attr( 'data-value' ) != undefined ) ? $(this).attr( 'data-value' ).toLowerCase() : '' ;
							var keyword_lowercase = keyword.toLowerCase().trim();
							if( textField.indexOf( keyword_lowercase ) == -1 ) {
								$(this).hide();
							} else {
								$(this).fadeIn( 200 );
							}
						} );

						window.keyword_font_old = keyword;
					}
				} else {
					list_fonts.show();
				}
			});
		},
	}
})(jQuery);
