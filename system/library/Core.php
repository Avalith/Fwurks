<?php

//require_once __DIR__.'/types/Type.php';
//require_once __DIR__.'/types/String.php';

require_once __DIR__.'/Inflector.php';
require_once __DIR__.'/Registry.php';
require_once __DIR__.'/Localizer.php';
require_once __DIR__.'/Template.php';
//require_once __DIR__.'/Exceptions.php';
require_once __DIR__.'/../configs/system.config.php';

//require_once __DIR__.'/Response.php';

//ini_set('display_errors', SystemConfig::$show_errors);


// removing notices
isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] = '/';

/**
 * Autoloader 
 *
 * @param string $classname
 */
function __autoload($classname)
{
	$configs_path	 	= SystemConfig::$configsPath;
	$controllers_path 	= SystemConfig::$controllersPath;
	$models_path	 	= SystemConfig::$modelsPath;
	
	if($type = strchr($classname, '_Controller'))
	{
		$classname = Inflector::to_file($classname);
		
		if(!file_exists($class = $controllers_path . $classname . '.php'))
		{
			$class = $controllers_path . strtolower(Router::admin_or_public()) . '/' . $classname . '.php';
		}
	}
	else if($type = strchr($classname, '_Config'))
	{
		if(!file_exists( $class = SystemConfig::$systemConfigsPath. Inflector::to_file($classname) . '.php' ))
		{
			$class = $configs_path . Inflector::to_file($classname) . '.php';
		}
	}
	else
	{
		if(!file_exists( $class = SystemConfig::$libraryPath.$classname . '.php' ) && !file_exists( $class = SystemConfig::$alibraryPath.$classname . '.php' ))
		{
			$class = $models_path . Inflector::to_file($classname) . '.model.php';
		}
	}
	
	require_once $class;
}

/**
 * Prints debug information for a variable
 *
 * @param mixed $var varialbe to print
 * @param string $label label of the variable
 * @return $var
 */
function d($var = null, $label = null, $return = false, $backtrace = true)
{
	$type = gettype($var);
	$regexps = array
	(
		'/\[([\w\d]*)(?>:(protected|private))\] =>/u' => '[<strong style="color: #069;">\\1</strong> : <span style="color: #666;">\\2</span>] =>',
		'/\[([\w\d]*)] =>/u' => '[<strong style="color: #069;">\\1</strong>] =>',
	);
	$dump = preg_replace(array_keys($regexps), array_values($regexps), print_r($var, true));
	$dump = "<strong style='font-size: 15px; line-height: 40px;'>Debug <em>($type)</em>: $label</strong>\n$dump\n";
	
	if($backtrace || !$var)
	{
		$backtrace = debug_backtrace(false);
		$trace = '<table border="0" style="font-size: 11px; border-collapse: collapse;">';
		foreach($backtrace as $bt)
		{
			$trace .= "<tr><td style='padding-right: 20px;'>{$bt['class']}{$bt['type']}{$bt['function']}()</td><td>".substr($bt['file'], strlen(getcwd())+1).":{$bt['line']}</td></tr>";
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
function de($var = null, $label = null){ d($var, $label); exit; }

function df($var, $file = 'log')
{
	file_put_contents(SystemConfig::$filesPath.'temp/'.$file.'.log', print_r($var, 1)."\n\n");
	chmod(SystemConfig::$filesPath.'temp/'.$file.'.log', 0777);
}

/**
 * Qotes a string
 *
 * @param string $string
 * @param string $db_instance
 * @return string
 */
function qstr($string)
{
	return Registry::$db->qstr($string);
}

/**
 * Validates email
 *
 * @param string $email
 * @return bool
 */
function is_email($email){ return preg_match('/'.SystemConfig::$email_regexp.'/ui', $email); }

/**
 * Checks if the request is XMLHttp (AJAX)
 *
 * @return bool
 */
function is_ajax() { return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER ['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest'; }

/**
 * Checks if the request is post
 *
 * @return bool
 */
function is_post(){ return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST'; }


function error($error, $code = null, $type = 'Custom')
{
	if(!SystemConfig::$show_errors){ return; }
	
	d(array('{$error}'=>$error)); return;
	
	if(SystemConfig::$show_exceptions){ $class = $type.'Exception'; throw new $class($error, $code); }
	else { echo strtr(file_get_contents(dirname(__FILE__).'/../../exceptions.php'), array('{$error}'=>$error)); exit; }
}

function dbd($state = 1, $db = 'db'){ Registry::$db->debug = $state; }

?>
