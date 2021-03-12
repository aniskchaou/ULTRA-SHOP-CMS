<?php
	
add_filter('ninja_popups_subscribe_by_mymail', 'ninja_popups_subscribe_by_mymail', 10, 1);

function ninja_popups_subscribe_by_mymail($params = array()) 
{
	$userdata = array(
		'firstname' => $params['data']['names']['first'],
        'lastname' => $params['data']['names']['last']
    );
    
    $ml_mm_list = $params['popup_meta']['snp_ml_mm_list'][0];
    if (!$ml_mm_list) {
	    $ml_mm_list = snp_get_option('ml_mm_list');
	}

    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_mm_list,
			'errorMessage' => '',
		)
	);
	
	$lists = array($ml_mm_list);
	if (function_exists('mymail')) {
		$entry = $userdata;
		$entry['email'] = snp_trim($params['data']['post']['email']);
        
        $double_optin = snp_get_option('ml_mm_double_optin');
        
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
        
        $subscriber_id = mymail('subscribers')->add($entry, true);
        if (!is_wp_error($subscriber_id)) {
	        $success = mymail('subscribers')->assign_lists($subscriber_id, $lists, false);
	    }
	    
	    if ($success) {
		    $result['status'] = true;
		} else {
			$result['log']['errorMessage'] = 'MyMail Problem!';
        }
    } else {
	    $return = mymail_subscribe(snp_trim($params['data']['post']['email']), $userdata, $lists);
	    if (!is_wp_error($return)) {
		    $result['status'] = true;
		} else {
			$result['log']['errorMessage'] = 'MyMail Problem!';
		}
	}
            
	return $result;	
}