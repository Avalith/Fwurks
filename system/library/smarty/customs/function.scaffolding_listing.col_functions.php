<?php


function smarty_function_scaffolding_listing_col__selector($val, $params, $data, $model, $pk, $col_params)
{
	return $col_params['data'][$val];
}

function smarty_function_scaffolding_listing_col__boolean($val, $params, $data, $model, $pk, $col_params)
{
	$g = Registry::$globals;
	return $val ? $g['YES'] : $g['NO'];
}

function smarty_function_scaffolding_listing_col__image($val, $params, $data, $model, $pk, $col_params)
{
	$val &&	$return = "<a href=\"{$col_params['path']}/{$val}\" rel=\"image-box\"><img src=\"{$col_params['path']}/thmb_{$val}\" /></a>";
	return $return;
}

function smarty_function_scaffolding_listing_col__md5($val){ return md5($val); }


?>
