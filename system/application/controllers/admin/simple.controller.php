<?php

abstract class Simple_Controller extends Scaffolding_Controller
{
	/**
	 * Model for scaffolding
	 * 
	 * @var string
	 * @example ModelName
	 */
	protected $_model							= '';
	
	/**
	 * Columns for scaffolding listing 
	 * 
	 * @var array
	 * @example array('id', 'title', 'active')
	 */
	protected $_columns 						= array('title', 'active');
	
	/**
	 * Select fields for scaffolding listing 
	 * 
	 * @var string
	 * @example 'id, title, active'
	 */
	protected $_selector 						= '';
	
	/**
	 * Selector type can be 'only' or 'append'
	 * 
	 * @var array
	 * @example 'append'
	 */
	protected $_selector_type					= 'only';
	
	
	/**
	 * Joins for scaffolding listing
	 * 
	 * @var array
	 * @example array(array('model' => 'FloorGroup', 'type' => 'LEFT'));
	 */
	protected $_joins 							= array();
	
	/**
	 * Buttons for scaffolding listing after edit and delete buttons  
	 * 
	 * In the href attribute it is possible using '$primary', 
	 * it will be replaced with the current record primary key value
	 * 
	 * @var array
	 * @example array( array('title' => 'Button Title', 'class' => 'icon icon-type', 'href' => 'button_action/$primary', 'permission' => 'edit') )
	 */
	protected $_buttons 						= array();
	
	/**
	 * Buttons for scaffolding listing after edit and delete buttons  
	 * 
	 * 'title' 		attribute will be automatically get from the actions' locales
	 * 'href' 		attribute is optional the default value is the same as 'title'
	 * 'permission' attribute is optional the default value is 'edit'
	 * 'id' 		attribute is optional 
	 * 
	 * @var array
	 * @example array( array('title' => 'some_action', 'class' => 'some_action_class', 'href' => 'some_action', 'permission' => 'edit', 'id' => 'some_action') )
	 */
	protected $_actions 						= array();
	
	/**
	 * Conditions for filtering the scaffolding listing
	 * 
	 * @var string
	 * @example 'parent_id=2'
	 */
	protected $_conditions 						= '';
	
	/**
	 * Conditions for grouping the scaffolding listing
	 * 
	 * @var string
	 * @example 'id'
	 */
	protected $_group							= '';
	
	/**
	 * Conditions for ordering the scaffolding listing
	 * 
	 * @var string
	 * @example 'title ASC'
	 */
	protected $_order							= '';
	
	/**
	 * Conditions for limiting the scaffolding listing
	 * 
	 * @var string
	 */
	protected $_limit 							= '';
	
	/**
	 * Overwrites the user permissions for scaffolding listing
	 * 
	 * If the value is null it will automaticali check 
	 * for this permission in the current controller=
	 * 
	 * If the value is true it will be accsesible or false - inaccessible
	 * 
	 * This is useful if this is a sub controller and is using 
	 * its parent's user permissions.
	 * 
	 * @var array
	 * 
	 * @example in the redefined index you can set this as follows
	 * 		$this->_user_can_do	= array
	 * 		(
	 * 			'add' 		=> $this->_user_can('add', 'different_controller_name'),
	 * 			'auto' 		=> null,
	 * 			'denied' 	=> false,
	 * 			'granted'	=> true
	 * 		);
	 */
	protected $_user_can_do						= array();
	
	/**
	 * Fields for building a filter for the listing
	 * 
	 * @var array
	 * @example array('filter_name' => 'field_name')
	 */
	protected $_filters 	= array(); 
	
	/**
	 * Is there paging
	 *
	 * @var bool
	 */
	protected $_paging		= true;
	
	/**
	 * Is there sorting
	 *
	 * @var bool
	 */
	protected $_sorting		= true;
	
	
	protected $_colgroups						= array();
	
	protected $_listing_has_checkboxes 	= false;
	protected $_listing_has_ordering 			= false;
	
	protected $find_before_delete 				= false;
	protected $check_editable	 				= false;
	protected $check_deletable	 				= false;
	
	
	
	/**
	 * Event called before and after add and edit
	 *
	 * @param ActiveRecord $model model instantiated in the add action and edit, if edit is not redefined
	 */
	protected function _before_load($model, $params){}
	protected function _after_load($model, $params){}
	
	
	
	/**
	 * Event called before listing in index action if not redefined
	 *
	 * @param array() $params optional
	 */	
	protected function _before_listing($params){}
	protected function _after_listing($params){}
	
	
	/**
	 * Listing of the model data.
	 * 
	 * @param array $params
	 */
	public function index($params)
	{
		unset($this->__session->after_edit_uri);
		
		$this->_before_listing($params);
		
		$this->_listing(array
		(
			'model' 		=> $this->_model,
			'columns'		=> $this->_columns,
			'selector'		=> $this->_selector,
			'selector_type'	=> $this->_selector_type,
			'joins'			=> $this->_joins,
			'buttons'		=> $this->_buttons,
			'actions'		=> $this->_actions,
			'conditions'	=> $this->_conditions,
			'group'			=> $this->_group,
			'order'			=> $this->_order,
			'limit'			=> $this->_limit,
			'user_can'		=> $this->_user_can_do,
			'filters'		=> $this->_filters,
			'paging'		=> $this->_paging,
			'sorting'		=> $this->_sorting,
			'colgroups'		=> $this->_colgroups,
			'has_checkboxes'=> $this->_listing_has_checkboxes,
			'has_ordering'	=> $this->_listing_has_ordering,
		
			'check_editable'	=> $this->check_editable,
			'check_deletable'	=> $this->check_deletable,
			
			'action_params' => $params,
		));
		
		$this->_after_listing($params);
	}
	
		
	/**
	 * Saves form data.
	 * Expects form field name to be the same as the names of the columns in the database
	 * 
	 * If there is a id parameter and called through edit, this action will act as an edit
	 * 
	 * @param int $id record id 
	 */
	public function add($params)
	{
		$this->includeJavascript('tinymce/tiny_mce', 'tinymce/tiny_mce_init');
	
		$model = $this->_model;
		$model = new $model(isset($params['id']) ? $params['id'] : null);
		
		$this->_before_load($model, $params);
		
		$this->croppables();
		$this->files_from_session();
		
		if($this->__action == 'edit' && !$model->id || $this->check_editable && $model->_editable === '0'){ redirect(-1); }
		
		if(is_post())
		{
			if($model->save($_POST)){ redirect( $_POST['save_reload'] ? ($this->__action == 'edit' ? '../' : '').'../edit/'.$model->id : ($this->__session->after_edit_uri ?: '') ); }
			else					{ $this->errors = $model->errors; }
		}
		else if(substr($_SERVER['HTTP_REFERER'], strpos($_SERVER['HTTP_REFERER'], '://')+3) != $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])
		{
			$this->__session->after_edit_uri = $_SERVER['HTTP_REFERER'];
			$this->__session->after_edit_uri || $this->__session->after_edit_uri = url_for($this->__action == 'edit' ? '../../' : '../'); 
		}
		
