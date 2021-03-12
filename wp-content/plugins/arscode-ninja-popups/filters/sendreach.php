<?php
	
add_filter('ninja_popups_subscribe_by_sendreach', 'ninja_popups_subscribe_by_sendreach', 10, 1);

function ninja_popups_subscribe_by_sendreach($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/sendreach/sendreach_api.php';

	$ml_sendreach_list = $params['popup_meta']['snp_ml_sendreach_list'][0];
    if (!$ml_sendreach_list) {
    	$ml_sendreach_list = snp_get_option('ml_sendreach_list');
    }

    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_sendreach_list,
			'errorMessage' => '',
		)
	);

	if (
		snp_get_option('ml_sendreach_pubkey') &&
		snp_get_option('ml_sendreach_privkey') &&
		$ml_sendreach_list
	) {
		$rest = new snp_sendreach(snp_get_option('ml_sendreach_pubkey'), snp_get_option('ml_sendreach_privkey'));

		$args = array();
        $args['EMAIL'] = snp_trim($params['data']['post']['email']);
        if (!empty($params['data']['post']['name'])) {
        	$args = array_merge($args, array(
        		'FNAME' => $params['data']['names']['first'],
        		'LNAME' => $params['data']['names']['last']
        	));
        }

        if (count($params['data']['cf']) > 0) {
        	$args = array_merge($args, $params['data']['cf']);
        }

        try {
        	$response = $rest->subscribe($args, $ml_sendreach_list);
        	$response = json_decode($response, true);

        	if (isset($response) && ($response['status'] == 'success' || $response['error'] == 'The subscriber already exists in this list.')) {
        		$result['status'] = true;
        	}
        } catch (Exception $e) {
        	$result['log']['errorMessage'] = $e->getMessage();
        }
    }

	return $result;
}