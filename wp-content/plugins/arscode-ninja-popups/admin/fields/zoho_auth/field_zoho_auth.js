jQuery(document).ready(function() {
    jQuery('.zoho_auth').click(function(){
        jQuery.ajax({
            url: ajaxurl,
            data:{
                'action': 'snp_ml_list',
                'ml_manager': 'zoho_auth',
                'ml_zoho_email': jQuery('#ml_zoho_email').val(),
                'ml_zoho_password': jQuery('#ml_zoho_password').val(),
                'ml_zoho_application': jQuery('#ml_zoho_application').val(),
            },
            dataType: 'JSON',
            type: 'POST',
            success:function(response){
                if (response.Ok == true) {
                    if (response.redirect) {
                        window.location = response.redirect;
                    } else {
                        jQuery('#ml_zoho_auth_disconnect_div').show();
                        jQuery('#ml_zoho_auth_connect_div').hide();
                        jQuery('.zoho_campaigns_gl').click();
                    }
                } else {
                    alert(response.Error);
                }
            },
            error: function(errorThrown){
                alert('Error...');
            }
        });
    });

    jQuery('.zoho_remove_auth').click(function(){
        jQuery.ajax({
            url: ajaxurl,
            data:{
                'action': 'snp_ml_list',
                'ml_manager': 'zoho_remove_auth',
                'ml_zoho_email': jQuery('#ml_zoho_email').val(),
                'ml_zoho_password': jQuery('#ml_zoho_password').val(),
                'ml_zoho_application': jQuery('#ml_zoho_application').val(),
            },
            dataType: 'JSON',
            type: 'POST',
            success:function(response) {
                if (response.Ok == true) {
                    jQuery('#ml_zoho_auth_disconnect_div').hide();
                    jQuery('#ml_zoho_auth_connect_div').show();
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