<?php

namespace library;

interface Controller
{
	public function __construct(RouterRequest $request);
}

abstract class BaseController implements Controller
{
	public $__request;
	
	protected $__view;
	
	private $__before_filters		= array();
	private $__after_filters		= array();
	
	public function __construct(RouterRequest $request)
	{
		$this->__request = $request;
		$this->__initialize();
	}
	
	protected function __initialize(){}
	
	public final function __execute()
	{
		foreach($this->__before_filters	as $filter){ $this->$filter(); }
		$content = $this->{'action__' . $this->__request->route->action}($this->__request->get);
		foreach($this->__after_filters	as $filter){ $this->$filter(); }
		
		return $content;
	}
	
	
	protected	final function __before_filter	($filter)	{ $this->__before_filters	= array_merge($this->__before_filters	, func_get_args()); }
	protected	final function __after_filter	($filter)	{ $this->__after_filters	= array_merge($this->__after_filters	, func_get_args()); }
	protected	final function __javascript		($file)		{ $this->__javascripts		= array_merge($this->__javascripts		, func_get_args()); }
	protected	final function __style			($file)		{ $this->__styles			= array_merge($this->__styles			, func_get_args()); }
	protected	final function __library		($name)		{ require_once Paths_Config::$atom_library . $name . '.php'; }
	
	public		final function __render			($data, $obj_vars)	{ return $this->{'__render_' . $this->__request->response_type}($data, $obj_vars); }
	private		final function __render_		($data, $obj_vars)	{ return $data; }
	private		final function __render_html	($data, $obj_vars)	{ return (new template\Template())->assign($obj_vars)->assign($data)->fetch($this->__view()); }
	private		final function __render_json	($data, $obj_vars)	{ return json_encode($data); }
	
	private final function __view()
	{
		return $this->__request->route->controller . '/' . ($this->__view === null ? $this->__request->route->action : $this->__view) . '.html';
	}
}

?>
