<?php

class snp_benchmark_class
{
	private $api_key;
	private $url='http://api.benchmarkemail.com/1.0/';

	public function __construct($api_key)
	{
		$this -> api_key = $api_key;
	}
	
	public function getLists()
	{
		$param = http_build_query(array('token' => $this->api_key, 'pageNumber' => 1, 'pageSize' => 50));

		$handle = curl_init($this->url.'?output=php&method=listGet');
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $param);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($handle);
		curl_close($handle);
		$response = unserialize($response);
		return $response;
	}
	
	public function subscribe($data)
	{	
		$data['token'] = $this->api_key;
		$param = http_build_query((array)$data);

		$handle = curl_init($this->url .'?output=php&method=listAddContacts');
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $param);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($handle);
		curl_close($handle);
		$response = unserialize($response);
		
		return $response;
	}
}
