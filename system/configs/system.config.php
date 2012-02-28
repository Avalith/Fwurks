<?php

final class SystemConfig
{
	/**
	 * Whether to display php error messages or not
	 * @var bool
	 */
	public static $show_errors = 1;
	
	/**
	 * Whether to display exception messages or not
	 * @var bool
	 */
	public static $show_exceptions = 1;
	
	
	/**
	 * Force recompilation of the templates on every refresh
	 * @var bool
	 */
	const FORCE_COMPILE = true;
	
	
	// Directories
	// with trailing slash
	public static $rootPath;
	public static $configsPath;
	public static $controllersPath;
	public static $modelsPath;
	public static $viewsPath;
	public static $localesPath;
	public static $tempPath;
	public static $alibraryPath;
	
	public static $admin_configsPath;
	public static $admin_controllersPath;
	public static $admin_modelsPath;
	public static $admin_viewsPath;
	public static $admin_localesPath;
	public static $admin_tempPath;
	public static $admin_alibraryPath;
	
	
	public static $libraryPath;
	public static $systemConfigsPath;
	
	public static $filesPath;
	
	// email regular expression
	public static $email_regexp = '^[a-z0-9-_]+(?:\.[a-z0-9-_]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9]{2,6}$';
	
	public static function load()
	{
		$sys = Dispatcher::$system;
		$app = Dispatcher::$application;
		$pub = Dispatcher::$public;
		$adm = Dispatcher::$admin_application;
		
		$folder_configs 				= '/configs/';
		$folder_controllers 			= '/controllers/';
		$folder_models 					= '/models/';
		$folder_views 					= '/views/';
		$folder_locales					= '/locales/';
		$folder_alibrary				= '/library/';
		$folder_temp 					= '/temp/';
		
		$root = self::$rootPath	 		= getcwd().'/';
		
		$_path = $root.$sys.'/'.$app;
		self::$configsPath 				= $_path.$folder_configs;
		self::$controllersPath			= $_path.$folder_controllers;
		self::$modelsPath 				= $_path.$folder_models;
		self::$viewsPath 				= $_path.$folder_views;
		self::$localesPath 				= $_path.$folder_locales;
		self::$alibraryPath 			= $_path.$folder_alibrary;
		self::$tempPath 				= $_path.$folder_temp;
		
		
		self::$libraryPath 				= $root.$sys.'/library/';
		self::$systemConfigsPath 		= $root.$sys.'/configs/';
		
		self::$filesPath				= $root.$pub.'/files/';
		
		
		if(!$adm){ return; }
		
		$_path = $root.$sys.'/'.$adm;
		self::$admin_configsPath 		= $_path.$folder_configs;
		self::$admin_controllersPath	= $_path.$folder_controllers;
		self::$admin_modelsPath 		= $_path.$folder_models;
		self::$admin_viewsPath 			= $_path.$folder_views;
		self::$admin_localesPath 		= $_path.$folder_locales;
		self::$admin_alibraryPath 		= $_path.$folder_alibrary;
		self::$admin_tempPath 			= $_path.$folder_temp;
		
		
		Dispatcher::$use_admin_configs 	&& self::$configsPath	= $_path.$folder_configs;
		Dispatcher::$use_admin_models 	&& self::$modelsPath	= $_path.$folder_models;
		Dispatcher::$use_admin_locales 	&& self::$localesPath	= $_path.$folder_locales;
		Dispatcher::$use_admin_alibrary	&& self::$alibraryPath	= $_path.$folder_alibrary;
	}
}
SystemConfig::load();

?>
