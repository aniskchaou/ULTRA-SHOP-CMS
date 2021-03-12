<?php

class snp_customerio
{
    private $apikey;
    private $sitekey;
    private $customerio_url = 'https://track.customer.io/api/v1/customers/';
    
    public function __construct($apikey, $sitekey)
    {
        $this->apikey = $apikey;
        $this->sitekey = $sitekey;
    }
     
    public function subscribe($data)
    {
        $data['created_at'] = time();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->customerio_url.  urlencode($data['email']));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERPWD, $this->sitekey . ':' . $this->apikey);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}