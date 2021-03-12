<?php
	
class snp_sendgrid_class
{
	private $username;
	private $password;
	private $url = 'https://api.sendgrid.com/v3/contactdb';

	
	public function __construct($username, $password)
	{
		$this -> username = $username;
		$this -> password = $password;
	}
	public function getLists()
	{
		$request = $this -> url.'/lists';
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$request); 

                curl_setopt($ch,CURLOPT_HTTPHEADER,array('Authorization: Bearer '.$this->password));

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$result = curl_exec($ch);
//                var_dump($result);
		curl_close($ch);
		return $result;
	}
	public function addSubscriber($list, $data)
	{
		$request = $this-> url.'/recipients';
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$request); 
//		curl_setopt($ch,CURLOPT_POST,true);
		$data = json_encode(array($data));
//		$param = http_build_query(array($data));
                curl_setopt($ch,CURLOPT_HTTPHEADER,array('Authorization: Bearer '.$this->password, 'Content-Type: application/json', 'Content-Length: ' . strlen($data)));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch,CURLOPT_POSTFIELDS, $data); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);
                
                $result = json_decode($result);
                $id = $result->persisted_recipients[0];
                $request = $this-> url.'/lists/'. $list .'/recipients/'.$id;
		$ch = curl_init();
                curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch,CURLOPT_URL,$request); 
                 curl_setopt($ch,CURLOPT_HTTPHEADER,array('Authorization: Bearer '.$this->password));

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//                curl_setopt($ch,CURLOPT_POSTFIELDS, $param); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);
                if(strpos($result, 'HTTP/1.1 201 CREATED') !== false){
                    return true;
                }else{
                    return false;
                }
		return json_encode($array);
	}
	
	
	
	
	
	
	
	/*
	
	public function get_lists($list=NULL)
	{
		if (is_null($list))
		{
			return $this->_send('newsletter/lists/get.' . $this->api_format);
		}
		return $this->_send('newsletter/lists/get.' . $this->api_format, array('list' => $list));
	}


	
	
	*/
	
}




?>