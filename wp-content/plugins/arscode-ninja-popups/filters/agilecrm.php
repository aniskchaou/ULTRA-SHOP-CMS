<?php
	
add_filter('ninja_popups_subscribe_by_agilecrm', 'ninja_popups_subscribe_by_agilecrm', 10, 1);

function ninja_popups_subscribe_by_agilecrm($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/agilecrm/agilecrm_api.php';

	$ml_agilecrm_tag = $params['popup_meta']['snp_ml_agilecrm_tag'][0];
	if (!$ml_agilecrm_tag) {
		$ml_agilecrm_tag = snp_get_option('ml_agilecrm_tag');
	}

	$ml_agilecrm_useremail = $params['popup_meta']['snp_ml_agilecrm_useremail'][0];
	if (!$ml_agilecrm_useremail) {
		$ml_agilecrm_useremail = snp_get_option('ml_agilecrm_useremail');
	}
            
    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_agilecrm_tag,
			'errorMessage' => '',
		)
	);

	if (
		snp_get_option('ml_agilecrm_apikey') &&
		snp_get_option('ml_agilecrm_userdomain') &&
		$ml_agilecrm_useremail
	) {
		$rest = new snp_agilecrm_class(snp_get_option('ml_agilecrm_apikey'), snp_get_option('ml_agilecrm_userdomain'), $ml_agilecrm_useremail);

		$args = array();
		$args['properties'] = array();
		if (isset($ml_agilecrm_tag)) {
			$tags = explode(',', $ml_agilecrm_tag);
			$n = count($tags);
			for($i=0; $i<$n; $i++) {
				$tags[$i] = trim($tags[$i]);
			}
			$args['tags'] = $tags;
		}

		$args['properties'][] = array(
			"name" => "email",
			"value" => snp_trim($params['data']['post']['email']),
			"type" => "SYSTEM"
		);

		if (!empty($params['data']['post']['name'])) {
			$args['properties'][] = array(
				"name"=>"first_name",
				"value"=> $params['data']['names']['first'],
				"type"=>"SYSTEM"
            );

            $args['properties'][] = array(
            	"name" => "last_name",
				"value" => $params['data']['names']['last'],
				"type" => "SYSTEM"
            );
        }

        if (count($params['data']['cf']) > 0) {
        	foreach($params['data']['cf'] as $k => $v) {
            	if (
            		$k == 'phone' ||
                    $k == 'address' ||
                    $k == 'website' ||
                    $k == 'title' ||
                    $k == 'company'
                ) {
            		$args['properties'][] = array(
            			"name"=>$k,
            			"value"=> $v,
            			"type"=>"SYSTEM"
	                );
				} else {
					$args['properties'][] = array(
						"name"=>$k,
						"value"=> $v,
						"type"=>"CUSTOM"
					);	
				}
			}
		}

		try {
			$args = json_encode($args);
			$response = $rest->curl_wrap("contacts", $args, "POST");
			$decoded = json_decode($response);
			if (!empty($response) && ($response == 'Sorry, duplicate contact found with the same email address.' || $decoded -> type == 'PERSON' )) {
				$result['status'] = true;
			}
		} catch (Exception $e) {
			$result['log']['errorMessage'] = $e->getMessage();
		}
	}

	return $result;
}