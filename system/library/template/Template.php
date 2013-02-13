<?php

namespace library\template;

require_once __DIR__.'/smarty/Smarty.class.php';

use Smarty, Paths_Config, Template_Config;

final class Template extends Smarty
{
	public function __construct()
	{
		parent::__construct();
		
		$this->auto_literal = true;
		
		$this->template_dir		= Paths_Config::$atom_views;
		$this->compile_dir		= Paths_Config::$atom_temp . 'tpl_compile/';
		
		$this->force_compile	= Template_Config::SMARTY_FORCE_COMPILE;
		$this->compile_check	= Template_Config::SMARTY_COMPILE_CHECK;
		$this->compile_check	= Template_Config::SMARTY_COMPILE_CHECK;
		
		$this->loadFilter(Smarty::FILTER_VARIABLE, "htmlentities");
		// $this->caching_type		= 'memcache';
		
		$this->addPluginsDir('./customs/');
	}
	
	public static function instance()
	{
		static $instance;
		
		$instance || $instance = new Template();
		
		return $instance;
	}
}

?>
