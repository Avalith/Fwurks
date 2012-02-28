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
	
	public function __initialize()
	{
		$this->addBeforeFilter('initialize');
		$this->includeJavascript('jquery', 'jquery.jcrop', 'validator', 'image_box', 'jquery-ui-datepicker', 'interface', 'humanmsg');
		$this->includeCss('jquery-ui', 'jquery.jcrop', 'humanmsg');
	}
	
	protected function initialize($params) 
	{
		Registry::$settings = $this->settings = new AdminSettings();
		
		$this->__session->start();
		
		$home_controller = Router::$routes['home'][1];
		
		$this->language_locales = Registry::$locales->info;
		$this->language_locale = Registry::$locales->current;
		
		if($this->__controller != 'login')
		{
			if(!($this->user = $this->__session->logged_user)){ redirect('//login'); }
			else 
			{
//				de($this->__session->logged_user);
				switch ($this->__session->logged_user->display_name)
				{
				    case $this->__globals['DISPLAY_NAMES']['full_name']:
				        $this->__session->logged_user->_display_name = $this->__session->logged_user->first_name.' '.$this->__session->logged_user->last_name;
				        break;
				    case $this->__globals['DISPLAY_NAMES']['custom']:
				        $this->__session->logged_user->_display_name = $this->__session->logged_user->nick_name;
				        break;
				    default:
				         $this->__session->logged_user->_display_name = $this->__session->logged_user->username;
				        break;
				}
			}
			
			$this->user->load_group();
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
			
			$this->load_admin_menu();
			$this->action_locales = $this->__globals['ACTIONS'];
			
			$this->_selected_path = '//'.(Router::$url ? Router::$url : Router::$routes['home'][0]);
			$this->init_breadcrumbs();
			
			$this->addAfterFilter('init_admin_menu');
		}
	}
	
	protected function load_admin_menu()
	{
		// Load Admin Menu YAML begin
		$admin_menu_yaml_path = SystemConfig::$configsPath.'admin_menu.yaml';
		$admin_menu_yaml_path_compile = SystemConfig::$tempPath.'locales_compile/admin_menu.yaml';
		$this->_admin_menu = array();
		if(file_exists($admin_menu_yaml_path))
		{
			if(file_exists($admin_menu_yaml_path_compile) && filemtime($admin_menu_yaml_path_compile) >= filemtime($admin_menu_yaml_path))
			{
				$this->_admin_menu = include $admin_menu_yaml_path_compile;
			}
			else
			{
				$this->_admin_menu = Spyc::YAMLLoad(file_get_contents($admin_menu_yaml_path));
				file_put_contents($admin_menu_yaml_path_compile, '<?php class_exists(Dispatcher) || exit; return '.var_export($this->_admin_menu, 1).'; ?>');
			}
		}
		// Load Admin Menu YAML end
		
		$this->admin_menu_locales = Localizer::load('_admin_menu');
	}
	
	
	protected function init_admin_menu()
	{
		$this->traverse_menu($this->_admin_menu);
	}
	
	private function traverse_menu(&$array, $link = '//')
	{
		$return = false;
		foreach ($array as $k => &$v)
		{
			if($v == 'separator'){ continue; }
			$controller = isset($v['real_controller']) ? $v['real_controller'] : (isset($v['controller']) ? $v['controller'] : $k);
			
			if( 
				(isset($v['permission_name']) && !$this->_user_can('index', $v['permission_name'])) 
				||(
					!isset($v['permission_name']) && $v['type'] != 'link'
					&&	
					(	($v['type'] == 'controller' && !$this->_user_can('index', $controller)) 
					||	($v['type'] == 'action' 	&& !$this->_user_can($k, $controller))
					)
				)
			){ 
				if($v['menu']){ $v['type'] = 'link'; } else { unset($array[$k]); continue; } 
			}
			
			if( !($v['title'] = (isset($this->admin_menu_locales[$k]) ? $this->admin_menu_locales[$k] : '')) ){ 
				$v['title'] = 
				($v['type'] == 'action' && $this->action_locales[$k]) 
				? $this->action_locales[$k] : 
				$k; }
			
			if(!isset($v['link'])){ $v['link'] = $link.$k; }
			if(isset($v['menu']) && !$this->traverse_menu($v['menu'], $v['link'].'/') && $v['type'] == 'link'){ unset($array[$k]); continue; }
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
			
			if(!isset($admin_menu_part[$p]) && !isset($this->action_locales[$p])){ continue; }
			
			$title = $admin_menu_part[$p]['type'] == 'action' || !$admin_menu_part ? $this->action_locales[$p] : $this->admin_menu_locales[$p];
			$link = isset($link) ? $link.'/'.$p : '//'.$p;
			
			$this->breadcrumbs[] = array
			(
				'title' => $title ? $title : $p,
				'link' 	=> $link,
				'type' 	=> $admin_menu_part[$p]['type']
			);
			isset($admin_menu_part[$p]['menu']) || $admin_menu_part[$p]['menu'] = null;
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
	
	
	protected function setBreadcurmbs()
	{
		return array
		(
			$this->__controller => 'Controller Name',
			$this->__action => $this->__globals[''],
			$this->data->id	=> $this->data->title,
		);
	}
	
	public static function actions(){ return array('listing' => 'index', 'add', 'edit', 'delete'); }
}

?>
