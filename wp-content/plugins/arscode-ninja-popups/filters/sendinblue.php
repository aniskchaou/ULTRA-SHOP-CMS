<?php
	
add_filter('ninja_popups_subscribe_by_sendinblue', 'ninja_popups_subscribe_by_sendinblue', 10, 1);

function ninja_popups_subscribe_by_sendinblue($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/sendinblue/Sendinblue.php';
	
	$ml_sendinblue_list = $params['popup_meta']['snp_ml_sendinblue_list'][0];
    if (!$ml_sendinblue_list) {
	    $ml_sendinblue_list = snp_get_option('ml_sendinblue_list');
    }
    
    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_sendinblue_list,
			'errorMessage' => '',
		)
	);
	
	if (snp_get_option('ml_sendinblue_apikey') && $ml_sendinblue_list) {
		$api = new SNPSendinblue("https://api.sendinblue.com/v2.0", snp_get_option('ml_sendinblue_apikey'));
		if (!empty($params['data']['post']['name'])) {
			$atributes = array(
				'NAME' => $params['data']['names']['first'],
				'SURNAME' => $params['data']['names']['last']
			);
		}
	
		if (count($params['data']['cf']) > 0) {
			foreach ($params['data']['cf'] as $field => $value) {
				$atributes[$field] = $value;
			}
		}
		
		$args = array(
			"email"      => snp_trim($params['data']['post']['email']),
			"attributes" => $atributes,
			"listid"     => array($ml_sendinblue_list),
		);
			
		$response = $api->create_update_user($args);
		if (isset($response['code']) && $response['code'] == 'success') {
			$result['status'] = true;
		} else {
			$result['log']['errorMessage'] = var_export($response, true);
		}
    }
    
    return $result;
}