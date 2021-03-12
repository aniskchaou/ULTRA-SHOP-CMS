<?php
	
add_filter('ninja_popups_subscribe_by_activecampaign', 'ninja_popups_subscribe_by_activecampaign', 10, 1);

function ninja_popups_subscribe_by_activecampaign($params = array()) 
{
	if (!class_exists('ActiveCampaign')) {
		require_once SNP_DIR_PATH . '/include/activecampaign/ActiveCampaign.class.php';
	}

	$ml_activecampaign_list = $params['popup_meta']['snp_ml_activecampaign_list'][0];
	if (!$ml_activecampaign_list) {
		$ml_activecampaign_list = snp_get_option('ml_activecampaign_list');
	}

	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_activecampaign_list,
			'errorMessage' => '',
		)
	);

	if (
		snp_get_option('ml_activecampaign_apiurl') &&
		snp_get_option('ml_activecampaign_apikey') &&
		$ml_activecampaign_list
	) {
		$ac = new ActiveCampaign(snp_get_option('ml_activecampaign_apiurl'), snp_get_option('ml_activecampaign_apikey'));

		$args = array(
			"email" => snp_trim($params['data']['post']['email']),
			"p[{$ml_activecampaign_list}]" => $ml_activecampaign_list,
			"status[{$ml_activecampaign_list}]" => 1, // "Active" status
		);

		if (!empty($params['data']['post']['name'])) {
			$args['first_name'] = $params['data']['names']['first'];
			$args['last_name'] = $params['data']['names']['last'];
		}

		if (count($params['data']['cf']) > 0) {
			foreach ($params['data']['cf'] as $field => $value) {
				$args['field[%' . $field . '%,0]'] = $value;
			}
		}

		$double_optin = snp_get_option('ml_activecampaign_double_optin');
		if ($double_optin == 1) {
			$args["instantresponders[{$ml_activecampaign_list}]"] = 1;
		}

		$form = snp_get_option('ml_activecampaign_form');
		if (isset($params['popup_meta']['snp_ml_activecampaign_form'][0]) && $params['popup_meta']['snp_ml_activecampaign_form'][0]) {
			$form = $params['popup_meta']['snp_ml_activecampaign_form'][0];
		}

		if ($form) {
			$args["form"] = $form;
		}

		$response = $ac->api("contact/sync", $args);
		if (!(int) $response->success) {
			$result['log']['errorMessage'] = $response->error;
		} else {
			$result['status'] = true;
		}
	}

	return $result;
}