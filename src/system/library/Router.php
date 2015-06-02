<?php

namespace library;
use Exception, Paths_Config, Application_Config, Atom_Config;


class RouterException		extends Exception			{}
class RouterRouteException	extends RouterException		{}

class RouterRequest
{
	public $route;
	public $get;
	public $post;
	
	public $type;
	
	public function __construct($route, array $get = [], array $post = [], $type = 'html')
	{
		$this->route	= $route;
		$this->get		= $get;
		$this->post		= $post;
		$this->type		= $type;
	}
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
	
	private $parts = [];
	private $add;
	
	public function __construct($key, $route)
	{
		$this->key 			= $key;
		$this->path			= trim($route[0], '/');
		$this->params		= $route[1];
		
		$path				= explode('/', trim($this->path, '/'));
		$regexp				= [];
		
		foreach($path as $p)
		{
			if(preg_match('#^(?<key>[\w]+)?:(?<opt>\??)(?<name>[\w\d_-]+)(?:~(?<regexp>[^~]+)~)?#ui', $p, $var))
			{
				$part = array
				(
					'name'		=> $var['name'],
					'regexp'	=> (isset($var['regexp']) ? $var['regexp'] : ''),
					'default'	=> (isset($this->params[$var['name']]) ? $this->params[$var['name']] : ''),
				);
				
				$r = "/(?<{$var['name']}>" . ($var['key'] ? "{$var['key']}:" : '') . (isset($var['regexp']) ? $var['regexp'] : '[\w\d_-]+') . ')';
				
				$part['has_key'] = !!$var['key'];
				
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
				// TODO: WILDCART ROUTE
				
				de('TODO WILDCART ROUTE');
				$regexp[] = '/(?<__wildcard__>[/\w\d_-]+)';
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
			if(isset($p['raw'])){ $p['value'] = $p['raw']; }
			else
			{
				if($p['vars'])
				{
					$search		= [];
					$replace	= [];
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
					if($p['has_key'])
					{
						$p['value'] = explode(':', $p['value']);
						$p['value'] = $p['value'][1];
					}
				}
				
				if(!$p['optional'] && !$p['value']){ throw new RouterRouteException('Missing route part: ' . $p['name'] .' of $' . $route->key); }
			}
			
			if(in_array($p['name'], $route_variables)){ $route->{$p['name']} = $p['value']; }
		}
		
		if(!($route->controller	|| $route->controller	= $route->params['controller']	)){ throw new RouterRouteException('Missing route part: controller of $'	. $route->key); }
		if(!($route->action		|| $route->action		= $route->params['action']		)){ throw new RouterRouteException('Missing route part: action of $'		. $route->key); }
		unset($route->params['controller'], $route->params['action']);
		
		return $route;
	}
	
	public function change($part, $value)
	{
		d('TODO ROUTE CHANGE');
		
		$this->parts[$part] && $this->parts[$part]['value'] = $value;
		return $this;
	}
	
	function go()
	{
		header('Location: '. $this->__toString());
		exit;
	}
	
	public function __toString()
	{
		$url = [];
		
		$this->atom != Application_Config::$atom_default && $url[] = $this->atom;
		($this->locale || Router::$locale_force) && $url[] = $this->locale;
		
		
		foreach($this->parts as $p)
		{
			if($p['value'])
			{
				$url[] = $p['vars'] ? $p['value_url'] : $p['value'];
			}
		}
		
		$url[] = null;
		$this->add && $url[] = $this->add;
		
		
		// TODO: add
		return Paths_Config::$base . implode('/', $url);
	}
}

final class Router
{
	public static $atom_all = [];
	public static $atom_current;
	
	public static $locale_all = [];
	public static $locale_current;
	public static $locale_force;
	
	public static $url;
	public static $routes;
	
	
	public static function start()
	{
		self::load();
		
		$route = self::find(implode('/', self::$url));
		$route->locale = self::$locale_current; # Application_Config::$locale_default;
		if(self::$locale_force && !self::$locale_current)
		{
			$route->locale = Application_Config::$locale_default;
			self::route($route)->go();
		}
		
		$get	= $_GET;
		$post	= $_POST;
		unset($_GET, $_POST);
		
		return self::request($route, $get, $post); # (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest')
	}
	
	public static function load()
	{
		self::load_url();
		self::load_atom();
		self::load_configs();
		self::load_locales();
		self::load_routes();
	}
	
	public static function load_url()
	{
		self::$url = preg_split('#/+#', strtolower(isset($_GET['route']) ? $_GET['route'] : ''), null, PREG_SPLIT_NO_EMPTY);
		unset($_GET['route']);
	}
	
	public static function load_atom()
	{
		# TODO Profile this and probably change the glob as system application config
		self::$atom_all			= Paths_Config::glob(Paths_Config::$app_atoms);
		self::$atom_current		= isset(self::$url[0]) && in_array(self::$url[0], self::$atom_all) ? array_shift(self::$url) : Application_Config::$atom_default;
		Paths_Config::set_atom(self::$atom_current);
	}
	
	public static function load_configs()
	{
		require_once Paths_Config::$atom_configs . 'atom.config.php';
		require_once Paths_Config::$atom_configs . 'routes.config.php';
	}
	
	public static function load_locales()
	{
		# TODO Profile this and probably change the glob as system application config
		self::$locale_all		= Paths_Config::glob(Paths_Config::$app_locales);
		self::$locale_current	= isset(self::$url[0]) && in_array(self::$url[0], self::$locale_all) ? array_shift(self::$url) : Application_Config::$locale_default;
		self::$locale_force		= Atom_Config::$locale_force || count(self::$locale_all) != 1;
	}
	
	public static function load_routes()
	{
		$routes = get_class_vars('Routes_Config');
		
		foreach($routes as $key => $route){ self::$routes[$key] = new RouterRoute($key, $route); }
	}
	
	public static function request(RouterRoute $route, $get = [], $post = [], $response_type = 'html')
	{
		$get = array_merge($get, $route->params);
		
		$controller_name = Inflector::classify($route->controller) . '_Controller';
		if(class_exists($controller_name, false) || file_exists($class = Paths_Config::$atom_controllers . $route->controller . '.controller.php'))
		{
			if($class){ require_once $class; }
			
			$controller = new $controller_name(new RouterRequest($route, $get, $post, $response_type));
			return $controller->__render($controller->__execute(), get_object_vars($controller));
		}
		else
		{
			throw new RouterException('Missing controller: ' . $controller_name);
		}
	}
	
	
	public static function find($url)
	{
		foreach(self::$routes as $route)
		{
			if(preg_match("#{$route->regexp}#ui", '/' . ltrim($url, '/'), $url_parts)){ $selected = $route; break; }
		}
		
		if($selected)
		{
			foreach($url_parts as $i => $u){ if(is_numeric($i)){ unset($url_parts[$i]); } }
			return $selected->url($url_parts);
		}
		else
		{
			throw new RouterRouteException('No route found: ' . $url);
		}
	}
	
	public static function route($route)
	{
		
		if($r = self::$routes[$route])
		{
			return $r;
		}
		else
		{
			throw new RouterRouteException('Route does not exists: ' . $route);
		}
	}
	
	private function __construct(){}
}

?>
