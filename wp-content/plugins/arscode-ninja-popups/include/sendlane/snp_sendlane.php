<?php
class snp_sendlane{
    private $apikey;
    private $hashcode;
    private $subdomain;
    public function __construct($apikey, $hashcode, $subdomain){
        $this->apikey = $apikey;
        $this->hashcode = $hashcode;
        $this->subdomain = 'https://'. $subdomain .'.sendlane.com'; 
    }
    public function getLists(){
        $endpoint = $this->subdomain.'/api/v1/lists';
        $params = http_build_query(array('api' => $this->apikey, 'hash' => $this->hashcode));
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    public function subscribe($data){
        $endpoint = $this->subdomain.'/api/v1/list-subscribers-add';
        $credencials = array('api' => $this->apikey, 'hash' => $this->hashcode);
        $params = http_build_query(array_merge($credencials, $data));
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        if(isset($response->error)){
            throw new Exception(json_encode($response->error->messages));
        }
        if(isset($response->success)){
            return 'success';
        }
        throw new Exception(json_encode($response));
    }
}