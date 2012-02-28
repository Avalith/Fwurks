<?php

class MySQL_Adapter extends SQL_Adapter
{
	protected $is_connected = true;
	
	public function __destruct()
	{
		mysql_close($this->connection);
	}
	
	protected function connect()
	{
		$this->connection = mysql_connect($this->host, $this->user, $this->pass) or error('Error connecting to database');
		$this->connection || $this->is_connected = false;
		
		$this->is_connected() && mysql_select_db($this->name, $this->connection) or error('Error selecting database');
		$this->is_connected() && $this->query("SET NAMES utf8");
	}

	public function query($query, $count = true)
	{
		if($count && strpos($query, 'SELECT ') === 0)
		{
			$query = 'SELECT SQL_CALC_FOUND_ROWS '.substr($query, 7);
			$rows_query = 'SELECT FOUND_ROWS()';
		}
		
		if($this->debug){ d($query, 'query'); }
		
		$result = mysql_query($query, $this->connection) or error(array($query, mysql_errno($this->connection) ,mysql_error($this->connection)));
		
		if(isset($rows_query))
		{
			$total_rows = $this->fetch_result(mysql_query($rows_query, $this->connection)); 
			$this->total_rows = end($total_rows); 
		}
		
		$this->last_id = mysql_insert_id($this->connection);
		$this->affected_rows = mysql_affected_rows($this->connection);
		
		return $result;
	}
	
	public function fetch_result($result, $class_name = null, array $class_params = array())
	{
		$params = array($result);
		$class_name && $params[] = $class_name; 
		$class_name && $params[] = $class_params;
		
		return call_user_func_array('mysql_fetch_object', $params);
	}
	
	public function escape($string)
	{
		$return = get_magic_quotes_gpc() ? $string : mysql_real_escape_string($string, $this->connection);
		$return || $return = mysql_escape_string($string);
		
		return $return;
	}
	
	
	final public function is_connected()
	{
		return $this->is_connected;
	}
}

?>
