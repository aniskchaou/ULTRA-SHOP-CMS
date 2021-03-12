<?php
	
add_filter('ninja_popups_subscribe_by_mailjet', 'ninja_popups_subscribe_by_mailjet', 10, 1);

function ninja_popups_subscribe_by_mailjet($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/mailjet/mailjet_class.php';
    
    $ml_mailjet_list = $params['popup_meta']['snp_ml_mailjet_list'][0];
    if (!$ml_mailjet_list) {
	    $ml_mailjet_list = snp_get_option('ml_mailjet_list');
	}

    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_mailjet_list,
			'errorMessage' => '',
		)
	);
	
	if (
		snp_get_option('ml_mailjet_apisecret') &&
		snp_get_option('ml_mailjet_apikey') && 
		$ml_mailjet_list
    ) {
	    $rest = new snp_mailjet(snp_get_option('ml_mailjet_apikey'), snp_get_option('ml_mailjet_apisecret'));
	    
	    $param = array();
	    if (count($params['data']['cf']) > 0) {
		    foreach($params['data']['cf'] as $k => $v) {
			    $param[] = array(
			    	'Name' => $k,
			    	'Value' => $v
			    );
			}
        }

        try {
	        $response = $rest->subscribe($ml_mailjet_list, snp_trim($params['data']['post']['email']), $params['data']['post']['name'], $param);
            
            if (isset($response) && $response->Count == 1) {
				$result['status'] = true;	
			}
        } catch (Exception $e) {
	        $result['log']['errorMessage'] = $e->getMessage();
        }
    }
            
	return $result;
}