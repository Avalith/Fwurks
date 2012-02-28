<?php

class SqlTable extends SqlBase
{
	protected $columns;
	protected $columns_actions;
	
	protected $temporary;
	protected $if_exists;
	protected $if_not_exists;
	protected $like;
	
	protected $options;
	protected $opt_comment;
	
	protected $opt_insert_method;
	protected $opt_data_dir;
	protected $opt_index_dir;
	
	const ENGINE_INNODB 					= 'INNODB';
	const ENGINE_MYISAM 					= 'MYISAM';
	const ENGINE_MARIA 						= 'MARIA';
	const ENGINE_MEMORY 					= 'MEMORY';
	const ENGINE_BLACKHOLE					= 'BLACKHOLE';
	const ENGINE_ARCHIVE 					= 'ARCHIVE';
	const ENGINE_CSV	 					= 'CSV';
	
	const FOREIGN_KEY_ACTION_NO_ACTION 		= 'NO ACTION';
	const FOREIGN_KEY_ACTION_SET_NULL		= 'SET NULL';
	const FOREIGN_KEY_ACTION_SET_DEFAULT	= 'SET DEFAULT';
	const FOREIGN_KEY_ACTION_RESTRICT 		= 'RESTRICT';
	const FOREIGN_KEY_ACTION_CASCADE		= 'CASCADE';
	
	
	public function __construct($table)
	{
		$this->table($table);
	}
	
	public function __get($name){ return $this->$name; }
	
	public function query()
	{
		return SqlQuery()->table($this->table); 
	}
	
	/**
	 * @return SqlTable
	 */
	public function create()
	{
		$this->type = 'create';
		return $this;
	}
	
	/**
	 * @return SqlTable
	 */
	public function drop()
	{
		$this->type = 'drop';
		return $this;
	}
	
	/**
	 * @return SqlTable
	 */
	public function alter()
	{
		$this->type = 'alter';
		return $this;
	}
	
	/**
	 * @return SqlTable
	 */
	public function truncate()
	{
		$this->type = 'truncate';
		return $this;
	}
	
	/**
	 * @return SqlTable
	 */
	public function showCreate()
	{
		$this->type = 'show_create';
		return $this;
	}
	
	/**
	 * @return SqlTable
	 */
	public function showColumns()
	{
		$this->type = 'show_columns';
		return $this;
	}
	
	/**
	 * @return SqlTable
	 */
	public function showKeys()
	{
		$this->type = 'show_keys';
		return $this;
	}
	
	/**
	 * @return SqlTable
	 */
	public function showTablesLike()
	{
		$this->type = 'show_tables_like';
		return $this;
	}
	
	/**
	 * @return SqlTable
	 */
	public function showTableStatusLike()
	{
		$this->type = 'show_table_status_like';
		return $this;
	}
	
	/**
	 * @param String $table
	 * @return SqlTable
	 */
	public function like($table = null)
	{
		$this->like = $table;
		return $this;
	}
	
	/**
	 * @return SqlTable
	 */
	public function temporary()
	{
		$this->temporary = 'TEMPORARY ';
		
		return $this;
	}
	
	/**
	 * @return SqlTable
	 */
	public function if_exists()
	{
		$this->if_exists = 'IF EXISTS ';
		
		return $this;
	}
	
	/**
	 * @return SqlTable
	 */
	public function if_not_exists()
	{
		$this->if_not_exists = 'IF NOT EXISTS ';
		
		return $this;
	}
	
	/**
	 * @param SqlColumn $column
	 * @return SqlTable
	 */
	public function column(SqlColumn $column)
	{
		$this->columns[] = $column;
		$this->columns_actions[] = "ADD COLUMN";
		return $this;
	}
	
	/**
	 * @param SqlColumn $name
	 * @param String $column
	 * @return SqlTable
	 */
	public function changeColumn($name, SqlColumn $column)
	{
		$this->columns[] = $column;
		$this->columns_actions[] = "CHANGE COLUMN `$name`";
		return $this;
	}
	
	/**
	 * @param SqlColumn $column
	 * @return SqlTable
	 */
	public function modifyColumn(SqlColumn $column)
	{
		$this->columns[] = $column;
		$this->columns_actions[] = "MODIFY COLUMN";
		return $this;
	}
	
