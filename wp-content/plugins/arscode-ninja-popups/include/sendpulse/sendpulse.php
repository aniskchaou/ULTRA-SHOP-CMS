<?php
class snp_sendpulse
{
	private $id;
	private $secret;
	private $url = 'https://api.sendpulse.com';				//		/addressbooks  --listy
	
	public function __construct($id, $secret)
	{
		$this->id = $id;
		$this->secret = $secret;
	}
	private function auth()
	{
		$pass = array(
			'client_id' => $this->id,
			'client_secret' => $this->secret,
			'grant_type' => 'client_credentials'
		);

		$ch = curl_init($this->url.'/oauth/access_token');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($pass));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($response, true);

		return $response;		
	}

	public function getLists()
	{
		$auth = $this->auth();
		$header = array('Authorization: Bearer '.$auth['access_token']);
		$ch = curl_init($this->url .'/addressbooks');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

		$response = json_decode(curl_exec($ch), true);
		curl_close($ch);

		return $response;
	}

	public function subscribe($data, $list)
	{
		$auth = $this->auth();
		$data = array('emails' => serialize(array($data)));
		$header = array('Authorization: Bearer '.$auth['access_token']);
		$ch = curl_init($this->url .'/addressbooks/'.$list.'/emails');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		
		$response = curl_exec($ch);
		curl_close($ch);

		return json_decode($response);
	}
}