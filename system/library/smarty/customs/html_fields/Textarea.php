<?php

class HtmlForm_TextareaField extends HtmlForm_Field
{
	protected $rows = 7;
	public function field()
	{
		return "<div class=\"{$this->class_field_wrapper}\"><textarea name=\"{$this->name}\" id=\"{$this->id}\" class=\"{$this->class}\" rows=\"{$this->rows}\" rel=\"{$this->rel}\" lang=\"{$this->lang}\">{$this->value}</textarea></div>";
	}
}

?>