<?php

add_filter('ninja_popups_subscribe_by_phplist', 'ninja_popups_subscribe_by_phplist', 10, 1);

function ninja_popups_subscribe_by_phplist($params = array())
{
    $username = snp_get_option('ml_phplist_username');
    $password = snp_get_option('ml_phplist_password');
    $base_uri = trim(snp_get_option('ml_phplist_uri')) . '/api/v2';

    if (
        snp_get_option('ml_manager') != 'phplist' ||
        !$username ||
        !$password
    ) {
        return;
    }

    $result = array(
        'status' => false,
        'log' => array(
            'listId' => null,
            'errorMessage' => '',
        )
    );


    $data = [];
    $data['email'] = snp_trim($params['data']['post']['email']);
    $data['confirmed'] = true;
    $data['blacklisted'] = false;
    $data['html_email'] = true;
    $data['disabled'] = false;

    if (!empty($params['data']['post']['name'])) {
        $data['first_name'] = $params['data']['names']['first'];
        $data['last_name'] = $params['data']['names']['last'];
    }

    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->request('POST', $base_uri . '/sessions', [
            'form_params' => [
                'login_name' => $username,
                'password' => $password,
            ],
        ]);

        $credentials = null;
        if ($response->getBody()) {
            $obj = json_decode($response->getBody(), true);
            $key = $obj['key'];

            $credentials = base64_encode($username . ':' . $key);
        }

        if ($credentials) {
            $subscriberRequest = $client->request('POST', $base_uri . '/subscribers', [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);

            if ($subscriberRequest->getBody()) {
                $obj = json_decode($subscriberRequest->getBody(), true);

                if ($obj['id']) {
                    $result['status'] = true;
                } else {
                    $result['log']['errorMessage'] = 'phpList error message: ' . print_r($subscriberRequest, true);
                }
            }
        }

    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        $result['log']['errorMessage'] = 'phpList error message: ' . $e->getMessage();
    }

    return $result;
}