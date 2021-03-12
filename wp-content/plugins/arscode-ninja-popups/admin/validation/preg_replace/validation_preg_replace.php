<?php
class SNP_NHP_Validation_preg_replace extends SNP_NHP_Options
{	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since SNP_NHP_Options 1.0.1
	*/
	function __construct($field, $value, $current)
	{
		parent::__construct();

		$this->field = $field;
		$this->value = $value;
		$this->current = $current;

		$this->validate();	
	}
	
	/**
	 * Field Render Function.
	 *
	 * Takes the vars and validates them
	 *
	 * @since SNP_NHP_Options 1.0.1
	*/
	function validate()
	{
		$this->value = preg_replace_callback($this->field['preg']['pattern'], function($matches){
			return $this->field['preg']['replacement'];
		}, $this->value);
				
	}
}