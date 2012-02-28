<?php

class AdminSettings extends ActiveRecord 
{
	protected $has_i18n = false;
	public $primary_key = 'name';

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
		$this->update($name, array('value' => $value));
	}
}

?>