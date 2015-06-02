<?php

function smarty_function_route($params, $smarty)
{
	$query = $params['query'] ?: array();
	unset($params['query']);
	
	$route = $params['key'] ? route($params['key']) : $smarty->getTemplateVars('__request')->route;
	
	return $route->url($params, $query);
}

?>