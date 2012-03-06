<?php

// Directories
// with trailing slash
final class Paths_Config
{
	/**
	 * @default: '/'
	 */
	public static $base = '/fwurks/';
	
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
	public static $atom_library			= 'locales';
	public static $atom_views			= 'views';
	public static $atom_temp			= 'temp';
	
	
	public static function load()
	{
		self::$root			= getcwd() . '/';
		
		self::$system		= self::$root			. Dispatcher::$folder_system		. '/';
		self::$resources	= self::$root			. Dispatcher::$folder_resources		. '/';
		
		self::$configs		= self::$system			. Dispatcher::$folder_configs		. '/';
		self::$library		= self::$system			. Dispatcher::$folder_library		. '/';
		self::$application	= self::$system			. Dispatcher::$folder_application	. '/';
		
		self::$app_atoms	= self::$application	. self::$app_atoms					. '/';
		self::$app_configs	= self::$application	. self::$app_configs				. '/';
		self::$app_locales	= self::$application	. self::$app_locales				. '/';
		self::$app_models	= self::$application	. self::$app_models					. '/';
		
	}
	
	public static function set_atom($atom)
	{
		$atom = self::$app_atoms . $atom . '/';
		self::$atom_configs			= $atom . self::$atom_configs		. '/';
		self::$atom_controllers		= $atom . self::$atom_controllers	. '/';
		self::$atom_library			= $atom . self::$atom_library		. '/';
		self::$atom_views			= $atom . self::$atom_views			. '/';
		self::$atom_temp			= $atom . self::$atom_temp			. '/';
	}
}

Paths_Config::load();

?>
