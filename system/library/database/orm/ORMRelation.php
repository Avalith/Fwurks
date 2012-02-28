<?php

class ORMRelation
{
	protected $name;
	protected $type;
	protected $model;
	protected $rmodel;
	protected $through;
	protected $through_join;
	
	protected $key;
	protected $fkey;
	
	public function __construct($name = null)
	{
		$this->name = $name;
	}
	
	public function hasMany($items)
	{
		$this->type = 'has_many';
		$this->rmodel = $items;
		$this->name || $this->name = $items;
		
		return $this;
	}
	
	public function hasOne($item)
	{
		$this->type = 'has_one';
		$this->rmodel = $item;
		$this->name || $this->name = $item;
		
		return $this;
	}
	
	
	public function through($relation, $join = false)
	{
		$this->through = $relation;
		$this->through_join = $join;
		
		return $this;
	}
	
	
	public function belongsTo($item)
	{
		$this->type = 'belongs_to';
		$this->rmodel = $item;
		$this->name || $this->name = $item;
		
		return $this;
	}
	
		
	public function query(ORMResult $result)
	{
		$model		= $this->model;
		$table 		= $model::table_name();
		$pkey		= $model::primary_keys(0);
		
		$rmodel 	= $this->rmodel();
		$rtable 	= $rmodel::table_name();
		$rpkey		= $rmodel::primary_keys(0);
		
		$search = true;
		
		$query = $rmodel::find();
		
		switch($this->type)
		{
			case 'has_many':
			{
				if($this->through)
				{
					$result->$pkey == null && $search = false;
					
					$mn_model = $this->through;
					$mn_table = $mn_model::table_name();
					
					$fkey = $this->fkey();
					$key = $this->key();
					
					$query->join($mn_model::join()->left()->on("$mn_table.$fkey = $rtable.$rpkey"));
					$query->where("$mn_table.$key = ?", $result->$pkey);
				}
				else
				{
					$result->$pkey == null && $search = false;
					$key = $this->key();
					$query->where("$rtable.$key = ?", $result->$pkey);
				}
			}
			break;
			
			case 'belongs_to':
			{
				$fkey = $this->fkey();
				$result->$fkey == null && $search = false;
				
				$query->where("$rtable.$rpkey = ?", $result->$fkey);
				$query->limit(1);
			}
			break;
			
			case 'has_one':
			{
				$result->$pkey == null && $search = false;
				$key = $this->key();
				
				$query->where("$rtable.$key = ?", $result->$pkey);
				$query->limit(1);
			}
			break;
		}
		
		return $search ? $query : null;
	}
	
