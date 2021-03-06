<?php

require_once substr(__FILE__, 0, -3).'col_functions.php';
require_once 'function.paging.php';

function smarty_function_scaffolding_listing($params, &$smarty)
{
	$reserved_column_names = array('active', 'title');
	
	foreach ($params['params'] as $k => $v){ ${'_'.$k} = $v; }
	/*
	 * $_model
	 * $_columns
	 * $_locales
	 * $_data
	 * $_user_can
	 * $_buttons
	 * $_filters
	 * $_sorting
	 * $_sorting_params
	 * $_paging
	 * $_has_checkboxes
	 * $_has_ordering
	 */
	
	$_columns_info 		= $_model->get_columns_info();
	$_columns_locales 	= $_locales['DATABASE_FIELDS'];
	$_actions 			= $_locales['ACTIONS'];
	
	if($_user_can['add']){ $smarty->assign('listing_add', '<a class="button add" title="'.$_actions['add'].'" href="add"><span>'.$_actions['add'].'</span></a>'); }
	if(!count($_data)){ $smarty->assign('listing', '<p class="no_entries">'.$_locales['ERRORS']['no_entries'].'</p>'); return; }
	
	if($_buttons)
	{
		foreach($_buttons as $bk => $b)
		{			
			if(!(isset($b['permission']) ? $_user_can[$b['permission']] : $_user_can['edit'])){ continue; }
			++$actions_col_span;
			$buttons_last = $bk;
		}
	}
	
	if($_user_can['edit'] && $_has_ordering && !$_sorting_params && !$_filters){ $actions_col_span += 3; } 
	if($_user_can['edit']){ ++$actions_col_span; }
	if($_user_can['delete']){ ++$actions_col_span; } 
	
	$html = '<table class="listing">';
	
	$colgroup = $_has_checkboxes ? '<col class="toggle" />' : '';
	
	foreach ($_columns as $col_key => $col_val)
	{
		$key = is_array($col_val) ? $col_key : $col_val;
		$colgroup .= '<col '.($key == 'id' || in_array($key, $reserved_column_names) ? "class=\"$key\"" : '').' />';
	}
	
	$html .= '<colgroup>'. $colgroup . str_repeat('<col class="icons" />', $actions_col_span) .'</colgroup>';
	
	$html .= '<thead><tr>';
	
	$_has_checkboxes 
		? $html .= '<th class="first toggle"><input type="checkbox" name=records /></th>'
		: $col_first = array_shift(array_values($_columns));
	
	$col_last = array_pop(array_values($_columns));
	
	foreach ($_columns as $col_key => $col_val)
	{
		$key_locale = '';
		
		$key = is_array($col_val) ? $col_key : $col_val;
		
		$key_locale = is_array($col_val) && array_key_exists('label', $col_val) ? $col_val['label'] : $key;
		$key_locale = $_columns_locales[$key_locale] ? $_columns_locales[$key_locale] : $key_locale;
		
		if($_sorting)
		{
			$type = $_sorting_params[$key];
			$sorting_class = $type ? ' sorting-'.$type : '';
			
			$type = ( $type == 'a' ? 'd' : ($type == 'd' ? null : 'a') );
			
			$sp = $_sorting_params;
			unset($sp[$key]);
			
			$add = null;
			if($sp)
			{
				foreach($sp as $k => &$s){ $s = "sorting[$k]=$s"; }
				$add = implode('&',$sp);
				($add && $type) && $add = '&'.$add;
			}
			
			$key_locale = "<a href=\"?".($type ? "sorting[$key]=$type" : '')."$add\">$key_locale</a>";
		}
		
		$html .= '<th class="'.($key == $col_first ? 'first' : (!$actions_col_span && $key == $col_last && !$_user_can['edit'] && !$_user_can['delete'] ? 'last' : '')).$sorting_class.'">'.$key_locale.'</th>';
	}
	
	if($actions_col_span){ $html .= '<th class="last" colspan="'.$actions_col_span.'"></th>'; }
	
	$html .= '</tr></thead>';
	$html .= '<tbody>';
	
	$last_row = count($_data)-1;
	
	foreach($_data as $row => $d)
	{
		$html .= '<tr '.((++$i & 1) ? 'class="odd"' : '').'>';
		
		$_has_checkboxes && $html .= '<td class="first toggle"><input type="checkbox" name=record value="'.$d->{$_model->primary_key}.'" /></td>';
		
		foreach ($_columns as $col_key => $col_val)
		{
			$key = (is_array($col_val) ? $col_key : $col_val);
			
			$val = $d->$key;
			if(is_array($col_val) && array_key_exists('function', $col_val))
			{
				foreach(explode('|', $col_val['function']) as $col_val_function)
				{
					$val_function = 'smarty_function_scaffolding_listing_col__'.$col_val_function;
					$val = $val_function($val, $params['params'], $d, $_model, $col_val);
				}
			}
			else if(in_array($key, $reserved_column_names))
			{
				$val_function = 'smarty_function_scaffolding_listing_col__reserved_'.$key;
				$val = $val_function($val, $params['params'], $d, $_model);
			}
			
			$html .= '<td class="'.($key == $col_first ? 'first' : ($key == $col_last && !$_user_can['edit'] && !$_user_can['delete'] ? 'last' : '')).'">'.$val.'</td>';
		}
		
		if($_user_can['edit'] && $_has_ordering && !$_sorting_params && !$_filters)
		{
			$html .= '<td class="movers action-button"><a href="#" class="icon arrow_up" title="Go Up">Go Up</a></td>';
			$html .= '<td class="movers action-button"><a href="#" class="icon arrow_down" title="Go Down">Go Up</a></td>';
			$html .= '<td class="movers action-button"><a href="#" class="icon arrow_updown" title="Move">Go Up</a><input class="order_index" type="hidden" name="listing_order['.$d->{$_model->primary_key}.']" value='.$d->order_index.' /></td>';
		}
		
		if($_user_can['edit'])		{ $html .= '<td class="action-button'.($_user_can['delete'] || $buttons_last ? '' : ' last').'"><a class="icon edit" title="'.$_actions['edit'].'" href="edit/'.$d->{$_model->primary_key}.'">'.$_actions['edit'].'</a></td>'; }
		if($_user_can['delete'])	{ $html .= '<td class="action-button'.($buttons_last ? '' : ' last').'"><a class="icon delete" title="'.$_actions['delete'].'" href="delete/'.$d->{$_model->primary_key}.'">'.$_actions['delete'].'</a></td>'; }
		
		if($_buttons)
		{
			$replacer = array
			(
				'$primary' => $d->{$_model->primary_key},
			);
			
			foreach($_buttons as $bk => $b)
			{
				if(!(isset($b['permission']) ? $_user_can[$b['permission']] : $_user_can['edit'])){ continue; }
				
				$html .= '<td class="action-button'.($buttons_last == $bk ? ' last' : '').'">';
				
				if($b['html'])
				{
					$html .= strtr($b['html'], $replacer);
				}
				else
				{
					// class, title, href
					$b['title'] = strtr($b['title'], $replacer);
					$b['href'] 	= strtr($b['href'], $replacer);
					$b_title = $_actions[$b['title']] ? $_actions[$b['title']] : $b['title']; 
					
					$html .= "<a class=\"{$b['class']}\" title=\"$b_title\" href=\"{$b['href']}\">$b_title</a>";
				}
				
				$html .= '</td>';
			}
		}
		
		$html .= '</tr>';
	}
	
	$html .= '<tfoot>';
	$html .= '<tr><td colspan="'.(count($_columns) + $actions_col_span + ($_has_checkboxes?1:0)).'">';
	
	$_paging && $html .= smarty_function_paging(array('paging' => $_paging), $smarty);
	
	$html .= '<ul></td></tr>';
	$html .= '</tfoot>';
	
	$html .= '</tbody>';
	$html .= '</table>';
	
	$smarty->assign('listing', $html);
}





function smarty_function_scaffolding_listing_col__reserved_active($val, $params, $data, $model)
{
	$tag = $params['user_can']['edit'] ? 'a' : 'span';
	return '<'.$tag.' class="icon '.($val ? 'active' : 'inactive').'" '.($params['user_can']['edit'] ? 'href="?activate='.$data->{$model->primary_key}.'"' : '').'>'.$val.'</'.$tag.'>';
}

function smarty_function_scaffolding_listing_col__reserved_title($val, $params, $data, $model)
{
	if(!$params['user_can']['edit'] || !Registry()->settings->edit_link_titles){ return $val; }
	
	return '<a href="edit/'.$data->{$model->primary_key}.'">'.$val.'</a>';
}



?>
