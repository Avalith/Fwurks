<?php

class HtmlForm_SimpleField extends HtmlForm_Field
{
	protected $maxlength;
	
	public function field()
	{
		$this->value = htmlspecialchars($this->value);
		return "<div class=\"{$this->class_field_wrapper}\"><input type=\"{$this->type}\" name=\"{$this->name}\" id=\"{$this->id}\" value=\"{$this->value}\" class=\"{$this->class_field} {$this->class}\" rel=\"{$this->rel}\" lang=\"{$this->lang}\" maxlength=\"{$this->maxlength}\" {$this->disabled} /></div>";
	}
}

?>