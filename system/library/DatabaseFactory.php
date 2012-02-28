<?php

final class DatabaseFactory
{
	public static $instances = array();
	
	static function create($type = null, array $params = array(), $key = null)
	{
		$adapter = $type ? $type : Database_Config::$adapter;
		
		foreach (array('host', 'port', 'name', 'user', 'pass', 'prefix') as $k)
		{
			$_params[$k] = $params[$k] ? $params[$k] : Database_Config::${$k};
		}
		
		require_once dirname(__FILE__).'/database/SQL.php';
		require_once dirname(__FILE__).'/database/'.$adapter.'.php';
		
		$instance = new $adapter($_params);
		
		if($key)	{ self::$instances[$key] 	= $instance; }
		else		{ self::$instances[] 		= $instance; }
		
		return $instance;
	}

	
	private function __construct(){}
}

?>
