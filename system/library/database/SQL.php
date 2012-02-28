<?php

abstract class SQL
{
	protected $host;

	protected $name;

	protected $user;

	protected $pass;

	protected $port;
	
	protected $connection;

	public $prefix;
	
	protected $last_id;
	protected $affected_rows;
	protected $total_rows;
	
	public $debug;
	
	/**
	 * Stored info for table information and table exists
	 *
	 * @var array
	 * @static
	 * @access protected
	 */
	protected static $cache;
	
	public function __construct(array $params)
	{
		$this->host = $params['host'];
		$this->name = $params['name'];
		$this->user = $params['user'];
		$this->pass = $params['pass'];
		$this->port = $params['port'];
		$this->prefix = $params['prefix'];
		
		$this->connect();
		return true;
	}
	
	abstract public function __destruct();
	
	final public function insert($table, array $fields_values)
	{
		foreach ($fields_values as $field => $value)
		{
			$fields[] = '`'.$field.'`';
			$values[] = $this->qstr($value);
		}
		
		$this->query('INSERT INTO '.$table.'('.implode(', ', $fields).') VALUES ('.implode(', ', $values).')');
		
		return $this->last_id();
	}

	final public function update($table, array $fields_values, $where = null)
	{
		$where = $this->checkWhereParam($where);
		foreach ($fields_values as $field => $value) { $fieldVals[] = "`$field` = " . $this->qstr($value); }
		
		if($this->query('UPDATE '.$table.' SET '.implode(', ', $fieldVals).' '.$where))
		{
			return 1;
		}
		return 0;
	}

	final public function delete( $table, $where = null)
	{
		if($this->query("DELETE FROM $table " . $this->checkWhereParam($where)))
		{
			return $this->affected_rows();
		}
		return 0;
	}

	final public function select($table, $what = '*', $where = null, $add = null, $change = false)
	{
		$where = $this->checkWhereParam($where);
		$query = "SELECT $what FROM $table $where $add";
		return $this->fetch($this->query($query), $change);
	}
	
	final public function fetch($result, $change = false, $class_name = null, array $class_params = null)
	{
		$data = array();
		while ($row = $this->fetch_result($result, $class_name, $class_params)){ $data[] = $row; }
		
		return $change && count($data) == 1 ? end($data) : $data;
	}
	
	final public function last_id()
	{
		return $this->last_id;
	}
	
	final public function affected_rows()
	{
		return $this->affected_rows;
	}
	
	final public function total_rows()
	{
		return $this->total_rows;
	}
	
	final public function qstr($string)
	{
		if (is_numeric($string))
		{
			return $string;
		}
		if (is_null($string) || empty($string))
		{
			return "NULL";
		}
		else
		{
			return "'".$this->escape($string)."'";
		}
	}
	
	final protected function checkWhereParam($param)
	{
		if(empty($param)){ $param = null; }
		
		switch (gettype($param))
		{
			case 'integer': 	return "WHERE id=$param"; 	break;
			case 'string': 		return "WHERE ".$param; 		break;
			default: 			return ''; 						break;
		}
	}
	
	abstract protected function connect();

	abstract public function query($query, $count = 1);
	
	abstract public function fetch_result($result, $class_name = null, array $class_params = null);

	abstract public function escape($string);

	abstract public function table_exists($name);

	abstract public function table_info($table_name);
	
	abstract public function transaction( array $array );

	abstract public function prepare($sql);
}

?>