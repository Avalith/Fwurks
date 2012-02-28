<?php

class Files_Controller extends Public_Controller
{
	protected function file()
	{
		$model 	= Inflector::classify(Inflector::singularize($this->__get['model']));
		$field 	= $this->__get['field'];
		$thumb 	= isset($this->__get['thumb']) ? $this->__get['thumb']: null;
		$id 	= (int)$this->__get['id'];
		
		if($file = $model::getFile($id, $field, $thumb))
		{
			isset($file->type) && header('Content-Type: '.$file->type);
			header('Content-Length: '.strlen($file->file));
			
			$this->__get['type'] == 'download' && header('Content-Disposition: attachment; filename="'.$file->name.'"');
			
			echo $file->file;
		}
		
		exit;
	}
}

?>
