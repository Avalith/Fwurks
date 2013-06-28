<?php

abstract class SQL_Adapter
{
	protected $host;

	protected $name;

	protected $user;

	protected $pass;

	protected $port;
	
	protected $connection;
	
	protected $is_connected;

	protected $last_id;
	protected $affected_rows;
	protected $total_rows;
	
	public $debug;
	
	
	public function __construct(array $params)
	{
		$this->host = $params['host'];
		$this->name = $params['name'];
		$this->user = $params['user'];
		$this->pass = $params['pass'];
		$this->port = $params['port'];
		
		$this->connect();
		return true;
	}
	
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

	final public function update($table, array $fields_values, $where = null, $add = null)
	{
		$where = $this->checkWhereParam($where);
		foreach ($fields_values as $field => $value) { $fieldVals[] = "`$field` = " . $this->qstr($value); }
		$field_vals = implode(', ', $fieldVals);
		
		if($this->query("UPDATE $table SET $field_vals $where $add")){ return $this->affected_rows(); }
		return 0;
	}

	final public function delete($table, $where = null, $add = null)
	{
		$where = $this->checkWhereParam($where);
		if($this->query("DELETE FROM $table $where $add")){ return $this->affected_rows(); }
		return 0;
	}

	final public function select($table, $what = '*', $where = null, $add = null)
	{
		$where = $this->checkWhereParam($where);
		$query = "SELECT $what FROM $table $where $add";
		return $this->fetch($this->query($query));
	}
	
	final public function fetch($result, $class_name = null, array $class_params = array())
	{
		$data = array();
		while ($row = $this->fetch_result($result, $class_name, $class_params)){ $data[] = $row; }
		
		return $data;
	}
	
	final public function last_id(){ return $this->last_id; }
	
	final public function affected_rows(){ return $this->affected_rows; }
	
	final public function total_rows(){ return $this->total_rows; }
	
	final public function qstr($string)
	{
		if 		(is_numeric($string) && !is_string($string)){ return $string; }
		else if (is_null($string))							{ return "NULL"; }
		else												{ return "'".$this->escape($string)."'"; }
	}
	
	final protected function checkWhereParam($param)
	{
		switch (gettype($param))
		{
			case 'integer': 	return "WHERE id=$param"; 	break;
			case 'string': 		return "WHERE ".$param; 	break;
			default: 			return '';		 			break;
		}
	}
	
	
	public function table_exists($table)
	{
		static $cache;
		if(isset($cache[$table])){ return $cache[$table]; }
		
		$result = $this->fetch_result($this->query("SHOW TABLES LIKE '$table'"));
		is_object($result) && $result = end($result);
		
		return $cache[$table] = ($table == $result);
	}
	
	public function table_info($table)
	{
		static $cache;
		if(isset($cache[$table])){ return $cache[$table]; }
		
		
		// Status
		$status = (object)array();
		foreach($this->fetch_result($this->query("SHOW TABLE STATUS WHERE Name = '$table'")) as $k => $s){ $status->{strtolower($k)} = $s; }
		
		// Columns
		$special_types = array
		(
			'tinytext' 		=> array('real_type' => 'varchar', 'max_length' => 255),
			'text' 			=> array('real_type' => 'varchar', 'max_length' => 65535),
			'mediumtext' 	=> array('real_type' => 'varchar', 'max_length' => 16777215),
			'longtext' 		=> array('real_type' => 'varchar', 'max_length' => 4294967295),
		
			'tinyint' 		=> array('real_type' => 'integer', 'max_length' => 3),
			'smallint' 		=> array('real_type' => 'integer', 'max_length' => 5),
			'mediumint'		=> array('real_type' => 'integer', 'max_length' => 8),
			'int' 			=> array('real_type' => 'integer', 'max_length' => 11),
			'bigint' 		=> array('real_type' => 'integer', 'max_length' => 20),
		);
		$columns = array();
		foreach($this->fetch($this->query('SHOW COLUMNS FROM ' . $table)) as $c)
		{
			$c->sql_type = $c->Type;
			
			if (preg_match("/^(.+)\((\d+)(,(\d+))?/", $c->sql_type, $type_array))
			{
				$c->real_type = $c->Type 	= $type_array[1];
				isset($special_types[$c->real_type]) && $c->real_type = $special_types[$c->real_type]['real_type'];
				
				$c->max_length 	= isset($type_array[2]) && is_numeric($type_array[2]) ? $type_array[2] : -1;
				$c->scale 		= isset($type_array[4]) && is_numeric($type_array[4]) ? $type_array[4] : -1;
			}
			else if (preg_match("/^(enum)\((.*)\)$/i", $c->sql_type, $type_array))
			{
				$c->real_type 	= $type_array[1];
				$c->max_length 	= max(array_map("strlen",explode(",",$type_array[2]))) - 2;
				$c->max_length 	|| $c->max_length = 1;
			}
			else if(isset($special_types[$c->sql_type]))
			{
				$st = $special_types[$c->sql_type];
				$c->real_type = $st['real_type'];
				isset($c->max_length) || $c->max_length = $st['max_length'];
			}
			else
			{
				$c->real_type 	= $c->sql_type;
				$c->max_length 	= -1;
			}
			
			$columns[$c->Field] = (object)array
			(
				'name' 				=> $c->Field,
				'type' 				=> $c->Type,
				'real_type'			=> $c->real_type,
				'sql_type' 			=> $c->sql_type,
				'max_length' 		=> $c->max_length,
				'scale' 			=> isset($c->scale) ? $c->scale : null,
				'null' 				=> $c->Null != 'NO',
				'not_null' 			=> $c->Null == 'NO',
				'key' 				=> $c->Key,
				'default' 			=> $c->Default,
				'has_default' 		=> $c->Default && $c->default != 'NULL',
				'extra' 			=> $c->Extra,
				'primary_key'		=> strpos($c->Key, "PRI") === 0,
				'unique'			=> strpos($c->Key, "UNI") === 0 || strpos($c->Key, "PRI") === 0,
				'auto_increment'	=> strpos($c->Extra, 'auto_increment') !== false,
				'binary' 			=> strpos($c->Type,'binary') !== false,
				'unsigned' 			=> strpos($c->Type,'unsigned') !== false,
				'zerofill' 			=> strpos($c->Type,'zerofill') !== false,
			);
		}
		
		// Indices
		$index = array();
		foreach($this->fetch($this->query('SHOW INDEX FROM ' . $table)) as $i)
		{
			//d($i); 	
			isset($index[$i->Key_name]) || $index[$i->Key_name] = (object)array();
			$index[$i->Key_name]->name 		= $i->Key_name;
			$index[$i->Key_name]->table 	= $i->Table;
			$index[$i->Key_name]->null 		= $i->Null == 'YES';
			$index[$i->Key_name]->unique 	= !$i->Non_unique;
			$index[$i->Key_name]->fulltext 	= $i->Index_type == 'FULLTEXT';
			
			$index[$i->Key_name]->columns[$i->Seq_in_index-1] = $i->Column_name;
		}
		
		
		// Result
		return $cache[$table] = (object)array('status' => $status, 'columns' => $columns, 'indices' => $index);
	}
	
	
	/**
	 * ------------------------------------
     * =========== Abstract Methods ===========
	 * ------------------------------------
     */
	abstract protected function connect();

	abstract public function __destruct();
	
	abstract public function query($query, $count = true);
	
	abstract public function fetch_result($result, $class_name = null, array $class_params = array());

	abstract public function escape($string);

	abstract public function is_connected();
}
?>