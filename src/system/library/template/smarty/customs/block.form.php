<?php

require_once __DIR__.'/controls/Control.php';

function smarty_block_form($params, $content, $smarty, $repeat)
{
	if(!$repeat)
	{
		return <<<Html
<form class="form-horizontal">$content</form>
Html;
	}
}

?>