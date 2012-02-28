<?php

class NestedTreeNode extends TreeNode 
{
	public $children;
	
	protected $fields = array
	(
		'primary'	=> null,
		'parent' 	=> 'parent',
		'left' 		=> 'nleft',
		'right' 	=> 'nright',
		'level' 	=> 'nlevel',
		
		'title'		=> 'title',
	);
	
	public function parent()
	{
		return $this->query()->where("{$this->fields['primary']}?i = {$this->parent}");
	}
	
	
	public function parents($self = false, $conditions = null)
	{
		if(!$this->primary){ return; }
		
		$left = $this->fields['left'];
		$right = $this->fields['right'];
		$level = $this->fields['level'];
		
		$self = $self ? '=' : '';
		$conditions = $conditions ? 'AND '.$conditions : '';
		
		return $this->query()
			->where("$left <$self {$this->$left} AND $right >$self {$this->$right} $conditions")
			->order($left)
			->result();
	}
	
	
	public function children($conditions = null)
	{
		return $this->branches(1, false, $conditions);
	}

	
	public function isChildOf($parent_id)
	{
		return $this->parent == $parent_id;
	}
	
	
	public function countChildren($conditions = null)
	{
		return $this->countBranches(1, false, $conditions);
	}
	
	
	public function isLeaf()
	{
		if(!$this->primary){ return; }
		
		$left = $this->fields['left'];
		$right = $this->fields['right'];
		
		return $this->$left == $this->$right - 1;  
	}
	
	
	public function branches($levels = 0, $self = false, $conditions = null, $recurse = false)
	{
		if(!$this->primary){ return; }
		
		$left = $this->fields['left'];
		$right = $this->fields['right'];
		$level = $this->fields['level'];
		
		$self = $self ? '=' : '';
		$levels = $levels ? 'AND '.$level.'='.($this->$level+$levels) : '';
		$conditions = $conditions ? 'AND '.$conditions : '';
		
		return $this->query(false)
			->where("$left >$self {$this->$left} AND $right <$self {$this->$right} AND $levels $conditions")
			->order($left);
	}
	
	
	public function isBranchOf($ancestor_id)
	{
		if(!$this->primary){ return; }
		
		$left = $this->fields['left'];
		$right = $this->fields['right'];
		
		$query = $this->query(false)
			->select("COUNT({$this->fields['primary']}) as count")
			->where("{$this->fields['primary']} = $ancestor_id AND $left < {$this->$left} AND $right > {$this->$right}");
		
		return $query->result()->seek(0)->count ? true : false;
	}
	
	
	public function countBranches($levels = 0, $self = false, $conditions = null)
	{
		if(!$this->primary){ return; }
		
		$left = $this->fields['left'];
		$right = $this->fields['right'];
		$level = $this->fields['level'];
		
		$self = $self ? '=' : '';
		$levels = $levels ? 'AND '.$level.'='.($this->$level+$levels) : '';
		$conditions = $conditions ? 'AND '.$conditions : '';
		
		$query = $this->query(false)
			->select("COUNT({$this->fields['primary']}) as count")
			->where("$left >$self {$this->$left} AND $right <$self {$this->$right} AND $levels $conditions")
			->order($left);
		
		return (int)$query->result()->seek(0)->count;
	}
	
}


class NestedTree extends Tree 
{
	protected $tree_node_class = 'NestedTreeNode';
	
	protected $fields = array
	(
		'primary'	=> null,
		'parent' 	=> 'parent',
		'left' 		=> 'nleft',
		'right' 	=> 'nright',
		'level' 	=> 'nlevel',
		
		'title'		=> 'title',
	);
	
	public function load($conditions = null)
	{
		$model = $this->model_name;
		
		return $this->query()
			->where($conditions)
			->order($this->fields['left']);
	}
	
	public function rebuild($conditions = null)
	{
		
	}
	
	public function reorder($order)
	{
		$primary 	= $this->fields['primary'];
		$parent 	= $this->fields['parent'];
		$level 		= $this->fields['level'];
		
		foreach($order as $id => &$item)
		{
			$item[$level] = (int)$order[$item[$parent]][$level] + 1;
			$item[$primary] = (int)$id;
			
			$item[$parent] && $order[$item[$parent]]['children'][] = &$item;
		}
		
		// unset those with parents from the trunk
		foreach($order as $id => &$item){ if($item[$parent]){ unset($order[$id]); } }
		
		$this->reorder_lr_gen($order);
		
		echo '1';
	}
	
	protected function reorder_lr_gen(&$order, &$nleft = 0)
	{
		$primary 	= $this->fields['primary'];
		$parent 	= $this->fields['parent'];
		$level 		= $this->fields['level'];
		$left 		= $this->fields['left'];
		$right 		= $this->fields['right'];
		
		foreach($order as $id => &$item)
		{
			$item[$left] = ++$nleft;
			isset($item['children']) && $this->reorder_lr_gen($item['children'], $nleft);
			$item[$right] = ++$nleft;
			
			$this->query()
				->update(array($parent => $item[$parent], $level => $item[$level], $left => $item[$left], $right => $item[$right]))
				->where("{$primary} = {$item[$primary]}")
			->run();
		}
		
	}
	
}


?>
