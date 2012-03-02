<?php

class Application_Controller extends BaseController
{
	public function __construct(RouterRequest $request)
	{
		$this->__style('common');
		$this->__javascript('jquery', 'interface');
		parent::__construct($request);
	}
	
	public function action__404()
	{
		
	}
}

?>
