<?php

final class Router
{
	public static $atom_all = array();
	public static $atom_current;
	
	public static $routes;
	
	
	public static function start()
	{
		foreach(glob(Paths_Config::$app_atoms . '*', GLOB_ONLYDIR) as $dir)
		{
			$dir = explode('/', $dir);
			self::$atom_all[] = array_pop($dir);
		}
		
		$url = strtolower($_GET['route']);
		unset($_GET['route']);
		
		$url = preg_split('#/+#', $url, null, PREG_SPLIT_NO_EMPTY);
		
		self::$atom_current = isset($url[0]) && in_array($url[0], self::$atom_all) ? array_shift($url) : Application_Config::$atom_default;
		Paths_Config::set_atom(self::$atom_current);
		
		require_once Paths_Config::$atom_configs . 'Atom.config.php';
		require_once Paths_Config::$atom_configs . 'Routes.config.php';
		self::$routes = get_class_vars('Routes_Config');
		
		// TODO get locale not a language
		
		$get	= $_GET;
		$post	= $_POST;
		unset($_GET, $POST);
		
		return self::request(self::find(implode('/', $url)), $get, $post, (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest'), 'html');
	}
	
	public static function request($route, $get = array(), $post = array(), $is_ajax = false, $data_type = 'html')
	{
		$controller	= $route->controller	= $route->params['controller'];
		$action		= $route->action		= $route->params['action'];
		unset($route->params['controller'], $route->params['action']);
		
		$get = array_merge($get, $route->params);
		
		$controller_classname = Inflector::classify($controller) . '_Controller';
		
		// If controller class or file exists load
		if(class_exists($controller_classname) || file_exists($class = Paths_Config::$atom_controllers . $controller . '.controller.php'))
		{
			require_once $class;
			
			$object		= new $controller_classname($route);
			$content	= $object->__executeController();
			
			// TODO questioning this
			if($data_type == 'html'){ $content = $object->__render(); }
			
			return $content;
		}
		
		d($class);
		d($route);
	}
	
	
	public static function find($url)
	{
		$url = '/' . $url;
		
		foreach(self::$routes as $route => $r)
		{
			$r['regexp'] = self::build($r[0]);
			if(preg_match("#{$r['regexp']}#ui", $url, $url_parts)){ break; }
		}
		
		if($r[1])
		{
			foreach($r[1] as $k => $part)
			{
				if(preg_match('#\$([\w\d_]+)#ui', $part, $key)){ $url_parts[$k] = strtr($part, array($key[0] => $url_parts[$key[1]])); }
			}
		}
		else { $r[1] = array(); }
		
		foreach($url_parts as $k => $p){ if(is_string($k)){ $r[1][$k] = $p; } }
		
		return new RouterRoute($route, trim($r[0], '/'), $r[1]);
	}
	
	private static function build($path)
	{
		$path = explode('/', trim($path, '/'));
		
		if($path)
		{
			$regexp = array();
			
			foreach($path as $p)
			{
				if(preg_match('#^:(?P<opt>\??)(?P<name>[\w\d_-]+)(?:~(?P<regexp>[^~]+)~)?#ui', $p, $var))
				{
					$r = "/(?P<{$var['name']}>" . (isset($var['regexp']) ? $var['regexp'] : '[\w\d_-]+') . ')';
					$regexp[] = $var['opt'] ? "(?:$r)?" : $r;
				}
				else if($p == '*')
				{
					$regexp[] = '/(?P<__wildcard__>[/\w\d_-]+)';
				}
				else
				{
					$regexp[] = '/' . $p;
				}
			}
			
			$regexp = '^' . implode('', $regexp) . '$';
		}
		
		return $regexp;
	}
	
	public static function route($route_name, $params = array(), $add = null, $atom = null)
	{
		$route = self::$routes[$route_name];
		if(!$route){ throw new RouterException('Missing route: ' . $route_name); }
		
		$url_parts = explode('/', trim($route[0], '/'));
		foreach($url_parts as $i => &$u)
		{
			if($u{0} == ':')
			{
				$key = ltrim(substr($u, 1), '?');
				if(!($val = $params[$key]))
				{
					if($u{1} == '?')
					{
						unset($url_parts[$i]);
					}
					else
					{
						throw new RouterException('Missing required route variable: ' . $key);
					}
				}
				else
				{
					$u = $val;
				}
			}
		}
		
		$atom = $atom ?: self::$atom_current;
		if($atom != Application_Config::$atom_default){ array_unshift($url_parts, $atom); }
		
		$url_parts[] = '';
		
		// TODO locale
		
		return Paths_Config::$base . implode('/', $url_parts);
	}
	
	private function __construct(){}
	
}

class RouterException extends Exception{}

class RouterRoute
{
	public $key;
	public $path;
	
	public $controller;
	public $action;
	public $params;
	
	public function __construct($key, $path, $params)
	{
		$this->key 		= $key;
		$this->path		= $path;
		$this->params	= $params;
	}
	
	public function __toString()
	{
		
	}
}

class RouterRequest
{
	public $route;
	public $get;
	public $post;
	
	public $is_ajax;
	
	public function __construct($route, array $get = array(), array $post = array(), $is_ajax = false)
	{
		$this->route 	= $route;
		$this->get		= $get;
		$this->post		= $post;
		$this->is_ajax	= $is_ajax;
		
	}
}



/**
 * Redirects to a page or link
 *
 * @param integer|string|array $url 'back' or -1, 'action_name', array(':controller' => 'controller_name', ':action' => 'action_name')
 */
/*
function redirect($url = null)
{
	$url = Router::url_for($url);
//	if(headers_sent())	{ echo '<html><head><meta http-equiv="refresh" content="0; url="'.$url.'"></head></html>'; }
//	else				{ header("Location: ".$url); }
	
	header('Location: '.$url);
	exit;
}
*/

/**
 * Returns absolute path
 *
 * @param string $route route name
 * @param array	 $params array with params  
 * @param string $add additional url
 * @return stirng
 */
function route($route_name = null, $params = array(), $add = null)
{
	return Router::route($route_name, $params, $add);
}

?>
