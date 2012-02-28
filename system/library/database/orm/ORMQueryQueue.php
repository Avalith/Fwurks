<?php

class ORMQueryQueue implements Countable
{
	protected $queue = array();
	
	/**
	 * @param SqlBase $query
	 * @return ORMQueryQueue
	 */
	public function add(ORMQuery $query, $key = null)
	{
		$this->queue[$key ?: $this->count()] = $query;
		
		return $this;
	}
	
	/**
	 * @param Integer $index
	 * @return ORMQuery
	 */
	public function get($index)
	{
		return isset($this->queue[$index]) ? $this->queue[$index] : null;
	}
	
	/**
	 * @param Integer $index
	 * @return ORMQueryQueue
	 */
	public function remove($index)
	{
		if(isset($this->queue[$index])){ unset($this->queue[$index]); };
		return $this;
	}
	
	/**
	 * @param Integer $index
	 * @return ORMQueryQueue
	 */
	public function run($index)
	{
		isset($this->queue[$index]) && $this->queue[$index]->run();
		return $this;
	}
	
	/**
	 * @param Integer $index
	 * @return ORMResultSet
	 */
	public function result($index)
	{
		return isset($this->queue[$index]) ? $this->queue[$index]->result() : null;
	}
	
	/**
	 * @return ORMQueryQueue
	 */
	public function runAll()
	{
		foreach($this->queue as $query){ $query->run(); }
		return $this;
	}
	
	/**
	 * @return Array
	 */
	public function resultAll()
	{
		$results = array();
		foreach($this->queue as $key => $query){ $results[$key] = $query->result(); }
		return $results;
	}
	
	
	/**
	 * ------------------------------------
	 * ========= Countable Methods ========
	 * ------------------------------------
	 */
	public function count(){ return count($this->queue); }
	
}

function ORMQueryQueue()
{
	return new ORMQueryQueue();
}

?>
