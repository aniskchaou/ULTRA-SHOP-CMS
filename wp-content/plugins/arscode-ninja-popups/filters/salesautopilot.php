<?php
	
add_filter('ninja_popups_subscribe_by_salesautopilot', 'ninja_popups_subscribe_by_salesautopilot', 10, 1);

function ninja_popups_subscribe_by_salesautopilot($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/salesautopilot/MailMaster.php';
	
	$list_id = $params['popup_meta']['snp_ml_salesautopilot_list'][0];
    if (!$list_id) {
	    $list_id = snp_get_option('ml_salesautopilot_list');
	}
	
	$form_id = $params['popup_meta']['snp_ml_salesautopilot_form'][0];
	if (!$form_id) {
		$form_id = snp_get_option('ml_salesautopilot_form');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $list_id,
			'errorMessage' => '',
		)
	);
	
	if (
		snp_get_option('ml_salesautopilot_apikey') && 
		snp_get_option('ml_salesautopilot_login')
	) {
		$rest = new MailMaster($list_id, $form_id, snp_get_option('ml_salesautopilot_login'), snp_get_option('ml_salesautopilot_apikey'));
		
		$args =  new stdClass();
		$args->email = snp_trim($params['data']['post']['email']);
		if (!empty($params['data']['post']['name'])) {
			$args->mssys_firstname = $names['first'];
			$args->mssys_lastname = $names['last'];
        }
        
        if (count($params['data']['cf']) > 0) {
	        foreach ($params['data']['cf'] as $k => $v) {
		        $args->$k = $v;
			}
       }
       
       try {
	       $response = $rest->subscribe($args);
	       $response = json_decode($response);
           if (isset($response) && ($response > 0 || $response == -1)) {
	           $result['status'] = true;
	       } else {
		       $api_error_msg = $response . ' ' . (isset($response) ? var_export($response, true) : '');
		       
		       $result['log']['errorMessage'] = $api_error_msg;
           }
        } catch (Exception $e) {
	        $api_error_msg = var_export($response, true);
	        
	        $result['log']['errorMessage'] = $api_error_msg;
        }
    }
    
    return $result;
}