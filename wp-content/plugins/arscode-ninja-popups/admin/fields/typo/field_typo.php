<?php

class SNP_NHP_Options_typo extends SNP_NHP_Options
{

	public function __construct($field = array(), $value ='', $parent)
	{

		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

		$this->field = $field;
		$this->value = $value;
	}

	public function render()
	{

		$class = (isset($this->field['class'])) ? $this->field['class'] : 'colorSelectorInput';

		if (!isset($this->field['std'])) {
			$this->field['std'] = array();
		}

		if (!isset($this->value['font'])) {
			$this->value['font'] = isset($this->field['std']['font']) ? $this->field['std']['font'] : '';
		}

		if (!isset($this->value['size'])) {
			$this->value['size'] = isset($this->field['std']['size']) ? $this->field['std']['size'] : '';
		}

		if (!isset($this->value['color'])) {
			$this->value['color'] = isset($this->field['std']['color']) ? $this->field['std']['color'] : '';
		}

		echo '<div class="farb-popup-wrapper">';
		
		if (!isset($this->field['args']['disable_fonts'])) {
			echo '<select id="' . $this->field['id'] . 'font" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][font]" class="' . $class . '" rows="6" >';
			foreach ($this->field['args']['fonts'] as $k => $v) {
			    if (is_array($v)) {
			    	echo ' <optgroup label="'.$v['label'].'">';
			    	foreach ($v['fonts'] as $k2 => $v2) {
			    		echo '<option value="' . $v2 . '" ' . selected($this->value['font'], $v2, false) . '>' . $v2 . '</option>';
			    	}
			    	echo '</optgroup>';
			    } else {
			    	echo '<option value="' . $v . '" ' . selected($this->value['font'], $v, false) . '>' . $v . '</option>';
			    }
			}
			echo '</select>';
		}

		if (!isset($this->field['args']['disable_sizes'])) {
			echo '<select id="' . $this->field['id'] . 'size" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][size]" class="' . $class . '" rows="6" >';
			foreach ($this->field['args']['sizes'] as $k => $v) {
				echo '<option value="' . $k . '" ' . selected($this->value['size'], $k, false) . '>' . $v . '</option>';
			}
			echo '</select>';
		}

		if (!isset($this->field['args']['disable_colors'])) {
			echo '<div class="colorSelector" id="' . $this->field['id'] . 'colorpicker"><div></div></div>';
			echo '<input type="text" id="' . $this->field['id'] . 'color" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][color]" value="' . $this->value['color'] . '" class="' . $class . ' popup-colorpicker2 colorSelectorInput" style="width:70px;"/>';
		}

		echo (isset($this->field['desc']) && !empty($this->field['desc'])) ? ' <span class="description" style="line-height: 33px; margin-left: 5px;">' . $this->field['desc'] . '</span>' : '';

		echo '</div>';
	}

	public function enqueue()
	{
		wp_enqueue_script(
			'nhp-opts-field-color-js', SNP_NHP_OPTIONS_URL . 'fields/color/field_color.js', array('jquery'), time(), true
		);
	}
}