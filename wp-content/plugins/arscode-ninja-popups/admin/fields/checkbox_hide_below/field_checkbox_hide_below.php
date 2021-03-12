<?php
class SNP_NHP_Options_checkbox_hide_below extends SNP_NHP_Options
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
		
		echo ($this->field['desc'] != '')?' <label for="'.$this->field['id'].'">':'';
		
		echo '<input type="checkbox" id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" value="1" class="'.$class.' nhp-opts-checkbox-hide-below" '.checked($this->value, '1', false).' />';
		
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?' '.$this->field['desc'].'</label>':'';
	}

	public function enqueue()
	{
		wp_enqueue_script('nhp-opts-checkbox-hide-below-js', SNP_NHP_OPTIONS_URL.'fields/checkbox_hide_below/field_checkbox_hide_below.js', array('jquery'), time(), true);
	}
}