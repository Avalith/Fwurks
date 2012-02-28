<?php

class HtmlForm_CheckboxField extends HtmlForm_Field
{
	protected $field_value = '1';
	protected $value_field = 'id';
	
	protected $label_class = 'checkbox';
	protected $class_field = 'checkbox';
	
	protected $no_semicolon = 1;
	
	
	public function field()
	{
		$this->field_value = htmlspecialchars($this->field_value);
		
		if($this->value instanceof ORMResultSet)
		{
			foreach($this->value as $v)
			{
				if($v->{$this->value_field} == $this->field_value){ $this->value = $v->{$this->value_field}; break; }
			}
			if($this->value instanceof ORMResultSet){ $this->value = array(); }
		}
		else if($this->value instanceof ORMResult)	{ $this->value = $this->value->{$this->value_field}; }
		
		if(is_string($this->value)){ $this->value = explode(',', $this->value); }
		else if(!is_array($this->value)){ $this->value = (array)$this->value; }	
		
		in_array($this->field_value, $this->value) && $checked = 'checked="checked"';
		return "<input type=\"checkbox\" name=\"{$this->name}\" id=\"{$this->id}\" value=\"{$this->field_value}\" class=\"{$this->class_field} {$this->class}\" $checked />";
	}
	
	protected function innerHtml(){ return $this->field() .' '. $this->label() .' '. $this->helper(); }
}

?>