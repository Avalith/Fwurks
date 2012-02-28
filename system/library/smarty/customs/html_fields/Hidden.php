<?php

class HtmlForm_HiddenField extends HtmlForm_Field
{
	public function field()
	{
		$this->value = htmlspecialchars($this->value);
		return "<input type=\"hidden\" name=\"{$this->name}\" id=\"{$this->id}\" value=\"{$this->value}\" class=\"{$this->class}\" rel=\"{$this->rel}\" />";
	}
	
	public function html()
	{
		return $this->field();
	}
	
}

?>