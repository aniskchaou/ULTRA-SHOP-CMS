jQuery(document).ready(function() {
	jQuery('.mautic_auth').click(function(){
		jQuery.ajax({
			url: ajaxurl,
			data:{
				'action': 'snp_ml_list',
				'ml_manager': 'mautic_auth',
				'ml_mautic_url': jQuery('#ml_mautic_url').val(),
				'ml_mautic_key': jQuery('#ml_mautic_key').val(),
				'ml_mautic_secret': jQuery('#ml_mautic_secret').val(),
			},
			dataType: 'JSON',
			type: 'POST',
			success:function(response) {
				console.log(response);
				if (response.Ok == true) {
					if (response.redirect) {
						window.location = response.redirect;
					} else {
						jQuery('#ml_mautic_auth_disconnect_div').show();
						jQuery('#ml_mautic_auth_connect_div').hide();	
						jQuery('.mautic_owner_gl').click();
						jQuery('.mautic_stage_gl').click();
						jQuery('.mautic_segment_gl').click();
					}
				} else {
					alert(response.Error);
				}
			},
			error: function(errorThrown){
				console.log(errorThrown);
			   alert('Error...');
			}
		});
	});

	jQuery('.mautic_remove_auth').click(function(){
		jQuery.ajax({
			url: ajaxurl,
			data:{
				'action': 'snp_ml_list',
				'ml_manager': 'mautic_remove_auth',
				'ml_mautic_url': jQuery('#ml_mautic_url').val(),
				'ml_mautic_key': jQuery('#ml_mautic_key').val(),
				'ml_mautic_secret': jQuery('#ml_mautic_secret').val(),
			},
			dataType: 'JSON',
			type: 'POST',
			success:function(response) {
				if (response.Ok == true) {
					jQuery('#ml_mautic_auth_disconnect_div').hide();
					jQuery('#ml_mautic_auth_connect_div').show();
				} else {
					alert(response.Error);
				}
			},
			error: function(errorThrown){
				alert('Error...');
			}
		});
	});
});