jQuery(document).ready(function(){
	
	/*
	 *
	 * NHP_Options_color function
	 * Adds farbtastic to color elements
	 *
	 */
	$colorpicker_inputs = jQuery('input.popup-colorpicker2');

	$colorpicker_inputs.each(
		function(){
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
/*
			
			var oFarb = jQuery.farbtastic(
				sIdSelector,
				function( color ){
	
					$input.css({
						backgroundColor: color,
						color: oFarb.hsl[2] > 0.5 ? '#000' : '#fff'
					}).val( color );
	
	
					if( oFarb.bound == true ){
						$input.change();
					}else{
						oFarb.bound = true;
					}
				}
				);
			oFarb.setColor( $input.val() );
	*/
		}
		);
	/*
	$colorpicker_inputs.each(function(e){
		jQuery(this).next('.farb-popup').hide();
	});
	
	
	$colorpicker_inputs.live('focus',function(e){
		jQuery(this).next('.farb-popup').show();
		jQuery(this).parents('li').css({
			position : 'relative',
			zIndex : '9999'
		})
		jQuery('#tabber').css({
			overflow:'visible'
		});
	});
	
	$colorpicker_inputs.live('blur',function(e){
		jQuery(this).next('.farb-popup').hide();
		jQuery(this).parents('li').css({
			zIndex : '0'
		})
	});
	*/
});