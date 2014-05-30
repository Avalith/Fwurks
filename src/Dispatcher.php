<?php

class Dispatcher
{
	/**
	 * The base url to the framework
	 * @default: '/'
	 */
	public static $url_base = '/';
	
	/**
	 * The name of your system folder
	 * @default: 'system'
	 */
	public static $folder_system = 'system';
	
	/**
	 * The name of your application folder inside the system folder
	 * @default: 'application'
	 */
	public static $folder_application = 'application';
	
	/**
	 * The name of your configs folder inside the system folder
	 * @default: 'configs'
	 */
	public static $folder_configs = 'configs';
	
	/**
	 * The name of your library folder inside the system folder
	 * @default: 'library'
	 */
	public static $folder_library = 'library';
	
	/**
	 * The name of your resource folder
	 * @default: 'resources'
	 */
	public static $folder_resources = 'resources';
	
	
	/**
	 * The name of your resource folder
	 * @default: 'resources'
	 */
	public static $cwd = __DIR__;
	
	
	public static function load()
	{
		library\Router::start();
	}
}

require_once Dispatcher::$folder_system . '/' . Dispatcher::$folder_library . '/Core.php';

?>