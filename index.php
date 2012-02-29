<?php

final class Dispatcher
{
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
	
	public static function load()
	{
		Router::start();
	}
}


require_once Dispatcher::$folder_system . '/' . Dispatcher::$folder_library . '/Core.php';

Dispatcher::load();
//try { Dispatcher::load(); } catch(Exception $e){ if(SystemConfig::$show_exceptions){ d($e->getTraceAsString(), '<span style="color: #900;">'.$e->getMessage().'</span>', false, false); } }

?>
