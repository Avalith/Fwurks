<?php

final class Dispatcher
{
	public static $public = 'public';
	public static $system = 'system';
	public static $application = 'application';
	public static $admin_application 	= null;
}

function dbd($state = 1, $db = 'db'){ Registry::$db->debug = $state; }

function qstr($string)
{
	return Registry::$db->qstr($string);
}


require_once __DIR__.'/../../system/library/Registry.php';
require_once __DIR__.'/../../system/library/Inflector.php';
require_once __DIR__.'/../../system/library/Localizer.php';
require_once __DIR__.'/../../system/configs/system.config.php';
require_once __DIR__.'/../../system/configs/database.config.php';
require_once __DIR__.'/../../system/application/configs/application.config.php';
require_once __DIR__.'/../../system/library/DatabaseFactory.php';

Registry::$locales->info = $locales_info = Localizer::getLocales();
Registry::$locales->current = $locales_info[$locale];

Registry::$db = DatabaseFactory::create('MySQLi');

require_once __DIR__.'/../../system/library/ActiveRecord.php';

class Test extends ActiveRecord
{
//	protected static $has_i18n = true;
//
//	protected static $save_cache = true;
//	protected static $cache_expire = .6;
	
}

class ActiveRecord_TestSuite extends TestSuite 
{
	private $table_name = 'tests';
	private $model_name = 'Test';
	private $model;
	
	protected function test__Table_name()
	{
		Test::table_name() == $this->table_name || $this->error("Incorrect table name, must be '{$this->table_name}' and not '".Test::table_name().'\'');
	}
	
	
	protected function test__Find_all_simple()
	{
		
		$result = Test::find_all();
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('id' => '1', 'test' => '123', 'active' => '1' ),
				(object)array('id' => '2', 'test' => '234', 'active' => '1' ),
				(object)array('id' => '3', 'test' => '345', 'active' => '0' ),
				(object)array('id' => '4', 'test' => '456', 'active' => '1' ),
				(object)array('id' => '5', 'test' => '567', 'active' => '0' ),
			),
			'total' => '5',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_where()
	{
		$result = Test::find_all('active = 1');
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('id' => '1', 'test' => '123', 'active' => '1' ),
				(object)array('id' => '2', 'test' => '234', 'active' => '1' ),
				(object)array('id' => '4', 'test' => '456', 'active' => '1' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_by_active_1()
	{
		$result = Test::find_all_by_active(1);
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('id' => '1', 'test' => '123', 'active' => '1' ),
				(object)array('id' => '2', 'test' => '234', 'active' => '1' ),
				(object)array('id' => '4', 'test' => '456', 'active' => '1' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_by_active_1_and_order()
	{
		$result = Test::find_all_by_active(1, 'id DESC');
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('id' => '4', 'test' => '456', 'active' => '1' ),
				(object)array('id' => '2', 'test' => '234', 'active' => '1' ),
				(object)array('id' => '1', 'test' => '123', 'active' => '1' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_by_active_1_and_limit()
	{
		$result = Test::find_all_by_active(1, null, '2');
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('id' => '1', 'test' => '123', 'active' => '1' ),
				(object)array('id' => '2', 'test' => '234', 'active' => '1' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_by_active_1_and_append()
	{
		$result = Test::find_all_by_active(1, null, '2', null, '123 as "asd"', 'append');
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('id' => '1', 'test' => '123', 'active' => '1', 'asd' => '123' ),
				(object)array('id' => '2', 'test' => '234', 'active' => '1', 'asd' => '123' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_by_active_1_and_only()
	{
		$result = Test::find_all_by_active(1, null, '2', null, '123 as "asd"');
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('asd' => '123' ),
				(object)array('asd' => '123' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_by_active_1_and_order_and_limit()
	{
		$result = Test::find_all_by_active(1, 'id DESC', 2);
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('id' => '4', 'test' => '456', 'active' => '1' ),
				(object)array('id' => '2', 'test' => '234', 'active' => '1' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_by_active_1_and_order_and_append()
	{
		$result = Test::find_all_by_active(1, 'id DESC', null, null, '123 as "asd"', 'append');
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('id' => '4', 'test' => '456', 'active' => '1', 'asd' => '123' ),
				(object)array('id' => '2', 'test' => '234', 'active' => '1', 'asd' => '123' ),
				(object)array('id' => '1', 'test' => '123', 'active' => '1', 'asd' => '123' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_by_active_1_and_limit_and_append()
	{
		$result = Test::find_all_by_active(1, null, 2, null, '123 as "asd"', 'append');
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('id' => '1', 'test' => '123', 'active' => '1', 'asd' => '123' ),
				(object)array('id' => '2', 'test' => '234', 'active' => '1', 'asd' => '123' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_by_active_1_and_order_and_only()
	{
		$result = Test::find_all_by_active(1, 'id DESC', null, null, '123 as asd');
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('asd' => '123' ),
				(object)array('asd' => '123' ),
				(object)array('asd' => '123' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_by_active_1_and_limit_and_only()
	{
		$result = Test::find_all_by_active(1, null, 2, null, '123 as asd');
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('asd' => '123' ),
				(object)array('asd' => '123' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_by_active_1_and_order_and_limit_and_append()
	{
		$result = Test::find_all_by_active(1, 'id DESC', 2, null, '123 as asd', 'append');
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('id' => '4', 'test' => '456', 'active' => '1', 'asd' => '123' ),
				(object)array('id' => '2', 'test' => '234', 'active' => '1', 'asd' => '123' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	protected function test__Find_all_by_active_1_and_order_and_limit_and_only()
	{
		$result = Test::find_all_by_active(1, 'id DESC', 2, null, '123 as asd');
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('asd' => '123' ),
				(object)array('asd' => '123' ),
			),
			'total' => '3',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	
	protected function test__Find_all_order()
	{
		$result = Test::find_all(null, 'active ASC');
		
		$correct = (object)array
		(
			'results' => array
			(
				(object)array('id' => '3', 'test' => '345', 'active' => '0' ),
				(object)array('id' => '5', 'test' => '567', 'active' => '0' ),
				(object)array('id' => '1', 'test' => '123', 'active' => '1' ),
				(object)array('id' => '2', 'test' => '234', 'active' => '1' ),
				(object)array('id' => '4', 'test' => '456', 'active' => '1' ),
			),
			'total' => '5',
		);
		
		json_encode($result) == json_encode($correct) || $this->error('Result is not correct <pre>'.print_r($result, 1).'</pre> <strong>should be</strong> <pre>'.print_r($correct, 1).'</pre>');
	}
	
	
}

?>

