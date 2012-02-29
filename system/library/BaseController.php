<?php

abstract class BaseController
{
	protected $__route;
	protected $__paths;
	
	protected $__view;
	
	protected $__before_filters		= array();
	protected $__after_filters		= array();
	
	public $__javascripts			= array();
	public $__styles				= array();
	
	
	final public function __construct($route)
	{
		$this->__route	 	= $route;
		
		$res		= '/' . Dispatcher::$folder_resources . '/' . Application_Config::$folder_resources . '/';
		$res_atom	= $res . Atom_Config::$folder_resources . '/';
		
		$this->__paths = array
		(
			'styles'		=> $res_atom . 'styles/',
			'javascripts'	=> $res_atom . 'javascripts/',
		
			'public'		=> $res,
			'files'			=> $res . 'files/'
		);
		
		/*
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if(strchr($user_agent, 'MSIE'))		{ $this->__browser = 'msie'; }
		if(strchr($user_agent, 'Firefox'))	{ $this->__browser = 'firefox'; }
		if(strchr($user_agent, 'Safari'))	{ $this->__browser = 'safari'; }
		if(strchr($user_agent, 'Opera'))	{ $this->__browser = 'opera'; }
		*/
		
		$this->__initialize();
	}
	
	protected function __initialize(){}
	
	public final function __executeController()
	{
		/*
		if(Registry::$globals)
		{
			$this->__globals = Registry::$globals;
		}
		else
		{
			$this->__globals = Localizer::getGlobals();
			Registry::$globals = $this->__globals;
		}
		
		$this->__labels = Localizer::load($this->__controller);
		Registry::$labels = $this->__labels;
		*/
		
		foreach($this->__before_filters as $filter){ $this->$filter(); }
		
		$this->{$this->__route->action}();
		
		foreach($this->__after_filters as $filter){ $this->$filter(); }
	}
	
	public final function __render()
	{
		de('TODO RENDER');
		
		// Set view file for loading
		$this->__view === null && $this->__view = $this->__action;
		
		// $view_file = strpos($this->__view, '../') === 0 ? substr($this->__view, 3) : $this->__controller.'/'.$this->__view;
		
		$template = clone Template();
		$template->assign(get_object_vars($this));
		return $template->fetch($view_file.'.tpl');
	}
	
	protected final function __before_filter($filter)
	{
		$this->__before_filters = array_merge($this->__before_filters, func_get_args());
	}
	
	
	protected final function __after_filter($filter)
	{
		$this->__after_filters = array_merge($this->__after_filters, func_get_args());
	}
	
	protected final function __javascript()
	{
		$this->__javascripts = array_merge($this->__javascripts, func_get_args());
	}
	
	protected final function __style()
	{
		$this->__styles = array_merge($this->__styles, func_get_args());
	}
	
	protected final function __library($name)
	{
		require_once Paths_Config::$atom_library . $name . '.php';
	}
	
}


?>
