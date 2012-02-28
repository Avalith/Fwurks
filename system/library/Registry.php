<?php

final class Registry
{
	/**
	 * Locale information
	 * 
	 * @var Array 
	 */
	public static $locales;
	
	public static $single_locale;
	
	/**
	 * Global locale labels
	 */
	public static $globals;
	
	/**
	 * Controller's locale labels
	 */
	public static $labels;
	
	/**
	 * Determines whether the user is in the back-end or no
	 * 
	 * @var Boolean 
	 */
	public static $is_admin;
	
	/**
	 * Controller instance
	 */
	public static $controller;
	
	/**
	 * Session instance
	 */
	public static $session;
	
	/**
	 * Database instance
	 */
	public static $db;
	
	/**
	 * Params extracted from the url
	 */
	public static $action_params;
	
	/*
	 * ===========================
	 * BackEnd Variables
	 * ===========================
	 */
	public static $settings;
	
	
	/**
	 * Temporal Storage
	 */
	public static $storage;
	
	public static function get($variable)			{ return self::$storage->$variable; }
	public static function set($variable, $value)	{ return self::$storage->$variable = $value; }
	public static function _isset($variable)		{ return isset(self::$storage->$variable); }
	public static function _unset($variable)		{ unset(self::$storage->$variable); }
	
	
	
	private function __construct(){}
	
	public static function __initialize()
	{
		self::$locales = (object)array('info' => array(), 'current' => array());
	}
}
Registry::__initialize();

?>