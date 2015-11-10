<?php

namespace library\database\FwurksORM\QueryBuilders\Mysql;

use library\database\FwurksORM\QueryBuilders\QueryBuilder;
use library\database\FwurksORM\QueryBuilders\Raw;
// use library\database\FwurksORM\QueryBuilders\Formatter;


/*

NOTE:
When there is an alias it will be written with key => value


*/



class Builder extends QueryBuilder
{
	protected $type;
	
	protected $from;
	
	
	protected $select = [];
	protected $where = [];
	protected $having = [];
	protected $group;
	protected $order;
	protected $limit;
	protected $offset;
	
	
	// public function __construct( result_class ){}
	// public function __clone(){}
	
	public function __toString()
	{
		// Prolly do this as factory
		$formatter = 'library\\database\\FwurksORM\\QueryBuilders\\Mysql\\Formatters\\' . $this->type;
		$formatter = new $formatter($this);
		
		return $formatter->get();
	}
	
	public function __get($name)
	{
		return $this->$name;
	}
	
	public function sql(){}
	
	public function get(){}
	
	public function raw($v)
	{
		return new Raw($v);
	}
	
	
	public function from($from)
	{
		// if $from is a model instance
		
		$this->from = $from;
	}
	// public function force_index(){}
	// public function explain(){}
	
	public function select($fields = null)
	{
		$this->type = 'Select';
		
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
	
	public function order(...$order)
	{
		$this->order = $order;
	}
	
	public function group(...$fields) // ??? WITH ROLLUP
	{
		$this->group = $fields;
	}
	
	public function having($having, $value = null)
	{
		if($having instanceof Raw)
		{
			$having = [func_get_args()];
		}
		elseif(!is_array($having))
		{
			$having = [$having => $value];
		}
		
		$this->having = array_merge($this->having, $having);
	}
	
	public function limit($limit, $offset = null)
	{
		$this->limit = $limit;
		$offset && $this->offset = $offset;
	}
	
	public function offset($offset)
	{
		$this->offset = $offset;
	}
	
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