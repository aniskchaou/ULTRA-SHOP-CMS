<?php

class SNP_NHP_Options_button_set extends SNP_NHP_Options
{
	public function __construct($field = array(), $value ='', $parent)
	{
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

		$this->field = $field;
		$this->value = $value;
	}

	public function render()
	{
		$class = (isset($this->field['class'])) ? 'class="'.$this->field['class'].'" ' : '';
		
		echo '<fieldset class="buttonset">';
		
		foreach ($this->field['options'] as $k => $v) {
			echo '<input type="radio" id="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" '.$class.' value="'.$k.'" '.checked($this->value, $k, false).'/>';
			echo '<label for="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'">'.$v.'</label>';
		}
			
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'&nbsp;&nbsp;<span class="description">'.$this->field['desc'].'</span>':'';
		
		echo '</fieldset>';
	}

	public function enqueue()
	{
		wp_enqueue_style('nhp-opts-jquery-ui-css');

		wp_enqueue_script(
			'nhp-opts-field-button_set-js', SNP_NHP_OPTIONS_URL.'fields/button_set/field_button_set.js', array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'), time(), true
		);
	}
}