<?php

abstract class AbstractImage extends ActiveRecord 
{
	protected $uploaded_pic_path = 'images';
	protected $uploaded_pic_tpath;
	protected $allowed_pic_exts = array('jpg', 'jpeg', 'png', 'gif');
	
	protected $uploaded_pic_sizes = array
	(
		'listing' 	=> '100x100',
		'big' 		=> '1024x1024',
	);
	
	protected function after_load()
	{
		$this->storage->picture && $this->storage->picture = $this->storage->{$this->primary_key}.'.'.$this->storage->picture;
	}

	protected function after_update_storage(&$attributes)
	{
		$this->storage->__delete = $attributes['__delete'];
	}
	
	protected function before_validation()
	{
		if($this->storage->__delete['picture'])
		{
			$this->deleteFiles($this->old_storage->picture);
			$this->storage->picture = '';
		}
		unset($this->storage->__delete);
		
		$file = new UploadedFile('picture');
		if($file->tmp_name)
		{
			if($file->name && in_array(strtolower($file->extension), $this->allowed_pic_exts))
			{
				$this->storage->picture = strtolower($file->extension);
			}
			else { $this->add_error('not_allow_extention', 'picture'); }
			$this->storage->picture_filename = $file->name;
		}
		
		strpos($this->storage->picture, '.') && $this->storage->picture = strtolower(File::extension($this->storage->picture));
	}
	
	protected function deleteFiles($ext)
	{
		if($ext)
		{
			$pic = SystemConfig::$filesPath.$this->uploaded_pic_path.'/'.$this->storage->id.'.'.$ext;
			if(file_exists($pic)){ unlink($pic); }
			
			foreach($this->uploaded_pic_sizes as $t => $s)
			{
				$pic = SystemConfig::$filesPath.$this->uploaded_pic_path.'/'.$t.'/'.$this->storage->id.'.'.$ext;
				if(file_exists($pic)){ unlink($pic); }
			}
		}
	}
	
protected function before_save()
	{
		if($crop_pos = $_POST['crop_position'])
		{
			$file_path = end(glob(getcwd()."/public/files/{$this->uploaded_pic_path}_temp/{$this->storage->id}.*"));
			$extension = strtolower( File::extension(end(explode('/', $file_path))) );
			
			if (!$extension)
			{
				$file_path = end(glob(getcwd()."/public/files/{$this->uploaded_pic_path}_temp/u".self::$sess->logged_user->id.'.*'));
				$extension = strtolower( File::extension(end(explode('/', $file_path))) );
			}
			$this->storage->picture = $extension;
			$this->uploaded_pic_tpath = $file_path;
		}
	}
	
	protected function after_save()
	{
		if($crop_pos = $_POST['crop_position'])
		{
			$file = new UploadedFile('picture');
			$file_path = $this->uploaded_pic_tpath;
			
			$file->extension = File::extension($this->storage->picture);
			
			$this->deleteFiles($file->extension);
			
			$file->path($this->uploaded_pic_path);
			$file->new_name($this->storage->id);
			
			rename($file_path, $file->path.$file->name);
			
			foreach($this->uploaded_pic_sizes as $t => $s)
			{
				$file->thumbnail($s, $t, $crop_pos[$t]);
				rename($file->path.$t.'_'.$file->name, $file->path.$t.'/'.$this->storage->id.'.'.$file->extension);
			}
		}
	}
	
	protected function after_delete($params)
	{
		$this->deleteFiles($this->old_storage->picture);
	}
	
}

?>
