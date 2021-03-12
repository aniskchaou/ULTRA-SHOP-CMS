<?php

class SNP_NHP_Options_csv_file extends SNP_NHP_Options
{
	public function __construct($field = array(), $value ='', $parent)
	{
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

		$this->field = $field;
		$this->value = $value;
	}

	public function render()
	{
		$class = (isset($this->field['class']))?$this->field['class']:'regular-text';
		
		$input_type = (isset($this->field['input_type']))?$this->field['input_type']:'text';
		
		if(!$this->value) {
			$this->value = uniqid().'.csv';
		}
		
		echo '<input type="'.$input_type.'" id="'.$this->field['id'].''.$this->field['vcb_id'].'" name="'.$this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'].']" value="'.esc_attr($this->value).'" class="'.$class.'" />';
		echo '<br />File will be stored in: <b>'.SNP_DIR_PATH.'csv/</b>.<br />';
		
		if (is_writable(SNP_DIR_PATH.'csv/')) {
			echo 'Folder permission: <span style="color: green;"><b>OK</b></span><br />';
		} else {
			echo '<b>Folder permission: <span style="color: red;">Error</span>.<br />Please check permission for storage folder.</b><br />';
		}

		echo '<input type="button" id="'.$this->field['id'].'_download" class="button" value="Download" />';	
		echo '<script>jQuery(document).ready(function(){jQuery("#'.$this->field['id'].'_download").click(function(){window.open("'.SNP_URL.'/csv/"+jQuery("#'.$this->field['id'].'").val());});});</script>';
	}
}