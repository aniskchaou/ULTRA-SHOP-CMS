<?php
	
add_filter('ninja_popups_subscribe_by_apsis', 'ninja_popups_subscribe_by_apsis', 10, 1);

function ninja_popups_subscribe_by_apsis($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/httpful.phar';
	
	$key = snp_get_option('ml_apsis_key');
	
	if (
		snp_get_option('ml_manager') != 'apsis' || 
		!$key
	) {
		return;
	}
	
	$listId = $params['popup_meta']['snp_ml_apsis_list'][0];
    if (!$listId) {
	    $listId = snp_get_option('ml_apsis_list');
    }

    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $listId,
			'errorMessage' => '',
		)
	);

    $body = array(
    	'Email' => snp_trim($params['data']['post']['email']),
    	'Name' => $params['data']['names']['first'] . ' ' . $params['data']['names']['last'],
    	'DemDataFields' => array()
    );
    
    if (count($params['data']['cf']) > 0) {
	    foreach ($params['data']['cf'] as $k => $v) {
		    $body['DemDataFields'][] = array(
			    'Key' => $k,
			    'Value' => $v
			);
        }
    }
    
    try {
	    $response = \Httpful\Request::post('http://se.api.anpdm.com/v1/subscribers/mailinglist/' . $listId . '/create?updateIfExists=false')
        	->expectsJson()
            ->sendsJson()
            ->authenticateWith($key, '')
            ->body(json_encode($body))
            ->send();

        if ($response->body->Message == 'Subscriber successfully created and/or added to list') {
	        $result['status'] = true;
        } else {
	        $result['log']['errorMessage'] = 'APSIS error message: ' . print_r($response, true);
        }
    } catch (Exception $e) {
	    $result['log']['errorMessage'] = 'APSIS error message: ' . $e->getMessage();
    }
            
    return $result;    
}