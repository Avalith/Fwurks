<?php

class AdminUser extends ActiveRecord 
{
	protected $has_i18n = false;
	
	protected $belongs_to = array('admin_group');
	
	private $permissions;
	
	public function __construct($id = null)
	{
		parent::__construct($id);
		if($this->storage->id){ $this->reload_permissions(); }
	}
	
	public function loadSessionUser($user, $pass)
	{
		return $this->find_by_username_and_password_and_active($user, md5($pass), 1);
	}
	
	public function can($action = null, $controller = null)
	{
		if		($this->permissions == 'all'){ return true; }
		else if	(!$this->permissions){ return false; }
		
		if(!$action && $controller){ $regexp = "(?:\||^)$controller{"; }
		else
		{
			if(!$action)		{$action 		= Registry()->controller->__action; }
			if(!$controller)	{ $controller 	= Registry()->controller->__controller; }
			
			$reg = '[a-z0-9_,]';
			$regexp = "(?:\||^)$controller({(?:$reg*($action)$reg*)?})";
		}
		
		$result = preg_match("/$regexp/ui", $this->permissions, $matches);
		
		if		(!count($matches))								{ return false; }
		else if	($matches[1] == '{}')							{ return true; }
		else if	($matches[2] == $action) 						{ return true; }
		else if	(!$action && $controller && count($matches))	{ return true; }

		return false;
	}
	
	public function reload_permissions()
	{
		$this->permissions = $this->storage->holder()->permissions;
	}
	
	
	protected function before_update_storage(&$attributes)
	{
		if(!$attributes['password'])
		{
			$this->dont_validate_fields[] = 'password';
			unset($attributes['password']);
		}
		else
		{
			$attributes['password'] = md5($attributes['password']);
			$attributes['password_confirm'] = md5($attributes['password_confirm']);
		}
	}
	
	protected function before_validation()
	{
		if(!is_email($this->storage->email)){ $this->add_error('email', 'email'); }
	}
}

?>
