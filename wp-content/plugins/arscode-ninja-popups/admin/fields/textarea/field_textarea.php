<?php
class SNP_NHP_Options_textarea extends SNP_NHP_Options
{
	public function __construct($field = array(), $value ='', $parent)
	{
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		
		$this->field = $field;
		$this->value = $value;		
	}

	public function render()
	{
		$class = (isset($this->field['class']))?$this->field['class']:'large-text';
		
		echo '<textarea id="'.$this->field['id'].''.$this->field['vcb'].'" name="'.$this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'].']" class="'.$class.'" rows="6" >'.esc_attr($this->value).'</textarea>';
		
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'<br/><span class="description">'.$this->field['desc'].'</span>':'';
	
	}	
}