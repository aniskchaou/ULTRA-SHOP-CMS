<?php
	
add_filter('ninja_popups_subscribe_by_csv', 'ninja_popups_subscribe_by_csv', 10, 1);

function ninja_popups_subscribe_by_csv($params = array()) 
{
	if (!isset($params['data']['post']['name'])) {
		$params['data']['post']['name'] = '';
	}
	
	if (count($params['data']['cf']) > 0) {
		$CustomFields = '';
		foreach ($params['data']['cf'] as $k => $v) {
			$CustomFields.= $k . ' = ' . $v . ';';
		}
	}

	$filename = snp_get_option('ml_csv_file');
	if (isset($params['filename'])) {
	    $filename = $params['filename'];
    }
	
	$data = snp_trim($params['data']['post']['email']) . ";" . $params['data']['post']['name'] . ";" . $CustomFields . get_the_title($params['data']['post']['popup_ID']) . " (" . $params['data']['post']['popup_ID'] . ");" . date('Y-m-d H:i') . ";" . $_SERVER['REMOTE_ADDR'] . ";\n";
    
    if (file_put_contents(SNP_DIR_PATH . 'csv/' . $filename, $data, FILE_APPEND | LOCK_EX) !== FALSE) {
	    $result['status'] = true;
	} else {
		$result['log']['errorMessage'] = 'CSV Problem!';
	}
	
	return $result;
}