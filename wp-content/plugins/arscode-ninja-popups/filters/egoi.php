<?php
	
add_filter('ninja_popups_subscribe_by_egoi', 'ninja_popups_subscribe_by_egoi', 10, 1);

function ninja_popups_subscribe_by_egoi($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/egoi/snp_egoi.php';

    $ml_egoi_apikey = snp_get_option('ml_egoi_apikey');

    $rest = new snp_egoi($ml_egoi_apikey);

    try {
    	$ml_egoi_list = $params['popup_meta']['snp_ml_egoi_list'][0];
    	if (!$ml_egoi_list) {
    		$ml_egoi_list = snp_get_option('ml_egoi_list');
    	}

        $result = array(
			'status' => false,
			'log' => array(
				'listId' => $ml_egoi_list,
				'errorMessage' => '',
			)
		);

		$args = array(
			'listID' => $ml_egoi_list,
			'email' => snp_trim($params['data']['post']['email']),
		);

		$double_optin = snp_get_option('ml_egoi_double_optin');
		if ($double_optin == 1) {
			$args['status'] = 0;
		} else {
			$args['status'] = 1;
		}

		if (!empty($params['data']['post']['name'])) {
			$args['first_name'] = $params['data']['names']['first'];
			$args['last_name'] = $params['data']['names']['last'];
		}

		if (count($params['data']['cf']) > 0) {
			foreach ($params['data']['cf'] as $k => $v) {
				$args[$k] = $v;
			}
		}

		$res = $rest->subscribe($args);

		if ($res === true) {
			$result['status'] = true;
		}
	} catch (Exception $e) {
		$result['log']['errorMessage'] = $e->getMessage();
	}

	return $result;
}