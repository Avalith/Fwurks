<?php

class MySQL extends SQL
{
	public function __destruct()
	{
		mysql_close($this->connection);
	}
	
	protected function connect()
	{
		$this->connection = mysql_connect($this->host, $this->user, $this->pass) or error('Error connecting to database');
		mysql_select_db($this->name, $this->connection) or error('Error selecting database');
		$this->query("SET NAMES utf8");
		
		
	}

	public function query($query, $count = 1)
	{
		if($count && (strpos($query, 'SELECT ') === 0 || strpos($query, '(SELECT ') === 0))
		{
			if (strpos($query, '(SELECT ') === 0){ $query = '(SELECT SQL_CALC_FOUND_ROWS '.substr($query, 8); }
			else{ $query = 'SELECT SQL_CALC_FOUND_ROWS '.substr($query, 7); }
			$rows_query = 'SELECT FOUND_ROWS()';
		}
		
		if($this->debug){ d($query, 'query'); }
		$result = mysql_query($query, $this->connection) or error(array($query, mysql_errno($this->connection) ,mysql_error($this->connection)));
		
		if($rows_query)
		{
			$total_rows = mysql_fetch_row(mysql_query($rows_query, $this->connection)); 
			$this->total_rows = end($total_rows); 
		}
		
		$this->last_id = mysql_insert_id($this->connection);
		$this->affected_rows = mysql_affected_rows($this->connection);
		
		return $result;
	}
	
	public function fetch_result($result, $class_name = null, array $class_params = null)
	{
		$params = array($result);
		if($class_name)
		{
			$params[] = $class_name; 
			$params[] = $class_params;
		}
		return call_user_func_array('mysql_fetch_object', $params);
	}
	
	public function escape($string)
	{
		if(get_magic_quotes_gpc())
		{
			return $string;
		}
		else
		{
			return mysql_real_escape_string($string, $this->connection);
		}
	}
	
	public function table_info($table)
	{
		if(isset(self::$cache[$table])){ return self::$cache[$table]; }
		
		foreach ($this->fetch($this->query("SHOW COLUMNS FROM " . $table)) as $c)
		{
			if (preg_match("/^(.+)\((\d+)(,(\d+))?/", $c->Type, $type_array))
			{
				$c->real_type 	= $type_array[1];
				$c->max_length 	= is_numeric($type_array[2]) ? $type_array[2] : -1;
				$c->scale 		= is_numeric($type_array[4]) ? $type_array[4] : -1;
			}
			else if (preg_match("/^(enum)\((.*)\)$/i", $c->Type, $type_array))
			{
				$c->real_type 	= $type_array[1];
				$c->max_length 	= max(array_map("strlen",explode(",",$type_array[2]))) - 2;
				$c->max_length 	= ($c->max_length == 0 ? 1 : $c->max_length);
			}
			else
			{
				$c->real_type 	= $c->Type;
				$c->max_length 	= -1;
			}
			
			$columns[$c->Field] = array
			(
				'name' 				=> $c->Field,
				'type' 				=> $c->Type,
				'real_type'			=> $c->real_type,
				'max_length' 		=> $c->max_length,
				'scale' 			=> $c->scale,
				'null' 				=> $c->Null == 'NO' ? false : true,
				'key' 				=> $c->Key,
				'default' 			=> $c->Default,
				'has_default' 		=> $c->Default && $c->default != 'NULL' ? true : false,
				'extra' 			=> $c->Extra,
				'primary_key'		=> strpos($c->Key, "PRI") === 0,
				'unique'			=> strpos($c->Key, "UNI") === 0 || strpos($c->Key, "PRI") === 0,
				'auto_increment'	=> strpos($c->Extra, 'auto_increment') !== false,
				'binary' 			=> strpos($c->Type,'binary') !== false,
				'unsigned' 			=> strpos($c->Type,'unsigned') !== false,
				'zerofill' 			=> strpos($c->Type,'zerofill') !== false,
			);
		}
		
		
		return self::$cache[$table] = $columns;
	}
	
	public function table_exists($table)
	{
		$result = end(mysql_fetch_row($this->query("SHOW TABLES LIKE '$table'")));
		return $table == $result ? true : false;
	}
	
	public function transaction( array $array ){}
	public function prepare($sql){}
}

?>
