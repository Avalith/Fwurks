<?php

abstract class Scaffolding_Controller extends Admin_Controller
{
	public function _listing(array $params)
	{
		$this->__view = '../scaffolding/listing';
		
		$model = new $params['model']();
		
		if(isset($params['action_params']['activate']) && $this->_user_can('edit'))
		{
			$model->load($params['action_params']['activate']);
			$activate_field = $params['action_params']['field'] ?: 'active';
			$model->update((array)$model->{$model::$primary_keys[$model::$primary_key_to_i18n_fk_field]}, array( $activate_field => (1 ^ $model->$activate_field) ));
			redirect(-1);
		}
		else if(is_ajax() && $params['has_ordering'] && !$params['action_params']['sorting'] && !$this->_listing['filters'] && $_POST['listing_order'] && $params['action_params']['save_listing_order'] && $this->_user_can('edit'))
		{
			foreach($_POST['listing_order'] as $id => $order_index){ $model->update($id, array('order_index' => $order_index)); }
			exit;
		}
		
		if($params['paging'])
		{
			$page = isset($params['action_params']['page']) ? (int)$params['action_params']['page'] : 0;
			$paging = new Paging($page, $this->settings->records_per_page);
			$params['limit'] = $paging->limit;
		}
		
		$this->_listing['filters'] = $this->getFilterData($params['filters'], $model, $params['action_params'], $params['conditions'], $params['group'], $params['order'], $params['limit'], $params['joins'], $params['selector'], $params['selector_type'], $this->_listing['filter_params']);
		
		foreach($params['columns'] as $kc => $c)
		{
			is_array($c) && $c = $kc;
			
			$v = null;
			isset($params['action_params']['sorting'][$c]) && $v = $params['action_params']['sorting'][$c];
			switch($v)
			{
				case 'a': 	$v = 'ASC'; 	break;
				case 'd': 	$v = 'DESC'; 	break;
				
				default:	break;
			}
			$v && $order[] = $c.' '.$v;
		}
		
		isset($order) && $params['order'] = implode(', ', $order);
		
		$params['group'] && $params['group'] = ' GROUP BY '.$params['group'];
		$params['conditions'] || $params['conditions'] = '1';
		$data = $model->find_all($params['conditions'].$params['group'], $params['order'], $params['limit'], $params['joins'], $params['selector'], $params['selector_type']);
		
		if($params['paging'])
		{
			$this->_listing['paging'] = $paging->build($data->total);
			if($page){ $data->total || redirect(); }
		}
		
		if($params['has_ordering'] && !$params['action_params']['sorting'] && !$this->_listing['filter_params'])
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
		$this->_listing['sorting_params']	= $params['action_params']['sorting'];
		
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
		
		$tree = TreeFactory::create($params['type'], array(Inflector::tableize($params['model'])));
		
		if(is_ajax())
		{
			$tree->reorder($_POST['tree_order']);
			exit;
		}
		
		$this->_tree['data'] 				= $tree->load($params['conditions']);
		$this->_tree['locales'] 			= $this->__globals;
		
		$params['user_can'] || $params['user_can'] = array();
		foreach(array_merge(array('add', 'edit', 'delete'), array_keys($params['user_can'])) as $a)
		{
			$this->_tree['user_can'][$a] = $params['user_can'][$a] !== null ? $params['user_can'][$a] : $this->_user_can($a);
		}
		
		$this->_tree['up_down'] = $params['up_down'];
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