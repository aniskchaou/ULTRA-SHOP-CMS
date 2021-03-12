<?php

class snp_sendreach
{
	private $apikey;
	private $apisecret;
	private $url = 'http://dashboard.sendreach.com/index.php';
	private $method;
	private $headers=array();
	
	public function __construct($key, $secret)
	{
		$this->apikey = $key;
		$this->apisecret = $secret;
	}
	
	public function getLists()
	{
		$this->method = "GET";
		$this->headers = array(
			'X-MW-PUBLIC-KEY' => $this->apikey,
			'X-MW-TIMESTAMP' => time(),
			'X-MW-REMOTE-ADDR' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
			'X-MW-SIGNATURE' =>'',
			'If-None-Match' => '',
		);
		$requestUrl = 'http://dashboard.sendreach.com/api/index.php/lists?page=1&per_page=100';
		$this->sign($requestUrl);
        $ch = curl_init($requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER , true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
        if (count($this->headers | 1) > 0) 
        {
            $headers = array();
            foreach($this->headers as $name => $value) {
                $headers[] = $name.': '.$value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $body = json_decode(curl_exec($ch), true);
		return $body['data']['records'];
	}
	
	protected function sign($requestUrl, $data=null)
    {
        $publicKey  = $this->apikey;
        $privateKey = $this->apisecret;
        $timestamp  = time();
        $params = array(
            'X-MW-PUBLIC-KEY'   => $publicKey,
            'X-MW-TIMESTAMP'    => $timestamp,
            'X-MW-REMOTE-ADDR'  => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,                                    
        );
        if(isset($data))
        {
			$params = array_merge($params, $data);	
		}
        ksort($params, SORT_STRING);
        $separator          = strpos($requestUrl, '?') !== false ? '&' : '?';
        $signatureString    = strtoupper($this->method) . ' ' . $requestUrl . $separator . http_build_query($params, '', '&');
        $signature          = hash_hmac('sha1', $signatureString, $privateKey, false);
        $this->headers['X-MW-SIGNATURE'] = $signature;
    }
	
	public function subscribe($data, $list)
	{
		$this->method = "POST";
		$this->headers = array(
			'X-MW-PUBLIC-KEY' => $this->apikey,
			'X-MW-TIMESTAMP' => time(),
			'X-MW-REMOTE-ADDR' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
			'X-MW-SIGNATURE' =>'',
		);
		$requestUrl = 'http://dashboard.sendreach.com/api/index.php/lists/'.$list.'/subscribers';
		$this->sign($requestUrl, $data);
		$ch = curl_init($requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER , true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        if (count($this->headers | 1) > 0) {
            $headers = array();
            foreach($this->headers as $name => $value) {
                $headers[] = $name.': '.$value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
		$body = curl_exec($ch);
		curl_close($ch);

		return $body;
	}
}