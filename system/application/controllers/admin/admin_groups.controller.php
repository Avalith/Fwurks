<?php

class AdminGroups_Controller extends Simple_Controller
{
	protected $_model 		= 'AdminGroup';
	
	protected $_check_editable 	= true;
	protected $_check_deletable 	= true;
	
	protected function _listing_params()
	{
		return array
		(
			'conditions'		=> $this->user->is_root ? '' : 'admin_groups.is_root = 0',
			'columns' 			=> array('title', 'active'),
			'order'				=> 'title ASC',
			
		//	'actions' 			=> array( array('title' => 'default_group', 'class' => 'group', 'permission' => 'default_group') ),
		//	'user_can' 			=> array('default_group' => null),
		);
	}
	
	
	protected function _after_load($model, $params)
	{
		$this->includeJavascript('admin_permissions');
		$this->permissions = $model->permissionsToArray();
	}
	
	
	public function default_group()
	{
		if($this->is_post() && ($g = (int)$_POST['group'])){ $this->settings->set('default_group', $g); }
		
		$groups = new AdminGroup();
		$this->data->groups = $groups->get_all('is_root = 0', 'title');
	}
	
	
//	public static function actions(){ return array('listing' => 'index', 'add', 'edit', 'delete', 'default_group'); }
}

?>
