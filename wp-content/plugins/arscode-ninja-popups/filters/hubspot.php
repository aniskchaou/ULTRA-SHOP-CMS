<?php
	
add_filter('ninja_popups_subscribe_by_hubspot', 'ninja_popups_subscribe_by_hubspot', 10, 1);

function ninja_popups_subscribe_by_hubspot($params = array()) 
{
	if (!class_exists('HubSpot_Forms')) {
		require_once SNP_DIR_PATH . '/include/hubspot/class.forms.php';
	}
	
	$HAPIKey = snp_get_option('ml_hubspot_apikey');
	
	$portalId = snp_get_option('ml_hubspot_portal');
	
	$formId = $params['popup_meta']['snp_ml_hubspot_list'][0];
    if (!$formId) {
	    $formId = snp_get_option('ml_hubspot_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $formId,
			'errorMessage' => '',
		)
	);
	
	try {
		$forms = new HubSpot_Forms($HAPIKey);
		
		$postParams =  array(
			'email' => snp_trim($params['data']['post']['email']),
			'firstname' => $params['data']['names']['first'],
			'lastname' => $params['data']['names']['last']
        );
        
        if (count($params['data']['cf']) > 0) {
	        foreach ($params['data']['cf'] as $k => $v) {
		        $postParams[$k] = $v;
		    }
		}
		
		$result = $forms->submit_form($portalId, $formId, $postParams, array(
			'ipAddress' => $_SERVER['REMOTE_ADDR']
        ));
        
        $result['status'] = true;
    } catch (Exception $e) {
	    $result['log']['errorMessage'] = 'HubSpot Problem: ' . $e->getMessage();
    }
            
	return $result;
}