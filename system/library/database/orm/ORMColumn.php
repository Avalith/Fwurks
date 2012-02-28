<?php

class ORMColumn
{
	protected $name;
	protected $old_name;
	protected $type;
	
	protected $length;
	protected $decimals;
	
	protected $not_null;
	protected $requiered;
	protected $default;
	protected $primary;
	protected $index;
	protected $unique;
	protected $fulltext;
	protected $unsigned;
	protected $zerofill;
	protected $auto_increment;
	
	protected $on_create_timestamp;
	protected $on_update_timestamp;
	
	// ORM Properties
	protected $can_validate = true;
	protected $can_escape_html = true;
	
	protected $foreign_key;
	
	protected $file_options = null;
	protected $file_field_name = null;
	
	public function __construct($name)
	{
		$this->name = $name;
	}
	
	
	/**
	 * @param String $name
	 * @return ORMColumn
	 */
	public function name($name = null)
	{
		if($name)
		{
			$this->name = $name;
			return $this;
		}
		return $this->name;
	}
	
	
	/**
	 * @param String $name
	 * @return ORMColumn
	 */
	public function oldName($name = null)
	{
		if($name)
		{
			$this->old_name = $name;
			return $this;
		}
		return $this->old_name;
	}
	
	

	public function copy(ORMColumn $column)
	{
		$this->type 	= $column->type();
		$this->length 	= $column->get_length();
		$this->default 	= $column->get_decimals();
		$this->not_null	= $column->is_not_null();
		$this->unsigned	= $column->is_unsigned();
		$this->zerofill	= $column->is_zerofill();
		
		$this->can_validate 	= $column->canValidate();
		$this->can_escape_html 	= $column->canEscapeHTML();
		
		return $this;
	}
	
	
	/**
	 * ------------------------------------
     * ============== Types ===============
	 * ------------------------------------
     */
	
