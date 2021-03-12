jQuery(document).ready(function($){
	jQuery('#ml_htmlform_exp').keyup(function(){
		var $html = jQuery('<div/>').html( $(this).val( ));
		if($html.find('form').attr('action') !== undefined)
		{
		    $('#ml_html_url').val($html.find('form').attr('action'));
		    $('#ml_html_hidden').val('');
		    $html.find('input[type=hidden]').each(function( ) {
			$('#ml_html_hidden').val($('#ml_html_hidden').val() + '<input type="hidden" name="'+ $(this).attr('name') +'" value="'+ $(this).val() +'" />\n');
		    });
		    var inputs = [];
		    $html.find('input[type=text],input[type=email]').each(function( ) {
			var name = $(this).attr('name');
			var name_s = name.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '').toLowerCase();
			if(name_s=='email' || name_s=='mail' || name_s.indexOf("mail")!=-1)
		        {   
			    $('#ml_html_email').val(name);
			}
			else
			{
			    $('#ml_html_name').val(name);
			}
			// email, mail
			// name, lastname
		    });
		}
	});
});