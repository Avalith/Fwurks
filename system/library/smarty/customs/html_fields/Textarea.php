<?php

class HtmlForm_TextareaField extends HtmlForm_Field
{
	protected $rows = 7;
	public function field()
	{
		return "<div class=\"{$this->class_field_wrapper}\"><textarea name=\"{$this->name}\" id=\"{$this->id}\" class=\"field {$this->class}\" rows=\"{$this->rows}\" ".($this->cols ? "cols=\"{$this->cols}\"" : "")."rel=\"{$this->rel}\" lang=\"{$this->lang}\" placeholder=\"{$this->placeholder}\">{$this->value}</textarea></div>";
	}
}

?>