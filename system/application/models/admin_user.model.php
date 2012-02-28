<?php

class AdminUser extends ActiveRecordFile
{
	protected static $has_i18n = false;
	
	protected static $result_class = 'AdminUserResult';
	
	protected $many_to_many = array('admin_groups' => 'AdminGroupsAdminUsers');
	
	public static function loadSessionUser($user, $pass)
	{
		return self::find_by_username_and_password_and_active($user, md5($pass), 1);
	}

	protected function validate__email()
	{
		if(!is_email($this->storage->email)){ $this->add_error('email', 'email'); }
	}
	
	protected function before_validation_on_update()
	{
		if(!$this->storage->password && !$this->storage->password_confirm)
		{
			$this->dont_validate_fields[] = 'password';
		}
	}
	
	protected function before_save()
	{
		$this->storage->password = $this->storage->password ? md5($this->storage->password) : $this->old_storage->password;
	}
}

?>
