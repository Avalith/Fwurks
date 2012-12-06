<?php

namespace library;
use Paths_Config;

class AutoLoader
{
	public static function load($class_name)
	{
		if(substr($class_name, 0, 8) == 'library\\')
		{
			$class_file = Paths_Config::$library . str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 8)) . '.php';
		}
		else if(substr($class_name, -11) == '_Controller')
		{
			$class_file = Paths_Config::$atom_controllers . $path . Inflector::to_file($class_name) . '.php';
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
		
		if(file_exists($class_file)){ require $class_file; }
	}
}

?>
