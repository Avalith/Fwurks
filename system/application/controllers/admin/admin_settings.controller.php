<?php

class AdminSettings_Controller extends Admin_Controller 
{
	public function index()
	{
		if(is_post())
		{
			$this->settings->set('records_per_page', (int)$_POST['records_per_page']);
			$this->settings->set('edit_link_titles', (int)$_POST['edit_link_titles']);
			$this->settings->set('google_analytics', $_POST['google_analytics']);
		}
		
		$this->admin_settings = $this->settings->get_storage();		
	}
	
	public static function actions(){ return array('listing' => 'index'); }
}

?>