<?php

// Directories
// with trailing slash
final class Paths_Config
{
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
	public static $atom_views			= 'models';
	
	
	public static function load()
	{
		self::$atom_configs			= self::$app_atoms . $atom . '/' . self::$atom_configs . '/';
		self::$atom_controllers		= self::$app_atoms . $atom . '/' . self::$atom_controllers . '/';
		self::$atom_library			= self::$app_atoms . $atom . '/' . self::$atom_library . '/';
		self::$atom_views			= self::$app_atoms . $atom . '/' . self::$atom_views . '/';
	}
	
	public static function set_atom($atom)
	{
	}
}

Paths_Config::load();

?>
