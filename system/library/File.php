<?php

class File
{
	/**
	 * Returns file extension
	 *
	 * @param string $string filename
	 * @return string extension
	 */
	public static function extension($string)
	{
		return strtolower(strchr($string, '.') ? substr($string, strrpos($string, '.')+1) : $string);
	}
	
	
	public static function move($from, $to, $overwrite = true)
	{
		if( !is_file($from) || (is_file($to) && !$overwrite) ){ return false; }
		
		is_file($to) && unlink($to);
		return rename($from, $to);
	}
	
	
	public static function copy($from, $to, $overwrite = true)
	{
		if( !is_file($from) || (is_file($to) && !$overwrite) ){ return false; }
		
		return copy($from, $to);
	}
	
	
	public static function delete($file)
	{
		return is_file($file) ? unlink($file) : false;
	}
	
	
	public static function permissions($filepath)
	{
		if(!file_exists($filepath)){ return false; }
		
		$perms = fileperms($filepath);
		
		if		(($perms & 0xC000) == 0xC000){ $type = 'socket'; }
		else if	(($perms & 0xA000) == 0xA000){ $type = 'link'; }
		else if	(($perms & 0x8000) == 0x8000){ $type = 'file'; }
		else if	(($perms & 0x6000) == 0x6000){ $type = 'block'; }
		else if	(($perms & 0x4000) == 0x4000){ $type = 'directory'; }
		else if	(($perms & 0x2000) == 0x2000){ $type = 'character'; }
		else if	(($perms & 0x1000) == 0x1000){ $type = 'pipe'; } 
		
		$oct = decoct($perms);
		return (object)array
		(
			'int'	=> $perms,
			'full'	=> $oct,
			'short'	=> substr($oct, -4),
		
			'type'	=> $type,
			'owner'	=> (object)array
			(
				'read'		=> $perms & 0x0100 ? true : false,
				'write'		=> $perms & 0x0080 ? true : false,
				'run'		=> $perms & 0x0040 ? ($perms & 0x0400 ? 's' : 'x') : ($perms & 0x0800 ? 'S' : false),
			),
			'group'	=> (object)array
			(
				'read'		=> $perms & 0x0020 ? true : false,
				'write'		=> $perms & 0x0010 ? true : false,
				'run'		=> $perms & 0x0008 ? ($perms & 0x0400 ? 's' : 'x' ) : ($perms & 0x0400 ? 'S' : false),
			),
			'other'	=> (object)array
			(
				'read'		=> $perms & 0x0004 ? true : false,
				'write'		=> $perms & 0x0002 ? true : false,
				'run'		=> $perms & 0x0001 ? ($perms & 0x0200 ? 't' : 'x' ) : ($perms & 0x0200 ? 'T' : false),
			),
		);
	}
}
?>
