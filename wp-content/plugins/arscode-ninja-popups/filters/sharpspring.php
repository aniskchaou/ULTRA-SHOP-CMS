<?php
	
add_filter('ninja_popups_subscribe_by_sharpspring', 'ninja_popups_subscribe_by_sharpspring', 10, 1);

function ninja_popups_subscribe_by_sharpspring($params = array()) 
{
    require_once SNP_DIR_PATH . '/include/sharpspring/sharpspring.php';

	if (!defined('SHARPSPRING_ACCOUNTID')) {
        define('SHARPSPRING_ACCOUNTID', snp_get_option('ml_sharpspring_account_id'));
    }

    if (!defined('SHARPSPRING_SECRETKEY')) {
        define('SHARPSPRING_SECRETKEY', snp_get_option('ml_sharpspring_secret_key'));
    }


    $listId = $params['popup_meta']['snp_ml_sharpspring_list_id'][0];
    if (!$listId) {
        $listId = snp_get_option('ml_sharpspring_list_id');
    }

   	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $listId,
			'errorMessage' => '',
		)
	);

	$data = array(
		'emailAddress' => snp_trim($params['data']['post']['email']),

	);

	if (!empty($params['data']['post']['name'])) {
		$data['firstName'] = $params['data']['names']['first'];
		$data['lastName'] = $params['data']['names']['last'];
    }
   	
   	if (count($params['data']['cf']) > 0) {
   		foreach($params['data']['cf'] as $k => $v) {
   			$data[$k] = $v;
   		}
   	}

    $api = new \CollingMedia\SharpSpring(SHARPSPRING_ACCOUNTID, SHARPSPRING_SECRETKEY);

    $response = $api->call('createLeads', $data);

    if (isset($response['error'])) {
    	$result['log']['errorMessage'] = $response['error']['message'];
    } else {
    	$errors = array();
    	if (isset($result['creates'])) {
    		foreach ($result['creates'] as $message) {
    			if (isset($message['error'])) {
    				$errors[] = $message['error']['message'];
    			}
    		}
    	}

    	if (empty($errors)) {
    		$result['status'] = true;
    	} else {
    		$result['log']['errorMessage'] = implode("\n", $errors);
    	}
    }

    return $result;
}
