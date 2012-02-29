<?php

require_once getcwd() . '/' . Dispatcher::$folder_system . '/' . Dispatcher::$folder_configs .'/Paths.config.php';

require_once Paths_Config::$configs . 'System.config.php';
require_once Paths_Config::$configs . 'Session.config.php';
require_once Paths_Config::$app_configs . 'Application.config.php';

require_once Paths_Config::$library . 'Inflector.php';
require_once Paths_Config::$library . 'Router.php';
require_once Paths_Config::$library . 'BaseController.php';

require_once Paths_Config::$library . 'AutoLoader.php';
spl_autoload_register('AutoLoader::load', true, true);





//require_once __DIR__.'/types/Type.php';
//require_once __DIR__.'/types/String.php';

//require_once __DIR__.'/../configs/database.config.php';

//require_once __DIR__.'/Registry.php';
//require_once __DIR__.'/Localizer.php';
//require_once __DIR__.'/Template.php';
//require_once __DIR__.'/BaseController.php';
//require_once __DIR__.'/database/DataBase.php';

//ini_set('display_errors', SystemConfig::$show_errors);


// removing notices
isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] = '/';


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
		'/\[([\w\d\?]*)(?>:(protected|private))\] =>/u' => '[<strong style="color: #069;">\\1</strong> : <span style="color: #666;">\\2</span>] =>',
		'/\[([\w\d\?]*)] =>/u' => '[<strong style="color: #069;">\\1</strong>] =>',
	);
	$dump = preg_replace(array_keys($regexps), array_values($regexps), print_r($var, true));
	$dump = "<strong style='font-size: 15px; line-height: 40px;'>Debug <em>($type)</em>: $label</strong>\n$dump\n";
	
	$trace = '';
	if($backtrace)
	{
		$backtrace = debug_backtrace(false);
		$trace = '<table border="0" style="font-size: 11px; border-collapse: collapse;">';
		foreach($backtrace as $bt)
		{
			$class 		= isset($bt['class']) 		? $bt['class'] 		: '';
			$type 		= isset($bt['type']) 		? $bt['type'] 		: '';
			$function 	= isset($bt['function']) 	? $bt['function'] 	: '';
			$file	 	= isset($bt['file']) 		? $bt['file'] 		: '';
			$line	 	= isset($bt['line']) 		? $bt['line'] 		: '';
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

function df($var, $file = 'log')
{
	file_put_contents(SystemConfig::$filesPath.'temp/'.$file.'.log', print_r($var, 1)."\n\n");
	chmod(SystemConfig::$filesPath.'temp/'.$file.'.log', 0777);
}

?>
