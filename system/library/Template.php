<?php

require_once dirname(__FILE__).'/smarty/Smarty.class.php';

final class Template extends Smarty
{
	function __construct()
	{
		parent::__construct();
		
		$admin_or_public = Registry::$is_admin ? 'admin' : 'public';
		
		$views_path = SystemConfig::$viewsPath;
		$temp_path = SystemConfig::$tempPath;
		
		$this->auto_literal = true;
		$this->force_compile = SystemConfig::FORCE_COMPILE;
		
		$this->template_dir 	= $views_path 	. $admin_or_public . '/';
		$this->config_dir 		= $views_path 	. 'configs/' . $admin_or_public . '/';
		$this->compile_dir 		= $temp_path 	. 'tpl_compile/'.$admin_or_public . '/';
		$this->cache_dir 		= $temp_path 	. 'tpl_cache' . '/';
		
		$this->plugins_dir		= array(SMARTY_DIR.'plugins/', SMARTY_DIR.'customs/');
	}
	
	/**
	 * Instance
	 */
	public static function getInstance()
	{
		static $instance;
		if(!$instance){ $instance = new Template(); }
		return $instance;
	}
}
function Template(){ return Template::getInstance(); }

?>
