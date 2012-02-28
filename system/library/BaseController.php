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
	 * View name (without extension)
	 * @var sting
	 */
	public $__view;
	
	/**
	 * Layout name (without extension)
	 * @var sting
	 */
	public $__layout = 'layout';
	
	/**
	 * Locale label
	 * @var array
	 */
	public $__labels;
	
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
	 * Model to be assigned in the controller for easy use with $this.
	 * All model names are divided with comma(,) and a model can be assigned with different name with 'as'.   
	 * 
	 * @example 	$__models = 'SomeModel, AnotherModel as am, that_model, other_model as om'
	 * 
	 * SomeModel 	will assign SomeModel 		and will be available through $this->SomeModel
	 * AnotherModel will assign AnotherModel 	and will be available through $this->am
	 * that_model 	will assign ThatModel		and will be available through $this->that_model
	 * other_model 	will assign OtherModel	 	and will be available through $this->om
	 * 
	 * @var string
	 */
	protected $__models;
	
	/**
	 * All before filter function names
	 * 
	 * @var array
	 */
	private $__beforeFilters = array();
	
	/**
	 * All after filter function names
	 * 
	 * @var array
	 */
	private $__afterFilters = array();
	
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
	 * Google Analytics.
	 * 
	 * @var string
	 * @return Google Analytics ID
	 */
	public $__google_analytics;
	
	
	public function __construct()
	{
		$this->__session = Registry()->session = SessionFactory::create();
		
		$this->__paths = array
		(
			'public'		=> '/'.Dispatcher::$public,
			'styles'		=> '/'.Dispatcher::$public.'/styles/',
			'javascripts'	=> '/'.Dispatcher::$public.'/javascripts/',
			'files'			=> '/'.Dispatcher::$public.'/files/'
		);
		
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if(strchr($user_agent, 'MSIE'))		{ $this->__browser = 'msie'; }
		if(strchr($user_agent, 'Firefox'))	{ $this->__browser = 'firefox'; }
		if(strchr($user_agent, 'Safari'))	{ $this->__browser = 'safari'; }
		if(strchr($user_agent, 'Opera'))	{ $this->__browser = 'opera'; }
		
		$admin_settings = new AdminSettings();
		$this->__google_analytics = $admin_settings->find_by_name('google_analytics')->value;
	}
	
	
	/**
	 * Executes the controller. This method is called from the router.
	 * 
	 * - Assign models
	 * - Executes the before filters if there is
	 * - Calls the action
	 * - Executes the after filters if there is
	 * 
	 * @param array $action_params parameters from the url and $_GET
	 */
	public final function __executeController($action_params)
	{
		$this->assignModels();
		
		$this->executeBeforeFilters($action_params);
		$this->{$this->__action}($action_params);
		$this->executeAfterFilters($action_params);
	}
	
	
	/**
	 * Assigns the models from $this->__models in the controller for easy use with $this.
	 * All model names are divided with comma(,) and a model can be assigned with different name with 'as'.   
	 * 
	 * @example 	$__models = 'SomeModel, AnotherModel as am, that_model, other_model as om'
	 * 
	 * SomeModel 	will assign SomeModel 		and will be available through $this->SomeModel
	 * AnotherModel will assign AnotherModel 	and will be available through $this->am
	 * that_model 	will assign ThatModel		and will be available through $this->that_model
	 * other_model 	will assign OtherModel	 	and will be available through $this->om
	 * 
	 */
	private final function assignModels()
	{
		foreach(explode(', ', $this->__models) as $m)
		{
			if($m)
			{
				$m = explode(' as ', $m);
				
				// if there is 'as' statement and a short title 
				// it will set the model with this name
				// otherwise it will set it with the model name
				$m_name = Inflector()->classify($m[0]);
				$this->{$m[1] ? $m[1] : $m[0]} = new $m_name();
			}
		}
	}
	
	
	/**
	 * Execute all before filter in $this->__beforeFilters array
	 */
	private final function executeBeforeFilters($action_params)
	{
		foreach($this->__beforeFilters as $filter){ $this->$filter($action_params); }
	}
	
	
	/**
	 * Execute all after filter in $this->__beforeFilters array
	 */
	private final function executeAfterFilters($action_params)
	{
		foreach($this->__afterFilters as $filter){ $this->$filter($action_params); }
	}
	
	/**
	 * Add before filter function name
	 * 
	 * @param stirng|array $function_names
	 */
	protected final function addBeforeFilter($function_names)
	{
		$this->__beforeFilters = array_merge($this->__beforeFilters, is_array($function_names) ? $function_names : array($function_names));
	}
	
	
	/**
	 * Add before filter function name
	 * 
	 * @param stirng|array $function_names
	 */
	protected final function addAfterFilter($function_names)
	{
		$this->__afterFilters = array_merge($this->__afterFilters, is_array($function_names) ? $function_names : array($function_names));
	}
	
	
 	/**
 	 * Adds javascript filename to be included in the header
 	 * 
 	 * @param string|array javascript filename without extension
 	 */
	protected final function includeJavascript($files)
	{
		$files = is_array($files) ? $files : array($files);
		$this->includeJavascripts = array_merge($this->includeJavascripts, $files);
	}
	
	
	/**
 	 * Adds css filename to be included in the header
 	 * 
 	 * @param string|array css filename without extension
 	 */
	protected final function includeCss($files)
	{
		$files = is_array($files) ? $files : array($files);
		$this->includeCss = array_merge($this->includeCss, $files);
	}
	
	
	/**
	 * Load library from the library folder in the application
	 * 
	 * @param string $name library filename without extension
	 */
	protected final function loadLibrary($name)
	{
		$alibrary_path = Dispatcher::$admin_application && Registry()->is_admin ? SystemConfig::$admin_alibraryPath : SystemConfig::$alibraryPath;
		include_once $alibrary_path.$name.'.php';
	}
	
}


?>