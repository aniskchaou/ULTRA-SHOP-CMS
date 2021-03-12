<?php
if (!defined('ABSPATH')) {
    die('-1');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    	<title>Shortcode Panel</title>
    	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
    	<base target="_self" />
    	<script type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    </head>
    <body>
    	<script>
    		function snp_insert_shortcode() {
    			var popup = document.getElementById('snp_popup').value;
    			var autoopen = document.getElementById('snp_autoopen').checked;
    			var autoopenv = '';

    			if (popup) {
    				var selected = tinyMCEPopup.getWindowArg('selected');
    				if (autoopen == true) {
    					autoopenv = ' autoopen=true';
    				}

    				if (selected) {
    					content =  '[ninja-popup id='+popup+''+autoopenv+']'+selected+'[/ninja-popup]';
    				} else {
    					content =  '[ninja-popup id='+popup+''+autoopenv+']';
    				}

    				tinymce.execCommand('mceInsertContent', false, content);
    			}    

    			tinyMCEPopup.close();

    			return false;
    		}
    	</script>
    	<form action="#">
    		<div>
    			<br />
    			<table border="0" cellpadding="4" cellspacing="0">
    				<tr>
    					<td nowrap="nowrap"><label for="snp_popup"><?php _e("Select Popup:", 'nhp-opts'); ?></label></td>
    					<td>
    						<select id="snp_popup" style="width: 205px">
	    						<?php
	    						$Popups = snp_get_popups();
	    						if (count($Popups) > 0) {
	    							foreach ((array) $Popups as $ID => $Name) {
	    								if (!empty($Name)) {
	    									echo '<option value="' . $ID . '">' . $Name . '</option>';
	    								}
	    							}
	    						} else {
	    							echo '<option value="">Create some popups first...</option>';
	    						}
	    						?>
	    					</select>
	    				</td>
	    			</tr>
	    			<tr>
	    				<td nowrap="nowrap"><label for="snp_autoopen"><?php _e("Autoopen:", 'nhp-opts'); ?></label></td>
	    				<td><input type="checkbox" id="snp_autoopen" value="1" /></td>
	    			</tr>
	    		</table>
	    	</div>
	    	
	    	<br />
	    	<br />

	    	<div class="mceActionPanel">
	    		<div style="float: left">
	    			<input type="button" id="cancel" name="cancel" value="Cancel" onClick="tinyMCEPopup.close();" />
	    		</div>
	    		<div style="float: right">
	    			<input type="submit" id="insert" name="insert" onClick="return snp_insert_shortcode();" value="Insert" />
	    		</div>
	    	</div>
	    </form>
    </body>
</html>
