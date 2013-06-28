<?php

class HttpRequest
{
	protected $url;
	
	protected $opts;
	protected $curl;
	protected $curl_info;
	protected $curl_errors;
	
	public function __construct($url)
	{
		$this->url		= $url;
		
		
		$this->curl = curl_init();
		curl_setopt_array($this->curl, array
		(
			CURLOPT_RETURNTRANSFER	=> 1,
		));
	}
	
	public function __destruct()
	{
		curl_close($this->curl);
	}
	
	public function send($get = null, array $post = array())
	{
		curl_setopt($this->curl, CURLOPT_URL, $this->url.(!empty($get) ? (strpos($this->url, '?') !== false ? '&' : '?').http_build_query($get) : ''));
		
		if (!empty($post))
		{
			curl_setopt_array($this->curl, array
			(
				CURLOPT_POST		=> 1,
				CURLOPT_POSTFIELDS	=> http_build_query($post)
			));
		}
		
		$response = curl_exec($this->curl);
		
		$this->curl_info			= curl_getinfo($this->curl);
		$this->curl_errors->message	= curl_error($this->curl);
		$this->curl_errors->code	= curl_errno($this->curl);
		
		return $response;
	}

	public function set_opt($name, $val)
	{
		curl_setopt($this->curl, $name, $val);
	}
	
	
	public static function reCaptcha()
	{
		$privkey = Application_Config::RECAPTCHA_PRIVATE;
		$remoteip = $_SERVER['REMOTE_ADDR'];
		
		$challenge = $_POST['recaptcha_challenge_field'];
		$response =	$_POST['recaptcha_response_field'];
		
		$host = 'api-verify.recaptcha.net/verify';
		
		
		//discard spam submissions
		if($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0)
		{
			return false;
		}
		$data = array ('privatekey' => $privkey, 'remoteip' => $remoteip, 'challenge' => $challenge, 'response' => $response);   
		foreach($data as $key => &$value){ $value = stripslashes($value); }
		
		$request = new self($host); 
		$response = explode("\n", $request->send(null, $data));
		
		return $response[0] == 'true' ? true : false;
	}
}

?>