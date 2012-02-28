<?php

class MySQLi_Adapter extends SQL_Adapter
{
	public function __destruct()
	{
		$this->connection->close();
	}
	
	protected function connect()
	{
		$this->connection = new mysqli($this->host, $this->user, $this->pass, $this->name, $this->port) or error('Error connecting to database');
		$this->is_connected() && $this->query("SET NAMES utf8");
	}

	public function query($query, $count = true)
	{
		if($count && strpos($query, 'SELECT ') === 0)
		{
			$query = 'SELECT SQL_CALC_FOUND_ROWS '.substr($query, 7);
			$rows_query = 'SELECT FOUND_ROWS() as count';
		}
		
		if($this->debug){ d($query, 'query'); }
		
		$result = $this->connection->query($query) or error(array($query, $this->connection->errno ,$this->connection->error));
		
		isset($rows_query) && $this->total_rows = $this->connection->query($rows_query)->fetch_object()->count;
		
		$this->last_id = $this->connection->insert_id;
		$this->affected_rows = $this->connection->affected_rows;
		
		return $result;
	}
	
	public function fetch_result($result, $class_name = null, array $class_params = array())
	{
		$params = array();
		$class_name && $params[] = $class_name; 
		$class_name && $params[] = $class_params;
		
		return call_user_func_array(array($result, 'fetch_object'), $params);
	}
	
	public function escape($string)
	{
		$return = get_magic_quotes_gpc() ? $string : $this->connection->real_escape_string($string);
		$return || $return = mysql_escape_string($string);
		
		return $return;
	}
	
	final public function is_connected()
	{
		return !mysqli_connect_errno();
	}
}

?>
