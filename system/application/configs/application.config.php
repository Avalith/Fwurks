<?php

final class Application_Config
{
	public static $admin_path = 'admin';
	
	// Locale settings
	public static $single_locale = true;
	public static $installed_locales = array('en');
	public static $defaultLocale = 'en';
	public static $has_mirror = false;
	public static $has_i18n = true;
	
	// Name Nameston <name.nameston@example.com>
	public static $system_email = "Alexander Ivanov <a.ivanov@antipodes.bg>";
	
	
	const FE_RECORDS_PER_PAGE = 20;
	
	public static function load()
	{
		
	}
}
Application_Config::load();

?>
