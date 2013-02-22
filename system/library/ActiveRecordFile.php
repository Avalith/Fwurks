<?php

// methods that needs to be called from parrent if redeclared 
// after_update_storage, after_validation, after_save, after_delete
abstract class ActiveRecordFile extends ActiveRecord 
{
	protected $upload_folder = '';
	
//	'db_field' 	=> array
//	(
//		'extension_only' => true, 							// if set this will save only the extension in the db, there should a filename field too					
//		'folder' => 'sub_folder', 							// if set this will save the files in a subfolder of $upload_folder 
//		'crop_sizes' => array('80x70' => 20, '100x100'), 	// if set the file might be cropped, possible are WxH or WxH => Q

//		'resizes' => array('80x70' => 20, '100x100'), 		// if set the file will be resized, possible are WxH^! or WxH => Q
															// Wx - the width will be always the same
															// xH - the height will be always the same
															// WxH - will resize inside
															// WxH^ - will resize outside
															// WxH> - will resize only larger images
															// WxH< - will resize only smaller images
															// WxH! - won't keep the aspect ratio
															
//		'valid' => array('jpg', 'png'),						// valid extensions
//		'valid_size' => 1000								// maximum size in KB
//	)
	protected $upload_files = array();
	
	
	protected function after_update_storage(&$attributes)
	{
		foreach($this->upload_files as $file => &$opts)
		{
			$f = new UploadedFile($file);
			
			if(!($opts instanceof stdClass))
			{
				isset($opts['folder']) || $opts['folder'] = '';
				$opts = (object)array( 'opts' => (object)$opts );
			}
			isset($attributes['upload_delete'][$file]) && $opts->delete = 1;
			
			if(!$f->tmp_name){ $this->storage->$file = $this->old_storage->$file; continue; }
			
			$opts->file = $f; 
			isset($opts->opts->crop_sizes) && $opts->crop_coords = $attributes['upload_crop_coords'][$file];
			
			$this->dont_validate_fields[] = $file;
		}
	}
	
	protected function after_validation()
	{
		$columns = self::columns(self::COLUMNS_TYPE_ALL);
		
		foreach($this->upload_files as $file => $f)
		{
			if(isset($f->file))
			{ 
				if($columns[$file]->not_null && $f->file->size <= 0)
				{
					$this->add_error('cant_be_empty', $file);
				}
				else if(is_array($f->opts->valid) && (!in_array($f->file->extension, $f->opts->valid) || in_array('!'.$f->file->extension, $f->opts->valid)))
				{
					$this->add_error('file_extension', $file, null, array('%EXT%' => $f->file->extension, '%VALID_EXTS%' => implode(', ', $f->opts->valid)));
				}
				else if(isset($f->opts->valid_size) && $f->file->size > $f->opts->valid_size*1024)
				{
					$this->add_error('file_too_large', $file, null, array('%SIZE%' => ($f->opts->valid_size).'KB'));
				}
				
				unset($f->delete);
			}
			
			if($columns[$file]->not_null ){ unset($f->delete); }
			
			if($f->delete)
			{
				$this->storage->$file = '';
				$f->opts->extension_only && $this->storage->{"{$file}_filename"} = '';
			}
			else if(isset($f->file))
			{
				$this->storage->$file = $f->opts->extension_only ? $f->file->extension : $f->file->name;
				$f->opts->extension_only && $this->storage->{"{$file}_filename"} = $f->file->name;
			}
			else
			{
				$this->storage->$file = $this->old_storage->$file;
				$f->opts->extension_only && $this->storage->{"{$file}_filename"} = $this->old_storage->{"{$file}_filename"};
			}
		}
	}
	
	
	protected function after_save()
	{
		foreach($this->upload_files as $file => $f)
		{
			if(!isset($f->file) && !isset($f->delete)){ continue; }
			
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
	
	protected function after_destroy($primary_key)
	{
		foreach($this->upload_files as $file => $f)
		{
			$fname = isset($f->opts->extension_only) ? $this->storage->id.'.'.$this->storage->$file : $this->storage->$file;
			
			$path = rtrim($this->upload_folder.'/'.($f['folder'] ?: ''), '/').'/';
			$fpath = SystemConfig::$filesPath.$path;
			
			File::delete($fpath.$fname);
			if(is_array($f['crop_sizes'])){ foreach($f['crop_sizes'] as $c){ File::delete($fpath.$c.'/'.$fname); } }
			if(is_array($f['resizes'])){ foreach($f['resizes'] as $c){ File::delete($fpath.strtr($c, array('^' => '_out', '>' => '_l', '<' => '_s', '!' => '_nr')).'/'.$fname); } }
		}
	}
}

?>
