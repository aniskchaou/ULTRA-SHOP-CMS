<?php
	
add_filter('ninja_popups_subscribe_by_salesmanago', 'ninja_popups_subscribe_by_salesmanago', 10, 1);

function ninja_popups_subscribe_by_salesmanago($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/salesmanago/salesmanago.php';
	
	$ml_salesmanago_tag = $params['popup_meta']['snp_ml_salesmanago_tag'][0];
    if (!$ml_salesmanago_tag) {
	    $ml_salesmanago_tag = snp_get_option('ml_salesmanago_tag');
	}
	
	$ml_salesmanago_useremail = $params['popup_meta']['snp_ml_salesmanago_useremail'][0];
	if (!$ml_salesmanago_useremail) {
		$ml_salesmanago_useremail = snp_get_option('ml_salesmanago_useremail');
	}

	$ml_salesmanago_opt_type = $params['popup_meta']['snp_ml_salesmanago_type'][0];
    if (!$ml_salesmanago_opt_type) {
	    $ml_salesmanago_opt_type = snp_get_option('ml_salesmanago_type');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_salesmanago_tag,
			'errorMessage' => '',
		)
	);
	
	if (
		snp_get_option('ml_salesmanago_apikey') &&
        snp_get_option('ml_salesmanago_apisecret') &&
        snp_get_option('ml_salesmanago_endpoint') &&
        snp_get_option('ml_salesmanago_clientid') && 
        $ml_salesmanago_useremail
    ) {
	    $rest = new snp_salesmanago(
		    snp_get_option('ml_salesmanago_endpoint'),
		    snp_get_option('ml_salesmanago_clientid'),
		    snp_get_option('ml_salesmanago_apisecret'),
		    snp_get_option('ml_salesmanago_apikey'),
		    $ml_salesmanago_useremail
        );
        
        if (isset($ml_salesmanago_tag)) {
	        $tags = explode(',', $ml_salesmanago_tag);
	        $n = count($tags);
	        for($i=0; $i<$n; $i++) {
		        $tags[$i] = trim($tags[$i]);
		    }
		}
		
		$args = array();
		$args['email'] = snp_trim($params['data']['post']['email']);
		if (!empty($params['data']['post']['name'])) {
			$args['name'] = $params['data']['post']['name'];
		}
		
		if (count($params['data']['cf']) > 0) {
			foreach ($params['data']['cf'] as $k => $v) {
				$args[$k] = $v;	
			}
		}
		
		try {
			$response = json_decode($rest->subscribe($args, $tags), true, $ml_salesmanago_opt_type);
			
			if (!empty($response) && is_array($response) && $response['success'] === true) {
				$result['status'] = true;
			}
        } catch (Exception $e) {
	        $result['log']['errorMessage'] = $e->getMessage();
        }
    }
            
	return $result;
}