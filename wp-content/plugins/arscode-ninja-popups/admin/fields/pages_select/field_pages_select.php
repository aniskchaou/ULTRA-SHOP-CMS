<?php

class SNP_NHP_Options_pages_select extends SNP_NHP_Options
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
		
		echo '<select id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" '.$class.'rows="6" >';
		
		$args = wp_parse_args($this->field['args'], array());
		
		$pages = get_pages($args); 
		foreach ($pages as $page) {
			echo '<option value="'.$page->ID.'"'.selected($this->value, $page->ID, false).'>'.$page->post_title.'</option>';
		}

		echo '</select>';

		echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':'';
	}
}