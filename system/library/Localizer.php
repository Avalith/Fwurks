<?php

final class Localizer
{
	private static $_locales = array();
	private static $_cache = array();
	
	public static function getLocales()
	{
		if(!self::$_locales)
		{
			foreach(Application_Config::$installed_locales as $locale)
			{
				self::$_locales[$locale] = self::load($locale, 'info');
			}
		}
		return self::$_locales;
	}
	
	public static function load($locale, $file)
	{
		$cacheKey = $locale.'/'.$file;
		if( !self::$_cache[$cacheKey] && file_exists($file_path = SystemConfig::$localesPath.$cacheKey.'.yaml') )
		{
			return self::$_cache[$cacheKey] = Spyc::YAMLLoad(file_get_contents($file_path));
		}
		return self::$_cache[$cacheKey];
	}
	
	public static function getGlobals()
	{
		return self::load(Registry()->locale, '_globals');
	}

	
	private function __construct(){}
}

?>
