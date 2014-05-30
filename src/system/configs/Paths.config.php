<?php

// Directories
// with trailing slash
final class Paths_Config
{
	public static $base;
	
	public static $root;
	
	public static $system;
	public static $resources;
	
	public static $configs;
	public static $library;
	public static $application;
	
	public static $app_atoms			= 'atoms';
	public static $app_configs			= 'configs';
	public static $app_locales			= 'locales';
	public static $app_models			= 'models';
	
	public static $atom_configs			= 'configs';
	public static $atom_controllers		= 'controllers';
	public static $atom_models			= 'models';
	public static $atom_library			= 'locales';
	public static $atom_views			= 'views';
	public static $atom_temp			= 'temp';
	
	
	public static function load()
	{
		self::$base			= Dispatcher::$url_base;
		
		self::$root			= Dispatcher::$cwd . DS;
		
		self::$system		= self::$root			. Dispatcher::$folder_system		. DS;
		self::$resources	= self::$root			. Dispatcher::$folder_resources		. DS;
		
		self::$configs		= self::$system			. Dispatcher::$folder_configs		. DS;
		self::$library		= self::$system			. Dispatcher::$folder_library		. DS;
		self::$application	= self::$system			. Dispatcher::$folder_application	. DS;
		
		self::$app_atoms	= self::$application	. self::$app_atoms					. DS;
		self::$app_configs	= self::$application	. self::$app_configs				. DS;
		self::$app_locales	= self::$application	. self::$app_locales				. DS;
		self::$app_models	= self::$application	. self::$app_models					. DS;
		
	}
	
	public static function set_atom($atom)
	{
		$atom = self::$app_atoms . $atom . DS;
		self::$atom_configs			= $atom . self::$atom_configs		. DS;
		self::$atom_controllers		= $atom . self::$atom_controllers	. DS;
		self::$atom_models			= $atom . self::$atom_models		. DS;
		self::$atom_library			= $atom . self::$atom_library		. DS;
		self::$atom_views			= $atom . self::$atom_views			. DS;
		self::$atom_temp			= $atom . self::$atom_temp			. DS;
	}
	
	public static function glob($path)
	{
		$glob = [];
		foreach(glob($path . '*', GLOB_ONLYDIR) as $dir)
		{
			$dir = explode('/', $dir);
			$glob[] = end($dir);
		}
		
		return $glob;
	}
}

Paths_Config::load();

?>
