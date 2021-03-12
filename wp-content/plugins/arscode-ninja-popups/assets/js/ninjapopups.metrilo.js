jQuery(document).on("ninja_popups_ajax_response", function(event) {
    if (event.response.metrilo !== undefined) {
        var metriloJson = jQuery.parseJSON(event.response.metrilo);
        metrilo.identify(metriloJson.email, metriloJson);
        metrilo.event('apply_tags', {
            tags: metriloJson.tags
        });
    }
});
