<?php
	
add_filter('ninja_popups_subscribe_by_sendy', 'ninja_popups_subscribe_by_sendy', 10, 1);

function ninja_popups_subscribe_by_sendy($params = array()) 
{
	$list_id = $params['popup_meta']['snp_ml_sendy_list'][0];
    if (!$list_id) {
	    $list_id = snp_get_option('ml_sendy_list');
    }
            
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $list_id,
			'errorMessage' => '',
		)
	);
	
	if ($list_id) {
		$options = array(
			'list' => $list_id,
			'api_key' => snp_get_option('ml_sendy_apikey'),
			'boolean' => 'true'
		);
	
		$args['email'] = snp_trim($params['data']['post']['email']);
		if (!empty($params['data']['post']['name'])) {
			$args['name'] = $params['data']['post']['name'];
		}
		
		if (count($params['data']['cf']) > 0) {
			$args = array_merge($args, (array) $params['data']['cf']);
		}
	
		$content = array_merge($args, $options);
		$postdata = http_build_query($content);
		
		$sendy_url = str_replace('/subscribe', '', snp_get_option('ml_sendy_url')) . '/subscribe';
		
		$ch = curl_init($sendy_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $api_result = curl_exec($ch);
        curl_close($ch);
        
        if (strval($api_result) == 'true' || strval($api_result) == '1' || strval($api_result) == 'Already subscribed.') {
	        $result['status'] = true;
	    } else {
		    $result['log']['errorMessage'] = $api_result;
        }
    }
    
    return $result;
}
