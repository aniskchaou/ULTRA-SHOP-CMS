<?php
class snp_ontraport
{
	private $key;
	private $id;
	public function __construct($id, $key)
	{
		$this->id = $id;
		$this->key = $key;
	}	
	public function subscribe($data)
	{
		$data = urlencode(urlencode($data));
		$reqType= "add";
		$postargs = "appid=". $this->id ."&key=". $this->key ."&return_id=1&reqType=".$reqType."&data=".$data;
		$request = "https://api.ontraport.com/cdata.php";
		$session = curl_init($request);
		curl_setopt ($session, CURLOPT_POST, true);
		curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($session);
		curl_close($session);
		$p = xml_parser_create();
		xml_parse_into_struct($p, $response, $vals, $index);
		xml_parser_free($p);
		return $vals[$index['STATUS'][0]]['value'];	
	}	
	public function getTags()
	{
		$reqType= "pull_tag";
		$postargs = "appid=". $this->id ."&key=". $this->key ."&reqType=".$reqType;
		$request = "https://api.ontraport.com/cdata.php";
		$session = curl_init($request);
		curl_setopt ($session, CURLOPT_POST, true);
		curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($session);
		curl_close($session);
		$p = xml_parser_create();
		xml_parse_into_struct($p, $response, $vals, $index);
		xml_parser_free($p);
		$response = array();
		foreach($index['TAG'] as $v)
		{
			$response[] = $vals[$v]['value'];
		}
		return $response;
	}
}
?>