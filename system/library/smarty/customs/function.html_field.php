<?php

require_once __DIR__.'/html_fields/HtmlForm_Field.php';

function smarty_function_html_field($params)
{
	$type = $params['type'] ? $params['type'] : 'text';

	switch ($type)
	{
		case 'text':
		case 'password':		$type = 'Simple';			break;
		
		case 'submit':
		case 'reset':			$type = 'Button';			break;
		
		case 'radiolist':		$type = 'RadioList'; 		break;
		case 'checkboxlist':	$type = 'CheckboxList'; 	break;
		
		default: $type = str_replace(' ', '', ucwords(str_replace('_',' ', $type))); break;
	}
	
	require_once __DIR__.'/html_fields/'.$type.'.php';
	$fieldClass = 'HtmlForm_'.$type.'Field';
	$field = new $fieldClass($params);
	
	return $field->html();
}

?>
