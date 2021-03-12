<?php
	
add_filter('ninja_popups_subscribe_by_sgautorepondeur', 'ninja_popups_subscribe_by_sgautorepondeur', 10, 1);

function ninja_popups_subscribe_by_sgautorepondeur($params = array()) 
{
	if (!class_exists('API_SG')) {
		require_once SNP_DIR_PATH . '/include/sgautorepondeur/api.php';
	}

    $listId = $params['popup_meta']['snp_ml_sgautorepondeur_list'][0];
    if (!$listId) {
	    $listId = snp_get_option('ml_sgautorepondeur_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $listId,
			'errorMessage' => '',
		)
	);
	
	$sgApi = new API_SG(snp_get_option('ml_sgautorepondeur_id'), snp_get_option('ml_sgautorepondeur_code'));
	
	try {
		$sgApi->set('listeid', $listId)
			->set('email', snp_trim($params['data']['post']['email']))
			->set('name', $params['data']['names']['first'])
			->set('first_name', $params['data']['names']['last']);
		
		if (count($params['data']['cf']) > 0) {
			foreach ($params['data']['cf'] as $k => $v) {
                if (in_array($k, array(
                    'civility',
                    'address',
                    'Zipcode',
                    'City',
                    'country',
                    'website',
                    'telephone',
                    'mobile',                     
                    'skype',
                    'UserName',
                    'Birthday',
                    'sponsor',
                    'field_1',
                    'field_2',
                    'field_3',
                    'field_4',
                    'field_5',
                    'field_6',
                    'field_7',
                    'field_8',
                    'field_9',
                    'field_10',
                    'field_11',
                    'field_12',
                    'field_13',
                    'field_14',
                    'field_15',
					'field_16'
                ))) {
	                $sgApi->set($k, $v);
                }
            }
        }
        
        $call = $sgApi->call('set_subscriber');
        $response = json_decode($call);
        
        if (isset($response->reponse)) {
	        if (!is_array($response->reponse) && $response->reponse == 'inscriptionok') {
		        $result['status'] = true;
            } else {
	            $error = '';
	            foreach ($response->reponse as $errorMessage) {
		            $error .= $errorMessage."\n";
		        }
		        
		        $result['log']['errorMessage'] = 'SG Autorepondeur error message: ' . $error;
            }
        }
    } catch (Exception $e) {
	    $result['log']['errorMessage'] = 'SG Autorepondeur error message: ' . $e->getMessage();
	}
	
    return $result;
}