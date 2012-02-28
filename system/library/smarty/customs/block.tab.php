<?php

class Smarty_Block_Tab
{
	public static function execute($params, $content, $smarty, $repeat, $template)
	{
		$key = $params['key'];
		$class = $params['class'];
		$tab_group = $params['tab_group'] ? $params['tab_group'].'-' : '';
		
	    return "<div id=\"{$tab_group}{$key}\" class=\"tab_container $class\">$content</div>";
	}
}

?>
