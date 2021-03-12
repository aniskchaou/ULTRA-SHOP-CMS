<?php

class SNP_NHP_Options_preview_camp extends SNP_NHP_Options
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
		
		echo '<input style="font-size: 18px; font-weight: bold; padding: 15px;" type="button" id="'.$this->field['id'].'_button" value="Preview" />';
		echo '<script type="text/javascript">';
		echo 'jQuery(document).ready(function(){';
		echo '	jQuery("#'.$this->field['id'].'_button").click(function(){';
		echo '		jQuery("#post").attr("target","_blank");';
		echo '		jQuery("#post").attr("action","admin-ajax.php");';
		echo "		jQuery('<input>').attr('type','hidden').attr('name','action').attr('id','snp_preview_camp_i').val('snp_preview_camp').appendTo('#post');";
		echo '		jQuery("#post").submit();';
		echo '		jQuery("#snp_preview_camp_i").remove();';
		echo '		jQuery("#post").attr("target","");';
		echo '		jQuery("#post").attr("action","post.php");';
		echo '	});';
		echo '});';
		echo '</script>';	
	}

	public function enqueue() {}
}