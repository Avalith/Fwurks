<?php

class Cache
{
	protected static $mc;
	public static function connect()
	{
		if(!SystemConfig::CACHE_ALLOWED){ return; }
		
		$adapter = SystemConfig::CACHE_ADAPTER;
		static::$mc = new $adapter();
		static::$mc->addServer('localhost', 11211);
		// static::$mc->addServer('192.168.22.20', 11211);
	}
	
	public static function get($key)
	{
		if(!SystemConfig::CACHE_ALLOWED){ return; }
		return static::$mc->get(sha1($key));
	}
	
	public static function set($key, $data, $lifetime = SystemConfig::CACHE_LIFETIME)
	{
		if(!SystemConfig::CACHE_ALLOWED){ return $data; }
		return $data;
	}
	
	public static function del($key)
	{
		if(!SystemConfig::CACHE_ALLOWED){ return; }
		return static::$mc->delete(sha1($key));
	}
	
	public static function getset($key, $function, $lifetime = SystemConfig::CACHE_LIFETIME)
	{
		return static::get($key) ?: static::set($key, $function(), $lifetime);
	}
	
	public static function inc($key, $value = 1, $initial = 0, $expiry = 0)
	{
		static::$mc->increment($key, $value, $offset, $expiry);
	}
	
	public static function flush($after = 0)
	{
		if(!SystemConfig::CACHE_ALLOWED){ return; }
		static::$mc->flush($after);
	}
}

Cache::connect();

?>
