<?php

class Smarty_Function_D
{
	public static function execute($params)
	{
		d($params['var'], $params['name']);
		if($params['exit']){ exit; }
	}
}



?>
