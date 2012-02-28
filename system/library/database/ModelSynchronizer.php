<?php

class ModelSynchronizer
{
	protected $model;
	protected $cols;
	protected $keys;
	protected $opts;
	
	public function __construct($model)
	{
		$this->model = $model;
	}
	
	public function sync()
	{
		$result = new StdClass();
		
		if(is_subclass_of($this->model, 'ORMView'))
		{
			$this->is_synced_view() 	|| $result->main = $this->definition();
		}
		else
		{
			$this->is_synced() 				|| $result->main = $this->create_query();
			$this->is_synced(true) 			|| $result->i18n = $this->create_query(true);
		}
		
		return (isset($result->main) || isset($result->i18n)) ? $result : false;
	}
	
	
	protected function is_synced($i18n = false)
	{
		$is_synced = true;
		
		$model 		= $this->model;
		$m_cols 	= $model::columns($i18n ? $model::COLUMNS_TYPE_I18N : $model::COLUMNS_TYPE_TABLE);
		$m_cols2 	= new StdClass(); // for dropping purpose
		
		$table	 	= $i18n ? $model::i18n_table_name() : $model::table_name(); if(!$table){ return true; }
		$table	 	= SqlTable($table);
		$t_cols 	= $table->getColumns();
		$t_keys 	= $table->getKeys();
		
		$this->cols = new StdClass();
		$this->keys = new StdClass();
		
		$last_position = 'first';
		
		foreach($m_cols as $name => $c)
		{
			$c->_new_column = false;
			if(isset($t_cols->$name))
			{
				$c->_synced = $this->compare_columns($c, $t_cols->$name);
				$c->_synced || $is_synced = false;
				$c->oldName($name);
			}
			else if($c->oldName() && isset($t_cols->{$c->oldName()}))
			{
				$c->_synced = $is_synced = $this->compare_columns($c, $t_cols->{$c->oldName()});
				$m_cols2->{$c->oldName()} = $c;
			}
			else
			{
				$c->oldName($name);
				$c->_new_column = true;
				$c->_synced = false;
				
				$is_synced = false;
			}
			
			$c->_position = $last_position;
			$c->_drop = false;
			
			$last_position = 'after';
			$this->cols->$name = $c;
			
			// Build Keys
			if($c->is_primary())
			{
				isset($this->keys->PRIMARY) || $this->keys->PRIMARY = (object)array('type' => 'PRIMARY', 'columns' => array());
				$this->keys->PRIMARY->columns[] = $c->name();
			}
			
			foreach(array('index','unique','fulltext') as $type)
			{
				if($key = $c->{'is_'.$type}())
				{
					isset($this->keys->$key) || $this->keys->$key = (object)array('type' => strtoupper($type), 'columns' => array());
					$this->keys->$key->columns[] = $c->name();
				}
			}
			
			if($key = $c->has_foreign_key())
			{
				$this->keys->{$key->name} = (object)array('type' => 'FOREIGN', 'definition' => $key);
			}
		}
		
		if(is_object($t_cols))
		{
			foreach($t_cols as $name => $c)
			{
				if(!isset($m_cols->$name) && !isset($m_cols2->$name))
				{
					unset($t_cols->$name);
					$this->cols->$name->_drop = true;
					$this->cols->$name->_synced = false;
					$this->cols->$name->_new_column = false;
					
					$is_synced = false;
				}
			}
		}
		
		$ck = array(); foreach($this->cols as $name => $c){ $ck[] = $c instanceof ORMColumn ? $c->oldName() : $name; }
		$tk = array_keys((array)$t_cols);
		$new_cols = 0;
		foreach($ck as $i => $_c)
		{
			if($this->cols->$_c->_new_column){ $new_cols++; }
			if(isset($tk[$i-$new_cols]) && $_c != $tk[$i-$new_cols]){ $this->cols->$_c->_synced = false; $is_synced = false; }
		}
		
		// Keys
		foreach($this->keys as $name => &$k)
		{
			$k->_drop = false;
			$k->_synced = false;
			$k->_create = false;
			
			if(isset($t_keys->$name))
			{
				if(isset($k->columns))
				{
					if($k->columns != $t_keys->$name->columns)
					{
						$is_synced = false;
						$k->_drop = true;
						$k->_create = true;
						continue;
					}
				}
				else
				{
					$tk = $t_keys->$name;
					
					if($k->definition->foreign_table 	!= $tk->foreign_table 
					|| $k->definition->columns 			!= $tk->columns 
					|| $k->definition->foreign_columns 	!= $tk->foreign_columns
					|| $k->definition->on_delete 		!= $tk->on_delete
					|| $k->definition->on_update 		!= $tk->on_update
					){
						$is_synced = false;
						$k->_drop = true;
						continue;
					}
				}
								
				$k->_synced = true;
			}
			else
			{
				$k->_create = true;
				$is_synced = false;
			}
		}
		
		if(is_object($t_keys))
		{
			foreach($t_keys as $name => $tk)
			{
				if(!isset($this->keys->$name))
				{
					$this->keys->$name->type 	= $name == 'PRIMARY' ? 'PRIMARY' : $tk->foreign_table ? 'FOREIGN' : '';
					$this->keys->$name->_drop 	= true;
					$this->keys->$name->_synced = false;
					$this->keys->$name->_create = false;
					$is_synced = false;
				}
			}
		}
		
		// Options
		if(!$i18n)
		{
			$m_engine 	= strtolower($model::table_engine());
			$t_opts 	= $table->getOptions();
			$t_engine 	= strtolower($t_opts->Engine);
			
			if($m_engine != $t_engine)
			{
				$this->opts->engine = true;
				$is_synced = false;
			}
		}
		
		
		return $is_synced;
	}
	
