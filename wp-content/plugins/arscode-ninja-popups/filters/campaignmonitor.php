<?php
	
add_filter('ninja_popups_subscribe_by_campaignmonitor', 'ninja_popups_subscribe_by_campaignmonitor', 10, 1);

function ninja_popups_subscribe_by_campaignmonitor($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/campaignmonitor/csrest_subscribers.php';
    
    $ml_cm_list = $params['popup_meta']['snp_ml_cm_list'][0];
    if (!$ml_cm_list) {
    	$ml_cm_list = snp_get_option('ml_cm_list');
    }
            
    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_cm_list,
			'errorMessage' => '',
		)
	);

	$wrap = new CS_REST_Subscribers($ml_cm_list, snp_get_option('ml_cm_apikey'));
    
    $args = array(
    	'EmailAddress' => snp_trim($params['data']['post']['email']),
    	'Resubscribe' => true
    );

    if (!empty($params['data']['post']['name'])) {
    	$args['Name'] = $params['data']['post']['name'];
    }

    if (count($params['data']['cf']) > 0) {
    	$CustomFields = array();
    	foreach ($params['data']['cf'] as $k => $v) {
    		$CustomFields[] = array(
    			'Key' => $k,
    			'Value' => $v
    		);
    	}

    	$args['CustomFields'] = $CustomFields;
    }

    $res = $wrap->add($args);

    if ($res->was_successful()) {
    	$result['status'] = true;
    } else {
    	$result['log']['errorMessage'] = 'Failed with code ' . $res->http_status_code;
    }

	return $result;
}