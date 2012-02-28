<?php

class AdminSettings extends ActiveRecord 
{
	public static $primary_keys = array('name');

	protected static function __columnDefinitions()
	{
		return array
		(
			ORMColumn('name')		->string()->primary(),
			ORMColumn('value')		->text()->requiered(),
			ORMColumn('comment')	->string()->requiered(),
		);
	}
	
	protected function __init()
	{
		foreach($this->get_all() as $s)
		{
			$this->storage->{$s->name} = $s->value;
		}
	}
	
	public function set($name, $value)
	{
		$this->storage->$name = $value;
		self::update(array('value' => $value), array('name = ?', $name))->run();
	}
}

?>
