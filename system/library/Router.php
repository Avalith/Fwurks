<?php

final class Router
{
//	public static $url;
	
	public static $routes;
//	public static $route;
//	public static $routeKey;
	
	public static $admin_or_public = 'Public';
	
	public static $single_locale;
	public static $default_locale;
	
	public static function start()
	{
		// Gets the url from the $_GET and unset it from there
		$url = $_GET['route'] ? strtolower($_GET['route']) : '';
		unset($_GET['route']);
		
		// If the $url doesn't end with slash it will redirect you to the link with appended slash
		if($url && $url{strlen($url)-1} != '/'){ redirect('/'.$url.'/'); }
		
		
		// Determine locales options 
		$locales = Application_Config::$installed_locales;
		Registry::$single_locale = $single_locale = !isset($locales[1]);
		
		$default_locale = Application_Config::$default_locale;
		$default_locale || $default_locale = $locales[0];
		
		self::$single_locale 	= $single_locale;
		self::$default_locale 	= $default_locale;
		
		// Assigns the back-end path for local usage
		$admin_path = Application_Config::$admin_path;
		
		// Remove all proceeding and trailing slashes, and replace multiple slashes with one. Then explode by slash 
		$url = explode('/', trim(preg_replace('~/+~', '/', $url), '/'));
		
		// If the framework is not in single locale mode and the $url is not starting with a locale code prepend the default locale code
		if(!$single_locale && !in_array($url[0], $locales)){ redirect('/'.$default_locale.'/'.implode($url, '/')); }
		
		// Extract current locale
		$locale = $single_locale ? $locales[0] : strtolower(array_shift($url));
		
		
		// Load data for all locales and assign the current one to different variable
		Registry::$locales->info = $locales_info = Localizer::getLocales();
		foreach($locales_info as $l){ Registry::$locales->all[$l->code] = $l->i18n; }
		
		// If no locale is selected send to default locale
		if(!$locales_info[$locale]){ redirect('/'.$default_locale); }
		
		// Adds the current locale to Registry for easier access
		Registry::$locales->current = $locales_info[$locale];
		
		// Sets back-end variables 
		if(isset($url[0]) && $url[0] == $admin_path || empty($admin_path))
		{
			empty($admin_path) || array_shift($url);
			Registry::$is_admin = true;
			self::$admin_or_public = 'Admin';
		}
		
		// Load routes
		self::$routes = get_class_vars(self::$admin_or_public . 'Routes_Config');
		
		$get = $_GET;
		$post = $_POST;
		
		unset($_GET, $POST);
		
		return self::request($url, $get, $post);
	}
	
	
	public static function request($url, array $get = array(), array $post = array(), $is_ajax = false)
	{
		$is_ajax = $is_ajax ?: (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest');
		
		$request = new RouterRequest($url, $get, $post, $is_ajax);
		
		if($error = $request->error())
		{
			// TODO: handle error;
			de($error, 'this must be handled');
			
			return false;
		}
		
		return $request->content;
	}
	

	public function find($url)
	{
		foreach(self::$routes as $k => $r)
		{
			isset($r['regexp']) || $r['regexp'] = self::build($r[0]);
			if((!$url && !$r['regexp']) || (preg_match("~{$r['regexp']}~ui", $url, $url_parts) && $r['regexp'])){ break; }
		}
		
		if($r[1])
		{
			foreach($r[1] as &$part)
			{
				// checks if there is value containing "$" to raplace it with apropriate key, might be escaped with "$"
				if(preg_match('~\$([a-z0-9_]+)~ui', $part, $key)){ $part = strtr($part, array('$'.$key[1] => $url_parts[$key[1]])); }
			}
		}
		else { $r[1] = array(); }
		
		foreach($url_parts as $k => $p){ if(is_string($k) && !isset($r[1][$k])){ $r[1][$k] = $p; } }
		
		return array('key' => $k, 'path' => trim($r[0], '/'), 'params' => $r[1]);
	}
	
	
	private static function build($path)
	{
		$path = explode('/', trim($path, '/'));
		
		if($path)
		{
			$regexp = array();
			
			foreach($path as $p)
			{
				if(preg_match('~^:(?P<name>[a-z0-9_-]+)(?:{(?P<regexp>[^}]+)})?~ui', $p, $var))
				{
					$regexp[] = "(?P<{$var['name']}>".(isset($var['regexp']) ? $var['regexp'] : '[a-z0-9_-]+').')';
				}
				else if($p == '*')
				{
					$regexp[] = '(?P<__wildcard__>[/a-z0-9_-]+)';
				}
				else
				{
					$regexp[] = $p;
				}
			}
			
			$regexp = '^' . implode('/', $regexp) . '$';
		}
		
		return $regexp;
	}
	
	
	public static function admin_or_public(){ return self::$admin_or_public; }
	
	
	public static function url_for($url = null, $lang = null)
	{
		if(strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0){ return $url; }
		
		$path = trim(Session_Config::$cookie['path'], '/');
		$path && $path = '/'.trim(Session_Config::$cookie['path'], '/');
		
		$admin = (Registry::$is_admin ? (Application_Config::$admin_path ? $admin_path = Application_Config::$admin_path.'/' : '') : '');
		$uri = trim($url, '/');
		
		if(self::$single_locale){ $lang = '/'; } else { $lang || $lang = '/'.Registry::$locales->current->code.'/'; } 

		if(strpos($url, '///') === 0)
		{
			$url = $lang . $uri;
		}
		else if(strpos($url, '//') === 0)
		{
			$url = $lang . $admin . $uri;
		}
		else if(strpos($url, '/') === 0)
		{
			$url = '/'.ltrim($uri, '/');
		}
		else if(strpos($url, '../') === 0)
		{
			// Count how many time '../' is repeated in the beginning of the url 
			$new_url = preg_replace('~\.\./~', '', $url);
			$backer = substr($url, 0, strlen($url)-strlen($new_url));
			$count = count(explode('../', $backer . '__'))-1;
			
			$uri = rtrim($_SERVER['REQUEST_URI'], '/');
			
			
			// remove the cookie path from the beginning of the URI
			Session_Config::$cookie['path'] && $uri = substr($uri, strlen(Session_Config::$cookie['path']));
			
			// remove the language path from the beginning of the URI
			$lang != '/' && $uri = substr($uri, strpos($uri, '/', 1)+1);
			
			// remove the admin path from the beginning of the URI
			$admin && $uri = substr($uri, strpos($uri, '/', 1)+1);
			
			// remove folder from the end of the URI
			$uri = preg_replace('~(?:[^/]+/){0,'.$count.'}$~', '', $uri.'/');
			$new_url = trim($new_url, '/');
			
			$url = rtrim($lang . $admin . trim($uri, '/'), '/') . '/' . $new_url;
		}
		else if($url == -1)
		{
			return $_SERVER['HTTP_REFERER'];
		}
		else
		{
			$loopback = strpos($url, './') === 0;
			$loopback && $url = substr($url, 2);
			$url && $url = '/'.$url;
			
			$uri = rtrim($_SERVER['REQUEST_URI'], '/');
			
			($loopback && $pos = strpos($uri, '?')) && $uri = substr($uri, 0, $pos);
			
			// remove the cookie path from the beginning of the URI
			Session_Config::$cookie['path'] && $uri = substr($uri, strlen(Session_Config::$cookie['path']));
			
			// remove the language path from the beginning of the URI
			$lang != '/' && $uri = substr($uri, strpos($uri, '/', 1)+1);
			
			$url == 'default_group' && de($url);
			// remove the admin path from the beginning of the URI
			$admin && $uri = substr($uri, strpos($uri, '/', 1)+1);
			
			$url = $lang . $admin . trim($uri, '/') . $url;
		}

		return $path . rtrim($url, '/') . (strpos($url, '.') || strpos($url, '?')  ? '' : '/');
	}
	
	
	public static function route($route_name = null, array $params = array(), $add = null)
	{
		$route_name || $route_name = self::$routeKey;
		$route = self::$routes[$route_name];
		
		$url_parts = explode('/', $route[0]);
		foreach($url_parts as &$u){ if($u{0} == ':'){ $params[$u] || error("Router: Missing $u for route: $route_name"); $u = $params[$u]; } }
		$url = self::url_for('//'.implode('/', $url_parts).'/'.$add);
		
		return $url;
	}
	
	
	private function __construct(){}
	
}



class RouterRequest
{
	private $url;
	private $route = array(	'key' => null, 'path' => null, 'params' => array() );
	private $caller_controller;
	
