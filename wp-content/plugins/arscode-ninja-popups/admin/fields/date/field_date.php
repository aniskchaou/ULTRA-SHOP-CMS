<?php
class SNP_NHP_Options_date extends SNP_NHP_Options
{	
	public function __construct($field = array(), $value ='', $parent)
	{
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

		$this->field = $field;
		$this->value = $value;
	}

	public function render()
	{
		$class = (isset($this->field['class']))?$this->field['class']:'';
		
		echo '<input style="text-align: center;" type="text" id="snp-'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" value="'.$this->value.'" class="'.$class.' nhp-opts-datepicker" />';
		
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':'';	
	}

	public function enqueue()
	{
		wp_enqueue_script('nhp-opts-field-date-js', SNP_NHP_OPTIONS_URL.'fields/date/field_date.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), time(), true);	
	}
}