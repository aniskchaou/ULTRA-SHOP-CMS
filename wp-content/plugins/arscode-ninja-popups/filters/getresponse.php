<?php
	
add_filter('ninja_popups_subscribe_by_getresponse', 'ninja_popups_subscribe_by_getresponse', 10, 1);

function ninja_popups_subscribe_by_getresponse($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/getresponse/jsonRPCClient.php';
	
	$ml_gr_apikey = snp_get_option('ml_gr_apikey');
	
	$api = new jsonRPCClient('http://api2.getresponse.com');
	try {
		$ml_gr_list = $params['popup_meta']['snp_ml_gr_list'][0];
		if (!$ml_gr_list) {
			$ml_gr_list = snp_get_option('ml_gr_list');
		}
		
        $result = array(
			'status' => false,
			'log' => array(
				'listId' => $ml_gr_list,
				'errorMessage' => '',
			)
		);
		
		$args = array(
			'campaign' => $ml_gr_list,
			'email' => snp_trim($params['data']['post']['email']),
			'cycle_day' => '0',
        );
        
        if (!empty($params['data']['post']['name'])) {
	        $args['name'] = $params['data']['post']['name'];
	    }
	    
	    if (count($params['data']['cf']) > 0) {
		    $CustomFields = array();
		    foreach ($params['data']['cf'] as $k => $v) {
			    $CustomFields[] = array(
			    	'name' => $k,
					'content' => $v
				);
			}
			
			$args['customs'] = $CustomFields;
        }
        
        $res = $api->add_contact($ml_gr_apikey, $args);
        
        $result['status'] = true;               
    } catch (Exception $e) {
	    $result['log']['errorMessage'] = $e->getMessage();
    }
	
	return $result;
}