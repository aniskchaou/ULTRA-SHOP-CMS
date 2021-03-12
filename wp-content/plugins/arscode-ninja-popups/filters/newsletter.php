<?php
	
add_filter('ninja_popups_subscribe_by_newsletter', 'ninja_popups_subscribe_by_newsletter', 10, 1);

function ninja_popups_subscribe_by_newsletter($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/newsletter/newsletter.php';

	$api = new RelioNewsletterApi();

	$ml_newsletter_listid = $params['popup_meta']['snp_ml_newsletter_listid'][0];
	if (!$ml_newsletter_listid) {
		$ml_newsletter_listid = snp_get_option('ml_newsletter_listid');
	}

	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_newsletter_listid,
			'errorMessage' => '',
		)
	);

	$ml_newsletter_listid = explode(',', $ml_newsletter_listid);

	$res = $api->subscribe(snp_trim($params['data']['post']['email']), $params['data']['names']['first'], $params['data']['names']['last'], $ml_newsletter_listid, $params['data']['cf']);
    
    if (is_object($res)) {
    	$result['status'] = true;
    } else {
    	$result['log']['errorMessage'] = 'Newsletter Problem!';
    }

	return $result;
}