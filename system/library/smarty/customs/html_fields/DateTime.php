<?php

class HtmlForm_DateTimeField extends HtmlForm_Field
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
	protected $format_date = 'YYYY-MM-DD';
	protected $format_time = 'hh:mm:ss';
	
	
	protected $start_year = 1920;
	protected $end_year;
	protected $end_year_add;
	
	protected $time;
	protected $months;
	
	protected $show_empty_option = 0;
	protected $empty_option_value = '';
	protected $empty_option_title = '';
	
	protected $dont_use_fieldname;

	 
	
	public function __construct(array $params)
	{
		parent::__construct($params);
		
		$this->class_wrapper .= ' date-time';
		$this->type = 'text';
		$this->picker || $this->value || $this->show_empty_option || $this->value = strftime('Application_Config::MYSQL_DATETIME_FORMAT');
				
		$this->end_year 		|| $this->end_year = strftime('%Y');
		$this->end_year_add 	&& $this->end_year += $this->end_year_add;
		
		$this->months = Registry::$globals['MONTHS'];
	}
	
	public function field()
	{
		$adapter_strftime = array
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
		$format_date = strtr($this->format_date, $adapter_strftime);
		$format_time = strtr($this->format_time, $adapter_strftime);
		$this->value || $this->show_empty_option || $this->value = strftime($format_date . ' ' . $format_time);
		
		$this->value && $this->value = strftime($format_date . ' ' . $format_time, strtotime($this->value));
		
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
		$format_date = strtr($this->format_date, $adapter_js);
		$format_time = strtr($this->format_time, $adapter_js);
		
		//$this->value && is_array($this->value) && d($this->value);
		return "<div class=\"{$this->class_field_wrapper}\"><input type=\"{$this->type}\" name=\"{$this->name}\" id=\"{$this->id}\" value=\"{$this->value}\" class=\"datetimepicker {$this->class_field} {$this->class}\" rel=\"{$this->rel}\" lang=\"{$this->lang}\" maxlength=\"{$this->maxlength}\" {$this->disabled} date:dateFormat=\"$format_date\" date:timeFormat=\"$format_time\"/></div>";
	}
	
}

?>