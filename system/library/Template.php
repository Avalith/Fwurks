<?php

require_once dirname(__FILE__).'/smarty/Smarty.php';

final class Template extends Smarty
{
	public $compiler_file 	= 'Smarty_Compiler_New.class.php';
    public $compiler_class 	= 'Smarty_Compiler_New';
	
	function __construct()
	{
		$this->Smarty();
		
		$admin_or_public = Registry()->is_admin ? 'admin' : 'public';
		
		if(Dispatcher::$admin_application && Registry()->is_admin)
		{
			$views_path = SystemConfig::$admin_viewsPath;
			$temp_path = SystemConfig::$admin_tempPath;
		}
		else
		{
			$views_path = SystemConfig::$viewsPath;
			$temp_path = SystemConfig::$tempPath;
		}		
		
		$this->force_compile = SystemConfig::FORCE_COMPILE;
		
		$this->template_dir 	= $views_path 	. $admin_or_public . '/';
		$this->config_dir 		= $views_path 	. 'configs/' . $admin_or_public . '/';
		$this->compile_dir 		= $temp_path 	. 'tpl_compile/'.$admin_or_public . '/';
		$this->cache_dir 		= $temp_path 	. 'tpl_cache' . '/';
		
		$this->plugins_dir[]	= 'customs';
	}
	
	/**
	 * Instances
	 */
	private static $instance;
	public static function getInstance()
	{
		if(!self::$instance){ self::$instance = new Template(); }
		return self::$instance;
	}
}
function Template(){ return Template::getInstance(); }

?>
