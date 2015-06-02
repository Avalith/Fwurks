<?php

require_once __DIR__.'/controls/Control.php';

function smarty_block_fieldset($params, $content, $smarty, $repeat)
{
	$span = $params['span'] ?: 12;
	
	if(!$repeat)
	{
		return <<<Html
	<div class="span{$span} well">
		<div class="navbar"><div class="navbar-inner">
			<h5>{$params['title']}</h5>
		</div></div>
		{$content}
	</div>
Html;
	}
}

?>