<?php
	
add_filter('ninja_popups_subscribe_by_mailerlite', 'ninja_popups_subscribe_by_mailerlite', 10, 1);

function ninja_popups_subscribe_by_mailerlite($params = array()) 
{
	if (!class_exists('ML_Subscribers')) {
		require_once SNP_DIR_PATH . '/include/mailerlite/ML_Subscribers.php';
    }
    
    $ml_mailerlite_list = $params['popup_meta']['snp_ml_mailerlite_list'][0];
    if (!$ml_mailerlite_list) {
	    $ml_mailerlite_list = snp_get_option('ml_mailerlite_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_mailerlite_list,
			'errorMessage' => '',
		)
	);
	
	if (
		snp_get_option('ml_mailerlite_apikey') && 
		$ml_mailerlite_list
	) {
		$rest = new ML_Subscribers(snp_get_option('ml_mailerlite_apikey'));
		
		$args = array();
		$args['email'] = snp_trim($params['data']['post']['email']);
		$args['name'] = $params['data']['post']['name'];
		
		if (count($params['data']['cf']) > 0) {
			$args['fields'] = array();
            foreach ($params['data']['cf'] as $field => $value) {
	            array_push($args['fields'], array(
	            	'name' => $field,
					'value' => $value
				));
            }
        }
        
        try {
	        $response = $rest->setId($ml_mailerlite_list)->add( $args );
                    
            $result['status'] = true;
        } catch (Exception $e) {
	        $result['log']['errorMessage'] = $e->getMessage();
        }
    }
            
    return $result;
}