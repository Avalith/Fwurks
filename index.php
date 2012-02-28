<?php 

final class Dispatcher
{
	/**
	 * The name of your public folder containing styles, images, javascripts and other files
	 * @default: public
	 */
	public static $public = 'public';
	
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
	 * Variables whether to use the admin_application's folders for configs, models, locale, application and library
	 *
	 * @var unknown_type
	 */
	public static $use_admin_configs 	= 1;
	public static $use_admin_models 	= 1;
	public static $use_admin_locales 	= 1;
	public static $use_admin_alibrary 	= 1;
	
	
	/**
	 * Loads the System
	 */
	public static function load()
	{
		require_once self::$system . '/library/Core.php';
		
		Registry()->db 		= DatabaseFactory::create();
		
		Router::recognize();
	}
}
try { Dispatcher::load(); } catch (Exception $e){ if(SystemConfig::$show_exceptions){ $e->showTrace(); } };


?>
