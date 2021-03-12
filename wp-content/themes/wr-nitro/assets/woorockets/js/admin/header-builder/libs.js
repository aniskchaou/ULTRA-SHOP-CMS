void function ($) {
    $.fn.clickOutside = function (callback, context) {
        return typeof callback != 'function' ? this : this.each(function (index, element) {
            $(window).on('mousedown', function clickHandler(e) {
                if (!$(element).is(e.target) && !$(e.target).parents('.' + $(element).attr('class').replace(/\s+/g, '.')).length)
                    $(window).off('mousedown', clickHandler),
                        callback.call(context, e);
            });
        });
    };

    window.wrOpenMediaBox = function (e) {
        var selector = e;//e.currentTarget;
        //e.preventDefault();
        // Store clicked element for later reference
        var $btn = $(selector), frame = $btn.data('wr_media_selector'), $input = $(selector).parent().find('.wr-background-image');
        if (!frame) { 
            // Create the media frame
            frame = wp.media({
                button: {
                    text: 'Select',
                },
                states: [
                    new wp.media.controller.Library({
                        title: 'select media',
                        library: wp.media.query({type: 'image'}),
                        multiple: false,
                        date: false,
                    })
                ]
            });

            // When an image is selected, run a callback
            frame.on('select', function () {
                console.log(123)
                // Grab the selected attachment
                var attachment = frame.state().get('selection').first();
                // Update the field value
                $input.val(attachment.attributes.url).trigger('change');
                console.log(attachment.attributes.url)
                //$input.val(attachment.attributes.url).trigger('wr:change');
                //view.enableClickOutSide();
            });
            console.log(frame)

            // Store media selector object for later reference
            $btn.data('wr_media_selector', frame);
        }
        aaa = frame
        console.log(frame)
        frame.open();

        /*  var selector = e.currentTarget;
         e.preventDefault();
         var $btn = $(selector),
         $input = $(selector).parent().find('.input-file');

         wp.media.editor.send.attachment = function(props, attachment){
         var link = attachment.url
         if ( props.size ) {
         link =  eval('attachment.sizes.' + props.size +'.url');
         }
         $input.val( link );
         $input.trigger('change');
         }
         // Open wp media editor without select multiple media option
         wp.media.editor.open({
         multiple: false
         });
         return false;*/
    }
}(jQuery);
