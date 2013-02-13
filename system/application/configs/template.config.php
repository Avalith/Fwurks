<?php

final class Template_Config
{
	const COMPILE_CHECK			= true;
	const FORCE_COMPILE			= false;
	
	const ESCAPE_HTML			= true;
	
	// array of classes that are considered trusted. An empty array which allows access to all static classes. To disable access to all static classes set to null.
	static SECURITY_STATIC_CLASSES		= array();
	
	// array of PHP functions that are considered trusted and can be used from within template. To disable access to all PHP functions set to null. An empty array will allow all PHP functions.
	static SECURITY_PHP_FUNCTIONS		= array('isset', 'empty', 'count', 'sizeof', 'in_array', 'is_array','time','nl2br', 'de');
	
	// array of PHP functions that are considered trusted and can be used from within template as modifier. To disable access to all PHP modifier set to null. An empty array will allow all PHP functions.
	static SECURITY_PHP_MODIFIERS		= array('escape','count');
	
	// array of streams that are considered trusted and can be used from within template. To disable access to all streams set to null. An empty array will allow all streams.
	static SECURITY_STREAMS				= array('file');
	
	// array of (registered / autoloaded) modifiers that should be accessible to the template. If this array is non-empty, only the herein listed modifiers may be used. This is a whitelist.
	static SECURITY_ALLOWED_MODIFIERS	= array();
	
	// array of (registered / autoloaded) modifiers that may not be accessible to the template.
	static SECURITY_DISABLED_MODIFIERS	= array();
	
	// array of (registered / autoloaded) function-, block and filter plugins that should be accessible to the template. If this array is non-empty, only the herein listed function-, block and filter plugins may be used. This is a whitelist.
	static SECURITY_ALLOWED_TAGS		= array();
	
	// array of (registered / autoloaded) function-, block and filter plugins that may not be accessible to the template.
	static SECURITY_DISABLED_TAGS		= array();
	
	// boolean flag which controls if constants can be accessed by the template.
	const SECURITY_ALLOW_CONSTANTS		= false;
	
	// boolean flag which controls if the PHP super globals can be accessed by the template.
	const SECURITY_ALLOW_SUPER_GLOBALS	= false;
	
	// boolean flag which controls if {php} and {include_php} tags can be used by the template.
	const SECURITY_ALLOW_PHP_TAG		= false;
	
	public static function load()
	{
		
	}
}
Template_Config::load();

?>
