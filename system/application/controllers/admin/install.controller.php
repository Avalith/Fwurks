<?php

class Install_Controller extends Admin_Controller
{
	public function index()
	{
		redirect('//');
	}
		
	public function sync_models_with_database()
	{
		$this->user->is_root || redirect('//');
		
		$dir = SystemConfig::$modelsPath;
		
		if($this->is_post() && isset($_POST['sync']))
		{
			foreach(array_keys($_POST['sync']) as $class)
			{
				$file = $dir.Inflector::to_file($class).'.model.php';
				if(!file_exists($file)){ continue; }
				require_once $file;
				
				$q = new ModelSynchronizer($class);
				if($sync = $q->sync())
				{
					isset($sync->main) && $sync->main->run();
					isset($sync->i18n) && $sync->i18n->run();
				}
			}
			
			redirect('./');
		}
		
		
		$files = array();
		$handler = opendir($dir);
		while(($file = readdir($handler)) !== false){ substr($file, -10) == '.model.php' && $files[] = $file; }
		sort($files);
		
		$this->sync = array();
		foreach($files as $file)
		{
			require_once $dir.$file;
			
			$class = Inflector::classify(substr($file, 0, -10));
			if(!is_subclass_of($class, 'ORM')){ continue; }
			
			$reflection = new ReflectionClass($class);
			if($reflection->isAbstract()){ continue; }
			
			$q = new ModelSynchronizer($class);
			$this->sync[] = (object)array
			(
				'model' 	=> $class,
				'queries' 	=> $q->sync(),
			);
		}
		
		closedir($handler);
	}
	
}

?>

