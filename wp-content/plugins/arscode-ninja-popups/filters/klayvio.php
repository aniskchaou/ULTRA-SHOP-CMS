<?php

add_filter('ninja_popups_subscribe_by_klayvio', 'ninja_popups_subscribe_by_klayvio', 10, 1);

function ninja_popups_subscribe_by_klayvio($params = array())
{
    $key = snp_get_option('ml_klayvio_api_key');

    if (
        snp_get_option('ml_manager') != 'klayvio' ||
        !$key
    ) {
        return;
    }

    require_once SNP_DIR_PATH . '/include/klayvio/klayvio.php';

    $api = new Klayvio();
    $api->setApiKey($key);

    $ml_klayvio_list = $params['popup_meta']['snp_ml_klayvio_list'][0];
    if (!$ml_klayvio_list) {
        $ml_klayvio_list = snp_get_option('ml_klayvio_list');
    }

    $result = array(
        'status' => false,
        'log' => array(
            'listId' => $ml_klayvio_list,
            'errorMessage' => '',
        )
    );

    $data = array(
        'first_name' => $params['data']['names']['first'],
        'last_name'  => $params['data']['names']['last']
    );

    if (count($params['data']['cf']) > 0) {
        foreach ($params['data']['cf'] as $k => $v) {
            $data[$k] = $v;
        }
    }

    $klayvioData = array();
    foreach ($data as $key => $value) {
        if ($key == 'email') {
            $key = '$email';
        } else if ($key == 'first_name') {
            $key = '$first_name';
        } else if ($key == 'last_name') {
            $key = '$last_name';
        }

        $klayvioData[$key] = $value;
    }

    $result['learnq'] = $klayvioData;

    try {
        $response = $api->createSubscriber([
            'email' => snp_trim($params['data']['post']['email']),
            'custom' => $klayvioData
        ], $ml_klayvio_list);

        if (isset($response->body->person->email)) {
            $result['status'] = true;
        } else {
            $result['log']['errorMessage'] = 'Klayvio error message: ' . print_r($response, true);
        }
    } catch (Exception $e) {
        $result['log']['errorMessage'] = 'Klayvio error message: ' . $e->getMessage();
    }

    return $result;
}