<?php

class HtmlForm_RteField extends HtmlForm_Field
{
	protected $rows = 5;
	protected $class = 'default';
	protected $ib_folder;
	protected $ib_show_libs_select = false;
	
	public function field()
	{
		Registry()->session->iBrowser = array
		(
			'folder' => $this->ib_folder,
			'show_libs_select' => $this->ib_show_libs_select,
			'public'			=> Dispatcher::$public
		);
		
		return "<textarea name=\"{$this->name}\" id=\"{$this->id}\" class=\"rte-{$this->class}\" rows=\"{$this->rows}\" rel=\"{$this->rel}\" lang=\"{$this->lang}\">$this->value</textarea>";
	}
}

?>