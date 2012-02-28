<?php

// methods that needs to be called from parrent if redeclared 
// after_update_storage, after_validation, after_save, after_delete
abstract class ActiveRecordFile extends ActiveRecord 
{
	protected $upload_files = array();
	
	public static function getFile($id, $field, $thumb = null)
	{
		$field_name = $thumb ? $field . '_' . $thumb : $field;
		$columns = static::columns(self::COLUMNS_TYPE_TABLE);
		
		if(!isset($columns->$field_name)){ return false; }
		$field = $columns->$field_name->get_file_field_name() ?: $field;
		if(substr($columns->$field->type(), -4) != 'blob'){ return false; }
		
		$query = static::find()->select($field_name .' as file')->where(static::primary_keys(0).'=?', $id)->limit(1);
		
		isset($columns->{$field.'_filename'}) && $query->addSelect($field.'_filename as name');
		isset($columns->{$field.'_filetype'}) && $query->addSelect($field.'_filetype as type');
		isset($columns->{$field.'_filesize'}) && $query->addSelect($field.'_filesize as size');
		
		$result = $query->result();
		return isset($result[0]) ? $result[0] : false;
	}
	
	
	protected function after_update_storage($attributes)
	{
		foreach(static::columns(self::COLUMNS_TYPE_TABLE) as $field_name => $c)
		{
			if(!$c->is_file() || $c->get_file_field_name() ){ continue; } // only file columns
			
			$opts = $c->get_file_options();
		
			$opts->delete = !$c->is_requiered() && isset($attributes['upload_delete'][$field_name]) && $attributes['upload_delete'][$field_name];
			
			$opts->file = new UploadedFile($field_name);
			if(!$opts->file->tmp_name && !$opts->delete)
			{ 
				$this->storage->$field_name = isset($this->old_storage->$field_name) ? $this->old_storage->$field_name : null; 
				
				if($opts->save_to == ORMColumn::FILE_SAVE_DB)
				{
					foreach($opts->crops as $i)		{ $this->storage->{"{$field_name}_c{$i}"} = isset($this->old_storage->{"{$field_name}_c{$i}"}) ? $this->old_storage->{"{$field_name}_c{$i}"} : null; }
					foreach($opts->resizes as $i)	{ $this->storage->{"{$field_name}_r{$i}"} = isset($this->old_storage->{"{$field_name}_r{$i}"}) ? $this->old_storage->{"{$field_name}_r{$i}"} : null; }
				}
				
				continue;
			}
			
			isset($opts->crops) && $opts->crop_coords = $attributes['upload_crop_coords'][$field_name];
			
			$this->dont_validate_fields[] = $field_name;
			$this->upload_files[$field_name] = $c;
		}
	}
	
	
	protected function after_validation()
	{
		foreach($this->upload_files as $field_name => $f)
		{
			$opts = $f->get_file_options();
			
			if($opts->delete)
			{
				$this->storage->$field_name 				= null;
				$this->storage->{"{$field_name}_filename"} 	= null;
				$this->storage->{"{$field_name}_filetype"} 	= null;
				$this->storage->{"{$field_name}_filesize"} 	= null;
				
				if($opts->save_to == ORMColumn::FILE_SAVE_DB)
				{
					foreach($opts->crops as $c){ $this->storage->{"{$field_name}_c{$c}"} = null; };
					foreach($opts->resizes as $r){ $this->storage->{"{$field_name}_r{$r}"} = null; };
				}
			}
			
			if(isset($opts->file->tmp_name) && $opts->file->tmp_name)
			{ 
				if($f->is_requiered() && $opts->file->size <= 0)
				{
					$this->add_error('cant_be_empty', $field_name);
				}
				else if(is_array($opts->valid_extensions) && (!in_array($opts->file->extension, $opts->valid_extensions) || in_array('!'.$opts->file->extension, $opts->valid_extensions)))
				{
					$this->add_error('file_extension', $field_name, null, array('%EXT%' => $opts->file->extension, '%VALID_EXTS%' => implode(', ', $opts->valid_extensions)));
				}
				else if($opts->valid_max_size && $opts->file->size > $opts->valid_max_size*1024)
				{
					$this->add_error('file_too_large', $field_name, null, array('%SIZE%' => ($opts->valid_max_size).'KB'));
				}
				
				$opts->delete = false;
			}
//			else
//			{
//				$this->storage->$field_name = $this->old_storage->$field_name;
//				$f->opts->extension_only && $this->storage->{"{$field_name}_filename"} = $this->old_storage->{"{$field_name}_filename"};
//			}
		}
	}
	
	
	protected function before_save()
	{
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$temp = SystemConfig::$filesPath.'temp/';
		
		foreach($this->upload_files as $field_name => $f)
		{
			$opts = $f->get_file_options();
			if(isset($opts->file->tmp_name) && $opts->file->tmp_name)
			{
				$this->storage->{"{$field_name}_filename"} = $opts->file->name;
				$this->storage->{"{$field_name}_filetype"} = $finfo->file($opts->file->tmp_name);
				$this->storage->{"{$field_name}_filesize"} = $opts->file->size;
				
				if($opts->save_to == ORMColumn::FILE_SAVE_DB)
				{
					$this->storage->$field_name = File::read($opts->file->tmp_name);
					
					$uid = uniqid('image_');
					$tpath = "{$temp}{$uid}{$opts->file->name}";
					foreach($opts->crops as $i)
					{
						$q = strpos($i, 'q');
						$q = $opts->file->thumbnail($tpath, $opts->crop_coords, substr($i, 0, $q), substr($i, $q+1));
						
						$this->storage->{"{$field_name}_c{$i}"} = File::read($tpath);
						File::delete($tpath);
					}
					foreach($opts->resizes as $i)
					{
						$q = strpos($i, 'q');
						$q = $opts->file->resize($tpath, substr($i, 0, $q), substr($i, $q+1));
						
						$this->storage->{"{$field_name}_r{$i}"} = File::read($tpath);
						File::delete($tpath);
					}
					
					File::delete($opts->file->tmp_name);
				}
			}
			else if(!$opts->delete)
			{
				unset($this->storage->$field_name);
			}
		}
		
		unset($finfo);
	}
	
	
	protected function after_save()
	{
		foreach($this->upload_files as $field_name => $f)
		{
			$opts = $f->get_file_options();
			
			
			if($opts->save_to == ORMColumn::FILE_SAVE_DB || !isset($opts->file) && !isset($opts->delete)){ continue; }
			
			
			// TODO: File FS
		}
		
		
		return;
		
		
		
		
		foreach($this->upload_files as $file => $f)
		{
			$fname = isset($f->opts->extension_only) ? $this->storage->id.'.'.$this->storage->$file : $this->storage->$file;
			$fnamed = isset($f->opts->extension_only) ? $this->storage->id.'.'.$this->old_storage->$file : $this->old_storage->$file;
			$path = rtrim($this->upload_folder.'/'.$f->opts->folder, '/').'/';
			$fpath = SystemConfig::$filesPath.$path;
			
			File::delete($fpath.$fnamed);
			if(is_array($f->opts->crop_sizes))	{ foreach($f->opts->crop_sizes 	as $k => $c){ strpos($k, 'x') && $c = $k; File::delete($fpath.$c.'/'.$fnamed); } }
			if(is_array($f->opts->resizes))		{ foreach($f->opts->resizes 	as $k => $c){ strpos($k, 'x') && $c = $k; File::delete($fpath.'r'.$c.'/'.$fnamed); } }
			
			// extension_only, folder, crop_sizes, valid, valid_size;
			if($f->delete){ continue; }
			
			!is_dir($fpath) && mkdir($fpath, 0777);
			
			$f->file->new_name(substr($fname, 0, strrpos($fname, '.')));
			$f->file->move($path);
			
			if(is_array($f->opts->crop_sizes))
			{
				foreach($f->opts->crop_sizes as $k => $c)
				{
					$q = null;
					if(strpos($k, 'x')){ $q = $c; $c = $k; }
					
					$cpath = $fpath.$c.'/';
					!is_dir($cpath) && mkdir($cpath, 0777);
					$f->file->thumbnail($f->crop_coords, $c, $q);
				}
			}
			
			if(is_array($f->opts->resizes))
			{
				foreach($f->opts->resizes as $k => $c)
				{
					$q = null;
					if(strpos($k, 'x')){ $q = $c; $c = $k; }
					
					$cpath = $fpath.'r'.strtr($c, array('^' => '_out', '>' => '_l', '<' => '_s', '!' => '_nr')).'/';
					!is_dir($cpath) && mkdir($cpath, 0777);
					
					$f->file->resize($c, $q);
				}
			}
		}
	}
	
	
	protected function after_destroy()
	{
		foreach(static::columns(self::COLUMNS_TYPE_TABLE) as $field_name => $c)
		{
			$opts = $f->get_file_options();
			if($opts->save_to == ORMColumn::FILE_SAVE_DB || !$c->is_file() || $c->get_file_field_name()){ continue; } // only fs file columns
			
		}
		
		return;
		
		foreach($this->upload_files as $file => $f)
		{
			$fname = isset($f->opts->extension_only) ? $this->storage->id.'.'.$this->storage->$file : $this->storage->$file;
			
			$path = rtrim($this->upload_folder.'/'.($f['folder'] ?: ''), '/').'/';
			$fpath = SystemConfig::$filesPath.$path;
			
			File::delete($fpath.$fname);
			if(is_array($f['crop_sizes']))	{ foreach($f['crop_sizes'] as $k => $c){ if(strpos($k, 'x')){ $q = $c; $c = $k; } File::delete($fpath.$c.'/'.$fname); } }
			if(is_array($f['resizes']))		{ foreach($f['resizes'] as $k => $c){ if(strpos($k, 'x'))	{ $q = $c; $c = $k; } File::delete($fpath.strtr($c, array('^' => '_out', '>' => '_l', '<' => '_s', '!' => '_nr')).'/'.$fname); } }
		}
	}
	
}

?>
