<?php

class HtmlForm_SelectField extends HtmlForm_Field 
{
	protected $value_field = 'id';
	protected $title_field = 'title';
	protected $data = array();
	
	protected $default;
	protected $show_empty_option;
	protected $empty_option_value = '';
	protected $empty_option_title = '&nbsp;';
	protected $size = 1;
	
	protected $disabled_values = array();
	
	public function field()
	{
		$value = $this->value_field;
		$title = $this->title_field;
		
		$this->show_empty_option && $options = "<option value=\"{$this->empty_option_value}\">{$this->empty_option_title}</option>";
		$this->value === null && $this->value = $this->default;
		
		is_array($this->disabled_values) || $this->disabled_values = array($this->disabled_values);
		
		if($this->data)
		{
			foreach ($this->data as $key => $opt)
			{
				is_array($opt) && $opt = (object)$opt;
				
//									Title										Value			Selected																								Disabled											Option data
				if(is_object($opt))	{ $options .= $this->option($opt->$title	, $opt->$value	, ( $opt->$value == $this->value || (is_array($this->value) && in_array($opt->$value, $this->value)) )	, in_array($opt->$value, $this->disabled_values)	, $opt); }
				else				{ $options .= $this->option($opt			, $key			, ($key == $this->value  || (is_array($this->value) && in_array($key, $this->value)))					, in_array($key, $this->disabled_values)			, $opt); }
			}
		}
		return "<select name=\"{$this->name}".($this->multiple ? '[]' : null)."\" id=\"{$this->id}\" class=\"{$this->class}\" lang=\"{$this->lang}\" rel=\"{$this->rel}\" ".($this->multiple ? ' multiple="multiple" ' : '')." size=\"{$this->size}\">$options</select>";
	}
	
	protected function option($title, $val, $selected = 0, $disabled = 0, $opt = null)
	{
		$val = htmlspecialchars($val);
		return "<option value=\"$val\" ".($selected ? 'selected="selected"' : '').' '.($disabled ? 'disabled="disabled"' : '').'>'.$this->optTitlePrefix($opt)." {$title}</option>";
	}
	
	protected function optTitlePrefix($opt){}
}

?>
