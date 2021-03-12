<?php
class ElasticEmail
{
	private $api_key;				
	private $url = 'http://api.elasticemail.com';			
	
	public function __construct($api_key)
	{
		$this -> api_key = $api_key;
	}
	
	public function get_lists()
	{
		$url = $this->url.'/lists/get?'.http_build_query(array('api_key' =>$this->api_key));
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);											
		$response = curl_exec($ch);
		$p = xml_parser_create();
		xml_parse_into_struct($p, $response, $values, $index);
		xml_parser_free($p);
		if (isset($index['LIST'])) 
		{
			$lists = array();
			foreach ($index['LIST'] as $v) 
			{
				$lists[] = $values[$v]['attributes']['NAME'];
			}
		}
		return $lists;
	}
	
	public function subscribe($args)
	{
		$args['api_key'] = $this -> api_key;
		$url = $this->url.'/lists/create-contact?'.http_build_query($args);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
																	
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
}
?>