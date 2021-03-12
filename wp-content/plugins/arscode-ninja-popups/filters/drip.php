<?php
	
add_filter('ninja_popups_subscribe_by_drip', 'ninja_popups_subscribe_by_drip', 10, 1);

function ninja_popups_subscribe_by_drip($params = array()) 
{
	if (!class_exists('Drip')) {
		require_once SNP_DIR_PATH . '/include/drip/drip.php';
    }
    
    $account = $params['popup_meta']['snp_ml_drip_account'][0];
    if (!$account) {
	    $account = snp_get_option('ml_drip_account');
    }

    $token = $params['popup_meta']['snp_ml_drip_token'][0];
    if (!$token) {
	    $token = snp_get_option('ml_drip_token');
	}
	
	$campaign = $params['popup_meta']['snp_ml_drip_campaigns'][0];
    if (!$campaign) {
	    $campaign = snp_get_option('ml_drip_campaigns');
    }
    
    $doubleOptin = $params['popup_meta']['snp_ml_drip_double_optin'][0];
    if (!$doubleOptin) {
	    $doubleOptin = snp_get_option('ml_drip_double_optin');
    }
    
    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $campaign,
			'errorMessage' => '',
		)
	);
	
	try {
		$data = array(
			'account_id'   => $account,
			'campaign_id'  => $campaign,
			'double_optin' => (bool)$doubleOptin,
			'first_name'   => $params['data']['names']['first'],
			'last_name'    => $params['data']['names']['last'],
			'email'        => snp_trim($params['data']['post']['email']),
	    );
	    
	    if (count($params['data']['cf']) > 0) {
		    foreach ($params['data']['cf'] as $k => $v) {
			    $data[$k] = $v;
			}
		}
		
		$dripResult = array();
		foreach ($data as $key => $value) {
			if (
				!in_array($key, array(
					'account_id',
					'campaign_id',
					'double_optin'
	            ))
	        ) {
		        $dripResult[$key] = $value;
	        }
	    }
	    
	    $result['drip'] = $dripResult;
	    
	    $api = new Drip($token);
	    $response = $api->subscribe_subscriber($data);
	    
	    $result['status'] = true;
    } catch (Exception $e) {
	    $result['log']['errorMessage'] = 'Drip error message: ' . $e->getMessage();
    }
            
	return $result;
}