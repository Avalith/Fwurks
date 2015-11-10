<?php

namespace library\database\FwurksORM\QueryBuilders;

class Mysql extends QueryBuilder
{
	// public function __construct( result_class ){}
	public function __clone(){}
	
	public function __toString(){}
	public function sql(){}
	public function get(){}
	
	// public function table(table|model){}
	public function force_index(){}
	public function explain(){}
	
	public function select(){}
	public function insert(){}
	public function update(){}
	public function replace(){}
	public function delete(){}
	public function union(){}
	
	public function where(){}
	public function where_raw(){}
	// public function order(‘q’, ‘-w’){}
	// public function group(‘q’, ‘w’) ??? WITH ROLLUP {}
	// public function having(‘c > 1’) ??? SQL Injection{}
	public function limit($limit, $offset = 0){}
	public function offset($offset){}
	
	// public function join(‘table|Model’, ‘a = b’){}
	// public function join([table|Model’, ‘tn’], ‘inner’, [‘a = b AND c = ?’, 1]){}
	
	public function only(){}
	public function defer(){}
	public function distinct(){}
	public function calc_rows(){}
	public function exist(){}
	
	public function min(){}
	public function max(){}
	// public function count( distinct ){}
	public function avg(){}
	public function sum(){}
	public function concat(){}
}


?>