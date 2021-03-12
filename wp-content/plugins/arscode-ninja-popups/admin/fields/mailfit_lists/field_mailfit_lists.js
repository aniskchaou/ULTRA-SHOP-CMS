jQuery(document).ready(function(){
    jQuery('.mailfit_lists_gl').click(function(){
        ListsSelect=jQuery('#'+jQuery(this).attr('rel-id'));
        ListsSelect.find('option').remove();
        jQuery("<option/>").val(0).text('Loading...').appendTo(ListsSelect);
        jQuery.ajax({
            url: ajaxurl,
            data:{
                'action': 'snp_ml_list',
                'ml_manager': jQuery('#ml_manager').val(),
                'ml_mailfit_endpoint': jQuery('#ml_mailfit_endpoint').val(),
                'ml_mailfit_apitoken': jQuery('#ml_mailfit_apitoken').val()
            },
            dataType: 'JSON',
            type: 'POST',
            success:function(response){
                ListsSelect.find('option').remove();
                jQuery.each(response, function(i, option) {
                    jQuery("<option/>").val(i).text(option.name).appendTo(ListsSelect);
                });
            },
            error: function(errorThrown){
                alert('Error...');
            }
        });
    });
});