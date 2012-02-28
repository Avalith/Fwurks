<?php

class SqlColumn
{
	protected $name;
	protected $type;
	
	protected $length;
	protected $decimals;
	protected $value;
	
	protected $unsigned;
	protected $zerofill;
	protected $null;
	protected $default;
	protected $auto_increment;
	protected $key_type;
	
	protected $charset;
	protected $collate;
	
	protected $comment;
	
	protected $position;
	
	
	public function __construct($name)
	{
		$this->name = $name;
	}
	
	public function __toString()
	{
		
		$length = $decimals = '';
		if($this->length)
		{
			$this->decimals && $decimals = ', '.$this->decimals;
			$length = "({$this->length}{$decimals})";
		}
		
		return "`{$this->name}` {$this->type}{$length}{$this->unsigned}{$this->zerofill}{$this->value}{$this->null}{$this->default}{$this->auto_increment}{$this->key_type}{$this->charset}{$this->collate}{$this->comment}{$this->position}";	}
	
	
	/**
	 * @param Integer $length
	 * @param Integer $decimals
	 * @return SqlColumn
	 */
	public function length($length, $decimals = null)
	{
		$this->length = $length; 
		$this->decimals = $decimals;
		return $this;
	}
	
	
	/**
	 * @return SqlColumn
	 */
	public function unsigned()
	{
		$this->unsigned = ' UNSIGNED';
		return $this;
	}
	
	
	/**
	 * @return SqlColumn
	 */
	public function zerofill()
	{
		$this->zerofill = ' ZEROFILL';
		return $this;
	}
	
	
	/**
	 * @return SqlColumn
	 */
	public function notNull()
	{
		$this->null = ' NOT NULL';
		return $this;
	}
	
	
	/**
	 * @param Integer $value
	 * @return SqlColumn
	 */
	public function defaultValue($value = null)
	{
		$value = is_numeric($value) ? $value : (is_null($value) ? $value = 'NULL' : "'$value'");
		$this->default = " DEFAULT {$value}";
		return $this;
	}
	
	
	/**
	 * @param String $charset
	 * @return SqlColumn
	 */
	public function charset($charset = 'utf8')
	{
		$this->charset = "CHARACTER SET {$charset}";
		return $this;
	}
	
	
	/**
	 * @param String $collate
	 * @return SqlColumn
	 */
	public function collate($collate = 'utf8_general_ci')
	{
		$this->collate = " COLLATE {$collate}";
		return $this;
	}
	
	
	/**
	 * @return SqlColumn
	 */
	public function autoIncrement()
	{
		$this->auto_increment = ' AUTO_INCREMENT';
		return $this;
	}
	
	
	/**
	 * @return SqlColumn
	 */
	public function unique()
	{
		$this->key_type = ' UNIQUE KEY';
		return $this;
	}
	
	
	/**
	 * @return SqlColumn
	 */
	public function primary()
	{
		$this->key_type = ' PRIMARY KEY';
		$this->notNull();
		return $this;
	}
	
	
	/**
	 * @param String $comment
	 * @return SqlColumn
	 */
	public function comment($comment)
	{
		$this->comment = " COMMENT '$comment'";
		return $this;
	}
	
	
	/**
	 * @return SqlColumn
	 */
	public function first()
	{
		$this->position = " FIRST";
		return $this;
	}
	
	
	/**
	 * @param String $name
	 * @return SqlColumn
	 */
	public function after($name)
	{
		$this->position = " AFTER `{$name}`";
		return $this;
	}
	
	
	/**
	 * @return SqlColumn
	 */
	public function clearPosition()
	{
		$this->position = null;
		return $this;
	}
	
	
	
	
	
	/**
	 * ****************** Types ******************
	 */
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function BIT			($length = null){ $this->type = 'BIT'; 			$this->length = $length; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function TINYINT		($length = null){ $this->type = 'TINYINT'; 		$this->length = $length; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function SMALLINT	($length = null){ $this->type = 'SMALLINT'; 	$this->length = $length; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function MEDIUMINT	($length = null){ $this->type = 'MEDIUMINT'; 	$this->length = $length; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function INT			($length = null){ $this->type = 'INT';	 		$this->length = $length; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function INTEGER		($length = null){ $this->type = 'INTEGER'; 		$this->length = $length; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function BIGINT		($length = null){ $this->type = 'BIGINT'; 		$this->length = $length; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function REAL		($length = null, $decimals = null){ $this->type = 'REAL'; 		$this->length = $length; $this->decimals = $decimals; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function DOUBLE		($length = null, $decimals = null){ $this->type = 'DOUBLE'; 	$this->length = $length; $this->decimals = $decimals; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function FLOAT		($length = null, $decimals = null){ $this->type = 'FLOAT'; 		$this->length = $length; $this->decimals = $decimals; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function DECIMAL		($length = null, $decimals = null){ $this->type = 'DECIMAL'; 	$this->length = $length; $this->decimals = $decimals; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function NUMERIC		($length = null, $decimals = null){ $this->type = 'NUMERIC'; 	$this->length = $length; $this->decimals = $decimals; return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function BOOL		(){ $this->type = 'TINYINT'; $this->length = 1; return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function BOOLEAN		(){ $this->type = 'TINYINT'; $this->length = 1; return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function DATE		(){ $this->type = 'DATE';		return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function TIME		(){ $this->type = 'TIME'; 		return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function TIMESTAMP	(){ $this->type = 'TIMESTAMP'; 	return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function DATETIME	(){ $this->type = 'DATETIME'; 	return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function YEAR		(){ $this->type = 'YEAR'; 		return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function CHAR		($length = null){ $this->type = 'CHAR'; 		$this->length = $length; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function VARCHAR		($length = null){ $this->type = 'VARCHAR'; 		$this->length = $length; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function BINARY		($length = null){ $this->type = 'BINARY'; 		$this->length = $length; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function VARBINARY	($length = null){ $this->type = 'VARBINARY'; 	$this->length = $length; return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function TINYBLOB	(){ $this->type = 'TINYBLOB';		return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function BLOB		(){ $this->type = 'BLOB'; 			return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function MEDIUMBLOB	(){ $this->type = 'MEDIUMBLOB'; 	return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function LONGBLOB	(){ $this->type = 'LONGBLOB'; 		return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function TINYTEXT	(){ $this->type = 'TINYTEXT';		return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function TEXT		(){ $this->type = 'TEXT'; 			return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function MEDIUMTEXT	(){ $this->type = 'MEDIUMTEXT'; 	return $this; }
	
	/**
	 * @return SqlColumn
	 */
	public function LONGTEXT	(){ $this->type = 'LONGTEXT'; 		return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function ENUM		($value){ $this->type = 'ENUM'; $this->value = " ('".implode("', '", func_get_args())."')"; return $this; }
	
	/**
	 * @param Integer $length
	 * @return SqlColumn
	 */
	public function SET			($value){ $this->type = 'SET'; 	$this->value = " ('".implode("', '", func_get_args())."')"; return $this; }
	
}


/**
 * 
 * @param $name
 * @return SqlColumn
 */
function SqlColumn($name)
{
	return new SqlColumn($name);
}

?>