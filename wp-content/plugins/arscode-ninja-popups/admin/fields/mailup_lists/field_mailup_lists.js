jQuery(document).ready(function(){
	jQuery('.mailup_lists_gl').click(function(){
		ListsSelect=jQuery('#'+jQuery(this).attr('rel-id'));
		ListsSelect.find('option').remove();
		jQuery("<option/>").val(0).text('Loading...').appendTo(ListsSelect);
		jQuery.ajax({
			url: ajaxurl,
			data:{
				'action': 'snp_ml_list',
				'ml_manager': jQuery('#ml_manager').val(),
				'ml_mailup_clientid': jQuery('#ml_mailup_clientid').val(),
				'ml_mailup_clientsecret': jQuery('#ml_mailup_clientsecret').val(),
                                'ml_mailup_login': jQuery('#ml_mailup_login').val(),
                                'ml_mailup_password': jQuery('#ml_mailup_password').val()
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