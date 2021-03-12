<?php
	
add_filter('ninja_popups_subscribe_by_wysija', 'ninja_popups_subscribe_by_wysija', 10, 1);

function ninja_popups_subscribe_by_wysija($params = array()) 
{
	$ml_wy_list = $params['popup_meta']['snp_ml_wy_list'][0];
	if (!$ml_wy_list) {
		$ml_wy_list = snp_get_option('ml_wy_list');
	}
            
    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_wy_list,
			'errorMessage' => '',
		)
	);
	
	$userData = array(
		'email' => snp_trim($params['data']['post']['email']),
		'firstname' => $params['data']['names']['first'],
		'lastname' => $params['data']['names']['last']
	);
    
    $data = array(
	    'user' => $userData,
	    'user_list' => array('list_ids' => array(
	    	$ml_wy_list
		))
	);
	
	$userHelper = &WYSIJA::get('user', 'helper');
	
	if ($userHelper->addSubscriber($data)) {
    	$result['status'] = true;
    } else {
	    $result['log']['errorMessage'] = 'MailPoet Problem!';
    }
            
	return $result;
}