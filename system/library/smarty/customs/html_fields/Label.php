<?php

class HtmlForm_LabelField extends HtmlForm_Field
{
	protected $class_field = 'button';
	
	protected function reinit(){ $this->class_wrapper .= ' label'; }
	
	public function field(){ return "<div id=\"{$this->id}\" class=\"label_value\">{$this->value}</div>"; }
}

?>