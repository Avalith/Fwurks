<?php

final class Router
{
	public static $routes;
	public static $route;
	public static $routeKey;
	public static $url;
	
	private static $admin_or_public = 'Public';
	
	public static function recognize()
	{
		$single_locale 	= Application_Config::$single_locale;
		$defaultLocale 	= Application_Config::$defaultLocale;
		$admin_path 	= Application_Config::$admin_path;
		
		$url = $_GET['route'] ? strtolower($_GET['route']) : '';
		unset($_GET['route']);
		
		
		if($url && $url[strlen($url)-1] != '/'){ redirect('/'.$url.'/'); }
		if($single_locale){ $url = $defaultLocale.'/'.ltrim($url,'/'); }
		
		if(strpos($url, $admin_path.'/') === 0){ redirect("//$defaultLocale/$admin_path/"); }
		
		$url = explode('/', trim(preg_replace('/\/+/', '/', $url), "/"));
		$locale = strtolower(array_shift($url));
		
		Registry()->locales = $locales = Localizer::getLocales();
		Registry()->locale = $locales[$locale] ? $locale : $defaultLocale;
		Registry()->i18n_locale = $locales[Registry()->locale]['i18n'];
		Registry()->localeInfo = $locales[$locale];
		
		if(!Registry()->localeInfo){ redirect('/'.Registry()->locale); }
		
		if($url[0] == $admin_path || empty($admin_path))
		{
			empty($admin_path) || array_shift($url);
			Registry()->is_admin = true;
			self::$admin_or_public = 'Admin';
		}
		
		self::find(self::$url = implode('/', $url));
		
		$path = explode("/", self::$route['path']);
		$params = self::$route['params'];
		
		if(is_array($params) && array_key_exists(":controller", $params))
		{
			$controller = $params[":controller"];
			unset($params[":controller"]);
		}
		else if(is_array($path) && in_array(":controller", $path) && count($url))
		{
			$controller = strtolower($url[array_search(":controller", $path)]);
		}
		
		if(is_array($params) && array_key_exists(":action", $params))
		{
			$action = $params[':action'];
			unset($params[":action"]);
		}
		else if(is_array($path) && in_array(":action", $path) && array_key_exists($action = array_search(":action", $path), $url))
		{
			$action = strtolower($url[$action]);
		}
		
		$action_params = array();
		foreach($path as $p)
		{
			if(substr($p, 0, 1) == ":" && !in_array($p, array(":controller", ":action")))
			{
				$p_name = trim($p, ":");
				if($url[array_search($p, $path)])
				{
					$action_params[$p_name] = strtolower($url[array_search($p, $path)]);
				}
				else
				{
					$action_params[$p_name] = '';
				}
			}
		}
		
		for($i = 0; $i < count($path); $i++){ $path[$i] != '*' && array_shift($url); }
		$params || $params = array();
		$action_params = array_merge($_GET, array_merge($url, array_merge($action_params, $params)));
		
		$controller_class = Inflector()->classify($controller) . '_Controller';
		
		$controller_path = Dispatcher::$admin_application && Registry()->is_admin ? SystemConfig::$admin_controllersPath : SystemConfig::$controllersPath;
				
		if(file_exists($class = $controller_path . strtolower(Router::admin_or_public()) . '/' . $controller . '.controller.php'))
		{
			require_once $class;
		}
		else{ redirect('//404'); }

		Registry()->controller = $object = new $controller_class($action_params); 
		
		$object->__controller = $controller;
		$action || $action = $object->__defaultAction;
		$object->__action = $object->__view = method_exists($object, $action) ? $action : (method_exists($object, '__noaction') ? '__noaction' : $object->__defaultAction);
		
		$object->__labels = Localizer::load(Registry()->locale, $controller);
		$object->__labels['__globals'] = Registry()->globals = Localizer::getGlobals();
		
		$object->__executeController($action_params);
		
		if($object->__view)
		{
			if(strpos($object->__view, '../') === 0){ $view_file = substr($object->__view, 3); }
			else									{ $view_file = $object->__controller.'/'.$object->__view; }
			
			Template()->assign('__view_file', $view_file.'.html'); 
		}
		
		Template()->assign(get_object_vars($object));
		Template()->display($object->__layout.'.html');
	}

