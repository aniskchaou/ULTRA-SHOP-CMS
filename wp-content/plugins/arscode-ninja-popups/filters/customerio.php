<?php
	
add_filter('ninja_popups_subscribe_by_customerio', 'ninja_popups_subscribe_by_customerio', 10, 1);

function ninja_popups_subscribe_by_customerio($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/customerio/snp_customerio.php';
            
    $log_list_id = 'customer.io - no lists';
            
    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $log_list_id,
			'errorMessage' => '',
		)
	);
	
	if (
		snp_get_option('ml_customerio_sitekey') &&
		snp_get_option('ml_customerio_apikey')
    ) {
	    $rest = new snp_customerio(snp_get_option('ml_customerio_apikey'), snp_get_option('ml_customerio_sitekey'));
	    
	    $args = array();
	    $args['email'] = snp_trim($params['data']['post']['email']);
	    if (!empty($params['data']['post']['name'])) {
		    $args['name'] = $params['data']['post']['name'];
		}
		
		if (count($params['data']['cf']) > 0) {
			foreach($params['data']['cf'] as $k => $v) {
				$args[$k] = $v;
            }
        }
        
        $response = $rest->subscribe($args);
        if (strlen($response) < 5 ){
	        $result['status'] = true;	        
        }
    }
            
    return $result;
}