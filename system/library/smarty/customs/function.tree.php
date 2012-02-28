<?php
/*
class Smarty_Function_Tree
{
	public static function execute($params)
	{
		foreach ($params as $k => $v){ ${'_'.$k} = $v; }
		/*
		 * $_data
		 * $_fold
		 * $_class
		 * /
		return  smarty_function_tree_ul($_data, $_fold, $_class);
	}
}
*/

function smarty_function_tree_ul($data, $fold=false, $class=null, $href='')
{
	if(!$data){	return;	}
	
	$html = "<ul class=\"$class\">";
	
	$last = array_pop(array_keys($data));
	
	foreach($data as $id => $d)
	{
		$html .= '<li class="'.($id == $last ? 'last' : '').' '.($d->children ? 'open' : '').'">';
		$fold && $html .= '<a class="'.($d->children ? 'switch' : 'empty').'">&nbsp;</a>';
		
		$link = $d->slug ? $d->slug : $d->primary;
		
		$html .= '<a class="title" href="'.$href.$link.'">'.$d->title.'</a>';
		
		if($d->children){ $html .= smarty_function_tree_ul($d->children, $fold, '', $href.$link.'/'); }
		
		$html .= '</li>';
	}
	$html .= '</ul>';
	
	return $html;
}

function smarty_function_tree($params)
{
	foreach ($params as $k => $v){ ${'_'.$k} = $v; }
	/*
	 * $_data
	 * $_fold
	 * $_class
	 */
	return  smarty_function_tree_ul($_data, $_fold, $_class);
}

?>
