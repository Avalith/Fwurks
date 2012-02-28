<?php


class Smarty_Function_Paging
{
	public static function execute($params)
	{
		if(($_paging = $params['paging']) && $_paging['pages'])
		{
			$_locales = Registry::$globals->PAGING;
			$uri_str = preg_replace('/&page=\d*/ui', '', substr($_SERVER['REDIRECT_QUERY_STRING'], strpos($_SERVER['REDIRECT_QUERY_STRING'], '&')));
			$uri_str = str_replace('route='.ltrim($_SERVER['REQUEST_URI'], '/'), '', $uri_str);
			
			$uri_str .= $_paging['uri_add'];
			
			$last_page 		= $_paging['hide_last_page'] ? '' : "{$_locales['page_of']} {$_paging['total_pages']}";
			$total_results 	= $_paging['hide_last_page'] ? '' : "({$_paging['records_count']} {$_locales['results_found']})";
			
			
			$html .= "<p class=\"page-now\">{$_locales['page']} {$_paging['current_page']} $last_page $total_results</p>";
			$html .= "<p class=\"page-jump\"><input type=\"text\" /> <a href=\"#\">{$_locales['jump']}</a></p>";
			
			$html .= '<ul class="paging">';
			$html .= '<li class="first">'.($_paging['page_first'] ? "<a href=\"?page={$_paging['page_first']}$uri_str\">{$_locales['first']}</a>" 	: $_locales['first']).'</li>';
			($_paging['show_numbered'] && $_paging['pages_to_show']) && $html .= '<li class="prev_num">'.( $_paging['page_prev_num'] ? "<a href=\"?page={$_paging['page_prev_num']}$uri_str\">".sprintf($_locales['prev_num'], $_paging['pages_to_show']).'</a>' : sprintf($_locales['prev_num'], $_paging['pages_to_show']) ).'</li>';
			$html .= '<li class="prev">'.($_paging['page_prev'] 	? "<a href=\"?page={$_paging['page_prev']}$uri_str\">{$_locales['prev']}</a>" 	: $_locales['prev']).'</li>';
			
			foreach ($_paging['pages'] as $p){ $html .= "<li".($p == $_paging['current_page'] ? ' class="selected"' : '')."><a href=\"?page={$p}$uri_str\">$p</a></li>"; }
			
			$html .= '<li class="next">'.($_paging['page_next'] 	? "<a href=\"?page={$_paging['page_next']}$uri_str\">{$_locales['next']}</a>" 	: $_locales['next']).'</li>';
			($_paging['show_numbered'] && $_paging['pages_to_show']) && $html .= '<li class="next_num">'.( $_paging['page_next_num'] ? "<a href=\"?page={$_paging['page_next_num']}$uri_str\">".sprintf($_locales['next_num'], $_paging['pages_to_show']).'</a>' : sprintf($_locales['next_num'], $_paging['pages_to_show']) ).'</li>';
			$html .= '<li class="last">'.($_paging['page_last'] 	? "<a href=\"?page={$_paging['page_last']}$uri_str\">{$_locales['last']}</a>" : $_locales['last']).'</li>';
			
			$html .= '</ul>';
		}	
		return $html;
	}
}


?>