	private $error = false;
	
	public $content;
	
	
	public function __construct($url, array $__get = array(), array $__post = array(), $__is_ajax = false)
	{
	
		if(is_array($url))
		{
			$this->url = implode('/', $url);
		}
		else
		{
			$this->url = $url;
			$url = explode('/', $url);
		}
		
		
		// Finds a route corresponding to the current url
		$this->route = Router::find($this->url);
		
		$path = explode('/', $this->route['path']);
		$params = $this->route['params'];
		
		// Gets controller name from params
		
		if(is_array($params) && isset($params[':controller']))
		{
			$controller = $params[':controller'];
		}
		// Gets controller name from path
		else if(isset($params['controller']))
		{
			$controller = strtolower($params['controller']);
		}
		else
		{
			$this->error = 'missing_controller';
			
			if(SystemConfig::DEVELOPMENT)
			{
				de(array('URL' => '//'.$this->url), 'Missing Controller Key', 0, 0);
			}
		}
		
		
		if($controller)
		{
			// Gets action name from params
			$action = '';
			if(is_array($params) && isset($params[':action']))
			{ 
				$action = $params[':action'];
			}
			// Gets action name from path
			else if(isset($params['action']))
			{
				$action = strtolower($params['action']);
			}
	
			unset($params[':controller'], $params['controller'], $params[':action'], $params['action']);
	
			// Assign unused path variables as action params
			$action_params = array();
			foreach($path as $p)
			{
				( isset($p{0}) && $p{0} == ':' && !in_array($p, array(':controller', ':action')) ) && $action_params[ltrim($p, ':')] = $url[array_search($p, $path)];
			}
	
			// Removing all url parts before the *
			for($i = 0; $i < count($path); $i++){ $path[$i] != '*' && array_shift($url); }
	
	
			// Assign controller name and path 
			$controller_classname = Inflector::classify($controller) . '_Controller';

			// If controller file exists load it else redirect to 404
			if(file_exists($class = SystemConfig::$controllersPath.strtolower(Router::admin_or_public()).'/'.$controller.'.controller.php'))
			{
				require_once $class;
	
				// Concatenating all get params
				$__get = array_merge($__get, array_merge($url, array_merge($action_params, $params)));
	
				// Instantiate controller and also save the instance in the registry
				$this->caller_controller = Registry::$controller;
				Registry::$controller = $object = new $controller_classname($controller, $action, $__get, $__post, $__is_ajax, $this->url, $this->route);
	
				// Execute and render the controller
				$object->__executeController();
				$this->content = $object->__render();
	
				Registry::$controller = $this->caller_controller;
			}
			else
			{
				$this->error = 'unknown_path';
			
				if(SystemConfig::DEVELOPMENT)
				{
					de(array
					(
						'CONTROLLER' 	=> $controller_classname,
						'ACTION'		=> $action,
						'URL' 			=> '//'.$this->url, 
						'ROUTE' 		=> $this->route, 
					), 'Uknown URL/Path', 0, 0);
				}
			}
		}
	}
	
	
	public function error()
	{
		return $this->error;
	}
}



/**
 * Redirects to a page or link
 *
 * @param integer|string|array $url 'back' or -1, 'action_name', array(':controller' => 'controller_name', ':action' => 'action_name')
 */
function redirect($url = null)
{
	$url = Router::url_for($url);
//	if(headers_sent())	{ echo '<html><head><meta http-equiv="refresh" content="0; url="'.$url.'"></head></html>'; }
//	else				{ header("Location: ".$url); }
	
	header('Location: '.$url);
	exit;
}


/**
 * Returns absolute path
 *
 * @param string $url '//controller/action', '/en/controller/action/', '', 'action_name', 'http://www.website.com'
 * @return stirng
 */
function url_for($url = null)
{
	return Router::url_for($url);
}

/**
 * Returns absolute path
 *
 * @param string $route route name
 * @param array	 $params array with params  
 * @param string $add additional url
 * @return stirng
 */
function route($route_name = null, array $params = array(), $add = null)
{
	return Router::route($route_name, $params, $add);
}

?>
