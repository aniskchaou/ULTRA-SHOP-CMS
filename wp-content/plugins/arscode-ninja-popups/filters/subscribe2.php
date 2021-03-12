<?php
	
add_filter('ninja_popups_subscribe_by_subscribe2', 'ninja_popups_subscribe_by_subscribe2', 10, 1);

function ninja_popups_subscribe_by_subscribe2($params = array()) 
{	
	global $wpdb;
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => 'Subscribe2 - no lists',
			'errorMessage' => '',
		)
	);
	
	$s2_email = snp_trim($params['data']['post']['email']);
	$s2_confirm = (snp_get_option('ml_subscribe2_double_optin') == 1)
		? false 
		: true
	;
	
	$s2 = new s2class();
	$s2->public = $wpdb->prefix.'subscribe2';
	$s2->add($s2_email, $s2_confirm);

    $result['status'] = true;
            
	return $result;	
}