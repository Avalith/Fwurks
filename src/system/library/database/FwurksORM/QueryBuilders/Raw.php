<?php

namespace library\database\FwurksORM\QueryBuilders;

class Raw
{
	protected $value;
	
	public function __construct($value)
	{
		$this->value = $value;
	}
}


?>