<?php

abstract class TreeAndNodeBase
{
	protected $model_name			= null;
	protected $table_name			= null;
	
	protected $fields 				= array
	(
		'primary'	=> null,
		'parent' 	=> 'parent',
		
		'title'		=> 'title',
	);
	protected $fields_str 			= null; 
	
	protected $tree_node_class 		= 'TreeNode';
	
	public function __construct()
	{
		$model = $this->model_name;
		$this->fields['primary'] = $model::primary_keys(0);
		$this->fields['parent'] .= '_'.$this->fields['primary'];
		
		$this->fields_str = implode(',', $this->fields);
	}
	
	protected function query($join_i18n = true)
	{
		$model = $this->model_name;
		$query = $model::query(null, $this->tree_node_class)->select($this->fields_str);
		
		$join_i18n && $query->join($model::join_i18n());
		
		return $query;
	}
	
	public function recurse($nodes)
	{
		foreach($nodes as $t)
		{
			$t->children = array();
			$result[$t->primary] = $t;
			
			if($t->parent){ $result[$t->parent]->children[] = &$result[$t->primary]; }
		}
		
		if($result)
		{
			foreach($result as $id => $t){ if($t->parent){ unset($result[$id]); } }
			return $result;
		}
	}
}


abstract class TreeNode extends TreeAndNodeBase implements TreeNodeInterface 
{
	public function __construct($params)
	{
		$this->table_name = $params['table_name'];
		$this->model_name = $params['model_name'];
		
		parent::__construct();
	}
	
	public function __get($name)
	{
		if(isset($this->fields[$name]))
		{
			return $this->{$this->fields[$name]}; 
		}
	}
	
	public function __set($name, $value)
	{
		if(isset($this->fields[$name]))
		{
			return $this->{$this->fields[$name]} = $value; 
		}
		
		return $this->$name = $value;
	}
}


abstract class Tree extends TreeAndNodeBase implements TreeInterface
{
	public function __construct($model)
	{
		$this->model_name = $model;
		$this->table_name = $model::table_name();
		
		parent::__construct();
	}
	
	public function node($id)
	{
		if(is_array($id))		{ $id = $this->fields['primary'].' IN ('.implode(',', array_map('qstr', $id)).')'; }
		if(!strpos($id, '='))	{ $id = $this->fields['primary'].' ='.qstr($id); }
		
		$sql = "SELECT {$this->fields_str} {$this->i18n_join['what']}
			FROM {$this->table_name} {$this->i18n_join['table']}
			WHERE $id";
		
		$node = $this->fetchQuery($sql);
		(is_array($node) && count($node)) && $node = $node[0];
		
		if(!$node)
		{
			$node = new $this->tree_node_class($this->getParams());
			$node->primary = $id;
		}
		
		return $node;
	}
}


?>

