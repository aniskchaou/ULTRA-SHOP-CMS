jQuery(document).ready(function(){
    getMcFieldsGroups(jQuery('.fg_ml_manager').val());

    jQuery('.fg_ml_manager').on('change',function(){
        getMcFieldsGroups(jQuery(this).val());
    });
});

function getMcFieldsGroups(list)
{
    jQuery.ajax({
        url: ajaxurl,
        data:{
            'action': 'snp_get_mc_groups',
            'ajax_mc_list_id': list 
        },
        dataType: 'JSON',
        type: 'POST',
        success:function(response){
            jQuery('.bld-mc-select-groups option').remove();
            jQuery('.bld-mc-select-groups').append(jQuery('<option>', {
                value: '',
                text : 'Select MailChimp Group',
                selected : true
            }));

            jQuery.each(response, function(i, field) {
                jQuery('.bld-mc-select-groups').append(jQuery('<option>', {
                    value: field.field,
                    text : field.name +' ('+ field.field +')' 
                }));
            });
            
            jQuery('.bld-mc-select-groups').each(function() {
                var value = jQuery(this).data('value');
                jQuery(this).val(value);
            });
        },
        error: function(errorThrown){
            alert('Error...');
        }
    });
}
