<?php

// function smarty_function_scaffolding_listing_col__FUNCTION_NAME($val, $key, $params, $data, $model, $pk, $col_params){  }

function smarty_function_scaffolding_listing_col__selector($val, $key, $params, $data, $model, $pk, $col_params)
{
	return $col_params['data'][$val];
}

function smarty_function_scaffolding_listing_col__boolean($val, $key, $params, $data, $model, $pk, $col_params)
{
	$tag 	= isset($col_params['boolean_link']) && $col_params['boolean_link'] && ($params['user_can']['edit'] && (!$params['check_editable'] || $params['check_editable'] && $data->_editable === '1')) ? 'a' : 'span';
	$class 	= $val ? 'active' : 'inactive';
	$action = $params['user_can']['edit'] ? " href=\"?boolean={$key}&boolean_id={$data->$pk}\"" : '';
	
	return "<{$tag} class=\"icon {$class}\"$action>{$val}</{$tag}>";
}

function smarty_function_scaffolding_listing_col__image($val, $key, $params, $data, $model, $pk, $col_params)
{
	$val &&	$return = "<a href=\"{$col_params['path']}/{$val}\" rel=\"image-box\"><img src=\"{$col_params['path']}/thmb_{$val}\" /></a>";
	return $return;
}

function smarty_function_scaffolding_listing_col__md5($val){ return md5($val); }


?>
