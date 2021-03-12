<?php

class SNP_NHP_Options_multi_checkbox extends SNP_NHP_Options
{
	public function __construct($field = array(), $value ='', $parent)
	{
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

		$this->field = $field;
		$this->value = $value;
	}

	public function render()
	{
		$class = (isset($this->field['class'])) ? $this->field['class'] : 'regular-text';
		
		echo '<fieldset>';
		
		foreach($this->field['options'] as $k => $v){
			$value = '';
			if (is_array($this->value)) {
				$value = $this->value[$k] = (isset($this->value[$k])) ? $this->value[$k] : '';
			} else {
				$value = $this->value;
			}
				
			echo '<label for="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'">';
			echo '<input type="checkbox" id="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'" name="'.$this->args['opt_name'].'['.$this->field['id'].']['.$k.']" '.$class.' value="1" '.checked($value, '1', false).'/>';
			echo ' '.$v.'</label><br/>';
		}

		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'<span class="description">'.$this->field['desc'].'</span>':'';
		
		echo '</fieldset>';
	}
}