	protected function compare_columns($m_col, $t_col)
	{
		if($m_col->real_type() != $t_col->type){ return false; }
		
		$fields = array(
			'get_length'		=> 'length',
			'get_decimals'		=> 'decimals',
			'get_default' 		=> 'default',
			'is_not_null' 		=> 'not_null',
			'is_unsigned'		=> 'unsigned',
			'is_auto_increment'	=> 'auto_increment',
			'is_zerofill'		=> 'zerofill',
		);
		
		foreach($fields as $m => $t){ if($m_col->{$m}() != $t_col->$t){ return false; } }
		
		if($m_col->get_default() === 0 && (string)$m_col->get_default() !== $t_col->default){ return false; }
		
		return true;
	}
	
	protected function create_query($i18n = false)
	{
		$model = $this->model;
		$table = SqlTable($i18n ? $model::i18n_table_name() : $model::table_name());
		
		$table->exists() ? $table->alter() : $table->create();
		
		
		$opts = array(
			'is_not_null' 		=> 'notNull',
			'get_default'		=> 'defaultValue',
			'is_unsigned'		=> 'unsigned',
			'is_auto_increment'	=> 'autoIncrement',
			'is_zerofill'		=> 'zerofill',
		);
		
		$last_field = null;
		
		foreach($this->cols as $name => $c)
		{
			if($c->_drop)
			{
				$table->dropColumn($name);
			}
			else if(!$c->_synced)
			{
				$col = SqlColumn($name)->{$c->_position}($last_field);
				
				$length = (array)$c->get_length();
				$decimals = $c->get_decimals();
				$decimals && $length[] = $c->get_decimals();
				call_user_func_array(array($col, strtoupper($c->real_type())), $length);
				
				foreach($opts as $o => $o2)
				{
					$value = $c->$o();
					$value !== null && $col->$o2($value);
				}
				
				$c->_new_column ? $table->column($col) : $table->changeColumn($c->oldName(), $col);
			}
			
			$last_field = $name;
		}
		
		// Keys
		foreach($this->keys as $name => $k)
		{
			if(!$k->_synced)
			{
				if($k->_drop)
				{
					if			($k->type == 'PRIMARY')	{ $table->dropPrimaryKey(); }
					else if 	($k->type == 'FOREIGN')	{ $table->dropForeignKey($name); }
					else 								{ $table->dropKey($name); }
				}
						
				if($k->_create)
				{
					if($k->type == 'PRIMARY')
					{
						$table->primary($k->columns);
					}
					else if($k->type == 'FOREIGN')
					{
						$table->foreign_key($k->definition->name, $k->definition->columns, $k->definition->foreign_table, $k->definition->foreign_columns, $k->definition->on_delete, $k->definition->on_update);
					}
					else
					{
						$table->{strtolower($k->type)}($name, $k->columns);
					}
				}
			}
		}
		
		// Options
		isset($this->opts->engine) && $table->engine($model::table_engine());
		
		return $table;
	}
	

	protected function is_synced_view()
	{
		$is_synced = true;
		
		$model = $this->model;
		
		
		
		
		de($model);
	}
	
}

?>