<?php

namespace library\database\FwurksORM\QueryBuilders\Mysql\Formatters;

abstract class Formatter
{
	protected $query;
	
	public function __construct($query)
	{
		$this->query = $query;
	}
	
	abstract public function get();
}


?>