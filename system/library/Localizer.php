<?php

final class Localizer
{
	private static $_locales = array();
	private static $_cache = array();
	
	private static $_compiles_folder = 'locales_compile';
	
	public static function getLocales()
	{
		if(!self::$_locales)
		{
			foreach(Application_Config::$installed_locales as $locale)
			{
				self::$_locales[$locale] = self::load('info', $locale);
			}
		}
		return self::$_locales;
	}
	
	public static function load($file, $locale = null)
	{
		$locale || $locale = Registry::$locales->current['code'];
		
		$cacheKey = $locale.'/'.$file;
		if( !isset(self::$_cache[$cacheKey]))
		{
			return self::$_cache[$cacheKey] = self::load_file($cacheKey);
		}
		return self::$_cache[$cacheKey];
	}
	
	protected static function load_file($path)
	{
		$full_path = SystemConfig::$localesPath.$path.'.yaml';
		$compile_path = SystemConfig::$tempPath.self::$_compiles_folder.'/'.strtr($path, array('/' => '_')).'.php';
		
		$contents = array();
		if(file_exists($full_path))
		{
			if(file_exists($compile_path) && filemtime($compile_path) >= filemtime($full_path))
			{
				$contents = include $compile_path;
			}
			else
			{
				$contents = Spyc::YAMLLoad(file_get_contents($full_path));
				file_put_contents($compile_path, '<?php class_exists(Dispatcher) || exit; return '.var_export($contents, 1).'; ?>');
			}
			
		}
		return $contents;
	}
	
	public static function getGlobals($locale = null)
	{
		return self::load('_globals', $locale);
	}

	
	private function __construct(){}
}

?>
