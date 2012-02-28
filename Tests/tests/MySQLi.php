<?php

require_once __DIR__.'/../../system/configs/database.config.php';
require_once __DIR__.'/../../system/library/DatabaseFactory.php';

class Result
{
	public $param;
	public function __construct($param)
	{
		$this->param = $param;
	}
}

class MySQLi_TestSuite extends TestSuite 
{
	private $db;
	
	private $table_name = 'tests';
	
	public function __construct()
	{
		$this->db = DatabaseFactory::create('MySQLi');
		
		$correct = array
		(
			array('id' => 1, 'test' => 123, 'active' => 1 ),
			array('id' => 2, 'test' => 234, 'active' => 1 ),
			array('id' => 3, 'test' => 345, 'active' => 0 ),
			array('id' => 4, 'test' => 456, 'active' => 1 ),
			array('id' => 5, 'test' => 567, 'active' => 0 ),
		);
		
		$this->db->query('TRUNCATE TABLE '.$this->table_name);
		foreach($correct as $c){ $this->db->insert($this->table_name, $c); }
		
		
		
		parent::__construct();
	}
	
	protected function test__instance()
	{
		$this->db instanceof SQL_Adpter || $this->error('the object is instance of "'.get_class($this->db).'" and not "SQL"');
	}
	
	protected function test__escape()
	{
		$var = array
		(
			123							=> 123,
			'a string' 					=> 'a string',
			'another\' string'			=> "another\' string",
			'st"r"ing'					=> 'st\\"r\\"ing',
			'str\ing'					=> 'str\\\\ing',
		);
		
		foreach($var as $v => $correct)
		{
			$result = $this->db->escape($v);
			$result != $correct && $this->error("&lt; $v &gt; must be &lt; $correct &gt; and not &lt; $result &gt;");
		}
	}
	
	protected function test__qstr()
	{
		$var = array
		(
			123							=> 123,
			'a string' 					=> "'a string'",
			'another\' string'			=> "'another\' string'",
			'st"r"ing'					=> "'st\\\"r\\\"ing'",
			'str\ing'					=> "'str\\\\ing'",
		);
		
		foreach($var as $v => $correct)
		{
			$result = $this->db->qstr($v);
			$result != $correct && $this->error("&lt; $v &gt; must be &lt; $correct &gt; and not &lt; $result &gt;");
		}
	}
	
	protected function test__query()
	{
		$result = $this->db->query($query = 'SELECT 1');
		$result instanceof mysqli_result || $this->error("Could not execute query '$query'");
	}
	
	protected function test__fetch_result()
	{
		$class = 'Result';
		$param = 123;
		
		$result = $this->db->fetch_result($this->db->query($query = 'SELECT 1 as n'), $class, array($param));
		
		$result instanceof $class 	|| $this->error('the result object is instance of "'.get_class($result).'" and not "'.$class.'"');
		$result->n == 1 			|| $this->error('the result is "'.$result->n.'" and not "1"');
		$result->param == $param	|| $this->error('the result param is "'.$result->param.'" and not "'.$param.'"');
	}
	
	protected function test__fetch()
	{
		$class = 'Result';
		$param = 123;
		
		$result = $this->db->fetch($this->db->query($query = 'SELECT 1 as n UNION SELECT 2 as n '), $class, array($param));
		count($result) == 2 || $this->error('the result count is "'.count($result).'" and not "'. 2 .'"');
	}
	
