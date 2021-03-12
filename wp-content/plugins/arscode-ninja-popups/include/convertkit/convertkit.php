<?php

/**
 * Class Convertkit
 */
class Convertkit {
    /**
     * @var string
     */
    private $baseUrl = 'https://api.convertkit.com/v3/';

    /**
     * @var
     */
    private $apiKey;

    /**
     * Convertkit constructor.
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return mixed
     */
    public function getForms()
    {
        return $this->requestGet('forms');
    }

    /**
     * @param $formId
     * @param $params
     * @return mixed
     */
    public function addToForm($formId, $params)
    {
        return $this->requestPost('forms/' . $formId . '/subscribe', $params);
    }

    /**
     * @param string $path
     * @param array $data
     * @return mixed
     */
    private function requestGet($path = '', $data = array())
    {
        $url = $this->baseUrl . $path;

        $data['api_key'] = $this->apiKey;

        if (!is_null($data)) {
            $get_fields = (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($data);
        }
        else {
            $get_fields = '';
        }

        $defaults = array(
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
            CURLOPT_AUTOREFERER => true,
            CURLOPT_URL => $url. $get_fields,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYPEER => false
        );

        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        if(!$result = curl_exec($ch)) {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result);
    }

    /**
     * @param string $path
     * @param array $data
     * @return mixed
     */
    private function requestPost($path = '', $data = array())
    {
        $url = $this->baseUrl . $path;

        $data['api_key'] = $this->apiKey;

        $defaults = array(
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
            CURLOPT_AUTOREFERER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_POSTFIELDS => !empty($data) ? http_build_query($data) : '',
            CURLOPT_SSL_VERIFYPEER => false
        );

        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        if(!$result = curl_exec($ch)) {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result);
    }
}