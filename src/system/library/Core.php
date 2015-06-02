<?php

define('DS', DIRECTORY_SEPARATOR);

require Dispatcher::$cwd . DS . Dispatcher::$folder_system . DS . Dispatcher::$folder_configs . DS .'Paths.config.php';

require Paths_Config::$configs		. 'System.config.php';

require Paths_Config::$library		. 'Inflector.php';
require Paths_Config::$library		. 'Router.php';
require Paths_Config::$library		. 'BaseController.php';
require Paths_Config::$library		. 'AutoLoader.php';

require Paths_Config::$app_controllers	. 'application.controller.php';
require Paths_Config::$app_configs		. 'application.config.php';


spl_autoload_register('library\AutoLoader::load', true, true);

foreach(Database_Config::$connections as $connection => $options)
{
	Database_Config::{'connect_' . $connection}($options);
}


function route($route = 'default', $params = array(), $add = null)
{
	return library\Router::route($route)->url($params, $add);
}



/**
 * Prints debug information for a variable
 *
 * @param mixed $var varialbe to print
 * @param string $label label of the variable
 * @return $var
 */
function d($var = null, $label = null, $return = false, $backtrace = false)
{
	$type = gettype($var);
	$regexps = array
	(
		'/\[([^\]]*)(:[\w\d\?]+)?(?>:(protected|private))\] =>/u' => '[<strong style="color: #069;">\\1</strong> <span style="color: #666;">\\3</span>\\2] =>',
		'/\[([^\]]*)] =>/u' => '[<strong style="color: #069;">\\1</strong>] =>',
	);
	$dump = preg_replace(array_keys($regexps), array_values($regexps), print_r($var, true));
	$dump = "<strong style='font-size: 15px; line-height: 40px;'>Debug <em>(<u>$type</u>)</em>: $label</strong>\n$dump\n";
	
	$trace = '';
	if($backtrace)
	{
		$backtrace = debug_backtrace(false);
		$trace = '<table border="0" style="font-size: 11px; border-collapse: collapse;">';
		foreach($backtrace as $bt)
		{
			$class		= isset($bt['class'])		? $bt['class']		: '';
			$type		= isset($bt['type'])		? $bt['type']		: '';
			$function	= isset($bt['function'])	? $bt['function']	: '';
			$file		= isset($bt['file'])		? $bt['file']		: '';
			$line		= isset($bt['line'])		? $bt['line']		: '';
			$trace .= "<tr><td style='padding-right: 20px;'>{$class}{$type}{$function}()</td><td>".substr($file, strlen(getcwd())+1).":{$line}</td></tr>";
		}
		$trace .= '</table>';
		
		$trace = "<strong style='font-size: 15px; line-height: 40px;'>Debug Trace: $label</strong> \n$trace";
	}
	
	$dump = '<pre style="font-size: 13px; font-family: Verdana, sans-serif; background: #F5F5F5;">'. $trace . $dump .'</pre><hr />';
	
	if(!$return){ echo $dump; }
	return !$return ? $var : $dump;
}

/**
 * Prints debug information for a variable and exits
 *
 * @param mixed $var varialbe to print
 * @param string $label label of the variable
 */
function de($var = null, $label = null, $backtrace = true){ d($var, $label, false, $backtrace); exit; }

//function df($var, $file = 'log')
//{
//	file_put_contents(SystemConfig::$filesPath.'temp/'.$file.'.log', print_r($var, 1)."\n\n");
//	chmod(SystemConfig::$filesPath.'temp/'.$file.'.log', 0777);
//}


function bench($text, callable $func, $iterations = 10000)
{
	$start = microtime(1);
	
	for($i = $iterations; $i; $i--){ $func(); }
	
	$time = explode('.', number_format((microtime(1) - $start)/$iterations, 50));
	$time = implode(' ', str_split($time[0], 3)) . '.' . substr(implode(' ', str_split($time[1], 3)), 0, 15);
	
	
	echo $time, ': ', $text, '<br />'; 
}

?>
