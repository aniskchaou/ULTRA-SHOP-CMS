<?php
	
add_filter('ninja_popups_subscribe_by_directmail', 'ninja_popups_subscribe_by_directmail', 10, 1);

function ninja_popups_subscribe_by_directmail($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/directmail/class.directmail.php';
	
	$form_id = snp_get_option('ml_dm_form_id');
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $form_id,
			'errorMessage' => '',
		)
	);
	
	if ($form_id) {
	    $args = [];
        if (!empty($params['data']['names']['first'])) {
            $args['first_name'] = $params['data']['names']['first'];
        }

        if (!empty($params['data']['names']['last'])) {
            $args['last_name'] = $params['data']['names']['last'];
        }

        if (count($params['data']['cf']) > 0) {
            foreach ($params['data']['cf'] as $k => $v) {
                $args[$k] = $v;
            }
        }

		$api = new DMSubscribe();
		$retval = $api->submitSubscribeForm($form_id, snp_trim($params['data']['post']['email']), $args, $error_message);
		
		if ($retval) {
			$result['status'] = true;
		} else {
			$result['log']['errorMessage'] = $error_message;
        }
    }
    
    return $result;
}