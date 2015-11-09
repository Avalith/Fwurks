<?php

final class Database_Config
{
	public static $default_connection = 'default';
	
	public static $connections = array
	(
		'activerecord' => array
		(
			'default'	=> 'mysql://root:@localhost/test',
			'test'		=> 'mysql://root:@localhost/test',
		),
	);
	
	public static function connect_activerecord()
	{
		// require Paths_Config::$library . 'database' . DS . 'ActiveRecord' . DS . 'ActiveRecord.php';
		
		// ActiveRecord\Config::initialize(function($config)
		// {
		// 	$config->set_connections(self::$connections['activerecord']);
		// 	$config->set_default_connection(self::$default_connection);
		// });
	}
}


?>
