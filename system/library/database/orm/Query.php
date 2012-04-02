<?php

require_once __DIR__ . '/DocBlockParser.php';

class Query
{
	public function __construct()
	{
		
	}
	
	public function find(array $criteria){} // [ id => 5 ]
	public function create(){}
	public function update(){}
	public function delete(){}
	
	
	public function select(){}
	public function order(){}
	public function limit(){}
	public function group(){}
	
	
	public function count(){}
	
	public function filter(){}
	
	/*
		[
			'FIELD' => VALUE,
			'FIELD' => [ 'METHOD' => VALUE, ... ],
			...
		]
		
		
		METHODS:
		=, !=, <, >, <=, >=, %, IN, !IN
		
		
		$all, $exists
	*/
	
	
	
}


?>
