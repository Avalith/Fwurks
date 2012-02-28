<?php

require_once __DIR__.'/ORM.php';

abstract class ActiveRecord extends ORM
{
	/**
	 * Determines if this record is a new one thus choosing insert or update
	 * 
	 * @var bool
	 * @access private
	 */
	protected $new_record 						= true;
	
	/**
	 * Stores all the data
	 * 
	 * @var ActiveRecordResult
	 */
	protected $storage;
	
	/**
	 * Clone of the storage before updating attributes
	 * 
	 * @var ActiveRecordResult
	 */
	protected $old_storage;
	
	/**
	 * Error stack, mainly filled after the validation process
	 * 
	 * @var array
	 * @access public
	 */
	protected $errors							= array();
	
	/**
	 * Primary keys' values stored on load
	 * 
	 * @var array
	 * @access private
	 */
	private $stored_primary_keys				= array();
	
	protected $dont_validate_fields				= array();
	
	final public function __get($variable)
	{
		return isset($this->storage->$variable) ? $this->storage->$variable : null;
	}
	
	final public function __set($variable, $value)
	{
		return isset($this->storage->$variable) ? ($this->storage->$variable = $value) : null;
	}
	
	final public function __isset($variable)
	{
		return isset($this->storage->$variable);
	}
	
	
//	public function __call($method, $arguments)
//	{
//		$relations = static::relations();
//		if(isset($relations->$method))
//		{
//			return $this->storage->$method();
//		}
//	}
	
	
	final public function __construct($primary_keys = null)
	{
		(is_array($primary_keys) || $primary_keys === null) || $primary_keys = func_get_args();
		$primary_keys ? $this->load($primary_keys) : $this->storage = static::new_result();
		$this->__init();
	}
	
	
	protected function __init()
	{
		
	}
	
	
	public static function get($primary_keys)
	{
		is_array($primary_keys) || $primary_keys = func_get_args();
		return new static($primary_keys);
	}
	
	
	final public function load($primary_keys)
	{
		is_array($primary_keys) || $primary_keys = func_get_args();
		
		$conditions = $this->do_primary_keys($primary_keys);
		
		$this->before_load($conditions);
		
		$result = static::find($conditions, null, null, true, static::$has_mirror)->result();
		
		if(count($result))
		{
			$this->new_record = false;
			
			if(static::$has_i18n && static::$has_mirror)
			{
				foreach(static::columns(self::COLUMNS_TYPE_TABLE__ONLY_NAMES) as $c)
				{
					isset($result[0]->$c) && $this->storage->$c = $result[0]->$c;
				}
				
				$i18n_columns = static::columns(self::COLUMNS_TYPE_I18N__ONLY_NAMES);
				$this->storage->locales_storage = new StdClass();
				foreach($result as $r)
				{
					$locale = $r->{static::$i18n_locale_field};
					$this->storage->locales_storage->$locale = new StdClass();
					foreach($i18n_columns as $c)
					{
						isset($r->$c) && $this->storage->locales_storage->$locale->$c = $r->$c;
					}
				}
			}
			else
			{
				$this->storage = $result[0];
			}
		}
		else
		{
			$this->new_record = true;
			$this->storage = static::new_result();
//			$this->stored_primary_keys = array();
		}
		
		$this->after_load($conditions);
		
		return $this->storage;
	}
	
	
	final public function reload()
	{
		$this->stored_primary_keys && $this->load(array_values($this->stored_primary_keys));
		return $this->storage;
	}
	
	
	final public function save($attributes = null, $validate = true)
	{
		$attributes && $this->update_storage($attributes);
		if(!$validate || $this->valid()){ return $this->create_or_update_record(); }
		
		return false;
	}
	
	
	final private function update_storage($attributes)
	{
		$this->old_storage = clone $this->storage;
		
		$this->before_update_storage($attributes);
		
		foreach($attributes as $c => $val)
		{
			if(in_array($c, static::$primary_keys)){ continue; }
			$this->storage->$c = $val; 
		}
		foreach(static::columns() as $c)
		{
			$c->type() == 'boolean' && $this->storage->{$c->name()} = (int)(isset($attributes[$c->name()]) && $attributes[$c->name()]);
		}
		
		$this->after_update_storage($attributes);
	}
	
	
	final private function create_or_update_record()
	{
		$this->before_save();
		
		if($this->new_record)
		{
			$this->before_create();
			$this->create_record();
			$this->replace_locales();
			$this->after_create();
		}
		else
		{
			$this->before_update();
			$this->update_record();
			$this->replace_locales();
			$this->after_update();
		}
		
		$this->before_save_relations();
		$this->save_relations();
		$this->after_save_relations();
		
		$this->after_save();
		
		return $this->storage;
	}
	
	
	final private function save_relations()
	{
		foreach(static::relations() as $rel_name => $r)
		{
			$model = $r->rmodel();
			
			switch($r->type())
			{
				case 'has_one':
				{
					$id = $this->storage->{static::primary_keys(0)};
					$model = new $model($id);
					$model->set_primary_keys($id)->save($_POST);
				}
				break;
				
				case 'has_many':
				{
					$key 	= $r->key();
					$fkey 	= $r->fkey();
					$id 	= $this->storage->{$model::primary_keys(0)};
					
					$old_rels = array(); foreach($r->query($this->storage)->select('id')->result() as $rel){ $old_rels[] = $rel->id; }
					$new_rels = isset($this->storage->$rel_name) ? $this->storage->$rel_name : array();
					
					if($rmodel = $r->rmodel_through())
					{
						if(isset($this->storage->ManyToManyCounter[$rel_name]))
						{
							if($del_rels = array_diff($old_rels, $new_rels))
							{
								$rmodel::delete(array("$key = $id AND $fkey IN (?arr_i)", $del_rels))->run();
							}
							
							if($add_rels = array_diff($new_rels, $old_rels))
							{
								foreach($add_rels as &$r){ $r = array($key => $id, $fkey => $r); }
								$rmodel::query()->multi_insert(array($key, $fkey), $add_rels)->run();
							}
						}
					}
					else
					{
						de('has_many but NOT many to many');
					}
				}
				break;
			}
		}
	}
	
	
	final private function create_record()
	{
		$field_values = array();
		$cols = static::columns(self::COLUMNS_TYPE_TABLE);
		foreach($cols as $col => $c)
		{
			isset($this->storage->$col) && $field_values[$col.'?'.$c->getEscapeChar()] = $this->storage->$col;
		}
		
		static::insert($field_values)->run();
		
		$pk_name = static::$primary_keys[static::$primary_key_to_i18n_fk_field];
		$cols->$pk_name->is_auto_increment() && $this->storage->{$pk_name} = Registry::$db->last_id();
		
		$this->stored_primary_keys = array();
		foreach(static::$primary_keys as $pk){ $this->stored_primary_keys[] = $this->storage->$pk; }
	}
	
	
	final private function update_record()
	{
		$where = $this->do_primary_keys(array_values($this->stored_primary_keys));
		
		$field_values = array();
		foreach(static::columns(self::COLUMNS_TYPE_TABLE) as $col => $c)
		{
			if(in_array($col, static::$primary_keys)){ continue; }
			isset($this->storage->$col) && $field_values[$col.'?'.$c->getEscapeChar()] = $this->storage->$col;
		}
		static::update($field_values)->where($where)->run();
	}
	
	
	final private function replace_locales()
	{
		if(static::$has_i18n)
		{
			$i18n_field_values 	= array();
			
			foreach(static::columns(self::COLUMNS_TYPE_I18N) as $col => $c)
			{
				if(in_array($col, array(static::$i18n_fk_field, static::$i18n_locale_field))){ continue; }
				
				if(static::$has_mirror)
				{
					foreach(static::$i18n_locales as $l)
					{
						isset($this->storage->locales_storage[$l->i18n][$col]) && $i18n_field_values[$l->i18n][$col.'?'.$c->getEscapeChar()] = $this->storage->locales_storage[$l->i18n][$col];
					}
				}
				else
				{
					isset($this->storage->$col) && $i18n_field_values[static::$i18n_locale->i18n][$col.'?'.$c->getEscapeChar()] = $this->storage->$col;
				}
			}				
			
			foreach($i18n_field_values as $locale => &$fv)
			{
				$fv[static::$i18n_fk_field] = $this->storage->{static::$primary_keys[static::$primary_key_to_i18n_fk_field]};
				$fv[static::$i18n_locale_field] = $locale;
			}	
			
			static::query()->multi_replace(static::columns(self::COLUMNS_TYPE_I18N__ONLY_NAMES), $i18n_field_values)->table(static::i18n_table_name())->run();
		}
	}
	
	
	final public function destroy($primary_keys = null, $find = false)
	{
		is_array($primary_keys) || $primary_keys = func_get_args();
		$primary_keys || $primary_keys = array_values($this->stored_primary_keys);
		$find && $result = $this->load($primary_keys);
		
		$this->before_destroy($primary_keys);
		
		static::delete($this->do_primary_keys($primary_keys))->run();
		static::delete(array(static::$i18n_fk_field.' = ?', $this->stored_primary_keys[static::$primary_keys[static::$primary_key_to_i18n_fk_field]]))->table(static::i18n_table_name())->run();
		
		$this->after_destroy($primary_keys);
		
		$this->storage = static::new_result();
	}
	
	
	
