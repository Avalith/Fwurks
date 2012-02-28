<?php

/**
 * Routes Map
 */
class PublicRoutes_Config
{
	/**
	 * Custom Mapping
	 */	
	
	
	/**
	 * Default Mapping
	 */
	public $erorr404	= array( 'error404|404' 						, array(':controller' => 'home', ':action' => 'error404') );
	public $pages 		= array( 'pages/*'								, array(':controller' => 'pages', ':action' => 'index') );
	
	public $home 		= array( '' 									, array(':controller' => 'home', ':action' => 'index') );
	public $index 		= array( ':controller' 							, array(':action' => 'index') );
	public $default 	= array( ':controller/:action'	 				, array() );
	public $default_id 	= array( ':controller/:action/:id' 				, array() );
}

?>
