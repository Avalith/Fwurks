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
		
		self::$root			= getcwd() . DIRECTORY_SEPARATOR;
		
		self::$system		= self::$root			. Dispatcher::$folder_system		. DIRECTORY_SEPARATOR;
		self::$resources	= self::$root			. Dispatcher::$folder_resources		. DIRECTORY_SEPARATOR;
		
		self::$configs		= self::$system			. Dispatcher::$folder_configs		. DIRECTORY_SEPARATOR;
		self::$library		= self::$system			. Dispatcher::$folder_library		. DIRECTORY_SEPARATOR;
		self::$application	= self::$system			. Dispatcher::$folder_application	. DIRECTORY_SEPARATOR;
		
		self::$app_atoms	= self::$application	. self::$app_atoms					. DIRECTORY_SEPARATOR;
		self::$app_configs	= self::$application	. self::$app_configs				. DIRECTORY_SEPARATOR;
		self::$app_locales	= self::$application	. self::$app_locales				. DIRECTORY_SEPARATOR;
		self::$app_models	= self::$application	. self::$app_models					. DIRECTORY_SEPARATOR;
		
	}
	
	public static function set_atom($atom)
	{
		$atom = self::$app_atoms . $atom . DIRECTORY_SEPARATOR;
		self::$atom_configs			= $atom . self::$atom_configs		. DIRECTORY_SEPARATOR;
		self::$atom_controllers		= $atom . self::$atom_controllers	. DIRECTORY_SEPARATOR;
		self::$atom_models			= $atom . self::$atom_models		. DIRECTORY_SEPARATOR;
		self::$atom_library			= $atom . self::$atom_library		. DIRECTORY_SEPARATOR;
		self::$atom_views			= $atom . self::$atom_views			. DIRECTORY_SEPARATOR;
		self::$atom_temp			= $atom . self::$atom_temp			. DIRECTORY_SEPARATOR;
	}
}

Paths_Config::load();

?>
