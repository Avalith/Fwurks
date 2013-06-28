<?php

require_once __DIR__.'/Simple.php';

class HtmlForm_ButtonField extends HtmlForm_SimpleField
{
	protected $type = 'button';
	protected $tag = 'input';
	protected $class_field = 'button';
	
	protected function reinit()
	{
		$this->value || $this->value = Registry::$globals['FORM_BUTTONS'][$this->name];
		$this->value || $this->value = $this->name;
	}
		
	public function html(){ return $this->field(); }
	
	public function field()
	{
		if($this->tag != "button"){ return parent::field(); }
		$this->value = htmlspecialchars($this->value);
		return "<div class=\"{$this->class_field_wrapper}\"><button type=\"{$this->type}\" name=\"{$this->name}\" id=\"{$this->id}\" value=\"{$this->value}\" class=\"{$this->class_field} {$this->class}\" rel=\"{$this->rel}\" lang=\"{$this->lang}\" maxlength=\"{$this->maxlength}\" {$this->disabled} {$this->tabindex} ".($this->autocomplete ? "autocomplete=\"{$this->autocomplete}\"" : "")." placeholder=\"{$this->placeholder}\">{$this->value}</button></div>";
	}
	
}

?>
