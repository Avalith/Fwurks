<?php

class HtmlForm_DateField extends HtmlForm_Field
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
	protected $format = 'DD-MMMM-YYYY';
	
	protected $start_year = 1920;
	protected $end_year;
	protected $end_year_add;
	
	protected $time;
	protected $months;
	
	protected $show_empty_option = 0;
	protected $empty_option_value = '';
	protected $empty_option_title = '';
	
	protected $dont_use_fieldname;
	
	protected $day_name 	= 'day';
	protected $month_name 	= 'month';
	protected $year_name 	= 'year';
	
	protected $picker 		= false;
	 
	
	public function __construct(array $params)
	{
		parent::__construct($params);
		
		$this->class_wrapper .= ' date';
		
		$this->picker || $this->value || $this->show_empty_option || $this->value = strftime('%Y-%m-%d');
		$this->picker || $this->value && ($this->value = is_array($this->value) ? $this->num_array($this->value) : preg_split('/[^\d]+/ui', $this->value));
				
		$this->end_year 		|| $this->end_year = strftime('%Y');
		$this->end_year_add 	&& $this->end_year += $this->end_year_add;
		
		$this->months = Registry::$globals['MONTHS'];
	}
	
	public function field()
	{
		if($this->picker){ return $this->picker(); }
		
		$format = preg_split('/[^DMY]+/ui', $this->format);
		foreach($format as $f){ $html .= $this->draw_select($f).'&nbsp;'; }
		return $html;
	}
	
	protected function draw_select($format)
	{
		$this->show_empty_option && $options .= $this->option($this->empty_option_title, $this->empty_option_value);
		
		switch($format)
		{
			case 'D'	: for($i=1; $i<=31; $i++){ $options .= $this->option($i, 								sprintf('%02d', $i), (int)$this->value[2] == $i); } break;
			case 'DD'	: for($i=1; $i<=31; $i++){ $options .= $this->option(sprintf('%02d', $i), 				sprintf('%02d', $i), (int)$this->value[2] == $i); }	break;
			
			case 'M'	: for($i=1; $i<=12; $i++){ $options .= $this->option($i, 								sprintf('%02d', $i), (int)$this->value[1] == $i); }	break;
			case 'MM'	: for($i=1; $i<=12; $i++){ $options .= $this->option(sprintf('%02d', $i), 				sprintf('%02d', $i), (int)$this->value[1] == $i); }	break;
			case 'MMM'	: for($i=1; $i<=12; $i++){ $options .= $this->option(substr($this->months[$i-1], 0, 3), sprintf('%02d', $i), (int)$this->value[1] == $i); }	break;
			case 'MMMM'	: for($i=1; $i<=12; $i++){ $options .= $this->option($this->months[$i-1], 				sprintf('%02d', $i), (int)$this->value[1] == $i); }	break;
			
			case 'YY'	: for($i=$this->end_year; $i>=$this->start_year; $i--){ $options .= $this->option(substr($i, 2),	$i, 	(int)$this->value[0] == $i); }	break;
			case 'YYYY'	: for($i=$this->end_year; $i>=$this->start_year; $i--){ $options .= $this->option($i, 				$i, 	(int)$this->value[0] == $i); }	break;
			
			default: break;
		}
		
		switch($format[0])
		{
			case 'D':	$_name = $this->day_name;		break;
			case 'M':	$_name = $this->month_name;		break;
			case 'Y':	$_name = $this->year_name;		break;
			default:									break;
		}
		
		$this->dont_use_fieldname && $name = $_name;
		
		$name || $name 	= $this->dont_use_fieldname ? $name : "{$this->name}[{$_name}]";
		$id = "{$this->id}_{$_name}";
		
		return "<select name=\"$name\" id=\"$id\">$options</select>";
	}
	
	protected function option($title, $val, $selected = 0)
	{
		$val = htmlspecialchars($val);
		return "<option value=\"$val\" ".($selected ? 'selected="selected"' : '').">$title</option>";
	}
	
	protected function num_array($array){ return array($array['year'], $array['month'], $array['day']); }
	
	protected function picker()
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
			'YYYY'	=> '%Y'
		);
		$format = strtr($this->format, $adapter_strftime);
		$this->value || $this->show_empty_option || $this->value = strftime($format);
		
		$adapter_js = array
		(
			'D'		=> 'd',
			'DD'	=> 'dd',
			'M'		=> 'm',
			'MM'	=> 'mm',
			'MMM'	=> 'M',
			'MMMM'	=> 'MM',
			'YY'	=> 'y',
			'YYYY'	=> 'yy'
		);
		$format = strtr($this->format, $adapter_js);
		
		//$this->value && is_array($this->value) && d($this->value);
		
		return "<div class=\"{$this->class_field_wrapper}\"><input type=\"{$this->type}\" name=\"{$this->name}\" id=\"{$this->id}\" value=\"{$this->value}\" class=\"datepicker {$this->class_field} {$this->class}\" rel=\"{$this->rel}\" lang=\"{$this->lang}\" maxlength=\"{$this->maxlength}\" {$this->disabled} date:dateFormat=\"$format\" /></div>";
	}
	
}

?>