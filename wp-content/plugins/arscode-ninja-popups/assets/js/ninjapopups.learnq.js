jQuery(document).on("ninja_popups_ajax_response", function(event) {
    if (event.response.learnq !== undefined) {
        _learnq.push(["identify", event.response.learnq]);
    }
});

