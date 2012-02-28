<?php

function smarty_function_admin_menu($params, &$smarty)
{
	$_menu 		= $params['menu'];
	$_selected 	= $params['selected'];
	$_id	 	= $params['id'] 	? ' id="' . 	$params['id'].'"' 		: '';
	$_class	 	= $params['class'] 	? ' class="'. 	$params['class'].'"' 	: '';
	
	$_issub		= $params['issub'];
	
	$html = "<ul{$_id}{$_class}>";
	
	$hasTheParent = 0;
	
	foreach($_menu as $m)
	{
		$isParent = 0;
		
		$sub = '';
		if($m['menu'])
		{
			$isParent = 'parent';
			$sub = smarty_function_admin_menu(array('menu' => $m['menu'], 'selected' => $_selected, 'issub' => 1), $smarty);
			$isTheParent = $sub[0];
			$sub = substr($sub, 1);
		}
		
		if( $m['link'] == $_selected || (!$isTheParent && strpos($_selected, $m['link']) === 0) )
		{
			$s = 'selected';
			$hasTheParent = 1;
		}
		else if($isTheParent)
		{
			$s = 'selected_parent';
			$hasTheParent = 1;
		}
		else
		{
			$s = '';
		}
		
		$href = $m['type'] == 'link' ? '<strong>'.$m['title'].'</strong>' : '<a href="'.url_for($m['link']).'">'.$m['title'].'</a>';
		$html .= "<li class=\"$isParent {$s}\">{$href} $sub</li>";
	}
	
	$html .= '</ul>';
	
	
	return $_issub ? $hasTheParent.$html : $html;
}

?>
