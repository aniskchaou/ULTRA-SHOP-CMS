<?php

class SNP_NHP_Options_mautic_auth extends SNP_NHP_Options
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

		$snp_ml_mautic_auth_info = get_option('snp_ml_mautic_auth_info');

		$displayConnect = false;
		if (isset($snp_ml_mautic_auth_info['accessToken']) && !empty($snp_ml_mautic_auth_info['accessToken'])) {
			$displayConnect = true;
		}

		echo '<div id="' . $this->field['id'] . '_disconnect_div"'.($displayConnect ? '': ' style="display:none;"').' '.$class.'>';
		echo '<input type="button" class="button-primary mautic_remove_auth" name="" value="Remove Connection" />';
		echo '</div>';
		echo '<div id="' . $this->field['id'] . '_connect_div"'.($displayConnect ? ' style="display:none;"' : '').' '.$class.'>';
		echo '<input type="button" rel-id="' . $this->field['id'] . '_auth_code" class="button-primary mautic_auth" name="" value="Connect" />';
		echo '</div>';
	}

	public function enqueue()
	{
		wp_enqueue_script(
			'nhp-opts-field-mautic_auth-js', SNP_NHP_OPTIONS_URL . 'fields/mautic_auth/field_mautic_auth.js', array('jquery', 'farbtastic'), time(), true
		);
	}
}