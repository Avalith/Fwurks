<?php

// literal
// :variable
// :?optional
// :variable~regexp~
// :?optional~regexp~

/**
 * Routes Map
 */
class Routes_Config 
{
	public $custom	= array( 'alabala/:controller/qqq/:action/:?ddd~\d{4}-\d{4}~'		, array('action' => 'asd_{action}_{controller}') );
	
	
	public $default	= array( ':?controller/:?action/:?id'	 							, array('controller' => 'home', 'action' => 'index') );
}

?>
