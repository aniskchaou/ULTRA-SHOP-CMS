<?php

class SNP_NHP_Options_select_theme extends SNP_NHP_Options
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
                wp_register_style( 'snp-nhp-opts-field-select_theme-css', SNP_NHP_OPTIONS_URL . 'fields/select_theme/field_select_theme.css' );
		wp_enqueue_style( 'snp-nhp-opts-field-select_theme-css' );
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
		global $SNP_THEMES_DIR;
		$class = (isset($this->field['class']))?$this->field['class']:'';
		//var_dump($this->value);
		
                echo '<div class="snp-select-mode">';
                echo '<div class="snp-select-mode-desc"><strong>Make Your Choise</strong><br />Choose ready themes<br />or build your own theme</div>';
		echo '<label class="snp-mode-swicher-label '.((isset($this->value['mode']) ? $this->value['mode'] : '0')==0 ? 'selected' : '' ).'" for="snp-theme-mode-swicher-1">';
                echo '<span class="snp-theme-mode-ico-1"></span>';
                echo '<input id="snp-theme-mode-swicher-1" class="snp-theme-mode-swicher" type="radio" ' . checked((isset($this->value['mode']) ? $this->value['mode'] : '0'), 0, false) . ' name="'.$this->args['opt_name'].'['.$this->field['id'].'][mode]" value="0" />';
                echo 'Ready Themes';
                echo '</label>';
		echo '<label class="snp-mode-swicher-label '.((isset($this->value['mode']) ? $this->value['mode'] : '0')==1 ? 'selected' : '' ).'" for="snp-theme-mode-swicher-2">';
                echo '<span class="snp-theme-mode-ico-2"></span>';
                echo '<input id="snp-theme-mode-swicher-2" class="snp-theme-mode-swicher" type="radio" ' . checked((isset($this->value['mode']) ? $this->value['mode'] : '0'), 1, false) . ' name="'.$this->args['opt_name'].'['.$this->field['id'].'][mode]" value="1" />';
                echo 'Theme Builder';
                echo '</label>';
                echo '</div>';
                
                
		echo '<div id="snp-theme-mode-1">';
		if(!isset($this->value['theme']))
		{
		    echo '<input type="hidden" id="nhp-opts-select-theme-load" value="1" />';
		}
		else
		{
		    echo '<input type="hidden" id="nhp-opts-select-theme-load" value="0" />';
		}
		echo '<select  id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].'][theme]" class="'.$class.' nhp-opts-select-theme" >';
		foreach ($this->field['options'] as $k => $v)
		{
                    if(isset($v['NAME']) && $v['NAME'])
                    {
			echo '<option value="' . $k . '" ' . selected((isset($this->value['theme']) ? $this->value['theme'] : ''), $k, false) . ' data-preview="'.plugins_url('', realpath($v['DIR'])).'">' . $v['NAME'] . '</option>';
                    }
		}//foreach

		echo '</select>';
		
		echo '<select id="'.$this->field['id'].'-color" name="'.$this->args['opt_name'].'['.$this->field['id'].'][color]" class="'.$class.' nhp-opts-select-theme-color" >';
		echo '</select>';
		
		echo '<select id="'.$this->field['id'].'-type" name="'.$this->args['opt_name'].'['.$this->field['id'].'][type]" class="'.$class.' nhp-opts-select-theme-type">';
		echo '</select>';

		echo (isset($this->field['desc']) && !empty($this->field['desc'])) ? ' <span class="description">' . $this->field['desc'] . '</span>' : '';
		
		echo '<input type="hidden" id="SNP_URL" value="'.SNP_URL.'" />';
		if(isset($this->value['color']))
		{
		    echo '<input type="hidden" id="nhp-opts-select-theme-color-org-val" value="'.$this->value['color'].'" />';
		}
		if(isset($this->value['type']))
		{
		    echo '<input type="hidden" id="nhp-opts-select-theme-type-org-val" value="'.$this->value['type'].'" />';
		}
		echo '<div class="snp-nhp-opts-select-theme-preview"><img id="nhp-opts-select-theme-preview-img"/></div>';
		echo '</div>';
		// mode 2
		echo '<div id="snp-theme-mode-2">';
		//echo 'Use builder...';
		echo '</div>';
	}
	function enqueue(){
		
		wp_enqueue_script(
			'nhp-opts-select-theme-js', 
			SNP_NHP_OPTIONS_URL.'fields/select_theme/field_select_theme.js', 
			array('jquery'),
			time(),
			true
		);
		
	}//function

//function
}

//class
?>