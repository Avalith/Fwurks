<?php

class AdminSettings_Controller extends Admin_Controller 
{
	public function index()
	{
		if($this->is_post())
		{
			$this->settings->set('records_per_page',	(int)$this->__post['records_per_page']);
			$this->settings->set('edit_link_titles',	(int)$this->__post['edit_link_titles']);
			$this->settings->set('active_by_default',	(int)$this->__post['active_by_default']);
			$this->settings->set('google_analytics', 	$this->__post['google_analytics']);
		}
		
		$this->admin_settings = $this->settings->storage();
		
		if($this->user->is_root)
		{
			$this->check_latest_lib_versions();
		}
	}
	
	
	protected function check_latest_lib_versions()
	{
		$jquery = new HttpRequest('http://jquery.com/');
		$jquery = $jquery->send();
		$start 	= strpos($jquery, '<p class="jq-version"><strong>Current Release:</strong> v')+57;
		$this->jquery_latest 	= substr($jquery, $start, strpos($jquery, '</p>', $start)-$start);
		$this->jquery_back 		= trim(file_get_contents(SystemConfig::$rootPath.'back/javascripts/jquery.js'	, null, null, 34, 7));
		$this->jquery_public 	= trim(file_get_contents(SystemConfig::$rootPath.'public/javascripts/jquery.js'	, null, null, 34, 7));
	
		$this->jquery_latest != $this->jquery_back 		&& $this->jquery_back 		= '<strong style="color: #A00">'.$this->jquery_back.'</strong>';
		$this->jquery_latest != $this->jquery_public 	&& $this->jquery_public 	= '<strong style="color: #A00">'.$this->jquery_public.'</strong>';
	
		$tinymce = new HttpRequest('http://tinymce.moxiecode.com/download/download.php');
		$tinymce = $tinymce->send();
		$start 	 = strpos($tinymce, '<a href="/develop/changelog/?type=tinymce">TinyMCE ')+51;
		$this->tinymce_latest = substr($tinymce, $start, strpos($tinymce, '</a>', $start)-$start);
		$tinymce = file_get_contents(SystemConfig::$rootPath.'back/javascripts/tinymce/tiny_mce.js'	, null, null, 55, 50);
	
		$start = strpos($tinymce, ':"')+2;
		$this->tinymce = substr($tinymce, 0, 1) . '.' . substr($tinymce, $start, strpos($tinymce, '",',$start)-$start);
	
		$this->tinymce_latest != $this->tinymce && $this->tinymce = '<strong style="color: #A00">'.$this->tinymce.'</strong>';
	}
	
	
	public static function actions(){ return array('listing' => 'index'); }

}

?>

