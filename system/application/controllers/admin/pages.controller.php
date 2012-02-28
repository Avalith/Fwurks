<?php

class Pages_Controller extends Simple_Controller
{
	protected $_model 	= 'Page';
	protected $_find_before_delete = true;
	
	protected $_check_editable 		= true;
	protected $_check_deletable 	= true;
	
	public function index()
	{
//		throw new Exception('Must be rewritten'); 
		
		$this->_tree(array
		(
			'type' 			=> 'Nested',
			'model'			=> $this->_model,
			'conditions'	=> "navigation = 1",
			'user_can'		=> null
		));
		
		$this->_listing(array
		(
			'conditions'	=> 'navigation = 0',
			'columns'		=> array('title', 'active'),
			'selector'		=> 'id, title, active', 
			'order'			=> 'title',
		), $this->__get);
		
		$this->__view = 'index';
	}
	
	protected function _after_load($model)
	{
		$_model = $this->_model;
		$this->pages = $_model::getParentsSelector($model);
	}
}

?>
