<?php

abstract class BaseController
{
	/**
	 * Controller name
	 * @var string
	 */
	public $__controller;
	
	/**
	 * Action name
	 * @var sting
	 */
	public $__action;
	
	/**
	 * _GET variable
	 * @var array
	 */
	public $__get;
	
	/**
	 * _POST variables
	 * @var array
	 */
	public $__post;
	
	/**
	 * Is Post
	 * @var boolean
	 */
	private $__is_post;
	
	/**
	 * Is Ajax
	 * @var boolean
	 */
	private $__is_ajax;
	
	/**
	 * View name (without extension)
	 * @var sting
	 */
	public $__view;
	
	/**
	 * Locale labels
	 * @var array
	 */
	public $__labels;
	
	/**
	 * Global locale labels
	 * @var array
	 */
	public $__globals;
	
	/**
	 * Session class
	 * @var object 
	 */
	public $__session;
	
	/**
	 * Path to some of the folders (public, files), used in the templates
	 * @var array
	 */
	public $__paths;

	/**
	 * All before filter function names
	 * 
	 * @var array
	 */
	protected $__beforeFilters = array();
	
	/**
	 * All after filter function names
	 * 
	 * @var array
	 */
	protected $__afterFilters = array();
	
	/**
	 * Javascripts files to be included in the template. Without extensions. 
	 * 
	 * @var array
	 */
	public $includeJavascripts = array();
	
	/**
	 * CSS files to be included in the template. Without extensions. 
	 * 
	 * @var array
	 */
	public $includeCss = array();
	
	/**
	 * Default action name called if no action is specified.
	 * 
	 * @var string
	 */
	public $__defaultAction = 'index';
	
	/**
	 * What is the browser.
	 * 
	 * @var string
	 * @return string mise, firefox, safari or opera
	 */
	public $__browser;
		
	/**
	 * Constructor
	 */
	final public function __construct($__controller, $__action, $__get, $__post, $__is_ajax, $__url, $__route)
	{
		$this->__controller	= $__controller;
		$this->__action		= $__action;
		$this->__get 		= $__get;
		$this->__post 		= $__post;
		$this->__is_ajax 	= $__is_ajax;
		$this->__is_post 	= $__post ? true : false;
		$this->__url 		= $__url;
		$this->__route	 	= $__route;
		
		
		$this->__session 	= Registry::$session = SessionFactory::create();
		
		$this->__paths = array
		(
			'root'			=> getcwd(),
			'styles'		=> '/'.(Registry::$is_admin ? Dispatcher::$admin : Dispatcher::$public).'/styles/',
			'javascripts'	=> '/'.(Registry::$is_admin ? Dispatcher::$admin : Dispatcher::$public).'/javascripts/',
		
			'public'		=> '/'.Dispatcher::$public.'/',
			'files'			=> '/'.Dispatcher::$public.'/files/'
		);
		
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if(strchr($user_agent, 'MSIE'))		{ $this->__browser = 'msie'; }
		if(strchr($user_agent, 'Firefox'))	{ $this->__browser = 'firefox'; }
		if(strchr($user_agent, 'Safari'))	{ $this->__browser = 'safari'; }
		if(strchr($user_agent, 'Opera'))	{ $this->__browser = 'opera'; }
		
		$this->__initialize();
	}
	
	/**
	 * Executed at the end of the contstructor
	 */
	protected function __initialize(){}
	
	
	/**
	 * Check whether the request is an ajax call
	 * 
	 * @return boolean
	 */
	protected final function is_ajax(){ return $this->__is_ajax; }
	
	/**
	 * Check whether the request is a post call
	 * 
	 * @return boolean
	 */
	protected final function is_post(){ return $this->__is_post; }
	
		
	/**
	 * Executes the controller. This method is called from the router.
	 * 
	 * - Executes the before filters if there is
	 * - Calls the action
	 * - Executes the after filters if there is
	 */
	public final function __executeController()
	{
		// Assign controller variables 
		$action = $this->__action ?: $this->__defaultAction;
		
		$this->__action = method_exists($this, $action) ? $action : (method_exists($this, '__noaction') ? '__noaction' : $this->__defaultAction);
		$this->__view === null && $this->__view = $this->__action;
		
		// Load and assign global and controller's labels to controller and it the registry
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
		
		$this->executeBeforeFilters();
		$this->{$this->__action}();
		$this->executeAfterFilters();
	}
	
	public final function __render()
	{
		// Set view file for loading
		if($this->__view)
		{
			$view_file = strpos($this->__view, '../') === 0 ? substr($this->__view, 3) : $this->__controller.'/'.$this->__view;
			 
			// Assign variable and display the template
			$template = clone Template();
			$template->assign(get_object_vars($this));
			return $template->fetch($view_file.'.tpl');
		}
		else if($this->__view == 'JSON')
		{
			return json_encode($this->__json);
		}
		
		return get_object_vars($this);
	}
	
	
	/**
	 * Execute all before filter in $this->__beforeFilters array
	 */
	private final function executeBeforeFilters()
	{
		foreach($this->__beforeFilters as $filter){ $this->$filter(); }
	}
	
	
	/**
	 * Execute all after filter in $this->__beforeFilters array
	 */
	private final function executeAfterFilters()
	{
		foreach($this->__afterFilters as $filter){ $this->$filter(); }
	}
	
	/**
	 * Add before filter
	 * 
	 * @param string - every param is function name 
	 */
	protected final function addBeforeFilter($filter)
	{
		$this->__beforeFilters = array_merge($this->__beforeFilters, func_get_args());
	}
	
	
	/**
	 * Add before filter
	 * 
	 * @param string every param is function name 
	 */
	protected final function addAfterFilter($filter)
	{
		$this->__afterFilters = array_merge($this->__afterFilters, func_get_args());
	}
	
	
 	/**
 	 * Adds javascript filenames to be included in the header
 	 * 
 	 * @param string javascript filenames (without extension)
 	 */
	protected final function includeJavascript()
	{
		$this->includeJavascripts = array_merge($this->includeJavascripts, func_get_args());
	}
	
	
	/**
 	 * Adds css filenames to be included in the header
 	 * 
 	 * @param string css filenames (without extension)
 	 */
	protected final function includeCss()
	{
		$this->includeCss = array_merge($this->includeCss, func_get_args());
	}
	
	
	/**
	 * Load library from the library folder in the application
	 * 
	 * @param string $name library filename without extension
	 */
	protected final function loadLibrary($name)
	{
		include_once SystemConfig::$alibraryPath . $name . '.php';
	}
	
}


?>
