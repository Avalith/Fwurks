<?php

final class Session_Config
{
	public static $handler		= '';
	
	public static $name			= 'fwsess';
	
	public static $cookie		= array
	(
		'lifetime'	=> 0,
		'path'		=> '/',
		'domain'	=> null,
		'secure'	=> null,
		'httponly'	=> null
	);
}

?>
