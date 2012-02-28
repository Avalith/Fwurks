<?php

abstract class ORMView extends ORM
{
	final public static function definition()
	{
		static $views = array();
		
		$table = static::table_name();
		isset($views[$table]) || $views[$table] = array();
		
		if(!$views[$table])
		{
			$views[$table] = static::__definition();
		}
		
		return $views[$table];
	}
	
	abstract protected static function __definition();
	
	
	final protected static function __columnDefinitions(){ return array(); }
	
}

ORM::__initialize();

?>
