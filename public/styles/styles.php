<?php

header('Content-Type: text/css');

$styles = array('common');

foreach($styles as $css)
{
	echo "\n\n/*\n* -------------------------------\n*  $css \n* ------------------------------- \n*/\n\n";
	include_once $css.'.css';
}

?>