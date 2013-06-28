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
	public function __construct()
	{
		$this->__session = Registry::$session = SessionFactory::create();
		
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
	 * Executes the controller. This method is called from the router.
	 * 
	 * - Assign models
	 * - Executes the before filters if there is
	 * - Calls the action
	 * - Executes the after filters if there is
	 * 
	 * @param array $action_params parameters from the url and $_GET
	 */
	public final function __executeController($controller, $action, $action_params)
	{
		// Assign controller variables 
		$this->__controller = $controller;
		$action || $action = $this->__defaultAction;
		$this->__action = method_exists($this, $action) ? $action : (method_exists($this, '__noaction') ? '__noaction' : $this->__defaultAction);
		$this->__view === null && $this->__view = $this->__action;
		
		// Load and assign global and controller's labels to controller and it the registry
		$this->__globals = Localizer::getGlobals();
		$this->__labels = Localizer::load($controller);
		Registry::$globals = $this->__globals;
		Registry::$labels = $this->__labels;
		
		Registry::$action_params = $action_params;
		
		$this->assignModels();
		$this->executeBeforeFilters($action_params);
		$this->{$this->__action}($action_params);
		$this->executeAfterFilters($action_params);
	}
	
	public final function __render($variables)
	{
		// Set view file for loading
		if($this->__view)
		{
			$view_file = strpos($this->__view, '../') === 0 ? substr($this->__view, 3) : $this->__controller.'/'.$this->__view;
			 
			// Assign variable and display the template
			Template()->assign(get_object_vars($this));
			Template()->display($view_file.'.tpl');
		}
		
	}
	
	// Partial
	public final function __executePartialController($params)
	{
		$this->assignModels();
		
		is_array(Registry::$action_params) || Registry::$action_params = array();
		is_array($params) || $params = array();
		$this->{$this->__action}(array_merge(Registry::$action_params, $params));
	}
	
	public final function __renderPartial($params)
	{
		if($params['controller'])
		{ 
			if(file_exists($class = SystemConfig::$controllersPath.strtolower(Router::admin_or_public()).'/'.$params['controller'].'.controller.php'))
			{
				require_once $class;
				$controller_name = $params['controller'];
			}
			else{ return; }
		}
		else{ $controller_name = $this->__controller; }
		
		$action = $params['action'] ? $params['action'] : $this->__action;
		unset($params['controller'], $params['action']);
		
		$controller = Inflector::classify($controller_name) . '_Controller';
		$controller = new $controller();
		
		
		$controller->__controller 	= $controller_name;
		$controller->__action 		= 'partial__' . $action;
		$controller->__labels 		= ($controller_name == $this->__controller) ? $this->__labels : Localizer::load(Registry::$locales->current['code'], $controller_name);
		$controller->__globals 		= $this->__globals;
		$controller->__view 		= $action;
		$controller->__session 		= $this->__session;
		
		$controller->__executePartialController($params);
		
		$result = new stdClass();
		$result->variables = get_object_vars($controller);
		$result->params = $params;
		
		$template = clone Template();
		$template->assign($result->variables);
		$template->assign('params', $result->params);
		$result->template = $template->fetch($controller_name.'/__'.$action.'.tpl');
		
		return $result;
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
			if(!$m){ continue; }
			
			$m = explode(' as ', $m);
			
			// if there is 'as' statement and a short title 
			// it will set the model with this name
			// otherwise it will set it with the model name
			$m_name = Inflector()->classify($m[0]);
			$this->{$m[1] ? $m[1] : $m[0]} = new $m_name();
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
	 * Add before filter
	 * 
	 * @param string - every param is function name 
	 */
	protected final function addBeforeFilter()
	{
		$this->__beforeFilters = array_merge($this->__beforeFilters, func_get_args());
	}
	
	
	/**
	 * Add before filter
	 * 
	 * @param string every param is function name 
	 */
	protected final function addAfterFilter()
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