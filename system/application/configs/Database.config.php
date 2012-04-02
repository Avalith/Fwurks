<?php

final class Database_Config
{
	public static $default = array
	(
		'adapter'	=> 'MySQL',
		
//		'dsn'		=> 'mysql:dbname=testdb;host=127.0.0.1',
//		'dsn'		=> 'mydb',
//		'uri'		=> 'uri:file:///usr/local/dbconnect',
		
		'host'		=> 'localhost', 'port' => '3306', 'name' => '3306',
		
		'user'		=> 'root',
		'pass'		=> '',
	);
	
	
}


?>
