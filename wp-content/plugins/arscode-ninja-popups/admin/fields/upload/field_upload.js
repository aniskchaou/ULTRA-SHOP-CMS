/*global jQuery, document, redux_upload, formfield:true, preview:true, tb_show, window, imgurl:true, tb_remove, $relid:true*/
/*
This is the uploader for wordpress starting from version 3.5
*/
jQuery(document).ready(function(){

    jQuery("img[src='']").attr("src", nhp_upload.url);

    jQuery("#wpwrap").on('click', '.snp-nhp-opts-upload', function( event ) {
	var activeFileUploadContext = jQuery(this).parent();
	var relid = jQuery(this).attr('rel-id');

	event.preventDefault();

	// If the media frame already exists, reopen it.
	/*if ( typeof(custom_file_frame)!=="undefined" ) {
                    custom_file_frame.open();
                    return;
                }*/

	// if its not null, its broking custom_file_frame's onselect "activeFileUploadContext"
	custom_file_frame = null;

	// Create the media frame.
	custom_file_frame = wp.media.frames.customHeader = wp.media({
	    // Set the title of the modal.
	    title: jQuery(this).data("choose"),

	    // Tell the modal to show only images. Ignore if want ALL
	    library: {
		type: 'image'
	    },
	    // Customize the submit button.
	    button: {
		// Set the text of the button.
		text: jQuery(this).data("update")
	    }
	});

	custom_file_frame.on( "select", function() {
	    // Grab the selected attachment.
	    var attachment = custom_file_frame.state().get("selection").first();

	    // Update value of the targetfield input with the attachment url.
	    jQuery('.snp-nhp-opts-screenshot',activeFileUploadContext).attr('src', attachment.attributes.url);
	    jQuery('#' + relid ).val(attachment.attributes.url).trigger('change');

	    jQuery('.snp-nhp-opts-upload',activeFileUploadContext).hide();
	    jQuery('.snp-nhp-opts-screenshot',activeFileUploadContext).show();
	    jQuery('.snp-nhp-opts-upload-remove',activeFileUploadContext).show();
	});

	custom_file_frame.open();
    });

    jQuery("#wpwrap").on('click', '.snp-nhp-opts-upload-remove', function( event ) {
	var activeFileUploadContext = jQuery(this).parent();
	var relid = jQuery(this).attr('rel-id');

	event.preventDefault();

	jQuery('#' + relid).val('');
	jQuery(this).prev().fadeIn('slow');
	jQuery('.snp-nhp-opts-screenshot',activeFileUploadContext).fadeOut('slow');
	jQuery(this).fadeOut('slow');
    });

});
