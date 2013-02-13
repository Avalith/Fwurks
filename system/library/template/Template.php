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
		
		$this->compile_check	= Template_Config::COMPILE_CHECK;
		$this->force_compile	= Template_Config::FORCE_COMPILE;
		
		$this->escape_html		= Template_Config::ESCAPE_HTML;
		
		// $this->caching_type		= 'memcache';
		
		$security_policy = new Smarty_Security($smarty);
		$security_policy->$static_classes		= Template_Config::SECURITY_STATIC_CLASSES;
		$security_policy->$php_functions		= Template_Config::SECURITY_PHP_FUNCTIONS;
		$security_policy->$php_modifiers		= Template_Config::SECURITY_PHP_MODIFIERS;
		$security_policy->$streams				= Template_Config::SECURITY_STREAMS;
		$security_policy->$allowed_modifiers	= Template_Config::SECURITY_ALLOWED_MODIFIERS;
		$security_policy->$disabled_modifiers	= Template_Config::SECURITY_DISABLED_MODIFIERS;
		$security_policy->$allowed_tags			= Template_Config::SECURITY_ALLOWED_TAGS;
		$security_policy->$disabled_tags		= Template_Config::SECURITY_DISABLED_TAGS;
		$security_policy->allow_constants		= Template_Config::SECURITY_ALLOW_CONSTANTS;
		$security_policy->allow_super_globals	= Template_Config::SECURITY_ALLOW_SUPER_GLOBALS;
		$security_policy->allow_php_tag			= Template_Config::SECURITY_ALLOW_PHP_TAG;
		
		$this->enableSecurity($security_policy);
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