	/**
	 * ------------------------------------
	 * ============ Validation ============
	 * ------------------------------------
	 */
	final protected function valid()
	{
		$this->errors = array();
		
		$this->before_validation();
		$this->new_record ? $this->before_validation_on_create() : $this->before_validation_on_update();
		$this->validate();
		$this->validate_fields();
		$this->after_validation();
		$this->new_record ? $this->after_validation_on_create() : $this->after_validation_on_update();
		
		return !$this->errors;
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @param object $attributes
	 * @return array errors
	 */
	final public function validate($attributes = null)
	{
		$storage = $attributes ?: $this->storage;
		
		$dont_validate = array_merge(static::$primary_keys, array(static::$i18n_fk_field, static::$i18n_locale_field));
		foreach(static::columns(self::COLUMNS_TYPE_ALL) AS $field_name => $field)
		{
			if($field->is_auto_increment() || !$field->canValidate() || in_array($field_name, $this->dont_validate_fields)){ continue; }
			
			if(static::$has_i18n && static::$has_mirror && in_array($field_name, static::columns(self::COLUMNS_TYPE_I18N__ONLY_NAMES)))
			{
				foreach(self::$i18n_locales as $l)
				{
					if(!isset($storage->locales_storage[$l->i18n][$field_name])){ continue; }
					$field_value = $storage->locales_storage[$l->i18n][$field_name];
					$field_value_confirm = isset($storage->locales_storage[$l->i18n][$field_name.'_confirm']) ? $storage->locales_storage[$l->i18n][$field_name.'_confirm'] : '';
					
					$this->validate_actions($field, $field_value, $field_value_confirm, $l->code);
					$storage->locales_storage[$l->i18n][$field_name] = $field->escapeHTML($storage->locales_storage[$l->i18n][$field_name]);
				}
			}
			else
			{
				$field_value = isset($storage->$field_name) ? $storage->$field_name : null;
				$field_value_confirm = isset($storage->{$field_name.'_confirm'}) ? $storage->{$field_name.'_confirm'} : '';
				
				$this->validate_actions($field, $field_value, $field_value_confirm);
				$storage->$field_name = isset($storage->$field_name) ? $field->escapeHTML($storage->$field_name) : null;
			}
			
//			$field->is_unique();
//			$result = static::find()->select($field_name)->where($field_name.' = ?'.$field->getEscapeChar(), $storage->$field_name)->result();
//			$result && $this->add_error('already_exists', $index->name, $lang);
		}
		
		return !$this->errors;
	}
	
	
	final private function validate_actions($field, $field_value, $field_value_confirm = null, $lang = null)
	{
		if($field->is_requiered() && !strlen(trim($field_value)))
		{
			$this->add_error('cant_be_empty', $field->name(), $lang);
		}
		else if(is_int($field->get_length()) && $field->get_length() > 0 && strlen($field_value) > $field->get_length())
		{
			$this->add_error('too_long', $field->name(), $lang);
		}
		else if($field_value_confirm && $field_value !== $field_value_confirm)
		{
			$this->add_error('not_match', $field->name(), $lang);
		}
	}
	
	
	final private function validate_fields()
	{
		$columns = static::columns(self::COLUMNS_TYPE_ALL__ONLY_NAMES);
		
		$reflection = new ReflectionObject($this);
		foreach($reflection->getMethods(ReflectionMethod::IS_PROTECTED) as $method)
		{
			$method = $method->getName();
			if(substr($method, 0, 10) == 'validate__' && in_array(substr($method, 10), $columns)){ $this->$method(); }
		}
	}
	
	
	/**
	 * Adds error to the error stack
	 * 
	 * @param String $type
	 * @param String $field
	 * @param String $lang
	 * @param Array $replacer
	 */
	final protected function add_error($type, $field, $lang = null, array $replacer = array())
	{
		$field_name = $lang ? "locales_storage[$field][$lang]" : $field;
		
		$message 	= isset(Registry::$globals->DATABASE_ERRORS[$type]) 	? Registry::$globals->DATABASE_ERRORS[$type] : Registry::$globals->DATABASE_ERRORS['unknown_error'].' ('.$type.')';
		$label 		= isset(Registry::$globals->DATABASE_FIELDS[$field])	? Registry::$globals->DATABASE_FIELDS[$field] : $field;
		
		$lang && $lang = Registry::$locales->info[$lang];
		$replacer && $message = strtr($message, $replacer);
		
		$this->errors[$field_name] = (object)array
		(
			'field' 		=> $field,
			'field_name'	=> $field_name,
			'label' 		=> $label,
			'error' 		=> $type,
			'message' 		=> $message,
			'language' 		=> $lang,
		);
	}
	
	
	
	/**
	 * ------------------------------------
	 * ============== Events ==============
	 * ------------------------------------
	 */
	
	protected function before_load(){}
	
	protected function after_load(){}
	
	
	protected function before_update_storage(&$attributes){}
	
	protected function after_update_storage(&$attributes){}
	
	
	protected function before_validation(){}
	
	protected function before_validation_on_create() {}
	
	protected function before_validation_on_update() {}
	
	// validate()
	// validate_fields()
	
	protected function after_validation(){}
	
	protected function after_validation_on_create(){}
	
	protected function after_validation_on_update(){}
	
	
	protected function before_save(){}
	
	protected function after_save(){}
	
	
	protected function before_save_relations(){}
	
	protected function after_save_relations(){}
	
	
	protected function before_create(){}
	
	protected function after_create(){}
	
	
	protected function before_update(){}
	
	protected function after_update(){}
		
	
	protected function before_destroy($primary_keys){}
	
	protected function after_destroy($primary_keys){}
	
	
	/**
	 * ------------------------------------
	 * ============== Methods =============
	 * ------------------------------------
	 */
	
	final protected function do_primary_keys($primary_keys)
	{
		$columns = static::columns();
		
		$where = array();
		$this->stored_primary_keys = array();
		
		foreach(static::$primary_keys as $i => $k)
		{
			$where[0][] = $k.' = ?'.$columns->$k->getEscapeChar();
			if(isset($primary_keys[$i]))
			{
				$where[] = $this->stored_primary_keys[$k] =	$primary_keys[$i];
			}
			else
			{
				throw new Exception("Undefined primary key '$k'");
			}
		}
		$where[0] = implode(' AND ', $where[0]);
		
		return $where;
	}
	
	
	final public function set_primary_keys($primary_keys)
	{
		is_array($primary_keys) || $primary_keys = func_get_args();
		
		foreach(static::$primary_keys as $i => $k)
		{
			if(isset($primary_keys[$i]))
			{
				$this->storage->$k = $primary_keys[$i];
			}
			else
			{
				throw new Exception("Undefined primary key '$k'");
			}
		}
		
		return $this;
	}
	
	
	/**
	 * Return current record data
	 * 
	 * @access public
	 * @return ActiveRecordClass
	 */
	final public function __invoke(){ return $this->storage; }
	
	
	/**
	 * Return current record data
	 * 
	 * @access public
	 * @return ActiveRecordClass
	 */
	final public function storage(){ return $this->storage; }
	
	
	/**
	 * Return current record data
	 * 
	 * @access public
	 * @return ActiveRecordClass
	 */
	final public function old_storage(){ return $this->old_storage; }
	
	
	final public function errors(){ return $this->errors; }
}

?>
