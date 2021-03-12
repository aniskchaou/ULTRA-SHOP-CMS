<?php
	
add_filter('ninja_popups_subscribe_by_myemma', 'ninja_popups_subscribe_by_myemma', 10, 1);

function ninja_popups_subscribe_by_myemma($params = array()) 
{	
	require_once SNP_DIR_PATH . '/include/myemma/Emma.php';
	
	$ml_myemma_list = $params['popup_meta']['snp_ml_myemma_list'][0];
	if (!$ml_myemma_list) {
		$ml_myemma_list = snp_get_option('ml_myemma_list');
	}
	
	$result = array(
		'status' => false,
		'log' => array(
			'listId' => $ml_myemma_list,
			'errorMessage' => '',
		)
	);
	
	if (
		snp_get_option('ml_myemma_account_id') &&
        snp_get_option('ml_myemma_pubkey') &&
        snp_get_option('ml_myemma_privkey') &&
        $ml_myemma_list
    ) {
	    $rest = new Emma(snp_get_option('ml_myemma_account_id'), snp_get_option('ml_myemma_pubkey'), snp_get_option('ml_myemma_privkey'));
	    
	    $args = array();
	    $args['email'] = snp_trim($_POST['email']);
	    $args['group_ids'] = array($ml_myemma_list);
	    if (count($params['data']['cf']) > 0) {
		    $args['fields'] = (array) $params['data']['cf'];
        }
        
        try {
	        $response = $rest->membersAddSingle($args);
	        
	        $result['status'] = true;
        } catch (Exception $e) {
	        $result['log']['errorMessage'] = $e->getMessage();
        }
    }
            
	return $result;
}