<?php

class AdminUser extends ActiveRecordFile
{
	protected static $has_i18n = false;
	
	protected static $result_class = 'AdminUserResult';
	
	protected $many_to_many = array('admin_groups' => 'AdminGroupsAdminUsers');
	
	public static function loadSessionUser($username, $pass)
	{
		return self::find_by_username_and_password_and_active($username, Password::hash('admin' . $pass, $username), 1);
	}

	protected function validate__email()
	{
		if(!is_email($this->email)){ $this->add_error('email', 'email'); }
	}
	
	protected function before_validation_on_update()
	{
		if(!($this->password || $this->password_confirm))
		{
			$this->dont_validate_fields[] = 'password';
		}
	}
	
	protected function before_save()
	{
		$this->storage->password = $this->password ? Password::hash('admin' . $this->password, $this->username) : $this->old_storage->password;
	}
}

?>
