<?php

final class Router
{
	public static $url;
	
	public static $routes;
	public static $route;
	public static $routeKey;
	
	private static $admin_or_public = 'Public';
	
	private static $single_locale;
	private static $default_locale;
	
	public static function recognize()
	{
		// Determine locales options 
		$locales 		= Application_Config::$installed_locales;
		Registry::$single_locale = $single_locale 	= $locales[1] ? false : true;
		
		$default_locale = Application_Config::$default_locale;
		$default_locale || $default_locale = $locales[0];
		
		self::$single_locale 	= $single_locale;
		self::$default_locale 	= $default_locale;
		
		// Assigns the back-end path for local usage
		$admin_path 	= Application_Config::$admin_path;
		
		
		// Gets the url from the $_GET and unset it from there
		$url = $_GET['route'] ? strtolower($_GET['route']) : '';
		unset($_GET['route']);
		
		
		// If the $url doesn't end with slash it will redirect you to the link with appended slash
		if($url && $url{strlen($url)-1} != '/'){ redirect('/'.$url.'/'); }
		
		// Remove all proceeding and trailing slashes, and replace multiple slashes with one. Then explode by slash 
		$url = explode('/', trim(preg_replace('~/+~', '/', $url), '/'));
		
		// If the framework is not in single locale mode and the $url is not starting with a locale code prepend the default locale code
		if(!$single_locale && !in_array($url[0], $locales)){ redirect('/'.$default_locale.'/'.implode($url, '/')); }
		
		// Extract current locale
		$locale = $single_locale ? $locales[0] : strtolower(array_shift($url));
		
		
		// Load data for all locales and assign the current one to different variable
		Registry::$locales->info = $locales_info = Localizer::getLocales();
		
		// If no locale is selected send to default locale
		if(!$locales_info[$locale]){ redirect('/'.$default_locale); }
		
		// Adds the current locale to Registry for easier access
		Registry::$locales->current = $locales_info[$locale];
		
		// Sets back-end variables 
		if($url[0] == $admin_path || empty($admin_path))
		{
			empty($admin_path) || array_shift($url);
			Registry::$is_admin = true;
			self::$admin_or_public = 'Admin';
		}
		
		self::$url = implode('/', $url);
		
		// Load routes
		self::$routes = get_class_vars(self::$admin_or_public . 'Routes_Config');
		
		// Finds a route corresponding to the current url
		self::find(self::$url);
		
		$path = explode('/', self::$route['path']);
		$params = self::$route['params'];
		
		// Gets controller name from params
		if(is_array($params) && isset($params[':controller']))
		{
			$controller = $params[':controller'];
		}
		// Gets controller name from path
		else if(is_array($path) && in_array(':controller', $path) && count($url))
		{
			$controller = strtolower($url[array_search(':controller', $path)]);
		}
		
		
		// Gets action name from params
		$action = '';
		if(is_array($params) && isset($params[':action']))
		{ 
			$action = $params[':action'];
		}
		// Gets action name from path
		else if(is_array($path) && in_array(':action', $path) && isset($url[ ($action = array_search(':action', $path)) ]))
		{
			$action = strtolower($url[$action]);
		}
		
		unset($params[':controller'], $params[':action']);
		
		// Assign unused path variables as action params
		$action_params = array();
		
		foreach($path as $p)
		{
			(isset($p{0}) && $p{0} == ':') && !in_array($p, array(':controller', ':action')) && $action_params[ltrim($p, ':')] = $url[array_search($p, $path)];
		}
		
		// Removing all url parts before the *
		for($i = 0; $i < count($path); $i++){ $path[$i] != '*' && array_shift($url); }
		
		// Concatenating all params in one var
		$action_params = array_merge($_GET, array_merge($url, array_merge($action_params, $params)));
		
		// Assign controller name and path 
		$controller_classname = Inflector::classify($controller) . '_Controller';
	
		// If controller file exists load it else redirect to 404
		if(file_exists($class = SystemConfig::$controllersPath.strtolower(Router::admin_or_public()).'/'.$controller.'.controller.php'))
		{
			require_once $class;
		}
		else { redirect('//404'); }
		
		// Instantiate controller and also save the instance in the registry
		Registry::$controller = $object = new $controller_classname($action_params);
		
		// Execute and render the controller
		$object->__executeController($controller, $action, $action_params);
		$object->__render(get_object_vars($object));
	}
	
	
	private static function find($url)
	{
		foreach(self::$routes as $k => $r)
		{
			$regexp = self::build($r[0]);
			if((!$url && !$regexp) || (preg_match("~$regexp~ui", $url, $url_parts) && $regexp)){ break; }
		}
		
		if($r[1])
		{
			foreach($r[1] as &$part)
			{
				// checks if there is value containing "$" to raplace it with apropriate key, might be escaped with "$"
				if(preg_match('~(?<!\$)\$(\d+)~ui', $part, $key)){ $part = strtr($part, array('$'.$key[1] => $url_parts[$key[1]])); }
			}
		}
		else { $r[1] = array(); }
		
		self::$routeKey = $k;
		self::$route = array('path' => trim($r[0], '/'), 'params' => $r[1]);
	}
	
	
	private static function build($path)
	{
		$path = explode('/', trim($path, '/'));
		
		if($path)
		{
			$regexp = array();
			
			foreach($path as $p)
			{
				if		(preg_match('/^:[a-z0-9_-]+/ui', $p))	{ $regexp[] = '([a-z0-9_-]+)'; }
				else if	($p == '*')								{ $regexp[] = '([/a-z0-9_-]+)'; }
				else											{ $regexp[] = $p; }
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
		
		$admin = (Registry::$is_admin ? 'admin/' : '');
		$uri = trim($url, '/');
		
		
		if(self::$single_locale){ $lang = '/'; } else { $lang || $lang = '/'.Registry::$locales->current['code'].'/'; } 

		if(strpos($url, '//') === 0)
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
			trim($lang, '/ ') && $uri = substr($uri, strpos($uri, '/', 1)+1);
			
			// remove the admin path from the beginning of the URI
			$admin && $uri = substr($uri, strpos($uri, '/', 1)+1);
			
			// remove folder from the end of the URI
			$uri = preg_replace('~(?:[^/]+/){0,'.$count.'}$~', '', $uri.'/');
			$new_url = trim($new_url, '/');
			
			$url = rtrim($lang . $admin . trim($uri, '/'), '/') . '/' . $new_url;
		}
		else if($url === './' || $url === null)
		{
			$uri = $_SERVER['REQUEST_URI'];
			trim($lang, '/ ') && $uri = substr($uri, strpos($uri, '/', 1)+1);
			$admin && $uri = substr($uri, strpos($uri, '/', 1)+1);
			$url = $lang . $admin . trim($uri, '/');
		}
		else if($url == -1)
		{
			return $_SERVER['HTTP_REFERER'];
		}
		
		return $path . $url . (preg_match('~(?:\.|\?)|/$~', $url) ? '' : '/');
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
