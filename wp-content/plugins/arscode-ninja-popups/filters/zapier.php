<?php

add_filter('ninja_popups_subscribe_by_zapier', 'ninja_popups_subscribe_by_zapier', 10, 1);

function ninja_popups_subscribe_by_zapier($params = array())
{
    if (snp_get_option('ml_manager') != 'zapier') {
        return;
    }

    require_once SNP_DIR_PATH . '/include/httpful.phar';

    $ml_zapier_url = $params['popup_meta']['snp_ml_zapier_url'][0];
    if (!$ml_zapier_url) {
        $ml_zapier_url = snp_get_option('ml_zapier_url');
    }

    $result = array(
        'status' => false,
        'log' => array(
            'listId' => $ml_zapier_url,
            'errorMessage' => '',
        )
    );

    $data = array();
    $data['email_address'] = snp_trim($params['data']['post']['email']);
    if (!empty($params['data']['post']['name'])) {
        $data = array(
            'first_name' => $params['data']['names']['first'],
            'last_name' => $params['data']['names']['last']
        );
    }

    if (count($params['data']['cf']) > 0) {
        foreach ($params['data']['cf'] as $k => $v) {
            if ($k == 'ip_address') {

            } else {
                $data[$k] = $v;
            }
        }
    }

    try {
        $response = \Httpful\Request::post($ml_zapier_url)
            ->sendsJson()
            ->body(json_encode($data))
            ->expectsJson()
            ->send();


        $result['status'] = true;
    } catch (Exception $e) {
        $result['log']['errorMessage'] = 'Zapier error message: ' . $e->getMessage();
    }

    return $result;
}
