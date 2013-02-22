<?php

function smarty_block_tab($params, $content, $smarty, $repeat)
{
	$key = $params['key'];
	$class = $params['class'];
	$tab_group = $params['tab_group'] ? $params['tab_group'].'-' : '';
	
    return "<div id=\"{$tab_group}{$key}\" class=\"tab_container $class\">$content</div>";
}

?>
