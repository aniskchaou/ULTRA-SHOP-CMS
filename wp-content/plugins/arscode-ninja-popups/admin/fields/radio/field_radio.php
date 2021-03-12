<?php
class SNP_NHP_Options_radio extends SNP_NHP_Options
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
		
		echo '<fieldset>';

		foreach($this->field['options'] as $k => $v) {
			echo '<label for="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'">';
			echo '<input type="radio" id="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" '.$class.' value="'.$k.'" '.checked($this->value, $k, false).'/>';
			echo ' <span>'.$v.'</span>';
			echo '</label><br/>';
		}

		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'<span class="description">'.$this->field['desc'].'</span>':'';
		
		echo '</fieldset>';
		
	}
}