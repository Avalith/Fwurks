<?php

function smarty_function_d($params)
{
	d($params['var'], $params['name']);
	if($params['exit']){ exit; }
}


?>