	/**
	 * @param String $name
	 * @return SqlTable
	 */
	public function dropColumn($name)
	{
		$this->columns[] = $name;
		$this->columns_actions[] = "DROP COLUMN";
		return $this;
	}
	
	/**
	 * @param Array $columns
	 * @return SqlTable
	 */
	public function primary(array $columns)
	{
		$columns = '`'.implode('`,`', $columns).'`';
		$this->columns[] = null;
		$this->columns_actions[] = "ADD PRIMARY KEY ($columns)";
		return $this;
	}
	
	
	/**
	 * @return SqlTable
	 */
	public function dropPrimaryKey()
	{
		$this->columns[] = null;
		$this->columns_actions[] = "DROP PRIMARY KEY";
		return $this;
	}
	
	
	/**
	 * @param String $name
	 * @param Array $columns
	 * @return SqlTable
	 */
	public function index($name, array $columns)
	{
		$columns = '`'.implode('`,`', $columns).'`';
		$this->columns[] = null;
		$this->columns_actions[] = "ADD INDEX `$name` ($columns)";
		return $this;
	}
	
	/**
	 * @param String $name
	 * @param Array $columns
	 * @return SqlTable
	 */
	public function unique($name, array $columns)
	{
		$columns = '`'.implode('`,`', $columns).'`';
		$this->columns[] = null;
		$this->columns_actions[] = "ADD UNIQUE `$name` ($columns)";
		return $this;
	}
	
	/**
	 * @param String $name
	 * @param Array $columns
	 * @return SqlTable
	 */
	public function fulltext($name, array $columns)
	{
		$columns = '`'.implode('`,`', $columns).'`';
		$this->columns[] = null;
		$this->columns_actions[] = "ADD FULLTEXT `$name` ($columns)";
		return $this;
	}
	
	
	/**
	 * @param String $name
	 * @param Array $columns
	 * @return SqlTable
	 */
	public function foreign_key($name, array $columns, $foreign_table, array $foreign_columns, $on_delete = self::FOREIGN_KEY_ACTION_CASCADE, $on_update = self::FOREIGN_KEY_ACTION_CASCADE)
	{
		$columns 			= '`'.implode('`,`', $columns).'`';
		$foreign_columns 	= '`'.implode('`,`', $foreign_columns).'`';
		$this->columns[] = null;
		$this->columns_actions[] = "ADD CONSTRAINT `$name` FOREIGN KEY ($columns) REFERENCES `$foreign_table` ($foreign_columns) ON DELETE $on_delete ON UPDATE $on_update";
		return $this;
	}
	
	
	/**
	 * @return SqlTable
	 */
	public function dropForeignKey($name)
	{
		$this->columns[] = null;
		$this->columns_actions[] = "DROP FOREIGN KEY `$name`";
		return $this;
	}
	
	/**
	 * @param String $name
	 * @return SqlTable
	 */
	public function dropKey($name)
	{
		$this->columns[] = null;
		$this->columns_actions[] = "DROP KEY `$name`";
		return $this;
	}
	
	/**
	 * @param String $engine
	 * @return SqlTable
	 */
	public function engine($engine)
	{
		$this->options[] = 'ENGINE = '.strtoupper($engine);
		return $this;
	}

	/**
	 * @param Integer $value
	 * @return SqlTable
	 */
	public function autoIncrement($value = 0)
	{
		$this->options[] = "AUTO_INCREMENT = {$value}";
		return $this;
	}
	
	/**
	 * @param String $charset
	 * @return SqlTable
	 */
	public function charset($charset)
	{
		$this->options[] = "DEFAULT CHARACTER SET = {$charset}";
		return $this;
	}
	
	/**
	 * @param String $collate
	 * @return SqlTable
	 */
	public function collate($collate)
	{
		$this->options[] = "DEFAULT COLLATE = {$collate}";
		return $this;
	}
	
	/**
	 * @param String $comment
	 * @return SqlTable
	 */
	public function comment($comment)
	{
		$this->options[] = "COMMENT = '{$comment}'";
		return $this;
	}
	
