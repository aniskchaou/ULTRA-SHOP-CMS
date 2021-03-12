jQuery(document).ready(function(){
	if(jQuery('.nhp-opts-select-theme').lenght>0)
	{
		jQuery('#postdivrich').hide();
	}
	jQuery('.nhp-opts-select-theme').change(function(){
		if(jQuery('.snp-theme-mode-swicher:checked').val()==1)
		{
		    return;
		}
		var aID='#select-theme-' + jQuery(this).attr('id');
		var eColors = jQuery('.nhp-opts-select-theme-color');
		var eTypes = jQuery('.nhp-opts-select-theme-type');
		if(jQuery('#nhp-opts-select-theme-load').val()=='1')
		{
		    jQuery(aID).html('');
		    jQuery(aID).addClass('snp-loading');
		    jQuery.post(
		       ajaxurl, 
		       {
			      'action': 'snp_popup_fields',
			      'popup': jQuery(this).val(),
			      'snp_post_ID' : jQuery('#post_ID').val()
		       }, 
		       function(response){
			      jQuery(aID).removeClass('snp-loading');
			      jQuery(aID).html(response);
		       }
		    );
		}
		jQuery('#nhp-opts-select-theme-load').val('1');
		// Colors
		eColors.find('option').remove();
		jQuery("<option/>").val('').text('--').appendTo(eColors);
		jQuery.ajax({
			url: ajaxurl,
			data:{
				'action': 'snp_popup_colors',
				'popup': jQuery(this).val(),
				'snp_post_ID' : jQuery('#post_ID').val()
			},
			dataType: 'JSON',
			type: 'POST',
			success:function(response){
				eColors.find('option').remove();
				jQuery.each(response, function(i, option)
				{
					if(i==jQuery('#nhp-opts-select-theme-color-org-val').val())
					{
						jQuery("<option/>").val(i).attr('selected','selected').text(option.NAME).appendTo(eColors);
					}
					else
					{
						jQuery("<option/>").val(i).text(option.NAME).appendTo(eColors);
					}
				});
				eColors.change();
			},
			error: function(errorThrown){
			   alert('Error...');
			}
		});
		// Types
		eTypes.find('option').remove();
		jQuery("<option/>").val('').text('--').appendTo(eTypes);
		jQuery.ajax({
			url: ajaxurl,
			data:{
				'action': 'snp_popup_types',
				'popup': jQuery(this).val(),
				'snp_post_ID' : jQuery('#post_ID').val()
			},
			dataType: 'JSON',
			type: 'POST',
			success:function(response){
				eTypes.find('option').remove();
				jQuery.each(response, function(i, option)
				{
					if(i==jQuery('#nhp-opts-select-theme-type-org-val').val())
					{
						jQuery("<option/>").val(i).attr('selected','selected').text(option.NAME).appendTo(eTypes);
					}
					else
					{
						jQuery("<option/>").val(i).text(option.NAME).appendTo(eTypes);
					}
				});
				eTypes.change();
			},
			error: function(errorThrown){
			   alert('Error...');
			}
		});
		
	}).change();
	jQuery('.nhp-opts-select-theme-color').change(function(){
		var eTheme = jQuery(this).prev('.nhp-opts-select-theme');
		var preview_img = ''+eTheme.find('option:selected').data('preview')+'/'+eTheme.val()+'/preview/'+jQuery(this).val()+'.png';
		if(jQuery('#nhp-opts-select-theme-preview-img').attr('src')!=preview_img)
		{
		    jQuery('#nhp-opts-select-theme-preview-img').hide();
		    jQuery('.snp-nhp-opts-select-theme-preview').addClass('snp-loading');
		    jQuery('#nhp-opts-select-theme-preview-img').attr('src',preview_img);
		    jQuery('#nhp-opts-select-theme-preview-img').load(function(){
			    jQuery('.snp-nhp-opts-select-theme-preview').removeClass('snp-loading');
			    jQuery('#nhp-opts-select-theme-preview-img').show();
		    });		
		}
	});
	jQuery('.nhp-opts-select-theme-type').change(function(){
		if(jQuery(this).val()=='optin' || jQuery(this).val()=='iframe' || jQuery(this).val()=='html' || jQuery(this).val()=='likebox')
		{
			jQuery('#snp-cf-fb').hide();
			jQuery('#snp-cf-tw').hide();
			jQuery('#snp-cf-gp').hide();
			jQuery('#snp-cf-li').hide();
			jQuery('#snp-cf-pi').hide();
		}
		if(jQuery(this).val()=='optin')
		{
			jQuery('#snp-cf-optin').show();
		}
		else
		{
			jQuery('#snp-cf-optin').hide();	
		}
		if(jQuery(this).val()=='social')
		{
			jQuery('#snp-cf-fb').show();
			jQuery('#snp-cf-tw').show();
			jQuery('#snp-cf-gp').show();
			jQuery('#snp-cf-li').show();
			jQuery('#snp-cf-pi').show();
		}
		if(jQuery(this).val()=='html')
		{
			jQuery('#postdivrich').show();
		}
		else
		{
			jQuery('#postdivrich').hide();
		}
	});
	function snp_theme_mode_switch(mode)
	{
	    if(mode==1)
	    {
		jQuery('#snp-theme-mode-2').show();
		jQuery('#snp-theme-mode-1').hide();
		jQuery('#snp-cf-bld').show();
		jQuery('#snp-cf-cnt').hide();	
		jQuery('#snp-cf-fb').hide();
		jQuery('#snp-cf-tw').hide();
		jQuery('#snp-cf-gp').hide();
		jQuery('#snp-cf-li').hide();
		jQuery('#snp-cf-pi').hide();
		jQuery('#snp-cf-optin').show();
		jQuery('#postdivrich').hide();
                jQuery('#snp-cf-overlay-bld').show();
                jQuery('#snp-cf-overlay').hide();
                jQuery('#snp-cf-cb').hide();
	    }
	    else
	    {
		jQuery('#snp-theme-mode-1').show();
		jQuery('#snp-theme-mode-2').hide();
		jQuery('#snp-cf-bld').hide();	
		jQuery('#snp-cf-cnt').show();	
                jQuery('#snp-cf-overlay').show();
                jQuery('#snp-cf-overlay-bld').hide();
                jQuery('#snp-cf-cb').show();
		jQuery('.nhp-opts-select-theme').change();
	    }
	    var snpbldPosition = jQuery('#builder-tpl').offset();
	    jQuery(window).scroll(function() {
                if(snpbldPosition!==undefined)
                {
                    if (jQuery(window).scrollTop() > snpbldPosition.top)
                    {
                        jQuery('#builder-tpl').addClass('fixed');
                    }
                    else
                    {
                        jQuery('#builder-tpl').removeClass('fixed');
                    }
                }
	    });
	}
	jQuery('.snp-theme-mode-swicher').click(function(){
            jQuery('.snp-mode-swicher-label').removeClass('selected');
            jQuery(this).parents('.snp-mode-swicher-label').addClass('selected');
	    snp_theme_mode_switch(jQuery('.snp-theme-mode-swicher:checked').val());
	    if(jQuery('.snp-theme-mode-swicher:checked').val()==1)
	    {
		//jQuery('html, body').animate({
		//    scrollTop: jQuery("#snp-cf-bld").offset().top-50
		//}, 700);
	    }
	});
	snp_theme_mode_switch(jQuery('.snp-theme-mode-swicher:checked').val());
});