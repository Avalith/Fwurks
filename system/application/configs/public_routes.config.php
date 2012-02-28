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
	public $error404 	= array( '404' 													, array(':controller' => 'home', ':action' => 'error404') );
//	public $pages 		= array( '*'													, array(':controller' => 'pages', ':action' => 'index') );
	
	public $home 		= array( '' 													, array(':controller' => 'home', ':action' => 'index') );
	public $index 		= array( ':controller' 											, array(':action' => 'index') );
	public $default 	= array( ':controller/:action/:id' 								, '' );
}

?>
