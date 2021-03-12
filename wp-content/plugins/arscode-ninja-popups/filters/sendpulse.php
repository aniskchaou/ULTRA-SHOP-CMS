<?php
	
add_filter('ninja_popups_subscribe_by_sendpulse', 'ninja_popups_subscribe_by_sendpulse', 10, 1);

function ninja_popups_subscribe_by_sendpulse($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/sendpulse/sendpulse.php';
	
	$ml_sendpulse_list = $params['popup_meta']['snp_ml_sendpulse_list'][0];
	if (!$ml_sendpulse_list) {
		$ml_sendpulse_list = snp_get_option('ml_sendpulse_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_sendpulse_list,
			'errorMessage' => '',
		)
	);
	
	if (
		snp_get_option('ml_sendpulse_id') &&
		snp_get_option('ml_sendpulse_apisecret') && 
		$ml_sendpulse_list
	) {
		$rest = new snp_sendpulse(snp_get_option('ml_sendpulse_id'), snp_get_option('ml_sendpulse_apisecret'));
		
		$args = array();
		$args['email'] = snp_trim($params['data']['post']['email']);
		$args['variables'] = array();
		if (count($params['data']['cf']) > 0) {
			$args['variables'] = (array) $params['data']['cf'];
		}
		
		if (!empty($params['data']['post']['name'])) {
			$args['variables']['name'] = $params['data']['post']['name'];
		}
		
		try {
			$response = $rest->subscribe($args, $ml_sendpulse_list);
			
			if (isset($response) && $response->result === true) {
				$result['status'] = true;	
			}
    	} catch (Exception $e) {
	    	$result['log']['errorMessage'] = $e->getMessage();
        }
    }
            
	return $result;	
}