	/**
	 * @param String $method
	 * @return SqlTable
	 */
	public function insertMethod($method)
	{
		switch(strlower($method))
		{
			case 'no'				: $method = 'NO'; 		break;
			case 'first'			: $method = 'FIRST'; 	break; 
			case 'last': default	: $method = 'LAST'; 	break;
		}
		$this->options[] = "INSERT_METHOD = {$method}";
		return $this;
	}
	
	/**
	 * @param String $path
	 * @return SqlTable
	 */
	public function dataDir($path)
	{
		$this->options[] = "DATA DIRECTORY = '{$path}'";
		return $this;
	}
	
	/**
	 * @param String $path
	 * @return SqlTable
	 */
	public function indexDir($path)
	{
		$this->options[] = "INDEX DIRECTORY = '{$path}'";
		return $this;
	}
	
	
	protected function __str_create()
	{
		$like = $options = '';
		$columns = array();
		$this->like					&& $like 	 = 	' LIKE '.$this->like;
		if($this->columns_actions)
		{
			foreach($this->columns_actions as $i => $a)
			{
				$c = $this->columns[$i] instanceof SqlColumn ? $this->columns[$i]->clearPosition() : $this->columns[$i];
				$columns[] = strtr($a, array('ADD ' => '', 'COLUMN' => '')) . ' ' . $c;
			}
		}
		$columns = '('.implode(', ', $columns).')';
		is_array($this->options)	&& $options	 =	' '.implode(', ', $this->options);
		
		return "CREATE {$this->temporary}TABLE {$this->if_not_exists}{$this->table}{$like}{$columns}{$options}";
	}
	
	protected function __str_drop()
	{
		return "DROP {$this->temporary}TABLE {$this->if_exists}{$this->table}";
	}
	
	protected function __str_alter()
	{
		$columns = array();
		$options = '';
		if($this->columns_actions){ foreach($this->columns_actions as $i => $a){ $columns[] = "$a {$this->columns[$i]}"; } }
		$columns = implode(', ', $columns);
		is_array($this->options) && $options = ' '.implode(', ', $this->options);
		
		return "ALTER TABLE {$this->table} {$columns}{$options}";
	}
	
	protected function __str_truncate()
	{
		return "TRUNCATE TABLE {$this->table}";
	}
	
	
	protected function __str_show_create()
	{
		return "SHOW CREATE TABLE {$this->table}";
	}
	
	protected function __str_show_columns()
	{
		return "SHOW COLUMNS FROM {$this->table}";
	}
	
	protected function __str_show_keys()
	{
		return "SHOW KEYS FROM {$this->table}";
	}
	
	protected function __str_show_tables_like()
	{
		return "SHOW TABLES LIKE '{$this->table}'";
	}
	
	protected function __str_show_table_status_like()
	{
		return "SHOW TABLE STATUS LIKE '{$this->table}'";
	}
	
	
	/**
	 * @param Boolean $cache
	 * @return Boolean
	 */
	public function exists($cache = true)
	{
		static $table_cache;
		if($cache && isset($table_cache[$this->table])){ return $table_cache[$this->table]; }
		
		$result = $this->showTablesLike()->result();
		isset($result[0]) && $result = end($result[0]);
		
		return $table_cache[$this->table] = ($this->table == $result);
	}
	
	/**
	 * @return StdClass
	 */
	public function getColumns()
	{
		if(!$this->exists()){ return false; }
		
		$columns = new StdClass();
		foreach($this->showColumns()->result() as $c)
		{
			$c->length 	= null;
			$c->real_type = $c->Type;
			if (preg_match("/^(.+)\((\d+)(?:,(\d+))?/", $c->Type, $type_array))
			{
				$c->real_type = $type_array[1];
				$c->length 		= isset($type_array[2]) && is_numeric($type_array[2]) ? $type_array[2] : null;
				$c->decimals 	= isset($type_array[3]) && is_numeric($type_array[3]) ? $type_array[3] : null;
			}
			else if (preg_match("/^(enum|set)\((.*)\)$/i", $c->Type, $type_array))
			{
				$c->real_type 	= $type_array[1];
				$c->length 	= explode("','", trim($type_array[2], "'"));
			}
			
			$columns->{$c->Field} = (object)array
			(
				'name' 				=> $c->Field,
				'type' 				=> $c->real_type,
				'length' 			=> $c->length,
				'decimals' 			=> isset($c->decimals) ? $c->decimals : null,
				'not_null' 			=> $c->Null == 'NO',
				'default' 			=> $c->Default,
				'primary'			=> strpos($c->Key, "PRI") === 0,
				'unique'			=> strpos($c->Key, "UNI") === 0,
				'unsigned' 			=> strpos($c->Type,'unsigned') !== false,
				'auto_increment'	=> strpos($c->Extra, 'auto_increment') !== false,
				'binary' 			=> strpos($c->Type,'binary') !== false,
				'zerofill' 			=> strpos($c->Type,'zerofill') !== false,
			);		
		}
		
		return $columns;
	}
	
