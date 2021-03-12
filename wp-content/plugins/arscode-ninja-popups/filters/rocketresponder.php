<?php
	
add_filter('ninja_popups_subscribe_by_rocketresponder', 'ninja_popups_subscribe_by_rocketresponder', 10, 1);

function ninja_popups_subscribe_by_rocketresponder($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/rocketresponder/RocketResponder.class.php';
	
	$ml_rocketresponder_list = $params['popup_meta']['snp_ml_rocketresponder_list'][0];
	if (!$ml_rocketresponder_list) {
		$ml_rocketresponder_list=snp_get_option('ml_rocketresponder_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_rocketresponder_list,
			'errorMessage' => '',
		)
	);
    
    if (
    	snp_get_option('ml_rocketresponder_apipublic') && 
    	snp_get_option('ml_rocketresponder_apiprivate') && 
    	$ml_rocketresponder_list
    ) {
	    $api = new RocketResponder(snp_get_option('ml_rocketresponder_apipublic'), snp_get_option('ml_rocketresponder_apiprivate'), 1);
        
        $email = snp_trim($params['data']['post']['email']);
        $LID = $ml_rocketresponder_list;
        
        if (isset($params['data']['post']['name'])) {
	        $XTRA['name'] = $params['data']['post']['name'];
        } else {
	        $XTRA = NULL;
	    }
	    
	    try {
		    $response = $api->subscribe($email, $LID, $XTRA);
		    
		    $result['status'] = true;
        } catch (Exception $e) {
	        $result['log']['errorMessage'] = $e->getMessage();
        }
    }
            
	return $result;
}