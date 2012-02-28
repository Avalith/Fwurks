<?php

class NestedTreeNode extends TreeNode 
{
	public $children;
	
	public function children($conditions = null)
	{
		return $this->branches(1, false, $conditions);
	}
	
	public function branches($levels = 0, $self = false, $conditions = null, $recurse = false)
	{
		if(!$this->primary){ return; }
		
		$left = $this->fields['left'];
		$right = $this->fields['right'];
		$level = $this->fields['level'];
		
		$self = $self ? '=' : '';
		$levels = $levels ? $level.'='.($this->$level+$levels) : '1';
		$conditions = $conditions ? 'AND '.$conditions : '';
		
		$sql = "SELECT {$this->fields_str} {$this->i18n_join['what']}
			FROM {$this->table_name} {$this->i18n_join['table']}
			WHERE 	$left  >$self {$this->$left} 
				AND $right <$self {$this->$right}
				AND $levels
					$conditions
			ORDER BY $left";
		
		$sql = $this->fetchQuery($sql);
		if(!$recurse){ return $sql; }

		if(($result = $this->recurse($sql)))
		{
			return end(end($result));
		}
	}
	
	public function parents($self = false, $conditions = null)
	{
		if(!$this->primary){ return; }
		
		$left = $this->fields['left'];
		$right = $this->fields['right'];
		$level = $this->fields['level'];
		
		$self = $self ? '=' : '';
		$conditions = $conditions ? 'AND '.$conditions : '';
		
		$sql = "SELECT {$this->fields_str} {$this->i18n_join['what']}
			FROM {$this->table_name} {$this->i18n_join['table']}
			WHERE 	$left  <$self {$this->$left} 
				AND $right >$self {$this->$right}
					$conditions
			ORDER BY $left";
		
		return $this->fetchQuery($sql);
	}
	
	public function isLeaf()
	{
		if(!$this->primary){ return; }
		
		$left = $this->fields['left'];
		$right = $this->fields['right'];
		
		return $this->$left == $this->$right - 1;  
	}
	
	public function isBranchOf($ancestor_id)
	{
		if(!$this->primary){ return; }
		
		$left = $this->fields['left'];
		$right = $this->fields['right'];
		
		$sql = "SELECT COUNT({$this->fields['primary']}) as count
			FROM {$this->table_name} 
			WHERE 	{$this->fields['primary']} = $ancestor_id 
				AND $left  < {$this->$left} 
				AND $right > {$this->$right}";
		
		return $this->fetchQuery($sql, true)->count ? true : false;
	}
	
	public function countBranches($levels = 0, $self = false, $conditions = null)
	{
		if(!$this->primary){ return; }
		
		$left = $this->fields['left'];
		$right = $this->fields['right'];
		$level = $this->fields['level'];
		
		$self = $self ? '=' : '';
		$levels = $levels ? $level.'='.($this->$level+$levels) : '1';
		$conditions = $conditions ? 'AND '.$conditions : '';
		
		$sql = "SELECT COUNT({$this->fields['primary']}) as count
			FROM {$this->table_name} 
			WHERE 	$left  >$self {$this->$left} 
				AND $right <$self {$this->$right}
				AND $levels
					$conditions
			ORDER BY $left";
		
		return $this->fetchQuery($sql, true)->count;
	}
	
	public function countChildren($conditions = null)
	{
		return $this->countBranches(1, false, $conditions);
	}
}


class NestedTree extends Tree 
{
	protected $tree_node_class = 'NestedTreeNode';
	
	protected $fields = array
	(
		'primary'	=> 'id',
		'parent' 	=> 'parent',
		'left' 		=> 'nleft',
		'right' 	=> 'nright',
		'level' 	=> 'nlevel'
	);
	
	public function load($conditions = null, $recurse = false)
	{
		$left = $this->fields['left'];
		
		$conditions = $conditions ? 'WHERE '.$conditions : '';
		
		$sql = "SELECT {$this->fields_str} {$this->i18n_join['what']}
			FROM {$this->table_name} {$this->i18n_join['table']}
				$conditions
			ORDER BY $left";
		
		$sql = $this->fetchQuery($sql);
		if(!$recurse){ return $sql; }
			
		return $this->recurse($sql);
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
			$item[$primary] = $id;
			
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
			$item['children'] && $this->reorder_lr_gen($item['children'], $nleft);
			$item[$right] = ++$nleft;
			
			TreeAndNodeBase::$db->update($this->table_name, array($parent => $item[$parent], $level => $item[$level], $left => $item[$left], $right => $item[$right]), $item[$primary]);
		}
	}
	
}


?>
