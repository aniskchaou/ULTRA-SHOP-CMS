<?php
	
add_filter('ninja_popups_subscribe_by_convertkit', 'ninja_popups_subscribe_by_convertkit', 10, 1);

function ninja_popups_subscribe_by_convertkit($params = array()) 
{
	if (!class_exists('Convertkit')) {
		require_once SNP_DIR_PATH . '/include/convertkit/convertkit.php';
    }
    
    $ml_convertkit_apikey = snp_get_option('ml_convertkit_apikey');
    
    $formId = $params['popup_meta']['snp_ml_convertkit_list'][0];
    if (!$formId) {
	    $formId = snp_get_option('ml_convertkit_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $formId,
			'errorMessage' => '',
		)
	);

	$api = new Convertkit($ml_convertkit_apikey);
	
	$data =  array(
		'email' => snp_trim($params['data']['post']['email']),
		'first_name' => $params['data']['names']['first'],
		'fields' => array(
			'last_name' => $params['data']['names']['last']
        )
    );
    
    if (count($params['data']['cf']) > 0) {
	    foreach ($params['data']['cf'] as $k => $v) {
            $data['fields'][$k] = $v;
        }
    }
    
    try {
	    $response = $api->addToForm($formId, $data);
	    
	    if (isset($response->subscription->id)) {
		    $result['status'] = true;
		} else {
			$result['log']['errorMessage'] = 'ConvertKit Problem: ' . var_export($result);
        }
    } catch (Exception $e) {
	    $result['log']['errorMessage'] = 'ConvertKit Problem: ' . $e->getMessage();
    }
            
	return $result;
}