	public function query_multiple(ORMResultSet $result_set)
	{
		$model		= $this->model;
		$table 		= $model::table_name();
		$pkey		= $model::primary_keys(0);
		
		$rmodel 	= $this->rmodel();
		$rtable 	= $rmodel::table_name();
		$rpkey		= $rmodel::primary_keys(0);
		
		$search = true;
		
		$query = $rmodel::find();
		
		switch($this->type)
		{
			case 'has_many':
			{
				$keys = $this->collect_key_values($result_set, $pkey);
				$keys == null && $search = false;
				
				if($this->through)
				{
					$mn_model = $this->through;
					$mn_table = $mn_model::table_name();
					
					$fkey = $this->fkey();
					$key = $this->key();
					
					$query->join($mn_model::join()->left()->on("$mn_table.$fkey = $rtable.$rpkey"));
					$query->where($mn_table.'.'.$key.' IN ('.implode(',', $keys).')');
					$query->addSelect("GROUP_CONCAT($mn_table.$key) as rel_pkeys");
					$query->group("$rtable.$rpkey");
				}
				else
				{
					$key = $this->key();
					$query->where($rtable.'.'.$key.' IN ('.implode(',', $keys).')');
				}
			}
			break;
			
			case 'belongs_to':
			{
				$fkey = $this->fkey();
				
				$keys = $this->collect_key_values($result_set, $fkey);
				
				$keys == null && $search = false;
				$query->where("$rtable.$rpkey IN (".implode(',', $keys).')');
				$query->limit(count($keys));
			}
			break;
			
			case 'has_one':
			{
				$key = $this->key();
				$keys = $this->collect_key_values($result_set, $pkey);
				$keys == null && $search = false;
				
				$query->where("$rtable.$key IN (".implode(',', $keys).')');
				$query->limit(count($keys));
			}
			break;
		}
		
		return $search ? $query : null;
	}
	
	
	public function join($join_i18n = true)
	{
		$model		= $this->model;
		$table 		= $model::table_name();
		$pkey		= $model::primary_keys(0);
		
		$rmodel 	= $this->rmodel();
		$rtable 	= $rmodel::table_name();
		$rpkey		= $rmodel::primary_keys(0);
		
		$join = $mn_join = $i18n = $select = null;
		switch($this->type)
		{
			case 'has_many':
			{
				if($this->through)
				{
					$mn_model = $this->through;
					$mn_table = $mn_model::table_name();
					
					$key =	$this->key();
					$key2 = $pkey;
					$fkey = $this->fkey();
					$fkey2 = $rpkey;
					
					$mn_join = $mn_model::join()->left()->on("$mn_table.$key = $table.$key2");
					$join = $rmodel::join()->left()->on("$rtable.$fkey2 = $mn_table.$fkey");
				}
				else
				{
					$fkey = $pkey;
					$key = $this->key ?: Inflector::singularize($table) . '_' . $pkey;
					
					$join = $rmodel::join()->left()->on("$rtable.$key = $table.$fkey");
				}
			}
			break;
			
			case 'belongs_to':
			{
				$key = $rpkey;
				$fkey = $this->fkey();
				
				$join = $rmodel::join()->left()->on("$rtable.$key = $table.$fkey");
			}
			break;
			
			case 'has_one':
			{
				$fkey = $pkey;
				$key = $this->key();
				
				$join = $rmodel::join()->left()->on("$rtable.$key = $table.$fkey");
			}
		}
		
		$join_i18n && $i18n = $rmodel::join_i18n();
		
		$select = array();
		foreach($rmodel::columns($rmodel::COLUMNS_TYPE_ALL__ONLY_NAMES) as $c){ $select[] = "{$rtable}.{$c} as {$rtable}_{$c}"; }
		$select = implode(', ', $select);
		
		return (object)array('join' => $join, 'mn_join' => $mn_join, 'i18n' => $i18n, 'select' => $select);
	}
	
	/**
	 * -----------------------------
	 * ========== Getters ==========
	 * -----------------------------
	 */
	
	public function name(){ return $this->name; }
	
	public function type(){ return $this->type; }
	
	public function model($model = null){ if($model){ $this->model = $model; return $this; } return $this->model; }
	
	public function rmodel()
	{
		static $models = array();
		
		isset($models[$this->rmodel]) || $models[$this->rmodel] = $this->type == 'has_many' ? Inflector::classify(Inflector::singularize(Inflector::underscore($this->rmodel))) : $this->rmodel; 
		
		return $models[$this->rmodel];
	}
	
	public function rel_model(){ return $this->rmodel; }
	
	public function rmodel_through(){ return $this->through; }
	
	public function key($key = null)
	{
		if($key)
		{
			$this->key = $key;
			return $this;
		}
		
		if(!$this->key)
		{
			$model = $this->model;
			$this->key = Inflector::singularize($model::table_name()).'_'.$model::primary_keys(0);
		}
		
		return $this->key; 
	}
	
	public function fkey($key = null)
	{
		if($key)
		{
			$this->fkey = $key;
			return $this;
		}
		
		if(!$this->fkey)
		{
			$model = $this->rmodel();
			$this->fkey = Inflector::singularize($model::table_name()).'_'.$model::primary_keys(0);
		}
		
		return $this->fkey; 
	}
	
	
	public function collect_key_values($result_set, $key)
	{
		$keys = array();
		
		$i = 0;
		foreach($result_set as $r){ isset($r->$key) && $r->$key != null && !isset($keys[$r->$key]) && $keys[$r->$key] = $i++; }
		return array_flip($keys);
	}
}


/**
 * 
 * @return ORMRelation
 */
function ORMRelation($name = null)
{
	return new ORMRelation($name);
}


?>
