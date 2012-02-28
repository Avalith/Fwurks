<?php

function smarty_function_scaffolding_tree($params, &$smarty)
{
	foreach ($params['params'] as $k => $v){ ${'_'.$k} = $v; }
	/*
	 * $_data
	 * $_user_can
	 * $_locales
	 * $_class
	 */
	$_class || $_class = $params['class'];
	$_class || $_class = 'tree';
	
	$_actions = $_locales['ACTIONS'];
	
	if($_user_can['add']){ $smarty->assign('tree_add', '<a class="button add" title="'.$_actions['add'].'" href="add"><span>'.$_actions['add'].'</span></a>'); }
	if(!count($_data)){ $smarty->assign('tree', '<p class="no_entries">'.$_locales['ERRORS']['no_entries'].'</p>'); return; }
	
	$last_level = $first_level = $_data[array_shift(array_keys($_data))]->level-1;
	
	$path = array();
	$params['links'] && $params['path'] && $path[] = $params['path'];
	
	foreach($_data as $key => $d)
	{
		if($d->level > $last_level)
		{
			$html .= $_class ? '<ul class="'.$_class.'">' : '<ul>';
			$_class = '';
		}
		else if($d->level < $last_level)
		{
			if($params['links']){ for($i=$d->level; $i <= $last_level; $i++){ array_pop($path); } };	
			$html .= str_repeat('</ul></li>', $last_level - $d->level);
		}
		else
		{
			$html .= '</li>';
			$params['links'] && array_pop($path);
		}
		
		$params['links'] && $path[] = $d->slug;
		
		$html .= '<li rel="id_'.$d->primary.'" class="'.$last.' '.($d->isLeaf() ? '' : 'open').'">';
//		$_user_can && $html .= '<a class="switch '.($d->isLeaf() ? 'empty' : '').'">&nbsp;</a>';
		$_user_can && $html .= '<a class="'.($d->isLeaf() ? 'empty' : 'switch').'">&nbsp;</a>';
		
		$params['links'] && $href = 'href="'.url_for(implode('/', $path)).'"';
		
		$_up_down && $html .= '<a class="move up">Up</a>';
		$_up_down && $html .= '<a class="move down">Down</a>';
		
		$html .= '<a '.$href.' class="title">'.$d->title.'</a>';

		if($_user_can)
		{
			$html .= '<input type="hidden" name="tree_order['.$d->primary.'][parent_id]" value="'.$d->parent.'" />';
			$html .= '<span class="items">';
			if($_user_can['edit'])		{ $html .= ' <a class="icon edit" title="'.$_actions['edit'].'" href="edit/'.$d->primary.'">'.$_actions['edit'].'</a>'; }
			if($_user_can['delete'])	{ $html .= ' <a class="icon delete" title="'.$_actions['delete'].'" href="delete/'.$d->primary.'">'.$_actions['delete'].'</a>'; }
			$html .= '</span>';
		}
		
		$d->level > $_data[$key+1]->level && $html .= '</li>';
		
		$last_level = $d->level;
		$last_id = $d->id;
	}
	$html .= ($last_level-$first_level) > 0 ? str_repeat('</li></ul>', $last_level-$first_level) : '</ul>';
	
	if($params['return']){ return $html; }
	$smarty->assign('tree', $html);
}

?>
