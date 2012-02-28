<?php

require_once __DIR__.'/Spyc.php';

class YAML
{
	private static $_compiles_folder = 'yaml_compile';
	
	public static function load_file($path, $file)
	{
		$path .= '.yaml';
		$compile_path = SystemConfig::$tempPath.self::$_compiles_folder.'/'.strtr($file, array('/' => '_')).'.php';
		
		if(file_exists($path))
		{
			
			if(file_exists($compile_path) && filemtime($compile_path) >= filemtime($path))
			{
				$contents = include $compile_path;
			}
			else
			{
				$contents = Spyc::YAMLLoad(file_get_contents($path));
				file_put_contents($compile_path, '<?php class_exists(\'Dispatcher\') || exit; return '.var_export($contents, 1).'; ?>');
			}
		}
		else
		{
			$contents = new StdClass();
		}
		
		return (object)$contents;
	}
	
	private function __construct(){}
}

?>
