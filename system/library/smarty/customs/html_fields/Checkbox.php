<?php

class HtmlForm_CheckboxField extends HtmlForm_Field
{
	protected $field_value = '1';
	
	protected $label_class = 'checkbox';
	protected $class_field = 'checkbox';
	
	protected $no_semicolon = 1;
	
	public function field()
	{
		$this->field_value = htmlspecialchars($this->field_value);
		$checked = ($this->field_value==$this->value || ( is_array($this->value) && in_array($this->field_value, $this->value) )) ? 'checked="checked"' : '';
		return "<input type=\"checkbox\" name=\"{$this->name}\" id=\"{$this->id}\" value=\"{$this->field_value}\" class=\"{$this->class_field} {$this->class}\" $checked />";
	}
	
	protected function innerHtml(){ return $this->field() .' '. $this->label() .' '. $this->helper(); }
}

?>