<?php

class ORMResultSet implements ArrayAccess, SeekableIterator, Countable
{
	protected $total	= 0;
	protected $results 	= array();
	
	public function __construct(array $results = array(), array $params = array())
	{
		$this->results = $results;
		$this->total = Registry::$db->total_rows() ?: $this->count();
		
		$this->table_name 	= $params['table_name'];
		$this->model_name 	= $params['model_name'];
		$this->relations 	= $params['relations'];
	}
	
	
	/**
	 * ------------------------------------
	 * =========== Params Access ==========
	 * ------------------------------------
	 */
	
	protected $table_name	= null;
	protected $model_name	= null;
	
	public function table_name(){ return $this->table_name; }
	
	public function model_name(){ return $this->model_name; }
	
	public function total()		{ return $this->total; }
	
	
	/**
	 * ------------------------------------
	 * ======= Array Access Methods =======
	 * ------------------------------------
	 */
	public function offsetExists($offset)			{ return isset($this->results[$offset]); }
	
	public function offsetGet	($offset)			{ return isset($this->results[$offset]) ? $this->results[$offset] : null; }
	
	public function offsetSet	($offset, $value)	{ $this->results[$offset] = $value; }
	
	public function offsetUnset	($offset)			{ unset($this->results[$offset]); $this->results = array_values($this->results); }
	
	
	/**
	 * ------------------------------------
	 * ===== SeekableIterator Methods =====
	 * ------------------------------------
	 */
	private $position = 0;
	
	public function seek($position)
	{
		$this->position = $position;
		if(!$this->valid()){ throw new OutOfBoundsException("Invalid seek position ($position)"); }
		else return $this->results[$position];
		
	}
	
	public function rewind()	{ $this->position = 0; }
	
	public function current()	{ return $this->results[$this->position]; }
	
	public function key()		{ return $this->position; }
	
	public function next()		{ $this->position++; }
	
	public function valid()		{ return isset($this->results[$this->position]); }
	
	
	/**
	 * ------------------------------------
	 * ========= Countable Methods ========
	 * ------------------------------------
	 */
	public function count(){ return count($this->results); }
	
	
	/**
	 * ------------------------------------
	 * ============= Relations ============
	 * ------------------------------------
	 */
	
	protected $relations = null;
	
	public function populate_joined_relations($relations = null)
	{
		if($relations === false){ return; }
		$relations = $relations ? (array)$relations : array_keys((array)$this->relations);
		
		$links = array();
		$model = $this->model_name;
		$pkey = $model::primary_keys(0);
		
		// TODO: Relation = 'Reviews.User'
		foreach($this->results as $i => &$result)
		{
			isset($links[$result->$pkey]) || $links[$result->$pkey] = $i;
			$real_i = $links[$result->$pkey];
			
			foreach($relations as $relation)
			{
				$res = $result;
				
//				if(strpos($relation, '.'))
//				{
//					$_relation = explode('.', $relation);
//					
//					$rel = $this->relations->{$_relation[0]}->rmodel();
//					for($i = 1, $len = count($_relation); $i < $len; $i++)
//					{
//						$rel = $rel::relations()->{$_relation[$i]};
//						$res = $res->{$_relation[$i-1]};
//					}
//					$relation = $_relation[$i-1];
//				}
//				else
//				{
//				
					$rel = $this->relations->$relation;
//				}
				
				$rmodel = $rel->rmodel();
				$rtable = $rmodel::table_name();
				
				$rpkey = $rmodel::primary_keys(0);
				
				if(isset($result->{$rtable.'_'.$rpkey}) && $result->{$rtable.'_'.$rpkey})
				{
					$result_rel = $rmodel::new_result();

					foreach($rmodel::columns($rmodel::COLUMNS_TYPE_ALL__ONLY_NAMES) as $c)
					{
						$_c = $rtable.'_'.$c;
						$result_rel->$c = $result->$_c;
						unset($result->$_c);
					}
					
					if($rel->type() == 'has_many')
					{
						$next = count($this->results[$real_i]);
						isset($this->results[$real_i]->$relation) || $this->results[$real_i]->$relation = array();
						
						$this->results[$real_i]->{$relation}[$result_rel->$rpkey] = $result_rel;
					}
					else
					{
						$res->$relation = $result_rel;
					}
				}
				
				if($links[$result->$pkey] != $i){ unset($this->results[$i]); }
			}
		}
		
		$this->results = array_values($this->results);
		
		// TODO IDEA: Optimize Those Foreaches if possible
		
		// Has Many
		foreach($relations as $relation)
		{
			$rel = $this->relations->$relation;
			if($rel->type() == 'has_many')
			{
				foreach($this->results as &$result)
				{
					if(!isset($result->$relation)){ continue; }
					$rmodel = $rel->rmodel();
					$result->$relation = $rmodel::new_result_set(array_values($result->$relation));
				}
			}
		}
	}	
	
	
	public function populateRel($relations = null, $join_i18n = true, $select = true)
	{
		if($relations === false){ return; }
		$relations = $relations ? (array)$relations : array_keys((array)$this->relations);
		
		
		foreach($relations as $_rel)
		{
			// TODO: Relation = 'Reviews.User'
			$rel 	= $this->relations->$_rel;
			$model 	= $rel->model();
			$pkey 	= $model::primary_keys(0);
			
			$key 	= $rel->key();
			$fkey 	= $rel->fkey();
			
			
			$query = $rel->query_multiple($this);
			if($query instanceof ORMQuery)
			{
				$result_set = $query->result();
			

				$rmodel = $result_set->model_name();
				$rpkey = $rmodel::primary_keys(0);
				
				$results = array();
				
				// TODO IDEA: Optimize this if possible
				if($rel->type() == 'has_many')
				{
					if($rel->rmodel_through())			{ foreach($result_set as $r){ foreach(explode(',', $r->rel_pkeys) as $id){ unset($r->rel_pkeys); $results[$id][] = $r; } } }
					else /* !through */					{ foreach($result_set as $r){ $results[$r->$key][] = $r; } }
					
				}
				else if($rel->type() == 'belongs_to')	{ foreach($result_set as $r){ $results[$r->$pkey] = $r; } }
				else /* $rel->type() == 'has_one' */	{ foreach($result_set as $r){ $results[$r->$key] = $r; } }
			}
			else{ continue; }
			
			switch($rel->type())
			{
				case 'has_many'		: foreach($this->results as &$r){	isset($r->$pkey) && isset($results[$r->$pkey]) && $r->$_rel = $rmodel::new_result_set($results[$r->$pkey]); } break;
				case 'belongs_to'	: foreach($this->results as &$r){	isset($r->$fkey) && isset($results[$r->$fkey]) && $r->$_rel = $results[$r->$fkey]; } break;
				case 'has_one'		: foreach($this->results as &$r){	isset($r->$pkey) && isset($results[$r->$pkey]) && $r->$_rel = $results[$r->$pkey]; } break;
			}
		}
		
		return $this;
	}
}

?>
