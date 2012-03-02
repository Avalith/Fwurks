<?php

final class Database_Config
{
	public static $adapter = 'PDO_MySQL';
	
	public static $host = '';
	public static $port = '3306';
	public static $name = '';
	public static $user = '';
	public static $pass = '';
	
	
	// minutes
	public static $save_cache = false;
	public static $cache_folder = 'db_cache';
	public static $cache_expire = 60;
}


?>
