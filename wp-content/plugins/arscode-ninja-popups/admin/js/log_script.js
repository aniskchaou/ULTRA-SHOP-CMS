jQuery(document).ready(function(){
    jQuery('#log_form').submit(function(){
        if(confirm('Do you want to execute action?')) {
            return true;
        } else {
            return false;
        }
    });

    jQuery('#log_checkbox').click(function(){
        if (jQuery('#log_checkbox').prop('checked') == true) {
            jQuery('.snp_log_checkbox').attr('checked', true);
        } else {
            jQuery('.snp_log_checkbox').attr('checked', false);
        }
    });
});