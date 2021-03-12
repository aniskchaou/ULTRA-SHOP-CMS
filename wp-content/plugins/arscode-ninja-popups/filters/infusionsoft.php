<?php
	
add_filter('ninja_popups_subscribe_by_infusionsoft', 'ninja_popups_subscribe_by_infusionsoft', 10, 1);

function ninja_popups_subscribe_by_infusionsoft($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/infusionsoft/infusionsoft.php';

	if (snp_get_option('ml_inf_subdomain') && snp_get_option('ml_inf_apikey')) {
		$infusionsoft = new Infusionsoft(snp_get_option('ml_inf_subdomain'), snp_get_option('ml_inf_apikey'));

		$user = array(
			'Email' => snp_trim($params['data']['post']['email'])
		);

		if (!empty($params['data']['names']['first'])) {
			$user['FirstName'] = $params['data']['names']['first'];
		}

		if (!empty($params['data']['names']['last'])) {
			$user['LastName'] = $params['data']['names']['last'];
		}

		if (count($params['data']['cf']) > 0) {
			$user = array_merge($user, (array) $params['data']['cf']);
		}

		$ml_inf_list = $params['popup_meta']['snp_ml_inf_list'][0];
		if (!$ml_inf_list) {
			$ml_inf_list = snp_get_option('ml_inf_list');
		}

		$result = array(
			'status' => false,
			'log' => array(
				'listId' => $ml_inf_list,
				'errorMessage' => '',
			)
		);

		$data = $infusionsoft->contact('findByEmail', snp_trim($params['data']['post']['email']), array('Id'));
		if (!$data) {
			$contact_id = $infusionsoft->contact('add', $user);
		} else {
			$contact_id = $data[0]['Id'];
		}

		$r = $infusionsoft->APIEmail('optIn', snp_trim($params['data']['post']['email']), "Ninja Popups on " . get_bloginfo());
		if ($contact_id && $ml_inf_list)  {
			$infusionsoft->contact('addToGroup', $contact_id, $ml_inf_list);
		}

		if ($contact_id) {
			$result['status'] = true;
		}
    }

	return $result;
}