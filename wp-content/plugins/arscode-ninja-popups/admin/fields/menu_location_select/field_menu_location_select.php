<?php

class SNP_NHP_Options_menu_location_select extends SNP_NHP_Options
{
	public function __construct($field = array(), $value ='', $parent)
	{
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

		$this->field = $field;
		$this->value = $value;
	}

	public function render()
	{
		global $_wp_registered_nav_menus;
		
		$class = (isset($this->field['class'])) ? 'class="'.$this->field['class'].'" ' : '';

		echo '<select id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" '.$class.' >';
		
		if ($_wp_registered_nav_menus) {
			foreach ( $_wp_registered_nav_menus as $k => $v ) {
				echo '<option value="'.$k.'"'.selected($this->value, $k, false).'>'.$v.'</option>';
			}
		}

		echo '</select>';
	
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':'';
		
	}
}