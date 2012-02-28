<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title>Test Suite</title>
	
	<style>
		.test-cases strong { font-size: 13px; font-family: monospace; }
	</style>
	
</head>
<body>

<?php

function d($var){ echo '<hr /><pre>', print_r($var, 1), '</pre><hr />'; }
function de($var){ d($var); exit; }
function error($msg){ d($msg); }

echo '<h1>Test Suites</h1>';
echo '<ul style="overflow: hidden; padding: 0; list-style: none;">';

$dir = dir(__DIR__.'/tests/');
while($file = readdir($dir->handle))
{
	if(in_array($file, array('.', '..'))){ continue; }
	
	$files[] = substr($file, 0, -4);
}

sort($files);
foreach($files as $f)
{
	if(!$f){ continue; }
	
	echo "<li style='float: left; $style '><a href='?test_suite=$f'>$f</a></li>";
	$style = 'margin-left: 15px; padding-left: 15px; border-left: 1px solid #CCC;';
}
echo '</ul><hr style="clear: both;" />';

$test_suite = explode('/', $_GET['test_suite']);
$test_suite = end($test_suite);

if($test_suite && file_exists(__DIR__.'/tests/'.$test_suite.'.php'))
{
	require_once __DIR__.'/TestSuite.php';
	require_once __DIR__.'/tests/'.$test_suite.'.php';
	$class_name = $test_suite.'_TestSuite';
	
	echo '<div class="test-cases">';
	new $class_name();
	echo '</div>';
}
else if($test_suite && !file_exists(__DIR__.'/tests/'.$test_suite.'.php'))
{
	echo "Test suite '$test_suite' does not exists.";
}

echo '<br /><br />';


?>
</body>
</html>