		$this->data = $model->storage();
		$this->model = $model;
		
		$this->files_to_session();
		
		$this->_after_load($model, $params);
	}
	
	
	/**
	 * Calls add action and changes the view to add thus reusing the form. 
	 * 
	 * If there is no id parameter you will get redirected to index
	 * 
	 * @param array $params (in this particular case only id is expected)
	 *  
	 */
	public function edit($params)
	{
		if(!($id = (int)$params['id'])){ redirect(''); }
		
		$this->__view = 'add';
		$this->add($params);
	}
	
	
	/**
	 * Deletes a record from the database.
	 * 
	 * If there is no id parameter you will get redirected to index
	 * 
	 * @param array $params (only id is expected)
	 *  
	 */
	public function delete($params)
	{
		if(!($ids = (int)$params['id']) && !($ids = explode(',', $params['ids']))){ redirect(''); }
		is_array($ids) || $ids = array($ids);
		
		$model = $this->_model;
		$model = new $model();
		
		foreach($ids as $id)
		{
			if($this->check_deletable && $model->find($id, null, '_deletable')->_deletable === '0'){ continue; }
			$model->destroy($id, $this->find_before_delete);
		}
		
		if(is_ajax()){exit;}
		redirect(-1);
	}


	// FILE MANAGEMENT
	protected function croppables()
	{
		if(!isset($_POST['upload_croppable'])){ return; }
		
		foreach(array_keys($_POST['upload_croppable']) as $k)
		{
			$f = (object)$_FILES[$k];
			if($f->size == 0){ continue; }
			else if (is_uploaded_file($_FILES[$k]['tmp_name']))
			{
				unlink($this->__session->uploaded_files[$k]['tmp_name']);
			}
			
			$tmp_name = 'temp/'.uniqid().'.'.File::extension($f->name);
			$tmp_path = SystemConfig::$filesPath.$tmp_name;
			
			File::move($f->tmp_name, $tmp_path);
			
			$uploaded_files = $this->__session->uploaded_files;
			$uploaded_files[$k] = array
			(
				'name'			=> $f->name,
				'size'			=> $f->size,
				'type'			=> $f->type,
				'tmp_name'		=> $tmp_path,
				'public_name'	=> $tmp_name,
			);
			$this->__session->uploaded_files = $uploaded_files;
			
			echo '<script type="text/javascript">';
			echo "top.croppable('$k','$tmp_name')";
			echo '</script>';
		}
		
		exit;
	}
	
	protected function files_from_session()
	{
		if(!empty($this->__session->uploaded_files))
		{
			foreach($this->__session->uploaded_files as $k => $f)
			{
				if(is_uploaded_file($_FILES[$k]['tmp_name']) || $_POST['upload_delete'][$k])
				{
					File::delete($f['tmp_name']);
					
					// Tozi unset ne raboti po tozi nachin
					// unset($this->__session->uploaded_files[$k]);
					$uploaded_files = $this->__session->uploaded_files;
					unset($uploaded_files[$k]); 
					$this->__session->uploaded_files = $uploaded_files;
				}
				else
				{
					$f['name'] && $_FILES[$k] = $f;
				}
			}
		}
	}
	
	protected function files_to_session()
	{
		if(is_post() && !empty($this->errors) && !empty($_FILES))
		{
			$uploaded_files = $this->__session->uploaded_files;
			$uploaded_files || $uploaded_files = array();
			foreach($_FILES as $k => $f)
			{
				if($f['size'] == 0){ continue; }
				
				if(!$f['public_name'] && is_uploaded_file($f['tmp_name']))
				{
					$tmp_name = 'temp/'.uniqid();
					$tmp_path = SystemConfig::$filesPath.$tmp_name;
					File::move($f['tmp_name'], $tmp_path);
				}
				else { $tmp_path = $f['tmp_name']; }
				
				$uploaded_files[$k] = array
				(
					'name'			=> $f['name'],
					'size'			=> $f['size'],
					'type'			=> $f['type'],
					'tmp_name'		=> $tmp_path,
				);
				
				if ($_POST['upload_crop_coords'][$k])
				{
					$uploaded_files[$k] = array_merge($uploaded_files[$k], array('coords' => $_POST['upload_crop_coords'][$k]), array('public_name' => $f['public_name']));
				}
			}
			$this->__session->uploaded_files = $uploaded_files;
		}
		else
		{
			unset($this->__session->uploaded_files);
		}
	}
}

?>
