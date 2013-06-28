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
function core__autoload($classname)
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
	
	if(file_exists($class)){ require_once $class; }
}

spl_autoload_register('core__autoload', true, true);

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
		$trace = '<table border="0" style="color: #000; font-size: 11px; border-collapse: collapse;">';
		foreach($backtrace as $bt)
		{
			$trace .= "<tr><td style='padding-right: 20px;'>{$bt['class']}{$bt['type']}{$bt['function']}()</td><td>".substr($bt['file'], strlen(getcwd())+1).":{$bt['line']}</td></tr>";
		}
		$trace .= '</table>';
		
		$trace = "<strong style='font-size: 15px; line-height: 40px;'>Debug Trace: $label</strong> \n$trace";
	}
	
	$dump = '<pre style="color: #000; font-size: 13px; font-family: Verdana, sans-serif; background: #F5F5F5;">'. $trace . $dump .'</pre><hr />';
	
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
	file_put_contents(SystemConfig::$filesPath.'temp/'.$file.'.log', print_r($var, 1)."\n\n", FILE_APPEND);
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
function is_email($email){ return empty($email) ? true :  preg_match('/'.SystemConfig::$email_regexp.'/ui', $email); }

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

function signify($val)
{
	return ($val<0 ? '' : '+').print_2f($val);
}

function print_2f($val)
{
	return sprintf("%.2f", $val);
}

function intToSymbol($i)
{
	$i = (int) $i % 26;
	//if ($i < 10){ return $i; }
	return chr(ord('A') + ($i/* - 10*/));
}

function intToHash($int, $len = 6)
{
	$res = "";
	while($int > 0)
	{
		$res = intToSymbol($int) . $res;
		$int = (int)($int / 26);
	}
	while(strlen($res) < $len)
	{
		$res = 'A' . $res;
	}
	return $res;
}

if (!function_exists('http_build_url'))
{
    define('HTTP_URL_REPLACE', 1);              // Replace every part of the first URL when there's one of the second URL
    define('HTTP_URL_JOIN_PATH', 2);            // Join relative paths
    define('HTTP_URL_JOIN_QUERY', 4);           // Join query strings
    define('HTTP_URL_STRIP_USER', 8);           // Strip any user authentication information
    define('HTTP_URL_STRIP_PASS', 16);          // Strip any password authentication information
    define('HTTP_URL_STRIP_AUTH', 32);          // Strip any authentication information
    define('HTTP_URL_STRIP_PORT', 64);          // Strip explicit port numbers
    define('HTTP_URL_STRIP_PATH', 128);         // Strip complete path
    define('HTTP_URL_STRIP_QUERY', 256);        // Strip query string
    define('HTTP_URL_STRIP_FRAGMENT', 512);     // Strip any fragments (#identifier)
    define('HTTP_URL_STRIP_ALL', 1024);         // Strip anything but scheme and host

    // Build an URL
    // The parts of the second URL will be merged into the first according to the flags argument. 
    // 
    // @param   mixed           (Part(s) of) an URL in form of a string or associative array like parse_url() returns
    // @param   mixed           Same as the first argument
    // @param   int             A bitmask of binary or'ed HTTP_URL constants (Optional)HTTP_URL_REPLACE is the default
    // @param   array           If set, it will be filled with the parts of the composed url like parse_url() would return 
    function http_build_url($url, $parts=array(), $flags=HTTP_URL_REPLACE, &$new_url=false)
    {
        $keys = array('user','pass','port','path','query','fragment');

        // HTTP_URL_STRIP_ALL becomes all the HTTP_URL_STRIP_Xs
        if ($flags & HTTP_URL_STRIP_ALL)
        {
            $flags |= HTTP_URL_STRIP_USER;
            $flags |= HTTP_URL_STRIP_PASS;
            $flags |= HTTP_URL_STRIP_PORT;
            $flags |= HTTP_URL_STRIP_PATH;
            $flags |= HTTP_URL_STRIP_QUERY;
            $flags |= HTTP_URL_STRIP_FRAGMENT;
        }
        // HTTP_URL_STRIP_AUTH becomes HTTP_URL_STRIP_USER and HTTP_URL_STRIP_PASS
        else if ($flags & HTTP_URL_STRIP_AUTH)
        {
            $flags |= HTTP_URL_STRIP_USER;
            $flags |= HTTP_URL_STRIP_PASS;
        }

        // Parse the original URL
        $parse_url = parse_url($url);

        // Scheme and Host are always replaced
        if (isset($parts['scheme']))
            $parse_url['scheme'] = $parts['scheme'];
        if (isset($parts['host']))
            $parse_url['host'] = $parts['host'];

        // (If applicable) Replace the original URL with it's new parts
        if ($flags & HTTP_URL_REPLACE)
        {
            foreach ($keys as $key)
            {
                if (isset($parts[$key]))
                    $parse_url[$key] = $parts[$key];
            }
        }
        else
        {
            // Join the original URL path with the new path
            if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH))
            {
                if (isset($parse_url['path']))
                    $parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '', $parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
                else
                    $parse_url['path'] = $parts['path'];
            }

            // Join the original query string with the new query string
            if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY))
            {
                if (isset($parse_url['query']))
                    $parse_url['query'] .= '&' . $parts['query'];
                else
                    $parse_url['query'] = $parts['query'];
            }
        }

        // Strips all the applicable sections of the URL
        // Note: Scheme and Host are never stripped
        foreach ($keys as $key)
        {
            if ($flags & (int)constant('HTTP_URL_STRIP_' . strtoupper($key)))
                unset($parse_url[$key]);
        }


        $new_url = $parse_url;

        return 
             ((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '')
            .((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' . $parse_url['pass'] : '') .'@' : '')
            .((isset($parse_url['host'])) ? $parse_url['host'] : '')
            .((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '')
            .((isset($parse_url['path'])) ? $parse_url['path'] : '')
            .((isset($parse_url['query'])) ? '?' . $parse_url['query'] : '')
            .((isset($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '')
        ;
    }
}

?>
