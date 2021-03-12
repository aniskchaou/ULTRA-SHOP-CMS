(function($) {
	$.WR_Colors_Field = function(field, text) {
		var self = this;

		self.field = field;
		self.text = text;

		self.init();
	}

	$.WR_Colors_Field.prototype = {
		init: function() {
			var self = this;

			// Get necessary elements.
			self.container = $('#' + self.field.id);
			self.input_colors = self.container.find('input.color-picker');

			// Init color picker.
			self.input_colors.each(function(i, e) {
				$(e).spectrum({
					color: $(e).val(),
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
							$(e).spectrum('set', $(e).attr('default-value'));
							$(e).parent().children('.color-hex').text($(e).attr('default-value'));
						});
					},
					move: function(color) {
						$(e).parent().children('.color-hex').text('');

						if (color) {
							$(e).parent().children('.color-hex').text(color.getAlpha() == 1 ? color.toHexString() : color.toRgbString());
						}
					},
					hide: function( color ) {
						if( ! color ) {
							$(this).siblings('.color-hex').text('');
							$(this).val('').trigger('change');
						} else {
							var val = color.getAlpha() == 1 ? color.toHexString() : color.toRgbString();
							$(this).siblings('.color-hex').text(val);
							$(this).val(val).trigger('change');
							$('.sp-container:visible').find('.sp-input').val(val);
						}
					}
				});
			});

			// Track data input.
			self.container.on('change', 'input.color-picker', function() {
				var color = $(this).spectrum('get'), value = color ? (color.getAlpha() == 1 ? color.toHexString() : color.toRgbString()) : '';

				if ($(this).val() != value) {
					$(this).val(value);
				}
			});
		},
	}
})(jQuery);
