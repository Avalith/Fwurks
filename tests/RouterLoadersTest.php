<?php

require_once 'helpers.php';
use library\Router;

class RouterLoadersTest extends PHPUnit_Framework_TestCase
{
	public function test_load_url()
	{
		$_GET['route'] = '/test/this/fancy-url/';
		Router::load_url();
		self::assertEquals(['test', 'this', 'fancy-url'], Router::$url);
		
		self::assertFalse(isset($_GET['route']));
	}
	
	public function test_load_url_default()
	{
		$_GET['route'] = '';
		Router::load_url();
		self::assertEquals([], Router::$url);
		
		self::assertFalse(isset($_GET['route']));
	}
	
	public function test_load_url_without_GET()
	{
		Router::load_url();
		self::assertEquals([], Router::$url);
	}
	
	
	public function test_atom_default()
	{
		Router::load_atom();
		self::assertEquals('public', Router::$atom_current);
		self::assertEquals(['admin', 'public'], Router::$atom_all);
	}
	
	public function test_atom_from_url()
	{
		Router::$url = ['admin'];
		Router::load_atom();
		self::assertEquals('admin', Router::$atom_current);
		self::assertEquals(['admin', 'public'], Router::$atom_all);
	}
	
	public function test_configs()
	{
		Router::load_atom();
		Router::load_configs();
		
		self::assertTrue(class_exists('Atom_Config'));
		self::assertTrue(class_exists('Routes_Config'));
	}
	
	public function test_locales_default()
	{
		Router::load_atom();
		Router::load_configs();
		Router::load_locales();
		self::assertEquals('en', Router::$locale_current);
		self::assertEquals(['bg', 'en'], Router::$locale_all);
	}
	
	public function test_locales_from_url()
	{
		Router::$url = ['en'];
		Router::load_atom();
		Router::load_locales();
		self::assertEquals('en', Router::$locale_current);
		self::assertEquals(['bg', 'en'], Router::$locale_all);
	}
	
	public function test_routes()
	{
		Router::load();
		self::assertGreaterThan(0, count(Router::$routes));
	}
}

?>