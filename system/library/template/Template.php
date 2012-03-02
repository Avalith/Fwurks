<?php

require_once __DIR__.'/smarty/Smarty.class.php';

final class Template extends Smarty
{
	public function __construct()
	{
		parent::__construct();
		
		$this->auto_literal = true;
		
//		$this->force_compile = System_Config::SMARTY_FORCE_COMPILE;
//		$this->compile_check = System_Config::SMARTY_COMPILE_CHECK;
		
		$this->template_dir 	= Paths_Config::$atom_views;
		$this->compile_dir 		= Paths_Config::$atom_temp . 'tpl_compile/';
		
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
