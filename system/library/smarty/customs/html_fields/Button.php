<?php

require_once __DIR__.'/Simple.php';

class HtmlForm_ButtonField extends HtmlForm_SimpleField
{
	protected $type = 'button';
	protected $class_field = 'button';
	
	protected function reinit()
	{
		$this->value || $this->value = Registry::$globals['FORM_BUTTONS'][$this->name];
		$this->value || $this->value = $this->name;
	}
		
	public function html(){ return $this->field(); }
}

?>
