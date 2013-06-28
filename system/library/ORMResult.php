<?php

class ORMResult
{
	
	public function __construct(array $params = array())
	{
		$this->table_name = $params['table_name'];
		$this->model_name = $params['model_name'];
	}
	
	/**
	 * -----------------------------
	 * ======= Params Access =======
	 * -----------------------------
	 */
	protected $table_name	= '';
	protected $model_name	= '';
	
	public function table_name(){ return $this->table_name; }
	public function model_name(){ return $this->model_name; }
	public function after_init(){ }
}

?>
