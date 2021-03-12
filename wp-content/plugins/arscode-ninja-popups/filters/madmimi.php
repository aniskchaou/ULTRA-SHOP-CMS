<?php
	
add_filter('ninja_popups_subscribe_by_madmimi', 'ninja_popups_subscribe_by_madmimi', 10, 1);

function ninja_popups_subscribe_by_madmimi($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/madmimi/MadMimi.class.php';
	
	if (
		snp_get_option('ml_madm_username') && 
		snp_get_option('ml_madm_apikey')
	) {
		$mailer = new MadMimi(snp_get_option('ml_madm_username'), snp_get_option('ml_madm_apikey'));
		
		$user = array('email' => snp_trim($params['data']['post']['email']));
		if (!empty($params['data']['names']['first'])) {
			$user['FirstName'] = $params['data']['names']['first'];
		}
		
		if (!empty($params['data']['names']['last'])) {
			$user['LastName'] = $params['data']['names']['last'];
		}
		
		if (count($params['data']['cf']) > 0) {
			$user = array_merge($user, (array) $params['data']['cf']);
		}
		
		$ml_madm_list = $params['popup_meta']['snp_ml_madm_list'][0];
		if (!$ml_madm_list) {
			$ml_madm_list = snp_get_option('ml_madm_list');
		}

        $result = array(
			'status' => false,
			'log' => array(
				'listId' => $ml_madm_list,
				'errorMessage' => '',
			)
		);
		
		$user['add_list'] = $ml_madm_list;
		
		$res = $mailer->AddUser($user);
		
		$result['status'] = true;
    }
            
    return $result;
}