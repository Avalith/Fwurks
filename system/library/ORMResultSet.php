<?php

class ORMResultSet implements ArrayAccess, SeekableIterator, Countable
{
	public $total		= 0;
	public $results 	= array();
	
	public function __construct(array $results = array(), array $params = array())
	{
		$this->results = $results;
		$this->total = Registry::$db->total_rows();
		
		$this->table_name = $params['table_name'];
		$this->model_name = $params['model_name'];
	}
	
	
	/**
	 * -----------------------------
	 * ======= Params Access =======
	 * -----------------------------
	 */
	protected $table_name	= '';
	protected $model_name	= '';
	
	public function table_name(){ return $this->table_name; }
	public function model_name(){ return $this->model_name; }
	
	
	/**
	 * ------------------------------------
	 * ======= Array Access Methods =======
	 * ------------------------------------
	 */
	public function offsetExists($offset)			{ return isset($this->results[$offset]); }
	
	public function offsetGet	($offset)			{ return isset($this->results[$offset]) ? $this->results[$offset] : null; }
	
	public function offsetSet	($offset, $value)	{ $this->results[$offset] = $value; }
	
	public function offsetUnset	($offset)			{ unset($this->results[$offset]); $this->results = array_values($this->results); }
	
	
	/**
	 * ------------------------------------
	 * ========= Iterator Methods =========
	 * ------------------------------------
	 */
	private $position = 0;
	
	public function seek($position)
	{
		$this->position = $position;
		if(!$this->valid()){
			throw new OutOfBoundsException("invalid seek position ($position)");
		}
	}
	
	public function rewind()	{ $this->position = 0; }
	
	public function current()	{ return $this->results[$this->position]; }
	
	public function key()		{ return $this->position; }
	
	public function next()		{ ++$this->position; }
	
	public function valid()		{ return isset($this->results[$this->position]); }
	
	
	/**
	 * ------------------------------------
	 * ========= Countable Methods ========
	 * ------------------------------------
	 */
	public function count(){ return count($this->results); }
	
}

?>
