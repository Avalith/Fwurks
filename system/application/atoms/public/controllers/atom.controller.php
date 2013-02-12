<?php

abstract class Atom_Controller extends Application_Controller
{
	public function __construct(library\RouterRequest $request)
	{
		parent::__construct($request);
		
		$this->__style('common');
		$this->__javascript('jquery', 'interface');
		
	}
}

?>
