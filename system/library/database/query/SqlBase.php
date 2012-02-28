<?php

/**
 * @abstract
 */
class SqlBase
{
	protected $type;
	
	public $table;
	protected $table_as;
		
	protected $calc_rows;
	
	
	public function __toString()
	{
		$type = $this->type ?: 'string';
		return $this->{"__str_{$type}"}();
	}
	
	
	/**
	 * @param String $table
	 * @param String $as
	 * @return SqlQuery
	 */
	public function table($table, $as = null)
	{
		$this->table = $table;
		$this->table_as = $as;
		
		return $this;
	}
	
	
	/**
	 * @param SqlAdapter $db
	 * @param result $result
	 * @return SqlQuery
	 */
	public function run(SqlAdapter $db = null, &$result = null)
	{
		$db || $db = Registry::$db; 
		$result = $db->query($this);
		return $this;
	}
	
	
	/**
	 * @param SqlAdapter $db
	 * @param String $class_name
	 * @param String $class_params
	 * @return Array
	 */
	public function result(SqlAdapter $db = null, $class_name = null, array $class_params = array())
	{
		$db || $db = Registry::$db;
		return $db->fetch($this, $class_name, $class_params);
	}
	
	
	public function debug()
	{
		$nl_keywords = array
		(
			'FROM', 'JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN',
			'WHERE', 'GROUP BY', 'ORDER BY', 'HAVING', 'LIMIT', 
			
			'SET', 
			
			'ADD COLUMN', 'CHANGE COLUMN', 'MODIFY COLUMN', 'DROP COLUMN',
			'ADD PRIMARY', 'ADD UNIQUE', 'ADD FULLTEXT', 'DROP PRIMARY KEY', 'DROP KEY',
		);
		
		$sql = preg_replace('~('.implode('|', $nl_keywords).')~', "\n$1", (string)$this);
		$sql = preg_replace('~([A-Z]{2,})~', '<span style="color: #00F;">$1</span>', $sql);
		$sql = preg_replace('~`([^`]+)`~', '<span style="color: #C0C;">`$1`</span>', $sql);
		
		echo "<pre style=\"color: #333;\">$sql;</pre>";
		
		return $this;
	}
	
	
	protected function __str_string()
	{
		return '';
	}
	
	
	/**
	 * @return Boolean
	 */
	public function canCalcRows()
	{
		return $this->calc_rows ? true : false;
	}
	
} 

?>
