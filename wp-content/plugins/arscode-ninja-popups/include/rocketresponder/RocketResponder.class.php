<?php 

class RocketResponder {
	public $PublicKey;
	public $PrivateKey;

	public function __construct($pub, $priv, $unsecure = 0) 
	{
		$this->PublicKey = $pub;
		$this->PrivateKey = $priv;
		$this->Unsecure = $unsecure;
	}
	
	public function request($endpoint, $DATA = array()) {
		$DATA["Time"] = time();
		
		$DATA = array_map('strval',$DATA);
		array_multisort($DATA, SORT_ASC, SORT_STRING);
		$HASH = md5(json_encode($DATA));
		
		$Signature = md5($this->PrivateKey . "https://www.rocketresponder.com/api/".$endpoint . $HASH);
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, "https://www.rocketresponder.com/api/".$endpoint);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $this->PublicKey . ":" . $Signature);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($DATA));
		
		if ($this->Unsecure == 1) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$this->output = curl_exec($ch);
		
		if ($this->output === false) {
			$message = curl_error($ch);
			curl_close($ch);
			throw new Exception("Curl Error: ".$message);
		}
		
		try {
			return json_decode($this->output, false);
		} catch (Exception $e) {
			throw new Exception("Invalid response from API: ".$this->output);
		}
	} 

	public function subscribe($email, $LID, $XTRA = null) {
		if (!self::validate_email($email)) {
			throw new Exception("Invalid Email Address");
		}
		if ($LID == "" || $LID != intval($LID)) {
			throw new Exception("Invalid List ID");
		}
		
		$DATA["email"] = $email;
		$DATA["LID"] = $LID;
		if ($XTRA["name"] != "") { $DATA["name"] = $XTRA["name"]; }
		if ($XTRA["ConfirmURL"] != "" && self::validate_url($XTRA["ConfirmURL"])) { $DATA["ConfirmURL"] = $XTRA["ConfirmURL"]; }
		
		return self::request("subscriber/subscribe", $DATA);
	}

	public function unsubscribe($email, $LID) {
		if (!self::validate_email($email)) {
			throw new Exception("Invalid Email Address");
		}
		if ($LID == "" || $LID != intval($LID)) {
			throw new Exception("Invalid List ID");
		}
		
		$DATA["email"] = $email;
		$DATA["LID"] = $LID;
		
		return self::request("subscriber/unsubscribe", $DATA);
	}


	public function modify($oldemail, $LID, $newemail, $XTRA = null) {
		if (!self::validate_email($oldemail) || !self::validate_email($newemail)) {
			throw new Exception("Invalid Email Address");
		}
		if ($LID == "" || $LID != intval($LID)) {
			throw new Exception("Invalid List ID");
		}
	
		$DATA["email"] = $oldemail;
		$DATA["newemail"] = $newemail;
		$DATA["LID"] = $LID;
		if ($XTRA["name"] != "") { $DATA["name"] = $XTRA["name"]; }
		if ($XTRA["ConfirmURL"] != "" && self::validate_url($XTRA["ConfirmURL"])) { $DATA["ConfirmURL"] = $XTRA["ConfirmURL"]; }
	
		return self::request("subscriber/modify", $DATA);
	}


	public function lookup($email, $LID) {
		if (!self::validate_email($email)) {
			throw new Exception("Invalid Email Address");
		}
		if ($LID == "" || $LID != intval($LID)) {
			throw new Exception("Invalid List ID");
		}
	
		$DATA["email"] = $email;
		$DATA["LID"] = $LID;
		
		return self::request("subscriber/lookup", $DATA);
	}


	public function getlists() {
		return self::request("list/all");
	}


	public function createlist($name, $permalink, $fromname, $fromemail, $address, $header = "", $footer = "", $notify = 0) {
		
		
		if (!self::validate_email($fromemail)) {
			throw new Exception("Invalid Email Address");
		}
		
		$DATA["Name"] = $name;
		$DATA["Permalink"] = $permalink;
		$DATA["FromName"] = $fromname;
		$DATA["FromEmail"] = $fromemail;
		$DATA["Address"] = $address;
		$DATA["Header"] = $header;
		$DATA["Footer"] = $footer;
		$DATA["Notify"] = $notify;
		
		return self::request("list/create", $DATA);
	}
	

	private function validate_email($email) {
		if (preg_match("/^([_a-z0-9\+-]+)(\.[_a-z0-9\+-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email)) {
			return true;
		}
		return false;
	}
	
	private function validate_url($url) {
		
		return true;
	}
	
	
	
}

?>