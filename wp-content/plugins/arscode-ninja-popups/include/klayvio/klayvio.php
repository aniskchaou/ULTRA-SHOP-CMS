<?php

require_once SNP_DIR_PATH . '/include/httpful.phar';

/**
 * Class Klayvio
 */
class Klayvio
{
    /**
     * @var string
     */
    protected $baseUrl = 'https://a.klaviyo.com/';

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @param $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Returns lists
     * @return mixed
     */
    public function getLists()
    {
        $response = \Httpful\Request::get($this->baseUrl . 'api/v1/lists?api_key=' . $this->apiKey)
            ->expectsJson()
            ->send();

        return $response->body->data;
    }

    /**
     * Save subscriber on list
     * @param $data
     * @param $list
     * @return mixed
     */
    public function createSubscriber($data, $list)
    {
        $requestData = [
            'email' => $data['email'],
            'properties' => json_encode($data['custom']),
            'confirm_optin' => true
        ];

        $response = \Httpful\Request::post($this->baseUrl . 'api/v1/list/' . $list . '/members?api_key=' . $this->apiKey, $requestData, \Httpful\Mime::FORM)
            ->expectsJson()
            ->send();

        return $response;
    }
}