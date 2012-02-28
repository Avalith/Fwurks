<?php

require_once __DIR__.'/Textarea.php';

class HtmlForm_RteField extends HtmlForm_TextareaField
{
	protected $class = 'default';
	protected $ib_folder;
	protected $ib_show_libs_select = false;
	
	public function field()
	{
		Registry::$session->iBrowser = array
		(
			'folder' 			=> $this->ib_folder,
			'show_libs_select' 	=> $this->ib_show_libs_select,
			'public'			=> Dispatcher::$public
		);
		
		$this->class = 'rte-'.$this->class;
		
		$this->value = str_replace('<textarea>', htmlentities('<textarea>'), $this->value);
		$this->value = str_replace('</textarea>', htmlentities('</textarea>'), $this->value);
		
		return parent::field();
	}
}

?>
