<?php

class SNP_NHP_Options_radio_img extends SNP_NHP_Options
{	
	public function __construct($field = array(), $value = '', $parent = '')
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
			$selected = (checked($this->value, $k, false) != '') ? ' nhp-radio-img-selected' : '';

			echo '<label class="nhp-radio-img'.$selected.' nhp-radio-img-'.$this->field['id'].'" for="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'">';
			echo '<input type="radio" id="'.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" '.$class.' value="'.$k.'" '.checked($this->value, $k, false).'/>';
			echo '<img src="'.$v['img'].'" alt="'.$v['title'].'" onclick="jQuery:nhp_radio_img_select(\''.$this->field['id'].'_'.array_search($k,array_keys($this->field['options'])).'\', \''.$this->field['id'].'\');" />';
			echo '<br/><span>'.$v['title'].'</span>';
			echo '</label>';		
		}

		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'<br/><span class="description">'.$this->field['desc'].'</span>':'';
		
		echo '</fieldset>';	
	}

	public function enqueue()
	{
		wp_enqueue_script(
			'nhp-opts-field-radio_img-js', SNP_NHP_OPTIONS_URL . 'fields/radio_img/field_radio_img.js', array('jquery'), time(), true
		);	
	}
}