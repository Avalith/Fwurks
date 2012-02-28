<?php

class Install_Controller extends Admin_Controller
{
	public function index($params)
	{
		
	}
		
	public function create_models()
	{
		$mPath = SystemConfig::$modelsPath;
		$mPerms = File::permissions($mPath);
		if(!$mPerms->other->write){ return; }
		
		if(is_post() && ($models = $_POST['models']))
		{
			$content = "<?php\n\nclass {classname} extends ActiveRecord{file}\n{\n{params}\n}\n\n?>";
			
			foreach($models as $m)
			{
				$params = "\t";
				$m['has_i18n'] 		&& $params .= "public static \$has_i18n = true;\n\t"; 
				$m['has_mirror'] 	&& $params .= "public static \$has_mirror = true;\n\t"; 
				
				$c = strtr($content, array
				(
					'{classname}' 	=> $m['modelname'],
					'{file}' 		=> $m['has_files'] ? 'File' : '',
					'{params}' 		=> $params,
				));
				
				$file = $mPath.$m['filename'].'.model.php';
				file_put_contents($file, $c);
			}
		}
		
		$db = Registry()->db;
		$this->models = array();
		foreach($db->fetch($db->query('SHOW TABLES')) as $table)
		{
			$table = current(array_values((array)$table));
			
			$model_file = Inflector::singularize($table);
			$model_name = Inflector::classify($model_file);
						
			$file = $mPath.$model_file.'.model.php';
			$fileP = $mPath.$table.'.model.php';
			
			if(substr($table, -5) != '_i18n' && !file_exists($file) && !file_exists($fileP))
			{
				$this->models[$model_file] = (object)array
				(
					'filename' => $model_file,
					'modelname' => $model_name,
				);
			}
			else if(substr($table, -5) == '_i18n')
			{
				$table = substr($table, 0, -5);
				$model_file = Inflector::singularize($table);
				
				$file = $mPath.$model_file.'.model.php';
				$fileP = $mPath.$table.'.model.php';
			
				if(!file_exists($file) && !file_exists($fileP))
				{
					$this->models[$model_file]->has_i18n = true;
					$this->models[$model_file]->has_mirror = true;
				}
			}
		}
	}
}

?>
