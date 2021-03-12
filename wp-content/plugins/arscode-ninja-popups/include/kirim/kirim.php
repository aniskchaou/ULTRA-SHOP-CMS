<?php

/**
 * Class Kirim
 */
class Kirim {
	    /**
     * @var
     */
    protected $baseUrl = 'https://aplikasi.kirim.email/api/v3/';
    
    /**
     * @var
     */
    protected $username;

    /**
     * @var
     */
    protected $token;

    /**
     * Campaigner constructor.
     */
    public function __construct() {}

    /**
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    public function getLists()
    {
    	$timeStamp = time();

    	$response = $this->curlGet('list', null, array(
    		CURLOPT_HTTPHEADER => array(
    			'Auth-Id: ' . $this->getUsername(),
    			'Auth-Token: ' . hash_hmac("sha256", $this->getUsername() . "::" . $this->getToken() . "::" . $timeStamp, $this->getToken()),
    			'Timestamp: ' . $timeStamp
    		)
    	));

    	$json = json_decode($response);

    	if (isset($json->code)) {
    		if ($json->status == 'success') {
    			$list = array();

    			foreach ($json->data as $data) {
    				$list[$data->id] = $data->name;
    			}

    			return $list;
    		} else {
    			throw new Exception('Error while fetching lists: ' . $json->message);
    		}
    	} else {
    		throw new Exception('Bad response format: ' . var_export($json));
    	}
    }

    public function addSubscriber($listId, $email, $firstName, $lastName, $params)
    {
    	$data = array(
    		'full_name' => $firstName . ' ' . $lastName,
    		'email' => $email,
    		'lists' => $listId,
    	);

    	if (!empty($params)) {
    		$data['fields'] = $params;
    	}

    	$timeStamp = time();

    	$response = $this->curlPost('subscriber', $data, array(
    		CURLOPT_HTTPHEADER => array(
    			'Auth-Id: ' . $this->getUsername(),
    			'Auth-Token: ' . hash_hmac("sha256", $this->getUsername() . "::" . $this->getToken() . "::" . $timeStamp, $this->getToken()),
    			'Timestamp: ' . $timeStamp
    		)
    	));

    	$json = json_decode($response);

    	if (isset($json->code)) {
    		if ($json->status == 'success') {
    			return true;
    		} else {
    			throw new Exception('Error adding subscriber: ' . $json->message);
    		}
    	} else {
    		throw new Exception('Bad response format: ' . var_export($json));
    	}
    }

    /** 
     * Send a POST requst using cURL
     * 
     * @param string $url to request 
     * @param array $post values to send 
     * @param array $options for cURL 
     * @return string 
     */ 
    protected function curlPost($url, array $post = NULL, array $options = array()) 
    {
        $defaults = array( 
            CURLOPT_USERAGENT       => 'Mozilla/5.0 (Windows; U; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
            CURLOPT_AUTOREFERER     => true,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_POST            => 1, 
            CURLOPT_HEADER          => 0, 
            CURLOPT_URL             => $this->baseUrl . $url, 
            CURLOPT_RETURNTRANSFER  => 1, 
            CURLOPT_TIMEOUT         => 300, 
            CURLOPT_POSTFIELDS      => !empty($post) ? http_build_query($post) : '',
            CURLOPT_COOKIEFILE      => 'cookie.txt',
            CURLOPT_COOKIEJAR       => 'cookie.txt',
            CURLOPT_SSL_VERIFYPEER  => false
        );
    
        $ch = curl_init(); 
        curl_setopt_array($ch, ($options + $defaults)); 
        if(!$result = curl_exec($ch)) { 
            trigger_error(curl_error($ch)); 
        } 
        curl_close($ch); 
        
        return $result; 
    }

    /**
     * Send a GET request using cURL
     *
     * @param string $url to request
     * @param array $get values to send
     * @param $options for cURL
     * @return string
     */
    protected function curlGet($url, array $get = NULL, array $options = array())
    {
        if (!is_null($get)) {
            $get_fields = (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($get);
        } else {
            $get_fields = '';
        }

        $defaults = array(
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows; U; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_URL            => $this->baseUrl . $url . $get_fields, 
            CURLOPT_HEADER         => 0, 
            CURLOPT_RETURNTRANSFER => TRUE, 
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_COOKIEFILE     => 'cookie.txt',
            CURLOPT_COOKIEJAR      => 'cookie.txt',
            CURLOPT_SSL_VERIFYPEER => false
        ); 

        $ch = curl_init(); 
        curl_setopt_array($ch, ($options + $defaults)); 
        if(!$result = curl_exec($ch)) { 
            trigger_error(curl_error($ch)); 
        } 
        curl_close($ch);

        return $result;
    }
}