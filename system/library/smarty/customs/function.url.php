<?php
/*
class Smarty_Function_URL
{
	public function execute($params) 
	{
		$for = $params['for'];
		if(is_string($for))
		{
			return url_for($params['for']);
		}
		else if(is_array($for))
		{
			return route($params['route'], $for, $params['add']);
		}
	}
}
*/
function smarty_function_url($params)
{
	$for = $params['for'];
	if(is_string($for))
	{
		return url_for($params['for']);
	}
	else if(is_array($for))
	{
		return route($params['route'], $for, $params['add']);
	}
}

?>

