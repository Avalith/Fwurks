<?php

class Dashboard_Controller extends Admin_Controller
{
	public function index()
	{
		$this->includeCss('dashboard');
	}
	
	public static function actions(){ return 'index'; }
}

?>
