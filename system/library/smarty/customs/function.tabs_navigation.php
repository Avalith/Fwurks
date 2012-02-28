<?php

class Smarty_Function_Tabs_Navigation
{
	public static function execute($params)
	{
		$id = $params['id'] ? $params : 'tabs_ul';
		$titles = $params['titles'];
		$title = $params['title'];
		$key = $params['key'];
		$selected = $params['selected'];
		$noselected = false;
		$cleaner = $params['cleaner'] ? false : true;
		$tab_group = $params['tab_group'] ? $params['tab_group'].'-' : '';
		
		$html = "<ul class=\"$id\">";
		foreach ($titles as $k => $t)
		{
			$tab_key = $key ? (is_array($t) ? $t[$key] : $t->$key) : $k;
			
			if(!$noselected && $selected && $selected == $tab_key)
			{
				$class = 'class="selected"';
			}
			else
			{
				$noselected = $selected ? false : true;
				$class = '';
			}
			
			$t = $title ? (is_array($t) ? $t[$title] : $t->$title) : $t;
			
			$html .= "<li $class><a href=\"#{$tab_group}{$tab_key}\">{$t}</a></li>";
		}
		$html .= '</ul>';
		
		if($cleaner){ $html .= '<div class="cleaner">&nbsp;</div>'; }
			
		return $html;
	}
}

function smarty_function_tabs_navigation($params)
{
	$id = $params['id'] ? $params : 'tabs_ul';
	$titles = $params['titles'];
	$title = $params['title'];
	$key = $params['key'];
	$selected = $params['selected'];
	$noselected = false;
	$cleaner = $params['cleaner'] ? false : true;
	$tab_group = $params['tab_group'] ? $params['tab_group'].'-' : '';
	
	$html = "<ul class=\"$id\">";
	foreach ($titles as $k => $t)
	{
		$tab_key = $key ? (is_array($t) ? $t[$key] : $t->$key) : $k;
		
		if(!$noselected && $selected && $selected == $tab_key)
		{
			$class = 'class="selected"';
		}
		else
		{
			$noselected = $selected ? false : true;
			$class = '';
		}
		
		$t = $title ? (is_array($t) ? $t[$title] : $t->$title) : $t;
		
		$html .= "<li $class><a href=\"#{$tab_group}{$tab_key}\">{$t}</a></li>";
	}
	$html .= '</ul>';
	
	if($cleaner){ $html .= '<div class="cleaner">&nbsp;</div>'; }
		
	return $html;
}

?>
