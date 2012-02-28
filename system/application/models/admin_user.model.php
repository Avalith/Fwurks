<?php

class AdminUser extends ActiveRecordFile
{
	protected static $result_class = 'AdminUserResult';
	
	protected static function __columnDefinitions()
	{
		return array
		(
			ORMColumn()				->ID(),
			ORMColumn('username')	->string(100)->unique()->index('user_pass'),
			ORMColumn()				->password()->requiered()->index('user_pass'),
			ORMColumn('email')		->string(100)->unique(),
			ORMColumn('is_root')	->boolean(0)->index(),
			ORMColumn()				->Active(),
			ORMColumn('_editable')	->boolean(1)->index('edit_delete'),
			ORMColumn('_deletable')	->boolean(1)->index('edit_delete'),
		);
	}
	
	protected function __relations()
	{
		return array
		(
			ORMRelation()->hasMany('AdminGroups')->through('AdminGroupsAdminUsers'),
		);
	} 
	
	
	
	public static function loadSessionUser($user, $pass)
	{
		$result = self::find_by_username_and_password_and_active($user, self::hashPass($pass), 1)->result();
		return $result[0];
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
		$this->storage->password = $this->storage->password ? self::hashPass($this->storage->password) : $this->old_storage->password;
	}
	
	public static function hashPass($pass)
	{
		return hash('sha512', hash('sha512', strrev($pass)).$pass);
	}
}

?>
