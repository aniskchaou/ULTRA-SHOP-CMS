<?php
	
add_filter('ninja_popups_subscribe_by_sendpress', 'ninja_popups_subscribe_by_sendpress', 10, 1);

function ninja_popups_subscribe_by_sendpress($params = array()) 
{	
	$ml_sp_list = $params['popup_meta']['snp_ml_sp_list'][0];
	if (!$ml_sp_list) {
		$ml_sp_list = snp_get_option('ml_sp_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_sp_list,
			'errorMessage' => '',
		)
	);
	
	try {
		SendPress_Data::subscribe_user($ml_sp_list, snp_trim($_POST['email']), $names['first'], $names['last'], 2);
		
		$result['status'] = true;
    } catch (Exception $e) {
	    $result['log']['errorMessage'] = 'SendPress Problem!';
    }
    
	return $result;}