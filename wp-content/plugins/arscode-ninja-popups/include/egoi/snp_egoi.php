<?php

class snp_egoi
{
    private $apikey;
    private $url = 'http://api.e-goi.com/v2/rest.php';

    public function __construct($apikey)
    {
        $this->apikey = $apikey;
    }

    public function getLists()
    {
        $params = array(
            "method" => 'getLists',
            "functionOptions" => array('apikey' => $this->apikey),
            "type" => "json"
        );

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $response = json_decode(curl_exec($ch), true);
        if ($response['Egoi_Api']['getLists']['status'] != 'success') {
            throw new Exception('Unable to get lists.');
        }
        unset($response['Egoi_Api']['getLists']['status']);
        curl_close($ch);

        return $response['Egoi_Api']['getLists'];
    }

    public function subscribe($data)
    {
        $data['apikey'] = $this->apikey;

        $params = array(
            "method" => 'addSubscriber',
            "functionOptions" => $data,
            "type" => "json"
        );

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $response = json_decode(curl_exec($ch), true);
        if (isset($response['Egoi_Api']['addSubscriber']['ERROR'])) {
            throw new Exception($response['Egoi_Api']['addSubscriber']['ERROR']);
        }

        return true;
    }
}