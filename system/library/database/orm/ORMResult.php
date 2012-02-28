<?php

class ORMResult
{
	protected $table_name	= null;
	protected $model_name	= null;
	
	protected $relations	= null;
	
	public function __construct(array $params = array())
	{
		if(isset($params['table_name']))
		{
			$this->table_name 	= $params['table_name'];
			$this->model_name 	= $params['model_name'];
			$this->relations	= $params['relations'];
		}
	}
	
	
	
	/**
	 * -----------------------------
	 * ======= Params Access =======
	 * -----------------------------
	 */
	
	public function table_name(){ return $this->table_name; }
	
	public function model_name(){ return $this->model_name; }

	
	
	/**
	 * -----------------------------
	 * ========= Relations =========
	 * -----------------------------
	 */
	
	public function __call($method, $arguments)
	{
		if(isset($this->relations->$method))
		{
			return $this->relation_get($method);
		}
		
		throw new Exception("Unknown method called: $method()");
	}
	
	protected function relation_get($model)
	{
		if(!isset($this->$model))
		{
			$query = $this->relations->$model->query($this);
			if($query instanceof ORMQuery)
			{
				$result = $query->result();
				(in_array($this->relations->$model->type(), array('belongs_to', 'has_one')) && isset($result[0])) && $result = $result[0];
			}
			
			$this->$model = $result;
		}
		
		return $this->$model;
	}
}

?>
