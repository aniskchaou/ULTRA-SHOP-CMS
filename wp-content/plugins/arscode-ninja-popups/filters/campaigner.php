<?php
	
add_filter('ninja_popups_subscribe_by_campaigner', 'ninja_popups_subscribe_by_campaigner', 10, 1);

function ninja_popups_subscribe_by_campaigner($params = array()) 
{
	if (!class_exists('Campaigner')) {
		require_once SNP_DIR_PATH . '/include/campaigner/campaigner.php';
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
	
	$api = new Campaigner();
	$api->setUsername(snp_get_option('ml_campaigner_username'));
	$api->setPassword(snp_get_option('ml_campaigner_password'));
	
	try {
		$params = array();
		if (count($params['data']['cf']) > 0) {
			foreach ($params['data']['cf'] as $k => $v) {
				$params[] = array(
					'IsNull' => false,
					'Id'     => $k,
					'Value'  => $v
				);
            }
        }
        
        $api->addContact($listId, snp_trim($params['data']['post']['email']), $params['data']['names']['first'], $params['data']['names']['last'], $params);
        
        $result['status'] = true;
    } catch (Exception $e) {
	    $result['log']['errorMessage'] = 'Campaigner API error: ' . $e->getMessage();
	}
            
	return $result;
}