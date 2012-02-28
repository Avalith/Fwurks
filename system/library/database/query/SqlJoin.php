<?php 

class SqlJoin
{
	protected $type;
	
	protected $table;
	protected $table_as;
	
	protected $on;
	protected $using;
	
	public function __construct($table, $as = null)
	{
		$this->table = $table;
		$this->table_as = $as;
		
		return $this;
	}
	
	/**
	 * @return SqlJoin
	 */
	public function left()		{ $this->type = 'LEFT '; 	return $this; }
	
	/**
	 * @return SqlJoin
	 */
	public function right()		{ $this->type = 'RIGHT '; 	return $this; }
	
	/**
	 * @return SqlJoin
	 */
	public function inner()		{ $this->type = 'INNER '; 	return $this; }
	
	/**
	 * @param String $on
	 * @return SqlJoin
	 */
	public function on($on)
	{
		$this->on = ' ON '.$on;
		
		return $this;
	}
	
	/**
	 * @param String $using
	 * @return SqlJoin
	 */
	public function using($using)
	{
		$this->using = ' USING '.$using;
		
		return $this;
	}
	
	
	public function __toString()
	{
		$this->table 			&& $table 		 = 	$this->table;
		$this->table_as 		&& $table 		.= 	' AS '.$this->table_as;
		
		return "{$this->type}JOIN {$table}{$this->on}{$this->using}";
	}
	
}

/**
 * @return SqlJoin
 */
function SqlJoin($table, $as = null)
{
	return new SqlJoin($table, $as);
}

?>