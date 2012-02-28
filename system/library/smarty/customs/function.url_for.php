<?php


function smarty_function_url_for($params, &$smarty)
{
	foreach ($params as $k => $p)
	{
		if		($k == 'link')													{ $link = $p; break; }
		else if	(in_array($k, array('locale', 'controller', 'action', 'add')))	{ $link[':'.$k] = $p; }
		else 																	{ $link[$k] = $p; }
	}
	return url_for($link);
}

?>
