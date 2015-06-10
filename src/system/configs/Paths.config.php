<?php

// Directories
// with trailing slash
final class Paths_Config
{
	public static $_app_atoms			= 'atoms';
	
	public static $_app_configs			= 'configs';
	public static $_app_controllers		= 'controllers';
	public static $_app_locales			= 'locales';
	public static $_app_models			= 'models';
	
	public static $_atom_configs		= 'configs';
	public static $_atom_controllers	= 'controllers';
	public static $_atom_library		= 'locales';
	public static $_atom_models			= 'models';
	public static $_atom_temp			= 'temp';
	public static $_atom_views			= 'views';
	
	
	public static $base;
	public static $root;
	
	public static $resources;
	public static $system;
	
	public static $application;
	public static $configs;
	public static $library;
	public static $temp;
	
	public static $app_atoms;
	public static $app_configs;
	public static $app_controllers;
	public static $app_locales;
	public static $app_models;
	
	public static $current_atom;
	
	public static $atom_configs;
	public static $atom_controllers;
	public static $atom_library;
	public static $atom_models;
	public static $atom_temp;
	public static $atom_views;
	
	public static function load()
	{
		self::$base = Dispatcher::$url_base;
		self::$root = Dispatcher::$cwd . DS;
		
		self::$resources		= self::$root			. Dispatcher::$folder_resources		. DS;
		self::$system			= self::$root			. Dispatcher::$folder_system		. DS;
		
		self::$application		= self::$system			. Dispatcher::$folder_application	. DS;
		self::$configs			= self::$system			. Dispatcher::$folder_configs		. DS;
		self::$library			= self::$system			. Dispatcher::$folder_library		. DS;
		self::$temp				= self::$system			. Dispatcher::$folder_temp			. DS;
		
		self::$app_atoms		= self::$application	. self::$_app_atoms			. DS;
		self::$app_configs		= self::$application	. self::$_app_configs		. DS;
		self::$app_controllers	= self::$application	. self::$_app_controllers	. DS;
		self::$app_locales		= self::$application	. self::$_app_locales		. DS;
		self::$app_models		= self::$application	. self::$_app_models		. DS;
	}
	
	public static function set_atom($atom)
	{
		self::$current_atom = $atom;
		$atom = self::$app_atoms . $atom . DS;
		
		self::$atom_configs			= $atom . self::$_atom_configs		. DS;
		self::$atom_controllers		= $atom . self::$_atom_controllers	. DS;
		self::$atom_library			= $atom . self::$_atom_library		. DS;
		self::$atom_models			= $atom . self::$_atom_models		. DS;
		self::$atom_temp			= $atom . self::$_atom_temp			. DS;
		self::$atom_views			= $atom . self::$_atom_views		. DS;
	}
	
	public static function glob($path)
	{
		# TODO should this be done with a glob or config?
		
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
