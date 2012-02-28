<?php

header('Content-Type: text/css');

$styles = array('common', 'fonts', 'forms', 'jquery-ui', 'icons', 'image_box', 'tabs', 'lists', 'navigation', 'tree', 'dashboard');


$replacer = array();
foreach($styles as $css)
{
	echo "\n\n/*\n* -------------------------------\n*  $css \n* ------------------------------- \n*/\n\n";
	
	$css = file_get_contents($css.'.css');
	
	preg_match('/{{(.*)}}/us', $css, $matches);
	
	foreach(explode("\n", trim($matches[1])) as $kv)
	{
		$kv = explode(':', $kv); 
		$k = trim($kv[0]); 
		$v = trim($kv[1]);
		
		if($k{0} != '$'){ continue; }
		
		$replacer[$k] = $v;		
	}
	
	echo strtr(preg_replace('/{{.*}}/us', '', $css), $replacer);
}

?>
