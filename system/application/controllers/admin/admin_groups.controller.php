<?php

class AdminGroups_Controller extends Simple_Controller
{
	protected $_model 		= 'AdminGroup';
	protected $_columns		= array('title', 'active');
	protected $_actions 	= array( array( 'title' => 'default_group', 'class' => 'group', 'permission' => 'default_group' ) );
	protected $_user_can_do	= array('default_group' => null);
	protected $_order		= 'title ASC';
	
	protected function _after_load($model, $params)
	{
		$this->includeJavascript('admin_permissions');
		$this->permissions = $model->permissionsToArray();
	}
	
	public function default_group($params)
	{
		if(is_post() && ($g = (int)$_POST['group'])){ $this->settings->set('default_group', $g); }
		
		$groups = new AdminGroup();
		$this->data->groups = $groups->find_all();
	}
	
	public static function actions(){ return array('listing' => 'index', 'add', 'edit', 'delete', 'default_group'); }
}

?>
