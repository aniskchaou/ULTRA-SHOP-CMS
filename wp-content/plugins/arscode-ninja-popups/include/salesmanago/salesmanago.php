<?php
class snp_salesmanago
{
	private $apiKey;
	private $apiSecret;
	private $clientId;
	private $endpoint;
	private $email;
	
	public function __construct($endpoint, $clientId, $apiSecret, $apiKey, $email)
	{
		$this->endpoint = $endpoint;
		$this->clientId = $clientId;
		$this->apiSecret = $apiSecret;
		$this->apiKey = $apiKey;
		$this->email = $email;
	}

	public function do_request($url, $data)
	{
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    	'Content-Type: application/json',
	    	'Content-Length: ' . strlen($data)
	    ));
		$res = curl_exec($ch);
		curl_close($ch);

		return $res;
	}

	public function getTags()
	{
		$url = 'http://' . $this->endpoint .'/api/contact/tags';

		$request = array(
			'apiKey'         => $this->apiKey,
  			'clientId'       => $this->clientId,
			'showSystemTags' => true,
			'owner'          => $this->email,
			'sha'            => sha1($this->apiKey . $this->clientId . $this->apiSecret),
		);

		return $this->do_request($url, json_encode($request));
	}

	public function subscribe($data, $tags, $type = 'optin')
	{
		$url = 'http://' . $this->endpoint .'/api/contact/upsert';

		$request = array(
			'apiKey' => $this->apiKey,
			'clientId' => $this->clientId,
			'owner' => $this->email,
			'sha' => sha1($this->apiKey . $this->clientId . $this->apiSecret),
		);

		$request["contact"] = $data;
		$request["tags"] = $tags;

		if ($type == 'optout') {
			$request['forceOptOut'] = true;
		}

		$request = json_encode($request);
		
		return $this->do_request($url, $request);
	}
}