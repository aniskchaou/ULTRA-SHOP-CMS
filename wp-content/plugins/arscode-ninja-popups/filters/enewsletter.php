<?php
	
add_filter('ninja_popups_subscribe_by_enewsletter', 'ninja_popups_subscribe_by_enewsletter', 10, 1);

function ninja_popups_subscribe_by_enewsletter($params = array()) 
{
	if (class_exists('Email_Newsletter')) {
		$groupId = $params['popup_meta']['snp_ml_enewsletter_list'][0];
		if (!$groupId) {
			$groupId = snp_get_option('snp_ml_enewsletter_list');
		}

        $result = array(
			'status' => false,
			'log' => array(
				'listId' => $groupId,
				'errorMessage' => '',
			)
		);
		
		$newsletter = new Email_Newsletter();
		
		$settings = $newsletter->get_settings();
		
		//Sets up groups to subscribe to on the beginning
        $subscribe_groups = (isset($settings['subscribe_groups'])) ? explode(',', $settings['subscribe_groups']) : array();
        $subscribe_groups = array_merge(array($groupId), $subscribe_groups);
        $subscribe_groups = array_unique($subscribe_groups);
        
        //set up if double opt in is on
        $double_opt_in = (isset($newsletter->settings['double_opt_in']) && $newsletter->settings['double_opt_in']) ? 1 : 0;
        
        $member_data['email']       =  snp_trim($params['data']['post']['email']);
        $member_data['fname']       =  $params['data']['names']['first'];
        $member_data['lname']       =  $params['data']['names']['last'];
        $member_data['groups_id']   =  $subscribe_groups;
        
        //first lets check if email exists somewhere
        $currentMemberId = '';
        if (email_exists($member_data['email']) !== false) {
	        $wp_user_id = email_exists($member_data['email']);
	        $member_id = $newsletter->get_members_by_wp_user_id($wp_user_id);
	        
	        if (0 < $member_id) {
		        $currentMemberId = $member_id;
		    }
        } else {
	        $member =  $newsletter->get_member_by_email($member_data['email']);
	        
	        if (isset($member['member_id'])) {
		        $currentMemberId = $member['member_id'];
		    }
        }
        
        if ($currentMemberId) {
	        $newsletter->add_members_to_groups($currentMemberId, $subscribe_groups);
	        
	        $result['status'] = true;
        } else {
	        $result = $newsletter->add_member($member_data, $double_opt_in);
	        
	        if (!$result['error']) {
		       $result['status'] = true;
            } else {
	            $result['log']['errorMessage'] = 'WPMU DEV E-newsletter error message: ' . $result['message'];
            }
        }
    } else {
	    $result['log']['errorMessage'] = 'WPMU DEV E-newsletter plugin not found';
    }
            
	return $result;	
}