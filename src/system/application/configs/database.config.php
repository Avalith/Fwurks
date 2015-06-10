<?php

final class Database_Config
{
	public static $default_connection = 'default';
	
	public static $connections = array
	(
		'eloquent' => array
		(
			'default'	=> array(
				'driver'	=> 'mysql',
				'host'		=> 'localhost',
				'database'	=> 'test',
				'username'	=> 'root',
				'password'	=> '',
				'charset'	=> 'utf8',
				'collation'	=> 'utf8_general_ci',
				'prefix'	=> ''
			)
		),
	);
	
	public static function connect_eloquent()
	{
		require_once Paths_Config::$library . 'database' . DS . 'vendor' . DS . 'autoload.php';
		
		$capsule = new Illuminate\Database\Capsule\Manager;
		
		foreach(self::$connections['eloquent'] as $name => $config){ $capsule->addConnection($config, $name); }
		
		$capsule->bootEloquent();
	}
}


?>
