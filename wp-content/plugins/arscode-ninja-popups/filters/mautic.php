<?php
	
add_filter('ninja_popups_subscribe_by_mautic', 'ninja_popups_subscribe_by_mautic', 10, 1);

function ninja_popups_subscribe_by_mautic($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/mautic/QueryBuilder/QueryBuilder.php';
    
    require_once SNP_DIR_PATH . '/include/mautic/Auth/AuthInterface.php';    
    require_once SNP_DIR_PATH . '/include/mautic/Auth/ApiAuth.php';
    require_once SNP_DIR_PATH . '/include/mautic/Auth/AbstractAuth.php';
    require_once SNP_DIR_PATH . '/include/mautic/Auth/OAuth.php';
    
    require_once SNP_DIR_PATH . '/include/mautic/Api/Api.php';
    require_once SNP_DIR_PATH . '/include/mautic/Api/Contacts.php';
    require_once SNP_DIR_PATH . '/include/mautic/Api/Stages.php';
    require_once SNP_DIR_PATH . '/include/mautic/Api/Segments.php';
    require_once SNP_DIR_PATH . '/include/mautic/MauticApi.php';
    
    $uri = snp_get_option('ml_mautic_url');
    $public = snp_get_option('ml_mautic_key');
    $secret = snp_get_option('ml_mautic_secret');
    
    $ownerId = $params['popup_meta']['snp_ml_mautic_owner'][0];
    if (!$ownerId) {
	    $ownerId = snp_get_option('ml_mautic_owner');
	}
	
	$stageId = $params['popup_meta']['snp_ml_mautic_stage'][0];
	if (!$stageId) {
		$stageId = snp_get_option('ml_mautic_stage');
    }
    
    $segmentId = $params['popup_meta']['snp_ml_mautic_segment'][0];
    if (!$segmentId) {
	    $segmentId = snp_get_option('ml_mautic_segment');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ownerId . '||' . $stageId . '||' . $segmentId,
			'errorMessage' => '',
		)
	);
	
	$settings = array(
		'baseUrl'           => $uri,
		'clientKey'         => $public,
		'clientSecret'      => $secret,
		'callback'          => admin_url('edit.php?post_type=snp_popups&page=snp_opt'),
		'version'           => 'OAuth2'
    );
    
    if (($info = get_option('snp_ml_mautic_auth_info'))) {
	    $settings['accessToken']        = $info['accessToken'] ;
	    $settings['accessTokenSecret']  = $info['accessTokenSecret'];
	    $settings['accessTokenExpires'] = $info['accessTokenExpires'];
	}
	
	try {
		$auth = \Mautic\Auth\ApiAuth::initiate($settings);
		
		$contactApi = \Mautic\MauticApi::getContext(
			'contacts',
			$auth,
			$uri . '/api/'
		);
		
		$data = array(
			'firstname' => $params['data']['names']['first'],
			'lastname'  => $params['data']['names']['last'],
			'email'     => snp_trim($params['data']['post']['email']),
			'ipAddress' => $_SERVER['REMOTE_ADDR'],
			'owner'     => $ownerId
        );
        
        if (count($params['data']['cf']) > 0) {
	        foreach ($params['data']['cf'] as $k => $v) {
		        $data[$k] = $v;
		    }
		}
		
		$contact = $contactApi->create($data);
		
		if (isset($contact['contact'])) {
			if ($stageId) {
				$stageApi = \Mautic\MauticApi::getContext(
					'stages',
					$auth,
					$uri . '/api/'
                );
                
                $response = $stageApi->addContact($contact['contact']['id'], $stageId);
            }
            
            if ($segmentId) {
	            $segmentApi = \Mautic\MauticApi::getContext(
	            	'segments',
	            	$auth,	            	
	            	$uri . '/api/'
				);
				
				$response = $segmentApi->addContact($segmentId, $contact['contact']['id']);
            }
            
            $result['status'] = true;
        } else {
	        $result['log']['errorMessage'] = 'Mautic error message: ' . var_export($contact);
        }
    } catch (Exception $e) {
	    $result['log']['errorMessage'] = 'Mautic error message: ' . $e->getMessage();
    }
    
	return $result;
}