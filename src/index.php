<?php

require_once 'Dispatcher.php';

try
{
	Dispatcher::load();
}
catch(Exception $e)
{
	if(System_Config::PRODUCTION)
	{
		// REDIRECT 404
	}
	else
	{
		d(str_replace(getcwd(), '', $e->getTraceAsString()), '<span style="color: #900;">'.$e->getMessage().'</span><br />' . get_class($e) . ' in ' . str_replace(getcwd(), '', $e->getFile()) . ' (' . $e->getLine() . ')' . '</span>', false, false);
	}
}

?>
