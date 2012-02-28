<?php

abstract class Simple_Controller extends Scaffolding_Controller
{
	/**
	 * Model for scaffolding
	 * 
	 * @var string
	 * @example ModelName
	 */
	protected $_model		= '';
	
	/**
	 * Columns for scaffolding listing 
	 * 
	 * @var array
	 * @example array('id', 'title', 'active')
	 */
	protected $_columns 	= array('title', 'active');
	
	/**
	 * Joins for scaffolding listing
	 * 
	 * @var array
	 * @example array(array('model' => 'FloorGroup', 'type' => 'LEFT'));
	 */
	protected $_joins 		= array();
	
	/**
	 * Buttons for scaffolding listing after edit and delete buttons  
	 * 
	 * In the href attribute it is possible using '$primary', 
	 * it will be replaced with the current record primary key value
	 * 
	 * @var array
	 * @example array( array('title' => 'Button Title', 'class' => 'icon icon-type', 'href' => 'button_action/$primary', 'permission' => 'edit') )
	 */
	protected $_buttons 	= array();
	
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
	protected $_actions 	= array();
	
	/**
	 * Conditions for filtering the scaffolding listing
	 * 
	 * @var string
	 * @example 'parent_id=2'
	 */
	protected $_conditions 	= '';
	
	/**
	 * Conditions for ordering the scaffolding listing
	 * 
	 * @var string
	 * @example 'title ASC'
	 */
	protected $_order		= '';
	
	/**
	 * Conditions for limiting the scaffolding listing
	 * 
	 * @var string
	 */
	protected $_limit 		= '';
	
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
	 * 			'granted'	=> granted'
	 * 		);
	 */
	protected $_user_can_do	= array();
	
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
	
	
	protected $_colgroups	= array();
	
	protected $_listing_has_checkboxes 	= false;
	protected $_listing_has_ordering 	= false;
	
	protected $find_before_delete 		= false;
	
	
	/**
	 * Event called after add and edit, if edit is not redefined
	 *
	 * @param ActiveRecord $model model instantiated in the add action and edit, if edit is not redefined
	 */
	protected function _load($model){}
	
	
	/**
	 * Event called before listing in index action if not redefined
	 *
	 * @param array() $params optional
	 */	
	protected function _before_listing($params){}
	
	
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
			'joins'			=> $this->_joins,
			'buttons'		=> $this->_buttons,
			'actions'		=> $this->_actions,
			'conditions'	=> $this->_conditions,
			'order'			=> $this->_order,
			'limit'			=> $this->_limit,
			'user_can_do'	=> $this->_user_can_do,
			'filters'		=> $this->_filters,
			'paging'		=> $this->_paging,
			'sorting'		=> $this->_sorting,
			'colgroups'		=> $this->_colgroups,
			'has_checkboxes'=> $this->_listing_has_checkboxes,
			'has_ordering'	=> $this->_listing_has_ordering,
			
			'action_params' => $params,
		));
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
		$this->includeJavascript(array('tinymce/tiny_mce', 'tinymce/tiny_mce_init'));
	
		$model = $this->_model;
		$model = new $model($params['id']);
		
		if($this->__action == 'edit' && !$model->id){ redirect(-1); }
		
		if(is_post())
		{
			if($model->save($_POST)){ $this->__session->after_edit_uri ? redirect($this->__session->after_edit_uri, 0) : redirect(''); }
			else					{ $this->errors = $model->errors; }
		}
		else
		{
			$this->__session->after_edit_uri = $_SERVER['HTTP_REFERER'];
		}
		
		$this->data = $model->get_storage();
		
		$this->_load($model, $params);
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
		if(!($id = (int)$params['id']) && !($id = explode(',', $params['ids']))){ redirect(''); }
		
		$model = $this->_model;
		$model = new $model();
		if($model->delete($id, $this->find_before_delete)){ redirect(''); }
	}
}

?>
