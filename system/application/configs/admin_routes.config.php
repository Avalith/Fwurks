<?php

/**
 * Routes Map
 */
class AdminRoutes_Config
{
	/**
	 * Custom Mapping
	 */
	
	
	/**
	 * Default Mapping
	 */
	public $administration_profile 		= array( 'administration/profile' 					, array(':controller' => 'admin_users', ':action' => 'profile') );
	public $administration 				= array( 'administration/:controller' 				, array(':controller' => 'admin_$1') );
	public $administration_action 		= array( 'administration/:controller/:action' 		, array(':controller' => 'admin_$1') );
	public $administration_id			= array( 'administration/:controller/:action/:id' 	, array(':controller' => 'admin_$1') );
	public $logout 						= array( 'logout' 									, array(':controller' => 'login', ':action' => 'logout') );
	
	public $access_denied				= array( 'access_denied' 							, array(':controller' => 'dashboard', ':action' => 'access_denied') );
	public $error404 					= array( '404' 										, array(':controller' => 'dashboard', ':action' => 'error404') );
	public $home 						= array( '' 										, array(':controller' => 'dashboard') );
	public $index 						= array( ':controller' 								, array() );
	public $default 					= array( ':controller/:action/:id' 					, array() );
}
?>
