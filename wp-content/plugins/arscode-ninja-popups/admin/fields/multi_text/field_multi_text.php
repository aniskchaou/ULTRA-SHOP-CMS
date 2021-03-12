<?php

class SNP_NHP_Options_multi_text extends SNP_NHP_Options
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

		echo '<ul id="'.$this->field['id'].'-ul">';
		
		if (isset($this->value) && is_array($this->value)) {
			foreach($this->value as $k => $value){
				if ($value != '') {
					echo '<li><input type="text" id="'.$this->field['id'].'-'.$k.'" name="'.$this->args['opt_name'].'['.$this->field['id'].'][]" value="'.esc_attr($value).'" class="'.$class.'" /> ';
					echo '<input type="button" class="nhp-opts-multi-text-remove button" value="'.__('Remove', 'nhp-opts').'" />';	
					echo '</li>';
					
				}
			}
		} else {
			echo '<li><input type="text" id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].'][]" value="" class="'.$class.'" /> ';
			echo '<input type="button" class="nhp-opts-multi-text-remove button" value="'.__('Remove', 'nhp-opts').'" />';	
			echo '</li>';
		}

		echo '<li style="display:none;"><input type="text" id="'.$this->field['id'].'" name="" value="" class="'.$class.'" /> ';
		echo '<input type="button" class="nhp-opts-multi-text-remove button" value="'.__('Remove', 'nhp-opts').'" />';	
		echo '</li>';

		echo '</ul>';

		echo '<input type="button" class="nhp-opts-multi-text-add button" rel-id="'.$this->field['id'].'-ul" rel-name="'.$this->args['opt_name'].'['.$this->field['id'].'][]" value="'.__('Add More', 'nhp-opts').'" />';	
		echo '<br/>';
		
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':'';	
	}

	public function enqueue()
	{
		wp_enqueue_script('nhp-opts-field-multi-text-js', SNP_NHP_OPTIONS_URL.'fields/multi_text/field_multi_text.js', array('jquery'), time(), true);
	}
}