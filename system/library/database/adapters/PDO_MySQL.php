<?php

class PDO_MySQL_Adapter extends SqlAdapter
{
	protected function connect()
	{
		$this->connection = new PDO("mysql:host={$this->host};port={$this->port};dbname={$this->name}", $this->user, $this->pass, array
		(
			PDO::MYSQL_ATTR_INIT_COMMAND 		=>  'SET NAMES utf8',
//			PDO::MYSQL_ATTR_DIRECT_QUERY 		=>  1,
//			PDO::MYSQL_ATTR_MAX_BUFFER_SIZE 	=>  100,
		));
		
		if(!$this->is_connected())
		{
			throw new SqlAdapterException('Cannot connect to the database');
		}
	}
	
	public function disconnect()
	{
//		$this->is_connected() && $this->connection->close();
	}
	
	
	public function query(SqlBase $query)
	{
		$this->debug && $query->debug();
		
		$result = $this->connection->query($query);
		if(!$result){ throw new SqlAdapterException($this->connection->error ."\n$query", $this->connection->errno); }
		
		$query->canCalcRows() && $this->total_rows = $this->connection->query('SELECT FOUND_ROWS() as count')->fetch_object()->count;
		
		$this->last_id = $this->connection->lastInsertId();
		$this->affected_rows = $result->rowCount();
		
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
		return $class_name ? $result->fetchObject($class_name, $class_params) : $result->fetch_object();
	}
	
	
	public function escape($string)
	{
		$return = $this->connection->real_escape_string($string);
		$return || $return = mysql_escape_string($string);
		
		return $return;
	}
}

?>
