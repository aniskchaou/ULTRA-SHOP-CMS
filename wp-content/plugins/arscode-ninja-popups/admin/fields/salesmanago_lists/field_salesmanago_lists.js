jQuery(document).ready(function(){
	jQuery('.salesmanago_lists_gl').click(function(){
		ListsSelect=jQuery('#'+jQuery(this).attr('rel-id'));
		ListsSelect.find('option').remove();
		jQuery("<option/>").val(0).text('Loading...').appendTo(ListsSelect);
		jQuery.ajax({
			url: ajaxurl,
			data:{
				'action': 'snp_ml_list',
				'ml_manager': jQuery('#ml_manager').val(),
				'ml_salesmanago_apikey': jQuery('#ml_salesmanago_apikey').val(),
				'ml_salesmanago_apisecret': jQuery('#ml_salesmanago_apisecret').val(),
				'ml_salesmanago_endpoint': jQuery('#ml_salesmanago_endpoint').val(),
				'ml_salesmanago_clientid': jQuery('#ml_salesmanago_clientid').val(),
				'ml_salesmanago_tag': jQuery('#ml_salesmanago_tag').val(),
				'ml_salesmanago_useremail': jQuery('#ml_salesmanago_useremail').val()
			},
			dataType: 'JSON',
			type: 'POST',
			success:function(response){
				ListsSelect.find('option').remove();
				jQuery.each(response, function(i, option)
				{
					jQuery("<option/>").val(i).text(option.name).appendTo(ListsSelect);
				});
			},
			error: function(errorThrown){
			   alert('Error...');
			}
		});
	});
});