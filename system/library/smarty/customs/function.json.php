<?php

function smarty_function_json($params)
{
	return strtr(json_encode($params['var']), array("'" => "\'"));
}

/* vim: set expandtab: */

?>
