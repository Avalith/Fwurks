<?php

function smarty_function_paging($params)
{
	if(($_paging = $params['paging']) && $_paging['pages'])
	{
		$_paging_locales = Registry::$globals['PAGING'];
		$uri_str = preg_replace('/&page=\d*/ui', '', substr($_SERVER['REDIRECT_QUERY_STRING'], strpos($_SERVER['REDIRECT_QUERY_STRING'], '&')));
		$uri_str = str_replace('route='.ltrim($_SERVER['REQUEST_URI'], '/'), '', $uri_str);

		$uri_str .= $_paging['uri_add'];
		$globals = Registry::$globals;
			
		Registry::$is_admin && $html .= '<p class="page-now">'.$globals['PAGING']['page'].' '.$_paging['current_page'].'</p>';
		Registry::$is_admin && $html .= '<p class="page-jump"><input type="text" /> <a href="#">'.$globals['PAGING']['jump'].'</a></p>';
		
		Registry::$is_admin && $html .= '<br /><br /><div class="cleaner"></div><div class="first">'.($_paging['page_first'] ? "<a href=\"?page={$_paging['page_first']}$uri_str\">{$_paging_locales['first']}</a>" 	: $_paging_locales['first']).'</div>';
		(Registry::$is_admin && $_paging['show_numbered'] && $_paging['pages_to_show']) && $html .= '<div class="prev_num">'.( $_paging['page_prev_num'] ? "<a href=\"?page={$_paging['page_prev_num']}$uri_str\">".sprintf($_paging_locales['prev_num'], $_paging['pages_to_show']).'</a>' : sprintf($_paging_locales['prev_num'], $_paging['pages_to_show']) ).'</div>';
		$html .= '<div class="prev">'.($_paging['page_prev'] 	? "<a href=\"?page={$_paging['page_prev']}$uri_str\">{$_paging_locales['prev']}</a>" 	: $_paging_locales['prev']).'</div>';
		
		Registry::$is_admin && $html .= '<div class="last">'.($_paging['page_last'] 	? "<a href=\"?page={$_paging['page_last']}$uri_str\">{$_paging_locales['last']} ({$_paging['total_pages']})</a>" : "{$_paging_locales['last']} ({$_paging['total_pages']})").'</div>';
		(Registry::$is_admin && $_paging['show_numbered'] && $_paging['pages_to_show']) && $html .= '<div class="next_num">'.( $_paging['page_next_num'] ? "<a href=\"?page={$_paging['page_next_num']}$uri_str\">".sprintf($_paging_locales['next_num'], $_paging['pages_to_show']).'</a>' : sprintf($_paging_locales['next_num'], $_paging['pages_to_show']) ).'</div>';
		
		$html .= '<div class="next">'.($_paging['page_next'] 	? "<a href=\"?page={$_paging['page_next']}$uri_str\">{$_paging_locales['next']}</a>" 	: $_paging_locales['next']).'</div>';

		
		$html .= '<ul class="paging">';
		foreach ($_paging['pages'] as $p){ $html .= "<li".($p == $_paging['current_page'] ? ' class="selected"' : '')."><a href=\"?page={$p}$uri_str\">$p</a></li>"; }
		$html .= '</ul>';
		
			
	}	
	return $html;
}

?>
