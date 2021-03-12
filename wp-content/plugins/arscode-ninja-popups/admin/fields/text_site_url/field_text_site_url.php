<?php

class SNP_NHP_Options_text_site_url extends SNP_NHP_Options
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
		
		echo '<b>'.site_url().'/</b><input type="text" id="'.$this->field['id'].''.$this->field['vcb_id'].'" name="'.$this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'].']" value="'.esc_attr($this->value).'" class="'.$class.'" />';
		
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':'';
	}
}