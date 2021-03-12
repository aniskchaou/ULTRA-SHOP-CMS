<?php

add_filter('ninja_popups_subscribe_by_moosend', 'ninja_popups_subscribe_by_moosend', 10, 1);

function ninja_popups_subscribe_by_moosend($params = array())
{
    $key = snp_get_option('ml_moosend_api_key');

    if (
        snp_get_option('ml_manager') != 'moosend' ||
        !$key
    ) {
        return;
    }

    require_once SNP_DIR_PATH . '/include/moosend/autoload.php';

    $ml_moosend_list = '';
    if (isset($params['popup_meta']['snp_ml_moosend_list'][0])) {
        $ml_moosend_list = $params['popup_meta']['snp_ml_moosend_list'][0];
    }

    if (!$ml_moosend_list) {
        $ml_moosend_list = snp_get_option('ml_moosend_list');
    }

    $result = array(
        'status' => false,
        'log' => array(
            'listId' => $ml_moosend_list,
            'errorMessage' => '',
        )
    );

    $data = array();
    if (count($params['data']['cf']) > 0) {
        foreach ($params['data']['cf'] as $k => $v) {
            $data[$k] = $v;
        }
    }

    try {
        $body = new \Swagger\Client\Model\AddingSubscribersRequest();
        $body->setEmail(snp_trim($params['data']['post']['email']));

        if (!empty($params['data']['post']['name'])) {
            $body->setName($params['data']['names']['first'] . ' ' . $params['data']['names']['last']);
        }

        $api = new Swagger\Client\Api\SubscribersApi();
        $response = $api->addingSubscribers('json', $ml_moosend_list, $key, $body);

        $result['status'] = true;
    } catch (Exception $e) {
        $result['log']['errorMessage'] = 'Moosend error message: ' . $e->getMessage();
    }

    return $result;
}
