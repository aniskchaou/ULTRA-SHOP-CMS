<?php

class SNP_NHP_Options_htmlform_exp extends SNP_NHP_Options
{
    public function __construct($field = array(), $value = '', $parent)
    {
    	parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

    	$this->field = $field;
    	$this->value = $value;
    }

    public function render()
    {
    	$class = (isset($this->field['class'])) ? $this->field['class'] : 'regular-text';
    	$input_type = (isset($this->field['input_type'])) ? $this->field['input_type'] : 'text';

    	echo '<textarea style="width: 100%; height: 300px;" id="' . $this->field['id'] . '' . $this->field['vcb_id'] . '" name="' . $this->args['opt_name'] . '' . $this->field['vcb'] . '[' . $this->field['id'] . ']" class="' . $class . '" />'.$this->value.'</textarea><br />';

    	echo (isset($this->field['desc']) && !empty($this->field['desc'])) ? ' <span class="description">' . $this->field['desc'] . '</span>' : '';
    }

    public function enqueue()
    {
    	wp_enqueue_script(
    		'nhp-opts-field-htmlform_exp-js', SNP_NHP_OPTIONS_URL . 'fields/htmlform_exp/field_htmlform_exp.js', array('jquery'), time(), true
    	);
    }
}