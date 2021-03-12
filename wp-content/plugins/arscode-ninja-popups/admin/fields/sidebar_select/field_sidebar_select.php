<?php

class SNP_NHP_Options_sidebar_select extends SNP_NHP_Options
{	
	public function __construct($field = array(), $value ='', $parent)
	{
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		
		$this->field = $field;
		$this->value = $value;
	}
	
	public function render()
	{
		global $wp_registered_sidebars;
		
		$class = (isset($this->field['class']))?'class="'.$this->field['class'].'" ':'';
		
		echo '<select id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" '.$class.'rows="6" >';
		$args = wp_parse_args($this->field['args'], array());
		if ($this->field['defaultforsite']) {
			echo '<option value=""'.selected($this->value, '', false).'>-- default for site --</option>';
		}
		
		foreach ((array) $wp_registered_sidebars as $sidebar) {
			echo '<option value="'.$sidebar['id'].'"'.selected($this->value, $sidebar['id'], false).'>'.$sidebar['name'].'</option>';
		}
		echo '</select>';

		echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':'';
	}
}