<?php

namespace library\session;

use Session_Config;

class Session
{
	public static function init()
	{
		static::cookie_params();
		static::handler();
		static::name(Session_Config::$name);
		static::start();
	}
	
	protected static function cookie_params()
	{
		call_user_func_array('session_set_cookie_params', Session_Config::$cookie);
	}
	
	protected static function handler()
	{
		if(Session_Config::$handler)
		{
			$handler = 'Handler' . Session_Config::$handler;
			session_set_save_handler(new $handler, true);
		}
	}
	
	protected static function name()
	{
		session_name(Session_Config::$name);
	}
	
	public static function id($id = null)
	{
		session_id($id);
	}
	
	public static function get($key)
	{
		return $_SESSION[$key];
	}
	
	public static function set($key, $value = null)
	{
		return $_SESSION[$key] = $value;
	}
	
	public static function del($key)
	{
		unset($_SESSION[$key]);
	}
	
	public static function start()
	{
		session_start();
	}
	
	public static function destroy()
	{
		session_destroy();
	}
}

Session::init();

?>