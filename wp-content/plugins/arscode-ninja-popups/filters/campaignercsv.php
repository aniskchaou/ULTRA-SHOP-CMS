<?php
	
add_filter('ninja_popups_subscribe_by_campaignercsv', 'ninja_popups_subscribe_by_campaignercsv', 10, 1);

function ninja_popups_subscribe_by_campaignercsv($params = array()) 
{
	if (!class_exists('CampaignerCsv')) {
		require_once SNP_DIR_PATH . '/include/campaignercsv/campaignercsv.php';
    }
    
    $listId = $params['popup_meta']['snp_ml_campaigner_list'][0];
    if (!$listId) {
	    $listId = snp_get_option('ml_campaigner_list');
    }

    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $listId,
			'errorMessage' => '',
		)
	);
	
	$api = new CampaignerCsv();
	$api->setUsername(snp_get_option('ml_campaignercsv_username'));
	$api->setPassword(snp_get_option('ml_campaignercsv_password'));
	$api->login();
	
	$resultApi = $api->importContact(array(
		'email'     => snp_trim($params['data']['post']['email']),
		'firstname' => $params['data']['names']['first'],
		'lastname'  => $params['data']['names']['last'],
		'phone'     => '',
		'fax'       => '',
    ), $listId);
    
    if ($resultApi) {
	    $result['status'] = true;
	} else {
		$result['log']['errorMessage'] = 'Campaigner CSV error!';
	}     
            
	return $result;
}