<?php
class snp_mailrelay
{
    private $apikey;
    private $url;
    
    public function __construct($apikey, $address)
    {
        $this->apikey = $apikey;
        $this->url = 'https://'. trim($address, ' /') .'/ccm/admin/api/version/2/&type=json';
    }
    
    public function getLists()
    {
        $curl = curl_init($this->url);
        $postData = array(
            'function' => 'getGroups',
            'apiKey' => $this->apikey,
        );
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $json = curl_exec($curl);
        $result = json_decode($json);
        curl_close($curl);
        return $result;
    }
    
    public function subscribe($data)
    {
        $data['function'] = 'addSubscriber';
        $data['apiKey'] = $this->apikey;
        
        $curl = curl_init($this->url);
        $post = http_build_query($data);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        
        $json = curl_exec($curl);
        $result = json_decode($json);
        curl_close($curl);
        return $result;
    }
}