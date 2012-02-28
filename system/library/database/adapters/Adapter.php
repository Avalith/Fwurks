<?php

class SqlAdapterException extends Exception { }

abstract class SqlAdapter
{
	protected $host;
	protected $port;
	
	protected $name;
	protected $user;
	protected $pass;
	
	protected $connection;
	
	protected $last_id;
	protected $affected_rows;
	protected $total_rows;
	
	public $debug = false;
	
	final public function __construct($name, $user, $pass, $host = 'localhost', $port = 3306)
	{
		$this->host = $host;
		$this->port = $port;
		
		$this->name = $name;
		$this->user = $user;
		$this->pass = $pass;
		
		$this->connect();
		return $this->is_connected();
	}
	
	final public function __destruct()
	{
		$this->disconnect();
		$this->connection = null;
	}
	
	final public function is_connected(){ return $this->connection !== null; }
	
		
	final public function last_id(){ return $this->last_id; }
	
	final public function affected_rows(){ return $this->affected_rows; }
	
	final public function total_rows(){ return $this->total_rows; }
	
		
	final public function fetch(SqlBase $query, $class_name = null, array $class_params = array())
	{
		$this->debug && $query->debug();
		return $this->fetch_data($this->query($query), $class_name, $class_params);
	}
	
	
		
	/**
	 * ------------------------------------
     * =========== Abstract Methods ===========
	 * ------------------------------------
     */
	abstract protected function connect();
	abstract protected function disconnect();
	
	abstract public function query(SqlBase $query);
	abstract protected function fetch_data($result, $class, array $params);
	abstract public function fetch_result($result, $class = null, array $params = array());
	
	abstract public function escape($string);
	
	
}

?>