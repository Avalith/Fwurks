<?php

require_once __DIR__.'/yaml/YAML.php';

final class Localizer extends YAML
{
	private static $_locales = array();
	private static $_cache = array();
	
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
		$locale || $locale = Registry::$locales->current->code;
		
		$cacheKey = $locale.'/'.$file;
		if( !isset(self::$_cache[$cacheKey]))
		{
			return self::$_cache[$cacheKey] = self::load_file(SystemConfig::$localesPath.$cacheKey, $cacheKey);
		}
		return self::$_cache[$cacheKey];
	}
	
	public static function getGlobals($locale = null)
	{
		return self::load('_globals', $locale);
	}

	
	private function __construct(){}
}

?>
