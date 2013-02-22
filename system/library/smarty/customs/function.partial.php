<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsFunction
 */

function smarty_function_partial($params)
{
	$result = Registry::$controller->__renderPartial($params);
	
	$params['assign'] && Template()->tpl_vars[$params['assign']]->value = $result; // assign($params['assign'], $result); 
	
	return !$params['render'] && $params['assign'] ? '' : $result->template;
}

?>