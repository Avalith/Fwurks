<?php

class ActiveRecordResult
{
	public $total_results;
	
	private $cache;
	
	protected $belongs_to;
	protected $has_one;
	protected $has_many;
	protected $has_many_through;
	
	private $model_name;
	private $primary_key;
	
	private $relations = array
	(
		'holder'			=> 'belongs_to',
		'belonging'			=> 'has_one',
		'belongings'		=> 'has_many',
		'through'			=> 'has_many_through'
	);
	
	public $i18n_locales_storage;
	
	public function __construct($params)
	{
		$this->total_results = Registry()->db->total_rows();
		
		foreach($params as $k => $v)
		{
			if(!$this->$k){ $this->$k = $v; }
		}
	}
	
	public function __call($method, $params = array())
	{
		if( array_key_exists($method, $this->relations) )
		{	
			return call_user_func_array(array($this, 'get_related_object'), array_merge(array($this->relations[$method]), $params));
		}
	}
	
	final private function get_related_object($relation, $name = null, $params = null)
	{
		$this->cache = array();
		
		$name = $name ? $name : $this->{$relation}[0];
		
		if($name && in_array($name, $this->$relation))
		{
			if(!$object = $this->cache[Inflector()->pluralize($name)][$name])
			{
				$class = Inflector()->classify(Inflector()->singularize($name));
				$object = $this->cache[Inflector()->pluralize($name)][$name] = new $class();
			}
			
			switch($relation)
			{
				case 'belongs_to':
					$id = $this->{Inflector()->singularize($name).'_'.$object->primary_key};
					if($params){ $id = Inflector()->singularize($name).'_'.$object->primary_key.'='.$id.' AND '.$params; }
					
					if(!($this->caching && $result = $this->cache['_results'][$id]))
					{
						$object->load($id, 'asdasd');
						$result = $this->cache['_results'][$id] = $object;
					}
				break;
				
				case 'has_one':
					$id = $this->model_name.'_'.$this->primary_key.'='.$this->{$this->primary_key};
					if($params){ $id .= ' AND '.$params; }
					
					if(!($this->caching && $result = $this->cache['_results'][$id]))
					{
						$object->load($id);
						$result = $this->cache['_results'][$id] = $object;
					}
				break;
				
				case 'has_many':
					$cache_key = $this->model_name.'_'.$this->primary_key.'='.$this->{$this->primary_key}.' AND '.$params;
					if(!($this->caching && $result = $this->cache['_results'][$cache_key]))
					{
						if($params && !is_array($params)){ $params = array('conditions' => $params); }
						$result = $this->cache['_results'][$cache_key] = $object->{'find_all_by_'.$this->model_name.'_'.$this->primary_key}($this->{$this->primary_key}, $params);
					}
				break;
				
				case 'has_many_through':
					// TODO @Karamfil: has_many_through
				break;
			}
			
			return $result;
		}
	}
	
	public function clearCache()
	{
		$this->cache = array();
	}
	
	public function turn_cache($on_off = 'on')
	{
		$this->caching = ($on_off == 'on');
	}
	
	public function __clone()
	{
		if($this->i18n_locales_storage && is_object($this->i18n_locales_storage))
		{
			$this->i18n_locales_storage = clone $this->i18n_locales_storage;
		}   
	}
	
}

?>
