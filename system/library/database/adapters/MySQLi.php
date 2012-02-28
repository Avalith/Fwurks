<?php

class MySQLi_Adapter extends SqlAdapter
{
	protected function connect()
	{
		$this->connection = new mysqli($this->host, $this->user, $this->pass, $this->name, $this->port);
		
		if($this->is_connected())
		{
			$this->query(new SqlQuery('SET NAMES utf8'));
		}
		else
		{
			throw new SqlAdapterException('Cannot connect to the database');
		}
	}
	
	public function disconnect()
	{
		$this->is_connected() && $this->connection->close();
	}
	
	
	public function query(SqlBase $query)
	{
		$result = $this->connection->query($query);
		if(!$result){ throw new SqlAdapterException($this->connection->error ."\n$query", $this->connection->errno); }
		
		$query->canCalcRows() && $this->total_rows = $this->connection->query('SELECT FOUND_ROWS() as count')->fetch_object()->count;
		
		$this->last_id = $this->connection->insert_id;
		$this->affected_rows = $this->connection->affected_rows;
		
		return $result;
	}
	
	protected function fetch_data($result, $class_name, array $class_params)
	{
		if($result instanceof mysqli_result)
		{
			$data = array();
			while($row = $this->fetch_result($result, $class_name, $class_params)){ $data[] = $row; }
		}
		else
		{
			$data = $result;
		}
		
		return $data;
	}
	
	public function fetch_result($result, $class_name = 'stdClass', array $class_params = array())
	{
		return $class_name ? $result->fetch_object($class_name, $class_params) : $result->fetch_object();
	}
	
	
	public function escape($string)
	{
		$return = $this->connection->real_escape_string($string);
		
		return $return;
	}
}

?>
