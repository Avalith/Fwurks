<?php

class RouterException		extends Exception			{}
class RouterRouteException	extends RouterException		{}

class RouterRequest
{
	public $route;
	public $get;
	public $post;
	
	public $is_ajax;
	public $response_type;
	
	public function __construct($route, array $get = array(), array $post = array(), $is_ajax = false, $response_type = 'html')
	{
		$this->route			= $route;
		$this->get				= $get;
		$this->post				= $post;
		$this->is_ajax			= $is_ajax;
		$this->response_type	= $response_type;
	}
}


final class Router
{
	public static $atom_all = array();
	public static $atom_current;
	
	public static $locale_all = array();
	public static $locale_current;
	public static $locale_force;
	
	public static $routes;
	
	
	public static function start()
	{
		$url = strtolower($_GET['route']);
		unset($_GET['route']);
		
		$url = preg_split('#/+#', $url, null, PREG_SPLIT_NO_EMPTY);
		
		foreach(glob(Paths_Config::$app_atoms . '*', GLOB_ONLYDIR) as $dir){ $dir = explode('/', $dir); self::$atom_all[] = array_pop($dir); }
		self::$atom_current		= isset($url[0]) && in_array($url[0], self::$atom_all) ? array_shift($url) : Application_Config::$atom_default;
		Paths_Config::set_atom(self::$atom_current);
		
		require_once Paths_Config::$atom_configs . 'Atom.config.php';
		require_once Paths_Config::$atom_configs . 'Routes.config.php';
		
		
		foreach(glob(Paths_Config::$app_locales . '*', GLOB_ONLYDIR) as $dir){ $dir = explode('/', $dir); self::$locale_all[] = array_pop($dir); }
		isset($url[0]) && in_array($url[0], self::$locale_all) && self::$locale_current	= array_shift($url);
		self::$locale_force		= Atom_Config::$locale_force || count(self::$locale_all) != 1;
		
		self::$routes = get_class_vars('Routes_Config');
		foreach(self::$routes as $key => &$r){ $r = new RouterRoute($key); }
		
		
		$get	= $_GET;
		$post	= $_POST;
		unset($_GET, $_POST);
		
		$route = self::find(implode('/', $url));
		if(self::$locale_force && !self::$locale_current){ $route->locale = Application_Config::$locale_default; redirect($route); }
		echo self::request($route, $get, $post, (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest'));
	}
	
	public static function request(RouterRoute $route, $get = array(), $post = array(), $is_ajax = false, $response_type = 'html')
	{
		$get = array_merge($get, $route->params);
		
		$controller_classname = Inflector::classify($route->controller) . '_Controller';
		if(class_exists($controller_classname, false) || file_exists($class = Paths_Config::$atom_controllers . $route->controller . '.controller.php'))
		{
			if($class){ require_once $class; }
			
			$controller = new $controller_classname(new RouterRequest($route, $get, $post, $is_ajax, $response_type));
			$content = $controller->__execute();
			return $controller->__render($content, get_object_vars($controller));
		}
		else
		{
			throw new RouterException('Missing controller: ' . $controller_classname);
		}
		
		return null;
	}
	
	
	public static function find($url)
	{
		$url = '/' . $url;
		
		foreach(self::$routes as $route){ if(preg_match("#{$route->regexp}#ui", $url, $url_parts)){ break; } }
		foreach($url_parts as $i => $u){ if(is_numeric($i)){ unset($url_parts[$i]); } }
		
		return $route->url($url_parts);
	}
	
	private function __construct(){}
	
}

class RouterRoute
{
	public $key;
	public $path;
	public $regexp;
	
	public $atom;
	public $locale;
	public $controller;
	public $action;
	public $params;
	
	
	private $parts		= array();
	private $add;
	
	public function __construct($key)
	{
		$route = Router::$routes[$key];
		if(!$route){ throw new RouterRouteException('Missing route: ' . $key); }
		
		$this->key 			= $key;
		$this->path			= trim($route[0], '/');
		$this->params		= $route[1];
		
		$path				= explode('/', trim($this->path, '/'));
		$regexp				= array();
		foreach($path as $p)
		{
			if(preg_match('#^:(?P<opt>\??)(?P<name>[\w\d_-]+)(?:~(?P<regexp>[^~]+)~)?#ui', $p, $var))
			{
				$part = array('name' => $var['name'], 'regexp' => $var['regexp'], 'default' => $this->params[$var['name']]);
				$r = "/(?P<{$var['name']}>" . (isset($var['regexp']) ? $var['regexp'] : '[\w\d_-]+') . ')';
				
				if($var['opt'])
				{
					$part['optional'] = True;
					$r = "(?:$r)?";
				}
				
				$regexp[] = $r;
				
				if($part['default'] && preg_match_all('#\{([\w\d_]+)\}#', $part['default'], $matches))
				{
					$part['vars'] = $matches[1];
					unset($this->params[$var['name']]);
				}
				
				$this->parts[$var['name']] = $part;
			}
			else if($p == '*')
			{
				de('TODO WILDCART ROUTE');
				$regexp[] = '/(?P<__wildcard__>[/\w\d_-]+)';
			}
			else
			{
				$this->parts[] = array('raw' => $p);
				$regexp[] = '/' . $p;
			}
		}
		
		$this->regexp = '^' . implode('', $regexp) . '$';
	}
	
	public function url($params, $add = null)
	{
		$route = clone $this;
		$route->params = array_merge($route->params, $params);
		
		$route->add		= $add;
		$route->atom	= $route->params['atom']	?: Router::$atom_current; 
		$route->locale	= $route->params['locale']	?: Router::$locale_current; 
		
		unset($route->params['atom'], $route->params['locale']);
		
		
		$route_variables = array('controller', 'action');
		
		
		foreach($route->parts as &$p)
		{
			if($p['raw']){ $p['value'] = $p['raw']; }
			else
			{
				if($p['vars'])
				{
					$search		= array();
					$replace	= array();
					foreach($p['vars'] as $v)
					{
						if($route->params[$v])
						{
							$search[] = "{{$v}}"; $replace[] = $route->params[$v];
						}
						else
						{
							throw new RouterRouteException('Missing route variable: {' . $v . '} for ' . $p['name'] . ' - ' . $p['default'] .' of $' . $route->key);
						}
					}
					
					$p['value']		= str_replace($search, $replace, $p['default']); 
					$p['value_url']	= $route->params[$p['name']];
				}
				else
				{
					$p['value'] = $route->params[$p['name']] ?: $p['default'];
				}
				
				if(!$p['optional'] && !$p['value']){ throw new RouterRouteException('Missing route part: ' . $p['name'] .' of $' . $route->key); }
			}
			
			if(in_array($p['name'], $route_variables)){ $route->{$p['name']} = $p['value']; }
		}
		
		if(!($route->controller	|| $route->controller	= $route->params['controller']	)){ throw new RouterRouteException('Missing route part: controller of $'	. $route->key); }
		if(!($route->action		|| $route->action		= $route->params['action']		)){ throw new RouterRouteException('Missing route part: action of $'		. $route->key); }
		unset($route->params['controller'], $route->params['action']);
		
		foreach($route->parts as $_p)
		{
			unset($route->params[$_p['name']]);
		}
		
		return $route;
	}
	
	public function change($part, $value)
	{
		d('TODO ROUTE CHANGE');
		
		$this->parts[$part] && $this->parts[$part]['value'] = $value;
		return $this;
	}
	
	public function __toString()
	{
		$url = array();
		
		$this->atom != Application_Config::$atom_default && $url[] = $this->atom;
		Router::$locale_force && $url[] = $this->locale;
		
		foreach($this->parts as $p)
		{
			if(!$p['optional'] && $p['value'] || $p['optional'] && $p['value'] != $p['default'])
			{
				$url[] = $p['vars'] ? $p['value_url'] : $p['value'];
			}
		}
		
		$url[] = $this->add;
		
		// TODO: add
		return Paths_Config::$base . implode('/', $url);
	}
}


function route($route, $params = array(), $add = null)
{
	return Router::$routes[$route]->url($params, $add);
}

function redirect($route)
{
	header('Location: '. $route);
	exit;
}

?>
