<?php
	
add_filter('ninja_popups_subscribe_by_aweber', 'ninja_popups_subscribe_by_aweber', 10, 1);

function ninja_popups_subscribe_by_aweber($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/aweber/aweber_api.php';

	if (get_option('snp_ml_aw_auth_info')) {
		$aw = get_option('snp_ml_aw_auth_info');
		try {
			$aweber = new AWeberAPI($aw['consumer_key'], $aw['consumer_secret']);
			$account = $aweber->getAccount($aw['access_key'], $aw['access_secret']);
			$aw_list = $params['popup_meta']['snp_ml_aw_lists'][0];
			if (!$aw_list) {
				$aw_list = snp_get_option('ml_aw_lists');
			}

			$result = array(
				'status' => false,
				'log' => array(
					'listId' => $aw_list,
					'errorMessage' => '',
				)
			);

			$list = $account->loadFromUrl('/accounts/' . $account->id . '/lists/' . $aw_list);
			
			$subscriber = array(
				'email' => snp_trim($params['data']['post']['email']),
				'ip_address' => $_SERVER['REMOTE_ADDR']
	        );

	        if (!empty($params['data']['post']['name'])) {
	        	$subscriber['name'] = $params['data']['post']['name'];
	        }

	        if (count($params['data']['cf']) > 0) {
	        	$subscriber['custom_fields'] = $params['data']['cf'];
	        }

	        $tags = $params['popup_meta']['snp_ml_aw_tags'][0];
	        if (!$tags) {
	        	$tags = snp_get_option('ml_aw_tags');
	        }

	        if ($tags) {
	        	$toTag = array();
	        	foreach ($tags as $t) {
	        		$toTag[] = $t;
	        	}

	        	if (!empty($toTag)) {
	        		$subscriber['tags'] = $toTag;
	        	}
	        }

	        $r = $list->subscribers->create($subscriber);

	        $result['status'] = true;
	    } catch (AWeberException $e) {
	    	$result['log']['errorMessage'] = 'AWEBER Error: ' . $e->getMessage();
	    }
	}

	return $result;
}
