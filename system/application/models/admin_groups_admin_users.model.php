<?php

class AdminGroupsAdminUsers extends ActiveRecord
{
	protected static function __columnDefinitions()
	{
		return array
		(
			ORMColumn('admin_group_id')	->primary()->foreign_key('admin_groups_admin_users__admin_group_id', 'AdminGroup', 'id'),
			ORMColumn('admin_user_id') 	->primary()->foreign_key('admin_groups_admin_users__admin_user_id', 'AdminUser', 'id')->index(),
		);
	}
	
}

?>
