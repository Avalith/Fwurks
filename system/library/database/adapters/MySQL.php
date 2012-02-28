<?php

class MySQL_Adapter extends SqlAdapter
{
	protected $result_class_name = 'mysql_result';
	
	protected function connect()
	{
		$this->connection = mysql_connect($this->host.':'.$this->port, $this->user, $this->pass);
		
		if($this->is_connected())
		{
			mysql_select_db($this->name, $this->connection);
			$this->query(new SqlQuery('SET NAMES utf8'));
		}
		else
		{
			throw new SqlAdapterException('Cannot connect to the database');
		}
	}
	
	public function disconnect()
	{
		$this->is_connected() && mysql_close($this->connection);
	}
	
	
	public function query(SqlBase $query)
	{
		$result = $this->connection->query($query);
		
		if(!$result){ throw new SqlAdapterException($this->connection->error ."\n$query", $this->connection->errno); }
		
		$query->canCalcRows() && $this->total_rows = $this->connection->query('SELECT FOUND_ROWS() as count')->fetch_object()->count;
		
		$this->last_id = mysql_insert_id($this->connection);
		$this->affected_rows = mysql_affected_rows($this->connection);
		
		return $result;
	}
	
	protected function fetch_data($result, $class_name, array $class_params)
	{
		if(is_resource($result))
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
		return $class_name ? mysql_fetch_object($result, $class_name, $class_params) : mysql_fetch_object($result);
	}
	
	
	public function escape($string)
	{
		$return = mysql_real_escape_string($string);
		
		return $return;
	}
}

?>
