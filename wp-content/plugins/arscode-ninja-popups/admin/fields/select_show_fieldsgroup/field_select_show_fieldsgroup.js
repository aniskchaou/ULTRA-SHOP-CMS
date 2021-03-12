jQuery(document).ready(function(){
	jQuery('.nhp-opts-select-show-fieldsgroup').change(function(){
		var option = jQuery('option:selected', this);
		if(!option.attr('data-fieldsgroup'))
		{
			jQuery('.fg_'+jQuery(this).attr('id')).parents('tr').hide();
		}
		else
		{
			jQuery('.fg_'+jQuery(this).attr('id')).not('.'+option.attr('data-fieldsgroup')).parents('tr').hide();
			jQuery('.'+option.attr('data-fieldsgroup')).parents('tr').show();
			
		}
		//alert(jQuery(this).attr('id'));
		//alert(option.attr('data-fieldsgroup'));
		/*
		if(option.attr('data-fieldsgroup') == 'false')
		{
			if(jQuery(this).closest('tr').next('tr').is(':visible'))
			{
				jQuery(this).closest('tr').next('tr').fadeOut('slow');
			}
		}
		else
		{
			if(jQuery(this).closest('tr').next('tr').is(':hidden'))
			{
				jQuery(this).closest('tr').next('tr').fadeIn('slow');
			}
		}
		*/
	}).change();
});