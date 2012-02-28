<?php

class Application_Controller extends BaseController
{
	public function error404()
	{
		header('HTTP/1.1 404 Not Found');
		header('Status: 404 Not Found');
	}
}

?>
