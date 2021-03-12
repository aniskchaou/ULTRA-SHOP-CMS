<?php
	
class snp_agilecrm_class
{
	private $api_key;
	private $subdomain;
	private $useremail;
	
	public function __construct($api_key, $subdomain, $useremail)
	{
		$this -> api_key = $api_key;
		$this -> subdomain = $subdomain;
		$this -> useremail = $useremail;
	}

	public function curl_wrap($entity, $data, $method)
	{
	    $agile_url     = "https://" . $this->subdomain . ".agilecrm.com/dev/api/" . $entity;
	    $agile_php_url = "https://" . $this->subdomain . ".agilecrm.com/core/php/api/" . $entity . "?id=" . $this->api_key;

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
	    curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, true);

	    switch ($method) {
	        case "POST":
	            $url = ($entity == "tags" ? $agile_php_url : $agile_url);
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	            break;
	        case "GET":
	            $url = ($entity == "tags" ? $agile_php_url . '&email=' . $data->{'email'} : $agile_url);
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	            break;
	        case "PUT":
	            $url = ($entity == "tags" ? $agile_php_url : $agile_url);
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	            break;
	        case "DELETE":
	            $url = ($entity == "tags" ? $agile_php_url : $agile_url);
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	            break;
	        default:
	            break;
	    }

	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	        'Content-type : application/json;','Accept : application/json'
	    ));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_USERPWD, $this->useremail . ':' . $this->api_key);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	    $output = curl_exec($ch);
	    curl_close($ch);

	    return $output;
	}	
}