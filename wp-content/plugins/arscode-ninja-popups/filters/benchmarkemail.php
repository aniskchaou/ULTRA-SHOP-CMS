<?php
	
add_filter('ninja_popups_subscribe_by_benchmarkemail', 'ninja_popups_subscribe_by_benchmarkemail', 10, 1);

function ninja_popups_subscribe_by_benchmarkemail($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/benchmarkemail/snp_benchmark_class.php';

	$ml_benchmarkemail_list = $params['popup_meta']['snp_ml_benchmarkemail_list'][0];
	if (!$ml_benchmarkemail_list) {
		$ml_benchmarkemail_list = snp_get_option('ml_benchmarkemail_list');
	}

	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_benchmarkemail_list,
			'errorMessage' => '',
		)
	);

	if (snp_get_option('ml_benchmarkemail_apikey') && $ml_benchmarkemail_list) {
		$rest = new snp_benchmark_class(snp_get_option('ml_benchmarkemail_apikey'));

		$args = array();
		$args['contacts']['email'] = snp_trim($params['data']['post']['email']);
		if (!empty($params['data']['post']['name'])) {
			$args['contacts']['firstname'] = $params['data']['names']['first'];
			$args['contacts']['lastname'] = $params['data']['names']['last'];
		}

		$args['listID'] = $ml_benchmarkemail_list;

		if (count($params['data']['cf']) > 0) {
			foreach($params['data']['cf'] as $k => $v) {
				$args['contacts'][$k] = $v;
			}
		}

		$double_optin = snp_get_option('ml_benchmarkemail_double_optin');
		if ($double_optin == 1) {
			$args['optin'] = 1;
		} else {
			$args['optin'] = 0;
		}

		try {
			$response = $rest->subscribe($args);

			if (isset($response) && $response == 1) {
				$result['status'] = true;
			}
		} catch (Exception $e) {
			$result['log']['errorMessage'] = $e->getMessage();
		}
	}

	return $result;
}