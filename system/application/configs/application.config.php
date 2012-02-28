<?php

final class Application_Config
{
	public static $admin_path = 'admin';
	
	// Locale settings
	public static $installed_locales = array('en', 'bg');
	public static $default_locale = null;
	public static $has_i18n = false;
	public static $has_mirror = false;
	
	// Name Nameston <name.nameston@example.com>
	public static $system_email = "Name Nameston <name.nemston@mail.com>";
	
	
	const FE_RECORDS_PER_PAGE = 20;
	
	public static function load()
	{
		
	}
}
Application_Config::load();

?>
