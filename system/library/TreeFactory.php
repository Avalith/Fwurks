<?php

final class TreeFactory
{
	public static $instances = array();
	
	static function create($type, array $params = array(), $key = null)
	{
		$adapter = $type.'Tree';
		
		require_once dirname(__FILE__).'/tree/Interfaces.php';
		require_once dirname(__FILE__).'/tree/Abstractions.php';
		require_once dirname(__FILE__).'/tree/'.$adapter.'.php';
		
		$instance = new $adapter( $params[0], ($params[1] ? $params[1] : array()) );
		
		if($key)	{ self::$instances[$key] 	= $instance; }
		else		{ self::$instances[] 		= $instance; }
		
		return $instance;
	}
	
	private function __construct(){}
}

?>
