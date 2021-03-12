jQuery(document).ready(function(){
	
	/*
	 *
	 * NHP_Options_color function
	 * Adds farbtastic to color elements
	 *
	 */
	//$('input.popup-colorpicker2').bind('contentappended', function() {
		//alert('a/');
	//});
	$colorpicker_inputs = jQuery('input.popup-colorpicker2');

	$colorpicker_inputs.each(function(){
			var $input = jQuery(this);
			var sIdSelector = "#" + jQuery(this).attr('id') + "picker";
			var sIdSelectorInput = "#" + jQuery(this).attr('id') + "";
			var initialColor = jQuery(sIdSelectorInput).val().replace('#','');
			jQuery(sIdSelector).children('div').css('background-color', '#' + initialColor);
			jQuery(sIdSelector).ColorPicker({
				color: initialColor,
				onShow: function (colpkr) {
					jQuery(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					jQuery(colpkr).fadeOut(500);
					return false;
				},
				//onSubmit: function(hsb, hex, rgb, el) {
				///	jQuery(el).ColorPickerHide();
				///},
				//onBeforeShow: function () {
				//	jQuery(this).ColorPickerSetColor(this.value);
				//},
				onChange: function (hsb, hex, rgb) {
					jQuery(sIdSelectorInput).val('#' + hex);
					jQuery(sIdSelector).children('div').css('background-color', '#' + hex);
				}
			});
			jQuery(sIdSelectorInput).bind('keyup', function(){
				jQuery(sIdSelector).ColorPickerSetColor(this.value);
				jQuery(sIdSelector).children('div').css('background-color', '#' + this.value.replace('#',''));
			});
	});
});