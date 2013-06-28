<?php

class Cache
{
	// protected static $mc;
	// public static function connect()
	// {
	// 	if(!SystemConfig::CACHE_ALLOWED){ return; }
	// 	static::$mc = new Memcache(); 
	// 	static::$mc->addServer('192.168.22.20', 11211);
	// }
	
	// public static function get($name)
	// {
	// 	if(!SystemConfig::CACHE_ALLOWED){ return; }
	// 	return static::$mc->get($name);
	// }
	
	// public static function set($name, $data, $lifetime = SystemConfig::CACHE_LIFETIME)
	// {
	// 	if(!SystemConfig::CACHE_ALLOWED){ return; }
	// 	static::$mc->set($name, $data, MEMCACHE_COMPRESSED, $lifetime);
	// }
	
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
		
		d(static::$mc->getStats(), $key);
		// if(SystemConfig::CACHE_ADAPTER == 'memcache')
		// {
		// 	static::$mc->set($key, $data, MEMCACHE_COMPRESSED, $lifetime);
		// }
		// else if(SystemConfig::CACHE_ADAPTER == 'memcached')
		// {
			static::$mc->set(sha1($key), $data, $lifetime);
		// }
		
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
