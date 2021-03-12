<?php

class SNP_NHP_Options_customerio_lists extends SNP_NHP_Options
{
	public function __construct($field = array(), $value ='', $parent)
	{
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

		$this->field = $field;
		$this->value = $value;
	}

	public function enqueue()
	{
		wp_enqueue_script(
			'nhp-opts-field-customerio_lists-js', SNP_NHP_OPTIONS_URL . 'fields/customerio_lists/field_customerio_lists.js', array('jquery', 'farbtastic'), time(), true
		);
	}
}