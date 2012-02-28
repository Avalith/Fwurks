<?php

class StandartSession extends Session
{
	public function start()
	{
		if(!self::$started)
		{
			session_name($this->name);
			session_cache_expire($this->cache_expire);
			
			session_set_cookie_params
			(
				$this->cookie['lifetime'], 
				$this->cookie['path'], 
				$this->cookie['domain'], 
				$this->cookie['secure'], 
				$this->cookie['httponly']
			);
			
			session_start();
			$this->id = session_id();
			
			$this->load();
						
			self::$started = true;
		}
	}
	
	
	public function end()
	{
		$c = session_get_cookie_params();
		session_destroy();
		setcookie($this->name, 1, time()+10, $c['path'], $c['domain'], $c['secure'], $c['httponly']);
	}
	
	
	protected function load()
	{
		$this->storage = &$_SESSION;
	}
	
	
	protected function save(){}

}

?>