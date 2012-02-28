<?php

class StandartSession extends Session
{
	public function start()
	{
		if(!self::$started)
		{
			session_name($this->name);
			session_cache_expire($this->ttl);
			call_user_func_array('session_set_cookie_params', $this->cookie);
			
			session_start();
			$this->id = session_id();
			
			$this->load();
						
			self::$started = true;
		}
	}
	
	public function close()
	{
		session_destroy();
	}
	
	protected function load()
	{
		$this->storage = &$_SESSION;
	}
	
	protected function save(){}
}

?>