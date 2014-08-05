<?php

// literal
// key:variable
// :variable
// :?optional
// :variable~regexp~
// :?optional~regexp~

// TODO literate expressions without regexp

/**
 * Routes Map
 */
class Routes_Config 
{
	public $home	= ['/'														, ['controller' => 'home', 'action' => 'index']];
	public $e404	= ['404'													, ['controller' => 'home', 'action' => '404']];
	
	// BEGIN Custom URLs
	
	public $custom	= ['alabala/:controller/qqq/:action/:?ddd~\d{4}-\d{4}~'		, ['action' => 'asd_{action}_{controller}']];
	
	// END Custom URLs
	
	public $default	= [':?controller/:?action/:?id'								, ['controller' => 'home', 'action' => 'index']];
}

?>
