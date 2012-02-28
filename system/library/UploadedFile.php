<?php

/**
 * UploadedFile
 * 
 * @method move moves uploaded file to the given location (path)
 * @method new_name changes the filename (must be without extension!)
 * @method thumbnail creates thumbnail of an image 
 */
class UploadedFile
{
	public $name;
	public $tmp_name;
	public $type;
	public $size;
	public $error;
	
	public $extension;
	
	protected $path;
	
	public function __construct($file)
	{
		$this->name 		= $_FILES[$file]['name'];
		$this->tmp_name 	= $_FILES[$file]['tmp_name'];
		$this->type 		= $_FILES[$file]['type'];
		$this->size 		= $_FILES[$file]['size'];
		$this->error 		= $_FILES[$file]['error'];
		
		$this->extension = File::extension($this->name);
	}
	
	
	/**
	 * moves uploaded file to the given location (path)
	 *
	 * @param string $path path where file will be placed
	 * @return bool
	 */
	public function move($path)
	{
		$this->path = SystemConfig::$filesPath . rtrim($path, '/').'/';
		return File::move($this->tmp_name, $this->path . $this->name);
	}
	
	
	/**
	 * changes the filename (must be without extension!)
	 *
	 * @param string $name
	 */
	public function new_name($name)
	{
		$this->name = $name.'.'.$this->extension; 
	}
	
	
	/**
	 * creates thumbnail of an image 
	 *
	 */
	public function thumbnail($coords, $size, $quality)
	{
		if(!$this->path){ return false; }
		
		$quality || $quality = 80;
		$ocx = (int)$coords['x'];   $ocy = (int)$coords['y'];   $ocx2 = (int)$coords['x2'];   $ocy2 = (int)$coords['y2'];   $ocw = $ocx2 - $ocx;   $och = $ocy2 - $ocy;
		
		$command = $ocw > 0
			? "convert {$this->path}{$this->name} -crop {$ocw}x{$och}+{$ocx}+{$ocy} -thumbnail {$size} -quality {$quality} {$this->path}{$size}/{$this->name}"
			: "convert {$this->path}{$this->name} -thumbnail {$size}^ -gravity center -crop {$size}+0+0 -quality {$quality} {$this->path}{$size}/{$this->name}"
		;
		
		exec($command, $output);
		return $output;
	}

	
	/**
	 * @param $size (string)
	 * Wx - the width will be always the same
	 * xH - the height will be always the same
	 * WxH - will resize inside
	 * WxH^ - will resize outside
	 * WxH> - will resize only larger images
	 * WxH< - will resize only smaller images
	 * WxH! - won't keep the aspect ratio
	 */
	public function resize($size, $quality)
	{
		if(!$this->path){ return false; }
		
		$quality || $quality = 80;
		$folder = strtr($size, array('^' => '_out', '>' => '_l', '<' => '_s', '!' => '_nr'));
		$command = "convert {$this->path}{$this->name} -resize '{$size}' -quality {$quality} {$this->path}r{$folder}/{$this->name}";
		
		exec($command, $output);
		return $output;
	}
}
?>