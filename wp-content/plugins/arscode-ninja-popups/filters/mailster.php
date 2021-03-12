<?php
	
add_filter('ninja_popups_subscribe_by_mailster', 'ninja_popups_subscribe_by_mailster', 10, 1);

function ninja_popups_subscribe_by_mailster($params = array()) 
{
	$userdata = array(
		'firstname' => $params['data']['names']['first'],
		'lastname' => $params['data']['names']['last']
	);
	
	$ml_mailster_list = $params['popup_meta']['snp_ml_mm_list'][0];
	if (!$ml_mailster_list) {
		$ml_mailster_list = snp_get_option('ml_mailster_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_mailster_list,
			'errorMessage' => '',
		)
	);
	
	$lists = array($ml_mailster_list);
	
	if (function_exists('mailster')) {
		$entry = $userdata;
		$entry['email'] = snp_trim($params['data']['post']['email']);
		$double_optin = snp_get_option('ml_mailster_double_optin');
		if ($double_optin == 1) {
			$entry['status'] = 0;
		} else {
			$entry['status'] = 1;
		}
		
		if (count($params['data']['cf']) > 0) {
			foreach ($params['data']['cf'] as $k => $v) {
				$entry[$k] = $v;
			}
		}
		
		if ($data = mailster('subscribers')->get_by_mail($entry['email'])) {
			$subscriber_id = $data->ID;
		} else {
			$subscriber_id = mailster('subscribers')->add($entry, true);
		}
		
		if (!is_wp_error($subscriber_id)) {
			$success = mailster('subscribers')->assign_lists($subscriber_id, $lists, false);
		}
		
		if ($success) {
			$result['status'] = true;
		} else {
			$result['log']['errorMessage'] = 'Mailster Problem!';
		}
	} else {
		$return = mailster_subscribe(snp_trim($_POST['email']), $userdata, $lists);
		if (!is_wp_error($return)) {
			$result['status'] = true;
		} else {
			$result['log']['errorMessage'] = 'Mailster Problem!';
		}
	}
            
	return $result;
}