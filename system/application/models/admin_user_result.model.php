<?php

class AdminUserResult extends ORMResult
{
	public $_groups;
	
	public $permissions;
	
	
	public function load_group()
	{
		$this->_groups = AdminGroup::find_all("admin_user_id=$this->id AND active=1", 'permissions != "all"', null, array
		(
			array('model' => 'AdminGroupsAdminUsers', 'type' => 'LEFT', 'on' => 'admin_groups_admin_users.admin_group_id = admin_groups.id')
		));
		
		$permissions = array();
		foreach($this->_groups as $g){ $permissions[] .= $g->permissions; if($g->permissions == 'all'){ break; } }
		$this->permissions = implode('|', $permissions);
	}
	
	public function can($action = null, $controller = null)
	{
		$permissions = $this->permissions;
		
		if		($permissions == 'all'){ return true; }
		else if	(!$permissions){ return false; }
		
		if(!$action && $controller){ $regexp = "(?:\||^)$controller{"; }
		else
		{
			if(!$action)		{ $action 		= Registry::$controller->__action; }
			if(!$controller)	{ $controller 	= Registry::$controller->__controller; }
			
			$reg = '[a-z0-9_,]*';
			$regexp = "(?:\||^)$controller({(?:$reg($action)$reg)?})";
		}
		
		$result = preg_match("/$regexp/ui", $permissions, $matches);
		
		if		(!count($matches))								{ return false; }
		else if	($matches[1] == '{}')							{ return true; }
		else if	($matches[2] == $action) 						{ return true; }
		else if	(!$action && $controller && count($matches))	{ return true; }
		
		return false;
	}
}

?>