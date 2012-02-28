<?php 

require_once __DIR__.'/adapters/Adapter.php';
require_once __DIR__.'/query/SqlBase.php';
require_once __DIR__.'/query/SqlQuery.php';
require_once __DIR__.'/query/SqlJoin.php';
require_once __DIR__.'/query/SqlTable.php';
require_once __DIR__.'/query/SqlColumn.php';

class DataBase
{
	public static function connect($type, $name, $user, $pass, $host = 'localhost', $port = 3306)
	{
		$file = __DIR__.'/adapters/'.$type.'.php';
		
		if(file_exists($file))
		{
			require_once $file;
			$class = $type.'_Adapter';
			return new $class($name, $user, $pass, $host, $port);
		}
		else
		{
			throw new SqlAdapterException('Adapter for $type does not exists.');
		}
	}
	
}

?>