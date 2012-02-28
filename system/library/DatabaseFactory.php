<?php

final class DatabaseFactory
{
	public static $instances = array();
	
	static function create($type = null, array $params = array(), $key = null)
	{
		$adapter = $type ? $type : Database_Config::$adapter;
		
		foreach (array('host', 'port', 'name', 'user', 'pass') as $k)
		{
			$_params[$k] = (isset($params[$k]) && $params[$k] !== null) ? $params[$k] : Database_Config::${$k};
		}
		
		require_once __DIR__.'/database/SQL.php';
		require_once __DIR__.'/database/'.$adapter.'.php';
		
		$adapter = $adapter.'_Adapter';
		$instance = new $adapter($_params);
		
		if($key)	{ self::$instances[$key] 	= $instance; }
		else		{ self::$instances[] 		= $instance; }
		
		return $instance;
	}

	
	private function __construct(){}
}

?>
