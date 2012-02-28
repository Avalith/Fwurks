<?php

require_once dirname(__FILE__).'/Inflector.php';
require_once dirname(__FILE__).'/Registry.php';
require_once dirname(__FILE__).'/Template.php';
require_once dirname(__FILE__).'/Exceptions.php';
require_once dirname(__FILE__).'/../configs/system.config.php';

ini_set('display_errors', SystemConfig::$show_errors);

/**
 * Autoloader 
 *
 * @param string $classname
 */
function __autoload($classname)
{
	if(Dispatcher::$admin_application && Registry()->is_admin)
	{
		$configs_path	 	= SystemConfig::$admin_configsPath;
		$controllers_path 	= SystemConfig::$admin_controllersPath;
		$models_path	 	= SystemConfig::$admin_modelsPath;
	}
	else
	{
		$configs_path	 	= SystemConfig::$configsPath;
		$controllers_path 	= SystemConfig::$controllersPath;
		$models_path	 	= SystemConfig::$modelsPath;
	}
	
	if($type = strchr($classname, '_Controller'))
	{
		$classname = Inflector()->toFile($classname);
		
		if(!file_exists($class = $controllers_path . $classname . '.php'))
		{
			$class = $controllers_path . strtolower(Router::admin_or_public()) . '/' . $classname . '.php';
		}
	}
	else if($type = strchr($classname, '_Config'))
	{
		if(!file_exists( $class = SystemConfig::$systemConfigsPath. Inflector()->toFile($classname) . '.php' ))
		{
			$class = $configs_path . Inflector()->toFile($classname) . '.php';
		}
	}
	else
	{
		if(!file_exists( $class = SystemConfig::$libraryPath.$classname . '.php' ))
		{
			$class = $models_path . Inflector()->toFile($classname) . '.model.php';
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
function d($var = 'empty debug?', $label = null, $return = null)
{
	$regexps = array
	(
		'/\[([\w\d]*)(?>:(protected|private))\] =>/u' => '[<strong style="color: #069;">\\1</strong> : <span style="color: #666;">\\2</span>] =>',
		'/\[([\w\d]*)] =>/u' => '[<strong style="color: #069;">\\1</strong>] =>',
	);
	
	$dump = preg_replace
	(
		array_keys($regexps),
		array_values($regexps),
		print_r($var, true)
	);
	
	$dump = '<pre style="font-size: 13px; font-family: Verdana, sans-serif; background: #F5F5F5;">'.
				'<strong style="font-size: 15px; line-height: 40px;">Debug <em>('.gettype($var).')</em>: '.$label.'</strong>'.
				"\n".$dump."\n".
			'</pre><hr />';
	if($return === null){ echo $dump; }
	
	return $return === null ? $var : $dump;
}
/**
 * Prints debug information for a variable and exits
 *
 * @param mixed $var varialbe to print
 * @param string $label label of the variable
 */
function de($var = 'empty debug?', $label = null){ d($var, $label); exit; }

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
function qstr($string, $db_instance_name = 'db')
{
	return Registry()->$db_instance_name->qstr($string);
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


function is_amf(){ return (!$_SERVER['HTTP_USER_AGENT'] && $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR']); }


function error($error, $code = null, $type = 'Custom')
{
	if(!SystemConfig::$show_errors){ return; }
	
	d(array('{$error}'=>$error)); return;
	
	if(SystemConfig::$show_exceptions){ $class = $type.'Exception'; throw new $class($error, $code); }
	else { echo strtr(file_get_contents(dirname(__FILE__).'/../../exceptions.php'), array('{$error}'=>$error)); exit; }
}

function dbd($state = 1, $db = 'db'){ Registry()->$db->debug = $state; }

?>
