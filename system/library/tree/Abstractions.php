<?php

abstract class TreeAndNodeBase
{
	protected static $db;
	
	protected $table_name;
	
	protected $has_i18n;
	protected $i18n_join;
	protected $i18n_locale;
	protected $i18n_suffix 			= 'i18n';
	protected $i18n_fk_field 		= 'i18n_foreign_key';
	protected $i18n_locale_field 	= 'i18n_locale';
	
	
	protected $fields = array
	(
		'primary'	=> 'id',
		'parent' 	=> 'parent'
	);
	protected $fields_str;
	
	protected $tree_node_class = 'TreeNode';
	
	protected function fetchQuery($sql, $change = false)
	{
		return self::$db->fetch( self::$db->query($sql), $change, $this->tree_node_class, array($this->getParams()) );
	}
	
	protected function getParams()
	{
		return array
		(
			'table_name' 		=> $this->table_name,
			'has_i18n'			=> $this->has_i18n,
			'fields'			=> $this->fields, 
			'fields_str' 		=> $this->fields_str,
			'tree_node_class' 	=> $this->tree_node_class,
			'i18n_locale'		=> $this->i18n_locale,
			'i18n_join' 		=> $this->i18n_join
		);
	}
	
	protected function getJoin()
	{
		if($this->has_i18n)
		{
			$table = "{$this->table_name}_{$this->i18n_suffix}";
			$id = $this->fields['primary'];
			
			/*
			$columns_info = self::$db->table_info($table);
			
			foreach($columns_info as $c)
			{
				$c = $c['name'];
				if( !in_array($c, array($this->i18n_fk_field, $this->i18n_locale_field)) ){ $columns[] = "$table.$c"; }
			}
			*/
			
			return array
			(
				
				'what' => ", $table.title", //', '.implode(', ', $columns),
				'table' => "LEFT JOIN $table ON $id = {$this->i18n_fk_field} AND {$this->i18n_locale_field} = '{$this->i18n_locale}'"
			);
		}
		array('what' => '', 'table' => '');
	}
	
	protected function recurse($nodes)
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
		TreeAndNodeBase::$db = Registry()->db;
		foreach($params as $key => $value){ $this->$key = $value; }
	}
	
	public function __get($name)
	{
		if(array_key_exists($name, $this->fields))
		{
			return $this->{$this->fields[$name]}; 
		}
	}
	
	public function __set($name, $value)
	{
		if(array_key_exists($name, $this->fields))
		{
			return $this->{$this->fields[$name]} = $value; 
		}
		
		return $this->$name = $value;
	}
	
	public function parent()
	{
		$sql = "SELECT {$this->fields_str} 
			FROM {$this->table_name} 
			WHERE {$this->fields['primary']} = ".qstr($this->parent);
		
		return $this->fetchQuery($sql, true);
	}
	
	public function isChildOf($parent_id)
	{
		return $this->parent == $parent_id;
	}
}


abstract class Tree extends TreeAndNodeBase implements TreeInterface
{
	static $db;
	
	public $has_i18n;
	
	protected $tree_node_class = 'TreeNode';
	
	public function __construct($table, array $fields = array() )
	{
		TreeAndNodeBase::$db = Registry()->db;
		foreach($fields as $field => $value){ $this->fields[$field] = $value; }
		
		$this->table_name = $table;
		$this->fields['parent'] .= '_'.$this->fields['primary'];
		
		$this->fields_str = implode(', ', $this->fields);
		
		empty($this->has_i18n) && $this->has_i18n = true;
		$this->i18n_locale = Registry()->i18n_locale;
		$this->i18n_join = $this->getJoin();
	}
	
	public function node($id)
	{
		if(is_array($id))		{ $id = $this->fields['primary'].' IN ('.implode(',', array_map('qstr', $id)).')'; }
		if(!strpos($id, '='))	{ $id = $this->fields['primary'].' ='.qstr($id); }
		
		$sql = "SELECT {$this->fields_str} {$this->i18n_join['what']}
			FROM {$this->table_name} {$this->i18n_join['table']}
			WHERE $id";
		
		if(!($node = $this->fetchQuery($sql, true)))
		{
			$node = new $this->tree_node_class($this->getParams());
			$node->primary = $id;
		}
		
		return $node;
	}
}


?>
