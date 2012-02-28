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
	}
}
SystemConfig::load();

?>
