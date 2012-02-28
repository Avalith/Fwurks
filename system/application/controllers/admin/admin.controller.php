<?php

abstract class Admin_Controller extends Application_Controller
{
	public $_admin_menu;
	public $admin_menu_locales;
	public $language_locales;
	public $language_locale;
	public $language_locale_default;
	
	protected $user;
	protected $bypass_permissions = false;
	
	public $settings;
	
	public $breadcrumbs = array();
	
	public function __construct()
	{
		parent::__construct();
		
		$this->addBeforeFilter(array('initialize'));
		$this->includeJavascript(array('validator', 'jquery-ui-datepicker', 'interface','humanmsg'));
		$this->includeCss('humanmsg');
	}
	
	protected function initialize($params) 
	{
		$this->settings = Registry()->settings = new AdminSettings();
		
		$this->__session->start();
		$home_controller = Router::$routes['home'][1];
		
		$this->language_locales = Registry()->locales;
		$this->language_locale = Registry()->locale;
		
		if($this->__controller != 'login')
		{
			if(!($uid = $this->__session->logged_user->id)){ redirect(array(':controller' => 'login')); }
			else 
			{
				switch ($this->__session->logged_user->display_name)
				{
				    case $this->__labels['__globals']['DISPLAY_NAMES']['full_name']:
				        $this->__session->logged_user->_display_name = $this->__session->logged_user->first_name.' '.$this->__session->logged_user->last_name;
				        break;
				    case $this->__labels['__globals']['DISPLAY_NAMES']['custom']:
				        $this->__session->logged_user->_display_name = $this->__session->logged_user->nick_name;
				        break;
				    default:
				         $this->__session->logged_user->_display_name = $this->__session->logged_user->username;
				        break;
				}
			}
			$this->user = new AdminUser($uid);
			$this->user->turn_cache('off');	
			
			if
			(
				// will bypass the permission check if: 
				// --	bypass_permissions == true, 
				// --	bypass_permissions == current action,
				// --	bypass_permissions is array and one of its values == current action
				(	
					$this->bypass_permissions === false
					||
					(
						$this->bypass_permissions != $this->__action 
						||	(is_array($this->bypass_permissions) && !in_array($this->__action, $this->bypass_permissions))
					) 
				)
				// end bypass
				&& 	$this->__action != 'access_denied' 
				&& 	!$this->user->can()
			)
			{ redirect('//access_denied'); }
			
			$this->_admin_menu = Spyc::YAMLLoad(file_get_contents(SystemConfig::$configsPath.'admin_menu.yaml'));
			$this->admin_menu_locales = Localizer::load(Registry()->locale, '_admin_menu');
			$this->action_locales = $this->__labels['__globals']['ACTIONS'];
			
			$this->_selected_path = '//'.(Router::$url ? Router::$url : implode('/', Router::$routes['home']));
			$this->init_breadcrumbs();
			
			$this->addAfterFilter('init_admin_menu');
		}
	}
	
	protected function init_admin_menu()
	{
		$this->user->reload_permissions();
		$this->traverse_menu($this->_admin_menu);
	}
	
	private function traverse_menu(&$array, $link = '//')
	{
		$return = false;
		foreach ($array as $k => &$v)
		{
			$controller = $v['real_controller'] ? $v['real_controller'] : ($v['controller'] ? $v['controller'] : $k);
			
			if( 
				($v['permission_name'] && !$this->_user_can('index', $v['permission_name'])) 
				||(
					!$v['permission_name'] && $v['type'] != 'link'
					&&	
					(	($v['type'] == 'controller' && !$this->_user_can('index', $controller)) 
					||	($v['type'] == 'action' 	&& !$this->_user_can($k, $controller))
					)
				)
			){ 
				if($v['menu']){ $v['type'] = 'link'; } else { unset($array[$k]); continue; } 
			}
			
			if( !($v['title'] = $this->admin_menu_locales[$k]) ){ $v['title'] = ($v['type'] == 'action' && $this->action_locales[$k]) ? $this->action_locales[$k] : $k; }
			
			if(!$v['link']){ $v['link'] = $link.$k; }
			if($v['menu'] && !$this->traverse_menu($v['menu'], $v['link'].'/') && $v['type'] == 'link'){ unset($array[$k]); continue; }
			$v['path'] = $k;
			
			$return = true;
		}
		return $return;
	}
	
	protected function init_breadcrumbs()
	{
		$path = explode('/', trim($this->_selected_path, '/'));
		
		$admin_menu_part = $this->_admin_menu;
		foreach ($path as $p)
		{
			$title = '';
			
			
			if(!$admin_menu_part[$p] && !$this->action_locales[$p]){ continue; }
			
			$title = $admin_menu_part[$p]['type'] == 'action' || !$admin_menu_part ? $this->action_locales[$p] : $this->admin_menu_locales[$p];
			$link = $link ? $link.'/'.$p : '//'.$p;
			
			$this->breadcrumbs[] = array
			(
				'title' => $title ? $title : $p,
				'link' 	=> $link,
				'type' 	=> $admin_menu_part[$p]['type']
			);
			
			$admin_menu_part = $admin_menu_part[$p]['menu'];
		}
	}
	
	public function _user_can($action = null, $controller = null)
	{
		return $this->user->can($action, $controller);
	}
	
	public function access_denied()
	{
		$this->__view = '../_shared/access_denied';
	}
	
	
	public static function actions(){ return array('listing' => 'index', 'add', 'edit', 'delete'); }
}

?>
