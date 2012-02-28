<?php

class AutoLoader
{
	protected static $library = array
	(
		'SessionFactory'		=> '/session/',
		'DataBase' 				=> '/database/',
		'ModelSynchronizer' 	=> '/database/',
		'ORMView' 				=> '/database/orm/',
		'ActiveRecord' 			=> '/database/orm/',
		'ActiveRecordFile'		=> '/database/orm/',
		
		'Email'					=> '/',
		'Exceptions'			=> '/',
		'File'					=> '/file/',
		'HttpRequest'			=> '/',
		'Paging'				=> '/',
		'Profiler'				=> '/',
		'SimpleCrypt'			=> '/crypt/',
		'TreeFactory'			=> '/tree/',
		'UploadedFile'			=> '/file/',
	);
	
	public static function load($class_name)
	{
		static $admin_or_public;
		
		$models_path = SystemConfig::$modelsPath;
		
		if(isset(self::$library[$class_name]))
		{
			$class_file = __DIR__ . self::$library[$class_name] . $class_name.'.php';
		}
		else if(substr($class_name, -11) == '_Controller')
		{
			$admin_or_public || $admin_or_public = strtolower(Router::admin_or_public());
			$path = $class_name == 'Application_Controller' ? '' : $admin_or_public.'/';
			
			$class_file = SystemConfig::$controllersPath . $path . Inflector::to_file($class_name) . '.php';
		}
		else if(substr($class_name, -7) == '_Config')
		{
			$class_file = SystemConfig::$configsPath . Inflector::to_file($class_name) . '.php';
		}
		else
		{
			file_exists($class_file = SystemConfig::$alibraryPath.$class_name . '.php') || file_exists($class_file = $models_path . Inflector::to_file($class_name) . '.model.php') || $class_file = null;
		}
		
		if($class_file){ require_once $class_file; }
	}
	
}

?>