<?php

class Pages_Controller extends Simple_Controller
{
	protected $_model 	= 'Pages';
	protected $find_before_delete = true;
	
	
	public function index($params)
	{
		$this->_tree(array(
			'type' 			=> 'Nested',
			'model'			=> $this->_model,
			'conditions'	=> 'navigation = 1',
			'user_can_do'	=> array(),
			'up_down'		=> true,
		));
		
		$this->_listing(array
		(
			'model' 		=> $this->_model,
			'columns'		=> array('title', 'active'),
			'joins'			=> $this->_joins,
			'buttons'		=> $this->_buttons,
			'actions'		=> $this->_actions,
			'conditions'	=> 'navigation = 0',
			'order'			=> $this->_order,
			'limit'			=> $this->_limit,
			'user_can_do'	=> $this->_user_can_do,
			'filters'		=> $this->_filters,
			'paging'		=> $this->_paging,
			'sorting'		=> $this->_sorting,
			'has_checkboxes'=> $this->_listing_has_checkboxes,
			
			'action_params' => $params,
		));
		
		$this->__view = 'index';
	}
	
	protected function _load($model)
	{
		$this->pages = TreeFactory::create('Nested', array('pages'))->load(($model->id ? "nleft < {$model->nleft} OR nright > {$model->nright} AND navigation=1" : 'navigation=1'), false);
	}
	
	public static function actions(){ return array('listing' => 'index', 'add', 'edit', 'delete'); }
}

?>
