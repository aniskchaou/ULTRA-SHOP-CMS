jQuery(document).ready(function(){
	jQuery('.aweber_auth').click(function(){
		jQuery.ajax({
			url: ajaxurl,
			data:{
				'action': 'snp_ml_list',
				'ml_manager': 'aweber_auth',
				'ml_aw_auth_code': jQuery('#ml_aw_auth_auth_code').val()
			},
			dataType: 'JSON',
			type: 'POST',
			success:function(response){
				if(response.Ok==true)
				{
					jQuery('#ml_aw_auth_disconnect_div').show();
					jQuery('#ml_aw_auth_connect_div').hide();	
					jQuery('.aweber_lists_gl').click();
				}
				else
				{
					alert(response.Error);
				}
			},
			error: function(errorThrown){
			   alert('Error...');
			}
		});
	});
	jQuery('.aweber_remove_auth').click(function(){
		jQuery.ajax({
			url: ajaxurl,
			data:{
				'action': 'snp_ml_list',
				'ml_manager': 'aweber_remove_auth'
			},
			dataType: 'JSON',
			type: 'POST',
			success:function(response){
				if(response.Ok==true)
				{
					jQuery('#ml_aw_auth_disconnect_div').hide();
					jQuery('#ml_aw_auth_connect_div').show();
				}
				else
				{
					alert(response.Error);
				}
			},
			error: function(errorThrown){
			   alert('Error...');
			}
		});
	});
});