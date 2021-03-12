jQuery(document).ready(function(){

    jQuery('.nhp-opts-geoip-popup-remove').on('click', function(){
        jQuery(this).parent().fadeOut('slow', function(){jQuery(this).remove();});
    });

    jQuery('.nhp-opts-geoip-popup-add').on('click', function() {
        var container = jQuery('#'+jQuery(this).attr('rel-id'));
        var count = container.children('li').length;

        var proto = jQuery('#repeater-template').html().replace(/{COUNT}/g, count);
        container.append(proto);
    });
});
