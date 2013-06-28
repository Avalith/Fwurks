<?php

class AdminSettings extends ActiveRecord 
{
	protected static $has_i18n = false;
	public static $primary_keys = array('name');

	protected function init()
	{
		foreach($this->find_all() as $s)
		{
			$this->storage->{$s->name} = $s->value;
		}
	}
	
	public function set($name, $value)
	{
		$this->storage->$name = $value;
		self::update(array($name), array('value' => $value));
	}
}

?>