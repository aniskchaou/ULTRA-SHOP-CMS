<?php

class SNP_NHP_Options_aweber_auth extends SNP_NHP_Options
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
		$class = (isset($this->field['class'])) ? 'class="' . $this->field['class'] . '" ' : '';

		$snp_ml_aw_auth_info = get_option('snp_ml_aw_auth_info');
		
		echo '<div id="' . $this->field['id'] . '_disconnect_div"'.($snp_ml_aw_auth_info ? '': ' style="display:none;"').'>';
		echo '<input type="button" class="button-primary aweber_remove_auth" name="" value="Remove Connection" />';
		echo '</div>';
		echo '<div id="' . $this->field['id'] . '_connect_div"'.($snp_ml_aw_auth_info ? ' style="display:none;"' : '').'>';
		echo '<b>Step 1:</b> <a href="https://auth.aweber.com/1.0/oauth/authorize_app/8f90de9f" target="_blank">Click here to get your authorization code.</a><br />';
		echo '<b>Step 2:</b> Paste in your authorization code:<br />';
		echo '<textarea id="'.$this->field['id'].'_auth_code" '.$class.' rows="3"></textarea><br />';
		echo '<input type="button" rel-id="' . $this->field['id'] . '_auth_code" class="button-primary aweber_auth" name="" value="Connect" />';
		echo '</div>';
	}

	public function enqueue()
	{
		wp_enqueue_script(
			'nhp-opts-field-aweber_auth-js', SNP_NHP_OPTIONS_URL . 'fields/aweber_auth/field_aweber_auth.js', array('jquery', 'farbtastic'), time(), true
		);
	}
}