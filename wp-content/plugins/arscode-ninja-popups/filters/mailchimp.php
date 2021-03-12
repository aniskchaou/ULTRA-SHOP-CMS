<?php
	
add_filter('ninja_popups_subscribe_by_mailchimp', 'ninja_popups_subscribe_by_mailchimp', 10, 1);

function ninja_popups_subscribe_by_mailchimp($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/mailchimp/MC_Lists.php';
	
	$ml_mc_list = '';
	if (isset($params['popup_meta']['snp_ml_mc_list'][0])) {
		$ml_mc_list = $params['popup_meta']['snp_ml_mc_list'][0];
	}
	
	if (!$ml_mc_list) {
		$ml_mc_list = snp_get_option('ml_mc_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_mc_list,
			'errorMessage' => '',
		)
	);
	
	if (
		snp_get_option('ml_mc_apikey') && 
		$ml_mc_list
	) {
		$rest = new MC_Lists(snp_get_option('ml_mc_apikey'));
		
		$args = array();
		$args['email_address'] = snp_trim($params['data']['post']['email']);
		
		$merge_fields = array();
		if (!empty($params['data']['post']['name'])) {
			$merge_fields = array(
				'FNAME' => $params['data']['names']['first'],
				'LNAME' => $params['data']['names']['last']
            );
        }
        
        if (count($params['data']['cf']) > 0) {
	        $merge_fields = array_merge($merge_fields, (array) $params['data']['cf']);
	    }
	    
	    if (is_array($merge_fields) && count($merge_fields)>0) {
		    if (in_array('mcgroups', array_keys($merge_fields))) {
			    $args['interests'] = array();
			    
			    foreach (snp_array_values_recursive($merge_fields['mcgroups']) as $v) {
				    $args['interests'][$v] = true;
				}
				
				unset($merge_fields['mcgroups']);
			}
			
			$args['merge_fields'] = $merge_fields;
		}
		
		try {
			$double_optin = snp_get_option('ml_mc_double_optin');
			
			if ($double_optin == 1) {
				$args['status'] = 'pending';
				$args['status_if_new'] = 'pending';
            } else {
	            $args['status'] = 'subscribed';
	            $args['status_if_new'] = 'subscribed';
	        }

			$tags = $params['popup_meta']['snp_ml_mc_tags'][0];
			if ($tags) {
				$tags = unserialize($tags);
			} else {
                $tags = snp_get_option('ml_mc_tags');
            }

            if ($tags) {
                foreach ($tags as $t) {
                    if (!empty($t)) {
                        $args['tags'][] = $t;
                    }
                }
            }

            $searchResponse = $rest->searchMember($ml_mc_list, $args['email_address']);
            $searchResponse = json_decode($searchResponse);
            if (
                $searchResponse->exact_matches->total_items > 0 ||
                $searchResponse->full_search->total_items > 0
            ) {
                $rest->addMember($ml_mc_list, $args);

				$result['status'] = true;
                $result['log']['errorMessage'] = 'E-mail address that was entered, already exists';
            } else {
                $retval = $rest->addMember($ml_mc_list, $args);
                $retval = json_decode($retval);

                if (isset($retval->id) || (isset($retval->title) && $retval->title == 'Member Exists')) {
                    $result['status'] = true;
                } else {
                    $result['log']['errorMessage'] = $retval->detail . ' ' . (isset($retval->errors) ? var_export($retval->errors, true) : '');
                }
            }
		} catch (Exception $e) {
			$result['log']['errorMessage'] = var_export($response->errors, true);
		}
	}
            
	return $result;
}
