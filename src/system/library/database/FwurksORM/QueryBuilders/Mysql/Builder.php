<?php

namespace library\database\FwurksORM\QueryBuilders\Mysql;

use library\database\FwurksORM\QueryBuilders\QueryBuilder;
use library\database\FwurksORM\QueryBuilders\Raw;


/*

NOTE:
When there is an alias it will be written with key => value


*/



class Builder extends QueryBuilder
{
	protected $type;
	
	protected $table;
	
	
	protected $select = [];
	protected $where = [];
	protected $order;
	protected $group;
	
	
	
	
	// public function __construct( result_class ){}
	// public function __clone(){}
	
	public function __toString(){}
	public function sql(){}
	public function get(){}
	public function raw($v)
	{
		return new Raw($v);
	}
	
	
	public function table($table)
	{
		// if $table is a model instance
		
		$this->table = $table;
	}
	// public function force_index(){}
	// public function explain(){}
	
	public function select($fields = null)
	{
		$this->type = 'select';
		
		$fields = is_array($fields) ? $fields : func_get_args();
		
		$this->select = array_merge($this->select, $fields);
	}
	
	// public function insert(){}
	// public function update(){}
	// public function replace(){}
	// public function delete(){}
	// public function union(){}
	
	public function where($where, $value = null)
	{
		if($where instanceof Raw)
		{
			$where = [func_get_args()];
		}
		elseif(!is_array($where))
		{
			$where = [$where => $value];
		}
		
		$this->where = array_merge($this->where, $where);
	}
	
	public function order()
	{
		$this->order = func_get_args();
	}
	
	public function group(/*...$fields*/) // ??? WITH ROLLUP
	{
		$this->group = func_get_args();
	}
	
	// public function having(‘c > 1’) ??? SQL Injection{}
	public function limit($limit, $offset = 0){}
	// public function offset($offset){}
	
	// public function join(‘table|Model’, ‘a = b’){}
	// public function join([table|Model’, ‘tn’], ‘inner’, [‘a = b AND c = ?’, 1]){}
	
	// public function distinct(){}
	// public function calc_rows(){}
	// public function exist(){}
	
	// public function min(){}
	// public function max(){}
	// public function count( distinct ){}
	// public function avg(){}
	// public function sum(){}
	// public function concat(){}
}


?>