	/**
	 * @param Integer $length
	 * @return ORMColumn
	 */
	public function number($length = 10)
	{
		$this->type = 'number';
		$this->length = $length;
		$this->can_escape_html = false;
		
		return $this;
	}
	
	
	/**
	 * @param Integer $length
	 * @param Ingeter $decimals
	 * @return ORMColumn
	 */
	public function float($length = 9, $decimals = 2)
	{
		$this->type = 'float';
		$this->length = $length;
		$this->decimals = $decimals;
		$this->can_escape_html = false;
		
		return $this;
	}
	
	
	/**
	 * @param Integer $length
	 * @param Ingeter $decimals
	 * @return ORMColumn
	 */
	public function decimal($length = 9, $decimals = 2)
	{
		$this->type = 'decimal';
		$this->length = $length;
		$this->decimals = $decimals;
		$this->can_escape_html = false;
		
		return $this;
	}
	
	
	/**
	 * @param Integer $default
	 * @return ORMColumn
	 */
	public function boolean($default = 0)
	{
		$this->type = 'boolean';
		$this->default = $default;
		$this->length = 1;
		$this->unsigned()->not_null();
		$this->can_escape_html = false;
		
		return $this;
	}
	
	
	/**
	 * @param Integer $chars
	 * @return ORMColumn
	 */
	public function chars($length)
	{
		$this->type = 'chars';
		$this->length = $length;
		
		return $this;
	}
	
	
	/**
	 * @param Integer $length
	 * @return ORMColumn
	 */
	public function string($length = 200)
	{
		$this->type = 'string';
		$this->length = $length;
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function text()
	{
		$this->type = 'text';
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function mediumtext()
	{
		$this->type = 'mediumtext';
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function longtext()
	{
		$this->type = 'longtext';
		
		return $this;
	}

	
	/**
	 * @return ORMColumn
	 */
	public function binary($length)
	{
		$this->type = 'binary';
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function varbinary($length)
	{
		$this->type = 'varbinary';
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function blob()
	{
		$this->type = 'blob';
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function mediumblob()
	{
		$this->type = 'mediumblob';
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function longblob()
	{
		$this->type = 'longblob';
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function date()
	{
		$this->type = 'date';
		$this->can_escape_html = false;
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function time()
	{
		$this->type = 'time';
		$this->can_escape_html = false;
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function datetime()
	{
		$this->type = 'datetime';
		$this->can_escape_html = false;
		
		return $this;
	}
	
	
	/**
	 * @param Array $values
	 * @return ORMColumn
	 */
	public function enum(array $values)
	{
		$this->type = 'enum';
		$this->length = array_values($values);
		$this->can_escape_html = false;
		
		return $this;
	}
	
	
	/**
	 * @param Array $values
	 * @return ORMColumn
	 */
	public function set(array $values)
	{
		$this->type = 'set';
		$this->length = $values;
		$this->can_escape_html = false;
		
		return $this;
	}
	
	
	
	/**
	 * ------------------------------------
     * ========== Types Settings ==========
	 * ------------------------------------
     */
	
	/**
	 * @return ORMColumn
	 */
	public function not_null()				{ $this->not_null 				= true;			return $this; }
	
	
	/**
	 * @return ORMColumn
	 */
	public function default_value($default)	{ $this->default 				= $default;		return $this; }
	
	
	/**
	 * @return ORMColumn
	 */
	public function requiered()				{ $this->requiered 				= true;			$this->not_null(); return $this; }
	
	
	/**
	 * @return ORMColumn
	 */
	public function primary()				{ $this->primary 				= true; 		$this->not_null(); return $this; }
	
	
	/**
	 * @param Integer $key
	 * @return ORMColumn
	 */
	public function index($key = null)		{ $this->index	 				= $key ?: true;	return $this; }
	
	
	/**
	 * @param Integer $key
	 * @return ORMColumn
	 */
	public function unique($key = null)		{ $this->unique 				= $key ?: true;	return $this; }
	
	
	/**
	 * @param Integer $key
	 * @return ORMColumn
	 */
	public function fulltext($key = null)	{ $this->fulltext 				= $key ?: true;	return $this; }
	
	
	/**
	 * @return ORMColumn
	 */
	public function unsigned()				{ $this->unsigned 				= true;			return $this; }
	
	
	/**
	 * @return ORMColumn
	 */
	public function zerofill()				{ $this->zerofill 				= true;			return $this; }
	
	
	/**
	 * @return ORMColumn
	 */
	public function autoIncrement()			{ $this->auto_increment 		= true;			return $this; }
	
	
	/**
	 * @return ORMColumn
	 */
	public function on_create_timestamp()	{ $this->on_create_timestamp 	= true;			return $this; }
	
	
	/**
	 * @return ORMColumn
	 */
	public function on_update_timestamp()	{ $this->on_update_timestamp 	= true;			$this->on_create_timestamp(); return $this; }
	
	
	
	
	/**
	 * @param Integer $key
	 * @return ORMColumn
	 */
	public function foreign_key($key, $model, $column, $on_delete = SqlTable::FOREIGN_KEY_ACTION_CASCADE, $on_update = SqlTable::FOREIGN_KEY_ACTION_CASCADE)
	{
		$this->copy($model::columns()->$column);
		
		$this->foreign_key = (object)array
		(
			'name' 				=> 'fkey_'.$key,
			'foreign_model'		=> $model,
			'foreign_table' 	=> $model::table_name(),
			'columns'			=> array($this->name),
			'foreign_columns'	=> array($column),
			'on_delete'			=> $on_delete,
			'on_update'			=> $on_update,
		);
		
		return $this;
	}
	
	
	/**
	 * ------------------------------------
     * ========= Predefined Types =========
	 * ------------------------------------
     */
	
	/**
	 * @param Integer $length
	 * @return ORMColumn
	 */
	public function ID($length = 10)
	{
		$this->number($length)->autoIncrement()->primary()->unsigned();
		$this->name || $this->name = 'id';
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function UUID()
	{
		$this->name || $this->name = 'uuid';
		$this->chars(36);
		
		return $this;
	}
	
	
	/**
	 * @param Integer $length
	 * @return ORMColumn
	 */
	public function Password($length = 128)
	{
		$this->name || $this->name = 'password';
		$this->chars($length);
		
		return $this;
	}
	
	
	/**
	 * @param Integer $default
	 * @return ORMColumn
	 */
	public function Active($default = 0)
	{
		$this->name || $this->name = 'active';
		$this->boolean($default);
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function Created()
	{
		$this->name || $this->name = 'created';
		$this->datetime()->on_create_timestamp();
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function Updated()
	{
		$this->name || $this->name = 'updated';
		$this->datetime()->on_update_timestamp();
		
		return $this;
	}
	
	
	/**
	 * @param Integer $length
	 * @return ORMColumn
	 */
	public function Slug($length = 100)
	{
		$this->name || $this->name = 'slug';
		$this->string($length);
		
		return $this;
	}
	
	
	
	/**
	 * @param Integer $length
	 * @return ORMColumn
	 */
	public function OrderIndex($length = 10)
	{
		$this->name || $this->name = 'order_index';
		$this->number($length);
		
		return $this;
	}
	
	
	const FILE_SAVE_DB = 'db';
	const FILE_SAVE_FS = 'fs';
	
	/**
	 * @return ORMColumn
	 */
	public function File(array $file_opts = array(), $save_to = self::FILE_SAVE_DB)
	{
		$save_to == self::FILE_SAVE_DB ? $this->mediumblob()->doNotEscapeHTML() : $this->chars(3);
		
		$this->file_options = new StdClass();
		
		$this->file_options->save_to 			= $save_to;
		
		$this->file_options->save_filename 		= isset($file_opts['save_filename']) 	? (array)$file_opts['save_filename'] 	: true;
		$this->file_options->save_filetype 		= isset($file_opts['save_filetype']) 	? (array)$file_opts['save_filetype'] 	: true;
		$this->file_options->save_filesize 		= isset($file_opts['save_filesize']) 	? (array)$file_opts['save_filesize'] 	: true;
		
		$this->file_options->crops 				= isset($file_opts['crops']) 			? (array)$file_opts['crops'] 			: array();
		$this->file_options->resizes 			= isset($file_opts['resizes']) 			? (array)$file_opts['resizes'] 			: array();
		// strtr($size, array('^' => '_out', '>' => '_ol', '<' => '_os', '!' => '_nr'));
		
		$this->file_options->valid_extensions	= isset($file_opts['valid_extensions'])	? (array)$file_opts['valid_extensions']	: null;
		$this->file_options->valid_max_size		= isset($file_opts['valid_max_size'])	? $file_opts['valid_max_size']			: 0;
		
		$this->file_options->folder				= isset($file_opts['folder'])			? $file_opts['folder']					: null;
		$this->file_options->extension_only		= isset($file_opts['extension_only'])	? $file_opts['extension_only']			: true;
		
		return $this;
	}
	
	
 	/**
	 * @return ORMColumn
	 */
	public function FileName($length = 100)
	{
		$this->file_field_name = $this->name;
		$this->name .= '_filename';
		$this->string($length);
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function FileType()
	{
		$this->file_field_name = $this->name;
		$this->name .= '_filetype';
		$this->string(30);
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function FileSize()
	{
		$this->file_field_name = $this->name;
		$this->name .= '_filesize';
		$this->number()->unsigned();
		
		return $this;
	}
	
	
	/**
	 * @return ORMColumn
	 */
	public function FileImage($size)
	{
		$this->file_field_name = $this->name;
		$this->name .= '_'.$size;
		$this->blob()->doNotEscapeHTML();
		
		return $this;
	}
	
	
	
	
	/**
	 * ------------------------------------
     * ========== Getter Methods ==========
	 * ------------------------------------
     */
	
	
	/**
	 * @return String
	 */
	public function type(){ return $this->type; }
	
	
	/**
	 * @return String
	 */
	public function real_type()
	{
		switch($this->type)
		{
			case 'number':
			{
				$add = (int)$this->unsigned;
				if		($this->length < 3)			{ $real_type = 'tinyint'; }
				else if	($this->length < 5+$add)	{ $real_type = 'smallint'; }
				else if	($this->length < 7+$add)	{ $real_type = 'mediumint'; }
				else if	($this->length < 10+$add)	{ $real_type = 'int'; }
				else 								{ $real_type = 'bigint'; }
			}
			break;
			
			case 'float':
			{
				if		($this->length < 10)	{ $real_type = 'float'; }
				else 							{ $real_type = 'double'; }
			}
			break;
			
			case 'boolean'		: $real_type = 'tinyint';				break;
			
			case 'chars'		: $real_type = 'char';					break;
			case 'string'		: $real_type = 'varchar';				break;
			
			default				: $real_type = $this->type;	break;
		}
		
		return $real_type;
	}
	
	
	/**
	 * @return Integer
	 */
	public function get_length				(){ return $this->length; }
	
	
	/**
	 * @return Integer
	 */
	public function get_decimals			(){ return $this->decimals; }
	
	
	/**
	 * @return String
	 */
	public function get_default				(){ return $this->default; }
	
	
	/**
	 * @return Boolean
	 */
	public function is_not_null				(){ return $this->not_null; }
	
	
	/**
	 * @return Boolean
	 */
	public function is_requiered			(){ return $this->requiered; }
	
	
	/**
	 * @return Boolean
	 */
	public function is_primary				(){ return $this->primary; }
	
	
	/**
	 * @return Boolean
	 */
	public function is_index				(){ return $this->index === true ? $this->name : $this->index; }
	
	
	/**
	 * @return Boolean
	 */
	public function is_unique				(){ return $this->unique === true ? $this->name : $this->unique; }
	
	
	/**
	 * @return Boolean
	 */
	public function is_fulltext				(){ return $this->fulltext === true ? $this->name : $this->fulltext; }
	
	
	/**
	 * @return Boolean
	 */
	public function is_unsigned				(){ return $this->unsigned; }
	
	
	/**
	 * @return Boolean
	 */
	public function is_auto_increment		(){ return $this->auto_increment; }
	
	
	/**
	 * @return Boolean
	 */
	public function is_zerofill				(){ return $this->zerofill; }
	
	
	/**
	 * @return Boolean
	 */
	public function is_on_create_timestamp	(){ return $this->on_create_timestamp; }
	
	
	/**
	 * @return Boolean
	 */
	public function is_on_update_timestamp	(){ return $this->on_update_timestamp; }
	
	
	
	public function has_foreign_key			(){ return $this->foreign_key; }
	
	
	
	public function is_file					(){ return $this->file_options ? true : false; }
	
	
	public function get_file_options		(){ return $this->file_options; }
	
	
	public function get_file_field_name		(){ return $this->file_field_name; }
	
	
	
	/**
	 * ------------------------------------
     * =========== ORM Methods ============
	 * ------------------------------------
     */
	
	public function doNotValidate()
	{
		$this->can_validate = false;
		return $this;
	}
	
	
	public function canValidate()
	{
		return $this->can_validate;
	}
	
	
	public function doNotEscapeHTML()
	{
		$this->can_escape_html = false;
		return $this;
	}
	
	
	public function canEscapeHTML()
	{
		return $this->can_escape_html;
	}
	
	
	public function escapeHTML($value)
	{
		return $this->can_escape_html ? htmlspecialchars(html_entity_decode($value)) : $value;
	}
	
	
	
	/**
	 * ------------------------------------
     * ============== Events ==============
	 * ------------------------------------
     */
	
	public function onBeforeSave()
	{
		// TODO IDEA: On Before Save
	}
	
	
	public function onAfterLoad()
	{
		// TODO IDEA: On After Load
	}
	
	
	
	/**
	 * ------------------------------------
     * ========= Special Methods ==========
	 * ------------------------------------
     */
	
	/**
	 * @return String
	 */
	public function getEscapeChar()
	{
		switch($this->type)
		{
			case 'number'		: return 'i'; 	break;
			case 'float'		: return 'f'; 	break;
			case 'boolean'		: return 'b'; 	break;
			default				: return ''; 	break;
		}
	}
	
}


/**
 * @param $name
 * @return ORMColumn
 */
function ORMColumn($name = '')
{
	return new ORMColumn($name);
}

?>
