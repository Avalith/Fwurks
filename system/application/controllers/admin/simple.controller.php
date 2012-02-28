<?php

abstract class Simple_Controller extends Scaffolding_Controller
{
	/**
	 * Model for scaffolding
	 * 
	 * @var string
	 * @example ModelName
	 */
	protected $_model = '';
	
	protected $_find_before_delete 	= false;
	
	
	/**
	 * model 			=> $this->_model,
	 * columns			=> array('title', 'active'),
	 * selector			=> null,
	 * joins			=> array(),
	 * conditions		=> array(),
	 * group			=> null,
	 * order			=> null,
	 * limit			=> null,
	 * 
	 * paging			=> true,
	 * sorting			=> true,
	 * filters			=> array(),
	 * colgroups		=> array(),
	 * 
	 * check_editable	=> $this->_check_editable,
	 * check_deletable	=> $this->_check_deletable,
	 * has_checkboxes	=> false,
	 * has_ordering		=> false,
	 * 
	 * buttons			=> array(),
	 * actions			=> array(),
	 * 
	 * user_can			=> array(),
	 * 
	 * @return Array:
	 */
	protected function _listing_params($params)
	{
		return array();
	}
	
	
	/**
	 * Event called before add and edit
	 *
	 * @param ActiveRecord $model model instantiated in the add and edit actions, if not redefined
	 */
	protected function _before_load($model, $params){}
	
	
	/**
	 * Event called after add and edit
	 *
	 * @param ActiveRecord $model model instantiated in the add and edit actions, if not redefined
	 */
	protected function _after_load($model, $params){}
	
	
	/**
	 * Event called before listing in index action if not redefined
	 *
	 * @param array() $params optional
	 */	
	protected function _before_listing($params){}
	
	
	/**
	 * Event called after listing in index action if not redefined
	 *
	 * @param array() $params optional
	 */	
	protected function _after_listing($params){}
	
	
	/**
	 * Listing of the model data.
	 * 
	 * @param array $params
	 */
	public function index()
	{
		unset($this->__session->after_edit_uri);
		
		$this->_before_listing($this->__get);
		$this->_listing($this->_listing_params($this->__get), $this->__get);
		$this->_after_listing($this->__get);
	}
	
		
	/**
	 * Saves form data.
	 * Expects form field name to be the same as the names of the columns in the database
	 * 
	 * If there is a id parameter and called through edit, this action will act as an edit
	 * 
	 * @param int $id record id 
	 */
	public function add()
	{
		$this->includeJavascript('tinymce/tiny_mce', 'tinymce/tiny_mce_init');
		
		$model 	= $this->_model;
		$pk		= $model::primary_keys(0);
		$model 	= new $model(isset($this->__get['id']) ? $this->__get['id'] : null);
		
		
		$this->_before_load($model, $this->__get);
		
		$this->croppables();
		$this->files_from_session();
		
		if($this->__action == 'edit' && !$model->$pk || $this->_check_editable && $model->_editable === '0'){ redirect('../../'); }
		
		if($this->is_post())
		{
			$this->_check_editable && $_POST['_editable'] = $model->_editable;
			$this->_check_deletable && $_POST['_deletable'] = $model->_deletable;
			
			if($model->save($_POST)){ redirect( isset($_POST['save_reload']) ? ($this->__action == 'edit' ? '../' : '').'../edit/'.$model->$pk : ($this->__session->after_edit_uri ?: '') ); }
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
		
		$this->_after_load($model, $this->__get);
	}
	
	
	/**
	 * Calls add action and changes the view to add thus reusing the form. 
	 * 
	 * If there is no id parameter you will get redirected to index
	 * 
	 * @param array $params (in this particular case only id is expected)
	 *  
	 */
	public function edit()
	{
		if(!($id = (int)$this->__get['id'])){ redirect('../'); }
		
		$this->__view = 'add';
		$this->add();
	}
	
	
	/**
	 * Deletes a record from the database.
	 * 
	 * If there is no id parameter you will get redirected to index
	 * 
	 * @param array $params (only id is expected)
	 *  
	 */
	public function delete()
	{
		if(!($ids = (int)$this->__get['id']) && !($ids = explode(',', $this->__get['ids']))){ redirect(''); }
		is_array($ids) || $ids = array($ids);
		
		$model = $this->_model;
		$model = new $model();
		
		foreach($ids as $id)
		{
			if($this->_check_deletable && $model->get($id)->_deletable === '0'){ continue; }
			$model->destroy($id, $this->_find_before_delete);
		}
		
		if(is_ajax()){exit;}
		redirect(-1);
	}
	
	public function view()
	{
		$id = (int)$this->__get['id'];
		$id || redirect('../');
		
		$model = $this->_model;
		$model = new $model($id);
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
				File::delete($this->__session->uploaded_files[$k]['tmp_name']);
			}
			
			$tmp_name = 'temp/'.uniqid().'.'.File::extension($f->name);
			$tmp_path = SystemConfig::$filesPath.$tmp_name;
			
			File::move($f->tmp_name, $tmp_path);
			
			$uploaded_files = isset($this->__session->uploaded_files) ? $this->__session->uploaded_files : array();
			$uploaded_files[$k] = array
			(
				'name'			=> $f->name,
				'size'			=> $f->size,
				'type'			=> $f->type,
				'tmp_name'		=> $tmp_path,
				'public_name'	=> $tmp_name,
			);
			$this->__session->uploaded_files = $uploaded_files;
			
			echo "<script type=\"text/javascript\">top.croppable('$k','$tmp_name')</script>";
		}
		
		exit;
	}
	
	protected function files_from_session()
	{
		if(!empty($this->__session->uploaded_files))
		{
			foreach($this->__session->uploaded_files as $k => $f)
			{
				if(isset($_FILES[$k]['tmp_name']) && is_uploaded_file($_FILES[$k]['tmp_name']) || (isset($_POST['upload_delete'][$k]) && $_POST['upload_delete'][$k]))
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
		if($this->is_post() && !empty($this->errors) && !empty($_FILES))
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
