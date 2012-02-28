<?php

abstract class Session
{
	protected $id;
	
	protected $cache_expire;
	
	protected $name;
	
	protected $cookie = array
	(
		'lifetime' 	=> 0,
	    'path' 		=> '/',
	    'domain' 	=> null,
	    'secure' 	=> null,
	    'httponly' 	=> null
	);
	
	protected $storage;
	
	
	protected static $started = false;
	
	final public function __construct(array $params)
	{
		$this->cache_expire	= $params['cache_expire'];
		$this->name 		= $params['name'];
		$this->cookie 		= $params['cookie'];
	}
	
	
	final public function __get($variable)			{ return $this->storage[$variable]; }
	
	final public function __set($variable, $value)	{ return $this->storage[$variable] = $value; }
	
	final public function __isset($variable)		{ return isset($this->storage[$variable]); }
	
	final public function __unset($variable)		{ unset($this->storage[$variable]); }
	
	final public function storage()					{ return $this->storage; }
	
	
	final public function id(){ return $this->id; }
	final protected function generate_id() { return md5(uniqid(mt_rand(), true).$_SERVER['REMOTE_ADDR']); }
	
	
	abstract public function start();
	abstract public function end();
	abstract protected function load();
	abstract protected function save();
}


?>