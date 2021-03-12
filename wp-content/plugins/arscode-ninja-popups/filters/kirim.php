<?php
	
add_filter('ninja_popups_subscribe_by_kirim', 'ninja_popups_subscribe_by_kirim', 10, 1);

function ninja_popups_subscribe_by_kirim($params = array()) 
{
	if (!class_exists('Kirim')) {
		require_once SNP_DIR_PATH . '/include/kirim/kirim.php';
	}
	
	$listId = $params['popup_meta']['snp_ml_kirim_list'][0];
	if (!$listId) {
		$listId = snp_get_option('ml_kirim_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $listId,
			'errorMessage' => '',
		)
	);
	
	try {
		$params = array();
		if (count($params['data']['cf']) > 0) {
			foreach ($params['data']['cf'] as $k => $v) {
				$params[$k] = $v;
            }
        }
        
        $api = new Kirim();
        $api->setUsername(snp_get_option('ml_kirim_username'));
        $api->setToken(snp_get_option('ml_kirim_token'));
        if ($api->addSubscriber($listId, snp_trim($params['data']['post']['email']), $params['data']['names']['first'], $params['data']['names']['last'], $params)) {
	        $result['status'] = true;
        }
    } catch (Exception $e) {
	    $result['log']['errorMessage'] = 'Kirim.email error message: ' . $e->getMessage();
    }
	
	return $result;
}