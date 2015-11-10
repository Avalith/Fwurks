<?php

namespace library\database\FwurksORM\QueryBuilders\Mysql\Formatters;

use library\database\FwurksORM\QueryBuilders\Mysql\Formatters;
use library\database\FwurksORM\QueryBuilders\Raw;

class Select extends Formatter
{
	public function get()
	{
		$query = ['SELECT'];
		
		$query[] = $this->build_select();
		
		if($this->query->from){ $query[] = $this->build_from(); }
		if($this->query->where){ $query[] = $this->build_where(); }
		
		d($query);
		// de($this->query);
		
		return implode(' ', $query);
	}
	
	
	protected function alias($expr, $alias = null)
	{
		if(is_array($expr))
		{
			foreach($expr as $alias => $expr)
			{
				return $this->alias($expr, $alias);
			}
		}
		
		return is_string($alias) ? "$expr AS `$alias`" : $expr;
	}
	
	protected function condition($condition)
	{
		return $condition;
	}
	
	protected function build_select()
	{
		$select = [];
		
		foreach($this->query->select as $alias => $expr)
		{
			$expr = ($expr instanceof Raw) ? $expr->value : "`$expr`";
			$select[] = $this->alias($expr, $alias);
		}
		
		if(!$select){ return '*'; }
		
		return implode(', ', $select);
	}
	
	protected function build_from()
	{
		return $this->alias($this->query->from);
	}
	
	protected function build_where()
	{
		return 'WHERE ' . $this->condition($this->query->where);
	}
}


?>