<?php

function smarty_function_scaffolding_form($params, &$smarty)
{
	$types = array
	(
		'tinyint' 		=> array('type' => 'checkbox'),
		'smallint' 		=> array('type' => 'text'),
		'int' 			=> array('type' => 'text'),
		'float' 		=> array('type' => 'text'),
		'double' 		=> array('type' => 'text'),
		'bigint' 		=> array('type' => 'text'),
		'mediumint' 	=> array('type' => 'text'),
	
		'char' 			=> array('type' => 'text'),
		'varchar' 		=> array('type' => 'text'),
		'text' 			=> array('type' => 'textarea'),
		'mediumtext' 	=> array('type' => 'rte'),
		'longtext' 		=> array('type' => 'rte'),
	
		'datetime' 		=> array('type' => 'datetime'),
		'timestamp' 	=> array('type' => 'datetime'),
		'time' 			=> array('type' => 'time'),
		'year' 			=> array('type' => 'year'),
		
		'blob' 			=> array('type' => 'file'),
		'mediumblob' 	=> array('type' => 'file'),
		'longblob' 		=> array('type' => 'file')
	);
	
	$reserved_column_names = array
	(
		'password' 		=> array('type' => 'password'),
	);
	
	require_once $smarty->_get_plugin_filepath('function','html_field');
	
	foreach ($params['params'] as $k => $v){ ${'_'.$k} = $v; }
	/*
	 * $_columns
	 * $_columns_info
	 * $_locales
	 * $_data
	 */
	
	$_columns_locales 	= $_locales['DATABASE_FIELDS'];
	

	
	
		
	$smarty->assign('form', $html);
}

?>
