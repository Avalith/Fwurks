<?php

final class Dispatcher
{
	/**
	 * The name of your public folder containing styles, images, javascripts and other files
	 * @default: public
	 */
	public static $public = 'public';
	
	/**
	 * The name of your back-end folder containing styles, images and javascripts
	 * @default: back
	 */
	public static $admin = 'back';
	
	
	/**
	 * The name of your system folder
	 * @default: system
	 */
	public static $system = 'system';
	
	/**
	 * The name of your application folder inside the system folder
	 * @default: application
	 */
	public static $application = 'application';
	
	/**
	 * The name of your administration application folder inside the system folder
	 * If set the framework will use it instead of the application folder set above
	 * @default: null
	 */
	//public static $admin_application 	= 'administration';
	public static $admin_application 	= null;
	
	/**
	 * Loads the System
	 */
	public static function load()
	{
		Registry::$db = DatabaseFactory::create();
		Router::recognize();
	}
}


require_once Dispatcher::$system . '/library/Core.php';

Dispatcher::load();
//try { Dispatcher::load(); } catch(Exception $e){ if(SystemConfig::$show_exceptions){ $e->showTrace(); } }

?>
