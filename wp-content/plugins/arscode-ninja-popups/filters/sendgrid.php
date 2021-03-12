<?php
	
add_filter('ninja_popups_subscribe_by_sendgrid', 'ninja_popups_subscribe_by_sendgrid', 10, 1);

function ninja_popups_subscribe_by_sendgrid($params = array()) 
{	
	require_once SNP_DIR_PATH . '/include/sendgrid/sendgrid_api.php';
	
	$ml_sendgrid_list = $params['popup_meta']['snp_ml_sendgrid_list'][0];
	if (!$ml_sendgrid_list){
		$ml_sendgrid_list = snp_get_option('ml_sendgrid_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_sendgrid_list,
			'errorMessage' => '',
		)
	);
	
	if (
		snp_get_option('ml_sendgrid_username') &&
		snp_get_option('ml_sendgrid_password') &&
		$ml_sendgrid_list
	) {
		$rest = new snp_sendgrid_class(snp_get_option('ml_sendgrid_username'), snp_get_option('ml_sendgrid_password'));
		
		$args = array();
		$args['email'] = snp_trim($params['data']['post']['email']);
		if (!empty($params['data']['post']['name'])) {
			$args['first_name'] = $params['data']['names']['first'];
			$args['last_name'] = $params['data']['names']['last'];
		}
		
		if (count($params['data']['cf']) > 0) {
			foreach($params['data']['cf'] as $k => $v) {
				$args[$k] = $v;
			}
		}
		
		try {
			$result = $rest->addSubscriber($ml_sendgrid_list, $args);
			
			if (isset($result) && $result) {
				$result['status'] = true;
			} else {
                $result['log']['errorMessage'] = var_export($result, true);
			}
		} catch (Exception $e) {
            $result['log']['errorMessage'] = $e->getMessage();
		}
    }
            
	return $result;
}