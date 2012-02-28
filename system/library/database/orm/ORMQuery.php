<?php

class ORMQuery extends SqlQuery
{
	/**
	 * Result class name
	 * 
	 * @var string
	 * @access protected
	 */
	protected $result_class;
	
	/**
	 * ResultSet class name
	 * 
	 * @var string
	 * @access protected
	 */
	protected $result_set_class;
	
	/**
	 * ResultSet class params
	 * 
	 * @var string
	 * @access protected
	 */
	protected $result_class_params;
	
	protected $populate_relations = false;
	
	
	public function __construct($table, $result_class = 'ORMResult', $result_set_class = 'ORMResultSet', array $result_class_params = array())
	{
		$this->table 				= $table;
		$this->result_class 		= $result_class;
		$this->result_set_class 	= $result_set_class;
		$this->result_class_params 	= $result_class_params;
	}
	
	
	/**
	 * @param SqlAdapter $db
	 * @param String $class_name
	 * @param Array $class_params
	 * @return ORMResultSet
	 */
	public function result(SqlAdapter $db = null, $class_name = null, array $class_params = array())
	{
		$db 			|| $db 				= Registry::$db;
		$class_name 	|| $class_name 		= $this->result_class; 
		$class_params 	|| $class_params 	= $this->result_class_params;
		
		$result = new $this->result_set_class($db->fetch($this, $class_name, array($class_params)), $class_params);
		
		$result->populate_joined_relations($this->populate_relations);
		
		
		return $result;
	}
	
	
	
	/**
	 * -----------------------------------
     * ============ Relations ============
	 * -----------------------------------
     */
	
	public function joinRel($relations = null, $join_i18n = true, $select = true)
	{
		if($relations === false){ return; }
		$relations = $relations ? (array)$relations : array_keys((array)$this->result_class_params['relations']);
		
		foreach($relations as $relation)
		{
//			if(strpos($relation, '.'))
//			{
//				$_relation = explode('.', $relation);
//				
//				$rel = $this->result_class_params['relations']->{$_relation[0]};
//				for($i = 1, $len = count($_relation); $i < $len; $i++)
//				{
//					$rel = $rel->rmodel();
//					$rel = $rel::relations()->{$_relation[$i]};
//				}
//			}
//			else
//			{
				$rel = $this->result_class_params['relations']->$relation;
//			}
				
			$joins = $rel->join($join_i18n);
			$joins->mn_join && $this->join($joins->mn_join);
			$this->join($joins->join);
			($join_i18n && $joins->i18n) && $this->join($joins->i18n);
			
			$select && $this->addSelect($joins->select);
			$this->populate_relations[] = $relation;
		}
		
		return $this;
	}
	
}



?>
