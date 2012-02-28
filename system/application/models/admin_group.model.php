<?php

class AdminGroup extends ActiveRecord
{
	protected $many_to_many = array('admin_users' => 'AdminGroupsAdminUsers');
	
	protected static $has_i18n 		= true;
	protected static $has_mirror 	= true;
	
	protected function before_validation()
	{
		$p = &$this->storage->permissions;
		
		$p['admin_users'] || $p['admin_users'] = array();
		in_array('profile', $p['admin_users']) || $p['admin_users'][] = 'profile';
		
		$p = $this->arrayToPermissions();
		if($p == $this->arrayToPermissions($this->getAllPermissions())){ $p = 'all'; }
	}


	public function arrayToPermissions(array $array = array())
	{
		$array || $array = is_array($this->storage->permissions) ? $this->storage->permissions : $this->permissionsToArray($this->storage->permissions);
		
		foreach($array as $c => &$p){ $p = $c . '{' . (is_array($p) ? implode(',', $p) : '') . '}'; }
		return implode('|', $array);
	}
	
	
	public function permissionsToArray($string = null)
	{
		$string === null && $string = $this->storage->permissions;
		
		$permissions = array();
		if($string && $string != 'all')
		{
			foreach(explode('|', $string) as $p)
			{
				$p = explode('{', $p);
				$p[1] = rtrim($p[1], '}');
				$permissions[$p[0]] = $p[1] ? explode(',', $p[1]) : 1;
			}
		}
		else if($string == 'all')
		{
			$permissions = $string;
		}
		$admin_menu = Spyc::YAMLLoad(file_get_contents(SystemConfig::$configsPath . 'admin_menu.yaml'));
		
		$this->traverse_menu($admin_menu, $permissions);
		
		$admin_menu['dashboard']['type'] = 'hidden';
		
		return $admin_menu;
	}


	private function traverse_menu(&$array, $permissions)
	{
		foreach($array as $k => &$v)
		{
			if($v['type'] == 'controller')
			{
				$v['controller'] = isset($v['real_controller']) ? $v['real_controller'] : (isset($v['controller']) ? $v['controller'] : $k);
				
				eval('$v["actions"] = ' . Inflector::classify($v['controller']) . '_Controller' . '::actions();');
				if(is_array($v['actions']))
				{
					$v['actions'] = array_flip($v['actions']);
					foreach($v['actions'] as $action => &$action_options)
					{
						$action_label = is_string($action_options) ? $action_options : $action;
						if(!($ao_title = Registry::$globals['ACTIONS'][$action_label])){ $ao_title = $action; }
						
						$permission_name = isset($v['permission_name']) ? $v['permission_name'] : $v['controller'];
						
						$ao_allowed = $permissions == 'all' || (isset($permissions[$permission_name]) && ($permissions[$permission_name] === 1 || in_array($action, $permissions[$permission_name]) ));
						$action_options = array('title' => $ao_title, 'allowed' => $ao_allowed, 'controller' => $permission_name, 'type' => 'action');
					}
				}
				else if((int)$v['actions']){ $v['type'] = 'hidden'; }
			}
			else if($v['type'] == 'action')
			{
				unset($array[$k]);
				continue;
			}
			
			if(is_array($v['menu']))
			{
				foreach($v['menu'] as $key => $val)
				{
					if(isset($val['real_controller']) && $val['real_controller'] != $key)
					{
						$val['label_name'] = $key;
						$v['menu'][$val['real_controller']] = $val;
						unset($v['menu'][$key]);
					}
					else
					{
						unset($v['menu'][$key]);
						$v['menu'][$key] = $val;
					}
				}
				
				$this->traverse_menu($v['menu'], $permissions);
			}
			
			if($v != 'separator' && $v['type'] != 'action' && !($v['title'] = Registry::$controller->admin_menu_locales[isset($v['label_name']) ? $v['label_name'] : $k])){ $v['title'] = $k; }
		}
	}
	
	private function getAllPermissions()
	{
		$admin_menu = Spyc::YAMLLoad(file_get_contents(SystemConfig::$configsPath . 'admin_menu.yaml'));
		$permissions = $this->traverse_permissions($admin_menu);
		
		array_push($permissions['admin_users'], 'profile');
		
		return $permissions;
	}
	
	private function traverse_permissions($array)
	{
		$permissions = array();
		foreach($array as $k => $v)
		{
			if($v['type'] == 'controller')
			{
				$controller = $v['real_controller'] ? $v['real_controller'] : ($v['controller'] ? $v['controller'] : $k);
				eval('$actions = ' . Inflector::classify($controller) . '_Controller' . '::actions();');
				$permissions[$controller] = $actions;
			}
			
			if($v['menu']){ $permissions = array_merge($permissions, $this->traverse_permissions($v['menu'])); }
		}
		return $permissions;
	}
}
?>