	/**
	 * @return StdClass
	 */
	public function getKeys()
	{
		if(!$this->exists()){ return false; }
		
		$create = $this->showCreate()->result();
		$create = $create[0]->{'Create Table'};
		
		preg_match_all("~(?:PRIMARY KEY|UNIQUE KEY|CONSTRAINT|KEY) .*~", $create, $_keys, PREG_PATTERN_ORDER);
		$_keys = $_keys[0];
		
		$keys = new StdClass();
		foreach($_keys as $k)
		{
			$type = substr($k, 0, strpos($k, ' '));
			switch($type)
			{
				case 'PRIMARY':
				{
					$start = strpos($k, '(')+2;
					$keys->PRIMARY = (object)array
					(
						'name' 			=> 'PRIMARY',
						'table' 		=> $this->table,
						'unique' 		=> true,
						'fulltext' 		=> false,
						'columns' 		=> explode('`,`', substr($k, $start, strpos($k, ')')-$start-1)),
					);
				}
				break;
				
				case 'KEY':
				case 'UNIQUE':
				{
					$start = strpos($k, '`')+1;
					$name = substr($k, $start, strpos($k, '`', $start)-$start);
					$start = strpos($k, '(')+2;
					$keys->$name = (object)array
					(
						'name' 			=> $name,
						'table' 		=> $this->table,
						'unique' 		=> $type == 'UNIQUE',
						'fulltext' 		=> false,
						'columns' 		=> explode('`,`', substr($k, $start, strpos($k, ')')-$start-1)),
					);
				}
				break;
				
				case 'CONSTRAINT':
				{
					$start = strpos($k, '`')+1;
					$name = substr($k, $start, strpos($k, '`', $start)-$start);
					
					$start = strpos($k, 'REFERENCES `')+12;
					$foreign_table = substr($k, $start, strpos($k, '`', $start)-$start);
					
					$start = strpos($k, '(')+2;
					$columns = explode('`,`', substr($k, $start, strpos($k, ')')-$start-1));
					
					$start = strrpos($k, '(')+2;
					$foreign_columns = explode('`,`', substr($k, $start, strrpos($k, ')')-$start-1));
					
					$start = strpos($k, 'ON DELETE')+10;
					$on_delete = rtrim(substr($k, $start, strpos($k, 'ON UPDATE', $start)-$start), ' ');
					
					$start = strpos($k, 'ON UPDATE')+10;
					$on_update = rtrim(substr($k, $start), ' ,');
					
					$keys->$name = (object)array
					(
						'name' 				=> $name,
						'table' 			=> $this->table,
						'foreign_table'		=> $foreign_table,
						'unique' 			=> false,
						'fulltext' 			=> false,
						'columns' 			=> $columns,
						'foreign_columns' 	=> $foreign_columns,
						'on_delete' 		=> $on_delete,
						'on_update'	 		=> $on_update,
					);
				}
				break;
			}
			
			continue;
			$keys->{$k->Key_name} = (object)array
			(
				'name' 			=> $k->Key_name,
				'table' 		=> $k->Table,
				'unique' 		=> !$k->Non_unique,
				'fulltext' 		=> $k->Index_type == 'FULLTEXT',
				'columns' 		=> array(),
			);
		}
		
		return $keys;
	}
	
	/**
	 * @return StdClass
	 */
	public function getOptions()
	{
		if(!$this->exists()){ return false; }
		
		return end($this->showTableStatusLike()->result());
	}
	
	
	
}

/**
 * @param string $table
 * @return SqlTable
 */
function SqlTable($table)
{
	return new SqlTable($table);
}

?>