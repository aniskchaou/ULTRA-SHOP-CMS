<?php
	
add_filter('ninja_popups_subscribe_by_icontact', 'ninja_popups_subscribe_by_icontact', 10, 1);

function ninja_popups_subscribe_by_icontact($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/icontact/iContactApi.php';

	iContactApi::getInstance()->setConfig(array(
		'appId' => snp_get_option('ml_ic_addid'),
		'apiPassword' => snp_get_option('ml_ic_apppass'),
		'apiUsername' => snp_get_option('ml_ic_username')
	));

	if (snp_get_option('ml_ic_double_optin') == 1) {
		$double_optin = 'pending';
	} else {
		$double_optin = 'normal';
	}

	$oiContact = iContactApi::getInstance();
	$res1 = $oiContact->addContact(
		snp_trim($params['data']['post']['email']),
		$double_optin,
		null,
		(isset($params['data']['names']['first']) ? $params['data']['names']['first'] : ''),
		(isset($params['data']['names']['last']) ? $params['data']['names']['last'] : ''),
		null,
		null,
		null,
		null,
		null,
		null,
        (isset($params['data']['post']['phone']) ? $params['data']['post']['phone'] : ''),
		null,
		null
    );

    if ($res1->contactId) {
    	$ml_ic_list = $params['popup_meta']['snp_ml_ic_list'][0];
    	if (!$ml_ic_list) {
    		$ml_ic_list = snp_get_option('ml_ic_list');
    	}

    	$result = array(
			'status' => false,
			'log' => array(
				'listId' => $ml_ic_list,
				'errorMessage' => '',
			)
		);

		if ($oiContact->subscribeContactToList($res1->contactId, $ml_ic_list, $double_optin)) {
			$result['status'] = true;
		}
	} else {
		$result['log']['errorMessage'] = 'iContact Problem!';
	}

	return $result;
}