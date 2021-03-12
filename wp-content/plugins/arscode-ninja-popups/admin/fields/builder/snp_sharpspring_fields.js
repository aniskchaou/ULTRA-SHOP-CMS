jQuery(document).ready(function(){
    getSharpSpringFields();
    
    jQuery('body').on('change', '.bld-sharpspring-select-name', function(){
        if (jQuery(this).val() != '') {
            jQuery(this).parent().children('.bld-editbox-name').val(jQuery(this).val());
        }
    });

    jQuery('.fg_ml_manager').on('change',function(){
        getSharpSpringFields();
    });
});

function getSharpSpringFields()
{
    jQuery.ajax({
        url: ajaxurl,
        data:{
            'action': 'snp_get_sharpspring_fields' 
        },
        dataType: 'JSON',
        type: 'POST',
        success:function(response){
            jQuery('.bld-sharpspring-select-name option').remove();
            jQuery('.bld-sharpspring-select-name').append(jQuery('<option>', {
                value: '',
                text : 'Select SharpSpring field',
                selected : true
            }));
            
            jQuery.each(response, function(i, field) {
                    jQuery('.bld-sharpspring-select-name').append(jQuery('<option>', {
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
