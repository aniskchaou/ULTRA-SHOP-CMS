<?php
class snp_mailjet
{
	private $api_key;
	private $api_secret;
	private $url = 'https://api.mailjet.com/v3/REST' ;
	
	public function __construct($api_key, $api_secret)
	{
		$this->api_key = $api_key;
		$this->api_secret = $api_secret;
	}

	public function getLists()
	{
		$handle = curl_init($this->url.'/contactslist?limit=100');
		curl_setopt($handle, CURLOPT_USERPWD, $this->api_key.':'.$this->api_secret);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_POST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($handle);
		curl_close($handle);
		$response = json_decode($response);
		return $response;	
	}
	
	public function subscribe($list, $email, $name, $param = NULL)
	{
		$data = array();
		$data['Action'] = 'addforce';
		$data['email'] = $email;
		if (!empty($name)) {
		    $data['Name'] = $name['first'] . ' ' . $name['last'];
        }
		$data = http_build_query((array)$data);
		$handle = curl_init($this->url.'/contactslist/'.$list.'/managecontact');
		curl_setopt($handle, CURLOPT_USERPWD, $this->api_key.':'.$this->api_secret);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($handle);
		curl_close($handle);
		$response = json_decode($response, true);

		if($param !== NULL && isset($response) && $response['Count']>0)
		{
			$data = array('Data' => $param);
			$header = array('Content-Type: application/json');
			$handle = curl_init($this->url.'/contactdata/'.$response['Data'][0]['ContactID']);
			curl_setopt($handle, CURLOPT_USERPWD, $this->api_key.':'.$this->api_secret);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($handle, CURLOPT_POST, true);
			curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($handle, CURLOPT_HTTPHEADER, $header);
			$resp = curl_exec($handle);
			curl_close($handle);
		}
		return $response;	
	}
}
?>