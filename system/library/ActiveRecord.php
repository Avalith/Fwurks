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
	 * Fields not to be validated or skip the whole validation process if false.
	 * 
	 * @var array|true
	 * @access protected
	 * 
	 * @example array('field');
	 */
	protected $dont_validate_fields				= array();
	
	/**
	 * Fields not to be htmlspecialchar-ed or skip the whole escaping if false.
	 * 
	 * @var array|true
	 * @access protected
	 * 
	 * @example array('field');
	 */
	protected $dont_escape_fields				= array();
	
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
	public $errors								= array();
	
	/**
	 * Primary keys' values stored on load
	 * 
	 * @var array
	 * @access private
	 */
	private $stored_primary_keys				= array();
	
	
	protected $load_all_locales					= false;
	
	
	// Many to many 
	protected $many_to_many						= array();
	public $auto_many_to_many					= true;
	
	
	public function __construct($primary_key = null)
	{
		$primary_key ? call_user_func_array(array($this, 'load'), func_get_args()) : $this->storage = new static::$result_class();
		$this->init();
	}
	
	protected function init()
	{
		
	}
	
	final public function __get($variable)
	{
		if(in_array($variable, static::columns(self::COLUMNS_TYPE_ALL__ONLY_NAMES)) || isset($this->storage->$variable)){ return $this->storage->$variable; }
		return $this->$variable;
	}
	
	/*
	final public function __set($variable, $value)
	{
		if(array_key_exists($variable, static::columns(self::COLUMNS_TYPE_ALL__ONLY_NAMES))){ return $this->storage->$variable = $value; }
		return $this->$variable = $value;
	}
	*/
	
	final public function load($primary_keys)
	{
		$this->before_load();
		
		is_array($primary_keys) || $primary_keys = func_get_args();
		if($this->storage = static::find($primary_keys))
		{
			$this->new_record = false;
			$this->stored_primary_keys = array();
			foreach(static::$primary_keys as $pk){ $this->stored_primary_keys[] = $this->storage->$pk; }
			
			if(static::$has_mirror && (Registry::$is_admin || $this->load_all_locales))
			{
				$current_locale = static::$i18n_locale;
				foreach(static::$i18n_locales as $lcode => $l)
				{
					static::$i18n_locale = $l;
					$t_storage = $l == $current_locale ? $this->storage : call_user_func_array(array($this, 'find'), $this->stored_primary_keys);
					
					foreach(static::columns(self::COLUMNS_TYPE_I18N__ONLY_NAMES) as $c)
					{
						if(!in_array($c, array(static::$i18n_fk_field, static::$i18n_locale_field)))
						{
							$this->storage->i18n_locales_storage->{$c}[$lcode] = $t_storage->$c;
						}
					}
					unset($t_storage);
				}
				static::$i18n_locale = $current_locale;
			}
		}
		else
		{
			$this->new_record = true;
			$this->storage = new static::$result_class();
			$this->stored_primary_keys = array();
		}
		
		foreach(static::$primary_keys as $index => $pk){ $this->storage->$pk = $primary_keys[$index]; }
		
		
		$this->load_mn_relation();
		
		$this->after_load();
		
		return $this->storage;
	}
	
	final public function reload()
	{
		if($this->stored_primary_keys){ return call_user_func_array(array($this, 'load'), $this->stored_primary_keys); }
	}
	
	final public function destroy($primary_keys = null, $find = false)
	{
		$primary_keys || $primary_keys = $this->stored_primary_keys;
		$find && $result = call_user_func_array(array($this, 'load'), (array)$primary_keys);
		
		$this->before_destroy($primary_keys);
		self::delete(self::do_primary_keys($primary_keys));
		$this->destroy_mn_relation($primary_keys);
		$this->after_destroy($primary_keys);
		
		$this->storage = new static::$result_class();
	}
	
	final public function save($attributes = null, $validate = true)
	{
		$attributes && $this->update_storage($attributes);
		if(!$validate || $this->valid()){ return $this->add_or_update_record(); }
		else{ $this->load_anonymus_mn_relation(); }
	}
	
	final private function add_or_update_record()
	{
		$this->before_save();
		
		if($this->new_record)
		{
			$this->before_create();
			$this->add_record();
			$this->after_create();
		}
		else
		{
			$this->before_update();
			$this->update_record();
			$this->after_update();
		}
		
		$this->save_mn_relation();
		
		$this->after_save();
		
		return $this->storage;
	}
	
	final private function add_record()
	{
		$cols = static::columns(self::COLUMNS_TYPE_TABLE);
		foreach($cols as $c => $col)
		{
			isset($this->storage->$c) && $field_values[$c] = $this->storage->$c;
			$col->auto_increment && ($this->storage->$c || $field_values[$c] = null);
		}
		
		$last_id = self::$db->insert(static::table_name(), $field_values);
		
		if(static::$has_i18n)
		{
			$locales = static::$has_mirror ? static::$i18n_locales : array(static::$i18n_locale);
			foreach($locales as $lcode => $l)
			{
				$i18n_field_values = array();
				
				if(static::$has_mirror || $l == static::$i18n_locale)
				{
					foreach(static::columns(self::COLUMNS_TYPE_I18N__ONLY_NAMES) as $c)
					{
						$i18n_field_values[$c] = static::$has_mirror ? $this->storage->i18n_locales_storage->{$c}[$lcode] : $this->storage->$c;
					}
						
					$i18n_field_values[static::$i18n_fk_field] = $last_id;
					$i18n_field_values[static::$i18n_locale_field] = $l;
					
					self::$db->insert(static::i18n_table_name(), ($i18n_field_values));
				}
			}
		}
		
		$pk_name = static::$primary_keys[static::$primary_key_to_i18n_fk_field];
		$cols[$pk_name]->auto_increment && $this->storage->{$pk_name} = $last_id;
		$primary_keys = array_values(array_intersect_key((array)$this->storage, array_flip(static::$primary_keys)));
		
		unset($this->storage);
		call_user_func_array(array($this, 'load'), $primary_keys);
	}
	
	final private function update_record()
	{
		foreach(static::columns(self::COLUMNS_TYPE_ALL) as $c => $col)
		{
			if( !in_array($c, array_merge(static::$primary_keys, array(static::$i18n_fk_field, static::$i18n_locale_field))) )
			{
				if(static::$has_mirror)
				{
					foreach(static::$i18n_locales as $lcode => $l)
					{
						$field_values[$l][$c] = in_array($c, static::columns(self::COLUMNS_TYPE_I18N__ONLY_NAMES)) ? $this->storage->i18n_locales_storage->{$c}[$lcode] : $this->storage->$c;
						if($field_values[$l][$c] === null){ unset($field_values[$l][$c]); }
					}
				}
				else
				{
					$field_values[static::$i18n_locale][$c] = $this->storage->$c;
					if(!$field_values[static::$i18n_locale][$c] === null){ unset($field_values[$c]); }
				}
			}
		}
		$locale = $this->has_mirror ? $this->i18n_locales : array($this->i18n_locale);
		
		$i18n_table_name 	= static::i18n_table_name();
		$primary_key 		= static::$primary_keys[static::$primary_key_to_i18n_fk_field];
		$i18n_fk_field 		= static::$i18n_fk_field;
		$i18n_locale_field 	= static::$i18n_locale_field;
		if($field_values)
		{
			foreach($field_values as $l => $fv)
			{
				$i18n_add = static::$has_i18n ? " LEFT JOIN {$i18n_table_name} ON {$primary_key} = {$i18n_fk_field} AND {$i18n_locale_field} = '$l' " : '';
				$primary_keys = self::do_primary_keys( array_values(array_intersect_key((array)$this->storage, array_flip(static::$primary_keys))) );
				$return = self::$db->update(static::table_name().$i18n_add, $field_values[$l], $primary_keys);
			}
		}
		
		return $this->storage;
	}
	
	final private function update_storage($attributes)
	{
		$this->old_storage = clone $this->storage;
		
		$this->before_update_storage($attributes);
		
		foreach(static::columns(self::COLUMNS_TYPE_ALL) as $c => $col)
		{
			if( !in_array($c, array_merge(static::$primary_keys, array(static::$i18n_fk_field, static::$i18n_locale_field))) )
			{
				if(static::$has_mirror && in_array($c, static::columns(self::COLUMNS_TYPE_I18N__ONLY_NAMES)))
				{
					$this->storage->i18n_locales_storage->$c = $attributes['i18n_locales_storage'][$c];
					unset($this->storage->$c);
				}
				else if(isset($attributes[$c]) || ($col->type == 'tinyint' && $col->max_length == 1))
				{
					if($col->type == 'tinyint' && $col->max_length == 1){ $attributes[$c] = $attributes[$c] ? 1 : 0; }
					$this->storage->$c = $attributes[$c];
					
					isset($attributes[$c.'_confirm']) && $this->storage->{$c.'_confirm'} = $attributes[$c.'_confirm'];
				}
			}
			
			if($this->has_i18n)
			{
				$this->storage->{static::$i18n_fk_field} 		= $this->storage->{static::$primary_keys[static::$primary_key_to_i18n_fk_field]};
				$this->storage->{static::$i18n_locale_field} 	= static::$i18n_locale;
			}
		}
		
		// Many to Many Relation
		if(is_array($this->many_to_many))
		{
			foreach($this->many_to_many as $r => $rel_model){ $rel->$r = (object)array('model' => $rel_model, 'relations' => $attributes[$r]); }
			$this->many_to_many = $rel;
		}
		
		$this->after_update_storage($attributes);
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
		$storage = $attributes ? $attributes : $this->storage;
		if($this->dont_validate_fields === true || $this->dont_validate_fields === 1){ return; }
		
		$dont_validate = array_merge(array(static::$i18n_fk_field, static::$i18n_locale_field), $this->dont_validate_fields);
		
		$columns = static::columns(self::COLUMNS_TYPE_ALL);
		foreach(static::columns(self::COLUMNS_TYPE_ALL) AS $field_name => $field)
		{
			if($field->auto_increment || in_array($field_name, $dont_validate)){ continue; }
			
			$escape = in_array($field->type, array('varchar', 'char')) && (!in_array($field_name, $this->dont_escape_fields) || $this->dont_escape_fields === true);
			
			if(static::$has_i18n && static::$has_mirror && in_array($field_name, static::columns(self::COLUMNS_TYPE_I18N__ONLY_NAMES)))
			{
				foreach(self::$i18n_locales as $lcode => $l)
				{
					$field_value = $storage->i18n_locales_storage->{$field_name}[$lcode];
					$field_value_confirm = $storage->i18n_locales_storage->{$field_name.'_confirm'}[$lcode];
					$this->validate_actions($field_name, $field, $field_value, $field_value_confirm, $lcode, isset($storage->i18n_locales_storage->{$field_name.'_confirm'}[$lcode]));
					$escape && $storage->i18n_locales_storage->{$field_name}[$lcode] = htmlspecialchars(html_entity_decode($storage->i18n_locales_storage->{$field_name}[$lcode]));
				}
			}
			else
			{
				$field_value = $storage->$field_name;
				$field_value_confirm = $storage->{$field_name.'_confirm'};
				$this->validate_actions($field_name, $field, $field_value, $field_value_confirm, null, isset($storage->{$field_name.'_confirm'}));
				$escape && $storage->$field_name = htmlspecialchars(html_entity_decode($storage->$field_name));
			}
			
		}
		
		foreach(static::indices() as $index)
		{
			// AND $columns auto_increment 
			if(!$index->unique){ continue; }
			
			if($index->name == 'PRIMARY')
			{
				$continue = 0;
				foreach($index->columns as $c){ if($columns[$c]->auto_increment){ $continue = 1; break; } }
				if($continue || array(static::$i18n_fk_field, static::$i18n_locale_field) === $index->columns){ continue; }
			}
			
			$params = array();
			foreach($index->columns as $c){ $params[] = static::table_name().'.'.$c.'='. qstr($this->storage->$c); }
			
			$primary_keys = array_values(array_intersect_key((array)$this->storage, array_flip(static::$primary_keys)));
			$primary_keys = $primary_keys ? ' AND NOT('.static::do_primary_keys($primary_keys).')' : '';
			
			$params && $result = $this->find_first('('.implode(' AND ', $params).')'.$primary_keys);
			$result && $this->add_error('already_exists', $index->name, $lang);
		}
		
		return !$this->errors;
	}
	
	
	final private function validate_actions($field_name, $field, $field_value, $field_value_confirm, $lang = null, $check_for_match = false)
	{
		if(!$field->null && (!strlen(trim(preg_replace('~(&nbsp;|<br[^>]*>)~ixm', '', $field_value)))))
		{
			$this->add_error('cant_be_empty', $field_name, $lang);
		}
		else if($field->max_length > 0 && strlen($field_value) > $field->max_length)
		{
			$this->add_error('too_long', $field_name, $lang);
		}
		else if($field_value !== $field_value_confirm && $check_for_match)
		{
			$this->add_error('not_match', $field_name, $lang);
		}
	}
	
	
	final private function validate_fields()
	{
		$columns = static::columns(self::COLUMNS_TYPE_ALL__ONLY_NAMES);
		
		$reflection = new ReflectionObject($this);
		foreach($reflection->getMethods(ReflectionMethod::IS_PROTECTED) as $method)
		{
			$method = $method->getName();
			if(preg_match('~^validate__(.+)~', $method, $matches) && in_array($matches[1], $columns) && !in_array($matches[1], $this->dont_validate_fields))
			{
				$this->$method();
			}
		}
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
	
	protected function before_create(){}
	protected function after_create(){}
	
	
	protected function before_update(){}
	protected function after_update(){}
		
	
	protected function before_destroy($primary_keys){}
	protected function after_destroy($primary_keys){}
	
	
	/*
	 * ------------------------------------
	 * ============ Additional ============
	 * ------------------------------------
	 */
	
	/**
	 * Adds error to the error stack
	 */
	final protected function add_error($type, $field, $lang = null, array $replacer = array())
	{
		$field_name = $lang ? "i18n_locales_storage[$field][$lang]" : $field;
		
		$message = Registry::$globals['DATABASE_ERRORS'][$type];
		$message || $message = Registry::$globals['DATABASE_ERRORS']['unknown_error'].' ('.$type.')';
		$message = strtr($message, $replacer);
		
		$label = Registry::$globals['DATABASE_FIELDS'][$field];
		$label || $label = $field;
		
		$this->errors[$field_name] = (object)array
		(
			'field' 		=> $field, 
			'field_name'	=> $field_name, 
			'label' 		=> $label, 
			'error' 		=> $type,
			'message' 		=> $message,
			'language' 		=> Registry::$locales->info[$lang]
		);
	}	
	
	
	// Load Many to Many relation
	protected function load_mn_relation()
	{
		if(!$this->auto_many_to_many || !$this->many_to_many){ return; }
		
		// Many to many relation
		$pkey = static::$primary_keys[static::$primary_key_to_i18n_fk_field];
		$pk = Inflector::singularize(Inflector::tableize(get_called_class())). '_' . $pkey;
		$pk_condition = $pk.'='.qstr($this->storage->$pkey);
		
		foreach($this->many_to_many as $r => $rel_model)
		{
			
			$model = Inflector::classify(Inflector::singularize($r));
			$pk2 = $model::$primary_keys[$model::$primary_key_to_i18n_fk_field];
			
			is_object($rel_model) && $rel_model = $rel_model->model;
//			$_rel_model = Inflector::tableize($rel_model);
			$_rel_model = $rel_model::table_name();
			$rel_pk = $_rel_model . '.' . Inflector::singularize(Inflector::tableize($model)) . '_' . $pk2;
			$this->storage->$r = $model::find_all($_rel_model.'.'.$pk_condition, null, null, array(array('model' => $rel_model, 'on' => "$r.$pk2 = $rel_pk")));
		}
	}
	
	// load when there is an error
	protected function load_anonymus_mn_relation()
	{
		if(!$this->auto_many_to_many || !$this->many_to_many){ return; }
		
		foreach($this->many_to_many as $r => $rel_model)
		{
			if(!$rel_model->relations){ continue; }
			$model = Inflector::classify(Inflector::singularize($r));
			$pk2 = $model::$primary_keys[$model::$primary_key_to_i18n_fk_field];
			
			$this->storage->$r = $model::find_all("{$r}.{$pk2} IN(".implode(',', $rel_model->relations).")");
		}
	}
	
	// Save Many to Many relation
	protected function save_mn_relation()
	{
		if(!$this->auto_many_to_many || !$this->many_to_many){ return; }
	
		// Many to many relation
		$pkey = static::$primary_keys[static::$primary_key_to_i18n_fk_field];
		$pk = Inflector::singularize(Inflector::tableize(get_called_class())). '_' . $pkey;
		$pk_condition = $pk.'='.qstr($this->storage->$pkey);
		
		foreach($this->many_to_many as $r => $v)
		{
			$rel_model = $v->model;
//			$_rel_model = Inflector::tableize($rel_model);
			$_rel_model = $rel_model::table_name();
			$rel_model::delete($_rel_model.'.'.$pk_condition);
			
			if(!$v->relations){ continue; }
			
			$model2 = Inflector::classify(Inflector::singularize($r));
			$pk2 = $model2::$primary_keys[$model2::$primary_key_to_i18n_fk_field];
			$pk2 = Inflector::singularize($r) . '_' . $pk2;
			
			foreach($v->relations as $id)
			{
				$rel_model::add(array($pk => $this->storage->$pkey, $pk2 => $id));
			}
		}
	}
	
	protected function destroy_mn_relation($primary_key)
	{
		if(!$this->auto_many_to_many || !$this->many_to_many){ return; }
	
		// Many to many relation
		$pkey = static::$primary_keys[static::$primary_key_to_i18n_fk_field];
		$pk = Inflector::singularize(Inflector::tableize(get_called_class())). '_' . $pkey;
		$pk_condition = $pk.'='.qstr($primary_key);
		
		foreach($this->many_to_many as $r => $rel_model)
		{
			$_rel_model = $rel_model::table_name();
			$rel_model::delete($_rel_model.'.'.$pk_condition);
		}
	}
}


?>
