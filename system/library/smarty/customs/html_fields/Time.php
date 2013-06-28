<?php

class HtmlForm_TimeField extends HtmlForm_Field
{
	/**
	 * Format of the fields
	 * D 	- single digit day
	 * DD 	- day with leading zero
	 * M	- single digit month
	 * MM 	- month with leading zero
	 * MMM 	- month abbreviation
	 * MMMM - month full name
	 * YY 	- double digit year
	 * YYYY - quad digit year
	 * 
	 * @var string
	 * @example 'DD-MM-YYYY'
	 */

	protected $format_time = 'hh:mm:ss';
	
	protected $show_empty_option = 0;
	protected $empty_option_value = '';
	protected $empty_option_title = '';
	
	protected $dont_use_fieldname;

	private $adapter_strftime = array
		(
			'D'		=> '%e',
			'DD'	=> '%d',
			'M'		=> '%m',
			'MM'	=> '%m',
			'MMM'	=> '%b',
			'MMMM'	=> '%B',
			'YY'	=> '%y',
			'YYYY'	=> '%Y',
			'hh'	=> '%H',
			'mm'	=> '%M',
			'ss'	=> '%S', 
		);
	
	public function __construct(array $params)
	{
		parent::__construct($params);
		
		$this->class_wrapper .= ' time';
		$this->type = 'text';
		$this->picker || $this->value || $this->show_empty_option || $this->value = strftime(strtr($this->format_time, $this->adapter_strftime));

	}
	
	public function field()
	{
		$format_time = strtr($this->format_time, $this->adapter_strftime);
		$this->value || $this->show_empty_option || $this->value = strftime($format_time);
		
		$this->value && $this->value = strftime($format_time, strtotime($this->value));
		
		$adapter_js = array
		(
			'D'		=> 'd',
			'DD'	=> 'dd',
			'M'		=> 'm',
			'MM'	=> 'mm',
			'MMM'	=> 'M',
			'MMMM'	=> 'MM',
			'YY'	=> 'y',
			'YYYY'	=> 'yy',
			'hh'	=> 'hh',
			'mm'	=> 'mm',
			'ss'	=> 'ss', 
		);
		$format_time = strtr($this->format_time, $adapter_js);
		
		//$this->value && is_array($this->value) && d($this->value);
		return "<div class=\"{$this->class_field_wrapper}\"><input type=\"{$this->type}\" name=\"{$this->name}\" id=\"{$this->id}\" value=\"{$this->value}\" class=\"timepicker {$this->class_field} {$this->class}\" rel=\"{$this->rel}\" lang=\"{$this->lang}\" maxlength=\"{$this->maxlength}\" {$this->disabled} date:timeFormat=\"$format_time\"/></div>";
	}
	
}

?>