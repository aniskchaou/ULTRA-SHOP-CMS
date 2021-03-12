/**
 * Forward port jQuery.live()
 * Wrapper for newer jQuery.on()
 * Uses optimized selector context
 * Only add if live() not already existing.
 */
if (typeof jQuery.fn.live == 'undefined' || !(jQuery.isFunction(jQuery.fn.live))) {
    jQuery.fn.extend({
        live: function (event, callback) {
            if (this.selector) {
                jQuery(document).on(event, this.selector, callback);
            }
        }
    });
}
