<?php
	
add_filter('ninja_popups_subscribe_by_ontraport', 'ninja_popups_subscribe_by_ontraport', 10, 1);

function ninja_popups_subscribe_by_ontraport($params = array()) 
{
	require_once SNP_DIR_PATH . '/include/ontraport/snp_ontraport.php';
    
    $ml_ontraport_list = $params['popup_meta']['snp_ml_ontraport_list'][0];
    if (!$ml_ontraport_list) {
    	$ml_ontraport_list = snp_get_option('ml_ontraport_list');
    }

    $result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_ontraport_list,
			'errorMessage' => '',
		)
	);

	if (
		snp_get_option('ml_ontraport_apiid') &&
		snp_get_option('ml_ontraport_apikey') &&
		$ml_ontraport_list
	) {
		$rest = new snp_ontraport(snp_get_option('ml_ontraport_apiid'), snp_get_option('ml_ontraport_apikey'));               

		$args = '<contact>
			<Group_Tag name="Contact Information">
				<field name="Email">'. snp_trim($params['data']['post']['email']) .'</field>';

				if (!empty($params['data']['post']['name'])) {
                    $args .=  '<field name="First Name">'. $params['data']['names']['first'] .'</field>
                    		   <field name="Last Name">'. $params['data']['names']['last'] .'</field>';
                }	          
                
                if (count($params['data']['cf']) > 0) {
                    foreach($params['data']['cf'] as $k => $v) {
						$args .= '<field name="'. $k .'">'. $v .'</field>';
					}
                }

        $args .= '</Group_Tag>';
        $args .= '<Group_Tag name="Sequences and Tags">
        		<field name="Contact Tags">'. $ml_ontraport_list .'</field>
        	</Group_Tag>
        </contact>';   

        try {
        	$response = $rest->subscribe($args);

        	if (isset($response) && $response == 'Success') {
        		$result['status'] = true;
			}
		} catch (Exception $e) {
        	$result['log']['errorMessage'] = $e->getMessage();
        }
    }

	return $result;
}