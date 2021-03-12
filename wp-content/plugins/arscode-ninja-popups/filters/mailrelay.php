<?php
	
add_filter('ninja_popups_subscribe_by_mailrelay', 'ninja_popups_subscribe_by_mailrelay', 10, 1);

function ninja_popups_subscribe_by_mailrelay($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/mailrelay/snp_mailrelay.php';

	$ml_mailrelay_list = $params['popup_meta']['snp_ml_mailrelay_list'][0];
	if (!$ml_mailrelay_list) {
		$ml_mailrelay_list = snp_get_option('ml_mailrelay_list');
	}

    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_mailrelay_list,
			'errorMessage' => '',
		)
	);

	if (
		snp_get_option('ml_mailrelay_apikey') &&
		snp_get_option('ml_mailrelay_address') &&
		$ml_mailrelay_list
	) {
		$rest = new snp_mailrelay(snp_get_option('ml_mailrelay_apikey'), snp_get_option('ml_mailrelay_address'));

		$args = array();
		$args['email'] = snp_trim($params['data']['post']['email']);
		$args['groups'] = array($ml_mailrelay_list);
		$args['name'] = $params['data']['post']['name'];

		if (count($params['data']['cf']) > 0) {
			$args['customFields'] = array();
			foreach($params['data']['cf'] as $k => $v) {
				$args['customFields'][$k] = $v;
			}
		}

		try {
			$response = $rest->subscribe($args);
			if (isset($response) && ($response->status == 1 || $response->error == 'El email ya existe')) {
				$result['status'] = true;
			}
		} catch (Exception $e) {
			$result['log']['errorMessage'] = $e->getMessage();
		}
	}

	return $result;
}