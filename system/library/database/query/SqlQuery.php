<?php 

class SqlQueryException extends Exception { }


class SqlQuery extends SqlBase
{
	protected $string;
	
	protected $joins = array();
	protected $using;
	
	protected $fields;
	protected $values;
		
	protected $calc_rows;
	protected $distinct;
	protected $cache;
	
	protected $where;
	protected $having;
	
	protected $group;
	protected $group_rollup;
	
	protected $order;
	
	protected $limit;
	
	protected $delayed;
	protected $ignore;
//	protected $on_duplicate_update;
	
	
	public function __construct($string = null)
	{
		$string && $this->string = $string;
	}
	
	
	
	/**
	 * @param String $fields
	 * @return SqlQuery
	 */
	public function select($fields = '*')
	{
		$this->type = 'select';
		$this->fields = $fields;
		return $this;
	}
	
	
	/**
	 * @param String $fields
	 * @return SqlQuery
	 */
	public function addSelect($fields)
	{
		$this->type = 'select';
		$this->fields .= ($this->fields?', ':'').$fields;
		return $this;
	}
	
	
	/**
	 * @param Array $fields
	 * @return SqlQuery
	 */
	public function insert(array $fields = array())
	{
		$this->type = 'insert';
		$this->fields = $fields;
		return $this;
	}

	
	/**
	 * @param Array $fields
	 * @return SqlQuery
	 */
	public function multi_insert(array $fields, array $values)
	{
		$this->type = 'multi_insert';
		$this->values = $values;
		$this->fields = $fields;
		return $this;
	}

	
	/**
	 * @param Array $fields
	 * @return SqlQuery
	 */
	public function replace(array $fields = array())
	{
		$this->type = 'replace';
		$this->fields = $fields;
		return $this;
	}
	
	
	/**
	 * @param Array $fields
	 * @return SqlQuery
	 */
	public function multi_replace(array $fields, array $values)
	{
		$this->type = 'multi_replace';
		$this->values = $values;
		$this->fields = $fields;
		return $this;
	}
	
	
	/**
	 * @param Array $fields
	 * @return SqlQuery
	 */
	public function update(array $fields = array())
	{
		$this->type = 'update';
		$this->fields = $fields;
		return $this;
	}
	
	
	/**
	 * @param String $tables
	 * @return SqlQuery
	 */
	public function delete($tables = null)
	{
		$this->type = 'delete';
		$this->fields = $tables;
		return $this;
	}
	
	
	/**
	 * @param SqlJoin $join
	 * @return SqlQuery
	 */
	public function join($join)
	{
		$this->joins[] = $join;
		
		return $this;
	}
	
	
	/**
	 * @param Array SqlJoin $joins
	 * @return SqlQuery
	 */
	public function joins(array $joins)
	{
		$this->joins = array_merge($this->joins, $joins);
		return $this;
	}
	
	
	/**
	 * @param String $table
	 * @return SqlQuery
	 */
	public function using($table)
	{
		$this->using = $table;
		
		return $this;
	}
	
	
	/**
	 * @param String $criteria
	 * @param String $fields
	 * @return SqlQuery
	 */
	public function where($criteria, $fields = null)
	{
		$args = is_array($criteria) ? $criteria : func_get_args();
		$criteria = $this->_replaceCreteria(array_shift($args), $args);
		$criteria && $this->where = ' WHERE '.$criteria;
		
		return $this;
	}
	
	
	/**
	 * @param String $criteria
	 * @param String $fields ...
	 * @return SqlQuery
	 */
	public function having($criteria, $fields = null)
	{
		$args = func_get_args();
		$criteria = $this->_replaceCreteria(array_shift($args), $args);
		$criteria && $this->having = ' HAVING '.$criteria;
		
		return $this;
	}
	
	
	/**
	 * @param String $group
	 * @return SqlQuery
	 */
	public function group($group = null)
	{
		$group && $this->group[] = $group;
		
		return $this;
	}
	
	
	/**
	 * @return SqlQuery
	 */
	public function groupRollup()
	{
		$this->group_rollup = 'WITH ROLLUP';
		
		return $this;
	}
	
	
	/**
	 * @param String $group
	 * @param Boolean $asc
	 * @return SqlQuery
	 */
	public function order($order = null, $asc = true)
	{
		$order && $this->order[] = $order.($asc ? '' : ' DESC');
		
		return $this;
	}
	
	
	/**
	 * @param Integer $count
	 * @param Integer $offset
	 * @return SqlQuery
	 */
	public function limit($count = null, $offset = null)
	{
		$offset && $offset .= ', ';
		$count 	&& $this->limit = " LIMIT {$offset}{$count}";
		
		return $this;
	}
	
	
	/**
	 * @return SqlQuery
	 */
	public function calcRows()
	{
		$this->calc_rows = 'SQL_CALC_FOUND_ROWS ';
		
		return $this;
	}
	
	
	/**
	 * @return SqlQuery
	 */
	public function distinct()
	{
		$this->distinct = 'DISTINCT ';
		
		return $this;
	}
	
	
	/**
	 * @param Boolean $value
	 * @return SqlQuery
	 */
	public function cache($value = true)
	{
		$this->cache = 'SQL_'.($value ? '' : 'NO_').'CACHE ';
		
		return $this;
	}
	
	
	/**
	 * @return SqlQuery
	 */
	public function delayed()
	{
		$this->delayed = 'DELAYED ';
		
		return $this;
	}
	
	
	/**
	 * @return SqlQuery
	 */
	public function ignore()
	{
		$this->ignore = 'IGNORE ';
		
		return $this;
	}
	
	
	/**
	 * @return SqlQuery
	 */
	/*
	public function on_duplicate_update()
	{
		$this->on_duplicate_update = 'ON DUPLICATE KEY UPDATE ';
		
		return $this;
	}
	*/