	private static function find($url)
	{
		foreach(self::$routes = get_class_vars(self::$admin_or_public . 'Routes_Config') as $k => $r)
		{
			$regexp = self::build(trim($r[0], '/'));
			if((!$url && !$regexp) || (preg_match("/$regexp/", $url, $url_parts) && $regexp)){ break; }
		}
		
		if($r[1])
		{
			foreach($r[1] as &$part)
			{
				if(preg_match('/\$([0-9]+)/', $part, $key)){ $part = str_replace('$'.$key[1], $url_parts[$key[1]], $part ); }
			}
		}
		
		self::$routeKey = $k;
		self::$route = array('path' => $r[0], 'params' => $r[1]);
	}


	private static function build($path)
	{
		if(!is_array($path)){ $path = explode("/", $path); }
		
		if($path)
		{
			$regexp = array();
			foreach($path as $p)
			{
				if(preg_match('/^:[a-z0-9_\-]+/', $p))
				{
					$regexp[] = '([a-z0-9_\-]+)';
				}
				else if($p == '*')
				{
					$regexp[] = '([\/a-z0-9_\-]+)';
				}
				else
				{
					$regexp[] = $p;
				}
			}
			$regexp = "^" . implode("\/", $regexp) . "$";
		}
		
		return $regexp;
	}


	public static function admin_or_public(){ return self::$admin_or_public; }
	
	
	public static function url_for($url)
	{
		if(is_array($url))
		{
			$path['path'] 	= trim(Session_Config::$cookie['path'], '/');
			if(!Application_Config::$single_locale){ $path['locale'] = Registry()->locale; }
			$path['admin'] 	= Registry()->is_admin ? Application_Config::$admin_path : '';
			
			if($url[':controller'])		{ $path['controller'] = $url[':controller']; }
			if(!$path['controller'])	{ $path['controller'] = Registry()->controller->__controller; }
			
			if($url[':action'])			{ $path['action'] = $url[':action']; }
	
			if($url[':add'])
			{
				if(!$path['action']){ $path['action'] = Registry()->controller->__action; }
				$path['add'] = $url[':add'];
			}
			
			if($url[':locale'])
			{
				if(!$path['action']){ $path['action'] = Registry()->controller->__action; }
				$path['locale'] = $url[':locale'];
			}
			
			foreach ($path as $k => $p){ if(!$p){ unset($path[$k]); } }
			
			return '/'.implode('/', $path);
		}
		else if(stripos($url, '//') === 0)
		{
			$path['path'] 	= trim(Session_Config::$cookie['path'], '/');
			if(!Application_Config::$single_locale){ $path['locale'] = Registry()->locale; }
			$path['admin'] 	= Registry()->is_admin ? Application_Config::$admin_path : '';
			$path['url'] 	= trim($url, '/');
			
			foreach ($path as $k => $p){ if(!$p){ unset($path[$k]); } }
			if(strlen($url) == 2){ return str_replace('//', '/', '/'.implode('/', $path).'/'); }
			
			return '/'.implode('/', $path);
		}
		else if(strpos($url, '/') === 0)
		{
			return rtrim(Session_Config::$cookie['path'], '/').$url;
		}
		else if($url === '')
		{
			$uri = rtrim($_SERVER['REQUEST_URI'], '/');
			return substr($uri, 0, strrpos($uri, '/')+1) . substr($url, 1);
		}
		else if($url === null)
		{
			$uri = $_SERVER['REQUEST_URI'];
			return substr($uri, 0, strpos($uri, '?'));
		}
				
		return '/'.trim(Session_Config::$cookie['path'], '/').'/'.trim($url, '/').( strpos(array_pop(explode('/', $url)), '.') ? '' : '/' );
	}
	
	// singleton
	private function __construct()
	{}
}



/**
 * Redirects to a page or link
 *
 * @param integer|string|array $url 'back' or -1, 'action_name', array(':controller' => 'controller_name', ':action' => 'action_name')
 */
function redirect($url = null, $form_url = 1)
{
	if($form_url)
	{
		if($url === 'back' || $url === -1)
		{
			$url = $_SERVER['HTTP_REFERER'];
		}
		else if($url && is_string($url) && strpos($url, '/') === false)
		{
			$url = url_for(array(':action' => $url));
		}
		else
		{
			$url = url_for($url);
		}
	}
	
//	if(headers_sent())	{ echo '<html><head><meta http-equiv="refresh" content="0; url="'.$url.'"></head></html>'; }
//	else				{ header("Location: ".$url); }
	
	header("Location: ".$url);
	exit;
	
}


/**
 * Returns absolute path
 *
 * @param string|array $url '//controller/action', '/en/controller/action/', '', 'action_name', 'http://www.website.com', array(':controller' => 'controller_name', ':action' => 'action_name')
 * @return stirng
 */
function url_for($url)
{
	return Router::url_for($url);
}

?>
