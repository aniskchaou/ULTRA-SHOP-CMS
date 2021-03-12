<?php
class SNP_NHP_Options_preview_popup extends SNP_NHP_Options
{
	public function __construct($field = array(), $value ='', $parent)
	{
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

		$this->field = $field;
		$this->value = $value;
	}

	public function render()
	{	
		$class = (isset($this->field['class']))?'class="'.$this->field['class'].'" ':'';

		echo '<div style="text-align: center;">';
		echo '<input class="snp-button-preview" type="button" id="'.$this->field['id'].'_button" value="Preview" />';
		echo '<br /><br />';
		echo '<b>Clear Your Cookies to test the popup on your live website.</b><br /><br />';
		echo '<input type="button" id="'.$this->field['id'].'_button_cookie" class="button" value="Clear my cookie" />';		
		echo '</div>';
		echo '<script type="text/javascript">';
		echo 'jQuery(document).ready(function(){';
		echo '	jQuery("#'.$this->field['id'].'_button").click(function(){';
		echo '		jQuery("#post").attr("target","_blank");';
		echo '		jQuery("#post").attr("action","admin-ajax.php");';
		echo '		jQuery("#hiddenaction").val("snp_preview_popup");';
		echo '		jQuery("#originalaction").val("snp_preview_popup");';
		echo "		jQuery('<input>').attr('type','hidden').attr('name','action').attr('id','snp_preview_popup_i').val('snp_preview_popup').appendTo('#post');";
		echo '		jQuery("#post").submit();';
		echo '		jQuery("#hiddenaction").val("editpost");';
		echo '		jQuery("#originalaction").val("editpost");';
		echo '		jQuery("#snp_preview_popup_i").remove();';
		echo '		jQuery("#post").attr("target","");';
		echo '		jQuery("#post").attr("action","post.php");';
		echo '	});';
		echo '	jQuery("#'.$this->field['id'].'_button_cookie").click(function(){';
		echo '		jQuery.cookie("snp_'.snp_get_option('class_popup','snppopup').'-welcome",null, { path: "/"});';
		echo '		jQuery.cookie("snp_'.snp_get_option('class_popup','snppopup').'-exit",null, { path: "/"});';
		echo '		alert("Done!");';
		echo '	});';
		echo '});';
		echo '</script>';
	}

	public function enqueue() {}	
}