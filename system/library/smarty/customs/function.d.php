<?php

function smarty_function_d($params, &$smarty)
{
	d($params['var'], $params['name']);
	if($params['exit']){ exit; }
}

?>
