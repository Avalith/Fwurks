<?php

require_once 'helpers.php';
use library\Router;

class RouterLoadersTest extends PHPUnit_Framework_TestCase
{
	public function test_url()
	{
		$_GET['route'] = '';
		Router::load_url();
		self::assertEquals([], Router::$url);
		
		$_GET['route'] = '/url/';
		Router::load_url();
		self::assertEquals(['url'], Router::$url);
		
		
		self::assertFalse(isset($_GET['route']));
	}
	
	public function test_url_without_GET()
	{
		Router::load_url();
		self::assertCount(0, Router::$url);
	}
	
	
	public function test_atom()
	{
		
	}
	
	public function test_locales()
	{
		
	}
	
	public function test_configs()
	{
		
	}
	
	public function test_routes()
	{
		
	}
}

?>