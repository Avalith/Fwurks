<?php

class AdminUserResult extends ORMResult
{
	public $permissions;
	
	public function load_group()
	{
		$permissions = array();
		foreach($this->AdminGroups() as $g){ $permissions[] .= $g->permissions; if($g->permissions == 'all'){ break; } }
		$this->permissions = implode('|', $permissions);
	}
	
	public function can($action = null, $controller = null)
	{
		$this->permissions || $this->load_group();
		
		if		($this->permissions == 'all'){ return true; }
		else if	(!$this->permissions){ return false; }
		
		if(!$action && $controller){ $regexp = "(?:\||^)$controller{"; }
		else
		{
			if(!$action)		{ $action 		= Registry::$controller->__action; }
			if(!$controller)	{ $controller 	= Registry::$controller->__controller; }
			
			$reg = '[a-z0-9_,]*';
			$regexp = "(?:\||^)$controller({(?:$reg($action)$reg)?})";
		}
		
		$result = preg_match("/$regexp/ui", $this->permissions, $matches);
		
		if		(!count($matches))								{ return false; }
		else if	($matches[1] == '{}')							{ return true; }
		else if	($matches[2] == $action) 						{ return true; }
		else if	(!$action && $controller && count($matches))	{ return true; }
		
		return false;
	}
}

?>