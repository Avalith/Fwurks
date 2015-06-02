<?php

require_once __DIR__.'/controls/Control.php';

function smarty_function_control($params, $smarty)
{
	$params['type'] || $params['type'] = 'text';
	$type = $params['type'];
	
	require_once __DIR__.'/controls/'.$type.'.php';
	
	$field_class = 'Control_'.$type;
	$field = new $field_class($params);
	
	return $field->html();
}

?>