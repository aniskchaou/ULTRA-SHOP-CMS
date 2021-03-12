<?php

class SNP_NHP_Options_typo extends SNP_NHP_Options
{

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since SNP_NHP_Options 1.0
	 */
	function __construct($field = array(), $value ='', $parent)
	{

		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();
	}

//function

	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since SNP_NHP_Options 1.0
	 */
	function render()
	{

		$class = (isset($this->field['class'])) ? $this->field['class'] : 'colorSelectorInput';

		if(!isset($this->field['std']))
		{
			$this->field['std']=array();
		}
		if (!isset($this->value['font']))
		{
			$this->value['font'] = isset($this->field['std']['font']) ? $this->field['std']['font'] : '';
		}
		if (!isset($this->value['size']))
		{
			$this->value['size'] = isset($this->field['std']['size']) ? $this->field['std']['size'] : '';
		}
		if (!isset($this->value['color']))
		{
			$this->value['color'] = isset($this->field['std']['color']) ? $this->field['std']['color'] : '';
		}

		echo '<div class="farb-popup-wrapper">';
		if (!isset($this->field['args']['disable_fonts']))
		{
			echo '<select id="' . $this->field['id'] . 'font" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][font]" class="' . $class . '" rows="6" >';

			foreach ($this->field['args']['fonts'] as $k => $v)
			{

				echo '<option value="' . $k . '" ' . selected($this->value['font'], $k, false) . '>' . $v . '</option>';
			}//foreach

			echo '</select>';
		}
		if (!isset($this->field['args']['disable_sizes']))
		{
			echo '<select id="' . $this->field['id'] . 'size" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][size]" class="' . $class . '" rows="6" >';

			foreach ($this->field['args']['sizes'] as $k => $v)
			{

				echo '<option value="' . $k . '" ' . selected($this->value['size'], $k, false) . '>' . $v . '</option>';
			}//foreach

			echo '</select>';
		}
		if (!isset($this->field['args']['disable_colors']))
		{
			echo '<div class="colorSelector" id="' . $this->field['id'] . 'colorpicker"><div></div></div>';
			echo '<input type="text" id="' . $this->field['id'] . 'color" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][color]" value="' . $this->value['color'] . '" class="' . $class . ' popup-colorpicker2 colorSelectorInput" style="width:70px;"/>';
		}
		echo (isset($this->field['desc']) && !empty($this->field['desc'])) ? ' <span class="description" style="line-height: 33px; margin-left: 5px;">' . $this->field['desc'] . '</span>' : '';

		echo '</div>';
	}

//function

	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since SNP_NHP_Options 1.0
	 */
	function enqueue()
	{

		wp_enqueue_script(
				'nhp-opts-field-color-js', SNP_NHP_OPTIONS_URL . 'fields/color/field_color.js', array('jquery'), time(), true
		);
	}

//function
}

//class
?>