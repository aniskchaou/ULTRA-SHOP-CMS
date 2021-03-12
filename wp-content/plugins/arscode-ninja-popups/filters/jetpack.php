<?php
	
add_filter('ninja_popups_subscribe_by_jetpack', 'ninja_popups_subscribe_by_jetpack', 10, 1);

function ninja_popups_subscribe_by_jetpack($params = array()) 
{
	$log_list_id = 'jetpack: no lists';
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $log_list_id,
			'errorMessage' => '',
		)
	);
	
	$subscribe = Jetpack_Subscriptions::subscribe(snp_trim($params['data']['post']['email']), 0, false);
	
	if (is_wp_error($subscribe)) {
		$result['log']['errorMessage'] = 'JetPack Problem: ' . $subscribe->get_error_code();
    } else {
	    $error = false;
	    foreach ($subscribe as $response) {
		    if (is_wp_error($response)) {
			    $error = $response->get_error_code();
			    break;
			}
		}
	}
	
	switch ($error) {
		case false:
			$result['status'] = true;
            break;
        case 'invalid_email':
        	$result['log']['errorMessage'] = 'JetPack Problem: ' . $error;
            break;
        case 'active':
        case 'blocked_email':
        	$result['log']['errorMessage'] = 'JetPack Problem: opted_out. JetPack log: ' . $error;
            break;
        case 'pending':
        	$result['log']['errorMessage'] = 'JetPack Problem: pending. JetPack log: ' . $error;
            break;
        default:
        	$result['log']['errorMessage'] = 'JetPack Problem: error. JetPack log: ' . $error;
            break;
    }
    
	return $result;
}