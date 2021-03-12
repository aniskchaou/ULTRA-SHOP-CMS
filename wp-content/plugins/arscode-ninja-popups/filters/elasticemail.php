<?php
	
add_filter('ninja_popups_subscribe_by_elasticemail', 'ninja_popups_subscribe_by_elasticemail', 10, 1);

function ninja_popups_subscribe_by_elasticemail($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/elasticemail/ElasticEmail.php';
	
	$ml_elasticemail_list = $params['popup_meta']['snp_ml_elasticemail_list'][0];
	if (!$ml_elasticemail_list) {
		$ml_elasticemail_list = snp_get_option('ml_elasticemail_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_elasticemail_list,
			'errorMessage' => '',
		)
	);
	
	if (
		snp_get_option('ml_elasticemail_apikey') &&
		$ml_elasticemail_list
	) {
		$rest = new ElasticEmail(snp_get_option('ml_elasticemail_apikey'));
		
		$args = array();
		$args['email'] = snp_trim($params['data']['post']['email']);
		$args['listname'] = $ml_elasticemail_list;
		if (!empty($params['data']['post']['name'])) {
			$args['firstname'] = $params['data']['names']['first'];
			$args['lastname'] =  $params['data']['names']['last'];
		}
		
		if (count($params['data']['cf']) > 0) {
			foreach($params['data']['cf'] as $k => $v) {
				$args[$k] = $v;
			}
		}
		
		try {
			$response = $rest->subscribe($args);
			if (
				isset($response) &&
				($response == 'Contact created.' || strpos($response, 'Error: Contact is already on the list') !== false)
			) {
            	$result['status'] = true;
            }
        } catch (Exception $e) {
	        $result['log']['errorMessage'] = $e->getMessage();
        }
    }
            
	return $result;
}