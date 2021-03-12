<?php
	
add_filter('ninja_popups_subscribe_by_mailpoet', 'ninja_popups_subscribe_by_mailpoet', 10, 1);

function ninja_popups_subscribe_by_mailpoet($params = array()) 
{
	try {
		$listId = $params['popup_meta']['snp_ml_mailpoet_list'][0];
		if (!$listId) {
			$listId = snp_get_option('ml_mailpoet_list');
		}
                
        $lists = array($listId);
        
        $result = array(
			'status' => false,
			'log' => array(
				'listId' => $listId,
				'errorMessage' => '',
			)
		);

	    $subscriber_data = array(
	    	'email' => snp_trim($params['data']['post']['email']),
	        'first_name' => $params['data']['names']['first'],
	        'last_name' => $params['data']['names']['last'],
	    );
	    
	    $subscriber = \MailPoet\API\API::MP('v1')->addSubscriber($subscriber_data, $lists, array(
	    	'send_confirmation_email' => true,
	    	'schedule_welcome_email' => true
	    ));
    
		$result['status'] = true;
	} catch (Exception $e) {
		$result['log']['errorMessage'] = 'MailPoet error message: ' . $e->getMessage();
    }
    
    return $result;
}