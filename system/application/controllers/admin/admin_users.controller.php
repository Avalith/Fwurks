<?php

class AdminUsers_Controller extends Simple_Controller 
{
	protected $_model		= 'AdminUser';
	protected $_columns 	= array('username' => array('function' => 'reserved_title'), 'email', 'admin_group_title', 'active');
	protected $_joins 		= array(array('model' => 'AdminGroup', 'type' => 'LEFT'));
	protected $_order		= 'admin_group_title ASC, username ASC';
	
	
	protected function _load()
	{
		$groups = new AdminGroup();
		$this->data->groups = $groups->find_all();
	}
		
	public function profile()
	{
		$model = new AdminUser($this->__session->logged_user->id);
		if(is_post())
		{
			$_POST['active'] = $model->active;
			if(!$model->save($_POST)){ $this->errors = $model->errors; }
			else
			{
				$this->__session->logged_user = $model->find_by_id_and_active($this->__session->logged_user->id, 1);
				redirect('//administration/profile/');
			}
		}
		
		$this->data = $model->get_storage();
	}
	
	public static function actions(){ return array('listing' => 'index', 'add', 'edit', 'delete'); }
}

?>
