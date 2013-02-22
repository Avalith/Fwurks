<?php

function smarty_function_admin_permissions($params, $smarty, $class='permissions')
{
	if(!$params['struct']){ return; };
	$html = '<ul class="'.$class.'">';

	foreach($params['struct'] as $k => $s)
	{
		$sub_a = '';
		$sub_m = '';
		
		if(is_array($s['actions']) 	&& $s['type'] != 'hidden'){ $sub_a = smarty_function_admin_permissions(array('struct' => $s['actions']), $smarty, $template, ''); }
		if(is_array($s['menu'])		&& $s['type'] != 'hidden'){ $sub_m = smarty_function_admin_permissions(array('struct' => $s['menu']), $smarty, $template, ''); }
		
		$permission_name = $s['permission_name'] ? $s['permission_name'] : $s['controller'];
		
		$li_class = '';
		if($s == 'separator')
		{
			$li = '&nbsp;';
			$li_class = ' class="separator"';
		}
		else if($s['type'] == 'action' && $k != 'index')
		{
			$checked = $s['allowed'] ? 'checked="checked"' : '';
			$name = "permissions[$permission_name][]";
			$id	= "permissions_{$s['controller']}_$k".uniqid();
			
			$li = "<input type=\"checkbox\" name=\"$name\" value=\"$k\" id=\"$id\" $checked rel=\"admin_permissions_child\" /><label for=\"$id\">{$s['title']}</label>";
		}
		else if($s['type'] == 'hidden')
		{
			$name = "permissions[$permission_name]";
			$li = "<input type=\"hidden\" name=\"$name\" value=\"1\"/>";
		}
		else if($k != 'index')
		{
			$chbox = '';
			
			$s['actions'] && (is_array($s['actions']) || $s['actions'] = array($s['actions']));
			if($s['controller'] && (is_array($s['actions']) || is_array($s['menu'])))
			{
				$s['actions'] && $act = current(array_keys($s['actions']));
				
				$checked = $s['actions'][$act]['allowed'] ? 'checked="checked"' : '';
				$name = "permissions[$permission_name][]";
				$id	= "permissions_{$s['controller']}_{$k}_$act".uniqid();
				
				$chbox = "<input type=\"checkbox\" name=\"$name\" value=\"$act\" id=\"$id\" $checked rel=\"admin_permissions_parent\" />";
				$s['title'] = "<label for=\"$id\">{$s['title']}</label>";
			}
			
			$li = "<strong>$chbox {$s['title']}</strong>";
		}
		
		($sub_a || $sub_m) && $li_class = ' class="has-submenu"';
		$li && $html .= "<li$li_class>{$li}\n{$sub_a}\n{$sub_m}</li>";
	}
	$html .= '</ul>';
	
	return $html;
}


?>
