jQuery(document).on("ninja_popups_ajax_response", function(event) {
    if (event.response.drip !== undefined) {
        _dcq.push(["identify", event.response.drip]);
    }
});
