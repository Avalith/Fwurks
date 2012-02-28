<?php

class Registry
{
	private $data = array();
	
	private function __construct(){}
	
	public function __set($label, $var)
	{
		$this->data[$label] = $var;
	}
	
	public function __unset($label)
	{
		if($this->store[$label]){ unset($this->data[$label]); }
	}

	public function __get($label) {
		if($this->data[$label]){ return $this->data[$label]; }
		return false;
	}

	public function __isset($label)
	{
		if($this->data[$label]){ return true; }
		return false;
	}
	
	/**
	 * Instance
	 */
	private static $instance;
	public static function getInstance()
	{
		if(!self::$instance){ self::$instance = new Registry(); }
		return self::$instance;
	}
}
function Registry(){ return Registry::getInstance(); }

?>