<?php

abstract class Scaffolding_Controller extends Admin_Controller
{
	protected $_user_can_do 		= false;
	
	protected $_check_editable 		= false;
	protected $_check_deletable 	= false;
	
	
	public function _listing(array $params, $action_params)
	{
		$this->__view = '../scaffolding/listing';
		
		$params = array_merge(array
		(
			'model' 			=> $this->_model,
			'columns'			=> array('title', 'active'),
			'selector'			=> null,
			'joins'				=> array(),
			'joinRel'			=> array(),
			'conditions'		=> array(),
			'group'				=> null,
			'order'				=> null,
			'limit'				=> null,
		
			'paging'			=> true,
			'sorting'			=> true,
			'filters'			=> array(),
			'colgroups'			=> array(),
		
			'check_editable'	=> $this->_check_editable,
			'check_deletable'	=> $this->_check_deletable,
			'has_checkboxes'	=> false,
			'has_ordering'		=> false,
		
			'buttons'			=> array(),
			'actions'			=> array(),
		
			'user_can'			=> $this->_user_can_do,
		), $params);
		
		$model = new $params['model']();
		$this->_listing = array();
		
		if(isset($action_params['boolean']) && isset($action_params['boolean_id']) && $this->_user_can('edit'))
		{
			$model::update(array($action_params['boolean'].'?sql' => 'IF('.$action_params['boolean'].',0,1)'))->where($model::primary_keys(0).' = ?', $action_params['boolean_id'])->run();
			redirect(-1);
		}
		else if($this->is_ajax() && $params['has_ordering'] && $action_params['save_listing_order'] && $_POST['listing_order'] && !isset($action_params['sorting']) && !isset($this->_listing['filters']) && $this->_user_can('edit'))
		{
			foreach($_POST['listing_order'] as $id => $order_index)
			{
				$model::update(array('order_index?i' => $order_index))->where($model::primary_keys(0).' = ?', $id)->run();
			}
			
			exit;
		}
		
		if($params['paging'])
		{
			$page = isset($action_params['page']) ? (int)$action_params['page'] : 0;
			$paging = new Paging($page, $this->settings->records_per_page);
			$params['limit'] = $paging->limit;
		}
		
		$this->_listing['filters'] = $this->getFilterData($params['filters'], $model, $action_params, $params['conditions'], $params['group'], $params['order'], $params['limit'], $params['joins'], $params['selector'], $params['selector_type'], $this->_listing['filter_params']);
		
		foreach($params['columns'] as $kc => $c)
		{
			is_array($c) && $c = $kc;
			
			$v = null;
			isset($action_params['sorting'][$c]) && $v = $action_params['sorting'][$c];
			switch($v)
			{
				case 'a': 	$v = 'ASC'; 	break;
				case 'd': 	$v = 'DESC'; 	break;
				
				default:	break;
			}
			$v && $order[] = $c.' '.$v;
		}
		
		$query = $model::find()->group($params['group'])->order($params['order'])->limit($params['limit'])->calcRows();
		$params['conditions'] 		&& $query->where($params['conditions']);
		$params['joinRel'] 			&& $query->joinRel($params['joinRel']);
		$params['joins'] 			&& $query->joins($params['joins']);
		$params['selector'] 		&& $query->select($params['selector']);
		$params['check_editable']	&& $query->addSelect('_editable');
		$params['check_deletable']	&& $query->addSelect('_deletable');
		
		$data = $query->result();
		
		if($params['paging'])
		{
			$this->_listing['paging'] = $paging->build($data->total());
			if($page){ count($data) || redirect('./'); }
		}
		
		if($params['has_ordering'] && !isset($action_params['sorting']) && !$this->_listing['filter_params'])
		{
			$params['actions'][] = array('class' => 'save_listing_order', 'title' => 'save_listing_order', 'href' => 'save_listing_order', 'permission' => 'edit');
		}
		
		$this->_listing['model']			= $model;
		$this->_listing['columns'] 			= $params['columns'];
		$this->_listing['locales'] 			= $this->__globals;
		$this->_listing['data'] 			= $data;
		
		$this->_listing['buttons']			= $params['buttons'];
		$this->_listing['actions']			= $params['actions'];
		
		$this->_listing['sorting']			= $params['sorting'];
		$this->_listing['sorting_params']	= isset($action_params['sorting']) ? $action_params['sorting'] : null;
		
		$this->_listing['has_checkboxes']	= $params['has_checkboxes'];
		$this->_listing['has_ordering']		= $params['has_ordering'];
		
		$this->_listing['check_editable']	= $params['check_editable'];
		$this->_listing['check_deletable']	= $params['check_deletable'];
		
		$params['user_can'] || $params['user_can'] = array();
		foreach(array_merge(array('add', 'edit', 'delete'), array_keys($params['user_can'])) as $a)
		{
			$this->_listing['user_can'][$a] = (isset($params['user_can'][$a]) && $params['user_can'][$a] !== null) ? $params['user_can'][$a] : $this->_user_can($a);
		}
	}

	
	public function _form(array $params)
	{
		
	}
	
	
	public function _tree(array $params)
	{
		$this->includeJavascript('tree', 'jquery.jtree');
		$this->__view = '../scaffolding/tree';
		
		$params = array_merge(array
		(
			'model' 			=> $this->_model,
			'conditions'		=> array(),

			'locales' 			=> $this->__globals,
		
			'check_editable'	=> $this->_check_editable,
			'check_deletable'	=> $this->_check_deletable,
		
			'buttons'			=> array(),
			'actions'			=> array(),
		
			'user_can'			=> $this->_user_can_do,
			
			'class'				=> 'tree',
			'up_down'			=> false,
		), $params);
		
		$tree = TreeFactory::create($params['type'], $params['model']);
		
		if($this->is_ajax() && isset($_POST['tree_order']))
		{
			$tree->reorder($_POST['tree_order']);
			exit;
		}
		
		$query = $tree->load($params['conditions']);
		$params['check_editable']	&& $query->addSelect('_editable');
		$params['check_deletable']	&& $query->addSelect('_deletable');
		
		$params['data'] = $query->result();
		
		$params['user_can'] || $params['user_can'] = array();
		foreach(array_merge(array('add', 'edit', 'delete'), array_keys($params['user_can'])) as $a)
		{
			$params['user_can'][$a] = isset($params['user_can'][$a]) ? $params['user_can'][$a] : $this->_user_can($a);
		}
		
		$this->_tree = $params;
	}
	
	
	protected function getFilterData($filters, $model, $action_params, &$conditions, &$group, &$order, &$limit, &$joins, &$selector, &$selector_type, &$filter_params)
	{
		$conditions && $conditions = array($conditions);
		
		$result = array();
		foreach($filters as $name)
		{
			if(method_exists($model, 'filter__'.$name))
			{
				$action_params[$name] && $filter_params[$name] = $action_params[$name];
				$result[$name] = $model->{'filter__'.$name}($action_params[$name], $conditions, $group, $order, $limit, $joins, $selector, $selector_type);
			}
			else if($name == 'separator')
			{
				$result[++$i] = $name;
			}
		}
		
		is_array($conditions) && $conditions = implode(' AND ', $conditions);
		
		return $result;
	}
}

?>

