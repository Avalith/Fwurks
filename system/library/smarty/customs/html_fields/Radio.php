<?php

class HtmlForm_RadioField extends HtmlForm_Field
{
	protected $checked;
	protected $value = '1';
	
	protected $group_name = '1';
	
	protected $label_class = 'checkbox';
	protected $class_field = 'checkbox';
	
	protected $no_semicolon = 1;
	
	public function field()
	{
		$this->value = htmlspecialchars($this->value);
		$checked = $this->checked ? 'checked="checked"' : '';
		return "<input type=\"radio\" name=\"{$this->group_name}\" id=\"{$this->id}\" value=\"{$this->value}\" class=\"{$this->class_field} {$this->class}\" $checked />";
	}
	
	protected function innerHtml(){ return $this->field() .' '. $this->label() .' '. $this->helper(); }
}

?>