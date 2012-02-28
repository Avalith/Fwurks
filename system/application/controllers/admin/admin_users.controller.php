<?php

class AdminUsers_Controller extends Simple_Controller 
{
	protected $_model		= 'AdminUser';
	protected $_columns 	= array('username' => array('function' => 'reserved_title'), 'email', 'admin_groups', 'active');
	protected $_order		= 'admin_groups IS NULL, admin_groups, username ASC';
	protected $_selector	= 'id, username, email, active, 
								(SELECT GROUP_CONCAT(title SEPARATOR ", ") 
								FROM admin_groups_admin_users 
								LEFT JOIN admin_groups_i18n 
									ON admin_groups_i18n.i18n_foreign_key = admin_groups_admin_users.admin_group_id
									AND admin_groups_i18n.i18n_locale = "en_EN" 
								WHERE admin_groups_admin_users.admin_user_id = admin_users.id) as admin_groups';
	
	public function profile()
	{
		$model = new AdminUser($this->__session->logged_user->id);
		if(is_post())
		{
			$model->auto_many_to_many = false;
			$_POST['active'] = $model->active;
			unset($_POST['admin_groups'], $_POST['username']);
			if(!$model->save($_POST)){ $this->errors = $model->errors; }
			else
			{
				$this->__session->logged_user = AdminUser::find_by_id_and_active($this->__session->logged_user->id, 1);
				redirect('//administration/profile/');
			}
		}
		
		$this->data = $model();
		$this->data->load_group();
	}
}

?>
