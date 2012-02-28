<?php

class SessionFactory
{
	public static $instances = array();
	
	static function create($type = null, array $params = array(), $key = null)
	{
		$adapter = ($type ? $type : Session_Config::$adapter).'Session';
		
		foreach (array('cache_expire', 'name', 'cookie') as $k)
		{
			$_params[$k] = isset($params[$k]) ? $params[$k] : Session_Config::${$k};
		}
		
		require_once dirname(__FILE__).'/session/Session.php';
		require_once dirname(__FILE__).'/session/'.$adapter.'.php';
		
		$instance = new $adapter($_params);
		
		if($key)	{ self::$instances[$key] 	= $instance; }
		else		{ self::$instances[] 		= $instance; }
		
		return $instance;
	}

	
	private function __construct(){}
}
?>
