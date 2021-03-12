<?php

class SNP_NHP_Options_select_hide_below extends SNP_NHP_Options
{
	public function __construct($field = array(), $value ='', $parent)
	{
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

		$this->field = $field;
		$this->value = $value;
	}

	public function render()
	{
		$class = (isset($this->field['class'])) ? $this->field['class'] : '';
		
		echo '<select id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" class="' . $class . ' nhp-opts-select-hide-below" >';

		foreach($this->field['options'] as $k => $v) {
			echo '<option value="' . $k . '" ' . selected($this->value, $k, false) . ' data-allow="' . $v['allow'] . '">' . $v['name'] . '</option>';
		}

		echo '</select>';

		echo (isset($this->field['desc']) && !empty($this->field['desc'])) ? ' <span class="description">' . $this->field['desc'] . '</span>':'';
	}

	public function enqueue()
	{
		wp_enqueue_script(
			'nhp-opts-select-hide-below-js', SNP_NHP_OPTIONS_URL . 'fields/select_hide_below/field_select_hide_below.js', array('jquery'), time(), true
		);
	}
}