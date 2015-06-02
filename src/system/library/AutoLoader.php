<?php

namespace library;
use Paths_Config;

class AutoLoader
{
	public static $cache_file;
	public static $cache_dirty = false;
	public static $cache;
	
	public static function get($class_name)
	{
		if(isset(self::$cache[$class_name])){ return self::$cache[$class_name]; }
	}
	
	public static function find($class_name)
	{
		if(substr($class_name, 0, 8) == 'library\\')
		{
			$class_file = Paths_Config::$library . str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 8)) . '.php';
		}
		else if(substr($class_name, -11) == '_Controller')
		{
			$filename = Inflector::to_file($class_name) . '.php';
			
				file_exists($class_file = Paths_Config::$atom_controllers	. $filename)
			||	file_exists($class_file = Paths_Config::$app_controllers	. $filename)
			||	$class_file = null;
		}
		else if(substr($class_name, -7) == '_Config')
		{
			$filename = Inflector::to_file($class_name) . '.php';
			
				file_exists($class_file = Paths_Config::$atom_configs	. $filename)
			||	file_exists($class_file = Paths_Config::$app_configs	. $filename)
			||	file_exists($class_file = Paths_Config::$configs		. $filename)
			||	$class_file = null;
		}
		else
		{
				file_exists($class_file = Paths_Config::$atom_library . $class_name . '.php')
			||	file_exists($class_file = Paths_Config::$atom_models . Inflector::to_file($class_name) . '.model.php')
			||	file_exists($class_file = Paths_Config::$app_models . Inflector::to_file($class_name) . '.model.php')
			||	$class_file = null;
		}
		
		return $class_file;
	}
	
	
	public static function load($class_name)
	{
		if($class_file = self::get($class_name))
		{
			require $class_file;
		}
		else if(file_exists($class_file = self::find($class_name)))
		{
			self::$cache_dirty = true;
			self::$cache[$class_name] = $class_file;
			
			require $class_file;
		}
	}
	
	public static function save()
	{
		self::$cache_dirty && file_put_contents(self::$cache_file, '<?php return ' . var_export(self::$cache, true) . '; ?>');
	}
	
	public static function init()
	{
		self::$cache_file	= Paths_Config::$temp . 'autoloader.cache.php';
		self::$cache		= include_once self::$cache_file;
		
		register_shutdown_function(function(){ self::save(); });
	}
};

AutoLoader::init();


?>
