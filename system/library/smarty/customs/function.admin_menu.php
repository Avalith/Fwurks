<?php
/*
class Smarty_Function_Admin_Menu
{
	public static function execute($params)
	{
		$_menu 		= $params['menu'];
		$_selected 	= $params['selected'];
		$_id	 	= $params['id'] 	? ' id="' . 	$params['id'].'"' 		: '';
		$_class	 	= $params['class'] 	? ' class="'. 	$params['class'].'"' 	: '';
		$_issub		= $params['issub'];
		
		$hasTheParent = 0;
		$html = "<ul{$_id}{$_class}>";
		foreach($_menu as $m)
		{
			if($m == 'separator'){ $html .= "<li class=\"separator\"></li>"; continue; }
			
			$iconClass = $m['icon'] ? "menu-icon {$m['icon']}" : ''; 
			
			$isParent = '';
			$sub = '';
			if($m['menu'])
			{
				$isParent = 'parent';
				$sub = self::execute(array('menu' => $m['menu'], 'selected' => $_selected, 'issub' => 1));
				$isTheParent = $sub[0];
				$sub = substr($sub, 1);
			}
			
			if( $m['link'] == $_selected || (!$isTheParent && strpos($_selected, $m['link']) === 0) )
			{
				$s = ' selected';
				$hasTheParent = 1;
			}
			else if($isTheParent)
			{
				$s = ' selected_parent';
				$hasTheParent = 1;
			}
			else
			{
				$s = '';
			}
			
			$href = $m['type'] == 'link' ? '<strong>'.$m['title'].'</strong>' : '<a href="'.url_for($m['link']).'">'.$m['title'].'</a>';
			$html .= "<li class=\"$isParent{$s}{$iconClass}\">{$href} $sub</li>";
		}
		$html .= '</ul>';
		
		return $_issub ? $hasTheParent.$html : $html;
	}
}
*/

function smarty_function_admin_menu($params)
{
	$_menu 		= $params['menu'];
	$_selected 	= $params['selected'];
	$_id	 	= $params['id'] 	? ' id="' . 	$params['id'].'"' 		: '';
	$_class	 	= $params['class'] 	? ' class="'. 	$params['class'].'"' 	: '';
	$_issub		= $params['issub'];
	
	$hasTheParent = 0;
	$html = "<ul{$_id}{$_class}>";
	foreach($_menu as $m)
	{
		if($m == 'separator'){ $html .= "<li class=\"separator\"></li>"; continue; }
		
		$iconClass = $m['icon'] ? "menu-icon {$m['icon']}" : ''; 
		
		$isParent = '';
		$sub = '';
		if($m['menu'])
		{
			$isParent = 'parent';
			$sub = smarty_function_admin_menu(array('menu' => $m['menu'], 'selected' => $_selected, 'issub' => 1));
			$isTheParent = $sub[0];
			$sub = substr($sub, 1);
		}
		
		if( $m['link'] == $_selected || (!$isTheParent && strpos($_selected, $m['link']) === 0) )
		{
			$s = ' selected';
			$hasTheParent = 1;
		}
		else if($isTheParent)
		{
			$s = ' selected_parent';
			$hasTheParent = 1;
		}
		else
		{
			$s = '';
		}
		
		$href = $m['type'] == 'link' ? '<strong>'.$m['title'].'</strong>' : '<a href="'.url_for($m['link']).'">'.$m['title'].'</a>';
		$html .= "<li class=\"$isParent{$s}{$iconClass}\">{$href} $sub</li>";
	}
	$html .= '</ul>';
	
	return $_issub ? $hasTheParent.$html : $html;
}

?>
