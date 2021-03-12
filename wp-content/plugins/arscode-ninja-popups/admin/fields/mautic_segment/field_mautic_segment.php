<?php

class SNP_NHP_Options_mautic_segment extends SNP_NHP_Options
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

		if (!$this->value) {
			$this->value = $this->field['std'];
		}

		$this->field['options'] = snp_ml_get_mautic_segment();
		
		$class = (isset($this->field['class'])) ? 'class="' . $this->field['class'] . '" ' : '';
		
		echo '<select id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" ' . $class . 'rows="6" >';
		
		if (isset($this->field['meta']) && $this->field['meta'] == 1) {
		    echo '<option value="" ' . selected($this->value, '', false) . '>Use global settings</option>';
		}

		if (count((array)$this->field['options']) == 0) {
			echo '<option value="" ' . selected($this->value, '', false) . '>--</option>';
		} else {
			foreach ($this->field['options'] as $k => $v) {
				echo '<option value="' . $k . '" ' . selected($this->value, $k, false) . '>' . $v['name'] . '</option>';
			}
		}

		echo '</select>';

		if(!isset($this->field['meta']) || !$this->field['meta']) {
		    echo '<input type="button" rel-id="' . $this->field['id'] . '" class="button mautic_segment_gl" name="" value="Grab Lists" />';
		}

		echo (isset($this->field['desc']) && !empty($this->field['desc'])) ? ' <span class="description">' . $this->field['desc'] . '</span>' : '';
	}

	public function enqueue()
	{
		wp_enqueue_script(
				'nhp-opts-field-mautic_segment-js', SNP_NHP_OPTIONS_URL . 'fields/mautic_segment/field_mautic_owner.js', array('jquery'), time(), true
		);
	}
}