<?php

abstract class Scaffolding_Controller extends Admin_Controller
{
	public function _listing(array $params)
	{
		$this->__view = '../scaffolding/listing';
		
		$model = new $params['model']();
		
		if($params['action_params']['activate'] && $this->_user_can('edit'))
		{
			$model->load(qstr($params['action_params']['activate']));
			$model->update($model->id, array( 'active' => (($model->active - 1) * -1) ));
			redirect();
		}
		else if(is_ajax() && $params['has_ordering'] && !$params['action_params']['sorting'] && !$this->_listing['filters'] && $_POST['listing_order'] && $params['action_params']['save_listing_order'] && $this->_user_can('edit'))
		{
			foreach($_POST['listing_order'] as $id => $order_index){ $model->update($id, array('order_index' => $order_index)); }
			exit;
		}
		
		if($params['paging'])
		{
			$page = (int)$params['action_params']['page'];
			$paging = new Paging($page, $this->settings->records_per_page);
			$params['limit'] = $paging->limit;
		}
		
		$this->_listing['filters']	= $this->getFilterData($params['filters'], $model, $params['action_params'], $params['conditions'], $params['order'], $params['limit']);
		
		foreach($params['columns'] as $kc => $c)
		{
			is_array($c) && $c = $kc;
			
			$v = $params['action_params']['sorting'][$c];
			switch($v)
			{
				case 'a': 	$v = 'ASC'; 	break;
				case 'd': 	$v = 'DESC'; 	break;
				
				default:	break;
			}
			
			$v && $order[] = $c.' '.$v;
		}
		
		$order && $params['order'] = implode(', ', $order);
		
		$data = $model->find_all($params['conditions'], $params['order'], $params['limit'], $params['joins']);
		
		if($params['paging'])
		{
			$this->_listing['paging'] = $paging->build($data[0]->total_results);
			if($page){ $data[0]->total_results || redirect(); }
		}
		
		$this->_listing['model']			= $model;
		$this->_listing['columns'] 			= $params['columns'];
		$this->_listing['locales'] 			= $this->__labels['__globals'];
		$this->_listing['data'] 			= $data;
		
		foreach(array_merge(array('add', 'edit', 'delete'), array_keys($params['user_can_do'])) as $a)
		{
			$this->_listing['user_can'][$a] = $params['user_can_do'][$a] !== null ? $params['user_can_do'][$a] : $this->_user_can($a);
		}
		
		$this->_listing['buttons']	= $params['buttons'];
		$this->_listing['actions']	= $params['actions'];
		
		$this->_listing['sorting']	= $params['sorting'];
		$this->_listing['sorting_params']	= $params['action_params']['sorting'];
		
		$this->_listing['has_checkboxes']	= $params['has_checkboxes'];
		$this->_listing['has_ordering']	= $params['has_ordering'];
	}

	
	public function _form(array $params)
	{
		
	}
	
	
	public function _tree(array $params)
	{
		$this->includeJavascript(array('tree', 'jquery.jtree'));
		$this->__view = '../scaffolding/tree';
		
		$tree = TreeFactory::create($params['type'], array(Inflector()->tableize($params['model'])));
		
		if(is_ajax())
		{
			$tree->reorder($_POST['tree_order']);
			exit;
		}
		
		$this->_tree['data'] 				= $tree->load($params['conditions']);
		$this->_tree['locales'] 			= $this->__labels['__globals'];
		
		foreach(array_merge(array('add', 'edit', 'delete'), array_keys($params['user_can_do'])) as $a)
		{
			$this->_tree['user_can'][$a] = $params['user_can_do'][$a] !== null ? $params['user_can_do'][$a] : $this->_user_can($a);
		}
		
		$this->_tree['up_down'] = $params['up_down'];
	}
	
	
	protected function getFilterData($filters, $model, $action_params, &$conditions, &$order, &$limit)
	{
		$conditions && $conditions = array($conditions);
		
		foreach($filters as $name)
		{
			if(method_exists($model, 'filter__'.$name))
			{
				$result[$name] = $model->{'filter__'.$name}($action_params[$name], $conditions, $order, $limit);
			}
			else if($name == 'separator')
			{
				$result[++$i] = $name;
			}
		}
		
		is_array($conditions) && $conditions = implode(' AND ', $conditions);
		
		return $result;
	}
	
	protected function uploaded_crop($field, $folder, $crop_pos)
	{
		$file = new UploadedFile($field);
		if(is_post() && $file->name && !$_POST['save'])
		{
			$file->new_name('u'.$this->__session->logged_user->id);
			$file->move($folder.'_temp');
			
			$image = url_for("/public/files/{$folder}_temp/u{$this->__session->logged_user->id}.".$file->extension);
			
			echo '<script type="text/javascript">top.uploadedImage("'.$image.'?'.time().'");</script>';
			exit;
		}
		
		if(is_post() && $_POST['crop_position'])
		{
			$pos = explode('+', substr($_POST['crop_position'], 1));
			$_POST['crop_position'] = array();
			foreach($crop_pos as $t => $k)
			{
				$_POST['crop_position'][$t] = '+'.(int)($pos[0]*$k[0]).'+'.(int)($pos[1]*$k[1]);
			}
		}
	}
}

?>
