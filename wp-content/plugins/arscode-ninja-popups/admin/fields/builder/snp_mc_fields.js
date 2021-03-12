jQuery(document).ready(function(){
    getMcFields(jQuery('.fg_ml_manager').val());
    
    jQuery('body').on('change', '.bld-mc-select-name', function(){
        if(jQuery(this).val() != '')
        {
            jQuery(this).parent().children('.bld-editbox-name').val( jQuery(this).val() );
        }
    });
    //$('#builder-tpl').on('mouseenter', '.bld-el-cont', function() {}
    jQuery('.fg_ml_manager').on('change',function(){
        getMcFields(jQuery(this).val());
    });
});

function getMcFields(list)
{
    jQuery.ajax({
            url: ajaxurl,
            data:{
                'action': 'snp_get_mc_fields',
                'ajax_mc_list_id': list 
            },
            dataType: 'JSON',
            type: 'POST',
            success:function(response){
                jQuery('.bld-mc-select-name option').remove();
                jQuery('.bld-mc-select-name').append(jQuery('<option>', {
                        value: '',
                        text : 'Select MailChimp field',
                        selected : true
                    }));
                jQuery.each(response, function(i, field)
                {
                    jQuery('.bld-mc-select-name').append(jQuery('<option>', {
                        value: field.field,
                        text : field.name +' ('+ field.field +')' 
                    }));
                });
            },
            error: function(errorThrown){
               alert('Error...');
            }
        });
}
