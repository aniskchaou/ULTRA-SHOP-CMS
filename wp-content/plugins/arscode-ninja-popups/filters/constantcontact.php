<?php
	
add_filter('ninja_popups_subscribe_by_constantcontact', 'ninja_popups_subscribe_by_constantcontact', 10, 1);

function ninja_popups_subscribe_by_constantcontact($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/constantcontact/class.cc.php';
    
    $cc = new constantcontact(snp_get_option('ml_cc_username'), snp_get_option('ml_cc_pass'));
    $send_welcome = snp_get_option('ml_cc_send_welcome');

    if ($send_welcome == 1) {
    	$cc->set_action_type('contact');
    }

    $email = snp_trim($params['data']['post']['email']);

    $contact_list = $params['popup_meta']['snp_ml_cc_list'][0];
    if (!$contact_list) {
    	$contact_list = snp_get_option('ml_cc_list');
    }

    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $contact_list,
			'errorMessage' => '',
		)
	);

	$extra_fields = array();
	if (!empty($params['data']['names']['first'])) {
		$extra_fields['FirstName'] = $params['data']['names']['first'];
	}

	if (!empty($params['data']['names']['last'])) {
		$extra_fields['LastName'] = $params['data']['names']['last'];
	}

	if (count($params['data']['cf']) > 0) {
		$extra_fields = array_merge($extra_fields, (array) $params['data']['cf']);
	}

	$contact = $cc->query_contacts($email);
	if ($contact) {
	    $currentContact = $cc->get_contact($contact['id']);
	    if (is_array($currentContact['lists'])) {
	        $contact_list = array_merge([$contact_list], array_keys($currentContact['lists']));
        }
		$status = $cc->update_contact($contact['id'], $email, $contact_list, $extra_fields);
        
        if ($status) {
        	$result['status'] = true;
        } else {
        	$result['log']['errorMessage'] = "Contact Operation failed: " . $cc->http_get_response_code_error($cc->http_response_code);
        }
    } else {
    	$new_id = $cc->create_contact($email, $contact_list, $extra_fields);

    	if ($new_id) {
    		$result['status'] = true;
    	} else {
    		$result['log']['errorMessage'] = "Contact Operation failed: " . $cc->http_get_response_code_error($cc->http_response_code);
    	}
    }

	return $result;
}