	protected function __str_string()
	{
		return "$this->string";
	}
	
	protected function __str_select()
	{
		$group = $order = $joins = '';
		
		$this->table 			&& $from 	 = 	' FROM '.$this->table;
		$this->table_as 		&& $from 	.= 	' AS '.$this->table_as;
		is_array($this->group) 	&& $group 	 = 	' GROUP BY '.implode(', ', $this->group);
		$this->group_rollup 	&& $group	.= 	' '.$this->group_rollup;
		is_array($this->order) 	&& $order 	 = 	' ORDER BY '.implode(', ', $this->order);
		is_array($this->joins)	&& $joins 	 =	' '.implode(' ', $this->joins);
		
		return "SELECT {$this->calc_rows}{$this->cache}{$this->distinct}{$this->fields}{$from}{$joins}{$this->where}{$group}{$this->having}{$order}{$this->limit}";
	}
	
	protected function __str_insert()
	{
		$fields = $this->_fields();
		
		return "INSERT {$this->delayed}{$this->ignore}INTO {$this->table} SET {$fields}";//{$this->on_duplicate_update}";
	}
	
	protected function __str_multi_insert()
	{
		$fields = $this->fields;
		sort($fields);
		$fields = '(`'.implode('`, `', $fields).'`)';
		
		$values = $this->_values();
		
		return "INSERT {$this->delayed}{$this->ignore}INTO {$this->table} {$fields} VALUES {$values}";//{$this->on_duplicate_update}";
	}
	
	protected function __str_replace()
	{
		$fields = $this->_fields();
		
		return "REPLACE {$this->delayed}INTO {$this->table} SET {$fields}";
	}
	
	protected function __str_multi_replace()
	{
		$fields = $this->fields;
		sort($fields);
		$fields = '(`'.implode('`, `', $fields).'`)';
		
		$values = $this->_values();
		
		return "REPLACE {$this->delayed}INTO {$this->table} {$fields} VALUES {$values}";
	}
	
	protected function __str_update()
	{
		$group = $order = $joins = '';
		
		$table = $this->table;
		
		$this->table_as 		&& $table	.= 	' AS '.$this->table_as;
		is_array($this->group) 	&& $group 	 = 	' GROUP BY '.implode(', ', $this->group);
		is_array($this->order) 	&& $order 	 = 	' ORDER BY '.implode(', ', $this->order);
		is_array($this->joins)	&& $joins 	 =	' '.implode(' ', $this->joins);
		
		$fields = $this->_fields();
		return "UPDATE {$this->ignore}{$table}{$joins} SET {$fields}{$this->where}{$order}{$this->limit}";
	}
	
	protected function __str_delete()
	{
		$group = $order = $joins = $using = '';
		
		$this->table 			&& $table 	 = 	' FROM '.$this->table;
		is_array($this->using)	&& $using 	 =	' USING '. $this->using;
		is_array($this->joins)	&& $joins 	 =	' '.implode(' ', $this->joins);
		is_array($this->order) 	&& $order 	 = 	' ORDER BY '.implode(', ', $this->order);
		
		if($this->using)
		{
			return "DELETE {$this->ignore}{$table}{$using}{$joins}{$this->where}{$order}{$this->limit}";
		}
		else
		{
			return "DELETE {$this->ignore}{$this->fields}{$table}{$joins}{$this->where}{$order}{$this->limit}";
		}
	}
	
	
	protected function _replaceCreteria($criteria, array $fields = array())
	{
		$_this = $this;
		
		$criteria = preg_replace_callback('~\?(\w+)?~', function($matches) use (&$fields, $_this){ $matches[] = ''; return $_this->_escape(array_shift($fields), $matches[1]); }, $criteria);
		
		return $criteria;
	}
	
	protected function _fields()
	{
		$fields = array();
		foreach($this->fields as $k => $val)
		{
			$k = explode('?', $k); 
			$k[] = 's';
			
			$k[0] = strtr($k[0], array('.' => '`.`'));
			$fields[] = "`{$k[0]}` = ".$this->_escape($val, $k[1]);
		}
		return implode(', ', $fields);
	}
	
	protected function _values()
	{
		$values = array();
		foreach($this->values as $val)
		{
			foreach($val as $k => &$v)
			{
				$k = explode('?', $k); 
				$k[] = 's';
				$v = $this->_escape($v, $k[1]);
			}
			ksort($val);
			$values[] = implode(', ', $val);
		}
		
		return '('.implode('), (', $values).')';
	}
	
	public function _escape($val, $type)
	{
		switch($type)
		{
			case 'sql':
//				$val;
			break;
			
			case 'arr_i':
				foreach($val as &$v){ $v = (int)$v; }
				$val = implode(',', $val);
			break;
			
			case 'arr_f':
				foreach($val as &$v){ $v = (double)$v; }
				$val = implode(',', $val);
			break;
			
			case 'arr':
				foreach($val as &$v){ $v = is_numeric($v) ? $v : "'".Registry::$db->escape($v)."'"; }
				$val = implode(',', $val);
			break;
			
			case 'i':
				$val = (int)$val;
			break;
			
			case 'f':
				$val = (double)$val;
			break;
			
			case 'b':
				$val = $val ? 1 : 0;
			break;
			
			case 's': default:
				$val = is_numeric($val) ? $val : "'".Registry::$db->escape($val)."'";
			break;
		}
		
		return $val;
	}
	
}

/**
 * @param String $string Query string
 * @return SqlQuery
 */
function SqlQuery($string = null)
{
	return new SqlQuery($string);
}

?>