	protected function test__select()
	{
		$correct = array
		(
			(object)array('id' => 1, 'test' => 123, 'active' => 1 ),
			(object)array('id' => 2, 'test' => 234, 'active' => 1 ),
			(object)array('id' => 3, 'test' => 345, 'active' => 0 ),
			(object)array('id' => 4, 'test' => 456, 'active' => 1 ),
			(object)array('id' => 5, 'test' => 567, 'active' => 0 ),
		);
		$result = $this->db->select($this->table_name);
		$correct == $result || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
		
		$correct = array
		(
			(object)array('test' => 123, 'active' => 1 ),
			(object)array('test' => 234, 'active' => 1 ),
			(object)array('test' => 345, 'active' => 0 ),
			(object)array('test' => 456, 'active' => 1 ),
			(object)array('test' => 567, 'active' => 0 ),
		);
		$result = $this->db->select($this->table_name, 'test, active');
		$correct == $result || $this->error('Result (selecting columns) is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
		
		$correct = array
		(
			(object)array('id' => 1, 'test' => 123, 'active' => 1 ),
			(object)array('id' => 2, 'test' => 234, 'active' => 1 ),
			(object)array('id' => 4, 'test' => 456, 'active' => 1 ),
		);
		$result = $this->db->select($this->table_name, '*', 'active = 1');
		$correct == $result || $this->error('Result (using where) is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
		
		$correct = array
		(
			(object)array('id' => 5, 'test' => 567, 'active' => 0 ),
			(object)array('id' => 4, 'test' => 456, 'active' => 1 ),
		);
		$result = $this->db->select($this->table_name, '*', null, 'ORDER BY test DESC limit 2');
		$correct == $result || $this->error('Result (using additional) is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__insert()
	{
		$id = $this->db->insert($this->table_name, array('test' => 1234, 'active' => 1));
		$result = $this->db->select($this->table_name, 'test, active', 'id = '.$id);
		
		$correct = array( (object)array('test' => 1234, 'active' => 1 ) );
		$correct == $result || $this->error('Result (insert) is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
		
		$this->db->query("DELETE FROM {$this->table_name} WHERE id = $id");
	}
	
	protected function test__update()
	{
		$id = $this->db->insert($this->table_name, array('test' => 1234, 'active' => 1));
		
		$this->db->update($this->table_name, array('test' => 2345, 'active' => 0), 'id = '.$id);
		$result = $this->db->select($this->table_name, 'test, active', 'id = '.$id);
		
		$correct = array( (object)array('test' => 2345, 'active' => 0 ) );
		$correct == $result || $this->error('Result (update) is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
		
		$this->db->update($this->table_name, array('test' => 3456, 'active' => 1), $id);
		$result = $this->db->select($this->table_name, 'test, active', 'id = '.$id);
		
		$correct = array( (object)array('test' => 3456, 'active' => 1 ) );
		$correct == $result || $this->error('Result (update id only) is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
		
		$this->db->query("DELETE FROM {$this->table_name} WHERE id = $id");
	}
	
	protected function test__delete()
	{
		$id = $this->db->insert($this->table_name, array('test' => 1234, 'active' => 1));
		
		$deleted = $this->db->delete($this->table_name, 'id = '.$id);
		$result = $this->db->select($this->table_name, 'test, active', 'id = '.$id);
		
		(is_array($result) && count($result) != 0) && $this->error("Record with id = $id was not deleted");
		(is_array($result) && count($result) != 0 && $deleted != 0) && $this->error("Record with id = $id was not deleted and return value is not 0");
		(is_array($result) && count($result) == 0 && $deleted != 1) && $this->error('Record deleted but the return value is not 1');
	}
	
	protected function test__last_id()
	{
		$id = $this->db->insert($this->table_name, array('test' => 1234, 'active' => 1));
		
		$last_id = $this->db->last_id();
		$id == $last_id || $this->error("Last id is not correct $last_id should be $id");
		
		$this->db->query("DELETE FROM {$this->table_name} WHERE id = $id");
	}
	
	protected function test__affected_rows()
	{
		
		$id = $this->db->insert($this->table_name, array('test' => 1234, 'active' => 1));
		
		$affected_rows = $this->db->affected_rows();
		$affected_rows == 1 || $this->error("Affected rows are not correct $affected_rows should be 1");
		
		$this->db->query("DELETE FROM {$this->table_name} WHERE id = $id");
	}
	
	protected function test__total_rows()
	{
		$result = $this->db->select($this->table_name);
		$total_rows = $this->db->total_rows();
		$total_rows == 5 || $this->error("Total rows are not correct $total_rows should be 5");
		
		$result = $this->db->select($this->table_name, '*', null, 'LIMIT 3');
		$total_rows = $this->db->total_rows();
		$total_rows == 5 || $this->error("Total rows (using limit) are not correct $total_rows should be 5");
	}
	
	protected function test__table_exists()
	{
		$result = $this->db->table_exists( $this->table_name );
		$result == 1 || $this->error("Table exists but the returned result is $result and not 1");
		
		$result = $this->db->table_exists( 'unexisting_table' );
		$result == 0 || $this->error("Table does not exists but the returned result is $result and not 0");
	}
	
	protected function test__table_info()
	{
		$correct = array
		(
			'id' 		=> array('name' => 'id', 		'type' => 'int(10) unsigned', 		'real_type' => 'int', 		'max_length' => '10', 	'scale' => -1, 'null' => false, 'key' => 'PRI', 'default' => NULL, 'has_default' => false, 'extra' => 'auto_increment', 'primary_key' => true, 	'unique' => true, 	'auto_increment' => true, 	'binary' => false, 'unsigned' => true, 	'zerofill' => false), 
			'test' 		=> array('name' => 'test', 		'type' => 'varchar(255)', 			'real_type' => 'varchar', 	'max_length' => '255', 	'scale' => -1, 'null' => false, 'key' => '', 	'default' => NULL, 'has_default' => false, 'extra' => '', 				'primary_key' => false, 'unique' => false, 	'auto_increment' => false, 	'binary' => false, 'unsigned' => false, 'zerofill' => false),
			'active' 	=> array('name' => 'active', 	'type' => 'tinyint(1) unsigned', 	'real_type' => 'tinyint', 	'max_length' => '1', 	'scale' => -1, 'null' => true, 	'key' => '', 	'default' => NULL, 'has_default' => false, 'extra' => '', 				'primary_key' => false, 'unique' => false,	'auto_increment' => false, 	'binary' => false, 'unsigned' => true, 	'zerofill' => false)
		);
		
		$result = $this->db->table_info( $this->table_name );
		$correct === $result || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
}

?>