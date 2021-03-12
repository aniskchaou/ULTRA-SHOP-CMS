<?php

add_filter('ninja_popups_subscribe_by_mailfit', 'ninja_popups_subscribe_by_mailfit', 10, 1);

function ninja_popups_subscribe_by_mailfit($params = array())
{
    require_once SNP_DIR_PATH . '/include/httpful.phar';

    $listId = $params['popup_meta']['snp_ml_mailfit_list'][0];
    if (!$listId) {
        $listId = snp_get_option('ml_mailfit_list');
    }

    $endpoint = snp_get_option('ml_mailfit_endpoint');

    $token = snp_get_option('ml_mailfit_apitoken');

    $result = array(
        'status' => false,
        'log' => array(
            'listId' => $listId,
            'errorMessage' => '',
        )
    );

    $body = array();
    $body['EMAIL'] = snp_trim($params['data']['post']['email']);

    if (!empty($params['data']['post']['name'])) {
        $body = array_merge($body, array(
            'FIRST_NAME' => $params['data']['names']['first'],
            'LAST_NAME' => $params['data']['names']['last']
        ));
    }

    if (count($params['data']['cf']) > 0) {
        foreach ($params['data']['cf'] as $k => $v) {
            $body[$k] = $v;
        }
    }

    try {
        $response = \Httpful\Request::post('lists/' . $listId . '/subscribers/store?api_token=' . $token)
            ->expectsJson()
            ->body($body)
            ->send();

        if ($response->body->message == 'Subscriber was successfully created') {
            $result['status'] = true;
        } else {
            $result['log']['errorMessage'] = 'MailFit error message: ' . print_r($response, true);
        }
    } catch (Exception $e) {
        $result['log']['errorMessage'] = 'MailFit error message: ' . $e->getMessage();
    }

    return $result;
}