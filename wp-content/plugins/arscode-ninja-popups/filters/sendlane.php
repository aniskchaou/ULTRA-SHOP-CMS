<?php
	
add_filter('ninja_popups_subscribe_by_sendlane', 'ninja_popups_subscribe_by_sendlane', 10, 1);

function ninja_popups_subscribe_by_sendlane($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/sendlane/snp_sendlane.php';
	
	$ml_sendlane_list = $params['popup_meta']['snp_ml_sendlane_list'][0];
	if (!$ml_sendlane_list) {
		$ml_sendlane_list = snp_get_option('ml_sendlane_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_sendlane_list,
			'errorMessage' => '',
		)
	);
	
	if (
		snp_get_option('ml_sendlane_apikey') &&
		snp_get_option('ml_sendlane_hash') &&
		snp_get_option('ml_sendlane_subdomain') &&
		$ml_sendlane_list
    ) {
	    $rest = new snp_sendlane(snp_get_option('ml_sendlane_apikey'), snp_get_option('ml_sendlane_hash'), snp_get_option('ml_sendlane_subdomain'));
	    
	    $args = array();
	    $args['list_id'] = $ml_sendlane_list;
        
        $name = '';
        if (!empty($params['data']['post']['name'])) {
	        $name = $params['data']['names']['first'].' '.$params['data']['names']['last'];
	    }
	    
	    $args['email'] = $name.'<'.snp_trim($params['data']['post']['email']).'>';
	    try {
		    $response = $rest->subscribe($args);
		    
		    $result['status'] = true;
        } catch (Exception $e) {
	        $result['log']['errorMessage'] = $e->getMessage();
        }
    }
            
	return $result;
}