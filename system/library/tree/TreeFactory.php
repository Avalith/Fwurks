<?php

final class TreeFactory
{
	static function create($type, $model)
	{
		$adapter = $type.'Tree';
		
		require_once __DIR__.'/Interfaces.php';
		require_once __DIR__.'/Abstractions.php';
		require_once __DIR__.'/'.$adapter.'.php';
		
		return new $adapter($model);
	}
	
	private function __construct(){}
}

?>
