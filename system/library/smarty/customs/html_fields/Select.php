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
	
	public function field()
	{
		$value = $this->value_field;
		$title = $this->title_field;
		
		$this->show_empty_option && $options = "<option value=\"{$this->empty_option_value}\">{$this->empty_option_title}</option>";
		$this->value === null && $this->value = $this->default;
		
		if($this->data)
		{
			foreach ($this->data as $key => $opt)
			{
				if(is_array($opt))			{ $options .= $this->option($opt[$title]	, $opt[$value]	, $opt[$value] == $this->value	, $opt); }
				else if(is_object($opt))	{ $options .= $this->option($opt->$title	, $opt->$value	, $opt->$value == $this->value	, $opt); }
				else						{ $options .= $this->option($opt			, $key			, $key == $this->value			, $opt); }
			}
		}
		return "<select name=\"{$this->name}\" id=\"{$this->id}\" class=\"{$this->class}\" lang=\"{$this->lang}\" rel=\"{$this->rel}\">$options</select>";
	}
	
	protected function option($title, $val, $selected = 0, $opt = null)
	{
		$val = htmlspecialchars($val);
		return "<option value=\"$val\" ".($selected ? 'selected="selected"' : '').'>'.$this->optTitlePrefix($opt)." {$title}</option>";
	}
	
	protected function optTitlePrefix($opt){}
}

?>
