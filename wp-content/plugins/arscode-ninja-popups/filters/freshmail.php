<?php
	
add_filter('ninja_popups_subscribe_by_freshmail', 'ninja_popups_subscribe_by_freshmail', 10, 1);

function ninja_popups_subscribe_by_freshmail($params = array()) 
{
	if (!class_exists('FmRestApi')) {
		require_once SNP_DIR_PATH . '/include/freshmail/class.rest.php';
	}

	$ml_freshmail_list = $params['popup_meta']['snp_ml_freshmail_list'][0];
	if (!$ml_freshmail_list) {
		$ml_freshmail_list = snp_get_option('ml_freshmail_list');
	}

    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_freshmail_list,
			'errorMessage' => '',
		)
	);
	
	if (
		snp_get_option('ml_freshmail_apisecret') &&
		snp_get_option('ml_freshmail_apikey') &&
		$ml_freshmail_list
	) {
		$rest = new FmRestAPI();
        $rest->setApiKey(snp_get_option('ml_freshmail_apikey'));
        $rest->setApiSecret(snp_get_option('ml_freshmail_apisecret'));
        
        $args = array();
        $args['email'] = snp_trim($params['data']['post']['email']);
        $args['list'] = $ml_freshmail_list;
        if (count($cf_data) > 0) {
	        $args['custom_fields'] = (array) $cf_data;
	    }
	    
	    $double_optin = snp_get_option('ml_freshmail_double_optin');
        if ($double_optin == 1) {
	        $args['state'] = 2;
	        $args['confirm'] = 1;
        } else {
	        $args['state'] = 1;
	    }
	    
	    try {
		    $response = $rest->doRequest('subscriber/add', $args);
		    
		    $result['status'] = true;
        } catch (Exception $e) {
	        $result['log']['errorMessage'] = $e->getMessage();
	    }
    }
	
	return $result;
}